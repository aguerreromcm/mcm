<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta de Solicitudes Cultiva</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione una fecha para descargar el reporte</h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/Cultiva/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2"  autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $Inicial; ?>">
                                    <span id="availability1" >Desde</span>
                                </div>

                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2"  autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $Final; ?>">
                                    <span id="availability1" >Hasta</span>
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-default" type="submit">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card col-md-12">
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
                <form name="all" id="all" method="POST">
                    <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                            <tr>
                                <th>Sucursal</th>
                                <th>Credito</th>
                                <th>Nombre del Grupo</th>
                                <th>Ciclo</th>
                                <th>Nombre del Cliente</th>
                                <th>Direccion del Cliente</th>
                                <th>Fecha Solicitud</th>
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
