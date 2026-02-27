<?php echo $header; ?>

<?= $mensaje = "Este valor es fijo, no se puede modificar";
$spnMensaje = "<span class='text-danger' id='spnMensaje'>" . $mensaje . "</span>";
?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Registrar Quejas REUNE</h3>
            </div>
            <div class="card col-md-12">
                <div class="card-header">
                    <h5 class="card-title">Ingrese los datos solicitados</h5>
                </div>
                <div class="col-md-12">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="InstitucionClave">Institución</label>
                            <input class="form-control" id="InstitucionClave" value="Financiera Cultiva, S.A.P.I. de C.V., SOFOM, E.N.R." disabled />
                            <?= $spnMensaje ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Sector">Sector</label>
                            <input class="form-control" id="Sector" value="Sociedades Financieras de Objeto Múltiple E.N.R." disabled />
                            <?= $spnMensaje ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasTrim">Trimestre a informar *</label>
                            <select class="form-control" id="ConsultasTrim" onchange=validaRequeridos()>
                                <?= $meses; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="NumConsultas">Número de quejas</label>
                            <input class="form-control" id="NumConsultas" value="1" disabled />
                            <?= $spnMensaje ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasFolio">Número de folio *</label>
                            <input class="form-control" id="ConsultasFolio" oninput=validaRequeridos() />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasEstatusCon">Estatus *</label>
                            <select class="form-control" id="ConsultasEstatusCon">
                                <option value="1">PENDIENTE</option>
                                <option value="2">CONCLUIDO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasFecAten">Fecha de atención *</label>
                            <input type="date" class="form-control" id="ConsultasFecAten" value=<?= $fecha ?> />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="EstadosId">Estado *</label>
                            <select class="form-control" id="EstadosId" onchange=validaRequeridos() disabled></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasFecRecepcion">Fecha de la queja *</label>
                            <input class="form-control" id="ConsultasFecRecepcion" type="date" value="<?= $fecha ?>" oninput=validaRequeridos() />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="MediosId">Medio de recepción *</label>
                            <select class="form-control" id="MediosId" onchange=validaRequeridos()>
                                <?= $medios; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Producto">Producto o servicio *</label>
                            <select class="form-control" id="Producto" onchange=validaRequeridos()>
                                <?= $productos; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="CausaId">Causa de la queja *</label>
                            <select class="form-control" id="CausaId" onchange=validaRequeridos()>
                                <?= $causas; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="ConsultasCP">CP *</label>
                            <input class="form-control" id="ConsultasCP" maxlength="5" onkeypress="validaEntradaCP(event)" oninput=validaRequeridos() />
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="tbnCP">Buscar</label>
                            <button class="btn btn-primary" onclick=validaCP() id="btnCP">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasMpioId">Municipio *</label>
                            <select class="form-control" id="ConsultasMpioId" onchange=validaRequeridos() disabled></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasLocId">Tipo de localidad *</label>
                            <select class="form-control" id="ConsultasLocId" onchange=validaRequeridos() disabled></select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ConsultasColId">Colonia *</label>
                            <select class="form-control" id="ConsultasColId" onchange=validaRequeridos() disabled></select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ConsultascatnivelatenId">Nivel de atención o contacto</label>
                        <select class="form-control" id="ConsultascatnivelatenId" disabled></select>
                        <?= $spnMensaje ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ConsultasPori">PORI *</label>
                        <select class="form-control" id="ConsultasPori" onchange=validaRequeridos()>
                            <option value="SI">SI</option>
                            <option value="NO">NO</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="modal-footer">
                    <button id="btnAgregar" class="btn btn-primary" onclick=registrarQueja(event) disabled>
                        <span class="glyphicon glyphicon-floppy-disk"></span> Registrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>