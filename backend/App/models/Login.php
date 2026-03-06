<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;

class Login
{
    public static function getById($usuario)
    {
        $query1 = <<<SQL
            SELECT
                CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) NOMBRE,
                UT.CDGTUS PERFIL, PE.PUESTO , PE.CDGCO, PE.CODIGO 
            FROM
                PE,
                UT
            WHERE
                PE.CODIGO = UT.CDGPE
                AND PE.CDGEM = UT.CDGEM
                AND PE.CDGEM = 'EMPFIN'
                AND PE.ACTIVO = 'S'
                AND (PE.BLOQUEO = 'N' OR PE.BLOQUEO IS NULL)
                AND PE.CODIGO = :usuario
                AND PE.CLAVE = CODIFICA(:password)
                AND (UT.CDGTUS = 'ADMIN' ------ USUARIO ADMIN
                    OR UT.CDGTUS = 'CAJA' ------- USUARIO CAJA (EXTRA)
                    OR UT.CDGTUS = 'OCOF' ----- USUARIO OCOF
                    OR UT.CDGTUS = 'GTOCA' ------ USUARIO GERENTE SUCURSAL
                    OR UT.CDGTUS = 'AMOCA' ------ PERFIL DE CAJAS
                    OR UT.CDGTUS = 'GARAN' ------ USUARIO PARA REGISTRAR GARANTIAS
                    OR UT.CDGTUS = 'CAMAG' ------ 
                    OR UT.CDGTUS = 'CALLC' ------ USUARIO 
                    OR UT.CDGTUS = 'ACALL' ----- USUARIO ADMIN CALL CENTER
                    OR UT.CDGTUS = 'PLD' ---- USUARIO PLD CONSULTA
                    OR UT.CDGTUS = 'CPAGO' ---- USUARIO CONSULTA PAGOS
                    OR UT.CDGTUS = 'LAYOU' ---- USUARIO CONSULTA PAGOS
                )
        SQL;

        $params1 = array(
            ':usuario' => $usuario->_usuario,
            ':password' => $usuario->_password
        );

        $query_ahorro = <<<SQL
            SELECT 
                '1' as PERMISO
                , SUC_ESTADO_AHORRO.CDG_SUCURSAL AS CDGCO_AHORRO
                , HORA_APERTURA
                , HORA_CIERRE
            FROM
                SUC_CAJERA_AHORRO
                INNER JOIN SUC_ESTADO_AHORRO ON CDG_ESTADO_AHORRO = CODIGO
            WHERE
                SUC_CAJERA_AHORRO.CDG_USUARIO = :usuario
        SQL;

        $params_ahorro = array(
            ':usuario' => $usuario->_usuario,
        );

        $mysqli = new Database();
        return [$mysqli->queryOne($query1, $params1), $mysqli->queryOne($query_ahorro, $params_ahorro)];
    }

    public static function getUser($usuario)
    {
        $mysqli = new Database();
        $query = <<<sql
        SELECT
            CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) NOMBRE,
            UT.CDGTUS PERFIL, PE.PUESTO , PE.CDGCO, PE.CODIGO
        FROM
            PE,
            UT
        WHERE
            PE.CODIGO = UT.CDGPE
            AND PE.CDGEM = UT.CDGEM
            AND PE.CDGEM = 'EMPFIN'
            AND PE.ACTIVO = 'S'
            AND (PE.BLOQUEO = 'N' OR PE.BLOQUEO IS NULL)
            AND PE.CODIGO = '$usuario'

sql;

        //var_dump($query);

        return $mysqli->queryAll($query);
    }

    /**
     * Valida usuario y contraseña (doble verificación, ej. regenerar cierre diario).
     *
     * @param string $usuario Código de usuario
     * @param string $password Contraseña en texto plano (se codifica con CODIFICA en BD)
     * @return bool
     */
    public static function ValidaPassword($usuario, $password)
    {
        if (trim((string) $usuario) === '' || trim((string) $password) === '') {
            return false;
        }
        $query = <<<SQL
            SELECT COUNT(*) AS OK
            FROM PE
            WHERE PE.CDGEM = 'EMPFIN'
            AND PE.ACTIVO = 'S'
            AND (PE.BLOQUEO = 'N' OR PE.BLOQUEO IS NULL)
            AND PE.CODIGO = :usuario
            AND PE.CLAVE = CODIFICA(:password)
        SQL;
        try {
            $mysqli = new Database();
            $r = $mysqli->queryOne($query, [':usuario' => $usuario, ':password' => $password]);
            return $r && isset($r['OK']) && (int) $r['OK'] > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
