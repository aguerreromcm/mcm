<?php

namespace Jobs\controllers;

include_once dirname(__DIR__) . "\..\Core\Job.php";
include_once dirname(__DIR__) . "\..\App\libs\PHPMailer\Mensajero.php";
include_once dirname(__DIR__) . "\models\JobPruebaMail.php";


use Core\Job;
use Jobs\models\JobPruebaMail as JobsDao;
use Mensajero;

class JobsPrueba extends Job
{
    public function __construct()
    {
        parent::__construct("Job_pruba_mail");
    }

    public function run()
    {
        $datos = JobsDao::getUsuraio();
        if (!$datos['success']) {
            echo $datos['mensaje'];
            return;
        }

        $asunto = "Prueba de correo";
        $destinatarios = [
            "alberto.s@2gkapital.com.mx",
            "albertosoto.lab@gmail.com"
        ];
        $mensaje = <<<HTML
            <html>
                <head>
                    <title>Prueba de correo</title>
                    <meta charset="utf-8" />
                </head>
                <body>
                    <table
                        style="
                            background-color: #f0f0f0;
                            padding: 10px;
                            margin: auto;
                            width: 300px;
                            text-align: center;
                            border-radius: 10px;
                        "
                    >
                        <tr>
                            <td style="text-align: center">
                                <img
                                    src="https://18.117.29.228/img/logo.svg"
                                    alt="Empresa"
                                    style="width: 150px"
                                />
                                <h1 style="color: #333">Prueba de correo</h1>
                                <p style="color: #666">Esto es una prueba de correo</p>
                                <p style="color: #666">{$datos['mensaje']}</p>
                                <p style="color: #333"><b>{$datos['datos']['PATRON']}</b></p>
                            </td>
                        </tr>
                    </table>
                </body>
            </html>
        HTML;

        if (Mensajero::EnviarCorreo($destinatarios, $asunto, $mensaje)) echo "Correo enviado";
        else echo "Error al enviar correo";
    }
}

$job = new JobsPrueba();
$job->run();
