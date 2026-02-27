<?php echo $header; ?>

<div class="right_col">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3> Registro de Pagos</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Pagos/PagosConsultaUsuarios/" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control mr-sm-3" style="font-size: 18px;" autofocus type="select" id="opcion_credito" name="opcion_credito" placeholder="000000">
                                    <option value="credito">Crédito</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="text" onKeypress="if (event.keyCode < 9 || event.keyCode > 57) event.returnValue = false;" id="Credito" name="Credito" placeholder="000000" aria-label="Search" >

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
                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>

                            <div class="count" style="font-size: 14px"><?php echo $Administracion[0]['CLIENTE']; ?></div>
                            <span class="count_top badge" style="padding: 1px 1px; background: <?php echo $Administracion[0]['COLOR']; ?>"><h5><b><i class="">SITUACIÓN: <?php echo $Administracion[0]['SITUACION_NOMBRE']; ?></i></b></h5></span>

                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-clock-o"></i> Ciclo</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion[0]['CICLO']; ?> </div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Prestamo</span>
                            <div class="count" style="font-size: 14px"> $ <?php echo number_format($Administracion[0]['MONTO']); ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Día de Pago</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion[0]['DIA_PAGO']; ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Parcialidad</span>
                            <div class="count" style="font-size: 14px">$ <?php echo number_format($Administracion[0]['PARCIALIDAD']); ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Sucursal</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion[0]['SUCURSAL']; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Ejecutivo de cuenta</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion[0]['EJECUTIVO']; ?> </div>
                        </div>
                    </div>
                </div>
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                                    <th>Medio</th>
                                    <th>Consecutivo</th>
                                    <th>CDGNS</th>
                                    <th>Fecha</th>
                                    <th>Ciclo</th>
                                    <th>Monto</th>
                                    <th>Tipo</th>
                                    <th>Ejecutivo</th>
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

<?php echo $footer; ?>
