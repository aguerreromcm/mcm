<?php

namespace Jobs\controllers;

include_once dirname(__DIR__) . '/../Core/Job.php';
include_once dirname(__DIR__) . '/models/JobsCredito.php';
include_once dirname(__DIR__) . '/../libs/PHPMailer/Mensajero.php';
include_once dirname(__DIR__) . '/../libs/PhpSpreadsheet/PhpSpreadsheet.php';

use Core\Job;
use Jobs\models\JobsCredito as JobsDao;
use Mensajero;

define('APROBADA', 'Aprobada');
define('RECHAZADA', 'Rechazada');
define('PENDIENTE', 'En espera de liquidación');
define('CONCLUIDA', 'Concluida');

class JobsCredito extends Job
{
    public function __construct()
    {
        parent::__construct('JobsCredito');
    }

    public function JobCheques()
    {
        self::SaveLog('Inicio');
        return self::SaveLog('Finalizado: No hay créditos autorizados');
        $resumen = [];
        $creditos = JobsDao::GetCreditosAutorizados();
        if (!$creditos['success']) return self::SaveLog('Finalizado con error: ' . $creditos['mensaje'] . '->' . $creditos['error']);
        if (count($creditos['datos']) == 0) return self::SaveLog('Finalizado: No hay créditos autorizados');

        foreach ($creditos['datos'] as $key => $credito) {
            $chequera = JobsDao::GetNoChequera($credito['CDGCO']);
            if (!$chequera['success'] || count($chequera['datos']) == 0) {
                $resumen[] = [
                    'credito' => $credito['CDGCO'],
                    'error' => self::SaveLog($chequera['mensaje'] . ': ' . ($chequera['error'] ?? ''))
                ];
                continue;
            }

            $cheque = JobsDao::GetNoCheque($chequera['datos']['CDGCB']);
            if (!$cheque['success'] || count($cheque['datos']) == 0) {
                $resumen[] = [
                    'credito' => $credito['CDGCO'],
                    'chequera' => $chequera['datos']['CDGCB'],
                    'error' => self::SaveLog($cheque['mensaje'] . ': ' . ($cheque['error'] ?? ''))
                ];
                continue;
            }

            $datos = [
                //Datos para actualizar PRC y PRN
                'cheque' => $cheque['datos']['CHQSIG'],
                'cdgcb' => $chequera['datos']['CDGCB'],
                'cdgcl' => $credito['CDGCL'],
                'cdgns' => $credito['CDGNS'],
                'ciclo' => $credito['CICLO'],
                'cantautor' => $credito['CANTAUTOR'],
                //Datos para MP, JP y MPC
                'prmCDGCLNS' => $credito['CDGNS'],
                'prmCICLO' => $credito['CICLO'],
                'prmINICIO' => $credito['INICIO'],
                'vINTERES' => $credito['INTERES'],
                'vCLIENTE' => $credito['CDGCL'],
            ];

            $resumen[] = JobsDao::GeneraCheques($datos);
        }

        self::SaveLog(json_encode($resumen));
        self::SaveLog('Finalizado');
    }

    public function SolicitudesFinalizadas()
    {
        self::SaveLog('Inicio');
        $resumen = [];
        $creditos = JobsDao::GetSolicitudes();

        if (!$creditos['success']) return self::SaveLog('Finalizado con error: ' . $creditos['mensaje'] . '->' . $creditos['error']);
        if (count($creditos['datos']) == 0) return self::SaveLog('Finalizado: No hay solicitudes de crédito por procesar');

        $destAprobadas = $this->GetDestinatarios(JobsDao::GetDestinatarios_Aplicacion(1));
        $destRechazadas = $this->GetDestinatarios(JobsDao::GetDestinatarios_Aplicacion(2));

        foreach ($creditos['datos'] as $key => $credito) {
            $aprobada = $credito['APROBADA'] === '1' ? true : false;
            $r = ['success' => false];
            $estatus = 'No procesada';

            if ($aprobada && $credito['LIQUIDADO'] === '0' && $credito['SITUACION'] === 'S') {
                $r = JobsDao::PonerSolicitudEnEspera($credito);
                $estatus = PENDIENTE;
            } else if ($aprobada && $credito['LIQUIDADO'] === '1') {
                if ($credito['SITUACION'] === 'S') {
                    $r = JobsDao::ProcesaSolicitudAprobada($credito);
                    $estatus = APROBADA;
                } else if ($credito['SITUACION'] === 'T') {
                    $r = JobsDao::ConcluirSolicitudEnEspera($credito);
                    $estatus = CONCLUIDA;
                }
            } else if (!$aprobada) {
                $r = JobsDao::ProcesaSolicitudRechazada($credito);
                $estatus = RECHAZADA;
            }

            $r['datos'] = [
                'credito' => $credito['CREDITO'],
                'ciclo' => $credito['CICLO'],
                'fechaSolicitud' => $credito['SOLICITUD'],
                'concluyo' => $credito['CDGPE'],
                'liquidado' => $credito['LIQUIDADO'],
                'situacion' => $credito['SITUACION'],
                'estatus' => $estatus
            ];

            if ($r['success'] && $estatus !== PENDIENTE) {
                $dest = $aprobada ? $destAprobadas : $destRechazadas;
                $dest = $this->GetDestinatarios(JobsDao::GetDestinatarios_Sucursal($credito['CO']), $dest);
                $plantilla = $this->Plantilla_mail_Solicitud_Finalizada($credito, $aprobada);
                $tipo = $aprobada ? 'Aprobación' : 'Rechazo';

                Mensajero::EnviarCorreo(
                    $dest,
                    $tipo . ' de solicitud de crédito por Call Center',
                    Mensajero::Notificaciones($plantilla)
                );
            }

            $resumen[] = $r;
            //genera solo 1 solicitud para pruebas
            // break;
        }

        self::SaveLog($resumen);
        self::SaveLog('Finalizado');
    }

