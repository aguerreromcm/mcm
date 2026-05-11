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
     * Datos para la pantalla: últimos 5 cierres, estado de ejecución y tiempo estimado.
     *
     * @return array { success, mensaje, datos: { ultimos5, ejecutando, inicio, usuario, tiempoEstimado } }
     */
    public static function obtenerDatosPantalla()
    {
        $repo = new CierreDiaRepository();
        $ultimos5 = $repo->getUltimos5Cierres();
        $fechasResumen = [];
        foreach ($ultimos5 as $fila) {
            $fechaIso = isset($fila['FECHA_CIERRE_ISO']) ? trim((string) $fila['FECHA_CIERRE_ISO']) : '';
            if ($fechaIso !== '') {
                $fechasResumen[] = $fechaIso;
            }
        }
        $resumenes = $repo->getResumenesPorFechas($fechasResumen);
        $mapCierre = isset($resumenes['cierre']) && is_array($resumenes['cierre']) ? $resumenes['cierre'] : [];
        $mapDevengo = isset($resumenes['devengo']) && is_array($resumenes['devengo']) ? $resumenes['devengo'] : [];
        foreach ($ultimos5 as &$fila) {
            $fechaIso = isset($fila['FECHA_CIERRE_ISO']) ? (string) $fila['FECHA_CIERRE_ISO'] : '';
            unset($fila['FECHA_CIERRE_ISO']);
            if ($fechaIso !== '') {
                $registros = isset($mapCierre[$fechaIso]) ? (int) $mapCierre[$fechaIso] : 0;
                $resDevengo = isset($mapDevengo[$fechaIso]) && is_array($mapDevengo[$fechaIso]) ? $mapDevengo[$fechaIso] : ['creditos' => 0, 'monto' => 0];
                $fila['REGISTROS_PROCESADOS'] = $registros;
                $fila['CREDITOS_DEVENGO'] = (int) ($resDevengo['creditos'] ?? 0);
                $fila['MONTO_INTERESES_DEVENGADOS'] = '$ ' . number_format((float) ($resDevengo['monto'] ?? 0), 2);
            } else {
                $fila['REGISTROS_PROCESADOS'] = 0;
                $fila['CREDITOS_DEVENGO'] = 0;
                $fila['MONTO_INTERESES_DEVENGADOS'] = '$ 0.00';
            }
        }
        unset($fila);
        $enEjecucion = $repo->validaCierreEnEjecucion();
        $ejecutando = !empty($enEjecucion);
        $tiempoEstimado = $repo->tiempoEstimado();

        return Model::Responde(true, 'OK', [
            'ultimos5' => $ultimos5,
            'ejecutando' => $ejecutando,
            'inicio' => $ejecutando ? ($enEjecucion['INICIO'] ?? null) : null,
            'usuario' => $ejecutando ? ($enEjecucion['USUARIO'] ?? null) : null,
            'tiempoEstimado' => $tiempoEstimado,
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
        $r = $repo->validaCierreEnEjecucion();
        return Model::Responde(true, 'OK', $r);
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

        $enEjecucion = $repo->validaCierreEnEjecucion();
        if (!empty($enEjecucion)) {
            return Model::Responde(false, 'Ya hay un proceso de cierre diario en ejecución, no es posible iniciar otro.', $enEjecucion, 'Concurrencia');
        }

        // DESHABILITADO: esta validación interrumpía el proceso (exige filas en TBL_CIERRE_DIA del día anterior con FECHA_LIQUIDA IS NULL).
        // Misma regla que VB6; descomentar cuando el entorno tenga el cierre previo cargado correctamente.
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
        $repo = new CierreDiaRepository();
        if (!$repo->registrarInicio($fecha, $usuario)) {
            return Model::Responde(false, 'Error al registrar el inicio del cierre.', null, 'Registro bitácora');
        }
        return Model::Responde(true, 'El proceso de cierre diario se ha iniciado correctamente.');
    }

    /**
     * Ejecuta el cierre diario en la misma petición (sin Job en segundo plano).
     * Siempre ejecuta el proceso real; el flag solo flujo solo afecta destinatarios de correo en finalizarCierre().
     *
     * @param string $fechaCierre Y-m-d
     * @param string $usuario
     * @param int $regenerar 0 o 1 (no enviado al SP; conservado por compatibilidad con la pantalla)
     * @return array { success, mensaje }
     */
    public static function ejecutarCierreDiario($fechaCierre, $usuario, $regenerar = 0)
    {
        $repo = new CierreDiaRepository();
        try {
            $repo->ejecutarSpPagosCierreDevengo($fechaCierre, $usuario);
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

        // Resumen cierre: fecha de cierre. Devengo en resumen/correo: misma fecha calendario (como COUNT en BD por TRUNC(FECHA_CALC) = fecha_cierre).
        $resCierre = $repo->getResumenCierre($fechaCierre);
        $resDevengo = $repo->getResumenDevengo($fechaCierre);

        $registros = (int) ($resCierre['registros'] ?? 0);
        $creditos = (int) ($resDevengo['creditos'] ?? 0);
        $montoDevengo = (float) ($resDevengo['monto'] ?? 0);

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

        $fechaFmt = date('d/m/Y', strtotime($fechaCierre));
        $html = '<p>Se ha completado el proceso de cierre de día.</p>';
        $html .= '<p><b>Fecha de cierre:</b> ' . $fechaFmt . '</p>';
        $html .= '<p><b>Registros de cierre procesados:</b> ' . $registros . '</p>';
        $html .= '<p><b>Créditos (devengo):</b> ' . $creditos . '</p>';
        $html .= '<p><b>Monto intereses devengados:</b> $ ' . number_format($montoDevengo, 2) . '</p>';

        if (!class_exists('Mensajero')) {
            @include_once dirname(dirname(__DIR__)) . '/libs/PHPMailer/Mensajero.php';
        }
        if (class_exists('Mensajero')) {
            \Mensajero::EnviarCorreo(
                $destinatarios,
                'Resumen de cierre de día - ' . $fechaFmt,
                \Mensajero::Notificaciones($html),
                [],
                true
            );
        }
    }

    /**
     * Cuatro resúmenes solo para el día de la fecha operativa (no acumulado de fechas posteriores).
     *
     * @param string $fechaYmd Y-m-d
     * @return array Respuesta Model::Responde con datos: pagosdia, tbl_cierre_dia, devengo_diario, mp_pd
     */
    public static function obtenerInformacionDiaResumenes($fechaYmd)
    {
        $fechaYmd = trim((string) $fechaYmd);
        if ($fechaYmd === '') {
            return Model::Responde(false, 'Indique la fecha operativa.');
        }
        $ts = strtotime($fechaYmd);
        if ($ts === false) {
            return Model::Responde(false, 'Fecha no válida.');
        }
        $fechaYmd = date('Y-m-d', $ts);
        try {
            $repo = new CierreDiaRepository();
            $datos = $repo->getInformacionDiaResumenes($fechaYmd);

            return Model::Responde(true, 'OK', $datos);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al obtener la información del día.', null, $e->getMessage());
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
