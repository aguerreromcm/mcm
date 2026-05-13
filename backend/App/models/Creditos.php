<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class Creditos extends Model
{
    public static function ConsultaGarantias($datos)
    {
        $qryVal = <<<SQL
            SELECT
                SC.CDGNS NO_CREDITO
            FROM
                SC
            WHERE
                SC.CDGEM = 'EMPFIN'
                AND SC.CDGNS = :credito
        SQL;

        $qry = <<<SQL
            SELECT
                SECUENCIA,
                ARTICULO,
                MARCA,
                MODELO,
                SERIE NO_SERIE,
                MONTO,
                FACTURA,
                TO_CHAR(FECREGISTRO ,'DD/MM/YYYY') AS FECHA
            FROM
                GARPREN
            WHERE 
                CDGEM = 'EMPFIN'
                AND ESTATUS = 'A'
                AND CDGNS = :credito
        SQL;

        $prm = [
            'credito' => $datos['credito']
        ];

        try {
            $db = new Database();
            $val = $db->queryOne($qryVal, $prm);

            if (!$val) return self::Responde(false, 'Crédito no encontrado', null, 'Crédito no encontrado');

            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Garantías encontradas', $res, $val);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al buscar garantías', null, $e->getMessage());
        }
    }

    public static function ProcedureGarantias($datos)
    {
        $credito = $datos['credito'];
        $articulo = $datos['articulo'];
        $marca = $datos['marca'];
        $modelo = $datos['modelo'];
        $serie = $datos['serie'];
        $factura = $datos['factura'];
        $usuario = $datos['usuario'];
        $valor = $datos['valor'];

        $db = new Database();
        $res = $db->queryProcedureInsertGarantias($credito, $articulo, $marca, $modelo, $serie, $factura, $usuario, $valor, 1);

        if (substr($res, 0, 1) == 0) return self::Responde(false, 'Error al insertar garantía', null, $res);
        else return self::Responde(true, 'Garantía insertada correctamente', $res);
    }

    public static function ProcedureGarantiasUpdate($datos)
    {
        $credito = $datos['credito'];
        $articulo = $datos['articulo'];
        $marca = $datos['marca'];
        $modelo = $datos['modelo'];
        $serie = $datos['serie'];
        $factura = $datos['factura'];
        $usuario = $datos['usuario'];
        $valor = $datos['valor'];
        $secuencia = $datos['secuencia'];

        $db = new Database();
        $res = $db->queryProcedureUpdatesGarantias($credito, $articulo, $marca, $modelo, $serie, $factura, $usuario, $valor, $secuencia);

        if (substr($res, 0, 1) == 0) return self::Responde(false, 'Error al actualizar garantía', null, $res);
        else return self::Responde(true, 'Garantía actualizada correctamente', $res);
    }

    public static function ProcedureGarantiasDelete($datos)
    {

        $credito = $datos['credito'];
        $secuencia = $datos['secuencia'];

        $mysqli = new Database();
        $res = $mysqli->queryProcedureDeleteGarantias($credito, $secuencia, 3);

        if (substr($res, 0, 1) == 0) return self::Responde(false, 'Error al eliminar garantía', null, $res);
        else return self::Responde(true, 'Garantía eliminada correctamente', $res);
    }

    public static function ActualizacionCredito($noCredito)
    {
        $mysqli = new Database();
        $query = <<<sql
SELECT
    GARPREN.SECUENCIA,
    GARPREN.ARTICULO,
    GARPREN.MARCA,
    GARPREN.MODELO,
    GARPREN.SERIE NO_SERIE,
    GARPREN.MONTO,
    GARPREN.FACTURA
FROM
    GARPREN
WHERE 
	GARPREN.CDGEM = 'EMPFIN'
	AND GARPREN.ESTATUS = 'A'
	AND GARPREN.CDGNS = '$noCredito'

sql;

        return $mysqli->queryAll($query);
    }

    public static function SelectSucursalAllCreditoCambioSuc($noCredito, $ciclo = null)
    {
        $noCredito = self::normalizarNumeroCredito($noCredito);
        if ($noCredito === '') {
            return null;
        }

        $ciclo = $ciclo !== null ? trim((string) $ciclo) : '';
        $filtroCiclo = $ciclo !== '' ? "       AND SC.CICLO = '$ciclo'\n" : '';

        $query = <<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		SC.SITUACION,
		SN.PLAZOSOL PLAZO,
		CALCULA_PARCIALIDAD(SN.PERIODICIDAD, SN.TASA, NVL(SC.CANTAUTOR,SC.CANTSOLIC), SN.PLAZOSOL) PARCIALIDAD,
		Q2.CDGCL ID_AVAL,
		GET_NOMBRE_CLIENTE(Q2.CDGCL) AVAL,
		SN.CDGCO ID_SUCURSAL,
		GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
		SN.CDGOCPE ID_EJECUTIVO,
		GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO
	FROM 
		SN, SC, SC Q2, PRN 
	WHERE
		SC.CDGNS = '$noCredito'
$filtroCiclo		AND SC.CDGNS = Q2.CDGNS
		AND SC.CICLO = Q2.CICLO
		AND SC.CDGCL <> Q2.CDGCL
		AND SC.CDGNS = SN.CDGNS
		AND SC.CICLO = SN.CICLO
	  	AND SC.CICLO !='R1' 
		AND SC.CICLO != 'R2'
		AND SC.CICLO != 'R3'
		AND SC.CICLO != 'R4'
		AND SC.CICLO != 'R5'
		AND SC.CICLO != 'R6'
		AND SC.CICLO != 'R7'
		AND PRN.CDGNS = SN.CDGNS
		AND PRN.CICLO = SN.CICLO
		AND PRN.SITUACION != 'T'
		AND SC.CANTSOLIC <> '9999' order by SN.INICIO DESC
sql;


        $mysqli = new Database();
        return $mysqli->queryOne($query);
    }

    private static function normalizarNumeroCredito($noCredito): string
    {
        $noCredito = trim((string) $noCredito);
        if ($noCredito === '') {
            return '';
        }

        if (ctype_digit($noCredito) && strlen($noCredito) < 6) {
            return str_pad($noCredito, 6, '0', STR_PAD_LEFT);
        }

        return $noCredito;
    }

    public static function ListaSucursales()
    {
        //////cambiar el parametro CDGPE
        $query = <<<sql
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
sql;

        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function UpdateSucursal($sucursal_c)
    {

        $credito = $sucursal_c->_credito;
        $ciclo = $sucursal_c->_ciclo;
        $nueva_sucursal = $sucursal_c->_nueva_sucursal;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaSucursal($credito, $ciclo, $nueva_sucursal);
    }

    public static function ConsultarPagosAdministracionOne($noCredito)
    {

        $query = <<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		PRN.SITUACION,
		SN.PLAZOSOL PLAZO,
		SN.PERIODICIDAD,
		SN.TASA,
		DIA_PAGO(SN.NOACUERDO) DIA_PAGO,
		CALCULA_PARCIALIDAD(SN.PERIODICIDAD, SN.TASA, NVL(SC.CANTAUTOR,SC.CANTSOLIC), SN.PLAZOSOL) PARCIALIDAD,
		Q2.CDGCL ID_AVAL,
		GET_NOMBRE_CLIENTE(Q2.CDGCL) AVAL,
		SN.CDGCO ID_SUCURSAL,
		GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
		SN.CDGOCPE ID_EJECUTIVO,
		GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO,
		SC.CDGPI ID_PROYECTO
	FROM 
		SN, SC, SC Q2, PRN 
	WHERE
		SC.CDGNS = '$noCredito'
		AND SC.CDGNS = Q2.CDGNS
		AND SC.CICLO = Q2.CICLO
		AND SC.CDGCL <> Q2.CDGCL
		AND SC.CDGNS = SN.CDGNS
		AND SC.CICLO = SN.CICLO
	    AND PRN.CICLO = SC.CICLO 
		AND PRN.CDGNS = SC.CDGNS 
		AND SC.CANTSOLIC <> '9999' order by SC.SOLICITUD  desc
sql;


        $mysqli = new Database();
        return $mysqli->queryOne($query);
    }
    ///////////////////////////////////////////////////////////////////////////////////////////
    public static function UpdateActulizaCredito($credito_c)
    {

        $credito = $credito_c->_credito;
        $credito_n = $credito_c->_credito_nuevo;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaNumCredito($credito, $credito_n);
    }
    public static function UpdateActulizaCiclo($credito_c)
    {

        $credito = $credito_c->_credito;
        $ciclo_nuevo = $credito_c->_ciclo_nuevo;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaNumCreditoCiclo($credito, $ciclo_nuevo);
    }
    public static function UpdateActulizaSituacion($credito_c)
    {

        $credito = $credito_c->_credito;
        $ciclo_nuevo = $credito_c->_ciclo_nuevo;
        $situacion = $credito_c->_situacion;

        $mysqli = new Database();
        return $mysqli->queryProcedureActualizaNumCreditoSituacion($credito, $ciclo_nuevo, $situacion);
    }

    public static function GetCierreDiario($fecha)
    {
        $qry = <<<SQL
        SELECT
            CO.NOMBRE AS SUCURSAL,
            CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE_ASESOR,
            PRN.CDGNS AS CODIGO_GRUPO,
            CL.CODIGO AS CODIGO_CLIENTE,
            CL.CURP AS CURP_CLIENTE,
            CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) AS NOMBRE_COMPLETO_CLIENTE,
            CL_AVAL.CODIGO AS CODIGO_AVAL,
            CL_AVAL.CURP AS CURP_AVAL,
            CONCATENA_NOMBRE(CL_AVAL.NOMBRE1, CL_AVAL.NOMBRE2, CL_AVAL.PRIMAPE, CL_AVAL.SEGAPE) AS NOMBRE_COMPLETO_AVAL,
            PRN.CICLO AS CICLO,
            TO_CHAR(PRN.INICIO, 'DD/MM/YYYY') AS FECHA_INICIO,
            CD.SDO_TOTAL AS SALDO_TOTAL,
            CD.MORA_TOTAL AS MORA_TOTAL,
            CD.DIAS_MORA AS DIAS_MORA,
             -- NUEVA COLUMNA DIAS_ATRASO
            ESIACOM.FNCALDIASATRASO(
                'EMPFIN',
                PRN.CDGNS,
                PRN.CICLO,
                'G',                           -- puedes reemplazar con PRMCLNS si lo tienes
                TO_DATE('$fecha','YYYY-MM-DD')
            ) AS DIAS_ATRASO,
            CASE
                WHEN (SYSDATE > PRN.INICIO + (DURACINI * 7)) THEN 'VENCIDO'
                ELSE 'VIGENTE'
            END AS TIPO_CARTERA
        FROM
            PRN
            INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
            INNER JOIN PE ON PE.CODIGO = PRN.CDGOCPE
            INNER JOIN SC ON SC.CDGNS = PRN.CDGNS
            AND SC.CICLO = PRN.CICLO -- Join para el cliente
            INNER JOIN CL ON CL.CODIGO = SC.CDGCL
            AND SC.CANTSOLIC <> 9999 -- Subquery para el aval
            LEFT JOIN (
                SELECT
                    SC_AUX.CDGNS,
                    SC_AUX.CICLO,
                    CL_AUX.CODIGO,
                    CL_AUX.NOMBRE1,
                    CL_AUX.NOMBRE2,
                    CL_AUX.PRIMAPE,
                    CL_AUX.SEGAPE,
                    CL_AUX.CURP -- Agregado CL_AUX.CURP
                FROM
                    SC SC_AUX
                    INNER JOIN CL CL_AUX ON CL_AUX.CODIGO = SC_AUX.CDGCL
                WHERE
                    SC_AUX.CANTSOLIC = 9999
            ) CL_AVAL ON CL_AVAL.CDGNS = PRN.CDGNS
            AND CL_AVAL.CICLO = PRN.CICLO -- Join adicional para obtener información de TBL_CIERRE_DIA
            LEFT JOIN TBL_CIERRE_DIA CD ON CD.CDGEM = PRN.CDGEM
            AND CD.CDGCLNS = PRN.CDGNS
            AND (
                PRN.CICLO = CD.CICLO
                OR PRN.CICLOD = CD.CICLO
            )
            AND PRN.INICIO = CD.INICIO
        WHERE
            PRN.SITUACION = 'E'
            AND TO_CHAR(CD.FECHA_CALC, 'YYYY-MM-DD') = '$fecha'
            AND CD.CLNS = 'G'
        SQL;

        try {
            $mysqli = new Database();
            $res = $mysqli->queryAll($qry);
            return self::Responde(true, 'Cierre diario generado con éxito.', $res, $qry);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al generar el cierre diario.', null, $e->getMessage());
        }
    }

    public static function GetParametrosCorreos()
    {
        $qry1 = <<<SQL
            SELECT DISTINCT
                'AREA' AS TIPO,
                AREA AS VALOR,
                AREA AS MOSTRAR
            FROM
                CORREO_DIRECTORIO
            WHERE
                ESTATUS = 'A'
            ORDER BY
                AREA
        SQL;

        $qry2 = <<<SQL
            SELECT UNIQUE
                'SUCURSAL' AS TIPO,
                CD.SUCURSAL AS VALOR,
                CD.SUCURSAL || ' - ' || CO.NOMBRE AS MOSTRAR,
                CO.NOMBRE
            FROM
                CORREO_DIRECTORIO CD
                JOIN CO ON CO.CODIGO = CD.SUCURSAL
            WHERE
                CD.ESTATUS = 'A'
            ORDER BY
                CO.NOMBRE
        SQL;

        $qry3 = <<<SQL
            SELECT
                'GRUPO' AS TIPO
                , TO_CHAR(CG.ID) AS VALOR
                , CG.GRUPO AS MOSTRAR
                , NVL(USUARIOS, 0) AS USUARIOS
            FROM
                CORREO_GRUPO CG
                LEFT JOIN (
                    SELECT
                        ID_GRUPO
                        , COUNT(ID_CORREO) AS USUARIOS
                    FROM
                        CORREO_DIRECTORIO_GRUPO
                    GROUP BY
                        ID_GRUPO
                ) CDG ON CDG.ID_GRUPO = CG.ID
            WHERE
                CG.ESTATUS = 'A'
            ORDER BY
                CG.GRUPO
        SQL;

        $qry4 = <<<SQL
            SELECT
                'SUCURSALES' AS TIPO,
                CODIGO AS VALOR,
                CODIGO || ' - ' || NOMBRE AS MOSTRAR
            FROM
                CO
            ORDER BY
                NOMBRE
        SQL;

        try {
            $db = new Database();
            $res1 = $db->queryAll($qry1);
            $res2 = $db->queryAll($qry2);
            $res3 = $db->queryAll($qry3);
            $res4 = $db->queryAll($qry4);

            $res = array_merge($res1, $res2, $res3, $res4);
            return self::Responde(true, 'Parámetros de correos encontrados', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al buscar parámetros de correos', null, $e->getMessage());
        }
    }

    public static function GetCorreos($datos)
    {
        $qry = <<<SQL
            SELECT
                ID,
                NOMBRE,
                CORREO,
                AREA,
                SUCURSAL
            FROM
                CORREO_DIRECTORIO
        SQL;

        $filtros = [];
        $prm = [];

        if (isset($datos['area'])) {
            $filtros[] = 'AREA = :area';
            $prm['area'] = $datos['area'];
        }

        if (isset($datos['sucursal'])) {
            $filtros[] = 'SUCURSAL = :sucursal';
            $prm['sucursal'] = $datos['sucursal'];
        }

        if (count($filtros) > 0) $qry .= ' WHERE ' . implode(' AND ', $filtros);

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Correos encontrados', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al buscar correos', null, $e->getMessage());
        }
    }

    public static function GetCorreosGrupo($datos)
    {
        $qry = <<<SQL
            SELECT
                CDG.ID_CORREO,
                CD.CORREO,
                CDG.EDITABLE
            FROM
                CORREO_DIRECTORIO_GRUPO CDG
                LEFT JOIN CORREO_DIRECTORIO CD ON CD.ID = CDG.ID_CORREO
                LEFT JOIN CORREO_GRUPO CG ON CG.ID = CDG.ID_GRUPO
            WHERE
                CDG.ID_GRUPO = :grupo 
        SQL;

        $prm = [
            'grupo' => $datos['grupo']
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Correos de grupo encontrados', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al buscar correos de grupo', null, $e->getMessage());
        }
    }

    public static function AgregaCorreoGrupo($datos)
    {
        $qry = <<<SQL
            INSERT INTO CORREO_DIRECTORIO_GRUPO (ID_GRUPO, ID_CORREO, EDITABLE, USUARIO_CREACION, USUARIO_MODIFICACION)
            VALUES (:grupo, :correo, 1, :usuario, :usuario)
        SQL;

        $qrys = [];
        $prm = [];
        foreach ($datos['correos'] as $key => $value) {
            $qrys[] = $qry;
            $prm[] = [
                'grupo' => $datos['grupo'],
                'correo' => $value,
                'usuario' => $datos['usuario']
            ];
        }

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $prm);
            return self::Responde(true, 'Correo agregado a grupo correctamente');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al agregar correo a grupo', null, $e->getMessage());
        }
    }

    public static function EliminaCorreoGrupo($datos)
    {
        $qry = <<<SQL
            DELETE FROM CORREO_DIRECTORIO_GRUPO
            WHERE ID_GRUPO = :grupo
            AND ID_CORREO = :correo
        SQL;

        $qrys = [];
        $parametros = [];

        foreach ($datos['correos'] as $key => $value) {
            $qrys[] = $qry;
            $parametros[] = [
                'grupo' => $datos['grupo'],
                'correo' => $value
            ];
        }

        try {
            $db = new Database();
            $db->insertaMultiple($qrys, $parametros);
            return self::Responde(true, 'Correo eliminado del grupo correctamente');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al eliminar correo de grupo', null, $e->getMessage());
        }
    }

    public static function AgregaCorreo($datos)
    {
        $qry = <<<SQL
            INSERT INTO CORREO_DIRECTORIO (CORREO, NOMBRE, AREA, SUCURSAL, ESTATUS, USUARIO_CREACION, USUARIO_MODIFICACION)
            VALUES (:correo, :nombre, :area, :sucursal, 'A', :usuario, :usuario)
        SQL;

        $parametros = [
            'correo' => $datos['correo'],
            'nombre' => $datos['nombre'],
            'area' => $datos['area'],
            'sucursal' => $datos['sucursal'],
            'usuario' => $datos['usuario']
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $parametros);
            return self::Responde(true, 'Correo registrado correctamente');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al registrar correo', null, $e->getMessage());
        }
    }

    public static function AgregaGrupo($datos)
    {
        $qry = <<<SQL
            INSERT INTO CORREO_GRUPO (GRUPO, ESTATUS, USUARIO_CREACION, USUARIO_MODIFICACION)
            VALUES (:grupo, 'A', :usuario, :usuario)
        SQL;

        $prm = [
            'grupo' => $datos['grupo'],
            'usuario' => $datos['usuario']
        ];

        try {
            $db = new Database();
            $res = $db->insertar($qry, $prm);
            return self::Responde(true, 'Grupo registrado correctamente');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al registrar grupo', null, $e->getMessage());
        }
    }

    public static function EliminaGrupo($datos)
    {
        $qry = <<<SQL
            DELETE FROM CORREO_GRUPO
            WHERE ID = :grupo
        SQL;

        $prm = [
            'grupo' => $datos['grupo']
        ];

        try {
            $db = new Database();
            $db->eliminar($qry, $prm);
            return self::Responde(true, 'Grupo eliminado correctamente');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al eliminar grupo', null, $e->getMessage());
        }
    }
}
