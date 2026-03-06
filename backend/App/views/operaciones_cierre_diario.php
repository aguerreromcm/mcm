<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Cierre de día</label>
                <div class="clearfix"></div>
            </div>
            <p class="text-muted" style="margin-bottom: 15px;">
                Ejecuta el cierre del día seleccionado (esa fecha es la que se cerrará). El primer día hábil de la semana se cierran los días del fin de semana.
            </p>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="fecha">Fecha de cierre:</label>
                            </div>
                            <div class="form-group" style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <input type="date" id="fecha" class="form-control" style="font-size: 24px; width: auto; max-width: 200px;" min="<?= date('Y-m-d', strtotime('-30 days')) ?>" max="<?= date('Y-m-d', strtotime('1 days')) ?>" value="<?= date('Y-m-d', strtotime('-1 day')) ?>">
                                <button type="button" class="btn btn-primary" id="procesar">Generar</button>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 10px;">
                        <div class="col-md-12">
                            <div class="alert alert-warning" role="alert" style="text-align: left; padding: 10px; margin: 0; display: none;" id="alertaEjecucion">
                                <strong>El cierre se está ejecutando.</strong><br>
                                <span id="tiempoEstimado"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" style="margin-top: 15px;">
                <div class="card-header">
                    <span class="card-title">Últimos 5 cierres</span>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Fecha cierre</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Usuario</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $listaCierres = isset($listaCierres) ? $listaCierres : [];
                            if (empty($listaCierres)) {
                                echo '<tr><td colspan="5">No hay cierres registrados.</td></tr>';
                            } else {
                                foreach ($listaCierres as $c) {
                                    $estado = !empty($c['EXITO']) ? 'OK' : 'Error';
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($c['FECHA_CALCULO'] ?? '-') . '</td>';
                                    echo '<td>' . htmlspecialchars($c['INICIO'] ?? '-') . '</td>';
                                    echo '<td>' . htmlspecialchars($c['FIN'] ?? '-') . '</td>';
                                    echo '<td>' . htmlspecialchars($c['USUARIO'] ?? '-') . '</td>';
                                    echo '<td>' . htmlspecialchars($estado) . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>
