<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\App;
use Core\Database;
use Core\Model;
use App\repositories\PagosAplicacionRepository;

/**
 * Service: reglas de negocio para Aplicar Pagos.
 * Usa la misma lógica de datos que Layout Contable; solo orquesta consulta + SP + control de reproceso.
 * No modifica reglas contables ni Stored Procedures existentes.
 */
class PagosAplicacionService
{
    /**
     * Valida y obtiene resumen: si la fecha ya fue procesada devuelve el resumen guardado; si no, valida y opcionalmente ejecuta.
     *
     * @param string $fecha Fecha en Y-m-d
     * @param string $usuario Usuario que ejecuta
     * @param bool $ejecutar Si true, ejecuta el proceso; si false, solo valida y devuelve datos a mostrar
     * @return array { success, mensaje, datos, error } con datos.resumen, datos.filas, datos.yaProcesado, etc.
     */
    public static function procesarOResumen($fecha, $usuario, $ejecutar = false)
    {
        $repo = new PagosAplicacionRepository();

        if (trim($fecha) === '') {
            return Model::Responde(false, 'La fecha es obligatoria.', null, 'Fecha vacía');
        }

        $hoy = date('Y-m-d');
        if ($fecha > $hoy) {
            return Model::Responde(false, 'No se puede procesar una fecha futura.', null, 'Fecha futura');
        }

        $filas = $repo->getDatosLayoutPorFecha($fecha);
        if (empty($filas)) {
            return Model::Responde(false, 'No hay datos para la fecha seleccionada.', [
                'filas' => [],
                'resumen' => null,
            ], 'Sin datos');
        }

        $clavesAplicadas = $repo->obtenerClavesAplicadas($fecha);
        $setClaves = array_flip($clavesAplicadas);

        $ya = $repo->obtenerProcesado($fecha);
        $fechaEjec = '';
        if ($ya !== null && isset($ya['FECHA_EJECUCION'])) {
            $fe = $ya['FECHA_EJECUCION'];
            if (is_object($fe) && method_exists($fe, 'format')) {
                $fechaEjec = $fe->format('Y-m-d H:i:s');
            } else {
                $fechaEjec = (string) $fe;
            }
        }

        foreach ($filas as $i => $f) {
            $ref = isset($f['REFERENCIA']) ? trim((string) $f['REFERENCIA']) : '';
            $monto = isset($f['MONTO']) ? (float) $f['MONTO'] : 0;
            $fec = isset($f['FECHA']) ? (string) $f['FECHA'] : $fecha;
            $fecPart = strlen($fec) >= 10 ? substr($fec, 0, 10) : $fecha;
            $key = $ref . '|' . $monto . '|' . $fecPart;
            $filas[$i]['aplicado'] = isset($setClaves[$key]);
        }

        $totalImporte = 0;
        $totalAplicados = 0;
        $totalPendientes = 0;
        $importeAplicados = 0;
        $importePendientes = 0;
        foreach ($filas as $f) {
            $m = (float) ($f['MONTO'] ?? 0);
            $totalImporte += $m;
            if (!empty($f['aplicado'])) {
                $totalAplicados++;
                $importeAplicados += $m;
            } else {
                $totalPendientes++;
                $importePendientes += $m;
            }
        }

        if (!$ejecutar) {
            $resumen = [
                'totalRegistros' => count($filas),
                'totalImporte' => round($totalImporte, 2),
                'totalAplicados' => $totalAplicados,
                'totalPendientes' => $totalPendientes,
                'importeAplicados' => round($importeAplicados, 2),
                'importePendientes' => round($importePendientes, 2),
            ];
            if ($ya !== null) {
                $resumen['usuario'] = $ya['USUARIO'] ?? '';
                $resumen['fechaEjecucion'] = $fechaEjec;
                $resumen['estado'] = $ya['ESTADO'] ?? '';
                $resumen['mensaje'] = $ya['MENSAJE'] ?? null;
            }
            return Model::Responde(true, 'Datos listos para procesar.', [
                'yaProcesado' => $ya !== null,
                'filas' => $filas,
                'resumen' => $resumen,
            ]);
        }

        $filasPendientes = array_values(array_filter($filas, function ($f) {
            return empty($f['aplicado']);
        }));

        if (empty($filasPendientes)) {
            return Model::Responde(true, 'No hay pagos pendientes para aplicar en esta fecha.', [
                'yaProcesado' => $ya !== null,
                'filas' => $filas,
                'resumen' => [
                    'totalRegistros' => count($filas),
                    'totalImporte' => round($totalImporte, 2),
                    'totalAplicados' => $totalAplicados,
                    'totalPendientes' => 0,
                    'importeAplicados' => round($importeAplicados, 2),
                    'importePendientes' => 0,
                    'fechaEjecucion' => $fechaEjec,
                ],
            ]);
        }

        return self::ejecutarAplicacion($fecha, $usuario, $filasPendientes, $repo);
    }

