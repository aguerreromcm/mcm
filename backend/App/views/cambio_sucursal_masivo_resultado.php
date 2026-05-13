<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3>Cambio de Sucursal</h3>
                <div class="clearfix"></div>
            </div>

            <?php include __DIR__ . '/partials/cambio_sucursal_carga_masiva.php'; ?>

            <?php if (!empty($resumen)) : ?>
                <div class="col-md-12" style="margin-top: 15px;">
                    <div class="alert alert-<?php echo !empty($exito) ? 'success' : 'warning'; ?>" role="alert">
                        <?php echo htmlspecialchars((string) $resumen, ENT_QUOTES, 'UTF-8'); ?>
                        <?php if (!empty($detalle_error)) : ?>
                            <br><small><?php echo htmlspecialchars((string) $detalle_error, ENT_QUOTES, 'UTF-8'); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($errores)) : ?>
                <div class="card col-md-12" style="margin-top: 10px;">
                    <div class="card-header">
                        <h5 class="card-title">Filas no procesadas (<?php echo (int) $omitidos; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Fila Excel</th>
                                        <th>Credito</th>
                                        <th>Motivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($errores as $error) : ?>
                                        <tr>
                                            <td><?php echo (int) ($error['fila'] ?? 0); ?></td>
                                            <td><?php echo htmlspecialchars((string) ($error['grupo'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars((string) ($error['motivo'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($actualizados)) : ?>
                <?php
                $registros = $actualizados;
                $mostrarAccion = false;
                include __DIR__ . '/partials/cambio_sucursal_tabla_registros.php';
                ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php echo $footer; ?>
