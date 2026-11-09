<?php

namespace Core;

class Model
{
    public static function Responde($respuesta, $mensaje, $datos = null, $error = null)
    {
        $res = [
            'success' => $respuesta,
            'mensaje' => $mensaje
        ];

        if ($datos !== null) $res['datos'] = $datos;
        if ($error !== null) $res['error'] = $error;

        return $res;
    }

    public static function EjecutaSP($sp, $parametros)
    {
        $qry = <<<SQL
            CALL $sp
        SQL;

        try {
            $db = new Database();
            $res = $db->EjecutaSP_DBMS_OUTPUT($qry, $parametros);
            return self::Responde(true, "SP ejecutado correctamente", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al ejecutar el SP", null, $e->getMessage());
        }
    }

    public static function GetSucursales()
    {
        $qry = <<<SQL
            SELECT DISTINCT 
                RG.CODIGO ID_REGION,
                RG.NOMBRE REGION,
                CO.CODIGO ID_SUCURSAL,
                CO.NOMBRE SUCURSAL
            FROM
                PCO, CO, RG
            WHERE
                PCO.CDGCO = CO.CODIGO
                AND CO.CDGRG = RG.CODIGO 
                AND PCO.CDGEM = 'EMPFIN'
            ORDER BY
                    SUCURSAL ASC
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, 'Sucursales obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener sucursales', null, $e->getMessage());
        }
    }

    public static function GetDestinatarios_Usuario($usuarios)
    {
        $usuarios = is_array($usuarios) ? $usuarios : [$usuarios];
        $usuarios = implode(",", array_map(function ($u) {
            return "'$u'";
        }, $usuarios));

        $qry = <<<SQL
            SELECT DISTINCT
                CORREO
            FROM
                CORREO_DIRECTORIO
            WHERE
                USUARIO IN ($usuarios)
                AND ESTATUS = 'A'
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, 'Destinatarios obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener destinatarios', null, $e->getMessage());
        }
    }

    public static function GetDestinatarios_Sucursal($sucursales)
    {
        $sucursales = is_array($sucursales) ? $sucursales : [$sucursales];
        $sucursales = implode(",", array_map(function ($s) {
            return "'$s'";
        }, $sucursales));

        $qry = <<<SQL
            SELECT DISTINCT
                CD.CORREO
            FROM
                CORREO_GRUPO CG
                JOIN CORREO_DIRECTORIO_GRUPO CDG ON CDG.ID_GRUPO = CG.ID
                JOIN CORREO_DIRECTORIO CD ON CD.ID = CDG.ID_CORREO
            WHERE
                SUBSTR(CG.GRUPO, 1, 3) IN ($sucursales)
                AND CD.ESTATUS = 'A'
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, 'Destinatarios obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener destinatarios', null, $e->getMessage());
        }
    }

    public static function GetDestinatarios_Aplicacion($aplicacion)
    {
        $qry = <<<SQL
            SELECT DISTINCT
                CD.CORREO
            FROM
                CORREO_APLICACION_GRUPO CAG
                JOIN CORREO_DIRECTORIO_GRUPO CDG ON CAG.ID_GRUPO = CDG.ID_GRUPO
                JOIN CORREO_DIRECTORIO CD ON CD.ID = CDG.ID_CORREO
            WHERE
                CAG.ID_APLICACION = :aplicacion
                AND CD.ESTATUS = 'A'
        SQL;

        $prm = [
            'aplicacion' => $aplicacion
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Destinatarios obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener destinatarios', null, $e->getMessage());
        }
    }

    public static function GetCatalogoParentescos()
    {
        $query = 'SELECT * FROM CAT_PARENTESCO';

        try {
            $db = new Database();
            $res = $db->queryAll($query);
            return self::Responde(true, 'Parentescos obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener parentescos', null, $e->getMessage());
        }
    }
}
