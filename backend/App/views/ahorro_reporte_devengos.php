<?= $header; ?>

<div class="right_col">
    <div class="panel panel-default folios-tarjeta-page pagos-gl-page pagos-post-cierre-page">
        <div class="panel-body">
            <div class="x_title">
                <h3>Devengos de ahorro</h3>
                <div class="clearfix"></div>
            </div>

            <div class="panel-card ft-toolbar">
                <div class="ft-toolbar-inner">
                    <div class="ft-search">
                        <label class="ft-tb-lbl" for="fechaI">
                            <i class="fa fa-calendar"></i> Fecha inicial
                        </label>
                        <div class="ft-search-line">
                            <input class="form-control" type="date" id="fechaI">
                        </div>
                    </div>
                    <div class="ft-search">
                        <label class="ft-tb-lbl" for="fechaF">
                            <i class="fa fa-calendar"></i> Fecha final
                        </label>
                        <div class="ft-search-line">
                            <input class="form-control" type="date" id="fechaF">
                        </div>
                    </div>
                    <div class="ft-toolbar-sep" aria-hidden="true"></div>
                    <div class="ft-toolbar-hint">
                        <p>Consulta el movimiento del devengo de ahorro: los intereses anuales abonados a las cuentas y la parte de devengo que se entregó a los clientes en sus retiros.</p>
                    </div>
                    <div class="ft-toolbar-actions">
                        <button type="button" class="ft-btn-report" id="btnDescargarReporte" title="Descargar el reporte del rango seleccionado">
                            <span class="ft-btn-report-icon"><i class="fa fa-file-excel-o"></i></span>
                            <span class="ft-btn-report-text">
                                <strong>Descargar Excel</strong>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="resultado">
                <div class="panel-card">
                    <div class="head">
                        <h4><i class="fa fa-percent"></i> Devengos del <span id="lblRango">—</span></h4>
                        <div class="pgl-head-badges">
                            <span class="ft-conteo pgl-badge-ok">Depositado:&nbsp;<b id="totalDepositado">$ 0.00</b></span>
                            <span class="ft-conteo pgl-badge-danger">Retirado:&nbsp;<b id="totalRetirado">$ 0.00</b></span>
                        </div>
                    </div>
                    <div class="body">
                        <div class="ppc-tabla">
                            <table class="table table-striped table-bordered table-hover" id="devengos" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Crédito</th>
                                        <th>Cliente</th>
                                        <th>Sucursal</th>
                                        <th>Descripción</th>
                                        <th>Depositado</th>
                                        <th>Retirado</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>
