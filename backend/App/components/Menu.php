<?php

namespace App\components;

/**
 * Clase Menu
 * 
 * Representa el componente para el menu del sistema.
 */
class Menu
{
    /**
     * Perfil del usuario.
     * 
     * @var string
     */
    private $perfil;

    /**
     * Usuario.
     * 
     * @var string
     */
    private $usuario;

    /**
     * Permisos para modulo de Ahorro.
     * 
     * @var string
     */
    private $ahorro;

    /**
     * Constructor de la clase BuscarCliente.
     * 
     * @param string $recordatorio Recordatorio o indicaciones para la cajera.
     */
    public function __construct($_perfil, $_usuario, $_ahorro)
    {
        $this->perfil = $_perfil;
        $this->usuario = $_usuario;
        $this->ahorro = $_ahorro;
    }

    private function Menu()
    {
        $mis = $this->ValidaPermisos(['CALLC', 'ACALL']) ? 'Mis ' : '';
        $analistas = $this->ValidaPermisos(['ADMIN', 'HSEJ']) ? ' (Analistas)' : '';
        $tituloAdmin = $this->ValidaPermisos(['ADMIN', 'LGFR']) ? 'Administración' : 'Usuarios SICAFIN';

        return [
            [
                'seccion' => 'General WEB AHORRO',
                'opciones' => [
                    [
                        'titulo' => 'Mi espacio',
                        'icono' => 'glyphicon glyphicon-usd',
                        'url' => [
                            'directorio' => '/Ahorro/CuentaCorriente/',
                            'permisos' => ['AMGM'],
                        ]
                    ],
                    [
                        'titulo' => 'Admin Sucursales',
                        'icono' => 'glyphicon glyphicon-paste',
                        'url' => [
                            '/AdminSucursales/SaldosDiarios/',
                            'permisos' => ['AMGM', 'LGFR', 'PAES', 'PMAB', 'DCRI', 'GUGJ', 'JUSA', 'HEDC', 'PHEE'],
                        ]
                    ]
                ]
            ],
            [
                'seccion' => 'GENERAL',
                'opciones' => [
                    [
                        'titulo' => 'Pagos',
                        'icono' => 'glyphicon glyphicon-usd',
                        'items' => [
                            [
                                'titulo' => 'Administración Pagos',
                                'url' => [
                                    'directorio' => '/Pagos/',
                                    'permisos' => ['ADMIN', 'LGFR', 'MGJC', 'MCDP']
                                ]
                            ],
                            [
                                'titulo' => 'Recepción Pagos App',
                                'url' => [
                                    'directorio' => '/Pagos/CorteEjecutivo/',
                                    'permisos' => ['ADMIN']
                                ]
                            ],
                            [
                                'titulo' => 'Layout Contable',
                                'url' => [
                                    'directorio' => '/Pagos/Layout/',
                                    'permisos' => ['ADMIN', 'ACALL', 'LAYOU']
                                ]
                            ],
                            [
                                'titulo' => 'Registro de Pagos Caja',
                                'url' => [
                                    'directorio' => '/Pagos/PagosRegistro/',
                                    'permisos' => ['ADMIN', 'CAJA', 'LGFR', 'PLMV', 'PMAB', 'MGJC', 'AVGA', 'FLCR', 'COCS', 'GOIY', 'DAGC', 'COVG', 'TESP']
                                ]
                            ],
                            [
                                'titulo' => 'Consulta de Pagos Cliente',
                                'url' => [
                                    'directorio' => '/Pagos/PagosConsultaUsuarios/',
                                    'permisos' => ['ACALL']
                                ]
                            ],
                            [
                                'titulo' => 'Consultar Pagos',
                                'url' => [
                                    'directorio' => '/Pagos/PagosConsulta/',
                                    'permisos' => ['ADMIN', 'CAJA', 'GTOCA', 'AMOCA', 'OCOF', 'CPAGO', 'ACALL']
                                ]
                            ],
                            [
                                'titulo' => 'Reimprimir Recibo de Efectivo',
                                'url' => [
                                    'directorio' => '/Pagos/ReimprimirReciboEfectivo/',
                                    'permisos' => ['ADMIN', 'CAJA', 'GTOCA', 'AMOCA', 'OCOF', 'CPAGO', 'ACALL']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Créditos',
                        'icono' => 'fa fa-users',
                        'items' => [
                            [
                                'titulo' => 'Control de Garantías',
                                'url' => [
                                    'directorio' => '/Creditos/ControlGarantias/',
                                    'permisos' => ['ADMIN', 'GARAN', 'ORHM', 'MAPH', 'AMOCA']
                                ]
                            ],
                            [
                                'titulo' => 'Calculo Descuento Telaraña',
                                'url' => [
                                    'directorio' => '/Promociones/Telarana/',
                                    'permisos' => ['ADMIN', 'ORHM', 'MAPH']
                                ]
                            ],
                            [
                                'titulo' => 'Registro Telaraña',
                                'url' => [
                                    'directorio' => '/Validaciones/RegistroTelarana/',
                                    'permisos' => ['ADMIN', 'ORHM', 'MAPH']
                                ]
                            ],
                            [
                                'titulo' => 'Actualización de Créditos',
                                'url' => [
                                    'directorio' => '/Creditos/ActualizaCredito/',
                                    'permisos' => ['ADMIN', 'ORHM', 'MAPH']
                                ]
                            ],
                            [
                                'titulo' => 'Cambio de Sucursal',
                                'url' => [
                                    'directorio' => '/Creditos/CambioSucursal/',
                                    'permisos' => ['ADMIN', 'CAMAG', 'ORHM', 'MAPH']
                                ]
                            ],
                            [
                                'titulo' => 'Cancelación de Ref',
                                'url' => [
                                    'directorio' => '/CancelaRef/',
                                    'permisos' => ['ADMIN', 'CAMAG', 'ORHM', 'MAPH']
                                ]
                            ],
                            [
                                'titulo' => 'Corrección Mov Ajustes',
                                'url' => [
                                    'directorio' => '/CorreccionAjustes/',
                                    'permisos' => ['ADMIN', 'CAMAG', 'ORHM', 'MAPH']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Call Center',
                        'icono' => 'glyphicon glyphicon-phone-alt',
                        'items' => [
                            [
                                'titulo' => 'Asignar Sucursales',
                                'url' => [
                                    'directorio' => '/CallCenter/Administracion/',
                                    'permisos' => ['ADMIN', 'ACALL', 'ESMM', 'HSEJ']
                                ]
                            ],
                            [
                                'titulo' => 'Solicitudes de Prorroga',
                                'url' => [
                                    'directorio' => '/CallCenter/Prorroga/',
                                    'permisos' => ['ADMIN', 'ACALL', 'ESMM', 'HSEJ']
                                ]
                            ],
                            [
                                'titulo' => 'Reactivar Solicitudes',
                                'url' => [
                                    'directorio' => '/CallCenter/Reactivar/',
                                    'permisos' => ['ADMIN', 'ACALL', 'ESMM', 'HSEJ']
                                ]
                            ],
                            [
                                'titulo' => 'Búsqueda Rápida',
                                'url' => [
                                    'directorio' => '/CallCenter/Busqueda/',
                                    'permisos' => ['ADMIN', 'ACALL', 'ESMM', 'HSEJ']
                                ]
                            ],
                            [
                                'titulo' => 'Histórico Analistas',
                                'url' => [
                                    'directorio' => '/CallCenter/HistoricoAnalistas/',
                                    'permisos' => ['ESMM', 'MAPH']
                                ]
                            ],
                            [
                                'titulo' => 'Todos los Pendientes',
                                'url' => [
                                    'directorio' => '/CallCenter/Global/',
                                    'permisos' => ['CALLC', 'ACALL']
                                ]
                            ],
                            [
                                'titulo' => $mis . 'Pendientes' . $analistas,
                                'url' => [
                                    'directorio' => '/CallCenter/Pendientes/',
                                    'permisos' => ['ADMIN', 'CALLC', 'ACALL', 'HSEJ']
                                ]
                            ],
                            [
                                'titulo' => $mis . 'Históricos' . $analistas,
                                'url' => [
                                    'directorio' => '/CallCenter/Historico/',
                                    'permisos' => ['ADMIN', 'CALLC', 'ACALL', 'HSEJ']
                                ]
                            ],
                            [
                                'titulo' => 'Postventa',
                                'url' => [
                                    'directorio' => '/CallCenter/EncuestaPostventa/',
                                    'permisos' => ['ADMIN', 'CALLC', 'ACALL', 'HSEJ']
                                ]
                            ],
                            [
                                'titulo' => 'Reporte Postventa',
                                'url' => [
                                    'directorio' => '/CallCenter/ReporteEncuestaPostventa/',
                                    'permisos' => ['ADMIN', 'CALLC', 'ACALL', 'HSEJ']
                                ]
                            ],
                            [
                                'titulo' => 'Supervisión Postventa',
                                'url' => [
                                    'directorio' => '/CallCenter/SupervisionEncuestaPostventa/',
                                    'permisos' => ['ADMIN', 'CALLC', 'ACALL', 'HSEJ']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Cultiva',
                        'icono' => 'glyphicon glyphicon-globe',
                        'items' => [
                            [
                                'titulo' => 'Consulta Clientes Solicitudes',
                                'url' => [
                                    'directorio' => '/Cultiva/',
                                    'permisos' => ['ADMIN', 'PLMV', 'MCDP']
                                ]
                            ],
                            [
                                'titulo' => 'Reingresar Clientes a Grupo',
                                'url' => [
                                    'directorio' => '/Cultiva/ReingresarClientesCredito/',
                                    'permisos' => ['ADMIN', 'MCDP']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Incidencias MCM',
                        'icono' => 'glyphicon glyphicon-cog',
                        'items' => [
                            [
                                'titulo' => 'Error Autorizar y/o Rechazar Solicitud',
                                'url' => [
                                    'directorio' => '/Incidencias/AutorizaRechazaSolicitud/',
                                    'permisos' => ['ADMIN', 'PLMV', 'PHEE']
                                ]
                            ],
                            [
                                'titulo' => 'Calculo de Devengos',
                                'url' => [
                                    'directorio' => '/Incidencias/CalculoDevengo/',
                                    'permisos' => ['ADMIN', 'PLMV', 'PHEE']
                                ]
                            ],
                            [
                                'titulo' => 'Cancelar Refinanciamiento',
                                'url' => [
                                    'directorio' => '/Incidencias/CancelarRefinanciamiento/',
                                    'permisos' => ['ADMIN', 'PLMV', 'PHEE']
                                ]
                            ],
                            [
                                'titulo' => 'Cambio de Fecha para Pagos No conciliados del día',
                                'url' => [
                                    'directorio' => '/Incidencias/ActualizarFechaPagosNoConciliados/',
                                    'permisos' => ['ADMIN', 'PLMV', 'PHEE']
                                ]
                            ],
                            [
                                'titulo' => 'Telaraña agregar referencias',
                                'url' => [
                                    'directorio' => '/Incidencias/ActualizarFechaPagosNoConciliados/',
                                    'permisos' => ['ADMIN', 'PLMV', 'PHEE']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => $tituloAdmin,
                        'icono' => 'glyphicon glyphicon-cog',
                        'items' => [
                            [
                                'titulo' => 'Ajustar Hora de Cierre',
                                'url' => [
                                    'directorio' => '/Pagos/AjusteHoraCierre/',
                                    'permisos' => ['ADMIN', 'LGFR']
                                ]
                            ],
                            [
                                'titulo' => 'Asignación Días Festivos',
                                'url' => [
                                    'directorio' => '/Pagos/DiasFestivos/',
                                    'permisos' => ['ADMIN', 'LGFR']
                                ]
                            ],
                            [
                                'titulo' => 'Reporte Usuarios SICAFIN MCM',
                                'url' => [
                                    'directorio' => '/Reportes/UsuariosMCM/',
                                    'permisos' => ['ADMIN', 'MAPH', 'HSEJ', 'PHEE', 'ORHM']
                                ]
                            ],
                            [
                                'titulo' => 'Reporte Usuarios SICAFIN Cultiva',
                                'url' => [
                                    'directorio' => '/Reportes/UsuariosCultiva/',
                                    'permisos' => ['ADMIN', 'MAPH', 'HSEJ', 'PHEE', 'ORHM']
                                ]
                            ],
                            [
                                'titulo' => 'Situación Cartera',
                                'url' => [
                                    'directorio' => '/Creditos/cierreDiario',
                                    'permisos' => ['ADMIN', 'MAPH', 'HSEJ', 'PHEE', 'ORHM']
                                ]
                            ],
                            [
                                'titulo' => 'Administración de Correos',
                                'url' => [
                                    'directorio' => '/Creditos/AdminCorreos',
                                    'permisos' => ['ADMIN', 'MAPH', 'HSEJ', 'PHEE', 'ORHM']
                                ]
                            ]
                        ]
                    ],
                    [
                        'titulo' => 'Indicadores',
                        'icono' => 'glyphicon glyphicon-cog',
                        'items' => [
                            [
                                'titulo' => 'Productividad Operaciones',
                                'url' => [
                                    'directorio' => '/Indicadores/ProductividadOP/',
                                    'permisos' => ['ADMIN']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    function mostrar()
    {
        $menu = $this->Menu();
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
                            <span class="{$opcion['icono']}">&nbsp;</span>{$opcion['titulo']}<span class="fa fa-chevron-down"></span>
                        </a>
                        <ul class="nav child_menu">
                            {$html}
                        </ul>
                    </li>
                HTML;
            }
        } else {
            if ($this->ValidaPermisos($opcion['url']['permisos'])) {
                return <<<HTML
                    <li>
                        <a href="{$opcion['url']['directorio']}">
                            <span class="{$opcion['icono']}">&nbsp;</span>{$opcion['titulo']}
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