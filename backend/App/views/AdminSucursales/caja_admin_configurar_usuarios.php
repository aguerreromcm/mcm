<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" />
            <a id="link" href="/AdminSucursales/SaldosDiarios/">
                <div class="col-md-5" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2910/2910156.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Saldos de Sucursales </b></p>
                    <! -- -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/SolicitudesReimpresionTicket/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2972/2972449.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <span class="button__badge">4</span>
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
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491253.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
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
                            <a class="navbar-brand">Admin sucursales / Configurar de módulo / Permisos a usuarios</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li class="linea"><a href="/AdminSucursales/Configuracion/">
                                        <p style="font-size: 15px;">Activar modulo en sucursal</p>
                                    </a></li>
                                <li><a href="">
                                        <p style="font-size: 16px;"><b>Permisos a usuarios</b></p>
                                    </a></li>

                                <li class="linea"><a href="/AdminSucursales/ConfiguracionParametros/">
                                        <p style="font-size: 15px;">Parámetros de operación</p>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                           <div class="row">
                               <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_horario">
                                  <i class="fa fa-plus"></i> Nuevo Usuario
                              </button>-->
                               <p>Los usuarios que aqui se muestran tienen acceso al modulo de ahorro en la version <b>ADMINISTRADOR</b>, si desea asignar un nuevo usuario realice un soporte al área de desarrollo. </p>
                               <hr style=" margin-top: 5px;">
                            </div>
                            <div class="row">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                                        <thead>
                                            <tr>
                                                <th>Usuario</th>
                                                <th>Nombre</th>
                                                <th>Puesto</th>
                                                <th>Sucursal</th>
                                                <th>Estatus</th>
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
            </form>
        </div>
    </div>
</div>

<!-- <div class="modal fade in" id="modal_agregar_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: block; padding-right: 15px;"> -->
<div class="modal fade" id="modal_agregar_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Permisos Modulo Administración Ahorro</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="datos" onsubmit=noSUBMIT(event)>
                        <div class="row">
                            <div class="col-md-12">
                                <p>Selecciona las opciones a las que te gustaria dar acceso a sus colaboradores</p>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sucursal">Colaborador Administrativo MCM *</label>
                                    <select class="form-control" id="cajera" name="cajera" onchange=cambioCajera() >
                                        <?= $opcEmpleados; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 19px;">
                                    <label for="sucursal">SALDOS DE SUCURSALES</label>
                                    <hr>
                                    <input name="A" type="checkbox" value="1"  />
                                    <label for="A">Saldos del día por sucursal (A)</label>

                                    <br>
                                    <input name="B" type="checkbox" value="1"  />
                                    <label for="B">Cierre de día (B)</label>

                                    <br>
                                    <input name="C" type="checkbox" value="1"  />
                                    <label for="C">Fondear sucursal (C)</label>

                                    <br>
                                    <input name="D" type="checkbox" value=""  />
                                    <label for="D">Retiro efectivo (D)</label>

                                    <br>
                                    <input name="E" type="checkbox" value="1"  />
                                    <label for="E">Historail saldos por sucursal (E)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 19px;">
                                    <label for="sucursal">SOLICITUDES</label>
                                    <hr>
                                    <input name="F" type="checkbox" value="1"  />
                                    <label for="F">Reimpresión de tickets (F)</label>

                                    <br>
                                    <input name="G" type="checkbox" value="1"  />
                                    <label for="G">Resumen de movimientos (G)</label>

                                    <br>
                                    <input name="H" type="checkbox" value="1"  />
                                    <label for="H">Retiros ordinarios (H)</label>

                                    <br>
                                    <input name="I" type="checkbox" value="1"  />
                                    <label for="I">Retiros express (I)</label>

                                    <br>
                                    <input name="J" type="checkbox" value="1"  />
                                    <label for="J">Retirar efectivo de caja (J)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 128px!important;">
                                    <label for="sucursal">CATÁLOGO DE CLIENTES</label>
                                    <hr>
                                    <input name="K" type="checkbox" value="1"  />
                                    <label for="K">Catálogo de clientes (K)</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 108px;">
                                    <label for="sucursal">LOG TRANSACCIONAL</label>
                                    <hr>
                                    <input name="L" type="checkbox" value="1"  />
                                    <label for="L">Log transaccional (L)</label>


                                    <br>
                                    <input name="M" type="checkbox" value="1"  />
                                    <label for="M">Log de Configuración (M)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 80px;">
                                    <label for="sucursal">CONFIGURAR MÓDULO</label>
                                    <hr>
                                    <input name="N" type="checkbox" value="1"  />
                                    <label for="N">Activar módulo en sucursal (N)</label>

                                    <br>
                                    <input name="O" type="checkbox" value="1"  />
                                    <label for="O">Permisos a usuarios (O)</label>

                                    <br>
                                    <input name="P" type="checkbox" value="1"  />
                                    <label for="P">Parámetros de operación (P)</label>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="border: #9baab8;!important; border-style: solid; padding:10px; padding-bottom: 53px;">
                                    <label for="sucursal">CONSULTAR REPORTES</label>
                                    <hr>
                                    <input name="Q" type="checkbox" value="1"  />
                                    <label for="Q">Hostorial de transacciones (Q)</label>

                                    <br>
                                    <input name="R" type="checkbox" value="1"  />
                                    <label for="R">Historial fondeo sucursal (R)</label>

                                    <br>
                                    <input name="S" type="checkbox" value="1"  />
                                    <label for="S">Historial retiro sucursal(S)</label>
                                    <br>

                                    <input name="T" type="checkbox" value="1"  />
                                    <label for="T">Historial cierre día(T)</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button class="btn btn-primary" id="guardar" onclick=activarSucursal() disabled><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
            </div>
        </div>
    </div>
</div>

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


<?= $footer; ?>