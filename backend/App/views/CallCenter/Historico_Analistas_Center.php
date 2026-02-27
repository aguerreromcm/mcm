<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta de las Llamadas Finalizadas y Pendientes de todas las Analistas</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el rango de fechas a generar el reporte</h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/CallCenter/HistoricoAnalistas/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2"  autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $Inicial; ?>" min="" max="<?php echo $Final; ?>">
                                    <span id="availability1">Desde</span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $Final; ?>" min="" max="<?php echo $Final; ?>">
                                    <span id="availability1">Hasta</span>
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
                    <button id="export_excel_consulta_analistas" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                            <tr>
                                <th>-</th>
                                <th>Región/Agencia</th>
                                <th>Cliente</th>
                                <th>Detalle Encuesta</th>
                                <th>F. Solicitud</th>
                                <th>Bitácora</th>
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

<?php echo $footer; ?>
