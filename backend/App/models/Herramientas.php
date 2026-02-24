<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class Herramientas extends Model
{
    /**
     * Reporte de días de atraso (PRN situación L).
     * Opcional: filtrar desde el primer día de un mes/año hasta la fecha actual.
     *
     * @param array $datos Opcional: 'mes' (1-12), 'anio' (ej. 2025)
     * @return array { success, mensaje, datos }
     */
    public static function GetRepDiaAtraso($datos = [])
    {
        $qry = <<<SQL
            SELECT
                PRN.CDGNS AS COD_CTE,
                PRN.CICLO,
                NS.NOMBRE,
                TO_CHAR(PRN.INICIO, 'DD/MM/YYYY') AS INICIO,
                FNCALDIASATRASO(PRN.CDGEM, PRN.CDGNS, PRN.CICLO, 'G', SYSDATE) AS DIAS_ATRASO
            FROM
                PRN
                INNER JOIN NS ON PRN.CDGEM = NS.CDGEM
                             AND PRN.CDGNS = NS.CODIGO
            WHERE
                PRN.SITUACION = 'L'
        SQL;

        $prm = [];
        $mes = isset($datos['mes']) ? (int) $datos['mes'] : 0;
        $anio = isset($datos['anio']) ? (int) $datos['anio'] : 0;
        if ($mes >= 1 && $mes <= 12 && $anio >= 2000 && $anio <= 2100) {
            $qry .= ' AND PRN.INICIO >= TO_DATE(:fechaDesde, \'YYYY-MM-DD\')';
            $prm['fechaDesde'] = sprintf('%04d-%02d-01', $anio, $mes);
        }

        try {
            $db = new Database();
            $res = $db->queryAll($qry, $prm);
            return self::Responde(true, 'Consulta exitosa', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al consultar el reporte', null, $e->getMessage());
        }
    }

    /**
     * Devengos faltantes (una fila por cada fecha faltante).
     * Parámetros opcionales: credito, ciclo, fecha_desde (YYYY-MM-DD), fecha_hasta (YYYY-MM-DD).
     *
     * @param array $datos ['credito','ciclo','fecha_desde','fecha_hasta']
     * @return array { success, mensaje, datos }
     */
    public static function GetDevengosFaltantes($datos = [])
    {
        $credito = !empty(trim((string) ($datos['credito'] ?? ''))) ? trim($datos['credito']) : null;
        $ciclo = !empty(trim((string) ($datos['ciclo'] ?? ''))) ? trim($datos['ciclo']) : null;
        $fechaDesde = !empty(trim((string) ($datos['fecha_desde'] ?? ''))) ? trim($datos['fecha_desde']) : null;
        $fechaHasta = !empty(trim((string) ($datos['fecha_hasta'] ?? ''))) ? trim($datos['fecha_hasta']) : null;

        $filtroCte = '';
        $prm = [];
        if ($credito !== null) {
            $filtroCte .= ' AND CA.CDGNS = :credito';
            $prm['credito'] = $credito;
        }
        if ($ciclo !== null) {
            $filtroCte .= ' AND CA.CICLO = :ciclo';
            $prm['ciclo'] = $ciclo;
        }
        if ($fechaDesde !== null) {
            $filtroCte .= ' AND (CA.INICIO + 1) + N.NUM >= TO_DATE(:fecha_desde, \'YYYY-MM-DD\')';
            $prm['fecha_desde'] = $fechaDesde;
        }
        if ($fechaHasta !== null) {
            $filtroCte .= ' AND (CA.INICIO + 1) + N.NUM <= TO_DATE(:fecha_hasta, \'YYYY-MM-DD\')';
            $prm['fecha_hasta'] = $fechaHasta;
        }

        $qry = "WITH CTE_Numero AS (
                SELECT LEVEL - 1 AS NUM FROM DUAL CONNECT BY LEVEL <= 3660
            ),
            CTE_Faltantes AS (
                SELECT CA.CDGNS, CA.CICLO, (CA.INICIO + 1) + N.NUM AS FECHA_FALT
                FROM CREDITOS_ACTIVOS CA
                CROSS JOIN CTE_Numero N
                WHERE 1=1
                AND (CA.INICIO + 1) + N.NUM <= LEAST(TRUNC(SYSDATE) - 1, CA.FIN)
                AND (CA.INICIO + 1) + N.NUM >= CA.INICIO
                AND NOT EXISTS (
                    SELECT 1 FROM ESIACOM.DEVENGO_DIARIO DD
                    WHERE DD.CDGCLNS = CA.CDGNS AND DD.CICLO = CA.CICLO
                    AND TRUNC(DD.FECHA_CALC) = (CA.INICIO + 1) + N.NUM
                )
                $filtroCte
            )
            SELECT F.CDGNS AS CREDITO, F.CICLO,
                TO_CHAR(F.FECHA_FALT, 'DD/MM/YYYY') AS FECHA_FALTANTE,
                TO_CHAR(F.FECHA_FALT, 'DD/MM/YYYY') AS FECHA_CALC,
                NS.NOMBRE
            FROM CTE_Faltantes F
            INNER JOIN NS ON NS.CODIGO = F.CDGNS AND NS.CDGEM = 'EMPFIN'
            ORDER BY F.CDGNS, F.CICLO, F.FECHA_FALT";

        try {
            $db = new Database();
            $stmt = $db->db_activa->prepare($qry);
            $stmt->execute($prm);
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return self::Responde(true, 'Consulta exitosa', is_array($res) ? $res : []);
        } catch (\PDOException $e) {
            return self::Responde(false, 'Error al consultar devengos faltantes: ' . $e->getMessage(), null, $e->getMessage());
        } catch (\Throwable $e) {
            return self::Responde(false, 'Error al consultar devengos faltantes', null, $e->getMessage());
        }
    }

    /**
     * Procesamiento individual: transacción, validaciones, SP, bitácora.
     */
    public static function ProcesarDevengoIndividual(
        string $credito,
        string $ciclo,
        ?string $fechaCorte,
        string $usuario,
        string $perfil,
        string $ip,
        string $tipoEjecucion = 'INDIVIDUAL'
    ): array {
        $db = new Database();

        try {
            $db->AutoCommitOff();
            $db->IniciaTransaccion();

            $r = self::ValidarCreditoExiste($db, $credito);
            if (!$r['success']) {
                throw new \Exception($r['mensaje']);
            }

            $r = self::ValidarCicloExiste($db, $credito, $ciclo);
            if (!$r['success']) {
                throw new \Exception($r['mensaje']);
            }

            $r = self::ValidarFechaLiquida($db, $credito, $ciclo);
            if (!$r['success']) {
                throw new \Exception($r['mensaje']);
            }

            self::ObtenerBloqueo($db, $credito, $ciclo);

            $fechaCorteOracle = $fechaCorte !== null && $fechaCorte !== ''
                ? ($fechaCorte < date('Y-m-d') ? $fechaCorte : date('Y-m-d'))
                : date('Y-m-d');

            $insertados = self::InsertarDevengosFaltantes($db, $credito, $ciclo, $usuario, $fechaCorteOracle);

            // Preparar mensaje según el resultado
            if ($insertados > 0) {
                $mensajeResultado = "Devengo procesado correctamente. Registros insertados: $insertados.";
            } elseif ($insertados === -1) {
                $mensajeResultado = "Devengo procesado correctamente. Se procesaron devengos de fechas anteriores.";
            } elseif ($insertados === -2) {
                $mensajeResultado = "Devengo procesado correctamente (validación con error, pero SP ejecutado).";
            } else {
                $mensajeResultado = "Devengo procesado correctamente. No se insertaron nuevos registros.";
            }

            self::InsertarBitacora($db, $credito, $ciclo, $fechaCorteOracle, $tipoEjecucion, $usuario, $perfil, 'OK', null, $ip);

            $db->ConfirmaTransaccion();
            return self::Responde(true, $mensajeResultado);
        } catch (\Throwable $e) {
            $db->CancelaTransaccion();
            $msg = $e->getMessage();
            $logPath = defined('APPPATH') ? APPPATH . '/../logs/auditoria_devengo_proceso.log' : __DIR__ . '/../../logs/auditoria_devengo_proceso.log';
            if (strpos($msg, 'ORA-00054') !== false) {
                @file_put_contents($logPath, date('c') . " [SP] BLOQUEO ORA-00054 detectado: $msg\n", FILE_APPEND);
            }
            $fechaLog = $fechaCorte ?? date('Y-m-d');
            try {
                self::InsertarBitacoraLog($credito, $ciclo, $fechaLog, $tipoEjecucion, $usuario, $perfil, 'ERROR', $msg, $ip);
            } catch (\Throwable $ignored) {
            }
            return self::Responde(false, $msg, null, $msg);
        }
    }

    /**
     * Procesamiento masivo: transacción única, commit solo si todos OK.
     */
    public static function ProcesarDevengoMasivo(array $registros, string $usuario, string $perfil, string $ip): array
    {
        $db = new Database();

        try {
            $db->AutoCommitOff();
            $db->IniciaTransaccion();

            $procesados = 0;
            foreach ($registros as $item) {
                $credito = trim((string) ($item['credito'] ?? ''));
                $ciclo = trim((string) ($item['ciclo'] ?? ''));
                $fechaCorte = isset($item['fecha_corte']) && $item['fecha_corte'] !== ''
                    ? trim((string) $item['fecha_corte'])
                    : null;

                if ($credito === '' || $ciclo === '') {
                    throw new \Exception("Registro inválido: crédito y ciclo obligatorios.");
                }

                $r = self::ValidarCreditoExiste($db, $credito);
                if (!$r['success']) throw new \Exception("Crédito $credito: " . $r['mensaje']);

                $r = self::ValidarCicloExiste($db, $credito, $ciclo);
                if (!$r['success']) throw new \Exception("Crédito $credito ciclo $ciclo: " . $r['mensaje']);

                $r = self::ValidarTieneDevengosFaltantes($db, $credito, $ciclo);
                if (!$r['success']) throw new \Exception("Crédito $credito ciclo $ciclo: " . $r['mensaje']);

                $r = self::ValidarFechaLiquida($db, $credito, $ciclo);
                if (!$r['success']) throw new \Exception("Crédito $credito ciclo $ciclo: " . $r['mensaje']);

                self::ObtenerBloqueo($db, $credito, $ciclo);

                $fechaCorteOracle = $fechaCorte !== null && $fechaCorte !== ''
                    ? ($fechaCorte < date('Y-m-d') ? $fechaCorte : date('Y-m-d'))
                    : date('Y-m-d');

                self::InsertarDevengosFaltantes($db, $credito, $ciclo, $usuario, $fechaCorteOracle);
                self::InsertarBitacora($db, $credito, $ciclo, $fechaCorteOracle, 'MASIVO', $usuario, $perfil, 'OK', null, $ip);
                $procesados++;
            }

            $db->ConfirmaTransaccion();
            return self::Responde(true, "Se procesaron $procesados registro(s) correctamente.", ['procesados' => $procesados]);
        } catch (\Throwable $e) {
            $db->CancelaTransaccion();
            return self::Responde(false, $e->getMessage(), null, $e->getMessage());
        }
    }

    /**
     * Valida que el crédito exista en PRN.
     */
    public static function ValidarCreditoExiste(\Core\Database $db, string $credito): array
    {
        $qry = "SELECT COUNT(*) AS CNT FROM PRN WHERE CDGNS = :credito AND CDGEM = 'EMPFIN'";
        $res = $db->queryOne($qry, ['credito' => $credito]);
        $cnt = (int) ($res['CNT'] ?? 0);
        if ($cnt === 0) {
            return self::Responde(false, 'El crédito no existe.');
        }
        return self::Responde(true, 'OK');
    }

    /**
     * Valida que el ciclo exista para el crédito.
     */
    public static function ValidarCicloExiste(\Core\Database $db, string $credito, string $ciclo): array
    {
        $qry = "SELECT COUNT(*) AS CNT FROM PRN WHERE CDGNS = :credito AND CICLO = :ciclo AND CDGEM = 'EMPFIN'";
        $res = $db->queryOne($qry, ['credito' => $credito, 'ciclo' => $ciclo]);
        $cnt = (int) ($res['CNT'] ?? 0);
        if ($cnt === 0) {
            return self::Responde(false, 'El ciclo no existe para este crédito.');
        }
        return self::Responde(true, 'OK');
    }

    /**
     * Valida FECHA_LIQUIDA: si existe, no permitir procesar (crédito ya liquidado).
     */
    public static function ValidarFechaLiquida(\Core\Database $db, string $credito, string $ciclo): array
    {
        $qry = <<<SQL
            SELECT MAX(FECHA_LIQUIDA) AS FECHA_LIQUIDA
            FROM TBL_CIERRE_DIA
            WHERE CDGCLNS = :credito AND CICLO = :ciclo AND FECHA_LIQUIDA IS NOT NULL
SQL;
        $res = $db->queryOne($qry, ['credito' => $credito, 'ciclo' => $ciclo]);
        $fechaLiquida = $res['FECHA_LIQUIDA'] ?? null;
        if ($fechaLiquida !== null) {
            return self::Responde(false, 'No se puede procesar: el crédito ya ha sido liquidado (FECHA_LIQUIDA existe).');
        }
        return self::Responde(true, 'OK');
    }

    /**
     * Valida que existan devengos faltantes (al menos un día sin registro).
     */
    public static function ValidarTieneDevengosFaltantes(\Core\Database $db, string $credito, string $ciclo): array
    {
        $qry = <<<SQL
            SELECT COUNT(*) AS CNT
            FROM CREDITOS_ACTIVOS CA
            CROSS JOIN (
                SELECT LEVEL - 1 AS NUM
                FROM DUAL
                CONNECT BY LEVEL <= (
                    SELECT NVL(MAX(LEAST(TRUNC(SYSDATE) - 1, FIN) - (INICIO + 1)) + 1, 1)
                    FROM CREDITOS_ACTIVOS
                )
            ) N
            WHERE CA.CDGNS = :credito AND CA.CICLO = :ciclo
            AND (CA.INICIO + 1) + N.NUM <= LEAST(TRUNC(SYSDATE) - 1, CA.FIN)
            AND NOT EXISTS (
                SELECT 1 FROM ESIACOM.DEVENGO_DIARIO DD
                WHERE DD.CDGCLNS = CA.CDGNS AND DD.CICLO = CA.CICLO
                AND TRUNC(DD.FECHA_CALC) = (CA.INICIO + 1) + N.NUM
            )
SQL;
        $res = $db->queryOne($qry, ['credito' => $credito, 'ciclo' => $ciclo]);
        $cnt = (int) ($res['CNT'] ?? 0);
        if ($cnt === 0) {
            return self::Responde(false, 'No hay devengos faltantes para este crédito/ciclo.');
        }
        return self::Responde(true, 'OK');
    }

    /**
     * Bloqueo lógico: SELECT FOR UPDATE sobre PRN para evitar ejecución simultánea.
     */
    public static function ObtenerBloqueo(\Core\Database $db, string $credito, string $ciclo): void
    {
        $qry = "SELECT CDGNS, CICLO FROM PRN WHERE CDGNS = :credito AND CICLO = :ciclo AND CDGEM = 'EMPFIN' FOR UPDATE";
        $db->queryOne($qry, ['credito' => $credito, 'ciclo' => $ciclo]);
    }

    /**
     * Ejecuta ESIACOM.SP_AUDITA_DEVENGO con solo los 3 parámetros obligatorios.
     * No envía fecha de corte; el SP usa su DEFAULT (SYSDATE).
     * Tras el SP ejecuta una consulta simple de conteo; si TOTAL >= 0 se considera éxito.
     */
    public static function EjecutarSPAuditaDevengo(
        \Core\Database $db,
        string $credito,
        string $ciclo,
        string $usuario
    ): void {
        $plsql = "BEGIN ESIACOM.SP_AUDITA_DEVENGO(:p_credito, :p_ciclo, :p_usuario); END;";
        $stmt = $db->db_activa->prepare($plsql);
        $stmt->bindValue(':p_credito', $credito, \PDO::PARAM_STR);
        $stmt->bindValue(':p_ciclo', $ciclo, \PDO::PARAM_STR);
        $stmt->bindValue(':p_usuario', $usuario, \PDO::PARAM_STR);
        $stmt->execute();

        $valStmt = $db->db_activa->prepare("
            SELECT COUNT(*) AS TOTAL
            FROM ESIACOM.DEVENGO_DIARIO
            WHERE CDGCLNS = :credito AND CICLO = :ciclo
        ");
        $valStmt->bindValue(':credito', $credito, \PDO::PARAM_STR);
        $valStmt->bindValue(':ciclo', $ciclo, \PDO::PARAM_STR);
        $valStmt->execute();
        $r = $valStmt->fetch(\PDO::FETCH_ASSOC);
        $total = (int) ($r['TOTAL'] ?? 0);
        // TOTAL >= 0: ejecución exitosa (no se valida inserciones ni FREGISTRO).
    }

    /**
     * Inserta en BITACORA_AUDITORIA_DEVENGO (dentro de transacción).
     */
    public static function InsertarBitacora(
        \Core\Database $db,
        string $credito,
        string $ciclo,
        string $fechaProcesada,
        string $tipoEjecucion,
        string $usuario,
        string $perfil,
        string $resultado,
        ?string $mensajeError,
        string $ip
    ): void {
        $qry = <<<SQL
            INSERT INTO BITACORA_AUDITORIA_DEVENGO (
                CREDITO, CICLO, FECHA_PROCESADA, TIPO_EJECUCION, USUARIO, PERFIL,
                FECHA_EJECUCION, RESULTADO, MENSAJE_ERROR, IP
            ) VALUES (
                :credito, :ciclo, TO_DATE(:fecha_procesada, 'YYYY-MM-DD'), :tipo_ejecucion,
                :usuario, :perfil, SYSDATE, :resultado, :mensaje_error, :ip
            )
SQL;
        $db->insertar($qry, [
            'credito' => $credito,
            'ciclo' => $ciclo,
            'fecha_procesada' => $fechaProcesada,
            'tipo_ejecucion' => $tipoEjecucion,
            'usuario' => $usuario,
            'perfil' => $perfil,
            'resultado' => $resultado,
            'mensaje_error' => $mensajeError ?? '',
            'ip' => $ip,
        ]);
    }

    /**
     * Inserta en bitácora fuera de transacción (en catch, cuando ya se hizo rollback).
     */
    public static function InsertarBitacoraLog(
        string $credito,
        string $ciclo,
        string $fechaProcesada,
        string $tipoEjecucion,
        string $usuario,
        string $perfil,
        string $resultado,
        string $mensajeError,
        string $ip
    ): void {
        $db = new Database();
        self::InsertarBitacora($db, $credito, $ciclo, $fechaProcesada, $tipoEjecucion, $usuario, $perfil, $resultado, $mensajeError, $ip);
    }
}
