<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Model;
use App\repositories\CierreDiaRepository;
use App\repositories\PagosAplicacionRepository;
use Jobs\models\JobsCredito as JobsDao;

/**
 * Service: lógica de negocio del Cierre de Día.
 * Validaciones, control de concurrencia y regeneración (admin + contraseña), más el histórico, el resumen
 * de movimientos del día y el resumen del correo de cierre que muestra la pantalla de cierre.
 */
class CierreDiaService
{
    /** Días que muestra el histórico: la fecha consultada y los 6 anteriores. */
    const DIAS_HISTORICO = 7;

    /** Categorías del resumen de pagos del día. */
    const CATEGORIAS_PAGOS = ['pagos', 'garantias', 'ajustes', 'no_identificados', 'incidencias'];

    /**
     * Estado de ejecución para la carga inicial de la pantalla. No consulta histórico ni movimientos:
     * esa información se obtiene hasta que el usuario consulta una fecha.
     *
     * @return array { success, mensaje, datos: { ejecutando, inicio, usuario, segundos, fechaCierre, tiempoEstimado, jobActivo } }
     */
    public static function estadoEjecucion()
    {
        $repo = new CierreDiaRepository();
        $enEjecucion = $repo->validaCierreEnEjecucion();
        $jobActivo = CierreDiaJobLock::jobActivo();

        return Model::Responde(true, 'OK', [
            'ejecutando' => !empty($enEjecucion) || $jobActivo,
            'inicio' => $enEjecucion['INICIO'] ?? null,
            'usuario' => $enEjecucion['USUARIO'] ?? null,
            'segundos' => (int) ($enEjecucion['SEGUNDOS'] ?? 0),
            'fechaCierre' => $enEjecucion['FECHA_CIERRE_ISO'] ?? null,
            'tiempoEstimado' => $repo->tiempoEstimado(),
            'jobActivo' => $jobActivo,
        ]);
    }

    /**
     * Estado del cierre de una fecha: si ya concluyó con éxito y si hay algún cierre en ejecución.
     *
     * @param string $fecha Y-m-d
     * @return array { ejecutado, enEjecucion }
     */
    public static function estadoCierreFecha($fecha)
    {
        $repo = new CierreDiaRepository();
        $sp = $repo->getEstatusSpPorFecha($fecha);
        $ejecutado = $sp !== null
            && (int) ($sp['EN_PROCESO'] ?? 0) === 0
            && (int) ($sp['EXITO'] ?? 0) === 1;

        return [
            'ejecutado' => $ejecutado,
            'enEjecucion' => !empty($repo->validaCierreEnEjecucion()) || CierreDiaJobLock::jobActivo(),
        ];
    }

    /**
     * Histórico de la fecha consultada y los días anteriores según BITACORA_CIERRE_DIARIO.
     * Los días sin registro en bitácora se devuelven como pendientes de procesar.
     *
     * @param string $fecha Y-m-d
     * @return array { success, mensaje, datos: { fecha, dias: [...] } }
     */
    public static function historico($fecha)
    {
        $fechaNorm = PagosAplicacionRepository::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return Model::Responde(false, 'Seleccione una fecha válida.', null, 'Fecha inválida');
        }

        $inicio = date('Y-m-d', strtotime($fechaNorm . ' -' . (self::DIAS_HISTORICO - 1) . ' days'));
        $repo = new CierreDiaRepository();
        $porFecha = [];
        foreach ($repo->getHistoricoBitacora($inicio, $fechaNorm) as $fila) {
            $porFecha[(string) ($fila['FECHA_ISO'] ?? '')] = $fila;
        }

