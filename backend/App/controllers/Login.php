<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");
require_once dirname(__DIR__) . '../../libs/BrowserDetection/BrowserDetection.php';

use \Core\View;
use \Core\MasterDom;
use \App\models\Login as LoginDao;

class Login
{
    function __construct() {}

    public function index()
    {
        if (!$this->validaNavegador()) {
            echo $this->getErrorNavegador();
            return;
        }

        $extraHeader = <<<HTML
            <link rel="stylesheet" href="/css/bootstrap/bootstrap.css">
            <link rel="stylesheet" href="/css/bootstrap/datatables.bootstrap.css">
            <link rel="stylesheet" href="/css/contenido/custom.min.css">
            <link rel="stylesheet" href="/css/validate/screen.css">
        HTML;

        $extraFooter = <<<HTML
            <script src="/js/jquery.min.js"></script>
            <script src="/js/validate/jquery.validate.js"></script>
            <script>
                document.getElementById("usuario").focus()
                
                function enviar_formulario() {
                    $("#btnEntrar").click()
                }

                function mayus(e) {
                    e.value = e.value.toUpperCase()
                }

                $(document).ready(function () {
                    $.validator.addMethod(
                        "checkUserName",
                        function (value, element) {
                            var response = false
                            $.ajax({
                                type: "POST",
                                async: false,
                                url: "/Login/isUserValidate",
                                data: { usuario: $("#usuario").val() },
                                success: function (data) {
                                    if (data == "true") {
                                        $("#availability").html(
                                            '<span class="text-success glyphicon glyphicon-ok"></span>'
                                        )
                                        $("#btnEntrar").attr("disabled", false)
                                        response = true
                                    } else {
                                        $("#availability").html(
                                            '<span class="text-danger glyphicon glyphicon-remove"></span>'
                                        )
                                        $("#btnEntrar").attr("disabled", true)
                                    }
                                }
                            })

                            return response
                        },
                        "El usuario no es correcto, o no tiene acceso al sistema, verifique. "
                    )

                    $("#login").validate({
                        rules: {
                            usuario: {
                                required: true,
                                checkUserName: true
                            },
                            password: {
                                required: true
                            }
                        },
                        messages: {
                            usuario: {
                                required: "Este campo es requerido"
                            },
                            password: {
                                required: "Este campo es requerido"
                            }
                        }
                    })

                    $("#btnEntrar").click(function () {
                        $.ajax({
                            type: "POST",
                            url: "/Login/verificarUsuario",
                            data: $("#login").serialize(),
                            success: function (response) {
                                if (response != "") {
                                    var usuario = jQuery.parseJSON(response)
                                    if (usuario.nombre != "") {
                                        $("#login").append(
                                            '<input type="hidden" name="autentication" id="autentication" value="OK"/>'
                                        )
                                        $("#login").append(
                                            '<input type="hidden" name="nombre" id="nombre" value="' +
                                                usuario.nombre +
                                                '"/>'
                                        )
                                        $("#login").submit()
                                    } else {
                                        swal(
                                            "Error de autenticación",
                                            "El usuario o contraseña son incorrectos, consulte al administrador",
                                            "error"
                                        )
                                    }
                                } else {
                                    swal(
                                        "Error de autenticación",
                                        "El usuario o contraseña son incorrectos, consulte al administrador",
                                        "error"
                                    )
                                }
                            }
                        })
                    })

                    const ua = navigator.userAgent
                    const test = regexp => regexp.test(ua)

                    if (!test(/edg/i) && !test(/chrome|chromium|crios/i) || (test(/opr\//i) || !!window.opr))
                        $("#avisoNavegador").html(
                            '<div class="alert alert-danger" role="alert">El navegador que estás utilizando no ha sido probado con el sistema de MCM, le recomendamos usar Google Chrome o Microsoft Edge en su versión más reciente.<br>Si desea continuar con este navegador, es posible que algunas funciones no estén disponibles.</div>'
                        )
                })
            </script>
        HTML;

        View::set('header', $extraHeader);
        View::set('footer', $extraFooter);
        View::render("Login/login");
    }

    public function isUserValidate()
    {
        echo (count(LoginDao::getUser($_POST['usuario'])) >= 1) ? 'true' : 'false';
    }

    public function verificarUsuario()
    {
        $usuario = new \stdClass();
        $usuario->_usuario = MasterDom::getData("usuario");
        $usuario->_password = MasterDom::getData("password");
        $user = LoginDao::getById($usuario);

        if (count($user) >= 1) {
            $user['NOMBRE'] = mb_convert_encoding($user['NOMBRE'], 'UTF-8');
            echo json_encode($user);
        }
    }

    public function crearSession()
    {
        $usuario = new \stdClass();
        $usuario->_usuario = MasterDom::getData("usuario");
        $usuario->_password = MasterDom::getData("password");
        $user = LoginDao::getById($usuario);

        if ($user[1]['PERMISO'] == '') {
            $permiso = 0;
            $cdgco_ahorro = 'NULL';
            $inicio_ahorro = 'NULL';
            $fin_ahorro = 'NULL';
        } else {
            $permiso = $user[1]['PERMISO'];
            $cdgco_ahorro = $user[1]['CDGCO_AHORRO'];
            $inicio_ahorro = $user[1]['HORA_APERTURA'];
            $fin_ahorro = $user[1]['HORA_CIERRE'];
        }

        session_start();
        $_SESSION['usuario'] = $user[0]['CODIGO'];
        $_SESSION['nombre'] = $user[0]['NOMBRE'];
        $_SESSION['puesto'] = $user[0]['PUESTO'];
        $_SESSION['cdgco'] = $user[0]['CDGCO'];
        $_SESSION['perfil'] = $user[0]['PERFIL'];
        $_SESSION['ahorro'] = $permiso;
        $_SESSION['cdgco_ahorro'] = $cdgco_ahorro;
        $_SESSION['inicio'] = $inicio_ahorro;
        $_SESSION['fin'] = $fin_ahorro;

        header("location: /Principal/");
    }

    public function cerrarSession()
    {
        unset($_SESSION);
        session_unset();
        session_destroy();
        header("Location: /Login/");
    }

    public function validaNavegador()
    {
        $navegadores = [
            'Chrome' => 120,
            'Edge' => 120,
            // 'Firefox' => 130,
            // 'Safari' => 140,
            // 'Opera' => 105
        ];

        $b = new \foroco\BrowserDetection();
        $navegador = $b->getBrowser($_SERVER['HTTP_USER_AGENT']);

        // if ($navegador['browser_name'] === 'Internet Explorer') return false;
        if (!$navegadores[$navegador['browser_name']] || $navegador['browser_version'] < $navegadores[$navegador['browser_name']]) return false;

        return true;
    }

    public function getErrorNavegador()
    {
        return <<<HTML
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Navegador no compatible</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f2f2f2;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                    }
                    .container {
                        background-color: #ffffff;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        text-align: center;
                    }
                    .container h1 {
                        color: #ff0000;
                    }
                    .container p {
                        margin: 10px 0;
                    }
                    .container ul {
                        list-style: none;
                        padding: 0;
                    }
                    .container li {
                        margin: 10px 0;
                        display: flex;
                        align-items: center;
                    }
                    .container img {
                        width: 24px;
                        height: 24px;
                        margin-right: 10px;
                    }
                    .container a {
                        color: #007bff;
                        text-decoration: none;
                    }
                    .navegadores {
                        display: flex;
                        justify-content: center;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Navegador no compatible</h1>
                    <p>El navegador que estás utilizando no es compatible con el sistema de MCM</p>
                    <p>Le recomendamos usar uno de los siguientes navegadores:</p>
                    <div class="navegadores">
                        <ul>
                            <li>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/8/87/Google_Chrome_icon_%282011%29.png" alt="Google Chrome">
                                <a href="https://www.google.com/chrome/">Google Chrome</a>
                            </li>
                            <li>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/9/98/Microsoft_Edge_logo_%282019%29.svg" alt="Microsoft Edge">
                                <a href="https://www.microsoft.com/edge">Microsoft Edge</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </body>
            </html>
        HTML;
    }
}
