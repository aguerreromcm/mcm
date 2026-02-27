<?= $header; ?>

<?php
$oculto = $Administracion[0]['TIPO_C'] === 'MAS POR TI' || $Administracion[0]['SITUACION'] !== 'E' ? 'style="display:none;"' : '';
?>

<div class="right_col">
    <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <label style="font-size: large;"> Registro de Pagos</label>
            <div class="clearfix"></div>
        </div>
        <div class="card card-danger col-md-5">
            <div class="card-header">
                <h5 class="card-title">Seleccione el tipo de búsqueda e ingrese el número de crédito </h5>
            </div>

            <div class="card-body">
                <form class="" action="/Pagos/PagosRegistro/" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-control mr-sm-3" style="font-size: 18px;" autofocus type="select" id="opcion_credito" name="opcion_credito" placeholder="000000">
                                <option value="credito">Crédito</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="text" onKeypress="if (event.keyCode < 9 || event.keyCode > 57) event.returnValue = false;" id="Credito" name="Credito" placeholder="000000" aria-label="Search">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card card-danger col-md-7">
            <ul class="nav navbar-nav navbar-right">
                <b style="font-size: 20px; color: #286090;">Su horario de cierre es: | <?= $Administracion[1]['HORA_CIERRE']; ?> a.m. |</b>
                <br>
                <b>Si su horario es incorrecto o necesita más tiempo, comuníquelo</b>
                <br>
                <b>al área correspondiente.</b>
            </ul>
        </div>
        <div class="card col-md-12">
            <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
            <div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_pago" onclick="BotonPago('<?= $Administracion[0]['SITUACION_NOMBRE']; ?>', '<?= $Administracion[0]['CICLO']; ?>');">
                    <i class="fa fa-plus"></i> Agregar Pago
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
            </div>
            <div class="row">
                <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                    <div class="col-md-3 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>

                        <div class="count" style="font-size: 14px"><?= $Administracion[0]['CLIENTE']; ?></div>
                        <span class="count_top badge" style="padding: 1px 1px; background: <?= $Administracion[0]['COLOR']; ?>">
                            <h5><b><i class="">SITUACIÓN: <?= $Administracion[0]['SITUACION_NOMBRE']; ?></i></b> </h5><?= $Administracion[0]['TIPO_C']; ?>
                        </span>

                    </div>
                    <div class="col-md-1 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i class="fa fa-clock-o"></i> Ciclo</span>
                        <div class="count" style="font-size: 14px" id="cicloActual"><?= $Administracion[0]['CICLO']; ?></div>
                    </div>
                    <div class="col-md-1 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px">Prestamo</span>
                        <div class="count" style="font-size: 14px" id="prestamo"> $ <?= number_format($Administracion[0]['MONTO']); ?></div>
                    </div>
                    <div class="col-md-1 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px">Día de Pago</span>
                        <div class="count" style="font-size: 14px"><?= $Administracion[0]['DIA_PAGO']; ?></div>
                    </div>
                    <div class="col-md-1 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px">Parcialidad</span>
                        <div class="count" style="font-size: 14px" id="parcialidad">$ <?= number_format($Administracion[0]['PARCIALIDAD']); ?></div>
                    </div>
                    <div class="col-md-2 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px">Sucursal</span>
                        <div class="count" style="font-size: 14px"><?= $Administracion[0]['SUCURSAL']; ?></div>
                    </div>
                    <div class="col-md-2 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px">Ejecutivo de cuenta</span>
                        <div class="count" style="font-size: 14px"><?= $Administracion[0]['EJECUTIVO']; ?> </div>
                    </div>
                </div>
            </div>
            <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover" id="pagosRegistrados">
                    <thead>
                        <tr>
                            <th>Medio</th>
                            <th>Consecutivo</th>
                            <th>CDGNS</th>
                            <th>Fecha</th>
                            <th>Ciclo</th>
                            <th>Monto</th>
                            <th>Tipo</th>
                            <th>Ejecutivo</th>
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

