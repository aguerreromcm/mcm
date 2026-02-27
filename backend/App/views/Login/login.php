<?= $header; ?>

<?php
$anio = date('Y');
?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/img/logo_ico.png">
    <title>Login | MCM </title>

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css" rel="stylesheet">
</head>

<div class="login_wrapper" style="margin-top: 0%;">
    <div class="animate form login_form">
        <section class="login_content" style="padding: 100px 0 0;">
            <div style="text-align: center;">
                <img src="/img/logo.png" alt="Login" width="350" height="260">
            </div>
            <br>
            <form id="login" action="/Login/crearSession" method="POST" class="form-horizontal" name="login">
                <h1 style="color: #C43136; font-size: 30px; text-align: center;">Iniciar Sesión</h1>
                <div class="col-md-1 col-sm-1 col-xs-1"><span id="availability"> </span></div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <input type="text" name="usuario" id="usuario" class="form-control col-md-6 col-xs-12" placeholder="Usuario" required="" onkeyup="mayus(this);">
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <input type="password" name="password" id="password" class="form-control col-md-5 col-xs-12" placeholder="Contraseña" required="" onkeypress="if (event.keyCode == 13) enviar_formulario()">
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <button type="button" id="btnEntrar" class="btn btn-warning col-md-4 col-sm-4 col-xs-4 pull-right" style="background: #C43136; border-color: #C43136">Entrar <i class="glyphicon glyphicon-log-in"></i></button>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="separator">
                    <div class="clearfix"></div>
                    <br />
                    <div>
                        <h1><i class="fa fa-paw"></i>Más con Menos</h1>
                        <p>© <?= $anio ?> - Al ingresar al sistema de Más con Menos, los usuarios están de acuerdo con las políticas de privacidad y términos de uso establecidos por la empresa.</p>
                    </div>
                </div>
            </form>
            <div id="avisoNavegador"></div>
        </section>
    </div>
</div>

<?= $footer; ?>