<?php

namespace App\services;

include_once dirname(__DIR__, 2) . '/Jobs/models/JobsCredito.php';

use Jobs\models\JobsCredito as JobsDao;

/**
 * Contenido del correo de resumen del cierre de día. Lo usa el Job al enviar el correo
 * y la pantalla de cierre para mostrar exactamente el mismo resumen.
 */
class CierreDiaCorreoPresenter
{
    /**
     * @param array $datos Renglón de JobsCredito::ResultadoCierreDia con MENSAJE reducido a su última línea
     * @return string HTML interior para Mensajero::Notificaciones
     */
    public static function plantilla(array $datos)
    {
        $fmtMoneda = function ($n) {
            return '$ ' . number_format($n, 2);
        };

        $devengoFecha = isset($datos['DEVENGO_FECHA']) ? $datos['DEVENGO_FECHA'] : 'N/A';
        $fecha_calculo = isset($datos['FECHA_CALCULO']) ? $datos['FECHA_CALCULO'] : 'N/A';
        $devengo_registros = isset($datos['DEVENGO_REGISTROS']) ? $datos['DEVENGO_REGISTROS'] : '0';
        $devengo_monto = isset($datos['DEVENGO_MONTO']) ? $fmtMoneda($datos['DEVENGO_MONTO'] ?? 0) : '$ 0.00';

        $resumen = JobsDao::GetResumenCierreDia($datos);

        if ($resumen['success']) {
            $pagos = $resumen['datos']['pagos'] ?? [];
            $detalle = $resumen['datos']['detalle'] ?? [];
            $mp = $resumen['datos']['mp'] ?? [];
            $garantias = $resumen['datos']['garantias'] ?? [];

            $pagos_total_registros = $pagos['TOTAL_REGISTROS'] ?? 0;
            $pagos_total_monto = $fmtMoneda($pagos['TOTAL_MONTO'] ?? 0);
            $pagos_pendiente_registros = $pagos['PENDIENTES_REGISTROS'] ?? 0;
            $pagos_pendiente_monto = $fmtMoneda($pagos['PENDIENTES_MONTO'] ?? 0);
            $pagos_aplicados_registros = $pagos['APLICADOS_REGISTROS'] ?? 0;
            $pagos_aplicados_monto = $fmtMoneda($pagos['APLICADOS_MONTO'] ?? 0);
            $pagos_registros = $detalle['PAGOS_REGISTROS'] ?? 0;
            $pagos_monto = $fmtMoneda($detalle['PAGOS_MONTO'] ?? 0);
            $garantias_registros = $detalle['GARANTIAS_REGISTROS'] ?? 0;
            $garantias_monto = $fmtMoneda($detalle['GARANTIAS_MONTO'] ?? 0);
            $incidencias_registros = $detalle['INCIDENCIAS_REGISTROS'] ?? 0;
            $incidencias_monto = $fmtMoneda($detalle['INCIDENCIAS_MONTO'] ?? 0);
            $mp_total_registros = $mp['TOTAL_REGISTROS'] ?? 0;
            $mp_total_monto = $fmtMoneda($mp['TOTAL_MONTO'] ?? 0);
            $mp_pendiente_registros = $mp['PENDIENTES_REGISTROS'] ?? 0;
            $mp_pendiente_monto = $fmtMoneda($mp['PENDIENTES_MONTO'] ?? 0);
            $mp_conciliados_registros = $mp['CONCILIADOS_REGISTROS'] ?? 0;
            $mp_conciliados_monto = $fmtMoneda($mp['CONCILIADOS_MONTO'] ?? 0);
            $garantias_por_saldo = $garantias['POR_SALDO'] ?? 0;
            $garantias_por_saldo_monto = $fmtMoneda($garantias['POR_SALDO_MONTO'] ?? 0);
            $garantias_por_fecha_fin = $garantias['POR_FECHA'] ?? 0;
            $garantias_por_fecha_fin_monto = $fmtMoneda($garantias['POR_FECHA_MONTO'] ?? 0);
        }

        return <<<HTML
            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                style="border-spacing: 0; border-collapse: separate"
            >
                <tr>
                    <td colspan="3">
                        <div
                            style="
                                background: linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 16px;
                                margin-bottom: 18px;
                            "
                        >
                            Resumen de cierre del día $fecha_calculo
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div
                            style="
                                font-size: 13px;
                                font-weight: 700;
                                color: #475569;
                                text-transform: uppercase;
                                letter-spacing: 0.06em;
                                margin-bottom: 10px;
                            "
                        >
                            Proceso
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 33.33%; padding: 0 6px 12px 0">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 12px;
                                padding: 12px 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 4px;
                                    font-weight: 600;
                                "
                            >
                                Usuario
                            </div>
                            <div style="font-size: 14px; color: #0f172a; font-weight: 700">
                                {$datos['USUARIO']}
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 3px 12px 3px">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 12px;
                                padding: 12px 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 4px;
                                    font-weight: 600;
                                "
                            >
                                Inicio
                            </div>
                            <div style="font-size: 14px; color: #0f172a; font-weight: 700">
                                {$datos['INICIO']}
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 0 12px 6px">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 12px;
                                padding: 12px 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 4px;
                                    font-weight: 600;
                                "
                            >
                                Fin
                            </div>
                            <div style="font-size: 14px; color: #0f172a; font-weight: 700">
                                {$datos['FIN']}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 33.33%; padding: 0 6px 12px 0">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 12px;
                                padding: 12px 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 4px;
                                    font-weight: 600;
                                "
                            >
                                Registros
                            </div>
                            <div style="font-size: 14px; color: #0f172a; font-weight: 700">
                                {$datos['CIERRE_REGISTROS']}
                            </div>
                        </div>
                    </td>
                    <td colspan="2" style="width: 33.33%; padding: 0 3px 12px 3px">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 12px;
                                padding: 12px 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 4px;
                                    font-weight: 600;
                                "
                            >
                                Estatus
                            </div>
                            <div style="font-size: 14px; color: #0f172a; font-weight: 700">
                                {$datos['MENSAJE']}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 6px 0 18px">
                        <div style="height: 1px; background: #dbe3ef"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div
                            style="
                                font-size: 13px;
                                font-weight: 700;
                                color: #475569;
                                text-transform: uppercase;
                                letter-spacing: 0.06em;
                                margin-bottom: 10px;
                            "
                        >
                            Pagos del día
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 33.33%; padding: 0 6px 12px 0">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Total
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $pagos_total_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $pagos_total_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 3px 12px 3px">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Pendientes
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $pagos_pendiente_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $pagos_pendiente_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 0 12px 6px">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Aplicados
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $pagos_aplicados_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $pagos_aplicados_monto
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div
                            style="
                                font-size: 13px;
                                font-weight: 700;
                                color: #475569;
                                text-transform: uppercase;
                                letter-spacing: 0.06em;
                                margin-bottom: 10px;
                            "
                        >
                            Identificados
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 33.33%; padding: 0 6px 12px 0">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Pagos
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $pagos_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $pagos_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 3px 12px 3px">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Garantías
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $garantias_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $garantias_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 0 12px 6px">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Incidencias
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $incidencias_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $incidencias_monto
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 6px 0 18px">
                        <div style="height: 1px; background: #dbe3ef"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div
                            style="
                                font-size: 13px;
                                font-weight: 700;
                                color: #475569;
                                text-transform: uppercase;
                                letter-spacing: 0.06em;
                                margin-bottom: 10px;
                            "
                        >
                            Garantías aplicadas
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 33.33%; padding: 0 6px 12px 0">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                por saldo
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $garantias_por_saldo
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $garantias_por_saldo_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 6px 12px 0">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                por fecha fin
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $garantias_por_fecha_fin
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $garantias_por_fecha_fin_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 0 12px 6px"></td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 6px 0 18px">
                        <div style="height: 1px; background: #dbe3ef"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div
                            style="
                                font-size: 13px;
                                font-weight: 700;
                                color: #475569;
                                text-transform: uppercase;
                                letter-spacing: 0.06em;
                                margin-bottom: 10px;
                            "
                        >
                            Conciliación
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 33.33%; padding: 0 6px 12px 0">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Total
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $mp_total_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $mp_total_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 6px 12px 0">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Pendientes
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $mp_pendiente_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $mp_pendiente_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 3px 12px 3px">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Conciliados
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $mp_conciliados_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $mp_conciliados_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 0 12px 6px"></td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 6px 0 18px">
                        <div style="height: 1px; background: #dbe3ef"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div
                            style="
                                font-size: 13px;
                                font-weight: 700;
                                color: #475569;
                                text-transform: uppercase;
                                letter-spacing: 0.06em;
                                margin-bottom: 10px;
                            "
                        >
                            Devengo para el día {$devengoFecha}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 33.33%; padding: 0 6px 12px 0">
                        <div
                            style="
                                background: #f8fafc;
                                border: 1px solid #dbe3ef;
                                border-radius: 14px;
                                padding: 14px;
                            "
                        >
                            <div
                                style="
                                    font-size: 12px;
                                    color: #64748b;
                                    margin-bottom: 6px;
                                    font-weight: 600;
                                "
                            >
                                Créditos
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    text-align: end;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                $devengo_registros
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    text-align: end;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                $devengo_monto
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 3px 12px 3px"></td>
                    <td style="width: 33.33%; padding: 0 0 12px 6px"></td>
                </tr>
            </table>
        HTML;
    }
}
