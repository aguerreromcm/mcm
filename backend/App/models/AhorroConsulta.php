<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Core\Model;

class AhorroConsulta extends Model
{
    public static function GetRetirosAhorro($datos)
    {
        $qry = <<<SQL
            SELECT 
                RA.ID
                ,RA.CDGNS
                ,RA.CANT_SOLICITADA
                ,RA.ESTATUS
                ,TO_CHAR(RA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                ,TO_CHAR(RA.FECHA_ENTREGA, 'DD/MM/YYYY') AS FECHA_ENTREGA
                ,TO_CHAR(RA.FECHA_ENTREGA_REAL, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ENTREGA_REAL
                ,TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                ,TO_CHAR(RA.FECHA_CANCELACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CANCELACION
                ,TO_CHAR(RA.FECHA_DEVOLUCION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_DEVOLUCION
                ,TO_CHAR(CASE WHEN RAC.FECHA_LLAMADA_2 IS NOT NULL THEN RAC.FECHA_LLAMADA_2 ELSE RAC.FECHA_LLAMADA_1 END, 'DD/MM/YYYY HH24:MI:SS') AS ULTIMA_LLAMADA
            FROM 
                RETIROS_AHORRO RA
                LEFT JOIN RETIROS_AHORRO_CALLCENTER RAC ON RA.ID = RAC.RETIRO
            WHERE
                TRUNC(RA.FECHA_CREACION) BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                FILTRO_USUARIO
            ORDER BY 
                RA.ID DESC
        SQL;

        $params = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF']
        ];

        if ($_SESSION['perfil'] === 'ADMIN') {
            $qry = str_replace('FILTRO_USUARIO', '', $qry);
        } else {
            $qry = str_replace('FILTRO_USUARIO', 'AND RA.CDGPE_ADMINISTRADORA = :cdgpe', $qry);
            $params['cdgpe'] = $_SESSION['usuario'];
        }

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $params);
            if ($res === false) return self::Responde(false, "Error al obtener los retiros");
            return self::Responde(true, "Retiros obtenidos correctamente", $res ?? []);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los retiros", null, $e->getMessage());
        }
    }

    public static function getRetiroById($datos)
    {
        $qry = <<<SQL
            SELECT 
                RA.ID
                ,RA.CDGNS
                ,RA.CANT_SOLICITADA
                ,TO_CHAR(RA.FECHA_SOLICITUD, 'DD/MM/YYYY') AS FECHA_SOLICITUD
                ,TO_CHAR(RA.FECHA_ENTREGA, 'DD/MM/YYYY') AS FECHA_ENTREGA
                ,TO_CHAR(RA.FECHA_ENTREGA_REAL, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ENTREGA_REAL
                ,RA.OBSERVACIONES_ADMINISTRADORA
                ,RA.MOTIVO_CANCELACION
                ,RA.COMENTARIO_DEVOLUCION
                ,RA.CDGPE_ADMINISTRADORA
                ,GET_NOMBRE_EMPLEADO(RA.CDGPE_ADMINISTRADORA) AS NOMBRE_ADMINISTRADORA
                ,TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                ,RA.ESTATUS
                ,DECODE(RA.ESTATUS, 
                    'V', 'VALIDADO',
                    'C', 'CANCELADO',
                    'R', 'RECHAZADO',
                    'P', 'PENDIENTE',
                    'A', 'APROBADO',
                    'E', 'ENTREGADO',
                    'D', 'DEVUELTO',
                    NULL
                  ) AS ESTATUS_ETIQUETA
                ,RAC.ESTATUS AS ESTATUS_CC
                ,DECODE(RAC.ESTATUS,
                    'C', 'COMPLETADO',
                    'I', 'INCOMPLETO',
                    'P', 'PENDIENTE',
                    NULL) AS ESTATUS_CC_ETIQUETA
                ,RAC.CDGPE AS CDGPE_CC
                ,RAC.COMENTARIO_EXTERNO
                ,TO_CHAR(CASE WHEN RAC.FECHA_LLAMADA_2 IS NOT NULL THEN RAC.FECHA_LLAMADA_2 ELSE RAC.FECHA_LLAMADA_1 END, 'DD/MM/YYYY HH24:MI:SS') AS ULTIMA_LLAMADA
                ,RA.TIPO_FOTO
            FROM  
                RETIROS_AHORRO RA
                LEFT JOIN RETIROS_AHORRO_CALLCENTER RAC ON RA.ID = RAC.RETIRO
            WHERE 
                RA.ID = :id
        SQL;

        $params = [':id' => $datos['id']];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);

            if (!$res) {
                return self::Responde(false, "No se encontró el retiro", null);
            }

            return self::Responde(true, "Retiro obtenido correctamente", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el retiro", null, $e->getMessage());
        }
    }

    public static function BuscarSaldo($datos)
    {
        $qry = <<<SQL
            WITH CREDITOS
            AS (
                SELECT PRC.CDGCL
                    ,PRC.CDGNS
                    , LPAD(TO_CHAR(MAX(TO_NUMBER(PRC.CICLO))), 2, '0') AS CICLO
                    , GET_SITUACION(PRC.CDGNS, LPAD(TO_CHAR(MAX(TO_NUMBER(PRC.CICLO))), 2, '0')) AS SITUACION
                FROM PRC
                WHERE PRC.CDGEM = 'EMPFIN'
                    AND PRC.CICLO NOT LIKE 'D%'
                    AND PRC.CICLO NOT LIKE 'R%'
                GROUP BY PRC.CDGCL, PRC.CDGNS
                ),
            CREDITO_ADICIONAL
            AS (
                SELECT CN.CDGCL
                    ,CN.CDGNS
                    ,CR.CICLO
                    ,CR.SITUACION
                FROM CN
                    LEFT JOIN CREDITOS CR ON CR.CDGCL = CN.CDGCL AND CR.CDGNS = CN.CDGNS
                WHERE CN.CDGEM = 'EMPFIN'
                    AND CN.ESTATUS = 'A'
                    AND CN.CDGMS IS NULL
                ORDER BY CN.INICIO DESC
                )
            SELECT 
                PRN.CDGCO
                ,CA.CDGNS
                ,CR.CICLO AS ULTIMO_CICLO_TRADICIONAL
                ,CR.SITUACION AS SITUACION_TRADICIONAL
                ,CR_AD.CDGNS AS CREDITO_ADICIONAL
                ,CR_AD.CICLO AS ULTIMO_CICLO_ADICIONAL
                ,CR_AD.SITUACION AS SITUACION_ADICIONAL
                --,0 AS ATRASO_TRADICONAL
                ,CASE WHEN PRN.SITUACION <> 'L' THEN FNCALDIASMORA('EMPFIN', CR.CDGNS, 'G', CR.CICLO) ELSE 0 END AS DIAS_MORA_TRADICIONAL
                --,0 AS DIAS_MORA_ADICIONAL
                ,CASE WHEN NOT CR_AD.CICLO IS NULL THEN FNCALDIASMORA('EMPFIN', CR_AD.CDGNS, 'G', CR_AD.CICLO) ELSE 0 END AS DIAS_MORA_ADICIONAL
                ,GET_NOMBRE_CLIENTE(CL.CODIGO) AS NOMBRE_CLIENTE
                ,FN_GET_AHORRO(CR.CDGNS) AS SALDO_ACTUAL
                ,TO_CHAR(ADD_MONTHS(CA.FECHA_REGISTRO, 12), 'YYYY-MM-DD') AS ANIVERSARIO
            FROM CONTRATOS_AHORRO CA
            INNER JOIN CREDITOS CR ON CR.CDGNS = CA.CDGNS
            INNER JOIN CL ON CL.CODIGO = CR.CDGCL
            INNER JOIN PRN ON PRN.CDGNS = CR.CDGNS AND PRN.CICLO = CR.CICLO
            LEFT JOIN CREDITO_ADICIONAL CR_AD ON CR_AD.CDGCL = CL.CODIGO AND CR_AD.CDGNS <> CA.CDGNS
            WHERE CA.CDGNS = :cdgns
        SQL;

        $params = [
            ':cdgns' => $datos['cdgns']
        ];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);

            if (!$res) return self::Responde(false, "El crédito no tiene un contrato de ahorro.", null);

            return self::Responde(true, "Saldo obtenido correctamente", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el saldo", null, $e->getMessage());
        }
    }

