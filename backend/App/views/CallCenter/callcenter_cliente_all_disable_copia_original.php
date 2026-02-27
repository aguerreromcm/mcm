<?php echo $header; ?>
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
                <span class="badge" style="background: #57687b"><h4 style="margin-top: 4px; margin-bottom: 4px">Datos del Crédito | <i class="fa fa-user"></i> <?php echo $Administracion[0]['CLIENTE']; ?></h4></span>
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

                                                if($Administracion[3]['LLAMADA_UNO'] == NULL)
                                                {
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
                                                }

                                                else if($Administracion[3]['FINALIZADA'] == '')
                                                {
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

                                                }else if($Administracion[3]['FINALIZADA'] == '1')
                                                {
                                                    $titulo_l1 = "Validada";
                                                    $color_titulo_l1 = "success";
                                                    //-----
                                                    $titulo_l3 = "Detalle Encuesta";
                                                    //-----
                                                    $titulo_l4 = "FINALIZADA";
                                                    $boton_ver_encuesta_l4 = 'style= display:none!important;';
                                                    if($Administracion[3]['REACTIVACION'])
                                                    {
                                                        $titulo_boton_l4 = "Reintentar (Reactivada)";
                                                        $ocultar_boton_l4_iniciar = '';
                                                    }
                                                    else{
                                                        $titulo_boton_l4 = "Reintentar";
                                                        $ocultar_boton_l4_iniciar = 'style= display:none!important;';
                                                    }
                                                    $hora_l4 = "Primer llamada: ".$Administracion[3]['HORA_LLAMADA_UNO']."<br>"."Última Llamada: ".$Administracion[3]['HORA_LLAMADA_DOS'];
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
                                                <td style="font-size: 16px;  !important;" colspan="14">
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
                                                <td style="font-size: 19px"; colspan="10">
                                                    <i class="fa fa-phone-square"></i> <?php
                                                    $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],3,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
                                                    echo $format; ?>
                                                </td>
                                                <td style="font-size: 19px; font: " colspan="3">
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
                                                        <button type="button" disabled id="boton_iniciar" class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF" data-toggle="modal" data-target="#modal_encuesta_cliente" data-backdrop="static" data-keyboard="false">
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
                                                                <span class="label label-<?php echo $color_titulo_l1; ?>" style="font-size: 95% !important; border-radius: 50em !important;" align="right"><?php  if($Administracion[3]['NUMERO_INTENTOS_CL'] == NULL){$num = '0';}else {$num = $Administracion[3]['NUMERO_INTENTOS_CL'];} echo $num;?></span>
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
                                    <div class="dataTable_wrapper">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tbody>
                                            <tr>
                                                <?php

                                                if($Administracion[4]['NUM_LLAM'] == NULL)
                                                {
                                                    $titulo_l1_a = "Pendiente de validar";
                                                    $color_titulo_l1_a = "warning";
                                                    //-----
                                                    $titulo_l3_a = "Estatus Encuesta";
                                                    //-----
                                                    $titulo_l4_a = "PENDIENTE";
                                                    $boton_ver_encuesta_l4_a = 'style= display:none!important;';
                                                    $hora_l4_a = $Administracion[3]['HORA'];
                                                    $titulo_boton_l4_a = "Iniciar";
                                                    $ocultar_boton_l4_iniciar_a = '';
                                                    //------
                                                }

                                                else if($Administracion[4]['FINALIZADA'] == '')
                                                {
                                                    $titulo_l1_a = "Pendiente de validar";
                                                    $color_titulo_l1_a = "warning";
                                                    //-----
                                                    $titulo_l3_a = "Estatus Encuesta";
                                                    //-----
                                                    $titulo_l4_a = "EN ESPERA";
                                                    $boton_ver_encuesta_l4_a = 'style= display:none!important;';
                                                    $hora_l4_a = $Administracion[3]['HORA'];
                                                    $titulo_boton_l4_a = "Reintentar";
                                                    $ocultar_boton_l4_iniciar_a = '';
                                                    //------

                                                }else if($Administracion[4]['FINALIZADA'] == '1')
                                                {
                                                    //var_dump("Holaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
                                                    $titulo_l1_a = "Validada";
                                                    $color_titulo_l1_a = "success";
                                                    //-----
                                                    $titulo_l3_a = "Detalle Encuesta";
                                                    //-----
                                                    $titulo_l4_a = "FINALIZADA";
                                                    $boton_ver_encuesta_l4_a = 'style= display:none!important;';

                                                    if($Administracion[3]['REACTIVACION'])
                                                    {
                                                        $titulo_boton_l4_a = "Reintentar (Reactivada)";
                                                        $ocultar_boton_l4_iniciar_a = '';
                                                    }
                                                    else{
                                                        $titulo_boton_l4_a = "Reintentar";
                                                        $ocultar_boton_l4_iniciar_a = 'style= display:none!important;';
                                                    }


                                                    $hora_l4_a = "Primer llamada: ".$Administracion[4]['HORA_LLAMADA_UNO']."<br>"."Última Llamada: ".$Administracion[4]['HORA_LLAMADA_DOS'];
                                                    //------
                                                }
                                                ?>
                                                <td style="font-size: 18px; background: #73879C;color: white" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-8">

                                                            <strong>
                                                                Identificación del Aval
                                                            </strong>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <strong>
                                                                <span class="label label-<?php echo $color_titulo_l1_a; ?>" style="font-size: 95% !important; border-radius: 50em !important;" align="right"><?php echo $titulo_l1_a;?></span>
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px;  !important;" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-12" style="padding-top: 11px !important; padding-bottom: 11px !important;">
                                                            <?php echo $Administracion[0]['AVAL']; ?> (<?php echo $Administracion[0]['ID_AVAL']; ?>)
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr>
                                                <td style="font-size: 16px" colspan="10"><strong>Contacto</strong></td>
                                                <td style="font-size: 16px" colspan="3"><strong>Encuesta *</strong></td>
                                                <td style="font-size: 16px" colspan="5"><strong><?php echo $titulo_l3_a; ?></strong> </td>
                                            </tr>
                                            <tr>

                                                <td style="font-size: 19px"; colspan="10">
                                                    <i class="fa fa-phone-square"></i> <?php
                                                    $format = "(".substr($Administracion[2]['TELEFONO'],0,3).")"." ".substr($Administracion[2]['TELEFONO'],3,3)." - ".substr($Administracion[2]['TELEFONO'],6,4);
                                                    echo $format; ?>
                                                </td>
                                                <td style="font-size: 19px; font: " colspan="3">
                                                    <?php echo $titulo_l4_a ?>
                                                    <button type="button" <?php echo $boton_ver_encuesta_l4_a; ?> class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF" data-toggle="modal" data-target="#modal_ver_encuesta_aval" data-backdrop="static" data-keyboard="false">
                                                        <i class="fa fa-eye" style="color: #1c4e63"></i> <label style="color: #1c4e63">Ver</label>
                                                    </button>
                                                </td>
                                                <td style="font-size: 16px;" colspan="5">
                                                    <div><?php echo  $hora_l4_a ?></div>
                                                    <div>
                                                        <?php
                                                        if($desactivar_aval == 'false')
                                                        {
                                                            $tabla = <<<html
                                                            <button type="button" disabled $ocultar_boton_l4_iniciar_a class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF" data-toggle="modal" data-target="#modal_encuesta_aval" data-backdrop="static" data-keyboard="false">
                                                                <i class="fa fa-edit" style="color: #1c4e63"></i> <label style="color: #1c4e63">$titulo_boton_l4_a </label>
                                                            </button>
html;

                                                        }
                                                        else
                                                        {
                                                            $tabla = <<<html
                                                            <button type="button"disabled class="btn btn-primary" style="border: 1px solid #006700; background: #FFFFFF" data-backdrop="static" data-keyboard="false" onclick="InfoDesactivaEncuesta();">
                                                                <i class="fa fa-edit" style="color: #1c4e63"></i> <label style="color: #1c4e63">$titulo_boton_l4_a </label>
                                                            </button>
html;
                                                        }

                                                        echo $tabla;
                                                        ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 18px; background: #cccccc;color: #707070" colspan="14">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            Usted a registrado <strong>
                                                                <span class="label label-<?php echo $color_titulo_l1_a; ?>" style="font-size: 95% !important; border-radius: 50em !important;" align="right"><?php  if($Administracion[4]['NUMERO_INTENTOS_AV'] == NULL){$num = '0';}else {$num = $Administracion[4]['NUMERO_INTENTOS_AV'];} echo $num;?></span>                                                            </strong>
                                                            intentos de llamada al AVAL del cliente.
                                                        </div>
                                                        <div class="col-md-4">

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

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
                                        <input style="display: none!important;" onkeydown="return false"   type="text" class="form-control" id="cliente_aval" name="cliente_aval" value="<?php echo $titulo_l4_a; ?>" readonly>

                                        <form onsubmit="enviar_comentarios_add(); return false" id="Add_comentarios" class="col-sm-8">
                                        <?php
                                            if($Administracion[3]['PRORROGA'] == 2)
                                            {
                                                $com_prorroga = $Administracion[3]['COMENTARIO_PRORROGA'];
                                                $comentario_prorroga = <<<html
                                                    <div class="col-lg-4">
                                                    <label for="comentarios_prorroga">Comentarios de Prorroga *</label>
                                                    <textarea name="comentarios_prorroga" id="comentarios_prorroga" class="form-control" rows="7" cols="50" placeholder="Escribe tus comentarios FINALES, una vez que hayas completado el proceso correspondiente" style="background-color: white; resize: none"> $com_prorroga</textarea>

                                                    <button type="submit" disabled name="agregar_resumen" value="enviar_resumen" class="btn btn-primary">
                                                        <i class="fa fa-save"></i> <b>Guardar</b>
                                                    </button>
                                                </div>
html;
                                                $tamaño_col ='4';
                                            }
                                            else
                                            {
                                                $tamaño_col ='6';
                                            }
                                        ?>
                                            <div class="col-lg-<?php echo $tamaño_col; ?>">
                                                    <label for="Fecha">Comentarios Iniciales *</label>
                                                    <textarea name="comentarios_iniciales" id="comentarios_iniciales" class="form-control" rows="7" cols="50" placeholder="Escribe tus comentarios INICIALES una vez que hayas marcado al número del cliente o aval, por primera vez" style="background-color: white; resize: none" disabled><?php echo $Administracion[3]['COMENTARIO_INICIAL']; ?></textarea>


                                                </div>

                                               <div class="col-lg-<?php echo $tamaño_col; ?>">
                                                    <label for="Fecha">Comentarios Finales *</label>
                                                    <textarea name="comentarios_finales" id="comentarios_finales" class="form-control" rows="7" cols="50" placeholder="Escribe tus comentarios FINALES, una vez que hayas completado el proceso correspondiente" style="background-color: white; resize: none" disabled><?php echo $Administracion[3]['COMENTARIO_FINAL']; ?></textarea>

                                                </div>

                                                <?php echo $comentario_prorroga; ?>

                                        </form>

                                        <form onsubmit="enviar_resumen_add(); return false" id="Add_comentarios" class="col-sm-4">
                                            <div  class="col-lg-8">
                                                <div class="form-group">
                                                    <div>
                                                        <label for="estatus_solicitud"> Estatus Final de la Solicitud *</label>
                                                        <input autofocus="" type="text" class="form-control"  autocomplete="off" max="10000" value="<?php echo $Administracion[3]['ESTATUS'];?>" disabled>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div>
                                                        <label for="vobo_gerente"> VoBo Gerente Regional (Opcional)</label>
                                                        <input autofocus="" type="text" class="form-control"  autocomplete="off" max="10000" value="<?php if($Administracion[3]['VOBO_GERENTE_REGIONAL'] == 'S'){echo "SI";}else{echo "NO";}  ?>" disabled>
                                                    </div>
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
            <div class="modal-header">
                <button type="button" disabled class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important; background: #787878FF">CLIENTE</span>
                <form onsubmit="enviar_add_cl(); return false" id="Add_cl">
                <center><h4 class="modal-title"><?php echo $Administracion[0]['CLIENTE']; ?>, LLAMADA #<label id="titulo" name="titulo"><?php  if($Administracion[3]['NUMERO_INTENTOS_CL'] == NULL){$num = '1';}else {$num = $Administracion[3]['NUMERO_INTENTOS_CL'] + 1;} echo $num;?></label></h4></center>

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
                                    $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],3,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
                                    echo $format; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo_llamada_cl">Tipo de llamada que esta realizando *</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo_llamada_cl" name="tipo_llamada_cl">
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
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="uno_cl" name="uno_cl">
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
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="dos_cl" name="dos_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[1]['NACIMIENTO']; ?></b></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tres_cl">3.- ¿Cuál es su domicilio completo? *</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="tres_cl" name="tres_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[1]['CALLE']; ?>, <?php echo $Administracion[1]['COLONIA']; ?>, <?php echo $Administracion[1]['MUNICIPIO']; ?>, <?php echo $Administracion[1]['ESTADO']; ?>, C.P:<?php echo $Administracion[1]['CP']; ?>.</b></p>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin-top: 2px !important; margin-bottom: 2px !important;">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cuatro_cl">4.- ¿Tiempo viviendo en este domicilio? *</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="cuatro_cl" name="cuatro_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b></b></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cinco_cl">5.- Actualmente, ¿Cuál es su principal fuente de ingresos? *</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="cinco_cl" name="cinco_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[1]['ACT_ECO']; ?></b></p>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="seis_cl">6.- Mencione, ¿Cuál es el nombre completo de su aval? *</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="seis_cl" name="seis_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[0]['AVAL']; ?></b></p>
                                    </div>
                                </div>

                                <div class="col-md-2" style="display: none">
                                    <div class="form-group">NOMBRE AVAL *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="nombre_aval_cl" name="nombre_aval_cl" value="<?php echo $Administracion[0]['AVAL']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none">
                                    <div class="form-group">ID AVAL *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="id_aval_cl" name="id_aval_cl" value="<?php echo $Administracion[0]['ID_AVAL']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2" style="display: none">
                                    <div class="form-group">TELEFONO *</label>
                                        <input onkeydown="return false" type="text" class="form-control" id="telefono_aval_cl" name="telefono_aval_cl" value="<?php
                                        $format = "(".substr($Administracion[2]['TELEFONO'],0,3).")"." ".substr($Administracion[2]['TELEFONO'],3,3)." - ".substr($Administracion[2]['TELEFONO'],6,4);
                                        echo $format; ?>" readonly>
                                    </div>
                                </div>

                            </div>
                            <hr style="margin-top: 2px !important; margin-bottom: 2px !important;">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="siete_cl">7.- Mencione, ¿Qué relación directa tiene con su aval? *</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="siete_cl" name="siete_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b></b></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ocho_cl">8.- ¿ Cuál es la actividad económica de su aval? *</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="ocho_cl" name="ocho_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[2]['ACT_ECO']; ?></b></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nueve_cl">9.- Me proporciona el número telefónico de su aval *</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="nueve_cl" name="nueve_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php
                                                $format = "(".substr($Administracion[2]['TELEFONO'],0,3).")"." ".substr($Administracion[2]['TELEFONO'],3,3)." - ".substr($Administracion[2]['TELEFONO'],6,4);
                                                echo $format; ?></b></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="diez_cl">10.- ¿Firmó su solicitud?, ¿Cuándo firmo la solicitud? *</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="diez_cl" name="diez_cl">
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
                                        <label for="once_cl">11.- Me puede indicar ¿para qué utilizará su crédito? *</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="once_cl" name="once_cl">
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
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="doce_cl" name="doce_cl">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">NO LO COMPARTIRA</option>
                                            <option value="N">SI LO COMPARTIRA</option>
                                        </select>
                                        <p style="color: #007700"><b></b></p>
                                    </div>
                                </div>
                            </div>
                            <hr>
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
                <button type="button" disabled class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit"  disabled id="agregar_CL" name="agregar_CL" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Respuestas</button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_encuesta_aval" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 1300px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <span class="label label-danger" style="font-size: 95% !important; border-radius: 50em !important; background: #787878FF">AVAL</span>
                <form onsubmit="enviar_add_av(); return false" id="Add_av">
                    <center><h4 class="modal-title"><?php echo $Administracion[0]['AVAL']; ?>, LLAMADA #<label id="titulo_av" name="titulo_av"><?php  if($Administracion[4]['NUMERO_INTENTOS_AV'] == NULL){$num = '1';}else {$num = $Administracion[4]['NUMERO_INTENTOS_AV']+1;} echo $num;?></label></h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-2" style="display: none;">
                                <div class="form-group">
                                    <label for="fecha_solicitud_av">Fecha de solicitud *</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="fecha_solicitud_av" name="fecha_solicitud_av" value="<?php echo $Administracion[0]['FECHA_SOL']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2" style="display: none;">
                                <div class="form-group">
                                    <label for="cdgco_av">CDGCO *</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="cdgco_av" name="cdgco_av" value="<?php echo $suc; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2" style="display: none;">
                                <div class="form-group">
                                    <label for="cdgre_av">CDGRE *</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="cdgre_av" name="cdgre_av" value="<?php echo $reg; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2" style="display: none;">
                                <div class="form-group">
                                    <label for="cliente_id_av">Cliente ID CLIENTE *</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="cliente_id_av" name="cliente_id_av" value="<?php echo $Administracion[0]['ID_CLIENTE']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fecha_av">Fecha de trabajo *</label>
                                    <input onkeydown="return false" type="text" class="form-control" id="fecha_av" name="fecha_av" value="<?php echo date("d/m/Y h:i:s"); ?>" readonly>

                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ciclo_av">Ciclo del Crédito Solicitado*</label>
                                    <input type="text" class="form-control" id="ciclo_av" name="ciclo_av" value="<?php echo $Administracion[0]['CICLO']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="movil_av">Núm. telefono del cliente *</label>
                                    <input type="text" class="form-control" id="movil_av" aria-describedby="movil_av" readonly placeholder="" value="<?php
                                    $format = "(".substr($Administracion[2]['TELEFONO'],0,3).")"." ".substr($Administracion[2]['TELEFONO'],3,3)." - ".substr($Administracion[2]['TELEFONO'],6,4);
                                    echo $format; ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipo_llamada_av">Tipo de llamada que esta realizando *</label>
                                    <select class="form-control mr-sm-3"  autofocus type="select" id="tipo_llamada_av" name="tipo_llamada_av">
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
                                        <label for="uno_av">1.- ¿Qué edad tiene?</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="uno_av" name="uno_av">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[2]['EDAD']; ?> AÑOS</b></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="dos_av">2.- ¿Cuál es su fecha de nacimiento?</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="dos_av" name="dos_av">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[2]['NACIMIENTO']; ?></b></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tres_av">3.- ¿Cuál es su domicilio completo?</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="tres_av" name="tres_av">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[2]['CALLE']; ?>, <?php echo $Administracion[2]['COLONIA']; ?>, <?php echo $Administracion[2]['MUNICIPIO']; ?>, <?php echo $Administracion[2]['ESTADO']; ?>, C.P:<?php echo $Administracion[2]['CP']; ?>.</b></p>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin-top: 2px !important; margin-bottom: 2px !important;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cuatro_av">4.- ¿Tiempo viviendo en este domicilio?</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="cuatro_av" name="cuatro_av">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b></b></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cinco_av">5.- Actualmente, ¿Cuál es su principal fuente de ingresos?</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="cinco_av" name="cinco_av">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[2]['ACT_ECO']; ?> </b></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="seis_av">6.- ¿Hace cuanto conoce a <?php echo $Administracion[0]['CLIENTE']; ?>?</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="seis_av" name="seis_av">
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
                                        <label for="siete_av">7.- Mencione, ¿Qué relación directa tiene con <?php echo $Administracion[0]['CLIENTE']; ?>?</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="siete_av" name="siete_av">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b></b></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ocho_av">8.- ¿Sabe a que se dedica <?php echo $Administracion[0]['CLIENTE']; ?>?</label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="ocho_av" name="ocho_av">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php echo $Administracion[1]['ACT_ECO']; ?></b></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nueve_av">9.- Me proporciona el número telefónico de <?php echo $Administracion[0]['CLIENTE']; ?></label>
                                        <select class="form-control mr-sm-3"  autofocus type="select" id="nueve_av" name="nueve_av">
                                            <option selected disabled value="">Seleccione una opción</option>
                                            <option value="S">RESPONDIO CORRECTAMENTE</option>
                                            <option value="N">NO RESPONDIO</option>
                                        </select>
                                        <p style="color: #007700"><b>R: <?php
                                                $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],3,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
                                                echo $format; ?></b></p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="demo activado">
                                        <input type="radio" name="completo_av" id="completo1_av" value="1" checked="checked">
                                        <label for="completo1_av">Llamada exitosa</label>
                                        <br>
                                        <input type="radio" name="completo_av" id="completo2_av" value="0">
                                        <label for="completo2_av">La llamada no se completo satisfactoriamente</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" id="agregar_av" name="agregar_av" class="btn btn-primary" value="enviar_av"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Respuestas</button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal_expediente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 1000px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">EXPEDIENTE DEL CLIENTE:  <?php echo $Administracion[0]['CLIENTE']; ?>  (NÚMERO DE CRÉDITO: <?php echo $Administracion[0]['NO_CREDITO']; ?>)</h4></center>
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
                                    <input onkeydown="return false" type="text" class="form-control"  value="<?php echo $Administracion[0]['CLIENTE']; ?>" readonly>
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
                                    <input onkeydown="return false" type="text" class="form-control"value="<?php echo $Administracion[1]['EDAD']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Genéro</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php if($Administracion[1]['SEXO'] = 'F'){$sexo='Mujer';}else{$sexo='Hombre';} echo $sexo; ?>" readonly>
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
                                    $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],3,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
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
                                    <input onkeydown="return false" type="text" class="form-control"  value="<?php echo $Administracion[0]['AVAL']; ?>" readonly>
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
                                    <input onkeydown="return false" type="text" class="form-control"value="<?php echo $Administracion[2]['EDAD']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>Genéro</label>
                                    <input onkeydown="return false" type="text" class="form-control" value="<?php if($Administracion[2]['SEXO'] = 'F'){$sexo='Mujer';}else{$sexo='Hombre';} echo $sexo; ?>" readonly>
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
                                    $format = "(".substr($Administracion[2]['TELEFONO'],0,3).")"." ".substr($Administracion[2]['TELEFONO'],3,3)." - ".substr($Administracion[2]['TELEFONO'],6,4);
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
<div class="modal fade" id="modal_ver_encuesta_cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 1000px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">RESUMEN DE LA ENCUESTA DEL CLIENTE:  <?php echo $Administracion[0]['CLIENTE']; ?>  (# DE CLIENTE: <?php echo $Administracion[0]['ID_CLIENTE']; ?>)</h4></center>
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
                                <input onkeydown="return false" type="text" class="form-control"  value="<?php echo $Administracion[0]['CLIENTE']; ?>" readonly>
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
                                <input onkeydown="return false" type="text" class="form-control"value="<?php echo $Administracion[1]['EDAD']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>Genéro</label>
                                <input onkeydown="return false" type="text" class="form-control" value="<?php if($Administracion[1]['SEXO'] = 'F'){$sexo='Mujer';}else{$sexo='Hombre';} echo $sexo; ?>" readonly>
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
                                $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],3,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
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
                                <input onkeydown="return false" type="text" class="form-control"  value="<?php echo $Administracion[0]['AVAL']; ?>" readonly>
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
                                <input onkeydown="return false" type="text" class="form-control"value="<?php echo $Administracion[2]['EDAD']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>Genéro</label>
                                <input onkeydown="return false" type="text" class="form-control" value="<?php if($Administracion[2]['SEXO'] = 'F'){$sexo='Mujer';}else{$sexo='Hombre';} echo $sexo; ?>" readonly>
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
                                $format = "(".substr($Administracion[2]['TELEFONO'],0,3).")"." ".substr($Administracion[2]['TELEFONO'],3,3)." - ".substr($Administracion[2]['TELEFONO'],6,4);
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
                <center><h4 class="modal-title" id="myModalLabel">EXPEDIENTE DEL CLIENTE:  <?php echo $Administracion[0]['CLIENTE']; ?>  (NÚMERO DE CRÉDITO: <?php echo $Administracion[0]['NO_CREDITO']; ?>)</h4></center>
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
                                <input onkeydown="return false" type="text" class="form-control"  value="<?php echo $Administracion[0]['CLIENTE']; ?>" readonly>
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
                                <input onkeydown="return false" type="text" class="form-control"value="<?php echo $Administracion[1]['EDAD']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>Genéro</label>
                                <input onkeydown="return false" type="text" class="form-control" value="<?php if($Administracion[1]['SEXO'] = 'F'){$sexo='Mujer';}else{$sexo='Hombre';} echo $sexo; ?>" readonly>
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
                                $format = "(".substr($Administracion[1]['TELEFONO'],0,3).")"." ".substr($Administracion[1]['TELEFONO'],3,3)." - ".substr($Administracion[1]['TELEFONO'],6,4);
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
                                <input onkeydown="return false" type="text" class="form-control"  value="<?php echo $Administracion[0]['AVAL']; ?>" readonly>
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
                                <input onkeydown="return false" type="text" class="form-control"value="<?php echo $Administracion[2]['EDAD']; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>Genéro</label>
                                <input onkeydown="return false" type="text" class="form-control" value="<?php if($Administracion[2]['SEXO'] = 'F'){$sexo='Mujer';}else{$sexo='Hombre';} echo $sexo; ?>" readonly>
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
                                $format = "(".substr($Administracion[2]['TELEFONO'],0,3).")"." ".substr($Administracion[2]['TELEFONO'],3,3)." - ".substr($Administracion[2]['TELEFONO'],6,4);
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
    .activado input[type=radio]:checked + label {
        color: #2da92d; font-size: 20px;
    }
</style>

<?php echo $footer; ?>
