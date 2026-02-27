<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Gestión de Clientes en Telaraña</h3>
                <div class="clearfix"></div>
            </div>
            <div class="card col-md-12">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_vincular">
                    <i class="fa fa-plus"></i> Vincular Invitado
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="clientes">
                        <thead>
                            <tr>
                                <th>Código Crédito</th>
                                <th>Ciclo Invitación</th>
                                <th>Código Cliente</th>
                                <th>Nombre Cliente</th>
                                <th>Código Invitado</th>
                                <th>Nombre Invitado</th>
                                <th>Fecha Invitación</th>
                                <th>Acciones</th>
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

<div class="modal fade" id="modal_vincular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Vincular invitados - Recomienda más paga menos</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <span id="availability1">Buscar por:</span>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" name="tipoAnfitrion" id="anfiXcred" checked>
                            <label for="anfiXcred">Crédito</label>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" name="tipoAnfitrion" id="anfiXcgdns">
                            <label for="anfiXcgdns">Cliente</label>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <span id="availability1">Código:</span>
                                <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="Cliente" name="Cliente" value="" placeholder="000000" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary" id="btnAnfitrion">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <span id="availability1">Nombre de anfitrión:</span>
                                <input type="text" class="form-control" id="MuestraCliente" name="MuestraCliente" value="" readonly>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <span id="availability1">Fecha de Registro:</span>
                                <input type="date" class="form-control" id="Fecha" name="Fecha" value=<?= $fecha ?> min=<?= $fechaMin ?> max=<?= $fechaMax ?>>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12">
                            <span id="availability1">Buscar por:</span>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" name="tipoInvitado" id="invXcred" checked>
                            <label for="invXcred">Crédito</label>
                        </div>
                        <div class="col-md-4">
                            <input type="radio" name="tipoInvitado" id="invXcdgns">
                            <label for="invXcdgns">Cliente</label>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <span id="availability1">Código:</span>
                                <input type="text" onkeypress=validarYbuscar(event) class="form-control" id="Invitado" name="Invitado" value="" placeholder="000000" disabled required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary" id="btnInvitado" disabled>
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <span id="availability1">Nombre de invitado:</span>
                                <input type="text" class="form-control" id="MuestraInvitado" name="MuestraInvitado" value="" readonly>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <span id="availability1">Ciclo de invitación:</span>
                                <input type="text" class="form-control" id="Ciclo" name="Ciclo" value="" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnVincular" disabled><i class="glyphicon glyphicon-floppy-disk"></i> Guardar Registro</button>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>