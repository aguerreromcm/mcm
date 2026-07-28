<?= $header; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

<div class="right_col">
    <div class="prod-op-page reporte-ahorro-page">
        <div class="page">
            <div class="page-header">
                <div>
                    <h1>Reporte de Ahorro</h1>
                    <p>Estado general del ahorro al corte: saldos, movimientos y rankings</p>
                </div>
            </div>

            <div class="toolbar" id="toolbarResumen">
                <div class="field">
                    <label for="fechaCorte">Fecha de corte</label>
                    <input type="date" class="form-control" id="fechaCorte" name="fechaCorte" value="<?= $fechaCorte; ?>" max="<?= date('Y-m-d'); ?>">
                </div>
                <div class="field" style="min-width: 180px;">
                    <label for="region">Región</label>
                    <select class="form-control" id="region" name="region">
                        <option value="" selected>Todas</option>
                    </select>
                </div>
                <div class="field" style="min-width: 200px;">
                    <label for="sucursal">Sucursal</label>
                    <select class="form-control" id="sucursal" name="sucursal">
                        <option value="" selected>Todas</option>
                    </select>
                </div>
                <div class="field field-action">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary btn-sm" id="btnBuscar">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </div>
            </div>

            <div class="tab-bar">
                <a href="#" class="tab-link active" data-tab="resumen"><i class="fa fa-dashboard"></i> Resumen ejecutivo</a>
                <a href="#" class="tab-link" data-tab="detalle"><i class="fa fa-list"></i> Consulta detallada</a>
            </div>

            <div class="tab-pane active" id="tab-resumen">
                <div class="kpi-grid kpi-grid--hero" id="kpiGrid">
                    <div class="kpi kpi--ok">
                        <div class="kpi__icon"><i class="ti ti-wallet"></i></div>
                        <div class="kpi__body">
                            <div class="lbl">Saldo actual</div>
                            <div class="num num--money" id="kpiSaldo">$ 0.00</div>
                            <div class="kpi__sub" id="kpiTasa">Tasa ponderada —</div>
                        </div>
                    </div>
                    <div class="kpi kpi--blue">
                        <div class="kpi__icon"><i class="ti ti-arrow-down-circle"></i></div>
                        <div class="kpi__body">
                            <div class="lbl">Abonos</div>
                            <div class="num num--money" id="kpiAbonos">$ 0.00</div>
                        </div>
                    </div>
                    <div class="kpi kpi--danger">
                        <div class="kpi__icon"><i class="ti ti-arrow-up-circle"></i></div>
                        <div class="kpi__body">
                            <div class="lbl">Retiros</div>
                            <div class="num num--money" id="kpiRetiros">$ 0.00</div>
                        </div>
                    </div>
                </div>

                <div class="kpi-grid kpi-grid--mini" id="raCounts">
                    <div class="kpi kpi--mini kpi--accent kpi--click" data-ir-tipo="con" title="Ver en consulta detallada">
                        <div class="kpi__icon"><i class="ti ti-file-check"></i></div>
                        <div class="kpi__body">
                            <div class="lbl">Con contrato</div>
                            <div class="num" id="kpiContratos">0</div>
                        </div>
                    </div>
                    <div class="kpi kpi--mini kpi--accent kpi--click" id="kpiSinBox" title="Ver detalle de casos sin contrato">
                        <div class="kpi__icon"><i class="ti ti-file-off"></i></div>
                        <div class="kpi__body">
                            <div class="lbl">Sin contrato</div>
                            <div class="num" id="kpiSinContrato">0</div>
                        </div>
                    </div>
                    <div class="kpi kpi--mini kpi--click" data-ir-tipo="all" title="Ver en consulta detallada">
                        <div class="kpi__icon"><i class="ti ti-stack-2"></i></div>
                        <div class="kpi__body">
                            <div class="lbl">Total</div>
                            <div class="num" id="kpiClientes">0</div>
                        </div>
                    </div>
                    <div class="kpi kpi--mini kpi--clock">
                        <div class="kpi__icon"><i class="ti ti-clock-hour-4"></i></div>
                        <div class="kpi__body">
                            <div class="lbl">En tránsito</div>
                            <div class="num num--money" id="kpiTransito">$ 0.00</div>
                        </div>
                    </div>
                </div>

                <div class="charts-duo ra-charts">
                    <div class="chart-card chart-card--blue chart-card--trend" id="cardMensual">
                        <div class="chart-card__stripe"></div>
                        <div class="chart-card__body">
                            <div class="chart-card__head">
                                <div class="chart-card__title">
                                    <i class="ti ti-chart-line"></i>
                                    <span>Movimiento mensual</span>
                                </div>
                                <span class="chart-card__badge">Últimos 12 meses</span>
                            </div>
                            <p class="chart-card__subtitle">Abonos vs retiros por periodo</p>
                            <div class="chart-card__canvas chart-card__canvas--trend ra-chart-box">
                                <div class="ra-skeleton" id="skMensual"></div>
                                <div class="ra-empty" id="emptyMensual" style="display:none;">Sin movimientos en el periodo</div>
                                <canvas id="chrtMensual"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="chart-card chart-card--green" id="cardSucursal">
                        <div class="chart-card__stripe"></div>
                        <div class="chart-card__body">
                            <div class="chart-card__head">
                                <div class="chart-card__title">
                                    <i class="ti ti-building-bank"></i>
                                    <span>Acumulado por sucursal</span>
                                </div>
                                <span class="chart-card__badge">Top 10</span>
                            </div>
                            <p class="chart-card__subtitle">Saldo actual por sucursal · clic en barra para detalle</p>
                            <div class="chart-card__canvas chart-card__canvas--bar ra-chart-box">
                                <div class="ra-skeleton" id="skSucursal"></div>
                                <div class="ra-empty" id="emptySucursal" style="display:none;">Sin datos de sucursales</div>
                                <canvas id="chrtSucursal"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-card ra-panel" style="margin-bottom: 16px;">
                    <div class="head">
                        <h4><i class="ti ti-map-2"></i> Acumulado por sucursal</h4>
                        <small>Clic en una fila para ver el detalle</small>
                    </div>
                    <div class="body" style="padding: 0;">
                        <div class="table-wrap ra-table-scroll">
                            <div class="ra-skeleton ra-skeleton--table" id="skTblSuc"></div>
                            <div class="ra-empty" id="emptyTblSuc" style="display:none;">No hay sucursales con datos</div>
                            <table class="table table-hover" id="tblSucursales" style="margin: 0;">
                                <thead>
                                    <tr>
                                        <th class="th-text">Código</th>
                                        <th class="th-text">Sucursal</th>
                                        <th class="th-num">Contratos</th>
                                        <th class="th-monto">Abonos</th>
                                        <th class="th-monto">Ajustes</th>
                                        <th class="th-monto">Retiros</th>
                                        <th class="th-monto">Saldo actual</th>
                                        <th class="th-pct">% del total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="charts-duo ra-charts">
                    <div class="panel-card ra-panel">
                        <div class="head">
                            <h4><i class="ti ti-users"></i> Top ejecutivos</h4>
                            <small>Clic en una fila para ver cuentas</small>
                        </div>
                        <div class="body" style="padding: 0;">
                            <div class="table-wrap ra-table-scroll">
                                <div class="ra-skeleton ra-skeleton--table" id="skTblEje"></div>
                                <div class="ra-empty" id="emptyTblEje" style="display:none;">Sin ejecutivos para mostrar</div>
                                <table class="table table-hover" id="tblEjecutivos" style="margin: 0;">
                                    <thead>
                                        <tr>
                                            <th class="th-rank">#</th>
                                            <th class="th-text">Ejecutivo</th>
                                            <th class="th-num">Contratos</th>
                                            <th class="th-monto">Saldo</th>
                                            <th class="th-pct">% del total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="panel-card ra-panel">
                        <div class="head">
                            <h4><i class="ti ti-user-star"></i> Top clientes</h4>
                            <small>Mayor saldo actual</small>
                        </div>
                        <div class="body" style="padding: 0;">
                            <div class="table-wrap ra-table-scroll">
                                <div class="ra-skeleton ra-skeleton--table" id="skTblCli"></div>
                                <div class="ra-empty" id="emptyTblCli" style="display:none;">Sin clientes para mostrar</div>
                                <table class="table table-hover" id="tblClientes" style="margin: 0;">
                                    <thead>
                                        <tr>
                                            <th class="th-rank">#</th>
                                            <th class="th-text">Cliente</th>
                                            <th class="th-text">Sucursal</th>
                                            <th class="th-monto">Saldo</th>
                                            <th class="th-pct">% del total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="tab-detalle">
                <div class="detail-layout">
                    <aside class="detail-filters">
                        <div class="df-head">
                            <span><i class="fa fa-filter"></i> Filtros</span>
                            <a href="#" id="btnLimpiarDetalle">Limpiar</a>
                        </div>
                        <div class="df-body">
                            <div class="fg">
                                <label>Fecha de corte</label>
                                <input type="date" class="form-control" id="fFechaCorte" max="<?= date('Y-m-d'); ?>" value="<?= $fechaCorte; ?>">
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
                                <label>Ejecutivo</label>
                                <select class="form-control" id="fEjecutivo"><option value="">Todos</option></select>
                            </div>
                            <div class="df-actions">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="btnBuscarDetalle">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                                <button type="button" class="btn btn-success btn-sm btn-block" id="btnExcelDetalle">
                                    <i class="fa fa-file-excel-o"></i> Descargar Excel
                                </button>
                            </div>
                        </div>
                    </aside>
                    <div class="detail-main">
                        <div class="results-bar">
                            <div class="stats">
                                <div class="stat-item"><div class="n" id="resCount">0</div><div class="l">Registros</div></div>
                                <div class="stat-item"><div class="n" id="resSaldo">$0</div><div class="l">Saldo total</div></div>
                                <div class="stat-item"><div class="n" id="resProm">$0</div><div class="l">Promedio</div></div>
                            </div>
                        </div>
                        <div class="mod-filters" id="modFilters">
                            <button type="button" class="mod-btn active" data-tipo="all"><span class="dot" style="background:#888"></span> Todos</button>
                            <button type="button" class="mod-btn" data-tipo="con"><span class="dot" style="background:#2e7d52"></span> Con contrato</button>
                            <button type="button" class="mod-btn" data-tipo="sin"><span class="dot" style="background:#e8a020"></span> Sin contrato</button>
                        </div>
                        <div class="table-wrap">
                            <table id="tblDetalle" class="table table-hover" style="display:none;">
                                <thead>
                                    <tr>
                                        <th class="th-text">Crédito</th>
                                        <th class="th-text">Cliente</th>
                                        <th class="th-text">Sucursal</th>
                                        <th class="th-text">Ejecutivo</th>
                                        <th class="th-text">Apertura</th>
                                        <th class="th-monto">Abonos</th>
                                        <th class="th-monto">Retiros</th>
                                        <th class="th-monto">Saldo</th>
                                        <th class="th-monto">Tránsito</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div class="empty-state" id="emptyStateDetalle">
                                <i class="fa fa-search"></i>
                                <p><strong>Consulta no ejecutada</strong></p>
                                <p style="font-size:13px">Ajusta los filtros y pulsa <strong>Buscar</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detalleEjecutivoAhorro" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modal-title" id="ttlEjecutivoAhorro"></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
            </div>
            <div class="modal-body">
                <div class="modal-charts">
                    <div><canvas id="chrtEjeConteo"></canvas></div>
                    <div><canvas id="chrtEjeSaldo"></canvas></div>
                </div>
                <hr>
                <div style="text-align:center;margin-bottom:12px">
                    <button type="button" class="btn btn-success" id="btnExcelEjecutivo">
                        <i class="fa fa-file-excel-o"></i> Descargar Excel
                    </button>
                    <input type="hidden" id="xsl_ejecutivo" value="">
                    <input type="hidden" id="xsl_fechaCorte" value="">
                </div>
                <hr>
                <div class="table-wrap">
                    <table id="tblEjecutivoAhorro" class="table table-hover">
                        <thead>
                            <tr>
                                <th class="th-text">Crédito</th>
                                <th class="th-text">Cliente</th>
                                <th class="th-text">Sucursal</th>
                                <th class="th-text">Apertura</th>
                                <th class="th-monto">Abonos</th>
                                <th class="th-monto">Retiros</th>
                                <th class="th-monto">Saldo</th>
                                <th class="th-monto">Tránsito</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDrillAhorro" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalDrillTitulo">Detalle</h4>
            </div>
            <div class="modal-body">
                <div class="ra-modal-stats">
                    <div><strong id="modalDrillCount">0</strong> registros</div>
                    <div>Saldo <strong id="modalDrillSaldo">$ 0.00</strong></div>
                </div>
                <div class="table-wrap">
                    <table class="table table-hover" id="tblDrillAhorro">
                        <thead>
                            <tr>
                                <th class="th-text">Crédito</th>
                                <th class="th-text">Cliente</th>
                                <th class="th-text">Sucursal</th>
                                <th class="th-text">Apertura</th>
                                <th class="th-monto">Abonos</th>
                                <th class="th-monto">Saldo</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-sm" id="btnDrillIrConsulta">
                    <i class="fa fa-list"></i> Ir a consulta detallada
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .reporte-ahorro-page .kpi-grid {
        display: grid;
        gap: 10px;
        margin-bottom: 12px;
    }
    .reporte-ahorro-page .kpi-grid--hero {
        grid-template-columns: repeat(3, 1fr);
    }
    .reporte-ahorro-page .kpi-grid--mini {
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 16px;
    }
    .reporte-ahorro-page .kpi {
        position: relative;
        overflow: hidden;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        background: var(--surface-0, #fff);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 14px 16px;
        user-select: none;
        caret-color: transparent;
    }
    .reporte-ahorro-page .kpi::before {
        content: "";
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: var(--border);
    }
    .reporte-ahorro-page .kpi--ok::before { background: var(--ok); }
    .reporte-ahorro-page .kpi--blue::before { background: #2a78d6; }
    .reporte-ahorro-page .kpi--danger::before { background: var(--danger); }
    .reporte-ahorro-page .kpi--accent::before { background: var(--accent); }
    .reporte-ahorro-page .kpi--clock::before { background: var(--warn, #c9a227); }
    .reporte-ahorro-page .kpi__icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: var(--accent-soft);
        color: var(--accent);
        flex-shrink: 0;
        font-size: 18px;
    }
    .reporte-ahorro-page .kpi--ok .kpi__icon { background: var(--ok-soft, #e8f7ef); color: var(--ok); }
    .reporte-ahorro-page .kpi--blue .kpi__icon { background: #e8f2fb; color: #2a78d6; }
    .reporte-ahorro-page .kpi--danger .kpi__icon { background: #fdecea; color: var(--danger); }
    .reporte-ahorro-page .kpi--accent .kpi__icon { background: var(--accent-soft); color: var(--accent); }
    .reporte-ahorro-page .kpi--clock .kpi__icon { background: var(--warn-soft, #fff8e6); color: var(--warn, #b8860b); }
    .reporte-ahorro-page .kpi__body { min-width: 0; flex: 1; }
    .reporte-ahorro-page .kpi .lbl {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: var(--muted);
        font-weight: 600;
        margin-bottom: 4px;
    }
    .reporte-ahorro-page .kpi .num {
        font-size: 24px;
        font-weight: 700;
        line-height: 1.15;
        color: var(--dark);
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.3px;
    }
    .reporte-ahorro-page .kpi .num--money { font-size: 22px; }
    .reporte-ahorro-page .kpi-grid--mini .kpi .num { font-size: 20px; }
    .reporte-ahorro-page .kpi-grid--mini .kpi .num--money { font-size: 17px; }
    .reporte-ahorro-page .kpi-grid--mini .kpi__icon { width: 32px; height: 32px; font-size: 16px; border-radius: 8px; }
    .reporte-ahorro-page .kpi__sub {
        margin-top: 4px;
        font-size: 12px;
        color: var(--muted);
    }
    .reporte-ahorro-page .kpi--click { cursor: pointer; transition: transform .15s, box-shadow .15s; }
    .reporte-ahorro-page .kpi--click:hover { transform: translateY(-1px); box-shadow: var(--shadow); }

    .reporte-ahorro-page .ra-row-click { cursor: pointer; }
    .reporte-ahorro-page .ra-row-click:hover > td { background: var(--row-hover, #f3f6f9) !important; }
    .reporte-ahorro-page .ra-modal-stats {
        display: flex;
        gap: 24px;
        margin-bottom: 12px;
        font-size: 13px;
        color: var(--muted);
    }
    .reporte-ahorro-page .ra-modal-stats strong { color: var(--dark); }

    /* Modal ejecutivo (estilo Dashboard Operativo) */
    #detalleEjecutivoAhorro {
        --modal-bg: #fff;
        --modal-border: #e6ebf0;
        --modal-title: #1f2328;
        --modal-text: #2b2b2b;
        --modal-muted: #6b7280;
        --modal-row-odd: #f8fafc;
        --modal-row-even: #fff;
        --modal-row-hover: #eef2f6;
        --head-bg: #f8fafc;
        --dark: #2b2b2b;
        --muted: #738091;
        --border: #dde3ea;
        --row-hover: #f4f7fa;
        --code-bg: #f0f3f6;
    }
    #modalDrillAhorro {
        --head-bg: #f8fafc;
        --dark: #2b2b2b;
        --muted: #738091;
        --border: #dde3ea;
        --row-hover: #f4f7fa;
        --code-bg: #f0f3f6;
    }
    #detalleEjecutivoAhorro.modal { width: 100vw; height: 100vh; }
    #detalleEjecutivoAhorro .modal-dialog {
        width: 80%; height: 80%; margin: 5vh auto; max-width: none;
    }
    #detalleEjecutivoAhorro .modal-content {
        height: 100%; display: flex; flex-direction: column;
        background: var(--modal-bg); border: 1px solid var(--modal-border);
    }
    #detalleEjecutivoAhorro .modal-header {
        position: relative;
        display: block;
        border-bottom: 1px solid var(--modal-border);
        padding: 14px 48px 14px 18px;
    }
    #detalleEjecutivoAhorro .modal-title {
        display: block;
        font-size: 15px;
        color: var(--modal-title);
        margin: 0;
        font-weight: 600;
        padding-right: 8px;
        line-height: 1.4;
    }
    #detalleEjecutivoAhorro .modal-header .close,
    #modalDrillAhorro .modal-header .close {
        position: absolute;
        top: 8px;
        right: 12px;
        z-index: 2;
        margin: 0;
        padding: 4px 8px;
        float: none;
        color: var(--modal-muted, #6b7280);
        opacity: 1;
        text-shadow: none;
        font-size: 28px;
        font-weight: 400;
        line-height: 1;
    }
    #detalleEjecutivoAhorro .modal-header .close:hover,
    #modalDrillAhorro .modal-header .close:hover {
        color: var(--modal-title, #1f2328);
        opacity: 1;
    }
    #modalDrillAhorro .modal-header {
        position: relative;
        padding-right: 48px;
    }
    #detalleEjecutivoAhorro .modal-body {
        flex: 1; overflow: auto; padding: 16px 18px 24px;
    }
    #detalleEjecutivoAhorro .modal-charts {
        display: flex; flex-wrap: wrap; gap: 16px; justify-content: space-around;
    }
    #detalleEjecutivoAhorro .modal-charts > div { width: 42%; min-width: 280px; }
    #detalleEjecutivoAhorro hr { border-top: 1px solid var(--modal-border); margin: 12px 0; }
    #detalleEjecutivoAhorro .table { color: var(--modal-text); margin-bottom: 0; }
    #detalleEjecutivoAhorro .table > thead > tr > th {
        background: var(--head-bg, #f3f6f9); border-color: var(--modal-border) !important;
        color: var(--modal-muted); font-size: 11px; text-transform: uppercase;
    }
    #detalleEjecutivoAhorro .table > tbody > tr > td { border-color: var(--modal-border) !important; vertical-align: middle; }
    #detalleEjecutivoAhorro .table-striped > tbody > tr:nth-of-type(odd) { background: var(--modal-row-odd); }
    #detalleEjecutivoAhorro .table-striped > tbody > tr:nth-of-type(even) { background: var(--modal-row-even); }
    #detalleEjecutivoAhorro .table-hover > tbody > tr:hover { background: var(--modal-row-hover) !important; }
    #detalleEjecutivoAhorro .btn-success { background: #26b99a; border-color: #26b99a; }
    #detalleEjecutivoAhorro .btn-success:hover { background: #1fa386; border-color: #1fa386; }
    #detalleEjecutivoAhorro .table-wrap { border-color: var(--modal-border) !important; background: var(--modal-bg); }
    #detalleEjecutivoAhorro .dataTables_wrapper { margin-top: 8px; }
    #detalleEjecutivoAhorro .dataTables_length,
    #detalleEjecutivoAhorro .dataTables_filter,
    #detalleEjecutivoAhorro .dataTables_info,
    #detalleEjecutivoAhorro .dataTables_paginate { color: var(--modal-muted); margin-bottom: 8px; }
    #detalleEjecutivoAhorro .dataTables_filter input {
        background: var(--modal-bg); border: 1px solid var(--modal-border); color: var(--modal-text); border-radius: 6px;
    }
    #detalleEjecutivoAhorro .dataTables_length select {
        background: var(--modal-bg); border: 1px solid var(--modal-border); color: var(--modal-text); border-radius: 6px;
    }
    #detalleEjecutivoAhorro .dataTables_paginate .paginate_button { color: var(--modal-muted) !important; }
    #detalleEjecutivoAhorro .dataTables_paginate .paginate_button.current {
        background: var(--accent, #2a78d6) !important; border-color: var(--accent, #2a78d6) !important; color: #fff !important;
    }

    .reporte-ahorro-page .ra-charts { margin-bottom: 16px; }
    .reporte-ahorro-page .ra-charts .chart-card {
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    .reporte-ahorro-page .ra-charts .chart-card__body {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
    }
    .reporte-ahorro-page .ra-charts .chart-card__canvas--trend,
    .reporte-ahorro-page .ra-charts .chart-card__canvas--bar {
        flex: 1 1 auto;
        height: 320px;
        min-height: 320px;
    }
    .reporte-ahorro-page .ra-charts .chart-card__canvas canvas {
        display: block;
        width: 100% !important;
        height: 100% !important;
    }
    .reporte-ahorro-page .ra-chart-box { position: relative; }
    .reporte-ahorro-page .ra-table-scroll {
        border: none; box-shadow: none; border-radius: 0;
        overflow-x: auto; -webkit-overflow-scrolling: touch;
    }
    .reporte-ahorro-page .ra-skeleton {
        position: absolute; inset: 8px;
        border-radius: 8px;
        background: linear-gradient(90deg, var(--surface-0, #f0f3f6) 25%, var(--border, #e6ebf0) 37%, var(--surface-0, #f0f3f6) 63%);
        background-size: 400% 100%;
        animation: raShimmer 1.2s ease-in-out infinite;
        z-index: 2;
        display: none;
    }
    .reporte-ahorro-page .ra-skeleton--table {
        position: relative; inset: auto;
        height: 120px; margin: 12px 16px; display: none;
    }
    .reporte-ahorro-page.is-loading .ra-skeleton { display: block; }
    .reporte-ahorro-page.is-loading #tab-resumen canvas,
    .reporte-ahorro-page.is-loading #tab-resumen table { opacity: .25; }
    .reporte-ahorro-page .ra-empty {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        color: var(--muted); font-size: 13px; z-index: 1;
        background: var(--surface-0, #f8fafc);
        border-radius: 8px;
        border: 1px dashed var(--border);
        margin: 4px;
    }
    .reporte-ahorro-page .ra-panel .ra-empty {
        position: relative;
        min-height: 80px;
        margin: 12px 16px;
    }
    @keyframes raShimmer {
        0% { background-position: 100% 0; }
        100% { background-position: 0 0; }
    }
    .reporte-ahorro-page .panel-card .head h4 .ti {
        margin-right: 6px; vertical-align: -2px; color: var(--accent);
    }

    /* Tablas — estilo Dashboard Operativo */
    .reporte-ahorro-page .table-wrap table,
    #detalleEjecutivoAhorro .table-wrap table,
    #modalDrillAhorro table { margin: 0; font-size: 13px; }
    .reporte-ahorro-page .table-wrap thead th,
    #detalleEjecutivoAhorro .table-wrap thead th,
    #modalDrillAhorro thead th {
        background: var(--head-bg);
        border-bottom: 2px solid var(--border) !important;
        color: var(--muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .3px;
        font-weight: 600;
        white-space: nowrap;
        vertical-align: middle;
        padding: 11px 12px;
    }
    .reporte-ahorro-page .th-text,
    #detalleEjecutivoAhorro .th-text,
    #modalDrillAhorro .th-text { text-align: left !important; }
    .reporte-ahorro-page .th-rank,
    .reporte-ahorro-page .th-num,
    .reporte-ahorro-page .th-pct,
    .reporte-ahorro-page .th-monto,
    #detalleEjecutivoAhorro .th-rank,
    #detalleEjecutivoAhorro .th-num,
    #detalleEjecutivoAhorro .th-pct,
    #detalleEjecutivoAhorro .th-monto,
    #modalDrillAhorro .th-rank,
    #modalDrillAhorro .th-num,
    #modalDrillAhorro .th-pct,
    #modalDrillAhorro .th-monto { text-align: center !important; }
    .reporte-ahorro-page .table-wrap tbody td,
    #detalleEjecutivoAhorro .table-wrap tbody td,
    #modalDrillAhorro tbody td {
        vertical-align: middle;
        padding: 10px 12px;
        border-color: var(--border);
    }
    .reporte-ahorro-page .table-wrap tbody tr:hover,
    #detalleEjecutivoAhorro .table-wrap tbody tr:hover,
    #modalDrillAhorro tbody tr:hover { background: var(--row-hover); }
    .reporte-ahorro-page .cell-main,
    #detalleEjecutivoAhorro .cell-main,
    #modalDrillAhorro .cell-main { font-weight: 500; color: var(--dark); line-height: 1.25; }
    .reporte-ahorro-page .cell-sub,
    #detalleEjecutivoAhorro .cell-sub,
    #modalDrillAhorro .cell-sub {
        font-size: 11px; color: var(--muted); margin-top: 2px; line-height: 1.2;
        white-space: normal; word-break: break-word;
    }
    .reporte-ahorro-page .cell-num,
    #detalleEjecutivoAhorro .cell-num,
    #modalDrillAhorro .cell-num {
        text-align: center; white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    .reporte-ahorro-page .cell-monto,
    #detalleEjecutivoAhorro .cell-monto,
    #modalDrillAhorro .cell-monto {
        font-weight: 600; text-align: center !important; white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    .reporte-ahorro-page .cell-pct,
    #detalleEjecutivoAhorro .cell-pct,
    #modalDrillAhorro .cell-pct {
        text-align: center; white-space: nowrap; color: var(--muted); font-size: 12px;
        font-variant-numeric: tabular-nums;
    }
    .reporte-ahorro-page .cell-credito,
    #detalleEjecutivoAhorro .cell-credito,
    #modalDrillAhorro .cell-credito {
        font-family: Consolas, "SF Mono", monospace; font-size: 12px;
    }
    .reporte-ahorro-page .cell-user code,
    #detalleEjecutivoAhorro .cell-user code,
    #modalDrillAhorro .cell-user code {
        background: var(--code-bg); padding: 1px 6px; border-radius: 4px;
        font-size: 11px; font-weight: 600;
    }
    .reporte-ahorro-page .ra-rank { width: 44px; text-align: center; }
    .reporte-ahorro-page .ra-rank__n {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 22px; height: 22px; padding: 0 6px;
        border-radius: 999px; font-size: 11px; font-weight: 700;
        background: var(--accent-soft); color: var(--accent);
    }
    .reporte-ahorro-page .dataTables_wrapper { padding: 8px 12px 12px; }
    .reporte-ahorro-page .dataTables_wrapper .table > thead > tr > th {
        background: var(--head-bg);
        border-bottom: 2px solid var(--border) !important;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .3px;
        color: var(--muted);
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        vertical-align: middle;
        padding: 11px 12px;
        background-image: none !important;
    }
    .reporte-ahorro-page .dataTables_wrapper .table > thead > tr > th.th-text { text-align: left !important; }
    .reporte-ahorro-page .dataTables_wrapper .table > thead > tr > th.th-rank,
    .reporte-ahorro-page .dataTables_wrapper .table > thead > tr > th.th-num,
    .reporte-ahorro-page .dataTables_wrapper .table > thead > tr > th.th-pct,
    .reporte-ahorro-page .dataTables_wrapper .table > thead > tr > th.th-monto { text-align: center !important; }
    #detalleEjecutivoAhorro .dataTables_wrapper .table > thead > tr > th.th-text,
    #modalDrillAhorro .dataTables_wrapper .table > thead > tr > th.th-text { text-align: left !important; }
    #detalleEjecutivoAhorro .dataTables_wrapper .table > thead > tr > th.th-monto,
    #modalDrillAhorro .dataTables_wrapper .table > thead > tr > th.th-monto { text-align: center !important; }
    .reporte-ahorro-page .dataTables_wrapper .table > tbody > tr:hover { background: var(--row-hover); }
    .reporte-ahorro-page .dataTables_wrapper .table td { vertical-align: middle; }

    .reporte-ahorro-page #tab-detalle .dataTables_wrapper { padding: 0 0 8px; }
    .reporte-ahorro-page #tab-detalle .detail-main .table-wrap {
        overflow-x: hidden;
        max-width: 100%;
    }
    .reporte-ahorro-page #tblDetalle,
    .reporte-ahorro-page #tblDetalle_wrapper table.dataTable {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        table-layout: fixed;
    }
    .reporte-ahorro-page #tblDetalle > thead > tr > th:nth-child(1),
    .reporte-ahorro-page #tblDetalle_wrapper table.dataTable thead th:nth-child(1) { width: 9%; }
    .reporte-ahorro-page #tblDetalle > thead > tr > th:nth-child(2),
    .reporte-ahorro-page #tblDetalle_wrapper table.dataTable thead th:nth-child(2) { width: 18%; }
    .reporte-ahorro-page #tblDetalle > thead > tr > th:nth-child(3),
    .reporte-ahorro-page #tblDetalle_wrapper table.dataTable thead th:nth-child(3) { width: 12%; }
    .reporte-ahorro-page #tblDetalle > thead > tr > th:nth-child(4),
    .reporte-ahorro-page #tblDetalle_wrapper table.dataTable thead th:nth-child(4) { width: 16%; }
    .reporte-ahorro-page #tblDetalle > thead > tr > th:nth-child(5),
    .reporte-ahorro-page #tblDetalle_wrapper table.dataTable thead th:nth-child(5) { width: 9%; }
    .reporte-ahorro-page #tblDetalle > thead > tr > th:nth-child(n+6),
    .reporte-ahorro-page #tblDetalle_wrapper table.dataTable thead th:nth-child(n+6) { width: 9%; text-align: center; }
    .reporte-ahorro-page #tblDetalle > tbody > tr > td,
    .reporte-ahorro-page #tblDetalle_wrapper table.dataTable tbody td {
        padding: 9px 10px;
        white-space: normal;
    }
    .reporte-ahorro-page #tblDetalle .cell-monto,
    .reporte-ahorro-page #tblDetalle_wrapper .cell-monto { text-align: center !important; white-space: nowrap; }
    .reporte-ahorro-page #tab-detalle .detail-main .dataTables_wrapper {
        overflow-x: hidden;
        max-width: 100%;
    }
    .reporte-ahorro-page.is-detalle .toolbar#toolbarResumen { display: none; }

    @media (prefers-color-scheme: dark) {
        #detalleEjecutivoAhorro {
            --modal-bg: #161b22;
            --modal-border: #30363d;
            --modal-title: #e6edf3;
            --modal-text: #c9d1d9;
            --modal-muted: #8b949e;
            --modal-row-odd: #1c2128;
            --modal-row-even: #161b22;
            --modal-row-hover: #262c36;
            --head-bg: #21262d;
        }
        .reporte-ahorro-page .kpi {
            background: var(--surface-0, #1f2328);
            border-color: var(--border, #3d444d);
        }
        .reporte-ahorro-page .kpi .num { color: var(--dark, #e6edf3); }
        .reporte-ahorro-page .kpi--ok .kpi__icon { background: #163226; color: #3fb950; }
        .reporte-ahorro-page .kpi--blue .kpi__icon { background: #1a2f45; color: #79c0ff; }
        .reporte-ahorro-page .kpi--danger .kpi__icon { background: #3d1f1f; color: #f85149; }
        .reporte-ahorro-page .kpi--clock .kpi__icon { background: #3d3420; color: #d4a72c; }
        .reporte-ahorro-page .cell-main,
        #detalleEjecutivoAhorro .cell-main,
        #modalDrillAhorro .cell-main { color: #e6edf3; }
        .reporte-ahorro-page .cell-user code,
        #detalleEjecutivoAhorro .cell-user code,
        #modalDrillAhorro .cell-user code {
            background: #21262d; color: #79c0ff;
        }
        .reporte-ahorro-page .ra-rank__n { background: #1a2f45; color: #79c0ff; }
        .reporte-ahorro-page .ra-row-click:hover > td {
            background: var(--row-hover, #262c33) !important;
        }
        .reporte-ahorro-page .ra-modal-stats strong { color: #e6edf3; }
        .reporte-ahorro-page .ra-empty {
            background: var(--surface-0, #1f2328);
            color: var(--muted, #8b949e);
            border-color: var(--border, #3d444d);
        }
        .reporte-ahorro-page .ra-skeleton {
            background: linear-gradient(90deg, #252930 25%, #30363d 37%, #252930 63%);
            background-size: 400% 100%;
        }
        .reporte-ahorro-page .ra-chart-box { background: transparent; }
        .reporte-ahorro-page .table-wrap thead th,
        .reporte-ahorro-page .dataTables_wrapper .table > thead > tr > th {
            background: var(--head-bg, #252930);
            color: var(--muted, #8b949e);
            border-color: var(--border, #3d444d) !important;
        }
        .reporte-ahorro-page .dataTables_wrapper .table > tbody > tr > td,
        .reporte-ahorro-page .table-wrap tbody td {
            color: var(--input-text, #c9d1d9);
            border-color: var(--border, #3d444d);
        }
    }

    @media (max-width: 1100px) {
        .reporte-ahorro-page .kpi-grid--hero { grid-template-columns: 1fr; }
        .reporte-ahorro-page .kpi-grid--mini { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 700px) {
        .reporte-ahorro-page .charts-duo { grid-template-columns: 1fr; }
        .reporte-ahorro-page .kpi .num { font-size: 20px; }
        .reporte-ahorro-page .kpi .num--money { font-size: 18px; }
        .reporte-ahorro-page .kpi-grid--mini { grid-template-columns: 1fr 1fr; }
    }
</style>

<script>
    window.RA_CATALOGO = <?= $catalogoJson ?? '{"regiones":[],"sucursales":[]}'; ?>;
</script>
<?= $footer; ?>
