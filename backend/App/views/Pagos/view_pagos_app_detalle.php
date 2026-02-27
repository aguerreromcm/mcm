<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="card col-md-12">
                <form name="all" id="all" method="POST">
                    <div class="row">
                        <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                            <div class="col-md-4 col-sm-4  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Ejecutivo</span>
                                <div class="count" style="font-size: 18px"><?= $Ejecutivo ?></div>
                            </div>
                            <div class="col-md-2 col-sm-2  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i>#</i> de Pagos Validados</span>
                                <div class="count" style="font-size: 30px; color: #030303"><span style="font-size: 30px; color: #030303" id="validados_r" name="validados_r"><?= $DetalleGlobal['TOTAL_VALIDADOS']; ?></span> DE <span style="font-size: 30px; color: #030303" id="total_r" name="total_r"><?= $DetalleGlobal['TOTAL_PAGOS']; ?></span></div>
                                <div class="count" style="font-size: 30px; color: #030303"><span style="font-size: 30px; color: #030303; display: none;" id="validados_r_total" name="validados_r_total"><?= $DetalleGlobal['TOTAL_PAGOS_TOTAL']; ?></span></div>
                            </div>
                            <div class="col-md-3 col-sm-4  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Monto Validado</span>
                                <div class="count" style="font-size: 35px; color: #368a05">$<?= number_format($DetalleGlobal['TOTAL'], 2); ?></div>
                            </div>
                            <div class="col-md-3 col-sm-4  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Terminar Validación</span>
                                <div class="count" style="font-size: 35px; color: #368a05">
                                    <button type="button" id="procesar_pagos" class="btn btn-primary" onclick="boton_resumen_pago();" style="border: 1px solid #c4a603; background: #FFFFFF" data-keyboard="false">
                                        <i class="fa fa-spinner" style="color: #1c4e63"></i> <span style="color: #1E283D"><b>Procesar Pagos Validados</b></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-9">
                            <p><b><span class="fa fa-sticky-note">&nbsp;</span>Nota:Si ya valido el pago y es correcto marque la casilla (Validado)</b></p>
                        </div>
                        <div class="col-md-3">
                            <b style="font-size: 20px; color: #286090;">Su horario de cierre es: <?= $DetalleGlobal['HORA_CIERRE']; ?> a.m.</b>
                            <br>
                            Si su horario es incorrecto o necesita más tiempo, comuníquelo al área correspondiente.
                        </div>
                    </div>
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                                <tr>
                                    <th>Secuencia</th>
                                    <th>Cliente</th>
                                    <th>Movimiento</th>
                                    <th>Comentarios</th>
                                    <th>Fecha Captura</th>
                                    <th>Estatus</th>
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

