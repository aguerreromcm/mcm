<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;
use App\repositories\AplicacionPagosRepository;
use App\repositories\PagosAplicacionRepository;
use App\repositories\PagosNoIdentificadosRepository;

/**
 * Service: identificación de pagos no identificados (PDI) y su aplicación en cartera con SP_IMPORTA_CONCILIA.
 */
class PagosNoIdentificadosService
{
    /** Tipos de movimiento con los que se puede identificar un pago y el estatus que toma en PDI. */
    const ESTATUS_POR_TIPO = ['P' => 'IP', 'X' => 'IP', 'G' => 'IG'];

    /**
     * Pagos no identificados de la fecha de depósito indicada.
     *
     * @param string $fecha Y-m-d
     * @return array { success, mensaje, datos: { fecha, filas, resumen } }
     */
    public static function consultar($fecha)
    {
        $fechaNorm = PagosAplicacionRepository::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return Model::Responde(false, 'Seleccione una fecha válida.', null, 'Fecha inválida');
        }

        try {
            $filas = (new PagosNoIdentificadosRepository())->getPendientes($fechaNorm);
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al consultar los pagos no identificados.', null, $e->getMessage());
        }

        $monto = 0.0;
        foreach ($filas as $i => $f) {
            $filas[$i]['CANTIDAD'] = round((float) ($f['CANTIDAD'] ?? 0), 2);
            $filas[$i]['DESCRIPCION'] = CierreDiaService::limpiarMotivo($f['DESCRIPCION'] ?? '');
            $monto += $filas[$i]['CANTIDAD'];
        }

