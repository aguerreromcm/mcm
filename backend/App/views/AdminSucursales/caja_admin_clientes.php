<?php echo $header; ?>

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
                    <img src="https://cdn-icons-png.flaticon.com/512/5864/5864348.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
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
            <div class="modal-content">
                <div class="modal-header" style="padding-bottom: 0px">
                    <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                        <a class="navbar-brand">Admin Sucursales / Catálogo de Clientes</a>
                    </div>
                    <div class="col-md-12">
                        <br><br>
                        <div class="col-md-6">
                            <p>Podrá encontrar el resumen detallado de las cuentas de los clientes MCM – Ahorro, si la información no es correcta, contacte a soporte. Si desea el detalle de los movimentos, consulte el siguiente <a href="/AdminSucursales/Reporteria/" target="_blank">enlace</a>.</p>
                            <hr>
                        </div>
                        <div class="col-md-4">
                            <label for="clienteBuscado">Código de cliente SICAFIN *</label>
                            <input onkeypress=validarYbuscar(event) class="form-control" id="clienteBuscado" name="clienteBuscado" placeholder="000000" required>
                        </div>
                        <div class="col-md-2" style="padding-top: 25px">
                            <button class="btn btn-primary" id="btnBskClnt" onclick="buscaCliente()">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <ul class="nav navbar-nav" id="opcionesCat" style="margin-bottom: 20px;">
                            <li>
                                <span style="font-size: 15px; margin: 0 15px;font-weight: bold; color: #0D0A0A;" id="ResumenCuenta" onclick=actualizaVista(event)>Resumen de cuenta</span>
                            </li>
                            <li class="linea">
                                <span style="font-size: 15px; margin: 0 15px; color: #0D0A0A;" id="Rendimiento" onclick=actualizaVista(event)>Rendimiento</span>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row" id="cuerpoModal">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="pieModal">

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalClientes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="text-align: center;">
                <h4 class="modal-title" id="myModalLabel">Activar Modulo de Ahorro para Sucursal</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit=noSubmit() id="frmModal">
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                </div>
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

<?php echo $footer; ?>