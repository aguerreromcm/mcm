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

    public const MODO_REGISTRO_REAL = 'REAL';
    public const MODO_REGISTRO_MES_ACTUAL = 'MES_ACTUAL';

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
     * Normaliza una fecha a Y-m-d. Acepta Y-m-d o DD/MM/YYYY.
     */
    public static function normalizarFechaYmd(?string $valor): ?string
    {
        if ($valor === null || trim($valor) === '') {
            return null;
        }
        $valor = trim($valor);
        $d = \DateTime::createFromFormat('Y-m-d', $valor);
        if ($d) {
            return $d->format('Y-m-d');
        }
        $d = \DateTime::createFromFormat('d/m/Y', $valor);
        if ($d) {
            return $d->format('Y-m-d');
        }
        $ts = strtotime(str_replace('/', '-', $valor));
        return $ts !== false ? date('Y-m-d', $ts) : null;
    }

    /**
     * Indica si la fecha faltante pertenece a un mes anterior al mes calendario actual.
     */
    public static function esFechaMesAnterior(string $fechaFaltanteYmd, ?string $fechaReferencia = null): bool
    {
        $fechaFaltanteYmd = self::normalizarFechaYmd($fechaFaltanteYmd);
        if ($fechaFaltanteYmd === null) {
            return false;
        }
        $referencia = $fechaReferencia !== null ? self::normalizarFechaYmd($fechaReferencia) : date('Y-m-d');
        if ($referencia === null) {
            $referencia = date('Y-m-d');
        }
        return substr($fechaFaltanteYmd, 0, 7) < substr($referencia, 0, 7);
    }

    /**
     * Ventana del 1 al 10 del mes calendario actual para confirmar el registro.
     */
    public static function esVentanaConfirmacionMesAnterior(?string $fechaReferencia = null): bool
    {
        $referencia = $fechaReferencia !== null ? self::normalizarFechaYmd($fechaReferencia) : date('Y-m-d');
        if ($referencia === null) {
            return false;
        }
        $dia = (int) date('j', strtotime($referencia . ' 12:00:00'));
        return $dia >= 1 && $dia <= 10;
    }

    /**
     * Primer día del mes calendario actual (Y-m-d).
     */
    public static function obtenerPrimerDiaMesActual(?string $fechaReferencia = null): string
    {
        $referencia = $fechaReferencia !== null ? self::normalizarFechaYmd($fechaReferencia) : date('Y-m-d');
        if ($referencia === null) {
            $referencia = date('Y-m-d');
        }
        return date('Y-m-01', strtotime($referencia . ' 12:00:00'));
    }

    /**
     * Define si el devengo se registra en la fecha faltante o en el primer día del mes actual.
     */
    public static function resolverModoRegistroMesAnterior(
        string $fechaFaltanteYmd,
        ?string $modoSolicitado = null,
        ?string $fechaReferencia = null
    ): string {
        if (!self::esFechaMesAnterior($fechaFaltanteYmd, $fechaReferencia)) {
            return self::MODO_REGISTRO_REAL;
        }
        if (!self::esVentanaConfirmacionMesAnterior($fechaReferencia)) {
            return self::MODO_REGISTRO_MES_ACTUAL;
        }
        $modo = strtoupper(trim((string) $modoSolicitado));
        if ($modo === self::MODO_REGISTRO_MES_ACTUAL) {
            return self::MODO_REGISTRO_MES_ACTUAL;
        }
        return self::MODO_REGISTRO_REAL;
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
        $log = defined('APPPATH') ? APPPATH . '/../logs/auditoria_devengo_proceso.log' : __DIR__ . '/../../logs/auditoria_devengo_proceso.log';
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

        $fechaFaltante = self::normalizarFechaYmd(
            (string) ($fila['FECHA_CALC_ISO'] ?? $fila['FECHA_CALC'] ?? $fila['FECHA_FALTANTE'] ?? '')
        );
        if ($fechaFaltante === null) {
            return Model::Responde(false, 'No se pudo determinar la fecha faltante del devengo.', null, 'Fecha inválida');
        }

        $modoSolicitado = (string) ($datos['modo_registro'] ?? $fila['MODO_REGISTRO'] ?? $fila['modo_registro'] ?? '');
        $modoRegistro = self::resolverModoRegistroMesAnterior($fechaFaltante, $modoSolicitado);

        try {
            return HerramientasDao::ProcesarDevengoIndividual($fila, $usuario, $perfil, $ip, 'INDIVIDUAL', $modoRegistro);
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

        foreach ($registros as $idx => $fila) {
            if (!is_array($fila)) {
                continue;
            }
            $fechaFaltante = self::normalizarFechaYmd(
                (string) ($fila['FECHA_CALC_ISO'] ?? $fila['FECHA_CALC'] ?? $fila['FECHA_FALTANTE'] ?? '')
            );
            if ($fechaFaltante === null) {
                return Model::Responde(false, 'No se pudo determinar la fecha faltante de uno de los registros.', null, 'Fecha inválida');
            }
            $modoSolicitado = (string) ($fila['MODO_REGISTRO'] ?? $fila['modo_registro'] ?? '');
            $registros[$idx]['MODO_REGISTRO'] = self::resolverModoRegistroMesAnterior($fechaFaltante, $modoSolicitado);
        }

        try {
            return HerramientasDao::ProcesarDevengoMasivo($registros, $usuario, $perfil, $ip);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error en procesamiento masivo.', null, $e->getMessage());
        }
    }
}