<div class="modal fade" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Agregar Registro de Pago (Cajera)</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="Fecha">Fecha</label>
                                    <input onkeydown="return false" type="date" class="form-control" id="Fecha" name="Fecha" min="<?= $inicio_f; ?>" max="<?= $fin_f; ?>" value="<?= $inicio_f; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="movil">Medio de Registro</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled placeholder="" value="CAJERA">
                                    <small id="emailHelp" class="form-text text-muted">Medio de registro del pago.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cdgns">CDGNS</label>
                                    <input type="number" class="form-control" id="cdgns" name="cdgns" readonly value="<?= $credito; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Número del crédito.</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" readonly value="<?= $Administracion[0]['CLIENTE']; ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ciclo">Ciclo</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="ciclo" name="ciclo">
                                        <option value="<?= $Administracion[0]['CICLO']; ?>"><?= $Administracion[0]['CICLO']; ?></option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" id="usuario" name="usuario" value="<?= $usuario; ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="tipo">Tipo de Operación</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="tipo" name="tipo" onchange="CambioOperacion(this,'<?= $Administracion[0]['CICLO']; ?>');">
                                        <option value="P">PAGO</option>
                                        <option value="X">PAGO ELECTRÓNICO</option>
                                        <option value="Y">PAGO EXCEDENTE</option>
                                        <option value="O">PAGO EXCEDENTE ELECTRÓNICO</option>
                                        <option value="M">MULTA</option>
                                        <option value="Z">MULTA GESTORES</option>
                                        <option value="L">MULTA ELECTRÓNICA</option>
                                        <option value="G">GARANTÍA</option>
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
                                        <!--  <option value="H">RECOMIENDA</option> -->
                                        <option value="S">SEGURO</option>
                                        <option <?= $oculto ?> value="B">AHORRO</option>
                                        <option <?= $oculto ?> value="F">AHORRO ELECTRÓNICO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="monto">Monto *</label>
                                    <input autofocus type="text" class="form-control" id="monto" name="monto" autocomplete="off" max="10000">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-info" id="infoTipoOp" style="display: none;">
                                    <i class="fa fa-info"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo">Nombre del Ejecutivo</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="ejecutivo" name="ejecutivo">
                                        <?= $status; ?>
                                    </select>
                                    <small id="emailHelp" class="form-text text-muted">Nombre del ejecutivo que entrega el pago.</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" id="enviaAdd"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_editar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Editar Registro de Pago (Cajera)</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_edit(); return false" id="Edit">
                        <div class="row">
                            <div class="col-md-4" style="display: none">
                                <div class="form-group">
                                    <label for="Fecha_e_r">Fecha</label>
                                    <input onkeydown="return false" type="date" class="form-control" id="Fecha_e_r" name="Fecha_e_r" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="Fecha_e">Fecha</label>
                                    <input onkeydown="return false" type="date" class="form-control" id="Fecha_e" name="Fecha_e" min="<?= $inicio_f; ?>" max="<?= $fin_f; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="movil_e">Medio de Registro</label>
                                    <input type="text" class="form-control" id="movil_e" aria-describedby="movil_e" disabled placeholder="" value="CAJERA">
                                    <small id="emailHelp" class="form-text text-muted">Medio de registro del pago.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cdgns_e">CDGNS</label>
                                    <input type="number" class="form-control" id="cdgns_e" name="cdgns_e" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Número del crédito.</small>
                                </div>
                            </div>

                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="nombre_e">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="nombre_e" name="nombre_e" readonly>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="secuencia_e">Secuencia</label>
                                    <input type="number" class="form-control" id="secuencia_e" name="secuencia_e" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ciclo_e">Ciclo</label>
                                    <input type="number" class="form-control" id="ciclo_e" name="ciclo_e" readonly>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="tipo_e">Tipo de Operación</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="tipo_e" name="tipo_e">
                                        <option value="P">PAGO</option>
                                        <option value="X">PAGO ELECTRÓNICO</option>
                                        <option value="Y">PAGO EXCEDENTE</option>
                                        <option value="M">MULTA</option>
                                        <option value="Z">MULTA GESTORES</option>
                                        <option value="L">MULTA ELECTRÓNICA</option>
                                        <option value="G">GARANTÍA</option>
                                        <option value="D">DESCUENTO</option>
                                        <option value="R">REFINANCIAMIENTO</option>
                                        <!--  <option value="H">RECOMIENDA</option> -->
                                        <option value="S">SEGURO</option>
                                        <option <?= $oculto ?> value="B">AHORRO</option>
                                        <option <?= $oculto ?> value="F">AHORRO ELECTRÓNICO</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="monto_e">Monto *</label>
                                    <input type="text" class="form-control" id="monto_e" name="monto_e">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo_e">Nombre del Ejecutivo</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="ejecutivo_e" name="ejecutivo_e">
                                        <?= $status; ?>
                                    </select>
                                    <small id="emailHelp" class="form-text text-muted">Nombre del ejecutivo que entrega el pago.</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="button" class="btn btn-primary" id="enviaEdit"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>