<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" />
            <a id="link" href="/AdminSucursales/SaldosDiarios/">
                <div class="col-md-5" style=" margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2910/2910306.png" style="border-radius: 3px; padding-top: 5px" width="110" height="110" />
                    <p style="font-size: 12px; padding-top: 5px; color: #000000">
                        <b>Saldos de Sucursales </b>
                    </p>
                    <! -- -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/SolicitudesReimpresionTicket/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2972/2972449.png" style="border-radius: 3px; padding-top: 5px" width="110" height="110" />
                    <span class="button__badge">4</span>
                    <p style="font-size: 12px; padding-top: 5px; color: #000000">
                        <b>Solicitudes</b>
                    </p>
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
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf;
                        border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491249.png" style="border-radius: 3px; padding-top: 5px" width="100" height="110" />
                    <p style="font-size: 12px; padding-top: 6px; color: #000000">
                        <b>Configurar Módulo </b>
                    </p>
                    <! -- IMAGEN EN COLOR -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/Log/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491361.png" style="border-radius: 3px; padding-top: 5px" width="110" height="110" />
                    <p style="font-size: 12px; padding-top: 6px; color: #000000">
                        <b>Log Transaccional </b>
                    </p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2761/2761118.png IMAGEN EN COLOR -->
                </div>
            </a>

        </div>
        <div class="col-md-9">
            <form id="datos" onsubmit=noSUBMIT()>
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px">
                        <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                            <a class="navbar-brand">Admin sucursales / Fondeo de sucursal</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li class="linea"><a href="/AdminSucursales/SaldosDiarios/">
                                        <p style="font-size: 16px;">Saldos del día por sucursal</p>
                                    </a></li>
                                <li><a href="">
                                        <p style="font-size: 15px;"><b>Fondear sucursal</b></p>
                                    </a></li>
                                <li class="linea"><a href="/AdminSucursales/RetiroSucursal/">
                                        <p style="font-size: 15px;">Retiro efectivo</p>
                                    </a></li>

                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6">
                                    <p>Para poder fondear una sucursal, esta debe estar habilitada para realizar transacciones de ahorro. Para habilitar una sucursal para transacciones de ahorro, comuníquese con el ADMINISTRADOR</p>
                                    <hr>
                                </div>
                                <div class="col-md-4">
                                    <label for="sucursalBuscada">Código de sucursal</label>
                                    <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="sucursalBuscada" name="sucursalBuscada" placeholder="000" required>
                                </div>

                                <div class="col-md-2" style="padding-top: 25px">
                                    <button type="button" class="btn btn-primary" onclick="buscar()">
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="codigoSuc">Código de sucursal</label>
                                        <input type="text" class="form-control" id="codigoSuc" name="codigoSuc" readonly>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="nombreSuc">Nombre de la sucursal</label>
                                        <input type="text" class="form-control" id="nombreSuc" name="nombreSuc" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fechaFondeo">Fecha del fondeo</label>
                                        <input type="text" class="form-control" id="fechaFondeo" name="fechaFondeo" value="<?= $fecha; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="codigoCajera">Código de cajera</label>
                                        <input type="text" class="form-control" id="codigoCajera" name="codigoCajera" readonly>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="nombreCajera">Nombre cajera</label>
                                        <input type="text" class="form-control" id="nombreCajera" name="nombreCajera" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fechaCierre">Fecha del ultimo cierre</label>
                                        <input type="text" class="form-control" id="fechaCierre" name="fechaCierre" value="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <hr>
                            </div>
                            <div class="row">
                                <div class="col-md-3" style="font-size: 18px; padding-top: 5px;">
                                    <label style="color: #000000">Movimiento:</label>
                                </div>
                                <div class="col-md-4" style="text-align: center; font-size: 18px; padding-top: 5px;">
                                    <input type="radio" name="esFondeo" id="esFondeo" checked>
                                    <label for="esFondeo">Fondeo</label>
                                </div>
                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                    <h3>$</h3>
                                </div>
                                <div class="col-md-4" style="padding-top: 5px;">
                                    <input type="number" class="form-control" id="monto" name="monto" min="1" placeholder="0.00" style="font-size: 25px;" oninput=validaMonto(event) onkeydown=soloNumeros(event) disabled>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="text" class="form-control" id="monto_letra" name="monto_letra" style="border: 1px solid #000000; text-align: center; font-size: 25px;" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="text-align:center;">
                                    <hr>
                                    <h3 style="color: #000000">Resumen de movimientos</h3>
                                    <br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8" style="display: flex; justify-content: flex-start;">
                                    <h4>Saldo actual de la sucursal</h4>
                                </div>
                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                    <h4>$</h4>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="saldoActual" name="saldoActual" value="0.00" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                    <h4 id="simboloOperacion">+</h4>
                                </div>
                                <div class="col-md-7" style="display: flex; justify-content: flex-start;">
                                    <h4 id="descOperacion">Fondeo</h4>
                                </div>
                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                    <h4>$</h4>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="montoOperacion" name="montoOperacion" value="0.00" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8" style="display: flex; justify-content: flex-start;">
                                    <h2>Saldo final de la sucursal</h2>
                                </div>
                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                    <h4>$</h4>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="saldoFinal" name="saldoFinal" value="0.00" readonly>
                                </div>
                                <div class="col-md-12" style="display: flex; justify-content: center; color: red; height: 30px;">
                                    <label id="tipSaldo" style="font-size: 18px;"></label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button id="btnFondear" class="btn btn-primary" onclick=fondear(event) disabled><span class="glyphicon glyphicon-floppy-disk"></span> Confirmar fondeo</button>
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