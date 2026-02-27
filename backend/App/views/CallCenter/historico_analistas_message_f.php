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
                    <h5 class="card-title">Seleccione la sucursal y el rango de la fecha a generar el reporte </h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/CallCenter/HistoricoAnalistas/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2"  autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $Inicial; ?>" min="" max="<?php echo $Inicial; ?>">
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
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="x_content">
                            <br />
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <label style="font-size: 14px; color: black;">Usted no tiene registros finalizados con fecha de trabajo en el rango que selecciono:</label> <li style="color: black;">Valide que las fechas que desea consultar sean correctas</li> <li style="color: black;">Si el problema persiste, comuníquese con soporte técnico</li>
                                <br>
                                <a href="/Operaciones/ReportePLDPagos/" class="alert-link">Regresar</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php echo $footer; ?>
