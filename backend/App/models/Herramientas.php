<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class Herramientas extends Model
{
    /**
     * Reporte de días de atraso (PRN situación L).
     * Opcional: filtrar desde el primer día de un mes/año hasta la fecha actual.
     *
     * @param array $datos Opcional: 'mes' (1-12), 'anio' (ej. 2025)
     * @return array { success, mensaje, datos }
     */
    public static function GetRepDiaAtraso($datos = [])
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

        $prm = [];
        $mes = isset($datos['mes']) ? (int) $datos['mes'] : 0;
        $anio = isset($datos['anio']) ? (int) $datos['anio'] : 0;
        if ($mes >= 1 && $mes <= 12 && $anio >= 2000 && $anio <= 2100) {
            $qry .= ' AND PRN.INICIO >= TO_DATE(:fechaDesde, \'YYYY-MM-DD\')';
            $prm['fechaDesde'] = sprintf('%04d-%02d-01', $anio, $mes);
        }

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Consulta exitosa', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al consultar el reporte', null, $e->getMessage());
        }
    }
}
