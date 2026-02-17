<?php

namespace App\components;

/**
 * Clase Menu
 *
 * Componente del menú del sistema. La definición de accesos se organiza por
 * secciones y opciones; cada opción puede ser un enlace o un submenú con items.
 * Estructura de opción enlace: titulo, url['directorio','permisos'], icono (opcional).
 * Estructura de opción submenú: titulo, icono, items[] (cada item como enlace).
 */
class Menu
{
    /** @var string Perfil del usuario */
    private $perfil;

    /** @var string Usuario */
    private $usuario;

    /** @var string Permisos para módulo de Ahorro */
    private $ahorro;

    /** @var bool Mostrar menú Herramientas (según config) */
    private $mostrarHerramientas;

    public function __construct($_perfil, $_usuario, $_ahorro, $mostrarHerramientas = false)
    {
        $this->perfil = $_perfil;
        $this->usuario = $_usuario;
        $this->ahorro = $_ahorro;
        $this->mostrarHerramientas = (bool) $mostrarHerramientas;
    }

    /**
     * Devuelve la estructura completa del menú (secciones con opciones).
     */
    private function obtenerEstructuraMenu()
    {
        return [
            $this->seccionGeneral(),
        ];
    }

    /**
     * Helper: define un ítem de menú que es un enlace.
     *
     * @param string $titulo
     * @param string $ruta   URL (ej. /Pagos/PagosConsulta/)
     * @param array  $permisos
     * @param string|null $icono Clase CSS del icono (opcional, ej. glyphicon glyphicon-usd)
     * @return array
     */
    private function enlace($titulo, $ruta, array $permisos, $icono = null)
    {
        $item = [
            'titulo' => $titulo,
            'url'    => ['directorio' => $ruta, 'permisos' => $permisos],
        ];
        if ($icono !== null) {
            $item['icono'] = $icono;
        }
        return $item;
    }

    /**
     * Helper: define un ítem de menú que es un submenú (desplegable).
     *
     * @param string $titulo
     * @param string $icono  Clase CSS del icono
     * @param array  $items  Lista de ítems (enlaces o anidados)
     * @return array
     */
    private function submenu($titulo, $icono, array $items)
    {
        return [
            'titulo' => $titulo,
            'icono'  => $icono,
            'items'  => $items,
        ];
    }

    /** Sección: General WEB AHORRO (eliminada: ahora el menú no incluye esta sección) */

    /** Opciones del submenú Pagos */
    private function opcionesPagos()
    {
        return [
            $this->enlace('Administración Pagos', '/Pagos/', ['ADMIN', 'MCDP', 'LVGA', 'QARO', 'FLHR']),
            $this->enlace('Pagos App (DEMO)', '/Pagos_temporal/CorteEjecutivo/', ['ADMIN', 'FLHR']),
            $this->enlace('Pagos App', '/Pagos/CorteEjecutivo/', ['ADMIN', 'CAJA', 'FLHR', 'HEDC', 'JULM', 'CRCV', 'LUMM', 'EMGL', 'PEAE', 'MCDP', 'LVGA']),
            $this->enlace('Layout Contable', '/Pagos/Layout/', ['ADMIN', 'ACALL', 'LAYOU', 'FECR']),
            $this->enlace('Registro de Pagos Caja', '/Pagos/PagosRegistro/', ['ADMIN', 'CAJA', 'LGFR', 'PLMV', 'PMAB', 'MGJC', 'AVGA', 'FLCR', 'COCS', 'GOIY', 'DAGC', 'COVG', 'TESP', 'JACJ']),
            $this->enlace('Consulta de Pagos Cliente', '/Pagos/PagosConsultaUsuarios/', ['ADMIN', 'ACALL']),
            $this->enlace('Consultar Pagos', '/Pagos/PagosConsulta/', ['ADMIN', 'CAJA', 'GTOCA', 'AMOCA', 'OCOF', 'CPAGO', 'ACALL']),
            $this->enlace('Reimprimir Recibo de Efectivo', '/Pagos/ReimprimirReciboEfectivo/', ['ADMIN', 'CAJA', 'GTOCA', 'AMOCA', 'OCOF', 'CPAGO', 'ACALL']),
        ];
    }

