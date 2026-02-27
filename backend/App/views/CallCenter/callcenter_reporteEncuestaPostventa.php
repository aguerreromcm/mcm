<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="overflow: auto;">
        <div class="x_title">
            <h3>Reporte Postventa</h3>
            <div class="clearfix"></div>
        </div>
        <div class="contenedor-card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <span class="card-title">Ingrese los parámetros para generar el reporte</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <input class="form-control mr-sm-2" type="date" id="fechaI" value="<?php echo $fecha; ?>" min="2024-01-01" max="<?php echo $fecha; ?>">
                        <span>Desde</span>
                    </div>
                    <div class="col-md-2">
                        <input class="form-control mr-sm-2" type="date" id="fechaF" value="<?php echo $fecha; ?>" min="2024-01-01" max="<?php echo $fecha; ?>">
                        <span>Hasta</span>
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" id="estatus">
                            <?= $estatus ?>
                        </select>
                        <span>Estatus</span>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="button" id="buscar"><i class="glyphicon glyphicon-search"></i> Buscar</button>
                    </div>
                </div>
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
                <div class="row">
                    <div class="col-md-3">
                        <button id="descargar" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"></i> <b>Exportar a Excel</b></button>
                    </div>
                </div>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="reporte">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Ciclo</th>
                                <th>Teléfono</th>
                                <th>Fecha</th>
                                <th>Asesor</th>
                                <th>Estatus</th>
                                <th>Motivo</th>
                                <th>Comentario del asesor</th>
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

<?php echo $footer; ?>