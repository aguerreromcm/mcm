<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;


class AuditoriaDevengo extends Model
{
    /**
     * Devengos faltantes: solo créditos que puede procesar SP_AUDITA_DEVENGO.
     * Misma base que el SP: PRN + MP (TIPO='IN') + CF → DATOS_CREDITO.
     * CORTE = MAX(FECHA_LIQUIDA) por crédito/ciclo (TBL_CIERRE_DIA).
     * Calendario: INICIO+1 hasta LEAST(FIN, P_CORTE). Solo fechas no registradas en DEVENGO_DIARIO.
     *
     * @return array { success, mensaje, datos }
     */
    public static function GetDevengosFaltantes($filtros = [])
    {
        $credito = isset($filtros['credito']) && $filtros['credito'] !== '' ? trim($filtros['credito']) : null;
        $ciclo   = isset($filtros['ciclo']) && $filtros['ciclo'] !== '' ? trim($filtros['ciclo']) : null;

        $qry = <<<SQL
            WITH
            DATOS_CREDITO AS (
                SELECT
                    PRN.CDGNS AS CREDITO,
                    PRN.CICLO,
                    TRUNC(PRN.INICIO) AS INICIO,
                    DECODE(PRN.PERIODICIDAD, 'S', 7, 'C', 14, 'Q', 15, 'M', 30, 7) AS FACTOR_DIAS,
                    PRN.PLAZO
                FROM PRN
                JOIN MP ON PRN.CDGEM = MP.CDGEM
                    AND PRN.CDGNS = MP.CDGCLNS
                    AND PRN.CICLO = MP.CICLO
                    AND MP.TIPO = 'IN'
                JOIN CF ON PRN.CDGEM = CF.CDGEM
                    AND PRN.CDGFDI = CF.CDGFDI
                WHERE (:credito IS NULL OR PRN.CDGNS = :credito)
                  AND (:ciclo IS NULL OR PRN.CICLO = :ciclo)
            ),
            DATOS_CALCULO AS (
                SELECT
                    DC.CREDITO,
                    DC.CICLO,
                    DC.INICIO,
                    TRUNC(DC.INICIO + (DC.FACTOR_DIAS * DC.PLAZO)) AS FIN
                FROM DATOS_CREDITO DC
            ),
            CORTE AS (
                SELECT
                    CDGCLNS AS CREDITO,
                    CICLO,
                    MAX(FECHA_LIQUIDA) AS P_CORTE
                FROM TBL_CIERRE_DIA
                WHERE FECHA_LIQUIDA IS NOT NULL
                GROUP BY CDGCLNS, CICLO
            ),
            CREDITOS_PROCESABLES AS (
                SELECT
                    DC.CREDITO,
                    DC.CICLO,
                    DC.INICIO,
                    LEAST(DC.FIN, CO.P_CORTE) AS FECHA_HASTA
                FROM DATOS_CALCULO DC
                INNER JOIN CORTE CO ON CO.CREDITO = DC.CREDITO AND CO.CICLO = DC.CICLO
            ),
            CALENDARIO (CREDITO, CICLO, FECHA_CALC, FECHA_HASTA) AS (
                SELECT
                    CP.CREDITO,
                    CP.CICLO,
                    CP.INICIO + 1,
                    CP.FECHA_HASTA
                FROM CREDITOS_PROCESABLES CP
                WHERE CP.INICIO + 1 <= CP.FECHA_HASTA
                UNION ALL
                SELECT
                    C.CREDITO,
                    C.CICLO,
                    C.FECHA_CALC + 1,
                    C.FECHA_HASTA
                FROM CALENDARIO C
                WHERE C.FECHA_CALC + 1 <= C.FECHA_HASTA
            )
            SELECT
                C.CREDITO,
                C.CICLO,
                TO_CHAR(C.FECHA_CALC,'DD/MM/YYYY') AS FECHA_FALTANTE,
                TO_CHAR(C.FECHA_CALC,'YYYY-MM-DD') AS FECHA_CALC,
                NS.NOMBRE
            FROM CALENDARIO C
            LEFT JOIN DEVENGO_DIARIO D
                ON D.CDGCLNS = C.CREDITO
                AND D.CICLO = C.CICLO
                AND TRUNC(D.FECHA_CALC) = C.FECHA_CALC
            LEFT JOIN PRN ON PRN.CDGNS = C.CREDITO AND PRN.CICLO = C.CICLO
            LEFT JOIN NS ON NS.CDGEM = PRN.CDGEM AND NS.CODIGO = PRN.CDGNS
            WHERE D.CDGCLNS IS NULL
        SQL;

        $prm = [
            'credito' => $credito,
            'ciclo'   => $ciclo,
        ];

        if (!empty($filtros['fecha_desde'])) {
            $qry .= ' AND C.FECHA_CALC >= TO_DATE(:fecha_desde, \'YYYY-MM-DD\')';
            $prm['fecha_desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $qry .= ' AND C.FECHA_CALC <= TO_DATE(:fecha_hasta, \'YYYY-MM-DD\')';
            $prm['fecha_hasta'] = $filtros['fecha_hasta'];
        }
        $qry .= ' ORDER BY C.CREDITO, C.CICLO, C.FECHA_CALC';

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Consulta exitosa', is_array($res) ? $res : []);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al consultar devengos faltantes', null, $e->getMessage());
        }
    }

