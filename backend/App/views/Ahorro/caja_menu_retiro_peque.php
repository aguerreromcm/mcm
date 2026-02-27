<?= $header; ?>

<?php

use App\components\BuscarCliente;

$buscarCliente = new BuscarCliente('Para realizar un retiro es necesario que el cliente tenga una cuenta Peque activa, de lo contrario, es necesaria la creación de una a través de la opción: <a href="/Ahorro/ContratoCuentaCorriente/" target="_blank">Nuevo Contrato</a>.');

?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <a id="link" href="/Ahorro/CuentaCorriente/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5575/5575938.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Ahorro </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/5575/5575939.png -->
                </div>
            </a>
            <a id="link" href="/Ahorro/ContratoInversion/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5836/5836503.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Inversión </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/5836/5836477.png -->
                </div>
            </a>
            <a id="link" href="/Ahorro/CuentaPeque/">
                <div class="col-md-5" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2995/2995467.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Ahorro Peque </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2995/2995390.png -->
                </div>
            </a>
            <a id="link" href="/Ahorro/EstadoCuenta/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/12202/12202939.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Resumen Movimientos </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/12202/12202918.png -->
                </div>
            </a>
            <a id="link" href="/Ahorro/SaldosDia/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5833/5833855.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Arqueo </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/5833/5833897.png -->
                </div>
            </a>
            <a id="link" href="/Ahorro/ReimprimeTicket/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/7325/7325275.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Reimprime Ticket </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/942/942752.png -->
                </div>
            </a>
        </div>
        <div class="col-md-9">
            <form id="registroOperacion" name="registroOperacion">
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px">
                        <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                            <a class="navbar-brand">Mi espacio / Solicitud de Retiro</a>
                            &nbsp;&nbsp;
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li class="linea">
                                    <a href="/Ahorro/CuentaPeque/">
                                        <p style="font-size: 16px;">Ahorro cuenta corriente peque</p>
                                    </a>
                                </li>
                                <li class="linea">
                                    <a href="/Ahorro/ContratoCuentaPeque/">
                                        <p style="font-size: 15px;">Nuevo contrato</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="">
                                        <p style="font-size: 15px;"><b>Solicitud de retiro</b></p>
                                    </a>
                                </li>
                                <li class="linea">
                                    <a href="/Ahorro/HistorialSolicitudRetiroCuentaPeque/">
                                        <p style="font-size: 15px;">Procesar solicitudes de retiro</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <?= $buscarCliente->mostrar(); ?>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="contrato">Nombre del cliente peque *</label>
                                        <select class="form-control" id="contrato" name="contrato" disabled>
                                        </select>
                                        <input type="text" id="contratoSel" value="<?= $contratoSel; ?>" hidden>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="curp">CURP</label>
                                        <input type="text" class="form-control" id="curp" name="curp" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4" style="display: none;">
                                    <div class="form-group">
                                        <label for="nombre_ejecutivo">Nombre de la cajera que captura el pago</label>
                                        <input type="text" class="form-control" id="nombre_ejecutivo" name="nombre_ejecutivo" value="<?= $_SESSION['nombre'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nombre">Contrato del peque</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cliente">Código de cliente SICAFIN (Tutor)</label>
                                        <input type="number" class="form-control" id="cliente" name="cliente" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_retiro">Fecha del retiro</label>
                                        <input type="text" class="form-control" id="fecha_retiro" name="fecha_retiro" value="<?= $fecha; ?>" readonly>
                                        <input type="date" class="form-control" id="fecha_retiro_hide" name="fecha_retiro_sel" style="display: none" min="<?= $fechaInput; ?>" max="<?= $fechaInputMax; ?>" value="<?= $fechaInput; ?>" oninput=pasaFecha(event) />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <hr>
                            </div>
                            <div class="row">
                                <div class="col-md-2" style="font-size: 18px; padding-top: 5px;">
                                    <label style="color: #000000">Movimiento:</label>
                                </div>
                                <div class="col-md-2" style="text-align: center; font-size: 18px; padding-top: 5px;">
                                    <input type="radio" name="tipoRetiro" id="express" onchange=cambioMovimiento(event) checked disabled>
                                    <label for="express">Express</label>
                                </div>
                                <div class="col-md-3" style="text-align: center; font-size: 18px; padding-top: 5px;">
                                    <input type="radio" name="tipoRetiro" id="programado" onchange=cambioMovimiento(event) disabled>
                                    <label for="programado">Programado</label>
                                </div>
                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                    <h3>$</h3>
                                </div>
                                <div class="col-md-4" style="padding-top: 5px;">
                                    <input type="number" class="form-control" id="monto" name="monto" min="<?= $montoMinimoRetiro ?>" max="<?= $montoMaximoRetiro ?>" placeholder="0.00" style="font-size: 25px;" oninput=validaMonto(event) onkeydown=soloNumeros(event) onblur=valSalMin() disabled>
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
                            <div class="row" style="display: none!important;">
                                <div class="col-md-8" style="display: flex; justify-content: flex-start;">
                                    <h4>Saldo actual cuenta ahorro corriente</h4>
                                </div>
                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                    <h4>$</h4>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control" id="saldoActual" name="saldoActual" value="0.00" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1">
                                    <h4 id="simboloOperacion">+</h4>
                                </div>
                                <div class="col-md-7" style="display: flex; justify-content: flex-start;">
                                    <h4 id="descOperacion">Retiro de cuenta ahorro corriente</h4>
                                </div>
                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                    <h4>$</h4>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control" id="montoOperacion" name="montoOperacion" value="0.00" readonly>
                                </div>
                            </div>
                            <div class="row" style="display: none;">
                                <div class="col-md-8" style="display: flex; justify-content: flex-start;">
                                    <h2>Saldo final cuenta ahorro corriente</h2>
                                </div>
                                <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                    <h4>$</h4>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control" id="saldoFinal" name="saldoFinal" value="0.00" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="display: flex; justify-content: center; color: red; height: 30px;">
                                    <label id="tipSaldo" style="opacity:0; font-size: 18px;">El monto a retirar no puede ser mayor al saldo de la cuenta.</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="btnRegistraOperacion" name="agregar" class="btn btn-primary" value="enviar" onclick=registraSolicitud(event) disabled><span class="glyphicon glyphicon-floppy-disk"></span> Registrar solicitud</button>
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
</style>


<?= $footer; ?>