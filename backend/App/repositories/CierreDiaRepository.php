<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\Database;

/**
 * Repository: acceso a datos del Cierre de Día.
 * Consultas a BITACORA_CIERRE_DIARIO, TBL_CIERRE_DIA (resúmenes/correo), DEVENGO_DIARIO y SP_PAGOS_CIERRE_DEVENGO.
 * Concurrencia (cierre en curso): BITACORA_CIERRE_DIARIO. «Cierre ya ejecutado»: bitácora EXITO=1 o IMPORTACIONPAG (FEC_PAGO).
 * Sin lógica de negocio; solo SQL y llamadas a procedimientos.
 */
class CierreDiaRepository
{
    /**
     * Ejecuta un callable capturando cualquier echo (p. ej. Database::muestraError) para no contaminar respuestas JSON.
     *
     * @param callable $fn
     * @return mixed Valor de retorno del callable
     */
    private function sinSalida(callable $fn)
    {
        ob_start();
        try {
            return $fn();
        } finally {
            ob_end_clean();
        }
    }

    /**
     * FECHA_CALC en DEVENGO_DIARIO para cierre del día X: el SP registra devengo en X + 1.
     *
     * @param string $fechaCierre Fecha de cierre Y-m-d
     * @return string Fecha de devengo Y-m-d, o vacío si la entrada no es válida
     */
    private function fechaCalculoDevengoDesdeCierre($fechaCierre)
    {
        $fechaCierre = trim((string) $fechaCierre);
        if ($fechaCierre === '') {
            return '';
        }
        $dt = \DateTime::createFromFormat('Y-m-d', $fechaCierre);
        if ($dt === false) {
            return '';
        }

        return $dt->modify('+1 day')->format('Y-m-d');
    }

    /**
     * Formatea un DATE de bitácora a hora de México (el respaldo guarda SYSDATE en UTC;
     * producción suele coincidir con México). Usa el offset de SYSTIMESTAMP.
     */
    private function sqlHoraMexico($columna, $conSegundos = false)
    {
        $fmt = $conSegundos ? 'DD/MM/YYYY HH24:MI:SS' : 'DD/MM/YYYY HH24:MI';

        return "CASE WHEN {$columna} IS NULL THEN NULL ELSE TO_CHAR("
            . "FROM_TZ(CAST({$columna} AS TIMESTAMP), TO_CHAR(SYSTIMESTAMP, 'TZH:TZM')) "
            . "AT TIME ZONE 'America/Mexico_City', '{$fmt}') END";
    }

