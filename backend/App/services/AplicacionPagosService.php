<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;
use App\repositories\CierreDiaRepository;
use App\repositories\PagosAplicacionRepository;
use App\repositories\AplicacionPagosRepository;

/**
 * Service: importación y conciliación de pagos registrados después del cierre de día.
 * Ejecuta únicamente la parte de importación/conciliación de SP_PAGOS_CIERRE_DEVENGO
 * (sin TBL_CIERRE_DIA ni devengo), para fechas cuyo cierre ya concluyó con éxito.
 */
class AplicacionPagosService
{
    /** Días hacia atrás para detectar fechas con pagos pendientes. */
    const DIAS_DETECCION = 30;

    /** Motivo del último adquirirCandado() fallido por no poder crear el archivo (vacío si estaba ocupado). */
    private static $errorCandado = '';

    /**
     * Fechas recientes con pagos pendientes de importar.
     *
     * @return array { success, mensaje, datos: { fechas } }
     */
    public static function fechasConPendientes()
    {
        $repo = new AplicacionPagosRepository();
        $fechas = $repo->getFechasConPendientes(self::DIAS_DETECCION);

        return Model::Responde(true, 'OK', ['fechas' => $fechas]);
    }

    /**
     * Pagos pendientes de la fecha, estado del cierre y si se pueden procesar.
     *
     * @param string $fecha Y-m-d o d/m/Y
     * @return array { success, mensaje, datos: { fecha, filas, resumen, cierre, puedeProcesar } }
     */
    public static function consultar($fecha)
    {
        $fechaNorm = PagosAplicacionRepository::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return Model::Responde(false, 'Seleccione una fecha válida.', null, 'Fecha inválida');
        }
        if ($fechaNorm > date('Y-m-d')) {
            return Model::Responde(false, 'No se puede consultar una fecha futura.', null, 'Fecha futura');
        }

        $repo = new AplicacionPagosRepository();
        try {
            $filas = $repo->getPagosPendientes($fechaNorm);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al consultar los pagos pendientes.', null, $e->getMessage());
        }

        $estado = self::estadoCierre($fechaNorm);
        $resumen = self::resumenPendientes($filas);
        $puedeProcesar = $estado['ejecutado'] && !$estado['enEjecucion'] && $resumen['totalPagos'] > 0;

        if ($resumen['totalPagos'] === 0) {
            $mensaje = 'No hay pagos pendientes de importar para la fecha seleccionada.';
        } elseif ($estado['enEjecucion']) {
            $mensaje = 'Hay un cierre de día en ejecución. Espere a que termine para procesar los pagos.';
        } elseif (!$estado['ejecutado']) {
            $mensaje = 'El cierre de esta fecha no ha concluido con éxito. Los pagos pendientes se procesarán al ejecutar el cierre de día.';
        } else {
            $mensaje = 'Se encontraron ' . $resumen['totalPagos'] . ' pagos pendientes de importar.';
        }