    /**
     * Ejecuta la aplicación de pagos: transacción, SP por fila, insert PAGOS_PROCESADOS.
     *
     * @param string $fecha Y-m-d
     * @param string $usuario
     * @param array $filas Lista de filas (FECHA, REFERENCIA, MONTO, MONEDA)
     * @param PagosAplicacionRepository|null $repo
     * @return array
     */
    private static function ejecutarAplicacion($fecha, $usuario, array $filas, PagosAplicacionRepository $repo = null)
    {
        if ($repo === null) {
            $repo = new PagosAplicacionRepository();
        }

        $db = new Database();
        if ($db->db_activa === null) {
            return Model::Responde(false, 'No hay conexión a base de datos.', null, 'Conexión nula');
        }

        $identificador = date('YmdHis') . '_' . substr(str_replace(['-', ' ', ':'], '', microtime(false)), -4) . '_' . $usuario;
        $idImportacion = (int) time();
        $noPagos = count($filas);
        $detalle = [];
        $totalImporte = 0;
        $logPath = defined('APPPATH') ? dirname(APPPATH) . '/logs/aplicar_pagos.log' : __DIR__ . '/../../logs/aplicar_pagos.log';

        try {
            $db->IniciaTransaccion();

            foreach ($filas as $renglon => $f) {
                $renglon1 = $renglon + 1;
                $monto = isset($f['MONTO']) ? (float) $f['MONTO'] : 0;
                $totalImporte += $monto;
                $referencia = isset($f['REFERENCIA']) ? trim((string) $f['REFERENCIA']) : '';
                $moneda = isset($f['MONEDA']) ? trim((string) $f['MONEDA']) : 'MN';

                $fechaPago = $f['FECHA'] ?? $fecha;
                if (is_object($fechaPago)) {
                    $fechaPago = $fechaPago->format('Y-m-d H:i:s');
                } elseif (is_string($fechaPago) && strlen($fechaPago) <= 10) {
                    $fechaPago = $fechaPago . ' ' . date('H:i:s');
                }

                $res = $repo->ejecutarSpImportaPago(
                    $fechaPago,
                    $referencia,
                    (string) $monto,
                    $usuario,
                    $identificador,
                    $renglon1,
                    $renglon1,
                    $noPagos,
                    $idImportacion,
                    $moneda,
                    null,
                    $db
                );

                $detalle[] = [
                    'renglon' => $renglon1,
                    'fecha' => $fechaPago,
                    'referencia' => $referencia,
                    'monto' => $monto,
                    'moneda' => $moneda,
                    'ok' => $res['success'] && (int) $res['validacion'] === 1,
                    'resultado' => $res['resultado'] ?? '',
                    'validacion' => $res['validacion'] ?? -1,
                ];

                if (!$res['success']) {
                    throw new \RuntimeException('SP falló en renglón ' . $renglon1 . ': ' . ($res['resultado'] ?? 'sin mensaje'));
                }
                if ((int) ($res['validacion'] ?? -1) !== 1) {
                    throw new \RuntimeException('Validación del SP en renglón ' . $renglon1 . ': ' . ($res['resultado'] ?? 'código ' . ($res['validacion'] ?? '')));
                }
            }

            $config = App::getConfig();
            $valor = $config['APLICAR_PAGOS_SOLO_FLUJO'] ?? (isset($config['aplicar_pagos']) && is_array($config['aplicar_pagos']) ? ($config['aplicar_pagos']['APLICAR_PAGOS_SOLO_FLUJO'] ?? null) : null);
            $soloFlujo = $valor !== null && (filter_var($valor, FILTER_VALIDATE_BOOLEAN) || $valor === 'true' || $valor === '1');

            if (!$soloFlujo) {
                $detalleJson = json_encode($detalle, JSON_UNESCAPED_UNICODE);
                $okInsert = $repo->insertarOActualizarProcesado(
                    $fecha,
                    $noPagos,
                    round($totalImporte, 2),
                    $usuario,
                    'OK',
                    null,
                    $detalleJson,
                    $db
                );

                if (!$okInsert) {
                    throw new \RuntimeException('Error al registrar PAGOS_PROCESADOS');
                }
            }

            $db->ConfirmaTransaccion();

            $mensajeExito = $soloFlujo
                ? 'Flujo de prueba completado (sin cambios en BD ni registro en PAGOS_PROCESADOS).'
                : 'Pagos aplicados correctamente.';

            $filasCompletas = $filas;
            if (!$soloFlujo) {
                $clavesAhora = $repo->obtenerClavesAplicadas($fecha);
                $setAhora = array_flip($clavesAhora);
                $todas = $repo->getDatosLayoutPorFecha($fecha);
                foreach ($todas as $i => $f) {
                    $ref = isset($f['REFERENCIA']) ? trim((string) $f['REFERENCIA']) : '';
                    $monto = isset($f['MONTO']) ? (float) $f['MONTO'] : 0;
                    $fec = isset($f['FECHA']) ? (string) $f['FECHA'] : $fecha;
                    $fecPart = strlen($fec) >= 10 ? substr($fec, 0, 10) : $fecha;
                    $key = $ref . '|' . $monto . '|' . $fecPart;
                    $todas[$i]['aplicado'] = isset($setAhora[$key]);
                }
                $filasCompletas = $todas;
            } else {
                foreach ($filasCompletas as $i => $f) {
                    $filasCompletas[$i]['aplicado'] = true;
                }
            }

            $totalApl = 0;
            $totalPen = 0;
            $impApl = 0;
            $impPen = 0;
            $impTotal = 0;
            foreach ($filasCompletas as $f) {
                $m = (float) ($f['MONTO'] ?? 0);
                $impTotal += $m;
                if (!empty($f['aplicado'])) {
                    $totalApl++;
                    $impApl += $m;
                } else {
                    $totalPen++;
                    $impPen += $m;
                }
            }

            return Model::Responde(true, $mensajeExito, [
                'yaProcesado' => false,
                'modoPrueba'  => $soloFlujo,
                'resumen' => [
                    'totalRegistros' => count($filasCompletas),
                    'totalImporte' => round($impTotal, 2),
                    'totalAplicados' => $totalApl,
                    'totalPendientes' => $totalPen,
                    'importeAplicados' => round($impApl, 2),
                    'importePendientes' => round($impPen, 2),
                    'usuario' => $usuario,
                    'fechaEjecucion' => date('Y-m-d H:i:s'),
                    'estado' => 'OK',
                    'mensaje' => null,
                ],
                'filas' => $filasCompletas,
                'detalle' => $detalle,
            ]);
        } catch (\Throwable $e) {
            if ($db->db_activa !== null) {
                $db->CancelaTransaccion();
            }
            $mensajeLog = date('c') . ' [' . $usuario . '] Fecha=' . $fecha . ' Error=' . $e->getMessage() . "\n";
            @file_put_contents($logPath, $mensajeLog, FILE_APPEND);
            return Model::Responde(false, 'Error al aplicar pagos. No se realizaron cambios.', null, $e->getMessage());
        }
    }
}