    public function ResumenRechazadas()
    {
        self::SaveLog('Inicio');
        $procesadas = self::ReadLog('SolicitudesRechazadas');

        if (count($procesadas) == 0) return self::SaveLog('Finalizado: No se procesaron rechazos de crédito');

        $filas = [];
        foreach ($procesadas as $key => $procesada) {
            if ($procesada['success']) $filas[] = $procesada['datos'];
        }

        if (count($filas) == 0) return self::SaveLog('Finalizado: Los rechazos de crédito no se procesaron correctamente');

        $columnas = [
            ['letra' => 'A', 'titulo' => 'Crédito', 'campo' => 'credito', 'estilo' => []],
            ['letra' => 'B', 'titulo' => 'Ciclo', 'campo' => 'ciclo', 'estilo' => []],
            ['letra' => 'C', 'titulo' => 'Solicitud', 'campo' => 'solicitud', 'estilo' => []],
            ['letra' => 'D', 'titulo' => 'Concluyó', 'campo' => 'concluyo', 'estilo' => []],
        ];


        self::SaveLog('Finalizado');
    }

    private function Plantilla_mail_Solicitud_Finalizada($credito, $aprobada)
    {
        $pasosFinalesA = <<<HTML
            <p style="text-align: center">
                Para completar el proceso imprima la documentación legal correspondiente, si tiene alguna duda o inconveniente, comuníquese con {$credito['NOMBRE_PE']} ({$credito['CDGPE']}) o con el gerente de call center.
            </p>
            <p style="text-align: center">
                <b>Asegúrese de seguir todos los protocolos establecidos para la correcta gestión y archivo de los documentos.</b>
            </p>
        HTML;

        $pasosFinalesR = <<<HTML
            <p style="text-align: center">
                Si tiene alguna duda o inconveniente referente al rechazo de la solicitud, comuníquese con {$credito['NOMBRE_PE']} ({$credito['CDGPE']}) o con el gerente de call center.
            </p>
        HTML;

        $titulo = $aprobada ? '✅ Solicitud de crédito APROBADA.' : '❌ Solicitud de crédito RECHAZADA.';
        $pasosFinales = $aprobada ? $pasosFinalesA : $pasosFinalesR;

        return <<<HTML
            <!-- Encabezado -->
            <h2 style="text-align: center">$titulo</h2>
            <!-- Información General -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    📄 Información General
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <li>🔸<b>Cliente:</b> {$credito['CL']} - {$credito['NOMBRE_CL']}</li>
                    <li>🔸<b>Crédito:</b> {$credito['CDGNS']}</li>
                    <li>🔸<b>Ciclo:</b> {$credito['CICLO']}</li>
                    <li>🔸<b>Fecha de captura:</b> {$credito['SOLICITUD']}</li>
                    <li>🔸<b>Región:</b> {$credito['RG']} - {$credito['NOMBRE_RG']}</li>
                    <li>🔸<b>Agencia:</b> {$credito['CO']} - {$credito['NOMBRE_CO']}</li>
                    <li>🔸<b>Estatus final:</b> {$credito['ESTATUS']}</li>
                </ul>
            </div>

            <!-- Detalle de llamadas realizadas -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    ☎️ Detalle de llamadas realizadas
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <li>🔸<b>Total de llamadas:</b> {$credito['NO_LLAMADAS']}</li>
                    <li>🔸<b>Intentos realizados:</b> {$credito['INTENTOS']}</li>
                    <li>🔸<b>Fecha primera llamada:</b> {$credito['PRIMERA_LLAMADA']}</li>
                    <li>🔸<b>Fecha última llamada:</b> {$credito['ULTIMA_LLAMADA']}</li>
                </ul>
            </div>

            <!-- Comentarios del Call Center -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    📝 Comentarios del Call Center
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <!-- <li>🔸<b>Comentario inicial:</b> {$credito['COMENTARIO_INICIAL']}</li> -->
                    <li>🔸<b>Comentario final:</b> {$credito['COMENTARIO_FINAL']}</li>
                </ul>
            </div>

            <!-- Próximos pasos -->
            <div style="padding-top: 14px">
                $pasosFinales
            </div>
        HTML;
    }