        $dias = [];
        for ($i = 0; $i < self::DIAS_HISTORICO; $i++) {
            $dia = date('Y-m-d', strtotime($fechaNorm . ' -' . $i . ' days'));
            $fila = $porFecha[$dia] ?? null;
            $base = [
                'fecha' => $dia,
                'fechaFmt' => date('d/m/Y', strtotime($dia)),
                'consultada' => $i === 0,
            ];

            if ($fila === null) {
                $dias[] = $base + [
                    'estado' => 'pendiente',
                    'estadoTexto' => 'Pendiente de procesar',
                    'usuario' => null,
                    'inicio' => null,
                    'fin' => null,
                    'registrosCierre' => null,
                    'creditosDevengo' => null,
                    'montoDevengo' => null,
                    'mensaje' => '',
                ];
                continue;
            }

            $enProceso = (int) ($fila['EN_PROCESO'] ?? 0) === 1;
            $exito = (int) ($fila['EXITO'] ?? 0) === 1;
            if ($enProceso) {
                $estado = ['procesando', 'Procesando'];
            } elseif ($exito) {
                $estado = ['finalizado', 'Finalizado'];
            } else {
                $estado = ['error', 'Error'];
            }
            $metrica = function ($valor, $decimales = false) use ($enProceso) {
                if ($enProceso || $valor === null || $valor === '') {
                    return null;
                }
                return $decimales ? round((float) $valor, 2) : (int) $valor;
            };

            $dias[] = $base + [
                'estado' => $estado[0],
                'estadoTexto' => $estado[1],
                'usuario' => $fila['USUARIO'] ?? null,
                'inicio' => $fila['INICIO'] ?? null,
                'fin' => $fila['FIN'] ?? null,
                'registrosCierre' => $metrica($fila['CIERRE_REGISTROS'] ?? null),
                'creditosDevengo' => $metrica($fila['DEVENGO_REGISTROS'] ?? null),
                'montoDevengo' => $metrica($fila['DEVENGO_MONTO'] ?? null, true),
                'mensaje' => (string) ($fila['MENSAJE'] ?? ''),
            ];
        }

