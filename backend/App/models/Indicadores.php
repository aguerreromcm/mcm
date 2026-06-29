<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Model;
use Core\Database;

class Indicadores extends Model
{
    public static function GetIncidenciasUsuarios()
    {
        $qry = <<<SQL
            WITH DATOS AS (
                SELECT
                    PD.CDGPE,
                    TO_CHAR(PD.FREGISTRO, 'YYYY') AS ANO,
                    TO_CHAR(PD.FREGISTRO, 'MM') AS MES,
                    COUNT(PD.CDGPE) AS TOTAL_INCIDENCIAS,
                    'PAGO DÍA' AS TIPO, 
                    'ACTUALIZACIÓN' AS REFERENCIA
                FROM
                    PAGOSDIA PD
                WHERE
                    PD.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                GROUP BY
                    PD.CDGPE,
                    TO_CHAR(PD.FREGISTRO, 'YYYY'),
                    TO_CHAR(PD.FREGISTRO, 'MM')
                    
                UNION 
                    
                SELECT
                    m.ACTUALIZARPE AS CDGPE,  
                    TO_CHAR(m.FDEPOSITO , 'YYYY') AS ANO,
                    TO_CHAR(m.FDEPOSITO , 'MM') AS MES,
                    COUNT(m.ACTUALIZARPE) AS TOTAL_INCIDENCIAS, 
                    m.TIPO , 
                    m.REFERENCIA 
                FROM
                    MP m
                WHERE
                    m.FDEPOSITO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                    AND m.ACTUALIZARPE IS NOT NULL
                    AND m.TIPO != 'PD'
                GROUP BY
                    m.ACTUALIZARPE,
                    TO_CHAR(m.FDEPOSITO, 'YYYY'),
                    TO_CHAR(m.FDEPOSITO, 'MM'),
                    m.TIPO,
                    m.REFERENCIA 
                        
                UNION       
                    
                SELECT
                    pgs.CDGPE,  
                    TO_CHAR(pgs.FREGISTRO  , 'YYYY') AS ANO,
                    TO_CHAR(pgs.FREGISTRO  , 'MM') AS MES,
                    COUNT(pgs.CDGPE) AS TOTAL_INCIDENCIAS, 
                    'APLICACION GARANTIA' AS TIPO, 
                    '' AS REFERENCIA
                FROM
                    PAG_GAR_SIM pgs
                WHERE
                    pgs.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                    AND pgs.ESTATUS = 'CP'
                GROUP BY
                    pgs.CDGPE,
                    TO_CHAR(pgs.FREGISTRO, 'YYYY'),
                    TO_CHAR(pgs.FREGISTRO, 'MM')
                
                UNION
                    
                SELECT 
                    scc.CDGPE, 
                    TO_CHAR(scc.FECHA_TRA_CL  , 'YYYY') AS ANO,
                    TO_CHAR(scc.FECHA_TRA_CL  , 'MM') AS MES,
                    COUNT(scc.CDGPE) AS TOTAL_INCIDENCIAS, 
                    CASE SN.SITUACION
                        WHEN 'S' THEN 'CREDITO PENDIENTE'
                        WHEN 'A' THEN 'CREDITO APROBADO'
                        WHEN 'R' THEN 'CREDITO RECHAZADO'
                        ELSE 'OTRO' -- por si hubiera valores diferentes
                    END AS TIPO,
                    'CALL CENTER APROBACIONES/RECHAZOS' AS REFERENCIA
                FROM
                    SOL_CALL_CENTER scc 
                    JOIN SN ON SN.CDGNS = scc.CDGNS AND SN.CICLO = scc.CICLO AND SN.SOLICITUD = scc.FECHA_SOL 
                    JOIN CO ON CO.CODIGO = scc.CDGCO 
                    JOIN RG ON RG.CODIGO  = CO.CDGRG  
                WHERE
                    scc.FECHA_TRA_CL BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                GROUP BY
                    scc.CDGPE,
                    TO_CHAR(scc.FECHA_TRA_CL, 'YYYY'),
                    TO_CHAR(scc.FECHA_TRA_CL, 'MM'),
                    SN.SITUACION
            ),
            TOTAL AS (
                SELECT
                    D.CDGPE,
                    D.ANO,
                    D.MES,
                    SUM(D.TOTAL_INCIDENCIAS) AS TOTAL
                FROM
                    DATOS D
                GROUP BY
                    D.CDGPE,
                    D.ANO,
                    D.MES
            )
            SELECT
                T.CDGPE,
                T.ANO,
                T.MES,
                T.TOTAL AS TOTAL_INCIDENCIAS,
                CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE,
                TO_CHAR(TO_DATE(T.MES, 'MM'), 'Month') AS MES_LETRA
            FROM
                TOTAL T
                JOIN PE ON PE.CODIGO = T.CDGPE
            WHERE
                PE.ACTIVO = 'S'
                AND PE.CODIGO IN ('MCDP', 'LVGA', 'GOBA', 'MAPH', 'PHEE', 'JUJG', 'HTMP','HZDA', 'FSBA', 'CSLL', 'HELL', 'BCHF', 'JVRE', 'ESMM', 'GAGR')
            ORDER BY
                T.ANO DESC,
                T.MES DESC,
                T.CDGPE
        SQL;

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, 'Incidencias de usuario obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener incidencias de usuario', null, $e->getMessage());
        }
    }

    public static function GetIncidenciasUsuario($datos)
    {
        $qry = <<<SQL
            SELECT
                    Q1.FECHA AS FECHA,
                    Q1.CDGNS,
                    Q1.CICLO,
                    Q1.MONTO,
                    Q1.REFERENCIA AS DESCRIPCION,
                    Q1.TIPO,
                    Q1.REGION,
                    Q1.SUCURSAL
            FROM
                (
                    SELECT
                        PD.CDGNS,
                        PD.CICLO,
                        PD.MONTO,
                        PD.CDGPE,
                        TO_CHAR(PD.FREGISTRO, 'YYYY') AS ANO,
                        TO_CHAR(PD.FREGISTRO, 'MM') AS MES,
                        PD.FREGISTRO AS FECHA, 
                        COUNT(PD.CDGPE) AS TOTAL_INCIDENCIAS,
                        CASE
                            WHEN PD.ESTATUS = 'A' AND PD.FACTUALIZA IS NULL THEN 
                                'REGISTRO DE ' || 
                                CASE PD.TIPO
                                    WHEN 'P' THEN 'PAGO'
                                    WHEN 'X' THEN 'PAGO ELECTRÓNICO'
                                    WHEN 'Y' THEN 'PAGO EXCEDENTE'
                                    WHEN 'M' THEN 'MULTA'
                                    WHEN 'Z' THEN 'MULTA GESTORES'
                                    WHEN 'L' THEN 'MULTA ELECTRÓNICA'
                                    WHEN 'G' THEN 'GARANTÍA'
                                    WHEN 'D' THEN 'DESCUENTO'
                                    WHEN 'R' THEN 'REFINANCIAMIENTO'
                                    WHEN 'H' THEN 'RECOMIENDA'
                                    WHEN 'S' THEN 'SEGURO'
                                    ELSE 'DESCONOCIDO'
                                END
                            WHEN PD.ESTATUS = 'E' THEN 
                                'ELIMINACIÓN DE ' || 
                                CASE PD.TIPO
                                    WHEN 'P' THEN 'PAGO'
                                    WHEN 'X' THEN 'PAGO ELECTRÓNICO'
                                    WHEN 'Y' THEN 'PAGO EXCEDENTE'
                                    WHEN 'M' THEN 'MULTA'
                                    WHEN 'Z' THEN 'MULTA GESTORES'
                                    WHEN 'L' THEN 'MULTA ELECTRÓNICA'
                                    WHEN 'G' THEN 'GARANTÍA'
                                    WHEN 'D' THEN 'DESCUENTO'
                                    WHEN 'R' THEN 'REFINANCIAMIENTO'
                                    WHEN 'H' THEN 'RECOMIENDA'
                                    WHEN 'S' THEN 'SEGURO'
                                    ELSE 'DESCONOCIDO'
                                END
                            WHEN PD.ESTATUS = 'A' AND PD.FACTUALIZA IS NOT NULL THEN 
                                'ACTUALIZACIÓN DE ' || 
                                CASE PD.TIPO
                                    WHEN 'P' THEN 'PAGO'
                                    WHEN 'X' THEN 'PAGO ELECTRÓNICO'
                                    WHEN 'Y' THEN 'PAGO EXCEDENTE'
                                    WHEN 'M' THEN 'MULTA'
                                    WHEN 'Z' THEN 'MULTA GESTORES'
                                    WHEN 'L' THEN 'MULTA ELECTRÓNICA'
                                    WHEN 'G' THEN 'GARANTÍA'
                                    WHEN 'D' THEN 'DESCUENTO'
                                    WHEN 'R' THEN 'REFINANCIAMIENTO'
                                    WHEN 'H' THEN 'RECOMIENDA'
                                    WHEN 'S' THEN 'SEGURO'
                                    ELSE 'DESCONOCIDO'
                                END
                            ELSE 'TIPO NO DEFINIDO'
                        END AS TIPO,
                        'CAJA PAGOS DÍA' AS REFERENCIA,
                        CO.NOMBRE AS SUCURSAL,
                        RG.NOMBRE AS REGION
                    FROM
                        PAGOSDIA PD
                        JOIN PRN ON PD.CDGNS = PRN.CDGNS AND PD.CICLO = PRN.CICLO
                        JOIN CO ON PRN.CDGCO = CO.CODIGO
                        JOIN RG ON CO.CDGRG = RG.CODIGO
                    WHERE
                        PD.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                    GROUP BY
                        PD.CDGNS,
                        PD.CICLO,
                        PD.MONTO,
                        PD.CDGPE,
                        TO_CHAR(PD.FREGISTRO, 'YYYY'),
                        TO_CHAR(PD.FREGISTRO, 'MM'),
                        PD.FREGISTRO, 
                        CO.NOMBRE ,
                        RG.NOMBRE,
                        PD.ESTATUS,
                        PD.FACTUALIZA,
                        PD.TIPO
                    UNION
                    SELECT
                        m.CDGNS, 
                        m.CICLO,
                        m.CANTIDAD AS MONTO,
                        m.ACTUALIZARPE AS CDGPE,  
                        TO_CHAR(m.FDEPOSITO , 'YYYY') AS ANO,
                        TO_CHAR(m.FDEPOSITO , 'MM') AS MES,
                        m.FDEPOSITO AS FECHA, 
                        COUNT(m.ACTUALIZARPE) AS TOTAL_INCIDENCIAS, 
                        CASE WHEN CMA.DESCRIPCION = 'PAGO ELIMINADO POR EL BANCO'  AND MPR.OBSERVACIONES LIKE '%REFINANCIAMIENTO%'  AND MPR.RAZON = '05'THEN 'CANCELACIÓN DE REFINANCIAMIENTO'
                            WHEN CMA.DESCRIPCION = 'DAÑOS ECONOMICOS AUT. TERRITORIAL' THEN 'AJUSTE POR DAÑOS ECONOMICOS'
                            WHEN CMA.DESCRIPCION = 'DEVOLUCION POR CHEQUE A SOLICITUD DEL CLIENTE O SUCURSAL' THEN 'DEVOLUCION POR CHEQUE A SOLICITUD DEL CLIENTE O SUCURSAL'
                            WHEN CMA.DESCRIPCION = 'REFERENCIA INCORRECTA' THEN 'REFERENCIA INCORRECTA'
                            WHEN CMA.DESCRIPCION = 'TRASPASO DE GARANTIA A PAGO' THEN 'TRASPASO DE GARANTIA A PAGO'
                            WHEN CMA.DESCRIPCION = 'CONDONACION POR DEFUNCION' THEN 'DEFUNCION (CONDONACIÓN)'
                            WHEN CMA.DESCRIPCION = 'LIQUIDACION ANTICIPADA' THEN 'LIQUIDACION ANTICIPADA'
                            WHEN CMA.DESCRIPCION = 'PAGO ELIMINADO POR EL BANCO' THEN 'PAGO ELIMINADO DE CARTERA'
                            WHEN CMA.DESCRIPCION = 'DEVOLUCION DE CHEQUE' THEN 'DEVOLUCION DE CHEQUE'
                            WHEN CMA.DESCRIPCION = 'REESTRUCTURA' THEN 'REGISTRO DE REFINANCIAMIENTO'
                            WHEN CMA.DESCRIPCION = 'CONDONACION DE INTERES POR ERROR EN TASA' THEN 'CONDONACION DE INTERES POR ERROR EN TASA'
                            WHEN CMA.DESCRIPCION = 'DEVOLUCION POR CANCELACION DE CHEQUE' THEN 'DEVOLUCION POR CANCELACION DE CHEQUE'
                            WHEN CMA.DESCRIPCION = 'CANCELACION POR APLICACIÓN DE PAGO A MICROCREDITO' THEN 'CANCELACION POR APLICACIÓN DE PAGO A MICROCREDITO'
                            WHEN CMA.DESCRIPCION = 'CANCELACION PARA APLICACION AL SIGUIENTE CICLO' THEN 'CANCELACION PARA APLICACION AL SIGUIENTE CICLO'
                            WHEN CMA.DESCRIPCION = 'REGISTRO DE PAGO DE GARANTIA' THEN 'REGISTRO DE PAGO DE GARANTIA'
                            WHEN CMA.DESCRIPCION = 'PAGO DE MORA CON GARANTIA LIQUIDA' THEN 'PAGO DE MORA CON GARANTIA LIQUIDA'
                            WHEN CMA.DESCRIPCION = 'DEVOLUCION POR RECHAZO DE SOLICITUD' THEN 'DEVOLUCION POR RECHAZO DE SOLICITUD'
                            WHEN CMA.DESCRIPCION = 'CANCELACION PARA APLICACION A PAGO DE CREDITO' THEN 'CANCELACION PARA APLICACION A PAGO DE CREDITO'
                            WHEN CMA.DESCRIPCION = 'CANCELACION DE CHEQUE DE DEVOLUCION DE GARANTIA' THEN 'CANCELACION DE CHEQUE DE DEVOLUCION DE GARANTIA'
                            WHEN CMA.DESCRIPCION = 'CANCELACION PARA APLICACION A GARANTIA DE OTRO GRUPO' THEN 'CANCELACION PARA APLICACION A GARANTIA DE OTRO GRUPO'
                            WHEN CMA.DESCRIPCION = 'TRASPASO DE PAGO A GARANTIA' THEN 'TRASPASO DE PAGO A GARANTIA'
                            WHEN CMA.DESCRIPCION = 'CONDONACION DE INTERES PARA CASTIGOS' THEN 'MARCADO DE CARTERA POR CASTIGO'
                            WHEN CMA.DESCRIPCION = 'REDISTRIBUCION DE PAGOS' THEN 'REDISTRIBUCION DE PAGOS'
                            WHEN CMA.DESCRIPCION = 'CONDONACION DE INTERES POR AJUSTE' THEN 'CONDONACION DE INTERES POR AJUSTE'
                            WHEN CMA.DESCRIPCION = 'CONDONACION DE SALDOS' THEN 'CONDONACION DE SALDOS'
                            WHEN CMA.DESCRIPCION = 'CONDONACION DE INTERES PARA LIQUIDACION' THEN 'CONDONACION DE INTERES PARA LIQUIDACION'
                            WHEN CMA.DESCRIPCION = 'DEVOLUCION DE EXCEDENTE' THEN 'DEVOLUCION DE EXCEDENTE'
                            WHEN CMA.DESCRIPCION = 'CANCELACION DE DEVOLUCION DE EXCEDENTE' THEN 'CANCELACION DE DEVOLUCION DE EXCEDENTE'
                            WHEN CMA.DESCRIPCION = 'DEVOLUCION DE CHEQUE EXTEMPORANEO' THEN 'DEVOLUCION DE CHEQUE EXTEMPORANEO'
                            WHEN CMA.DESCRIPCION = 'DEVOLUCION POR DEPOSITO EXCEDENTE' THEN 'DEVOLUCION POR DEPOSITO EXCEDENTE'
                            WHEN CMA.DESCRIPCION = 'CANCELACION SALDOS A FAVOR CREDITOS OPORTUNOS' THEN 'CANCELACION SALDOS A FAVOR CREDITOS OPORTUNOS'
                            WHEN CMA.DESCRIPCION = 'DESCUENTO DE GL A OTROS QUEBRANTOS' THEN 'DESCUENTO DE GL A OTROS QUEBRANTOS'
                            WHEN CMA.DESCRIPCION = 'RENOVACION ANTICIPADA' THEN 'RENOVACION ANTICIPADA'
                            WHEN CMA.DESCRIPCION = 'DAÑOS ECONOMICOS' THEN 'DAÑOS ECONOMICOS'
                            ELSE 'DESCONOCIDO'
                            END AS TIPO,
                        UPPER('AJUSTE MANUAL - ' || m.REFERENCIA) AS REFERENCIA,
                        CO.NOMBRE AS SUCURSAL,
                        RG.NOMBRE AS REGION
                    FROM
                        MP m
                        JOIN MPR ON m.CDGNS = MPR.CDGNS AND m.CICLO = MPR.CICLO AND m.SECUENCIA = MPR.SECUENCIA AND m.PERIODO = MPR.PERIODO 
                        JOIN CAT_MOVS_AJUSTE CMA ON MPR.RAZON = CMA.CODIGO 
                        JOIN PRN ON m.CDGNS = PRN.CDGNS AND m.CICLO = PRN.CICLO
                        JOIN CO ON PRN.CDGCO = CO.CODIGO
                        JOIN RG ON CO.CDGRG = RG.CODIGO
                    WHERE
                        m.FDEPOSITO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                        AND m.ACTUALIZARPE IS NOT NULL
                        AND m.TIPO != 'PD'
                        AND  m.CANTIDAD > 2 OR m.CANTIDAD < -100
                        AND m.REFERENCIA != 'Interés total del préstamo'
                    GROUP BY
                        m.CDGNS, 
                        m.CICLO,
                        m.CANTIDAD,
                        m.ACTUALIZARPE,
                        TO_CHAR(m.FDEPOSITO, 'YYYY'),
                        TO_CHAR(m.FDEPOSITO, 'MM'),
                        m.FDEPOSITO, 
                        m.TIPO,
                        m.REFERENCIA, 
                        CO.NOMBRE ,
                        RG.NOMBRE,
                        MPR.RAZON, 
                        CMA.DESCRIPCION,
                        MPR.OBSERVACIONES,
                        CMA.CODIGO
                    UNION       
                    SELECT 
                        pgs.CDGCLNS AS CDGNS,
                        pgs.CICLO, 
                        pgs.CANTIDAD AS MONTO, 
                        pgs.CDGPE,  
                        TO_CHAR(pgs.FREGISTRO  , 'YYYY') AS ANO,
                        TO_CHAR(pgs.FREGISTRO  , 'MM') AS MES,
                        pgs.FREGISTRO AS FECHA, 
                        COUNT(pgs.CDGPE) AS TOTAL_INCIDENCIAS, 
                        CASE pgs.ESTATUS WHEN 'DE' THEN 'DEVOLUCION POR DEPOSITO EXCEDENTE'
                            WHEN 'RE' THEN 'PAGO GL'
                            WHEN 'CA' THEN 'MOVIMIENTO CANCELADO'
                            WHEN 'DC' THEN 'DEVOLUCION POR CANCELACION DE CHEQUE'
                            WHEN 'DS' THEN 'DEVOLUCION POR SOLICITUD DEL CLIENTE'
                            WHEN 'CP' THEN 'CANCELACION POR APLICACION A PAGO DE CREDITO'
                            WHEN 'CG' THEN 'CANCELACION POR TRASPASO DE GARANTIA A CICLO SIGUIENTE'
                            WHEN 'DM' THEN 'PAGO DE MORA CON GARANTIA LIQUIDA'
                            WHEN 'DG' THEN 'DESCUENTO DE GL A OTROS QUEBRANTOS'
                            WHEN 'DR' THEN 'DEVOLUCION POR RECHAZO DE CREDITO'
                            WHEN 'GP' THEN 'CANCELACION POR APLICACION DE GARANTIA A PAGO'
                            WHEN 'CD' THEN 'CANCELACION DE DEVOLUCION DE GARANTIA'
                            WHEN 'GG' THEN 'CANCELACION POR APLICACION DE GARANTIA A OTRO GRUPO'
                            WHEN 'CS' THEN 'CANCELACION DE SEGURO POR DEVOLUCION'
                            WHEN 'MS' THEN 'PAGO DE MICROSEGURO CON GARANTIA'
                            ELSE 'OTRO' -- por si llega un código no contemplado
                        END AS TIPO, 
                        'MODULO DE GARANTIAS' AS REFERENCIA,
                        CO.NOMBRE AS SUCURSAL, 
                        RG.NOMBRE AS REGION
                    FROM
                        PAG_GAR_SIM pgs
                        JOIN PRN ON pgs.CDGCLNS = PRN.CDGNS AND pgs.CICLO = PRN.CICLO
                        JOIN CO ON PRN.CDGCO = CO.CODIGO
                        JOIN RG ON CO.CDGRG = RG.CODIGO
                    WHERE
                        pgs.FREGISTRO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                        AND pgs.ESTATUS = 'CP'
                    GROUP BY
                        pgs.CDGCLNS, 
                        pgs.CICLO,
                        pgs.CANTIDAD, 
                        pgs.CDGPE,
                        TO_CHAR(pgs.FREGISTRO, 'YYYY'),
                        TO_CHAR(pgs.FREGISTRO, 'MM'),
                        pgs.FREGISTRO, 
                        CO.NOMBRE,
                        RG.NOMBRE,
                        pgs.ESTATUS
                    UNION 
                        SELECT 
                            scc.CDGNS,
                            scc.CICLO, 
                            SN.CANTAUTOR AS MONTO, 
                            scc.CDGPE,  
                            TO_CHAR(scc.FECHA_TRA_CL  , 'YYYY') AS ANO,
                            TO_CHAR(scc.FECHA_TRA_CL  , 'MM') AS MES,
                            scc.FECHA_TRA_CL AS FECHA,
                            COUNT(scc.CDGPE) AS TOTAL_INCIDENCIAS, 
                            CASE SN.SITUACION
                                WHEN 'S' THEN 'CREDITO PENDIENTE'
                                WHEN 'A' THEN 'CREDITO APROBADO'
                                WHEN 'R' THEN 'CREDITO RECHAZADO'
                                ELSE 'OTRO' -- por si hubiera valores diferentes
                            END AS TIPO,
                            'CALL CENTER APROBACIONES/RECHAZOS' AS REFERENCIA,
                            CO.NOMBRE AS SUCURSAL,
                            RG.NOMBRE AS REGION
                    FROM
                        SOL_CALL_CENTER scc 
                        JOIN SN ON SN.CDGNS = scc.CDGNS AND SN.CICLO = scc.CICLO AND SN.SOLICITUD = scc.FECHA_SOL 
                        JOIN CO ON CO.CODIGO = scc.CDGCO 
                        JOIN RG ON RG.CODIGO  = CO.CDGRG  
                    WHERE
                        scc.FECHA_TRA_CL BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                    GROUP BY
                        scc.CDGPE,
                        TO_CHAR(scc.FECHA_TRA_CL, 'YYYY'),
                        TO_CHAR(scc.FECHA_TRA_CL, 'MM'),
                        scc.CDGNS,
                        scc.CICLO,
                        SN.CANTAUTOR,
                        scc.CDGPE,
                        scc.FECHA_TRA_CL,
                        CO.NOMBRE,
                        RG.NOMBRE, 
                        SN.SITUACION
                ) Q1
                JOIN PE ON Q1.CDGPE = PE.CODIGO
            WHERE
                Q1.FECHA BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                AND Q1.CDGPE = :usuario
            ORDER BY
                Q1.FECHA DESC
        SQL;

        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF'],
            'usuario' => $datos['usuario']
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Incidencias de usuario obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener incidencias de usuario', null, $e->getMessage());
        }
    }
}
