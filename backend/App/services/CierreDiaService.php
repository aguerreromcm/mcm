<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Model;
use Core\App;
use App\repositories\CierreDiaRepository;

/**
 * Service: lógica de negocio del Cierre de Día.
 * Validaciones, control de concurrencia, regeneración (admin + contraseña), generación de resumen y envío de correo.
 */
class CierreDiaService
{
    /**
     * Datos para la pantalla: últimos 7 cierres en bitácora, estado de ejecución y tiempo estimado.
     * Las métricas salen de la bitácora (SP); si faltan, se completa con consultas agregadas livianas.
     *
     * @param bool $conResumenesExternos Si true y faltan métricas en bitácora, consulta TBL_CIERRE_DIA/DEVENGO
     * @return array { success, mensaje, datos: { ultimos5, ejecutando, inicio, usuario, tiempoEstimado } }
     */
    public static function obtenerDatosPantalla($conResumenesExternos = true)
    {
        $repo = new CierreDiaRepository();
        $ultimos5 = $repo->getUltimos7Cierres();
        $fechasFaltantes = [];
        foreach ($ultimos5 as $fila) {
            $enProceso = !empty($fila['EN_PROCESO']) && (int) $fila['EN_PROCESO'] === 1;
            $exito = isset($fila['EXITO']) ? (int) $fila['EXITO'] : 0;
            $fechaIso = isset($fila['FECHA_CIERRE_ISO']) ? trim((string) $fila['FECHA_CIERRE_ISO']) : '';
            if ($fechaIso === '') {
                continue;
            }
            // Errores: ceros en pantalla; no consultar tablas operativas.
            if (!$enProceso && $exito !== 1) {
                continue;
            }
            // Éxito: siempre por fecha de cierre (TBL_CIERRE_DIA / DEVENGO). El SP reutiliza
            // ID_IMPORTACION y deja las mismas cifras en bitácora en todos los renglones.
            if ($exito === 1) {
                $fechasFaltantes[] = $fechaIso;
                continue;
            }
            // En proceso: bitácora del SP en vivo; respaldo solo si faltan columnas.
            $tieneMetricas = array_key_exists('REGISTROS_PROCESADOS', $fila)
                || array_key_exists('CREDITOS_DEVENGO', $fila)
                || array_key_exists('DEVENGO_MONTO_NUM', $fila);
            if (!$tieneMetricas) {
                $fechasFaltantes[] = $fechaIso;
            }
        }

        $mapCierre = [];
        $mapDevengo = [];
        if ($conResumenesExternos && !empty($fechasFaltantes)) {
            $resumenes = $repo->getResumenesPorFechas($fechasFaltantes);
            $mapCierre = isset($resumenes['cierre']) && is_array($resumenes['cierre']) ? $resumenes['cierre'] : [];
            $mapDevengo = isset($resumenes['devengo']) && is_array($resumenes['devengo']) ? $resumenes['devengo'] : [];
        }

        foreach ($ultimos5 as &$fila) {
            $fechaIso = isset($fila['FECHA_CIERRE_ISO']) ? (string) $fila['FECHA_CIERRE_ISO'] : '';
            unset($fila['FECHA_CIERRE_ISO']);

            $enProceso = !empty($fila['EN_PROCESO']) && (int) $fila['EN_PROCESO'] === 1;
            $exito = isset($fila['EXITO']) ? (int) $fila['EXITO'] : 0;
            $mostrarMetricas = $enProceso || $exito === 1;

            if ($mostrarMetricas) {
                if ($exito === 1) {
                    $registros = (int) ($mapCierre[$fechaIso] ?? 0);
                    $creditos = (int) ($mapDevengo[$fechaIso]['creditos'] ?? 0);
                    $monto = (float) ($mapDevengo[$fechaIso]['monto'] ?? 0);
                } else {
                    $registros = array_key_exists('REGISTROS_PROCESADOS', $fila)
                        ? (int) $fila['REGISTROS_PROCESADOS']
                        : (int) ($mapCierre[$fechaIso] ?? 0);
                    $creditos = array_key_exists('CREDITOS_DEVENGO', $fila)
                        ? (int) $fila['CREDITOS_DEVENGO']
                        : (int) ($mapDevengo[$fechaIso]['creditos'] ?? 0);
                    if (array_key_exists('DEVENGO_MONTO_NUM', $fila)) {
                        $monto = (float) $fila['DEVENGO_MONTO_NUM'];
                    } else {
                        $monto = (float) ($mapDevengo[$fechaIso]['monto'] ?? 0);
                    }
                }
            } else {
                $registros = 0;
                $creditos = 0;
                $monto = 0.0;
            }
            unset($fila['DEVENGO_MONTO_NUM']);

            $fila['REGISTROS_PROCESADOS'] = $registros;
            $fila['CREDITOS_DEVENGO'] = $creditos;
            $fila['MONTO_INTERESES_DEVENGADOS'] = '$ ' . number_format($monto, 2);

            unset($fila['EN_PROCESO']);
            if ($enProceso) {
                $fila['ESTADO_TEXTO'] = 'Procesando';
            } elseif ($exito === 1) {
                $fila['ESTADO_TEXTO'] = 'Finalizado';
            } else {
                $fila['ESTADO_TEXTO'] = 'Error';
            }
        }
        unset($fila);
        $enEjecucion = $repo->validaCierreEnEjecucion();
        $jobActivo = CierreDiaJobLock::jobActivo();
        $ejecutando = !empty($enEjecucion) || $jobActivo;
        $tiempoEstimado = $repo->tiempoEstimado();

        return Model::Responde(true, 'OK', [
            'ultimos5' => $ultimos5,
            'ejecutando' => $ejecutando,
            'inicio' => !empty($enEjecucion) ? ($enEjecucion['INICIO'] ?? null) : null,
            'usuario' => !empty($enEjecucion) ? ($enEjecucion['USUARIO'] ?? null) : null,
            'segundos' => !empty($enEjecucion) ? (int) ($enEjecucion['SEGUNDOS'] ?? 0) : 0,
            'tiempoEstimado' => $tiempoEstimado,
            'jobActivo' => $jobActivo,
        ]);
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
     * Libera el candado PHP de la fecha (p. ej. si el Job no pudo arrancar tras adquirirlo).
     *
     * @param string $fecha Y-m-d
     * @param int $exito
     * @return bool
     */
    public static function liberarCandadoInicio($fecha, $exito = 0)
    {
        $repo = new CierreDiaRepository();
        return $repo->cerrarCandadoInicio($fecha, (int) $exito);
    }

    /**
     * Registra el inicio del cierre y deja listo para que el Job ejecute SP_PAGOS_CIERRE_DEVENGO.
     * No ejecuta el SP aquí (proceso pesado); el controlador lanzará el Job.
     *
     * @param string $fecha Y-m-d
     * @param string $usuario
     * @param int $regenerar 0 o 1 (solo UI/admin; el SP unificado no recibe este flag)
     * @return array { success, mensaje }
     */
    public static function registrarInicioYResponder($fecha, $usuario, $regenerar = 0)
    {
        return self::iniciarProcesoCierre($fecha, $usuario, '', $regenerar);
    }

    /**
     * Ejecuta el cierre diario en la misma petición (sin Job en segundo plano).
     *
     * @param string $fechaCierre Y-m-d
     * @param string $usuario
     * @param int $regenerar 0 o 1: si no es 0, borra devengo y TBL_CIERRE_DIA del día antes del SP (regenerar)
     * @return array { success, mensaje }
     */
    public static function ejecutarCierreDiario($fechaCierre, $usuario, $regenerar = 0)
    {
        $repo = new CierreDiaRepository();
        try {
            $repo->ejecutarSpPagosCierreDevengo($fechaCierre, $usuario, $regenerar !== 0);
            self::finalizarCierre($fechaCierre, 1);
            return Model::Responde(true, 'El cierre de día se ha completado correctamente.');
        } catch (\Throwable $e) {
            self::finalizarCierre($fechaCierre, 0);
            return Model::Responde(false, 'Error al ejecutar el cierre: ' . $e->getMessage(), null, $e->getMessage());
        }
    }

    /**
     * Finaliza el cierre: registra fin en bitácora, obtiene resumen y envía correo.
     * Lo invoca el Job al terminar el SP (o ejecutarCierreDiario cuando se ejecuta en la misma petición).
     *
     * @param string $fechaCierre Y-m-d (fecha del cierre)
     * @param int $exito 1 = éxito, 0 = error
     */
    public static function finalizarCierre($fechaCierre, $exito = 1)
    {
        $repo = new CierreDiaRepository();
        $repo->registrarFin($fechaCierre, $exito);

        if ($exito !== 1) {
            return;
        }

        try {
            $resumen = CierreDiaResumenPresenter::construir($fechaCierre);
        } catch (\Throwable $e) {
            return;
        }

        $configCierre = self::getConfigCierreDia();
        $soloFlujo = self::isSoloFlujo($configCierre);
        $correosDesarrollo = isset($configCierre['CORREOS_DESARROLLO']) ? trim((string) $configCierre['CORREOS_DESARROLLO']) : '';

        $destinatarios = [];
        if ($soloFlujo) {
            if ($correosDesarrollo === '') {
                return;
            }
            $destinatarios = array_unique(array_map('trim', explode(',', $correosDesarrollo)));
            $destinatarios = array_values(array_filter($destinatarios, function ($e) { return $e !== ''; }));
            if (empty($destinatarios)) {
                return;
            }
        } else {
            $destinatarios = $repo->getDestinatariosResumenCierreParametrosPld();
        }

        if (empty($destinatarios)) {
            return;
        }

        $fechaFmt = $resumen['fechaCierreFmt'] ?? date('d/m/Y', strtotime($fechaCierre));
        $html = CierreDiaResumenPresenter::htmlCorreo($resumen);

        if (!class_exists('Mensajero')) {
            @include_once dirname(dirname(__DIR__)) . '/libs/PHPMailer/Mensajero.php';
        }
        if (class_exists('Mensajero')) {
            \Mensajero::EnviarCorreo(
                $destinatarios,
                'Resumen de cierre de día - ' . $fechaFmt,
                \Mensajero::Notificaciones($html),
                [],
                !$soloFlujo
            );
        }
    }

    /**
     * Resumen unificado de cierre (modal y correo): proceso, pagos, conciliación y devengo.
     *
     * @param string $fechaYmd Y-m-d fecha operativa / de cierre
     * @return array Respuesta Model::Responde
     */
    public static function obtenerResumenCierreDia($fechaYmd)
    {
        $fechaYmd = trim((string) $fechaYmd);
        if ($fechaYmd === '') {
            return Model::Responde(false, 'Indique la fecha operativa.');
        }
        try {
            $datos = CierreDiaResumenPresenter::construir($fechaYmd);

            return Model::Responde(true, 'OK', $datos);
        } catch (\InvalidArgumentException $e) {
            return Model::Responde(false, 'Fecha no válida.');
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al obtener el resumen de cierre.', null, $e->getMessage());
        }
    }

    /**
     * Lee la sección [cierre_dia] del configuracion.ini (getConfig() devuelve array plano sin secciones).
     *
     * @return array
     */
    private static function getConfigCierreDia()
    {
        if (!function_exists('parse_ini_file')) {
            return [];
        }
        $ini = @parse_ini_file(dirname(__DIR__) . '/config/configuracion.ini', true);
        return isset($ini['cierre_dia']) && is_array($ini['cierre_dia']) ? $ini['cierre_dia'] : [];
    }

    /**
     * Indica si CIERRE_DIA_SOLO_FLUJO está activo (true, 1, "true", "1").
     *
     * @param array $configCierre Sección [cierre_dia]
     * @return bool
     */
    private static function isSoloFlujo(array $configCierre)
    {
        $val = isset($configCierre['CIERRE_DIA_SOLO_FLUJO']) ? trim((string) $configCierre['CIERRE_DIA_SOLO_FLUJO']) : '';
        return $val !== '' && (filter_var($val, FILTER_VALIDATE_BOOLEAN) || strtolower($val) === 'true' || $val === '1');
    }
}
