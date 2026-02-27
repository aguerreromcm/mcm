<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Asignación de Días Festivos</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">

                <button type="button" style="display: none" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_horario">
                    <i class="fa fa-plus"></i> Asignar Sucursal
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                            <th>Fecha Festiva</th>
                            <th>Descripción</th>
                            <th>Fecha de Captura</th>
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

<div class="modal fade" id="modal_agregar_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Asignar Horario de Cierre a Sucursal</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add_horario(); return false" id="Add_AHC">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="fecha_registro">Fecha de Registro</label>
                                        <input type="text" class="form-control" id="fecha_registro" name="fecha_registro" readonly placeholder=""  value="<?php $fechaActual = date('Y-m-d H:i:s'); echo $fechaActual; ?>">
                                        <small id="emailHelp" class="form-text text-muted">Fecha de registro para la asignación.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="sucursal">Sucursal *</label>
                                        <select class="form-control" autofocus type="select" id="sucursal" name="sucursal">
                                            <?php echo $opciones_suc; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="hora">Horario de Cierre *</label>
                                        <select class="form-control" autofocus type="select" id="hora" name="hora">
                                            <option value="10:00:00">10:00 a.m</option>
                                            <option value="10:10:00">10:10 a.m</option>
                                            <option value="10:15:00">10:15 a.m</option>
                                            <option value="10:30:00">10:30 a.m</option>
                                            <option value="11:00:00">11:00 a.m</option>
                                            <option value="11:10:00">11:10 a.m</option>
                                            <option value="11:15:00">11:15 a.m</option>
                                            <option value="11:30:00">11:30 a.m</option>
                                            <option value="11:40:00">11:40 a.m</option>
                                            <option value="11:50:00">11:50 a.m</option>
                                            <option value="11:59:00">11:59 p.m</option>
                                        </select>
                                    </div>
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

<div class="modal fade" id="modal_update_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Asignar Horario de Cierre a Sucursal</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_update_horario(); return false" id="Update_AHC">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="sucursal_e">Sucursal *</label>
                                        <select class="form-control" autofocus type="select" id="sucursal_e" name="sucursal_e">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="hora_ae">Horario de Cierre Actual *</label>
                                        <input type="text" name="hora_ae" id="hora_ae" class="form-control col-md-6 col-xs-12" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="hora_e">Nuevo Horario *</label>
                                        <select class="form-control" autofocus type="select" id="hora_e" name="hora_e">
                                            <option value="10:00:00">10:00 a.m</option>
                                            <option value="10:10:00">10:10 a.m</option>
                                            <option value="10:15:00">10:15 a.m</option>
                                            <option value="10:30:00">10:30 a.m</option>
                                            <option value="11:00:00">11:00 a.m</option>
                                            <option value="11:10:00">11:10 a.m</option>
                                            <option value="11:15:00">11:15 a.m</option>
                                            <option value="11:30:00">11:30 a.m</option>
                                            <option value="11:40:00">11:40 a.m</option>
                                            <option value="11:50:00">11:50 a.m</option>
                                            <option value="11:59:00">11:59 p.m</option>
                                        </select>
                                    </div>
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
    function EditarHorario(sucursal, nombre_suc, hora_actual) {


        var o = new Option(nombre_suc, sucursal);
        $(o).html(nombre_suc);
        $("#sucursal_e").append(o);

        document.getElementById("hora_ae").value = hora_actual;

        $('#modal_update_horario').modal('show');

    }
</script>

<?php echo $footer; ?>
