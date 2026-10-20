<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\Database;

/**
 * Repository: pagos registrados después del cierre de día.
 * Replica la sección «IMPORTAR Y CONCILIAR PAGOS» y «RESUMEN IMPORTACION» de SP_PAGOS_CIERRE_DEVENGO
 * usando SP_IMPORTA_CONCILIA por pago (mismos parámetros que el cierre: cuenta 02, ID_IMPORTACION, fila/filas).
 * Sin lógica de negocio; solo SQL y llamadas a procedimientos.
 */
class AplicacionPagosRepository
{
    /** Cuenta bancaria que usa SP_PAGOS_CIERRE_DEVENGO para importar y conciliar. */
    const CUENTA_BANCARIA = '02';

    const EMPRESA = 'EMPFIN';

    /**
     * Ejecuta un callable capturando cualquier echo (p. ej. Database::muestraError) para no contaminar respuestas JSON.
     *
     * @param callable $fn
     * @return mixed
     */
    private function sinSalida(callable $fn)
    {
        ob_start();
        try {
            return $fn();
        } finally {
            ob_end_clean();
        }
    }

    /**
     * Pagos de la fecha sin ID_IMPORTACION (mismo criterio que el cursor c_PAGOS del cierre).
     *
     * @param string $fecha Y-m-d
     * @param Database|null $db Conexión con transacción iniciada (opcional)
     * @return array
     */
    public function getPagosPendientes($fecha, Database $db = null)
    {
        $qry = <<<SQL
            SELECT
                PGD.SECUENCIA,
                TO_CHAR(PGD.FECHA, 'YYYY-MM-DD') AS FECHA,
                TO_CHAR(PGD.FECHA, 'DD/MM/YYYY') AS FECHA_FMT,
                PGD.CDGNS,
                PGD.CICLO,
                PGD.TIPO,
                DECODE(PGD.TIPO, 'G', '0', 'P')
                    || PRN.CDGNS
                    || PRN.CDGTPC
                    || FN_DV(DECODE(PGD.TIPO, 'G', '0', 'P') || PRN.CDGNS || PRN.CDGTPC) AS REFERENCIA,
                PGD.MONTO,
                NVL(RTRIM(LTRIM(NS.NOMBRE)), '') AS NOMBRE
            FROM
                PAGOSDIA PGD
                LEFT JOIN PRN
                    ON PGD.CDGNS = PRN.CDGNS
                    AND PGD.CICLO = PRN.CICLO
                LEFT JOIN NS
                    ON NS.CDGEM = PGD.CDGEM
                    AND NS.CODIGO = PGD.CDGNS
            WHERE
                PGD.ESTATUS = 'A'
                AND PGD.TIPO IN ('P', 'G', 'X')
                AND PGD.MONTO != 0
                AND TRUNC(PGD.FECHA) = TO_DATE(:fecha, 'YYYY-MM-DD')
                AND PGD.ID_IMPORTACION IS NULL
            ORDER BY
                PGD.SECUENCIA
        SQL;

        $database = $db !== null ? $db : new Database();
        if ($database->db_activa === null) {
            return [];
        }

        $stmt = $database->db_activa->prepare($qry);
        $stmt->execute(['fecha' => $fecha]);
        $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!is_array($filas)) {
            return [];
        }

        foreach ($filas as $i => $row) {
            $filas[$i]['MONTO'] = (float) ($row['MONTO'] ?? 0);
            $filas[$i]['TIPO'] = strtoupper(trim((string) ($row['TIPO'] ?? '')));
            $filas[$i]['CDGNS'] = trim((string) ($row['CDGNS'] ?? ''));
            $filas[$i]['CICLO'] = trim((string) ($row['CICLO'] ?? ''));
            $filas[$i]['REFERENCIA'] = trim((string) ($row['REFERENCIA'] ?? ''));
            $filas[$i]['NOMBRE'] = trim((string) ($row['NOMBRE'] ?? ''));
        }

