<?= $header; ?>

<?php

use App\components\BuscarCliente;

$buscarCliente = new BuscarCliente('Para poder dar de alta un nuevo contrato de una cuenta Peque, el cliente debe estar registrado en SICAFIN, si el cliente no tiene una cuenta abierta solicite el alta a su ADMINISTRADORA.');

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
            <form id="registroInicialAhorro" name="registroInicialAhorro">
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px">
                        <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                            <a class="navbar-brand">Mi espacio / Cuentas de ahorro corriente peque </a>
                            &nbsp;&nbsp;
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li>
                                    <a href="/Ahorro/CuentaPeque/">
                                        <p style="font-size: 16px;">Ahorro cuenta corriente peque</p>
                                    </a>
                                </li>
                                <li class="linea">
                                    <a onclick=mostrarAhorro() href="">
                                        <p style="font-size: 16px;"><b>Nuevo contrato</b></p>
                                    </a>
                                </li>
                                <li class="linea">
                                    <a href="/Ahorro/SolicitudRetiroCuentaPeque/">
                                        <p style="font-size: 15px;">Solicitud de retiro</p>
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
                                <div class="col-md-8">
                                    <div class="col-md-12">
                                        <p><b><span class="fa fa-sticky-note"></span> Identificación del cliente SICAFIN</b></p>
                                        <hr>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fechaRegistro">Fecha de registro del cliente SICAFIN</label>
                                            <input type="text" class="form-control" id="fechaRegistro" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="noCliente">Código de cliente SICAFIN</label>
                                            <input type="number" class="form-control" id="noCliente" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="nombre">Nombre del cliente SICAFIN</label>
                                            <input type="text" class="form-control" id="nombre" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <p><b><span class="fa fa-sticky-note"></span> Identificación del peque</b></p>
                                        <hr>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre1">Primer nombre del peque *</label>
                                            <input type="text" class="form-control" id="nombre1" name="nombre1" oninput=camposLlenos(event) disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre2">Segundo nombre del peque</label>
                                            <input type="text" class="form-control" id="nombre2" name="nombre2" oninput=camposLlenos(event) disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="apellido1">Primer apellido del peque *</label>
                                            <input type="text" class="form-control" id="apellido1" name="apellido1" oninput=camposLlenos(event) disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="apellido2">Segundo apellido del peque</label>
                                            <input type="text" class="form-control" id="apellido2" name="apellido2" oninput=camposLlenos(event) disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="nombre">Sexo *</label>
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <input type="radio" name="sexo" id="sexoH" checked>
                                                <label for="sexoH">Hombre</label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="radio" name="sexo" id="sexoM">
                                                <label for="sexoM">Mujer</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="fecha_nac">Fecha de nacimiento *</label>
                                            <input type="date" class="form-control" id="fecha_nac" name="fecha_nac" min="<?= date("Y-m-d", strtotime('-18 years')) ?>" max="<?= $fecha ?>" oninput=camposLlenos(event) onkeydown=iniveCambio(event) disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="edad">Edad</label>
                                            <input type="text" class="form-control" id="edad" oninput=camposLlenos(event) readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pais">País *</label>
                                            <input type="text" class="form-control" id="pais" name="pais" value="MÉXICO" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ciudad">Entidad de nacimiento *</label>
                                            <select class="form-control mr-sm-3" id="ciudad" name="ciudad" onchange=camposLlenos(event) disabled>
                                                <?php echo $opciones_ent; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="curp">CURP *</label>
                                            <input type="text" class="form-control" name="curp" id="curp" maxlength="18" oninput=camposLlenos(event) disabled>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div style="display: flex; justify-content: space-between;">
                                                <div class="izquierda">
                                                    <label for="direccion">Dirección *</label>
                                                </div>
                                                <div class="derecha" style="margin-left: 15px; font-size:12px">
                                                    <input type="radio" name="confirmaDir" id="confirmaDir" onchange=camposLlenos(event) checked />
                                                    <label for="confirmaDir"><b>Se autoriza usar la dirección del titular</b></label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <textarea type="text" style="resize: none;" class="form-control" id="direccion" rows="3" cols="50" readonly>
                                                        </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                                <div class="col-md-4">
                                    <form id="registroInicialAhorro" name="registroInicialAhorro">
                                        <div class="col-md-12">
                                            <p><b><span class="fa fa-sticky-note"></span> Datos básicos de apertura para la cuenta de Ahorro Peque</b></p>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="tasa">Tasa Anual</label>
                                                <select class="form-control mr-sm-3" autofocus="" type="select" id="tasa" name="tasa" disabled>
                                                    <option value="5">5 %</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="Fecha">Sucursal</label>
                                                <select class="form-control mr-sm-3" id="sucursal" name="sucursal" disabled>
                                                    <option value="1514">CORPORATIVO</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="ejecutivo">Ejecutivo</label>
                                                <input id="ejecutivo" name="ejecutivo" class="form-control" value="<?= $_SESSION['nombre']; ?>" disabled />
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="margin-top:40px;">
                                            <button type="button" name="btnGeneraContrato" id="btnGeneraContrato" class="btn btn-primary" onclick="generaContrato(event)" style="border: 1px solid #c4a603; background: #ffffff" data-keyboard="false" disabled>
                                                <i class="fa fa-spinner" style="color: #1c4e63"></i>
                                                <span style="color: #1e283d"><b>GUARDAR DATOS Y PROCEDER AL COBRO </b></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
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


<?php echo $footer; ?>