<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Creación de usuarios REDECO</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">

                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_usuario">
                    <i class="fa fa-plus"></i> Crear Usuario
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Perfil</th>
                            <th>Fecha de Registro</th>
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

<div class="modal fade" id="modal_agregar_usuario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Crear Nuevo Usuario REDECO</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add_user(); return false" id="Add_user">
                        <div class="row">


                            <div class="col-md-12">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="fecha_registro">Usuario *</label>
                                        <input type="text" class="form-control" id="usuario" name="usuario">
                                        <small id="emailHelp" class="form-text text-muted">Máximo 6 caracteres</small>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="hora">Perfil del Usuario *</label>
                                        <select class="form-control" autofocus type="select" id="hora" name="hora">
                                            <option value="1">Admin</option>
                                            <option value="2">SuperUser</option>
                                        </select>
                                        <small id="emailHelp" class="form-text text-muted">Perfil</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_registro">Contraseña *</label>
                                        <input type="password" class="form-control" id="pass1" name="pass1">
                                        <small id="emailHelp" class="form-text text-muted">Escriba la contraseña</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fecha_registro">Confirmar Contraseña *</label>
                                        <input type="password" class="form-control" id="pass2" name="pass2">
                                        <small id="emailHelp" class="form-text text-muted">Confirme la contraseña anterior</small>
                                    </div>
                                </div>

                            </div>

                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Registrar Usuario</button>
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