<div class="modal fade" id="modal_agregar_horario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Editar el Pago del Ejecutivo (App)</h4>
                </center>
            </div>
            <form onsubmit="enviar_add_edit_app(); return false" id="Add_Edit_Pago">
                <div class="modal-body">
                    <div class="container-fluid">
                        <input type="hidden" class="form-control" id="fecha" name="fecha">
                        <input type="hidden" class="form-control" id="grupo" name="grupo">
                        <input type="hidden" class="form-control" id="ciclo" name="ciclo">
                        <input type="hidden" class="form-control" id="secuencia" name="secuencia">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_cl">Fecha de trabajo *</label>
                                <input onkeydown="return false" type="text" class="form-control" id="fecha_cl" name="fecha_cl" value="<?= date("Y-m-d h:i:s"); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_pago_detalle">Tipo de Pago *</label>
                                <select class="form-control" autofocus type="select" id="tipo_pago_detalle" name="tipo_pago_detalle">
                                    <option value="0" disabled>Seleccione una opción</option>
                                    <option value="P">PAGO</option>
                                    <option value="X">PAGO ELECTRÓNICO</option>
                                    <option value="Y">PAGO EXCEDENTE</option>
                                    <option value="O">PAGO EXCEDENTE ELECTRÓNICO</option>
                                    <option value="M">MULTA</option>
                                    <option value="Z">MULTA GESTORES</option>
                                    <option value="L">MULTA ELECTRÓNICA</option>
                                    <option value="D">DESCUENTO</option>

                                    <?php
                                    if (
                                        $cdgco == '007' ||
                                        $cdgco == '014' ||
                                        $cdgco == '020' ||
                                        $cdgco == '025' ||
                                        $cdgco == '026' ||
                                        $cdgco == '027' ||
                                        $usuario == 'AMGM' ||
                                        $usuario == 'GASC'
                                    ) echo '<option value="D">DESCUENTO DE CAMPAÑA POR LEALTAD</option>';
                                    ?>

                                    <option value="R">REFINANCIAMIENTO</option>
                                    <option value="S">SEGURO</option>
                                    <option value="B">AHORRO</option>
                                    <option value="F">AHORRO ELECTRÓNICO</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="monto_detalle">Monto Registrado *</label>
                                <input type="text" class="form-control" id="monto_detalle" readonly name="monto_detalle" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nuevo_monto">Nuevo Monto *</label>
                                <input type="text" class="form-control" id="nuevo_monto" name="nuevo_monto" placeholder="" value="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="comentario_detalle">Comentario/Incidencia (motivo de cambio) *</label>
                                <textarea class="form-control" id="comentario_detalle" name="comentario_detalle" placeholder="Comentarios exclusivos para la cajera"></textarea>
                                <small id="emailHelp" class="form-text text-muted">Detalle el motivo del cambio, para el tipo de pago o el nuevo monto</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" name="agregar" class="btn btn-primary" value="enviar" id="btn_terminar" disabled><span class="glyphicon glyphicon-floppy-disk"></span>Terminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_resumen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Resumen - Recepción de Pagos (App) - Folio: <?= $barcode; ?></h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add_edit_app(); return false" id="Add_Edit_Pago">
                        <div class="row">
                            <div class="col-md-4" style="display: none">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="usuario" name="usuario" value="<?= $usuario; ?>">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="movil">Medio de Registro</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled placeholder="" value="APP MÓVIL">
                                    <small id="emailHelp" class="form-text text-muted">Medio de registro.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ejecutivo">Nombre del Ejecutivo</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="ejecutivo" name="ejecutivo">
                                        <option value="<?= $cdgpe_ejecutivo; ?>"><?= $ejecutivo; ?></option>
                                    </select>
                                    <small id="emailHelp" class="form-text text-muted">Nombre del ejecutivo que entrega el pago.</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="dataTable_wrapper">
                                <table style="margin-bottom: 0px;" class="table table-striped table-bordered table-hover" id="terminar_resumen" name="terminar_resumen">
                                    <thead>
                                        <tr style="color:#000 !important; text-align:center;">
                                            <th style="display: none; color:#000 !important; text-align:center;"></th>
                                            <th style="color:#000 !important; text-align:center; width: 100px;">Fecha</th>
                                            <th style="color:#000 !important; text-align:center;">Cliente</th>
                                            <th style="color:#000 !important; text-align:center;">Ciclo</th>
                                            <th style="color:#000 !important; text-align:center;">Nombre</th>
                                            <th style="color:#000 !important; text-align:center;">Tipo</th>
                                            <th style="color:#000 !important; text-align:center;">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?= $tabla_resumen; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card card-danger col-md-12" style="padding: 2px">
                            <ul class="nav navbar-nav navbar-right">
                                <b style="font-size: 20px; color: #173b00;">Total: $<?= number_format($DetalleGlobal['TOTAL'], 2); ?></b>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="boton_terminar('<?= $barcode; ?>');"><span class="glyphicon glyphicon-floppy-disk"></span>Cobrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver comprobante de pago (como en Solicitudes de retiro) -->
<div class="modal fade" id="modalComprobantePago" tabindex="-1" role="dialog" aria-labelledby="modalComprobantePagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title" id="modalComprobantePagoLabel">Comprobante de pago</h4>
                </center>
            </div>
            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="container-fluid">
                    <div id="comprobantePagoContainer" class="text-center">
                        <img src="/img/wait.gif" alt="Cargando..." id="loadingComprobantePago">
                        <img src="" alt="Comprobante" class="img-fluid" id="comprobantePagoImg" style="display:none; max-width: 100%; height: auto;" />
                        <p id="comprobantePagoError" class="text-danger" style="display:none;">No se encontró el comprobante para este pago.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Footer button removed as requested -->
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>