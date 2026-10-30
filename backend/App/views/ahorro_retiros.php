<?= $header; ?>

<div class="right_col">
    <div class="panel panel-default folios-tarjeta-page pagos-gl-page pagos-post-cierre-page">
        <div class="panel-body">
            <div class="x_title">
                <h3>Retiros de ahorro</h3>
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
                        <p>Muestra los retiros con entrega programada en el rango de fechas. Debajo de cada monto se indica si se paga con devengo, con depósitos o con ambos.</p>
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
                        <h4><i class="fa fa-money"></i> Solicitudes de retiro</h4>
                        <div class="pgl-head-badges">
                            <span class="ft-conteo" id="lblRegistros"></span>
                        </div>
                    </div>
                    <div class="body">
                        <div class="ppc-tabla" style="overflow-x: auto;">
                            <table class="table table-striped table-bordered table-hover" id="retiros" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Región</th>
                                        <th>Sucursal</th>
                                        <th>Crédito</th>
                                        <th>Monto</th>
                                        <th>Fecha de registro</th>
                                        <th>Fecha de entrega programada</th>
                                        <th>Fecha de devolución</th>
                                        <th>Estatus</th>
                                        <th>Administradora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>

<style>
    .pagos-post-cierre-page #retiros > thead > tr > th,
    .pagos-post-cierre-page #retiros > tbody > tr > td {
        padding: 8px 5px;
        white-space: normal;
        word-break: normal;
        overflow-wrap: normal;
    }

    .pagos-post-cierre-page #retiros > thead > tr > th {
        font-size: 10px;
        letter-spacing: 0;
    }

    .pagos-post-cierre-page #retiros > tbody > tr > td {
        font-size: 12px;
    }

    /* Los renglones de DataTables traen margen negativo y generan desplazamiento horizontal */
    .pagos-post-cierre-page #retiros_wrapper > .row {
        margin-left: 0;
        margin-right: 0;
    }

    .pagos-post-cierre-page #retiros_wrapper > .row > [class*="col-"] {
        padding-left: 0;
        padding-right: 0;
    }

    .pagos-post-cierre-page #retiros_filter input {
        width: 150px;
        margin-right: 0;
    }

    /* DataTables fija anchos en línea al dibujar, se liberan para que se ajusten al contenido */
    .pagos-post-cierre-page #retiros > thead > tr > th {
        width: auto !important;
    }
</style>