    /**
     * Valida que crédito y ciclo existan en PRN.
     */
    public static function ExisteCreditoCiclo($db, $credito, $ciclo)
    {
        $qry = <<<SQL
            SELECT 1
            FROM PRN
            WHERE CDGNS = :credito AND CICLO = :ciclo
        SQL;
        $prm = ['credito' => $credito, 'ciclo' => $ciclo];
        $res = $db->queryOne($qry, $prm);
        return $res !== false && !empty($res);
    }

    /**
     * Fecha límite para procesamiento (restricción FECHA_LIQUIDA desde TBL_CIERRE_DIA).
     * Si existe MAX(FECHA_LIQUIDA) → esa fecha; si no → hoy.
     *
     * @return string Fecha YYYY-MM-DD
     */
    public static function ObtenerFechaLiquida($db, $credito, $ciclo)
    {
        $qry = <<<SQL
            SELECT MAX(FECHA_LIQUIDA) AS FECHA_LIQUIDA
            FROM TBL_CIERRE_DIA
            WHERE CDGCLNS = :credito AND CICLO = :ciclo AND FECHA_LIQUIDA IS NOT NULL
        SQL;
        $prm = ['credito' => $credito, 'ciclo' => $ciclo];
        $res = $db->queryOne($qry, $prm);

        if ($res && isset($res['FECHA_LIQUIDA']) && $res['FECHA_LIQUIDA'] !== null) {
            return date('Y-m-d', strtotime($res['FECHA_LIQUIDA']));
        }

        return date('Y-m-d');
    }

    /**
     * Nivel 1 anti-duplicado: existe registro en DEVENGO_DIARIO para crédito/ciclo/fecha.
     */
    public static function ExisteDevengoEnDiario($db, $credito, $ciclo, $fechaCalc)
    {
        $qry = <<<SQL
            SELECT 1
            FROM DEVENGO_DIARIO
            WHERE CDGCLNS = :credito AND CICLO = :ciclo
              AND TRUNC(FECHA_CALC) = TO_DATE(:fecha_calc, 'YYYY-MM-DD')
        SQL;
        $prm = ['credito' => $credito, 'ciclo' => $ciclo, 'fecha_calc' => $fechaCalc];
        $res = $db->queryOne($qry, $prm);
        return $res !== false && !empty($res);
    }

