<?php

namespace Jobs\controllers;

include_once dirname(__DIR__) . '/../Core/Job.php';
include_once dirname(__DIR__) . '/../libs/PHPMailer/Mensajero.php';
include_once dirname(__DIR__) . "/models/JobPruebaMail.php";


use Core\Job;
use Jobs\models\JobPruebaMail as JobsDao;
use App\services\CierreDiaResumenPresenter;
use Mensajero;

class JobsPrueba extends Job
{
    public function __construct()
    {
        parent::__construct("Job_pruba_mail");
    }

    /**
     * Autoload App/Core para consultar el mismo resumen que la pantalla.
     */
    private function registrarAutoloadApp()
    {
        if (defined('PROJECTPATH')) {
            return;
        }
        define('PROJECTPATH', dirname(__DIR__) . '/..');
        define('APPPATH', PROJECTPATH . '/App');
        spl_autoload_register(function ($class_name) {
            $filename = PROJECTPATH . '/' . str_replace('\\', '/', $class_name) . '.php';
            if (is_file($filename)) {
                include_once $filename;
            }
        });
    }

    private function fmtEnteroCorreo($n)
    {
        return number_format((int) $n, 0, '.', ',');
    }

    private function fmtMonedaCorreo($n)
    {
        return '$' . number_format((float) $n, 2, '.', ',');
    }

