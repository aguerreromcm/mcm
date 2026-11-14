<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Model;
use App\repositories\AjustesPrecierreRepository;
use App\repositories\PagosAplicacionRepository;

/**
 * Service: ajustes de pre cierre. Los refinanciamientos (R) restan al saldo total y los descuentos (D)
 * solo al interés; cada movimiento se aplica con SP_AJUSTES_PRECIERRE.
 */
class AjustesPrecierreService
{
    /** Motivo del último adquirirCandado() fallido por no poder crear el archivo (vacío si estaba ocupado). */
    private static $errorCandado = '';

    /**
     * Movimientos D y R pendientes de la fecha con sus saldos y el saldo resultante del ajuste.
     *
     * @param string $fecha Y-m-d
     * @return array { success, mensaje, datos: { fecha, movimientos, resumen, cierre } }
     */
    public static function consultar($fecha)
    {
        $fechaNorm = PagosAplicacionRepository::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return Model::Responde(false, 'Seleccione una fecha válida.', null, 'Fecha inválida');
        }

        $repo = new AjustesPrecierreRepository();
        try {
            $filas = $repo->getMovimientosPendientes($fechaNorm);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al consultar los movimientos de pre cierre.', null, $e->getMessage());
        }

        $movimientos = array_map([self::class, 'calcularSaldos'], $filas);
        $resumen = [
            'total' => count($movimientos),
            'monto' => 0.0,
            'descuentos' => 0,
            'montoDescuentos' => 0.0,
            'refinanciamientos' => 0,
            'montoRefinanciamientos' => 0.0,
        ];
        foreach ($movimientos as $m) {
            $resumen['monto'] += $m['MONTO'];
            if ($m['TIPO'] === 'D') {
                $resumen['descuentos']++;
                $resumen['montoDescuentos'] += $m['MONTO'];
            } else {
                $resumen['refinanciamientos']++;
                $resumen['montoRefinanciamientos'] += $m['MONTO'];
            }
        }
        $resumen['monto'] = round($resumen['monto'], 2);
        $resumen['montoDescuentos'] = round($resumen['montoDescuentos'], 2);
        $resumen['montoRefinanciamientos'] = round($resumen['montoRefinanciamientos'], 2);

        $mensaje = $resumen['total'] === 0
            ? 'No hay movimientos de descuento o refinanciamiento pendientes para la fecha seleccionada.'
            : 'Se encontraron ' . $resumen['total'] . ' movimientos pendientes.';

