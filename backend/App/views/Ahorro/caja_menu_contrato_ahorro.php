<?php echo $header; ?>

<?php

use App\components\TarjetaMano;

$izquierda = new TarjetaMano('izquierda');
$derecha = new TarjetaMano('derecha');

?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <a id="link" href="/Ahorro/CuentaCorriente/">
                <div class="col-md-5" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5575/5575939.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Ahorro </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/5575/5575938.png -->
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
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2995/2995390.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Ahorro Peque </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2995/2995467.png -->
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
            <div class="modal-content">
                <div class="modal-header" style="padding-bottom: 0px">
                    <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                        <a class="navbar-brand">Mi espacio / Nuevo contrato de ahorro corriente</a>
                    </div>
                    <div>
                        <ul class="nav navbar-nav">
                            <li class="linea"><a href="/Ahorro/CuentaCorriente/">
                                    <p style="font-size: 15px;">Ahorro cuenta corriente</p>
                                </a></li></a></li>
                            <li><a href="/Ahorro/ContratoCuentaCorriente/">
                                    <p style="font-size: 16px;"><b>Nuevo contrato</b></p>
                                </a></li>
                            <li class="linea"><a href="/Ahorro/SolicitudRetiroCuentaCorriente/">
                                    <p style="font-size: 15px;">Solicitud de retiro</p>
                                </a></li></a></li>
                            <li class="linea"><a href="/Ahorro/HistorialSolicitudRetiroCuentaCorriente/">
                                    <p style="font-size: 15px;">Procesar solicitudes de retiro</p>
                                </a></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <p>Para la apertura de una cuenta ahorro corriente, el cliente debe estar registrado en SICAFIN, en caso contrario, es necesario solicitar el alta en SICAFIN con la ADMINISTRADORA.</p>
                                        <hr>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="movil">Código de cliente SICAFIN *</label>
                                        <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="clienteBuscado" name="clienteBuscado" placeholder="000000" value="<?= $cliente ?>" required>
                                    </div>
                                    <div class="col-md-2" style="padding-top: 25px">
                                        <button type="button" class="btn btn-primary" onclick="buscaCliente()">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="card col-md-12">
                                        <p><b><span class="fa fa-sticky-note"></span> Identificación del cliente</b></p>
                                    </div>
                                    <div class="card col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="fechaRegistro">Fecha de registro</label>
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
                                                    <label for="nombre">Nombre del cliente</label>
                                                    <input type="text" class="form-control" id="nombre" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="curp">CURP</label>
                                                    <input type="text" class="form-control" id="curp" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="edad">Edad</label>
                                                    <input type="text" class="form-control" id="edad" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="direccion">Dirección</label>
                                                    <textarea type="text" style="resize: none;" class="form-control" id="direccion" rows="3" cols="50" readonly>
                                                        </textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7" style="min-height: 400px;">
                                    <form id="registroInicialAhorro" name="registroInicialAhorro">
                                        <p><b><span class="fa fa-sticky-note"></span> Datos básicos de apertura para la cuenta de Ahorro Corriente</b></p>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="Fecha">Fecha de apertura</label>
                                                    <input type="text" class="form-control" id="fecha" name="fecha" value="<?= $fecha; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="tipo">Tasa anual</label>
                                                    <select class="form-control mr-sm-3" autofocus="" type="select" id="tasa" name="tasa" disabled>
                                                        <option value="5">5 %</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="monto_min">Monto mínimo <span class="fa fa-info"></span></label>
                                                    <input type="text" class="form-control" id="monto_min" name="monto_min" value="<?= number_format($saldoMinimoApertura, 2, '.', ','); ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="manejo_cta">Manejo de ahorro cuenta</label>
                                                    <select class="form-control mr-sm-3" id="manejo_cta" name="manejo_cta" readonly>
                                                        <option value="1">APLICA</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="form-group">
                                                    <label for="sucursal">Sucursal *</label>
                                                    <select class="form-control mr-sm-3" id="sucursal" name="sucursal" disabled>
                                                        <?= $sucursales; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="ejecutivo_comision">Comisión ejecutivo *</label>
                                                    <select class="form-control mr-sm-3" id="ejecutivo_comision" name="ejecutivo_comision" readonly>
                                                        <?= $ejecutivos; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="tipo">Nombre completo beneficiario *</label>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="tipo">Parentesco *</label>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="tipo">Porcentaje *</label>
                                            </div>
                                        </div>
                                        <div class="row" id="ben1" style="opacity:1">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" id="beneficiario_1" name="beneficiario_1" oninput=camposLlenos(event) disabled maxlength="30" />
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control mr-sm-3" id="parentesco_1" name="parentesco_1" onchange=camposLlenos(event) disabled>
                                                    <?= $opcParentescos; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" min=1 max=100 class="form-control" id="porcentaje_1" name="porcentaje_1" oninput=camposLlenos(event) disabled>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" id="btnBen1" class="btn btn-primary" onclick=addBeneficiario(event) disabled>
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row" id="ben2" style="opacity:0">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" id="beneficiario_2" name="beneficiario_2" oninput=camposLlenos(event) disabled maxlength="30" />
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control mr-sm-3" id="parentesco_2" name="parentesco_2" onchange=camposLlenos(event) disabled>
                                                    <?= $opcParentescos; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" min=1 max=100 class="form-control" id="porcentaje_2" name="porcentaje_2" oninput=camposLlenos(event) disabled>
                                            </div>
                                            <div class=" col-md-1">
                                                <button type="button" id="btnBen2" class="btn btn-primary" onclick=addBeneficiario(event) disabled>
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row" id="ben3" style="opacity:0">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" id="beneficiario_3" name="beneficiario_3" oninput=camposLlenos(event) disabled maxlength="30" />
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control mr-sm-3" id="parentesco_3" name="parentesco_3" onchange=camposLlenos(event) disabled>
                                                    <?= $opcParentescos; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" min=1 max=100 class="form-control" id="porcentaje_3" name="porcentaje_3" oninput=camposLlenos(event) disabled>
                                            </div>
                                            <div class=" col-md-1">
                                                <button type="button" id="btnBen3" class="btn btn-primary" onclick=addBeneficiario(event) disabled>
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row" id="marcadores" style="height: 20px; opacity: 0">
                                            <div class="col-md-4" style="display: flex; justify-content: center; align-items: center;">
                                                <input id="contratoOK" type="hidden" />
                                                <i class="fa fa-times red" id="chkCreacionContrato"></i><a href="javascript:void(0);" onclick=reImprimeContrato(event) style="color: #000; cursor: default;" id="lnkContrato">Creación del contrato</a>
                                            </div>
                                            <div class="col-md-4" style="display: flex; justify-content: center; align-items: center;">
                                                <i class="fa fa-times red" id="chkPagoApertura"></i><span style="color: #000; user-select: none;">Deposito de apertura</span>
                                            </div>
                                            <div class="col-md-4" style="display: flex; justify-content: center; align-items: center;">
                                                <i class="fa fa-times red" id="chkRegistroHuellas"></i><a href="javascript:void(0);" onclick=mostrarModalHuellas() style="color: #000; cursor: default;" id="lnkHuellas">Registro de Huellas</a>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="height: 20px;">
                                            <button id="btnGeneraContrato" class="btn btn-primary" onclick="generaContrato(event)" style="border: 1px solid #c4a603; background: #ffffff; display: none;">
                                                <i class="fa fa-spinner" style="color: #1c4e63"></i>
                                                <span style="color: #1e283d" id="btnGuardar"><b>GUARDAR DATOS Y PROCEDER AL COBRO</b></span>
                                            </button>
                                        </div>
                                        <br>
                                    </form>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade in" id="modal_registra_huellas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: block; padding-right: 15px;"> -->