    public function run()
    {
        global $argv;

        $this->registrarAutoloadApp();

        $fechaCierre = date('Y-m-d', strtotime('yesterday'));
        if (PHP_SAPI === 'cli' && isset($argv[1]) && trim((string) $argv[1]) !== '') {
            $f = trim((string) $argv[1]);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $f) && strtotime($f) !== false) {
                $fechaCierre = $f;
            } else {
                echo "Fecha inválida (use YYYY-MM-DD). Se usa ayer.\n";
            }
        }
        $fechaFmt = date('d/m/Y', strtotime($fechaCierre));
        $fechaDevengoFmt = date('d/m/Y', strtotime($fechaCierre . ' +1 day'));

        $iniPath = dirname(__DIR__) . '/../App/config/configuracion.ini';
        $ini = @parse_ini_file($iniPath, true);
        $seccionCierre = isset($ini['cierre_dia']) && is_array($ini['cierre_dia']) ? $ini['cierre_dia'] : [];
        $correosRaw = isset($seccionCierre['CORREOS_DESARROLLO']) ? trim((string) $seccionCierre['CORREOS_DESARROLLO']) : '';
        $destinatarios = array_values(array_filter(array_map('trim', explode(',', $correosRaw))));
        if (empty($destinatarios)) {
            echo "Error: configure CORREOS_DESARROLLO en [cierre_dia] del configuracion.ini\n";
            return;
        }

        $asunto = '[PRUEBA] Resumen de cierre de día - ' . $fechaFmt;

        try {
            $resumen = CierreDiaResumenPresenter::construir($fechaCierre);
        } catch (\Throwable $e) {
            echo 'Error al obtener resumen de cierre: ' . $e->getMessage() . "\n";
            return;
        }

        $proc = $resumen['proceso'] ?? [];
        $pag = $resumen['pagos'] ?? [];
        $conc = $resumen['conciliacion'] ?? [];
        $dev = $resumen['devengo'] ?? [];

        $procUsuario = (string) ($proc['usuario'] ?? '-');
        $procInicio = (string) ($proc['inicio'] ?? '-');
        $procFin = (string) ($proc['fin'] ?? '-');
        $procRegistros = $this->fmtEnteroCorreo($proc['registros'] ?? 0);
        $procEstatus = (string) ($proc['estatus'] ?? '-');

        $pagTotalN = $this->fmtEnteroCorreo(($pag['total'] ?? [])['registros'] ?? 0);
        $pagTotalM = $this->fmtMonedaCorreo(($pag['total'] ?? [])['importe'] ?? 0);
        $pagPendN = $this->fmtEnteroCorreo(($pag['pendientes'] ?? [])['registros'] ?? 0);
        $pagPendM = $this->fmtMonedaCorreo(($pag['pendientes'] ?? [])['importe'] ?? 0);
        $pagAplN = $this->fmtEnteroCorreo(($pag['aplicados'] ?? [])['registros'] ?? 0);
        $pagAplM = $this->fmtMonedaCorreo(($pag['aplicados'] ?? [])['importe'] ?? 0);
        $pagPagosN = $this->fmtEnteroCorreo(($pag['pagos'] ?? [])['registros'] ?? 0);
        $pagPagosM = $this->fmtMonedaCorreo(($pag['pagos'] ?? [])['importe'] ?? 0);
        $pagGarN = $this->fmtEnteroCorreo(($pag['garantias'] ?? [])['registros'] ?? 0);
        $pagGarM = $this->fmtMonedaCorreo(($pag['garantias'] ?? [])['importe'] ?? 0);
        $pagIncN = $this->fmtEnteroCorreo(($pag['incidencias'] ?? [])['registros'] ?? 0);
        $pagIncM = $this->fmtMonedaCorreo(($pag['incidencias'] ?? [])['importe'] ?? 0);

        $concPendN = $this->fmtEnteroCorreo(($conc['pendientes'] ?? [])['registros'] ?? 0);
        $concPendM = $this->fmtMonedaCorreo(($conc['pendientes'] ?? [])['importe'] ?? 0);
        $concConcN = $this->fmtEnteroCorreo(($conc['conciliados'] ?? [])['registros'] ?? 0);
        $concConcM = $this->fmtMonedaCorreo(($conc['conciliados'] ?? [])['importe'] ?? 0);

        $devCreditosN = $this->fmtEnteroCorreo($dev['creditos'] ?? 0);
        $devCreditosM = $this->fmtMonedaCorreo($dev['monto'] ?? 0);

        $datos = JobsDao::getUsuraio();
        if (!$datos['success']) {
            echo $datos['mensaje'] . "\n";
        }

        $cuerpo = <<<HTML
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
                            Resumen de cierre del día {$fechaFmt}
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
                                {$procUsuario}
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
                                {$procInicio}
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
                                {$procFin}
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
                                {$procRegistros}
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
                                {$procEstatus}
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
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                {$pagTotalN}
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                {$pagTotalM}
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
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                {$pagPendN}
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                {$pagPendM}
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
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                {$pagAplN}
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                {$pagAplM}
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
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                {$pagPagosN}
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                {$pagPagosM}
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
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                {$pagGarN}
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                {$pagGarM}
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
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                {$pagIncN}
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                {$pagIncM}
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
                                Pendientes
                            </div>
                            <div
                                style="
                                    font-size: 28px;
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                {$concPendN}
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                {$concPendM}
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
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                {$concConcN}
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                {$concConcM}
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
                            Devengo para el día {$fechaDevengoFmt}
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
                                    line-height: 1;
                                    color: #0f172a;
                                    font-weight: 800;
                                    letter-spacing: -0.02em;
                                "
                            >
                                {$devCreditosN}
                            </div>
                            <div
                                style="
                                    font-size: 12px;
                                    color: #334155;
                                    margin-top: 8px;
                                    font-weight: 600;
                                "
                            >
                                {$devCreditosM}
                            </div>
                        </div>
                    </td>
                    <td style="width: 33.33%; padding: 0 3px 12px 3px"></td>
                    <td style="width: 33.33%; padding: 0 0 12px 6px"></td>
                </tr>
            </table>
        HTML;

        $mensaje = Mensajero::Notificaciones($cuerpo);

        if (Mensajero::EnviarCorreo($destinatarios, $asunto, $mensaje, [], false)) {
            echo "Correo enviado a " . implode(', ', $destinatarios) . " (" . $fechaFmt . ")\n";
        } else {
            echo "Error al enviar correo\n";
        }
    }
}

$job = new JobsPrueba();
$job->run();
