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
                            <a class="navbar-brand">Admin sucursales / Configuración de módulo para Ahorro / Parametros de operacion</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li class="linea"><a href="/AdminSucursales/Configuracion/">
                                        <p style="font-size: 15px;">Activar modulo en sucursal</p>
                                    </a></li>

                                <li class="linea"><a href="/AdminSucursales/ConfiguracionUsuarios/">
                                        <p style="font-size: 15px;">Permisos a usuarios</p>
                                    </a></li>

                                <li><a href="">
                                        <p style="font-size: 16px;"><b>Parámetros de operación </b></p>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <form id="datos" onsubmit="noSUBMIT(event)">
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="tasa_anual">TASA  ANUAL, CONTRATO AHORRO </label>
                                                    <input type="text" class="form-control" id="tasa_anual" name="tasa_anual" placeholder="0.00" min="0" max="100000" onkeydown="soloNumeros(event)" onblur="validaMaxMin()" oninput="cambioMonto()" disabled="" value="5% (CINCO PORCIENTO ANUAL)">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-success btn-circle" onclick="EditarTasa();"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="monto_minimo_apertura">PRECIO POR MANEJO DE CUENTA</label>
                                                    <input type="text" class="form-control" id="monto_minimo_apertura" name="monto_minimo_apertura" placeholder="0.00" min="0" max="100000" onkeydown="soloNumeros(event)" onblur="validaMaxMin()" oninput="cambioMonto()" readonly value="200.00 (DOSCIENTOS PESOS 00/100 M.N)">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-success btn-circle" onclick="EditarTasa();"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="monto_minimo_apertura">MONTO MAXIMO A DEPOSITAR POR TRANSACCIÓN</label>
                                                    <input type="text" class="form-control" id="monto_minimo_apertura" name="monto_minimo_apertura" placeholder="0.00" min="0" max="1000000" value="1,000,000.00 (UN MILLON 00/100 M.N)" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-success btn-circle" onclick="EditarTasa();"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="monto_minimo_apertura">MONTO MÍNIMO, CONTRATO AHORRO </label>
                                                    <input type="text" class="form-control" id="monto_minimo_apertura" name="monto_minimo_apertura" placeholder="0.00" min="0" max="100000" onkeydown="soloNumeros(event)" onblur="validaMaxMin()" oninput="cambioMonto()" disabled="" value="300.00 (TRESCIENTOS PESOS 100/00 M.N)">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-success btn-circle" onclick="EditarTasa();"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="col-md-6">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="monto_minimo_apertura">MONTO MAXIMO A RETIRAR POR CLIENTE AL DÍA</label>
                                                    <input type="text" class="form-control" id="monto_minimo_apertura" name="monto_minimo_apertura" placeholder="0.00" min="0" max="100000" onkeydown="soloNumeros(event)" onblur="validaMaxMin()" oninput="cambioMonto()" readonly value="50,000.00 (CINCUENTA MIL PESOS 00/100 M.N)">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-success btn-circle" onclick="EditarTasa();"><i class="fa fa-edit"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <small id="emailHelp" class="form-text text-muted"><b>ATENCIÓN:</b> Al momento de modificar la tasa anual o el monto mínimo, estos cambios seran inmediatos en la creación de un nuevo contrato.</small>
                                        </div>

                                    </div>
                                    <br>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
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