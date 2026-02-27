<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Reporte de Usuarios SICAFIN CULTIVA</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">
                <form name="all" id="all" method="POST">
                    <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                            <tr>
                                <th>COD Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Fecha De Alta</th>
                                <th>Codigo De Sucursal</th>
                                <th>Sucursal</th>
                                <th>Nomina</th>
                                <th>Nomina Jefe</th>
                                <th>Activo</th>
                                <th>Puesto</th>
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
</div>

<?php echo $footer; ?>
