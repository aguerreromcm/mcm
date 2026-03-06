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
     * Validación previa antes de ejecutar: concurrencia, cierre ya ejecutado, o si puede regenerar (admin + fecha ≤ 3 días).
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

        $configCierre = self::getConfigCierreDia();
        $soloFlujo = self::isSoloFlujo($configCierre);

        if (!$soloFlujo && !$repo->existeCierreDiaAnterior($fecha)) {
            return Model::Responde(false, 'No se puede ejecutar el cierre: no se ha realizado el Cierre del Día Anterior.', null, 'Cierre día anterior');
        }

        $yaEjecutado = $repo->cierreYaEjecutado($fecha);
        $esAdmin = $perfil !== '' && stripos($perfil, 'ADMIN') !== false;
        $limite = date('Y-m-d', strtotime('-3 days'));
        $puedeRegenerar = $esAdmin && $fecha >= $limite;

        if ($yaEjecutado && !$puedeRegenerar) {
            if ($soloFlujo) {
                return Model::Responde(true, 'Validación correcta (modo solo flujo: puede ejecutar para probar el correo).', ['yaEjecutado' => false, 'puedeRegenerar' => false]);
            }
            return Model::Responde(true, 'El cierre de ese día ya fue ejecutado.', [
                'yaEjecutado' => true,
                'puedeRegenerar' => false,
            ]);
        }

        if ($yaEjecutado && $puedeRegenerar) {
            return Model::Responde(true, 'El cierre ya fue ejecutado. Como administrador puede regenerar (últimos 3 días).', [
                'yaEjecutado' => true,
                'puedeRegenerar' => true,
            ]);
        }

        return Model::Responde(true, 'Validación correcta.', ['yaEjecutado' => false, 'puedeRegenerar' => false]);
    }

    /**
     * Registra el inicio del cierre y deja listo para que el Job ejecute el SP.
     * No ejecuta el SP aquí (proceso pesado); el controlador lanzará el Job.
     *
     * @param string $fecha Y-m-d
     * @param string $usuario
     * @param int $regenerar 0 o 1
     * @return array { success, mensaje }
     */
    public static function registrarInicioYResponder($fecha, $usuario, $regenerar = 0)
    {
        $soloFlujo = self::isSoloFlujo(self::getConfigCierreDia());

        if (!$soloFlujo) {
            $repo = new CierreDiaRepository();
            if (!$repo->registrarInicio($fecha, $usuario)) {
                return Model::Responde(false, 'Error al registrar el inicio del cierre.', null, 'Registro bitácora');
            }
        }
        return Model::Responde(true, 'El proceso de cierre diario se ha iniciado correctamente.');
    }

    /**
     * Ejecuta el cierre diario en la misma petición (sin Job en segundo plano):
     * si solo flujo solo envía correo; si no, ejecuta SPs y finaliza.
     *
     * @param string $fechaCierre Y-m-d
     * @param string $usuario
     * @param int $regenerar 0 o 1
     * @return array { success, mensaje }
     */
    public static function ejecutarCierreDiario($fechaCierre, $usuario, $regenerar = 0)
    {
        $soloFlujo = self::isSoloFlujo(self::getConfigCierreDia());

        if ($soloFlujo) {
            self::finalizarCierre($fechaCierre, 1);
            return Model::Responde(true, 'Proceso de cierre (solo flujo) completado. Se envió el correo a CORREOS_DESARROLLO.');
        }

        $repo = new CierreDiaRepository();
        try {
            $repo->ejecutarSpCierreDia($fechaCierre, $regenerar);
            $fechaDevengo = date('Y-m-d', strtotime($fechaCierre . ' +1 day'));
            $repo->ejecutarSpGenDevengoDiario($fechaDevengo, $usuario);
            $advertenciaPld = '';
            $resRel = $repo->ejecutarSpGenAlertarRelPld($fechaCierre, $usuario);
            if (self::resultadoPldEsError($resRel)) {
                $advertenciaPld = 'Alertas PLD (Relevantes): ' . self::mensajeResultadoPld($resRel);
            } else {
                $resInu = $repo->ejecutarSpGenAlertaInuPld($fechaCierre, $usuario);
                if (self::resultadoPldEsError($resInu)) {
                    $advertenciaPld = 'Alertas PLD (Inusuales): ' . self::mensajeResultadoPld($resInu);
                }
            }
            self::finalizarCierre($fechaCierre, 1);
            $mensaje = 'El cierre de día se ha completado correctamente.';
            if ($advertenciaPld !== '') {
                $mensaje .= ' Advertencia PLD: ' . $advertenciaPld;
            }
            return Model::Responde(true, $mensaje);
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

        // Resumen cierre: consulta con fecha de cierre (ej. 3 marzo). Resumen intereses: consulta con fecha al día siguiente (ej. 4 marzo)
        $fechaDevengo = date('Y-m-d', strtotime($fechaCierre . ' +1 day'));
        $resCierre = $repo->getResumenCierre($fechaCierre);
        $resDevengo = $repo->getResumenDevengo($fechaDevengo);

        $registros = (int) ($resCierre['registros'] ?? 0);
        $creditos = (int) ($resDevengo['creditos'] ?? 0);
        $montoDevengo = (float) ($resDevengo['monto'] ?? 0);

        $configCierre = self::getConfigCierreDia();
        $soloFlujo = self::isSoloFlujo($configCierre);
        $correosDesarrollo = isset($configCierre['CORREOS_DESARROLLO']) ? trim((string) $configCierre['CORREOS_DESARROLLO']) : '';

        $destinatarios = [];
        if ($soloFlujo && $correosDesarrollo !== '') {
            $destinatarios = array_unique(array_map('trim', explode(',', $correosDesarrollo)));
            $destinatarios = array_values(array_filter($destinatarios, function ($e) { return $e !== ''; }));
        }
        if (empty($destinatarios)) {
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
                \Mensajero::Notificaciones($html)
            );
        }
    }

    /**
     * Indica si el resultado del SP PLD indica error (como en VB6: primer carácter "0" = error).
     *
     * @param string $resultado
     * @return bool
     */
    private static function resultadoPldEsError($resultado)
    {
        $r = trim((string) $resultado);
        return $r !== '' && $r[0] === '0';
    }

    /**
     * Extrae el mensaje de error del resultado PLD (VB6: Mid(res, 3, Len(res)-2)).
     *
     * @param string $resultado
     * @return string
     */
    private static function mensajeResultadoPld($resultado)
    {
        $r = (string) $resultado;
        $len = strlen($r);
        if ($len <= 2) {
            return $len > 0 ? $r : 'El SP devolvió un resultado vacío.';
        }
        return trim(substr($r, 2, $len - 2));
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
