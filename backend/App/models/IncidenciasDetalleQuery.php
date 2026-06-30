<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

class IncidenciasDetalleQuery
{
    public static function codigosPeIn(): string
    {
        return "'MCDP', 'LVGA', 'ORHM', 'MAPH', 'PHEE', 'JUJG', 'HTMP','HZDA', 'FSBA', 'CSLL', 'HELL', 'BCHF', 'JVRE', 'ESMM', 'GAGR'";
    }

    public static function sqlModuloOrigen(): string
    {
        return <<<'CASESQL'
CASE
    WHEN UPPER(Q1.REFERENCIA) LIKE 'CAJA PAGOS%' THEN 'pagos'
    WHEN Q1.REFERENCIA LIKE 'AJUSTE MANUAL%' THEN 'ajuste'
    WHEN UPPER(Q1.REFERENCIA) LIKE 'MODULO DE GARANT%' THEN 'gar'
    WHEN Q1.REFERENCIA LIKE 'CALL CENTER%' THEN 'call'
    ELSE 'otro'
END
CASESQL;
    }

    public static function sqlQ1(): string
    {
        return <<<'SQL'
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
                        'CAJA PAGOS DIA' AS REFERENCIA,
                        NVL(CO.NOMBRE, '—') AS SUCURSAL,
                        NVL(RG.NOMBRE, '—') AS REGION
                    FROM
                        PAGOSDIA PD
                        LEFT JOIN PRN ON PD.CDGNS = PRN.CDGNS AND PD.CICLO = PRN.CICLO
                        LEFT JOIN CO ON PRN.CDGCO = CO.CODIGO
                        LEFT JOIN RG ON CO.CDGRG = RG.CODIGO
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
                        NVL(CO.NOMBRE, '—'),
                        NVL(RG.NOMBRE, '—'),
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
                        CASE WHEN CMA.DESCRIPCION IS NULL THEN NVL(UPPER(m.REFERENCIA), 'AJUSTE MANUAL')
                            WHEN CMA.DESCRIPCION = 'PAGO ELIMINADO POR EL BANCO'  AND MPR.OBSERVACIONES LIKE '%REFINANCIAMIENTO%'  AND MPR.RAZON = '05'THEN 'CANCELACIÓN DE REFINANCIAMIENTO'
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
                        NVL(CO.NOMBRE, '—') AS SUCURSAL,
                        NVL(RG.NOMBRE, '—') AS REGION
                    FROM
                        MP m
                        LEFT JOIN MPR ON m.CDGNS = MPR.CDGNS AND m.CICLO = MPR.CICLO AND m.SECUENCIA = MPR.SECUENCIA AND m.PERIODO = MPR.PERIODO 
                        LEFT JOIN CAT_MOVS_AJUSTE CMA ON MPR.RAZON = CMA.CODIGO 
                        LEFT JOIN PRN ON m.CDGNS = PRN.CDGNS AND m.CICLO = PRN.CICLO
                        LEFT JOIN CO ON PRN.CDGCO = CO.CODIGO
                        LEFT JOIN RG ON CO.CDGRG = RG.CODIGO
                    WHERE
                        m.FDEPOSITO BETWEEN TRUNC(ADD_MONTHS(SYSDATE, -12), 'MM') AND LAST_DAY(SYSDATE)
                        AND m.ACTUALIZARPE IS NOT NULL
                        AND m.TIPO != 'PD'
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
                        NVL(CO.NOMBRE, '—'),
                        NVL(RG.NOMBRE, '—'),
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
                
SQL;
    }
}
