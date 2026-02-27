<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mensajero
{
    static private $SMTP_SERVER = '';
    static private $SMTP_PORT = 0;
    static private $SMTP_USER = '';
    static private $SMTP_PASS = '';
    static private $SMTP_FROM = '';

    /**
     * Configura los parámetros del servidor SMTP para el envío de correos electrónicos.
     *
     * @param string|null $server Dirección del servidor SMTP. Si es null, se toma del archivo de configuración.
     * @param int|null $port Puerto del servidor SMTP. Si es null, se toma del archivo de configuración.
     * @param string|null $user Nombre de usuario para autenticarse en el servidor SMTP. Si es null, se toma del archivo de configuración.
     * @param string|null $pass Contraseña para autenticarse en el servidor SMTP. Si es null, se toma del archivo de configuración.
     * @param string|null $from Dirección de correo electrónico del remitente. Si es null, se toma del archivo de configuración.
     * @return void
     */
    public static function configura($server = null, $port = null, $user = null, $pass = null, $from = null)
    {
        $config = (class_exists(\Core\App::class) && method_exists(\Core\App::class, 'getConfig'))
            ? \Core\App::getConfig()
            : parse_ini_file(dirname(__DIR__) . '/../App/config/configuracion.ini');
        self::$SMTP_SERVER = $server ?? $config['SMTP_SERVER'];
        self::$SMTP_PORT = $port ?? $config['SMTP_PORT'];
        self::$SMTP_USER = $user ?? $config['SMTP_USER'];
        self::$SMTP_PASS = $pass ?? $config['SMTP_PASS'];
        self::$SMTP_FROM = $from ?? $config['SMTP_FROM'];
    }

    /**
     * Envía un correo electrónico utilizando PHPMailer.
     *
     * @param array|string $destinatarios Lista de destinatarios del correo. Puede ser un array o una cadena con un solo destinatario.
     * @param string $asunto Asunto del correo.
     * @param string $mensaje Cuerpo del mensaje del correo. Puede contener HTML.
     * @param array|string $adjuntos (Opcional) Lista de archivos adjuntos. Puede ser un array o una cadena con un solo archivo.
     * @return bool Devuelve true si el correo se envió correctamente, false en caso contrario.
     */
    public static function EnviarCorreo($destinatarios, $asunto, $mensaje, $adjuntos = [])
    {

        $mensajero = new PHPMailer(true);
        self::configura();

        try {
            $mensajero->setLanguage('es', __DIR__ . '\vendor\phpmailer\phpmailer\language');
            $mensajero->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mensajero->isSMTP();
            $mensajero->Host = self::$SMTP_SERVER;
            $mensajero->Port = self::$SMTP_PORT;
            $mensajero->SMTPAuth = true;
            $mensajero->Username = self::$SMTP_USER;
            $mensajero->Password = self::$SMTP_PASS;
            $mensajero->isHTML(true);
            $mensajero->Subject = $asunto;
            $mensajero->Body = $mensaje;
            $mensajero->AltBody = strip_tags($mensaje);
            $mensajero->CharSet = 'UTF-8';
            $mensajero->setFrom(self::$SMTP_USER, self::$SMTP_FROM);
            $mensajero->addCustomHeader('Return-Path', self::$SMTP_USER);

            $adjuntos = is_array($adjuntos) ? $adjuntos : [$adjuntos];
            if (count($adjuntos) > 0) {
                foreach ($adjuntos as $adjunto) {
                    $mensajero->addAttachment($adjunto);
                }
            }

            $destinatarios = is_array($destinatarios) ? $destinatarios : [$destinatarios];
            foreach ($destinatarios as $destinatario) {
                $mensajero->clearAddresses();
                $mensajero->addAddress($destinatario);
                $mensajero->send();
            }

            // Se crea el JSON
            $destInfo = __DIR__ . '/destinatarios.json';
            file_put_contents($destInfo, json_encode($destinatarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Se envia una copia a la cuenta de SMTP para historico
            $mensajero->clearAddresses();
            $mensajero->addAddress(self::$SMTP_USER);
            $mensajero->addAttachment($destInfo);
            $mensajero->send();

            // Se eliminan los archivos temporales
            unlink($destInfo);

            return true;
        } catch (Exception $e) {
            error_log("Error al enviar correo: {$e->getMessage()}");
            // mostrar la pila de errores
            error_log($e->getTraceAsString());
            return false;
        }
    }

    public static function Notificaciones($body)
    {
        return <<<HTML
            <!DOCTYPE html>
            <html lang="es">
                <head>
                    <meta charset="UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                </head>
                <body style="margin: 0; padding: 0 10px; font-family: Arial, sans-serif; background-color: #f4f4f4">
                    <div
                        style="
                            max-width: 600px;
                            margin: 20px auto;
                            background-color: #ffffff;
                            border-radius: 10px;
                            overflow: hidden;
                            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
                        "
                    >
                        <!-- Encabezado -->
                        <div style="background-color: #494949; color: #fff; height: 60px">
                            <table style="width: 95%; height: 100%; border-spacing: 0; margin: auto">
                                <tr>
                                    <td style="padding: 0">
                                        <img
                                            src="https://18.117.29.228/img/logo_ico.png"
                                            alt="Logo"
                                            style="height: 55px; display: block"
                                        />
                                    </td>
                                    <td style="padding: 0">
                                        <h1 style="margin: 0; text-align: right">Notificaciones</h1>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Cuerpo -->
                        <div style="padding: 15px; color: #333333">
                            {$body}
                        </div>

                        <!-- Pie de página -->
                        <div
                            style="
                                background-color: #f4f4f4;
                                height: 60px;
                                text-align: center;
                                font-size: 10px;
                                color: #555555;
                                border-top: 1px solid #ddd;
                            "
                        >
                            <p>Este correo ha sido generado automáticamente, no responda a este mensaje.</p>
                            <p>Si usted no es el destinatario previsto, favor de reenviarlo a soporte.</p>
                        </div>
                    </div>
                </body>
            </html>
        HTML;
    }
}
