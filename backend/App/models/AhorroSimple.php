<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class AhorroSimple extends Model
{
    public static function GetInfoAhorro($datos)
    {
        $qry = <<<SQL
            SELECT CA.CDGNS
                ,CL.CODIGO AS CLIENTE
                ,GET_NOMBRE_CLIENTE(CL.CODIGO) AS NOMBRE_CLIENTE
                ,CO.CODIGO AS SUCURSAL
                ,CO.NOMBRE AS NOMBRE_SUCURSAL
                ,PE.CODIGO AS EJECUTIVO
                ,GET_NOMBRE_EMPLEADO(PE.CODIGO) AS NOMBRE_EJECUTIVO
                ,TO_CHAR(CA.FECHA_REGISTRO, 'DD/MM/YYYY') AS APERTURA
                ,TO_CHAR(ADD_MONTHS(CA.FECHA_REGISTRO, 12), 'DD/MM/YYYY') AS ANIVERSARIO
                ,CA.TASA_ANUAL AS TASA
                ,0 AS INTERES
                ,FN_GET_AHORRO(PRC.CDGNS) AS SALDO_ACTUAL
                ,NVL((SELECT SUM(MONTO)
                    FROM PAGOSDIA
                    WHERE CDGNS = CA.CDGNS
                        AND ESTATUS = 'A'
                        AND TIPO IN ('B', 'F', 'E')
                ), 0) AS ABONOS
                ,NVL((SELECT SUM(RA.CANT_SOLICITADA)
                    FROM RETIROS_AHORRO RA
                    WHERE RA.CDGNS = CA.CDGNS
                    AND RA.ESTATUS = 'E'), 0) +
                    NVL((SELECT SUM(MONTO)
                    FROM PAGOSDIA
                    WHERE CDGNS = CA.CDGNS
                        AND ESTATUS = 'A'
                        AND TIPO = 'A'
                ), 0) AS RETIROS
                ,NVL((SELECT SUM(RA.CANT_SOLICITADA)
                    FROM RETIROS_AHORRO RA
                    WHERE RA.CDGNS = CA.CDGNS
                    AND RA.ESTATUS IN ('P', 'V')
                ), 0) AS RETIROS_TRANSITO
            FROM CONTRATOS_AHORRO CA
                INNER JOIN PRC ON PRC.CDGNS = CA.CDGNS
                INNER JOIN PRN ON PRN.CDGNS = PRC.CDGNS AND PRN.CICLO = PRC.CICLO
                INNER JOIN CL ON CL.CODIGO = PRC.CDGCL
                INNER JOIN CO ON PRN.CDGCO = CO.CODIGO
                INNER JOIN PE ON PE.CODIGO = PRN.CDGOCPE
            WHERE CA.CDGNS = :credito
                AND PRC.CICLO NOT LIKE 'D%'
                AND PRC.CICLO NOT LIKE 'R%'
            ORDER BY
                TO_NUMBER(PRC.CICLO) DESC
            FETCH FIRST 1 ROWS ONLY
        SQL;

        $params = [
            'credito' => $datos['credito']
        ];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);
            return self::Responde(true, 'Datos de ahorro obtenidos', $res);
        } catch (\Exception $e) {
            return  self::Responde(false, 'Error al obtener datos de ahorro', null, $e->getMessage());
        }
    }

    public static function GetMovimientosAhorro($datos)
    {
        $qry = <<<SQL
            SELECT * FROM (
                SELECT CASE PD.TIPO WHEN 'A' THEN 'RETIRO' ELSE 'ABONO' END AS TIPO
                    ,TIPO_OPERACION(PD.TIPO) AS DESCRIPCION
                    ,TO_CHAR(PD.FECHA, 'DD/MM/YYYY') AS APLICACION
                    ,TO_CHAR(PD.FREGISTRO, 'DD/MM/YYYY HH24:MI:SS') AS REGISTRO
                    ,PD.MONTO
                    ,PD.EJECUTIVO
                FROM PAGOSDIA PD
                WHERE PD.CDGEM = 'EMPFIN'
                    AND PD.ESTATUS = 'A'
                    AND PD.TIPO IN ('B' ,'F', 'E', 'A')
                    AND PD.CDGNS = :credito
                UNION
                SELECT 'RETIRO' AS TIPO
                    ,CASE
                        WHEN RA.ESTATUS = 'C' THEN 'CANCELADO'
                        WHEN RA.ESTATUS = 'R' THEN 'RECHAZADO'
                        WHEN RA.ESTATUS = 'D' THEN 'DEVUELTO'
                        WHEN RA.ESTATUS IN ('V', 'P', 'A') THEN 'EN TRÁNSITO'
                        WHEN RA.ESTATUS = 'E' THEN 'ENTREGADO'
                        ELSE 'DESCONOCIDO'
                    END AS DESCRIPCION
                    ,TO_CHAR(CASE
                        WHEN RA.ESTATUS = 'E' THEN RA.FECHA_ENTREGA_REAL
                        WHEN RA.ESTATUS = 'D' THEN RA.FECHA_DEVOLUCION
                        WHEN RA.ESTATUS IN ('C', 'R') THEN RA.FECHA_CANCELACION
                        ELSE RA.FECHA_CREACION
                    END, 'DD/MM/YYYY') AS APLICACION
                    ,TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS REGISTRO
                    ,RA.CANT_SOLICITADA AS MONTO
                    ,GET_NOMBRE_EMPLEADO(RA.CDGPE_ADMINISTRADORA) AS EJECUTIVO
                FROM RETIROS_AHORRO RA
                WHERE RA.CDGNS = :credito
            )
            ORDER BY TO_DATE(REGISTRO, 'DD/MM/YYYY HH24:MI:SS') DESC
        SQL;

        $params = [
            'credito' => $datos['credito']
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $params);
            return self::Responde(true, 'Movimientos de ahorro obtenidos', $res);
        } catch (\Exception $e) {
            return  self::Responde(false, 'Error al obtener movimientos de ahorro', null, $e->getMessage());
        }
    }

    public static function ConsultarPagosFechaSucursal($cdgns)
    {
        $query_datos = <<<SQL
            SELECT
                PD.PAGOSDIA,
                PD.RETIROS_AHORRO_SIMPLE,
                PD.PAGOSDIA - PD.RETIROS_AHORRO_SIMPLE AS TOTAL,
                PD.FECHA_APERTURA_AHORRO,
                C.NO_CREDITO,
                C.CICLO,
                C.CLIENTE,
                C.ID_SUCURSAL,
                C.SUCURSAL,
                C.ID_EJECUTIVO,
                C.EJECUTIVO
            FROM (
                SELECT 
                    -- Total de pagos del día
                    (SELECT NVL(SUM(MONTO), 0)
                    FROM PAGOSDIA
                    WHERE CDGNS = '$cdgns'
                        AND ESTATUS = 'A' AND TIPO IN('B', 'F')) AS PAGOSDIA,
                        
                    -- Total de retiros de ahorro simple
                    (SELECT NVL(SUM(CANTIDAD_AUTORIZADA), 0)
                    FROM RETIROS_AHORRO_SIMPLE
                    WHERE CDGNS = '$cdgns') AS RETIROS_AHORRO_SIMPLE,
                    
                    -- Fecha del primer registro tipo B o F (inicio del ahorro)
                    (SELECT MIN(FECHA)
                    FROM PAGOSDIA
                    WHERE CDGNS = '$cdgns'
                        AND TIPO IN ('B','F')) AS FECHA_APERTURA_AHORRO
                FROM DUAL
            ) PD
            CROSS JOIN (
                SELECT *
                FROM (
                    SELECT 
                        SC.CDGNS NO_CREDITO,
                        SC.CICLO,
                        GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
                        SN.CDGCO ID_SUCURSAL,
                        GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
                        SN.CDGOCPE ID_EJECUTIVO,
                        GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO
                    FROM 
                        SN
                        JOIN SC ON SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO
                        JOIN SC Q2 ON SC.CDGNS = Q2.CDGNS AND SC.CICLO = Q2.CICLO AND SC.CDGCL <> Q2.CDGCL
                        JOIN PRN ON PRN.CICLO = SC.CICLO AND PRN.CDGNS = SC.CDGNS
                    WHERE
                        SC.CDGNS = '$cdgns'
                        AND SC.CANTSOLIC <> '9999'
                    ORDER BY SC.SOLICITUD DESC
                )
                WHERE ROWNUM = 1
            ) C
        SQL;


        $query = <<<SQL
            SELECT
                RG.CODIGO ID_REGION,
                RG.NOMBRE REGION,
                NS.CDGCO ID_SUCURSAL,
                GET_NOMBRE_SUCURSAL(NS.CDGCO) AS NOMBRE_SUCURSAL,
                PAGOSDIA.SECUENCIA,
                PAGOSDIA.FECHA,
                PAGOSDIA.CDGNS,
                PAGOSDIA.MONTO,
                TIPO_OPERACION(PAGOSDIA.TIPO) as TIPO,
                PAGOSDIA.TIPO AS TIP,
                PAGOSDIA.CICLO,
                PAGOSDIA.EJECUTIVO,
                PAGOSDIA.CDGOCPE,
                TO_CHAR(PAGOSDIA.FREGISTRO ,'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO,
                'ABONO' AS TIPO_OPERA
            FROM
                PAGOSDIA, NS, CO, RG
            WHERE
                PAGOSDIA.CDGEM = 'EMPFIN'
                AND PAGOSDIA.ESTATUS = 'A'
                AND NS.CODIGO = PAGOSDIA.CDGNS
                AND NS.CDGCO = CO.CODIGO 
                AND CO.CDGRG = RG.CODIGO
                AND PAGOSDIA.TIPO IN ('B', 'F')
                AND PAGOSDIA.CDGNS = $cdgns
            ORDER BY
                FREGISTRO DESC, SECUENCIA
        SQL;
        $mysqli = new Database();

        $res1 = $mysqli->queryOne($query_datos);
        $res2 = $mysqli->queryAll($query);
        return [$res1, $res2];
    }

    public static function ConsultarActivosExcepciones($cdgns)
    {
        $query = <<<sql
        SELECT
       *
    FROM
        EXCEPCIONES_CREDITO
    WHERE
        CDGNS = $cdgns
sql;
        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function ActualizaExcepciones($datos)
    {
        // $datos es un stdClass, no un array
        $cdgns = $datos->_no_credito;
        $ciclo = $datos->_ciclo;

        $uno = $datos->_exc_uno;
        $dos = $datos->_exc_dos;
        $tres = $datos->_exc_tres;
        $cuatro = $datos->_exc_cuatro;
        $cinco = $datos->_exc_cinco;
        $seis = $datos->_exc_seis;

        $mysqli = new Database();

        // Buscar si ya existe una excepción activa
        $query_datos = <<<sql
        SELECT ID_EXCEPCION
        FROM ESIACOM.EXCEPCIONES_CREDITO
        WHERE CDGNS = '$cdgns'
          AND CICLO = '$ciclo'
          AND ESTATUS = 'ACTIVO'
          AND ROWNUM = 1
sql;

        $res1 = $mysqli->queryOne($query_datos);

        // Si NO existe → INSERT
        if (!$res1) {

            $query = <<<sql
            INSERT INTO ESIACOM.EXCEPCIONES_CREDITO
            (CDGNS, CICLO, EXC_UNO, EXC_DOS, EXC_TRES, EXC_CUATRO, EXC_CINCO, EXC_SEIS, ESTATUS, FECHA_CARGA, USUARIO_CARGA)
            VALUES
            ('$cdgns', '$ciclo', '$uno','$dos','$tres','$cuatro','$cinco','$seis','ACTIVO', SYSTIMESTAMP, NULL)
sql;

            $mysqli->queryAll($query);
            return "INSERT";
        } else {

            // Si existe → UPDATE
            $id = $res1->ID_EXCEPCION;  // porque queryOne también devuelve stdClass

            $query = <<<sql
            UPDATE ESIACOM.EXCEPCIONES_CREDITO
            SET EXC_UNO='$uno',
                EXC_DOS='$dos',
                EXC_TRES='$tres',
                EXC_CUATRO='$cuatro',
                EXC_CINCO='$cinco',
                EXC_SEIS='$seis',
                FECHA_CARGA=SYSTIMESTAMP,
                USUARIO_CARGA=NULL
            WHERE CDGNS = '$cdgns'
          AND CICLO = '$ciclo'
sql;

            return $mysqli->insert($query);
        }
    }

    public static function ProcesaProcedure($credito_, $ciclo_)
    {

        $mysqli = new Database();
        return $mysqli->queryValidacionClienteCO($credito_, $ciclo_);
    }

    public static function ListarClientesSinContrato($user)
    {

        // 1️⃣ Obtiene las sucursales asignadas al usuario
        $query_obten_sucu = "SELECT CDGCO FROM PCO WHERE CDGPE = '$user'";

        $mysqli = new Database();
        $sucursales = $mysqli->queryAll($query_obten_sucu);

        // 2️⃣ Convierte los resultados en una lista para el IN
        $listaSucursales = [];

        if (!empty($sucursales)) {
            foreach ($sucursales as $row) {
                $listaSucursales[] = "'" . $row['CDGCO'] . "'";
            }
        }

        // Si el usuario no tiene sucursales asignadas, evita error en SQL
        if (empty($listaSucursales)) {
            $listaSucursales[] = "'NULL'";
        }

        $in_sucursales = implode(",", $listaSucursales);

        // 3️⃣ Consulta principal, ahora filtrando por las sucursales del usuario
        $query = <<<SQL
        SELECT 
            P.CDGNS,
            P.NOMBRE,
            PRN.CDGCO,
            CO.NOMBRE AS SUCURSAL
        FROM 
            PAGOSDIA P
            INNER JOIN PRN ON PRN.CDGNS = P.CDGNS AND PRN.CICLO = P.CICLO
            INNER JOIN CO ON CODIGO = PRN.CDGCO
        WHERE 
            P.TIPO IN ('F', 'B')
            AND ESTATUS = 'A'
            AND PRN.CDGCO IN ($in_sucursales)
            AND NOT EXISTS (
                SELECT 1 FROM CONTRATOS_AHORRO C WHERE C.CDGNS = P.CDGNS
            )
        GROUP BY 
            P.CDGNS, P.NOMBRE, PRN.CDGCO, CO.NOMBRE
        ORDER BY 
            P.NOMBRE
    SQL;

        // 4️⃣ Retorna el resultado
        return $mysqli->queryAll($query);
    }



    public static function insertContrato($contrato)
    {
        $mysqli = new Database();

        // 1. Obtenemos el siguiente valor de la secuencia
        $idQuery = "SELECT CONTRATOS_AHORRO_SEQQ.NEXTVAL AS ID_CONTRATO FROM DUAL";
        $idData = $mysqli->queryOne($idQuery);
        $id_contrato = $idData['ID_CONTRATO'];

        // 2. Insertamos el contrato usando ese ID
        $query = <<<sql
            INSERT INTO CONTRATOS_AHORRO
            (ID_CONTRATO, CDGNS, FECHA_REGISTRO, TIPO_AHORRO, TASA_ANUAL, CDGPE)
            VALUES(
                '{$id_contrato}', 
                '{$contrato->_cdgns}', 
                TO_DATE('{$contrato->_fecha_registro}', 'YYYY-MM-DD HH24:MI:SS'),
                '{$contrato->_tipo_ahorro}',
                '{$contrato->_tasa_anual}',
                'AMGM'
            )
        sql;

        $mysqli->insert($query);
        return $id_contrato;
    }

    public static function insertBeneficiarios($beneficiarios, $idContrato, $cdgpe_alta, $cdgpe)
    {
        $mysqli = new Database();
        $insertados = 0;

        foreach ($beneficiarios as $benef) {
            $nombre = substr(str_replace("'", "''", $benef['nombre']), 0, 100);
            $parentesco = substr(str_replace("'", "''", $benef['parentesco']), 0, 30);
            $porcentaje = floatval($benef['porcentaje']);
            $cdgpe_alta = substr(str_replace("'", "''", $cdgpe_alta), 0, 10);
            $cdgpe = substr(str_replace("'", "''", $cdgpe), 0, 10);

            $query = <<<sql
                INSERT INTO CONTRATOS_BENEFICIARIOS
                (ID_CONTRATO, NOMBRE_COMPLETO, PARENTESCO, PORCENTAJE, CDGPE_ALTA, CDGPE)
                VALUES
                ($idContrato, '$nombre', '$parentesco', $porcentaje, '$cdgpe_alta', '$cdgpe')
            sql;

            $mysqli->insert_bene($query);
            $insertados++;
        }

        return $insertados;
    }

    public static function GetContrato($datos)
    {
        $query = <<<SQL
            SELECT
                COUNT(*) AS EXISTE
            FROM 
                CONTRATOS_AHORRO CA
            WHERE 
                CA.CDGNS = :credito
        SQL;

        $params = ['credito' => $datos['credito']];

        try {
            $db = new Database();
            $res = $db->queryOne($query, $params);
            return self::Responde(true, 'Cliente obtenido', $res);
        } catch (\Exception $e) {
            return  self::Responde(false, 'Error al obtener cliente', null, $e->getMessage());
        }
    }

    public static function GetCliente($datos)
    {
        $query = <<<SQL
            SELECT
                CL.CODIGO AS CLIENTE
                , CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE
            FROM 
                PRC
                LEFT JOIN CL ON PRC.CDGCL = CL.CODIGO
                INNER JOIN PRN ON PRN.CDGNS = PRC.CDGNS AND PRN.CICLO = PRC.CICLO
            WHERE 
                PRC.CDGNS = :credito
                AND PRN.SITUACION = 'E'
            FETCH FIRST 1 ROWS ONLY
        SQL;

        $params = ['credito' => $datos['credito']];

        try {
            $db = new Database();
            $res = $db->queryOne($query, $params);
            return self::Responde(true, 'Cliente obtenido', $res);
        } catch (\Exception $e) {
            return  self::Responde(false, 'Error al obtener cliente', null, $e->getMessage());
        }
    }

    public static function RegistraContrato($datos)
    {
        $qryContrato = <<<SQL
            INSERT INTO CONTRATOS_AHORRO
                (CDGNS, TIPO_AHORRO, TASA_ANUAL, CDGPE_ALTA, CDGPE)
            VALUES
                (:credito, :tipo, :tasa, :cdgpe_alta, :cdgpe)
        SQL;

        $qryBeneficiario = <<<SQL
            INSERT INTO CONTRATOS_BENEFICIARIOS
                (ID_CONTRATO, NOMBRE_COMPLETO, PARENTESCO, PORCENTAJE, CDGPE_ALTA, CDGPE)
            VALUES
                ((SELECT ID_CONTRATO FROM CONTRATOS_AHORRO WHERE CDGNS = :credito), :nombre, :parentesco, :porcentaje, :cdgpe_alta, :cdgpe)
        SQL;

        $inserts = [];
        $datosInsert = [];

        $inserts[] = $qryContrato;
        $datosInsert[] = [
            'credito' => $datos['noCredito'],
            'tipo' => $datos['tipo'],
            'tasa' => $datos['tasa'],
            'cdgpe_alta' => $datos['ejecutivo'],
            'cdgpe' => $datos['ejecutivo']
        ];

        foreach ($datos['beneficiario_parentesco'] as $index => $p) {
            $inserts[] = $qryBeneficiario;
            $datosInsert[] = [
                'credito' => $datos['noCredito'],
                'nombre' => $datos['beneficiario_nombre'][$index],
                'parentesco' => $datos['beneficiario_parentesco'][$index],
                'porcentaje' => $datos['beneficiario_porcentaje'][$index],
                'cdgpe_alta' => $datos['ejecutivo'],
                'cdgpe' => $datos['ejecutivo']
            ];
        }

        try {
            $db = new Database();
            $res = $db->insertaMultiple($inserts, $datosInsert);
            if ($res) return self::Responde(true, "Contrato de ahorro registrado correctamente", $res);
            return self::Responde(false, "Ocurrió un error al registrar el contrato de ahorro.");
        } catch (\Exception $e) {
            return  self::Responde(false, 'Error al registrar el contrato de ahorro', null, $e->getMessage());
        }
    }

    public static function GetBeneficiarios($datos)
    {
        $query = <<<SQL
            SELECT
                NOMBRE_COMPLETO,
                PARENTESCO,
                PORCENTAJE
            FROM 
                CONTRATOS_BENEFICIARIOS
            WHERE 
                ID_CONTRATO = (SELECT ID_CONTRATO FROM CONTRATOS_AHORRO WHERE CDGNS = :credito)
        SQL;

        $params = ['credito' => $datos['credito']];

        try {
            $db = new Database();
            $res = $db->queryAll($query, $params);
            return self::Responde(true, 'Beneficiarios obtenidos', $res);
        } catch (\Exception $e) {
            return  self::Responde(false, 'Error al obtener beneficiarios', null, $e->getMessage());
        }
    }

    public static function ActualizaBeneficiarios($datos)
    {
        $qryDelete = <<<SQL
            DELETE FROM CONTRATOS_BENEFICIARIOS
            WHERE ID_CONTRATO = (SELECT ID_CONTRATO FROM CONTRATOS_AHORRO WHERE CDGNS = :credito)
        SQL;

        $qryBeneficiario = <<<SQL
            INSERT INTO CONTRATOS_BENEFICIARIOS
                (ID_CONTRATO, NOMBRE_COMPLETO, PARENTESCO, PORCENTAJE, CDGPE_ALTA, CDGPE)
            VALUES
                ((SELECT ID_CONTRATO FROM CONTRATOS_AHORRO WHERE CDGNS = :credito), :nombre, :parentesco, :porcentaje, :cdgpe_alta, :cdgpe)
        SQL;

        $inserts = [];
        $datosInsert = [];

        $inserts[] = $qryDelete;
        $datosInsert[] = [
            'credito' => $datos['noCredito']
        ];

        foreach ($datos['beneficiario_parentesco'] as $index => $p) {
            $inserts[] = $qryBeneficiario;
            $datosInsert[] = [
                'credito' => $datos['noCredito'],
                'nombre' => $datos['beneficiario_nombre'][$index],
                'parentesco' => $datos['beneficiario_parentesco'][$index],
                'porcentaje' => $datos['beneficiario_porcentaje'][$index],
                'cdgpe_alta' => $datos['ejecutivo'],
                'cdgpe' => $datos['ejecutivo']
            ];
        }

        try {
            $db = new Database();
            $res = $db->insertaMultiple($inserts, $datosInsert);
            if ($res) return self::Responde(true, "Beneficiarios actualizados correctamente", $res);
            return self::Responde(false, "Ocurrió un error al actualizar los beneficiarios.");
        } catch (\Exception $e) {
            return  self::Responde(false, 'Error al actualizar los beneficiarios', null, $e->getMessage());
        }
    }
}
