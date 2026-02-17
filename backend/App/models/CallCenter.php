<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use \Core\Database;
use \Core\Model;

class CallCenter extends Model
{
    public static function getAllDescription($credito, $ciclo, $fec)
    {
        $date = str_replace('/', '-', $fec);
        $newDate = date("Y-m-d H:i:s", strtotime($date));

        $query = <<<SQL
            SELECT 
                SC.CDGNS NO_CREDITO,
                SC.CDGCL ID_CLIENTE,
                GET_NOMBRE_CLIENTE(SC.CDGCL) CLIENTE,
                CASE 
                    WHEN SN.CICLOR IS NOT NULL THEN  SN.CICLOR || '(RECHAZADO)' 
                    ELSE SN.CICLO 
                END AS CICLO,
                NVL(SC.CANTAUTOR,SC.CANTSOLIC) MONTO,
                SC.SITUACION,
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
                TO_CHAR(SN.SOLICITUD ,'YYYY-MM-DD HH24:MI:SS') AS FECHA_SOL, 
                SN.CICLOR,
                SN.CREDITO_ADICIONAL
            FROM 
                SN, SC
            WHERE
                SC.CDGNS = '$credito'
                AND SC.CICLO = '$ciclo'
                AND SC.CDGNS = SN.CDGNS
                AND SC.CICLO = SN.CICLO
                AND SC.CANTSOLIC <> '9999'  
        SQL;

        $mysqli = new Database();
        $credito_ = $mysqli->queryOne($query);

        $id_cliente = $credito_['ID_CLIENTE'];
        $id_proyecto = $credito_['ID_PROYECTO'];

        $query2 = <<<SQL
            SELECT
                CONCATENA_NOMBRE(CL.NOMBRE1,CL.NOMBRE2,CL.PRIMAPE,CL.SEGAPE) NOMBRE,
                CL.NACIMIENTO,
                TRUNC(MONTHS_BETWEEN(SYSDATE, CL.NACIMIENTO) / 12) EDAD,
                CL.SEXO,
                EDO_CIVIL(CL.EDOCIVIL) EDO_CIVIL,
                CL.TELEFONO,
                EF.NOMBRE ESTADO,
                UPPER(MU.NOMBRE) MUNICIPIO,
                LO.NOMBRE LOCALIDAD,
                COL.NOMBRE COLONIA,
                COL.CDGPOSTAL CP,
                CL.CALLE, PI.NOMBRE ACT_ECO, 
                CL.CLABE
            FROM
                CL,
                EF,
                MU,
                LO,
                COL, 
                PI
            WHERE
                CL.CODIGO = '$id_cliente'
                AND EF.CODIGO = CL.CDGEF
                AND MU.CODIGO = CL.CDGMU
                AND LO.CODIGO = CL.CDGLO 
                AND COL.CODIGO = CL.CDGCOL
                AND EF.CODIGO = MU.CDGEF 
                AND EF.CODIGO = LO.CDGEF
                AND EF.CODIGO = COL.CDGEF
                AND MU.CODIGO = LO.CDGMU 
                AND MU.CODIGO = COL.CDGMU 
                AND LO.CODIGO = COL.CDGLO
                AND PI.CDGCL = CL.CODIGO 
                AND PI.PROYECTO = '$id_proyecto'
                ORDER BY PI.ACTUALIZA DESC
        SQL;

        $cliente = $mysqli->queryOne($query2);

        $res_recomendado = '';
        $id_cliente_recomendado = $cliente['CLABE'];
        if ($id_cliente_recomendado != '') {
            $query_recomendado = <<<SQL
                SELECT
                    CONCATENA_NOMBRE(CL.NOMBRE1,CL.NOMBRE2,CL.PRIMAPE,CL.SEGAPE) NOMBRE,
                    CL.NACIMIENTO,
                    TRUNC(MONTHS_BETWEEN(SYSDATE, CL.NACIMIENTO) / 12) EDAD,
                    CL.SEXO,
                    EDO_CIVIL(CL.EDOCIVIL) EDO_CIVIL,
                    CL.TELEFONO,
                    EF.NOMBRE ESTADO,
                    UPPER(MU.NOMBRE) MUNICIPIO,
                    LO.NOMBRE LOCALIDAD,
                    COL.NOMBRE COLONIA,
                    COL.CDGPOSTAL CP,
                    CL.CALLE, 
                    PI.NOMBRE ACT_ECO
                FROM
                    CL,
                    EF,
                    MU,
                    LO,
                    COL, 
                    PI
                WHERE
                    CL.CODIGO = '$id_cliente_recomendado'
                    AND EF.CODIGO = CL.CDGEF
                    AND MU.CODIGO = CL.CDGMU
                    AND LO.CODIGO = CL.CDGLO 
                    AND COL.CODIGO = CL.CDGCOL
                    AND EF.CODIGO = MU.CDGEF 
                    AND EF.CODIGO = LO.CDGEF
                    AND EF.CODIGO = COL.CDGEF
                    AND MU.CODIGO = LO.CDGMU 
                    AND MU.CODIGO = COL.CDGMU 
                    AND LO.CODIGO = COL.CDGLO
                    AND PI.CDGCL = CL.CODIGO 
                    ORDER BY PI.ACTUALIZA DESC
            SQL;

            $res_recomendado = $mysqli->queryOne($query_recomendado);
        }

        $query3 = <<<SQL
            WITH SCC AS (
                SELECT
                    SCC.*
                FROM
                    SOL_CALL_CENTER SCC
                WHERE
                    SCC.CDGNS = '$credito'
                    AND SCC.CICLO = '$ciclo'
                    AND SCC.CDGCL_CL = '$id_cliente'
                    AND FECHA_SOL = TIMESTAMP '$newDate.000'
                ORDER BY
                    SCC.FECHA_TRA_CL
                FETCH FIRST
                    1 ROW ONLY
            )
            SELECT
                CL.CODIGO,
                CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE) NOMBRE,
                TO_CHAR(CL.NACIMIENTO, 'YYYY-MM-DD') NACIMIENTO,
                TRUNC(MONTHS_BETWEEN(SYSDATE, CL.NACIMIENTO) / 12) EDAD,
                CL.SEXO,
                EDO_CIVIL(CL.EDOCIVIL) EDO_CIVIL,
                CL.TELEFONO,
                EF.NOMBRE ESTADO,
                UPPER(MU.NOMBRE) MUNICIPIO,
                LO.NOMBRE LOCALIDAD,
                COL.NOMBRE COLONIA,
                COL.CDGPOSTAL CP,
                CL.CALLE,
                (SELECT PI.NOMBRE FROM PI WHERE PI.CDGCL = CL.CODIGO AND PI.ACTUALIZA = (SELECT MAX(PI2.ACTUALIZA) FROM PI PI2 WHERE PI2.CDGCL = CL.CODIGO) ORDER BY PI.ACTUALIZA DESC FETCH FIRST 1 ROW ONLY) ACT_ECO,
                NVL(
                    (
                        SELECT
                            CASE
                                WHEN SCC.CDGCL_AV = CL.CODIGO THEN SCC.NUMERO_INTENTOS_AV
                                WHEN SCC.CDGCL_AV_2 = CL.CODIGO THEN SCC.NUMERO_INTENTOS_AV_2
                                ELSE '0'
                            END
                        FROM
                            SCC
                    ),
                    '0'
                ) INTENTOS,
                NVL(
                    (
                        SELECT
                            CASE
                                WHEN SCC.CDGCL_AV = CL.CODIGO THEN SCC.FIN_AV
                                WHEN SCC.CDGCL_AV_2 = CL.CODIGO THEN SCC.FIN_AV_2
                                ELSE '0'
                            END
                        FROM
                            SCC
                    ),
                    '0'
                ) FINALIZADA,
                (
                    SELECT
                        CASE
                            WHEN SCC.CDGCL_AV = CL.CODIGO
                            AND SCC.DIA_LLAMADA_1_AV IS NOT NULL THEN TO_CHAR(SCC.DIA_LLAMADA_1_AV, 'DD/MM/YYYY HH24:MI:SS') || ' (' || SCC.TIPO_LLAM_1_AV || ')'
                            WHEN SCC.CDGCL_AV_2 = CL.CODIGO
                            AND SCC.DIA_LLAMADA_1_AV_2 IS NOT NULL THEN TO_CHAR(SCC.DIA_LLAMADA_1_AV_2, 'DD/MM/YYYY HH24:MI:SS') || ' (' || SCC.TIPO_LLAM_1_AV_2 || ')'
                            ELSE '-'
                        END
                    FROM
                        SCC
                ) LLAMADA_1,
                (
                    SELECT
                        CASE
                            WHEN SCC.CDGCL_AV = CL.CODIGO
                            AND SCC.DIA_LLAMADA_2_AV IS NOT NULL THEN TO_CHAR(SCC.DIA_LLAMADA_2_AV, 'DD/MM/YYYY HH24:MI:SS') || ' (' || SCC.TIPO_LLAM_2_AV || ')'
                            WHEN SCC.CDGCL_AV_2 = CL.CODIGO
                            AND SCC.DIA_LLAMADA_2_AV_2 IS NOT NULL THEN TO_CHAR(SCC.DIA_LLAMADA_2_AV_2, 'DD/MM/YYYY HH24:MI:SS') || ' (' || SCC.TIPO_LLAM_2_AV_2 || ')'
                            ELSE '-'
                        END
                    FROM
                        SCC
                ) LLAMADA_2
            FROM
                CL
                LEFT JOIN EF ON EF.CODIGO = CL.CDGEF
                LEFT JOIN MU ON MU.CODIGO = CL.CDGMU AND EF.CODIGO = MU.CDGEF
                LEFT JOIN LO ON LO.CODIGO = CL.CDGLO AND EF.CODIGO = LO.CDGEF AND MU.CODIGO = LO.CDGMU
                LEFT JOIN COL ON COL.CODIGO = CL.CDGCOL AND EF.CODIGO = COL.CDGEF AND MU.CODIGO = COL.CDGMU AND LO.CODIGO = COL.CDGLO
            WHERE
                CL.CODIGO IN (SELECT SC.CDGCL FROM SC WHERE SC.CDGNS = '$credito' AND SC.CICLO = '$ciclo' AND SC.CANTSOLIC = '9999')
        SQL;

