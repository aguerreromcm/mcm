<?= $header; ?>

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
                <div class="col-md-5" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5833/5833897.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Arqueo </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/5833/5833855.png -->
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
                        <a class="navbar-brand">Mi espacio / Arqueo</a>
                        &nbsp;&nbsp;
                    </div>
                    <div>
                        <ul class="nav navbar-nav">
                            <li>
                                <a onclick=mostrarAhorro() href="">
                                    <p style="font-size: 16px;"><b>Resumen de arqueos</b></p>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fechaInicio">Fecha inicio</label>
                                        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" max="<?= $fechaFin; ?>" value="<?= $fechaInicio; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fechaFin">Fecha fin</label>
                                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" max="<?= $fechaFin; ?>" value="<?= $fechaFin; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-top: 23px;">
                                    <button id="btnBsk" class="btn btn-primary" onclick=buscarArqueos()><i class="fa fa-search"></i><b> Buscar</b></button>
                                </div>
                            </div>
                            <div class="row" style="padding-top: 20px;">
                                <div class="col-md-3">
                                    <button id="btnArqueo" class="btn btn-primary" onclick=mostrarModal()><i class="glyphicon glyphicon-floppy-disk"></i><b> Generar arqueo</b></button>
                                </div>
                                <div class="col-md-3">
                                    <button id="btnExportaExcel" class="btn btn-success btn-circle" onclick=imprimeExcel()><i class="fa fa-file-excel-o"></i><b> Exportar a Excel</b></button>
                                </div>
                            </div>
                            <hr>
                            <div class="card col-md-12">
                                <form name="all" id="all" method="POST">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover" id="tblArqueos">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Código ejecutivo</th>
                                                    <th>Nombre ejecutivo</th>
                                                    <th>Código sucursal</th>
                                                    <th>Nombre sucursal</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?= $tabla; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade in" id="modalArqueo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: block; padding-right: 15px;"> -->
<div class="modal fade" id="modalArqueo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="text-align: center;">
                <h4 class="modal-title" id="myModalLabel">Arqueo de caja</h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit=noSubmit() id="frmModal">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fechaArqueo">Fecha</label>
                                    <input class="form-control" id="fechaArqueo" name="fechaArqueo" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sucursalArqueo">Código sucursal</label>
                                    <input class="form-control" id="sucursalArqueo" name="sucursalArqueo" value="<?= $_SESSION['cdgco_ahorro']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="nombreSucursal">Nombre sucursal</label>
                                    <input class="form-control" id="nombreSucursal" name="nombreSucursal" value="<?= $nomSucursal; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cajeraArqueo">Código Cajera</label>
                                    <input class="form-control" id="cajeraArqueo" name="cajeraArqueo" value="<?= $_SESSION['usuario']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nombreCajera">Nombre Cajera</label>
                                    <input class="form-control" id="nombreCajera" name="nombreCajera" value="<?= $_SESSION['nombre']; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <table style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; font-size:20px;">Billetes</th>
                                        <th style="text-align: center; font-size:20px;">Monedas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding: 10px; border: #000000 1px solid;">
                                            <?= $tablaBilletes; ?>
                                        </td>
                                        <td style="padding: 10px; border: #000000 1px solid;">
                                            <?= $tablaMonedas; ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">
                                            <div class="col-md-12" style="text-align: center;">
                                                <p>En caso de tener monedas de denominación mayor a $10.00, ingresar como billetes.</p>
                                            </div>
                                            <hr>
                                            <div class="row" style="display: flex; justify-content: flex-end; align-items: center;">
                                                <div class="col-md-4" style="text-align: right;">
                                                    <label for="totalEfectivo">Total efectivo:</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <input style="text-align: right;" class="form-control" id="totalEfectivo" name="totalEfectivo" value="0.00" readonly />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button class="btn btn-primary" id="btnRegistrarArqueo" value="enviar" onclick=registraArqueo() disabled><span class="glyphicon glyphicon-floppy-disk"></span> Registrar arqueo</button>
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


<?= $footer; ?>