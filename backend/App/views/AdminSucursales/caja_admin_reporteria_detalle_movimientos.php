<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" />
            <a id="link" href="/AdminSucursales/SaldosDiarios/">
                <div class="col-md-5" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">

                    <img src="https://cdn-icons-png.flaticon.com/512/2910/2910156.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">

                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Saldos de Sucursales </b></p>
                    <! --  -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/SolicitudesReimpresionTicket/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2972/2972449.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">

                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Solicitudes</b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2972/2972528.png IAMGEN EN COLOR -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/Reporteria/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3201/3201495.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b> Consultar Reportes</b></p>
                    <! --https://cdn-icons-png.flaticon.com/512/1605/1605350.png IMAGEN EN COLOR -->
                </div>
            </a>

            <a id="link" href="/AdminSucursales/EstadoCuentaCliente/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5864/5864275.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Catalogo de Clientes </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/3201/3201558.png IMAGEN EN COLOR -->
                </div>
            </a>

            <a id="link" href="/AdminSucursales/Configuracion/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491249.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Configurar Módulo </b></p>
                    <! -- IMAGEN EN COLOR -->
                </div>
            </a>

            <a id="link" href="/AdminSucursales/Log/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491361.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Log Transaccional </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2761/2761118.png IMAGEN EN COLOR -->
                </div>
            </a>


        </div>
        <div class="col-md-9">
            <form id="registroOperacion" name="registroOperacion">
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px">
                        <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                            <a class="navbar-brand">Admin sucursales / Catálogo de Clientes</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li><a href="">
                                        <p style="font-size: 16px;"><b>Saldos al d´´ia</b></p>
                                </a></li>
                                <li><a href="">
                                        <p style="font-size: 16px;"><b>Cuentas</b></p>
                                </a></li>
                                <li><a href="">
                                        <p style="font-size: 16px;"><b>Detalle Movimientos</b></p>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="card col-md-12">

                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_horario">
                                             <i class="fa fa-plus"></i> Nueva Activación
                                        </button>
                                         <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                                         <div class="dataTable_wrapper">
                                              <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                                              <thead>
                                                   <tr>
                                                       <th>Cod Sucursal</th>
                                                       <th>Nombre Sucursal</th>
                                                       <th>Hora Cierre</th>
                                                       <th>Prorroga</th>
                                                       <th>Fecha de Registro</th>
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_agregar_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <center><h4 class="modal-title" id="myModalLabel">Activar Modulo de Ahorro para Sucursal</h4></center>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form onsubmit="enviar_add_horario(); return false" id="Add_AHC">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="fecha_registro">Fecha de Registro</label>
                                            <input type="text" class="form-control" id="fecha_registro" name="fecha_registro" readonly placeholder=""  value="<?php $fechaActual = date('Y-m-d H:i:s'); echo $fechaActual; ?>">
                                            <small id="emailHelp" class="form-text text-muted">Fecha de registro para la asignación.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="sucursal">Sucursal *</label>
                                            <select class="form-control" autofocus type="select" id="sucursal" name="sucursal">
                                                <?php echo $opciones_suc; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sucursal">Cajera *</label>
                                            <select class="form-control" autofocus type="select" id="sucursal" name="sucursal">
                                                <?php echo $opciones_suc; ?>
                                            </select>
                                            <small id="emailHelp" class="form-text text-muted"></small>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="hora">Horario de Apertura *</label>
                                            <select class="form-control" autofocus type="select" id="hora" name="hora">
                                                <option value="09:00:00">09:00 a.m</option>
                                                <option value="10:00:00">10:00 a.m</option>
                                                <option value="11:00:00">11:00 a.m</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="hora">Horario de Cierre *</label>
                                            <select class="form-control" autofocus type="select" id="hora" name="hora">
                                                <option value="15:00:00">03:00 p.m</option>
                                                <option value="16:00:00">04:00 p.m</option>
                                                <option value="17:00:00">05:00 p.m</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <small id="emailHelp" class="form-text text-muted">ATENCIÓN: La cajera no tendrá acceso antes del horario de apertura y después del horario de cierre solo podrá acceder al arqueo y cierre de día.</small>
                                    </div>

                                </div>
                                <div class="col-md-12">
                                    <br>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="hora">Horario de Apertura *</label>
                                            <select class="form-control" autofocus type="select" id="hora" name="hora">
                                                <option value="09:00:00">09:00 a.m</option>
                                                <option value="10:00:00">10:00 a.m</option>
                                                <option value="11:00:00">11:00 a.m</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="hora">Horario de Cierre *</label>
                                            <select class="form-control" autofocus type="select" id="hora" name="hora">
                                                <option value="15:00:00">03:00 p.m</option>
                                                <option value="16:00:00">04:00 p.m</option>
                                                <option value="17:00:00">05:00 p.m</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <small id="emailHelp" class="form-text text-muted">ATENCIÓN: La cajera no tendrá acceso antes del horario de apertura y después del horario de cierre solo podrá acceder al arqueo y cierre de día.</small>
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

<style>
    .imagen {
        transform: scale(var(--escala, 1));
        transition: transform 0.25s;
    }

    .imagen:hover {
        --escala: 1.2;
        cursor: pointer;
    }

    .linea:hover {
        --escala: 1.2;
        cursor: pointer;
        text-decoration: underline;
    }


    /* Make the badge float in the top right corner of the button */
    .button__badge {
        background-color: #fa3e3e;
        border-radius: 50px;
        color: white;
        padding: 2px 10px;
        font-size: 19px;
        position: absolute;
        /* Position the badge within the relatively positioned button */
        top: 0;
        right: 0;
    }
</style>


<?php echo $footer; ?>