        return Model::Responde(true, 'OK', ['fecha' => $fechaNorm, 'dias' => $dias]);
    }

    /**
     * Resumen del día por categoría (pagos, garantías, ajustes, no identificados e incidencias)
     * con lo aplicado y lo pendiente.
     *
     * @param string $fecha Y-m-d
     * @return array { success, mensaje, datos: { fecha, resumen } }
     */
    public static function resumenPagos($fecha)
    {
        $fechaNorm = PagosAplicacionRepository::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return Model::Responde(false, 'Seleccione una fecha válida.', null, 'Fecha inválida');
        }

        try {
            $resumen = (new CierreDiaRepository())->getResumenPagosDia($fechaNorm);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al consultar el resumen de pagos.', null, $e->getMessage());
        }

        foreach ($resumen as $categoria => $r) {
            $resumen[$categoria]['pendientes'] = max(0, $r['total'] - $r['aplicados']);
            $resumen[$categoria]['montoPendiente'] = round($r['monto'] - $r['montoAplicado'], 2);
        }

        return Model::Responde(true, 'OK', ['fecha' => $fechaNorm, 'resumen' => $resumen]);
    }

    /**
     * Detalle de una categoría del resumen de pagos del día.
     *
     * @param string $fecha Y-m-d
     * @param string $categoria
     * @return array { success, mensaje, datos: { fecha, categoria, filas } }
     */
    public static function detallePagos($fecha, $categoria)
    {
        $fechaNorm = PagosAplicacionRepository::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return Model::Responde(false, 'Seleccione una fecha válida.', null, 'Fecha inválida');
        }
        $categoria = trim((string) $categoria);
        if (!in_array($categoria, self::CATEGORIAS_PAGOS, true)) {
            return Model::Responde(false, 'Categoría no válida.', null, 'Categoría inválida');
        }

        try {
            $filas = (new CierreDiaRepository())->getDetallePagosDia($fechaNorm, $categoria);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al consultar el detalle.', null, $e->getMessage());
        }

        foreach ($filas as $i => $f) {
            $filas[$i]['MONTO'] = round((float) ($f['MONTO'] ?? 0), 2);
            $filas[$i]['APLICADO'] = (int) ($f['APLICADO'] ?? 0) === 1;
            if (isset($f['DESCRIPCION'])) {
                $filas[$i]['DESCRIPCION'] = self::limpiarMotivo($f['DESCRIPCION']);
            }
            if (isset($f['MSGRESUL'])) {
                $filas[$i]['MSGRESUL'] = self::limpiarMotivo($f['MSGRESUL']);
            }
        }

        return Model::Responde(true, 'OK', ['fecha' => $fechaNorm, 'categoria' => $categoria, 'filas' => $filas]);
    }

    /**
     * Quita el prefijo «Resultado de la validación:» y los saltos de línea que dejan los SP de importación.
     *
     * @param string|null $texto
     * @return string
     */
    public static function limpiarMotivo($texto)
    {
        $texto = preg_replace('/^\s*Resultado de la validaci\S*n:\s*/iu', '', (string) $texto);
        return trim(preg_replace('/\s+/u', ' ', $texto));
    }

    /**
     * Valida si hay cierre en ejecución (concurrencia).
     *
     * @return array { success, datos: { INICIO, USUARIO } o vacío }
     */
    public static function validaCierreEnEjecucion()
    {
        $repo = new CierreDiaRepository();
        $repo->liberarCandadosPhpHuerfanos();
        $r = $repo->validaCierreEnEjecucion();
        $resp = Model::Responde(true, 'OK', $r ?: []);
        $resp['jobActivo'] = CierreDiaJobLock::jobActivo();
        return $resp;
    }

    /**
     * Estatus del cierre de una fecha según el renglón que el SP deja en bitácora.
     * Permite a la vista distinguir «el SP nunca corrió» de «ya terminó» sin depender de temporizadores.
     *
     * @param string $fecha Y-m-d
     * @return array { success, mensaje, datos: { registrado, enProceso?, exito?, inicio?, fin?, usuario? } }
     */
    public static function estatusCierrePorFecha($fecha)
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return Model::Responde(false, 'Indique la fecha de cierre.');
        }

        $repo = new CierreDiaRepository();
        $fila = $repo->getEstatusSpPorFecha($fecha);
        if ($fila !== null
            && (int) ($fila['EN_PROCESO'] ?? 0) === 1
            && (int) ($fila['EXITO'] ?? 0) === 1
        ) {
            $repo->sincronizarFinBitacoraSp($fecha, 1);
            $fila = $repo->getEstatusSpPorFecha($fecha);
        }
        if ($fila === null) {
            return Model::Responde(true, 'OK', ['registrado' => false]);
        }

        return Model::Responde(true, 'OK', [
            'registrado' => true,
            'enProceso' => (int) ($fila['EN_PROCESO'] ?? 0) === 1,
            'exito' => (int) ($fila['EXITO'] ?? 0) === 1,
            'inicio' => $fila['INICIO'] ?? null,
            'fin' => $fila['FIN'] ?? null,
            'usuario' => $fila['USUARIO'] ?? null,
        ]);
    }

    /**
     * Validación previa antes de ejecutar: concurrencia, cierre ya ejecutado, o si puede regenerar (admin).
     * Nota: límite de 3 días para regenerar está deshabilitado temporalmente (ver bloque comentado abajo).
     *
     * @param string $fecha Y-m-d
     * @param string $perfil Perfil del usuario (ej. ADMIN)
     * @return array { success, mensaje, datos: { yaEjecutado, puedeRegenerar, TOTAL?, ... } }
     */
    public static function validacionPrevia($fecha, $perfil = '')
    {
        $repo = new CierreDiaRepository();

        if (trim($fecha) === '') {
            return Model::Responde(false, 'La fecha es obligatoria.', null, 'Fecha vacía');
        }

        if (CierreDiaJobLock::jobActivo()) {
            return Model::Responde(false, 'Ya hay un proceso de cierre diario en ejecución, no es posible iniciar otro.', [], 'Concurrencia Job');
        }

        $enEjecucion = $repo->validaCierreEnEjecucion();
        if (!empty($enEjecucion)) {
            return Model::Responde(false, 'Ya hay un proceso de cierre diario en ejecución, no es posible iniciar otro.', $enEjecucion, 'Concurrencia');
        }

        // Opcional: exige cierre exitoso del día previo registrado en BITACORA_CIERRE_DIARIO (no usa TBL_CIERRE_DIA).
        // Descomentar si en producción debe bloquearse sin cierre previo en bitácora.
        /*
        if (!$repo->existeCierreDiaAnterior($fecha)) {
            return Model::Responde(false, 'No se puede ejecutar el cierre: no se ha realizado el Cierre del Día Anterior.', null, 'Cierre día anterior');
        }
        */

        $yaEjecutado = $repo->cierreYaEjecutado($fecha);
        $esAdmin = $perfil !== '' && stripos($perfil, 'ADMIN') !== false;
        // Temporal: sin ventana de 3 días; el admin puede regenerar cualquier fecha ya cerrada.
        // $limite = date('Y-m-d', strtotime('-3 days'));
        // $puedeRegenerar = $esAdmin && $fecha >= $limite;
        $puedeRegenerar = $esAdmin;

        if ($yaEjecutado && !$puedeRegenerar) {
            return Model::Responde(true, 'El cierre de ese día ya fue ejecutado.', [
                'yaEjecutado' => true,
                'puedeRegenerar' => false,
            ]);
        }

        if ($yaEjecutado && $puedeRegenerar) {
            return Model::Responde(true, 'El cierre ya fue ejecutado. Como administrador puede regenerar.', [
                'yaEjecutado' => true,
                'puedeRegenerar' => true,
            ]);
        }

        return Model::Responde(true, 'Validación correcta.', ['yaEjecutado' => false, 'puedeRegenerar' => false]);
    }

    /**
     * Valida en servidor antes de lanzar el Job. No inserta bitácora:
     * el SP crea y actualiza el registro; la vista solo consulta ese estatus.
     *
     * @param string $fecha Y-m-d
     * @param string $usuario
     * @param string $perfil
     * @param int|bool $regenerar
     * @return array { success, mensaje, datos?: { usuario, regenerar } }
     */
    public static function iniciarProcesoCierre($fecha, $usuario, $perfil = '', $regenerar = 0)
    {
        $fecha = trim((string) $fecha);
        $usuario = trim((string) $usuario);
        $regenerar = $regenerar ? 1 : 0;

        if ($fecha === '' || $usuario === '') {
            return Model::Responde(false, 'No se ha indicado los parámetros necesarios para el cierre diario.');
        }

        $repo = new CierreDiaRepository();
        $repo->liberarCandadosPhpHuerfanos();

        if (CierreDiaJobLock::jobActivo()) {
            return Model::Responde(false, 'Ya hay un proceso de cierre diario en ejecución, no es posible iniciar otro.', [], 'Concurrencia Job');
        }

        $previa = self::validacionPrevia($fecha, $perfil);
        if (empty($previa['success'])) {
            return $previa;
        }

        $datosPrevia = isset($previa['datos']) && is_array($previa['datos']) ? $previa['datos'] : [];
        $yaEjecutado = !empty($datosPrevia['yaEjecutado']);
        $puedeRegenerar = !empty($datosPrevia['puedeRegenerar']);

        if ($yaEjecutado && !$regenerar) {
            return Model::Responde(false, 'El cierre de ese día ya fue ejecutado. No es posible iniciarlo de nuevo.', $datosPrevia, 'Ya ejecutado');
        }

        if ($yaEjecutado && $regenerar && !$puedeRegenerar) {
            return Model::Responde(false, 'No tiene permisos para regenerar el cierre de ese día.', $datosPrevia, 'Sin permiso regenerar');
        }

        $enEjecucion = $repo->validaCierreEnEjecucion();
        if (!empty($enEjecucion)) {
            return Model::Responde(false, 'Ya hay un proceso de cierre diario en ejecución, no es posible iniciar otro.', $enEjecucion, 'Concurrencia');
        }

        return Model::Responde(true, 'El proceso de cierre diario se ha iniciado correctamente.', [
            'usuario' => $usuario,
            'regenerar' => $regenerar,
        ]);
    }

    /**
     * Resumen del cierre de una fecha: mismo contenido y mismos datos que el correo que envía el Job al concluir,
     * sin el encabezado ni el pie de la notificación.
     *
     * @param string $fechaYmd Y-m-d
     * @return array Respuesta Model::Responde con datos { asunto, html }
     */
    public static function obtenerResumenCierreDia($fechaYmd)
    {
        $fechaNorm = PagosAplicacionRepository::coerceFechaYmD($fechaYmd);
        if ($fechaNorm === null) {
            return Model::Responde(false, 'Fecha no válida.');
        }

        $idProceso = (new CierreDiaRepository())->getIdProcesoCierre($fechaNorm);
        if ($idProceso === null) {
            return Model::Responde(false, 'No hay un cierre concluido para el ' . date('d/m/Y', strtotime($fechaNorm)) . '.');
        }

        try {
            $resultado = JobsDao::ResultadoCierreDia($fechaNorm, $idProceso);
            if (empty($resultado['success'])) {
                return Model::Responde(false, $resultado['mensaje'] ?? 'No se encontró el resultado del cierre.');
            }

            $datos = $resultado['datos'];
            $lineas = preg_split('/\r\n|\r|\n/', trim((string) ($datos['MENSAJE'] ?? '')));
            $datos['MENSAJE'] = end($lineas);

            return Model::Responde(true, 'OK', [
                'asunto' => 'Cierre del día ' . $datos['FECHA_CALCULO'],
                'html' => CierreDiaCorreoPresenter::plantilla($datos),
            ]);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al obtener el resumen de cierre.', null, $e->getMessage());
        }
    }
}
