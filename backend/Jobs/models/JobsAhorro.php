<?php

namespace Jobs\models;

include_once dirname(__DIR__) . "\..\Core\Model.php";
include_once dirname(__DIR__) . "\..\Core\Database.php";

use Core\Model;
use Core\Database;

class JobsAhorro extends Model
{
    public static function GetCuentasAhorro($fecha)
    {
        $qry = <<<SQL
            WITH MOVIMIENTOS AS (
                SELECT CDGNS
                    ,FECHA + 1 AS FECHA
                    ,'D' AS TIPO
                    ,MONTO
                FROM PAGOSDIA
                WHERE ESTATUS = 'A'
                    AND TIPO IN (
                        'B'
                        ,'F'
                        ,'E'
                    )
                UNION
                SELECT CDGNS
                    ,FECHA_ENTREGA_REAL + 1
                    ,'R' AS TIPO
                    ,- CANT_SOLICITADA
                FROM RETIROS_AHORRO
                WHERE ESTATUS = 'E'
                    AND FECHA_ENTREGA_REAL IS NOT NULL
                    AND CANT_SOLICITADA > MONTO_DEVENGO
                UNION
                SELECT CDGNS
                    ,FECHA_FIN + 1
                    ,'I' AS TIPO
                    ,MONTO
                FROM INTERES_AHORRO
                WHERE ESTATUS = 'A'
            ),
            MOVIMIENTOS_ACUMULADOS AS (
                SELECT CDGNS
                    ,TIPO
                    ,MONTO
                    ,FECHA
                    ,SUM(MONTO) OVER (
                        PARTITION BY CDGNS ORDER BY FECHA
                        ) AS ACUMULADO
                FROM MOVIMIENTOS
                ORDER BY CDGNS
                    ,FECHA
            ),
            FECHAS_INICIALES AS (
                SELECT MA.CDGNS
                    ,MIN(CASE 
                            WHEN MA.TIPO = 'D'
                                THEN MA.FECHA
                            END) AS PRIMER_DEPOSITO
                    ,MAX(CASE 
                            WHEN MA.TIPO = 'R'
                                THEN MA.FECHA
                            END) AS ULTIMO_RETIRO
                    ,MAX(CASE 
                            WHEN MA.TIPO = 'I'
                                THEN MA.FECHA
                            END) AS ULTIMO_INTERES
                    ,MAX(CASE 
                            WHEN TIPO = 'R'
                                AND ACUMULADO = 0
                                THEN FECHA
                            END) AS ULTIMO_RETIRO_TOTAL
                FROM MOVIMIENTOS_ACUMULADOS MA
                GROUP BY CDGNS
            ),
            FECHAS_FINALES AS (
                SELECT
                    FI.CDGNS
                    , (
                            SELECT MIN(M.FECHA)
                            FROM MOVIMIENTOS_ACUMULADOS M
                            WHERE M.CDGNS = FI.CDGNS
                            AND M.TIPO = 'D'
                            AND M.FECHA > FI.ULTIMO_RETIRO_TOTAL
                            ) AS REINICIO
                    , GREATEST(
                        NVL((
                            SELECT MIN(M.FECHA)
                            FROM MOVIMIENTOS_ACUMULADOS M
                            WHERE M.CDGNS = FI.CDGNS
                            AND M.TIPO = 'D'
                            AND M.FECHA > FI.ULTIMO_RETIRO_TOTAL
                            ),
                            FI.PRIMER_DEPOSITO
                        )
                        , NVL(FI.ULTIMO_RETIRO, FI.PRIMER_DEPOSITO)
                        , NVL(FI.ULTIMO_INTERES, FI.PRIMER_DEPOSITO)
                    ) AS INICIO
                FROM FECHAS_INICIALES FI
            )
            SELECT CA.CDGNS
                ,CA.TASA_ANUAL AS TASA
                , FI.PRIMER_DEPOSITO
                , FI.ULTIMO_RETIRO
                , FI.ULTIMO_INTERES
                , FF.REINICIO
                , TO_CHAR(FF.INICIO, 'YYYY-MM-DD') AS INICIO
                , TO_CHAR(ADD_MONTHS(FF.INICIO - 1, 12), 'YYYY-MM-DD') AS FIN
                , ADD_MONTHS(FF.INICIO, 12) - FF.INICIO AS DIAS
            FROM CONTRATOS_AHORRO CA
            INNER JOIN FECHAS_INICIALES FI ON FI.CDGNS = CA.CDGNS
            INNER JOIN FECHAS_FINALES FF ON FF.CDGNS = CA.CDGNS
            WHERE ADD_MONTHS(FF.INICIO - 1, 12) <= TO_DATE(:fecha, 'YYYY-MM-DD')
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry, ["fecha" => $fecha]);
            if ($res === false) return self::Responde(false, "Error al obtener las cuentas de ahorro", null, "Error en la consulta de cuentas de ahorro");
            return self::Responde(true, "Cuentas de ahorro obtenidas correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener las cuentas de ahorro", null, $e->getMessage());
        }
    }

    public static function RegistraDevengos($datos)
    {
        $qryElimina = <<<SQL
            DELETE FROM
                DEVENGO_AHORRO
            WHERE
                CONTRATO = :cdgns
                AND ID_INTERES IS NULL
                AND FECHA >= TO_DATE(:desde, 'YYYY-MM-DD')
        SQL;

        $qryDevengo = <<<SQL
            INSERT INTO DEVENGO_AHORRO (
                CONTRATO,
                SALDO_CIERRE,
                FECHA,
                DEVENGO,
                TASA
            )
            WITH DIAS AS (
                SELECT TO_DATE(:desde, 'YYYY-MM-DD') + LEVEL - 1 AS FECHA
                FROM DUAL
                CONNECT BY LEVEL <= TO_DATE(:hasta, 'YYYY-MM-DD') - TO_DATE(:desde, 'YYYY-MM-DD') + 1
            )
            , MOVIMIENTOS AS (
                SELECT TRUNC(PD.FECHA) AS FECHA
                    , DECODE(PD.TIPO, 'A', -PD.MONTO, PD.MONTO) AS MONTO
                FROM PAGOSDIA PD
                WHERE PD.CDGNS = :cdgns
                    AND PD.ESTATUS = 'A'
                    AND PD.TIPO IN ('B', 'F', 'E', 'A')
                UNION ALL
                SELECT TRUNC(NVL(RA.FECHA_CREACION, RA.FECHA_SOLICITUD))
                    , -RA.CANT_SOLICITADA
                FROM RETIROS_AHORRO RA
                WHERE RA.CDGNS = :cdgns
                    AND (RA.ESTATUS NOT IN ('C', 'R', 'D') OR COALESCE(RA.FECHA_CANCELACION, RA.FECHA_DEVOLUCION) IS NOT NULL)
                UNION ALL
                SELECT TRUNC(COALESCE(RA.FECHA_CANCELACION, RA.FECHA_DEVOLUCION))
                    , RA.CANT_SOLICITADA
                FROM RETIROS_AHORRO RA
                WHERE RA.CDGNS = :cdgns
                    AND RA.ESTATUS IN ('C', 'R', 'D')
                    AND COALESCE(RA.FECHA_CANCELACION, RA.FECHA_DEVOLUCION) IS NOT NULL
                UNION ALL
                SELECT IA.FECHA_FIN
                    , IA.MONTO
                FROM INTERES_AHORRO IA
                WHERE IA.CDGNS = :cdgns
                    AND IA.ESTATUS = 'A'
            )
            , DIARIO AS (
                SELECT GREATEST(M.FECHA, TO_DATE(:desde, 'YYYY-MM-DD')) AS FECHA
                    , SUM(M.MONTO) AS MONTO
                FROM MOVIMIENTOS M
                WHERE M.FECHA <= TO_DATE(:hasta, 'YYYY-MM-DD')
                GROUP BY GREATEST(M.FECHA, TO_DATE(:desde, 'YYYY-MM-DD'))
            )
            , SALDOS AS (
                SELECT D.FECHA
                    , GREATEST(SUM(NVL(DI.MONTO, 0)) OVER (ORDER BY D.FECHA), 0) AS SALDO
                FROM DIAS D
                    LEFT JOIN DIARIO DI ON DI.FECHA = D.FECHA
            )
            SELECT :cdgns
                , S.SALDO
                , S.FECHA
                , S.SALDO * (:tasa / 100) / :base
                , :tasa
            FROM SALDOS S
        SQL;

        $qrys = [
            $qryElimina,
            $qryDevengo
        ];

        $parametros = [
            [
                "cdgns" => $datos["cdgns"],
                "desde" => $datos["desde"]
            ],
            [
                "cdgns" => $datos["cdgns"],
                "desde" => $datos["desde"],
                "hasta" => $datos["hasta"],
                "tasa" => $datos["tasa"],
                "base" => $datos["base"]
            ]
        ];

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Devengos registrados correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al registrar los devengos", null, $e->getMessage());
        }
    }

    public static function RegistraInteres($datos)
    {
        $qryInteres = <<<SQL
            INSERT INTO INTERES_AHORRO (
                CDGNS,
                FECHA_INICIO,
                FECHA_FIN,
                DIAS,
                TASA,
                MONTO
            )
            SELECT :cdgns
                , TO_DATE(:inicio, 'YYYY-MM-DD')
                , TO_DATE(:fin, 'YYYY-MM-DD')
                , :dias
                , :tasa
                , ROUND(NVL(SUM(DA.DEVENGO), 0), 2)
            FROM DEVENGO_AHORRO DA
            WHERE DA.CONTRATO = :cdgns
                AND DA.ID_INTERES IS NULL
                AND DA.FECHA BETWEEN TO_DATE(:inicio, 'YYYY-MM-DD') AND TO_DATE(:fin, 'YYYY-MM-DD')
            HAVING COUNT(*) = :dias
        SQL;

        $qryDevengos = <<<SQL
            UPDATE
                DEVENGO_AHORRO
            SET
                ID_INTERES = (
                    SELECT ID
                    FROM INTERES_AHORRO
                    WHERE CDGNS = :cdgns
                        AND FECHA_INICIO = TO_DATE(:inicio, 'YYYY-MM-DD')
                )
            WHERE
                CONTRATO = :cdgns
                AND ID_INTERES IS NULL
                AND FECHA BETWEEN TO_DATE(:inicio, 'YYYY-MM-DD') AND TO_DATE(:fin, 'YYYY-MM-DD')
        SQL;

        $qrys = [
            $qryInteres,
            $qryDevengos
        ];

        $parametros = [
            [
                "cdgns" => $datos["cdgns"],
                "inicio" => $datos["inicio"],
                "fin" => $datos["fin"],
                "dias" => $datos["dias"],
                "tasa" => $datos["tasa"]
            ],
            [
                "cdgns" => $datos["cdgns"],
                "inicio" => $datos["inicio"],
                "fin" => $datos["fin"]
            ]
        ];

        // El INSERT no genera registro si faltan días devengados en el periodo
        $qryValida = <<<SQL
            SELECT
                (
                    SELECT COUNT(*)
                    FROM INTERES_AHORRO
                    WHERE CDGNS = :cdgns
                        AND FECHA_INICIO = TO_DATE(:inicio, 'YYYY-MM-DD')
                ) AS REGISTRADO
                , (
                    SELECT COUNT(*)
                    FROM DEVENGO_AHORRO
                    WHERE CONTRATO = :cdgns
                        AND FECHA BETWEEN TO_DATE(:inicio, 'YYYY-MM-DD') AND TO_DATE(:fin, 'YYYY-MM-DD')
                ) AS DIAS
            FROM
                DUAL
        SQL;

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            $valida = $db->queryOne($qryValida, [
                "cdgns" => $datos["cdgns"],
                "inicio" => $datos["inicio"],
                "fin" => $datos["fin"]
            ]);
            if ($valida === false) return self::Responde(false, "Error al validar el registro del interés", null, "Error en la consulta de validación");
            if ((int)$valida["REGISTRADO"] === 0) return self::Responde(false, "Error al registrar el interés", null, "El periodo tiene {$valida["DIAS"]} días devengados de {$datos["dias"]} esperados.");
            return self::Responde(true, "Interés registrado correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al registrar el interés", null, $e->getMessage());
        }
    }
}