<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class Herramientas extends Model
{
    /**
     * Normaliza una fecha a Y-m-d. Acepta Y-m-d o DD/MM/YYYY.
     *
     * @param string|null $valor
     * @return string|null
     */
    private static function normalizarFechaYmd($valor)
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }
        $valor = trim($valor);
        $d = \DateTime::createFromFormat('Y-m-d', $valor);
        if ($d) {
            return $d->format('Y-m-d');
        }
        $d = \DateTime::createFromFormat('d/m/Y', $valor);
        if ($d) {
            return $d->format('Y-m-d');
        }
        $ts = strtotime(str_replace('/', '-', $valor));
        return $ts !== false ? date('Y-m-d', $ts) : null;
    }

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
     * Devengos faltantes: misma lógica y universo que SP_AUDITA_DEVENGO (sin llamar al SP).
     * Base: PRN + MP (TIPO='IN') + CF. FACTOR_DIAS, FIN, DEVENGO_DIARIO, calendario INICIO+1 hasta CORTE_EFECTIVA,
     * ajuste último día, detección por NOT EXISTS en ESIACOM.DEVENGO_DIARIO (CDGEM, CDGCLNS, CICLO, TRUNC(FECHA_CALC)).
     * Exclusión por FECHA_LIQUIDA aplicada al final (no altera universo base).
     *
     * @param array $datos ['credito','ciclo','fecha_desde','fecha_hasta','fecha_corte']
     * @return array { success, mensaje, datos }
     */
    public static function GetDevengosFaltantes($datos = [])
    {
        $credito = !empty(trim((string) ($datos['credito'] ?? ''))) ? trim($datos['credito']) : null;
        $ciclo = !empty(trim((string) ($datos['ciclo'] ?? ''))) ? trim($datos['ciclo']) : null;
        $fechaCorte = !empty(trim((string) ($datos['fecha_corte'] ?? ''))) ? trim($datos['fecha_corte']) : null;


        if ($fechaCorte === null) {
            $fechaCorte = date('Y-m-d');
        }
        $hoy = date('Y-m-d');
        if ($fechaCorte > $hoy) {
            $fechaCorte = $hoy;
        }

        $filtroExtra = '';
        $prm = ['fecha_corte' => $fechaCorte];
        if ($credito !== null) {
            $filtroExtra .= ' AND CF.CREDITO = :credito';
            $prm['credito'] = $credito;
        }
        if ($ciclo !== null) {
            $filtroExtra .= ' AND CF.CICLO = :ciclo';
            $prm['ciclo'] = $ciclo;
        }

        $qry = <<<SQL
WITH DATOS_BASE AS (
    SELECT
        PRN.CDGNS AS CREDITO,
        PRN.CICLO,
        PRN.CDGEM,
        TRUNC(PRN.INICIO) AS INICIO,
        PRN.PLAZO,
        NVL(PRN.PERIODICIDAD, 'S') AS PERIODICIDAD,
        DECODE(NVL(PRN.PERIODICIDAD, 'S'), 'S', 7, 'C', 14, 'Q', 15, 'M', 30, 7) AS FACTOR_DIAS,
        TRUNC(PRN.INICIO + (DECODE(NVL(PRN.PERIODICIDAD, 'S'), 'S', 7, 'C', 14, 'Q', 15, 'M', 30, 7) * PRN.PLAZO)) AS FIN,
        ABS(APagarInteresPrN(
            PRN.CDGEM, PRN.CDGNS, PRN.CICLO,
            NVL(PRN.CANTENTRE, PRN.CANTAUTOR), PRN.TASA, PRN.PLAZO, PRN.PERIODICIDAD,
            PRN.CDGMCI, PRN.INICIO, PRN.DIAJUNTA, PRN.MULTPER, PRN.PERIGRCAP, PRN.PERIGRINT,
            PRN.DESFASEPAGO, PRN.CDGTI
        )) AS DEVENGO_TOTAL
    FROM PRN
    INNER JOIN MP ON PRN.CDGEM = MP.CDGEM AND PRN.CDGNS = MP.CDGCLNS AND PRN.CICLO = MP.CICLO AND MP.TIPO = 'IN'
    LEFT JOIN CF ON PRN.CDGEM = CF.CDGEM AND PRN.CDGFDI = CF.CDGFDI
),
RNG AS (
    SELECT LEVEL - 1 AS N FROM DUAL CONNECT BY LEVEL <= 3660
),
CALENDARIO AS (
    SELECT
        DB.CREDITO,
        DB.CICLO,
        DB.CDGEM,
        DB.INICIO,
        DB.FIN,
        DB.DEVENGO_TOTAL,
        (DB.FACTOR_DIAS * DB.PLAZO) AS PLAZO_DIAS,
        ROUND(DB.DEVENGO_TOTAL / NULLIF(DB.FACTOR_DIAS * DB.PLAZO, 0), 2) AS DEVENGO_DIARIO_FIJO,
        TRUNC(DB.INICIO) + 1 + RNG.N AS FECHA_CALC
    FROM DATOS_BASE DB
    CROSS JOIN RNG
    WHERE TRUNC(DB.INICIO) + 1 + RNG.N <= LEAST(TO_DATE(:fecha_corte, 'YYYY-MM-DD'), TRUNC(SYSDATE), DB.FIN)
    AND TRUNC(DB.INICIO) + 1 + RNG.N >= TRUNC(DB.INICIO) + 1
),
CON_ACUM AS (
    SELECT
        C.CREDITO,
        C.CICLO,
        C.CDGEM,
        C.INICIO,
        C.FIN,
        C.FECHA_CALC,
        C.DEVENGO_TOTAL,
        C.DEVENGO_DIARIO_FIJO,
        C.PLAZO_DIAS,
        COUNT(*) OVER (PARTITION BY C.CREDITO, C.CICLO ORDER BY C.FECHA_CALC ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING) AS NUM_PREV
    FROM CALENDARIO C
),
CALCULO_FINAL AS (
    SELECT
        CA.CREDITO,
        CA.CICLO,
        CA.CDGEM,
        CA.FECHA_CALC,
        CASE
            WHEN TRUNC(CA.FECHA_CALC) = TRUNC(CA.FIN)
            THEN CA.DEVENGO_TOTAL - NVL(CA.NUM_PREV, 0) * CA.DEVENGO_DIARIO_FIJO
            ELSE CA.DEVENGO_DIARIO_FIJO
        END AS DEV_DIARIO
    FROM CON_ACUM CA
)
SELECT
    CF.CREDITO,
    CF.CICLO,
    TO_CHAR(CF.FECHA_CALC, 'DD/MM/YYYY') AS FECHA_FALTANTE,
    TO_CHAR(CF.FECHA_CALC, 'DD/MM/YYYY') AS FECHA_CALC,
    TO_CHAR(CF.FECHA_CALC, 'YYYY-MM-DD') AS FECHA_CALC_ISO,
    NS.NOMBRE
FROM CALCULO_FINAL CF
LEFT JOIN NS ON NS.CODIGO = CF.CREDITO AND NS.CDGEM = CF.CDGEM
WHERE NOT EXISTS (
    SELECT 1
    FROM ESIACOM.DEVENGO_DIARIO DD
    WHERE DD.CDGCLNS = CF.CREDITO
    AND DD.CICLO = CF.CICLO
    AND DD.CDGEM = CF.CDGEM
    AND TRUNC(DD.FECHA_CALC) = TRUNC(CF.FECHA_CALC)
)
AND NOT EXISTS (
    SELECT 1 FROM TBL_CIERRE_DIA TCD
    WHERE TCD.CDGCLNS = CF.CREDITO AND TCD.CICLO = CF.CICLO AND TCD.FECHA_LIQUIDA IS NOT NULL
)
{$filtroExtra}
ORDER BY CF.CREDITO, CF.CICLO, CF.FECHA_CALC
SQL;

        try {
            $db = new Database();
            $stmt = $db->db_activa->prepare($qry);
            $stmt->execute($prm);
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $res = is_array($res) ? $res : [];
            try {
                $res = self::EnriquecerFilasConDatosInsertar($db, $res);
            } catch (\Throwable $e) {
                $logPath = defined('APPPATH') ? APPPATH . '/../logs/auditoria_devengo_proceso.log' : __DIR__ . '/../../logs/auditoria_devengo_proceso.log';
                @file_put_contents($logPath, date('c') . " [GetDevengosFaltantes] EnriquecerFilasConDatosInsertar: " . $e->getMessage() . "\n", FILE_APPEND);
            }
            return self::Responde(true, 'Consulta exitosa', $res);
        } catch (\PDOException $e) {
            return self::Responde(false, 'Error al consultar devengos faltantes: ' . $e->getMessage(), null, $e->getMessage());
        } catch (\Throwable $e) {
            return self::Responde(false, 'Error al consultar devengos faltantes', null, $e->getMessage());
        }
    }

    /**
     * Procesamiento individual: recibe una fila con todos los datos para el INSERT (la misma que devuelve la consulta de faltantes).
     * Valida crédito/ciclo/FECHA_LIQUIDA, inserta la fila con INSERT simple y registra en bitácora.
     *
     * @param array $fila Una fila con CREDITO, CICLO y todos los campos para INSERT (FECHA_CALC_ISO, DEV_DIARIO, etc.)
     */
    public static function ProcesarDevengoIndividual(
        array $fila,
        string $usuario,
        string $perfil,
        string $ip,
        string $tipoEjecucion = 'INDIVIDUAL'
    ): array {
        $credito = trim((string) ($fila['CREDITO'] ?? $fila['CDGCLNS'] ?? $fila['credito'] ?? ''));
        $ciclo = trim((string) ($fila['CICLO'] ?? $fila['ciclo'] ?? ''));
        $db = new Database();

        try {
            $db->AutoCommitOff();
            $db->IniciaTransaccion();

            if ($credito === '' || $ciclo === '') {
                throw new \Exception('Crédito y ciclo son obligatorios.');
            }

            $r = self::ValidarCreditoExiste($db, $credito);
            if (!$r['success']) throw new \Exception($r['mensaje']);

            $r = self::ValidarCicloExiste($db, $credito, $ciclo);
            if (!$r['success']) throw new \Exception($r['mensaje']);

            $r = self::ValidarFechaLiquida($db, $credito, $ciclo);
            if (!$r['success']) throw new \Exception($r['mensaje']);

            self::ObtenerBloqueo($db, $credito, $ciclo);

            $insertados = self::InsertarFilasDevengo($db, [$fila], $usuario);
            $fechaCorte = trim((string) ($fila['FECHA_CALC_ISO'] ?? $fila['FECHA_CALC'] ?? date('Y-m-d')));

            self::InsertarBitacora($db, $credito, $ciclo, $fechaCorte, $tipoEjecucion, $usuario, $perfil, 'OK', null, $ip);
            $db->ConfirmaTransaccion();

            $mensaje = $insertados > 0 ? "$insertados devengos procesados correctamente" : "No había devengos pendientes";
            return [
                'success' => true,
                'mensaje' => $mensaje,
                'insertados' => $insertados,
                'credito' => $credito,
                'ciclo' => $ciclo
            ];
        } catch (\Throwable $e) {
            $db->CancelaTransaccion();
            $msg = $e->getMessage();
            $logPath = defined('APPPATH') ? APPPATH . '/../logs/auditoria_devengo_proceso.log' : __DIR__ . '/../../logs/auditoria_devengo_proceso.log';
            if (strpos($msg, 'ORA-00054') !== false) {
                @file_put_contents($logPath, date('c') . " [INSERT] BLOQUEO ORA-00054: $msg\n", FILE_APPEND);
            }
            $fechaLog = $fila['FECHA_CALC_ISO'] ?? $fila['FECHA_CALC'] ?? date('Y-m-d');
            try {
                self::InsertarBitacoraLog($credito, $ciclo, $fechaLog, $tipoEjecucion, $usuario, $perfil, 'ERROR', $msg, $ip);
            } catch (\Throwable $ignored) {
            }
            return [
                'success' => false,
                'mensaje' => 'Error al procesar el crédito',
                'insertados' => 0,
                'credito' => $credito,
                'ciclo' => $ciclo
            ];
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

            $paresValidados = [];
            foreach ($registros as $fila) {
                $credito = trim((string) ($fila['CREDITO'] ?? $fila['CDGCLNS'] ?? $fila['credito'] ?? ''));
                $ciclo = trim((string) ($fila['CICLO'] ?? $fila['ciclo'] ?? ''));
                if ($credito === '' || $ciclo === '') {
                    throw new \Exception("Registro inválido: crédito y ciclo obligatorios.");
                }
                $key = $credito . '|' . $ciclo;
                if (!isset($paresValidados[$key])) {
                    $r = self::ValidarCreditoExiste($db, $credito);
                    if (!$r['success']) throw new \Exception("Crédito $credito: " . $r['mensaje']);
                    $r = self::ValidarCicloExiste($db, $credito, $ciclo);
                    if (!$r['success']) throw new \Exception("Crédito $credito ciclo $ciclo: " . $r['mensaje']);
                    $r = self::ValidarFechaLiquida($db, $credito, $ciclo);
                    if (!$r['success']) throw new \Exception("Crédito $credito ciclo $ciclo: " . $r['mensaje']);
                    self::ObtenerBloqueo($db, $credito, $ciclo);
                    $paresValidados[$key] = true;
                }
            }

            $insertados = self::InsertarFilasDevengo($db, $registros, $usuario);

            foreach (array_keys($paresValidados) as $key) {
                list($credito, $ciclo) = explode('|', $key, 2);
                self::InsertarBitacora($db, $credito, $ciclo, date('Y-m-d'), 'MASIVO', $usuario, $perfil, 'OK', null, $ip);
            }

            $db->ConfirmaTransaccion();

            $mensaje = $insertados > 0 ? "$insertados devengos procesados correctamente" : "No había devengos pendientes";

            // Extraer lista de créditos procesados
            $creditosProcesados = [];
            foreach (array_keys($paresValidados) as $key) {
                list($credito, $ciclo) = explode('|', $key, 2);
                $creditosProcesados[] = ['credito' => $credito, 'ciclo' => $ciclo];
            }

            return [
                'success' => true,
                'mensaje' => $mensaje,
                'insertados' => $insertados,
                'creditosProcesados' => $creditosProcesados,
                'credito' => '', // Para masivo no hay un crédito específico
                'ciclo' => ''    // Para masivo no hay un ciclo específico
            ];
        } catch (\Throwable $e) {
            $db->CancelaTransaccion();
            return [
                'success' => false,
                'mensaje' => 'Error al procesar los créditos',
                'insertados' => 0,
                'credito' => '',
                'ciclo' => ''
            ];
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
     * Obtiene datos base para calcular e insertar devengos (lógica equivalente a SP_AUDITA_DEVENGO).
     * Fuentes: PRN, MP (TIPO='IN'), CF. FACTOR_DIAS por PERIODICIDAD, PLAZO_DIAS, FIN, DEVENGO_TOTAL, IVA.
     *
     * @return array|null Una fila con INICIO, PLAZO, PERIODICIDAD, CDGEM, CDGPE, FACTOR_DIAS, PLAZO_DIAS, FIN_TS, DEVENGO_TOTAL, IVA; null si no hay datos.
     */
    public static function ObtenerDatosBaseDevengo(\Core\Database $db, string $credito, string $ciclo): ?array
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(TRUNC(PRN.INICIO), 'YYYY-MM-DD') AS INICIO,
                PRN.PLAZO,
                NVL(PRN.PERIODICIDAD, 'S') AS PERIODICIDAD,
                NVL(PRN.CDGEM, 'EMPFIN') AS CDGEM,
                NVL(PRN.CDGOCPE, 'AMGM') AS CDGPE,
                DECODE(NVL(PRN.PERIODICIDAD, 'S'), 'S', 7, 'C', 14, 'Q', 15, 'M', 30, 7) AS FACTOR_DIAS,
                (DECODE(NVL(PRN.PERIODICIDAD, 'S'), 'S', 7, 'C', 14, 'Q', 15, 'M', 30, 7) * PRN.PLAZO) AS PLAZO_DIAS,
                TO_CHAR(TRUNC(PRN.INICIO + (DECODE(NVL(PRN.PERIODICIDAD, 'S'), 'S', 7, 'C', 14, 'Q', 15, 'M', 30, 7) * PRN.PLAZO)), 'YYYY-MM-DD') AS FIN_TS,
                ABS(APagarInteresPrN(
                    PRN.CDGEM, PRN.CDGNS, PRN.CICLO,
                    NVL(PRN.CANTENTRE, PRN.CANTAUTOR), PRN.TASA, PRN.PLAZO, PRN.PERIODICIDAD,
                    PRN.CDGMCI, PRN.INICIO, PRN.DIAJUNTA, PRN.MULTPER, PRN.PERIGRCAP, PRN.PERIGRINT,
                    PRN.DESFASEPAGO, PRN.CDGTI
                )) AS DEVENGO_TOTAL,
                0.16 AS IVA
            FROM PRN
            INNER JOIN MP ON PRN.CDGEM = MP.CDGEM AND PRN.CDGNS = MP.CDGCLNS AND PRN.CICLO = MP.CICLO AND MP.TIPO = 'IN'
            LEFT JOIN CF ON PRN.CDGEM = CF.CDGEM AND PRN.CDGFDI = CF.CDGFDI
            WHERE PRN.CDGNS = :credito AND PRN.CICLO = :ciclo AND PRN.CDGEM = 'EMPFIN'
        SQL;
        try {
            $row = $db->queryOne($qry, ['credito' => $credito, 'ciclo' => $ciclo]);
            if (!$row || empty($row)) {
                return null;
            }
            return $row;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * A partir de las filas de devengos faltantes (CREDITO, CICLO, FECHA_CALC_ISO, ...), agrupa por (credito, ciclo),
     * obtiene datos base y calcula por cada fila: DEV_DIARIO (ajuste último día), INT_DEV acumulado, DIAS_DEV,
     * DEV_DIARIO_SIN_IVA, IVA_INT. Añade a cada fila todos los campos necesarios para el INSERT en DEVENGO_DIARIO.
     *
     * @param \Core\Database $db
     * @param array $filas Lista con al menos CREDITO, CICLO, FECHA_CALC_ISO
     * @return array Las mismas filas con campos añadidos para insertar
     */
    public static function EnriquecerFilasConDatosInsertar(\Core\Database $db, array $filas): array
    {
        if (empty($filas)) {
            return $filas;
        }
        $filas = array_values($filas);
        $grupos = [];
        foreach ($filas as $idx => $f) {
            $f = array_change_key_case((array) $f, CASE_UPPER);
            $filas[$idx] = $f;
            $c = trim((string) ($f['CREDITO'] ?? ''));
            $cic = trim((string) ($f['CICLO'] ?? ''));
            $key = $c . '|' . $cic;
            if (!isset($grupos[$key])) {
                $grupos[$key] = ['credito' => $c, 'ciclo' => $cic, 'indices' => []];
            }
            $grupos[$key]['indices'][] = $idx;
        }

        foreach ($grupos as $key => $g) {
            $base = self::ObtenerDatosBaseDevengo($db, $g['credito'], $g['ciclo']);
            if (!$base || !is_array($base)) {
                continue;
            }
            $base = array_change_key_case($base, CASE_UPPER);
            $plazoDias = (int) ($base['PLAZO_DIAS'] ?? 0);
            $devengoTotal = (float) $base['DEVENGO_TOTAL'];
            $iva = (float) $base['IVA'];
            $inicioStr = is_string($base['INICIO']) ? $base['INICIO'] : date('Y-m-d', strtotime($base['INICIO']));
            $finStr = is_string($base['FIN_TS']) ? $base['FIN_TS'] : date('Y-m-d', strtotime($base['FIN_TS']));
            $cdgem = $base['CDGEM'] ?? 'EMPFIN';
            $cdgpe = $base['CDGPE'] ?? 'AMGM';
            $periodicidad = $base['PERIODICIDAD'] ?? 'S';
            $plazo = (int) ($base['PLAZO'] ?? 0);
            $devengoDiarioFijo = $plazoDias > 0 ? round($devengoTotal / $plazoDias, 2) : 0.0;
            $acumulado = 0.0;

            $filasGrupo = [];
            foreach ($g['indices'] as $idx) {
                $filasGrupo[] = ['idx' => $idx, 'fecha' => trim((string) ($filas[$idx]['FECHA_CALC_ISO'] ?? ''))];
            }
            usort($filasGrupo, function ($a, $b) {
                return strcmp($a['fecha'], $b['fecha']);
            });

            foreach ($filasGrupo as $i => $item) {
                $idx = $item['idx'];
                $fechaCalc = $item['fecha'];
                $diasDev = (int) round((strtotime($fechaCalc . ' 12:00:00') - strtotime($inicioStr . ' 12:00:00')) / 86400);
                $esUltimoDia = ($fechaCalc === $finStr);
                if ($esUltimoDia) {
                    $devDiario = round($devengoTotal - $acumulado, 2);
                } else {
                    $devDiario = $devengoDiarioFijo;
                }
                // Asegurar que el devengo diario tenga 2 decimales como el SP
                $devDiario = round($devDiario, 2);

                // Acumulado igual que en el SP (suma anterior + diario actual)
                $acumulado = round($acumulado + $devDiario, 2);
                $devDiarioSinIva = round($devDiario / (1 + $iva), 2);
                $ivaInt = round($devDiario - $devDiarioSinIva, 2);

                $filas[$idx]['FECHA_CALC_ISO'] = $fechaCalc;
                $filas[$idx]['CDGEM'] = $cdgem;
                $filas[$idx]['CDGCLNS'] = $g['credito'];
                $filas[$idx]['CICLO'] = $g['ciclo'];
                $filas[$idx]['INICIO'] = $inicioStr;
                $filas[$idx]['DEV_DIARIO'] = $devDiario;
                $filas[$idx]['DIAS_DEV'] = $diasDev;
                $filas[$idx]['INT_DEV'] = $acumulado;
                $filas[$idx]['CDGPE'] = $cdgpe;
                $filas[$idx]['DEV_DIARIO_SIN_IVA'] = $devDiarioSinIva;
                $filas[$idx]['IVA_INT'] = $ivaInt;
                $filas[$idx]['PLAZO'] = $plazo;
                $filas[$idx]['PERIODICIDAD'] = $periodicidad;
                $filas[$idx]['PLAZO_DIAS'] = $plazoDias;
                $filas[$idx]['FIN_DEVENGO'] = $finStr;
                $filas[$idx]['ESTATUS'] = 'RE';
                $filas[$idx]['CLNS'] = 'G';
            }
        }
        return $filas;
    }

    /**
     * Enriquece una sola fila (credito, ciclo, fecha_calc_iso) con todos los datos para INSERT.
     * Usado cuando la búsqueda devolvió filas sin enriquecer.
     *
     * @return array|null Fila con todos los campos para INSERT, o null si no se pudo obtener.
     */
    public static function EnriquecerUnaFilaParaInsertar(\Core\Database $db, string $credito, string $ciclo, string $fechaCalcIso): ?array
    {
        $fila = ['CREDITO' => $credito, 'CICLO' => $ciclo, 'FECHA_CALC_ISO' => $fechaCalcIso];
        $enriquecidas = self::EnriquecerFilasConDatosInsertar($db, [$fila]);
        return isset($enriquecidas[0]) && !empty(trim((string) ($enriquecidas[0]['INICIO'] ?? ''))) ? $enriquecidas[0] : null;
    }

    /**
     * Inserta en ESIACOM.DEVENGO_DIARIO las filas recibidas (cada una con todos los campos del INSERT).
     * Si una fila no trae INICIO/DEV_DIARIO (no fue enriquecida), se enriquece en el acto antes de insertar.
     * Usa INSERT simple por fila; evita duplicados con comprobación NOT EXISTS antes de insertar.
     *
     * @return int Número de registros insertados
     */
    public static function InsertarFilasDevengo(\Core\Database $db, array $filas, string $usuario): int
    {
        if (empty($filas)) {
            return 0;
        }
        $logPath = defined('APPPATH') ? APPPATH . '/../logs/auditoria_devengo_proceso.log' : __DIR__ . '/../../logs/auditoria_devengo_proceso.log';

        for ($i = 0; $i < count($filas); $i++) {
            $f = array_change_key_case((array) $filas[$i], CASE_UPPER);
            $inicio = trim((string) ($f['INICIO'] ?? ''));
            if ($inicio !== '') {
                continue;
            }
            $fechaCalc = trim((string) ($f['FECHA_CALC_ISO'] ?? $f['FECHA_CALC'] ?? ''));
            $credito = trim((string) ($f['CDGCLNS'] ?? $f['CREDITO'] ?? ''));
            $ciclo = trim((string) ($f['CICLO'] ?? ''));
            if ($fechaCalc === '' || $credito === '' || $ciclo === '') {
                continue;
            }
            try {
                $enriquecida = self::EnriquecerUnaFilaParaInsertar($db, $credito, $ciclo, $fechaCalc);
                if ($enriquecida !== null) {
                    $filas[$i] = $enriquecida;
                }
            } catch (\Throwable $e) {
                @file_put_contents($logPath, date('c') . " [InsertarFilasDevengo] Enriquecer $credito/$ciclo/$fechaCalc: " . $e->getMessage() . "\n", FILE_APPEND);
            }
        }

        try {
            $schemaStmt = $db->db_activa->prepare("SELECT SYS_CONTEXT('USERENV','CURRENT_SCHEMA') AS SCH FROM DUAL");
            $schemaStmt->execute();
            $row = $schemaStmt->fetch(\PDO::FETCH_ASSOC);
            $currentSchema = $row['SCH'] ?? '';
            if (strtoupper((string) $currentSchema) !== 'ESIACOM') {
                $db->db_activa->exec("ALTER SESSION SET CURRENT_SCHEMA = ESIACOM");
            }
        } catch (\Throwable $e) {
            @file_put_contents($logPath, date('c') . " [InsertarFilasDevengo] Error esquema: " . $e->getMessage() . "\n", FILE_APPEND);
            throw new \Exception("Error al cambiar esquema a ESIACOM: " . $e->getMessage());
        }

        $sqlInsert = <<<SQL
            INSERT INTO ESIACOM.DEVENGO_DIARIO (
                FECHA_CALC, CDGEM, CDGCLNS, CICLO, INICIO, DEV_DIARIO, DIAS_DEV, INT_DEV,
                CDGPE, FREGISTRO, DEV_DIARIO_SIN_IVA, IVA_INT, PLAZO, PERIODICIDAD, PLAZO_DIAS, FIN_DEVENGO, ESTATUS, CLNS
            ) VALUES (
                TO_TIMESTAMP(:fecha_calc || ' 00:00:00', 'YYYY-MM-DD HH24:MI:SS'),
                :cdgem, :cdgclns, :ciclo, TO_DATE(:inicio, 'YYYY-MM-DD'), :dev_diario, :dias_dev, :int_dev,
                :cdgpe, SYSTIMESTAMP, :dev_diario_sin_iva, :iva_int, :plazo, :periodicidad, :plazo_dias,
                TO_DATE(:fin_devengo, 'YYYY-MM-DD'), 'RE', 'G'
            )
        SQL;
        $stmtInsert = $db->db_activa->prepare($sqlInsert);
        $stmtCheck = $db->db_activa->prepare(
            "SELECT 1 FROM ESIACOM.DEVENGO_DIARIO WHERE CDGCLNS = :c AND CICLO = :cic AND TRUNC(FECHA_CALC) = TO_DATE(:f, 'YYYY-MM-DD')"
        );
        $insertados = 0;
        foreach ($filas as $f) {
            $f = array_change_key_case((array) $f, CASE_UPPER);
            $fechaCalc = trim((string) ($f['FECHA_CALC_ISO'] ?? $f['FECHA_CALC'] ?? ''));
            $credito = trim((string) ($f['CDGCLNS'] ?? $f['CREDITO'] ?? ''));
            $ciclo = trim((string) ($f['CICLO'] ?? ''));
            if ($fechaCalc === '' || $credito === '' || $ciclo === '') {
                continue;
            }
            try {
                $stmtCheck->execute(['c' => $credito, 'cic' => $ciclo, 'f' => $fechaCalc]);
                if ($stmtCheck->fetch()) {
                    continue;
                }
            } catch (\Throwable $e) {
                continue;
            }
        $inicio = trim((string) ($f['INICIO'] ?? ''));
        if ($inicio === '') {
            continue;
        }
        $usuarioSesion = $_SESSION['usuario'] ?? 'SYSTEM';
        try {
            $stmtInsert->execute([
                'fecha_calc' => $fechaCalc,
                'cdgem' => $f['CDGEM'] ?? 'EMPFIN',
                'cdgclns' => $credito,
                'ciclo' => $ciclo,
                'inicio' => $inicio,
                'dev_diario' => (float) ($f['DEV_DIARIO'] ?? 0),
                'dias_dev' => (int) ($f['DIAS_DEV'] ?? 0),
                'int_dev' => (float) ($f['INT_DEV'] ?? 0),
                'cdgpe' => $usuarioSesion,
                'dev_diario_sin_iva' => (float) ($f['DEV_DIARIO_SIN_IVA'] ?? 0),
                'iva_int' => (float) ($f['IVA_INT'] ?? 0),
                'plazo' => (int) ($f['PLAZO'] ?? 0),
                'periodicidad' => $f['PERIODICIDAD'] ?? 'S',
                'plazo_dias' => (int) ($f['PLAZO_DIAS'] ?? 0),
                'fin_devengo' => trim((string) ($f['FIN_DEVENGO'] ?? '')),
            ]);
                $insertados++;
            } catch (\Throwable $e) {
                @file_put_contents($logPath, date('c') . " [InsertarFilasDevengo] Fila $credito/$ciclo/$fechaCalc: " . $e->getMessage() . "\n", FILE_APPEND);
            }
        }
        return $insertados;
    }

    /**
     * Devuelve las fechas (YYYY-MM-DD) que faltan en ESIACOM.DEVENGO_DIARIO para credito/ciclo desde INICIO+1 hasta LEAST(FIN, fechaCorte).
     * Respeta FECHA_LIQUIDA: no se incluyen fechas si el crédito está liquidado (ya validado antes).
     *
     * @return array Lista de fechas en formato Y-m-d
     */
    public static function ObtenerFechasFaltantesParaInsertar(
        \Core\Database $db,
        string $credito,
        string $ciclo,
        string $fechaCorte,
        $inicioTs,
        $finTs
    ): array {
        $finTsStr = $finTs instanceof \DateTimeInterface
            ? $finTs->format('Y-m-d') : date('Y-m-d', is_numeric($finTs) ? $finTs : strtotime($finTs));
        $inicioStr = $inicioTs instanceof \DateTimeInterface
            ? $inicioTs->format('Y-m-d') : date('Y-m-d', is_numeric($inicioTs) ? $inicioTs : strtotime($inicioTs));
        $corte = $fechaCorte;
        if (strtotime($finTsStr) < strtotime($corte)) {
            $corte = $finTsStr;
        }
        $qry = <<<SQL
            WITH n AS (SELECT LEVEL - 1 AS l FROM DUAL CONNECT BY LEVEL <= 3660)
            SELECT TO_CHAR(TO_DATE(:inicio, 'YYYY-MM-DD') + 1 + n.l, 'YYYY-MM-DD') AS FECHA_CALC
            FROM n
            WHERE TO_DATE(:inicio, 'YYYY-MM-DD') + 1 + n.l <= LEAST(TO_DATE(:corte, 'YYYY-MM-DD'), TO_DATE(:fin, 'YYYY-MM-DD'))
            AND NOT EXISTS (
                SELECT 1 FROM ESIACOM.DEVENGO_DIARIO DD
                WHERE DD.CDGCLNS = :credito AND DD.CICLO = :ciclo
                AND TRUNC(DD.FECHA_CALC) = TO_DATE(:inicio, 'YYYY-MM-DD') + 1 + n.l
            )
            ORDER BY n.l
        SQL;
        $params = [
            'credito' => $credito,
            'ciclo' => $ciclo,
            'inicio' => $inicioStr,
            'fin' => $finTsStr,
            'corte' => $corte,
        ];
        try {
            $rows = $db->queryAll($qry, $params);
            if (!is_array($rows)) {
                return [];
            }
            $fechas = [];
            foreach ($rows as $r) {
                if (!empty($r['FECHA_CALC'])) {
                    $fechas[] = $r['FECHA_CALC'];
                }
            }
            return $fechas;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Inserta en ESIACOM.DEVENGO_DIARIO los registros de devengo para las fechas indicadas,
     * replicando la lógica del SP: DEV_DIARIO (con ajuste último día), DIAS_DEV, INT_DEV acumulado,
     * DEV_DIARIO_SIN_IVA, IVA_INT. NOT EXISTS para evitar duplicados.
     *
     * @param array $datosBase Una fila de ObtenerDatosBaseDevengo (INICIO, PLAZO, PERIODICIDAD, CDGEM, CDGPE, FACTOR_DIAS, PLAZO_DIAS, FIN_TS, DEVENGO_TOTAL, IVA)
     * @param array $fechasOrdenadas Lista de fechas Y-m-d en orden
     * @return int Número de filas insertadas
     */
    public static function InsertarRegistrosDevengoDiario(
        \Core\Database $db,
        string $credito,
        string $ciclo,
        string $usuario,
        array $datosBase,
        array $fechasOrdenadas
    ): int {
        if (empty($fechasOrdenadas)) {
            return 0;
        }
        $plazoDias = (int) $datosBase['PLAZO_DIAS'];
        $devengoTotal = (float) $datosBase['DEVENGO_TOTAL'];
        $iva = (float) $datosBase['IVA'];
        $inicioRaw = $datosBase['INICIO'];
        $inicioStr = $inicioRaw instanceof \DateTimeInterface
            ? $inicioRaw->format('Y-m-d') : (is_numeric($inicioRaw) ? date('Y-m-d', $inicioRaw) : date('Y-m-d', strtotime($inicioRaw)));
        $finTs = $datosBase['FIN_TS'];
        $finStr = $finTs instanceof \DateTimeInterface
            ? $finTs->format('Y-m-d') : (is_numeric($finTs) ? date('Y-m-d', $finTs) : date('Y-m-d', strtotime($finTs)));
        $cdgem = $datosBase['CDGEM'] ?? 'EMPFIN';
        $cdgpe = $datosBase['CDGPE'] ?? 'AMGM';
        $periodicidad = $datosBase['PERIODICIDAD'] ?? 'S';
        $plazo = (int) ($datosBase['PLAZO'] ?? 0);

        $devengoDiarioFijo = $plazoDias > 0 ? round($devengoTotal / $plazoDias, 2) : 0.0;
        $acumulado = 0.0;
        $insertados = 0;

        $stmtInsert = $db->db_activa->prepare(<<<SQL
            INSERT INTO ESIACOM.DEVENGO_DIARIO (
                FECHA_CALC, CDGEM, CDGCLNS, CICLO, INICIO, DEV_DIARIO, DIAS_DEV, INT_DEV,
                CDGPE, FREGISTRO, DEV_DIARIO_SIN_IVA, IVA_INT, PLAZO, PERIODICIDAD, PLAZO_DIAS, FIN_DEVENGO, ESTATUS, CLNS
            ) VALUES (
                TO_TIMESTAMP(:fecha_calc || ' 00:00:00', 'YYYY-MM-DD HH24:MI:SS'),
                :cdgem, :cdgclns, :ciclo, TO_DATE(:inicio, 'YYYY-MM-DD'), :dev_diario, :dias_dev, :int_dev,
                :cdgpe, SYSTIMESTAMP, :dev_diario_sin_iva, :iva_int, :plazo, :periodicidad, :plazo_dias,
                TO_DATE(:fin_devengo, 'YYYY-MM-DD'), 'RE', 'G'
            )
        SQL);

        foreach ($fechasOrdenadas as $fechaCalc) {
            $fechaCalc = trim($fechaCalc);
            if ($fechaCalc === '') {
                continue;
            }
            $ts = strtotime($fechaCalc);
            if ($ts === false) {
                continue;
            }
            $diasDev = (int) round((strtotime($fechaCalc . ' 12:00:00') - strtotime($inicioStr . ' 12:00:00')) / 86400);
            if ($diasDev < 0) {
                continue;
            }
            $esUltimoDia = ($fechaCalc === $finStr);
            if ($esUltimoDia) {
                $devDiario = round($devengoTotal - $acumulado, 2);
            } else {
                $devDiario = $devengoDiarioFijo;
            }
            // Asegurar que el devengo diario tenga 2 decimales como el SP
            $devDiario = round($devDiario, 2);

            // Acumulado igual que en el SP (suma anterior + diario actual)
            $acumulado = round($acumulado + $devDiario, 2);
            $devDiarioSinIva = round($devDiario / (1 + $iva), 2);
            $ivaInt = round($devDiario - $devDiarioSinIva, 2);

            try {
                $stmtCheck = $db->db_activa->prepare(
                    "SELECT 1 FROM ESIACOM.DEVENGO_DIARIO WHERE CDGCLNS = :c AND CICLO = :cic AND TRUNC(FECHA_CALC) = TO_DATE(:f, 'YYYY-MM-DD')"
                );
                $stmtCheck->execute(['c' => $credito, 'cic' => $ciclo, 'f' => $fechaCalc]);
                if ($stmtCheck->fetch()) {
                    continue;
                }
            } catch (\Throwable $e) {
                continue;
            }

            $usuarioSesion = $_SESSION['usuario'] ?? 'SYSTEM';
            try {
                $stmtInsert->execute([
                    'fecha_calc' => $fechaCalc,
                    'cdgem' => $cdgem,
                    'cdgclns' => $credito,
                    'ciclo' => $ciclo,
                    'inicio' => $inicioStr,
                    'dev_diario' => $devDiario,
                    'dias_dev' => $diasDev,
                    'int_dev' => $acumulado,
                    'cdgpe' => $usuarioSesion,
                    'dev_diario_sin_iva' => $devDiarioSinIva,
                    'iva_int' => $ivaInt,
                    'plazo' => $plazo,
                    'periodicidad' => $periodicidad,
                    'plazo_dias' => $plazoDias,
                    'fin_devengo' => $finStr,
                ]);
                $insertados++;
            } catch (\Throwable $e) {
                // Continuar con la siguiente fecha; el NOT EXISTS implícito (check previo) evita duplicados
            }
        }
        return $insertados;
    }

    /**
     * Inserta devengos faltantes para credito/ciclo hasta fechaCorte replicando la lógica de SP_AUDITA_DEVENGO:
     * datos base (PRN, MP, CF), calendario diario, ajuste último día, INSERT directo en ESIACOM.DEVENGO_DIARIO.
     * No usa el SP. Respeta FECHA_LIQUIDA (validado antes) y evita duplicados.
     *
     * @return int Número de registros insertados; -1 si se procesaron fechas pasadas sin inserts hoy; 0 si no hubo nada que insertar.
     */
    public static function InsertarDevengosFaltantes(
        \Core\Database $db,
        string $credito,
        string $ciclo,
        string $usuario,
        string $fechaCorte
    ): int {
        $logPath = defined('APPPATH') ? APPPATH . '/../logs/auditoria_devengo_proceso.log' : __DIR__ . '/../../logs/auditoria_devengo_proceso.log';

        $currentSchema = null;
        try {
            $schemaStmt = $db->db_activa->prepare("SELECT SYS_CONTEXT('USERENV','CURRENT_SCHEMA') AS SCH FROM DUAL");
            $schemaStmt->execute();
            $row = $schemaStmt->fetch(\PDO::FETCH_ASSOC);
            $currentSchema = $row['SCH'] ?? '?';
        } catch (\Throwable $e) {
            $currentSchema = 'error:' . $e->getMessage();
        }
        @file_put_contents($logPath, date('c') . " [INSERT] PRE credito=$credito | ciclo=$ciclo | usuario=$usuario | fecha_corte=$fechaCorte | schema=$currentSchema\n", FILE_APPEND);

        if ($currentSchema !== null && strtoupper((string) $currentSchema) !== 'ESIACOM') {
            try {
                $db->db_activa->exec("ALTER SESSION SET CURRENT_SCHEMA = ESIACOM");
                @file_put_contents($logPath, date('c') . " [INSERT] ALTER SESSION SET CURRENT_SCHEMA = ESIACOM\n", FILE_APPEND);
            } catch (\Throwable $e) {
                @file_put_contents($logPath, date('c') . " [INSERT] ALTER SESSION error: " . $e->getMessage() . "\n", FILE_APPEND);
                throw new \Exception("Error al cambiar esquema a ESIACOM: " . $e->getMessage());
            }
        }

        $datosBase = self::ObtenerDatosBaseDevengo($db, $credito, $ciclo);
        if (!$datosBase) {
            @file_put_contents($logPath, date('c') . " [INSERT] No se obtuvieron datos base para $credito / $ciclo\n", FILE_APPEND);
            return 0;
        }

        $inicioTs = $datosBase['INICIO'];
        $finTs = $datosBase['FIN_TS'];
        $fechas = self::ObtenerFechasFaltantesParaInsertar($db, $credito, $ciclo, $fechaCorte, $inicioTs, $finTs);
        if (empty($fechas)) {
            $existStmt = $db->db_activa->prepare(
                "SELECT COUNT(*) AS C FROM ESIACOM.DEVENGO_DIARIO WHERE CDGCLNS = :c AND CICLO = :cic"
            );
            $existStmt->execute(['c' => $credito, 'cic' => $ciclo]);
            $r = $existStmt->fetch(\PDO::FETCH_ASSOC);
            $totalExistentes = (int) ($r['C'] ?? 0);
            @file_put_contents($logPath, date('c') . " [INSERT] Cero fechas faltantes; total existentes=$totalExistentes\n", FILE_APPEND);
            return $totalExistentes > 0 ? -1 : 0;
        }

        $insertados = self::InsertarRegistrosDevengoDiario($db, $credito, $ciclo, $usuario, $datosBase, $fechas);
        @file_put_contents($logPath, date('c') . " [INSERT] Insertados=$insertados\n", FILE_APPEND);

        if ($insertados === 0) {
            $existStmt = $db->db_activa->prepare(
                "SELECT COUNT(*) AS C FROM ESIACOM.DEVENGO_DIARIO WHERE CDGCLNS = :c AND CICLO = :cic"
            );
            $existStmt->execute(['c' => $credito, 'cic' => $ciclo]);
            $er = $existStmt->fetch(\PDO::FETCH_ASSOC);
            $totalExistentes = (int) ($er['C'] ?? 0);
            if ($totalExistentes > 0) {
                return -1;
            }
        }
        return $insertados;
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
