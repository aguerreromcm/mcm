<?php echo $header; ?>

<div class="right_col" style="color: #000;">

    <!-- Panel principal -->
    <div class="panel panel-body" style="margin-bottom: 0px; background: #f9f9f9; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 20px;">

        <div class="x_title">
            <a href="/AhorroSimple/EstadoCuenta/" style="text-decoration: none; color: inherit;">
                <label style="font-size: 28px; font-weight: bold; cursor: pointer;">📊 Resultado Validación</label>
            </a>
            <div class="clearfix"></div>
        </div>

        <div class="card col-md-12 mb-3" style="padding: 15px;">

            <div class="row">

                <!-- TILE INFORMATION -->
                <div class="tile_count col-sm-12" style="margin-bottom: 10px;">

                    <!-- Cliente -->
                    <div class="col-md-4 col-sm-4 tile_stats_count">
                        <span class="count_top" style="font-size: 15px;">
                            <i class="fa fa-user-circle"></i> Cliente
                        </span>
                        <div class="count" style="font-size: 16px; font-weight: bold;">
                            (<?= $ConsultaDatos['NO_CREDITO'] ?>) - <?= $ConsultaDatos['CLIENTE'] ?>
                        </div>
                    </div>

                    <!-- Sucursal -->
                    <div class="col-md-2 col-sm-2 tile_stats_count">
                        <span class="count_top" style="font-size: 15px;">
                            <i class="fa fa-building"></i> Sucursal
                        </span>
                        <div class="count" style="font-size: 16px; font-weight: bold;">
                            <?= $ConsultaDatos['SUCURSAL'] ?>
                        </div>
                    </div>

                    <!-- Ejecutivo -->
                    <div class="col-md-2 col-sm-2 tile_stats_count">
                        <span class="count_top" style="font-size: 15px;">
                            <i class="fa fa-user-tie"></i> Ejecutivo
                        </span>
                        <div class="count" style="font-size: 16px; font-weight: bold;">
                            <?= $ConsultaDatos['EJECUTIVO']; ?>
                        </div>
                    </div>

                </div>

                <!-- CONSOLA SQL -->
                <div class="col-md-12" style="margin-top: 15px;">
                    <div style="
                        background-color: #1e1e1e;
                        color: #dcdcdc;
                        padding: 18px;
                        border-radius: 6px;
                        font-family: Consolas, 'Courier New', monospace;
                        font-size: 21px;
                        white-space: pre-wrap;
                        border: 1px solid #444;
                        line-height: 1.4;
                    "><?= $resultado ?>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

<?php echo $footer; ?>
