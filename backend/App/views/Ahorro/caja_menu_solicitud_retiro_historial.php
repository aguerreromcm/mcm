<?php echo $header; ?>

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
                        <a class="navbar-brand">Mi espacio / Procesar solicitudes de retiro</a>
                    </div>
                    <div>
                        <ul class="nav navbar-nav">
                            <li class="linea"><a href="/Ahorro/CuentaCorriente/">
                                    <p style="font-size: 15px;">Ahorro cuenta corriente</p>
                                </a></li></a></li>
                            <li><a href="/Ahorro/ContratoCuentaCorriente/">
                                    <p style="font-size: 16px;">Nuevo contrato</p>
                                </a></li>
                            <li class="linea"><a href="/Ahorro/SolicitudRetiroCuentaCorriente/">
                                    <p style="font-size: 15px;">Solicitud de retiro</p>
                                </a></li></a></li>
                            <li class="linea"><a href="/Ahorro/HistorialSolicitudRetiroCuentaCorriente/">
                                    <p style="font-size: 15px;"><b>Procesar solicitudes de retiro</b></p>
                                </a></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fechaI">Fecha inical</label>
                                    <input type="date" class="form-control" id="fechaI" name="fechaI" value="<?= $fecha; ?>" onchange="validaFIF('fechaI', 'fechaF')" max="<?= $fecha; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fechaF">Fecha final</label>
                                    <input type="date" class="form-control" id="fechaF" name="fechaF" value="<?= $fecha; ?>" onchange="validaFIF('fechaI', 'fechaF')" max="<?= $fecha; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="estatus">Estatus</label>
                                    <select class="form-control" id="estatus" name="estatus">
                                        <option value="">TODOS</option>
                                        <option value="0">REGISTRADO</option>
                                        <option value="1" selected>APROBADO</option>
                                        <option value="2">RECHAZADO</option>
                                        <option value="3">ENTREGADO</option>
                                        <option value="4">DEVUELTO</option>
                                        <option value="5">CANCELADO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo">Tipo</label>
                                    <select class="form-control" id="tipo" name="tipo">
                                        <option value="">TODOS</option>
                                        <option value="1">EXPRESS</option>
                                        <option value="2">PROGRAMADO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" style="margin-top: 25px;">
                                    <button type="button" class="btn btn-primary" onclick=buscar()><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" style="margin-top: 25px;">
                                    <button id="btnExportaExcel" type="button" class="btn btn-success btn-circle" onclick=imprimeExcel()><i class="fa fa-file-excel-o"></i><b> Exportar a Excel</b></button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <hr>
                            <form name="all" id="all" method="POST">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover" id="hstSolicitudes">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Nombre cliente</th>
                                                <th>Código cliente</th>
                                                <th>Fecha actualización</th>
                                                <th>Monto solicitado</th>
                                                <th>Estatus</th>
                                                <th>Fecha entrega solicitada</th>
                                                <th>Acciones</th>
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