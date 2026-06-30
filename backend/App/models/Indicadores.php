<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Model;
use Core\Database;

class Indicadores extends Model
{
    public static function GetIncidenciasUsuarios()
    {
        $ini = new \DateTime('first day of this month');
        $ini->modify('-12 months');
        $fin = new \DateTime('last day of this month');
        $prm = [
            'fechaI' => $ini->format('Y-m-d'),
            'fechaF' => $fin->format('Y-m-d'),
        ];

        try {
            $db = new Database();
            $res = $db->queryAll(IncidenciasAgregadoQuery::sqlGetIncidenciasUsuarios(), $prm);
            if ($res === false) {
                return self::Responde(false, 'Error al obtener incidencias de usuario', null, 'Error en consulta a base de datos');
            }
            return self::Responde(true, 'Incidencias de usuario obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener incidencias de usuario', null, $e->getMessage());
        }
    }

    public static function GetIncidenciasUsuario($datos)
    {
        $q1 = IncidenciasDetalleQuery::sqlQ1();
        $qry = "SELECT
                    TO_CHAR(Q1.FECHA, 'DD/MM/YY HH24:MI') AS FECHA,
                    Q1.CDGNS,
                    Q1.CICLO,
                    Q1.MONTO,
                    Q1.REFERENCIA AS DESCRIPCION,
                    Q1.TIPO,
                    Q1.REGION,
                    Q1.SUCURSAL
            FROM (
                {$q1}
            ) Q1
            JOIN PE ON Q1.CDGPE = PE.CODIGO
            WHERE
                Q1.FECHA BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                AND Q1.CDGPE = :usuario
            ORDER BY
                Q1.FECHA DESC";

        $prm = [
            'fechaI' => $datos['fechaI'] ?? '',
            'fechaF' => $datos['fechaF'] ?? '',
            'usuario' => $datos['usuario'] ?? '',
        ];

        if ($prm['fechaI'] === '' || $prm['fechaF'] === '' || $prm['usuario'] === '') {
            return self::Responde(false, 'Parámetros incompletos para consulta de usuario');
        }

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            if ($res === false) {
                return self::Responde(false, 'Error al obtener incidencias de usuario', null, 'Error en consulta a base de datos');
            }
            return self::Responde(true, 'Incidencias de usuario obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener incidencias de usuario', null, $e->getMessage());
        }
    }
}
