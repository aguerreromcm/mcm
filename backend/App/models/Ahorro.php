<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Core\Model;

class Ahorro extends Model
{
    public static function ConsultaTickets($usuario)
    {
        if ($usuario == 'AMGM') {
            $query = <<<sql
             SELECT TICKETS_AHORRO.CODIGO, TICKETS_AHORRO.CDG_CONTRATO,
            TO_CHAR(TICKETS_AHORRO.FECHA, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ALTA, TICKETS_AHORRO.MONTO, TICKETS_AHORRO.CDGPE, (CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE ) AS NOMBRE_CLIENTE,
            'CUENTA AHORRO' AS TIPO_AHORRO
            FROM TICKETS_AHORRO
            INNER JOIN ASIGNA_PROD_AHORRO ON ASIGNA_PROD_AHORRO.CONTRATO = TICKETS_AHORRO.CDG_CONTRATO 
            INNER JOIN CL ON CL.CODIGO = ASIGNA_PROD_AHORRO.CDGCL 
            ORDER BY FECHA DESC
sql;
        } else {
            $query = <<<sql
             SELECT TICKETS_AHORRO.CODIGO, TICKETS_AHORRO.CDG_CONTRATO,
            TO_CHAR(TICKETS_AHORRO.FECHA, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ALTA, TICKETS_AHORRO.MONTO, TICKETS_AHORRO.CDGPE, (CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE ) AS NOMBRE_CLIENTE,
              'CUENTA AHORRO' AS TIPO_AHORRO 
            FROM TICKETS_AHORRO
            INNER JOIN ASIGNA_PROD_AHORRO ON ASIGNA_PROD_AHORRO.CONTRATO = TICKETS_AHORRO.CDG_CONTRATO 
            INNER JOIN CL ON CL.CODIGO = ASIGNA_PROD_AHORRO.CDGCL 
            WHERE TICKETS_AHORRO.CDGPE = '$usuario' 
            ORDER BY TICKETS_AHORRO.FECHA DESC
sql;
        }


        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function ConsultaSolicitudesTickets($datos)
    {
        $query = <<<sql
        SELECT TAR.CODIGO, TAR.CDGTICKET_AHORRO, TAR.FREGISTRO, TAR.FREIMPRESION, TAR.MOTIVO, TAR.ESTATUS, TAR.CDGPE_SOLICITA, TAR.CDGPE_AUTORIZA, TAR.AUTORIZA, TAR.DESCRIPCION_MOTIVO, 
        TAR.AUTORIZA_CLIENTE, TA.CDG_CONTRATO 
        FROM ESIACOM.TICKETS_AHORRO_REIMPRIME TAR
        INNER JOIN TICKETS_AHORRO TA ON TA.CODIGO = TAR.CDGTICKET_AHORRO 
        sql;

        $filtros = [];
        if ($datos['usuario'] != 'AMGM') $filtros[] = "TAR.CDGPE_SOLICITA = '{$datos['usuario']}'";
        if ($datos['estatus']) $filtros[] = "TAR.ESTATUS = '{$datos['estatus']}'";
        if ($datos['fechaI'] && $datos['fechaF']) $filtros[] = "TRUNC(TAR.FREGISTRO) BETWEEN TO_DATE('{$datos['fechaI']}', 'YYYY-MM-DD') AND TO_DATE('{$datos['fechaF']}', 'YYYY-MM-DD')";

        if (count($filtros) > 0) $query .= " WHERE " . implode(" AND ", $filtros);
        $query .= " ORDER BY TAR.FREGISTRO DESC";

        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function insertSolicitudAhorro($solicitud)
    {

        $query_consulta_existe_sol = <<<sql
            SELECT COUNT(*) AS EXISTE
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME
            WHERE CDGPE_SOLICITA = '$solicitud->_cdgpe' 
            AND CDGTICKET_AHORRO = '$solicitud->_folio'
            AND ESTATUS = '0'
            AND AUTORIZA = '0'
sql;

        //var_dump($query_consulta_existe_sol);

        $mysqli = new Database();
        $res = $mysqli->queryOne($query_consulta_existe_sol);



        if ($res['EXISTE'] == 0) {
            //Agregar un registro
            $query = <<<sql
        INSERT INTO ESIACOM.TICKETS_AHORRO_REIMPRIME
        (CODIGO, CDGTICKET_AHORRO, FREGISTRO, FREIMPRESION, MOTIVO, ESTATUS, CDGPE_SOLICITA, CDGPE_AUTORIZA, AUTORIZA, DESCRIPCION_MOTIVO, FAUTORIZA, AUTORIZA_CLIENTE)
        VALUES(SEC_TICKET_REIMPRIME.NEXTVAL, '$solicitud->_folio', CURRENT_TIMESTAMP, '', '$solicitud->_motivo', '0', '$solicitud->_cdgpe', '', '0', '$solicitud->_descripcion', NULL , '0')
sql;

            return $mysqli->insert($query);
        } else {
            echo "Ya solicito la reimpresión de este ticket, espere a su validacion o contacte a tesorería.";
        }
    }


    public static function ConsultaMovimientosDia($fecha)
    {
        if ($fecha == 'AMGM') {
            $query = <<<sql
            SELECT TAR.CODIGO, TAR.CDGTICKET_AHORRO, TAR.FREGISTRO, TAR.FREIMPRESION, TAR.MOTIVO, TAR.ESTATUS, TAR.CDGPE_SOLICITA, TAR.CDGPE_AUTORIZA, TAR.AUTORIZA, TAR.DESCRIPCION_MOTIVO, 
            TAR.AUTORIZA_CLIENTE, TA.CDG_CONTRATO 
            FROM ESIACOM.TICKETS_AHORRO_REIMPRIME TAR
            INNER JOIN TICKETS_AHORRO TA ON TA.CODIGO = TAR.CDGTICKET_AHORRO 
            
sql;
        } else {
            $query = <<<sql
            SELECT * FROM MOVIMIENTOS_AHORRO
sql;
        }


        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function getPagoAhorro($datos)
    {
        $qry = <<<SQL
            SELECT 
                PAGOSDIA,
                RETIROS_AHORRO,
                PAGOSDIA - RETIROS_AHORRO AS TOTAL,
                FECHA_APERTURA_AHORRO
            FROM (
                SELECT 
                    -- Total de pagos del día
                    (SELECT NVL(SUM(MONTO), 0)
                    FROM PAGOSDIA
                    WHERE CDGNS = :credito
                        AND ESTATUS = 'A') AS PAGOSDIA,
                        
                    -- Total de retiros de ahorro simple
                    (SELECT NVL(SUM(CANTIDAD_AUTORIZADA), 0)
                    FROM RETIROS_AHORRO
                    WHERE CDGNS = :credito) AS RETIROS_AHORRO,
                    
                    -- Fecha del primer registro tipo B o F (inicio del ahorro)
                    (SELECT MIN(FECHA)
                    FROM PAGOSDIA
                    WHERE CDGNS = :credito
                        AND TIPO IN ('B','F')) AS FECHA_APERTURA_AHORRO
                FROM DUAL
            )
        SQL;

        $params = [
            'credito' => $datos['credito']
        ];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);
            return self::Responde(true, "Pago de ahorro obtenido correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el pago de ahorro", null, $e->getMessage());
        }
    }

    public static function ReporteSolicitudesRetiro($datos)
    {
        $qry = <<<SQL
            SELECT 
                RA.ID
                , RA.CDGNS
                , RA.CANT_SOLICITADA
                , TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                , TO_CHAR(RA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                , TO_CHAR(RA.FECHA_ENTREGA, 'DD/MM/YYYY') AS FECHA_ENTREGA
                , TO_CHAR(RA.FECHA_DEVOLUCION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_DEVOLUCION
                , RA.CDGPE_ADMINISTRADORA
                , RG.CODIGO AS REGION
                , RG.NOMBRE AS NOMBRE_REGION
                , CO.CODIGO AS SUCURSAL
                , CO.NOMBRE AS NOMBRE_SUCURSAL
                , DECODE(RA.ESTATUS, 
                    'V', 'VALIDADO',
                    'C', 'CANCELADO',
                    'R', 'RECHAZADO',
                    'P', 'PENDIENTE',
                    'A', 'APROBADO',
                    'E', 'ENTREGADO',
                    'D', 'DEVUELTO',
                    NULL
                ) AS ESTATUS
            FROM 
                RETIROS_AHORRO RA
                INNER JOIN SN ON SN.CDGNS = RA.CDGNS AND SN.CICLO = RA.CICLO
                INNER JOIN SC ON SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO AND SC.CANTSOLIC <> 9999
                INNER JOIN CL ON CL.CODIGO = SC.CDGCL 
                INNER JOIN CO ON SN.CDGCO = CO.CODIGO 
                INNER JOIN RG ON CO.CDGRG = RG.CODIGO 
                INNER JOIN PE ON PE.CODIGO = SN.CDGOCPE
            WHERE 
                RA.FECHA_ENTREGA BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                FILTRO_USUARIO
            ORDER BY 
                RA.FECHA_ENTREGA ASC
        SQL;

        $prms = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF']
        ];

        if ($_SESSION['perfil'] != 'ADMIN' && $_SESSION['usuario'] !== 'LGFR') {
            $qry = str_replace('FILTRO_USUARIO', 'AND CO.CODIGO = :sucursal', $qry);
            $prms['sucursal'] = $_SESSION['cdgco'];
        } else {
            $qry = str_replace('FILTRO_USUARIO', '', $qry);
        }

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prms);
            return self::Responde(true, "Retiros obtenidos correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los retiros", null, $e->getMessage());
        }
    }

    public static function getRetirosAdmin($datos)
    {
        $qry = <<<SQL
            SELECT 
                RA.ID
                ,RA.CDGNS
                ,RA.CANT_SOLICITADA
                ,TO_CHAR(RA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                ,TO_CHAR(RA.FECHA_ENTREGA, 'DD/MM/YYYY') AS FECHA_ENTREGA
                ,TO_CHAR(RA.FECHA_ENTREGA_REAL, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ENTREGA_REAL
                ,TO_CHAR(RA.FECHA_DEVOLUCION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_DEVOLUCION
                ,RA.ESTATUS
                ,RA.CDGPE_ADMINISTRADORA
                ,RG.CODIGO AS REGION
                ,RG.NOMBRE AS NOMBRE_REGION
                ,CO.CODIGO AS SUCURSAL
                ,CO.NOMBRE AS NOMBRE_SUCURSAL
            FROM 
                RETIROS_AHORRO RA
                INNER JOIN SN ON SN.CDGNS = RA.CDGNS AND SN.CICLO = RA.CICLO
                INNER JOIN SC ON SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO AND SC.CANTSOLIC <> 9999
                INNER JOIN CL ON CL.CODIGO = SC.CDGCL 
                INNER JOIN CO ON SN.CDGCO = CO.CODIGO 
                INNER JOIN RG ON CO.CDGRG = RG.CODIGO 
                INNER JOIN PE ON PE.CODIGO = SN.CDGOCPE
                LEFT JOIN RETIROS_AHORRO_CALLCENTER RAC ON RA.ID = RAC.RETIRO
            WHERE
                TRUNC(RA.FECHA_CREACION) BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
            ORDER BY 
                RA.ID DESC
        SQL;

        $params = [
            ':fechaI' => $datos['fechaI'],
            ':fechaF' => $datos['fechaF']
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $params);
            return self::Responde(true, "Retiros obtenidos correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los retiros", null, $e->getMessage());
        }
    }

    public static function CancelarRetiro($datos)
    {
        $qry = <<<SQL
            UPDATE 
                RETIROS_AHORRO
            SET 
                ESTATUS = 'C'
                , FECHA_CANCELACION = SYSDATE
                , MOTIVO_CANCELACION = :comentario
                , CDGPE_CANCELACION = :cdgpe
            WHERE 
                ID = :retiro
        SQL;

        $params = [
            ':retiro' => $datos['retiro'],
            ':cdgpe' => $datos['usuario'],
            ':comentario' => $datos['comentario']
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $params);
            return self::Responde(true, "Retiro cancelado correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al cancelar el retiro", null, $e->getMessage());
        }
    }

    public static function confirmarEntregaRetiroAhorro($datos)
    {
        $qry = <<<SQL
            UPDATE RETIROS_AHORRO
            SET ESTATUS = 'E'
                , FECHA_ENTREGA_REAL = SYSDATE
            WHERE ID = :id
        SQL;

        $prms = ['id' => $datos['id']];

        try {
            $db = new Database();
            $db->insertar($qry, $prms);
            return self::Responde(true, "Entrega confirmada correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al confirmar la entrega", null, $e->getMessage());
        }
    }

    public static function devolverRetiro($datos)
    {
        $qry = <<<SQL
            UPDATE RETIROS_AHORRO
            SET ESTATUS = 'D'
            , FECHA_DEVOLUCION = SYSDATE
            , COMENTARIO_DEVOLUCION = :comentario
            WHERE ID = :id
        SQL;

        $prms = [
            'id' => $datos['id'],
            'comentario' => $datos['comentario']
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $prms);
            return self::Responde(true, "Retiro devuelto correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al devolver el retiro", null, $e->getMessage());
        }
    }

    public static function getInfoCorreoRetiroFinalizado($datos)
    {
        $qry = <<<SQL
            SELECT
                RA.ID
                , CL.CODIGO AS CLIENTE
                , GET_NOMBRE_CLIENTE(CL.CODIGO) AS NOMBRE_CLIENTE
                , RA.CDGNS AS CREDITO
                , TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                , TO_CHAR(RA.FECHA_ENTREGA, 'DD/MM/YYYY') AS FECHA_ENTREGA_PROGRAMADA
                , RG.CODIGO AS REGION
                , RG.NOMBRE AS NOMBRE_REGION
                , CO.CODIGO AS SUCURSAL
                , CO.NOMBRE AS NOMBRE_SUCURSAL
                , RA.ESTATUS
                , RA.COMENTARIO_DEVOLUCION
            FROM
                RETIROS_AHORRO RA
                INNER JOIN SN ON SN.CDGNS = RA.CDGNS AND SN.CICLO = RA.CICLO
                INNER JOIN SC ON SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO AND SC.CANTSOLIC <> 9999
                INNER JOIN CL ON CL.CODIGO = SC.CDGCL 
                INNER JOIN CO ON SN.CDGCO = CO.CODIGO 
                INNER JOIN RG ON CO.CDGRG = RG.CODIGO 
                INNER JOIN PE ON PE.CODIGO = SN.CDGOCPE
                LEFT JOIN RETIROS_AHORRO_CALLCENTER RAC ON RA.ID = RAC.RETIRO
            WHERE
                RA.ID = :id
        SQL;

        $prms = [
            'id' => $datos['id']
        ];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $prms);
            return self::Responde(true, 'Información de retiro obtenida', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener información de retiro', null, $e->getMessage());
        }
    }
}