        return $filas;
    }

    /**
     * Fechas recientes con cierre concluido con éxito que aún tienen pagos pendientes de importar.
     *
     * @param int $dias Días hacia atrás a revisar
     * @return array Lista de { FECHA, FECHA_FMT, PAGOS, MONTO }
     */
    public function getFechasConPendientes($dias = 30)
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(TRUNC(PGD.FECHA), 'YYYY-MM-DD') AS FECHA,
                TO_CHAR(TRUNC(PGD.FECHA), 'DD/MM/YYYY') AS FECHA_FMT,
                COUNT(*) AS PAGOS,
                NVL(SUM(PGD.MONTO), 0) AS MONTO
            FROM
                PAGOSDIA PGD
            WHERE
                PGD.ESTATUS = 'A'
                AND PGD.TIPO IN ('P', 'G', 'X')
                AND PGD.MONTO != 0
                AND PGD.ID_IMPORTACION IS NULL
                AND PGD.FECHA >= TRUNC(SYSDATE) - :dias
                AND PGD.FECHA < TRUNC(SYSDATE) + 1
                AND EXISTS (
                    SELECT 1
                    FROM BITACORA_CIERRE_DIARIO B
                    WHERE TRUNC(B.FECHA_CALCULO) = TRUNC(PGD.FECHA)
                      AND B.ID_IMPORTACION IS NOT NULL
                      AND B.FIN IS NOT NULL
                      AND NVL(B.EXITO, 0) = 1
                )
            GROUP BY
                TRUNC(PGD.FECHA)
            ORDER BY
                TRUNC(PGD.FECHA) DESC
        SQL;

        return $this->sinSalida(function () use ($qry, $dias) {
            try {
                $db = new Database();
                if ($db->db_activa === null) {
                    return [];
                }
                $stmt = $db->db_activa->prepare($qry);
                $stmt->execute(['dias' => max(1, (int) $dias)]);
                $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                if (!is_array($filas)) {
                    return [];
                }
                foreach ($filas as $i => $row) {
                    $filas[$i]['PAGOS'] = (int) ($row['PAGOS'] ?? 0);
                    $filas[$i]['MONTO'] = round((float) ($row['MONTO'] ?? 0), 2);
                }
                return $filas;
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    /**
     * Siguiente ID_IMPORTACION de la cuenta del cierre (misma fórmula que SP_PAGOS_CIERRE_DEVENGO).
     *
     * @param Database $db
     * @return int
     */
    public function siguienteIdImportacion(Database $db)
    {
        $qry = <<<SQL
            SELECT NVL(MAX(ID_IMPORTACION), 0) + 1 AS ID
            FROM IMPORTACIONPAG
            WHERE CDGEM = 'EMPFIN'
              AND CTA_BANCARIA = 2
        SQL;

        $stmt = $db->db_activa->prepare($qry);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int) ($row['ID'] ?? 1);
    }

    /**
     * Importa y concilia un pago con SP_IMPORTA_CONCILIA, con los parámetros que usa el cierre.
     *
     * @param array $pago Fila de getPagosPendientes()
     * @param int $fila Posición del pago en el lote (1..n)
     * @param int $filas Total de pagos del lote
     * @param int $idImportacion
     * @param string $usuario
     * @param string $identificador
     * @param Database $db Conexión con transacción iniciada
     * @return array ['success' => bool, 'resultado' => string, 'validacion' => int]
     */
    public function ejecutarImportaConcilia(array $pago, $fila, $filas, $idImportacion, $usuario, $identificador, Database $db)
    {
        $credito = (string) $pago['CDGNS'];
        $ciclo = (string) $pago['CICLO'];
        $monto = (string) $pago['MONTO'];
        $fechaPago = (string) $pago['FECHA'];
        $referencia = (string) $pago['REFERENCIA'];
        $cta = self::CUENTA_BANCARIA;
        $usuario = (string) $usuario;
        $identificador = (string) $identificador;
        $secuencia = (string) $pago['SECUENCIA'];
        $fila = (string) $fila;
        $filas = (string) $filas;
        $idImportacion = (string) $idImportacion;
        $resImportacion = '';
        $valImportacion = '0';

        $sql = "BEGIN SP_IMPORTA_CONCILIA(
            p_CREDITO => :p_credito,
            p_CICLO => :p_ciclo,
            p_MONTO => :p_monto,
            p_FECHA_PAGO => TO_DATE(:p_fecha, 'YYYY-MM-DD'),
            p_REFERENCIA => :p_referencia,
            p_CTA_BANCARIA => :p_cta,
            p_USUARIO => :p_usuario,
            p_IDENTIFICADOR => :p_identificador,
            p_SECUENCIA => :p_secuencia,
            p_FILA => :p_fila,
            p_FILAS => :p_filas,
            p_ID_IMPORTACION => :p_id_importacion,
            p_RES_IMPORTACION => :p_res,
            p_VAL_IMPORTACION => :p_val
        ); END;";

        try {
            $stmt = $db->db_activa->prepare($sql);
            $stmt->bindParam(':p_credito', $credito, \PDO::PARAM_STR, 20);
            $stmt->bindParam(':p_ciclo', $ciclo, \PDO::PARAM_STR, 10);
            $stmt->bindParam(':p_monto', $monto, \PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_fecha', $fechaPago, \PDO::PARAM_STR, 20);
            $stmt->bindParam(':p_referencia', $referencia, \PDO::PARAM_STR, 120);
            $stmt->bindParam(':p_cta', $cta, \PDO::PARAM_STR, 20);
            $stmt->bindParam(':p_usuario', $usuario, \PDO::PARAM_STR, 50);
            $stmt->bindParam(':p_identificador', $identificador, \PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_secuencia', $secuencia, \PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_fila', $fila, \PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_filas', $filas, \PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_id_importacion', $idImportacion, \PDO::PARAM_STR, 30);
            $stmt->bindParam(':p_res', $resImportacion, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 4000);
            $stmt->bindParam(':p_val', $valImportacion, \PDO::PARAM_STR | \PDO::PARAM_INPUT_OUTPUT, 20);

            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                return [
                    'success' => false,
                    'resultado' => $errorInfo[2] ?? ($errorInfo[1] ?? 'execute() retornó false sin detalle'),
                    'validacion' => -1,
                ];
            }
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'resultado' => $e->getMessage(),
                'validacion' => -1,
            ];
        }

        return [
            'success' => true,
            'resultado' => trim((string) $resImportacion),
            'validacion' => (int) $valImportacion,
        ];
    }

    /**
     * Motivo que dejó SPIMPORTAPAGOSOF en RES_IMPOR para el pago y código de validación indicados.
     *
     * @param string $identificador Identificador del lote
     * @param int|string $secuencia Secuencia del pago (RENEXCEL)
     * @param int $validacion
     * @param Database $db
     * @return string Vacío si no hay registro
     */
    public function getMotivoValidacion($identificador, $secuencia, $validacion, Database $db)
    {
        $sql = <<<SQL
            SELECT MSGRESUL
            FROM RES_IMPOR
            WHERE IDENTIFICADOR = :identificador
              AND RENEXCEL = :secuencia
              AND VALIDACION = :validacion
            FETCH FIRST 1 ROW ONLY
        SQL;

        try {
            $stmt = $db->db_activa->prepare($sql);
            $stmt->execute([
                'identificador' => (string) $identificador,
                'secuencia' => (int) $secuencia,
                'validacion' => (int) $validacion,
            ]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return '';
        }

        $motivo = trim((string) ($row['MSGRESUL'] ?? ''));
        $motivo = preg_replace('/^Resultado de la validaci\S*n:\s*/iu', '', $motivo);
        $motivo = preg_replace('/\s*\(\d+\)\s*$/u', '', $motivo);

        return trim(preg_replace('/\s+/u', ' ', $motivo));
    }

    /**
     * Marca el pago como procesado en PAGOSDIA (igual que el cierre).
     *
     * @param array $pago Fila de getPagosPendientes()
     * @param int $idImportacion
     * @param Database $db
     * @return void
     */
    public function marcarPagoProcesado(array $pago, $idImportacion, Database $db)
    {
        $sql = <<<SQL
            UPDATE PAGOSDIA PGD
            SET ID_IMPORTACION = :id_importacion
            WHERE PGD.SECUENCIA = :secuencia
              AND PGD.FECHA = TO_DATE(:fecha, 'YYYY-MM-DD')
              AND PGD.CDGNS = :cdgns
              AND PGD.CICLO = :ciclo
        SQL;

        $stmt = $db->db_activa->prepare($sql);
        $stmt->execute([
            'id_importacion' => (int) $idImportacion,
            'secuencia' => $pago['SECUENCIA'],
            'fecha' => $pago['FECHA'],
            'cdgns' => $pago['CDGNS'],
            'ciclo' => $pago['CICLO'],
        ]);
    }

    /**
     * Registra el resumen de la importación en IMPORTACIONPAG / IMPORTACIONPAGDET (spInsImportacion),
     * con la misma estructura que el cierre: total general y desglose por validación de RES_IMPOR.
     *
     * @param int $idImportacion
     * @param string $fecha Y-m-d
     * @param int $noRegistros
     * @param float $monto
     * @param string $identificador
     * @param Database $db
     * @return void
     */
    public function registrarResumenImportacion($idImportacion, $fecha, $noRegistros, $monto, $identificador, Database $db)
    {
        $sqlIns = <<<SQL
            BEGIN
                spInsImportacion(
                    pACCION => :accion,
                    pCDGEM => 'EMPFIN',
                    pID_IMPORTACION => :id_importacion,
                    pCTA_BANCARIA => 2,
                    pNO_REGISTROS => :no_registros,
                    pESTATUS => :estatus,
                    pFEC_CARGA => SYSDATE,
                    pFEC_PAGO => TO_DATE(:fecha, 'YYYY-MM-DD'),
                    pMONTO => :monto
                );
            END;
        SQL;

        $stmtIns = $db->db_activa->prepare($sqlIns);
        $stmtIns->execute([
            'accion' => 0,
            'id_importacion' => (int) $idImportacion,
            'no_registros' => (int) $noRegistros,
            'estatus' => 0,
            'fecha' => $fecha,
            'monto' => (float) $monto,
        ]);

        $sqlTipo = <<<SQL
            SELECT
                NVL(SUM(DECODE(VALIDACION, :tipo1, 1, 0)), 0) AS FILAS,
                NVL(SUM(DECODE(VALIDACION, :tipo2, MONTO, 0)), 0) AS MONTO
            FROM RES_IMPOR
            WHERE IDENTIFICADOR = :identificador
        SQL;
        $stmtTipo = $db->db_activa->prepare($sqlTipo);

        // 0 = pagos, 1 = garantías, 2 = no identificados → ESTATUS 1, 2, 3
        for ($tipo = 0; $tipo <= 2; $tipo++) {
            $stmtTipo->execute(['tipo1' => $tipo, 'tipo2' => $tipo, 'identificador' => (string) $identificador]);
            $row = $stmtTipo->fetch(\PDO::FETCH_ASSOC);
            $filasTipo = (int) ($row['FILAS'] ?? 0);
            if ($filasTipo <= 0) {
                continue;
            }
            $stmtIns->execute([
                'accion' => 1,
                'id_importacion' => (int) $idImportacion,
                'no_registros' => $filasTipo,
                'estatus' => $tipo + 1,
                'fecha' => $fecha,
                'monto' => (float) ($row['MONTO'] ?? 0),
            ]);
        }
    }
}