    public static function insertRetiro($datos)
    {
        $qry = <<<SQL
            INSERT INTO RETIROS_AHORRO (
                CDGNS
                , CICLO
                ,CANT_SOLICITADA
                ,FECHA_SOLICITUD
                ,FECHA_ENTREGA
                ,OBSERVACIONES_ADMINISTRADORA
                ,CDGPE_ADMINISTRADORA
                ,FOTO
                ,TIPO_FOTO
                ,FECHA_CREACION
            ) VALUES (
                :cdgns
                ,:ciclo
                ,:cantidad_solicitada
                ,TO_DATE(:fecha_solicitud, 'YYYY-MM-DD')
                ,TO_DATE(:fecha_entrega, 'YYYY-MM-DD')
                ,:observaciones_administradora
                ,:cdgpe_administradora
                ,EMPTY_BLOB()
                ,:tipo_foto
                ,SYSDATE
            )
            RETURNING FOTO INTO :foto
        SQL;

        $params = [
            'cdgns' => $datos['cdgns'],
            'ciclo' => $datos['ciclo'],
            'cantidad_solicitada' => $datos['cantidad_solicitada'],
            'fecha_solicitud' => $datos['fecha_solicitud'],
            'fecha_entrega' => $datos['fecha_entrega'],
            'observaciones_administradora' => $datos['observaciones_administradora'],
            'cdgpe_administradora' => $datos['cdgpe_administradora'],
            'foto' => $datos['foto'],
            'tipo_foto' => $datos['tipo_foto']
        ];

        try {
            $db = new Database();
            $db->insertarBlob($qry, $params, ['foto']);
            return self::Responde(true, "Solicitud de retiro creada correctamente");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al crear la solicitud", null, $e->getMessage());
        }
    }