    /** Opciones del submenú Resumen Ahorro */
    private function opcionesResumenAhorro()
    {
        return [
            $this->enlace('Alta Contrato', '/AhorroSimple/Contrato/', ['ADMIN', 'AMOCA', 'VAOY', 'TOOA', 'HTMP', 'JUJG', 'QARO', 'MAPH']),
            $this->enlace('Estado de Cuenta Ahorro', '/AhorroSimple/EstadoCuenta/', ['ADMIN', 'CAJA', 'AMOCA', 'CPAGO', 'VAOY', 'TOOA', 'HTMP', 'JUJG', 'LGFR', 'MGJC', 'MAPH']),
            $this->enlace('Valida Crédito Adicional', '/AhorroSimple/ValidaAdicional/', ['ADMIN', 'AMOCA', 'VAOY', 'TOOA', 'HTMP', 'JUJG', 'MGJC', 'MAPH']),
            $this->enlace('Agregar Exepciones MXT', '/AhorroSimple/ExepcionesMXT/', ['ADMIN', 'AMOCA', 'VAOY', 'TOOA', 'HTMP', 'JUJG', 'MGJC', 'MAPH']),
            $this->enlace('Solicitudes Retiro', '/AhorroConsulta/', ['ADMIN', 'AMOCA', 'VAOY', 'TOOA', 'HTMP', 'JUJG', 'MGJC', 'MAPH']),
            // Permisos temporales para pruebas de retiro de ahorro a 'LVGA', 'MCDP' y 'FLHR'
            $this->enlace('Gestión de Retiros', '/Ahorro/SolicitudesRetiroAdmin/', ['ADMIN', 'LVGA', 'MCDP', 'FLHR']),
        ];
    }

    /** Opciones del submenú Créditos */
    private function opcionesCreditos()
    {
        return [
            $this->enlace('Control de Garantías', '/Creditos/ControlGarantias/', ['ADMIN', 'GARAN', 'ORHM', 'MAPH', 'AMOCA']),
            $this->enlace('Calculo Descuento Telaraña', '/Promociones/Telarana/', ['ADMIN', 'ORHM', 'MAPH']),
            $this->enlace('Registro Telaraña', '/Validaciones/RegistroTelarana/', ['ADMIN', 'ORHM', 'MAPH']),
            $this->enlace('Actualización de Créditos', '/Creditos/ActualizaCredito/', ['ADMIN', 'ORHM', 'MAPH']),
            $this->enlace('Cambio de Sucursal', '/Creditos/CambioSucursal/', ['ADMIN', 'CAMAG', 'ORHM', 'MAPH']),
            $this->enlace('Cancelación de Ref', '/CancelaRef/', ['ADMIN', 'CAMAG', 'ORHM', 'MAPH']),
            $this->enlace('Corrección Mov Ajustes', '/CorreccionAjustes/', ['ADMIN', 'CAMAG', 'ORHM', 'MAPH']),
            $this->enlace('Consulta Clientes Solicitudes', '/Cultiva/', ['ADMIN', 'CAMAG', 'ORHM', 'MAPH']),
        ];
    }

    /** Opciones del submenú Call Center (títulos dinámicos por perfil) */
    private function opcionesCallCenter()
    {
        $mis = $this->ValidaPermisos(['CALLC', 'ACALL', 'ESMM', 'MAPH', 'FLHR']) ? 'Mis ' : '';
        $analistas = $this->ValidaPermisos(['ADMIN', 'HSEJ']) ? ' (Analistas)' : '';

        return [
            $this->enlace('Asignar Sucursales', '/CallCenter/Administracion/', ['ADMIN', 'ACALL', 'ESMM', 'HSEJ']),
            $this->enlace('Solicitudes de Prorroga', '/CallCenter/Prorroga/', ['ADMIN', 'ACALL', 'ESMM', 'HSEJ']),
            $this->enlace('Reactivar Solicitudes', '/CallCenter/Reactivar/', ['ADMIN', 'ACALL', 'ESMM', 'HSEJ']),
            $this->enlace('Búsqueda Rápida', '/CallCenter/Busqueda/', ['ADMIN', 'CALLC', 'ACALL', 'HSEJ', 'ESMM', 'MAPH']),
            $this->enlace('Histórico Analistas', '/CallCenter/HistoricoAnalistas/', ['ESMM', 'MAPH']),
            $this->enlace('Todos los Pendientes', '/CallCenter/Global/', ['ADMIN', 'CALLC', 'ACALL', 'HSEJ', 'FLHR']),
            $this->enlace($mis . 'Pendientes' . $analistas, '/CallCenter/Pendientes/', ['ADMIN', 'CALLC', 'ACALL', 'HSEJ']),
            $this->enlace($mis . 'Históricos' . $analistas, '/CallCenter/Historico/', ['ADMIN', 'CALLC', 'ACALL', 'HSEJ', 'ESMM', 'MAPH']),
            $this->enlace('Postventa', '/CallCenter/EncuestaPostventa/', ['ADMIN', 'CALLC', 'ACALL', 'HSEJ', 'ESMM', 'MAPH']),
            $this->enlace('Reporte Postventa', '/CallCenter/ReporteEncuestaPostventa/', ['ADMIN', 'CALLC', 'ACALL', 'HSEJ', 'ESMM', 'MAPH']),
            $this->enlace('Supervisión Postventa', '/CallCenter/SupervisionEncuestaPostventa/', ['ADMIN', 'CALLC', 'ACALL', 'HSEJ', 'ESMM', 'MAPH']),
        ];
    }