    /**
     * Nivel 2: Lock Oracle. SELECT FOR UPDATE NOWAIT.
     * Lanza Exception si ORA-00054 (recurso ocupado).
     */
    public static function AdquirirLockCreditoCiclo($db, $credito, $ciclo)
    {
        $qry = <<<SQL
            SELECT 1 FROM PRN
            WHERE CDGNS = :credito AND CICLO = :ciclo
            FOR UPDATE NOWAIT
        SQL;
        $prm = ['credito' => $credito, 'ciclo' => $ciclo];
        try {
            $stmt = $db->db_activa->prepare($qry);
            $stmt->execute($prm);
            return true;
        } catch (\PDOException $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'ORA-00054') !== false || strpos($msg, 'resource busy') !== false) {
                throw new \Exception('Proceso concurrente: otro usuario está procesando este crédito/ciclo. Intente más tarde.', 0, $e);
            }
            throw $e;
        }
    }

    /**
     * Ejecuta SP_AUDITA_DEVENGO (caja negra). Firma: (credito, ciclo, corte, output).
     * El SP es el único motor que calcula proyección, montos e inserta en DEVENGO_DIARIO.
     */
    public static function EjecutarSPAuditoriaDevengo($db, $credito, $ciclo, $corte)
    {
        $sp = <<<SQL
            BEGIN SP_AUDITA_DEVENGO(:credito, :ciclo, :corte, :output); END;
        SQL;
        $prm = [
            'credito' => $credito,
            'ciclo'   => $ciclo,
            'corte'   => $corte,
        ];
        return $db->EjecutaSP($sp, $prm);
    }

    /**
     * Valida que el SP haya insertado: COUNT(*) >= 1 en DEVENGO_DIARIO para crédito/ciclo/fecha.
     */
    public static function ValidarInsercionDevengo($db, $credito, $ciclo, $fechaCalc)
    {
        $qry = <<<SQL
            SELECT COUNT(*) AS CANT
            FROM DEVENGO_DIARIO
            WHERE CDGCLNS = :credito AND CICLO = :ciclo
              AND TRUNC(FECHA_CALC) = TO_DATE(:fecha_calc, 'YYYY-MM-DD')
        SQL;
        $prm = ['credito' => $credito, 'ciclo' => $ciclo, 'fecha_calc' => $fechaCalc];
        $res = $db->queryOne($qry, $prm);
        $cant = isset($res['CANT']) ? (int) $res['CANT'] : 0;
        if ($cant >= 1) {
            return true;
        }
        throw new \Exception('El SP no insertó el registro esperado en DEVENGO_DIARIO.');
    }

    /**
     * Registra ejecución en BITACORA_AUDITORIA_DEVENGO.
     */
    public static function InsertarBitacora($db, $datos)
    {
        $qry = <<<SQL
            INSERT INTO BITACORA_AUDITORIA_DEVENGO (
                CREDITO, CICLO, FECHA_PROCESADA, TIPO_EJECUCION,
                USUARIO, PERFIL, IP, HASH_EJECUCION, RESULTADO, MENSAJE_ERROR
            ) VALUES (
                :credito, :ciclo, TO_DATE(:fecha_procesada, 'YYYY-MM-DD'), :tipo_ejecucion,
                :usuario, :perfil, :ip, :hash_ejecucion, :resultado, :mensaje_error
            )
        SQL;
        $prm = [
            'credito'         => $datos['credito'],
            'ciclo'           => $datos['ciclo'],
            'fecha_procesada' => $datos['fecha_procesada'],
            'tipo_ejecucion'  => $datos['tipo_ejecucion'],
            'usuario'         => $datos['usuario'],
            'perfil'          => $datos['perfil'],
            'ip'              => $datos['ip'] ?? null,
            'hash_ejecucion'  => $datos['hash_ejecucion'] ?? null,
            'resultado'       => $datos['resultado'],
            'mensaje_error'   => $datos['mensaje_error'] ?? null,
        ];
        $db->insertar($qry, $prm);
    }
}
