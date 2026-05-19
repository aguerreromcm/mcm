<?php

namespace Jobs\models;

include_once dirname(__DIR__) . "/../Core/Model.php";
include_once dirname(__DIR__) . "/../Core/Database.php";

use Core\Model;
use Core\Database;

class JobsCredito extends Model
{
    public static function GetSolicitudes()
    {
        $qry = <<<SQL
            WITH SOLICITUDES AS (
                SELECT 
                    COALESCE(SN.CDGNS, PRN.CDGNS) AS CREDITO,
                    COALESCE(SN.CICLO, PRN.CICLO) AS CICLO,
                    TO_CHAR(COALESCE(SN.SOLICITUD, PRN.SOLICITUD), 'DD/MM/YYYY  HH24:MI:SS') AS SOLICITUD,
                    SCC.CDGPE,
                    CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE_PE,
                    CASE WHEN SCC.ESTATUS LIKE 'LISTA%' THEN 1 ELSE 0 END AS APROBADA,
                    SCC.ESTATUS,
                    CASE WHEN SCC.DIA_LLAMADA_2_CL IS NULL THEN 2 ELSE 1 END AS NO_LLAMADAS,
                    TO_CHAR(SCC.DIA_LLAMADA_1_CL, 'DD/MM/YYYY HH24:MI:SS') AS PRIMERA_LLAMADA,
                    TO_CHAR(
                        COALESCE(SCC.DIA_LLAMADA_2_CL, SCC.DIA_LLAMADA_1_CL), 
                        'DD/MM/YYYY HH24:MI:SS'
                    ) AS ULTIMA_LLAMADA,
                    SCC.NUMERO_INTENTOS_CL AS INTENTOS,
                    SCC.COMENTARIO_INICIAL,
                    SCC.COMENTARIO_FINAL,
                    CL.CODIGO AS CL,
                    CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE_CL,
                    CO.CODIGO AS CO,
                    CO.NOMBRE AS NOMBRE_CO,
                    RG.CODIGO AS RG,
                    RG.NOMBRE AS NOMBRE_RG,
                    (
                        SELECT
                            COUNT(*) + CASE WHEN COALESCE(SN.CICLO, PRN.CICLO) = '01' THEN 1 ELSE 0 END
                        FROM
                            PRN PRN2
                        WHERE 
                            PRN2.CDGNS = COALESCE(SN.CDGNS, PRN.CDGNS)
                            AND PRN2.SITUACION = 'L'
                            AND REGEXP_LIKE(PRN2.CICLO, '^[0-9]+$')
                            AND REGEXP_LIKE(COALESCE(SN.CICLO, PRN.CICLO), '^[0-9]+$')
                            AND TO_NUMBER(PRN2.CICLO) = TO_NUMBER(COALESCE(SN.CICLO, PRN.CICLO)) - 1
                    ) AS LIQUIDADO,
                    COALESCE(SN.SITUACION, PRN.SITUACION) AS SITUACION
                FROM 
                    SOL_CALL_CENTER SCC
                    LEFT JOIN SN ON SN.CDGNS = SCC.CDGNS AND SN.SOLICITUD = SCC.FECHA_SOL AND SN.SITUACION = 'S'
                    LEFT JOIN PRN ON PRN.CDGNS = SCC.CDGNS AND PRN.SOLICITUD = SCC.FECHA_SOL AND PRN.SITUACION = 'T' AND PRN.NOCHEQUE IS NULL
                    LEFT JOIN CO ON COALESCE(SN.CDGCO, PRN.CDGCO) = CO.CODIGO
                    LEFT JOIN RG ON CO.CDGRG = RG.CODIGO
                    LEFT JOIN SC ON SC.CDGNS = COALESCE(SN.CDGNS, PRN.CDGNS) AND SC.CICLO = COALESCE(SN.CICLO, PRN.CICLO) AND SC.SOLICITUD = COALESCE(SN.SOLICITUD, PRN.SOLICITUD) AND SC.CANTSOLIC <> 9999
                    LEFT JOIN CL ON SC.CDGCL = CL.CODIGO
                    LEFT JOIN PE ON SCC.CDGPE = PE.CODIGO
                WHERE 
                    SCC.ESTATUS NOT LIKE 'PENDIENTE%'
                    AND SCC.FECHA_TRA_CL > TRUNC(SYSDATE) - 7
            )
            SELECT * FROM SOLICITUDES WHERE CREDITO IS NOT NULL ORDER BY TO_DATE(SOLICITUD, 'DD/MM/YYYY  HH24:MI:SS')
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Se obtuvieron las solicitudes de crédito",  $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener las solicitudes de crédito", null, $e->getMessage());
        }
    }

    // Metodos para las solicitudes de crédito aprobadas
    public static function ProcesaSolicitudAprobada($credito)
    {
        $qrys = [];
        $parametros = [];

        [$qrys[], $parametros[]] = self::Solicitud_A_Actualiza_SC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Actualiza_SN($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Inserta_PRN($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Inserta_PRC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Limpia_MPC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Limpia_JP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Limpia_MP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Inserta_MP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Inserta_JP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Inserta_MPC($credito);

        try {
            $db = new Database();
            $r = $db->insertaMultiple($qrys, $parametros, null, true);
            return self::Responde(true, "Solicitud aprobada correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al actualizar la solicitud aprobada", null, $e->getMessage());
        }
    }

    public static function Solicitud_A_Actualiza_SC($datos)
    {
        $qry = <<<SQL
            UPDATE
                SC
            SET
                SC.CANTAUTOR = CASE
                                WHEN SC.CANTSOLIC = 9999 THEN 0
                                ELSE SC.CANTSOLIC
                            END,
                SC.SITUACION = CASE
                                WHEN SC.CANTSOLIC = 9999 THEN 'R'
                                ELSE 'A'
                            END
            WHERE
                SC.SITUACION = 'S'
                AND SC.CDGNS = :CDGNS
                AND SC.CICLO = :CICLO
                AND SC.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_A_Actualiza_SN($datos)
    {
        $qry = <<<SQL
            UPDATE
                SN
            SET
                SN.CANTAUTOR = (SELECT SUM(SC.CANTAUTOR) FROM SC WHERE SC.SITUACION = 'A' AND SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO)
                , SN.SITUACION = 'A'
            WHERE
                SN.SITUACION = 'S'
                AND SN.CDGNS = :CDGNS
                AND SN.CICLO = :CICLO
                AND SN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_E_Inserta_PRN($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                PRN (
                    CDGEM,
                    CDGNS,
                    CICLO,
                    CDGCO,
                    CDGOCPE,
                    SOLICITUD,
                    INICIO,
                    PERIODICIDAD,
                    CANTAUTOR,
                    CANTENTRE,
                    DIAJUNTA,
                    HORARIO,
                    DESFASEPAGO,
                    TASAINI,
                    DURACINI,
                    TASAFIN,
                    DURACFIN,
                    PERIGRCAP,
                    PERIGRINT,
                    CDGMCI,
                    CDGFDI,
                    DEPOSITA,
                    SITUACION,
                    REPORTE,
                    CONCILIADO,
                    AUTCARPE,
                    FAUTCAR,
                    AUTTESPE,
                    FAUTTES,
                    PRESIDENTE,
                    TESORERO,
                    SECRETARIO,
                    ACTUALIZAENPE,
                    MODOAPLIRECA,
                    FCOMITE,
                    NOCHEQUE,
                    ACTUALIZACHPE,
                    FEXP,
                    CDGCB,
                    FORMAENTREGA,
                    CDGTPC,
                    CDGPCR,
                    TASA,
                    PLAZO,
                    CDGMO,
                    CDGPRPE,
                    ACTUALIZACPE,
                    NOACUERDO,
                    MULTPER
                )
            SELECT
                SN.CDGEM,
                SN.CDGNS,
                SN.CICLO,
                SN.CDGCO,
                SN.CDGOCPE,
                SN.SOLICITUD,
                SN.INICIO,
                SN.PERIODICIDAD,
                (SELECT SUM(SC.CANTAUTOR) FROM SC WHERE SC.SITUACION = 'A' AND SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO),
                (SELECT SUM(SC.CANTAUTOR) FROM SC WHERE SC.SITUACION = 'A' AND SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO),
                SN.DIAJUNTA,
                SN.HORARIO,
                SN.DESFASEPAGO,
                SN.TASA,
                SN.DURACION,
                SN.TASA,
                SN.DURACION,
                SN.PERIGRCAP,
                SN.PERIGRINT,
                SN.CDGMCI,
                SN.CDGFDI,
                SN.DEPOSITA,
                'E',
                '   C',
                'C',
                :USUARIO,
                SYSDATE,
                :USUARIO,
                SYSDATE,
                SN.PRESIDENTE,
                SN.TESORERO,
                SN.SECRETARIO,
                :USUARIO,
                SN.MODOAPLIRECA,
                SYSDATE,
                GET_CHQ(SN.CDGCO),
                :USUARIO,
                SYSDATE,
                GET_CDGCB(SN.CDGCO),
                'I',
                SN.CDGTPC,
                SN.CDGPCR,
                SN.TASA,
                SN.DURACION,
                SN.CDGMO,
                SN.CDGPRPE,
                :USUARIO,
                SN.NOACUERDO,
                SN.MULTPER
            FROM
                SN
            WHERE
                SN.SITUACION = 'A'
                AND SN.CDGNS = :CDGNS
                AND SN.CICLO = :CICLO
                AND SN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            'CDGNS' => $datos['CREDITO'],
            'CICLO' => $datos['CICLO'],
            'SOLICITUD' => $datos['SOLICITUD'],
            'USUARIO' => 'AMGM'
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_E_Inserta_PRC($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                PRC (
                    CDGEM,
                    CDGCL,
                    CICLO,
                    CDGNS,
                    SOLICITUD,
                    CANTAUTOR,
                    CANTENTRE,
                    CDGORF,
                    CDGLC,
                    REPORTE,
                    NOCHEQUE,
                    FEXPCHEQUE,
                    SITUACION,
                    CONCILIADO,
                    ACTUALIZACHPE,
                    CLNS,
                    FEXP,
                    CDGCB,
                    FORMAENTREGA,
                    CDGCLNS,
                    ENTRREAL,
                    DOMICILIA
                )
            SELECT
                PRN.CDGEM,
                SC.CDGCL,
                PRN.CICLO,
                PRN.CDGNS,
                PRN.SOLICITUD,
                SC.CANTAUTOR,
                SC.CANTAUTOR,
                '0001',
                '001',
                PRN.REPORTE,
                PRN.NOCHEQUE,
                SYSDATE,
                PRN.SITUACION,
                PRN.CONCILIADO,
                PRN.ACTUALIZACHPE,
                SC.CLNS,
                PRN.FEXP,
                PRN.CDGCB,
                PRN.FORMAENTREGA,
                SC.CDGNS,
                PRN.CANTENTRE,
                SC.DOMICILIA
            FROM
                SC
                JOIN PRN ON PRN.CDGNS = SC.CDGNS AND PRN.CICLO = SC.CICLO AND PRN.SOLICITUD = SC.SOLICITUD AND PRN.SITUACION = 'E'
            WHERE
                SC.CDGNS = :CDGNS
                AND SC.CICLO = :CICLO
                AND SC.SITUACION = 'A'
                AND SC.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            'CDGNS' => $datos['CREDITO'],
            'CICLO' => $datos['CICLO'],
            'SOLICITUD' => $datos['SOLICITUD']
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_E_Limpia_MPC($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                MPC
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND CLNS = 'G'
                AND TIPO IN ('IN', 'GR', 'Co', 'GA')
                AND PERIODO = 0
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"]
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_E_Limpia_JP($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                JP
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND CLNS = 'G'
                AND TIPO in ('IN', 'GR', 'Co', 'GA')
                AND PERIODO = 0
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"]
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_E_Limpia_MP($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                MP
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGNS = :CDGNS
                AND CICLO = :CICLO
                AND CLNS = 'G'
                AND TIPO IN ('IN', 'GR', 'Co', 'GA')
                AND PERIODO = 0
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"],
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_E_Inserta_MP($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                MP (
                    CDGEM,
                    CDGCLNS,
                    CLNS,
                    CDGNS,
                    CICLO,
                    PERIODO,
                    SECUENCIA,
                    REFERENCIA,
                    REFCIE,
                    TIPO,
                    FDEPOSITO,
                    FREALDEP,
                    CANTIDAD,
                    MODO,
                    CONCILIADO,
                    ESTATUS,
                    ACTUALIZARPE,
                    PAGADOCAP,
                    PAGADOINT,
                    PAGADOREC
                )
            SELECT
                PRN.CDGEM,
                PRC.CDGCLNS,
                PRC.CLNS,
                PRN.CDGNS,
                PRN.CICLO,
                0,
                '01',
                :ETIQUETA,
                :ETIQUETA,
                'IN',
                PRN.INICIO,
                PRN.INICIO,
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.TASA,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                'G',
                'D',
                'B',
                'AMGM',
                0,
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.TASA,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                0
            FROM
                PRN
                JOIN PRC ON PRN.CDGNS = PRC.CDGNS AND PRN.CICLO = PRC.CICLO AND PRN.SOLICITUD = PRC.SOLICITUD
            WHERE
                PRN.SITUACION = 'E'
                AND PRN.CDGNS = :CDGNS
                AND PRN.CICLO = :CICLO
                AND PRN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"],
            "ETIQUETA" => "Interés total del préstamo",
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_E_Inserta_JP($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                JP (
                    CDGEM,
                    CDGCLNS,
                    CLNS,
                    CDGNS,
                    CICLO,
                    PERIODO,
                    TEXTO,
                    FECHA,
                    PAGOINFORME,
                    PAGOFICHA,
                    AHORRO,
                    RETIRO,
                    CONCBANINF,
                    CONCBANFI,
                    COINCIDEPAG,
                    TIPO,
                    ACTUALIZARPE,
                    CONCILIADO
                )
            SELECT
                PRN.CDGEM,
                PRC.CDGCLNS,
                PRC.CLNS,
                PRN.CDGNS,
                PRN.CICLO,
                0,
                'Interés total del préstamo',
                PRN.INICIO,
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.Tasa,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.Tasa,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                0,
                0,
                'S',
                'S',
                'S',
                'IN',
                'AMGM',
                'C'
            FROM
                PRN
                JOIN PRC ON PRN.CDGNS = PRC.CDGNS AND PRN.CICLO = PRC.CICLO AND PRN.SOLICITUD = PRC.SOLICITUD
            WHERE
                PRN.SITUACION = 'E'
                AND PRN.CDGNS = :CDGNS
                AND PRN.CICLO = :CICLO
                AND PRN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_E_Inserta_MPC($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                MPC (
                    CDGEM,
                    CDGCL,
                    CICLO,
                    PERIODO,
                    TIPO,
                    CDGNS,
                    CANTIDAD,
                    CLNS,
                    FECHA,
                    CDGCLNS
                )
            SELECT
                PRN.CDGEM,
                PRC.CDGCL,
                PRN.CICLO,
                0,
                'IN',
                PRN.CDGNS,
                -APagarInteresPrN(
                    PRN.CDGEM,
                    PRN.CDGNS,
                    PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                    PRN.Tasa,
                    PRN.PLAZO,
                    PRN.PERIODICIDAD,
                    PRN.CDGMCI,
                    PRN.INICIO,
                    PRN.DIAJUNTA,
                    PRN.MULTPER,
                    PRN.PERIGRCAP,
                    PRN.PERIGRINT,
                    PRN.DESFASEPAGO,
                    PRN.CDGTI
                ),
                PRC.CLNS,
                PRN.INICIO,
                PRC.CDGCLNS
            FROM
                PRN
                JOIN PRC ON PRN.CDGNS = PRC.CDGNS AND PRN.CICLO = PRC.CICLO AND PRN.SOLICITUD = PRC.SOLICITUD
            WHERE
                PRN.SITUACION = 'E'
                AND PRN.CDGNS = :CDGNS
                AND PRN.CICLO = :CICLO
                AND PRN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [$qry, $parametros];
    }

    // Metodos para poner solicitudes de crédito en espera
    public static function PonerSolicitudEnEspera($credito)
    {
        $qrys = [];
        $parametros = [];

        [$qrys[], $parametros[]] = self::Solicitud_A_Actualiza_SC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_A_Actualiza_SN($credito);
        [$qrys[], $parametros[]] = self::Solicitud_T_Inserta_PRN($credito);
        [$qrys[], $parametros[]] = self::Solicitud_T_Inserta_PRC($credito);

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Solicitud puesta en espera correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al actualizar la solicitud en espera", null, $e->getMessage());
        }
    }

    public static function Solicitud_T_Inserta_PRN($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                PRN (
                    CDGEM,
                    CDGNS,
                    CICLO,
                    CDGCO,
                    CDGOCPE,
                    SOLICITUD,
                    INICIO,
                    PERIODICIDAD,
                    CANTAUTOR,
                    DIAJUNTA,
                    HORARIO,
                    DESFASEPAGO,
                    TASAINI,
                    DURACINI,
                    TASAFIN,
                    DURACFIN,
                    PERIGRCAP,
                    PERIGRINT,
                    CDGMCI,
                    CDGFDI,
                    DEPOSITA,
                    SITUACION,
                    CONCILIADO,
                    AUTCARPE,
                    FAUTCAR,
                    AUTTESPE,
                    FAUTTES,
                    PRESIDENTE,
                    TESORERO,
                    SECRETARIO,
                    MODOAPLIRECA,
                    FORMAENTREGA,
                    CDGTPC,
                    CDGPCR,
                    TASA,
                    PLAZO,
                    CDGMO,
                    CDGPRPE,
                    NOACUERDO,
                    MULTPER
                )
            SELECT
                SN.CDGEM,
                SN.CDGNS,
                SN.CICLO,
                SN.CDGCO,
                SN.CDGOCPE,
                SN.SOLICITUD,
                SN.INICIO,
                SN.PERIODICIDAD,
                (SELECT SUM(SC.CANTAUTOR) FROM SC WHERE SC.SITUACION = 'A' AND SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO),
                SN.DIAJUNTA,
                SN.HORARIO,
                SN.DESFASEPAGO,
                SN.TASA,
                SN.DURACION,
                SN.TASA,
                SN.DURACION,
                SN.PERIGRCAP,
                SN.PERIGRINT,
                SN.CDGMCI,
                SN.CDGFDI,
                SN.DEPOSITA,
                'T',
                'C',
                :USUARIO,
                SYSDATE,
                :USUARIO,
                SYSDATE,
                SN.PRESIDENTE,
                SN.TESORERO,
                SN.SECRETARIO,
                SN.MODOAPLIRECA,
                'I',
                SN.CDGTPC,
                SN.CDGPCR,
                SN.TASA,
                SN.DURACION,
                SN.CDGMO,
                SN.CDGPRPE,
                SN.NOACUERDO,
                SN.MULTPER
            FROM
                SN
            WHERE
                SN.SITUACION = 'A'
                AND SN.CDGNS = :CDGNS
                AND SN.CICLO = :CICLO
                AND SN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            'CDGNS' => $datos['CREDITO'],
            'CICLO' => $datos['CICLO'],
            'SOLICITUD' => $datos['SOLICITUD'],
            'USUARIO' => 'AMGM'
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_T_Inserta_PRC($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                PRC (
                    CDGEM,
                    CDGCL,
                    CICLO,
                    CDGNS,
                    SOLICITUD,
                    CANTAUTOR,
                    CDGORF,
                    CDGLC,
                    SITUACION,
                    CONCILIADO,
                    CLNS,
                    FORMAENTREGA,
                    CDGCLNS,
                    DOMICILIA
                )
            SELECT
                PRN.CDGEM,
                SC.CDGCL,
                PRN.CICLO,
                PRN.CDGNS,
                PRN.SOLICITUD,
                SC.CANTAUTOR,
                '0001',
                '001',
                PRN.SITUACION,
                PRN.CONCILIADO,
                SC.CLNS,
                PRN.FORMAENTREGA,
                SC.CDGNS,
                SC.DOMICILIA
            FROM
                SC
                JOIN PRN ON PRN.CDGNS = SC.CDGNS AND PRN.CICLO = SC.CICLO AND PRN.SOLICITUD = SC.SOLICITUD AND PRN.SITUACION = 'T'
            WHERE
                SC.CDGNS = :CDGNS
                AND SC.CICLO = :CICLO
                AND SC.SITUACION = 'A'
                AND SC.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            'CDGNS' => $datos['CREDITO'],
            'CICLO' => $datos['CICLO'],
            'SOLICITUD' => $datos['SOLICITUD']
        ];

        return [$qry, $parametros];
    }

    // Metodos para concluir solicitudes de crédito en espera
    public static function ConcluirSolicitudEnEspera($credito)
    {
        $qrys = [];
        $parametros = [];

        [$qrys[], $parametros[]] = self::Solicitud_E_Actualiza_PRN($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Actualiza_PRC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Limpia_MPC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Limpia_JP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Limpia_MP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Inserta_MP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Inserta_JP($credito);
        [$qrys[], $parametros[]] = self::Solicitud_E_Inserta_MPC($credito);

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros, null, true);
            return self::Responde(true, "Solicitud concluida correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al actualizar la solicitud concluida", null, $e->getMessage());
        }
    }

    public static function Solicitud_E_Actualiza_PRN($datos)
    {
        $qry = <<<SQL
            UPDATE
                PRN
            SET 
                PRN.SITUACION = 'E',
                PRN.REPORTE = '   C',
                PRN.CONCILIADO = 'C',
                PRN.AUTTESPE = :USUARIO,
                PRN.FAUTTES = SYSDATE,
                PRN.ACTUALIZAENPE = :USUARIO,
                PRN.FCOMITE = SYSDATE,
                PRN.NOCHEQUE = GET_CHQ(PRN.CDGCO),
                PRN.ACTUALIZACHPE = :USUARIO,
                PRN.FEXP = SYSDATE,
                PRN.CDGCB = GET_CDGCB(PRN.CDGCO),
                PRN.FORMAENTREGA = 'I',
                PRN.ACTUALIZACPE = :USUARIO,
                PRN.CANTENTRE = (
                    SELECT
                        SUM(SC.CANTAUTOR) 
                    FROM
                        SC 
                    WHERE
                        SC.SITUACION = 'A' 
                        AND SC.CDGNS = PRN.CDGNS 
                        AND SC.CICLO = PRN.CICLO
                        AND SC.SOLICITUD = PRN.SOLICITUD
                )
            WHERE 
                PRN.SITUACION = 'T'
                AND PRN.CDGNS = :CDGNS
                AND PRN.CICLO = :CICLO
                AND PRN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            'CDGNS' => $datos['CREDITO'],
            'CICLO' => $datos['CICLO'],
            'SOLICITUD' => $datos['SOLICITUD'],
            'USUARIO' => 'AMGM'
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_E_Actualiza_PRC($datos)
    {
        $qry = <<<SQL
            UPDATE
                PRC
            SET
                CANTENTRE = (
                    SELECT
                        SC.CANTAUTOR
                    FROM
                        SC
                    WHERE
                        SC.CDGNS = PRC.CDGNS
                        AND SC.CICLO = PRC.CICLO
                        AND SC.CDGCL = PRC.CDGCL
                        AND SC.SITUACION = 'A'
                ),
                REPORTE = (
                    SELECT
                        REPORTE
                    FROM
                        PRN
                    WHERE
                        CDGNS = PRC.CDGNS
                        AND CICLO = PRC.CICLO
                        AND SOLICITUD = PRC.SOLICITUD
                ),
                NOCHEQUE = (
                    SELECT
                        NOCHEQUE
                    FROM
                        PRN
                    WHERE
                        CDGNS = PRC.CDGNS
                        AND CICLO = PRC.CICLO
                        AND SOLICITUD = PRC.SOLICITUD
                ),
                FEXPCHEQUE = SYSDATE,
                SITUACION = (
                    SELECT
                        SITUACION
                    FROM
                        PRN
                    WHERE
                        CDGNS = PRC.CDGNS
                        AND CICLO = PRC.CICLO
                        AND SOLICITUD = PRC.SOLICITUD
                ),
                CONCILIADO = (
                    SELECT
                        CONCILIADO
                    FROM
                        PRN
                    WHERE
                        CDGNS = PRC.CDGNS
                        AND CICLO = PRC.CICLO
                        AND SOLICITUD = PRC.SOLICITUD
                ),
                ACTUALIZACHPE = (
                    SELECT
                        ACTUALIZACHPE
                    FROM
                        PRN
                    WHERE
                        CDGNS = PRC.CDGNS
                        AND CICLO = PRC.CICLO
                        AND SOLICITUD = PRC.SOLICITUD
                ),
                FEXP = (
                    SELECT
                        FEXP
                    FROM
                        PRN
                    WHERE
                        CDGNS = PRC.CDGNS
                        AND CICLO = PRC.CICLO
                        AND SOLICITUD = PRC.SOLICITUD
                ),
                CDGCB = (
                    SELECT
                        CDGCB
                    FROM
                        PRN
                    WHERE
                        CDGNS = PRC.CDGNS
                        AND CICLO = PRC.CICLO
                        AND SOLICITUD = PRC.SOLICITUD
                ),
                FORMAENTREGA = (
                    SELECT
                        FORMAENTREGA
                    FROM
                        PRN
                    WHERE
                        CDGNS = PRC.CDGNS
                        AND CICLO = PRC.CICLO
                        AND SOLICITUD = PRC.SOLICITUD
                ),
                ENTRREAL = (
                    SELECT
                        SC.CANTAUTOR
                    FROM
                        SC
                    WHERE
                        SC.CDGNS = PRC.CDGNS
                        AND SC.CICLO = PRC.CICLO
                        AND SC.CDGCL = PRC.CDGCL
                        AND SC.SITUACION = 'A'
                )
            WHERE
                EXISTS (
                    SELECT
                        1
                    FROM
                        SC
                        JOIN PRN ON PRN.CDGNS = SC.CDGNS
                        AND PRN.CICLO = SC.CICLO
                        AND PRN.SOLICITUD = SC.SOLICITUD
                        AND PRN.SITUACION = 'E'
                    WHERE
                        SC.CDGNS = :CDGNS
                        AND SC.CICLO = :CICLO
                        AND SC.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
                        AND SC.SITUACION = 'A'
                        AND SC.CDGNS = PRC.CDGNS
                        AND SC.CDGCL = PRC.CDGCL
                        AND SC.CICLO = PRC.CICLO
                )
        SQL;

        $parametros = [
            'CDGNS' => $datos['CREDITO'],
            'CICLO' => $datos['CICLO'],
            'SOLICITUD' => $datos['SOLICITUD']
        ];

        return [$qry, $parametros];
    }

    // Metodos para las solicitudes de crédito rechazadas
    public static function ProcesaSolicitudRechazada($credito)
    {
        $qrys = [];
        $parametros = [];

        [$qrys[], $parametros[]] = self::Solicitud_R_Actualiza_SC($credito);
        [$qrys[], $parametros[]] = self::Solicitud_R_Actualiza_SN($credito);

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Solicitud rechazada correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al actualizar la solicitud rechazada", null, $e->getMessage());
        }
    }

    public static function Solicitud_R_Actualiza_SC($datos)
    {
        $qry = <<<SQL
            UPDATE
                SC
            SET
                SC.CICLO = 'R' || (
                    SELECT
                        COUNT(*) + 1
                    FROM
                        SC SC2
                    WHERE
                        SC2.CICLOR = SC.CICLO
                        AND SC2.CDGNS = SC.CDGNS
                ),
                SC.SITUACION = 'R',
                SC.CICLOR = :CICLO
            WHERE
                SC.SITUACION = 'S'
                AND SC.CDGNS = :CDGNS
                AND SC.CICLO = :CICLO
                AND SC.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"]
        ];

        return [$qry, $parametros];
    }

    public static function Solicitud_R_Actualiza_SN($datos)
    {
        $qry = <<<SQL
            UPDATE
                SN
            SET
                SN.CICLO = 'R' || (
                    SELECT
                        COUNT(*) + 1
                    FROM
                        SN SN2
                    WHERE
                        SN2.CICLOR = SN.CICLO
                        AND SN2.CDGNS = SN.CDGNS
                ),
                SN.SITUACION = 'R',
                SN.CANTAUTOR = 0,
                SN.CICLOR = :CICLO,
                SN.RECCARPE = :USUARIOR
            WHERE
                SN.SITUACION = 'S'
                AND SN.CDGNS = :CDGNS
                AND SN.CICLO = :CICLO
                AND SN.SOLICITUD = TO_DATE(:SOLICITUD, 'DD/MM/YYYY HH24:MI:SS')
        SQL;

        $parametros = [
            "CDGNS" => $datos["CREDITO"],
            "CICLO" => $datos["CICLO"],
            "SOLICITUD" => $datos["SOLICITUD"],
            "USUARIOR" => $datos["CDGPE"]
        ];

        return [$qry, $parametros];
    }

    // Metodos para los cheques
    public static function GetCreditosAutorizados()
    {
        $qry = <<<SQL
            SELECT
                PRC.CDGCL,
                PRN.CDGNS,
                PRN.CICLO,
                TO_CHAR(PRN.INICIO, 'YYYY-MM-DD') AS INICIO,
                PRN.CDGCO,
                PRN.CANTAUTOR,
                TRUNC(SYSDATE) AS FEXP,
                (
                    APagarInteresPrN(
                        'EMPFIN',
                        PRN.CDGNS,
                        PRN.CICLO,
                        NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                        PRN.Tasa,
                        PRN.PLAZO,
                        PRN.PERIODICIDAD,
                        PRN.CDGMCI,
                        PRN.INICIO,
                        PRN.DIAJUNTA,
                        PRN.MULTPER,
                        PRN.PERIGRCAP,
                        PRN.PERIGRINT,
                        PRN.DESFASEPAGO,
                        PRN.CDGTI
                    ) * -1
                ) AS INTERES,
                (
                    APagarInteresPrN(
                        'EMPFIN',
                        PRN.CDGNS,
                        PRN.CICLO,
                        NVL(PRN.CANTENTRE, PRN.CANTAUTOR),
                        PRN.Tasa,
                        PRN.PLAZO,
                        PRN.PERIODICIDAD,
                        PRN.CDGMCI,
                        PRN.INICIO,
                        PRN.DIAJUNTA,
                        PRN.MULTPER,
                        PRN.PERIGRCAP,
                        PRN.PERIGRINT,
                        PRN.DESFASEPAGO,
                        PRN.CDGTI
                    ) * -1
                ) AS PAGADOINT
            FROM
                PRN,
                PRC
            WHERE
                PRN.INICIO > TIMESTAMP '2024-04-11 00:00:00.000000'
                AND PRN.SITUACION = 'T'
                AND (
                    SELECT
                        COUNT(*)
                    FROM
                        PRN PRN2
                    WHERE
                        PRN2.SITUACION = 'E'
                        AND PRN2.CDGNS = PRN.CDGNS
                ) = 0
                AND PRC.CDGNS = PRN.CDGNS
                AND PRC.NOCHEQUE IS NULL
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Se obtuvieron los créditos autorizados",  $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los créditos autorizados", null, $e->getMessage());
        }
    }

    public static function GetNoChequera($cdgco)
    {
        $qry = <<<SQL
            SELECT
                CDGCB
            FROM
                CHEQUERA
            WHERE
                TO_NUMBER(CODIGO) = (
                    SELECT
                        MAX(TO_NUMBER(CODIGO)) AS int_column
                    FROM
                        CHEQUERA
                    WHERE
                        CDGCO = :cdgco
                )
                AND CDGCO = :cdgco
        SQL;

        try {
            $db = new Database();
            $res = $db->queryOne($qry, ["cdgco" => $cdgco]);
            return self::Responde(true, "Se obtuvo el número de chequera", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el número de chequera", null, $e->getMessage());
        }
    }

    public static function GetNoCheque($chequera)
    {
        $qry = <<<SQL
            SELECT
                FNSIGCHEQUE('EMPFIN', :chequera) CHQSIG
            FROM
                DUAL
        SQL;

        try {
            $db = new Database();
            $res = $db->queryOne($qry, ["chequera" => $chequera]);
            return self::Responde(true, "Se obtuvo el número de cheque", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el número de cheque", $e->getMessage());
        }
    }

    public static function GeneraCheques($datos)
    {
        $qrys = [];
        $parametros = [];

        [$qrys[], $parametros[]] = self::GenCheques_Actualiza_PRC($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Actualiza_PRN($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Limpiar_MPC($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Limpiar_JP($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Limpiar_MP($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Insertar_MP($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Insertar_JP($datos);
        [$qrys[], $parametros[]] = self::GenCheques_Insertar_MPC($datos);

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, "Cheque generado correctamente", $datos);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al generar el cheque", null, $e->getMessage());
        }
    }

    public static function GenCheques_Actualiza_PRC($datos)
    {
        $qry = <<<SQL
            UPDATE PRC SET
                NOCHEQUE = LPAD(:cheque,7,'0'),
                FEXP = SYSDATE,
                ACTUALIZACHPE = 'AMGM',
                SITUACION = 'E',
                CDGCB = :cdgcb,
                REPORTE = '   C',
                FEXPCHEQUE = SYSDATE,
                CANTENTRE = :cantautor,
                ENTRREAL = :cantautor
            WHERE
                CDGCL = :cdgcl
                AND CDGCLNS = :cdgns
                AND CICLO = :ciclo
        SQL;

        $parametros = [
            "cheque" => $datos["cheque"],
            "cdgcb" => $datos["cdgcb"],
            "cantautor" => $datos["cantautor"],
            "cdgcl" => $datos["cdgcl"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"],
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Actualiza_PRN($datos)
    {
        $qry = <<<SQL
            UPDATE PRN SET
                REPORTE = '   C',
                FEXP = SYSDATE,
                ACTUALIZACHPE= 'AMGM',
                SITUACION = 'E',
                CDGCB = :cdgcb,
                CANTENTRE = :cantautor,
                ACTUALIZAENPE = 'AMGM',
                ACTUALIZACPE = 'AMGM',
                FCOMITE = SYSDATE
            WHERE
                CDGNS = :cdgns
                AND CICLO = :ciclo
        SQL;

        $parametros = [
            "cdgcb" => $datos["cdgcb"],
            "cantautor" => $datos["cantautor"],
            "cdgns" => $datos["cdgns"],
            "ciclo" => $datos["ciclo"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Limpiar_MPC($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                MPC
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGCLNS = :prmCDGCLNS
                AND CLNS = 'G'
                AND CICLO = :prmCICLO
                AND FECHA = TO_DATE(:prmINICIO, 'YYYY-MM-DD')
                AND TIPO in ('IN', 'GR', 'Co', 'GA')
                AND PERIODO = '00'
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Limpiar_JP($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                JP
            WHERE
                CDGEM = 'EMPFIN'
                AND CDGCLNS = :prmCDGCLNS
                AND CLNS = 'G'
                AND CICLO = :prmCICLO
                AND FECHA = TO_DATE(:prmINICIO, 'YYYY-MM-DD')
                AND PERIODO = '00'
                AND TIPO in ('IN', 'GR', 'Co', 'GA')
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Limpiar_MP($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                MP
            WHERE
                CDGEM = 'EMPFIN'
                AND cdgclns = :prmCDGCLNS
                AND CLNS = 'G'
                AND ciclo = :prmCICLO
                AND frealdep = TO_DATE(:prmINICIO, 'YYYY-MM-DD')
                AND TIPO IN ('IN', 'GR', 'Co', 'GA')
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Insertar_MP($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                MP (
                    CDGEM,
                    CDGCLNS,
                    CLNS,
                    CDGNS,
                    CICLO,
                    PERIODO,
                    SECUENCIA,
                    REFERENCIA,
                    REFCIE,
                    TIPO,
                    FREALDEP,
                    FDEPOSITO,
                    CANTIDAD,
                    MODO,
                    CONCILIADO,
                    ESTATUS,
                    ACTUALIZARPE,
                    PAGADOCAP,
                    PAGADOINT,
                    PAGADOREC
                )
            VALUES
                (
                    'EMPFIN',
                    :prmCDGCLNS,
                    'G',
                    :prmCDGNS,
                    :prmCICLO,
                    '0',
                    '01',
                    'Interés total del préstamo',
                    'Interés total del préstamo',
                    'IN',
                    TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                    TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                    :vINTERES,
                    'G',
                    'D',
                    'B',
                    'AMGM',
                    0,
                    :vINTERES,
                    0
                )
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCDGNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "vINTERES" => $datos["vINTERES"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Insertar_JP($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                JP (
                    CDGEM,
                    CDGCLNS,
                    CICLO,
                    CLNS,
                    FECHA,
                    PERIODO,
                    PAGOINFORME,
                    PAGOFICHA,
                    AHORRO,
                    RETIRO,
                    TIPO,
                    CDGNS,
                    TEXTO,
                    CONCILIADO,
                    ACTUALIZARPE,
                    CONCBANINF,
                    CONCBANFI,
                    COINCIDEPAG
                )
            VALUES
                (
                    'EMPFIN',
                    :prmCDGCLNS,
                    :prmCICLO,
                    'G',
                    TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                    '00',
                    :vINTERES,
                    :vINTERES,
                    0,
                    0,
                    'IN',
                    :prmCDGCLNS,
                    'Interés total del préstamo',
                    'C',
                    'AMGM',
                    'S',
                    'S',
                    'S'
                )
        SQL;

        $parametros = [
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "vINTERES" => $datos["vINTERES"],
        ];

        return [
            $qry,
            $parametros
        ];
    }

    public static function GenCheques_Insertar_MPC($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                MPC (
                    CDGEM,
                    CDGCL,
                    CICLO,
                    CLNS,
                    FECHA,
                    TIPO,
                    PERIODO,
                    CDGCLNS,
                    CDGNS,
                    CANTIDAD
                )
            VALUES
                (
                    'EMPFIN',
                    :vCLIENTE,
                    :prmCICLO,
                    'G',
                    TO_DATE(:prmINICIO, 'YYYY-MM-DD'),
                    'IN',
                    '00',
                    :prmCDGCLNS,
                    :prmCDGCLNS,
                    :vINTERES
                )
        SQL;

        $parametros = [
            "vCLIENTE" => $datos["vCLIENTE"],
            "prmCICLO" => $datos["prmCICLO"],
            "prmINICIO" => $datos["prmINICIO"],
            "prmCDGCLNS" => $datos["prmCDGCLNS"],
            "vINTERES" => $datos["vINTERES"]
        ];

        return [
            $qry,
            $parametros
        ];
    }

    /**
     * Reporte de días de atraso (PRN situación L).
     *
     * @return array { success, mensaje, datos }
     */
    public static function GetRepDiasAtraso()
    {
        $qry = <<<SQL
            SELECT
                PRN.CDGNS AS COD_CTE,
                PRN.CICLO,
                NS.NOMBRE,
                TO_CHAR(PRN.INICIO, 'DD/MM/YYYY') AS INICIO,
                FNCALDIASATRASO(PRN.CDGEM, PRN.CDGNS, PRN.CICLO, 'G', SYSDATE) AS DIAS_ATRASO
            FROM
                PRN
                INNER JOIN NS ON PRN.CDGEM = NS.CDGEM
                             AND PRN.CDGNS = NS.CODIGO
            WHERE
                PRN.SITUACION = 'L'
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, 'Consulta exitosa', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al consultar el reporte', null, $e->getMessage());
        }
    }
}