    public static function getImgSolicitud($datos)
    {
        $qry = <<<SQL
            SELECT 
                FOTO
                ,TIPO_FOTO
            FROM 
                RETIROS_AHORRO
            WHERE 
                ID = :id
        SQL;

        $params = [':id' => $datos['id']];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);

            return self::Responde(true, "Imagen obtenida correctamente", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener la imagen", null, $e->getMessage());
        }
    }

    public static function getInfoCorreoCC($datos)
    {
        $qry = <<<SQL
            SELECT
                RA.ID
                , CL.CODIGO AS CLIENTE
                , GET_NOMBRE_CLIENTE(CL.CODIGO) AS NOMBRE_CLIENTE
                , RA.CDGNS AS CREDITO
                , TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                , RG.CODIGO AS REGION
                , RG.NOMBRE AS NOMBRE_REGION
                , CO.CODIGO AS SUCURSAL
                , CO.NOMBRE AS NOMBRE_SUCURSAL
                , RA.ESTATUS
                , CASE RA.ESTATUS
                    WHEN 'V' THEN 'Validado'
                    WHEN 'C' THEN 'Cancelado'
                    WHEN 'R' THEN 'Rechazado'
                    WHEN 'P' THEN 'Pendiente'
                    WHEN 'A' THEN 'Aprobado'
                    WHEN 'E' THEN 'Entregado'
                    WHEN 'D' THEN 'Devuelto'
                    ELSE NULL
                 END AS ESTATUS_ETIQUETA
                , RAC.CDGPE AS CALLCENTER
                , GET_NOMBRE_EMPLEADO(RAC.CDGPE) AS NOMBRE_CALLCENTER
                , CASE WHEN RAC.TIPO_LLAMADA_2 IS NOT NULL THEN 2 ELSE 1 END AS TOTAL_LLAMADAS
                , RAC.INTENTOS AS INTENTOS
                ,TO_CHAR(RAC.FECHA_LLAMADA_1, 'DD/MM/YYYY HH24:MI:SS') AS PRIMERA_LLAMADA
                ,TO_CHAR(CASE WHEN RAC.FECHA_LLAMADA_2 IS NULL THEN RAC.FECHA_LLAMADA_1 ELSE RAC.FECHA_LLAMADA_2 END, 'DD/MM/YYYY HH24:MI:SS') AS ULTIMA_LLAMADA
                , RAC.COMENTARIO_EXTERNO AS COMENTARIO_FINAL
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
                RA.ID = :retiro
        SQL;

        $prms = [
            'retiro' => $datos['retiro']
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
