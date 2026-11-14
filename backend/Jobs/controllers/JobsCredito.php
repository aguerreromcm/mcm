<?php

namespace Jobs\controllers;

include_once dirname(__DIR__) . '/../Core/Job.php';
include_once dirname(__DIR__) . '/models/JobsCredito.php';
include_once dirname(__DIR__) . '/../libs/PHPMailer/Mensajero.php';
include_once dirname(__DIR__) . '/../libs/PhpSpreadsheet/PhpSpreadsheet.php';
include_once dirname(__DIR__) . '/../App/services/CierreDiaJobLock.php';
include_once dirname(__DIR__) . '/../App/services/CierreDiaCorreoPresenter.php';

use Core\Job;
use Jobs\models\JobsCredito as JobsDao;
use Mensajero;
use App\services\CierreDiaJobLock;
use App\services\CierreDiaCorreoPresenter;

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
                    <li>🔸<b>Crédito:</b> {$credito['CREDITO']}</li>
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

    public function CierreDia($fecha, $usuario, $regenerar = 0)
    {
        set_time_limit(0);
        self::SaveLog('Inicio');
        if (empty($fecha) || empty($usuario))
            return self::SaveLog('Finalizado con error: Fecha y usuario son requeridos');

        if (!CierreDiaJobLock::adquirirJob()) {
            $motivo = CierreDiaJobLock::ultimoError();
            return self::SaveLog('Finalizado sin ejecutar el SP: ' . ($motivo !== '' ? $motivo : 'ya hay un Job de cierre de día en ejecución'));
        }

        $mensajeRes = null;
        $datos = [
            'fecha' => $fecha,
            'usuario' => $usuario,
            'regenerar' => $regenerar ? 1 : 0,
        ];

        try {
            $resultado = JobsDao::CierreDia($datos);

            if (isset($resultado['datos']) && isset($resultado['datos']['MENSAJE'])) {
                $texto = trim($resultado['datos']['MENSAJE']);
                $lineas = preg_split('/\r\n|\r|\n/', $texto);
                $mensajeRes = end($lineas);
                $resultado['datos']['MENSAJE'] = $mensajeRes;
            }

            $mensaje = "Cierre de día concluido: fecha $fecha - usuario $usuario";
            if (!$resultado['success']) {
                $error = isset($resultado['error']) ? $resultado['error'] : $mensajeRes;
                $mensaje = "Finalizado con error: $error";
            }

            if (!empty($resultado['datos'])) self::SaveLog($this->EnviarCorreoResultado($resultado['datos']));

            self::SaveLog($mensaje);
        } catch (\Throwable $e) {
            self::SaveLog('Finalizado con error: ' . $e->getMessage() . ':' . join('<br>', $e->getTrace()));
        } finally {
            CierreDiaJobLock::liberarJob();
        }
    }

    /**
     * Envía el correo de un resultado de cierre ya terminado.
     */
    public function EnviarCorreoResultado(array $datos): string
    {
        $exito = (int) ($datos['EXITO'] ?? 0) === 1;
        $envio = $this->destinosCorreoCierreDia($exito);
        if (empty($envio['destinatarios'])) return 'No hay destinatarios configurados para el correo de cierre.';
    
        try {
            $fecha = \DateTime::createFromFormat('d/m/Y', (string) ($datos['FECHA_CALCULO'] ?? ''));
            if (!$fecha) return 'El resultado del cierre no tiene una fecha válida.';

            $fechaFmt = $fecha->format('d/m/Y');
            
            $enviado = Mensajero::EnviarCorreo(
                $envio['destinatarios'],
                "Cierre del día $fechaFmt",
                Mensajero::Notificaciones(CierreDiaCorreoPresenter::plantilla($datos)),
                [],
                $envio['copiaHistorico']
            );
            
            if (!$enviado) return 'El correo de resumen no se pudo enviar (SMTP).';

            $resCorreo = JobsDao::CorreoCierreDia($datos);
            if (empty($resCorreo['success'])) return 'El correo se envió, pero no se pudo actualizar su estatus.' . $resCorreo['error'] ?? null;

            return 'Correo de resumen enviado correctamente.';
        } catch (\Throwable $e) {
            return 'Error al enviar correo:' . $e->getMessage();
        }
    }

    /**
     * Destinatarios del correo de cierre.
     * Con CIERRE_DIA_SOLO_FLUJO: solo CORREOS_DESARROLLO, sin copia a SMTP_USER.
     * En producción: grupos de aplicación 5 (éxito) / 6 (error) y copia histórica.
     *
     * @param bool $exito
     * @return array{destinatarios: string[], copiaHistorico: bool}
     */
    private function destinosCorreoCierreDia($exito)
    {
        $ini = @parse_ini_file(dirname(__DIR__) . '/../App/config/configuracion.ini', true);
        $cfg = isset($ini['cierre_dia']) && is_array($ini['cierre_dia']) ? $ini['cierre_dia'] : [];
        $val = isset($cfg['CIERRE_DIA_SOLO_FLUJO']) ? trim((string) $cfg['CIERRE_DIA_SOLO_FLUJO']) : '';
        $soloFlujo = $val !== '' && (filter_var($val, FILTER_VALIDATE_BOOLEAN) || strtolower($val) === 'true' || $val === '1');

        if ($soloFlujo) {
            $raw = isset($cfg['CORREOS_DESARROLLO']) ? trim((string) $cfg['CORREOS_DESARROLLO']) : '';
            $dest = array_values(array_filter(array_unique(array_map('trim', explode(',', $raw)))));
            if (empty($dest)) {
                self::SaveLog('CIERRE_DIA_SOLO_FLUJO activo y CORREOS_DESARROLLO vacío: no se envía correo.');
            } else {
                self::SaveLog('Correo solo desarrollo: ' . implode(', ', $dest));
            }
            return ['destinatarios' => $dest, 'copiaHistorico' => false];
        }

        $appId = $exito ? 5 : 6;
        $dest = $this->GetDestinatarios(JobsDao::GetDestinatarios_Aplicacion($appId));
        return ['destinatarios' => $dest, 'copiaHistorico' => true];
    }

    public function RenviarCorreo($fecha, $proceso)
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha))
            return self::SaveLog("Debe especificar una fecha válida (YYYY-MM-DD).");

        if (empty($proceso)) return self::SaveLog("Debe especificar un ID de proceso.");

        $datos = JobsDao::ResultadoCierreDia($fecha, $proceso);
        if (!$datos['success'])
            return self::SaveLog($datos['mensaje']);
        
        $datos = $datos['datos'];
        $texto = trim($datos['MENSAJE']);
        $lineas = preg_split('/\r\n|\r|\n/', $texto);
        $mensajeRes = end($lineas);
        $datos['MENSAJE'] = $mensajeRes;
        self::SaveLog(self::EnviarCorreoResultado($datos));
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
            $jobs->CierreDia($argv[2] ?? '', $argv[3] ?? '', isset($argv[4]) ? (int) $argv[4] : 0);
            break;
        case 'RenviarCorreo':
            $jobs->RenviarCorreo($argv[2] ?? '', $argv[3] ?? '');
            break;
        case 'help':
            echo 'JobCheques: Actualiza los cheques de los créditos autorizados\n';
            echo 'SolicitudesFinalizadas: Evalúa el comentario final de la solicitud y la procesa para concluir con la solicitud\n';
            echo 'RepDiasAtraso [ruta]: Genera Rep_Días_atraso_YYYYMMDD.csv; ruta opcional (por defecto Desktop del usuario)\n';
            echo 'CierreDia [fecha] [usuario] [regenerar]: Ejecuta el cierre del día (regenerar opcional 0|1)\n';
            echo "RenviarCorreo [fecha YYYY-MM-DD] [id_proceso]: Renvía el correo de conclusión de cierre\n";
            break;
        default:
            echo 'No se encontró el job solicitado.\nEjecute "php JobsAhorro.php help" para ver los jobs disponibles.\n';
            break;
    }
} else echo 'Debe especificar el job a ejecutar.\nEjecute "php JobsAhorro.php help" para ver los jobs disponibles.\n';
