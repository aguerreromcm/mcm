<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Actualización de Crédito</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5" >
                <div class="card-header">
                    <h5 class="card-title">Ingrese el número de crédito</h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Creditos/ActualizaCredito/" method="get">
                        <div class="row">
                                <div class="col-md-4">
                                    <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="number" id="Credito" name="Credito" placeholder="000000" aria-label="Search" value="<?php echo $credito; ?>">
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
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal_editar_numero_credito">
                    <i class="fa fa-edit"></i> Cambiar Número de Crédito
                </button>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal_actualizar_ciclo">
                    <i class="fa fa-edit"></i> Actualizar Ciclo
                </button>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal_actualizar_situacion">
                    <i class="fa fa-edit"></i> Actualizar Situación
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Sucursal</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['SUCURSAL']; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Ejecutivo</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['EJECUTIVO']; ?> </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row" >
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>

                            <div class="count" style="font-size: 14px"><?php echo $Administracion['CLIENTE']; ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-clock-o"></i> Ciclo</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['CICLO']; ?> </div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Prestamo</span>
                            <div class="count" style="font-size: 14px"> $ <?php echo number_format($Administracion['MONTO']); ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Situación</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['SITUACION']; ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Plazo</span>
                            <div class="count" style="font-size: 14px"> <?php echo number_format($Administracion['PLAZO']); ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Perioricidad</span>
                            <div class="count" style="font-size: 14px"> <?php echo $Administracion['PERIODICIDAD']; ?></div>
                        </div>

                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Tasa</span>
                            <div class="count" style="font-size: 14px"> <?php echo $Administracion['TASA']; ?>%</div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Día de Pago</span>
                            <div class="count" style="font-size: 14px"><?php echo $Administracion['DIA_PAGO']; ?></div>
                        </div>
                        <div class="col-md-1 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Parcialidad</span>
                            <div class="count" style="font-size: 14px">$ <?php echo number_format($Administracion['PARCIALIDAD']); ?></div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_editar_numero_credito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Cambiar Número de Crédito</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_edit_credito(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Número de Credito Actual</label>
                                    <input type="text" class="form-control" id="credito" name="credito" aria-describedby="credito" placeholder="Escribe el número del credito"  value="<?php echo $credito; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Número de Credito Nuevo *</label>
                                    <input type="number" class="form-control" id="credito_nuevo" name="credito_nuevo" aria-describedby="credito_nuevo" placeholder="Escribe el número del credito"  value="" max="300000">
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_actualizar_ciclo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Actualizar Ciclo</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_edit_ciclo(); return false" id="AddCiclo">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Número de Credito *</label>
                                    <input type="number" class="form-control" id="credito_c" name="credito_c" aria-describedby="credito_c" placeholder="Escribe el número del credito"  value="<?php echo $credito; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Ciclo Actual *</label>
                                    <input type="number" class="form-control" id="ciclo_c" name="ciclo_c" aria-describedby="ciclo_c" placeholder="Escribe el número del credito"  value="<?php echo $Administracion['CICLO']; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-6" >
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Ciclo Nuevo *</label>
                                    <input type="number" class="form-control" id="ciclo_c_n" name="ciclo_c_n" aria-describedby="ciclo_c_n" placeholder="Escribe el nuevo ciclo"  value="<?php echo $Administracion['CICLO']; ?>" max="30">
                                </div>
                            </div>


                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar_update" class="btn btn-primary" value="enviar_update"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_actualizar_situacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Actualizar Situación</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_edit_situacion(); return false" id="AddSituacion">
                        <div class="row">

                            <div class="col-md-6" >
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Número de Crédito *</label>
                                    <input type="text" class="form-control" id="credito_s" name="credito_s" aria-describedby="credito_s" placeholder="Escribe el número de crédito"  value="<?php echo $credito; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group" >
                                    <label for="exampleInputEmail1">Ciclo *</label>
                                    <input type="number" class="form-control" id="ciclo_s" name="ciclo_s" aria-describedby="ciclo_s" placeholder="Escribe el cilco"  value="<?php echo $Administracion['CICLO']; ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="situacion_s">Situación *</label>
                                    <select class="form-control" autofocus type="select" id="situacion_s" name="situacion_s" aria-label="Search">
                                        <?php echo $combo; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar_update" class="btn btn-primary" value="enviar_update"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>

</script>

<?php echo $footer; ?>
