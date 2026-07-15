<?php

namespace Jobs\models;

include_once dirname(__DIR__) . "\..\Core\Model.php";
include_once dirname(__DIR__) . "\..\Core\Database.php";

use Core\Model;
use Core\Database;

class JobsAhorro extends Model
{
    public static function GetCuentasActivas()
    {
        $qry = <<<SQL
            SELECT
                APA.CDGCL AS CLIENTE,
                APA.CONTRATO,
                APA.SALDO,
                APA.TASA,
                APA.FECHA_APERTURA
            FROM
                ASIGNA_PROD_AHORRO APA
            WHERE
                APA.ESTATUS = 'A'
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Créditos activos obtenidos correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los créditos activos", null, $e->getMessage());
        }
    }

    public static function AplicaDevengo($datos)
    {
        $f = isset($datos["fecha"]) ? ':fecha' : 'SYSDATE';
        $qryDevengo = <<<SQL
            INSERT INTO
                DEVENGO_AHORRO (
                    CONTRATO,
                    SALDO_CIERRE,
                    FECHA,
                    DEVENGO,
                    TASA
                )
            VALUES
                (
                    :contrato,
                    :saldo,
                    $f,
                    :devengo,
                    :tasa
                )
        SQL;

        $qrys = [
            $qryDevengo,
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro()
        ];

        $parametros = [
            [
                "contrato" => $datos["contrato"],
                "saldo" => $datos["saldo"],
                "devengo" => $datos["devengo"],
                "tasa" => $datos["tasa"]
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["devengo"],
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["devengo"],
                "tipo_pago" => 15,
                "movimiento" => 1,
                "cliente" => $datos["cliente"]
            ]
        ];

        if (isset($datos["fecha"])) $parametros[0]["fecha"] = $datos["fecha"];

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Devengo aplicado correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al aplicar el devengo", null, $e->getMessage());
        }
    }

    public static function GetInversiones()
    {
        $qry = <<<SQL
            SELECT
                (SELECT CDGCL FROM ASIGNA_PROD_AHORRO WHERE CONTRATO = CI.CDG_CONTRATO) AS CLIENTE,
                CI.CDG_CONTRATO AS CONTRATO,
                CI.FECHA_APERTURA AS APERTURA,
                CI.FECHA_VENCIMIENTO AS VENCIMIENTO,
                CI.MONTO_INVERSION AS MONTO,
                CI.CDG_TASA AS ID_TASA,
                TI.TASA,
                PI.PLAZO
            FROM
                CUENTA_INVERSION CI
            JOIN
                TASA_INVERSION TI ON CI.CDG_TASA = TI.CODIGO
            JOIN
                PLAZO_INVERSION PI ON TI.CDG_PLAZO = PI.CODIGO
            WHERE
                CI.ESTATUS = 'A'
                AND TRUNC(CI.FECHA_VENCIMIENTO) = TRUNC(SYSDATE)
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Inversiones obtenidas correctamente", ($res ?? []));
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener las inversiones", null, $e->getMessage());
        }
    }

    public static function LiquidaInversion($datos)
    {
        $qryLiquidacion = <<<SQL
            UPDATE
                CUENTA_INVERSION
            SET
                RENDIMIENTO = :rendimiento,
                ESTATUS = 'L',
                FECHA_LIQUIDACION = SYSDATE,
                MODIFICACION = SYSDATE
            WHERE
                CDG_CONTRATO = :contrato
                AND ESTATUS = 'A'
                AND FECHA_VENCIMIENTO = TO_TIMESTAMP(:fecha_vencimiento, 'DD/MM/YY HH24:MI:SS.FF6')
                AND FECHA_APERTURA = TO_TIMESTAMP(:fecha_apertura, 'DD/MM/YY HH24:MI:SS.FF6')
                AND CDG_TASA = :id_tasa
                AND MONTO_INVERSION = :monto
        SQL;

        $qrys = [
            $qryLiquidacion,
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro(),
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro()
        ];

        $parametros = [
            [
                "rendimiento" => $datos["rendimiento"],
                "contrato" => $datos["contrato"],
                "fecha_vencimiento" => $datos["fecha_vencimiento"],
                "fecha_apertura" => $datos["fecha_apertura"],
                "id_tasa" => $datos["id_tasa"],
                "monto" => $datos["monto"]
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["monto"],
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["monto"],
                "tipo_pago" => 11,
                "movimiento" => 1,
                "cliente" => $datos["cliente"],
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["rendimiento"],
            ],
            [
                "contrato" => $datos["contrato"],
                "monto" => $datos["rendimiento"],
                "tipo_pago" => 12,
                "movimiento" => 1,
                "cliente" => $datos["cliente"],

            ]
        ];

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Inversión liquidada correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al liquidar la inversión", null, $e->getMessage());
        }
    }

    public static function GetSolicitudesRetiro()
    {
        $qry = <<<SQL
            SELECT
                SRA.ID_SOL_RETIRO_AHORRO AS ID,
                CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE,
                CL.CODIGO AS CLIENTE,
                SRA.CANTIDAD_SOLICITADA AS MONTO,
                (
                    SELECT
                        CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE)
                    FROM
                        PE
                    WHERE
                        PE.CODIGO = SRA.CDGPE_ASIGNA_ESTATUS
                        AND CDGEM = 'EMPFIN'
                ) AS APROBADO_POR,
                TO_CHAR(SRA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_ESPERADA,
                SRA.CONTRATO,
                SRA.TIPO_RETIRO
            FROM
                SOLICITUD_RETIRO_AHORRO SRA
                INNER JOIN CL ON CL.CODIGO = (SELECT CDGCL FROM ASIGNA_PROD_AHORRO WHERE CONTRATO = SRA.CONTRATO)
            WHERE
                SRA.ESTATUS <= 1
                AND TRUNC(SRA.FECHA_SOLICITUD) < TRUNC(SYSDATE)
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Solicitudes de retiro obtenidas correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener las solicitudes de retiro", null, $e->getMessage());
        }
    }

    public static function CancelaSolicitudRetiro($datos)
    {
        $qry = <<<SQL
        UPDATE
            SOLICITUD_RETIRO_AHORRO
        SET
            FECHA_ESTATUS = SYSDATE,
            ESTATUS = '5',
            CDGPE_ASIGNA_ESTATUS = 'SSTM'
        WHERE
            ID_SOL_RETIRO_AHORRO = '{$datos['idSolicitud']}'
        SQL;

        try {
            $mysqli = new Database();
            $res = $mysqli->queryOne($qry);
            if (!$res) return self::Responde(true, "Solicitud cancelada correctamente.");
            return self::Responde(false, "Ocurrió un error al cancelar la solicitud.");
        } catch (\Exception $e) {
            return self::Responde(false, "Ocurrió un error al cancelar la solicitud.", null, $e->getMessage());
        }
    }

    public static function DevolucionRetiro($datos)
    {
        $query = [
            self::GetQueryTicket(),
            self::GetQueryMovimientoAhorro()
        ];

        $datosInsert = [
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['monto'],
            ],
            [
                'contrato' => $datos['contrato'],
                'monto' => $datos['monto'],
                'tipo_pago' => $datos['tipo'] == 1 ? '8' : '9',
                'movimiento' => '1',
                'cliente' => $datos['cliente'],
            ]
        ];

        try {
            $mysqli = new Database();
            $res = $mysqli->insertaMultiple($query, $datosInsert);
            if ($res) {
                $ticket = self::RecuperaTicket($datos['contrato']);
                return self::Responde(true, "Se han liberado $ " . number_format($datos['monto'], 2) . " a la cuenta del cliente por el apartado para el retiro " . ($datos['tipo'] == 1 ? "express" : "programado") . ".", ['ticket' => $ticket['CODIGO']]);
            }
            return self::Responde(false, "Ocurrió un error al registrar la devolución.");
        } catch (\Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar la devolución.", null, $e->getMessage());
        }
    }

    public static function GetSucursalesSinArqueo()
    {
        $qry = <<<SQL
        SELECT
            SEA.CDG_SUCURSAL,
            NVL(ARQ.CONTEO, 0) AS CONTEO
        FROM
            SUC_ESTADO_AHORRO SEA
        LEFT JOIN (
                SELECT
                    A.CDG_SUCURSAL,
                    TRUNC(A.FECHA) AS FECHA,
                    COUNT(*) AS CONTEO
                FROM
                    ARQUEO A
                WHERE
                    TRUNC(FECHA) = TRUNC(SYSDATE)
                GROUP BY
                    A.CDG_SUCURSAL,
                    TRUNC(A.FECHA)
            ) ARQ ON ARQ.CDG_SUCURSAL = SEA.CDG_SUCURSAL
        WHERE
            ARQ.CONTEO IS NULL
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Sucursales sin arqueo obtenidas correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener las sucursales sin arqueo", null, $e->getMessage());
        }
    }

    public static function RegistraArqueoPendiente($datos)
    {
        try {

            $qry = <<<SQL
            INSERT INTO ARQUEO
            (CDG_ARQUEO, CDG_USUARIO, CDG_SUCURSAL, FECHA, MONTO, B_1000, B_500, B_200, B_100, B_50, B_20, M_10, M_5, M_2, M_1, M_050, M_020, M_010, SALDO_SUCURSAL)
            VALUES
            ((SELECT NVL(MAX(CDG_ARQUEO),0) FROM ARQUEO) + 1, 'SSTM', :sucursal, SYSDATE, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT
                SALDO
            FROM
                SUC_ESTADO_AHORRO
            WHERE
                CDG_SUCURSAL = :sucursal))
            SQL;

            $parametros = [
                'sucursal' => $datos['sucursal']
            ];

            $mysqli = new Database();
            $res = $mysqli->insertar($qry, $parametros);
            return self::Responde(true, "Arqueo registrado correctamente.");
        } catch (\Exception $e) {
            return self::Responde(false, "Ocurrió un error al registrar el arqueo.", null, $e->getMessage());
        }
    }

    public static function GetSucursales()
    {
        $qry = <<<SQL
            SELECT
                CODIGO,
                CDG_SUCURSAL,
                SALDO
            FROM
                SUC_ESTADO_AHORRO
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Sucursales obtenidas correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener las sucursales", null, $e->getMessage());
        }
    }

    public static function CapturaSaldos($datos)
    {
        $qry = <<<SQL
            MERGE INTO SUC_MOVIMIENTOS_AHORRO dest
            USING (
                SELECT
                    NVL(MAX(TO_NUMBER(CODIGO)), 0) + 1 AS codigo,
                    :sucursal AS sucursal,
                    TO_DATE(:fecha, 'DD/MM/YYYY HH24:MI:SS') AS fecha,
                    :saldo AS saldo,
                    :movimiento AS movimiento,
                    'SYSTEM' AS cdg_usuario
                FROM
                    SUC_MOVIMIENTOS_AHORRO
            ) src
            ON (dest.CDG_ESTADO_AHORRO = src.sucursal AND TRUNC(dest.FECHA) = TRUNC(src.fecha) AND dest.MOVIMIENTO = src.movimiento)
            WHEN NOT MATCHED THEN
            INSERT (
                CODIGO,
                CDG_ESTADO_AHORRO,
                FECHA,
                MONTO,
                MOVIMIENTO,
                CDG_USUARIO
            ) VALUES (
                src.codigo,
                src.sucursal,
                src.fecha,
                src.saldo,
                src.movimiento,
                src.cdg_usuario
            )
        SQL;

        $qrys = [
            $qry,
            $qry
        ];

        $parametros = [
            [
                "sucursal" => $datos["codigo"],
                "saldo" => $datos["saldo"],
                "movimiento" => 3,
                "fecha" => date("d/m/Y H:i:s")
            ],
            [
                "sucursal" => $datos["codigo"],
                "saldo" => $datos["saldo"],
                "movimiento" => 2
            ]
        ];

        $parametros[1]["fecha"] = date("N") == 5 ? date("d/m/Y H:i:s", strtotime("+3 days 8am")) : date("d/m/Y H:i:s", strtotime("tomorrow 8am"));

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Saldos capturados correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al capturar los saldos", null, $e->getMessage());
        }
    }

    public static function GetQueryTicket()
    {
        return <<<SQL
        INSERT INTO TICKETS_AHORRO
            (CODIGO, FECHA, CDG_CONTRATO, MONTO, CDGPE, CDG_SUCURSAL)
        VALUES
            ((SELECT NVL(MAX(TO_NUMBER(CODIGO)),0) FROM TICKETS_AHORRO) + 1, SYSDATE, :contrato, :monto, 'SSTM', '000')
        SQL;
    }

    public static function GetQueryMovimientoAhorro()
    {
        return <<<SQL
            INSERT INTO
                MOVIMIENTOS_AHORRO (
                    CODIGO,
                    FECHA_MOV,
                    CDG_TIPO_PAGO,
                    CDG_CONTRATO,
                    MONTO,
                    MOVIMIENTO,
                    DESCRIPCION,
                    CDG_TICKET,
                    FECHA_VALOR,
                    CDG_RETIRO,
                    CDGCO,
                    CDGCL,
                    CDGPE
                )
            VALUES
                (
                    (
                        SELECT
                            NVL(MAX(TO_NUMBER(CODIGO)), 0)
                        FROM
                            MOVIMIENTOS_AHORRO
                    ) + 1,
                    SYSDATE,
                    :tipo_pago,
                    :contrato,
                    :monto,
                    :movimiento,
                    'ALGUNA_DESCRIPCION',
                    (
                        SELECT
                            MAX(TO_NUMBER(CODIGO)) AS CODIGO
                        FROM
                            TICKETS_AHORRO
                        WHERE
                            CDG_CONTRATO = :contrato
                    ),
                    SYSDATE,
                    (
                        SELECT
                            CASE
                                :tipo_pago
                                WHEN '6' THEN MAX(TO_NUMBER(ID_SOL_RETIRO_AHORRO))
                                WHEN '7' THEN MAX(TO_NUMBER(ID_SOL_RETIRO_AHORRO))
                                ELSE NULL
                            END
                        FROM
                            SOLICITUD_RETIRO_AHORRO
                        WHERE
                            CONTRATO = :contrato
                    ),
                    '000',
                    :cliente,
                    'SSTM'
                )
        SQL;
    }

    public static function RecuperaTicket($contrato)
    {
        $queryTicket = <<<SQL
            SELECT
                MAX(TO_NUMBER(CODIGO)) AS CODIGO
            FROM
                TICKETS_AHORRO
            WHERE
                CDG_CONTRATO = '$contrato'
        SQL;

        try {
            $mysqli = new Database();
            return $mysqli->queryOne($queryTicket);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public static function GetCuentasAhorroValidacionDevengo()
    {
        $qry = <<<SQL
            WITH FECHAS AS (
                SELECT
                    APA.CONTRATO,
                    TRUNC(APA.FECHA_APERTURA) + LEVEL - 1 AS FECHA
                FROM
                    ASIGNA_PROD_AHORRO APA CONNECT BY LEVEL <= TRUNC(SYSDATE) - TRUNC(APA.FECHA_APERTURA) + 1
                    AND PRIOR APA.CONTRATO = APA.CONTRATO
                    AND PRIOR SYS_GUID() IS NOT NULL
            )
            SELECT
                F.CONTRATO,
                F.FECHA,
                APA.CDGCL AS CLIENTE,
                APA.CONTRATO,
                APA.SALDO,
                APA.TASA
            FROM
                FECHAS F
                LEFT JOIN DEVENGO_AHORRO DA ON F.CONTRATO = DA.CONTRATO
                LEFT JOIN ASIGNA_PROD_AHORRO APA ON F.CONTRATO = APA.CONTRATO
            WHERE
                DA.CONTRATO IS NULL
                AND TRUNC(F.FECHA) != TRUNC(SYSDATE)
            ORDER BY
                F.CONTRATO,
                F.FECHA
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Créditos activos obtenidos correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los créditos activos", null, $e->getMessage());
        }
    }
}
