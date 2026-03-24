<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3>Módulo de Conciliación de Pagos</h3>
            <div class="clearfix"></div>
        </div>

        <div class="card card-danger col-md-12">
            <div class="card-header">
                <h5 class="card-title">Criterios de búsqueda</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <label for="fechaConciliacion">Fecha de pago</label>
                        <input class="form-control" type="date" id="fechaConciliacion" name="fechaConciliacion" value="" aria-label="Fecha de pago">
                    </div>
                    <div class="col-md-2">
                        <label for="codigoConciliacion">Crédito</label>
                        <input class="form-control" type="text" id="codigoConciliacion" name="codigoConciliacion" maxlength="6" value="" placeholder="Crédito" aria-label="Crédito">
                    </div>
                    <div class="col-md-2">
                        <label for="cicloConciliacion">Ciclo</label>
                        <input class="form-control" type="text" id="cicloConciliacion" name="cicloConciliacion" placeholder="Ciclo" aria-label="Ciclo">
                    </div>
                    <div class="col-md-2">
                        <label for="ctaBancariaConciliacion">Cta. bancaria</label>
                        <input class="form-control" type="text" id="ctaBancariaConciliacion" name="ctaBancariaConciliacion" value="" placeholder="00" aria-label="Cuenta bancaria">
                    </div>
                    <div class="col-md-2">
                        <label for="btnConsultarConciliacion" style="display: block; height: 20px; margin-bottom: 5px;">&nbsp;</label>
                        <button type="button" class="btn btn-primary" id="btnConsultarConciliacion">Buscar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card col-md-12">
            <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
            <div class="row" id="resumenConciliacion" style="margin-bottom: 15px;">
                <div class="tile_count col-sm-12" style="margin-bottom: 8px; margin-top: 8px;">
                    <div class="col-md-2 col-sm-4 tile_stats_count">
                        <span class="count_top" style="font-size: 14px"><i class="fa fa-list"></i> Total de Pagos Pendientes</span>
                        <div class="count" style="font-size: 18px" id="totalPagos">0</div>
                        <span class="count_bottom" id="importeTotal">$ 0.00</span>
                    </div>
                </div>
            </div>
            <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
            <div class="row" style="margin-bottom: 10px;">
                <div class="col-md-12">
                    <button type="button" class="btn btn-success" id="btnConciliarPagos">Conciliar pagos</button>
                </div>
            </div>
            <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover" id="tablaConciliacion">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="chkTodosConciliacion" title="Seleccionar todos" /></th>
                            <th>No.</th>
                            <th>Empresa</th>
                            <th>Fecha de Pago</th>
                            <th>Referencia</th>
                            <th>Tipo Cte.</th>
                            <th>Crédito (Ind./Gpo.)</th>
                            <th>Ciclo</th>
                            <th>Periodo</th>
                            <th>SecuenciaIM</th>
                            <th>Nombre (Ind./Gpo.)</th>
                            <th>Monto</th>
                            <th>Cta. Bancaria</th>
                            <th>Código Gpo.</th>
                            <th>Tasa</th>
                            <th>SecuenciaMP</th>
                            <th>Plazo</th>
                            <th>Periodicidad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tablaConciliacionBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
