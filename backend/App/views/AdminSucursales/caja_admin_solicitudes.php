<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" />
            <a id="link" href="/AdminSucursales/SaldosDiarios/">
                <div class="col-md-5" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2910/2910156.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Saldos de Sucursales </b></p>
                    <! -- -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/SolicitudesReimpresionTicket/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2972/2972528.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">

                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Solicitudes</b></p>
                    <span class="button__badge">4</span>
                    <! -- https://cdn-icons-png.flaticon.com/512/2972/2972528.png IAMGEN EN COLOR -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/Reporteria/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3201/3201495.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b> Consultar Reportes</b></p>
                    <! --https://cdn-icons-png.flaticon.com/512/1605/1605350.png IMAGEN EN COLOR -->
                </div>
            </a>

            <a id="link" href="/AdminSucursales/EstadoCuentaCliente/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/5864/5864275.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Catalogo de Clientes </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/3201/3201558.png IMAGEN EN COLOR -->
                </div>
            </a>


            <a id="link" href="/AdminSucursales/Configuracion/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491249.png" style="border-radius: 3px; padding-top: 5px;" width="100" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Configurar Módulo </b></p>
                    <! -- IMAGEN EN COLOR -->
                </div>
            </a>

            <a id="link" href="/AdminSucursales/Log/">
                <div class="col-md-5 imagen" style="margin-top: 20px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/10491/10491361.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 6px; color: #000000"><b>Log Transaccional </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2761/2761118.png IMAGEN EN COLOR -->
                </div>
            </a>


        </div>
        <div class="col-md-9">
            <form id="registroOperacion" name="registroOperacion">
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px">
                        <div class="navbar-header card col-md-12" style="background: #2b2b2b">
                            <a class="navbar-brand">Admin sucursales / Solicitudes / Reimpresión de tickets</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav">

                                <li><a href="">
                                        <p style="font-size: 16px;"><b>Reimpresión de tickets</b></p>
                                    </a></li>
                                <li class="linea"><a href="/AdminSucursales/SolicitudResumenMovimientos/">
                                        <p style="font-size: 16px;">Resumen de movimientos</p>
                                    </a></li>
                                <li class="linea"><a href="/AdminSucursales/SolicitudRetiroOrdinario/">
                                        <p style="font-size: 16px;">Retiros programados</p>
                                    </a></li>
                                <li class="linea"><a href="/AdminSucursales/SolicitudRetiroExpress/">
                                        <p style="font-size: 16px;">Retiros express</p>
                                    </a></li>

                            </ul>
                        </div>
                    </div>
                    <div class="modal-body">

                        <div class="container">
                            <section id="fancyTabWidget" class="tabs t-tabs">
                                <ul class="nav nav-tabs fancyTabs" role="tablist">

                                    <li class="tab fancyTab active">
                                        <div class="arrow-down">
                                            <div class="arrow-down-inner"></div>
                                        </div>
                                        <a id="tab0" href="#tabBody0" role="tab" aria-controls="tabBody0" aria-selected="true" data-toggle="tab" tabindex="0"><span class="fa fa-clock-o"></span><span class="hidden-xs"> Solicitudes pendientes</span></a>
                                        <div class="whiteBlock"></div>
                                    </li>

                                    <li class="tab fancyTab">
                                        <div class="arrow-down">
                                            <div class="arrow-down-inner"></div>
                                        </div>
                                        <a id="tab1" href="#tabBody1" role="tab" aria-controls="tabBody1" aria-selected="true" data-toggle="tab" tabindex="0"><span class="fa fa-history"></span><span class="hidden-xs"> Historial de Solicitudes</span></a>
                                        <div class="whiteBlock"></div>
                                    </li>
                                </ul>
                                <div id="myTabContent" class="tab-content fancyTabContent" aria-live="polite">
                                    <div class="tab-pane  fade active in" id="tabBody0" role="tabpanel" aria-labelledby="tab0" aria-hidden="false" tabindex="0">
                                        <div>
                                            <div class="row">

                                                <div class="col-md-12">


                                                    <div class="container-fluid">
                                                        <br>
                                                        <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                                                        <hr>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="card col-md-12">
                                                                    <div class="dataTable_wrapper">
                                                                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Cod Sucursal</th>
                                                                                    <th>Nombre Sucursal</th>
                                                                                    <th>Hora Cierre</th>
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
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane  fade" id="tabBody1" role="tabpanel" aria-labelledby="tab1" aria-hidden="true" tabindex="0">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="container-fluid">
                                                    <div class="col-md-12">
                                                        <div class="row">
                                                            <br>
                                                            <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                                                            <hr>
                                                            <hr>
                                                            <div class="card col-md-12">
                                                                <div class="dataTable_wrapper">
                                                                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones1">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Cod Sucursal</th>
                                                                                <th>Nombre Sucursal</th>
                                                                                <th>Estatus</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?= $tabla_his; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </section>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function EditarHorario(sucursal, nombre_suc, hora_actual) {


        var o = new Option(nombre_suc, sucursal);
        $(o).html(nombre_suc);
        $("#sucursal_e").append(o);

        document.getElementById("hora_ae").value = hora_actual;

        $('#modal_update_horario').modal('show');

    }
</script>

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


    .button__badge {
        background-color: #fa3e3e;
        border-radius: 50px;
        color: white;
        padding: 2px 10px;
        font-size: 19px;
        position: absolute;
        /* Position the badge within the relatively positioned button */
        top: 0;
        right: 0;
    }

    .container {
        margin-top: 0px;
    }


    .fancyTab.active .fa {
        color: #cfb87c;
    }

    .fancyTab a:focus {
        outline: none;
    }
</style>


<?php echo $footer; ?>