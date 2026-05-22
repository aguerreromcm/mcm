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
        $config = parse_ini_file(dirname(__DIR__) . '/../app/config/configuracion.ini');
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
     * @param bool $enviarCopiaHistorico Si es false, no se envía copia al buzón SMTP_USER (uso típico: cierre en modo desarrollo).
     * @return bool Devuelve true si el correo se envió correctamente, false en caso contrario.
     */
    public static function EnviarCorreo($destinatarios, $asunto, $mensaje, $adjuntos = [], $enviarCopiaHistorico = true)
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

            if ($enviarCopiaHistorico) {
                // Se crea el JSON
                $destInfo = __DIR__ . '/destinatarios.json';
                file_put_contents($destInfo, json_encode($destinatarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                // Se envia una copia a la cuenta de SMTP para historico
                $mensajero->clearAddresses();
                $mensajero->addAddress(self::$SMTP_USER);
                $mensajero->addAttachment($destInfo);
                $mensajero->send();

                unlink($destInfo);
            }

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
                                        <div style="height: 55px; display: block">
                                            <svg
                                                xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape"
                                                xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
                                                xmlns="http://www.w3.org/2000/svg"
                                                xmlns:svg="http://www.w3.org/2000/svg"
                                                version="1.1"
                                                id="Layer_1"
                                                x="0px"
                                                y="0px"
                                                width="60"
                                                viewBox="-73 -25 73 61"
                                                enable-background="new -73 -25 300 100"
                                                xml:space="preserve"
                                                sodipodi:docname="logo_ico.svg"
                                                inkscape:version="1.4.3 (0d15f75, 2025-12-25)"
                                            >
                                                <defs id="defs5" />
                                                <sodipodi:namedview
                                                    id="namedview5"
                                                    pagecolor="#ffffff"
                                                    bordercolor="#000000"
                                                    borderopacity="0.25"
                                                    inkscape:showpageshadow="2"
                                                    inkscape:pageopacity="0.0"
                                                    inkscape:pagecheckerboard="0"
                                                    inkscape:deskcolor="#d1d1d1"
                                                    inkscape:zoom="2.7416667"
                                                    inkscape:cx="86.990881"
                                                    inkscape:cy="-32.279635"
                                                    inkscape:window-width="3440"
                                                    inkscape:window-height="1417"
                                                    inkscape:window-x="-8"
                                                    inkscape:window-y="-8"
                                                    inkscape:window-maximized="1"
                                                    inkscape:current-layer="Layer_1"
                                                />
                                                <g id="g2" transform="translate(-3.9005005,-19.096773)">
                                                    <path
                                                        fill="#1d1d1b"
                                                        d="m -15.707,40.62 c -1.889,10.761 -23.844,18.226 -24.463,10.213 l -5.07,-16.144 c -0.197,-0.09 -0.382,-0.178 -0.572,-0.265 1.693,-2.242 2.698,-5.031 2.698,-8.057 0,-2.907 -0.929,-5.597 -2.503,-7.79 3.393,-0.825 7.25,-1.416 11.06,-1.779 1.849,1.748 4.091,2.88 6.534,2.88 2.565,0 4.905,-1.245 6.805,-3.141 4.52,0.451 5.07,2.119 11.836,8.775 -0.685,0.402 -1.313,0.888 -1.871,1.446 -0.96,0.96 -1.709,2.128 -2.175,3.434 l -9.191,1.582 c -0.919,-2.718 -2.784,-4.579 -4.934,-4.579 -3.063,0 -5.543,3.765 -5.543,8.412 0,4.647 2.482,8.412 5.543,8.412 2.152,0 4.017,-1.859 4.936,-4.579 z"
                                                        id="path1"
                                                    />
                                                    <circle
                                                        fill="#a93439"
                                                        cx="-56.487999"
                                                        cy="26.362"
                                                        r="11.832"
                                                        id="circle1"
                                                    />
                                                    <polygon
                                                        fill="#ffffff"
                                                        points="-58.423,19.075 -54.525,19.075 -54.525,24.442 -49.215,24.442 -49.215,28.34 -54.525,28.34 -54.525,33.651 -58.423,33.651 -58.423,28.34 -63.761,28.34 -63.761,24.442 -58.423,24.442 "
                                                        id="polygon1"
                                                    />
                                                    <path
                                                        fill="#a93439"
                                                        d="m -28.024,-5.003 c -5.599,0 -10.139,4.54 -10.139,10.139 0,5.599 4.54,12.997 10.139,12.997 5.599,0 10.139,-7.398 10.139,-12.997 0,-5.599 -4.539,-10.139 -10.139,-10.139 z"
                                                        id="path2"
                                                    />
                                                    <circle
                                                        fill="#a93439"
                                                        cx="-4.71"
                                                        cy="33.299999"
                                                        r="7.8309999"
                                                        id="circle2"
                                                    />
                                                    <rect
                                                        x="-7.2309999"
                                                        y="31.854"
                                                        fill="#ffffff"
                                                        width="5.3940001"
                                                        height="2.9130001"
                                                        id="rect2"
                                                    />
                                                </g>
                                            </svg>
                                        </div>
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
