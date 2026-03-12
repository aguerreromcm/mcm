<?php
$fechaCorte = date('Y-m-d');
?>

<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Reporte Interés Devengado</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="date" id="fechaCorte" value="<?= $fechaCorte ?>">
                                <span>Fecha de corte</span>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <div class="form-group">
                                <select class="form-control" id="situacionCredito">
                                    <option value="E">Entregado</option>
                                    <option value="L">Liquidado</option>
                                    <option value="*">Ambos</option>
                                </select>
                                <span>Situación del crédito</span>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary" id="btnConsultarDevengo">
                                    <span class="fa fa-search">&nbsp;</span>Generar Reporte
                                </button>
                                <span>&nbsp;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultadoDevengo">
            <div class="botones">
                <button type="button" class="btn btn-success" id="btnExcelDevengo">
                    <span class="fa fa-file-excel-o">&nbsp;</span>Exportar a Excel
                </button>
            </div>
            <hr>
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="reporteDevengo">
                    <thead>
                        <tr>
                            <th>Crédito</th>
                            <th>Ciclo</th>
                            <th>Situación</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Plazo (días)</th>
                            <th>Devengo diario</th>
                            <th>Interés total</th>
                            <th>Días transcurridos</th>
                            <th>Devengo transcurrido</th>
                            <th>Días registrados</th>
                            <th>Devengo registrado</th>
                            <th>Días diferencia</th>
                            <th>Devengo diferencia</th>
                            <th>Fecha liquidación</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>