<div class="modal fade" id="modal_registra_huellas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button id="cerrar_modal" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Registro de huellas dactilares</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="nombre_cliente_huellas">Nombre del cliente</label>
                                <input type="text" class="form-control" id="nombre_cliente_huellas" name="nombre_cliente" value="<?php echo $Cliente[0]['NOMBRE']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="codigo_cl_huellas">Código de cliente SICAFIN</label>
                                <input type="number" class="form-control" id="codigo_cl_huellas" name="codigo_cl" value="<?php echo $credito; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <?= $izquierda->mostrar(); ?>
                        <?= $derecha->mostrar(); ?>
                    </div>
                    <div class="row">
                        <div id="notificacionesHuella" style="display: flex; justify-content: center; align-items: center; width: 100%; height: 100px;">
                            <span id="mensajeHuella" style="font-size: x-large; text-align: center;"><?= $mensajeCaptura ?></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="registraHuellas" class="btn btn-primary" onclick=guardarHuellas() disabled><span class="glyphicon glyphicon-floppy-disk"></span>Registrar huellas</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade in" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: block; padding-right: 15px;"> -->
<div class="modal fade" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <center>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Registro de pago por apertura y ahorro inicial cuenta corriente</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="AddPagoApertura">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nombre_cliente">Nombre del cliente</label>
                                    <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" value="<?php echo $Cliente[0]['NOMBRE']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="mdlCurp">CURP</label>
                                    <input type="text" class="form-control" id="mdlCurp" name="mdlCurp" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fecha_pago">Fecha del depósito</label>
                                    <input type="text" class="form-control" id="fecha_pago" name="fecha_pago" readonly>
                                </div>
                            </div>
                            <div class="col-md-4" style="display: none!important;">
                                <div class="form-group">
                                    <label for="contrato">Número de contrato</label>
                                    <input type="text" class="form-control" id="contrato" name="contrato" aria-describedby="contrato" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="codigo_cl">Código de cliente SICAFIN</label>
                                    <input type="number" class="form-control" id="codigo_cl" name="codigo_cl" value="<?php echo $credito; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="nombre_ejecutivo">Nombre del ejecutivo</label>
                                    <input type="text" class="form-control" id="nombre_ejecutivo" name="nombre_ejecutivo" value="<?= $_SESSION['nombre']; ?>" readonly>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-md-3" style="font-size: 18px; padding-top: 5px;">
                                <label style="color: #000000">Movimiento:</label>
                            </div>
                            <div class="col-md-4" style="text-align: center; font-size: 18px; padding-top: 5px;">
                                <input type="radio" name="esDeposito" onchange=cambioMovimiento(event) checked>
                                <label for="deposito">Depósito</label>
                            </div>
                            <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                <h3>$</h3>
                            </div>
                            <div class="col-md-4" style="padding-top: 5px;">
                                <input type="number" class="form-control" id="monto" name="monto" min="1" max="100000" placeholder="0.00" style="font-size: 25px;" oninput=validaDeposito(event) onkeydown=soloNumeros(event)>
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
                            <div class="col-md-1">
                                <h4>+</h4>
                            </div>
                            <div class="col-md-7">
                                <h4>Depósito</h4>
                            </div>
                            <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                <h4>$</h4>
                            </div>
                            <div class="col-md-3">
                                <input class="form-control" id="deposito" name="deposito" value="0.00" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">
                                <h4>-</h4>
                            </div>
                            <div class="col-md-7">
                                <h4>Inscripción</h4>
                            </div>
                            <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                <h4>$</h4>
                            </div>
                            <div class="col-md-3">
                                <input class="form-control" id="inscripcion" name="inscripcion" value="<?= $costoInscripcion ?>.00" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <h4>Saldo inicial de la cuenta ahorro corriente</h4>
                            </div>
                            <div class="col-md-1" style="display: flex; justify-content: flex-end;">
                                <h4>$</h4>
                            </div>
                            <div class="col-md-3">
                                <input class="form-control" id="saldo_inicial" name="saldo_inicial" value="0.00" readonly>
                                <input type="hidden" class="form-control" id="sma" name="sma" value="<?= $saldoMinimoApertura ?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="display: flex; justify-content: center; color: red; height: 20px;">
                                <label id="tipSaldo" style="opacity:0; font-size: 18px;">El saldo inicial debe ser mínimo de $<?= $saldoMinimoApertura - $costoInscripcion ?>.00</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="registraDepositoInicial" name="agregar" class="btn btn-primary" value="enviar" onclick=pagoApertura(event) disabled><span class="glyphicon glyphicon-floppy-disk"></span> Registrar depósito</button>
                        </div>
                    </form>
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
</style>

<?php echo $footer; ?>