        $ciclo_actualizado =  str_starts_with($ciclo, 'R') ? $credito_['CICLOR'] : $ciclo;

        $desbloqueo_cl = <<<SQL
            SELECT COUNT(ID_SCALL) as LLAMADA_UNO, 
                    CASE WHEN (DIA_LLAMADA_1_CL IS NOT NULL)  THEN (DIA_LLAMADA_1_CL ||' '|| TO_CHAR(HORA_LLAMADA_1_CL ,'HH24:MI:SS') || ' (' || TIPO_LLAM_1_CL ||')' ) ELSE '-' END AS HORA_LLAMADA_UNO, 
                    CASE WHEN (DIA_LLAMADA_2_CL IS NOT NULL)  THEN (DIA_LLAMADA_2_CL ||' '|| TO_CHAR(HORA_LLAMADA_2_CL ,'HH24:MI:SS') || ' (' || TIPO_LLAM_2_CL ||')' )  ELSE '-' END AS HORA_LLAMADA_DOS, 
                    
            NUMERO_INTENTOS_CL, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS, VOBO_GERENTE_REGIONAL,
            FIN_CL AS FINALIZADA, COMENTARIO_PRORROGA, PRORROGA, REACTIVACION 
            FROM SOL_CALL_CENTER 
            WHERE CICLO ='$ciclo_actualizado' AND CDGCL_CL = '$id_cliente'  AND (FECHA_SOL = TIMESTAMP '$newDate.000')
            GROUP BY ID_SCALL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, PRG_UNO_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, NUMERO_INTENTOS_CL, COMENTARIO_INICIAL, COMENTARIO_FINAL, FIN_CL, COMENTARIO_PRORROGA, PRORROGA, REACTIVACION, ESTATUS, VOBO_GERENTE_REGIONAL, TIPO_LLAM_1_CL, TIPO_LLAM_2_CL          
        SQL;

        $desbloqueo_aval = <<<SQL
                select COUNT(ID_SCALL) as LLAMADA_UNO, 
                       DIA_LLAMADA_1_AV AS NUM_LLAM, 
                       CASE WHEN (DIA_LLAMADA_1_AV IS NOT NULL)  THEN (DIA_LLAMADA_1_AV ||' '|| TO_CHAR(HORA_LLAMADA_1_AV ,'HH24:MI:SS') || ' (' || TIPO_LLAM_1_AV ||')' ) ELSE '-' END AS HORA_LLAMADA_UNO, 
                       CASE WHEN (DIA_LLAMADA_2_AV IS NOT NULL)  THEN (DIA_LLAMADA_2_AV ||' '|| TO_CHAR(HORA_LLAMADA_2_AV ,'HH24:MI:SS') || ' (' || TIPO_LLAM_2_AV ||')' )  ELSE '-' END AS HORA_LLAMADA_DOS, 
                       
                       
                       PRG_UNO_AV, NUMERO_INTENTOS_AV, FIN_AV AS FINALIZADA
                from SOL_CALL_CENTER 
                WHERE CICLO ='$ciclo' AND CDGCL_CL = '$id_cliente' AND (CICLO != 'R1')  AND (FECHA_SOL = TIMESTAMP '$newDate.000')
                GROUP BY ID_SCALL, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, PRG_UNO_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, NUMERO_INTENTOS_AV, TIPO_LLAM_1_AV, TIPO_LLAM_2_AV,
                FIN_AV
            SQL;



        $llamada_cl = $mysqli->queryOne($desbloqueo_cl);
        $llamada_av = $mysqli->queryOne($desbloqueo_aval);
        $cliente = $mysqli->queryOne($query2);
        $aval = $mysqli->queryAll($query3);

