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
                                <th>Registros de cierre procesados</th>
                                <th>Créditos (devengo)</th>
                                <th>Monto intereses devengados</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyUltimosCierres">
                            <?php
                            $listaCierres = isset($listaCierres) ? $listaCierres : [];
                            if (empty($listaCierres)) {
                                echo '<tr><td colspan="8">No hay cierres registrados.</td></tr>';
                            } else {
                                foreach ($listaCierres as $c) {
                                    $estado = !empty($c['EXITO']) ? 'OK' : 'Error';
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($c['FECHA_CALCULO'] ?? '-') . '</td>';
                                    $inicioRaw = (string) ($c['INICIO'] ?? '-');
                                    $finRaw = (string) ($c['FIN'] ?? '-');
                                    echo '<td><span class="js-local-time">' . htmlspecialchars($inicioRaw) . '</span></td>';
                                    echo '<td><span class="js-local-time">' . htmlspecialchars($finRaw) . '</span></td>';
                                    echo '<td>' . htmlspecialchars($c['USUARIO'] ?? '-') . '</td>';
                                    echo '<td>' . htmlspecialchars($estado) . '</td>';
                                    echo '<td>' . htmlspecialchars((string) ($c['REGISTROS_PROCESADOS'] ?? '0')) . '</td>';
                                    echo '<td>' . htmlspecialchars((string) ($c['CREDITOS_DEVENGO'] ?? '0')) . '</td>';
                                    echo '<td>' . htmlspecialchars($c['MONTO_INTERESES_DEVENGADOS'] ?? '$ 0.00') . '</td>';
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

<script>
    (function () {
        const parseDmYHmAsUtc = (txt) => {
            if (!txt || txt === "-") return null;
            const m = txt.match(/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})$/);
            if (!m) return null;
            const dia = parseInt(m[1], 10);
            const mes = parseInt(m[2], 10) - 1;
            const anio = parseInt(m[3], 10);
            const hora = parseInt(m[4], 10);
            const minuto = parseInt(m[5], 10);
            return new Date(Date.UTC(anio, mes, dia, hora, minuto, 0));
        };

        document.querySelectorAll(".js-local-time").forEach((el) => {
            const original = (el.textContent || "").trim();
            const dt = parseDmYHmAsUtc(original);
            if (!dt || isNaN(dt.getTime())) return;
            const dd = String(dt.getDate()).padStart(2, "0");
            const mm = String(dt.getMonth() + 1).padStart(2, "0");
            const yyyy = dt.getFullYear();
            const hh = String(dt.getHours()).padStart(2, "0");
            const mi = String(dt.getMinutes()).padStart(2, "0");
            el.textContent = dd + "/" + mm + "/" + yyyy + " " + hh + ":" + mi;
        });
    })();
</script>

<?= $footer; ?>
