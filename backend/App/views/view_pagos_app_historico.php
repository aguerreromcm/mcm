<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Reimprimir Recibo de Efectivo <span class="fa fa-print"></span></h3>
                <p class="text-muted">Mostrando todos los registros de los últimos 2 días (ayer y hoy). Use el buscador a la derecha para filtrar.</p>
            </div>
            <div class="card col-md-12">
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="tbl-historico">
                        <thead>
                            <tr>
                                <th>Código de Barras</th>
                                <th>Sucursal</th>
                                <th>Pagos Cobrados</th>
                                <th>Ejecutivo</th>
                                <th>Cobro</th>
                                <th>Pagos</th>
                                <th>Multas</th>
                                <th>Ref</th>
                                <th>Des</th>
                                <th>Gar</th>
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