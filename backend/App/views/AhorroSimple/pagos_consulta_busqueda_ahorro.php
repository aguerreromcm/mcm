<?= $header; ?>

<div class="right_col" style="color: #000;">

    <div class="panel panel-body" style="margin-bottom: 0px; background: #f9f9f9; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 20px;">
        <div class="x_title">
            <label style="font-size: 28px; font-weight: bold; cursor: pointer;">📊 Estado de Cuenta</label>
            <div class="clearfix"></div>
        </div>

        <div class="row tile_count float-right" style="margin-bottom: 1px; margin-top: 1px;">

            <!-- Datos de generales del cliente -->
            <div class="col-md-6 tile_stats_count">
                <div>
                    <span style="font-size: 16px; font-weight: bold;"><i class="fa fa-user">&nbsp;</i>Cliente:</span>
                    <span style="font-size: 16px;"><?= $infoCredito['NOMBRE_CLIENTE'] ?> (<?= $infoCredito['CDGNS'] ?>)</span>
                </div>
                <div>
                    <span style="font-size: 16px; font-weight: bold;"><i class="fa fa-building">&nbsp;</i>Sucursal:</span>
                    <span style="font-size: 16px;"><?= $infoCredito['NOMBRE_SUCURSAL'] ?> (<?= $infoCredito['SUCURSAL'] ?>)</span>
                </div>
                <div>
                    <span style="font-size: 16px; font-weight: bold;"><i class="fa fa-briefcase">&nbsp;</i>Ejecutivo:</span>
                    <span style="font-size: 16px;;"><?= $infoCredito['NOMBRE_EJECUTIVO']; ?> (<?= $infoCredito['EJECUTIVO'] ?>)</span>
                </div>
            </div>
            <div class="col-md-3 tile_stats_count">
                <div>
                    <span style="font-size: 16px; font-weight: bold;"><i class="fa fa-calendar">&nbsp;</i>Apertura:</span>
                    <span style="font-size: 16px;"><?= $infoCredito['APERTURA'] ?></span>

                </div>
                <div>
                    <span style="font-size: 16px; font-weight: bold;"><i class="fa fa-percent">&nbsp;</i>Tasa:</span>
                    <span style="font-size: 16px;"><?= $infoCredito['TASA'] ?>% anual</span>
                </div>
                <div>
                    <span style="font-size: 16px; font-weight: bold;"><i class="fa fa-dollar">&nbsp;</i>Interés:</span>
                    <span style="font-size: 16px;">$ <?= number_format($infoCredito['INTERES'], 2, '.', ','); ?></span>
                </div>
            </div>
            <div class="col-md-3 tile_stats_count">
                <table style="width: 100%;">
                    <tbody>
                        <tr class="fila-abono">
                            <td class="simbolo-operacion">+</td>
                            <td class="concepto-operacion">Abonos</td>
                            <td class="monto-operacion">$ <?= number_format($infoCredito['ABONOS'], 2, '.', ','); ?></td>
                        </tr>
                        <tr class="fila-retiro">
                            <td class="simbolo-operacion">-</td>
                            <td class="concepto-operacion">Retiros</td>
                            <td class="monto-operacion">$ <?= number_format($infoCredito['RETIROS'], 2, '.', ','); ?></td>
                        </tr>
                        <tr class="fila-transito">
                            <td class="simbolo-operacion">-</td>
                            <td class="concepto-operacion">En tránsito</td>
                            <td class="monto-operacion">$ <?= number_format($infoCredito['RETIROS_TRANSITO'], 2, '.', ','); ?></td>
                        </tr>
                        <tr class="fila-saldo">
                            <td class="simbolo-operacion"></td>
                            <td class="concepto-operacion">Saldo disponible</td>
                            <td class="monto-operacion">$ <?= number_format($infoCredito['SALDO_ACTUAL'], 2, '.', ','); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dataTable_wrapper mt-3">
            <table class="table table-striped table-bordered table-hover" id="movimientosAhorro" style="font-size: 14px;">
                <thead class="thead-dark">
                    <tr class="encabezado">
                        <th>Medio</th>
                        <th>Fecha Registro</th>
                        <th>Fecha Aplicación</th>
                        <th>Operación</th>
                        <th>Monto</th>
                        <th>Descripción</th>
                        <th>Ejecutivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?= $tabla; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .encabezado {
        background-color: #c43136;
        color: #fff;
    }

    .encabezado th {
        padding: 5px;
        text-transform: uppercase;
        font-size: 14px;
        text-align: center;
        vertical-align: middle;
    }

    .simbolo-operacion {
        width: 30px;
        text-align: center;
        font-weight: bold;
    }

    .concepto-operacion {
        font-weight: 500;
        padding-left: 5px;
    }

    .monto-operacion {
        text-align: right;
        font-weight: bold;
        white-space: nowrap;
    }

    .fila-abono td {
        color: #28a745;
        font-size: 14px;
    }

    .fila-retiro td {
        color: #dc3545;
        font-size: 14px;
    }

    .fila-transito td {
        color: #6c757d;
        font-size: 14px;
    }

    .fila-saldo {
        border-top: 2px solid #333;
    }

    .fila-saldo td {
        color: #333;
        font-size: 16px;
        font-weight: 600;
        text-transform: uppercase;
    }
</style>

<?= $footer; ?>