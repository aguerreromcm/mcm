<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use App\models\Herramientas as HerramientasDao;
use Core\Model;

/**
 * Service de Auditoría Devengo.
 * Lógica de negocio, validaciones críticas, control transaccional y concurrencia.
 */
class AuditoriaDevengoService
{
    /** Perfiles autorizados para procesar devengos */
    private const PERFILES_AUTORIZADOS = ['ADMIN', 'PLMV', 'PHEE'];

    /**
     * Valida que el perfil esté autorizado.
     * Permite: coincidencia exacta en lista, o perfil que contenga "ADMIN" (ej. Q-ADMIN).
     */
    public static function validaPerfil(string $perfil): bool
    {
        if (in_array($perfil, self::PERFILES_AUTORIZADOS)) {
            return true;
        }
        return stripos($perfil, 'ADMIN') !== false;
    }

    /**
     * Buscar devengos faltantes.
     *
     * @param array $datos ['credito' => string|null, 'ciclo' => string|null]
     * @return array { success, mensaje, datos, error }
     */
    public static function GetDevengosFaltantes(array $datos = []): array
    {
        return HerramientasDao::GetDevengosFaltantes($datos);
    }

    /**
     * Procesamiento individual de devengo. Recibe la fila completa (con todos los datos para el INSERT).
     *
     * @param array $datos Una fila con CREDITO, CICLO y todos los campos para INSERT (FECHA_CALC_ISO, DEV_DIARIO, etc.)
     * @param string $usuario
     * @param string $perfil
     * @param string $ip
     * @return array { success, mensaje, datos, error }
     */
    public static function ProcesarIndividual(array $datos, string $usuario, string $perfil, string $ip): array
    {
        $log = defined('APPPATH') ? APPPATH . '/../storage/logs/auditoria_devengo_proceso.log' : __DIR__ . '/../../storage/logs/auditoria_devengo_proceso.log';
        $fila = isset($datos['fila']) ? $datos['fila'] : $datos;
        $credito = trim((string) ($fila['CREDITO'] ?? $fila['CDGCLNS'] ?? $fila['credito'] ?? ''));
        $ciclo = trim((string) ($fila['CICLO'] ?? $fila['ciclo'] ?? ''));

        @file_put_contents($log, date('c') . " [SVC] ProcesarIndividual credito=$credito | ciclo=$ciclo | usuario=$usuario | perfil=$perfil\n", FILE_APPEND);

        if ($credito === '' || $ciclo === '') {
            @file_put_contents($log, date('c') . " [SVC] BLOQUEO: credito o ciclo vacíos\n", FILE_APPEND);
            return Model::Responde(false, 'Crédito y ciclo son obligatorios.', null, 'Parámetros incompletos');
        }

        $validacionPerfil = self::validaPerfil($perfil);
        if (!$validacionPerfil) {
            @file_put_contents($log, date('c') . " [SVC] BLOQUEO: perfil '$perfil' no autorizado\n", FILE_APPEND);
            return Model::Responde(false, 'Perfil no autorizado para esta operación.', null, 'Acceso denegado');
        }

        try {
            return HerramientasDao::ProcesarDevengoIndividual($fila, $usuario, $perfil, $ip, 'INDIVIDUAL');
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al procesar devengo.', null, $e->getMessage());
        }
    }

    /**
     * Procesamiento masivo de devengos.
     *
     * @param array $registros [['credito','ciclo','fecha_corte'=>opcional], ...]
     * @param string $usuario
     * @param string $perfil
     * @param string $ip
     * @return array { success, mensaje, datos, error }
     */
    public static function ProcesarMasivo(array $registros, string $usuario, string $perfil, string $ip): array
    {
        if (empty($registros) || !is_array($registros)) {
            return Model::Responde(false, 'No se recibieron registros para procesar.', null, 'Lista vacía');
        }

        $validacionPerfil = self::validaPerfil($perfil);
        if (!$validacionPerfil) {
            return Model::Responde(false, 'Perfil no autorizado para esta operación.', null, 'Acceso denegado');
        }

        try {
            return HerramientasDao::ProcesarDevengoMasivo($registros, $usuario, $perfil, $ip);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error en procesamiento masivo.', null, $e->getMessage());
        }
    }
}
