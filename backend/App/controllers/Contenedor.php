<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\Controller;

require_once dirname(__DIR__) . '../../libs/mpdf/mpdf.php';
require_once dirname(__DIR__) . '../../libs/PhpSpreadsheet/PhpSpreadsheet.php';

class Contenedor extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUsuario()
    {
        return $this->__usuario;
    }

    public function header($extra = '')
    {
        $usuario = $this->__usuario;
        $nombre = $this->__nombre;
        $sucursal = $this->__cdgco;
        $perfil = $this->__perfil;
        $permiso_ahorro = $this->__ahorro;

        $config = \Core\App::getConfig();
        $mostrarHerramientas = (isset($config['HERRAMIENTAS_CLAVE']) && trim((string) $config['HERRAMIENTAS_CLAVE']) === 'accesoh');
        $menu = (new \App\components\Menu($perfil, $usuario, $permiso_ahorro, $mostrarHerramientas))->mostrar();

        $header = <<<HTML
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta http-equiv="Expires" content="0">
                <meta http-equiv="Last-Modified" content="0">
                <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
                <meta http-equiv="Pragma" content="no-cache">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta charset="utf-8">
                
                <link rel="shortcut icon" href="/img/logo_ico.png">
                <link rel="stylesheet" type="text/css" href="/css/nprogress.css">
                <link rel="stylesheet" type="text/css" href="/css/tabla/sb-admin-2.css">
                <link rel="stylesheet" type="text/css" href="/css/bootstrap/datatables.bootstrap.css">
                <link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap.css">
                <link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap-switch.css">
                <link rel="stylesheet" type="text/css" href="/css/validate/screen.css">
                <link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css">
                <link rel="stylesheet" type="text/css" href="/css/green.css">
                <link rel="stylesheet" type="text/css" href="/css/custom.min.css">
                <link rel="stylesheet" type="text/css" href="/css/custom_menu.css">
                $extra 
            </head>
            <body class="nav-md">
                <div class="container body" >
                    <div class="main_container" style="background: #ffffff">
                        <div class="col-md-3 left_col">
                            <div class="left_col scroll-view">
                                <div class="navbar nav_title" style="border: 0;"> 
                                    <a href="/Principal/" class="site_title" style="display: flex; align-items: center; justify-content: center; padding: 0; margin: 0;">
                                        <img src="/img/logo_ico.png" alt="Inicio" width="50px" id="ico_home" style="display: none;">
                                        <img src="/img/logo_nombre.png" alt="Inicio" width="210px" id="img_home">
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                                <div class="profile clearfix">
                                    <div class="profile_pic">
                                        <img src="/img/profile_default.png" alt="..." class="img-circle profile_img">
                                    </div>
                                    <div class="profile_info">
                                        <span><b>USUARIO: </b>{$usuario}</span>
                                        <br>
                                        <span><b>PERFIL: </b><span class="fa fa-key"></span>{$perfil}</span>
                                    </div>
                                </div>
                                {$menu}
                            </div>
                        </div>
                        <div class="top_nav">
                            <div class="nav_menu">
                                <nav>
                                    <div class="nav toggle">
                                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                                    </div>
                                    <ul class="nav navbar-nav navbar-right">
                                        <li class="">
                                            <a href="" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span class=" fa fa-user"></span> {$nombre}
                                                <span class=" fa fa-angle-down"></span>
                                            </a>
                                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                                <li><a href="/Login/cerrarSession"><i class="fa fa-sign-out pull-right"></i>Cerrar Sesión</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
        HTML;

        return $header . $menu;
    }

    public function footer($extra = '')
    {
        $footer = <<<HTML
                </div>
                <script src="/js/moment/moment.min.js"></script>
                <script src="/js/sweetalert.min.js"></script>
                <script src="/js/jquery.min.js"></script>
                <script src="/js/bootstrap.min.js"></script>
                <script src="/js/bootstrap/bootstrap-switch.js"></script>
                <script src="/js/nprogress.js"></script>
                <script src="/js/custom.min.js"></script>
                <script src="/js/validate/jquery.validate.js"></script>
                <script src="/js/login.js"></script>
                <script src="/js/tabla/jquery.dataTables.min.js"></script>
                <script src="/js/tabla/dataTables.bootstrap.min.js"></script>
                <script src="/js/tabla/jquery.tablesorter.js"></script>
                <script src="/js/dataTables.buttons.min.js" ></script>
                <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" ></script>
                <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js" ></script>
                <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js" ></script>
                <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js" ></script>
                $extra
            </body>
        </html>
        HTML;
        return $footer;
    }

    public function ValidaPermiso($permisos)
    {
        return in_array($this->__perfil, $permisos) || in_array($this->__usuario, $permisos);
    }
}