        return [$credito_, $cliente, $aval, $llamada_cl, $llamada_av, $res_recomendado];
    }

    public static function getComboSucursales($CDGPE)
    {
        $qry = <<<SQL
           SELECT CO.CODIGO, CO.NOMBRE  FROM ASIGNACION_SUC_A
           INNER JOIN CO ON CO.CODIGO = ASIGNACION_SUC_A.CDGCO 
           WHERE ASIGNACION_SUC_A.CDGPE = :cdgpe
           AND CO.CODIGO = ASIGNACION_SUC_A.CDGCO
        SQL;

        $prm = [
            'cdgpe' => $CDGPE
        ];

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Sucursales obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener sucursales', null, $e->getMessage());
        }
    }

    public static function getComboSucursalesAllCDGCO($datos)
    {
        $qry = <<<SQL
            SELECT
                LISTAGG('''' || CDGCO || '''', ', ') WITHIN GROUP (
                    ORDER BY
                        CDGCO
                ) AS SUCURSALES
            FROM
                ASIGNACION_SUC_A
            WHERE
                (:sucursal IS NULL
                OR CDGCO = :sucursal)
        SQL;

        $prm = [
            'sucursal' => ($datos['Suc'] == '000' || $datos['Suc'] == '') ? null : $datos['Suc']
        ];

        if (isset($datos['Usuario']) && $datos['Usuario'] != '') {
            $qry .= ' AND CDGPE = :usuario';
            $prm['usuario'] = $datos['Usuario'];
        }

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $prm);
            return self::Responde(true, 'Sucursales obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener sucursales', null, $e->getMessage());
        }
    }

    public static function getComboSucursalesGlobales()
    {

        $mysqli = new Database();
        $query = <<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM CO
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getComboSucursalesHorario()
    {

        $mysqli = new Database();
        $query = <<<sql
           SELECT CO.CODIGO, CO.NOMBRE  FROM CO
           WHERE NOT EXISTS(SELECT CDGCO FROM CIERRE_HORARIO WHERE CIERRE_HORARIO.CDGCO = CO.CODIGO)
		    
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudesHistorico($fecha_inicio, $fecha_fin, $cdgco, $cdgpe, $perfil, $suc)
    {

        if ($perfil == 'ADMIN' || $perfil == 'ACALL' || $cdgpe == 'ESMM') {
            $mysqli = new Database();
            $query = <<<sql
                SELECT * FROM (SELECT DISTINCT ID_SCALL, CDGNS, CASE
                WHEN SPR.CICLOR IS NOT NULL THEN SPR.CICLOR
                ELSE SPR.CICLO
                END AS CICLO, FECHA_SOL, INICIO, SPR.CDGCO, CDGCL, NOMBRE, NOMBRE_SUCURSAL, CODIGO_SUCURSAL, REGION, CODIGO_REGION, EJECUTIVO, ID_EJECUTIVO, FECHA, CDGPE, PE.NOMBRE1, PRIMAPE, SEGAPE,  ESTATUS_CL, ESTATUS_AV, SOLICITUD, FECHA_TRABAJO, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS_FINAL, VOBO_REG, SEMAFORO, PRORROGA, REACTIVACION, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, TIPO_LLAM_2_CL,DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL,PRG_DOS_CL,PRG_TRES_CL, PRG_CUATRO_CL, '' AS PRG_CINCO_CL, '' AS PRG_SEIS_CL, '' AS PRG_SIETE_CL, '' AS PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL,  PRG_DOCE_CL, CDGCL_AV,  TEL_AV,  FECHA_TRABAJO_AV, TIPO_LLAM_1_AV,  DIA_LLAMADA_1_AV,  HORA_LLAMADA_1_AV, TIPO_LLAM_2_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV,PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV,  PRG_OCHO_AV, PRG_NUEVE_AV, CDGPE_ANALISTA,LLAMADA_POST_VENTA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL,FIN_AV, COMENTARIO_PRORROGA, RECOMENDADO, CICLOR
                FROM SOLICITUDES_PROCESADAS SPR
                INNER JOIN PE ON PE.CODIGO = SPR.CDGPE
                WHERE TO_DATE(SPR.FECHA_SOL, 'DD/MM/YYYY HH24:MI:SS') BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000'
                AND SEMAFORO = '1'
                
                UNION
                
                SELECT DISTINCT ID_SCALL, CDGNS, 
                CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN 'CANCELADA ADMINISTRADORA' ELSE 'NO PROCESADA AÚN | CICLO:' || CICLO END AS CICLO,
            
                FECHA_SOL, INICIO, CDGCO, CDGCL, NOMBRE, NOMBRE_SUCURSAL, CODIGO_SUCURSAL, REGION, CODIGO_REGION, EJECUTIVO, ID_EJECUTIVO, FECHA, CDGPE, 'PENDIENTE DE VALIDAR' AS NOMBRE1, '' AS PRIMAPE, '' AS SEGAPE,  ESTATUS_CL, ESTATUS_AV, SOLICITUD, FECHA_TRABAJO, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS_FINAL, VOBO_REG, SEMAFORO, PRORROGA, REACTIVACION, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, TIPO_LLAM_2_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, TIPO_LLAM_2_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, CDGPE_ANALISTA, LLAMADA_POST_VENTA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL, FIN_AV, COMENTARIO_PRORROGA, RECOMENDADO, CICLOR
                FROM SOLICITUDES_PENDIENTES SP
                                WHERE SP.FECHA_SOL BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000')
            ORDER BY FECHA_SOL DESC
            sql;

            return $mysqli->queryAll($query);
        }

        if ($suc == '000') {
            $string_from_array = implode(', ', $cdgco);
            $con_array_cdgco = '';
        } else {
            $string_from_array = $suc;
            $con_array_cdgco = 'AND SPR.CDGCO IN(' . $string_from_array . ')';
        }

        //var_dump($string_from_array);
        if ($string_from_array != '') {
            $mysqli = new Database();
            $query = <<<sql
                 SELECT * FROM (SELECT DISTINCT ID_SCALL, CDGNS, CASE
                    WHEN SPR.CICLOR IS NOT NULL THEN SPR.CICLOR
                    ELSE SPR.CICLO
                 END AS CICLO, FECHA_SOL, INICIO, SPR.CDGCO, CDGCL, NOMBRE, NOMBRE_SUCURSAL, CODIGO_SUCURSAL, REGION, CODIGO_REGION, EJECUTIVO, ID_EJECUTIVO, FECHA, CDGPE, PE.NOMBRE1, PRIMAPE, SEGAPE,  ESTATUS_CL, ESTATUS_AV, SOLICITUD, FECHA_TRABAJO, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS_FINAL, VOBO_REG, SEMAFORO, PRORROGA, REACTIVACION, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, TIPO_LLAM_2_CL,DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL,PRG_DOS_CL,PRG_TRES_CL, PRG_CUATRO_CL, '' AS PRG_CINCO_CL, '' AS PRG_SEIS_CL, '' AS PRG_SIETE_CL, '' AS PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL,  PRG_DOCE_CL, CDGCL_AV,  TEL_AV,  FECHA_TRABAJO_AV, TIPO_LLAM_1_AV,  DIA_LLAMADA_1_AV,  HORA_LLAMADA_1_AV, TIPO_LLAM_2_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV,PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV,  PRG_OCHO_AV, PRG_NUEVE_AV, CDGPE_ANALISTA,LLAMADA_POST_VENTA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL,FIN_AV, COMENTARIO_PRORROGA, CICLOR
                 FROM SOLICITUDES_PROCESADAS SPR
                 INNER JOIN PE ON PE.CODIGO = SPR.CDGPE
                 WHERE SPR.CDGPE = '$cdgpe' AND TO_DATE(SPR.FECHA_SOL, 'DD/MM/YYYY HH24:MI:SS') BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000'
                 AND SEMAFORO = '1' $con_array_cdgco
                 
                 UNION
                 
                 SELECT DISTINCT ID_SCALL, CDGNS, 
                 CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN 'CANCELADA POR LA ADMINISTRADORA' ELSE 'NO PROCESADA AÚN | CICLO:' || CICLO END AS CICLO,
                 FECHA_SOL, INICIO, CDGCO, CDGCL, NOMBRE, NOMBRE_SUCURSAL, CODIGO_SUCURSAL, REGION, CODIGO_REGION, EJECUTIVO, ID_EJECUTIVO, FECHA, CDGPE, 
                 CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN '-' ELSE 'PENDIENTE DE VALIDAR' END AS NOMBRE1,
                 
                 '' AS PRIMAPE, '' AS SEGAPE, 
                 CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN '-' ELSE ESTATUS_CL END AS ESTATUS_CL,
                 CASE WHEN (CICLO = 'R1' OR CICLO = 'R2' OR CICLO = 'R3' OR CICLO = 'R4' OR CICLO = 'R5')  THEN '-' ELSE ESTATUS_AV END AS ESTATUS_AV, 
                
                 SOLICITUD, FECHA_TRABAJO, COMENTARIO_INICIAL, COMENTARIO_FINAL, ESTATUS_FINAL, VOBO_REG, SEMAFORO, PRORROGA, REACTIVACION, TEL_CL, TIPO_LLAM_1_CL, DIA_LLAMADA_1_CL, HORA_LLAMADA_1_CL, TIPO_LLAM_2_CL, DIA_LLAMADA_2_CL, HORA_LLAMADA_2_CL, PRG_UNO_CL, PRG_DOS_CL, PRG_TRES_CL, PRG_CUATRO_CL, PRG_CINCO_CL, PRG_SEIS_CL, PRG_SIETE_CL, PRG_OCHO_CL, PRG_NUEVE_CL, PRG_DIEZ_CL, PRG_ONCE_CL, PRG_DOCE_CL, CDGCL_AV, TEL_AV, FECHA_TRABAJO_AV, TIPO_LLAM_1_AV, DIA_LLAMADA_1_AV, HORA_LLAMADA_1_AV, TIPO_LLAM_2_AV, DIA_LLAMADA_2_AV, HORA_LLAMADA_2_AV, PRG_UNO_AV, PRG_DOS_AV, PRG_TRES_AV, PRG_CUATRO_AV, PRG_CINCO_AV, PRG_SEIS_AV, PRG_SIETE_AV, PRG_OCHO_AV, PRG_NUEVE_AV, CDGPE_ANALISTA, LLAMADA_POST_VENTA, CDGPE_ANALISTA_INICIAL, NUMERO_INTENTOS_CL, NUMERO_INTENTOS_AV, FIN_CL, FIN_AV, COMENTARIO_PRORROGA, CICLOR
                 FROM SOLICITUDES_PENDIENTES SP
                 WHERE SP.CODIGO_SUCURSAL IN($string_from_array) AND SP.FECHA_SOL BETWEEN TIMESTAMP '$fecha_inicio 00:00:00.000000' AND TIMESTAMP '$fecha_fin 23:59:59.000000')
                

sql;
            //var_dump($query);
            return $mysqli->queryAll($query);
        }
    }

    /////////////////////////////////////////////

    public static function getAllSolicitudesHistoricoExcel($datos)
    {
        $sucursales = $datos['sucursales'] == '' ? 'SPR.CDGCO IS NULL' : "SPR.CDGCO IN ({$datos['sucursales']})";
        $fecha = $datos['fechaI'] != '' && $datos['fechaF'] != '' ? "AND TO_DATE(SPR.FECHA_SOL, 'DD/MM/YYYY HH24:MI:SS') BETWEEN TIMESTAMP '{$datos['fechaI']} 00:00:00.000000' AND TIMESTAMP '{$datos['fechaF']} 23:59:59.000000'" : '';
        $usuario = $datos['usuario'] == '' ? '' : "AND SPR.CDGPE = '{$datos['usuario']}'";

        $qry = <<<SQL
            SELECT
                *
            FROM (
                SELECT DISTINCT
                    (SPR.CDGNS || '-' || 
                      CASE 
                          WHEN SPR.CICLOR IS NOT NULL THEN SPR.CICLOR
                          ELSE SPR.CICLO
                      END
                    ) AS A,
                    SPR.REGION AS B,
                    TO_CHAR(SPR.FECHA_TRABAJO, 'DD/MM/YYYY') AS C,
                    SPR.FECHA_SOL AS D,
                    CASE
                        WHEN SPR.CICLOR IS NOT NULL THEN 'RECHAZADO'
                        ELSE 'ACEPTADO'
                    END AS E,
                    SPR.NOMBRE_SUCURSAL AS F,
                    SPR.EJECUTIVO AS G,
                    SPR.CDGNS AS H,
                    (SPR.NOMBRE) AS I,
                    CASE
                        WHEN SPR.CICLOR IS NOT NULL THEN 'RECHAZADO ' || SPR.CICLOR
                        ELSE SPR.CICLO
                    END AS J,
                    SPR.TEL_CL AS K,
                    SPR.TIPO_LLAM_1_CL AS L,
                    CASE
                        WHEN SPR.PRG_UNO_CL IS NULL THEN '- *'
                        ELSE SPR.PRG_UNO_CL
                    END AS M,
                    CASE
                        WHEN SPR.PRG_DOS_CL IS NULL THEN '- *'
                        ELSE SPR.PRG_DOS_CL
                    END AS N,
                    CASE
                        WHEN SPR.PRG_TRES_CL IS NULL THEN '-'
                        ELSE SPR.PRG_TRES_CL
                    END AS O,
                    CASE
                        WHEN SPR.PRG_CUATRO_CL IS NULL THEN '-'
                        ELSE SPR.PRG_CUATRO_CL
                    END AS P,
                    CASE
                        WHEN SPR.PRG_CINCO_CL IS NULL THEN '-'
                        ELSE SPR.PRG_CINCO_CL
                    END AS Q,
                    CASE
                        WHEN SPR.PRG_SEIS_CL IS NULL THEN '-'
                        ELSE SPR.PRG_SEIS_CL
                    END AS R,
                    CASE
                        WHEN SPR.PRG_SIETE_CL IS NULL THEN '-'
                        ELSE SPR.PRG_SIETE_CL
                    END AS S,
                    CASE
                        WHEN SPR.PRG_OCHO_CL IS NULL THEN '-'
                        ELSE SPR.PRG_OCHO_CL
                    END AS T,
                    CASE
                        WHEN SPR.PRG_NUEVE_CL IS NULL THEN '-'
                        ELSE SPR.PRG_NUEVE_CL
                    END AS U,
                    CASE
                        WHEN SPR.PRG_DIEZ_CL IS NULL THEN '-'
                        ELSE SPR.PRG_DIEZ_CL
                    END AS V,
                    CASE
                        WHEN SPR.PRG_ONCE_CL IS NULL THEN '-'
                        ELSE SPR.PRG_ONCE_CL
                    END AS W,
                    CASE
                        WHEN SPR.PRG_DOCE_CL IS NULL THEN '-'
                        ELSE SPR.PRG_DOCE_CL
                    END AS X,
                    GET_NOMBRE_CLIENTE(SPR.CDGCL_AV) AS Y,
                    SPR.TEL_AV AS Z,
                    SPR.TIPO_LLAM_1_AV AS AA,
                    CASE
                        WHEN SPR.PRG_UNO_AV IS NULL THEN '-'
                        ELSE SPR.PRG_UNO_AV
                    END AS AB,
                    CASE
                        WHEN SPR.PRG_TRES_AV IS NULL THEN '-'
                        ELSE SPR.PRG_TRES_AV
                    END AS AC,
                    CASE
                        WHEN SPR.PRG_TRES_AV IS NULL THEN '-'
                        ELSE SPR.PRG_TRES_AV
                    END AS AD,
                    CASE
                        WHEN SPR.PRG_CUATRO_AV IS NULL THEN '-'
                        ELSE SPR.PRG_CUATRO_AV
                    END AS AE,
                    CASE
                        WHEN SPR.PRG_CINCO_AV IS NULL THEN '-'
                        ELSE SPR.PRG_CINCO_AV
                    END AS AF,
                    CASE
                        WHEN SPR.PRG_SEIS_AV IS NULL THEN '-'
                        ELSE SPR.PRG_SEIS_AV
                    END AS AG,
                    CASE
                        WHEN SPR.PRG_SIETE_AV IS NULL THEN '-'
                        ELSE SPR.PRG_SIETE_AV
                    END AS AH,
                    CASE
                        WHEN SPR.PRG_OCHO_AV IS NULL THEN '-'
                        ELSE SPR.PRG_OCHO_AV
                    END AS AI,
                    CASE
                        WHEN SPR.PRG_NUEVE_AV IS NULL THEN '-'
                        ELSE SPR.PRG_NUEVE_AV
                    END AS AJ,
                    TO_CHAR(SPR.DIA_LLAMADA_1_CL, 'DD/MM/YYYY HH24:MI:SS') AS AK,
                    TO_CHAR(SPR.DIA_LLAMADA_2_CL, 'DD/MM/YYYY HH24:MI:SS') AS AL,
                    TO_CHAR(SPR.DIA_LLAMADA_1_AV, 'DD/MM/YYYY HH24:MI:SS') AS AM,
                    TO_CHAR(SPR.DIA_LLAMADA_2_AV, 'DD/MM/YYYY HH24:MI:SS') AS AN,
                    SPR.COMENTARIO_INICIAL AS AO,
                    SPR.COMENTARIO_FINAL AS AP,
                    SPR.ESTATUS_FINAL AS AQ,
                    CASE
                        WHEN SPR.COMENTARIO_PRORROGA IS NULL THEN 'N'
                        ELSE 'S'
                    END AS AR,
                    SPR.VOBO_REG AS AS_,
                    PE.NOMBRE1 || ' ' || PE.NOMBRE2 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE AS AT_,
                    SPR.SEMAFORO AS AU,
                    '' AS AV,
                    '' AS AW,
                    '' AS AX,
                    '' AS AY,
                    '' AS AZ,
                    '' AS BA,
                    '' AS BB,
                    '' AS BC,
                    '' AS BD
                FROM 
                    SOLICITUDES_PROCESADAS SPR
                    INNER JOIN PE ON PE.CODIGO = SPR.CDGPE 
                WHERE
                    $sucursales
                    $fecha
                    $usuario
                    AND SEMAFORO = '1'
                 
                UNION
                 
                SELECT DISTINCT
                    (SPR.CDGNS || '-' || CASE
                            WHEN (
                                CICLO = 'R1'
                                OR CICLO = 'R2'
                                OR CICLO = 'R3'
                                OR CICLO = 'R4'
                                OR CICLO = 'R5'
                            ) THEN 'CANCELADA ADMINISTRADORA'
                            ELSE 'NO PROCESADA AÚN | CICLO:' || CICLO
                    END) AS A,
                    SPR.REGION AS B,
                    TO_CHAR(SPR.FECHA_TRABAJO, 'DD/MM/YYYY') AS C,
                    SPR.FECHA_SOL AS D,
                    '' AS E,
                    SPR.NOMBRE_SUCURSAL AS F,
                    SPR.EJECUTIVO AS G,
                    SPR.CDGNS AS H,
                    (SPR.NOMBRE) AS I,
                    CICLO AS J,
                    SPR.TEL_CL AS K,
                    SPR.TIPO_LLAM_1_CL AS L,
                    CASE
                        WHEN SPR.PRG_UNO_CL IS NULL THEN '- *'
                        ELSE SPR.PRG_UNO_CL
                    END AS M,
                    CASE
                        WHEN SPR.PRG_DOS_CL IS NULL THEN '- *'
                        ELSE SPR.PRG_DOS_CL
                    END AS N,
                    CASE
                        WHEN SPR.PRG_TRES_CL IS NULL THEN '-'
                        ELSE SPR.PRG_TRES_CL
                    END AS O,
                    CASE
                        WHEN SPR.PRG_CUATRO_CL IS NULL THEN '-'
                        ELSE SPR.PRG_CUATRO_CL
                    END AS P,
                    CASE
                        WHEN SPR.PRG_CINCO_CL IS NULL THEN '-'
                        ELSE SPR.PRG_CINCO_CL
                    END AS Q,
                    CASE
                        WHEN SPR.PRG_SEIS_CL IS NULL THEN '-'
                        ELSE SPR.PRG_SEIS_CL
                    END AS R,
                    CASE
                        WHEN SPR.PRG_SIETE_CL IS NULL THEN '-'
                        ELSE SPR.PRG_SIETE_CL
                    END AS S,
                    CASE
                        WHEN SPR.PRG_OCHO_CL IS NULL THEN '-'
                        ELSE SPR.PRG_OCHO_CL
                    END AS T,
                    CASE
                        WHEN SPR.PRG_NUEVE_CL IS NULL THEN '-'
                        ELSE SPR.PRG_NUEVE_CL
                    END AS U,
                    CASE
                        WHEN SPR.PRG_DIEZ_CL IS NULL THEN '-'
                        ELSE SPR.PRG_DIEZ_CL
                    END AS V,
                    CASE
                        WHEN SPR.PRG_ONCE_CL IS NULL THEN '-'
                        ELSE SPR.PRG_ONCE_CL
                    END AS W,
                    CASE
                        WHEN SPR.PRG_DOCE_CL IS NULL THEN '-'
                        ELSE SPR.PRG_DOCE_CL
                    END AS X,
                    GET_NOMBRE_CLIENTE(SPR.CDGCL_AV) AS Y,
                    SPR.TEL_AV AS Z,
                    SPR.TIPO_LLAM_1_AV AS AA,
                    CASE
                        WHEN SPR.PRG_UNO_AV IS NULL THEN '-'
                        ELSE SPR.PRG_UNO_AV
                    END AS AB,
                    CASE
                        WHEN SPR.PRG_TRES_AV IS NULL THEN '-'
                        ELSE SPR.PRG_TRES_AV
                    END AS AC,
                    CASE
                        WHEN SPR.PRG_TRES_AV IS NULL THEN '-'
                        ELSE SPR.PRG_TRES_AV
                    END AS AD,
                    CASE
                        WHEN SPR.PRG_CUATRO_AV IS NULL THEN '-'
                        ELSE SPR.PRG_CUATRO_AV
                    END AS AE,
                    CASE
                        WHEN SPR.PRG_CINCO_AV IS NULL THEN '-'
                        ELSE SPR.PRG_CINCO_AV
                    END AS AF,
                    CASE
                        WHEN SPR.PRG_SEIS_AV IS NULL THEN '-'
                        ELSE SPR.PRG_SEIS_AV
                    END AS AG,
                    CASE
                        WHEN SPR.PRG_SIETE_AV IS NULL THEN '-'
                        ELSE SPR.PRG_SIETE_AV
                    END AS AH,
                    CASE
                        WHEN SPR.PRG_OCHO_AV IS NULL THEN '-'
                        ELSE SPR.PRG_OCHO_AV
                    END AS AI,
                    CASE
                        WHEN SPR.PRG_NUEVE_AV IS NULL THEN '-'
                        ELSE SPR.PRG_NUEVE_AV
                    END AS AJ,
                    TO_CHAR(SPR.DIA_LLAMADA_1_CL, 'DD/MM/YYYY HH24:MI:SS') AS AK,
                    TO_CHAR(SPR.DIA_LLAMADA_2_CL, 'DD/MM/YYYY HH24:MI:SS') AS AL,
                    TO_CHAR(SPR.DIA_LLAMADA_1_AV, 'DD/MM/YYYY HH24:MI:SS') AS AM,
                    TO_CHAR(SPR.DIA_LLAMADA_2_AV, 'DD/MM/YYYY HH24:MI:SS') AS AN,
                    SPR.COMENTARIO_INICIAL AS AO,
                    SPR.COMENTARIO_FINAL AS AP,
                    SPR.ESTATUS_FINAL AS AQ,
                    CASE
                        WHEN SPR.COMENTARIO_PRORROGA IS NULL THEN 'N'
                        ELSE 'S'
                    END AS AR,
                    SPR.VOBO_REG AS AS_,
                    '' AS AT_,
                    SPR.SEMAFORO AS AU,
                    '' AS AV,
                    '' AS AW,
                    '' AS AX,
                    '' AS AY,
                    '' AS AZ,
                    '' AS BA,
                    '' AS BB,
                    '' AS BC,
                    '' AS BD
                FROM
                    SOLICITUDES_PENDIENTES SPR
                WHERE
                    $sucursales
                    $fecha
            ) ORDER BY D DESC
        SQL;

        try {
            $db = new Database();
            $reporte = $db->queryAll($qry);
            return self::Responde(true, "Reporte histórico.", $reporte);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al generar el histórico.", null, $e->getMessage());
        }
    }

    public static function getAllSolicitudes($cdgco)
    {
        $string_from_array = implode(', ', $cdgco);
        $in = '';
        $in_1 = '';

        if ($string_from_array != '') {
            $in = 'SPE.CDGCO IN(' . $string_from_array . ') AND';
            $in_1 = 'SPR.CDGCO IN(' . $string_from_array . ') AND';
        }

        $qry = <<<SQL
            SELECT * FROM  
            (
                SELECT DISTINCT * FROM SOLICITUDES_PENDIENTES SPE
                WHERE $in SPE.SOLICITUD > TO_DATE('19/10/2023', 'DD/MM/YYYY')
                AND SPE.CICLO NOT LIKE 'R%'
                AND SPE.CICLO NOT LIKE 'D%'
                UNION 
                SELECT DISTINCT * FROM SOLICITUDES_PROCESADAS SPR
                WHERE $in_1 SPR.SOLICITUD > TO_DATE('19/10/2023', 'DD/MM/YYYY')
                AND (ESTATUS_FINAL IS NULL OR ESTATUS_FINAL LIKE 'PENDIENTE%')
                AND SPR.CICLO NOT LIKE 'R%'
                AND SPR.CICLO NOT LIKE 'D%'
            )
            ORDER BY SOLICITUD ASC
        SQL;

        $db = new Database();
        return $db->queryAll($qry);
    }

    public static function getSolicitudesRetiro($cdgco)
    {
        $qry = <<<SQL
            SELECT
                RA.ID
                , RA.CDGNS AS CREDITO
                , RA.CICLO
                , RG.CODIGO AS REGION
                , RG.NOMBRE AS NOMBRE_REGION
                , CO.CODIGO AS SUCURSAL
                , CO.NOMBRE AS NOMBRE_SUCURSAL
                , GET_NOMBRE_EMPLEADO(PE.CODIGO) AS NOMBRE_EJECUTIVO
                , RA.FECHA_ENTREGA
                , GET_NOMBRE_CLIENTE(CL.CODIGO) AS NOMBRE_CLIENTE
                , CL.TELEFONO
                , RAC.COMENTARIO_INTERNO
                , RAC.COMENTARIO_EXTERNO
                , TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                , NVL(RAC.ESTATUS, 'P') AS ESTATUS
                , CASE RAC.ESTATUS
                    WHEN 'I' THEN 'INCOMPLETA'
                    WHEN 'C' THEN 'COMPLETA'
                    ELSE 'PENDIENTE'
                  END AS ESTATUS_ETIQUETA
            FROM
                RETIROS_AHORRO RA
                INNER JOIN SN ON SN.CDGNS = RA.CDGNS AND SN.CICLO = RA.CICLO
                INNER JOIN SC ON SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO AND SC.CANTSOLIC <> 9999
                INNER JOIN CL ON CL.CODIGO = SC.CDGCL 
                INNER JOIN CO ON SN.CDGCO = CO.CODIGO 
                INNER JOIN RG ON CO.CDGRG = RG.CODIGO 
                INNER JOIN PE ON PE.CODIGO = SN.CDGOCPE
                LEFT JOIN RETIROS_AHORRO_CALLCENTER RAC ON RA.ID = RAC.RETIRO
            WHERE
                RA.ESTATUS = 'P'
                FILTR_SUC
        SQL;

        $string_from_array = implode(', ', $cdgco);
        $filtro_suc = " AND CO.CODIGO IN ($string_from_array) ";
        $qry = $string_from_array != '' ? str_replace('FILTR_SUC', $filtro_suc, $qry) : str_replace('FILTR_SUC', '', $qry);

        try {
            $db = new Database();
            $res = $db->queryAll($qry);
            return self::Responde(true, 'Solicitudes de retiro obtenidas', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener solicitudes de retiro', null, $e->getMessage());
        }
    }

    public static function getInfoRetiro($datos)
    {
        $qry = <<<SQL
            SELECT
                RA.ID
                , RA.CDGNS AS CREDITO
                , RA.CICLO
                , RA.CANT_SOLICITADA
                , CO.CODIGO AS SUCURSAL
                , CO.NOMBRE AS NOMBRE_SUCURSAL
                , GET_NOMBRE_EMPLEADO(PE.CODIGO) AS NOMBRE_EJECUTIVO
                , RA.FECHA_ENTREGA
                , CL.CODIGO AS CLIENTE
                , GET_NOMBRE_CLIENTE(CL.CODIGO) AS NOMBRE_CLIENTE
                , TO_CHAR(CL.NACIMIENTO, 'DD/MM/YYYY') AS FECHA_NACIMIENTO
                , TRUNC(MONTHS_BETWEEN(SYSDATE, CL.NACIMIENTO) / 12) AS EDAD
                , CASE CL.SEXO
                    WHEN 'M' THEN 'Hombre'
                    WHEN 'F' THEN 'Mujer'
                    ELSE 'Otro'
                  END AS SEXO
                , CL.TELEFONO
                , DOMICILIO_CLIENTE(CL.CODIGO) AS DOMICILIO
                , TO_CHAR(RA.FECHA_CREACION, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_CREACION
                , RAC.ESTATUS
                , CASE RAC.ESTATUS
                    WHEN 'I' THEN 'INCOMPLETA'
                    WHEN 'C' THEN 'COMPLETA'
                    ELSE 'PENDIENTE'
                  END AS ESTATUS_ETIQUETA
                , RAC.COMENTARIO_INTERNO
                , RAC.COMENTARIO_EXTERNO
                , RAC.INTENTOS
                , TO_CHAR(CASE WHEN RAC.FECHA_LLAMADA_2 IS NOT NULL THEN RAC.FECHA_LLAMADA_2 ELSE RAC.FECHA_LLAMADA_1 END, 'DD/MM/YYYY HH24:MI:SS') AS ULTIMA_LLAMADA
                , CASE WHEN RAC.TIPO_LLAMADA_2 IS NOT NULL THEN RAC.TIPO_LLAMADA_2 ELSE RAC.TIPO_LLAMADA_1 END AS TIPO_ULTIMA_LLAMADA
                , RAC.R1
                , RAC.R2
            FROM
                RETIROS_AHORRO RA
                INNER JOIN SN ON SN.CDGNS = RA.CDGNS AND SN.CICLO = RA.CICLO
                INNER JOIN SC ON SC.CDGNS = SN.CDGNS AND SC.CICLO = SN.CICLO AND SC.CANTSOLIC <> 9999
                INNER JOIN CL ON CL.CODIGO = SC.CDGCL 
                INNER JOIN CO ON SN.CDGCO = CO.CODIGO 
                INNER JOIN RG ON CO.CDGRG = RG.CODIGO 
                INNER JOIN PE ON PE.CODIGO = SN.CDGOCPE
                LEFT JOIN RETIROS_AHORRO_CALLCENTER RAC ON RA.ID = RAC.RETIRO
            WHERE
                RA.ID = :retiro
        SQL;

        $prms = [
            'retiro' => $datos['retiro']
        ];

        try {
            $db = new Database();
            $res = $db->queryOne($qry, $prms);
            return self::Responde(true, 'Información de retiro obtenida', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener información de retiro', null, $e->getMessage());
        }
    }

    public static function iniciaRetiroCallCenter($datos)
    {
        $qry = <<<SQL
            MERGE INTO RETIROS_AHORRO_CALLCENTER RAC
            USING (
                SELECT :retiro AS RETIRO FROM DUAL
            ) S
            ON (RAC.RETIRO = S.RETIRO)
            WHEN NOT MATCHED THEN
                INSERT (
                    RETIRO,
                    CDGPE
                )
                VALUES (
                    :retiro,
                    :cdgpe
                )
        SQL;

        $prms = [
            'retiro' => $datos['retiro'],
            'cdgpe' => $datos['usuario']
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $prms);
            return self::Responde(true, 'Retiro iniciado correctamente', null);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al iniciar el retiro', null, $e->getMessage());
        }
    }

    public static function RegistraLlamadaRetiro($datos)
    {
        $qry = <<<SQL
            UPDATE RETIROS_AHORRO_CALLCENTER
            SET
                INTENTOS = INTENTOS + 1,
                ACTUALIZACION = SYSDATE,
                R1 = :r1,
                R2 = :r2,
                TIPO_LLAMADA_1 = CASE WHEN TIPO_LLAMADA_1 IS NULL THEN :tipo_llamada ELSE TIPO_LLAMADA_1 END,
                FECHA_LLAMADA_1 = CASE WHEN FECHA_LLAMADA_1 IS NULL THEN SYSDATE ELSE FECHA_LLAMADA_1 END,
                TIPO_LLAMADA_2 = CASE WHEN TIPO_LLAMADA_1 IS NOT NULL THEN :tipo_llamada ELSE TIPO_LLAMADA_2 END,
                FECHA_LLAMADA_2 = CASE WHEN FECHA_LLAMADA_1 IS NOT NULL THEN SYSDATE ELSE FECHA_LLAMADA_2 END,
                ESTATUS = CASE WHEN :completo = '1' THEN 'C' ELSE 'I' END
            WHERE
                RETIRO = :retiro
        SQL;

        $prms = [
            'retiro' => $datos['retiro'],
            'r1' => $datos['r1'],
            'r2' => $datos['r2'],
            'tipo_llamada' => $datos['tipo'],
            'completo' => $datos['completo']
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $prms);
            return self::Responde(true, 'Llamada registrada correctamente', null);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al registrar llamada', null, $e->getMessage());
        }
    }

    public static function ActualizaComentariosRetiro($datos)
    {
        $qry = <<<SQL
            UPDATE RETIROS_AHORRO_CALLCENTER
            SET
                CDGPE = :cdgpe,
                COMENTARIO_INTERNO = CASE WHEN :interno IS NOT NULL THEN :interno ELSE COMENTARIO_INTERNO END,
                COMENTARIO_EXTERNO = CASE WHEN :externo IS NOT NULL THEN :externo ELSE COMENTARIO_EXTERNO END,
                ACTUALIZACION = SYSDATE
            WHERE
                RETIRO = :retiro
        SQL;

        $prms = [
            'retiro' => $datos['retiro'],
            'cdgpe' => $datos['usuario'],
            'interno' => isset($datos['interno']) ? $datos['interno'] : null,
            'externo' => isset($datos['externo']) ? $datos['externo'] : null
        ];


        try {
            $db = new Database();
            $db->insertar($qry, $prms);
            return self::Responde(true, 'Comentarios de retiro actualizados correctamente', null);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al actualizar comentarios de retiro', null, $e->getMessage());
        }
    }

    public static function FinalizaSolicitudRetiro($datos)
    {
        $qry = <<<SQL
            UPDATE RETIROS_AHORRO
            SET
                ESTATUS = :estatus
                , CDGPE_CANCELACION = CASE WHEN :estatus IN ('C', 'R') THEN :usuario ELSE NULL END
                , MOTIVO_CANCELACION = CASE :estatus WHEN 'C' THEN 'CANCELADO POR CALL CENTER' WHEN 'R' THEN 'RECHAZADO POR CALL CENTER' ELSE NULL END
                , FECHA_CANCELACION = CASE WHEN :estatus IN ('C', 'R') THEN SYSDATE ELSE NULL END
            WHERE
                ID = :retiro
        SQL;

        $prms = [
            'retiro' => $datos['retiro'],
            'estatus' => $datos['estatus'],
            'usuario' => $datos['usuario']
        ];

        try {
            $db = new Database();
            $db->insertar($qry, $prms);
            return self::Responde(true, 'Solicitud de retiro finalizada correctamente', null);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al finalizar solicitud de retiro', null, $e->getMessage());
        }
    }

    public static function getAllSolicitudesBusquedaRapida($cdgns)
    {

        $mysqli = new Database();

        $query = <<<sql
	    SELECT * FROM (

        SELECT ID_SCALL, CDGNS, CICLO, FECHA_SOL, INICIO, 
        CDGCO, NOMBRE_SUCURSAL, CODIGO_REGION, 
        REGION, ID_EJECUTIVO, 
        CASE WHEN FECHA_SOL < TIMESTAMP '2023-10-19 00:00:00.000000' THEN 'SIN HISTORIAL' 
        ELSE ESTATUS_CL END AS ESTATUS_CL, 
        
        CASE WHEN FECHA_SOL < TIMESTAMP '2023-10-19 00:00:00.000000' THEN 'SIN HISTORIAL' 
        ELSE ESTATUS_CL END AS ESTATUS_AV, 
            
        FECHA_TRABAJO,
        CASE WHEN FECHA_SOL < TIMESTAMP '2023-10-19 00:00:00.000000' THEN 'SIN HISTORIAL' 
        ELSE 'PENDIENTE NO SE HA INICIADO VALIDACIÓN DE CALLCENTER, LA ADMINISTRADORA REGISTRO LA SOLICITUD EL DÍA: ' || FECHA_SOL END AS ESTATUS_GENERAL, 
            
        CASE WHEN FECHA_SOL < TIMESTAMP '2023-10-19 00:00:00.000000' THEN 'NO ESTA EN NINGUNA BANDEJA' 
        ELSE 'BANDEJA PENDIENTES - SUC: ' || NOMBRE_SUCURSAL END AS BANDEJA, COMENTARIO_INICIAL, COMENTARIO_FINAL, CICLOR, CREDITO_ADICIONAL
        FROM SOLICITUDES_PENDIENTES SPE WHERE CDGNS = '$cdgns'
                                        
	    UNION 
	    
        SELECT ID_SCALL, CDGNS, CICLO, FECHA_SOL, INICIO, 
        SPR.CDGCO, NOMBRE_SUCURSAL, CODIGO_REGION , 
        REGION, ID_EJECUTIVO, ESTATUS_CL, ESTATUS_AV, FECHA_TRABAJO,
        CASE WHEN (ESTATUS_FINAL = 'PENDIENTE' OR ESTATUS_FINAL IS NULL) THEN 'PENDIENTE DE ASIGNAR UN ESTATUS'
        ELSE ESTATUS_FINAL END AS ESTATUS_GENERAL,
            
        CASE WHEN (ESTATUS_FINAL = 'PENDIENTE' OR ESTATUS_FINAL IS NULL) THEN 'BANDEJA PENDIENTES, VALIDANDO EL EJECUTIVO: ' || PE.NOMBRE1 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE
        ELSE 'BANDEJA HISTORICOS - VALIDO EJECUTIVO: ' || PE.NOMBRE1 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE || ' EL DÍA:'|| FECHA_TRABAJO END AS BANDEJA, COMENTARIO_INICIAL, COMENTARIO_FINAL,
        CICLOR, CREDITO_ADICIONAL                                                                                                                                             
        FROM SOLICITUDES_PROCESADAS SPR 
        INNER JOIN PE ON PE.CODIGO = SPR.CDGPE 
            WHERE CDGNS = '$cdgns') ORDER BY INICIO DESC
sql;

        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudesProrroga($cdgco)
    {


        $mysqli = new Database();

        $query = <<<sql
	    SELECT DISTINCT * FROM SOLICITUDES_PROCESADAS SPR
	    WHERE SEMAFORO = '1' AND PRORROGA = '1'
sql;

        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudesReactivar($cdgco)
    {


        $mysqli = new Database();

        $query = <<<sql
	    SELECT DISTINCT * FROM SOLICITUDES_PROCESADAS SPR
	    WHERE SEMAFORO = '1' AND REACTIVACION = '1'
sql;

        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllSolicitudesConcentrado($Fecha, $Region)
    {

        if ($Region != '') {
            if ($Region != '0') {
                $Region_cond = "AND RG.CODIGO = '$Region'";
            } else {
                $Region_cond = '';
            }
            // $condicional = " AND SOL_CALL_CENTER.FECHA_SOL BETWEEN TO_DATE('$Fecha', 'YY-mm-dd') AND TO_DATE('$Fecha', 'YY-mm-dd') $Region_cond";
            $condicional = " $Region_cond";
        } else {
            $condicional = '';
        }


        $mysqli = new Database();
        $query = <<<sql
          SELECT DISTINCT 
 		SOL_CALL_CENTER.CDGNS || '-' || SOL_CALL_CENTER.CICLO AS CLAVE,
        RG.CODIGO AS CODIGO_REGION,
 		RG.NOMBRE AS REGION,
 		SOL_CALL_CENTER.FECHA_TRA_CL,
 		SOL_CALL_CENTER.FECHA_TRABAJO_AV,
 		TO_CHAR(SOL_CALL_CENTER.FECHA_SOL ,'DD/MM/YYYY HH24:MI:SS') AS FECHA_SOL, 
 		'' AS INICIO, 
 		CO.CODIGO AS CODIGO_SUCURSAL, 
 		CO.NOMBRE AS AGENCIA,
 		CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS EJECUTIVO,
 		SOL_CALL_CENTER.CDGCL_CL AS CLIENTE, 
 		CL.NOMBRE1 || ' ' ||  CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE AS NOMBRE_CLIENTE, 
 		SOL_CALL_CENTER.CICLO, 
 		SOL_CALL_CENTER.TEL_CL, 
 		SOL_CALL_CENTER.TIPO_LLAM_1_CL,
 		SOL_CALL_CENTER.TIPO_LLAM_2_CL,
 		SOL_CALL_CENTER.PRG_UNO_CL,
 		SOL_CALL_CENTER.PRG_DOS_CL,
 		SOL_CALL_CENTER.PRG_TRES_CL,
 		SOL_CALL_CENTER.PRG_CUATRO_CL,
 		SOL_CALL_CENTER.PRG_CINCO_CL,
 		SOL_CALL_CENTER.PRG_SEIS_CL,
 		SOL_CALL_CENTER.PRG_SIETE_CL,
 		SOL_CALL_CENTER.PRG_OCHO_CL,
 		SOL_CALL_CENTER.PRG_NUEVE_CL,
 		SOL_CALL_CENTER.PRG_DIEZ_CL,
 		SOL_CALL_CENTER.PRG_ONCE_CL,
 		SOL_CALL_CENTER.PRG_DOCE_CL,
 		CL.NOMBRE1 || ' ' ||  CL.NOMBRE2 || ' ' || CL.PRIMAPE || ' ' || CL.SEGAPE AS NOMBRE_AVAL,
 		SOL_CALL_CENTER.TEL_AV, 
 		SOL_CALL_CENTER.TIPO_LLAM_1_AV,
 		SOL_CALL_CENTER.TIPO_LLAM_2_AV, 
 		SOL_CALL_CENTER.PRG_UNO_AV,
 		SOL_CALL_CENTER.PRG_DOS_AV,
 		SOL_CALL_CENTER.PRG_TRES_AV,
 		SOL_CALL_CENTER.PRG_CUATRO_AV,
 		SOL_CALL_CENTER.PRG_CINCO_AV,
 		SOL_CALL_CENTER.PRG_SEIS_AV,
 		SOL_CALL_CENTER.PRG_SIETE_AV,
 		SOL_CALL_CENTER.PRG_OCHO_AV,
 		SOL_CALL_CENTER.PRG_NUEVE_AV
            
        FROM SOL_CALL_CENTER 
        
        INNER JOIN CL ON CL.CODIGO = SOL_CALL_CENTER.CDGCL_CL
        INNER JOIN CO ON SOL_CALL_CENTER.CDGCO = CO.CODIGO
        INNER JOIN RG ON CO.CDGRG = RG.CODIGO 
        INNER JOIN PE ON PE.CODIGO = SOL_CALL_CENTER.CDGPE 
        
        WHERE CL.CODIGO = SOL_CALL_CENTER.CDGCL_CL
        $condicional
         
sql;
        //var_dump($query);
        return $mysqli->queryAll($query);
    }

    public static function getAllAnalistas()
    {

        $mysqli = new Database();

        $query3 = <<<sql
        SELECT
            CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) NOMBRE,
            UT.CDGTUS PERFIL, PE.CDGCO, PE.CODIGO AS USUARIO
        FROM
            PE,
            UT
        WHERE
            PE.CODIGO = UT.CDGPE
            AND PE.CDGEM = UT.CDGEM
            AND PE.CDGEM = 'EMPFIN'
            AND PE.ACTIVO = 'S'
            AND (PE.BLOQUEO = 'N' OR PE.BLOQUEO IS NULL)
            AND UT.CDGTUS = 'CALLC'
sql;
        return $mysqli->queryAll($query3);
    }

    public static function getAllRegiones()
    {
        $mysqli = new Database();

        $query3 = <<<sql
        SELECT RG.NOMBRE AS REGION, CO.CODIGO, CO.NOMBRE FROM CO
        INNER JOIN RG ON RG.CODIGO = CO.CDGRG                                                          
        WHERE NOT EXISTS(SELECT CDGCO FROM ASIGNACION_SUC_A WHERE ASIGNACION_SUC_A.CDGCO = CO.CODIGO)
        ORDER BY CODIGO
sql;
        return $mysqli->queryAll($query3);
        /////
        ///
        ///
        /// SELECT CO.CODIGO, CO.NOMBRE  FROM CO
        //           WHERE NOT EXISTS(SELECT CDGCO FROM CIERRE_HORARIO WHERE CIERRE_HORARIO.CDGCO = CO.CODIGO)

    }

    public static function getAllAnalistasAsignadas()
    {

        $mysqli = new Database();

        $query3 = <<<sql
         SELECT ASIGNACION_SUC_A.CDGPE, ASIGNACION_SUC_A.CDGCO, CO.NOMBRE, ASIGNACION_SUC_A.FECHA_INICIO, 
                ASIGNACION_SUC_A.FECHA_FIN, ASIGNACION_SUC_A.FECHA_ALTA, ASIGNACION_SUC_A.CDGOCPE, 
                PE.NOMBRE1 || ' ' || PE.NOMBRE2 || ' ' || PE.PRIMAPE || ' ' || PE.SEGAPE AS NOMBRE_EJEC
           
        FROM
           ASIGNACION_SUC_A
        INNER JOIN CO ON CO.CODIGO = ASIGNACION_SUC_A.CDGCO
        INNER JOIN PE ON PE.CODIGO = ASIGNACION_SUC_A.CDGPE
        ORDER BY ASIGNACION_SUC_A.CDGPE ASC
        
               
sql;
        return $mysqli->queryAll($query3);
    }

    public static function UpdateResumen($encuesta)
    {
        $mysqli = new Database();

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET COMENTARIO_INICIAL='$encuesta->_comentarios_iniciales', COMENTARIO_FINAL='$encuesta->_comentarios_finales' , COMENTARIO_PRORROGA='$encuesta->_comentarios_prorroga'
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function UpdateProrroga($prorroga)
    {
        $mysqli = new Database();

        if ($prorroga->_prorroga == '2') {
            $q = ", ESTATUS = NULL, SEMAFORO = NULL ";
        } else {
            $q = "";
        }

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET PRORROGA='$prorroga->_prorroga' $q
                WHERE ID_SCALL='$prorroga->_id_call'
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function ReactivarSolicitud($reactivar)
    {
        $mysqli = new Database();

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET REACTIVACION = 1 
                WHERE ID_SCALL='$reactivar->_id_call'
sql;
        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function ReactivarSolicitudAdmin($reactivar)
    {
        $mysqli = new Database();
        if ($reactivar->_opcion == 'SI') {
            $qu = " ,ESTATUS = NULL, SEMAFORO = NULL ";
        } else {
            $qu = "";
        }

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET REACTIVACION = '400' $qu
                WHERE ID_SCALL='$reactivar->_id_call'
sql;
        //var_dump($query);
        return $mysqli->insert($query);
    }


    public static function UpdateResumenFinal($encuesta)
    {
        $mysqli = new Database();

        $query = <<<sql
                UPDATE SOL_CALL_CENTER
                SET COMENTARIO_INICIAL='$encuesta->_comentarios_iniciales', COMENTARIO_FINAL='$encuesta->_comentarios_finales', SEMAFORO = '1', ESTATUS = '$encuesta->_estatus_solicitud', VOBO_GERENTE_REGIONAL = '$encuesta->_vobo_gerente', PRORROGA = '4', REACTIVACION = NULL  
                WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }


    public static function insertEncuestaCL($encuesta)
    {
        $campos = [
            "PRG_UNO_CL" => $encuesta->_uno,
            "PRG_DOS_CL" => $encuesta->_dos,
            "PRG_TRES_CL" => $encuesta->_tres,
            "PRG_CUATRO_CL" => $encuesta->_cuatro,
            "PRG_CINCO_CL" => $encuesta->_cinco,
            "PRG_SEIS_CL" => $encuesta->_seis,
            "PRG_SIETE_CL" => $encuesta->_siete,
            "PRG_OCHO_CL" => $encuesta->_ocho,
            "PRG_NUEVE_CL" => $encuesta->_nueve,
            "PRG_DIEZ_CL" => $encuesta->_diez,
            "PRG_ONCE_CL" => $encuesta->_once,
            "PRG_DOCE_CL" => $encuesta->_doce,
            "NUMERO_INTENTOS_CL" => $encuesta->_llamada,
        ];
        $isInsert = ($encuesta->_llamada == '1');

        if ($isInsert) {
            // Se inserta cuando es la primera llamada
            $campos["ID_SCALL"] = "sol_call_center_id.nextval";
            $campos["CDGRG"] = $encuesta->_cdgre;
            $campos["FECHA_TRA_CL"] = "TIMESTAMP '$encuesta->_fecha.000000'";
            $campos["FECHA_SOL"] = "TIMESTAMP '$encuesta->_fecha_solicitud.000000'";
            $campos["CDGNS"] = $encuesta->_cdgns;
            $campos["CDGCO"] = $encuesta->_cdgco;
            $campos["CDGPE"] = $encuesta->_cdgpe;
            $campos["CDGCL_CL"] = $encuesta->_cliente;
            $campos["CICLO"] = $encuesta->_ciclo;
            $campos["TEL_CL"] = $encuesta->_movil;
            $campos["TIPO_LLAM_1_CL"] = $encuesta->_tipo_llamada;
            $campos["DIA_LLAMADA_1_CL"] = "CURRENT_TIMESTAMP";
            $campos["HORA_LLAMADA_1_CL"] = "CURRENT_TIMESTAMP";
            $campos["CDGCL_AV"] = $encuesta->_id_aval_cl;
            $campos["TEL_AV"] = $encuesta->_telefono_aval_cl;
            $campos["CDGCL_AV_2"] = $encuesta->_id_aval_cl_2;
            $campos["TEL_AV_2"] = $encuesta->_telefono_aval_cl_2;

            if ($encuesta->_completo == '1') {
                $campos["FIN_CL"] = '1';
            }

            $columnas = implode(", ", array_keys($campos));

            $valores = [];
            foreach ($campos as $campo => $valor) {
                if (
                    strpos($valor, 'CURRENT_TIMESTAMP') !== false ||
                    strpos($valor, 'TIMESTAMP') !== false ||
                    strpos($valor, 'nextval') !== false
                ) {
                    $valores[] = $valor;
                } else {
                    $valores[] = "'$valor'";
                }
            }

            $valoresStr = implode(", ", $valores);

            $query = <<<SQL
            INSERT INTO SOL_CALL_CENTER
            ($columnas)
            VALUES($valoresStr)
            SQL;
        } else {
            // Se actualiza cuando no es la primera llamada
            $campos["TIPO_LLAM_2_CL"] = $encuesta->_tipo_llamada;
            $campos["DIA_LLAMADA_2_CL"] = "CURRENT_TIMESTAMP";
            $campos["HORA_LLAMADA_2_CL"] = "CURRENT_TIMESTAMP";

            if ($encuesta->_completo == '1') {
                $campos["FIN_CL"] = '1';
            }

            $setClause = [];
            foreach ($campos as $campo => $valor) {
                if ($valor === "NULL") {
                    $setClause[] = "$campo=$valor";
                } else if (strpos($valor, 'CURRENT_TIMESTAMP') !== false) {
                    $setClause[] = "$campo=$valor";
                } else {
                    $setClause[] = "$campo='$valor'";
                }
            }

            $setClauseStr = implode(', ', $setClause);

            $query = <<<SQL
            UPDATE SOL_CALL_CENTER
            SET $setClauseStr
            WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO='$encuesta->_ciclo'
            SQL;
        }

        $mysqli = new Database();
        return $mysqli->insert($query);
    }

    public static function insertEncuestaAV($encuesta)
    {
        $no_av = $encuesta->_no_av == '1' ? '' : "_$encuesta->_no_av";
        $campos = [
            "PRG_UNO_AV$no_av"  => $encuesta->_uno,
            "PRG_DOS_AV$no_av" => $encuesta->_dos,
            "PRG_TRES_AV$no_av" => $encuesta->_tres,
            "PRG_CUATRO_AV$no_av" => $encuesta->_cuatro,
            "PRG_CINCO_AV$no_av" => $encuesta->_cinco,
            "PRG_SEIS_AV$no_av" => $encuesta->_seis,
            "PRG_SIETE_AV$no_av" => $encuesta->_siete,
            "PRG_OCHO_AV$no_av" => $encuesta->_ocho,
            "PRG_NUEVE_AV$no_av" => $encuesta->_nueve,
            "NUMERO_INTENTOS_AV$no_av" => $encuesta->_llamada
        ];

        if ($encuesta->_llamada == '1') {
            $campos["FECHA_TRABAJO_AV$no_av"] = "TIMESTAMP '2023-08-22 04:21:40.000000'";
            $campos["TIPO_LLAM_1_AV$no_av"] = $encuesta->_tipo_llamada;
            $campos["DIA_LLAMADA_1_AV$no_av"] = "CURRENT_TIMESTAMP";
            $campos["HORA_LLAMADA_1_AV$no_av"] = "CURRENT_TIMESTAMP";
        } else {
            $campos["TIPO_LLAM_2_AV$no_av"] = $encuesta->_tipo_llamada;
            $campos["DIA_LLAMADA_2_AV$no_av"] = "CURRENT_TIMESTAMP";
            $campos["HORA_LLAMADA_2_AV$no_av"] = "CURRENT_TIMESTAMP";
        }

        if ($encuesta->_completo == '1') {
            $campos["FIN_AV$no_av"] = '1';
        }

        $setClause = [];
        foreach ($campos as $campo => $valor) {
            if ($valor === "NULL") {
                $setClause[] = "$campo=$valor";
            } else if (strpos($valor, 'CURRENT_TIMESTAMP') !== false || strpos($valor, 'TIMESTAMP') !== false) {
                $setClause[] = "$campo=$valor";
            } else {
                $setClause[] = "$campo='$valor'";
            }
        }

        $setClauseStr = implode(', ', $setClause);

        $query = <<<SQL
        UPDATE SOL_CALL_CENTER
        SET $setClauseStr
        WHERE CDGCO='$encuesta->_cdgco' AND CDGCL_CL='$encuesta->_cliente' AND CICLO = '$encuesta->_ciclo'
        SQL;

        $mysqli = new Database();
        return $mysqli->insert($query);
    }

    public static function DeleteAsignaSuc($cdgco)
    {

        $mysqli = new Database();
        $query = <<<sql
                DELETE FROM ASIGNACION_SUC_A
                WHERE CDGCO = '$cdgco'
sql;
        //var_dump($query);
        return $mysqli->insert($query);
    }




    public static function insertAsignaSucursal($asigna)
    {

        $mysqli = new Database();

        $query = <<<sql
            INSERT INTO ASIGNACION_SUC_A
            (ID_ASIGNACION, CDGEM, CDGPE, CDGCO, FECHA_INICIO, FECHA_FIN, FECHA_ALTA, CDGOCPE)
            VALUES(SUC_SUCURSALES.nextval, 'EMPFIN', '$asigna->_ejecutivo', '$asigna->_region', TIMESTAMP '$asigna->_fecha_registro', TIMESTAMP '$asigna->_fecha_inicio 00:00:00.000000', TIMESTAMP '$asigna->_fecha_fin 00:00:00.000000', 'AMGM')
sql;

        //var_dump($query);
        return $mysqli->insert($query);
    }

    public static function AsignaClienteEncuestaPostventa($datos)
    {
        $query = <<<SQL
            SELECT
                *
            FROM
                CLIENTES_ENCUESTA_POSTVENTA
            WHERE
                ASESOR IS NULL
        SQL;

        if ($datos['cliente']) $query .= " AND CLIENTE <> '" . $datos['cliente'] . "'";

        try {
            $db = new Database('SERVIDOR-AWS');
            $clientes = $db->queryAll($query);
            foreach ($clientes as $cliente) {
                $r = self::ActualizaClienteEncuestaPostventa([
                    'asesor' => $datos['asesor'],
                    'cliente' => $cliente['CLIENTE'],
                    'ciclo' => $cliente['CICLO']
                ]);

                return self::Responde(true, "Cliente asignado correctamente.", $cliente);
            }

            return self::Responde(false, "No hay clientes disponibles para asignar.");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al asignar el cliente.", null, $e->getMessage());
        }
    }

    public static function ActualizaClienteEncuestaPostventa($datos)
    {
        $qry1 = <<<SQL
            UPDATE CLIENTES_ENCUESTA_POSTVENTA
            SET
                ASESOR = :asesor
            WHERE
                CLIENTE = :cliente
                AND CICLO = :ciclo
                AND ASESOR IS NULL
        SQL;

        $qry2 = <<<SQL
            UPDATE CLIENTES_ENCUESTA_POSTVENTA
            SET
                ASESOR = NULL
            WHERE
                CLIENTE = :cliente
                AND CICLO = :ciclo
                AND ASESOR = :asesor
        SQL;

        $qry = $datos['liberar'] ? $qry2 : $qry1;
        unset($datos['liberar']);
        try {
            $db = new Database('SERVIDOR-AWS');
            $r = $db->actualizar($qry, $datos);

            if ($r) return self::Responde(true, "Cliente asignado correctamente.");
            return self::Responde(false, "No se asigno el cliente.");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al actualizar la información.", null, $e->getMessage());
        }
    }

    public static function GuardaEncuestaPostventa($datos)
    {
        $qry = <<<SQL
            INSERT INTO
                ENCUESTA_POSTVENTA (
                    CLIENTE,
                    TELEFONO,
                    FECHA,
                    ASESOR,
                    ESTATUS,
                    COMENTARIO_ASESOR,
                    PREGUNTA_1,
                    COMENTARIO_1,
                    PREGUNTA_2,
                    COMENTARIO_2,
                    PREGUNTA_3,
                    COMENTARIO_3,
                    PREGUNTA_4,
                    COMENTARIO_4,
                    PREGUNTA_5,
                    COMENTARIO_5,
                    COMENTARIO_GENERAL,
                    DURACION,
                    CICLO,
                    MOTIVO_ABANDONO
                )
            VALUES
                (
                    :cliente,
                    :telefono,
                    SYSDATE,
                    :asesor,
                    :estatus,
                    :comentario_asesor,
                    :respuesta_1,
                    :comentario_1,
                    :respuesta_2,
                    :comentario_2,
                    :respuesta_3,
                    :comentario_3,
                    :respuesta_4,
                    :comentario_4,
                    :respuesta_5,
                    :comentario_5,
                    :comentario_general,
                    :duracion,
                    :ciclo,
                    :motivo
                )
        SQL;

        // recorrer el array de datos y asignar null a los valores vacios
        foreach ($datos as $key => $value) {
            $datos[$key] = ($datos[$key] == 'null') ? null : $datos[$key];
        }

        try {
            $db = new Database('SERVIDOR-AWS');
            $db->insertar($qry, $datos);
            return self::Responde(true, "Información guardada correctamente.");
        } catch (\Exception $e) {
            return self::Responde(false, "Error al guardar la encuesta.", null, $e->getMessage());
        }
    }

    public static function GetEstatusEncuestaPostventa()
    {
        $qry = <<<SQL
            SELECT UNIQUE
                ESTATUS
            FROM
                ENCUESTA_POSTVENTA
            ORDER BY
                ESTATUS
        SQL;

        try {
            $db = new Database('SERVIDOR-AWS');
            $estatus = $db->queryAll($qry);
            return self::Responde(true, "Estatus encontrados.", $estatus);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener los estatus.", null, $e->getMessage());
        }
    }

    public static function GetReporteEncuestaPostventa($datos)
    {
        $qry = <<<SQL
            SELECT
                CLIENTE,
                CICLO,
                TELEFONO,
                TO_CHAR(FECHA, 'DD/MM/YYYY HH24:MI:SS') FECHA,
                ASESOR,
                ESTATUS,
                MOTIVO_ABANDONO,
                COMENTARIO_ASESOR
            FROM
                ENCUESTA_POSTVENTA
            WHERE
                TRUNC(FECHA) BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
        SQL;

        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF']
        ];

        if ($datos['estatus'] && $datos['estatus'] !== '*') {
            $qry .= ' AND ESTATUS = :estatus';
            $prm['estatus'] = $datos['estatus'];
        } else unset($datos['estatus']);

        $qry .= ' ORDER BY FECHA DESC';

        try {
            $db = new Database('SERVIDOR-AWS');
            $reporte = $db->queryAll($qry, $prm);
            return self::Responde(true, "Reporte encontrado.", $reporte);
        } catch (\Exception $e) {
            return self::Responde(false, "Error al obtener el reporte.", null, $e->getMessage());
        }
    }
}
