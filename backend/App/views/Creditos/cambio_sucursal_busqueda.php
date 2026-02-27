<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3> Cambio de Sucursal</h3>
            <div class="clearfix"></div>
        </div>

        <div class="card card-danger col-md-5" >
            <div class="card-header">
                <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
            </div>
            <div class="card-body">
                <form class="" action="/Creditos/CambioSucursal/" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-control mr-sm-3" style="font-size: 18px;" autofocus type="select" id="" name="" placeholder="000000" aria-label="Search">
                                <option value="credito">Crédito</option>
                                <option value="fecha">Fecha</option>
                            </select>
                            <span id="availability1"></span>
                        </div>
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
            <hr style="border-top: 1px solid #787878; margin-top: 5px;">
            <div class="row" >
                <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                    <div class="col-md-3 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>
                        <div class="count" style="font-size: 14px"><?php echo $Administracion['CLIENTE']; ?></div>
                    </div>
                    <div class="col-md-1 col-sm-1  tile_stats_count">
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
                    <div class="col-md-1 col-sm-3  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i></i> Sucursal</span>
                        <div class="count" style="font-size: 14px"><?php echo $Administracion['SUCURSAL']; ?></div>
                    </div>
                    <div class="col-md-3 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i></i> Ejecutivo</span>
                        <div class="count" style="font-size: 14px"><?php echo $Administracion['EJECUTIVO']; ?> </div>
                    </div>
                    <div class="col-md-2 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i></i> Acción</span>
                        <div class="count" style="font-size: 14px">
                            <button type="button" class="btn btn-success btn-circle" onclick="EditarSucursal('<?php echo $Administracion['ID_EJECUTIVO']; ?>');"><i class="fa fa-edit"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
</div>

<div class="modal fade" id="modal_cambio_sucursal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Cambiar Crédito de Sucursal </h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add('<?php echo $Administracion['CICLO']; ?>'); return false" id="Add">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo">Sucursal *</label>
                                    <select class="form-control" autofocus type="select" id="sucursal" name="sucursal" aria-label="Search">
                                        <?php echo $sucursal; ?>
                                    </select>
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

<script>
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    function EditarSucursal(id_suc)
    {
        credito = getParameterByName('Credito');
        id_sucursal = id_suc;

        $('#modal_cambio_sucursal').modal('show'); // abrir

    }
</script>


<?php echo $footer; ?>
