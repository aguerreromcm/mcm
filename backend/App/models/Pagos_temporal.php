<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class Pagos_temporal extends Model
{

    public static function ConsultarPagosAdministracion($noCredito, $hora)
    {

        $query = <<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        NS.CDGCO ID_SUCURSAL,
        GET_NOMBRE_SUCURSAL(NS.CDGCO),
        PAGOSDIA.SECUENCIA,
        TO_CHAR(PAGOSDIA.FECHA, 'YYYY-MM-DD' ) AS FECHA,
        TO_CHAR(PAGOSDIA.FECHA, 'DD/MM/YYYY' ) AS FECHA_TABLA,
        PAGOSDIA.CDGNS,
        PAGOSDIA.NOMBRE,
        PAGOSDIA.CICLO,
        PAGOSDIA.MONTO,
        TIPO_OPERACION(PAGOSDIA.TIPO) as TIPO,
        PAGOSDIA.TIPO AS TIP,
        PAGOSDIA.EJECUTIVO,
        PAGOSDIA.CDGOCPE,
        (PE.NOMBRE1 || ' ' || PE.NOMBRE2 || ' ' ||PE.PRIMAPE || ' ' ||PE.SEGAPE) AS NOMBRE_CDGPE,
        PAGOSDIA.FREGISTRO,
        ------PAGOSDIA.FIDENTIFICAPP,
        TRUNC(FECHA) AS DE,
        TRUNC(FECHA) + 1 + 10/24 +  10/1440 AS HASTA,
        CASE
            WHEN SYSDATE 
            BETWEEN (FECHA) 
            AND TO_DATE((TO_CHAR((TRUNC(FECHA) + 1),  'YYYY-MM-DD') || ' ' || '$hora'), 'YYYY-MM-DD HH24:MI:SS')
            THEN 'SI'
        Else 'NO'
        END AS DESIGNATION,
        CASE
        WHEN SYSDATE BETWEEN (FECHA) AND (TRUNC(FECHA) + 2 + 11/24 + 0/1440) THEN 'SI'
        Else 'NO'
        END AS DESIGNATION_ADMIN
    FROM
        PAGOSDIA, NS, CO, RG, PE    
    WHERE
        PAGOSDIA.CDGEM = 'EMPFIN'
        AND PAGOSDIA.ESTATUS = 'A'
        AND PAGOSDIA.CDGNS = '$noCredito'
        AND NS.CODIGO = PAGOSDIA.CDGNS
        AND NS.CDGCO = CO.CODIGO 
        AND CO.CDGRG = RG.CODIGO
        AND PE.CODIGO = PAGOSDIA.CDGPE
        AND PE.CDGEM = 'EMPFIN'
    ORDER BY
        FREGISTRO DESC, SECUENCIA
sql;

        // var_dump($query);
        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function insertHorarios($horario)
    {

        $mysqli = new Database();

        //Agregar un registro completo (Bien) lLAMADA 1
        $query = <<<sql
           INSERT INTO CIERRE_HORARIO
            (ID_CIERRE_HORARIO, HORA_CIERRE, HORA_PRORROGA, CDGCO, CDGPE, FECHA_ALTA)
            VALUES(CIERRE_HORARIO_SECUENCIA.nextval, '$horario->_hora', 'NULL', '$horario->_sucursal', 'AMGM', '$horario->_fecha_registro')
             
sql;
        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function updateHorarios($horario)
    {

        $mysqli = new Database();

        //Agregar un registro completo (Bien) lLAMADA 1
        $query = <<<sql
        UPDATE CIERRE_HORARIO
        SET HORA_CIERRE='$horario->_hora'
        WHERE CDGCO='$horario->_sucursal'
sql;
        //var_dump($query);
        return $mysqli->insert($query);
    }


    public static function updatePagoApp($update)
    {

        $mysqli = new Database();

        //Agregar un registro completo (Bien) lLAMADA 1
        $query = <<<sql
        UPDATE CORTECAJA_PAGOSDIA
        SET INCIDENCIA='1', NUEVO_MONTO = '$update->_nuevo_monto', COMENTARIO_INCIDENCIA = '$update->_comentario_detalle', ESTATUS_CAJA = '0', TIPO = '$update->_tipo_pago'
        WHERE CORTECAJA_PAGOSDIA_PK='$update->_id_registro'
sql;
        return $mysqli->insert($query);
    }

    public static function AddPagoApp($pk, $barcode)
    {

        $mysqli = new Database();

        //Agregar un registro completo (Bien) lLAMADA 1
        $query = <<<sql
        INSERT INTO FOLIO_APP
        (ID_FOLIO_APP, FOLIO, CORTECAJA_PAGOSDIA_PK, FECHA_REGISTRO)
        VALUES(FOLIO_APP_I.nextval, '$barcode', '$pk', CURRENT_TIMESTAMP)
sql;
        $query_1 = <<<sql
        UPDATE CORTECAJA_PAGOSDIA
        SET  PROCESA_PAGOSDIA = '1'
        WHERE CORTECAJA_PAGOSDIA_PK= '$pk'
sql;
        //        UPDATE CORTECAJA_PAGOSDIA SET PROCESA_PAGOSDIA=NULL
        //var_dump($query_1);

        $insert_folio = $mysqli->insert($query);
        $update_pk = $mysqli->insert($query_1);



        return [$insert_folio, $update_pk];
    }


    public static function getByIdReporte($folio)
    {
        $mysqli = new Database();
        $query = <<<sql
        SELECT * FROM FOLIO_APP fa
        INNER JOIN CORTECAJA_PAGOSDIA cp ON cp.CORTECAJA_PAGOSDIA_PK = fa.CORTECAJA_PAGOSDIA_PK
        WHERE FOLIO = '$folio'
        ORDER BY decode(cp.TIPO ,
                                'P',1,
                                'M',2,
                                'G',3,
                                'D',4,
                                'R',5
                                ) asc
sql;

        $query_1 = <<<sql
        SELECT
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_VALIDADOS, 
        SUM(CASE 
        WHEN ((TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_PAGOS,
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) AS TOTAL_NUEVOS_MONTOS, 
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END) AS TOTAL_MONT_SIN_MOD, 
        (SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) + SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END)) AS TOTAL, CO.NOMBRE AS NOMBRE_SUC, FA.FOLIO, CORTECAJA_PAGOSDIA.EJECUTIVO
        FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN FOLIO_APP FA ON FA.CORTECAJA_PAGOSDIA_PK = CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
        WHERE PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND FA.FOLIO = '$folio'
        AND PROCESA_PAGOSDIA = '1'
        GROUP BY FA.FOLIO, CO.NOMBRE, CORTECAJA_PAGOSDIA.EJECUTIVO
sql;

        $tabla = $mysqli->queryAll($query);
        $datos = $mysqli->queryOne($query_1);


        return [$datos, $tabla];
    }


    public static function ConsultarHorarios()
    {

        $query = <<<sql
        SELECT * FROM CIERRE_HORARIO
        INNER JOIN CO ON CO.CODIGO = CIERRE_HORARIO.CDGCO
        ORDER BY CIERRE_HORARIO.FECHA_ALTA ASC 
sql;

        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function ConsultarDiasFestivos()
    {

        $query = <<<sql
        SELECT 
        TO_CHAR(FECHA, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') || '- ' || TO_CHAR(FECHA, 'DD-MON-YYYY' ) AS FECHA,
        UPPER(DESCRIPCION) AS DESCRIPCION, 
        TO_CHAR(FECHA_CAPTURA , 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') || '- ' || TO_CHAR(FECHA_CAPTURA , 'DD-MON-YYYY' ) AS FECHA_CAPTURA
        FROM DIAS_FESTIVOS
        ORDER BY DIA_FESTIVO_PK ASC 
sql;

        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function ConsultarPagosApp()
    {

        $query = <<<SQL
            SELECT
                (COD_SUC || COUNT(NOMBRE) || COMP_BARRA || CAST(SUM(MONTO) AS INTEGER)) AS BARRAS, COD_SUC, SUCURSAL, COUNT(NOMBRE) AS NUM_PAGOS, NOMBRE, FECHA_D, FECHA, 
            FECHA_REGISTRO, CDGOCPE,
            SUM(PAGOS) AS TOTAL_PAGOS, 
            SUM(MULTA) AS TOTAL_MULTA, 
            SUM(REFINANCIAMIENTO) AS TOTAL_REFINANCIAMIENTO, 
            SUM(DESCUENTO) AS TOTAL_DESCUENTO, 
            SUM(GARANTIA) AS GARANTIA, 
            SUM(MONTO) AS MONTO_TOTAL
            FROM
            (
            SELECT TO_CHAR(FECHA, 'DDMMYYYY' ) AS COMP_BARRA ,CO.CODIGO AS COD_SUC, CO.NOMBRE AS SUCURSAL, CORTECAJA_PAGOSDIA.EJECUTIVO AS NOMBRE, 
            TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') || '- ' || TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MON-YYYY' ) AS FECHA_D ,
            TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) AS FECHA,
            TO_CHAR(CORTECAJA_PAGOSDIA.FREGISTRO) AS FECHA_REGISTRO,
            CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'P' THEN MONTO END PAGOS,
            CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'M' THEN MONTO END MULTA,
            CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'R' THEN MONTO END REFINANCIAMIENTO,
            CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'D' THEN MONTO END DESCUENTO,
            CASE WHEN CORTECAJA_PAGOSDIA.TIPO = 'G' THEN MONTO END GARANTIA, 
            CORTECAJA_PAGOSDIA.MONTO, CORTECAJA_PAGOSDIA.CDGOCPE
            FROM CORTECAJA_PAGOSDIA
            INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
            INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
            WHERE PROCESA_PAGOSDIA = '0'
            AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
            AND PRN.CDGCO = CO.CODIGO
            )
            GROUP BY NOMBRE, FECHA_D, FECHA, CDGOCPE, FECHA_REGISTRO, COD_SUC, SUCURSAL, COMP_BARRA
        SQL;

        /////AND PRN.SITUACION = 'E' PONER ESTA CUANDO ESTEMOS EN PRODUCTIVO
        try {
            $db = new Database();
            $res = $db->queryAll($query);
            return self::Responde(true, 'Pagos obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener pagos', null, $e->getMessage());
        }
    }

    public static function ConsultarPagosAppHistorico($fi, $ff)
    {
        $qry = <<<SQL
            SELECT
                (
                    COD_SUC || COUNT(NOMBRE) || COMP_BARRA || CAST(SUM(MONTO) AS INTEGER)
                ) AS BARRAS,
                COD_SUC,
                SUCURSAL,
                COUNT(NOMBRE) AS NUM_PAGOS,
                NOMBRE,
                FECHA_D,
                FECHA,
                FECHA_REGISTRO,
                CDGOCPE,
                SUM(PAGOS) AS TOTAL_PAGOS,
                SUM(MULTA) AS TOTAL_MULTA,
                SUM(REFINANCIAMIENTO) AS TOTAL_REFINANCIAMIENTO,
                SUM(DESCUENTO) AS TOTAL_DESCUENTO,
                SUM(GARANTIA) AS GARANTIA,
                SUM(MONTO) AS MONTO_TOTAL
            FROM
                (
                    SELECT
                        TO_CHAR(FECHA, 'DDMMYYYY') AS COMP_BARRA,
                        CO.CODIGO AS COD_SUC,
                        CO.NOMBRE AS SUCURSAL,
                        CORTECAJA_PAGOSDIA.EJECUTIVO AS NOMBRE,
                        TO_CHAR(
                            CORTECAJA_PAGOSDIA.FECHA,
                            'DAY',
                            'NLS_DATE_LANGUAGE=SPANISH'
                        ) || '- ' || TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MON-YYYY') AS FECHA_D,
                        TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY') AS FECHA,
                        TO_CHAR(CORTECAJA_PAGOSDIA.FREGISTRO) AS FECHA_REGISTRO,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'P' THEN MONTO
                        END PAGOS,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'M' THEN MONTO
                        END MULTA,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'R' THEN MONTO
                        END REFINANCIAMIENTO,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'D' THEN MONTO
                        END DESCUENTO,
                        CASE
                            WHEN CORTECAJA_PAGOSDIA.TIPO = 'G' THEN MONTO
                        END GARANTIA,
                        CORTECAJA_PAGOSDIA.MONTO,
                        CORTECAJA_PAGOSDIA.CDGOCPE
                    FROM
                        CORTECAJA_PAGOSDIA
                        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS
                        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
                    WHERE
                        PROCESA_PAGOSDIA = '1'
                        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
                        AND PRN.CDGCO = CO.CODIGO
                        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'YYYY-MM-DD') BETWEEN '$fi' AND '$ff'
                )
            GROUP BY
                NOMBRE,
                FECHA_D,
                FECHA,
                CDGOCPE,
                FECHA_REGISTRO,
                COD_SUC,
                SUCURSAL,
                COMP_BARRA
        SQL;

        $mysqli = new Database();
        return $mysqli->queryAll($qry);
    }

    public static function ConsultarPagosAppDetalle($ejecutivo, $fecha, $suc)
    {
        $query = <<<sql
        SELECT CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK, CORTECAJA_PAGOSDIA.FECHA, CORTECAJA_PAGOSDIA.CDGNS, CORTECAJA_PAGOSDIA.NOMBRE, 
        CORTECAJA_PAGOSDIA.CICLO, CORTECAJA_PAGOSDIA.CDGOCPE, CORTECAJA_PAGOSDIA.EJECUTIVO,	
        CORTECAJA_PAGOSDIA.FREGISTRO, CORTECAJA_PAGOSDIA.CDGPE, CORTECAJA_PAGOSDIA.ESTATUS, CORTECAJA_PAGOSDIA.FACTUALIZA,
        CORTECAJA_PAGOSDIA.MONTO, CORTECAJA_PAGOSDIA.TIPO, CORTECAJA_PAGOSDIA.ESTATUS_CAJA, CORTECAJA_PAGOSDIA.INCIDENCIA, CORTECAJA_PAGOSDIA.NUEVO_MONTO, 
        COMENTARIO_INCIDENCIA, CORTECAJA_PAGOSDIA.PROCESA_PAGOSDIA, TO_CHAR(CORTECAJA_PAGOSDIA.FIDENTIFICAPP ,'DD/MM/YYYY HH24:MI:SS') AS FIDENTIFICAPP FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo'
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND PROCESA_PAGOSDIA = '0'
        ORDER BY decode(CORTECAJA_PAGOSDIA.TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D',4,
                        'R',5
                        ) asc
sql;

        $query2 = <<<sql
        SELECT
            SUM(CASE 
        WHEN (ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_PAGOS_TOTAL,
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_VALIDADOS, 
        SUM(CASE 
        WHEN ((TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_PAGOS,
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) AS TOTAL_NUEVOS_MONTOS, 
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END) AS TOTAL_MONT_SIN_MOD, 
        (SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) + SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END)) AS TOTAL
        FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo' 
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND PROCESA_PAGOSDIA = '0'
        ORDER BY decode(TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D',4,
                        'R',5
                        ) asc
sql;

        //var_dump($query);
        $mysqli = new Database();
        $res1 = $mysqli->queryAll($query);
        $res2 = $mysqli->queryAll($query2);
        return [$res1, $res2];
    }

    public static function ConsultarPagosAppDetalleImprimir($ejecutivo, $fecha, $suc)
    {
        $query = <<<sql
        SELECT CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK, CORTECAJA_PAGOSDIA.FECHA, CORTECAJA_PAGOSDIA.CDGNS, CORTECAJA_PAGOSDIA.NOMBRE, 
        CORTECAJA_PAGOSDIA.CICLO, CORTECAJA_PAGOSDIA.CDGOCPE, CORTECAJA_PAGOSDIA.EJECUTIVO,	
        CORTECAJA_PAGOSDIA.FREGISTRO, CORTECAJA_PAGOSDIA.CDGPE, CORTECAJA_PAGOSDIA.ESTATUS, CORTECAJA_PAGOSDIA.FACTUALIZA,
        CORTECAJA_PAGOSDIA.MONTO, CORTECAJA_PAGOSDIA.TIPO, CORTECAJA_PAGOSDIA.ESTATUS_CAJA, CORTECAJA_PAGOSDIA.INCIDENCIA, CORTECAJA_PAGOSDIA.NUEVO_MONTO, 
        COMENTARIO_INCIDENCIA, CORTECAJA_PAGOSDIA.PROCESA_PAGOSDIA, TO_CHAR(CORTECAJA_PAGOSDIA.FIDENTIFICAPP ,'DD/MM/YYYY HH24:MI:SS') AS FIDENTIFICAPP FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo'
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND PROCESA_PAGOSDIA = '1'
        ORDER BY decode(CORTECAJA_PAGOSDIA.TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D',4,
                        'R',5
                        ) asc
sql;

        $query2 = <<<sql
        SELECT
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_VALIDADOS, 
        SUM(CASE 
        WHEN ((TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_PAGOS,
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) AS TOTAL_NUEVOS_MONTOS, 
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END) AS TOTAL_MONT_SIN_MOD, 
        (SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) + SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END)) AS TOTAL
        FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo' 
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND PROCESA_PAGOSDIA = '1'
        ORDER BY decode(TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D',4,
                        'R',5
                        ) asc
sql;

        //var_dump($query);
        $mysqli = new Database();
        $res1 = $mysqli->queryAll($query);
        $res2 = $mysqli->queryAll($query2);
        return [$res1, $res2];
    }

    public static function ConsultarPagosAppResumen($ejecutivo, $fecha, $suc)
    {

        $query = <<<sql
        SELECT * FROM (
        SELECT CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK, CORTECAJA_PAGOSDIA.FECHA, CORTECAJA_PAGOSDIA.CDGNS, CORTECAJA_PAGOSDIA.NOMBRE, 
        CORTECAJA_PAGOSDIA.CICLO, CORTECAJA_PAGOSDIA.CDGOCPE, CORTECAJA_PAGOSDIA.EJECUTIVO,	
        CORTECAJA_PAGOSDIA.FREGISTRO, CORTECAJA_PAGOSDIA.CDGPE, CORTECAJA_PAGOSDIA.ESTATUS, CORTECAJA_PAGOSDIA.FACTUALIZA,
        CORTECAJA_PAGOSDIA.MONTO, CORTECAJA_PAGOSDIA.TIPO, CORTECAJA_PAGOSDIA.ESTATUS_CAJA, CORTECAJA_PAGOSDIA.INCIDENCIA, CORTECAJA_PAGOSDIA.NUEVO_MONTO, 
        COMENTARIO_INCIDENCIA, CORTECAJA_PAGOSDIA.PROCESA_PAGOSDIA, TO_CHAR(CORTECAJA_PAGOSDIA.FIDENTIFICAPP ,'DD/MM/YYYY HH24:MI:SS') AS FIDENTIFICAPP 
        FROM CORTECAJA_PAGOSDIA
        INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO 
        WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo' 
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND CORTECAJA_PAGOSDIA.ESTATUS_CAJA = '1' AND (CORTECAJA_PAGOSDIA.TIPO = 'P' OR CORTECAJA_PAGOSDIA.TIPO = 'M')
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO
        AND PRN.CDGCO = '$suc'
        AND PROCESA_PAGOSDIA = '0'
        UNION
        SELECT CORTECAJA_PAGOSDIA.CORTECAJA_PAGOSDIA_PK, CORTECAJA_PAGOSDIA.FECHA, CORTECAJA_PAGOSDIA.CDGNS, CORTECAJA_PAGOSDIA.NOMBRE, 
        CORTECAJA_PAGOSDIA.CICLO, CORTECAJA_PAGOSDIA.CDGOCPE, CORTECAJA_PAGOSDIA.EJECUTIVO,	
        CORTECAJA_PAGOSDIA.FREGISTRO, CORTECAJA_PAGOSDIA.CDGPE, CORTECAJA_PAGOSDIA.ESTATUS, CORTECAJA_PAGOSDIA.FACTUALIZA,
        0 AS MONTO, CORTECAJA_PAGOSDIA.TIPO, CORTECAJA_PAGOSDIA.ESTATUS_CAJA, CORTECAJA_PAGOSDIA.INCIDENCIA, CORTECAJA_PAGOSDIA.NUEVO_MONTO, 
        COMENTARIO_INCIDENCIA, CORTECAJA_PAGOSDIA.PROCESA_PAGOSDIA, TO_CHAR(CORTECAJA_PAGOSDIA.FIDENTIFICAPP ,'DD/MM/YYYY HH24:MI:SS') AS FIDENTIFICAPP 
        FROM CORTECAJA_PAGOSDIA INNER JOIN PRN ON PRN.CDGNS = CORTECAJA_PAGOSDIA.CDGNS 
        INNER JOIN CO ON CO.CODIGO = PRN.CDGCO WHERE CORTECAJA_PAGOSDIA.CDGOCPE = '$ejecutivo' 
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha' 
        AND CORTECAJA_PAGOSDIA.ESTATUS_CAJA = '0' 
        AND (CORTECAJA_PAGOSDIA.TIPO != 'P' OR CORTECAJA_PAGOSDIA.TIPO != 'M') 
        AND PRN.CICLO = CORTECAJA_PAGOSDIA.CICLO AND PRN.CDGCO = '$suc' AND PROCESA_PAGOSDIA = '0' )
        ORDER BY decode(TIPO , 'P',1, 'M',2, 'G',3, 'D', 4, 'R', 5 ) ASC

sql;
        //var_dump($query);
        $query2 = <<<sql
        SELECT
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_VALIDADOS, 
        
        SUM(CASE 
        WHEN ((TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN 1
        ELSE 0
        END) AS TOTAL_PAGOS,
    
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) AS TOTAL_NUEVOS_MONTOS, 
        
        SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END) AS TOTAL_MONT_SIN_MOD, 
        
        
        (SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 1 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN NUEVO_MONTO 
        ELSE 0
        END) + SUM(CASE 
        WHEN (ESTATUS_CAJA = 1 AND INCIDENCIA = 0 AND (TIPO = 'P' OR TIPO = 'M') AND ESTATUS = 'A') THEN MONTO
        ELSE 0
        END)) AS TOTAL
        FROM CORTECAJA_PAGOSDIA
        WHERE CDGOCPE = '$ejecutivo' 
        AND TO_CHAR(CORTECAJA_PAGOSDIA.FECHA, 'DD-MM-YYYY' ) = '$fecha'
        AND ESTATUS_CAJA = '1'
        ORDER BY decode(TIPO ,
                        'P',1,
                        'M',2,
                        'G',3,
                        'D', 4,
                        'R', 5
                        ) asc
sql;


        //var_dump($query2);
        $mysqli = new Database();
        $res1 = $mysqli->queryAll($query);
        $res2 = $mysqli->queryAll($query2);
        return [$res1, $res2];
    }

    public static function ConsultarPagosFechaSucursal($id_sucursal, $Inicial, $Final)
    {

        if ($id_sucursal) {
            $valor_sucursal = 'AND NS.CDGCO =' . $id_sucursal;
        }
        $query = <<<sql
        SELECT
        RG.CODIGO ID_REGION,
        RG.NOMBRE REGION,
        NS.CDGCO ID_SUCURSAL,
        GET_NOMBRE_SUCURSAL(NS.CDGCO) AS NOMBRE_SUCURSAL,
        PAGOSDIA.SECUENCIA,
        PAGOSDIA.FECHA,
        PAGOSDIA.CDGNS,
        PAGOSDIA.NOMBRE,
        PAGOSDIA.CICLO,
        PAGOSDIA.MONTO,
        TIPO_OPERACION(PAGOSDIA.TIPO) as TIPO,
        PAGOSDIA.TIPO AS TIP,
        PAGOSDIA.EJECUTIVO,
        PAGOSDIA.CDGOCPE,
        TO_CHAR(PAGOSDIA.FREGISTRO ,'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO,       
        CASE WHEN NOT FREGISTRO_APP IS NULL THEN 'APP' ELSE 'OTRO' END AS MEDIO,
        TRUNC(FREGISTRO) + 12/24 AS DE,
        TRUNC(FREGISTRO) + 1 + 12/24 AS HASTA,
        CASE
        WHEN FREGISTRO >= TRUNC(FREGISTRO) + 12/24 AND FREGISTRO <=TRUNC(FREGISTRO) + 1 + 12/24 THEN 'SI'
        Else 'NO'
        END AS DESIGNATION
    FROM
        PAGOSDIA, NS, CO, RG
    WHERE
        PAGOSDIA.CDGEM = 'EMPFIN'
        AND PAGOSDIA.ESTATUS = 'A'
        AND PAGOSDIA.FECHA BETWEEN TO_DATE('$Inicial', 'YY-mm-dd') AND TO_DATE('$Final', 'YY-mm-dd') 
        AND NS.CODIGO = PAGOSDIA.CDGNS
        AND NS.CDGCO = CO.CODIGO 
        AND CO.CDGRG = RG.CODIGO
        $valor_sucursal
    ORDER BY
        FREGISTRO DESC, SECUENCIA
sql;
        $mysqli = new Database();

        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function ConsultarPagosAdministracionOne($noCredito, $perfil, $user)
    {



        $query_determina_adicional = <<<sql
        select * from SN where CREDITO_ADICIONAL is not null and cdgns = '$noCredito'
sql;
        $mysqli = new Database();




        if ($perfil != 'ADMIN') {
            $Q1 = "AND PRN.CDGCO = 
            
            ANY(SELECT
        CO.CODIGO ID_SUCURSAL
        FROM
        PCO, CO, RG
        WHERE
        PCO.CDGCO = CO.CODIGO
        AND CO.CDGRG = RG.CODIGO
        AND PCO.CDGEM = 'EMPFIN'
        AND PCO.CDGPE = '$user') 
            
            
            ";
        } else {
            $Q1 = '';
        }


        $consulta = $mysqli->queryOne($query_determina_adicional);

        $res_adicional = $consulta['CREDITO_ADICIONAL'];
        if ($res_adicional == null) {
            $query = <<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		PRN.SITUACION,
        CASE PRN.SITUACION
        WHEN 'S'THEN 'SOLICITADO' 
        WHEN 'E'THEN 'ENTREGADO' 
        WHEN 'A'THEN 'AUTORIZADO' 
        WHEN 'L'THEN 'LIQUIDADO' 
        ELSE 'DESCONOCIDO'
      END SITUACION_NOMBRE,
               CASE PRN.SITUACION
    WHEN 'S'THEN '#1F6CC1FF'
    WHEN 'E'THEN '#298732FF' 
    WHEN 'A'THEN '#A31FC1FF' 
    WHEN 'L'THEN '#000000FF' 
    ELSE '#FF0000FF'
  END COLOR,
               CASE PRN.SITUACION
    WHEN 'E'THEN ''
    ELSE 'none'
  END ACTIVO,
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
		SC.CDGPI ID_PROYECTO,
		'TRADICIONAL' as TIPO_C
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
		AND PRN.SITUACION IN('E', 'L')
	    $Q1
		AND SC.CANTSOLIC <> '9999' order by SC.SOLICITUD  desc
sql;
        } else {
            $query = <<<sql
        SELECT 
		SC.CDGNS NO_CREDITO,
		SC.CDGCL ID_CLIENTE,
		GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
		SC.CICLO,
		NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
		PRN.SITUACION,
        CASE PRN.SITUACION
        WHEN 'S'THEN 'SOLICITADO' 
        WHEN 'E'THEN 'ENTREGADO' 
        WHEN 'A'THEN 'AUTORIZADO' 
        WHEN 'L'THEN 'LIQUIDADO' 
        ELSE 'DESCONOCIDO'
      END SITUACION_NOMBRE,
               CASE PRN.SITUACION
    WHEN 'S'THEN '#1F6CC1FF'
    WHEN 'E'THEN '#298732FF' 
    WHEN 'A'THEN '#A31FC1FF' 
    WHEN 'L'THEN '#000000FF' 
    ELSE '#FF0000FF'
  END COLOR,
               CASE PRN.SITUACION
    WHEN 'E'THEN ''
    ELSE 'none'
  END ACTIVO,
		SN.PLAZOSOL PLAZO,
		SN.PERIODICIDAD,
		SN.TASA,
		DIA_PAGO(SN.NOACUERDO) DIA_PAGO,
		CALCULA_PARCIALIDAD(SN.PERIODICIDAD, SN.TASA, NVL(SC.CANTAUTOR,SC.CANTSOLIC), SN.PLAZOSOL) PARCIALIDAD,
		SN.CDGCO ID_SUCURSAL,
		GET_NOMBRE_SUCURSAL(SN.CDGCO) SUCURSAL,
		SN.CDGOCPE ID_EJECUTIVO,
		GET_NOMBRE_EMPLEADO(SN.CDGOCPE) EJECUTIVO,
		SC.CDGPI ID_PROYECTO,
		'MAS POR TI' as TIPO_C
	FROM 
		SN, SC, PRN
	WHERE
		SC.CDGNS = '$noCredito'
		
		AND SC.CDGNS = SN.CDGNS
		AND SC.CICLO = SN.CICLO
	    AND PRN.CICLO = SC.CICLO 
		AND PRN.CDGNS = SC.CDGNS 
		AND PRN.SITUACION IN('E', 'L')
	    $Q1
		AND SC.CANTSOLIC <> '9999' order by SC.SOLICITUD  desc
sql;
        }


        //var_dump($query);




        $consulta = $mysqli->queryOne($query);

        $cdgco = $consulta['ID_SUCURSAL'];

        $query_horario = <<<sql
        SELECT * FROM CIERRE_HORARIO WHERE CDGCO = '$cdgco'
sql;

        $fechaActual = date("Y-m-d");

        $query_dia_festivo = <<<sql
        SELECT COUNT(*) AS TOT, TO_CHAR(FECHA_CAPTURA, 'YYYY-mm-dd') as FECHA_CAPTURA FROM DIAS_FESTIVOS WHERE FECHA_CAPTURA = TIMESTAMP '$fechaActual 00:00:00.000000'
        GROUP BY FECHA_CAPTURA 
sql;

        //var_dump($query_dia_festivo);
        $consulta_horario = $mysqli->queryOne($query_horario);
        $consulta_dia_festivo = $mysqli->queryOne($query_dia_festivo);

        return [$consulta, $consulta_horario, $consulta_dia_festivo];
    }

    public static function CierreCaja($datos)
    {
        $qry = 'SELECT * FROM CIERRE_HORARIO WHERE CDGPE = :usuario';

        $prms = [':usuario' => $datos['usuario']];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $prms);
            return self::Responde(true, "Hora de cierre obtenida", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener la hora de cierre", null, $e->getMessage());
        }
    }

    public static function DiasFestivos()
    {
        $qry = <<<SQL
            SELECT 
                TO_CHAR(FECHA, 'YYYY-mm-dd') AS FECHA
            FROM 
                DIAS_FESTIVOS
            WHERE 
                FECHA BETWEEN TRUNC(SYSDATE) - 7 AND TRUNC(SYSDATE) + 7
            ORDER BY 
                FECHA ASC
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, "Días festivos obtenidos", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los días festivos", null, $e->getMessage());
        }
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

    public static function getAllCorteCaja()
    {
        $mysqli = new Database();
        $query = <<<sql
SELECT COUNT(CDGPE) AS NUM_PAG, CDGPE, SUM(MONTO) AS MONTO_TOTAL,
SUM(CASE WHEN TIPO = 'P' THEN monto ELSE 0 END) AS MONTO_PAGO,
SUM(CASE WHEN TIPO = 'M' THEN monto ELSE 0 END) AS MONTO_GARANTIA,
SUM(CASE WHEN TIPO = 'D' THEN monto ELSE 0 END) AS MONTO_DESCUENTO,
SUM(CASE WHEN TIPO = 'R' THEN monto ELSE 0 END) AS MONTO_REFINANCIAMIENTO,
SUM(CASE WHEN TIPO = 'G' THEN monto ELSE 0 END) AS MONTO_MULTA
FROM CORTECAJA_PAGOSDIA
GROUP BY CDGPE 
HAVING COUNT (CDGPE) > 0

sql;

        return $mysqli->queryAll($query);
    }

    public static function getAllCorteCajaByID($id)
    {
        $mysqli = new Database();
        $query = <<<sql
SELECT EJECUTIVO, COUNT(CDGPE) AS NUM_PAG, CDGPE, SUM(MONTO) AS MONTO_TOTAL,
SUM(CASE WHEN TIPO = 'P' THEN monto ELSE 0 END) AS MONTO_PAGO,
SUM(CASE WHEN TIPO = 'M' THEN monto ELSE 0 END) AS MONTO_GARANTIA,
SUM(CASE WHEN TIPO = 'D' THEN monto ELSE 0 END) AS MONTO_DESCUENTO,
SUM(CASE WHEN TIPO = 'R' THEN monto ELSE 0 END) AS MONTO_REFINANCIAMIENTO,
SUM(CASE WHEN TIPO = 'G' THEN monto ELSE 0 END) AS MONTO_MULTA
FROM CORTECAJA_PAGOSDIA
WHERE CDGPE = '$id'
GROUP BY CDGPE, EJECUTIVO 
HAVING COUNT (CDGPE) > 0 


sql;
        return $mysqli->queryOne($query);
    }

    public static function getAllByIdCorteCaja($user)
    {
        $mysqli = new Database();
        $query = <<<sql
SELECT *
FROM CORTECAJA_PAGOSDIA 

sql;

        return $mysqli->queryAll($query);
    }

    public static function insertProcedure($pago)
    {

        $credito_i = $pago->_credito;
        $fecha_i = $pago->_fecha;
        $ciclo_i = $pago->_ciclo;
        $monto_i = $pago->_monto;
        $tipo_i = $pago->_tipo;
        $nombre_i = $pago->_nombre;
        $user_i = $pago->_usuario;
        $ejecutivo_i = $pago->_ejecutivo;
        $ejecutivo_nombre_i = $pago->_ejecutivo_nombre;
        $tipo_procedure_ = 1;
        $fecha_aux = "";

        $mysqli = new Database();
        return $mysqli->queryProcedurePago($credito_i, $ciclo_i, $monto_i, $tipo_i, $nombre_i, $user_i,  $ejecutivo_i, $ejecutivo_nombre_i,  $tipo_procedure_, $fecha_aux, "", $fecha_i);
    }

    public static function EditProcedure($pago)
    {

        $credito_i = $pago->_credito;
        $fecha = $pago->_fecha;
        $secuencia_i = $pago->_secuencia;
        $ciclo_i = $pago->_ciclo;
        $monto_i = $pago->_monto;
        $tipo_i = $pago->_tipo;
        $nombre_i = $pago->_nombre;
        $user_i = $pago->_usuario;
        $ejecutivo_i = $pago->_ejecutivo;
        $ejecutivo_nombre_i = $pago->_ejecutivo_nombre;
        $tipo_procedure_ = 2;
        $fecha_aux = $pago->_fecha_aux;

        $mysqli = new Database();

        return $mysqli->queryProcedurePago($credito_i, $ciclo_i, $monto_i, $tipo_i, $nombre_i, $user_i,  $ejecutivo_i, $ejecutivo_nombre_i, $tipo_procedure_, $fecha_aux, $secuencia_i, $fecha);
    }

    public static function ListaEjecutivos($cdgco)
    {

        $query = <<<sql
        SELECT
        CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) EJECUTIVO,
        CODIGO ID_EJECUTIVO
        FROM
            PE
        WHERE
            CDGEM = 'EMPFIN' 
            AND CDGCO = '$cdgco'
            AND ACTIVO = 'S'
        ORDER BY 1
sql;

        //var_dump($query);

        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }


    public static function ListaEjecutivosAdmin($credito)
    {
        $query_cdgco = <<<sql
        SELECT PRN.CDGCO, PRN.CDGOCPE  FROM PRN WHERE PRN.CDGNS = '$credito' ORDER BY PRN.CICLO DESC
sql;

        $mysqli = new Database();
        $res_cdgco = $mysqli->queryOne($query_cdgco);
        //var_dump($query_cdgco);

        $cdgco = $res_cdgco['CDGCO'];
        $cdgocpe = $res_cdgco['CDGOCPE'];

        if ($cdgco == '026' || $cdgco == '025' || $cdgco == '014') {
            $cdgco = "'026','025','014'";
        } else {
            $cdgco = "'" . $cdgco . "'";
        }


        $query = <<<sql
        SELECT
	CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) EJECUTIVO,
	CODIGO ID_EJECUTIVO
FROM
	PE
WHERE
	CDGEM = 'EMPFIN' 
    AND CDGCO IN($cdgco)
	AND ACTIVO = 'S'
    AND BLOQUEO = 'N'
ORDER BY 1
sql;
        //var_dump($query);
        $val = $mysqli->queryAll($query);
        return [$val, $cdgocpe];
    }

    public static function ListaSucursales($id_user)
    {

        $query = <<<sql
        SELECT
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
        AND PCO.CDGPE = '$id_user'
        ORDER BY
        ID_REGION,
        ID_SUCURSAL
sql;

        $mysqli = new Database();
        return $mysqli->queryAll($query);
    }

    public static function GeneraLayoutContable($f1, $f2)
    {

        $query = <<<sql
        	SELECT
	FECHA,
	CASE
		WHEN (PGD.TIPO = 'P' OR PGD.TIPO = 'X') THEN 'P' || PRN.CDGNS || PRN.CDGTPC || FN_DV('P' || PRN.CDGNS || PRN.CDGTPC)
		WHEN PGD.TIPO = 'G' THEN '0' || PRN.CDGNS || PRN.CDGTPC || FN_DV('0' || PRN.CDGNS || PRN.CDGTPC)
		ELSE 'NO IDENTIFICADO'
	END REFERENCIA,
	PGD.MONTO,
	'MN' MONEDA
FROM
	PAGOSDIA PGD, PRN
WHERE
	PGD.CDGEM = PRN.CDGEM
	AND PGD.CDGNS = PRN.CDGNS
	AND PGD.CICLO = PRN.CICLO
	AND PGD.CDGEM = 'EMPFIN'
	AND PGD.ESTATUS = 'A'
	AND PGD.TIPO IN('P','G', 'X')
	AND PGD.MONTO != 0
	AND PGD.FECHA BETWEEN TO_DATE('$f1', 'YY-mm-dd') AND TO_DATE('$f2', 'YY-mm-dd') 
ORDER BY
	PGD.FECHA
sql;

        try {
            $mysqli = new Database();
            return $mysqli->queryAll($query);
        } catch (\Exception $e) {
            return "";
        }
    }

    public static function DeletePago($id, $secuencia, $fecha)
    {
        $mysqli = new Database();
        $query = <<<sql
      UPDATE PAGOSDIA SET ESTATUS = 'E' WHERE CDGNS = '$id' AND SECUENCIA = '$secuencia' AND FREGISTRO <> TIMESTAMP '$fecha 00:00:00.000000'
sql;
        $accion = new \stdClass();
        $accion->_sql = $query;
        return $mysqli->insert($query);
    }

    public static function DeleteProcedure($cdgns, $fecha, $user, $secuencia)
    {
        $mysqli = new Database();
        return $mysqli->queryProcedureDeletePago($cdgns, $fecha, $user, $secuencia);
    }

    public static function GetRegistroPagosDia($datos)
    {
        $qry = <<<SQL
            SELECT
                *
            FROM
                PAGOSDIA
            WHERE
                CDGNS = :cdgns
        SQL;

        $param = [
            'cdgns' => $datos['_credito'] ?? $datos['cdgns'] ?? null
        ];

        if ($datos['_secuencia'] || $datos['secuencia']) {
            $qry .= " AND SECUENCIA = :secuencia AND FECHA = TO_DATE(:fecha, 'YYYY-MM-DD')";
            $param['secuencia'] = $datos['_secuencia'] ?? $datos['secuencia'] ?? null;
            $param['fecha'] = $datos['_fecha_aux'] ?? $datos['fecha'] ?? null;
        } else {
            $qry .= " AND FACTUALIZA = (SELECT MAX(FACTUALIZA) FROM PAGOSDIA WHERE CDGNS = :cdgns)";
        }

        try {
            $db = new Database();
            return $db->queryOne($qry, $param) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public static function RegistroBitacoraAdmin($datos)
    {
        $qry = <<<SQL
            INSERT INTO PAGOSDIA_BITACORA_ADMIN 
                (USUARIO, ORIGINAL, JUSTIFICACION, SOPORTE, NOMBRE_SOPORTE, TIPO_SOPORTE)
            VALUES 
                (:usuario, :original, :justificacion, EMPTY_BLOB(), :nombre_soporte, :tipo_soporte) 
            RETURNING SOPORTE INTO :soporte
        SQL;

        $param = [
            'usuario' => $datos['usuario'] ?? $datos['_usuario'] ?? null,
            'original' => $datos['original'],
            'justificacion' => $datos['justificacion'],
            'soporte' => $datos['soporte'] ?? null,
            'nombre_soporte' => $datos['nombre_soporte'] ?? null,
            'tipo_soporte' => $datos['tipo_soporte'] ?? null
        ];

        try {
            $db = new Database();
            $db->insertarBlob($qry, $param, ['soporte']);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function ActualizaBitacoraAdmin($datos)
    {
        $qry = <<<SQL
            UPDATE
                PAGOSDIA_BITACORA_ADMIN
            SET
                MODIFICADO = :modificado
            WHERE
                USUARIO = :usuario
                AND ORIGINAL = :original
        SQL;

        $param = [
            'usuario' => $datos['usuario'] ?? $datos['_usuario'] ?? null,
            'original' => $datos['original'],
            'modificado' => $datos['modificado'],
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $param);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function EliminaBitacoraAdmin($datos)
    {
        $qry = <<<SQL
            DELETE FROM
                PAGOSDIA_BITACORA_ADMIN
            WHERE
                USUARIO = :usuario
                AND ORIGINAL = :original
        SQL;

        $param = [
            'usuario' => $datos['usuario'],
            'original' => $datos['original'],
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $param);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function RecuperaSoporte($datos)
    {
        $qry = <<<SQL
            SELECT
                SOPORTE,
                NOMBRE_SOPORTE,
                TIPO_SOPORTE
            FROM
                PAGOSDIA_BITACORA_ADMIN
            WHERE
                USUARIO = :usuario
            ORDER BY
                FECHA DESC
            FETCH FIRST 1 ROWS ONLY
        SQL;

        $param = ['usuario' => $datos['usuario']];

        try {
            $db = new Database();
            return $db->queryOne($qry, $param);
        } catch (\Exception $e) {
            return [];
        }
    }

    public static function GetPagosApp()
    {
        $qry = <<<SQL
            SELECT (COD_SUC || '{$_SESSION['usuario']}' || TO_CHAR(SYSDATE, 'DDMMYYYYHH24MISS')) AS BARRAS
              ,COD_SUC
              ,SUCURSAL
              ,COUNT(NOMBRE) AS NUM_PAGOS
              ,NOMBRE
              ,FECHA_D
              ,FECHA
              ,CDGOCPE
              ,SUM(TOTAL_PAGO) AS TOTAL_PAGOS
              ,SUM(MULTA + MULTA_ELECTRONICA + MULTA_GESTORES) AS TOTAL_MULTA
              ,SUM(SEGURO) AS TOTAL_SEGURO
              ,SUM(AHORRO + AHORRO_ELECTRONICO) AS TOTAL_AHORRO
              ,SUM(MONTO) AS MONTO_TOTAL
            FROM (
                SELECT CO.CODIGO AS COD_SUC
                    ,CO.NOMBRE AS SUCURSAL
                    ,PA.EJECUTIVO AS NOMBRE
                    ,TO_CHAR(PA.FECHA, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') 
                        || ' | ' || TO_CHAR(PA.FECHA, 'DD-MON-YYYY') AS FECHA_D
                    ,TO_CHAR(PA.FECHA, 'DD-MM-YYYY') AS FECHA
                    ,DECODE(PA.TIPO, 'P', MONTO, 0) AS PAGOS
                    ,DECODE(PA.TIPO, 'X', MONTO, 0) AS PAGOS_ELECTRONICOS
                    ,DECODE(PA.TIPO, 'Y', MONTO, 0) AS PAGOS_EXCEDENTE
                    ,DECODE(PA.TIPO, 'O', MONTO, 0) AS PAGOS_EXCEDENTE_ELECTRONICO
                    ,DECODE(PA.TIPO,
                            'P', MONTO,
                            'X', MONTO,
                            'Y', MONTO,
                            'O', MONTO,
                            0) AS TOTAL_PAGO
                    ,DECODE(PA.TIPO, 'M', MONTO, 0) AS MULTA
                    ,DECODE(PA.TIPO, 'Z', MONTO, 0) AS MULTA_GESTORES
                    ,DECODE(PA.TIPO, 'L', MONTO, 0) AS MULTA_ELECTRONICA
                    ,DECODE(PA.TIPO, 'S', MONTO, 0) AS SEGURO
                    ,DECODE(PA.TIPO, 'B', MONTO, 0) AS AHORRO
                    ,DECODE(PA.TIPO, 'F', MONTO, 0) AS AHORRO_ELECTRONICO
                    ,PA.MONTO
                    ,PA.CDGOCPE
                FROM PAGOSDIA_APP PA
                    INNER JOIN PRN ON PRN.CDGNS = PA.CDGNS
                    INNER JOIN CO  ON CO.CODIGO = PRN.CDGCO
                WHERE NVL(PA.ESTATUS_CAJA, 0) != 2
                    AND PRN.CICLO = PA.CICLO
                    AND PRN.CDGCO = CO.CODIGO
                    AND PA.ESTATUS = 'P'
            )
            FILTRO_SUCURSAL
            GROUP BY NOMBRE
                ,FECHA_D
                ,FECHA
                ,CDGOCPE
                ,COD_SUC
                ,SUCURSAL
            ORDER BY TO_DATE(FECHA, 'DD-MM-YYYY')
        SQL;

        // Se añade excepcion temporal para el usuario FLHR que apoya con las pruebas
        if ($_SESSION['perfil'] != 'ADMIN' && $_SESSION['usuario'] != 'FLHR') {
            $qry = str_replace('FILTRO_SUCURSAL', 'WHERE COD_SUC = :sucursal', $qry);
            $params = [
                'sucursal' => $_SESSION['cdgco'] ?? null,
            ];
        } else {
            $qry = str_replace('FILTRO_SUCURSAL', '', $qry);
            $params = [];
        }

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $params);
            return self::Responde(true, "Pagos obtenidos", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los pagos", null, $e->getMessage());
        }
    }

    public static function GetPagosAppResumen($datos)
    {
        $qry = <<<SQL
            SELECT
                SUM(
                    CASE 
                        WHEN PA.ESTATUS <> 'E'
                            AND NVL(PA.TIPO_ORIGINAL, PA.TIPO) IN ('P','X','Y','O','M','Z','L','S','B','F')
                            AND NVL(PA.ESTATUS_CAJA,0) <> 0 
                        THEN 1
                        ELSE 0
                    END
                ) AS TOTAL_VALIDADOS,
                SUM(
                    CASE 
                        WHEN NVL(PA.ESTATUS_CAJA,0) = 2 THEN 1
                        ELSE 0
                    END
                ) AS TOTAL_PROCESADOS,
                SUM(
                    CASE 
                        WHEN PA.ESTATUS <> 'E'
                            AND PA.TIPO IN ('P','X','Y','O','M','Z','L','S','B','F')
                        THEN 1
                        ELSE 0
                    END
                ) AS TOTAL_PAGOS,
                SUM(
                    CASE
                        WHEN PA.ESTATUS <> 'E'
                            AND PA.TIPO IN ('P','X','Y','O','M','Z','L','S','B','F')
                            AND NVL(PA.ESTATUS_CAJA,0) <> 0
                        THEN
                            PA.MONTO
                        ELSE 0
                    END
                ) AS TOTAL
                ,CH.HORA_CIERRE
            FROM
                PAGOSDIA_APP PA
                INNER JOIN PRN ON PRN.CDGNS = PA.CDGNS
                INNER JOIN CO  ON CO.CODIGO = PRN.CDGCO
                INNER JOIN CIERRE_HORARIO CH ON CH.CDGCO = PRN.CDGCO
            WHERE
                PA.CDGOCPE = :ejecutivo
                AND PRN.CICLO = PA.CICLO
                AND PRN.CDGCO = :sucursal
                IMPRIMIR
            GROUP BY
                CH.HORA_CIERRE
        SQL;

        $params = [
            'ejecutivo' => $datos['ejecutivo'] ?? null,
            'sucursal' => $datos['sucursal'] ?? null,
        ];

        if (isset($datos['imprimir'])) {
            $qry = str_replace('IMPRIMIR', 'AND PA.FOLIO_ENTREGA = :folio_entrega', $qry);
            $params['folio_entrega'] = $datos['barcode'] ?? null;
        } else {
            $qry = str_replace('IMPRIMIR', "AND TRUNC(PA.FECHA) = TO_DATE(:fecha, 'DD-MM-YYYY') AND PA.ESTATUS <> 'E' AND PA.FOLIO_ENTREGA IS NULL", $qry);
            $params['fecha'] = $datos['fecha'] ?? null;
        }

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $params);
            return self::Responde(true, "Pagos obtenidos", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los pagos", null, $e->getMessage());
        }
    }

    public static function GetPagosAppEjecutivoDetalle($datos)
    {
        $qry = <<<SQL
            SELECT
                PA.SECUENCIA
                ,TO_CHAR(PA.FECHA, 'DD/MM/YYYY') AS FECHA
                ,PA.CDGNS
                ,PA.NOMBRE
                ,PA.CICLO
                ,PA.CDGOCPE
                ,PA.EJECUTIVO
                ,PA.CDGPE
                ,PA.ESTATUS
                ,PA.FACTUALIZA
                ,PA.MONTO
                ,PA.TIPO
                ,PA.TIPO_ORIGINAL
                ,PA.INCIDENCIA
                ,PA.MONTO_ORIGINAL
                ,UPPER(PA.COMENTARIOS_EJECUTIVO) AS COMENTARIOS_EJECUTIVO
                ,PA.COMENTARIOS_INCIDENCIA
                ,PA.ESTATUS_CAJA
                ,TO_CHAR(PA.FREGISTRO, 'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO
                ,TO_CHAR(PA.FREGISTRO_APP, 'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO_APP
                ,PRN.SITUACION
                ,DECODE(NVL(PA.ESTATUS_CAJA, 0), '0', 'PENDIENTE', '1', 'VALIDADO', '2', 'PROCESADO', 'DESCONOCIDO') AS ESTATUS_CAJA_NOMBRE
            FROM
                PAGOSDIA_APP PA
                INNER JOIN PRN ON PRN.CDGNS = PA.CDGNS
                INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
            WHERE
                PA.CDGOCPE = :ejecutivo
                AND PRN.CICLO = PA.CICLO
                AND PRN.CDGCO = :sucursal
                AND PA.ESTATUS <> 'E'
                IMPRIMIR
            ORDER BY
                DECODE(PA.TIPO, 'P', 1, 'M', 2, 'G', 3, 'D', 4, 'R', 5) ASC, PA.FREGISTRO
        SQL;

        $params = [
            'ejecutivo' => $datos['ejecutivo'] ?? null,
            'sucursal' => $datos['sucursal'] ?? null,
        ];

        if (isset($datos['imprimir'])) {
            $qry = str_replace('IMPRIMIR', 'AND PA.FOLIO_ENTREGA = :folio_entrega', $qry);
            $params['folio_entrega'] = $datos['barcode'] ?? null;
        } else {
            $qry = str_replace('IMPRIMIR', " AND TRUNC(PA.FECHA) = TO_DATE(:fecha, 'DD-MM-YYYY') AND PA.FOLIO_ENTREGA IS NULL", $qry);
            $params['fecha'] = $datos['fecha'] ?? null;
        }

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $params);
            return self::Responde(true, "Pagos obtenidos", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los pagos", null, $e->getMessage());
        }
    }

    public static function GetPagosAppEjecutivo($datos)
    {
        $qry = <<<SQL
            SELECT
                PA.SECUENCIA
                ,TO_CHAR(PA.FECHA, 'DD/MM/YYYY') AS FECHA
                ,PA.CDGNS
                ,PA.NOMBRE
                ,PA.CICLO
                ,PA.CDGOCPE
                ,PA.EJECUTIVO
                ,PA.FREGISTRO
                ,PA.CDGPE
                ,PA.ESTATUS
                ,PA.FACTUALIZA
                ,PA.MONTO
                ,PA.TIPO
                ,PA.TIPO_ORIGINAL
                ,PA.INCIDENCIA
                ,PA.MONTO_ORIGINAL
                ,PA.COMENTARIOS_EJECUTIVO
                ,PA.ESTATUS_CAJA
                ,TO_CHAR(PA.FREGISTRO, 'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO
            FROM
                PAGOSDIA_APP PA
                INNER JOIN PRN ON PRN.CDGNS = PA.CDGNS
                INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
            WHERE
                PA.CDGOCPE = :ejecutivo
                AND TRUNC(PA.FECHA) = TO_DATE(:fecha, 'DD-MM-YYYY')
                AND PA.TIPO IN ('P','X','Y','O','M','Z','L','S','B','F')
                AND PRN.CICLO = PA.CICLO
                AND PRN.CDGCO = :sucursal
                AND PA.ESTATUS = 'P'
                AND NVL(PA.ESTATUS_CAJA, 0) <> 0
                AND PA.FOLIO_ENTREGA IS NULL
            UNION
            SELECT
                PA.SECUENCIA
                ,TO_CHAR(PA.FECHA, 'DD/MM/YYYY') AS FECHA
                ,PA.CDGNS
                ,PA.NOMBRE
                ,PA.CICLO
                ,PA.CDGOCPE
                ,PA.EJECUTIVO
                ,PA.FREGISTRO
                ,PA.CDGPE
                ,PA.ESTATUS
                ,PA.FACTUALIZA
                ,0 AS MONTO
                ,PA.TIPO
                ,PA.TIPO_ORIGINAL
                ,PA.INCIDENCIA
                ,PA.MONTO_ORIGINAL
                ,PA.COMENTARIOS_EJECUTIVO
                ,PA.ESTATUS_CAJA
                ,TO_CHAR(PA.FREGISTRO, 'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO
            FROM
                PAGOSDIA_APP PA
                INNER JOIN PRN ON PRN.CDGNS = PA.CDGNS
                INNER JOIN CO ON CO.CODIGO = PRN.CDGCO
            WHERE
                PA.CDGOCPE = :ejecutivo
                AND TRUNC(PA.FECHA) = TO_DATE(:fecha, 'DD-MM-YYYY')
                AND PA.TIPO IN ('P','X','Y','O','M','Z','L','S','B','F')
                AND PRN.CICLO = PA.CICLO
                AND PRN.CDGCO = :sucursal
                AND PA.ESTATUS = 'P'
                AND NVL(PA.ESTATUS_CAJA, 0) = 0
                AND PA.FOLIO_ENTREGA IS NULL
        SQL;

        $params = [
            'ejecutivo' => $datos['ejecutivo'] ?? null,
            'fecha' => $datos['fecha'] ?? null,
            'sucursal' => $datos['sucursal'] ?? null,
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $params);

            return self::Responde(true, "Pagos obtenidos", $res);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los pagos", null, $e->getMessage());
        }
    }

    public static function ActualizaEstatusPagoApp($datos)
    {
        $qry = <<<SQL
            UPDATE PAGOSDIA_APP
            SET 
                ESTATUS_CAJA = :estatus
            WHERE 
                TRUNC(FECHA) = TO_DATE(:fecha, 'DD/MM/YYYY')
                AND CDGNS = :grupo
                AND CICLO = :ciclo
                AND SECUENCIA = :secuencia
        SQL;

        $params = [
            'estatus' => $datos['estatus'] ?? null,
            'fecha' => $datos['fecha'] ?? null,
            'grupo' => $datos['grupo'] ?? null,
            'ciclo' => $datos['ciclo'] ?? null,
            'secuencia' => $datos['secuencia'] ?? null
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $params);
            return self::Responde(true, "Pago actualizado", $params);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al actualizar el pago", null, $e->getMessage());
        }
    }

    public static function ActualizaInfoPagoApp($datos)
    {
        $qry = <<<SQL
            UPDATE PAGOSDIA_APP
            SET 
                TIPO = :tipo
                , MONTO = :monto
                , TIPO_ORIGINAL = DECODE(TIPO_ORIGINAL, NULL, DECODE(TIPO, :tipo, TIPO_ORIGINAL, TIPO), TIPO_ORIGINAL)
                , MONTO_ORIGINAL = DECODE(MONTO_ORIGINAL, NULL, DECODE(MONTO, :monto, MONTO_ORIGINAL, MONTO), MONTO_ORIGINAL)
                , COMENTARIOS_INCIDENCIA = :comentario
                , INCIDENCIA = 1
                , FACTUALIZA = SYSDATE
            WHERE 
                TRUNC(FECHA) = TO_DATE(:fecha, 'DD/MM/YYYY')
                AND CDGNS = :grupo
                AND CICLO = :ciclo
                AND SECUENCIA = :secuencia
        SQL;

        $params = [
            'tipo' => $datos['tipo'] ?? null,
            'monto' => $datos['monto'] ?? null,
            'comentario' => $datos['comentario'] ?? null,
            'fecha' => $datos['fecha'] ?? null,
            'grupo' => $datos['grupo'] ?? null,
            'ciclo' => $datos['ciclo'] ?? null,
            'secuencia' => $datos['secuencia'] ?? null
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $params);
            return self::Responde(true, "Pago actualizado", $params);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al actualizar el pago", null, $e->getMessage());
        }
    }

    public static function ProcesarPagosApp($datos)
    {
        $qry = <<<SQL
            UPDATE
                PAGOSDIA_APP
            SET  FECHA = TO_DATE(:fecha_aplicacion, 'YYYY-MM-DD')
                ,ESTATUS = 'A'
                ,ESTATUS_CAJA = 2
                ,FPROCESAPAGO = SYSDATE
                ,FOLIO_ENTREGA = :barcode
                ,CDGPE = :cdgpe
            WHERE
                TRUNC(FECHA) = TO_DATE(:fecha, 'DD/MM/YYYY')
                AND CDGNS = :grupo
                AND CICLO = :ciclo
                AND SECUENCIA = :secuencia
        SQL;

        $params = [];

        try {
            $db = new Database();
            foreach ($datos['pagos'] as $pago) {
                $qrys[] = $qry;
                $params[] = [
                    'fecha' => $pago['fecha'],
                    'fecha_aplicacion' => $pago['fechaAplicacion'],
                    'grupo' => $pago['grupo'],
                    'ciclo' => $pago['ciclo'],
                    'secuencia' => $pago['secuencia'],
                    'barcode' => $datos['barcode'],
                    'cdgpe' => $datos['cdgpe'],
                ];
            }

            $db->insertaMultiple($qrys, $params);
            return self::Responde(true, 'Pago agregado correctamente');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al agregar pago', null, $e->getMessage());
        }
    }

    public static function ReciboPagosApp($datos)
    {
        $qry_monto = <<<SQL
            SELECT
                TO_CHAR(TRUNC(FPROCESAPAGO), 'DD/MM/YYYY') AS FECHA_ENTREGA
                ,SUM(PA.MONTO) AS MONTO
                ,PA.FOLIO_ENTREGA AS FOLIO
            FROM
                PAGOSDIA_APP PA
                INNER JOIN PRN ON PRN.CDGNS = PA.CDGNS
            WHERE
                PA.FOLIO_ENTREGA = :folio_entrega
                AND PA.ESTATUS = 'A'
                AND PA.TIPO IN ('P', 'Y', 'M', 'Z', 'S', 'B')
                AND PRN.CICLO = PA.CICLO
                AND NVL(PA.ESTATUS_CAJA, 0) = 2
            GROUP BY
                TO_CHAR(TRUNC(FPROCESAPAGO), 'DD/MM/YYYY')
                ,PA.FOLIO_ENTREGA
        SQL;

        $params_monto = [
            'folio_entrega' => $datos['barcode'] ?? null,
        ];

        $qry_sucursal = <<<SQL
            SELECT
                CO.CODIGO AS SUCURSAL
                , CO.NOMBRE AS SUCURSAL_NOMBRE
            FROM
                CO
            WHERE
                CO.CODIGO = :sucursal
        SQL;

        $params_sucursal = [
            'sucursal' => $datos['sucursal'] ?? null,
        ];

        $qry_ejecutivo = <<<SQL
            SELECT
                CODIGO AS EJECUTIVO
                , CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS EJECUTIVO_NOMBRE
                , CASE WHEN PE.SEXO = 'M' THEN 'del ejecutivo' ELSE 'de la ejecutiva' END AS EJECUTIVO_GENERO
            FROM
                PE
            WHERE
                PE.CODIGO = :ejecutivo
        SQL;

        $params_ejecutivo = [
            'ejecutivo' => $datos['cdgpe'] ?? null,
        ];

        try {
            $db = new Database();
            $monto = $db->queryOne($qry_monto, $params_monto);
            if (!$monto) return self::Responde(false, "No se encontraron pagos para el recibo solicitado", null);

            $sucursal = $db->queryOne($qry_sucursal, $params_sucursal);
            if (!$sucursal) return self::Responde(false, "No se encontró la sucursal para el recibo solicitado", null);

            $ejecutivo = $db->queryOne($qry_ejecutivo, $params_ejecutivo);
            if (!$ejecutivo) return self::Responde(false, "No se encontró el ejecutivo para el recibo solicitado", null);

            $resultado = array_merge($monto, $sucursal, $ejecutivo);

            return self::Responde(true, "Datos para recibo obtenidos", $resultado);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los datos para recibo solicitado", null, $e->getMessage());
        }
    }
}
