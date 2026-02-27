<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3> Generador de Layouts</h3>
            <div class="clearfix"></div>
        </div>
        <div class="card card-danger col-md-5">
            <div class="card-header">
                <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
            </div>
            <div class="card-body">
                <form class="" action="/Pagos/Layout/" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control mr-sm-4" autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $Inicial; ?>">
                            <span id="availability1"></span>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control mr-sm-4" autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $Final; ?>">
                            <span id="availability1"></span>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-default" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card col-md-12">
            <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
            <form name="all" id="all" method="POST">
                <button id="export_excel" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Referencia</th>
                                <th>Monto</th>
                                <th>Moneda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $tabla; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<?php echo $footer; ?>