    /**
     * Genera el reporte de días de atraso en CSV (PRN situación L, reporte completo).
     *
     * @param string $rutaSalida Directorio de salida; si vacío, usa USERPROFILE\Desktop
     */
    public function RepDiasAtraso($rutaSalida = '')
    {
        self::SaveLog('Inicio');
        set_time_limit(0);

        $ruta = trim((string) $rutaSalida);
        if ($ruta === '') {
            $profile = getenv('USERPROFILE');
            if ($profile) {
                $ruta = $profile . DIRECTORY_SEPARATOR . 'Desktop';
            }
        }

        if ($ruta === '' || !is_dir($ruta)) {
            self::SaveLog('Ruta de salida no existe, se omite la generación del CSV: ' . $ruta);
            return;
        }

        $resultado = JobsDao::GetRepDiasAtraso();
        if (!$resultado['success']) {
            self::SaveLog('Finalizado con error: ' . $resultado['mensaje'] . '->' . ($resultado['error'] ?? ''));
            return;
        }

        $filas = $resultado['datos'] ?? [];
        $nombre = 'Rep_Días_atraso_' . date('Ymd') . '.csv';
        $archivo = $ruta . DIRECTORY_SEPARATOR . $nombre;

        $out = @fopen($archivo, 'w');
        if ($out === false) {
            self::SaveLog('No se pudo escribir el archivo CSV: ' . $archivo);
            return;
        }

        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['COD_CTE', 'CICLO', 'NOMBRE', 'INICIO', 'DIAS_ATRASO'], ',');
        foreach ($filas as $fila) {
            fputcsv($out, [
                $fila['COD_CTE'] ?? '',
                $fila['CICLO'] ?? '',
                $fila['NOMBRE'] ?? '',
                $fila['INICIO'] ?? '',
                $fila['DIAS_ATRASO'] ?? '',
            ], ',');
        }
        fclose($out);

