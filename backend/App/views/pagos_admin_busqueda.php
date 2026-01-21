<?= $header; ?>

<?php
$oculto = $Administracion[0]['TIPO_C'] === 'MAS POR TI' ? 'style="display:none;"' : '';
?>

<div class="right_col">
    <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <label style="font-size: large;"> Administración de Pagos</label>
            <div class="clearfix"></div>
        </div>

        <div class="card card-danger col-md-5">
            <div class="card-header">
                <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
            </div>

            <div class="card-body">
                <form class="" action="/Pagos/" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <select class="form-control sm-3 mr-sm-3" style="font-size: 18px;" autofocus type="select" id="" name="" placeholder="000000" aria-label="Search">
                                <option value="credito">Crédito</option>
                            </select>
                            <span id="availability1"></span>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="text" onKeypress="if (event.keyCode < 9 || event.keyCode > 57) event.returnValue = false;" id="Credito" name="Credito" placeholder="000000" aria-label="Search" value="<?= $credito; ?>">
                            <span id="availability1"></span>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card col-md-12">
            <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_agregar_pago" onclick="BotonPago('<?= $Administracion[0]['SITUACION_NOMBRE']; ?>');">
                <i class="fa fa-plus"></i> Agregar Pago
            </button>
            <hr style="border-top: 1px solid #787878; margin-top: 5px;">
            <div class="row">
                <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                    <div class="col-md-3 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>

                        <div class="count" style="font-size: 14px" id="nombreCliente"><?= $Administracion[0]['CLIENTE']; ?></div>
                        <span class="count_top badge" style="padding: 1px 1px; background: <?= $Administracion[0]['COLOR']; ?>">
                            <h5><b><i class="">SITUACIÓN: <?= $Administracion[0]['SITUACION_NOMBRE']; ?></i></b></h5><?= $Administracion[0]['TIPO_C']; ?>
                        </span>
                    </div>
                    <div class="col-md-1 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i class="fa fa-clock-o"></i> Ciclo</span>
                        <div class="count" style="font-size: 14px"><?= $Administracion[0]['CICLO']; ?> </div>
                    </div>
                    <div class="col-md-1 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i></i> Prestamo</span>
                        <div class="count" style="font-size: 14px"> $ <?= number_format($Administracion[0]['MONTO']); ?></div>
                    </div>
                    <div class="col-md-1 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i></i> Día de Pago</span>
                        <div class="count" style="font-size: 14px"><?= $Administracion[0]['DIA_PAGO']; ?></div>
                    </div>
                    <div class="col-md-1 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i></i> Parcialidad</span>
                        <div class="count" style="font-size: 14px">$ <?= number_format($Administracion[0]['PARCIALIDAD']); ?></div>
                    </div>
                    <div class="col-md-2 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i></i> Sucursal</span>
                        <div class="count" style="font-size: 14px"><?= $Administracion[0]['SUCURSAL']; ?></div>
                    </div>
                    <div class="col-md-2 col-sm-4  tile_stats_count">
                        <span class="count_top" style="font-size: 15px"><i></i> Ejecutivo de cuenta</span>
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
                            <th>Ultima Modificación</th>
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
                    <h4 class="modal-title" id="myModalLabel">Agregar Registro de Pago (Administrador Central)</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="Fecha">Fecha</label>
                                    <input onkeydown="return false" type="date" class="form-control" id="Fecha" name="Fecha" min="<?= $inicio_f; ?>" max="<?= $fin_f; ?>" value="<?= $fin_f; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>

                            <div class="col-md-4" style="display: none">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="usuario" name="usuario" value="<?= $usuario; ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="movil">Medio de Registro</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled placeholder="" value="CENTRAL">
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

                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" readonly value="<?= $Administracion[0]['CLIENTE']; ?>">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ciclo">Ciclo</label>
                                    <input type="number" class="form-control" id="ciclo" name="ciclo" min="1" value="<?= $Administracion[0]['CICLO']; ?>">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="tipo">Tipo de Operación</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="tipo" name="tipo">
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
                                            $cdgco == '027'


                                            || $usuario == 'AMGM' || $usuario == 'GASC'
                                        ) {
                                            $imp = '<option value="D">DESCUENTO DE CAMPAÑA POR LEALTAD</option>';

                                            echo $imp;
                                        }
                                        ?>
                                        <option value="R">REFINANCIAMIENTO</option>
                                        <option value="H">RECOMIENDA</option>
                                        <option value="S">SEGURO</option>
                                        <option <?= $oculto ?> value="B">AHORRO</option>
                                        <option <?= $oculto ?> value="F">AHORRO ELECTRÓNICO</option>
                                        <option <?= $oculto ?> value="E">ABONO AHORRO (AJUSTE)</option>
                                        <option <?= $oculto ?> value="A">RETIRO AHORRO (AJUSTE)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="monto">Monto *</label>
                                    <input autofocus type="text" class="form-control" id="monto" name="monto" autocomplete="off" max="10000">
                                </div>
                            </div>

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
                    <h4 class="modal-title" id="myModalLabel">Editar Registro de Pago (Administrador Central)</h4>
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
                                    <input type="text" class="form-control" id="movil_e" aria-describedby="movil_e" disabled placeholder="" value="CENTRAL">
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
                                    <input type="number" class="form-control" id="ciclo_e" name="ciclo_e">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="tipo_e">Tipo de Operación</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="tipo_e" name="tipo_e">
                                        <option value="P">PAGO</option>
                                        <option value="X">PAGO ELECTRÓNICO</option>
                                        <option value="Y">PAGO EXCEDENTE</option>
                                        <option value="O">PAGO EXCEDENTE ELECTRÓNICO</option>
                                        <option value="M">MULTA</option>
                                        <option value="Z">MULTA GESTORES</option>
                                        <option value="L">MULTA ELECTRÓNICA</option>
                                        <option value="G">GARANTÍA</option>
                                        <option value="D">DESCUENTO</option>
                                        <option value="R">REFINANCIAMIENTO</option>
                                        <option value="H">RECOMIENDA</option>
                                        <option value="S">SEGURO</option>
                                        <option <?= $oculto ?> value="B">AHORRO</option>
                                        <option <?= $oculto ?> value="F">AHORRO ELECTRÓNICO</option>
                                        <option <?= $oculto ?> value="E">ABONO AHORRO (AJUSTE)</option>
                                        <option <?= $oculto ?> value="A">RETIRO AHORRO (AJUSTE)</option>
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

