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
     * Obtiene los datos con la misma consulta exacta que Layout Contable (Pagos).
     * Consulta copiada de App\models\Pagos::GeneraLayoutContable.
     *
     * @param string $fecha Fecha en Y-m-d
     * @return array Lista de filas con FECHA, REFERENCIA, MONTO, MONEDA
     */
    public function getDatosLayoutPorFecha($fecha)
    {
        $db = new Database();
        if ($db->db_activa === null) {
            return [];
        }
        $fecha = trim($fecha);
        if ($fecha === '') {
            return [];
        }
        // Misma consulta que Layout Contable + clave para UPDATE y F_IMPORTACION (por pago).
        $query = <<<sql
	SELECT
		PGD.CDGEM,
		PGD.CDGNS,
		PGD.CICLO,
		PGD.SECUENCIA,
		PGD.FECHA,
		PGD.F_IMPORTACION,
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
		AND PGD.FECHA BETWEEN TO_DATE(:f1, 'YYYY-MM-DD') AND TO_DATE(:f2, 'YYYY-MM-DD')
	ORDER BY
		PGD.FECHA
sql;
        try {
            $stmt = $db->db_activa->prepare($query);
            $stmt->execute(['f1' => $fecha, 'f2' => $fecha]);
            $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!is_array($filas)) {
                return [];
            }
            // Normalizar para JSON (Oracle puede devolver FECHA/F_IMPORTACION como objeto)
            foreach ($filas as $i => $row) {
                if (isset($row['FECHA'])) {
                    if (is_object($row['FECHA']) && method_exists($row['FECHA'], 'format')) {
                        $filas[$i]['FECHA'] = $row['FECHA']->format('Y-m-d H:i:s');
                    } elseif (is_object($row['FECHA'])) {
                        $filas[$i]['FECHA'] = (string) $row['FECHA'];
                    }
                }
                if (isset($row['F_IMPORTACION']) && $row['F_IMPORTACION'] !== null) {
                    if (is_object($row['F_IMPORTACION']) && method_exists($row['F_IMPORTACION'], 'format')) {
                        $filas[$i]['F_IMPORTACION'] = $row['F_IMPORTACION']->format('Y-m-d H:i:s');
                    } else {
                        $filas[$i]['F_IMPORTACION'] = (string) $row['F_IMPORTACION'];
                    }
                } else {
                    $filas[$i]['F_IMPORTACION'] = null;
                }
                if (isset($row['MONTO'])) {
                    $filas[$i]['MONTO'] = (float) $row['MONTO'];
                }
                if (!isset($row['MONEDA']) || $row['MONEDA'] === null) {
                    $filas[$i]['MONEDA'] = 'MN';
                }
            }
            return $filas;
        } catch (\Throwable $e) {
            return [];
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
}
