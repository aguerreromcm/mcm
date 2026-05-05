<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Model;
use Core\App;
use Core\Database;
use App\repositories\ConciliacionRepository;

/**
 * Service para Conciliación de pagos: consulta MP y ejecución de spRedistribucionPagos (réplica VB6).
 */
class ConciliacionService
{
    /**
     * Busca pagos por conciliar. Permite buscar por cualquiera de los filtros (al menos uno).
     *
     * @param string $empresa
     * @param string $fechaPago Y-m-d
     * @param string $tipoCliente I, G o vacío
     * @param string $codigo Crédito (código ind./gpo.)
     * @param string $ciclo
     * @param string $ctaBancaria
     * @return array { success, mensaje, datos: { filas, resumen }, error }
     */
    public static function buscarPagosConciliacion($empresa, $fechaPago, $tipoCliente, $codigo, $ciclo, $ctaBancaria)
    {
        $repo = new ConciliacionRepository();

        $empresa = trim((string) $empresa);
        $fechaPago = trim((string) $fechaPago);
        $codigo = trim((string) $codigo);
        $ciclo = trim((string) $ciclo);
        $tipoCliente = trim((string) $tipoCliente);
        $ctaBancaria = trim((string) $ctaBancaria);

        $tieneAlguno = $fechaPago !== '' || $codigo !== '' || $ciclo !== '' || $ctaBancaria !== '';
        if (!$tieneAlguno) {
            return Model::Responde(
                false,
                'Debe indicar al menos un filtro (Fecha, Crédito, Ciclo o Cta. bancaria).',
                null,
                'Filtros insuficientes'
            );
        }

        $empresa = ($empresa !== '' && strtoupper($empresa) !== '(TODAS)') ? trim($empresa) : ConciliacionRepository::EMPRESA_DEFAULT;

        $filas = $repo->getPagosPorConciliarMP($empresa, $fechaPago, $tipoCliente, $codigo, $ciclo, $ctaBancaria);

        $totalRegistros = count($filas);
        $totalImporte = 0;
        $totalConciliados = 0;
        $importeConciliados = 0;
        $totalNoConciliados = 0;
        $importeNoConciliados = 0;

        foreach ($filas as $f) {
            $monto = (float) ($f['CANTIDAD'] ?? 0);
            $totalImporte += $monto;
            $conciliado = isset($f['CONCILIADO']) ? trim((string) $f['CONCILIADO']) : '';
            if (strtoupper($conciliado) === 'C') {
                $totalConciliados++;
                $importeConciliados += $monto;
            } else {
                $totalNoConciliados++;
                $importeNoConciliados += $monto;
            }
        }

        $resumen = [
            'totalRegistros' => $totalRegistros,
            'totalImporte' => round($totalImporte, 2),
            'totalConciliados' => $totalConciliados,
            'totalNoConciliados' => $totalNoConciliados,
            'importeConciliados' => round($importeConciliados, 2),
            'importeNoConciliados' => round($importeNoConciliados, 2),
        ];

        return Model::Responde(true, $totalRegistros > 0 ? "Se encontraron un total de {$totalRegistros} pagos." : 'No hay pagos pendientes de conciliar.', [
            'filas' => $filas,
            'resumen' => $resumen,
        ]);
    }

    /**
     * Obtiene movimientos por fecha (PAGOSDIA) y resumen (compatibilidad).
     *
     * @param string $fecha Fecha en Y-m-d
     * @return array { success, mensaje, datos: { filas, resumen }, error }
     */
    public static function obtenerConciliacion($fecha)
    {
        $repo = new ConciliacionRepository();

        if (trim($fecha) === '') {
            return Model::Responde(false, 'La fecha es obligatoria.', null, 'Fecha vacía');
        }

        $hoy = date('Y-m-d');
        if ($fecha > $hoy) {
            return Model::Responde(false, 'No se puede consultar una fecha futura.', null, 'Fecha futura');
        }

        $filas = $repo->getMovimientosPorFecha($fecha);
        $totalRegistros = count($filas);
        $totalImporte = 0;
        $totalAplicados = 0;
        $importeAplicados = 0;
        $totalPendientes = 0;
        $importePendientes = 0;

        foreach ($filas as $f) {
            $monto = (float) ($f['MONTO'] ?? 0);
            $totalImporte += $monto;
            if (isset($f['F_IMPORTACION']) && $f['F_IMPORTACION'] !== null && $f['F_IMPORTACION'] !== '') {
                $totalAplicados++;
                $importeAplicados += $monto;
            } else {
                $totalPendientes++;
                $importePendientes += $monto;
            }
        }

        $resumen = [
            'totalRegistros' => $totalRegistros,
            'totalImporte' => round($totalImporte, 2),
            'totalAplicados' => $totalAplicados,
            'totalPendientes' => $totalPendientes,
            'importeAplicados' => round($importeAplicados, 2),
            'importePendientes' => round($importePendientes, 2),
        ];

        return Model::Responde(true, 'Consulta de conciliación.', [
            'filas' => $filas,
            'resumen' => $resumen,
        ]);
    }