    /** Opciones del submenú Operaciones */
    private function opcionesOperaciones()
    {
        return [
            $this->enlace('Reporte Cliente y Aval Consolidado', '/Operaciones/ReportePC', ['ADMIN', 'PHEE', 'MCDP', 'FECR', 'ORHM']),
        ];
    }

    /** Opciones del submenú Tesorería */
    private function opcionesTesoreria()
    {
        return [
            $this->enlace('Reporte Productora Cultiva', '/Tesoreria/ReportePC', ['ADMIN', 'PLMV', 'MCDP', 'LGFR', 'MACI', 'MGJC', 'JACJ', 'LVGA', 'FLHR']),
            $this->enlace('Solicitudes de Retiro', '/Ahorro/Retiros/', ['ADMIN', 'CAJA', 'PLMV', 'MCDP', 'LGFR', 'MACI', 'MGJC', 'JACJ', 'LVGA', 'FLHR']),
        ];
    }

    /** Opciones del submenú Incidencias MCM */
    private function opcionesIncidencias()
    {
        return [
            $this->enlace('Error Autorizar y/o Rechazar Solicitud', '/Incidencias/AutorizaRechazaSolicitud/', ['ADMIN', 'PLMV', 'PHEE']),
            $this->enlace('Calculo de Devengos', '/Incidencias/CalculoDevengo/', ['ADMIN', 'PLMV', 'PHEE']),
            $this->enlace('Cancelar Refinanciamiento', '/Incidencias/CancelarRefinanciamiento/', ['ADMIN', 'PLMV', 'PHEE']),
            $this->enlace('Cambio de Fecha para Pagos No conciliados del día', '/Incidencias/ActualizarFechaPagosNoConciliados/', ['ADMIN', 'PLMV', 'PHEE']),
            $this->enlace('Telaraña agregar referencias', '/Incidencias/ActualizarFechaPagosNoConciliados/', ['ADMIN', 'PLMV', 'PHEE']),
        ];
    }


    /** Opciones del submenú Cultiva */
    private function opcionesCultiva()
    {
        return [
            $this->enlace('Reingresar Clientes a Grupo', '/Cultiva/ReingresarClientesCredito/', ['ADMIN', 'MCDP']),
        ];
    }
    /** Opciones del submenú Administración / Usuarios SICAFIN (título dinámico) */
    private function opcionesAdministracion()
    {
        return [
            $this->enlace('Ajustar Hora de Cierre', '/Pagos/AjusteHoraCierre/', ['ADMIN', 'LGFR']),
            $this->enlace('Asignación Días Festivos', '/Pagos/DiasFestivos/', ['ADMIN', 'LGFR']),
            $this->enlace('Reporte Usuarios SICAFIN MCM', '/Reportes/UsuariosMCM/', ['ADMIN', 'MAPH', 'HSEJ', 'PHEE', 'ORHM', 'FECR']),
            $this->enlace('Reporte Usuarios SICAFIN Cultiva', '/Reportes/UsuariosCultiva/', ['ADMIN', 'MAPH', 'HSEJ', 'PHEE', 'ORHM', 'FECR']),
            $this->enlace('Situación Cartera', '/Creditos/cierreDiario', ['ADMIN', 'MAPH', 'HSEJ', 'PHEE', 'ORHM', 'FECR']),
            $this->enlace('Administración de Correos', '/Creditos/AdminCorreos', ['ADMIN', 'MAPH', 'HSEJ', 'PHEE', 'ORHM', 'FECR']),
        ];
    }

