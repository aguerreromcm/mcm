<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\App;
use Core\Database;

/**
 * Repository: consultas y ejecución del SP de aplicación de pagos.
 * Usa la misma consulta exacta que Layout Contable (Pagos → Layout Contable).
 * No contiene lógica de negocio; solo acceso a datos y llamada al SP.
 */
class PagosAplicacionRepository
{
    /** Cuenta bancaria por defecto (2 caracteres). VB6 usa una por archivo; aquí un valor por defecto. */
    const CUENTA_BANCARIA_DEFAULT = '01';

    /** Empresa fija, igual que Layout Contable. */
    const EMPRESA = 'EMPFIN';

    /**
     * Normaliza fecha enviada desde input date (Y-m-d) o texto d/m/Y, d-m-Y.
     */
    public static function coerceFechaYmD($fecha): ?string
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return null;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return $fecha;
        }
        if (preg_match('#^(\d{1,2})[/-](\d{1,2})[/-](\d{4})$#', $fecha, $m)) {
            $d = (int) $m[1];
            $mo = (int) $m[2];
            $y = (int) $m[3];
            if (!checkdate($mo, $d, $y)) {
                return null;
            }

            return sprintf('%04d-%02d-%02d', $y, $mo, $d);
        }

        return null;
    }

    /**
     * Pago ya procesado por importación: VB6/PHP usa F_IMPORTACION; SP_PAGOS_CIERRE_DEVENGO usa ID_IMPORTACION.
     */
    public static function filaMarcadaImportada(array $f): bool
    {
        $fi = $f['F_IMPORTACION'] ?? $f['f_importacion'] ?? null;
        if ($fi !== null && trim((string) $fi) !== '') {
            return true;
        }
        $idImp = $f['ID_IMPORTACION'] ?? $f['id_importacion'] ?? null;
        if ($idImp === null || $idImp === '') {
            return false;
        }
        if (is_numeric($idImp)) {
            return ((float) $idImp) > 0;
        }

        return trim((string) $idImp) !== '';
    }

    /**
     * SELECT layout sin depender de columnas opcionales (p. ej. si F_IMPORTACION no existe en BD).
     */
    private function ejecutarSelectLayout(Database $db, string $sql): ?array
    {
        if ($db->db_activa === null) {
            return null;
        }
        try {
            $stmt = $db->db_activa->query($sql);
            if ($stmt === false) {
                return null;
            }
            $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return is_array($filas) ? $filas : [];
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Obtiene los datos con la misma consulta exacta que Layout Contable (Pagos).
     * Consulta copiada de App\models\Pagos::GeneraLayoutContable.
     *
     * @param string $fecha Fecha en Y-m-d
     * @return array Lista de filas con FECHA, REFERENCIA, MONTO, MONEDA
     */
    public function getDatosLayoutPorFecha($fecha)
    {
        $fechaNorm = self::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return [];
        }
        $f1 = $fechaNorm;

        $db = new Database();
        if ($db->db_activa === null) {
            return [];
        }

        // Misma lógica que Layout Contable / SQL Developer: día completo con BETWEEN.
        // Sin usar Database::queryAll aquí: si falla el SQL (p. ej. columna inexistente),
        // queryAll escribe en salida y rompe JSON; además devolvía [] igual que “sin filas”.
        $condFecha = "PGD.FECHA BETWEEN TO_DATE('$f1 00:00:00', 'YYYY-MM-DD HH24:MI:SS') "
            . "AND TO_DATE('$f1 23:59:59', 'YYYY-MM-DD HH24:MI:SS')";

        $selectBase = <<<SQL
		PGD.CDGEM,
		PGD.CDGNS,
		PGD.CICLO,
		PGD.SECUENCIA,
		PGD.TIPO,
		PGD.FECHA,
		CASE
			WHEN (PGD.TIPO = 'P' OR PGD.TIPO = 'X') THEN 'P' || PRN.CDGNS || PRN.CDGTPC || FN_DV('P' || PRN.CDGNS || PRN.CDGTPC)
			WHEN PGD.TIPO = 'G' THEN '0' || PRN.CDGNS || PRN.CDGTPC || FN_DV('0' || PRN.CDGNS || PRN.CDGTPC)
			ELSE 'NO IDENTIFICADO'
		END REFERENCIA,
		PGD.MONTO,
		'MN' MONEDA
SQL;

        $queryConFi = <<<sql
	SELECT
		{$selectBase},
		PGD.F_IMPORTACION,
		PGD.ID_IMPORTACION
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
		AND {$condFecha}
	ORDER BY
		PGD.FECHA
sql;

        $querySinFi = <<<sql
	SELECT
		{$selectBase},
		PGD.ID_IMPORTACION
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
		AND {$condFecha}
	ORDER BY
		PGD.FECHA
sql;

        $querySoloLayout = <<<sql
	SELECT
		{$selectBase}
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
		AND {$condFecha}
	ORDER BY
		PGD.FECHA
sql;

        $filas = $this->ejecutarSelectLayout($db, $queryConFi);
        if ($filas === null) {
            $filas = $this->ejecutarSelectLayout($db, $querySinFi);
            if ($filas === null) {
                $filas = $this->ejecutarSelectLayout($db, $querySoloLayout);
                if ($filas === null) {
                    return [];
                }
                foreach ($filas as $i => $_) {
                    $filas[$i]['F_IMPORTACION'] = null;
                    $filas[$i]['ID_IMPORTACION'] = null;
                }
            } else {
                foreach ($filas as $i => $_) {
                    $filas[$i]['F_IMPORTACION'] = null;
                }
            }
        }

        foreach ($filas as $i => $row) {
            $row = array_change_key_case((array) $row, CASE_UPPER);
            if (isset($row['FECHA'])) {
                $row['FECHA'] = $this->fechaOracleAString($row['FECHA']);
            }
            if (isset($row['F_IMPORTACION']) && $row['F_IMPORTACION'] !== null && $row['F_IMPORTACION'] !== '') {
                $row['F_IMPORTACION'] = $this->fechaOracleAString($row['F_IMPORTACION']);
            } else {
                $row['F_IMPORTACION'] = null;
            }
            if (isset($row['ID_IMPORTACION']) && $row['ID_IMPORTACION'] !== null && $row['ID_IMPORTACION'] !== '') {
                $row['ID_IMPORTACION'] = (int) (float) $row['ID_IMPORTACION'];
            } else {
                $row['ID_IMPORTACION'] = null;
            }
            if (isset($row['MONTO'])) {
                $row['MONTO'] = (float) $row['MONTO'];
            }
            if (isset($row['TIPO'])) {
                $row['TIPO'] = strtoupper(trim((string) $row['TIPO']));
            } else {
                $row['TIPO'] = '';
            }
            if (!isset($row['MONEDA']) || $row['MONEDA'] === null) {
                $row['MONEDA'] = 'MN';
            }
            $filas[$i] = $row;
        }

        return $filas;
    }

    /**
     * Totales por ESTATUS en IMPORTACIONPAGDET (1=Pagos, 2=Garantías, 3=Incidencias).
     * Se elige un solo criterio de fecha por lote: primero FEC_CARGA (día en que se registró el cierre
     * en IMPORTACIONPAG, alineado con consultas típicas y con el SP), y si no hay filas se usa FEC_PAGO
     * (día operativo del pago). Antes se hacía al revés: un lote distinto filtrado solo por FEC_PAGO
     * podía devolver solo líneas ESTATUS=1 y mostrar todo como “Pagos”.
     */
    public function getResumenPorEstatusImportacion($fecha)
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return self::resumenPorEstatusVacio();
        }
        $db = new Database();
        if ($db->db_activa === null) {
            return self::resumenPorEstatusVacio();
        }

        $sql = <<<'sql'