        return Model::Responde(true, $mensaje, [
            'fecha' => $fechaNorm,
            'movimientos' => $movimientos,
            'resumen' => $resumen,
            'cierre' => CierreDiaService::estadoCierreFecha($fechaNorm),
        ]);
    }

    /**
     * Aplica los movimientos seleccionados. Cada ajuste se confirma por separado, así que un error
     * no revierte los ya aplicados.
     *
     * @param string $fecha Y-m-d
     * @param string $usuario
     * @param array $claves "CDGNS|CICLO|SECUENCIA|TIPO"
     * @return array { success, mensaje, datos: { detalle, resumen, consulta } }
     */
    public static function procesar($fecha, $usuario, $claves)
    {
        $usuario = trim((string) $usuario);
        if ($usuario === '') {
            return Model::Responde(false, 'Usuario no identificado.', null, 'Usuario vacío');
        }

        $fechaNorm = PagosAplicacionRepository::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return Model::Responde(false, 'Seleccione una fecha válida.', null, 'Fecha inválida');
        }

        $seleccion = [];
        foreach ((array) $claves as $clave) {
            $partes = explode('|', trim((string) $clave));
            if (count($partes) !== 4 || !preg_match('/^\d+$/', $partes[2]) || !in_array($partes[3], ['D', 'R'], true)) {
                continue;
            }
            $seleccion[implode('|', $partes)] = $partes;
        }
        if (empty($seleccion)) {
            return Model::Responde(false, 'Seleccione al menos un movimiento para procesar.', null, 'Sin selección');
        }

        $estado = CierreDiaService::estadoCierreFecha($fechaNorm);
        if ($estado['enEjecucion']) {
            return Model::Responde(false, 'Hay un cierre de día en ejecución. Espere a que termine para procesar los ajustes.', null, 'Cierre en ejecución');
        }

        $candado = self::adquirirCandado();
        if ($candado === null) {
            if (self::$errorCandado !== '') {
                return Model::Responde(false, 'No fue posible iniciar el proceso: no se pudo crear el archivo de control. Revise los permisos de escritura de la carpeta de logs del sistema.', null, self::$errorCandado);
            }
            return Model::Responde(false, 'Ya hay un proceso de ajustes de pre cierre en ejecución. Intente de nuevo en unos minutos.', null, 'Concurrencia');
        }

        $repo = new AjustesPrecierreRepository();
        $detalle = [];
        try {
            foreach ($seleccion as $partes) {
                list($cdgns, $ciclo, $secuencia, $tipo) = $partes;
                $base = ['CDGNS' => $cdgns, 'CICLO' => $ciclo, 'SECUENCIA' => $secuencia, 'TIPO' => $tipo, 'MONTO' => 0.0];

                $mov = $repo->getMovimientoPendiente($fechaNorm, $cdgns, $ciclo, $secuencia, $tipo);
                if ($mov === null) {
                    $detalle[] = $base + ['CORRECTO' => false, 'ESTADO' => 'Sin procesar', 'MENSAJE' => 'El movimiento ya fue aplicado o ya no está activo.'];
                    continue;
                }

                $base['MONTO'] = round((float) $mov['MONTO'], 2);
                $res = $repo->aplicarAjuste($mov, $usuario);
                if (!$res['success']) {
                    $detalle[] = $base + ['CORRECTO' => false, 'ESTADO' => 'Error', 'MENSAJE' => $res['mensaje']];
                    continue;
                }
                $detalle[] = $base + [
                    'CORRECTO' => true,
                    'ESTADO' => 'Aplicado',
                    'MENSAJE' => $res['confirmado'] ? '' : 'El procedimiento no devolvió confirmación; verifique el ajuste en el crédito.',
                ];
            }
        } catch (\Throwable $e) {
            $detalle[] = ['CDGNS' => '', 'CICLO' => '', 'SECUENCIA' => '', 'TIPO' => '', 'MONTO' => 0.0, 'CORRECTO' => false, 'ESTADO' => 'Error', 'MENSAJE' => 'El proceso se detuvo: ' . $e->getMessage()];
        } finally {
            self::liberarCandado($candado);
        }

        $correctos = count(array_filter($detalle, function ($d) {
            return $d['CORRECTO'];
        }));
        $conError = count($detalle) - $correctos;

        @file_put_contents(
            self::archivoLog(),
            date('c') . ' [' . $usuario . '] Fecha=' . $fechaNorm
                . ' Seleccionados=' . count($seleccion)
                . ' Aplicados=' . $correctos
                . ' ConError=' . $conError
                . ' Detalle=' . json_encode($detalle, JSON_UNESCAPED_UNICODE)
                . "\n",
            FILE_APPEND
        );

        $mensaje = "Se aplicaron {$correctos} de " . count($seleccion) . ' ajustes.';
        if ($conError > 0) {
            $mensaje .= " {$conError} no se aplicaron (revise el detalle).";
        }

        $consulta = self::consultar($fechaNorm);

        return Model::Responde(true, $mensaje, [
            'detalle' => $detalle,
            'resumen' => ['seleccionados' => count($seleccion), 'correctos' => $correctos, 'conError' => $conError],
            'consulta' => ($consulta['success'] ?? false) ? ($consulta['datos'] ?? null) : null,
        ]);
    }

    /**
     * Saldos del crédito y saldo después del ajuste: R resta al saldo total (sin pasar de cero, el SP liquida el saldo
     * cuando el monto lo cubre) y D solo al interés.
     *
     * @param array $f Fila de AjustesPrecierreRepository::getMovimientosPendientes()
     * @return array
     */
    private static function calcularSaldos(array $f)
    {
        $monto = round((float) ($f['MONTO'] ?? 0), 2);
        $total = round((float) ($f['SALDO_TOTAL'] ?? 0), 2);
        $capital = round((float) ($f['SALDO_CAPITAL'] ?? 0), 2);
        $interes = round($total - $capital, 2);
        $tipo = strtoupper(trim((string) ($f['TIPO'] ?? '')));

        if ($tipo === 'R') {
            $despues = max(0, $total - $monto);
            $excede = $monto > $total;
        } else {
            $despues = $capital + max(0, $interes - $monto);
            $excede = $monto > $interes;
        }

        return [
            'FECHA' => substr((string) ($f['FECHA_HORA'] ?? ''), 0, 10),
            'FECHA_FMT' => (string) ($f['FECHA_FMT'] ?? ''),
            'CDGNS' => trim((string) ($f['CDGNS'] ?? '')),
            'CICLO' => trim((string) ($f['CICLO'] ?? '')),
            'SECUENCIA' => (string) ($f['SECUENCIA'] ?? ''),
            'TIPO' => $tipo,
            'NOMBRE' => trim((string) ($f['NOMBRE'] ?? '')),
            'MONTO' => $monto,
            'SALDO_CAPITAL' => $capital,
            'SALDO_INTERES' => $interes,
            'SALDO_TOTAL' => $total,
            'SALDO_DESPUES' => round($despues, 2),
            'EXCEDE' => $excede,
        ];
    }

    /**
     * @return string
     */
    private static function archivoLog()
    {
        return defined('APPPATH') ? dirname(APPPATH) . '/logs/ajustes_precierre.log' : __DIR__ . '/../../logs/ajustes_precierre.log';
    }

    /**
     * Candado exclusivo no bloqueante: evita que dos usuarios apliquen los mismos ajustes al mismo tiempo.
     *
     * @return resource|null
     */
    private static function adquirirCandado()
    {
        self::$errorCandado = '';
        $archivo = defined('APPPATH') ? dirname(APPPATH) . '/logs/ajustes_precierre.lock' : __DIR__ . '/../../logs/ajustes_precierre.lock';
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