<div class="modal fade" id="modal_admin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Editar registro de pago (Super Usuario)</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_edit(); return false" id="AdminInfoPAgo">
                        <div class="row">
                            <div class="col-md-4" style="display: none">
                                <div class="form-group">
                                    <label for="Fecha_admin_r">Fecha</label>
                                    <input onkeydown="return false" type="date" class="form-control" id="Fecha_admin_r" name="Fecha_admin_r" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="Fecha_admin">Fecha</label>
                                    <input onkeydown="return false" type="date" class="form-control" id="Fecha_admin" name="Fecha_admin">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en sistema.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="movil_admin">Medio de Registro</label>
                                    <input type="text" class="form-control" id="movil_admin" aria-describedby="movil_admin" disabled placeholder="" value="CAJERA">
                                    <small id="emailHelp" class="form-text text-muted">Medio de registro del pago.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cdgns_admin">CDGNS</label>
                                    <input type="number" class="form-control" id="cdgns_admin" name="cdgns_admin" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Número del crédito.</small>
                                </div>
                            </div>

                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="nombre_admin">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="nombre_admin" name="nombre_admin" readonly>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="secuencia_admin">Secuencia</label>
                                    <input type="number" class="form-control" id="secuencia_admin" name="secuencia_admin" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ciclo_admin">Ciclo</label>
                                    <input type="number" class="form-control" id="ciclo_admin" name="ciclo_admin" readonly>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="tipo_admin">Tipo de Operación</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="tipo_admin" name="tipo_admin">
                                        <option value="P">PAGO</option>
                                        <option value="X">PAGO ELECTRÓNICO</option>
                                        <option value="Y">PAGO EXCEDENTE</option>
                                        <option value="O">PAGO EXCEDENTE ELECTRÓNICO</option>
                                        <option value="M">MULTA</option>
                                        <option value="Z">MULTA GESTORES</option>
                                        <option value="L">MULTA ELECTRÓNICA</option>
                                        <option value="G">GARANTÍA</option>
                                        <option value="D">DESCUENTO</option>
                                        <option value="R">REFINANCIAMIENTO</option>
                                        <option value="H">RECOMIENDA</option>
                                        <option value="S">SEGURO</option>
                                        <option <?= $oculto ?> value="B">AHORRO</option>
                                        <option <?= $oculto ?> value="F">AHORRO ELECTRÓNICO</option>
                                        <option <?= $oculto ?> value="E">ABONO AHORRO (AJUSTE)</option>
                                        <option <?= $oculto ?> value="A">RETIRO AHORRO (AJUSTE)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="monto_admin">Monto *</label>
                                    <input type="text" class="form-control" id="monto_admin" name="monto_admin">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo_admin">Nombre del Ejecutivo</label>
                                    <select class="form-control mr-sm-3" autofocus type="select" id="ejecutivo_admin" name="ejecutivo_admin">
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
                <button type="button" class="btn btn-primary" id="editAdmin"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar cambios</button>
                <button type="button" class="btn btn-danger" id="deleteAdmin"><span class="glyphicon glyphicon-trash"></span> Eliminar registro</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_justificacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="tituloJustificacion"></h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="justificacion">Justificación *</label>
                                <textarea class="form-control" id="justificacion" rows="10" style="resize: none;" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="archivo">Si lo considera necesario, adjunte un archivo de soporte para su justificación</label>
                                <input type="file" class="form-control" id="archivo" accept=".pdf, .jpg, .png">
                                <small class="form-text text-muted">Solo se permiten archivos en formato PDF, JPG o PNG con un tamaño máximo de 2MB.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="tipoMovAdmin" name="cdgns_justificacion">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="glyphicon glyphicon-arrow-left"></i> Regresar</button>
                <button type="button" class="btn btn-primary" id="enviaJustificacion"><i class="glyphicon glyphicon-send"></i> Continuar</button>
            </div>
        </div>
    </div>
</div>




<?= $footer; ?>