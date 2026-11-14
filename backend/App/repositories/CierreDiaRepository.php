<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\Database;

/**
 * Repository: acceso a datos del Cierre de Día.
 * Consultas a BITACORA_CIERRE_DIARIO (concurrencia, estatus e histórico), TBL_CIERRE_DIA y DEVENGO_DIARIO (resumen
 * del job de prueba de correo) y el resumen de movimientos del día (PAGOSDIA, PDI y RES_IMPOR) de la pantalla de cierre.
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
     * Formatea columna DATE de bitácora sin conversión de zona (hora tal como la guardó Oracle).
     * La sesión se fija a -06:00 en Core\Database al conectar.
     */
    private function sqlFechaHora($columna, $conSegundos = false)
    {
        $fmt = $conSegundos ? 'DD/MM/YYYY HH24:MI:SS' : 'DD/MM/YYYY HH24:MI';

        return "TO_CHAR({$columna}, '{$fmt}')";
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

                // Solo el renglón del SP (siempre trae ID_PROCESO; ID_IMPORTACION queda NULL en días sin pagos).
                // Un INSERT de PHP sin ID_PROCESO no cuenta como proceso.
                $qryConCierrePendiente = <<<SQL
                SELECT COUNT(*) AS TOTAL
                FROM BITACORA_CIERRE_DIARIO
                WHERE FIN IS NULL
                  AND INICIO IS NOT NULL
                  AND ID_PROCESO IS NOT NULL
            SQL;

                $cnt = $db->queryOne($qryConCierrePendiente);
                $total = ($cnt !== false && isset($cnt['TOTAL'])) ? (int) $cnt['TOTAL'] : 0;
                if ($total === 0) {
                    return [];
                }

                $inicioMx = $this->sqlFechaHora('INICIO', true);
                $qry = <<<SQL
                SELECT
                    TO_CHAR(FECHA_CALCULO, 'DD/MM/YYYY') AS FECHA_CIERRE,
                    TO_CHAR(FECHA_CALCULO, 'YYYY-MM-DD') AS FECHA_CIERRE_ISO,
                    {$inicioMx} AS INICIO,
                    USUARIO,
                    NVL(EXITO, 0) AS EXITO,
                    GREATEST(0, ROUND((SYSDATE - INICIO) * 86400)) AS SEGUNDOS
                FROM BITACORA_CIERRE_DIARIO
                WHERE FIN IS NULL
                  AND INICIO IS NOT NULL
                  AND ID_PROCESO IS NOT NULL
                ORDER BY INICIO DESC
                FETCH FIRST 1 ROW ONLY
            SQL;

                $r = $db->queryOne($qry);
                if (is_array($r) && (int) ($r['EXITO'] ?? 0) === 1) {
                    $iso = isset($r['FECHA_CIERRE_ISO']) ? trim((string) $r['FECHA_CIERRE_ISO']) : '';
                    if ($iso !== '') {
                        $this->sincronizarFinBitacoraSp($iso, 1);
                    }
                    return [];
                }
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
     * Cierres registrados en bitácora entre dos fechas: un renglón por fecha de cierre.
     * Si hay varias ejecuciones del mismo día se toma la más reciente del SP (ID_PROCESO); los candados PHP
     * (sin ID_PROCESO) solo se usan cuando no hay renglón del SP.
     *
     * @param string $fechaInicio Y-m-d
     * @param string $fechaFin Y-m-d
     * @return array Filas con FECHA_ISO, INICIO, FIN, USUARIO, EXITO, EN_PROCESO, métricas y MENSAJE
     */
    public function getHistoricoBitacora($fechaInicio, $fechaFin)
    {
        $inicioMx = $this->sqlFechaHora('b.INICIO');
        $finMx = $this->sqlFechaHora('b.FIN');
        $qry = <<<SQL
            SELECT
                FECHA_ISO,
                INICIO,
                FIN,
                USUARIO,
                EXITO,
                EN_PROCESO,
                CIERRE_REGISTROS,
                DEVENGO_REGISTROS,
                DEVENGO_MONTO,
                MENSAJE
            FROM (
                SELECT
                    TO_CHAR(TRUNC(b.FECHA_CALCULO), 'YYYY-MM-DD') AS FECHA_ISO,
                    {$inicioMx} AS INICIO,
                    {$finMx} AS FIN,
                    b.USUARIO,
                    NVL(b.EXITO, 0) AS EXITO,
                    CASE WHEN b.FIN IS NULL THEN 1 ELSE 0 END AS EN_PROCESO,
                    b.CIERRE_REGISTROS,
                    b.DEVENGO_REGISTROS,
                    b.DEVENGO_MONTO,
                    b.MENSAJE,
                    ROW_NUMBER() OVER (
                        PARTITION BY TRUNC(b.FECHA_CALCULO)
                        ORDER BY CASE WHEN b.ID_PROCESO IS NULL THEN 1 ELSE 0 END, b.INICIO DESC NULLS LAST
                    ) AS RN
                FROM BITACORA_CIERRE_DIARIO b
                WHERE b.FECHA_CALCULO >= TO_DATE(:inicio, 'YYYY-MM-DD')
                  AND b.FECHA_CALCULO < TO_DATE(:fin, 'YYYY-MM-DD') + 1
            )
            WHERE RN = 1
        SQL;

        return $this->sinSalida(function () use ($qry, $fechaInicio, $fechaFin) {
            try {
                $db = new Database();
                $filas = $db->queryAll($qry, ['inicio' => $fechaInicio, 'fin' => $fechaFin]);
                return is_array($filas) ? $filas : [];
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Resumen de movimientos del día por categoría: pagos (P, X), garantías (G) y ajustes (D, R) de PAGOSDIA,
     * pagos no identificados (PDI) e incidencias de importación (RES_IMPOR, cuenta del cierre).
     * Un movimiento de PAGOSDIA está aplicado cuando tiene ID_IMPORTACION; un no identificado cuando ya se
     * identificó (IP/IG); una incidencia cuando su pago no identificado ya se identificó.
     *
     * @param string $fecha Y-m-d
     * @return array [ categoria => ['total', 'monto', 'aplicados', 'montoAplicado'] ]
     */
    public function getResumenPagosDia($fecha)
    {
        $qryPagosDia = <<<SQL
            SELECT
                CASE WHEN PGD.TIPO IN ('P', 'X') THEN 'pagos' WHEN PGD.TIPO = 'G' THEN 'garantias' ELSE 'ajustes' END AS CATEGORIA,
                COUNT(*) AS TOTAL,
                NVL(SUM(PGD.MONTO), 0) AS MONTO,
                SUM(CASE WHEN PGD.ID_IMPORTACION IS NULL THEN 0 ELSE 1 END) AS APLICADOS,
                NVL(SUM(CASE WHEN PGD.ID_IMPORTACION IS NULL THEN 0 ELSE PGD.MONTO END), 0) AS MONTO_APLICADO
            FROM PAGOSDIA PGD
            WHERE PGD.CDGEM = 'EMPFIN'
              AND PGD.ESTATUS = 'A'
              AND PGD.TIPO IN ('P', 'X', 'G', 'D', 'R')
              AND PGD.MONTO != 0
              AND PGD.FECHA >= TO_DATE(:f1, 'YYYY-MM-DD')
              AND PGD.FECHA < TO_DATE(:f2, 'YYYY-MM-DD') + 1
            GROUP BY CASE WHEN PGD.TIPO IN ('P', 'X') THEN 'pagos' WHEN PGD.TIPO = 'G' THEN 'garantias' ELSE 'ajustes' END
        SQL;

        $qryNoIdentificados = <<<SQL
            SELECT
                'no_identificados' AS CATEGORIA,
                COUNT(*) AS TOTAL,
                NVL(SUM(P.CANTIDAD), 0) AS MONTO,
                NVL(SUM(CASE WHEN P.ESTATUS IN ('IP', 'IG') THEN 1 ELSE 0 END), 0) AS APLICADOS,
                NVL(SUM(CASE WHEN P.ESTATUS IN ('IP', 'IG') THEN P.CANTIDAD ELSE 0 END), 0) AS MONTO_APLICADO
            FROM PDI P
            WHERE P.CDGEM = 'EMPFIN'
              AND P.ESTATUS IN ('RE', 'IP', 'IG')
              AND P.FDEPOSITO >= TO_DATE(:f1, 'YYYY-MM-DD')
              AND P.FDEPOSITO < TO_DATE(:f2, 'YYYY-MM-DD') + 1
        SQL;

        $qryIncidencias = <<<SQL
            SELECT
                'incidencias' AS CATEGORIA,
                COUNT(*) AS TOTAL,
                NVL(SUM(MONTO), 0) AS MONTO,
                NVL(SUM(APLICADA), 0) AS APLICADOS,
                NVL(SUM(APLICADA * MONTO), 0) AS MONTO_APLICADO
            FROM (
                {$this->sqlIncidenciasImportacion()}
            )
        SQL;

        $resumen = [];
        foreach (['pagos', 'garantias', 'ajustes', 'no_identificados', 'incidencias'] as $categoria) {
            $resumen[$categoria] = ['total' => 0, 'monto' => 0.0, 'aplicados' => 0, 'montoAplicado' => 0.0];
        }

        $db = new Database();
        if ($db->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }

        $consultas = [
            [$qryPagosDia, ['f1' => $fecha, 'f2' => $fecha]],
            [$qryNoIdentificados, ['f1' => $fecha, 'f2' => $fecha]],
            [$qryIncidencias, ['fechaPago' => date('d/m/Y', strtotime($fecha)), 'fdep' => $fecha]],
        ];
        foreach ($consultas as $consulta) {
            $stmt = $db->db_activa->prepare($consulta[0]);
            $stmt->execute($consulta[1]);
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [] as $row) {
                $categoria = (string) ($row['CATEGORIA'] ?? '');
                if (!isset($resumen[$categoria])) {
                    continue;
                }
                $resumen[$categoria] = [
                    'total' => (int) ($row['TOTAL'] ?? 0),
                    'monto' => round((float) ($row['MONTO'] ?? 0), 2),
                    'aplicados' => (int) ($row['APLICADOS'] ?? 0),
                    'montoAplicado' => round((float) ($row['MONTO_APLICADO'] ?? 0), 2),
                ];
            }
        }

        return $resumen;
    }

    /**
     * Detalle de una categoría del resumen de pagos del día.
     *
     * @param string $fecha Y-m-d
     * @param string $categoria pagos|garantias|ajustes|no_identificados|incidencias
     * @return array
     */
    public function getDetallePagosDia($fecha, $categoria)
    {
        $tiposPorCategoria = [
            'pagos' => "'P', 'X'",
            'garantias' => "'G'",
            'ajustes' => "'D', 'R'",
        ];

        if (isset($tiposPorCategoria[$categoria])) {
            $qry = <<<SQL
                SELECT
                    PGD.CDGNS,
                    PGD.CICLO,
                    NVL(TRIM(PGD.NOMBRE), '') AS NOMBRE,
                    PGD.SECUENCIA,
                    PGD.TIPO,
                    TIPO_OPERACION(PGD.TIPO) AS OPERACION,
                    PGD.MONTO,
                    CASE WHEN PGD.ID_IMPORTACION IS NULL THEN 0 ELSE 1 END AS APLICADO
                FROM PAGOSDIA PGD
                WHERE PGD.CDGEM = 'EMPFIN'
                  AND PGD.ESTATUS = 'A'
                  AND PGD.TIPO IN ({$tiposPorCategoria[$categoria]})
                  AND PGD.MONTO != 0
                  AND PGD.FECHA >= TO_DATE(:f1, 'YYYY-MM-DD')
                  AND PGD.FECHA < TO_DATE(:f2, 'YYYY-MM-DD') + 1
                ORDER BY APLICADO, PGD.CDGNS, PGD.SECUENCIA
            SQL;
            $params = ['f1' => $fecha, 'f2' => $fecha];
        } elseif ($categoria === 'no_identificados') {
            $qry = <<<SQL
                SELECT
                    P.SECUENCIA,
                    P.REFERENCIA,
                    P.CDGCB,
                    P.CANTIDAD AS MONTO,
                    P.DESCRIPCION,
                    P.CDGCLNS,
                    P.CICLO,
                    P.CDGPE_IDEN,
                    CASE WHEN P.ESTATUS IN ('IP', 'IG') THEN 1 ELSE 0 END AS APLICADO
                FROM PDI P
                WHERE P.CDGEM = 'EMPFIN'
                  AND P.ESTATUS IN ('RE', 'IP', 'IG')
                  AND P.FDEPOSITO >= TO_DATE(:f1, 'YYYY-MM-DD')
                  AND P.FDEPOSITO < TO_DATE(:f2, 'YYYY-MM-DD') + 1
                ORDER BY APLICADO, P.SECUENCIA
            SQL;
            $params = ['f1' => $fecha, 'f2' => $fecha];
        } elseif ($categoria === 'incidencias') {
            $qry = <<<SQL
                SELECT REFERENCIA, MONTO, MSGRESUL, APLICADA AS APLICADO
                FROM (
                    {$this->sqlIncidenciasImportacion()}
                )
                ORDER BY APLICADO, REFERENCIA
            SQL;
            $params = ['fechaPago' => date('d/m/Y', strtotime($fecha)), 'fdep' => $fecha];
        } else {
            return [];
        }

        $db = new Database();
        if ($db->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }

        $stmt = $db->db_activa->prepare($qry);
        $stmt->execute($params);
        $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return is_array($filas) ? $filas : [];
    }

    /**
     * Incidencias de importación del día (RES_IMPOR de la cuenta del cierre con código distinto de éxito).
     * Una incidencia se da por aplicada cuando su pago no identificado ya se identificó en PDI
     * (se relaciona por fecha, referencia, monto y cuenta: SECUEPDI no siempre se registra).
     * Parámetros: :fechaPago (DD/MM/YYYY) y :fdep (YYYY-MM-DD).
     *
     * @return string
     */
    private function sqlIncidenciasImportacion()
    {
        return <<<SQL
            SELECT
                RI.REFERENCIA,
                RI.MONTO,
                RI.MSGRESUL,
                CASE WHEN EXISTS (
                    SELECT 1
                    FROM PDI P
                    WHERE P.CDGEM = 'EMPFIN'
                      AND P.FDEPOSITO = TO_DATE(:fdep, 'YYYY-MM-DD')
                      AND P.REFERENCIA = RI.REFERENCIA
                      AND P.CANTIDAD = RI.MONTO
                      AND P.CDGCB = RI.CTABANCARIA
                      AND P.ESTATUS IN ('IP', 'IG')
                ) THEN 1 ELSE 0 END AS APLICADA
            FROM RES_IMPOR RI
            WHERE RI.CTABANCARIA = '02'
              AND RI.FECHAPAGO = :fechaPago
              AND RI.VALIDACION NOT IN (0, 1, 6, 8, 9)
        SQL;
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

        $inicioMx = $this->sqlFechaHora('b.INICIO');
        $finMx = $this->sqlFechaHora('b.FIN');
        $qry = <<<SQL
            SELECT
                b.USUARIO,
                {$inicioMx} AS INICIO,
                {$finMx} AS FIN,
                NVL(b.EXITO, 0) AS EXITO,
                CASE WHEN b.FIN IS NULL THEN 1 ELSE 0 END AS EN_PROCESO
            FROM BITACORA_CIERRE_DIARIO b
            WHERE TRUNC(b.FECHA_CALCULO) = TO_DATE(:fecha, 'YYYY-MM-DD')
            ORDER BY b.INICIO DESC NULLS LAST
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
     * ID_PROCESO del cierre concluido más reciente de la fecha (el que usa el Job para armar el correo).
     *
     * @param string $fechaCierre Y-m-d
     * @return string|null
     */
    public function getIdProcesoCierre($fechaCierre)
    {
        $qry = <<<SQL
            SELECT b.ID_PROCESO
            FROM BITACORA_CIERRE_DIARIO b
            WHERE TRUNC(b.FECHA_CALCULO) = TO_DATE(:fecha, 'YYYY-MM-DD')
              AND b.FIN IS NOT NULL
              AND b.ID_PROCESO IS NOT NULL
            ORDER BY b.INICIO DESC NULLS LAST
            FETCH FIRST 1 ROW ONLY
        SQL;

        return $this->sinSalida(function () use ($qry, $fechaCierre) {
            try {
                $db = new Database();
                $r = $db->queryOne($qry, ['fecha' => $fechaCierre]);

                return is_array($r) && !empty($r['ID_PROCESO']) ? (string) $r['ID_PROCESO'] : null;
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    /**
     * Estatus del renglón que el SP escribe en bitácora para la fecha; el candado PHP
     * (sin ID_PROCESO) no cuenta como ejecución del procedimiento.
     *
     * @param string $fechaCierre Y-m-d
     * @return array|null { USUARIO, INICIO, FIN, EXITO, EN_PROCESO } o null si el SP no dejó registro
     */
    public function getEstatusSpPorFecha($fechaCierre)
    {
        $fechaCierre = trim((string) $fechaCierre);
        if ($fechaCierre === '') {
            return null;
        }

        // ORDER BY debe usar la columna DATE (b.INICIO), nunca el alias TO_CHAR DD/MM:
        // ordenar el texto deja fuera fechas recientes (p. ej. 20/08 detrás de 24/07).
        $inicioMx = $this->sqlFechaHora('b.INICIO', true);
        $finMx = $this->sqlFechaHora('b.FIN', true);
        $qry = <<<SQL
            SELECT
                b.USUARIO,
                {$inicioMx} AS INICIO,
                {$finMx} AS FIN,
                NVL(b.EXITO, 0) AS EXITO,
                CASE WHEN b.FIN IS NULL THEN 1 ELSE 0 END AS EN_PROCESO
            FROM BITACORA_CIERRE_DIARIO b
            WHERE TRUNC(b.FECHA_CALCULO) = TO_DATE(:fecha, 'YYYY-MM-DD')
              AND b.ID_PROCESO IS NOT NULL
            ORDER BY b.INICIO DESC NULLS LAST
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
     * Cierra el renglón del SP si terminó (EXITO definido) pero FIN quedó NULL.
     * Respaldo cuando el SP concluye la lógica pero no actualiza FIN en bitácora.
     *
     * @return bool true si se actualizó al menos una fila
     */
    public function sincronizarFinBitacoraSp($fecha, $exito = 1)
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return false;
        }

        $qry = <<<SQL
            UPDATE BITACORA_CIERRE_DIARIO b
            SET b.FIN = SYSDATE, b.EXITO = :exito
            WHERE TRUNC(b.FECHA_CALCULO) = TO_DATE(:fecha, 'YYYY-MM-DD')
              AND b.ID_PROCESO IS NOT NULL
              AND b.FIN IS NULL
              AND b.INICIO IS NOT NULL
        SQL;

        try {
            $db = new Database();
            if ($db->db_activa === null) {
                return false;
            }
            $stmt = $db->db_activa->prepare($qry);
            $stmt->execute(['fecha' => $fecha, 'exito' => (int) $exito]);

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cierra solo candados PHP huérfanos (sin ID_PROCESO). No toca el renglón del SP.
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
              AND b.ID_PROCESO IS NULL
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
            // Esquema sin ID_PROCESO: cierra cualquier abierto viejo.
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
}