        return Model::Responde(true, 'OK', [
            'fecha' => $fechaNorm,
            'filas' => $filas,
            'resumen' => ['total' => count($filas), 'monto' => round($monto, 2)],
        ]);
    }

    /**
     * Identifica el pago con el crédito, ciclo y tipo indicados y lo aplica en cartera.
     * El crédito debe existir en PRN en situación Entregado (E). Si la aplicación no se concreta,
     * el pago regresa a no identificado.
     *
     * @param array $datos fecha, secuencia, credito, ciclo, tipo, usuario
     * @return array { success, mensaje, datos?: { observacion, credito, ciclo, referencia } }
     */
    public static function aplicar(array $datos)
    {
        $fecha = PagosAplicacionRepository::coerceFechaYmD($datos['fecha'] ?? '');
        $secuencia = trim((string) ($datos['secuencia'] ?? ''));
        $credito = trim((string) ($datos['credito'] ?? ''));
        $ciclo = trim((string) ($datos['ciclo'] ?? ''));
        $tipo = strtoupper(trim((string) ($datos['tipo'] ?? '')));
        $usuario = trim((string) ($datos['usuario'] ?? ''));

        if ($usuario === '') {
            return Model::Responde(false, 'Usuario no identificado.', null, 'Usuario vacío');
        }
        if ($fecha === null || $secuencia === '') {
            return Model::Responde(false, 'No se identificó el pago a aplicar.', null, 'Pago inválido');
        }
        if (!isset(self::ESTATUS_POR_TIPO[$tipo])) {
            return Model::Responde(false, 'Seleccione un tipo de movimiento válido (P, X o G).', null, 'Tipo inválido');
        }
        if (!preg_match('/^\d{1,6}$/', $credito)) {
            return Model::Responde(false, 'El número de crédito debe ser numérico (máximo 6 dígitos).', null, 'Crédito inválido');
        }
        if (!preg_match('/^\d{1,2}$/', $ciclo)) {
            return Model::Responde(false, 'El ciclo debe ser numérico (máximo 2 dígitos).', null, 'Ciclo inválido');
        }
        $credito = str_pad($credito, 6, '0', STR_PAD_LEFT);
        $ciclo = str_pad($ciclo, 2, '0', STR_PAD_LEFT);
        $estatus = self::ESTATUS_POR_TIPO[$tipo];

        if (CierreDiaService::estadoCierreFecha($fecha)['enEjecucion']) {
            return Model::Responde(false, 'Hay un cierre de día en ejecución. Espere a que termine para aplicar el pago.', null, 'Cierre en ejecución');
        }

        $repo = new PagosNoIdentificadosRepository();
        try {
            $pago = $repo->getPago($fecha, $secuencia);
            if ($pago === null) {
                return Model::Responde(false, 'El pago no identificado ya no existe.', null, 'Pago inexistente');
            }
            if (trim((string) $pago['ESTATUS']) !== 'RE') {
                return Model::Responde(
                    false,
                    'El pago ya fue identificado por ' . ($pago['CDGPE_IDEN'] ?: 'otro usuario') . ($pago['FECHA_IDEN_FMT'] ? ' el ' . $pago['FECHA_IDEN_FMT'] : '') . '.',
                    null,
                    'Pago identificado'
                );
            }

            $infoCredito = $repo->getCreditoEntregado($credito, $ciclo, $tipo);
            if ($infoCredito === null) {
                return Model::Responde(false, "El crédito {$credito} ciclo {$ciclo} no existe o no está en situación Entregado.", null, 'Crédito no válido');
            }
        } catch (\Throwable $e) {
            return Model::Responde(false, 'Error al validar el pago.', null, $e->getMessage());
        }

        $db = new Database();
        if ($db->db_activa === null) {
            return Model::Responde(false, 'No hay conexión a la base de datos.', null, 'Conexión nula');
        }

        $referencia = trim((string) $infoCredito['REFERENCIA']);
        $identificador = date('dmYHis');
        $renExcel = (int) $pago['SECUENCIA'];

        try {
            $db->IniciaTransaccion();
            if (!$repo->marcarIdentificado($pago, $infoCredito, $estatus, $usuario, $db)) {
                $db->CancelaTransaccion();
                return Model::Responde(false, 'El pago ya fue identificado por otro usuario. Consulte de nuevo.', null, 'Pago identificado');
            }

            $res = $db->spImportaConcilia(
                $credito,
                $ciclo,
                (string) $pago['CANTIDAD'],
                $fecha,
                $referencia,
                (string) $pago['CDGCB'],
                $usuario,
                $identificador,
                $renExcel
            );
        } catch (\Throwable $e) {
            self::revertir($repo, $db, $pago, $estatus);
            return Model::Responde(false, 'Error al aplicar el pago; el pago sigue como no identificado.', null, $e->getMessage());
        }

        $val = (int) ($res['validacion'] ?? -1);
        $resultado = CierreDiaService::limpiarMotivo($res['resultado'] ?? '');

        if (($res['success'] ?? false) && in_array($val, [0, 1, 6], true)) {
            // El pago ya está en cartera (el SP puede haber confirmado): si falla la liga, no se revierte.
            try {
                $secuenciaCartera = $repo->registrarSecuenciaCartera($pago, $db);
            } catch (\Throwable $e) {
                $secuenciaCartera = null;
            }
            $db->ConfirmaTransaccion();
            $observacion = $val !== 1;
            $mensaje = $observacion
                ? 'El pago se identificó y se importó al crédito ' . $credito . ', pero no se pudo conciliar: ' . ($resultado !== '' ? $resultado : 'sin detalle del procedimiento.')
                : 'Pago aplicado en cartera al crédito ' . $credito . ' ciclo ' . $ciclo . '.';
            if ($secuenciaCartera === null) {
                $observacion = true;
                $mensaje .= ' No se encontró el movimiento en cartera para ligarlo al pago no identificado.';
            }

            return Model::Responde(true, $mensaje, [
                'observacion' => $observacion,
                'credito' => $credito,
                'ciclo' => $ciclo,
                'referencia' => $referencia,
            ]);
        }

        $motivo = '';
        if ($val > 1) {
            $motivo = (new AplicacionPagosRepository())->getMotivoValidacion($identificador, $renExcel, $val, $db);
        }
        self::revertir($repo, $db, $pago, $estatus);

        $detalle = $motivo !== '' ? $motivo : ($resultado !== '' ? $resultado : 'El procedimiento no aplicó el pago.');
        return Model::Responde(false, 'No fue posible aplicar el pago; sigue como no identificado. ' . $detalle, null, $res);
    }

    /**
     * Revierte la transacción y regresa el pago a no identificado
     * (los SP de importación pueden haber confirmado la transacción por su cuenta).
     *
     * @param PagosNoIdentificadosRepository $repo
     * @param Database $db
     * @param array $pago
     * @param string $estatus
     * @return void
     */
    private static function revertir(PagosNoIdentificadosRepository $repo, Database $db, array $pago, $estatus)
    {
        try {
            $db->CancelaTransaccion();
        } catch (\Throwable $e) {
            // Sin transacción activa
        }
        $repo->restaurarNoIdentificado($pago, $estatus, $db);
    }
}
