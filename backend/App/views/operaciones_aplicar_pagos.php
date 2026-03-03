<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3>Aplicar Pagos</h3>
            <div class="clearfix"></div>
        </div>
        <div class="card card-danger col-md-5">
            <div class="card-header">
                <h5 class="card-title">Seleccione la fecha a procesar (misma información que Layout Contable)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <input class="form-control mr-sm-4" type="date" id="fechaAplicar" name="fechaAplicar" value="<?php echo isset($fechaActual) ? $fechaActual : date('Y-m-d'); ?>" aria-label="Fecha">
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-default" id="btnConsultarAplicar">Consultar</button>
                        <button type="button" class="btn btn-primary" id="btnAplicarPagos">Aplicar pagos</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card col-md-12">
            <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
            <div class="row" id="resumenAplicarPagos" style="margin-bottom: 15px;">
                <div class="tile_count col-sm-12" style="margin-bottom: 8px; margin-top: 8px;">
                    <div class="col-md-2 col-sm-4 tile_stats_count">
                        <span class="count_top" style="font-size: 14px"><i class="fa fa-clock-o"></i> Pendientes</span>
                        <div class="count" style="font-size: 18px" id="totalPagosPendientes">0</div>
                        <span class="count_bottom" id="importePendientes">$ 0.00</span>
                    </div>
                    <div class="col-md-2 col-sm-4 tile_stats_count">
                        <span class="count_top" style="font-size: 14px"><i class="fa fa-check"></i> Aplicados</span>
                        <div class="count" style="font-size: 18px" id="totalPagosAplicados">0</div>
                        <span class="count_bottom" id="importeAplicados">$ 0.00</span>
                    </div>
                    <div class="col-md-2 col-sm-4 tile_stats_count">
                        <span class="count_top" style="font-size: 14px"><i class="fa fa-list"></i> Total</span>
                        <div class="count" style="font-size: 18px" id="totalPagos">0</div>
                        <span class="count_bottom" id="importeTotal">$ 0.00</span>
                    </div>
                    <div class="col-md-2 col-sm-4 tile_stats_count">
                        <span class="count_top" style="font-size: 14px"><i class="fa fa-info-circle"></i> Estado</span>
                        <div class="count" style="font-size: 14px" id="estadoAplicar">-</div>
                    </div>
                    <div class="col-md-2 col-sm-4 tile_stats_count">
                        <span class="count_top" style="font-size: 14px"><i class="fa fa-calendar"></i> Fecha aplicación</span>
                        <div class="count" style="font-size: 14px" id="fechaAplicacion">-</div>
                    </div>
                </div>
            </div>
            <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
            <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover" id="tablaAplicarPagos">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Referencia</th>
                            <th>Monto</th>
                            <th>Moneda</th>
                        </tr>
                    </thead>
                    <tbody id="tablaAplicarPagosBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