        return Model::Responde(true, $mensaje, [
            'fecha' => $fechaNorm,
            'filas' => array_values($filas),
            'resumen' => $resumen,
            'cierre' => $estado,
            'puedeProcesar' => $puedeProcesar,
        ]);
    }

    /**
     * Importa y concilia los pagos pendientes de la fecha. Los SP internos confirman cada pago,
     * por lo que ante un error se detiene y conserva lo ya procesado.
     *
     * @param string $fecha Y-m-d
     * @param string $usuario
     * @param array|null $claves Pagos a procesar ("CDGNS|CICLO|SECUENCIA"); null = todos los pendientes
     * @return array { success, mensaje, datos: { idImportacion, detalle, resumen, consulta } }
     */
    public static function procesar($fecha, $usuario, $claves = null)
    {
        $usuario = trim((string) $usuario);
        if ($usuario === '') {
            return Model::Responde(false, 'Usuario no identificado.', null, 'Usuario vacío');
        }

        $seleccion = null;
        if ($claves !== null) {
            $seleccion = [];
            foreach ((array) $claves as $clave) {
                $clave = trim((string) $clave);
                if (preg_match('/^[0-9A-Za-z]+\|[0-9A-Za-z]+\|\d+$/', $clave)) {
                    $seleccion[$clave] = true;
                }
            }
            if (empty($seleccion)) {
                return Model::Responde(false, 'Seleccione al menos un pago para importar y conciliar.', null, 'Sin selección');
            }
        }

        $fechaNorm = PagosAplicacionRepository::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return Model::Responde(false, 'Seleccione una fecha válida.', null, 'Fecha inválida');
        }
        if ($fechaNorm > date('Y-m-d')) {
            return Model::Responde(false, 'No se puede procesar una fecha futura.', null, 'Fecha futura');
        }

        $estado = self::estadoCierre($fechaNorm);
        if ($estado['enEjecucion']) {
            return Model::Responde(false, 'Hay un cierre de día en ejecución. Espere a que termine para procesar los pagos.', null, 'Cierre en ejecución');
        }
        if (!$estado['ejecutado']) {
            return Model::Responde(false, 'El cierre de esta fecha no ha concluido con éxito. Los pagos pendientes se procesarán al ejecutar el cierre de día.', null, 'Cierre no ejecutado');
        }

        $candado = self::adquirirCandado();
        if ($candado === null) {
            if (self::$errorCandado !== '') {
                return Model::Responde(false, 'No fue posible iniciar el proceso: no se pudo crear el archivo de control. Revise los permisos de escritura de la carpeta de logs del sistema.', null, self::$errorCandado);
            }
            return Model::Responde(false, 'Ya hay un proceso de aplicación de pagos en ejecución. Intente de nuevo en unos minutos.', null, 'Concurrencia');
        }

        $logPath = self::archivoLog();
        $repo = new AplicacionPagosRepository();
        $db = new Database();
        if ($db->db_activa === null) {
            self::liberarCandado($candado);
            return Model::Responde(false, 'No hay conexión a la base de datos.', null, 'Conexión nula');
        }

        try {
            $db->IniciaTransaccion();

            $pendientes = $repo->getPagosPendientes($fechaNorm, $db);
            if (empty($pendientes)) {
                $db->CancelaTransaccion();
                return Model::Responde(false, 'No hay pagos pendientes de importar para la fecha seleccionada.', null, 'Sin pendientes');
            }

            if ($seleccion !== null) {
                $pendientes = array_values(array_filter($pendientes, function ($p) use ($seleccion) {
                    return isset($seleccion[$p['CDGNS'] . '|' . $p['CICLO'] . '|' . $p['SECUENCIA']]);
                }));
                if (empty($pendientes)) {
                    $db->CancelaTransaccion();
                    return Model::Responde(false, 'Los pagos seleccionados ya no están pendientes de importar. Consulte la fecha de nuevo.', null, 'Selección sin pendientes');
                }
            }

            $idImportacion = $repo->siguienteIdImportacion($db);
            $identificador = date('dmYHis');
            $totalPendientes = count($pendientes);
            $totalFilas = 0;
            $totalMonto = 0;
            $detalle = [];
            $errorProceso = null;

            foreach ($pendientes as $i => $pago) {
                $fila = $i + 1;

                $res = $repo->ejecutarImportaConcilia($pago, $fila, $totalPendientes, $idImportacion, $usuario, $identificador, $db);
                if (!$res['success']) {
                    // Los SP internos confirman (COMMIT) cada pago: los anteriores ya no se pueden revertir,
                    // así que se detiene aquí y se registra lo procesado hasta el momento.
                    $errorProceso = 'Error en el pago ' . $pago['CDGNS'] . '-' . $pago['CICLO'] . ' (secuencia ' . $pago['SECUENCIA'] . '): ' . $res['resultado'];
                    $detalle[] = [
                        'SECUENCIA' => $pago['SECUENCIA'],
                        'CDGNS' => $pago['CDGNS'],
                        'CICLO' => $pago['CICLO'],
                        'TIPO' => $pago['TIPO'],
                        'REFERENCIA' => $pago['REFERENCIA'],
                        'MONTO' => (float) $pago['MONTO'],
                        'VALIDACION' => -1,
                        'ESTADO' => 'Error en el proceso',
                        'CORRECTO' => false,
                        'PROCESADO' => false,
                        'MENSAJE' => 'No se completó la importación; el pago sigue pendiente. Verifique en importaciones que no se haya registrado antes de volver a procesarlo.',
                    ];
                    break;
                }

                $repo->marcarPagoProcesado($pago, $idImportacion, $db);
                $totalFilas++;
                $totalMonto += (float) $pago['MONTO'];

                $val = (int) $res['validacion'];
                $motivo = in_array($val, [0, 1, 6], true) ? '' : $repo->getMotivoValidacion($identificador, $pago['SECUENCIA'], $val, $db);
                $resultado = self::clasificarResultado($pago, $res, $motivo);
                $detalle[] = [
                    'SECUENCIA' => $pago['SECUENCIA'],
                    'CDGNS' => $pago['CDGNS'],
                    'CICLO' => $pago['CICLO'],
                    'TIPO' => $pago['TIPO'],
                    'REFERENCIA' => $pago['REFERENCIA'],
                    'MONTO' => (float) $pago['MONTO'],
                    'VALIDACION' => (int) $res['validacion'],
                    'ESTADO' => $resultado['estado'],
                    'CORRECTO' => $resultado['correcto'],
                    'PROCESADO' => true,
                    'MENSAJE' => $resultado['mensaje'],
                ];
            }

            if ($totalFilas > 0 && $totalMonto > 0) {
                $repo->registrarResumenImportacion($idImportacion, $fechaNorm, $totalFilas, round($totalMonto, 2), $identificador, $db);
            }

            $db->ConfirmaTransaccion();
        } catch (\Throwable $e) {
            try {
                $db->CancelaTransaccion();
            } catch (\Throwable $e2) {
                // Sin transacción activa
            }
            @file_put_contents($logPath, date('c') . ' [' . $usuario . '] Fecha=' . $fechaNorm . ' Error=' . $e->getMessage() . "\n", FILE_APPEND);
            return Model::Responde(false, 'Ocurrió un error al procesar los pagos. Algunos pudieron quedar registrados: consulte la fecha de nuevo para ver los que siguen pendientes.', null, $e->getMessage());
        } finally {
            self::liberarCandado($candado);
        }

        $correctos = count(array_filter($detalle, function ($d) {
            return $d['CORRECTO'];
        }));
        $conObservacion = count($detalle) - $correctos;
        $sinProcesar = $totalPendientes - $totalFilas;

        @file_put_contents(
            $logPath,
            date('c') . ' [' . $usuario . '] Fecha=' . $fechaNorm
                . ' IdImportacion=' . $idImportacion
                . ' Identificador=' . $identificador
                . ' Pagos=' . $totalFilas
                . ' Monto=' . round($totalMonto, 2)
                . ' Correctos=' . $correctos
                . ' ConObservacion=' . $conObservacion
                . ' SinProcesar=' . $sinProcesar
                . ($errorProceso !== null ? ' Error=' . $errorProceso : '')
                . "\n",
            FILE_APPEND
        );

        $mensaje = "Se procesaron {$totalFilas} pagos. Correctos: {$correctos}.";
        if ($conObservacion > 0) {
            $mensaje .= " Con observaciones: {$conObservacion} (revise el detalle).";
        }
        if ($errorProceso !== null) {
            $mensaje .= " El proceso se detuvo por un error; {$sinProcesar} registro(s) siguen pendientes.";
        }

        $consulta = self::consultar($fechaNorm);

        return Model::Responde(true, $mensaje, [
            'idImportacion' => $idImportacion,
            'detalle' => $detalle,
            'resumen' => [
                'totalPagos' => $totalFilas,
                'totalMonto' => round($totalMonto, 2),
                'correctos' => $correctos,
                'conObservacion' => $conObservacion,
                'sinProcesar' => $sinProcesar,
                'detenido' => $errorProceso !== null,
            ],
            'consulta' => ($consulta['success'] ?? false) ? ($consulta['datos'] ?? null) : null,
        ]);
    }

    /**
     * Interpreta el resultado de SP_IMPORTA_CONCILIA:
     * 1 = pago importado y conciliado (o garantía identificada); 0/6 = importado sin conciliar; otro = incidencia.
     *
     * @param array $pago
     * @param array $res
     * @param string $motivo Motivo registrado en RES_IMPOR para las incidencias
     * @return array { estado, correcto, mensaje }
     */
    private static function clasificarResultado(array $pago, array $res, $motivo = '')
    {
        $val = (int) $res['validacion'];
        $mensaje = trim((string) $res['resultado']);

        if ($val === 1) {
            return [
                'estado' => $pago['TIPO'] === 'G' ? 'Garantía importada' : 'Importado y conciliado',
                'correcto' => true,
                'mensaje' => '',
            ];
        }
        if ($val === 0 || $val === 6) {
            return [
                'estado' => 'Importado sin conciliar',
                'correcto' => false,
                'mensaje' => $mensaje !== '' ? $mensaje : 'No fue posible conciliar el pago.',
            ];
        }

        $significados = [
            2 => 'Pago no identificado',
        ];
        $significado = $significados[$val] ?? 'Código de validación ' . $val;

        if ($motivo !== '') {
            $mensaje = $significado . ': ' . $motivo;
        } elseif ($mensaje === '' || stripos($mensaje, 'dio un codigo') !== false) {
            $mensaje = $significado . '.';
        }

        return [
            'estado' => 'Incidencia de importación',
            'correcto' => false,
            'mensaje' => $mensaje,
        ];
    }

    /**
     * @param array $filas
     * @return array
     */
    private static function resumenPendientes(array $filas)
    {
        $resumen = [
            'totalPagos' => 0,
            'totalMonto' => 0.0,
            'pagos' => 0,
            'montoPagos' => 0.0,
            'garantias' => 0,
            'montoGarantias' => 0.0,
        ];
        foreach ($filas as $f) {
            $monto = (float) ($f['MONTO'] ?? 0);
            $resumen['totalPagos']++;
            $resumen['totalMonto'] += $monto;
            if (($f['TIPO'] ?? '') === 'G') {
                $resumen['garantias']++;
                $resumen['montoGarantias'] += $monto;
            } else {
                $resumen['pagos']++;
                $resumen['montoPagos'] += $monto;
            }
        }
        $resumen['totalMonto'] = round($resumen['totalMonto'], 2);
        $resumen['montoPagos'] = round($resumen['montoPagos'], 2);
        $resumen['montoGarantias'] = round($resumen['montoGarantias'], 2);

        return $resumen;
    }

    /**
     * Estado del cierre de la fecha: si ya concluyó con éxito y si hay algún cierre en ejecución.
     *
     * @param string $fecha Y-m-d
     * @return array { ejecutado, enEjecucion }
     */
    private static function estadoCierre($fecha)
    {
        $repo = new CierreDiaRepository();
        $sp = $repo->getEstatusSpPorFecha($fecha);
        $ejecutado = $sp !== null
            && (int) ($sp['EN_PROCESO'] ?? 0) === 0
            && (int) ($sp['EXITO'] ?? 0) === 1;
        $enEjecucion = !empty($repo->validaCierreEnEjecucion()) || CierreDiaJobLock::jobActivo();

        return [
            'ejecutado' => $ejecutado,
            'enEjecucion' => $enEjecucion,
        ];
    }

    /**
     * @return string
     */
    private static function archivoLog()
    {
        return defined('APPPATH') ? dirname(APPPATH) . '/logs/aplicacion_pagos.log' : __DIR__ . '/../../logs/aplicacion_pagos.log';
    }

    /**
     * Candado exclusivo no bloqueante: evita dos lotes simultáneos con el mismo ID_IMPORTACION.
     *
     * @return resource|null
     */
    private static function adquirirCandado()
    {
        self::$errorCandado = '';
        $archivo = defined('APPPATH') ? dirname(APPPATH) . '/logs/aplicacion_pagos.lock' : __DIR__ . '/../../logs/aplicacion_pagos.lock';
        $dir = dirname($archivo);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $fp = @fopen($archivo, 'c+');
        if (!is_resource($fp)) {
            self::$errorCandado = 'No se pudo abrir el archivo de candado: ' . $archivo;
            return null;
        }
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            fclose($fp);
            return null;
        }
        return $fp;
    }

    /**
     * @param resource|null $fp
     * @return void
     */
    private static function liberarCandado($fp)
    {
        if (!is_resource($fp)) {
            return;
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
