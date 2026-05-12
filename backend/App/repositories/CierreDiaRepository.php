<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\Database;

/**
 * Repository: acceso a datos del Cierre de Día.
 * Consultas a BITACORA_CIERRE_DIARIO, TBL_CIERRE_DIA, DEVENGO_DIARIO y ejecución de SP_PAGOS_CIERRE_DEVENGO.
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
     * Indica si hay un proceso de cierre en ejecución (registro con FIN IS NULL).
     *
     * @return array { INICIO, USUARIO } o vacío
     */
    public function validaCierreEnEjecucion()
    {
        return $this->sinSalida(function () {
            $db = new Database();
            if ($db->db_activa === null) {
                return [];
            }

            // Limpieza preventiva: si hay EXITO definido pero FIN sigue null,
            // entonces el registro está inconsistente. Marcamos FIN para evitar falsos positivos.
            try {
                $db->db_activa
                    ->prepare("UPDATE BITACORA_CIERRE_DIARIO SET FIN = SYSDATE WHERE FIN IS NULL AND EXITO IS NOT NULL")
                    ->execute();
            } catch (\Exception $e) {
                // Si falla la limpieza, seguimos con la validación por estado/consistencia.
            }

            // Proceso activo (equivalente a EN_PROCESO):
            // - fecha_inicio presente (INICIO)
            // - fecha_fin ausente (FIN IS NULL)
            // - estatus EN_PROCESO: en este esquema equivale a EXITO IS NULL
            // - consistencia con la tabla de cierre: descartamos si la fecha ya fue liquidada (FECHA_LIQUIDA no es null)
            $qryConCierrePendiente = <<<SQL
                SELECT
                    TO_CHAR(b.INICIO, 'DD/MM/YYYY HH24:MI:SS') AS INICIO,
                    TO_CHAR(b.FECHA_CALCULO, 'DD/MM/YYYY') AS FECHA_CIERRE,
                    b.USUARIO
                FROM BITACORA_CIERRE_DIARIO b
                WHERE b.FIN IS NULL
                  AND b.INICIO IS NOT NULL
                  AND b.EXITO IS NULL
                  AND NOT EXISTS (
                      SELECT 1
                      FROM TBL_CIERRE_DIA tcd
                      WHERE tcd.FECHA_CALC = TRUNC(b.FECHA_CALCULO)
                        AND tcd.FECHA_LIQUIDA IS NOT NULL
                  )
                ORDER BY b.INICIO DESC
                FETCH FIRST 1 ROW ONLY
            SQL;

            // Fallback por compatibilidad: si la tabla no permite consultar FECHA_LIQUIDA (o falla),
            // al menos aplicamos el filtro estricto de FIN/INICIO/EXITO.
            $qryFallback = <<<SQL
                SELECT
                    TO_CHAR(INICIO, 'DD/MM/YYYY HH24:MI:SS') AS INICIO,
                    TO_CHAR(FECHA_CALCULO, 'DD/MM/YYYY') AS FECHA_CIERRE,
                    USUARIO
                FROM BITACORA_CIERRE_DIARIO
                WHERE FIN IS NULL
                  AND INICIO IS NOT NULL
                  AND EXITO IS NULL
                ORDER BY INICIO DESC
                FETCH FIRST 1 ROW ONLY
            SQL;

            $r = $db->queryOne($qryConCierrePendiente);
            if ($r === false) {
                $r = $db->queryOne($qryFallback);
            }
            return $r ?: [];
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
     * Últimos 5 cierres finalizados para mostrar en pantalla.
     *
     * @return array Lista de filas con FECHA_CALCULO, INICIO, FIN, USUARIO, EXITO
     */
    public function getUltimos5Cierres()
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(FECHA_CALCULO, 'DD/MM/YYYY') AS FECHA_CALCULO,
                TO_CHAR(FECHA_CALCULO, 'YYYY-MM-DD') AS FECHA_CIERRE_ISO,
                TO_CHAR(INICIO, 'DD/MM/YYYY HH24:MI') AS INICIO,
                TO_CHAR(FIN, 'DD/MM/YYYY HH24:MI') AS FIN,
                USUARIO,
                NVL(EXITO, 0) AS EXITO
            FROM BITACORA_CIERRE_DIARIO
            WHERE FIN IS NOT NULL
            ORDER BY FIN DESC
            FETCH FIRST 5 ROWS ONLY
        SQL;
        return $this->sinSalida(function () use ($qry) {
            try {
                $db = new Database();
                $filas = $db->queryAll($qry);
                return is_array($filas) ? $filas : [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Comprueba si existe cierre del día anterior (como en VB6). Sin esto no se puede ejecutar el cierre.
     *
     * @param string $fechaCierre Y-m-d (fecha del cierre que se quiere ejecutar)
     * @return bool true si hay al menos un registro para (fechaCierre - 1)
     */
    public function existeCierreDiaAnterior($fechaCierre)
    {
        $fechaAnterior = date('Y-m-d', strtotime($fechaCierre . ' -1 day'));
        $qry = <<<SQL
            SELECT COUNT(*) AS TOTAL
            FROM TBL_CIERRE_DIA
            WHERE FECHA_LIQUIDA IS NULL AND FECHA_CALC = TO_DATE(:fecha, 'YYYY-MM-DD')
        SQL;
        $ok = $this->sinSalida(function () use ($qry, $fechaAnterior) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry, ['fecha' => $fechaAnterior]);
                if ($r !== false && isset($r['TOTAL'])) {
                    return (int) $r['TOTAL'] > 0;
                }
            } catch (\Exception $e) {
            }
            $qryFallback = "SELECT COUNT(*) AS TOTAL FROM TBL_CIERRE_DIA WHERE FECHA_CALC = TO_DATE(:fecha, 'YYYY-MM-DD')";
            try {
                $db = new Database();
                $r = $db->queryOne($qryFallback, ['fecha' => $fechaAnterior]);
                return $r !== false && isset($r['TOTAL']) && (int) $r['TOTAL'] > 0;
            } catch (\Exception $e) {
                return false;
            }
        });
        return $ok;
    }

    /**
     * Indica si ya existe cierre para la fecha (criterio VB6: TBL_CIERRE_DIA con FECHA_LIQUIDA IS NULL).
     * Si la columna FECHA_LIQUIDA no existe, se usa fallback por FECHA_CALC.
     *
     * @param string $fecha Y-m-d
     * @return bool
     */
    public function cierreYaEjecutado($fecha)
    {
        $qry = <<<SQL
            SELECT COUNT(*) AS TOTAL
            FROM TBL_CIERRE_DIA
            WHERE FECHA_LIQUIDA IS NULL AND FECHA_CALC = TO_DATE(:fecha, 'YYYY-MM-DD')
        SQL;
        return $this->sinSalida(function () use ($qry, $fecha) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry, ['fecha' => $fecha]);
                if ($r !== false && isset($r['TOTAL'])) {
                    return (int) $r['TOTAL'] > 0;
                }
            } catch (\Exception $e) {
                // FECHA_LIQUIDA puede no existir en algún esquema
            }
            $qryFallback = "SELECT COUNT(*) AS TOTAL FROM TBL_CIERRE_DIA WHERE FECHA_CALC = TO_DATE(:fecha, 'YYYY-MM-DD')";
            try {
                $db = new Database();
                $r = $db->queryOne($qryFallback, ['fecha' => $fecha]);
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
     * Resumen devengo para correo y pantalla: mismo criterio que validación manual en BD.
     * SELECT COUNT(*), SUM(DEV_DIARIO) FROM DEVENGO_DIARIO WHERE TRUNC(FECHA_CALC) = TO_DATE(:fecha, ...)
     *
     * @param string $fechaDevengo Y-m-d (fecha calendario de FECHA_CALC; alinear con fecha de cierre en bitácora)
     * @return array [ 'creditos' => int, 'monto' => float ]
     */
    public function getResumenDevengo($fechaDevengo)
    {
        $qry = <<<SQL
            SELECT COUNT(*) AS CREDITOS, NVL(SUM(DEV_DIARIO), 0) AS MONTO
            FROM DEVENGO_DIARIO
            WHERE TRUNC(FECHA_CALC) = TO_DATE(:fecha, 'YYYY-MM-DD')
        SQL;
        return $this->sinSalida(function () use ($qry, $fechaDevengo) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry, ['fecha' => $fechaDevengo]);
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
                $binds = [];
                $holders = [];
                foreach ($fechas as $i => $fecha) {
                    $k = 'f' . $i;
                    $holders[] = "TO_DATE(:$k, 'YYYY-MM-DD')";
                    $binds[$k] = $fecha;
                }
                $inList = implode(', ', $holders);

                $qryCierre = <<<SQL
                    SELECT
                        TO_CHAR(TCD.FECHA_CALC, 'YYYY-MM-DD') AS FECHA,
                        COUNT(*) AS TOTAL
                    FROM TBL_CIERRE_DIA TCD
                    WHERE TCD.FECHA_CALC IN ($inList)
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
                        TO_CHAR(TRUNC(FECHA_CALC), 'YYYY-MM-DD') AS FECHA,
                        COUNT(*) AS CREDITOS,
                        NVL(SUM(DEV_DIARIO), 0) AS MONTO
                    FROM DEVENGO_DIARIO
                    WHERE TRUNC(FECHA_CALC) IN ($inList)
                    GROUP BY TO_CHAR(TRUNC(FECHA_CALC), 'YYYY-MM-DD')
                SQL;

                $filasCierre = $db->queryAll($qryCierre, $binds);
                $filasDevengo = $db->queryAll($qryDevengo, $binds);

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
                        $fecha = isset($r['FECHA']) ? (string) $r['FECHA'] : '';
                        if ($fecha === '') {
                            continue;
                        }
                        $mapDevengo[$fecha] = [
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
                return array_values(array_filter($lista, function ($e) { return $e !== ''; }));
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Registra el inicio del cierre en la bitácora (FIN = NULL hasta que finalice).
     *
     * @param string $fecha Y-m-d
     * @param string $usuario
     * @return bool
     */
    public function registrarInicio($fecha, $usuario)
    {
        $qry = "INSERT INTO BITACORA_CIERRE_DIARIO (FECHA_CALCULO, USUARIO) VALUES (TO_DATE(:fecha, 'YYYY-MM-DD'), :usuario)";
        try {
            $db = new Database();
            $db->insertar($qry, ['fecha' => $fecha, 'usuario' => $usuario]);
            return true;
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
     * Proceso unificado de cierre: importación/aplicación de pagos del día, cierre cartera y devengo (BD).
     *
     * @param string $fecha Y-m-d (fecha de cálculo / pagos a procesar)
     * @param string $usuario Usuario que ejecuta el proceso
     * @throws \Throwable
     */
    public function ejecutarSpPagosCierreDevengo($fecha, $usuario)
    {
        $db = new Database();
        if ($db->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }
        $u = trim((string) $usuario);
        if ($u === '') {
            $u = 'SYSTEM';
        }
        $stmt = $db->db_activa->prepare(
            'BEGIN SP_PAGOS_CIERRE_DEVENGO(TO_DATE(:fecha, \'YYYY-MM-DD\'), :usuario); END;'
        );
        $stmt->execute(['fecha' => $fecha, 'usuario' => $u]);
    }

    /**
     * Cuatro resúmenes exclusivos del día indicado (TRUNC(campo_fecha) = fecha).
     *
     * @param string $fechaDesde Y-m-d (día único a consultar)
     * @return array{
     *   pagosdia: list<array{FECHA: string, CNT: int}>,
     *   tbl_cierre_dia: list<array{FECHA_CALC: string, CNT: int}>,
     *   devengo_diario: list<array{FECHA_CALC: string, CNT: int}>,
     *   mp_pd: list<array{FDEPOSITO: string, CNT: int}>
     * }
     */
    public function getInformacionDiaResumenes($fechaDesde)
    {
        return $this->sinSalida(function () use ($fechaDesde) {
            $fechaDesde = trim((string) $fechaDesde);
            $vacio = [
                'pagosdia' => [],
                'tbl_cierre_dia' => [],
                'devengo_diario' => [],
                'mp_pd' => [],
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

            $qPagosdia = <<<'SQL'
SELECT TO_CHAR(TRUNC(PGD.FECHA), 'DD/MM/YYYY') AS FECHA, COUNT(*) AS CNT
FROM PAGOSDIA PGD
WHERE TRUNC(PGD.FECHA) = TO_DATE(:f1, 'YYYY-MM-DD')
  AND PGD.TIPO IN ('P', 'X', 'G')
GROUP BY TRUNC(PGD.FECHA)
ORDER BY TRUNC(PGD.FECHA) DESC
SQL;

            $qTblCierre = <<<'SQL'
SELECT TO_CHAR(TRUNC(t.FECHA_CALC), 'DD/MM/YYYY') AS FECHA_CALC, COUNT(*) AS CNT
FROM TBL_CIERRE_DIA t
WHERE TRUNC(t.FECHA_CALC) = TO_DATE(:f1, 'YYYY-MM-DD')
GROUP BY TRUNC(t.FECHA_CALC)
ORDER BY TRUNC(t.FECHA_CALC) DESC
SQL;

            $qDevengo = <<<'SQL'
SELECT TO_CHAR(TRUNC(d.FECHA_CALC), 'DD/MM/YYYY') AS FECHA_CALC, COUNT(*) AS CNT
FROM DEVENGO_DIARIO d
WHERE TRUNC(d.FECHA_CALC) = TO_DATE(:f1, 'YYYY-MM-DD')
GROUP BY TRUNC(d.FECHA_CALC)
ORDER BY TRUNC(d.FECHA_CALC) DESC
SQL;

            $qMpPd = <<<'SQL'
SELECT TO_CHAR(TRUNC(m.FDEPOSITO), 'DD/MM/YYYY') AS FDEPOSITO, COUNT(*) AS CNT
FROM mp m
WHERE TRUNC(m.FDEPOSITO) = TO_DATE(:f1, 'YYYY-MM-DD')
  AND m.TIPO = 'PD'
GROUP BY TRUNC(m.FDEPOSITO)
ORDER BY TRUNC(m.FDEPOSITO) DESC
SQL;

            $normaliza = function (array $filas, $claveFecha) {
                $out = [];
                foreach ($filas as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $fecha = isset($row[$claveFecha]) ? (string) $row[$claveFecha] : '';
                    $cnt = isset($row['CNT']) ? (int) $row['CNT'] : (isset($row['cnt']) ? (int) $row['cnt'] : 0);
                    $out[] = [$claveFecha => $fecha, 'CNT' => $cnt];
                }

                return $out;
            };

            try {
                $st = $pdo->prepare($qPagosdia);
                $st->execute($param);
                $pagosdia = $normaliza($st->fetchAll(\PDO::FETCH_ASSOC), 'FECHA');
            } catch (\Throwable $e) {
                $pagosdia = [];
            }

            try {
                $st = $pdo->prepare($qTblCierre);
                $st->execute($param);
                $tblCierre = $normaliza($st->fetchAll(\PDO::FETCH_ASSOC), 'FECHA_CALC');
            } catch (\Throwable $e) {
                $tblCierre = [];
            }

            try {
                $st = $pdo->prepare($qDevengo);
                $st->execute($param);
                $devengo = $normaliza($st->fetchAll(\PDO::FETCH_ASSOC), 'FECHA_CALC');
            } catch (\Throwable $e) {
                $devengo = [];
            }

            try {
                $st = $pdo->prepare($qMpPd);
                $st->execute($param);
                $mpPd = $normaliza($st->fetchAll(\PDO::FETCH_ASSOC), 'FDEPOSITO');
            } catch (\Throwable $e) {
                $mpPd = [];
            }

            return [
                'pagosdia' => $pagosdia,
                'tbl_cierre_dia' => $tblCierre,
                'devengo_diario' => $devengo,
                'mp_pd' => $mpPd,
            ];
        });
    }

}
