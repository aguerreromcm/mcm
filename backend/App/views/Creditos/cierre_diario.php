<?= $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3>Situación Cartera MCM</h3>
            <div class="clearfix"></div>
        </div>

        <div class="card card-danger col-md-5">
            <div class="card-header">
                <h5 class="card-title">Ingrese una fecha a generar</h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <input class="form-control mr-sm-2" type="date" id="fecha" name="fecha" value="<?= $fecha; ?>" max="<?= $fecha; ?>">
                        <span id="availability1" style="font-size:15px">Día</span>
                    </div>
                    <div class="col-md-4">
                        <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle" onclick=descarga()><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>