<?= $header; ?>

    <div class="right_col">
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <div class="panel panel-body" style="margin-bottom: 7px;">
                <div class="">
                    <div class="box-tools pull-left" data-toggle="tooltip" title="" data-original-title="Regresa a la página anterior para verl el listado de solicitudes">
                        <h3> Validación de Cliente y Aval</h3>
                    </div>
                    <div class="box-tools pull-right" data-toggle="tooltip" title="" data-original-title="Regresa a la página anterior para verl el listado de solicitudes">
                        <div class="btn-group" data-toggle="btn-toggle">
                            <a type="button" href="/CallCenter/Pendientes/" class="btn btn-default btn-sm"><i class="fa fa-undo"></i> Regresar a mis pendientes</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-10">
                <span class="badge" style="background: #57687b">
                    <h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Crédito | <i class="fa fa-user"></i> <?php

                        use Dom\HTMLElement;

                        echo $Administracion[0]['CLIENTE']; ?></h4>
                </span>
                    <div class="panel panel-body" style="padding: 0px">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                                        <div class="col-md-2 col-sm-4  tile_stats_count" style="padding-bottom: 1px !important; margin-bottom: 1px !important;">
                                            <span class="count_top" style="font-size: 19px"><i class="">#</i> Crédito</span>

                                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['NO_CREDITO']; ?></div>
                                        </div>
                                        <div class="col-md-2 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"> Ciclo</span>

                                            <div class="count" style="font-size: 17px"> <?php echo $Administracion[0]['CICLO']; ?></div>
                                        </div>

                                        <div class="col-md-3 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i class="fa fa-clock-o"></i> Sucursal</span>
                                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['ID_SUCURSAL']; ?>|<?php echo $Administracion[0]['SUCURSAL']; ?></div>
                                        </div>

                                        <div class="col-md-4 col-sm-4  tile_stats_count">
                                            <span class="count_top" style="font-size: 19px"><i><i class="fa fa-calendar"></i></i> Ejecutivo</span>
                                            <div class="count" style="font-size: 17px"><?php echo $Administracion[0]['EJECUTIVO']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2" style="padding-top: 33px">
                    <div class="panel panel-body" style="padding: 0px">
                        <div class="x_content">
                            <div class="col-sm-12 text-center text-sm-left">
                                <div class="card-body pb-0 px-0 px-md-12 text-center text-sm-left ">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3281/3281312.png" height="97" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-body" style="margin-bottom: 7px;">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                            <tr>
                                                <?php

                                                if ($Administracion[3]['LLAMADA_UNO'] == NULL) {
                                                    $titulo_l1 = "Pendiente de validar";
                                                    $color_titulo_l1 = "warning";
                                                    //-----
                                                    $titulo_l3 = "Estatus Encuesta";
                                                    //-----
                                                    $titulo_l4 = "PENDIENTE";
                                                    $boton_ver_encuesta_l4 = 'style= display:none!important;';
                                                    $hora_l4 = $Administracion[3]['HORA'];
                                                    $titulo_boton_l4 = "Iniciar";
                                                    $ocultar_boton_l4_iniciar = '';
                                                    //------
                                                    $desactivar_aval = 'true';
                                                    $check = '';
                                                } else if ($Administracion[3]['FINALIZADA'] == '') {
                                                    $titulo_l1 = "Pendiente de validar";
                                                    $color_titulo_l1 = "warning";
                                                    //-----
                                                    $titulo_l3 = "Estatus Encuesta";
                                                    //-----
                                                    $titulo_l4 = "EN ESPERA";
                                                    $boton_ver_encuesta_l4 = 'style= display:none!important;';
                                                    $hora_l4 = $Administracion[3]['HORA'];
                                                    $titulo_boton_l4 = "Reintentar";
                                                    $ocultar_boton_l4_iniciar = '';
                                                    //------
                                                    $desactivar_aval = 'false';
                                                    $check = 'display:none;';
                                                } else if ($Administracion[3]['FINALIZADA'] == '1') {
                                                    $titulo_l1 = "Validada";
                                                    $color_titulo_l1 = "success";
                                                    //-----
                                                    $titulo_l3 = "Detalle Encuesta";
                                                    //-----
                                                    $titulo_l4 = "FINALIZADA";
                                                    $boton_ver_encuesta_l4 = 'style= display:none!important;';
                                                    if ($Administracion[3]['REACTIVACION']) {
                                                        $titulo_boton_l4 = "Reintentar (Reactivada)";
                                                        $ocultar_boton_l4_iniciar = '';
                                                    } else {
                                                        $titulo_boton_l4 = "Reintentar";
                                                        $ocultar_boton_l4_iniciar = 'style= display:none!important;';
                                                    }
                                                    $hora_l4 = "Primer llamada: " . $Administracion[3]['HORA_LLAMADA_UNO'] . "<br>" . "Última Llamada: " . $Administracion[3]['HORA_LLAMADA_DOS'];
                                                    //------
                                                    $desactivar_aval = 'false';
                                                    $check = 'display:none;';
                                                }
                                                ?>
                                                <td style="font-size: 18px; background: #787878;color: white" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <strong>
                                                                Identificación del Cliente
                                                            </strong>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <strong>
                                                                <span class="label label-<?php echo $color_titulo_l1; ?>" style="font-size: 95% !important; border-radius: 50em !important;" align="right"><?php echo $titulo_l1 ?></span>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px !important;" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-8" style="padding-top: 11px">
                                                            <b><?php echo $Administracion[0]['CLIENTE']; ?> (<?php echo $Administracion[0]['ID_CLIENTE']; ?>) </b>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-primary" style="border: 1px solid #c4a603; background: #FFFFFF" data-toggle="modal" data-target="#modal_expediente" data-backdrop="static" data-keyboard="false">
                                                                <i class="fa fa-eye" style="color: #1c4e63"></i> <label style="color: #1c4e63">Expediente</label>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px" colspan="10"><strong>Contacto</strong></td>
                                                <td style="font-size: 16px" colspan="3"><strong>Encuesta *</strong></td>
                                                <td style="font-size: 16px" colspan="5"><strong><?php echo $titulo_l3 ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 19px" ; colspan="10">
                                                    <i class="fa fa-phone-square"></i> <?php
                                                    $format = "(" . substr($Administracion[1]['TELEFONO'], 0, 3) . ")" . " " . substr($Administracion[1]['TELEFONO'], 3, 3) . " - " . substr($Administracion[1]['TELEFONO'], 6, 4);
                                                    echo $format; ?>
                                                </td>
                                                <td style="font-size: 19px;" colspan="3">
                                                    <?php echo $titulo_l4 ?>
                                                    <button type="button" <?php echo $boton_ver_encuesta_l4 ?> class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF" data-toggle="modal" data-target="#modal_ver_encuesta_cliente" data-backdrop="static" data-keyboard="false">
                                                        <i class="fa fa-eye" style="color: #1c4e63"></i> <label style="color: #1c4e63">Ver</label>
                                                    </button>
                                                </td>
                                                <td style="font-size: 16px;" colspan="5">
                                                    <div>
                                                        <?php echo $hora_l4 ?>
                                                    </div>
                                                    <div>
                                                        <button type="button" <?php echo $ocultar_boton_l4_iniciar ?> id="boton_iniciar" class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF" data-toggle="modal" data-target="#modal_encuesta_cliente" data-backdrop="static" data-keyboard="false">
                                                            <i class="fa fa-edit" style="color: #1c4e63"></i> <label style="color: #1c4e63"><?php echo $titulo_boton_l4 ?></label>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 18px; background: #cccccc;color: #707070" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            Usted a registrado <strong>
                                                                <span class="label label-<?php echo $color_titulo_l1; ?>" style="font-size: 95% !important; border-radius: 50em !important;" align="right"><?php if ($Administracion[3]['NUMERO_INTENTOS_CL'] == NULL) {
                                                                        $num = '0';
                                                                    } else {
                                                                        $num = $Administracion[3]['NUMERO_INTENTOS_CL'];
                                                                    }
                                                                    echo $num; ?></span>
                                                            </strong>
                                                            intentos de llamada al CLIENTE.
                                                        </div>

                                                        <div class="col-md-4">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>
                                        <div style="<?php echo $check; ?>">
                                            <input class="form-check-input" type="checkbox" value="" id="check_2610" name="check_2610" onclick="check_2610('');">
                                            <label class="form-check-label" for="flexCheckDefault" style="font-size: 15px">
                                                Información Inconsistente
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-body" style="margin-bottom: 7px;">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="dataTable_wrapper" style="display: <?php echo $visible; ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                            <tr>
                                                <?php
                                                $titulo_l3_a = "Estatus Encuesta";
                                                $boton_ver_encuesta_l4_a = 'style= display:none!important;';

                                                if ($Administracion[4]['NUM_LLAM'] == NULL) {
                                                    $titulo_l4_a = "PENDIENTE";
                                                } else if ($Administracion[4]['FINALIZADA'] == '') {
                                                    $titulo_l4_a = "EN ESPERA";
                                                } else if ($Administracion[4]['FINALIZADA'] == '1') {
                                                    $titulo_l3_a = "Detalle Encuesta";
                                                    $titulo_l4_a = "FINALIZADA";
                                                }
                                                ?>
                                                <td style="font-size: 18px; background: #73879C;color: white" colspan="4">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <strong>
                                                                Identificación de Avales
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px"><strong>Nombre</strong></td>
                                                <td style="font-size: 14px"><strong>Contacto</strong></td>
                                                <td style="font-size: 14px"><strong>Encuesta *</strong></td>
                                                <td style="font-size: 14px"><strong><?= $titulo_l3_a; ?></strong> </td>
                                            </tr>
                                            <?php
                                            foreach ($Administracion[2] as $key => $value) {
                                                $telefono_aval = "(" . substr($value['TELEFONO'], 0, 3) . ")" . " " . substr($value['TELEFONO'], 3, 3) . " - " . substr($value['TELEFONO'], 6, 4);
                                                $btn_ver = $desactivar_aval == 'false' ? "onclick='showEncuestaAval($key)'" : 'onclick="InfoDesactivaEncuesta();"';
                                                if ($value['FINALIZADA'] == 1) {
                                                    $estatus = "Validada";
                                                    $estatus_color = "success";
                                                    $resumen = "Primer llamada: {$value['LLAMADA_1']}";
                                                    if ($value['LLAMADA_2'] != '-') $resumen .= "<br><br>Última Llamada: {$value['LLAMADA_2']}";
                                                    $ocultar_boton = 'display:none!important;';
                                                } else {
                                                    $estatus = "Pendiente";
                                                    $estatus_color = "warning";
                                                    $resumen = "";
                                                    $ocultar_boton = '';
                                                    $titulo_boton = $value['INTENTOS'] == 0 ? 'Iniciar' : "Reintentar" . ($Administracion[3]['REACTIVACION'] ? " (Reactivada)" : "");
                                                }

                                                echo <<<HTML
                                                        <tr>
                                                            <td style="font-size: 15px;">
                                                                    {$value['NOMBRE']}<br>({$value['CODIGO']})
                                                            </td>
                                                            <td style="font-size: 16px">
                                                                <div style="width: 150px; margin: auto;">
                                                                    <i class="fa fa-phone-square"></i> $telefono_aval
                                                                </div>
                                                            </td>
                                                            <td style="font-size: 14px;">
                                                                <div style="display:flex; flex-direction:column; width: 100px; margin: auto;">
                                                                    <span class="label label-$estatus_color" style="font-size: 95%; border-radius: 100px;">$estatus</span>
                                                                    <span>Intentos: <strong>
                                                                            <span class="label label-$estatus_color" style="font-size: 95%; border-radius: 100px;">
                                                                                {$value['INTENTOS']}
                                                                            </span>
                                                                        </strong></span>
                                                                    <button type="button" $boton_ver_encuesta_l4_a class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF" data-toggle="modal" data-target="#modal_ver_encuesta_aval" data-backdrop="static" data-keyboard="false">
                                                                        <i class="fa fa-eye" style="color: #1c4e63"></i> <label style="color: #1c4e63">Ver</label>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                            <td style="font-size: 14px;">
                                                                <div style="display:flex; flex-direction:column; max-width: 230px; margin: auto;">
                                                                    $resumen
                                                                    <div>
                                                                        <button type="button" class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF; $ocultar_boton" data-backdrop="static" data-keyboard="false" $btn_ver>
                                                                            <i class="fa fa-edit" style="color: #1c4e63"></i> <label style="color: #1c4e63">$titulo_boton</label>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    HTML;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="panel panel-body" style="margin-bottom: 7px; margin-top: 15px;">
                        <div class="x_content">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                            <tr>
                                                <td style="font-size: 18px; background: #440101;color: white" colspan="6"><strong>Mi Resumen ejecutivo para Call Center</strong></td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <input style="display: none!important;" onkeydown="return false" type="text" class="form-control" id="cliente_encuesta" name="cliente_encuesta" value="<?php echo $titulo_l4; ?>" readonly>
                                        <input style="display: none!important;" onkeydown="return false" type="text" class="form-control" id="cliente_aval" name="cliente_aval" value="<?php echo $titulo_l4_a; ?>" readonly>

                                        <form onsubmit="enviar_comentarios_add(); return false" id="Add_comentarios" class="col-sm-8">
                                            <?php
                                            if ($Administracion[3]['PRORROGA'] == 2) {
                                                $com_prorroga = $Administracion[3]['COMENTARIO_PRORROGA'];
                                                /** @noinspection LanguageDetectionInspection */
                                                $comentario_prorroga = <<<html
                                                    <div class="col-lg-4">
                                                    <label for="comentarios_prorroga">Comentarios de Prorroga *</label>
                                                    <textarea name="comentarios_prorroga" id="comentarios_prorroga" class="form-control" rows="7" cols="50" placeholder="Escribe tus comentarios FINALES, una vez que hayas completado el proceso correspondiente" style="background-color: white; resize: none"> $com_prorroga</textarea>

                                                    <button type="submit" name="agregar_resumen" value="enviar_resumen" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> <b>Guardar</b>
                                                    </button>
                                                </div>
html;
                                                $tamaño_col = '4';
                                            } else {
                                                $tamaño_col = '6';
                                            }
                                            ?>
                                            <div class="col-lg-<?php echo $tamaño_col; ?>">
                                                <label for="Fecha">Comentarios Internos (Operaciones) *</label>
                                                <textarea name="comentarios_iniciales" id="comentarios_iniciales" class="form-control" rows="7" cols="50" placeholder="Escribe tus comentarios INICIALES una vez que hayas marcado al número del cliente o aval, por primera vez" style="background-color: white; resize: none"><?php echo $Administracion[3]['COMENTARIO_INICIAL']; ?></textarea>

                                                <button type="submit" name="agregar_resumen" value="enviar_resumen" class="btn btn-primary">
                                                    <i class="fa fa-save"></i> <b>Guardar</b>
                                                </button>
                                            </div>

                                            <div class="col-lg-<?php echo $tamaño_col; ?>">
                                                <label for="Fecha">Comentarios Externos (Sucursal) *</label>
                                                <textarea name="comentarios_finales" id="comentarios_finales" class="form-control" rows="7" cols="50" placeholder="Escribe tus comentarios FINALES, una vez que hayas completado el proceso correspondiente" style="background-color: white; resize: none"><?php echo $Administracion[3]['COMENTARIO_FINAL']; ?></textarea>

                                                <button type="submit" name="agregar_resumen" value="enviar_resumen" class="btn btn-primary">
                                                    <i class="fa fa-save"></i> <b>Guardar</b>
                                                </button>
                                            </div>

                                            <?php echo $comentario_prorroga; ?>

                                        </form>

                                        <form onsubmit="enviar_resumen_add(); return false" id="Add_comentarios" class="col-sm-4">
                                            <div class="col-lg-8">
                                                <div class="form-group">
                                                    <div>
                                                        <label for="estatus_solicitud"> Estatus Final de la Solicitud *</label>
                                                        <select class="form-control mr-sm-4" autofocus type="select" id="estatus_solicitud" name="estatus_solicitud">
                                                            <option selected disabled value="">Seleccione una opción</option>
                                                            <option value="PENDIENTE">PENDIENTE</option>
                                                            <option value="PENDIENTE, CORRECCION DE DATOS">PENDIENTE, CORRECCIÓN DE DATOS</option>
                                                            <option value="CANCELADA, NO LOCALIZADOS">CANCELADA, NO LOCALIZADOS</option>
                                                            <option value="CANCELADA POR CLIENTE">CANCELADA POR CLIENTE</option>
                                                            <option value="CANCELADA POR POLITICAS">CANCELADA POR POLÍTICAS</option>
                                                            <option value="CANCELADA POR GERENTE">CANCELADA POR GERENTE</option>
                                                            <option value="LISTA CON OBSERVACION">LISTA CON OBSERVACIÓN</option>
                                                            <option value="LISTA SIN INCIDENCIA">LISTA SIN INCIDENCIA</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div>
                                                        <label for="vobo_gerente"> VoBo Gerente Regional (Opcional)</label>
                                                        <select class="form-control mr-sm-3" autofocus type="select" id="vobo_gerente" name="vobo_gerente">
                                                            <option selected disabled value="">Seleccione una opción</option>
                                                            <option value="S">SI</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-8">
                                                <div class="card card-danger col-md-12">
                                                    <ul class="nav navbar-nav navbar-right">
                                                        <button type="submit" id="terminar_solicitud" name="terminar_solicitud" class="btn btn-success btn-lg" style="background: #2da92d; color: #ffffff; ">
                                                            Concluir Solicitud <i class="fa fa-hand-pointer-o" style="color: #ffffff"></i>
                                                        </button>
                                                    </ul>
                                                </div>
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
    </div>

    <div class="modal fade" id="modal_encuesta_cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 1300px !important;">
            <div class="modal-content">
                <form onsubmit="enviar_add_cl(); return false" id="Add_cl">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important; background: #787878FF">CLIENTE</span>
                        <center>
                            <h4 class="modal-title"><?php echo $Administracion[0]['CLIENTE']; ?>, LLAMADA #<label id="titulo" name="titulo"><?php if ($Administracion[3]['NUMERO_INTENTOS_CL'] == NULL) {
                                        $num = '1';
                                    } else {
                                        $num = $Administracion[3]['NUMERO_INTENTOS_CL'] + 1;
                                    }
                                    echo $num; ?></label></h4>
                        </center>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="fecha_solicitud">Fecha de solicitud *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo $Administracion[0]['FECHA_SOL']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="adicional">MAS POR TI *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="adicional" name="adicional" value="<?php echo $Administracion[0]['CREDITO_ADICIONAL']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="cdgco">CDGNS *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="cdgns" name="cdgns" value="<?php echo $Administracion[0]['NO_CREDITO']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="cdgco">CDGCO *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="cdgco" name="cdgco" value="<?php echo $suc; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="cdgre">CDGPE *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="cdgpe" name="cdgpe" value="<?php echo $cdgpe; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="cdgre">CDGRE *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="cdgre" name="cdgre" value="<?php echo $reg; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none;">
                                    <div class="form-group">
                                        <label for="cliente_id">Cliente ID CLIENTE *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="cliente_id" name="cliente_id" value="<?php echo $Administracion[0]['ID_CLIENTE']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="fecha_cl">Fecha de trabajo *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="fecha_cl" name="fecha_cl" value="<?php echo date("Y-m-d h:i:s"); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="ciclo_cl">Ciclo del Crédito Solicitado*</label>
                                        <input type="text" class="form-control" id="ciclo_cl" name="ciclo_cl" aria-describedby="ciclo_cl" readonly placeholder="" value="<?php echo $Administracion[0]['CICLO']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="movil_cl">Núm. telefono del cliente *</label>
                                        <input type="text" class="form-control" id="movil_cl" name="movil_cl" aria-describedby="movil_cl" readonly placeholder="" value="<?php
                                        $format = "(" . substr($Administracion[1]['TELEFONO'], 0, 3) . ")" . " " . substr($Administracion[1]['TELEFONO'], 3, 3) . " - " . substr($Administracion[1]['TELEFONO'], 6, 4);
                                        echo $format; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tipo_llamada_cl">Tipo de llamada que esta realizando *</label>
                                        <select class="form-control mr-sm-3" autofocus type="select" id="tipo_llamada_cl" name="tipo_llamada_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="VOZ">VOZ</option>
                                            <option value="WHATSAPP">WHATSAPP</option>
                                            <option value="VIDEO LLAMADA">VIDEO LLAMADA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <hr>
                                <h5><b>Preguntas de validación</b></h5>
                                <hr style="background: black">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="uno_cl">1.- ¿Qué edad tiene? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="uno_cl" name="uno_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                            <p style="color: #007700"><b>R: <?php echo $Administracion[1]['EDAD']; ?> AÑOS</b></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="dos_cl">2.- ¿Cuál es su fecha de nacimiento? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="dos_cl" name="dos_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                            <p style="color: #007700"><b>R: <?php echo $Administracion[1]['NACIMIENTO']; ?></b></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php if ($Administracion[0]['CREDITO_ADICIONAL'] == 1): ?>
                                                <label for="monto_solicitado">¿Esta solicitando el crédito más por ti (adicional)? *</label>
                                            <?php else: ?>

                                                <label for="tres_cl">3.- ¿Cuál es su domicilio completo? *</label>

                                            <?php endif; ?>

                                            <select class="form-control mr-sm-3" autofocus type="select" id="tres_cl" name="tres_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                            <p style="color: #007700"><b style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'none' : 'block'; ?>;">R: <?php echo $Administracion[1]['CALLE']; ?>, <?php echo $Administracion[1]['COLONIA']; ?>, <?php echo $Administracion[1]['MUNICIPIO']; ?>, <?php echo $Administracion[1]['ESTADO']; ?>, C.P:<?php echo $Administracion[1]['CP']; ?>.</b></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">

                                            <?php if ($Administracion[0]['CREDITO_ADICIONAL'] == 1): ?>
                                                <label for="monto_solicitado">¿Qué monto está solicitando? *</label>
                                            <?php else: ?>

                                                <label for="cuatro_cl">4.- ¿Tiempo viviendo en este domicilio? *</label>

                                            <?php endif; ?>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="cuatro_cl" name="cuatro_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                            <p style="color: #007700"><b style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'block' : 'none'; ?>;"> </b></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'none' : 'block'; ?>;">
                                        <div class="form-group">
                                            <label for="cinco_cl">5.- Actualmente, ¿Cuál es su principal fuente de ingresos? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="cinco_cl" name="cinco_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                            <p style="color: #007700"><b>R: <?php echo $Administracion[1]['ACT_ECO']; ?></b></p>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin-top: 2px !important; margin-bottom: 2px !important;" style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'none' : 'block'; ?>;">
                                <div class="row" style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'none' : 'block'; ?>;">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="seis_cl">6.- ¿Cuál es el nombre completo de sus avales? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="seis_cl" name="seis_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'none' : 'block'; ?>;">
                                        <div class="form-group">
                                            <label for="siete_cl">7.- ¿Qué relación directa tiene con sus avales? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="siete_cl" name="siete_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'none' : 'block'; ?>;">
                                        <div class="form-group">
                                            <label for="ocho_cl">8.- ¿Actividad económica de sus avales? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="ocho_cl" name="ocho_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'none' : 'block'; ?>;">
                                        <div class="form-group">
                                            <label for="nueve_cl">9.- ¿Número telefónico de sus avales? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="nueve_cl" name="nueve_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-md-offset-3" style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'none' : 'block'; ?>;">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                            <thead>
                                            <tr>
                                                <th colspan="3" style="text-align: center;">Respuestas Aval</th>
                                            </tr>
                                            <tr>
                                                <th style="text-align: center;">Nombre Del Aval</th>
                                                <th style="text-align: center;">Actividad Económica Aval</th>
                                                <th style="text-align: center;">Teléfono Aval</th>
                                            </tr>
                                            </thead>
                                            <tbody id="tbody_aval">
                                            <?php
                                            foreach ($Administracion[2] as $key => $value) {
                                                $telefono_aval = "(" . substr($value['TELEFONO'], 0, 3) . ")" . " " . substr($value['TELEFONO'], 3, 3) . " - " . substr($value['TELEFONO'], 6, 4);
                                                echo <<<HTML
                                                <tr>
                                                    <td>
                                                        {$value['NOMBRE']}
                                                    </td>
                                                    <td>
                                                        {$value['ACT_ECO']}
                                                    </td>
                                                    <td>
                                                        $telefono_aval
                                                        <input type="text" name="id_aval_cl_$key" id="id_aval_cl_$key" value="{$value['CODIGO']}" hidden />
                                                        <input type="text" name="telefono_aval_cl_$key" id="telefono_aval_cl_$key" value="$telefono_aval" hidden />
                                                    </td>
                                                </tr>
                                            HTML;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr style="margin-top: 2px !important; margin-bottom: 2px !important; display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'block' : 'none'; ?>;">
                                <div class="row" style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'none' : 'block'; ?>;">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="diez_cl">10.- ¿Firmó su solicitud?, ¿Cuándo firmo la solicitud? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="diez_cl" name="diez_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                            <p style="color: #007700"><b></b></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="once_cl">11.- Me puede indicar ¿para qué utilizará su crédito? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="once_cl" name="once_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                <option value="N">NO RESPONDIO</option>
                                            </select>
                                            <p style="color: #007700"><b></b></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="doce_cl">12.- ¿Compartirá su crédito con alguna otra persona? *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="doce_cl" name="doce_cl">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="S">NO LO COMPARTIRA</option>
                                                <option value="N">SI LO COMPARTIRA</option>
                                            </select>
                                            <p style="color: #007700"><b></b></p>
                                        </div>
                                    </div>
                                </div>
                                <hr style="display: <?= ($Administracion[0]['CREDITO_ADICIONAL'] == 1) ? 'block' : 'none'; ?>;">
                                <div class="row">
                                    <div class="col-md-5">
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
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                        <button type="submit" id="agregar_CL" name="agregar_CL" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Respuestas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
foreach ($Administracion[2] as $key => $value) {
    $intento = $value['INTENTOS'] + 1;
    $fecha_modal_aval = date("d/m/Y h:i:s");
    $telefono_aval = "(" . substr($value['TELEFONO'], 0, 3) . ")" . " " . substr($value['TELEFONO'], 3, 3) . " - " . substr($value['TELEFONO'], 6, 4);
    $telefono_cliente = "(" . substr($Administracion[1]['TELEFONO'], 0, 3) . ")" . " " . substr($Administracion[1]['TELEFONO'], 3, 3) . " - " . substr($Administracion[1]['TELEFONO'], 6, 4);

    echo <<<HTML
        <div class="modal fade" id="modal_encuesta_aval_$key" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="width: 1300px !important;">
                <div class="modal-content">
                    <form onsubmit="enviar_add_av($key); return false" id="Add_av_$key">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important; background: #787878FF">AVAL</span>
                                <center>
                                    <h4 class="modal-title">{$value['NOMBRE']}, LLAMADA #<label id="titulo_av_$key">$intento</label></h4>
                                </center>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-2" style="display: none;">
                                        <div class="form-group">
                                            <label for="fecha_solicitud_av_$key">Fecha de solicitud *</label>
                                            <input onkeydown="return false" type="text" class="form-control" id="fecha_solicitud_av_$key" name="fecha_solicitud_av_$key" value="{$Administracion[0]['FECHA_SOL']}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="display: none;">
                                        <div class="form-group">
                                            <label for="cdgco_av_$key">CDGCO *</label>
                                            <input onkeydown="return false" type="text" class="form-control" id="cdgco_av_$key" name="cdgco_av_$key" value="{$suc}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="display: none;">
                                        <div class="form-group">
                                            <label for="cdgre_av_$key">CDGRE *</label>
                                            <input onkeydown="return false" type="text" class="form-control" id="cdgre_av_$key" name="cdgre_av_$key" value="{$reg}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="display: none;">
                                        <div class="form-group">
                                            <label for="cliente_id_av_$key">Cliente ID CLIENTE *</label>
                                            <input onkeydown="return false" type="text" class="form-control" id="cliente_id_av_$key" name="cliente_id_av_$key" value="{$Administracion[0]['ID_CLIENTE']}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fecha_av_$key">Fecha de trabajo *</label>
                                            <input onkeydown="return false" type="text" class="form-control" id="fecha_av_$key" name="fecha_av_$key" value="$fecha_modal_aval" readonly>

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="ciclo_av_$key">Ciclo del Crédito Solicitado*</label>
                                            <input type="text" class="form-control" id="ciclo_av_$key" name="ciclo_av_$key" value="{$Administracion[0]['CICLO']}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="movil_av_$key">Núm. telefono del cliente *</label>
                                            <input type="text" class="form-control" id="movil_av_$key" aria-describedby="movil_av_$key" readonly placeholder="" value="$telefono_aval">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tipo_llamada_av_$key">Tipo de llamada que esta realizando *</label>
                                            <select class="form-control mr-sm-3" autofocus type="select" id="tipo_llamada_av_$key" name="tipo_llamada_av_$key">
                                                <option selected disabled value="">Seleccione una opción</option>
                                                <option value="VOZ">VOZ</option>
                                                <option value="WHATSAPP">WHATSAPP</option>
                                                <option value="VIDEO LLAMADA">VIDEO LLAMADA</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <hr>
                                    <h5><b>Preguntas de validación</b></h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="uno_av_$key">1.- ¿Qué edad tiene?</label>
                                                <select class="form-control mr-sm-3" autofocus type="select" id="uno_av_$key" name="uno_av_$key">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                    <option value="N">NO RESPONDIO</option>
                                                </select>
                                                <p style="color: #007700"><b>R: {$value['EDAD']} AÑOS</b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="dos_av_$key">2.- ¿Cuál es su fecha de nacimiento?</label>
                                                <select class="form-control mr-sm-3" autofocus type="select" id="dos_av_$key" name="dos_av_$key">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                    <option value="N">NO RESPONDIO</option>
                                                </select>
                                                <p style="color: #007700"><b>R: {$value['NACIMIENTO']}</b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tres_av_$key">3.- ¿Cuál es su domicilio completo?</label>
                                                <select class="form-control mr-sm-3" autofocus type="select" id="tres_av_$key" name="tres_av_$key">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                    <option value="N">NO RESPONDIO</option>
                                                </select>
                                                <p style="color: #007700"><b>R: {$value['CALLE']}, {$value['COLONIA']}, {$value['MUNICIPIO']}, {$value['ESTADO']}, C.P:{$value['CP']}.</b></p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr style="margin-top: 2px !important; margin-bottom: 2px !important;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cuatro_av_$key">4.- ¿Tiempo viviendo en este domicilio?</label>
                                                <select class="form-control mr-sm-3" autofocus type="select" id="cuatro_av_$key" name="cuatro_av_$key">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                    <option value="N">NO RESPONDIO</option>
                                                </select>
                                                <p style="color: #007700"><b></b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cinco_av_$key">5.- Actualmente, ¿Cuál es su principal fuente de ingresos?</label>
                                                <select class="form-control mr-sm-3" autofocus type="select" id="cinco_av_$key" name="cinco_av_$key">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                    <option value="N">NO RESPONDIO</option>
                                                </select>
                                                <p style="color: #007700"><b>R: {$value['ACT_ECO']}</b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="seis_av_$key">6.- ¿Hace cuanto conoce a {$Administracion[0]['CLIENTE']}?</label>
                                                <select class="form-control mr-sm-3" autofocus type="select" id="seis_av_$key" name="seis_av_$key">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                    <option value="N">NO RESPONDIO</option>
                                                </select>
                                                <p style="color: #007700"><b></b></p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr style="margin-top: 2px !important; margin-bottom: 2px !important;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="siete_av_$key">7.- Mencione, ¿Qué relación directa tiene con {$Administracion[0]['CLIENTE']}?</label>
                                                <select class="form-control mr-sm-3" autofocus type="select" id="siete_av_$key" name="siete_av_$key">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                    <option value="N">NO RESPONDIO</option>
                                                </select>
                                                <p style="color: #007700"><b></b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="ocho_av_$key">8.- ¿Sabe a que se dedica {$Administracion[0]['CLIENTE']}?</label>
                                                <select class="form-control mr-sm-3" autofocus type="select" id="ocho_av_$key" name="ocho_av_$key">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                    <option value="N">NO RESPONDIO</option>
                                                </select>
                                                <p style="color: #007700"><b>R: {$Administracion[1]['ACT_ECO']}</b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nueve_av_$key">9.- Me proporciona el número telefónico de {$Administracion[0]['CLIENTE']}</label>
                                                <select class="form-control mr-sm-3" autofocus type="select" id="nueve_av_$key" name="nueve_av_$key">
                                                    <option selected disabled value="">Seleccione una opción</option>
                                                    <option value="S">RESPONDIO CORRECTAMENTE</option>
                                                    <option value="N">NO RESPONDIO</option>
                                                </select>
                                                <p style="color: #007700"><b>R: $telefono_cliente</b></p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="demo activado">
                                                <input type="radio" name="completo_av_$key" id="completo1_av_$key" value="1" checked="checked">
                                                <label for="completo1_av_$key">Llamada exitosa</label>
                                                <br>
                                                <input type="radio" name="completo_av_$key" id="completo2_av_$key" value="0">
                                                <label for="completo2_av_$key">La llamada no se completo satisfactoriamente</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                            <button type="submit" class="btn btn-primary" value="enviar_av" id="agregar_av_$key"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Respuestas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    HTML;
}
?>

    <div class="modal fade" id="modal_expediente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 1000px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <center>
                        <h4 class="modal-title" id="myModalLabel">EXPEDIENTE DEL CLIENTE: <?php echo $Administracion[0]['CLIENTE']; ?> (NÚMERO DE CRÉDITO: <?php echo $Administracion[0]['NO_CREDITO']; ?>)</h4>
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
                                            <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[0]['CLIENTE']; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Fecha de nacimiento</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['NACIMIENTO']; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>Edad</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['EDAD']; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Genéro</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="<?php if ($Administracion[1]['SEXO'] = 'F') {
                                                $sexo = 'Mujer';
                                            } else {
                                                $sexo = 'Hombre';
                                            }
                                            echo $sexo; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Domicilio</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['CALLE']; ?>, <?php echo $Administracion[1]['COLONIA']; ?>, <?php echo $Administracion[1]['MUNICIPIO']; ?>, <?php echo $Administracion[1]['ESTADO']; ?>, C.P:<?php echo $Administracion[1]['CP']; ?>." readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ocupación</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['ACT_ECO']; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Contacto</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="<?php
                                            $format = "(" . substr($Administracion[1]['TELEFONO'], 0, 3) . ")" . " " . substr($Administracion[1]['TELEFONO'], 3, 3) . " - " . substr($Administracion[1]['TELEFONO'], 6, 4);
                                            echo $format; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-2">
                                <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important; background: #787878FF">DATOS DE LOS AVALES</span>
                            </div>
                            <div class="col-md-10">
                                <?php
                                foreach ($Administracion[2] as $key => $value) {
                                    $telefono_aval = "(" . substr($value['TELEFONO'], 0, 3) . ")" . " " . substr($value['TELEFONO'], 3, 3) . " - " . substr($value['TELEFONO'], 6, 4);
                                    $fecha_nacimiento = new DateTime($value['NACIMIENTO']);
                                    $hoy = new DateTime();
                                    $fn = date("d/m/Y", strtotime($value['NACIMIENTO']));
                                    $edad = $hoy->diff($fecha_nacimiento)->y;
                                    $genero = $value['SEXO'] == 'F' ? 'Mujer' : 'Hombre';
                                    $numero = $key + 1;

                                    echo <<<HTML
                                <div class="row" style="border: 1px solid #e5e5e5; border-radius: 10px; padding: 10px; margin: 20px 0;">
                                    <div style="translate: 10px -25px;width: fit-content; padding: 5px; background: #73879C; color: white; border-radius: 10px;">
                                        Aval $numero
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nombre</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="{$value['NOMBRE']}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Fecha de nacimiento</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="$fn" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label>Edad</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="$edad" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Genéro</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="$genero" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Domicilio</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="{$value['CALLE']}, {$value['COLONIA']}, {$value['MUNICIPIO']}, {$value['ESTADO']}, C.P. {$value['CP']}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ocupación</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="{$value['ACT_ECO']}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Contacto</label>
                                            <input onkeydown="return false" type="text" class="form-control" value="$telefono_aval" readonly>
                                        </div>
                                    </div>
                                </div>
                                HTML;
                                }
                                ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cerrar Expediente</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_ver_encuesta_cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 1000px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <center>
                        <h4 class="modal-title" id="myModalLabel">RESUMEN DE LA ENCUESTA DEL CLIENTE: <?php echo $Administracion[0]['CLIENTE']; ?> (# DE CLIENTE: <?php echo $Administracion[0]['ID_CLIENTE']; ?>)</h4>
                    </center>
                </div>
                <div class="modal-body">

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-2" style="padding-bottom: 40px!important;">
                                <div class="card-body pb-0 px-0 px-md-12 text-center text-sm-left ">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4625/4625102.png" height="97" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Nombre completo del cliente</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[0]['CLIENTE']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Fecha de nacimiento</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['NACIMIENTO']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Edad</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['EDAD']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Genéro</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php if ($Administracion[1]['SEXO'] = 'F') {
                                        $sexo = 'Mujer';
                                    } else {
                                        $sexo = 'Hombre';
                                    }
                                    echo $sexo; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Domicilio</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['CALLE']; ?>, <?php echo $Administracion[1]['COLONIA']; ?>, <?php echo $Administracion[1]['MUNICIPIO']; ?>, <?php echo $Administracion[1]['ESTADO']; ?>, C.P:<?php echo $Administracion[1]['CP']; ?>." readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ocupación</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['ACT_ECO']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Contacto</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php
                                    $format = "(" . substr($Administracion[1]['TELEFONO'], 0, 3) . ")" . " " . substr($Administracion[1]['TELEFONO'], 3, 3) . " - " . substr($Administracion[1]['TELEFONO'], 6, 4);
                                    echo $format; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-2" style="padding-bottom: 120px!important;">
                                <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important; background: #787878FF">DATOS DEL AVAL</span>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Nombre completo del cliente</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[0]['AVAL']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Fecha de nacimiento</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[2]['NACIMIENTO']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Edad</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[2]['EDAD']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Genéro</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php if ($Administracion[2]['SEXO'] = 'F') {
                                        $sexo = 'Mujer';
                                    } else {
                                        $sexo = 'Hombre';
                                    }
                                    echo $sexo; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Domicilio</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[2]['CALLE']; ?>, <?php echo $Administracion[1]['COLONIA']; ?>, <?php echo $Administracion[2]['MUNICIPIO']; ?>, <?php echo $Administracion[2]['ESTADO']; ?>, C.P:<?php echo $Administracion[2]['CP']; ?>." readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ocupación</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[2]['ACT_ECO']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Contacto</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php
                                    $format = "(" . substr($Administracion[2]['TELEFONO'], 0, 3) . ")" . " " . substr($Administracion[2]['TELEFONO'], 3, 3) . " - " . substr($Administracion[2]['TELEFONO'], 6, 4);
                                    echo $format; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cerrar Expediente</button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_comentarios_fin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 1000px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <center>
                        <h4 class="modal-title" id="myModalLabel">EXPEDIENTE DEL CLIENTE: <?php echo $Administracion[0]['CLIENTE']; ?> (NÚMERO DE CRÉDITO: <?php echo $Administracion[0]['NO_CREDITO']; ?>)</h4>
                    </center>
                </div>
                <div class="modal-body">

                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-2" style="padding-bottom: 40px!important;">
                                <div class="card-body pb-0 px-0 px-md-12 text-center text-sm-left ">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4625/4625102.png" height="97" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Nombre completo del cliente</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[0]['CLIENTE']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Fecha de nacimiento</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['NACIMIENTO']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Edad</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['EDAD']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Genéro</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php if ($Administracion[1]['SEXO'] = 'F') {
                                        $sexo = 'Mujer';
                                    } else {
                                        $sexo = 'Hombre';
                                    }
                                    echo $sexo; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Domicilio</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['CALLE']; ?>, <?php echo $Administracion[1]['COLONIA']; ?>, <?php echo $Administracion[1]['MUNICIPIO']; ?>, <?php echo $Administracion[1]['ESTADO']; ?>, C.P:<?php echo $Administracion[1]['CP']; ?>." readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ocupación</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[1]['ACT_ECO']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Contacto</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php
                                    $format = "(" . substr($Administracion[1]['TELEFONO'], 0, 3) . ")" . " " . substr($Administracion[1]['TELEFONO'], 3, 3) . " - " . substr($Administracion[1]['TELEFONO'], 6, 4);
                                    echo $format; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-2" style="padding-bottom: 120px!important;">
                                <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important; background: #787878FF">DATOS DEL AVAL</span>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Nombre completo del cliente</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[0]['AVAL']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Fecha de nacimiento</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[2]['NACIMIENTO']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Edad</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[2]['EDAD']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Genéro</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php if ($Administracion[2]['SEXO'] = 'F') {
                                        $sexo = 'Mujer';
                                    } else {
                                        $sexo = 'Hombre';
                                    }
                                    echo $sexo; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Domicilio</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[2]['CALLE']; ?>, <?php echo $Administracion[1]['COLONIA']; ?>, <?php echo $Administracion[2]['MUNICIPIO']; ?>, <?php echo $Administracion[2]['ESTADO']; ?>, C.P:<?php echo $Administracion[2]['CP']; ?>." readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Ocupación</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php echo $Administracion[2]['ACT_ECO']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Contacto</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php
                                    $format = "(" . substr($Administracion[2]['TELEFONO'], 0, 3) . ")" . " " . substr($Administracion[2]['TELEFONO'], 3, 3) . " - " . substr($Administracion[2]['TELEFONO'], 6, 4);
                                    echo $format; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cerrar Expediente</button>
                        </div>

                    </div>
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