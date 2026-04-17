<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Reporte Acreditado</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-4">
                            <div class="form-group">
                                <label for="creditoAcreditado">Crédito</label>
                                <input class="form-control" type="text" id="creditoAcreditado" maxlength="20" autocomplete="off" placeholder="Ej. 014448">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-primary" id="btnConsultarAcreditado">
                                        <span class="fa fa-search">&nbsp;</span>Consultar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultadoAcreditado">
            <hr>
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="reporteAcreditado">
                    <thead>
                        <tr>
                            <th>Crédito</th>
                            <th>Ciclo</th>
                            <th>Plazo</th>
                            <th>Tasa</th>
                            <th>Avales</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Liquidación</th>
                            <th>Días atraso</th>
                            <th>Monto</th>
                            <th>Garantía</th>
                            <th>Cartera</th>
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
