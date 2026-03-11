<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use Core\Model;

class Operaciones extends Model
{
    public static function ConsultarDesembolsos($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                DESEMBOLSOS_VIEW  
            WHERE
                FDEPOSITO BETWEEN TO_DATE('$Inicial', 'YY-mm-dd') AND TO_DATE('$Final', 'YY-mm-dd')
            ORDER BY
                FDEPOSITO ASC
        SQL;

        try {
            $db = new Database('SERVIDOR-CULTIVA');
            return $db->queryAll($query);
        } catch (\Exception $e) {
            return "";
        }
    }

    public static function ConsultarClientes($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                DISTINCT TO_CHAR(' '||CDGCL) AS CDGCL, TO_CHAR(GRUPO) AS GRUPO, ORIGEN, CLIENTES AS NOMBRE, ADICIONAL,
                A_PATERNO, A_MATERNO, TIPO_PERSONA, RFC, CURP, RAZON_SOCIAL, FECHA_NAC, NACIONALIDAD, DOMICILIO,
                COLONIA, CIUDAD, PAIS, SUC_ID_ESTADO, TELEFONO, ID_ACTIVIDAD_ECONO, CALIFICACION, ALTA,
                TO_CHAR(ID_SUCL_SISTEMA) AS ID_SUCURSAL_SISTEMA, GENERO, CORREO_ELECTRONICO, FIRMA_ELECT, PROFESION,
                OCUPACION, PAIS_NAC, EDO_NAC, LUGAR_NAC, NUMERO_DOCUMENTO, CONOCIMIENTO, INMIGRACION,CUENTA_ORIGINAL,
                SITUACION_CREDITO, TIPO_DOCUMENTO, INDICADOR_EMPLEO, EMPRESAS, INDICADOR_GOBIERNO, PUESTO, FECHA_INICIO,
                FEH_FIN, CP, FECHA_ALTA
            FROM
                SUB_CLIENTES_PERFIL
            WHERE
                FECHA_ALTA BETWEEN TO_DATE('$Inicial', 'YY-mm-dd') AND TO_DATE('$Final', 'YY-mm-dd') 
            GROUP BY 
                CDGCL, GRUPO, ORIGEN, CLIENTES, ADICIONAL, A_PATERNO, A_MATERNO, TIPO_PERSONA, RFC, CURP, RAZON_SOCIAL, 
                FECHA_NAC, NACIONALIDAD, DOMICILIO, COLONIA, CIUDAD, PAIS, SUC_ID_ESTADO, TELEFONO, ID_ACTIVIDAD_ECONO, 
                CALIFICACION, ALTA, ID_SUCL_SISTEMA, GENERO, CORREO_ELECTRONICO, FIRMA_ELECT, PROFESION, OCUPACION, 
                PAIS_NAC, EDO_NAC, LUGAR_NAC, NUMERO_DOCUMENTO, CONOCIMIENTO, INMIGRACION,CUENTA_ORIGINAL, SITUACION_CREDITO,
                TIPO_DOCUMENTO, INDICADOR_EMPLEO, EMPRESAS, INDICADOR_GOBIERNO, PUESTO, FECHA_INICIO, FEH_FIN, CP, FECHA_ALTA
            ORDER BY
                FECHA_ALTA DESC
        SQL;

        $db = new Database('SERVIDOR-CULTIVA');
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function CuentasRelacionadas($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                DISTINCT TO_CHAR(' ' || CDGCL) AS CLIENTE,
                TO_CHAR(GRUPO) AS GRUPO,
                ULTIMO_CICLO AS CUENTA_RELACION,
                CLIENTES AS NOMBRE,
                TO_CHAR(ADICIONAL) AS ADICIONAL,
                TO_CHAR(A_PATERNO) AS A_PATERNO,
                TO_CHAR(A_MATERNO) AS A_MATERNO,
                'PRESTAMO ' || ULTIMO_CICLO AS DESCRIPCION_OPERACION,
                CASE
                    WHEN ULTIMO_CICLO = '01' THEN '0'
                    ELSE '1'
                END AS IDENTIFICA_CUENTA,
                '' AS CONSERVA,
                ID_SUCL_SISTEMA AS OFICINA_CLIENTE,
                ALTA AS FECHA_INICIO_OPERACION,
                FECHA_ALTA
            FROM
                SUB_CLIENTES_PERFIL
            WHERE
                FECHA_ALTA BETWEEN TO_DATE('$Inicial', 'YY-mm-dd')
                AND TO_DATE('$Final', 'YY-mm-dd')
            GROUP BY
                CDGCL,
                GRUPO,
                ULTIMO_CICLO,
                CLIENTES,
                ADICIONAL,
                A_PATERNO,
                A_MATERNO,
                ID_SUCL_SISTEMA,
                ALTA,
                FECHA_ALTA
            ORDER BY
                FECHA_ALTA DESC
        SQL;

        $db = new Database('SERVIDOR-CULTIVA');
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ConsultarPagos($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                PRN.CANTENTRE,
                PRC.CDGEM,
                PRN.CICLO,
                EF.NOMBRE AS LOCALIDAD,
                CASE
                    WHEN IB.CODIGO = 13 THEN '001' ------------------------ IMBURSA
                    WHEN IB.CODIGO = 11 THEN '002' ---------------------- PAYCASH
                    WHEN IB.CODIGO = 05 THEN '003' ----------------------- OXXO
                    WHEN IB.CODIGO = 00 THEN '001' ----------------------- ES BANORTE PERO PASA A IMBURSA
                    WHEN IB.CODIGO = 04 THEN '004' ----------------------- SON GARANTIAS
                    ELSE '000'
                END AS SUCURSAL,
                '09' AS TIPO_OPERACION,
                CL.CODIGO AS ID_CLIENTE,
                PRC.CDGNS AS NUM_CUENTA,
                '01' AS INSTRUMENTO_MONETARIO,
                'MXN' AS MONEDA,
                ROUND((MP.CANTIDAD * PRC.CANTENTRE) / PRN.CANTENTRE, 3) AS MONTO,
                to_char(MP.FDEPOSITO, 'yyyymmdd') AS FECHA_OPERACION,
                (
                    CASE
                        WHEN (CB.NOMBRE = 'OXXO' || 'PAYCASH') THEN 1
                        ELSE 4
                    END
                ) AS TIPO_RECEPTOR,
                (
                    CASE
                        WHEN (IB.NOMBRE = 'BANORTE') THEN 'INBURSA'
                        ELSE IB.NOMBRE
                    END
                ) AS CLAVE_RECEPTOR,
                '0' AS NUM_CAJA,
                '0' AS ID_CAJERO,
                to_char(MP.FDEPOSITO, 'yyyymmdd') AS FECHA_HORA,
                '036180500609569035' AS NOTARJETA_CTA,
                '4' AS TIPOTARJETA,
                '0' AS COD_AUTORIZACION,
                'NO' AS ATRASO,
                PRN.CDGCO AS OFICINA_CLIENTE,
                PRN.SITUACION,
                MP.FDEPOSITO
            FROM
                MP
                INNER JOIN PRN ON PRN.CDGNS = MP.CDGNS
                INNER JOIN PRC ON PRC.CDGNS = PRN.CDGNS
                INNER JOIN CL ON CL.CODIGO = PRC.CDGCL
                INNER JOIN EF ON CL.CDGEF = EF.CODIGO
                INNER JOIN CB ON CB.CODIGO = MP.CDGCB
                INNER JOIN IB ON IB.CODIGO = CB.CDGIB
            WHERE
                MP.CDGEM = 'EMPFIN'
                AND MP.TIPO = 'PD'
                AND MP.ESTATUS = 'B'
                AND MP.CICLO = PRC.CICLO
                AND MP.CICLO = PRN.CICLO
                AND MP.CDGNS = PRC.CDGNS
                AND MP.CDGNS = PRN.CDGNS
                AND MP.FDEPOSITO BETWEEN TO_DATE('$Inicial', 'YY-mm-dd')
                AND TO_DATE('$Final', 'YY-mm-dd')
            ORDER BY
                PRN.CICLO DESC  
        SQL;

        $db = new Database('SERVIDOR-CULTIVA');
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ConsultarPagosNacimiento($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                PRN.CANTENTRE,
                PRC.CDGEM,
                PRN.CICLO,
                EF.NOMBRE AS LOCALIDAD,
                CASE
                    WHEN IB.CODIGO = 13 THEN '001' ------------------------ IMBURSA
                    WHEN IB.CODIGO = 11 THEN '002' ---------------------- PAYCASH
                    WHEN IB.CODIGO = 05 THEN '003' ----------------------- OXXO
                    WHEN IB.CODIGO = 00 THEN '001' ----------------------- ES BANORTE PERO PASA A IMBURSA
                    WHEN IB.CODIGO = 04 THEN '004' ----------------------- SON GARANTIAS
                    ELSE '000'
                END AS SUCURSAL,
                '09' AS TIPO_OPERACION,
                CL.CODIGO AS ID_CLIENTE,
                PRC.CDGNS AS NUM_CUENTA,
                '01' AS INSTRUMENTO_MONETARIO,
                'MXN' AS MONEDA,
                ROUND((MP.CANTIDAD * PRC.CANTENTRE) / PRN.CANTENTRE, 3) AS MONTO,
                to_char(MP.FDEPOSITO, 'yyyymmdd') AS FECHA_OPERACION,
                (
                    CASE
                        WHEN (CB.NOMBRE = 'OXXO' || 'PAYCASH') THEN 1
                        ELSE 4
                    END
                ) AS TIPO_RECEPTOR,
                (
                    CASE
                        WHEN (IB.NOMBRE = 'BANORTE') THEN 'INBURSA'
                        ELSE IB.NOMBRE
                    END
                ) AS CLAVE_RECEPTOR,
                '0' AS NUM_CAJA,
                '0' AS ID_CAJERO,
                to_char(MP.FDEPOSITO, 'yyyymmdd') AS FECHA_HORA,
                '036180500609569035' AS NOTARJETA_CTA,
                '4' AS TIPOTARJETA,
                '0' AS COD_AUTORIZACION,
                'NO' AS ATRASO,
                PRN.CDGCO AS OFICINA_CLIENTE,
                PRN.SITUACION,
                MP.FDEPOSITO,
                TO_CHAR(CL.NACIMIENTO) AS FEC_NAC,
                TRUNC(
                    MONTHS_BETWEEN(
                        TO_DATE(SYSDATE, 'dd-mm-yy'),
                        CL.NACIMIENTO
                    ) / 12
                ) AS EDAD
            FROM
                MP
                INNER JOIN PRN ON PRN.CDGNS = MP.CDGNS
                INNER JOIN PRC ON PRC.CDGNS = PRN.CDGNS
                INNER JOIN CL ON CL.CODIGO = PRC.CDGCL
                INNER JOIN EF ON CL.CDGEF = EF.CODIGO
                INNER JOIN CB ON CB.CODIGO = MP.CDGCB
                INNER JOIN IB ON IB.CODIGO = CB.CDGIB
            WHERE
                MP.CDGEM = 'EMPFIN'
                AND MP.TIPO = 'PD'
                AND MP.ESTATUS = 'B'
                AND MP.CICLO = PRC.CICLO
                AND MP.CICLO = PRN.CICLO
                AND MP.CDGNS = PRC.CDGNS
                AND MP.CDGNS = PRN.CDGNS
                AND MP.FDEPOSITO BETWEEN TO_DATE('$Inicial', 'YY-mm-dd')
                AND TO_DATE('$Final', 'YY-mm-dd')
            ORDER BY
                PRN.CICLO DESC
        SQL;

        $db = new Database('SERVIDOR-CULTIVA');
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ConsultarPerfilTransaccional($Inicial, $Final)
    {
        $query = <<<SQL
            SELECT
                CDGCL, GRUPO, NOMBRE, INSTRUMENTO, 'MXN' AS TIPO_MONEDA, T_CAMBIO, MONT_PRESTAMO, PLAZO, 
                FRECUENCIA, TOTAL_PAGOS, MONTO_FIN_PAGO, ADELANTAR_PAGO, NUMERO_APORTACIONES,
                MONTO_APORTACIONES, CUOTA_PAGO, SALDO, ID_SUCURSAL_SISTEMA, ORIGEN_RECURSO,
                DESTINO_RECURSOS, FECHA_INICIO_CREDITO, FECHA_FIN, DESTINO, ORIGEN, TIPO_OPERACION,
                INST_MONETARIO, TIPO_CREDITO, PRODUCTO, PAIS_ORIGEN, PAIS_DESTINO, ALTA_CONTRATO,
                'PREC' AS TIPO_CONTRATO, '' AS TIP_DOC, '' AS LATLON, '' AS LOCALIZACION, CP
            FROM
                PERFIL_TRANSACCIONAL
            WHERE
                FECHA_ALTA BETWEEN TO_DATE('$Inicial', 'YY-mm-dd')
                AND TO_DATE('$Final', 'YY-mm-dd')
                AND ULTIMO_CICLO != 'D1'
        SQL;

        $db = new Database('SERVIDOR-CULTIVA');
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ConsultaGruposCultiva($fecha_inicial, $fecha_final)
    {
        $query = <<<SQL
            SELECT
                CO.NOMBRE AS SUCURSAL,
                SC.CDGNS,
                NS.NOMBRE as NOMBRE_GRUPO,
                TO_CHAR(
                        (
                                CL.NOMBRE1 || ' ' || CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE
                        )
                ) AS CLIENTE,
                (CL.CALLE) AS DOMICILIO,
                TO_CHAR(SC.SOLICITUD, 'DD/MM/YYYY HH24:MI:SS') AS SOLICITUD,
                SC.CICLO,
                NS.CODIGO AS CDGNS
            FROM
                SC
                INNER JOIN NS ON NS.CODIGO = SC.CDGNS
                INNER JOIN CL ON CL.CODIGO = SC.CDGCL
                INNER JOIN CO ON CO.CODIGO = NS.CDGCO
            WHERE
                SOLICITUD BETWEEN TIMESTAMP '$fecha_inicial 00:00:00.000000'
                AND TIMESTAMP '$fecha_final 11:59:00.000000'
            ORDER BY
                SC.SOLICITUD ASC
        SQL;

        $db = new Database('SERVIDOR-CULTIVA');
        if ($db->db_activa == null) return "";
        return $db->queryAll($query);
    }

    public static function ReingresarClientesCredito($credito)
    {
        $query = <<<SQL
            SELECT
                CDGNS,
                CDGCL,
                NOMBRE_CLIENTE,
                INICIO,
                FECHA_BAJA,
                FECHA_BAJA_REAL,
                CODIGO_MOTIVO,
                MOTIVO_BAJA
            FROM
                (
                    SELECT
                        CDGNS,
                        CDGCL,
                        (
                            NOMBRE1 || ' ' || NOMBRE2 || ' ' || PRIMAPE || ' ' || SEGAPE
                        ) AS NOMBRE_CLIENTE,
                        INICIO,
                        TO_CHAR(FIN, 'DD-MM-YYYY') AS FECHA_BAJA,
                        FIN AS FECHA_BAJA_REAL,
                        m.CODIGO AS CODIGO_MOTIVO,
                        UPPER(m.DESCRIPCION) AS MOTIVO_BAJA,
                        ROW_NUMBER() OVER (
                            PARTITION BY CDGCL
                            ORDER BY
                                FIN DESC
                        ) AS RN
                    FROM
                        CN c
                        INNER JOIN MS m ON m.CODIGO = c.CDGMS
                        INNER JOIN CL c2 ON c2.CODIGO = c.CDGCL
                    WHERE
                        CDGNS = '$credito'
                ) sub
            WHERE
                RN = 1
        SQL;

        $query2 = <<<SQL
            SELECT 
                NOMBRE
            FROM
                NS
            WHERE
                CODIGO = '$credito'
        SQL;

        $db = new Database('SERVIDOR-CULTIVA');
        if ($db->db_activa == null) return [];
        return [$db->queryAll($query), $db->queryOne($query2)];
    }

    public static function RegistraInicioCierreDiario($datos)
    {
        $qry = <<<SQL
            INSERT INTO BITACORA_CIERRE_DIARIO (FECHA_CALCULO, USUARIO)
            VALUES (TO_DATE(:fecha, 'YYYY-MM-DD'), :usuario)
        SQL;

        $prm = [
            'fecha' => $datos['fecha'],
            'usuario' => $datos['usuario']
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $prm);
            return self::Responde(true, "Registro correcto");
        } catch (\Exception $e) {
            return self::Responde(false, "Error en el registro", null, $e->getMessage());
        }
    }

    public static function TiempoEstimadoCierreDiario()
    {
        $qry = <<<SQL
            SELECT 
                ROUND(AVG((CAST(FIN AS DATE) - CAST(INICIO AS DATE)) * 24 * 60), 0) AS ESTIMADO
            FROM (
                SELECT
                    INICIO
                    , FIN
                FROM
                    BITACORA_CIERRE_DIARIO
                WHERE
                    FIN IS NOT NULL
                    AND INICIO IS NOT NULL
                    AND EXITO = 1
                ORDER BY
                    FIN DESC
                FETCH FIRST 7 ROWS ONLY
            )
        SQL;

        try {
            $db = new Database();
            $resultado = $db->queryOne($qry);
            return self::Responde(true, "Validación correcta", $resultado);
        } catch (\Exception $e) {
            return self::Responde(false, "Error en la validación", null, $e->getMessage());
        }
    }

    public static function ValidaCierreEnEjecucion()
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(INICIO, 'DD/MM/YYYY HH24:MI:SS') AS INICIO,
                TO_CHAR(FECHA_CALCULO, 'DD/MM/YYYY') AS FIN,
                USUARIO
            FROM
                BITACORA_CIERRE_DIARIO
            WHERE
                FIN IS NULL
        SQL;

        try {
            $db = new Database();
            $resultado = $db->queryOne($qry);
            return self::Responde(true, "Validación correcta", $resultado);
        } catch (\Exception $e) {
            return self::Responde(false, "Error en la validación", null, $e->getMessage());
        }
    }

    public static function ValidacionPreviaCierre($datos)
    {
        $qry = <<<SQL
            SELECT
                COUNT(*) AS TOTAL
            FROM
                TBL_CIERRE_DIA
            WHERE
                FECHA_CALC = TO_DATE(:fecha, 'YYYY-MM-DD')
                AND FECHA_LIQUIDA IS NULL
        SQL;

        $prm = [
            'fecha' => $datos['fecha']
        ];

        try {
            $db = new Database();
            $resultado = $db->queryOne($qry, $prm);
            return self::Responde(true, "Validación correcta", $resultado);
        } catch (\Exception $e) {
            return self::Responde(false, "Error en la validación", null, $e->getMessage());
        }
    }

    /////////////////////////////////////////////////////////////////
    static public function GetReportePC($datos)
    {
        $qry = <<<SQL
            WITH PERSONAS_BASE AS (
            SELECT DISTINCT
                SC.CDGNS,
                SC.CICLO,
                SC.CDGCL,
                CL.CODIGO AS CLAVE,
                CASE 
                    WHEN SC.CANTSOLIC = 9999 THEN 'AVAL'
                    ELSE 'CLIENTE'
                END AS TIPO,
                RTRIM(CL.NOMBRE1 || ' ' || NVL(CL.NOMBRE2, '') || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE) AS NOMBRE_COMPLETO,
                CL.TELEFONO,
                CL.CALLE,
                CL.CDGCOL,
                CL.CDGLO,
                CL.CDGMU,
                CL.CDGEF
            FROM SC
            JOIN CL ON SC.CDGCL = CL.CODIGO
        ),
        UNICOS AS (
            SELECT
                PB.CDGNS,
                PB.CICLO,
                PB.TIPO,
                PB.CDGCL,
                PB.NOMBRE_COMPLETO,
                PB.TELEFONO,
                PB.CALLE,
                PB.CDGCOL,
                PB.CDGLO,
                PB.CDGMU,
                PB.CDGEF,
                ROW_NUMBER() OVER (
                    PARTITION BY PB.CDGNS, PB.CICLO, PB.TIPO 
                    ORDER BY PB.CDGCL
                ) AS ORDEN
            FROM PERSONAS_BASE PB
        ),
        INTEGRANTES AS (
            SELECT
                U.CDGNS,
                U.CICLO,
        
                -- CLIENTE (ORDEN 1)
                MAX(CASE WHEN TIPO = 'CLIENTE' AND ORDEN = 1 THEN CDGCL END) AS CDGCL_CLIENTE,
                MAX(CASE WHEN TIPO = 'CLIENTE' AND ORDEN = 1 THEN NOMBRE_COMPLETO END) AS CLIENTE,
                MAX(CASE WHEN TIPO = 'CLIENTE' AND ORDEN = 1 THEN TELEFONO END) AS TELEFONO_CLIENTE,
                MAX(CASE WHEN TIPO = 'CLIENTE' AND ORDEN = 1 THEN CALLE END) AS CALLE_CLIENTE,
                MAX(CASE WHEN TIPO = 'CLIENTE' AND ORDEN = 1 THEN CDGCOL END) AS CDGCOL_CLIENTE,
                MAX(CASE WHEN TIPO = 'CLIENTE' AND ORDEN = 1 THEN CDGLO END) AS CDGLO_CLIENTE,
                MAX(CASE WHEN TIPO = 'CLIENTE' AND ORDEN = 1 THEN CDGMU END) AS CDGMU_CLIENTE,
                MAX(CASE WHEN TIPO = 'CLIENTE' AND ORDEN = 1 THEN CDGEF END) AS CDGEF_CLIENTE,
        
                -- AVAL 1 (ORDEN 1)
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 1 THEN CDGCL END) AS CDGCL_AVAL1,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 1 THEN NOMBRE_COMPLETO END) AS AVAL1,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 1 THEN TELEFONO END) AS TELEFONO_AVAL1,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 1 THEN CALLE END) AS CALLE_AVAL1,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 1 THEN CDGCOL END) AS CDGCOL_AVAL1,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 1 THEN CDGLO END) AS CDGLO_AVAL1,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 1 THEN CDGMU END) AS CDGMU_AVAL1,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 1 THEN CDGEF END) AS CDGEF_AVAL1,
        
                -- AVAL 2 (ORDEN 2)
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 2 THEN CDGCL END) AS CDGCL_AVAL2,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 2 THEN NOMBRE_COMPLETO END) AS AVAL2,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 2 THEN TELEFONO END) AS TELEFONO_AVAL2,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 2 THEN CALLE END) AS CALLE_AVAL2,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 2 THEN CDGCOL END) AS CDGCOL_AVAL2,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 2 THEN CDGLO END) AS CDGLO_AVAL2,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 2 THEN CDGMU END) AS CDGMU_AVAL2,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 2 THEN CDGEF END) AS CDGEF_AVAL2,
        
                -- AVAL 3 (ORDEN 3)
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 3 THEN CDGCL END) AS CDGCL_AVAL3,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 3 THEN NOMBRE_COMPLETO END) AS AVAL3,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 3 THEN TELEFONO END) AS TELEFONO_AVAL3,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 3 THEN CALLE END) AS CALLE_AVAL3,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 3 THEN CDGCOL END) AS CDGCOL_AVAL3,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 3 THEN CDGLO END) AS CDGLO_AVAL3,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 3 THEN CDGMU END) AS CDGMU_AVAL3,
                MAX(CASE WHEN TIPO = 'AVAL' AND ORDEN = 3 THEN CDGEF END) AS CDGEF_AVAL3
        
            FROM UNICOS U
            GROUP BY U.CDGNS, U.CICLO
        )
        SELECT 
            PRN.CDGNS, 
            PRN.CICLO, 
            PRN.PLAZO, 
            PRN.TASA, 
           TO_CHAR(PRN.INICIO, 'DD/MM/YYYY') AS INICIO,
           TO_CHAR(PRN.INICIO + NUMTODSINTERVAL(PRN.PLAZO * 7, 'DAY'), 'DD/MM/YYYY') AS FECHA_FIN,
            PRN.CANTENTRE,
            ABS(MP.CANTIDAD) + PRN.CANTENTRE AS TOTAL_CANTIDAD,
        
            -- Cliente
            I.CDGCL_CLIENTE,
            I.CLIENTE,
            I.TELEFONO_CLIENTE,
            RTRIM(I.CALLE_CLIENTE || ', ' || COL.NOMBRE || ', ' || LO.NOMBRE || ', ' || MU.NOMBRE || ', ' || EF.NOMBRE) AS DIRECCION_COMPLETA_CLIENTE,
        
            -- Aval 1
            I.CDGCL_AVAL1,
            I.AVAL1,
            I.TELEFONO_AVAL1,
            RTRIM(COL_AVAL1.NOMBRE || ', ' || LO_AVAL1.NOMBRE || ', ' || MU_AVAL1.NOMBRE || ', ' || EF_AVAL1.NOMBRE || ', ' || I.CALLE_AVAL1) AS DIRECCION_COMPLETA_AVAL1,
        
            -- Aval 2
            I.CDGCL_AVAL2,
            I.AVAL2,
            I.TELEFONO_AVAL2,
            RTRIM(COL_AVAL2.NOMBRE || ', ' || LO_AVAL2.NOMBRE || ', ' || MU_AVAL2.NOMBRE || ', ' || EF_AVAL2.NOMBRE || ', ' || I.CALLE_AVAL2) AS DIRECCION_COMPLETA_AVAL2,
        
            -- Aval 3
            I.CDGCL_AVAL3,
            I.AVAL3,
            I.TELEFONO_AVAL3,
            RTRIM(COL_AVAL3.NOMBRE || ', ' || LO_AVAL3.NOMBRE || ', ' || MU_AVAL3.NOMBRE || ', ' || EF_AVAL3.NOMBRE || ', ' || I.CALLE_AVAL3) AS DIRECCION_COMPLETA_AVAL3
        
        FROM PRN
        JOIN MP ON MP.CDGNS = PRN.CDGNS AND MP.CICLO = PRN.CICLO 
               AND MP.REFERENCIA = 'Interés total del préstamo'
        JOIN INTEGRANTES I ON I.CDGNS = PRN.CDGNS AND I.CICLO = PRN.CICLO
        
        -- Cliente ubicación
        LEFT JOIN COL ON COL.CODIGO = I.CDGCOL_CLIENTE
                     AND COL.CDGLO = I.CDGLO_CLIENTE
                     AND COL.CDGMU = I.CDGMU_CLIENTE
                     AND COL.CDGEF = I.CDGEF_CLIENTE
        
        LEFT JOIN LO ON LO.CODIGO = I.CDGLO_CLIENTE
                    AND LO.CDGMU = I.CDGMU_CLIENTE
                    AND LO.CDGEF = I.CDGEF_CLIENTE
        
        LEFT JOIN MU ON MU.CODIGO = I.CDGMU_CLIENTE
                    AND MU.CDGEF = I.CDGEF_CLIENTE
        
        LEFT JOIN EF ON EF.CODIGO = I.CDGEF_CLIENTE
        
        -- Aval 1 ubicación
        LEFT JOIN COL COL_AVAL1 ON COL_AVAL1.CODIGO = I.CDGCOL_AVAL1
                              AND COL_AVAL1.CDGLO = I.CDGLO_AVAL1
                              AND COL_AVAL1.CDGMU = I.CDGMU_AVAL1
                              AND COL_AVAL1.CDGEF = I.CDGEF_AVAL1
        
        LEFT JOIN LO LO_AVAL1 ON LO_AVAL1.CODIGO = I.CDGLO_AVAL1
                             AND LO_AVAL1.CDGMU = I.CDGMU_AVAL1
                             AND LO_AVAL1.CDGEF = I.CDGEF_AVAL1
        
        LEFT JOIN MU MU_AVAL1 ON MU_AVAL1.CODIGO = I.CDGMU_AVAL1
                             AND MU_AVAL1.CDGEF = I.CDGEF_AVAL1
        
        LEFT JOIN EF EF_AVAL1 ON EF_AVAL1.CODIGO = I.CDGEF_AVAL1
        
        -- Aval 2 ubicación
        LEFT JOIN COL COL_AVAL2 ON COL_AVAL2.CODIGO = I.CDGCOL_AVAL2
                              AND COL_AVAL2.CDGLO = I.CDGLO_AVAL2
                              AND COL_AVAL2.CDGMU = I.CDGMU_AVAL2
                              AND COL_AVAL2.CDGEF = I.CDGEF_AVAL2
        
        LEFT JOIN LO LO_AVAL2 ON LO_AVAL2.CODIGO = I.CDGLO_AVAL2
                             AND LO_AVAL2.CDGMU = I.CDGMU_AVAL2
                             AND LO_AVAL2.CDGEF = I.CDGEF_AVAL2
        
        LEFT JOIN MU MU_AVAL2 ON MU_AVAL2.CODIGO = I.CDGMU_AVAL2
                             AND MU_AVAL2.CDGEF = I.CDGEF_AVAL2
        
        LEFT JOIN EF EF_AVAL2 ON EF_AVAL2.CODIGO = I.CDGEF_AVAL2
        
        -- Aval 3 ubicación
        LEFT JOIN COL COL_AVAL3 ON COL_AVAL3.CODIGO = I.CDGCOL_AVAL3
                              AND COL_AVAL3.CDGLO = I.CDGLO_AVAL3
                              AND COL_AVAL3.CDGMU = I.CDGMU_AVAL3
                              AND COL_AVAL3.CDGEF = I.CDGEF_AVAL3
        
        LEFT JOIN LO LO_AVAL3 ON LO_AVAL3.CODIGO = I.CDGLO_AVAL3
                             AND LO_AVAL3.CDGMU = I.CDGMU_AVAL3
                             AND LO_AVAL3.CDGEF = I.CDGEF_AVAL3
        
        LEFT JOIN MU MU_AVAL3 ON MU_AVAL3.CODIGO = I.CDGMU_AVAL3
                             AND MU_AVAL3.CDGEF = I.CDGEF_AVAL3
        
        LEFT JOIN EF EF_AVAL3 ON EF_AVAL3.CODIGO = I.CDGEF_AVAL3


        WHERE PRN.INICIO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
            ORDER BY PRN.CDGNS, PRN.CICLO
             
        SQL;

        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF']
        ];


        try {
            $db =  new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Consulta exitosa', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al consultar el reporte', null, $e->getMessage());
        }
    }

    /**
     * Reporte Interés Devengado.
     * Parámetros:
     *  - fechaCorte: YYYY-MM-DD
     *  - situacion: 'E', 'L' o 'AMBOS'
     *
     * La consulta base se respeta; solo se parametrizan fecha de corte y situación.
     */
    public static function GetReporteInteresDevengado($datos)
    {
        $fechaCorte = isset($datos['fechaCorte']) ? $datos['fechaCorte'] : date('Y-m-d');
        $situacion = isset($datos['situacion']) ? strtoupper($datos['situacion']) : 'AMBOS';

        // Consulta base proporcionada (misma lógica, sin parámetros)
        $consultaBase = <<<SQL
WITH CREDITOS AS (
    SELECT 
        PRN.CDGNS
        ,PRN.CICLO
        ,DECODE(PRN.SITUACION, 'E', 'ENTREGADO', 'L', 'LIQUIDADO', 'OTRO') AS SITUACION
        ,PRN.INICIO AS INICIO
        ,DECODE(PRN.PERIODICIDAD, 'S', 7, 'C', 14, 'Q', 15, 'M', 30, 7) * PRN.PLAZO AS PLAZO_DIAS
        ,NVL(TCD.FECHA_LIQUIDA, TRUNC(TO_DATE(:fecha_corte, 'YYYY-MM-DD'))) - TRUNC(PRN.INICIO) AS DIAS_TRANSCURRIDOS
        ,ABS(MP.CANTIDAD) AS INTERES_TOTAL
        ,TCD.FECHA_LIQUIDA AS FECHA_LIQUIDACION
    FROM
        PRN
        JOIN MP ON PRN.CDGEM = MP.CDGEM AND PRN.CDGNS = MP.CDGCLNS AND PRN.CICLO = MP.CICLO
        LEFT JOIN TBL_CIERRE_DIA TCD ON PRN.CDGEM = TCD.CDGEM AND PRN.CDGNS = TCD.CDGCLNS AND PRN.CICLO = TCD.CICLO AND NOT TCD.FECHA_LIQUIDA  IS NULL
    WHERE
        PRN.SITUACION IN ('E', 'L')
        AND MP.TIPO = 'IN'
)
,CALCULOS AS (
    SELECT
        C.CDGNS
        ,C.CICLO
        ,TRUNC(C.INICIO + C.PLAZO_DIAS) AS FIN
        ,ROUND(C.INTERES_TOTAL / C.PLAZO_DIAS, 2) AS DEVENGO_DIARIO
        ,DECODE(C.PLAZO_DIAS, C.DIAS_TRANSCURRIDOS, C.INTERES_TOTAL, C.DIAS_TRANSCURRIDOS * ROUND(C.INTERES_TOTAL / C.PLAZO_DIAS, 2)) AS DEVENGO_TRANSCURRIDO
    FROM CREDITOS C
)
,DEVENGOS AS (
    SELECT
        DD.CDGCLNS
        ,DD.CICLO
        ,DD.INICIO
        ,COUNT(*) AS DIAS_REGISTRADOS
        ,SUM(DD.DEV_DIARIO) AS DEVENGO_REGISTRADO
    FROM
        DEVENGO_DIARIO DD
    WHERE
        DD.FECHA_CALC <= TRUNC(TO_DATE(:fecha_corte, 'YYYY-MM-DD'))
    GROUP BY
        DD.CDGCLNS
        ,DD.CICLO
        ,DD.INICIO
)
SQL;

        // Se reutilizan los CTEs anteriores y se aplica únicamente el filtro de situación
        // sin alterar la lógica interna ni los cálculos originales.
        $qry = <<<SQL
$consultaBase
SELECT
    C.CDGNS,
    C.CICLO,
    C.SITUACION,
    C.INICIO,
    CAL.FIN,
    C.PLAZO_DIAS,
    CAL.DEVENGO_DIARIO,
    C.INTERES_TOTAL,
    C.DIAS_TRANSCURRIDOS,
    CAL.DEVENGO_TRANSCURRIDO,
    D.DIAS_REGISTRADOS,
    D.DEVENGO_REGISTRADO,
    C.DIAS_TRANSCURRIDOS - NVL(D.DIAS_REGISTRADOS, 0) AS DIAS_DIF,
    CAL.DEVENGO_TRANSCURRIDO - NVL(D.DEVENGO_REGISTRADO, 0) AS DEVENGO_DIF,
    C.FECHA_LIQUIDACION
FROM
    CREDITOS C
    INNER JOIN CALCULOS CAL ON C.CDGNS = CAL.CDGNS AND C.CICLO = CAL.CICLO
    LEFT JOIN DEVENGOS D ON C.CDGNS = D.CDGCLNS AND C.CICLO = D.CICLO AND C.INICIO = D.INICIO
WHERE
    CAL.FIN >= TRUNC(TO_DATE(:fecha_corte, 'YYYY-MM-DD'))
    AND (
        :situacion = 'AMBOS'
        OR (:situacion = 'E' AND C.SITUACION = 'ENTREGADO')
        OR (:situacion = 'L' AND C.SITUACION = 'LIQUIDADO')
    )
ORDER BY
    C.INICIO
SQL;

        $prm = [
            'fecha_corte' => $fechaCorte,
            'situacion'   => $situacion,
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Consulta exitosa', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al consultar el reporte de interés devengado', null, $e->getMessage());
        }
    }
}
