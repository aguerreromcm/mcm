<?php
$fechaI = date('Y-m-d', strtotime('-3 days'));
if (date('N', strtotime($fechaI)) == 1 || date('N', strtotime($fechaI)) == 7) {
    $fechaI = date('Y-m-d', strtotime('-2 days', strtotime($fechaI)));
} elseif (date('N', strtotime($fechaI)) == 6) {
    $fechaI = date('Y-m-d', strtotime('-1 day', strtotime($fechaI)));
}

$fechaF = date('Y-m-d', strtotime('+3 days'));
if (date('N', strtotime($fechaF)) == 1 || date('N', strtotime($fechaF)) == 7) {
    $fechaF = date('Y-m-d', strtotime('+2 days', strtotime($fechaF)));
} elseif (date('N', strtotime($fechaF)) == 6) {
    $fechaF = date('Y-m-d', strtotime('+1 day', strtotime($fechaF)));
}

?>

<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Reporte Consolidado Clientes y Avales</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" type="date" id="fechaI" value="<?= $fechaI ?>">
                                <span>Fecha inicial</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" type="date" id="fechaF" value="<?= $fechaF ?>">
                                <span>Fecha final</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultado">
            <div class="botones">
                <button type="button" class="btn btn-success" id="excel">
                    <span class="fa fa-file-excel-o">&nbsp;</span>Exportar a Excel
                </button>
            </div>
            <hr>
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="reporte">
                    <thead>
                        <tr>
                            <th>Crédito</th>
                            <th>Ciclo</th>
                            <th>Plazo</th>
                            <th>Tasa</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Entregado</th>
                            <th>Total + interes</th>
                            <th># Cliente</th>
                            <th>Nombre Completo</th>
                            <th>Telefono</th>
                            <th>Dirección</th>
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