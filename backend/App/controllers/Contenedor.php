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
                                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                                    <div class="menu_section" style="overflow: auto;">
        HTML;

        $menu = "";

        $permisos = [];
        if ($permiso_ahorro == '1' || $this->ValidaPermiso($permisos)) {

            $menu .= <<<HTML
            <hr>
            <h3>General WEB AHORRO</h3>
            <ul class="nav side-menu">     
            HTML;
        }


        if ($this->__usuario == 'AMGMM') {
            $menu .= '<li><a href="/Ahorro/CuentaCorriente/"><i class="glyphicon glyphicon-usd"> </i>&nbsp; Mi espacio </a> </li>';
        }

        $permisos = [];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
            <li><a href="/AdminSucursales/SaldosDiarios/"><i class="glyphicon glyphicon-paste"> </i>&nbsp; Admin Sucursales </a> </li>
            </ul>
            HTML;
        }

        $menu .= <<<HTML
        <hr>
        <h3>GENERAL </h3>
        <ul class="nav side-menu">       
        HTML;

        $permisos = ['ADMIN', 'CAJA', 'GTOCA', 'AMOCA', 'OCOF', 'CPAGO', 'ACALL', 'LAYOU', 'TESP', 'MGJC'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
            <li><a><i class="glyphicon glyphicon-usd"> </i>&nbsp; Pagos <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
            HTML;
        }

        // Permisos temporales para pruebas de retiro de ahorro a 'FLHR' y 'PROA'
        $permisos = ['ADMIN', 'MCDP', 'LVGA', 'QARO', 'FLHR', 'PROA'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<li><a href="/Pagos/">Administración Pagos</a></li>';
        }

        $permisos = ['ADMIN', 'FLHR', 'HEDC', 'JULM', 'CRCV', 'LUMM', 'EMGL', 'PEAE', 'MCDP', 'LVGA'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
            <li><a href="/Pagos/CorteEjecutivo/">PRUEBAS Pagos App</a></li> 
            <li><a href="/Pagos/PagosConsultaAPP/">Consultar Pagos App (PRUEBAS)</a></li>
            <li><a href="/Pagos/CorteEjecutivoReimprimir/">Reimprimir Recibos App</a></li> 
            HTML;
        }

        $permisos = ['ADMIN', 'ACALL', 'LAYOU', 'FECR'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<li><a href="/Pagos/Layout/">Layout Contable</a></li>';
        }

        $permisos = ['ADMIN', 'CAJA', 'LGFR', 'PLMV', 'PMAB', 'MGJC', 'AVGA', 'FLCR', 'COCS', 'GOIY', 'DAGC', 'COVG', 'TESP', 'JACJ'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<li><a href="/Pagos/PagosRegistro/">Registro de Pagos Caja</a></li>';
        }

        if ($this->__perfil == 'ACALL') {
            $menu .= '<li><a href="/Pagos/PagosConsultaUsuarios/">Consulta de Pagos Cliente</a></li>';
        }

        $permisos = ['ADMIN', 'CAJA', 'GTOCA', 'AMOCA', 'OCOF', 'CPAGO', 'ACALL'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<li><a href="/Pagos/PagosConsulta/">Consultar Pagos</a></li>';
        }

        $menu .= '</ul></li>';

        // Permisos temporales para pruebas de retiro de ahorro a 'PROA'
        $permisos = ['ADMIN', 'QARO', 'AMOCA', 'MAPH', 'PROA'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
            <li><a><i class="fa fa-money"> </i>&nbsp; Resumen Ahorro <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
            HTML;
        }

        $permisos = ['ADMIN', 'AMOCA', 'VAOY', 'TOOA', 'HTMP', 'JUJG', 'QARO', 'MAPH'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<li><a href="/AhorroSimple/Contrato/">Alta Contrato</a></li>';
        }

        $permisos = ['ADMIN', 'AMOCA', 'VAOY', 'TOOA', 'HTMP', 'JUJG', 'LFGR', 'MGJC', 'MAPH'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<li><a href="/AhorroSimple/EstadoCuenta/">Estado de Cuenta Ahorro</a></li>';
            $menu .= '<li><a href="/AhorroSimple/ValidaAdicional/">Valida Crédito Adicional </a></li>';
            $menu .= '<li><a href="/AhorroSimple/ExepcionesMXT/">Agregar Exepciones MXT</a></li>';
        }

        // Permisos temporales para pruebas de retiro de ahorro a 'PROA'
        $permisos = ['ADMIN', 'LVGA', 'MCDP', 'FLHR', 'PROA'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<li><a href="/AhorroConsulta/">Solicitudes Retiro</a></li>';
            $menu .= '<li><a href="/Ahorro/SolicitudesRetiroAdmin/">Gestión de Retiros</a></li>';
        }

        $menu .= '</ul></li>';

        $persmisos = ['ADMIN', 'GARAN', 'CAMAG', 'ORHM', 'MAPH'];
        if ($this->ValidaPermiso($persmisos)) {
            $menu .= '<ul class="nav side-menu">';
            $menu .= <<<HTML
            <li><a><i class="fa fa-users"> </i>&nbsp; Créditos <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
            HTML;
        }

        $persmisos = ['ADMIN', 'GARAN', 'ORHM', 'MAPH', 'AMOCA'];
        if ($this->ValidaPermiso($persmisos)) {
            $menu .= '<li><a href="/Creditos/ControlGarantias/">Control de Garantías</a></li>';
        }

        $persmisos = ['ADMIN', 'ORHM', 'MAPH'];
        if ($this->ValidaPermiso($persmisos)) {
            $menu .= <<<HTML
            <li><a href="/Promociones/Telarana/">Calculo Descuento Telaraña</a></li>
            <li><a href="/Validaciones/RegistroTelarana/">Registro Telaraña</a></li>
            <li><a href="/Creditos/ActualizaCredito/">Actualización de Créditos</a></li>
            HTML;
        }

        $persmisos = ['ADMIN', 'CAMAG', 'ORHM', 'MAPH'];
        if ($this->ValidaPermiso($persmisos)) {
            $menu .= <<<HTML
            <li><a href="/Creditos/CambioSucursal/">Cambio de Sucursal</a></li>
            <li><a href="/CancelaRef/">Cancelación de Ref</a></li>
            <li><a href="/CorreccionAjustes/">Corrección Mov Ajustes </a></li>
            <li><a href="/Cultiva/">Consulta Clientes Solicitudes</a></li>
            HTML;
        }

        $menu .= '</ul></li>';

        // Permisos temporales para pruebas de retiro de ahorro a 'FLHR' y 'PROA'
        $permisos = ['ADMIN', 'CALLC', 'ACALL', 'FLHR', 'PROA'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
            <ul class="nav side-menu">
                <li><a><i class="glyphicon glyphicon glyphicon-phone-alt"> </i>&nbsp; Call Center <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu">
            HTML;
        }

        $permisos = ['ADMIN', 'ACALL', 'ESMM', 'HSEJ'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
            <li><a href="/CallCenter/Administracion/">Asignar Sucursales</a></li>
            <li><a href="/CallCenter/Prorroga/">Solicitudes de Prorroga</a></li>
            <li><a href="/CallCenter/Reactivar/">Reactivar Solicitudes</a></li>
            <li><a href="/CallCenter/Busqueda/">Búsqueda Rápida</a></li>
            HTML;
        }

        // Permisos temporales para pruebas de retiro de ahorro a 'FLHR' y 'PROA'
        $permisos = ['ADMIN', 'CALLC', 'ACALL', 'HSEJ', 'ESMM', 'MAPH', 'FLHR', 'PROA'];
        if ($this->ValidaPermiso($permisos)) {
            if ($this->__perfil == 'ADMIN' || $this->__usuario == 'HSEJ') {
                $titulo = "(Analistas)";
            } else {
                $mis = 'Mis';
                if ($this->__usuario == 'ESMM' || $this->__usuario == 'MAPH') {
                    $opcion = '<li><a href="/CallCenter/HistoricoAnalistas/">Histórico Analistas</a></li>';
                }
                $opcion .= '<li><a href="/CallCenter/Global/">Todos los Pendientes</a></li>';
            }


            $menu .= "<li><a href='/CallCenter/Pendientes/'>$mis Pendientes $titulo</a></li>";

            // Permisos temporales para pruebas de retiro de ahorro a 'FLHR' y 'PROA'
            if (!$this->ValidaPermiso(['FLHR', 'PROA'])) {
                $menu .= <<<HTML
                            <li><a href="/CallCenter/Historico/">$mis Históricos $titulo</a></li>
                            <li><a href="/CallCenter/EncuestaPostventa/">Postventa</a></li>
                            <li><a href="/CallCenter/ReporteEncuestaPostventa/">Reporte Postventa</a></li>
                            <li><a href="/CallCenter/SupervisionEncuestaPostventa/">Supervisión Postventa</a></li>
                            <li><a href="/CallCenter/Busqueda/">Búsqueda Rápida</a></li>
                            $opcion
                HTML;
            }

            $menu .= <<<HTML
                        </ul>
                    </li>
                </ul>
            HTML;
        }

        $permisos = ['ADMIN', 'PHEE', 'MCDP', 'FECR', 'ORHM'];
        if ($this->ValidaPermiso($permisos)) {
            // <li><a href="/Operaciones/CierreDiario/">Cierre Diario</a></li>
            $menu .= <<<HTML
            <li><a><i class="glyphicon glyphicon-usd"></i>&nbsp;Operaciones<span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                     <li><a href="/Operaciones/ReportePC">Reporte Cliente y Aval Consolidado</a></li>
                </ul>
            </li>
            HTML;
        }

        // Permisos temporales para pruebas de retiro de ahorro a 'LVGA', 'FLHR' y 'PROA'
        $permisos = ['ADMIN', 'PLMV', 'MCDP', 'LGFR', 'MACI', 'MGJC', 'JACJ', 'LVGA', 'FLHR', 'PROA'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
                <ul class="nav side-menu">
                    <li><a><i class="glyphicon glyphicon glyphicon glyphicon-globe"> 
                    </i>&nbsp;Tesorería<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                           
            HTML;
        }

        $permisos = ['ADMIN', 'PLMV', 'MCDP', 'LGFR', 'MACI', 'MGJC', 'JACJ'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<li><a href="/Tesoreria/ReportePC">Reporte Productora Cultiva</a></li>';
        }

        // Permisos temporales para pruebas de retiro de ahorro a 'LVGA', 'FLHR' y 'PROA'
        $permisos = ['ADMIN', 'LVGA', 'MCDP', 'FLHR', 'PROA'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<li><a href="/Ahorro/Retiros/">Solicitudes de Retiro</a></li>';
        }

        // $permisos = ['ADMIN', 'MCDP'];
        // if ($this->ValidaPermiso($permisos)) {
        //     $menu .= '<li><a href="/Cultiva/ReingresarClientesCredito/">Reingresar Clientes a Grupo</a></li>';
        // }

        $permisos = ['ADMIN', 'PLMV', 'MCDP'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '</ul></li></ul>';
        }

        $permisos = ['ADMIN', 'PLMV', 'PHEE'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
                <ul class="nav side-menu">
                    <li>
                        <a><i class="glyphicon glyphicon-exclamation-sign"> 
                    </i>&nbsp;Incidencias MCM<span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="/Incidencias/AutorizaRechazaSolicitud/">Error Autorizar y/o Rechazar Solicitud</a></li>
                            <li><a href="/Incidencias/CalculoDevengo/">Calculo de Devengos</a></li>
                            <li><a href="/Incidencias/CancelarRefinanciamiento/">Cancelar Refinanciamiento</a></li>
                            <li><a href="/Incidencias/ActualizarFechaPagosNoConciliados/">Cambio de Fecha para Pagos No conciliados del día</a></li>
                            <li><a href="/Incidencias/ActualizarFechaPagosNoConciliados/">Telaraña agregar referencias</a></li>
                        </ul>
                    </li>
                </ul>
            HTML;
        }

        $permisos = ['ADMIN', 'MAPH', 'HSEJ', 'ORHM', 'LGFR', 'FECR'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= '<ul class="nav side-menu">';

            if ($this->ValidaPermiso(['ADMIN', 'LGFR'])) {
                $menu .= '<li><a><i class="glyphicon glyphicon-cog"> </i>&nbsp; Administrar Caja <span class="fa fa-chevron-down"></span></a>';
            } else {
                $menu .= '<li><a><i class="glyphicon glyphicon-cog"> </i>&nbsp; Usuarios SICAFIN <span class="fa fa-chevron-down"></span></a>';
            }

            $menu .= '<ul class="nav child_menu">';

            if ($this->ValidaPermiso(['ADMIN', 'LGFR'])) {
                $menu .= <<<HTML
                   <li><a href="/Pagos/AjusteHoraCierre/">Ajustar Hora de Cierre</a></li>
                   <li><a href="/Pagos/DiasFestivos/">Asignación Días Festivos</a></li>
                HTML;
            }

            $permisos = ['ADMIN', 'MAPH', 'HSEJ', 'PHEE', 'ORHM', 'FECR'];
            if ($this->ValidaPermiso($permisos)) {
                $menu .= <<<HTML
                    <li><a href="/Reportes/UsuariosMCM/">Reporte Usuarios SICAFIN MCM</a></li>
                    <li><a href="/Reportes/UsuariosCultiva/">Reporte Usuarios SICAFIN Cultiva</a></li>
                    <li><a href="/Creditos/cierreDiario">Situación Cartera</a></li>
                    <li><a href="/Creditos/AdminCorreos">Administración de Correos</a></li>
                HTML;
            }

            $menu .= '</ul></li></ul>';
        }

        $permisos = ['AMGM'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
                <li>
                    <a>
                        <i class="glyphicon glyphicon-stats">&nbsp;</i>Indicadores
                        <span class="fa fa-chevron-down"></span>
                    </a>
                    <ul class="nav child_menu">
                        <li>
                            <a href="/Indicadores/ProductividadOP/">Productividad Operaciones</a>
                        </li>
                    </ul>
                </li>
            HTML;
        }

        $permisos = ['AMGM', 'ADMIN'];
        if ($this->ValidaPermiso($permisos)) {
            $menu .= <<<HTML
                <li>
                    <a>
                        <i class="glyphicon glyphicon-screenshot">&nbsp;</i>Radar de Cobranza
                        <span class="fa fa-chevron-down"></span>
                    </a>
                    <ul class="nav child_menu">
                        <li>
                            <a href="/RadarCobranza/DashboardDia">Dashboard Día</a>
                        </li>
                    </ul>
                </li>
            HTML;
        }

        $menu .= <<<HTML
                        </div>
                    </div>
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