        self::SaveLog('CSV generado: ' . $archivo . ' (' . count($filas) . ' registros)');
        self::SaveLog('Finalizado');
    }

    public function CierreDia($fecha, $usuario)
    {
        self::SaveLog('Inicio');
        if (empty($fecha) || empty($usuario)) {
            self::SaveLog('Finalizado con error: Fecha y usuario son requeridos');
            return;
        }

        $dest = null;
        $mensajeRes = null;
        $datos = [
            'fecha' => $fecha,
            'usuario' => $usuario
        ];

        $resultado = JobsDao::CierreDia($datos);

        if (isset($resultado['datos']) && isset($resultado['datos']['MENSAJE'])) {
            $lineas = explode(PHP_EOL, trim($resultado['datos']['MENSAJE']));
            $mensajeRes = end($lineas);
            $resultado['datos']['MENSAJE'] = $mensajeRes;
        }

        if ($resultado['success']) {
            $dest = $this->GetDestinatarios(JobsDao::GetDestinatarios_Aplicacion(5));
            $mensaje = "Cierre de día concluido: fecha $fecha - usuario $usuario";
        } else {
            $dest = $this->GetDestinatarios(JobsDao::GetDestinatarios_Aplicacion(6));
            $error = isset($resultado['error']) ? $resultado['error'] : $mensajeRes;
            $mensaje = "Finalizado con error: $error";
        }

        if (!empty($dest)) {
            try {
                $fecha = new \DateTime($fecha);
                $fecha = $fecha->format('d/m/Y');
                Mensajero::EnviarCorreo(
                    $dest,
                    "Cierre del día $fecha",
                    Mensajero::Notificaciones(self::PLantilla_mail_Cierre_Dia($resultado['datos'] ?? []))
                );

                $resCorreo = JobsDao::CorreoCierreDia($resultado['datos'] ?? []);
                self::SaveLog('Estatus del correo: ' . $resCorreo['mensaje'] . ' -> ' . ($resCorreo['error'] ?? ''));
            } catch (\Exception $e) {
                self::SaveLog('Error al enviar correo: ' . $e->getMessage());
            }
        }

        self::SaveLog($mensaje);
    }

    public function PLantilla_mail_Cierre_Dia($datos)
    {
        $moneda = new \NumberFormatter('es_MX', \NumberFormatter::CURRENCY);
        $fecha = new \IntlDateFormatter(
            'es_ES',
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            'America/Mexico_City',
            \IntlDateFormatter::GREGORIAN,
            "d 'de' MMMM 'de' y" // Definimos el patrón manualmente
        );
        $resumen = JobsDao::GetResumenCierreDia($datos);

        $fecha_calculo = isset($datos['FECHA_CALCULO']) ? $fecha->format(\DateTime::createFromFormat('d/m/Y', $datos['FECHA_CALCULO'])) : 'N/A';

        $devengo_registros = isset($datos['DEVENGO_REGISTROS']) ? $datos['DEVENGO_REGISTROS'] : '0';
        $devengo_monto = isset($datos['DEVENGO_MONTO']) ? $moneda->formatCurrency(($datos['DEVENGO_MONTO'] ?? 0), 'MXN') : '$ 0.00';

        if ($resumen['success']) {
            $pagos = $resumen['datos']['pagos'] ?? [];
            $detalle = $resumen['datos']['detalle'] ?? [];
            $mp = $resumen['datos']['mp'] ?? [];

            $pagos_total_registros = $pagos['TOTAL_REGISTROS'] ?? 0;
            $pagos_total_monto = $moneda->formatCurrency(($pagos['TOTAL_MONTO'] ?? 0), 'MXN');
            $pagos_pendiente_registros = $pagos['PENDIENTES_REGISTROS'] ?? 0;
            $pagos_pendiente_monto = $moneda->formatCurrency(($pagos['PENDIENTES_MONTO'] ?? 0), 'MXN');
            $pagos_aplicados_registros = $pagos['APLICADOS_REGISTROS'] ?? 0;
            $pagos_aplicados_monto = $moneda->formatCurrency(($pagos['APLICADOS_MONTO'] ?? 0), 'MXN');
            $pagos_registros = $detalle['PAGOS_REGISTROS'] ?? 0;
            $pagos_monto = $moneda->formatCurrency(($detalle['PAGOS_MONTO'] ?? 0), 'MXN');
            $garantias_registros = $detalle['GARANTIAS_REGISTROS'] ?? 0;
            $garantias_monto = $moneda->formatCurrency(($detalle['GARANTIAS_MONTO'] ?? 0), 'MXN');
            $incidencias_registros = $detalle['INCIDENCIAS_REGISTROS'] ?? 0;
            $incidencias_monto = $moneda->formatCurrency(($detalle['INCIDENCIAS_MONTO'] ?? 0), 'MXN');
            $mp_total_registros = $mp['TOTAL_REGISTROS'] ?? 0;
            $mp_total_monto = $moneda->formatCurrency(($mp['TOTAL_MONTO'] ?? 0), 'MXN');
            $mp_pendiente_registros = $mp['PENDIENTES_REGISTROS'] ?? 0;
            $mp_pendiente_monto = $moneda->formatCurrency(($mp['PENDIENTES_MONTO'] ?? 0), 'MXN');
            $mp_conciliados_registros = $mp['CONCILIADOS_REGISTROS'] ?? 0;
            $mp_conciliados_monto = $moneda->formatCurrency(($mp['CONCILIADOS_MONTO'] ?? 0), 'MXN');
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
                            Devengo para el día {$datos['DEVENGO_FECHA']}
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

if (isset($argv[1])) {
    $jobs = new JobsCredito();

    switch ($argv[1]) {
        case 'JobCheques':
            $jobs->JobCheques();
            break;
        case 'SolicitudesFinalizadas':
            $jobs->SolicitudesFinalizadas();
            break;
        case 'RepDiasAtraso':
            $ruta = isset($argv[2]) ? $argv[2] : '';
            $jobs->RepDiasAtraso($ruta);
            break;
        case 'CierreDia':
            $fecha = isset($argv[2]) ? $argv[2] : NULL;
            $usuario = isset($argv[3]) ? $argv[3] : NULL;
            $jobs->CierreDia($fecha, $usuario);
            break;
        case 'help':
            echo 'JobCheques: Actualiza los cheques de los créditos autorizados\n';
            echo 'SolicitudesFinalizadas: Evalúa el comentario final de la solicitud y la procesa para concluir con la solicitud\n';
            echo 'RepDiasAtraso [ruta]: Genera Rep_Días_atraso_YYYYMMDD.csv; ruta opcional (por defecto Desktop del usuario)\n';
            echo 'CierreDia [fecha] [usuario]: Ejecuta el cierre del día para la fecha y usuario especificados\n';
            break;
        default:
            echo 'No se encontró el job solicitado.\nEjecute "php JobsAhorro.php help" para ver los jobs disponibles.\n';
            break;
    }
} else echo 'Debe especificar el job a ejecutar.\nEjecute "php JobsAhorro.php help" para ver los jobs disponibles.\n';
