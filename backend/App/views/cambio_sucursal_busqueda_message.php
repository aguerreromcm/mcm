<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3>Cambio de Sucursal</h3>
                <div class="clearfix"></div>
            </div>

            <?php include __DIR__ . '/partials/cambio_sucursal_carga_masiva.php'; ?>

            <div class="card col-md-12">
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="row">
                    <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                        <div class="x_content">
                            <br />
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <label style="font-size: 14px; color: black;">Crédito no encontrado:</label>
                                <li style="color: black;">Valide que el número de crédito sea correcto</li>
                                <li style="color: black;">Si el problema persiste, comuníquese con soporte técnico</li>
                                <br>
                                <a href="/Creditos/CambioSucursal/" class="alert-link">Regresar</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
