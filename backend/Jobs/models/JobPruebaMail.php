<?php

namespace Jobs\models;

include_once dirname(__DIR__) . "/../Core/Model.php";
include_once dirname(__DIR__) . "/../Core/Database.php";

use Core\Model;
use Core\Database;

class JobPruebaMail extends Model
{
    public static function getUsuraio()
    {
        $qry = <<<SQL
            SELECT
                CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS PATRON
            FROM
                PE
            WHERE
                PE.CODIGO = 'AMGM'
                AND PE.CDGEM = 'EMPFIN'
        SQL;

        try {
            $db = new Database();
            $res = $db->queryOne($qry);
            return self::Responde(true, "Quien es el patrón?", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error", null, $e->getMessage());
        }
    }
}