    /** Opciones del submenú Indicadores */
    private function opcionesIndicadores()
    {
        return [
            $this->enlace('Productividad Operaciones', '/Indicadores/ProductividadOP/', ['AMGM']),
        ];
    }

    /** Opciones del submenú Radar de Cobranza */
    private function opcionesRadarCobranza()
    {
        return [
            $this->enlace('Dashboard Día', '/RadarCobranza/DashboardDia', ['AMGM', 'ADMIN']),
        ];
    }

    /** Opciones del submenú Herramientas (visibilidad según config) */
    private function opcionesHerramientas()
    {
        return [
            $this->enlace('Rep Dia de Atraso', '/Herramientas/RepDiaAtraso/', ['ADMIN']),
        ];
    }

    /** Sección: GENERAL (todas las opciones principales) */
    private function seccionGeneral()
    {
        $tituloAdmin = $this->ValidaPermisos(['ADMIN', 'LGFR']) ? 'Administración' : 'Usuarios SICAFIN';

        $opciones = [
            $this->submenu('Pagos', 'glyphicon glyphicon-usd', $this->opcionesPagos()),
            $this->submenu('Resumen Ahorro', 'fa fa-money', $this->opcionesResumenAhorro()),
            $this->submenu('Créditos', 'fa fa-users', $this->opcionesCreditos()),
            $this->submenu('Call Center', 'glyphicon glyphicon-phone-alt', $this->opcionesCallCenter()),
            $this->submenu('Operaciones', 'glyphicon glyphicon-usd', $this->opcionesOperaciones()),
            $this->submenu('Tesorería', 'glyphicon glyphicon-globe', $this->opcionesTesoreria()),
            $this->submenu('Cultiva', 'glyphicon glyphicon-globe', $this->opcionesCultiva()),
            $this->submenu('Incidencias MCM', 'glyphicon glyphicon-cog', $this->opcionesIncidencias()),
            $this->submenu($tituloAdmin, 'glyphicon glyphicon-cog', $this->opcionesAdministracion()),
            $this->submenu('Indicadores', 'glyphicon glyphicon-cog', $this->opcionesIndicadores()),
            $this->submenu('Radar de Cobranza', 'glyphicon glyphicon-screenshot', $this->opcionesRadarCobranza()),
        ];

        if ($this->mostrarHerramientas) {
            $opciones[] = $this->submenu('Herramientas', 'glyphicon glyphicon-wrench', $this->opcionesHerramientas());
        }

        return [
            'seccion'  => 'GENERAL',
            'opciones' => $opciones,
        ];
    }

    public function mostrar()
    {
        $menu = $this->obtenerEstructuraMenu();
        $html = <<<HTML
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                <div class="menu_section" style="overflow: auto">
        HTML;

        foreach ($menu as $seccion) {
            $html .= $this->mostrarSeccion($seccion);
        }

        $html .= <<<HTML
                </div>
            </div>
        HTML;

        return $html;
    }

    private function mostrarSeccion($seccion)
    {
        $html = '';
        foreach ($seccion['opciones'] as $opcion) {
            $html .= $this->mostrarOpcion($opcion);
        }

        if ($html !== '') {
            return <<<HTML
                <hr>
                <h3>{$seccion['seccion']}</h3>
                <ul class="nav side-menu">
                    {$html}
                </ul>
            HTML;
        }
    }

    private function mostrarOpcion($opcion)
    {
        if (isset($opcion['items'])) {
            $html = '';
            foreach ($opcion['items'] as $item) {
                $html .= $this->mostrarOpcion($item);
            }

            if ($html !== '') {
                return <<<HTML
                    <li>
                        <a>
                            <i class="{$opcion['icono']}"></i>&nbsp;{$opcion['titulo']}<span class="fa fa-chevron-down"></span>
                        </a>
                        <ul class="nav child_menu">
                            {$html}
                        </ul>
                    </li>
                HTML;
            }
        } else {
            if ($this->ValidaPermisos($opcion['url']['permisos'])) {
                $icono = isset($opcion['icono']) ? '<i class="' . $opcion['icono'] . '"></i>&nbsp;' : '';
                return <<<HTML
                    <li>
                        <a href="{$opcion['url']['directorio']}">
                            {$icono}{$opcion['titulo']}
                        </a>
                    </li>
                HTML;
            }
        }
    }

    private function ValidaPermisos($permisos)
    {
        return in_array($this->perfil, $permisos) || in_array($this->usuario, $permisos);
    }
}