    /**
     * Indica si hay un proceso de cierre en ejecución (bitácora: cierre abierto, FIN IS NULL).
     * Primero COUNT(*) sobre BITACORA_CIERRE_DIARIO; si hay pendiente, se obtiene INICIO/USUARIO del último.
     *
     * @return array { INICIO, FECHA_CIERRE, USUARIO, SEGUNDOS } o vacío
     */
    public function validaCierreEnEjecucion()
    {
        return $this->sinSalida(function () {
            try {
                $db = new Database();
                if ($db->db_activa === null) {
                    return [];
                }

                // Solo el renglón del SP (ID_IMPORTACION). Un INSERT de PHP sin actividad no cuenta como proceso.
                $qryConCierrePendiente = <<<SQL
                SELECT COUNT(*) AS TOTAL
                FROM BITACORA_CIERRE_DIARIO
                WHERE FIN IS NULL
                  AND INICIO IS NOT NULL
                  AND ID_IMPORTACION IS NOT NULL
            SQL;

                $cnt = $db->queryOne($qryConCierrePendiente);
                $total = ($cnt !== false && isset($cnt['TOTAL'])) ? (int) $cnt['TOTAL'] : 0;
                if ($total === 0) {
                    return [];
                }

                $inicioMx = $this->sqlHoraMexico('INICIO', true);
                $qry = <<<SQL
                SELECT
                    TO_CHAR(FECHA_CALCULO, 'DD/MM/YYYY') AS FECHA_CIERRE,
                    {$inicioMx} AS INICIO,
                    USUARIO,
                    GREATEST(0, ROUND((SYSDATE - INICIO) * 86400)) AS SEGUNDOS
                FROM BITACORA_CIERRE_DIARIO
                WHERE FIN IS NULL
                  AND INICIO IS NOT NULL
                  AND ID_IMPORTACION IS NOT NULL
                ORDER BY INICIO ASC
                FETCH FIRST 1 ROW ONLY
            SQL;

                $r = $db->queryOne($qry);
                return $r ?: [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Tiempo estimado de cierre en minutos (promedio de los últimos 7 cierres exitosos).
     *
     * @return int Minutos
     */
    public function tiempoEstimado()
    {
        $qry = <<<SQL
            SELECT ROUND(AVG((CAST(FIN AS DATE) - CAST(INICIO AS DATE)) * 24 * 60), 0) AS ESTIMADO
            FROM (
                SELECT INICIO, FIN
                FROM BITACORA_CIERRE_DIARIO
                WHERE FIN IS NOT NULL AND INICIO IS NOT NULL AND EXITO = 1
                ORDER BY FIN DESC
                FETCH FIRST 7 ROWS ONLY
            )
        SQL;
        return $this->sinSalida(function () use ($qry) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry);
                return (int) ($r['ESTIMADO'] ?? 0);
            } catch (\Exception $e) {
                return 0;
            }
        });
    }

    /**
     * Últimos 7 cierres en bitácora (más recientes primero; incluye en proceso y finalizados).
     * Prefiere filas del SP (con métricas / ID_IMPORTACION) y omite candados PHP ya cerrados sin importación.
     *
     * Importante: ORDER BY debe usar columnas de tabla (alias b.), nunca el alias TO_CHAR(FECHA_CALCULO,'DD/MM/YYYY'):
     * ordenar el texto DD/MM/YYYY deja fuera fechas recientes (p. ej. agosto tras julio).
     *
     * @return array Lista de filas con FECHA_CALCULO, INICIO, FIN, USUARIO, EXITO, métricas
     */
    public function getUltimos7Cierres()
    {
        // Producción (SP): columnas de métricas e ID_IMPORTACION.
        $inicioMx = $this->sqlHoraMexico('b.INICIO');
        $finMx = $this->sqlHoraMexico('b.FIN');
        $qryConMetricas = <<<SQL
            SELECT * FROM (
                SELECT
                    TO_CHAR(b.FECHA_CALCULO, 'DD/MM/YYYY') AS FECHA_CALCULO,
                    TO_CHAR(b.FECHA_CALCULO, 'YYYY-MM-DD') AS FECHA_CIERRE_ISO,
                    {$inicioMx} AS INICIO,
                    {$finMx} AS FIN,
                    b.USUARIO,
                    NVL(b.EXITO, 0) AS EXITO,
                    CASE WHEN b.FIN IS NULL THEN 1 ELSE 0 END AS EN_PROCESO,
                    CASE WHEN b.FIN IS NULL OR NVL(b.EXITO, 0) = 1
                        THEN NVL(b.CIERRE_REGISTROS, 0) ELSE 0 END AS REGISTROS_PROCESADOS,
                    CASE WHEN b.FIN IS NULL OR NVL(b.EXITO, 0) = 1
                        THEN NVL(b.DEVENGO_REGISTROS, 0) ELSE 0 END AS CREDITOS_DEVENGO,
                    CASE WHEN b.FIN IS NULL OR NVL(b.EXITO, 0) = 1
                        THEN NVL(b.DEVENGO_MONTO, 0) ELSE 0 END AS DEVENGO_MONTO_NUM
                FROM BITACORA_CIERRE_DIARIO b
                WHERE b.ID_IMPORTACION IS NOT NULL
                ORDER BY b.INICIO DESC NULLS LAST, b.FECHA_CALCULO DESC
            ) WHERE ROWNUM <= 7
        SQL;

        $qryBasico = <<<SQL
            SELECT * FROM (
                SELECT
                    TO_CHAR(b.FECHA_CALCULO, 'DD/MM/YYYY') AS FECHA_CALCULO,
                    TO_CHAR(b.FECHA_CALCULO, 'YYYY-MM-DD') AS FECHA_CIERRE_ISO,
                    {$inicioMx} AS INICIO,
                    {$finMx} AS FIN,
                    b.USUARIO,
                    NVL(b.EXITO, 0) AS EXITO,
                    CASE WHEN b.FIN IS NULL THEN 1 ELSE 0 END AS EN_PROCESO
                FROM BITACORA_CIERRE_DIARIO b
                WHERE b.FIN IS NOT NULL
                   OR b.INICIO = (
                        SELECT MAX(s.INICIO)
                        FROM BITACORA_CIERRE_DIARIO s
                        WHERE s.FIN IS NULL
                          AND TRUNC(s.FECHA_CALCULO) = TRUNC(b.FECHA_CALCULO)
                   )
                ORDER BY b.INICIO DESC NULLS LAST, b.FECHA_CALCULO DESC
            ) WHERE ROWNUM <= 7
        SQL;

        return $this->sinSalida(function () use ($qryConMetricas, $qryBasico) {
            try {
                $db = new Database();
                $filas = $db->queryAll($qryConMetricas);
                return is_array($filas) ? $filas : [];
            } catch (\Exception $e) {
                try {
                    $db = new Database();
                    $filas = $db->queryAll($qryBasico);
                    return is_array($filas) ? $filas : [];
                } catch (\Exception $e2) {
                    return [];
                }
            }
        });
    }

    /** @deprecated Use getUltimos7Cierres() */
    public function getUltimos5Cierres()
    {
        return $this->getUltimos7Cierres();
    }

    /**
     * Última entrada de bitácora para la fecha de cierre indicada (la más reciente por INICIO).
     *
     * @param string $fechaCierre Y-m-d
     * @return array|null
     */
    public function getBitacoraCierrePorFecha($fechaCierre)
    {
        $fechaCierre = trim((string) $fechaCierre);
        if ($fechaCierre === '') {
            return null;
        }

        $inicioMx = $this->sqlHoraMexico('INICIO');
        $finMx = $this->sqlHoraMexico('FIN');
        $qry = <<<SQL
            SELECT
                USUARIO,
                {$inicioMx} AS INICIO,
                {$finMx} AS FIN,
                NVL(EXITO, 0) AS EXITO,
                CASE WHEN FIN IS NULL THEN 1 ELSE 0 END AS EN_PROCESO
            FROM BITACORA_CIERRE_DIARIO
            WHERE TRUNC(FECHA_CALCULO) = TO_DATE(:fecha, 'YYYY-MM-DD')
            ORDER BY INICIO DESC
            FETCH FIRST 1 ROW ONLY
        SQL;

        return $this->sinSalida(function () use ($qry, $fechaCierre) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry, ['fecha' => $fechaCierre]);

                return is_array($r) && !empty($r) ? $r : null;
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    /**
     * Comprueba si hubo un cierre exitoso del día anterior (bitácora), sin usar TBL_CIERRE_DIA.
     *
     * @param string $fechaCierre Y-m-d (fecha del cierre que se quiere ejecutar)
     * @return bool true si hay al menos un cierre finalizado con éxito para (fechaCierre - 1)
     */
    public function existeCierreDiaAnterior($fechaCierre)
    {
        $fechaAnterior = date('Y-m-d', strtotime($fechaCierre . ' -1 day'));
        $qry = <<<SQL
            SELECT COUNT(*) AS TOTAL
            FROM BITACORA_CIERRE_DIARIO
            WHERE TRUNC(FECHA_CALCULO) = TO_DATE(:fecha, 'YYYY-MM-DD')
              AND FIN IS NOT NULL
              AND NVL(EXITO, 0) = 1
        SQL;

        return $this->sinSalida(function () use ($qry, $fechaAnterior) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry, ['fecha' => $fechaAnterior]);

                return $r !== false && isset($r['TOTAL']) && (int) $r['TOTAL'] > 0;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Indica si el cierre del día ya se ejecutó con éxito.
     * Prioridad: bitácora EXITO=1. Un cierre fallido (EXITO=0) no bloquea reintento.
     * IMPORTACIONPAG solo como respaldo si no hay bitácora de esa fecha.
     *
     * @param string $fecha Y-m-d
     * @return bool
     */
    public function cierreYaEjecutado($fecha)
    {
        $qryExito = <<<SQL
            SELECT COUNT(*) AS TOTAL
            FROM BITACORA_CIERRE_DIARIO
            WHERE TRUNC(FECHA_CALCULO) = TO_DATE(:fecha, 'YYYY-MM-DD')
              AND FIN IS NOT NULL
              AND NVL(EXITO, 0) = 1
        SQL;

        $qryCualquiera = <<<SQL
            SELECT COUNT(*) AS TOTAL
            FROM BITACORA_CIERRE_DIARIO
            WHERE TRUNC(FECHA_CALCULO) = TO_DATE(:fecha, 'YYYY-MM-DD')
              AND FIN IS NOT NULL
        SQL;

        $qryImportacion = <<<SQL
            SELECT COUNT(*) AS TOTAL
            FROM IMPORTACIONPAG
            WHERE TRUNC(FEC_PAGO) = TO_DATE(:fecha, 'YYYY-MM-DD')
        SQL;

        return $this->sinSalida(function () use ($qryExito, $qryCualquiera, $qryImportacion, $fecha) {
            try {
                $db = new Database();
                $rOk = $db->queryOne($qryExito, ['fecha' => $fecha]);
                if ($rOk !== false && isset($rOk['TOTAL']) && (int) $rOk['TOTAL'] > 0) {
                    return true;
                }
                $rAny = $db->queryOne($qryCualquiera, ['fecha' => $fecha]);
                if ($rAny !== false && isset($rAny['TOTAL']) && (int) $rAny['TOTAL'] > 0) {
                    // Hubo intento(s) fallidos y ninguno exitoso: se puede reintentar.
                    return false;
                }
            } catch (\Exception $e) {
                // Continúa con IMPORTACIONPAG
            }

            try {
                $db = new Database();
                $r = $db->queryOne($qryImportacion, ['fecha' => $fecha]);

                return $r !== false && isset($r['TOTAL']) && (int) $r['TOTAL'] > 0;
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Resumen de cierre para correo: número de registros (consulta indicada por negocio).
     *
     * @param string $fechaCierre Y-m-d (fecha del cierre)
     * @return array [ 'registros' => int ]
     */
    public function getResumenCierre($fechaCierre)
    {
        $qry = <<<SQL
            SELECT COUNT(*) AS TOTAL
            FROM TBL_CIERRE_DIA TCD
            WHERE TCD.FECHA_CALC = TO_DATE(:fecha, 'YYYY-MM-DD')
            AND NOT EXISTS (
                SELECT 1 FROM PRN_LEGAL PL
                WHERE PL.CDGEM = TCD.CDGEM AND PL.CDGCLNS = TCD.CDGCLNS
                  AND PL.CICLO = TCD.CICLO AND PL.CLNS = TCD.CLNS
                  AND PL.TIPO IN ('C','Z') AND PL.ALTA < TCD.FECHA_CALC + 1
            )
        SQL;
        return $this->sinSalida(function () use ($qry, $fechaCierre) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry, ['fecha' => $fechaCierre]);
                return ['registros' => (int) ($r['TOTAL'] ?? 0)];
            } catch (\Exception $e) {
                return ['registros' => 0];
            }
        });
    }

    /**
     * Resumen devengo para correo y pantalla (DEVENGO_DIARIO con FECHA_CALC = fecha de cierre + 1 día).
     *
     * @param string $fechaCierre Y-m-d fecha del cierre en bitácora
     * @return array [ 'creditos' => int, 'monto' => float ]
     */
    public function getResumenDevengo($fechaCierre)
    {
        $fechaDevengo = $this->fechaCalculoDevengoDesdeCierre($fechaCierre);
        if ($fechaDevengo === '') {
            return ['creditos' => 0, 'monto' => 0];
        }

        $qry = <<<'SQL'
SELECT COUNT(*) AS CREDITOS, NVL(SUM(DEV_DIARIO), 0) AS MONTO
FROM DEVENGO_DIARIO
WHERE TRUNC(FECHA_CALC) = TO_DATE(:f1, 'YYYY-MM-DD')
SQL;

        return $this->sinSalida(function () use ($qry, $fechaDevengo) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry, ['f1' => $fechaDevengo]);

                return [
                    'creditos' => (int) ($r['CREDITOS'] ?? 0),
                    'monto' => round((float) ($r['MONTO'] ?? 0), 2),
                ];
            } catch (\Exception $e) {
                return ['creditos' => 0, 'monto' => 0];
            }
        });
    }

    /**
     * Obtiene resúmenes de cierre y devengo para un conjunto de fechas en solo 2 consultas.
     * Mantiene exactamente los mismos criterios de getResumenCierre() y getResumenDevengo().
     *
     * @param array $fechasIso Lista de fechas Y-m-d
     * @return array [
     *   'cierre' => [ 'YYYY-MM-DD' => int ],
     *   'devengo' => [ 'YYYY-MM-DD' => ['creditos' => int, 'monto' => float] ]
     * ]
     */
    public function getResumenesPorFechas(array $fechasIso)
    {
        $fechas = array_values(array_unique(array_filter(array_map('trim', $fechasIso), function ($f) {
            return $f !== '';
        })));

        if (empty($fechas)) {
            return ['cierre' => [], 'devengo' => []];
        }

        return $this->sinSalida(function () use ($fechas) {
            try {
                $db = new Database();
                $bindsCierre = [];
                $holdersCierre = [];
                $bindsDevengo = [];
                $holdersDevengo = [];
                $devengoFechaACierre = [];

                foreach ($fechas as $i => $fechaCierre) {
                    $fechaDevengo = $this->fechaCalculoDevengoDesdeCierre($fechaCierre);
                    if ($fechaDevengo === '') {
                        continue;
                    }
                    $kc = 'c' . $i;
                    $kd = 'd' . $i;
                    $holdersCierre[] = "TO_DATE(:$kc, 'YYYY-MM-DD')";
                    $bindsCierre[$kc] = $fechaCierre;
                    $holdersDevengo[] = "TO_DATE(:$kd, 'YYYY-MM-DD')";
                    $bindsDevengo[$kd] = $fechaDevengo;
                    $devengoFechaACierre[$fechaDevengo] = $fechaCierre;
                }

                if (empty($holdersCierre)) {
                    return ['cierre' => [], 'devengo' => []];
                }

                $inListCierre = implode(', ', $holdersCierre);
                $inListDevengo = implode(', ', $holdersDevengo);

                $qryCierre = <<<SQL
                    SELECT
                        TO_CHAR(TCD.FECHA_CALC, 'YYYY-MM-DD') AS FECHA,
                        COUNT(*) AS TOTAL
                    FROM TBL_CIERRE_DIA TCD
                    WHERE TCD.FECHA_CALC IN ($inListCierre)
                      AND NOT EXISTS (
                          SELECT 1 FROM PRN_LEGAL PL
                          WHERE PL.CDGEM = TCD.CDGEM AND PL.CDGCLNS = TCD.CDGCLNS
                            AND PL.CICLO = TCD.CICLO AND PL.CLNS = TCD.CLNS
                            AND PL.TIPO IN ('C','Z') AND PL.ALTA < TCD.FECHA_CALC + 1
                      )
                    GROUP BY TO_CHAR(TCD.FECHA_CALC, 'YYYY-MM-DD')
                SQL;

                $qryDevengo = <<<SQL
                    SELECT
                        TO_CHAR(TRUNC(FECHA_CALC), 'YYYY-MM-DD') AS FECHA_DEVENGO,
                        COUNT(*) AS CREDITOS,
                        NVL(SUM(DEV_DIARIO), 0) AS MONTO
                    FROM DEVENGO_DIARIO
                    WHERE TRUNC(FECHA_CALC) IN ($inListDevengo)
                    GROUP BY TRUNC(FECHA_CALC)
                SQL;

                $filasCierre = $db->queryAll($qryCierre, $bindsCierre);
                $filasDevengo = empty($holdersDevengo)
                    ? []
                    : $db->queryAll($qryDevengo, $bindsDevengo);

                $mapCierre = [];
                if (is_array($filasCierre)) {
                    foreach ($filasCierre as $r) {
                        $fecha = isset($r['FECHA']) ? (string) $r['FECHA'] : '';
                        if ($fecha === '') {
                            continue;
                        }
                        $mapCierre[$fecha] = (int) ($r['TOTAL'] ?? 0);
                    }
                }

                $mapDevengo = [];
                if (is_array($filasDevengo)) {
                    foreach ($filasDevengo as $r) {
                        $fechaDevengo = isset($r['FECHA_DEVENGO']) ? (string) $r['FECHA_DEVENGO'] : '';
                        if ($fechaDevengo === '') {
                            continue;
                        }
                        $fechaCierre = $devengoFechaACierre[$fechaDevengo] ?? '';
                        if ($fechaCierre === '') {
                            continue;
                        }
                        $mapDevengo[$fechaCierre] = [
                            'creditos' => (int) ($r['CREDITOS'] ?? 0),
                            'monto' => round((float) ($r['MONTO'] ?? 0), 2),
                        ];
                    }
                }

                return ['cierre' => $mapCierre, 'devengo' => $mapDevengo];
            } catch (\Exception $e) {
                return ['cierre' => [], 'devengo' => []];
            }
        });
    }

    /**
     * Obtiene el correo del oficial de cumplimiento desde PARAMETROS_PLD (como en VB6).
     * Se usa como destinatario del resumen de cierre cuando no está en modo solo desarrollo.
     *
     * @return array Lista de correos (puede ser uno o varios si el campo contiene comas)
     */
    public function getDestinatariosResumenCierreParametrosPld()
    {
        $qry = <<<SQL
            SELECT CORREO_OFICIAL
            FROM PARAMETROS_PLD
            WHERE CDGEM = 'EMPFIN' AND ESTATUS = 'A'
        SQL;
        return $this->sinSalida(function () use ($qry) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry);
                if (!$r || empty($r['CORREO_OFICIAL'])) {
                    return [];
                }
                $correo = trim((string) $r['CORREO_OFICIAL']);
                if ($correo === '') {
                    return [];
                }
                $lista = array_unique(array_map('trim', explode(',', $correo)));
                return array_values(array_filter($lista, function ($e) {
                    return $e !== '';
                }));
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Adquiere el candado de concurrencia: inserta bitácora abierta solo si no hay otra con FIN IS NULL.
     * Usa bloqueo exclusivo breve de la tabla para evitar dos inicios concurrentes (TOCTOU).
     *
     * @param string $fecha Y-m-d
     * @param string $usuario
     * @return bool true si se adquirió el candado
     */
    public function registrarInicio($fecha, $usuario)
    {
        try {
            $db = new Database();
            if ($db->db_activa === null) {
                return false;
            }
            $pdo = $db->db_activa;
            $pdo->beginTransaction();
            try {
                $pdo->exec('LOCK TABLE BITACORA_CIERRE_DIARIO IN EXCLUSIVE MODE');

                $cntStmt = $pdo->query(
                    'SELECT COUNT(*) AS TOTAL FROM BITACORA_CIERRE_DIARIO WHERE FIN IS NULL AND INICIO IS NOT NULL'
                );
                $cntRow = $cntStmt ? $cntStmt->fetch(\PDO::FETCH_ASSOC) : false;
                $total = ($cntRow && isset($cntRow['TOTAL'])) ? (int) $cntRow['TOTAL'] : 0;
                if ($total > 0) {
                    $pdo->rollBack();
                    return false;
                }

                $ins = $pdo->prepare(
                    "INSERT INTO BITACORA_CIERRE_DIARIO (FECHA_CALCULO, USUARIO) VALUES (TO_DATE(:fecha, 'YYYY-MM-DD'), :usuario)"
                );
                $ins->execute(['fecha' => $fecha, 'usuario' => $usuario]);
                $pdo->commit();
                return true;
            } catch (\Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                throw $e;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Marca el cierre como finalizado (FIN = SYSDATE, EXITO = 0|1).
     *
     * @param string $fecha Y-m-d
     * @param int $exito 1 = éxito, 0 = error
     * @return bool
     */
    public function registrarFin($fecha, $exito = 1)
    {
        $qry = "UPDATE BITACORA_CIERRE_DIARIO SET FIN = SYSDATE, EXITO = :exito WHERE FECHA_CALCULO = TO_DATE(:fecha, 'YYYY-MM-DD') AND FIN IS NULL";
        try {
            $db = new Database();
            $db->db_activa->prepare($qry)->execute(['fecha' => $fecha, 'exito' => (int) $exito]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cierra el candado PHP (filas abiertas sin ID_IMPORTACION del SP) para la fecha.
     * Si la columna ID_IMPORTACION no existe, cierra cualquier FIN IS NULL de esa fecha.
     *
     * @param string $fecha Y-m-d
     * @param int $exito 1 = éxito, 0 = error
     * @return bool
     */
    public function cerrarCandadoInicio($fecha, $exito = 1)
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return false;
        }

        $qryConImportacion = <<<SQL
            UPDATE BITACORA_CIERRE_DIARIO
            SET FIN = SYSDATE, EXITO = :exito
            WHERE TRUNC(FECHA_CALCULO) = TO_DATE(:fecha, 'YYYY-MM-DD')
              AND FIN IS NULL
              AND ID_IMPORTACION IS NULL
        SQL;

        try {
            $db = new Database();
            if ($db->db_activa === null) {
                return false;
            }
            $stmt = $db->db_activa->prepare($qryConImportacion);
            $stmt->execute(['fecha' => $fecha, 'exito' => (int) $exito]);
            return true;
        } catch (\Exception $e) {
            return $this->registrarFin($fecha, $exito);
        }
    }

    /**
     * Cierra solo candados PHP huérfanos (sin ID_IMPORTACION). No toca el renglón del SP.
     *
     * @param int $minutosAntiguedad Por defecto 15
     * @return int Filas actualizadas (0 si ninguna / error)
     */
    public function liberarCandadosPhpHuerfanos($minutosAntiguedad = 15)
    {
        $minutos = max(5, (int) $minutosAntiguedad);
        $qry = <<<SQL
            UPDATE BITACORA_CIERRE_DIARIO b
            SET b.FIN = SYSDATE, b.EXITO = 0
            WHERE b.FIN IS NULL
              AND b.ID_IMPORTACION IS NULL
              AND b.INICIO IS NOT NULL
              AND b.INICIO < SYSDATE - (:mins / 1440)
        SQL;

        try {
            $db = new Database();
            if ($db->db_activa === null) {
                return 0;
            }
            $stmt = $db->db_activa->prepare($qry);
            $stmt->execute(['mins' => $minutos]);
            return (int) $stmt->rowCount();
        } catch (\Exception $e) {
            // Esquema sin ID_IMPORTACION: cierra cualquier abierto viejo.
            try {
                $db = new Database();
                if ($db->db_activa === null) {
                    return 0;
                }
                $qryBasico = <<<SQL
                    UPDATE BITACORA_CIERRE_DIARIO
                    SET FIN = SYSDATE, EXITO = 0
                    WHERE FIN IS NULL
                      AND INICIO IS NOT NULL
                      AND INICIO < SYSDATE - (:mins / 1440)
                SQL;
                $stmt = $db->db_activa->prepare($qryBasico);
                $stmt->execute(['mins' => $minutos]);
                return (int) $stmt->rowCount();
            } catch (\Exception $e2) {
                return 0;
            }
        }
    }

    /**
     * Marca como finalizado (con error) el último registro con FIN IS NULL.
     * Útil cuando el Job falla por fecha inválida y hay que desbloquear la concurrencia.
     *
     * @return bool
     */
    public function registrarFinUltimoAbierto()
    {
        $qry = <<<SQL
            UPDATE BITACORA_CIERRE_DIARIO SET FIN = SYSDATE, EXITO = 0
            WHERE ROWID = (SELECT r FROM (SELECT ROWID r FROM BITACORA_CIERRE_DIARIO WHERE FIN IS NULL AND ROWNUM = 1))
        SQL;
        try {
            $db = new Database();
            $stmt = $db->db_activa->prepare($qry);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Invoca SP_PAGOS_CIERRE_DEVENGO (cierre unificado en BD).
     * Si $regenerar es true, elimina TBL_CIERRE_DIA del día y DEVENGO_DIARIO de (día+1)
     * para evitar ORA-00001 al volver a ejecutar el SP.
     *
     * @param string $fecha Y-m-d (fecha de cálculo / cierre)
     * @param string $usuario Usuario que ejecuta el proceso
     * @param bool $regenerar Si true, borra devengo y cierre de cartera del día y luego ejecuta el SP
     * @throws \Throwable
     */
    public function ejecutarSpPagosCierreDevengo($fecha, $usuario, $regenerar = false)
    {
        $db = new Database();
        if ($db->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }
        $pdo = $db->db_activa;
        $u = trim((string) $usuario);
        if ($u === '') {
            throw new \InvalidArgumentException('Se requiere usuario para ejecutar el cierre (SP_PAGOS_CIERRE_DEVENGO).');
        }
        $fecha = trim((string) $fecha);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            throw new \InvalidArgumentException('Fecha inválida para cierre (se espera YYYY-MM-DD).');
        }

        if ($regenerar) {
            $fechaDevengo = $this->fechaCalculoDevengoDesdeCierre($fecha);
            $sql = <<<PLSQL
BEGIN
  DELETE FROM DEVENGO_DIARIO d
  WHERE TRUNC(d.FECHA_CALC) = TO_DATE(:f1, 'YYYY-MM-DD');
  DELETE FROM TBL_CIERRE_DIA t
  WHERE TRUNC(t.FECHA_CALC) = TO_DATE(:f2, 'YYYY-MM-DD');
  SP_PAGOS_CIERRE_DEVENGO(TO_DATE(:f3, 'YYYY-MM-DD'), :usuario);
END;
PLSQL;
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['f1' => $fechaDevengo, 'f2' => $fecha, 'f3' => $fecha, 'usuario' => $u]);

            return;
        }

        $stmt = $pdo->prepare(
            'BEGIN SP_PAGOS_CIERRE_DEVENGO(TO_DATE(:fecha, \'YYYY-MM-DD\'), :usuario); END;'
        );
        $stmt->execute(['fecha' => $fecha, 'usuario' => $u]);
    }

    /**
     * Cuatro resúmenes exclusivos del día indicado (TRUNC(campo_fecha) = fecha).
     * Las claves y campos del array de respuesta son de negocio (sin nombres de tablas/columnas en JSON).
     *
     * @param string $fechaDesde Y-m-d (día único a consultar)
     * @return array{
     *   cobranza_del_dia: list<array{fecha: string, registros: int}>,
     *   cierre_de_cartera: list<array{fecha: string, registros: int}>,
     *   devengo_registrado: list<array{fecha: string, registros: int}>,
     *   depositos_cuenta: list<array{fecha: string, registros: int}>
     * }
     */
    public function getInformacionDiaResumenes($fechaDesde)
    {
        return $this->sinSalida(function () use ($fechaDesde) {
            $fechaDesde = trim((string) $fechaDesde);
            $vacio = [
                'cobranza_del_dia' => [],
                'cierre_de_cartera' => [],
                'devengo_registrado' => [],
                'depositos_cuenta' => [],
            ];
            if ($fechaDesde === '') {
                return $vacio;
            }
            $db = new Database();
            if ($db->db_activa === null) {
                return $vacio;
            }
            $pdo = $db->db_activa;
            $param = ['f1' => $fechaDesde];
            $fechaDevengo = $this->fechaCalculoDevengoDesdeCierre($fechaDesde);
            $paramDevengo = ['f1' => $fechaDevengo];

            $qPagosdia = <<<'SQL'
SELECT TO_CHAR(TRUNC(PGD.FECHA), 'DD/MM/YYYY') AS FECHA, COUNT(*) AS CNT
FROM PAGOSDIA PGD
WHERE TRUNC(PGD.FECHA) = TO_DATE(:f1, 'YYYY-MM-DD')
  AND PGD.TIPO IN ('P', 'X', 'G')
GROUP BY TRUNC(PGD.FECHA)
ORDER BY TRUNC(PGD.FECHA) DESC
SQL;

            $qTblCierre = <<<'SQL'
SELECT TO_CHAR(TRUNC(t.FECHA_CALC), 'DD/MM/YYYY') AS FECHA, COUNT(*) AS CNT
FROM TBL_CIERRE_DIA t
WHERE TRUNC(t.FECHA_CALC) = TO_DATE(:f1, 'YYYY-MM-DD')
GROUP BY TRUNC(t.FECHA_CALC)
ORDER BY TRUNC(t.FECHA_CALC) DESC
SQL;

            $qDevengo = <<<'SQL'
SELECT TO_CHAR(TRUNC(d.FECHA_CALC), 'DD/MM/YYYY') AS FECHA, COUNT(*) AS CNT
FROM DEVENGO_DIARIO d
WHERE TRUNC(d.FECHA_CALC) = TO_DATE(:f1, 'YYYY-MM-DD')
GROUP BY TRUNC(d.FECHA_CALC)
ORDER BY TRUNC(d.FECHA_CALC) DESC
SQL;

            $qMpPd = <<<'SQL'
SELECT TO_CHAR(TRUNC(m.FDEPOSITO), 'DD/MM/YYYY') AS FECHA, COUNT(*) AS CNT
FROM mp m
WHERE TRUNC(m.FDEPOSITO) = TO_DATE(:f1, 'YYYY-MM-DD')
  AND m.TIPO = 'PD'
GROUP BY TRUNC(m.FDEPOSITO)
ORDER BY TRUNC(m.FDEPOSITO) DESC
SQL;

            $normaliza = function (array $filas) {
                $out = [];
                foreach ($filas as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $fecha = isset($row['FECHA']) ? (string) $row['FECHA'] : (isset($row['fecha']) ? (string) $row['fecha'] : '');
                    $cnt = isset($row['CNT']) ? (int) $row['CNT'] : (isset($row['cnt']) ? (int) $row['cnt'] : 0);
                    $out[] = ['fecha' => $fecha, 'registros' => $cnt];
                }

                return $out;
            };

            try {
                $st = $pdo->prepare($qPagosdia);
                $st->execute($param);
                $cobranza = $normaliza($st->fetchAll(\PDO::FETCH_ASSOC));
            } catch (\Throwable $e) {
                $cobranza = [];
            }

            try {
                $st = $pdo->prepare($qTblCierre);
                $st->execute($param);
                $tblCierre = $normaliza($st->fetchAll(\PDO::FETCH_ASSOC));
            } catch (\Throwable $e) {
                $tblCierre = [];
            }

            try {
                if ($fechaDevengo === '') {
                    $devengo = [];
                } else {
                    $st = $pdo->prepare($qDevengo);
                    $st->execute($paramDevengo);
                    $devengo = $normaliza($st->fetchAll(\PDO::FETCH_ASSOC));
                }
            } catch (\Throwable $e) {
                $devengo = [];
            }

            try {
                $st = $pdo->prepare($qMpPd);
                $st->execute($param);
                $mpPd = $normaliza($st->fetchAll(\PDO::FETCH_ASSOC));
            } catch (\Throwable $e) {
                $mpPd = [];
            }

            return [
                'cobranza_del_dia' => $cobranza,
                'cierre_de_cartera' => $tblCierre,
                'devengo_registrado' => $devengo,
                'depositos_cuenta' => $mpPd,
            ];
        });
    }
}