    /**
     * Concilia los pagos seleccionados ejecutando spRedistribucionPagos por cada uno, dentro de una transacción.
     *
     * @param array $pagos Array de objetos pago con CDGEM, CDGCLNS, CICLO, CLNS (o TIPOCTE), FREALDEP, PERIODO, SECUENCIA, CANTIDAD, CDGCB
     * @param string $usuario Usuario que ejecuta
     * @return array { success, mensaje, error }
     */
    public static function conciliarPagos(array $pagos, $usuario)
    {
        if (empty($pagos) || !is_array($pagos)) {
            return Model::Responde(false, 'No hay pagos seleccionados para conciliar.', null, 'Pagos vacíos');
        }

        $usuario = trim((string) $usuario);
        if ($usuario === '') {
            return Model::Responde(false, 'Usuario no identificado.', null, 'Usuario vacío');
        }

        $identificador = date('dmY') . date('His');

        $repo = new ConciliacionRepository();
        $db = new Database();
        if ($db->db_activa === null) {
            return Model::Responde(false, 'No hay conexión a la base de datos.', null, 'Conexión nula');
        }

        $config = App::getConfig();
        $valor = $config['CONCILIACION_SOLO_FLUJO'] ?? ($config['conciliacion']['CONCILIACION_SOLO_FLUJO'] ?? null);
        $valorNorm = $valor === null ? null : strtolower(trim((string) $valor));
        $soloFlujo = $valorNorm !== null ? (filter_var($valorNorm, FILTER_VALIDATE_BOOLEAN) === true) : false;

        $logPath = defined('APPPATH') ? dirname(APPPATH) . '/logs/conciliacion_pagos.log' : __DIR__ . '/../../logs/conciliacion_pagos.log';

        try {
            $db->IniciaTransaccion();

            // Snapshot previo para detectar si realmente se afectó MP.
            $estadoAntes = $repo->obtenerEstadosConciliacionPagos($pagos, $db);
            $totalSeleccionados = count($pagos);
            $totalProcesados = 0;
            $totalDuplicadosCierreDia = 0;

            foreach ($pagos as $i => $pago) {
                if (!is_array($pago)) {
                    $db->CancelaTransaccion();
                    return Model::Responde(false, 'Datos del pago no válidos.', null, 'Pago no es array');
                }
                try {
                    $repo->ejecutarSpRedistribucionPagos($pago, $usuario, $identificador, $db);
                    $totalProcesados++;
                } catch (\Throwable $e) {
                    $mensajeError = (string) $e->getMessage();
                    $esDuplicadoCierreDia = stripos($mensajeError, 'ORA-00001') !== false
                        && stripos($mensajeError, 'TBL_CIERRE_DIA_PK') !== false;
                    if ($esDuplicadoCierreDia) {
                        $totalDuplicadosCierreDia++;
                        $totalProcesados++;
                        @file_put_contents(
                            $logPath,
                            date('c') . ' [' . $usuario . '] WARN duplicado CIERRE_DIA omitido en pago idx=' . $i . ' msg=' . $mensajeError . "\n",
                            FILE_APPEND
                        );
                        // Mantener comportamiento operativo: no detener todo el lote por un registro duplicado.
                        continue;
                    }
                    throw $e;
                }
            }

            // Snapshot posterior (aún dentro de la transacción) para validar afectación.
            $estadoDespues = $repo->obtenerEstadosConciliacionPagos($pagos, $db);

            $db->ConfirmaTransaccion();

            $afectados = 0;
            for ($idx = 0; $idx < $totalSeleccionados; $idx++) {
                $a = $estadoAntes[$idx] ?? ['encontrado' => false, 'conciliado' => null, 'estatus' => null];
                $d = $estadoDespues[$idx] ?? ['encontrado' => false, 'conciliado' => null, 'estatus' => null];
                if (empty($a['encontrado']) || empty($d['encontrado'])) {
                    continue;
                }
                if (($a['conciliado'] ?? null) !== ($d['conciliado'] ?? null) || ($a['estatus'] ?? null) !== ($d['estatus'] ?? null)) {
                    $afectados++;
                }
            }

            $mensajeLog = date('c')
                . ' [' . $usuario . ']'
                . ' soloFlujo=' . ($soloFlujo ? '1' : '0')
                . ' seleccionados=' . $totalSeleccionados
                . ' procesados=' . $totalProcesados
                . ' duplicados=' . $totalDuplicadosCierreDia
                . ' afectados=' . $afectados
                . "\n";
            @file_put_contents($logPath, $mensajeLog, FILE_APPEND);

            if ($soloFlujo) {
                return Model::Responde(true, 'Conciliación en modo solo flujo completada (sin cambios aplicados).');
            }

            // Comportamiento VB6: si el proceso termina sin error fatal, se considera completado.
            if ($totalProcesados <= 0) {
                return Model::Responde(false, 'No fue posible procesar pagos en la conciliación.', null, 'Sin procesamiento');
            }

            $mensaje = "Conciliación completada. Pagos procesados: {$totalProcesados} de {$totalSeleccionados}.";
            $mensaje .= " Pagos afectados: {$afectados}.";
            if ($totalDuplicadosCierreDia > 0) {
                $mensaje .= " Duplicados en CIERRE_DIA omitidos: {$totalDuplicadosCierreDia}.";
            }

            return Model::Responde(true, $mensaje, [
                'seleccionados' => $totalSeleccionados,
                'procesados' => $totalProcesados,
                'afectados' => $afectados,
                'duplicadosCierreDia' => $totalDuplicadosCierreDia,
            ]);
        } catch (\InvalidArgumentException $e) {
            if (isset($db) && $db->db_activa !== null) {
                $db->CancelaTransaccion();
            }
            @file_put_contents($logPath, date('c') . ' [' . $usuario . '] Error=InvalidArgumentException ' . $e->getMessage() . "\n", FILE_APPEND);
            return Model::Responde(false, $e->getMessage(), null, $e->getMessage());
        } catch (\Throwable $e) {
            if (isset($db) && $db->db_activa !== null) {
                $db->CancelaTransaccion();
            }
            @file_put_contents($logPath, date('c') . ' [' . $usuario . '] Error=Throwable ' . $e->getMessage() . "\n", FILE_APPEND);
            return Model::Responde(false, 'Error al conciliar: ' . $e->getMessage(), null, $e->getMessage());
        }
    }
}
