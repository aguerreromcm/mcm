<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Cambio de Sucursal</h3>
                <div class="clearfix"></div>
            </div>

            <?php include __DIR__ . '/partials/cambio_sucursal_carga_masiva.php'; ?>

            <?php if (!empty($mensaje_error)) : ?>
                <div class="col-md-12" style="margin-top: 15px;">
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars((string) $mensaje_error, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php echo $footer; ?>
