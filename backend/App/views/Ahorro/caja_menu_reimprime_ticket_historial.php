<?php echo $header; ?>

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
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5833/5833855.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Arqueo </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/5833/5833897.png -->
                </div>
            </a>

            <a id="link" href="/Ahorro/ReimprimeTicket/">
                <div class="col-md-5" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/7325/7325359.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
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
                            <a class="navbar-brand">Mi espacio / Reimpresión de tickets</a>
                            &nbsp;&nbsp;
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li class="linea">
                                    <a href="/Ahorro/ReimprimeTicket/">
                                        <p style="font-size: 16px;">Tickets</p>
                                    </a>
                                </li>
                                <li><a href="/Ahorro/ReimprimeTicketSolicitudes/">
                                        <p style="font-size: 15px;"><b>Historial de solicitudes</b></p>
                                    </a>
                                </li>
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
                                            <option value="0">PENDIENTE</option>
                                            <option value="1">RECHAZADA</option>
                                            <option value="2">APROBADA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" style="margin-top: 25px;">
                                        <button type="button" class="btn btn-primary" onclick=buscar()><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group" style="margin-top: 25px;">
                                        <button id="btnExportaExcel" type="button" class="btn btn-success btn-circle" onclick=imprimeExcel()><i class="fa fa-file-excel-o"></i><b> Exportar a Excel</b></button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-8">
                                    <p>Podrás encontrar tus solicitudes a traves del historial, en el que se marcaran con tres estatus: <br> <span class="count_top" style="font-size: 18px"><i class="fa fa-clock-o" style="color: #ac8200"></i></span> PENDIENTE (Está en validación). <br><span class="count_top" style="font-size: 18px"><i class="fa fa-close" style="color: #ac1d00"></i></span> RECHAZADA (Tú solicitud fue rechazada por tesorería). <br><span class="count_top" style="font-size: 18px"><i class="fa fa-print" style="color: #26b99a"></i></span> APROBADA (Puedes imprimir tu ticket una vez más).</p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <form name="all" id="all" method="POST">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover" id="solicitudes">
                                            <thead>
                                                <tr>
                                                    <th>ID Ticket</th>
                                                    <th>Contrato</th>
                                                    <th>Fecha Solicitud</th>
                                                    <th>Motivo</th>
                                                    <th>Estatus</th>
                                                    <th>Autoriza</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?= $tabla; ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_ticket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Reimpresión de tickets</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add_sol(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha">Fecha de solicitud*</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="fecha" name="fecha" value="<?php echo $fecha_actual; ?>" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="folio">Folio del ticket*</label>
                                    <input type="text" class="form-control" id="folio" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Medio de registro del pago.</small>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo">Motivo *</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="motivo" name="motivo">
                                        <option value="SE EXTRAVIO">MOTIVO 1</option>
                                        <option value="SE EXTRAVIO">MOTIVO 2</option>
                                        <option value="SE EXTRAVIO">MOTIVO 3</option>
                                        <option value="SE EXTRAVIO">MOTIVO 4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tipo">Escriba brevemente el motivo de la reimpresión *</label>
                                    <textarea type="text" class="form-control" id="direccion" name="direccion" rows="3" cols="50"></textarea>
                                </div>
                            </div>


                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Terminar Solicitud</button>
                </form>
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