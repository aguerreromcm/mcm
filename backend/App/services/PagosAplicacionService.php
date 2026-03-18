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

        $pendientes = [];
        $importePend = 0;
        $importeApl = 0;
        foreach ($filas as $f) {
            $m = (float) ($f['MONTO'] ?? 0);
            if (isset($f['F_IMPORTACION']) && $f['F_IMPORTACION'] !== null && $f['F_IMPORTACION'] !== '') {
                $importeApl += $m;
            } else {
                $pendientes[] = $f;
                $importePend += $m;
            }
        }
        $totalImporte = $importePend + $importeApl;
        $totalPendientes = count($pendientes);
        $totalAplicados = count($filas) - $totalPendientes;

        $ya = $repo->obtenerProcesado($fecha);
        $fechaEjec = '-';
        if ($ya !== null && isset($ya['FECHA_EJECUCION'])) {
            $fechaEjec = $ya['FECHA_EJECUCION'];
            if (is_object($fechaEjec) && method_exists($fechaEjec, 'format')) {
                $fechaEjec = $fechaEjec->format('Y-m-d H:i:s');
            } else {
                $fechaEjec = (string) $fechaEjec;
            }
        }

        $resumenBase = [
            'totalRegistros' => count($filas),
            'totalImporte' => round($totalImporte, 2),
            'totalPendientes' => $totalPendientes,
            'totalAplicados' => $totalAplicados,
            'importePendientes' => round($importePend, 2),
            'importeAplicados' => round($totalImporte - $importePend, 2),
            'fechaEjecucion' => $fechaEjec,
            'estado' => $totalPendientes === 0
                ? ($totalAplicados > 0 ? 'Procesado' : '')
                : ($totalAplicados > 0 ? 'Parcial' : 'Pendiente'),
            'mensaje' => null,
        ];
        if ($ya !== null) {
            $resumenBase['usuario'] = $ya['USUARIO'] ?? '';
            $resumenBase['estado'] = $ya['ESTADO'] ?? $resumenBase['estado'];
        }

        if (!$ejecutar) {
            return Model::Responde(true, 'Datos listos para procesar.', [
                'yaProcesado' => count($pendientes) === 0,
                'filas' => $filas,
                'resumen' => $resumenBase,
            ]);
        }

        if (count($pendientes) === 0) {
            return Model::Responde(true, 'No hay pagos pendientes para esta fecha.', [
                'yaProcesado' => true,
                'filas' => $filas,
                'resumen' => $resumenBase,
            ]);
        }

        return self::ejecutarAplicacion($fecha, $usuario, $pendientes, $repo);
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

        $config = App::getConfig();
        $valorConfig = $config['APLICAR_PAGOS_SOLO_FLUJO'] ?? (isset($config['aplicar_pagos']) && is_array($config['aplicar_pagos']) ? ($config['aplicar_pagos']['APLICAR_PAGOS_SOLO_FLUJO'] ?? null) : null);
        $soloFlujo = $valorConfig !== null && (filter_var($valorConfig, FILTER_VALIDATE_BOOLEAN) || $valorConfig === 'true' || $valorConfig === '1');

        // Replica el identificador VB6: Format(Date, "DDMMYYYY") & Format(Time, "HHNNSS")
        // (14 dígitos). Esto evita errores de parseo dentro del SP (p. ej. ORA-01830).
        $identificador = date('dmY') . date('His');
        $idImportacion = (int) time();
        $noPagos = count($filas);
        $detalle = [];
        $totalImporte = 0;
        $logPath = defined('APPPATH') ? dirname(APPPATH) . '/logs/aplicar_pagos.log' : __DIR__ . '/../../logs/aplicar_pagos.log';
        // VB6 arma la fecha para el SP con fecha y hora (formato dependiente del SP).
        // Generamos candidatos equivalentes y ante ORA-01861/01830 reintentamos.
        $fechaPagoCandidatos = self::generarCandidatosFechaParaSp($fecha);

        try {
            $db->IniciaTransaccion();

            foreach ($filas as $renglon => $f) {
                $renglon1 = $renglon + 1;
                $monto = isset($f['MONTO']) ? (float) $f['MONTO'] : 0;
                $totalImporte += $monto;
                $referencia = isset($f['REFERENCIA']) ? trim((string) $f['REFERENCIA']) : '';
                $moneda = isset($f['MONEDA']) ? trim((string) $f['MONEDA']) : 'MN';

                // Reintentos por formato de fecha/hora.
                $ultimoError = null;
                $ultimaFechaIntentada = '';
                $res = null;
                foreach ($fechaPagoCandidatos as $i => $fechaPagoParaSp) {
                    $ultimaFechaIntentada = $fechaPagoParaSp;
                    try {
                        $res = $repo->ejecutarSpImportaPago(
                            $fechaPagoParaSp,
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
                        $ultimoError = null;
                        break;
                    } catch (\Throwable $e) {
                        $ultimoError = $e;
                        $msg = (string) $e->getMessage();
                        // Solo reintentamos cuando el error es por parsing de fecha.
                        if (strpos($msg, 'ORA-01861') === false && strpos($msg, 'ORA-01830') === false) {
                            throw $e;
                        }
                    }
                }
                if ($res === null) {
                    $msg = $ultimoError ? $ultimoError->getMessage() : 'Error al convertir fecha para SP.';
                    throw new \RuntimeException($msg . ' | fecha intento=' . $ultimaFechaIntentada);
                }

                $detalle[] = [
                    'renglon' => $renglon1,
                    'fecha' => $fechaPagoParaSp,
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

                if (!$soloFlujo && isset($f['CDGEM'], $f['CDGNS'], $f['CICLO'], $f['SECUENCIA'])) {
                    $repo->actualizarFImportacion(
                        $f['CDGEM'],
                        $f['CDGNS'],
                        $f['CICLO'],
                        // actualizarFImportacion espera una fecha tipo YYYY-MM-DD para TRUNC(FECHA).
                        $fecha,
                        $f['SECUENCIA'],
                        $db
                    );
                }
            }

            if (!$soloFlujo) {
                $detalleJson = json_encode($detalle, JSON_UNESCAPED_UNICODE);
                $okInsert = $repo->insertarProcesado(
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

            // Re-consultamos para que el resumen y el estatus por renglón coincidan con BD.
            $filasActualizadas = $repo->getDatosLayoutPorFecha($fecha);
            $pendientes = [];
            $importePend = 0;
            $importeApl = 0;
            foreach ($filasActualizadas as $f) {
                $m = (float) ($f['MONTO'] ?? 0);
                if (isset($f['F_IMPORTACION']) && $f['F_IMPORTACION'] !== null && $f['F_IMPORTACION'] !== '') {
                    $importeApl += $m;
                } else {
                    $pendientes[] = $f;
                    $importePend += $m;
                }
            }
            $totalRegistros = count($filasActualizadas);
            $totalPendientes = count($pendientes);
            $totalAplicados = $totalRegistros - $totalPendientes;
            $totalImporteFinal = $importePend + $importeApl;

            // Corregimos la lógica de "estado": no es "Parcial" cuando no hay pendientes.
            if ($totalPendientes === 0) {
                $estadoFinal = $totalAplicados > 0 ? 'Procesado' : '';
            } elseif ($totalAplicados > 0) {
                $estadoFinal = 'Parcial';
            } else {
                $estadoFinal = 'Pendiente';
            }

            return Model::Responde(true, $mensajeExito, [
                'yaProcesado' => $totalPendientes === 0,
                'modoPrueba'  => $soloFlujo,
                'resumen' => [
                    'usuario' => $usuario,
                    'fechaEjecucion' => date('Y-m-d H:i:s'),
                    'totalRegistros' => $totalRegistros,
                    'totalImporte' => round($totalImporteFinal, 2),
                    'totalPendientes' => $totalPendientes,
                    'totalAplicados' => $totalAplicados,
                    'importePendientes' => round($importePend, 2),
                    'importeAplicados' => round($importeApl, 2),
                    'estado' => $estadoFinal,
                    'mensaje' => null,
                ],
                'filas' => $filasActualizadas,
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

    /**
     * Replica candidatos equivalentes a VB6 para p_fecha del SP.
     * El SP real puede convertir internamente con distintos pictures; por eso probamos varias variantes.
     *
     * @return string[] ordenados por probabilidad (primero el más cercano a VB6)
     */
    private static function generarCandidatosFechaParaSp(string $fecha): array
    {
        $fecha = trim($fecha);
        if ($fecha === '') return [];

        $fecha = str_replace('-', '/', $fecha); // YYYY/MM/DD
        $fecha12 = date('h:i:s'); // 12-hour clock
        $fecha24 = date('H:i:s'); // 24-hour clock

        // VB6 concatena: Format(fecha,"YYYY/MM/DD") & Format(Time," hh:mm:ss")
        // y el " hh:mm:ss" suele traer un espacio previo al hour.
        $c1 = $fecha . ' ' . $fecha24; // YYYY/MM/DD HH:MM:SS
        $c2 = $fecha . ' ' . $fecha12; // YYYY/MM/DD hh:MM:SS
        $c3 = $fecha; // YYYY/MM/DD (solo fecha)

        // Variante day-first por si el SP usa DD/MM/YYYY internamente.
        $fechaDmY = date('d/m/Y', strtotime(str_replace('/', '-', $fecha))); // DD/MM/YYYY
        $c4 = $fechaDmY . ' ' . $fecha24; // DD/MM/YYYY HH:MM:SS
        $c5 = $fechaDmY . ' ' . $fecha12; // DD/MM/YYYY hh:MM:SS
        $c6 = $fechaDmY; // DD/MM/YYYY

        return [$c1, $c2, $c3, $c4, $c5, $c6];
    }
}
