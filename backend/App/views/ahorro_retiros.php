<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Retiros de ahorro</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" type="date" id="fechaI">
                                <span>Fecha inicial</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" type="date" id="fechaF">
                                <span>Fecha final</span>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group" style="text-align: right;">
                                <button type="button" class="btn btn-success" id="btnDescargarReporte" style="margin-top: 0;">
                                    <span class="fa fa-file-excel-o">&nbsp;</span>Descargar reporte en Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultado">
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="retiros">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Región</th>
                            <th>Sucursal</th>
                            <th>Crédito</th>
                            <th>Monto</th>
                            <th style="word-wrap: break-word; width: 150px;">Fecha de registro</th>
                            <th style="word-wrap: break-word; width: 150px;">Fecha de entrega programada</th>
                            <th style="word-wrap: break-word; width: 150px;">Fecha de devolución</th>
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

<?= $footer; ?>