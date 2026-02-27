<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Recepción de pagos capturados en Campo (App) <span class="fa fa-mobile"></span></h3>
                <div class="clearfix"></div>
            </div>
            <div class="card col-md-12">
                <div class="card card-danger col-md-12">
                    <ul class="nav navbar-nav navbar-right">
                        <b style="font-size: 20px; color: red;">Pagos capturados en campo por los ejecutivos</b>
                        <br>
                        <br>
                        <br>
                        <br>
                    </ul>
                </div>


                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                            <tr>
                                <th>Código de Barras</th>
                                <th>Sucursal</th>
                                <th>Pagos Cobrados</th>
                                <th>Ejecutivo</th>
                                <th>Fecha</th>
                                <th>Pagos</th>
                                <th>Multas</th>
                                <th>Ahorro</th>
                                <th>Monto Total Recolectado (Entregar)</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $tabla; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>