SELECT D.ESTATUS AS ESTATUS,
       SUM(CASE WHEN NVL(D.NO_REGISTROS, 0) > 0 THEN NVL(D.NO_REGISTROS, 0) ELSE 1 END) AS SUMA_REG,
       SUM(NVL(D.MONTO, 0)) AS SUMA_MONTO
FROM IMPORTACIONPAGDET D
INNER JOIN IMPORTACIONPAG IP ON IP.ID_IMPORTACION = D.ID_IMPORTACION
WHERE __FECHA_FILTER__
GROUP BY D.ESTATUS
sql;

        try {
            $filtros = [
                "TRUNC(IP.FEC_CARGA) = TO_DATE(:f1, 'YYYY-MM-DD')",
                "TRUNC(IP.FEC_PAGO) = TO_DATE(:f1, 'YYYY-MM-DD')",
            ];
            foreach ($filtros as $filtroFecha) {
                $stmt = $db->db_activa->prepare(str_replace('__FECHA_FILTER__', $filtroFecha, $sql));
                $stmt->execute(['f1' => $fecha]);
                $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $out = $this->acumularGrupoImportacion(is_array($filas) ? $filas : []);
                $n = $out['registrosPagos'] + $out['registrosGarantias'] + $out['registrosIncidencias'];
                if ($n > 0) {
                    return $out;
                }
            }

            return self::resumenPorEstatusVacio();
        } catch (\Throwable $e) {
            return self::resumenPorEstatusVacio();
        }
    }

    /**
     * Indica si la fecha ya fue procesada (existe en PAGOS_PROCESADOS).
     * Si la tabla no existe o hay error, devuelve null para no bloquear la consulta del layout.
     *
     * @param string $fecha Fecha en Y-m-d
     * @return array|null Registro de PAGOS_PROCESADOS o null
     */
    public function obtenerProcesado($fecha)
    {
        try {
            $db = new Database();
            if ($db->db_activa === null) {
                return null;
            }
            $sql = "SELECT ID, FECHA_PROCESO, TOTAL_REGISTROS, TOTAL_IMPORTE, USUARIO, FECHA_EJECUCION, ESTADO, MENSAJE, DETALLE_JSON
                    FROM (SELECT * FROM PAGOS_PROCESADOS WHERE FECHA_PROCESO = TO_DATE(:fecha, 'YYYY-MM-DD') ORDER BY FECHA_EJECUCION DESC)
                    WHERE ROWNUM = 1";
            $stmt = $db->db_activa->prepare($sql);
            $stmt->execute(['fecha' => trim($fecha)]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Inserta cabecera en PAGOS_PROCESADOS (dentro de transacción si se pasa $db).
     *
     * @param string $fechaProceso Y-m-d
     * @param int $totalRegistros
     * @param float $totalImporte
     * @param string $usuario
     * @param string $estado OK|ERROR
     * @param string|null $mensaje
     * @param string|null $detalleJson JSON
     * @param Database|null $db Si se pasa, se usa esta conexión (misma transacción)
     * @return bool
     */
    public function insertarProcesado($fechaProceso, $totalRegistros, $totalImporte, $usuario, $estado, $mensaje = null, $detalleJson = null, Database $db = null)
    {
        if ($db === null) {
            $db = new Database();
        }
        if ($db->db_activa === null) {
            return false;
        }
        $sql = "INSERT INTO PAGOS_PROCESADOS (FECHA_PROCESO, TOTAL_REGISTROS, TOTAL_IMPORTE, USUARIO, ESTADO, MENSAJE, DETALLE_JSON)
                VALUES (TO_DATE(:fecha, 'YYYY-MM-DD'), :total_reg, :total_imp, :usuario, :estado, :mensaje, :detalle)";
        $stmt = $db->db_activa->prepare($sql);
        return $stmt->execute([
            'fecha' => $fechaProceso,
            'total_reg' => (int) $totalRegistros,
            'total_imp' => (float) $totalImporte,
            'usuario' => $usuario,
            'estado' => $estado,
            'mensaje' => $mensaje,
            'detalle' => $detalleJson,
        ]);
    }

    /**
     * Marca el pago como importado en PAGOSDIA (F_IMPORTACION = SYSTIMESTAMP).
     * Clave: CDGEM, CDGNS, CICLO, FECHA (solo fecha), SECUENCIA.
     *
     * @param string $cdgem
     * @param string $cdgns
     * @param string $ciclo
     * @param string $fecha Fecha en Y-m-d o Y-m-d H:i:s (solo se usa la parte fecha)
     * @param int|string $secuencia
     * @param Database|null $db
     * @return bool
     */
    public function actualizarFImportacion($cdgem, $cdgns, $ciclo, $fecha, $secuencia, Database $db = null)
    {
        if ($db === null) {
            $db = new Database();
        }
        if ($db->db_activa === null) {
            return false;
        }
        $fechaSolo = substr((string) $fecha, 0, 10);
        if ($fechaSolo === '' || strlen($fechaSolo) < 10) {
            return false;
        }
        $sql = "UPDATE PAGOSDIA SET F_IMPORTACION = SYSTIMESTAMP
                WHERE CDGEM = :cdgem AND CDGNS = :cdgns AND CICLO = :ciclo
                AND TRUNC(FECHA) = TO_DATE(:fecha, 'YYYY-MM-DD') AND SECUENCIA = :secuencia";
        $stmt = $db->db_activa->prepare($sql);
        return $stmt->execute([
            'cdgem' => $cdgem,
            'cdgns' => $cdgns,
            'ciclo' => $ciclo,
            'fecha' => $fechaSolo,
            'secuencia' => $secuencia,
        ]);
    }

    /**
     * Ejecuta el mismo SP que usa VB6 para importar un pago (PKG_ImportaPagoSOF.spImportaPagoSOF).
     *
     * @param string $fechaPago Fecha de pago (Y-m-d H:i:s)
     * @param string $referencia
     * @param string $monto
     * @param string $usuario
     * @param string $identificador
     * @param int $renExcel
     * @param int $renglon
     * @param int $noPagos
     * @param int $idImportacion
     * @param string $moneda
     * @param string|null $cuentaBancaria Si null, usa CUENTA_BANCARIA_DEFAULT
     * @param Database|null $db Si se pasa, se usa esta conexión (misma transacción)
     * @return array ['success' => bool, 'resultado' => string, 'validacion' => int]
     */
    public function ejecutarSpImportaPago($fechaPago, $referencia, $monto, $usuario, $identificador, $renExcel, $renglon, $noPagos, $idImportacion, $moneda = 'MN', $cuentaBancaria = null, Database $db = null)
    {
        if ($db === null) {
            $db = new Database();
        }
        if ($db->db_activa === null) {
            return ['success' => false, 'resultado' => 'Sin conexión', 'validacion' => -1];
        }
        $cta = $cuentaBancaria !== null && $cuentaBancaria !== '' ? substr($cuentaBancaria, 0, 2) : self::CUENTA_BANCARIA_DEFAULT;

        $config = App::getConfig();
        $valor = $config['APLICAR_PAGOS_SOLO_FLUJO'] ?? ($config['aplicar_pagos']['APLICAR_PAGOS_SOLO_FLUJO'] ?? null);
        $soloFlujo = $valor !== null && (filter_var($valor, FILTER_VALIDATE_BOOLEAN) || $valor === 'true' || $valor === '1');

        if ($soloFlujo) {
            return $db->spImportaPagoSOFPrueba(
                $fechaPago,
                $referencia,
                $monto,
                self::EMPRESA,
                $cta,
                $usuario,
                $identificador,
                $renExcel,
                $renglon,
                $noPagos,
                $idImportacion,
                $moneda
            );
        }

        return $db->spImportaPagoSOF(
            $fechaPago,
            $referencia,
            $monto,
            self::EMPRESA,
            $cta,
            $usuario,
            $identificador,
            $renExcel,
            $renglon,
            $noPagos,
            $idImportacion,
            $moneda
        );
    }

    /** @return array{registrosPagos: int, registrosGarantias: int, registrosIncidencias: int, importePagos: float, importeGarantias: float, importeIncidencias: float} */
    private static function resumenPorEstatusVacio(): array
    {
        return [
            'registrosPagos' => 0,
            'registrosGarantias' => 0,
            'registrosIncidencias' => 0,
            'importePagos' => 0.0,
            'importeGarantias' => 0.0,
            'importeIncidencias' => 0.0,
        ];
    }

    private function fechaOracleAString($valor): string
    {
        if (is_object($valor) && method_exists($valor, 'format')) {
            return $valor->format('Y/m/d H:i:s');
        }
        $str = trim(is_object($valor) ? (string) $valor : (string) $valor);
        $ts = strtotime($str);

        return $ts !== false ? date('Y/m/d H:i:s', $ts) : $str;
    }

    private function acumularGrupoImportacion(array $filas): array
    {
        $out = self::resumenPorEstatusVacio();
        foreach ($filas as $row) {
            if (!is_array($row)) {
                continue;
            }
            $estRaw = $row['ESTATUS'] ?? $row['estatus'] ?? 0;
            $est = (int) round((float) (is_string($estRaw) ? trim($estRaw) : $estRaw));
            $cnt = (int) round((float) ($row['SUMA_REG'] ?? $row['suma_reg'] ?? 0));
            $monto = (float) ($row['SUMA_MONTO'] ?? $row['suma_monto'] ?? 0);
            if ($est === 1) {
                $out['registrosPagos'] += $cnt;
                $out['importePagos'] += $monto;
            } elseif ($est === 2) {
                $out['registrosGarantias'] += $cnt;
                $out['importeGarantias'] += $monto;
            } elseif ($est === 3) {
                $out['registrosIncidencias'] += $cnt;
                $out['importeIncidencias'] += $monto;
            }
        }
        $out['importePagos'] = round($out['importePagos'], 2);
        $out['importeGarantias'] = round($out['importeGarantias'], 2);
        $out['importeIncidencias'] = round($out['importeIncidencias'], 2);

        return $out;
    }
}
