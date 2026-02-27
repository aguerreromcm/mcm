<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="col-md-3 panel panel-body" style="margin-bottom: 0px;">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" />
            <a id="link" href="/AdminSucursales/SaldosDiarios/">
                <div class="col-md-5" style="margin-top: 5px; margin-left: 10px; margin-right: 30px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2910/2910306.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
                    <p style="font-size: 12px; padding-top: 5px; color: #000000"><b>Saldos de Sucursales </b></p>
                    <! -- https://cdn-icons-png.flaticon.com/512/2910/2910156.png -->
                </div>
            </a>
            <a id="link" href="/AdminSucursales/SolicitudesReimpresionTicket/">
                <div class="col-md-5 imagen" style="margin-top: 5px; margin-left: 0px; border: 1px solid #dfdfdf; border-radius: 10px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/2972/2972449.png" style="border-radius: 3px; padding-top: 5px;" width="110" height="110">
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
                    <! --https://cdn-icons-png.flaticon.com/512/900/900834.png IMAGEN EN COLOR -->
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
                            <a class="navbar-brand">Admin sucursales / Saldos del día</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav">
                                <li>
                                    <a href="">
                                        <p style="font-size: 16px;"><b>Saldos del día por sucursal</b></p>
                                    </a>
                                </li>
                                <li class="linea">
                                    <a href="/AdminSucursales/FondearSucursal/">
                                        <p style="font-size: 15px;">Fondear sucursal</p>
                                    </a>
                                </li>
                                <li class="linea">
                                    <a href="/AdminSucursales/RetiroSucursal/">
                                        <p style="font-size: 15px;">Retiro efectivo</p>
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
                                        <label for="fechaI">Desde *</label>
                                        <input type="date" class="form-control" id="fechaI" name="fechaI" value="<?= $fechaI; ?>" min="2024-01-01" max="<?= $fechaF; ?>" onchange="validaFIF('fechaI', 'fechaF')">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fechaF">Hasta *</label>
                                        <input type="date" class="form-control" id="fechaF" name="fechaF" value="<?= $fechaF; ?>" min="2024-01-01" max="<?= $fechaF; ?>" onchange="validaFIF('fechaI', 'fechaF')">
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-top: 25px">
                                    <button type="button" class="btn btn-primary btn-circle" onclick=consultaSaldos()>
                                        <i class="fa fa-search"></i> Buscar
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3" style="padding-top: 25px">
                                    <button type="button" class="btn btn-success btn-circle" data-toggle="modal" data-target="#modal_agregar_horario" onclick=imprimeExcel()>
                                        <i class="fa fa-file-excel-o"></i> Exportar a Excel
                                    </button>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="dataTable_wrapper">
                                    <table class="table table-striped table-bordered table-hover" id="saldos">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Cod sucursal</th>
                                                <th>Nombre sucursal</th>
                                                <th>Saldo</th>
                                                <th>Diferencia al cierre</th>
                                                <th>Capacidad operativa</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?= $filas; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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


    /* Make the badge float in the top right corner of the button */
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
</style>


<?php echo $footer; ?>