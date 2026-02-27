<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Reactivar crédito y recalcular intereses devengados</h3>
                <h5> Preguntar si ya se realizo el corte del día para poder realizar un devengo</h5>
                <div class="clearfix"></div>
            </div>
            <div class="card card-danger col-md-5">
                <div class="card-header">
                    <h5 class="card-title">Ingrese el número de crédito y ciclo</h5>
                </div>
                <div class="card-body">
                    <form class="" action="/Devengo/" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="number" id="Credito" name="Credito" placeholder="000000" min="1" aria-label="Search" value="<?= $credito; ?>">
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-3">
                                <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="number" id="Ciclo" name="Ciclo" placeholder="00" aria-label="Search" min="1" value="<?= $ciclo; ?>">
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-default" type="submit">Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="card col-md-12">
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
                <button type="button" class="btn btn-primary" onclick=reactivaCredito(event)>
                    <i class="fa fa-toggle-on"></i> Reactivar Crédito
                </button>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="row">
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-4 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Cliente</span>
                            <div class="count" style="font-size: 17px"><?= $Administracion['NOMBRE']; ?></div>

                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Número del Crédito</span>
                            <div class="count" style="font-size: 17px" id="credito"><?= $Administracion['CDGCLNS']; ?></div>

                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Ciclo del Crédito</span>
                            <div class="count" style="font-size: 17px" id="ciclo"><?= $Administracion['CICLO']; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-5  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Sucursal</span>
                            <div class="count" style="font-size: 14px"><?= $Administracion['COD_SUCURSAL']; ?> | <?= $Administracion['NOM_SUCURSAL']; ?> </div>
                            <span class="count_top" style="font-size: 15px"> Región</span>
                            <div class="count" style="font-size: 17px"><?= $Administracion['REGION']; ?></div>
                        </div>
                        <div class="col-md-2 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i></i> Periodo</span>
                            <div class="count" style="font-size: 14px"><i class="fa fa-calendar"></i> INICIO: <?= $Administracion['FECHA_INICIO']; ?></div>
                            <div class="count" style="font-size: 14px"><i class="fa fa-calendar"></i> FIN: <?= $Administracion['FECHA_FIN']; ?></div>
                        </div>

                    </div>
                </div>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <h4> Resumen del devengo</h4>
                <br>
                <div class="row">
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="col-md-4 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-calendar"></i> Fecha de liquidación</span>
                            <div class="count" style="font-size: 17px"><?= $Administracion['FECHA_LIQUIDACION']; ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Se van a devengar interés al día</span>
                            <div class="count" style="font-size: 17px"><?= $Administracion['FECHA_FIN_CALCULO']; ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-sort-numeric-asc"></i> Total de días</span>
                            <div class="count" style="font-size: 17px"><?= $Administracion['DIAS_PENDIENTES']; ?> DÍAS</div>
                        </div>
                        <div class="col-md-2 col-sm-5  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Interés total</span>
                            <div class="count" style="font-size: 17px">$<?= number_format($Administracion['INTERES_GLOBAL'], 2, '.', ','); ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Interés Diario</span>
                            <div class="count" style="font-size: 17px">$<?= number_format($Administracion['INT_DIARIO'], 2, '.', ','); ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Plazo de días</span>
                            <div class="count" style="font-size: 17px"><?= $Administracion['PLAZO_DIAS']; ?> DÍAS</div>
                        </div>
                        <div class="col-md-2 col-sm-5  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Dev diario sin IVA</span>
                            <div class="count" style="font-size: 17px">$<?= number_format($Administracion['INT_PENDIENTE'], 2, '.', ','); ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> IVA de intereses</span>
                            <div class="count" style="font-size: 17px">$<?= number_format($Administracion['IVA_PENDIENTE'], 2, '.', ','); ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Debe en total</span>
                            <div class="count" style="font-size: 17px">$<?= number_format($Administracion['INT_PENDIENTE'] + $Administracion['IVA_PENDIENTE'], 2, '.', ','); ?> </div>
                        </div>
                        <div class="col-md-2 col-sm-5  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Consecutivo</span>
                            <div class="count" style="font-size: 17px"><?= $Administracion['CONSECUTIVO']; ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Interés Devengado</span>
                            <div class="count" style="font-size: 17px">$<?= number_format($Administracion['DEVENGADO'], 2, '.', ','); ?></div>
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-building"></i> Interés recalculado</span>
                            <div class="count" style="font-size: 17px">$<?= number_format($Administracion['DEVENGADO'] + $Administracion['INT_PENDIENTE'] + $Administracion['IVA_PENDIENTE'], 2, '.', ','); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Dia devengo</th>
                            <th>Interés devengado</th>
                            <th>Fecha de calculo</th>
                        </tr>
                    </thead>
                    <tbody id="devengoPendiente">
                        <?= $tabla; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>