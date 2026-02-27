<?= $header; ?>

<?php
if ($Administracion[3]['LLAMADA_UNO'] == NULL) {
    $boton_ver_encuesta_l4 = 'style= display:none!important;';
    $check = '';
} else if ($Administracion[3]['FINALIZADA'] == '') {
    $boton_ver_encuesta_l4 = 'style= display:none!important;';
    $check = 'display:none;';
} else if ($Administracion[3]['FINALIZADA'] == '1') {
    $boton_ver_encuesta_l4 = 'style= display:none!important;';
    $check = 'display:none;';
}

$telefono = $datos_retiro['TELEFONO'];
$telefono = sprintf(
    '(%s) %s - %s',
    substr($telefono, 0, 3),
    substr($telefono, 3, 3),
    substr($telefono, 6, 4)
);
$color = $datos_retiro['ESTATUS'] == 'P' ? 'warning' : ($datos_retiro['ESTATUS'] == 'C' ? 'success' : 'danger');
$estilo_iniciar = $datos_retiro['ESTATUS'] == 'C' ? 'display:none;' : '';
$estilo_ver = $datos_retiro['ESTATUS'] != 'C' ? 'display:none;' : '';

?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 7px;">
            <div class="">
                <div class="box-tools pull-left" data-toggle="tooltip" title="" data-original-title="Regresa a la página anterior para verl el listado de solicitudes">
                    <h3>Validación de Retiro de ahorro</h3>
                </div>
                <div class="box-tools pull-right" data-toggle="tooltip" title="" data-original-title="Regresa a la página anterior para verl el listado de solicitudes">
                    <div class="btn-group" data-toggle="btn-toggle">
                        <a type="button" href="/CallCenter/Pendientes/" class="btn btn-default btn-sm"><i class="fa fa-undo"></i> Regresar a mis pendientes</a>
                    </div>
                </div>
            </div>
        </div>
        <span class="badge" style="background: #57687b">
            <h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Cliente | <i class="fa fa-user"></i>
                <?= $datos_retiro['NOMBRE_CLIENTE'] ?>
            </h4>
        </span>
        <div class="row">
            <div class="col-sm-10">
                <div class="panel panel-body">
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 19px"># Crédito</span>
                            <div class="count" style="font-size: 17px"><?= $datos_retiro['CREDITO'] ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 19px"> Ciclo</span>

                            <div class="count" style="font-size: 17px"><?= $datos_retiro['CICLO'] ?></div>
                        </div>

                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 19px"><i class="fa fa-clock-o"></i> Sucursal</span>
                            <div class="count" style="font-size: 17px"><?= $datos_retiro['SUCURSAL'] ?>|<?= $datos_retiro['NOMBRE_SUCURSAL'] ?></div>
                        </div>

                        <div class="col-md-4 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 19px"><i><i class="fa fa-calendar"></i></i> Ejecutivo</span>
                            <div class="count" style="font-size: 17px"><?= $datos_retiro['NOMBRE_EJECUTIVO'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="panel panel-body" style="display: flex; align-items: center; justify-content: center;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3281/3281312.png" height="97" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
                <div class="panel panel-body">
                    <table class="table table-striped table-bordered table-hover">
                        <tbody>
                            <tr>
                                <td style="font-size: 18px; background: #787878;color: white" colspan="3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <strong>
                                                Identificación del Cliente
                                            </strong>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>
                                                <span class="label label-<?= $color ?>" style="font-size: 95% !important; border-radius: 50em !important;" align="right"><?= $datos_retiro['ESTATUS_ETIQUETA'] ?></span>
                                            </strong>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 16px !important;" colspan="3">
                                    <div class="row">
                                        <div class="col-md-8" style="padding-top: 11px">
                                            <b><?= $datos_retiro['NOMBRE_CLIENTE'] ?> (<?= $datos_retiro['CLIENTE'] ?>) </b>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary" style="border: 1px solid #c4a603; background: #FFFFFF" data-toggle="modal" data-target="#modal_expediente" data-backdrop="static" data-keyboard="false">
                                                <i class="fa fa-eye" style="color: #1c4e63">&nbsp;</i><label style="color: #1c4e63">Expediente</label>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 16px"><strong>Contacto</strong></td>
                                <td style="font-size: 16px"><strong>Encuesta *</strong></td>
                                <td style="font-size: 16px"><strong><?= $etiqueta = $datos_retiro['ESTATUS'] != 'C' ? "Estatus Encuesta" : "Detalle Encuesta" ?></strong></td>
                            </tr>
                            <tr>
                                <td style="font-size: 19px; vertical-align: middle;">
                                    <div>
                                        <i class="fa fa-phone-square"></i>
                                        <?= $telefono ?>
                                    </div>
                                </td>
                                <td style="font-size: 19px; vertical-align: middle;">
                                    <div>
                                        <?= $datos_retiro['ESTATUS_ETIQUETA'] ?>
                                        <span style="font-size: 14px; display: block;">
                                            <?= $fecha_final = $datos_retiro['ESTATUS'] != 'C' ? '' : $datos_retiro['ULTIMA_LLAMADA'] ?>
                                        </span>
                                    </div>
                                </td>
                                <td style="font-size: 16px; vertical-align: middle;">
                                    <button type="button" id="boton_iniciar" class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF; <?= $estilo_iniciar ?>" data-toggle="modal" data-target="#modal_encuesta" data-backdrop="static" data-keyboard="false">
                                        <i class="fa fa-edit" style="color: #1c4e63"></i><label style="color: #1c4e63"><?= $etiqueta = $datos_retiro['INTENTOS'] > 0 ? 'Reintentar' : 'Iniciar' ?></label>
                                    </button>

                                    <button type="button" class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF; <?= $estilo_ver ?>" data-toggle="modal" data-target="#modal_ver_encuesta" data-backdrop="static" data-keyboard="false">
                                        <i class="fa fa-eye" style="color: #1c4e63"></i>&nbsp;<span style="color: #1c4e63">Ver</span>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 18px; background: #cccccc;color: #707070" colspan="3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            Usted a registrado <strong>
                                                <span class="label label-<?= $color ?>" style="font-size: 95% !important; border-radius: 50em !important;">
                                                    <?= $datos_retiro['INTENTOS'] ?></span>
                                            </strong>
                                            intentos de llamada al CLIENTE.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel panel-body">
                    <table class="table table-striped table-bordered table-hover">
                        <tbody>
                            <tr>
                                <td style="font-size: 18px; background: #440101;color: white"><strong>Mi Resumen ejecutivo para Call Center</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row" style="margin-top: 5px; margin-bottom: 10px;">
                        <input type="hidden" id="estatus" value="<?= $datos_retiro['ESTATUS'] ?>">
                        <div class="col-md-12">
                            <label for="Fecha">Comentarios Internos (Operaciones) *</label>
                            <textarea name="comentarios_internos" id="comentarios_internos" class="form-control" rows="3" cols="50" placeholder="Escribe tus comentarios INICIALES una vez que hayas marcado al número del cliente por primera vez" style="background-color: white; resize: none; margin-bottom:5px"><?= $datos_retiro['COMENTARIO_INTERNO'] ?></textarea>

                            <button type="button" id="guarda_internos" class="btn btn-primary">
                                <i class="fa fa-save"></i> <b>Guardar</b>
                            </button>
                        </div>

                        <div class="col-md-12">
                            <label for="Fecha">Comentarios Externos (Sucursal) *</label>
                            <textarea name="comentarios_externos" id="comentarios_externos" class="form-control" rows="3" cols="50" placeholder="Escribe tus comentarios FINALES, una vez que hayas completado el proceso correspondiente" style="background-color: white; resize: none; margin-bottom:5px"><?= $datos_retiro['COMENTARIO_EXTERNO'] ?></textarea>
                            <button type="button" id="guarda_externos" class="btn btn-primary">
                                <i class="fa fa-save"></i> <b>Guardar</b>
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-7">
                            <div class="form-group">
                                <label for="estatus_solicitud"> Estatus Final de la Solicitud *</label>
                                <select class="form-control" id="estatus_solicitud">
                                    <option selected disabled value="">Seleccione una opción</option>
                                    <option value="P">PENDIENTE</option>
                                    <option value="R">RECHAZADA POR EL CLIENTE</option>
                                    <option value="C">CANCELADA POR CLIENTE</option>
                                    <option value="V">VALIDADA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-5" style="display: flex; flex-direction: column; justify-content: center; padding-top: 12px;">
                            <button type="button" id="termina_retiro" class="btn btn-success btn-lg" style="background: #2da92d; color: #ffffff; ">
                                Concluir Solicitud <i class="fa fa-hand-pointer-o" style="color: #ffffff"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_encuesta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title"><?= $datos_retiro['NOMBRE_CLIENTE'] ?>, LLAMADA #<label id="titulo" name="titulo"><?= $datos_retiro['INTENTOS'] + 1 ?></label></h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_registro">Fecha de trabajo</label>
                                <input type="text" class="form-control" id="fecha_registro" value="<?= date("Y-m-d h:i:s"); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="credito">No. de Crédito</label>
                                <input type="text" class="form-control" id="credito" value="<?= $datos_retiro['CREDITO'] ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefono">Núm. telefono del cliente</label>
                                <input type="text" class="form-control" id="telefono" value="<?= $telefono ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_llamada">Tipo de llamada que esta realizando *</label>
                                <select class="form-control mr-sm-3" id="tipo_llamada">
                                    <option selected disabled value="">Seleccione una opción</option>
                                    <option value="VOZ">VOZ</option>
                                    <option value="WHATSAPP">WHATSAPP</option>
                                    <option value="VIDEO LLAMADA">VIDEO LLAMADA</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" style="margin-bottom: 10px; text-align: center;">
                        <h3><b>Preguntas de validación</b></h3>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="p1">1.- ¿Usted solicito un retiro? *</label>
                                <select class="form-control mr-sm-3" id="p1">
                                    <option selected disabled value="">Seleccione una opción</option>
                                    <option value="S">SI</option>
                                    <option value="N">NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="p2">2.- ¿Cuanto solicito? *</label>
                                <select class="form-control mr-sm-3" id="p2">
                                    <option selected disabled value="">Seleccione una opción</option>
                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                    <option value="N">NO RESPONDIO</option>
                                </select>
                                <p style="color: #007700"><b>R: <?= '$ ' . number_format($datos_retiro['CANT_SOLICITADA'], 2, '.', ',') ?></b></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="demo activado">
                            <input type="radio" name="completo" id="completo1" value="1" checked="checked">
                            <label for="completo1">Llamada exitosa</label>
                            <br>
                            <input type="radio" name="completo" id="completo2" value="0">
                            <label for="completo2">La llamada no se completo satisfactoriamente</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove">&nbsp;</span>Cancelar</button>
                <button type="button" id="guarda_encuesta_retiro" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk">&nbsp;</span>Guardar Respuestas</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_ver_encuesta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title"><?= $datos_retiro['NOMBRE_CLIENTE'] ?></label></h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fecha_registro">Fecha de trabajo</label>
                                <input type="text" class="form-control" id="fecha_registro" value="<?= $datos_retiro['ULTIMA_LLAMADA'] ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="credito">No. de Crédito</label>
                                <input type="text" class="form-control" id="credito" value="<?= $datos_retiro['CREDITO'] ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telefono">Núm. telefono del cliente</label>
                                <input type="text" class="form-control" id="telefono" value="<?= $telefono ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="tipo_llamada">Tipo de llamada que se realizó</label>
                                <input type="text" class="form-control" id="tipo_llamada" value="<?= $datos_retiro['TIPO_ULTIMA_LLAMADA'] ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tipo_llamada">Intentos</label>
                                <input type="text" class="form-control" id="tipo_llamada" value="<?= $datos_retiro['INTENTOS'] ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row" style="margin-bottom: 10px; text-align: center;">
                        <h3><b>Preguntas de validación</b></h3>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="r1_etiqueta">1.- ¿Usted solicito un retiro? *</label>
                                <input type="hidden" id="r1" value="<?= $datos_retiro['R1'] ?>">
                                <input type="text" class="form-control" id="r1_etiqueta" value="<?= $datos_retiro['R1'] == 'S' ? 'SI' : 'NO' ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="r2_etiqueta">2.- ¿Cuanto solicito? *</label>
                                <input type="hidden" id="r2" value="<?= $datos_retiro['R2'] ?>">
                                <input type="text" class="form-control" id="r2_etiqueta" value="<?= $datos_retiro['R2'] == 'S' ? 'RESPONDIO CORRECTAMENTE' : 'NO RESPONDIO' ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove">&nbsp;</span>Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_expediente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 1000px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">EXPEDIENTE DEL CLIENTE: <?= $datos_retiro['NOMBRE_CLIENTE']; ?> (NÚMERO DE CRÉDITO: <?= $datos_retiro['CREDITO']; ?>)</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="card-body pb-0 px-0 px-md-12 text-center text-sm-left ">
                                <img src="https://cdn-icons-png.flaticon.com/512/4625/4625102.png" height="97" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre completo</label>
                                        <input onkeydown="return false" type="text" class="form-control" value="<?= $datos_retiro['NOMBRE_CLIENTE']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha de nacimiento</label>
                                        <input onkeydown="return false" type="text" class="form-control" value="<?= $datos_retiro['FECHA_NACIMIENTO']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>Edad</label>
                                        <input onkeydown="return false" type="text" class="form-control" value="<?= $datos_retiro['EDAD']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Genéro</label>
                                        <input onkeydown="return false" type="text" class="form-control" value="<?= $datos_retiro['SEXO']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>Domicilio</label>
                                        <input onkeydown="return false" type="text" class="form-control" value="<?= $datos_retiro['DOMICILIO']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Contacto</label>
                                        <input onkeydown="return false" type="text" class="form-control" value="<?= $telefono ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cerrar Expediente</button>
            </div>
        </div>
    </div>
</div>

<style>
    .activado input[type=radio]:checked+label {
        color: #2da92d;
        font-size: 20px;
    }
</style>

<?= $footer; ?>