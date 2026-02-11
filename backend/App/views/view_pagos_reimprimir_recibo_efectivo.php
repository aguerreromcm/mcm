<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Reimprimir Recibo de Efectivo <span class="fa fa-print"></span></h3>
                <div class="clearfix"></div>
            </div>
            <div class="card col-md-12">
                <p class="text-muted">Solo se muestran recibos de efectivo del día anterior y el mismo día.</p>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="dataTable_wrapper">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <div>
                            <label for="mostrar-registros">Mostrar </label>
                            <select id="mostrar-registros" style="display:inline-block; width:70px;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span> registros</span>
                        </div>
                        <div>
                            <input type="text" id="search-folios" class="form-control" placeholder="Buscar:" style="width:250px; display:inline-block;" />
                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover" id="tabla-reimprimir">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Sucursal</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Ejecutivo</th>
                                <th>Registros</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $tabla; ?>
                        </tbody>
                    </table>
                </div>
                <script>
                    (function() {
                        const input = document.getElementById('search-folios');
                        const tabla = document.getElementById('tabla-reimprimir');
                        const select = document.getElementById('mostrar-registros');

                        function filtrar() {
                            const q = input.value.trim().toLowerCase();
                            const rows = Array.from(tabla.querySelectorAll('tbody tr'));
                            rows.forEach((tr, idx) => {
                                const text = tr.textContent.toLowerCase();
                                tr.style.display = text.indexOf(q) !== -1 ? '' : 'none';
                            });
                        }

                        input.addEventListener('input', filtrar);

                        // Opcional: limitar número de filas visibles (simple paginado client-side)
                        select.addEventListener('change', function() {
                            const perPage = parseInt(this.value, 10);
                            const rows = Array.from(tabla.querySelectorAll('tbody tr'));
                            rows.forEach((tr, idx) => {
                                tr.style.display = (idx < perPage) ? '' : 'none';
                            });
                        });

                        // Inicializar por defecto
                        select.dispatchEvent(new Event('change'));
                    })();
                </script>

            </div>
        </div>
    </div>
</div>
</div>

<?= $footer; ?>