<?= $header; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<div class="right_col">
    <div class="prod-op-page">
        <div class="page">
            <div class="page-header">
                <div>
                    <h1>Productividad Operaciones</h1>
                    <p>Desempeño de incidencias en operaciones</p>
                </div>
            </div>

            <div class="toolbar">
                <div>
                    <label>Periodo rápido</label>
                    <div class="period-pills" id="periodPills">
                        <button type="button" data-period="current" class="active">Este mes</button>
                        <button type="button" data-period="prev">Mes anterior</button>
                        <button type="button" data-period="3m">3 meses</button>
                        <button type="button" data-period="12m">12 meses</button>
                    </div>
                </div>
                <div class="sep"></div>
                <div class="field">
                    <label>Desde</label>
                    <input type="date" class="form-control" id="fechaDesde">
                </div>
                <div class="field">
                    <label>Hasta</label>
                    <input type="date" class="form-control" id="fechaHasta">
                </div>
                <div class="field">
                    <label>Región</label>
                    <select class="form-control" id="filtroRegionToolbar">
                        <option value="">Todas</option>
                    </select>
                </div>
                <div class="field field-action">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary btn-sm" id="btnBuscarToolbar"><i class="fa fa-search"></i> Buscar</button>
                </div>
            </div>

            <div class="tab-bar">
                <a href="#" class="tab-link active" data-tab="resumen"><i class="fa fa-dashboard"></i> Resumen ejecutivo</a>
                <a href="#" class="tab-link" data-tab="detalle"><i class="fa fa-list"></i> Consulta detallada</a>
            </div>

            <div class="tab-pane active" id="tab-resumen">
                <div class="insight" id="insightBanner"><i class="fa fa-lightbulb-o"></i><span id="insightText">Cargando…</span></div>
                <div class="kpi-row" id="kpiRow"></div>

                <div class="panel-card" style="margin-bottom:16px">
                    <div class="head">
                        <h4><i class="fa fa-star" style="color:#b8860b"></i> Lo más destacado del periodo</h4>
                        <small>Clic en usuario o sucursal/tipo para ver más detalle</small>
                    </div>
                    <div class="body"><div class="spotlight-row" id="spotlightRow"></div></div>
                </div>

                <div class="dash-grid">
                    <div class="dash-col">
                        <div class="chart-card chart-card--blue chart-card--trend">
                            <div class="chart-card__stripe"></div>
                            <div class="chart-card__body">
                                <div class="chart-card__head">
                                    <div class="chart-card__title"><i class="ti ti-chart-line"></i><span>Tendencia de incidencias</span></div>
                                    <div class="chart-card__head-right">
                                        <span class="chart-card__peak-badge" id="chartTendenciaPeak"></span>
                                        <span class="chart-card__badge" id="chartTendenciaBadge">Últimos 12 meses</span>
                                    </div>
                                </div>
                                <div class="chart-trend-stats" id="chartTendenciaStats"></div>
                                <div class="chart-card__canvas chart-card__canvas--trend"><canvas id="chartTendencia"></canvas></div>
                                <div class="chart-card__legend" id="chartTendenciaLegend"></div>
                            </div>
                        </div>
                        <div class="charts-duo">
                            <div class="chart-card chart-card--blue chart-card--week">
                                <div class="chart-card__stripe"></div>
                                <div class="chart-card__body">
                                    <div class="chart-card__head">
                                        <div class="chart-card__title"><i class="ti ti-calendar-week"></i><span>Carga por día de la semana</span></div>
                                        <div class="chart-card__head-right">
                                            <span class="chart-card__peak-badge" id="chartSemanaPeak"></span>
                                            <span class="chart-card__badge" id="chartSemanaBadge"></span>
                                        </div>
                                    </div>
                                    <p class="chart-card__subtitle" id="chartSemanaSub">Distribución de incidencias por día</p>
                                    <div class="chart-card__canvas chart-card__canvas--week"><canvas id="chartSemana"></canvas></div>
                                    <div class="chart-card__legend" id="chartSemanaLegend"></div>
                                </div>
                            </div>
                            <div class="chart-card chart-card--green">
                                <div class="chart-card__stripe"></div>
                                <div class="chart-card__body">
                                    <div class="chart-card__head">
                                        <div class="chart-card__title"><i class="ti ti-tag"></i><span>Top tipos de movimiento</span></div>
                                        <span class="chart-card__badge" id="chartTiposBadge"></span>
                                    </div>
                                    <p class="chart-card__subtitle" id="chartTiposSub">Cinco movimientos con mayor frecuencia</p>
                                    <div class="chart-card__canvas chart-card__canvas--tipos"><canvas id="chartTipos"></canvas></div>
                                    <div class="chart-card__legend chart-card__legend--tipos" id="chartTiposLegend"></div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-card grow">
                            <div class="head"><h4>Desempeño por usuario</h4><small id="tablaResumenPeriodo"></small></div>
                            <div class="body" style="padding:0">
                                <div class="table-wrap" style="border:none;box-shadow:none;border-radius:0">
                                    <table class="table table-hover" style="margin:0">
                                        <thead>
                                            <tr>
                                                <th style="width:40px">#</th>
                                                <th>Usuario</th>
                                                <th>Incidencias</th>
                                                <th style="width:160px">Participación</th>
                                                <th>Monto</th>
                                                <th title="Módulo donde el usuario concentró más incidencias en el periodo">Módulo predominante</th>
                                                <th style="width:40px"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tblResumenUsuarios"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dash-col">
                        <div class="panel-card">
                            <div class="head"><h4>Por módulo de origen</h4></div>
                            <div class="body" style="padding-bottom:8px">
                                <canvas id="chartModulos" height="140"></canvas>
                                <div class="mod-legend" id="modLegend"></div>
                            </div>
                        </div>
                        <div class="panel-card rank-card">
                            <div class="head"><h4>Top regiones</h4><a href="#" class="tab-link text-muted" data-tab="detalle" style="font-size:12px">Ver todos →</a></div>
                            <div class="body" id="rankRegiones"></div>
                        </div>
                        <div class="panel-card rank-card">
                            <div class="head"><h4>Top sucursales</h4><a href="#" class="tab-link text-muted" data-tab="detalle" style="font-size:12px">Ver todos →</a></div>
                            <div class="body" id="rankSucursales"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab-detalle">
                <div class="detail-layout">
                    <aside class="detail-filters">
                        <div class="df-head"><span><i class="fa fa-filter"></i> Filtros</span><a href="#" id="btnLimpiar">Limpiar</a></div>
                        <div class="df-body">
                            <div class="fg">
                                <label>Usuario</label>
                                <select class="form-control" id="fUsuario"><option value="">Todos los usuarios</option></select>
                            </div>
                            <div class="fg">
                                <label>Región</label>
                                <select class="form-control" id="fRegion"><option value="">Todas</option></select>
                            </div>
                            <div class="fg">
                                <label>Sucursal</label>
                                <select class="form-control" id="fSucursal"><option value="">Todas</option></select>
                            </div>
                            <div class="fg">
                                <label>Tipo de movimiento</label>
                                <select class="form-control" id="fTipo"><option value="">Todos</option></select>
                            </div>
                            <div class="df-actions">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="btnAplicar"><i class="fa fa-search"></i> Buscar</button>
                                <button type="button" class="btn btn-success btn-sm btn-block" id="btnExcelConsulta"><i class="fa fa-file-excel-o"></i> Descargar Excel</button>
                            </div>
                        </div>
                    </aside>
                    <div class="detail-main">
                        <div class="results-bar">
                            <div class="stats">
                                <div class="stat-item"><div class="n" id="resCount">0</div><div class="l">Registros</div></div>
                                <div class="stat-item"><div class="n" id="resMonto">$0</div><div class="l">Monto total</div></div>
                                <div class="stat-item"><div class="n" id="resProm">$0</div><div class="l">Promedio</div></div>
                            </div>
                        </div>
                        <div class="mod-filters" id="modFilters">
                            <button type="button" class="mod-btn active" data-mod="all"><span class="dot" style="background:#888"></span> Todos</button>
                            <button type="button" class="mod-btn" data-mod="pagos"><span class="dot" style="background:#1a6fb5"></span> Pagos Día</button>
                            <button type="button" class="mod-btn" data-mod="ajuste"><span class="dot" style="background:#e8a020"></span> Ajuste</button>
                            <button type="button" class="mod-btn" data-mod="gar"><span class="dot" style="background:#2e7d52"></span> Garantías</button>
                            <button type="button" class="mod-btn" data-mod="call"><span class="dot" style="background:#7d3c98"></span> Call Center</button>
                        </div>
                        <div class="active-filters" id="filterChips"></div>
                        <div class="table-wrap">
                            <table id="tblDetalle" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Crédito / Ciclo</th>
                                        <th>Tipo / Descripción</th>
                                        <th>Módulo</th>
                                        <th>Sucursal</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div class="empty-state" id="emptyState" style="display:none">
                                <i class="fa fa-inbox"></i>
                                <p><strong>Sin resultados</strong></p>
                                <p style="font-size:13px">Prueba ajustando los filtros o la búsqueda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detalleUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modal-title" id="ttlNombre"></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-charts">
                    <div><canvas id="chrtUConteo"></canvas></div>
                    <div><canvas id="chrtMonto"></canvas></div>
                </div>
                <hr>
                <div style="text-align:center;margin-bottom:12px">
                    <button type="button" class="btn btn-success" id="btnDescargaExcel"><i class="fa fa-file-excel-o"></i> Descargar Excel</button>
                    <input type="hidden" id="xsl_usuario" value="">
                    <input type="hidden" id="xsl_fechaI" value="">
                    <input type="hidden" id="xsl_fechaF" value="">
                </div>
                <hr>
                <div class="table-wrap">
                    <table id="tblUsuario" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th><th>Crédito</th><th>Ciclo</th><th>Monto</th>
                                <th>Descripción</th><th>Tipo</th><th>Región</th><th>Sucursal</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $footer; ?>
