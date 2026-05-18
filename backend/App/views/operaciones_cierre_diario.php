<?= $header; ?>

<div class="right_col">
    <style>
        .cierre-dia-wrap .bloque {
            border: 1px solid #2f3a4f;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 14px;
            background: transparent;
        }
        .cierre-dia-wrap .tab-content {
            padding-top: 12px;
        }
        .cierre-dia-wrap .tile_stats_count .count {
            font-size: 18px;
        }
        .cierre-dia-wrap .subtitulo {
            margin: 0 0 8px 0;
            font-size: 14px;
            font-weight: 600;
        }
        .cierre-dia-wrap .cierre-descripcion {
            margin: 0 0 12px 0;
        }
        /* Misma altura que .form-control (34px); el date nativo a veces pinta más bajo que los .btn */
        .cierre-dia-wrap .cierre-fila-fecha-acciones {
            --cierre-fecha-control-h: 34px;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px 16px;
        }
        .cierre-dia-wrap .cierre-fila-fecha-acciones .cierre-grupo-fecha-buscar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 8px;
        }
        .cierre-dia-wrap .cierre-fila-fecha-acciones .cierre-grupo-acciones {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 16px;
            margin-left: auto;
        }
        .cierre-dia-wrap .cierre-fila-fecha-acciones .input-fecha-operativa {
            max-width: 14.5rem;
            min-width: 12rem;
            height: var(--cierre-fecha-control-h);
            min-height: var(--cierre-fecha-control-h);
            box-sizing: border-box;
        }
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .cierre-dia-wrap .cierre-fila-fecha-acciones .input-fecha-operativa {
                line-height: var(--cierre-fecha-control-h);
            }
        }
        .cierre-dia-wrap .cierre-fila-fecha-acciones .cierre-bloque-botones .btn {
            height: var(--cierre-fecha-control-h);
            min-height: var(--cierre-fecha-control-h);
            padding-top: 0;
            padding-bottom: 0;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0;
        }
        .cierre-dia-wrap .select-filtro-aplicacion {
            max-width: 14.5rem;
            min-width: 12rem;
        }
        .cierre-dia-wrap .cierre-fila-fecha-acciones .cierre-bloque-fecha {
            flex: 0 0 auto;
        }
        .cierre-dia-wrap .cierre-fila-fecha-acciones .cierre-bloque-botones {
            flex: 0 0 auto;
        }
        .cierre-dia-wrap .cierre-filtro-tabla {
            margin-bottom: 12px;
            margin-top: 4px;
        }
        .cierre-dia-wrap .table > thead > tr > th,
        .cierre-dia-wrap .table > tbody > tr > td {
            text-align: center;
            vertical-align: middle;
        }
        .cierre-dia-wrap .dataTables_wrapper .table > thead > tr > th,
        .cierre-dia-wrap .dataTables_wrapper .table > tbody > tr > td {
            text-align: center;
            vertical-align: middle;
        }
    </style>

    <div class="panel panel-default cierre-dia-wrap">
        <div class="panel-body">
            <div class="x_title">
                <h3 style="margin: 0;">Cierre de día</h3>
                <div class="clearfix"></div>
            </div>

            <p class="text-muted cierre-descripcion">
                Consulta y ejecuta el cierre de día.
            </p>

            <div class="bloque">
                <div class="cierre-fila-fecha-acciones">
                    <div class="cierre-grupo-fecha-buscar">
                        <div class="cierre-bloque-fecha">
                            <label for="fecha">Fecha operativa</label>
                            <input type="date" id="fecha" class="form-control input-fecha-operativa" min="<?= date('Y-m-d', strtotime('-30 days')) ?>" max="<?= date('Y-m-d', strtotime('1 days')) ?>" value="<?= date('Y-m-d', strtotime('-1 day')) ?>">
                        </div>
                        <div class="cierre-bloque-botones">
                            <button type="button" class="btn btn-default" id="btnBuscarFecha">Buscar</button>
                        </div>
                    </div>
                    <div class="cierre-grupo-acciones cierre-bloque-botones">
                        <button type="button" class="btn btn-primary" id="procesar">Generar cierre</button>
                        <button type="button" class="btn btn-info" id="btnInfoDiaCierre" title="Muestra conteos del día seleccionado (cobranza, cierre de cartera, devengo y depósitos)">Resumen de Cierre</button>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning" role="alert" style="display: none;" id="alertaEjecucion">
                <strong>El cierre se está ejecutando.</strong><br>
                <span id="tiempoEstimado"></span>
            </div>

            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tabPagos" aria-controls="tabPagos" role="tab" data-toggle="tab">Pagos</a></li>
                <li role="presentation"><a href="#tabConciliacion" aria-controls="tabConciliacion" role="tab" data-toggle="tab">Conciliación</a></li>
                <li role="presentation"><a href="#tabCierre" aria-controls="tabCierre" role="tab" data-toggle="tab">Histórico</a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tabPagos">
                    <div class="bloque">
                        <h5 class="subtitulo">Resumen de pagos</h5>
                        <div class="tile_count col-sm-12" style="margin-bottom: 8px; margin-top: 8px;">
                            <div class="col-md-2 col-sm-4 tile_stats_count">
                                <span class="count_top"><i class="fa fa-list"></i> Total</span>
                                <div class="count" id="totalPagos">0</div>
                                <span class="count_bottom" id="importeTotal">$ 0.00</span>
                            </div>
                            <div class="col-md-2 col-sm-4 tile_stats_count">
                                <span class="count_top"><i class="fa fa-clock-o"></i> Pendientes</span>
                                <div class="count" id="totalPagosPendientes">0</div>
                                <span class="count_bottom" id="importePendientes">$ 0.00</span>
                            </div>
                            <div class="col-md-2 col-sm-4 tile_stats_count">
                                <span class="count_top"><i class="fa fa-check"></i> Aplicados</span>
                                <div class="count" id="totalPagosAplicados">0</div>
                                <span class="count_bottom" id="importeAplicados">$ 0.00</span>
                            </div>
                            <div class="col-md-2 col-sm-4 tile_stats_count">
                                <span class="count_top"><i class="fa fa-money"></i> Pagos</span>
                                <div class="count" id="cntResumenPagos">0</div>
                                <span class="count_bottom" id="impResumenPagos">$ 0.00</span>
                            </div>
                            <div class="col-md-2 col-sm-4 tile_stats_count">
                                <span class="count_top"><i class="fa fa-shield"></i> Garantías</span>
                                <div class="count" id="cntResumenGarantias">0</div>
                                <span class="count_bottom" id="impResumenGarantias">$ 0.00</span>
                            </div>
                            <div class="col-md-2 col-sm-4 tile_stats_count">
                                <span class="count_top"><i class="fa fa-exclamation-triangle"></i> Incidencias</span>
                                <div class="count" id="cntResumenIncidencias">0</div>
                                <span class="count_bottom" id="impResumenIncidencias">$ 0.00</span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="row cierre-filtro-tabla">
                        <div class="col-md-3 col-sm-5">
                            <label for="filtroEstadoAplicacion">Mostrar en tabla</label>
                            <select id="filtroEstadoAplicacion" class="form-control select-filtro-aplicacion">
                                <option value="pendientes" selected>Pendientes</option>
                                <option value="aplicados">Aplicados</option>
                                <option value="todos">Todos</option>
                            </select>
                        </div>
                    </div>

                    <div class="dataTable_wrapper">
                        <table id="tablaAplicarPagos" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Referencia</th>
                                    <th>Monto</th>
                                    <th>Moneda</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tablaAplicarPagosBody">
                                <tr><td colspan="5">Sin datos cargados.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="tabConciliacion">
                    <div class="bloque">
                        <h5 class="subtitulo">Resumen de conciliación</h5>
                        <div class="tile_count col-sm-12" style="margin-bottom: 8px; margin-top: 8px;">
                            <div class="col-md-2 col-sm-4 tile_stats_count">
                                <span class="count_top"><i class="fa fa-clock-o"></i> Pendientes</span>
                                <div class="count" id="totalPagosConciliacion">0</div>
                                <span class="count_bottom" id="importeTotalConciliacion">$ 0.00</span>
                            </div>
                            <div class="col-md-2 col-sm-4 tile_stats_count">
                                <span class="count_top"><i class="fa fa-check"></i> Conciliados</span>
                                <div class="count" id="totalPagosConciliacionHechos">0</div>
                                <span class="count_bottom" id="importeConciliacionHechos">$ 0.00</span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="row" style="margin-bottom: 12px;">
                        <div class="col-md-3 col-sm-5">
                            <label for="filtroEstadoConciliacion">Mostrar en tabla</label>
                            <select id="filtroEstadoConciliacion" class="form-control select-filtro-aplicacion">
                                <option value="pendientes" selected>Pendientes</option>
                                <option value="conciliados">Conciliados</option>
                                <option value="todos">Todos</option>
                            </select>
                        </div>
                    </div>

                    <div class="dataTable_wrapper">
                        <table id="tablaConciliacion" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Referencia</th>
                                    <th>Crédito</th>
                                    <th>Ciclo</th>
                                    <th>Nombre</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tablaConciliacionBody">
                                <tr><td colspan="7">Sin datos cargados.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="tabCierre">
                    <p class="text-muted cierre-descripcion">Resumen de los 7 días anteriores.</p>
                    <div class="table-responsive">
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
                                        $estado = isset($c['ESTADO_TEXTO']) ? (string) $c['ESTADO_TEXTO'] : (
                                            (!empty($c['EN_PROCESO']) || empty($c['FIN']) || trim((string) ($c['FIN'] ?? '')) === '')
                                                ? 'Procesando'
                                                : 'Finalizado'
                                        );
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

    <div class="modal fade" id="modalInfoDiaCierre" tabindex="-1" role="dialog" aria-labelledby="modalInfoDiaCierreTitle">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalInfoDiaCierreTitle">Resumen de Cierre <small id="modalInfoDiaFechaLabel" class="text-muted"></small></h4>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <p class="text-muted small" style="margin-bottom: 14px;">Para la fecha operativa elegida se muestran cuántos movimientos quedaron registrados en cuatro bloques: cobranza del día (pagos, garantías y operaciones equivalentes), cierre de cartera de créditos, intereses devengados del día y depósitos reflejados en cuenta.</p>

                    <h5 class="subtitulo" style="margin-top: 0;">Cobranza del día</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr><th>Fecha operativa</th><th>Registros</th></tr></thead>
                            <tbody id="tbodyInfoDiaPagosdia"><tr><td colspan="2">Sin datos.</td></tr></tbody>
                        </table>
                    </div>

                    <h5 class="subtitulo">Cierre de cartera</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr><th>Fecha de cierre</th><th>Registros</th></tr></thead>
                            <tbody id="tbodyInfoDiaTblCierre"><tr><td colspan="2">Sin datos.</td></tr></tbody>
                        </table>
                    </div>

                    <h5 class="subtitulo">Intereses devengados</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr><th>Fecha de cálculo</th><th>Registros</th></tr></thead>
                            <tbody id="tbodyInfoDiaDevengo"><tr><td colspan="2">Sin datos.</td></tr></tbody>
                        </table>
                    </div>

                    <h5 class="subtitulo">Depósitos en cuenta</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr><th>Fecha de depósito</th><th>Registros</th></tr></thead>
                            <tbody id="tbodyInfoDiaMpPd"><tr><td colspan="2">Sin datos.</td></tr></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const formateaMoneda = (n) => "$ " + (Number(n || 0)).toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        /** DataTables: mismo estilo que el resto del sistema; página inicial 20 registros. */
        const destruirDataTableSiExiste = (selectorTabla) => {
            if (typeof $ === "undefined" || !$.fn.DataTable) return;
            if ($.fn.DataTable.isDataTable(selectorTabla)) {
                $(selectorTabla).DataTable().destroy();
            }
        };

        const opcionesDataTableCierreDia = (emptyTableMsg) => ({
            pageLength: 20,
            lengthMenu: [[10, 20, 40, -1], [10, 20, 40, "Todos"]],
            order: [],
            autoWidth: false,
            language: {
                emptyTable: emptyTableMsg || "No hay datos disponibles",
                paginate: {
                    previous: "Anterior",
                    next: "Siguiente",
                },
                info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Sin registros para mostrar",
                zeroRecords: "No se encontraron registros",
                lengthMenu: "Mostrar _MENU_ registros por página",
                search: "Buscar:",
            },
            createdRow: function (row) {
                $(row).find("td").css({ verticalAlign: "middle", textAlign: "center" });
            },
            columnDefs: [{ targets: "_all", className: "text-center" }],
        });

        const aplicarDataTableCierreDia = (selectorTabla, emptyTableMsg) => {
            if (typeof $ === "undefined" || !$.fn.DataTable) return;
            destruirDataTableSiExiste(selectorTabla);
            $(selectorTabla).DataTable(opcionesDataTableCierreDia(emptyTableMsg));
        };

        const parseDmYHmAsUtc = (txt) => {
            if (!txt || txt === "-") return null;
            const m = txt.match(/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})$/);
            if (!m) return null;
            return new Date(Date.UTC(parseInt(m[3], 10), parseInt(m[2], 10) - 1, parseInt(m[1], 10), parseInt(m[4], 10), parseInt(m[5], 10), 0));
        };

        let cacheFilasAplicacion = [];
        let cacheResumenPagos = null;
        let cacheFilasConciliacion = [];
        let cacheResumenConciliacion = null;

        const codigoConciliado = (f) => {
            const raw = f.CONCILIADO != null ? f.CONCILIADO : f.conciliado;
            return raw != null ? String(raw).trim().toUpperCase() : "";
        };

        const filaConciliacionPendiente = (f) => codigoConciliado(f) === "C";

        const filaConciliacionConciliada = (f) => codigoConciliado(f) === "D";

        const textoEstadoConciliacion = (f) => {
            const c = codigoConciliado(f);
            if (c === "C") return "Pendiente";
            if (c === "D") return "Conciliado";
            if (c === "N") return "Ignorado";
            return c !== "" ? c : "Pendiente";
        };

        const filasConciliacionSegunFiltro = (filas, filtro) => {
            if (filtro === "pendientes") return filas.filter((f) => filaConciliacionPendiente(f));
            if (filtro === "conciliados") return filas.filter((f) => filaConciliacionConciliada(f));
            return filas.slice();
        };

        const pintarResumenConciliacion = (resumen) => {
            const r = resumen || {};
            const pend = r.totalNoConciliados != null ? Number(r.totalNoConciliados) : 0;
            const conc = r.totalConciliados != null ? Number(r.totalConciliados) : 0;
            const impPend = r.importeNoConciliados != null ? Number(r.importeNoConciliados) : 0;
            const impConc = r.importeConciliados != null ? Number(r.importeConciliados) : 0;
            const elPend = document.getElementById("totalPagosConciliacion");
            const elImpPend = document.getElementById("importeTotalConciliacion");
            const elConc = document.getElementById("totalPagosConciliacionHechos");
            const elImpConc = document.getElementById("importeConciliacionHechos");
            if (elPend) elPend.textContent = pend;
            if (elImpPend) elImpPend.textContent = formateaMoneda(impPend);
            if (elConc) elConc.textContent = conc;
            if (elImpConc) elImpConc.textContent = formateaMoneda(impConc);
        };

        const pintarSoloTablaConciliacion = () => {
            const tbody = document.getElementById("tablaConciliacionBody");
            if (!tbody) return;
            const sel = document.getElementById("filtroEstadoConciliacion");
            const filtro = sel ? (sel.value || "pendientes") : "pendientes";
            const filas = filasConciliacionSegunFiltro(cacheFilasConciliacion, filtro);

            destruirDataTableSiExiste("#tablaConciliacion");

            if (!cacheFilasConciliacion.length) {
                tbody.innerHTML = "";
                aplicarDataTableCierreDia("#tablaConciliacion", "No hay datos para la fecha seleccionada.");
                return;
            }
            if (!filas.length) {
                const msg = filtro === "conciliados"
                    ? "No hay pagos conciliados con el filtro actual."
                    : (filtro === "pendientes"
                        ? "No hay pagos pendientes con el filtro actual."
                        : "No hay registros.");
                tbody.innerHTML = "";
                aplicarDataTableCierreDia("#tablaConciliacion", msg);
                return;
            }
            tbody.innerHTML = filas.map((f) => {
                const monto = typeof f.CANTIDAD === "number" ? f.CANTIDAD : (parseFloat(f.CANTIDAD) || 0);
                const credito = f.CREDITO != null ? f.CREDITO : (f.CDGCLNS != null ? f.CDGCLNS : f.cdgclns);
                return "<tr>" +
                    "<td>" + (f.FECHA || f.fecha || f.FREALDEP || f.frealdep || "-") + "</td>" +
                    "<td>" + (f.REFERENCIA || f.referencia || "-") + "</td>" +
                    "<td>" + (credito || "-") + "</td>" +
                    "<td>" + (f.CICLO || f.ciclo || "-") + "</td>" +
                    "<td>" + (f.NOMBRE || f.nombre || "-") + "</td>" +
                    "<td>" + formateaMoneda(monto) + "</td>" +
                    "<td>" + textoEstadoConciliacion(f) + "</td>" +
                    "</tr>";
            }).join("");
            aplicarDataTableCierreDia("#tablaConciliacion", "No hay datos disponibles");
        };

        const filaEstaAplicada = (f) => {
            const fi = f.F_IMPORTACION != null ? f.F_IMPORTACION : f.f_importacion;
            if (fi != null && String(fi).trim() !== "") return true;
            const idImp = f.ID_IMPORTACION != null ? f.ID_IMPORTACION : f.id_importacion;
            if (idImp == null || idImp === "") return false;
            const n = Number(idImp);
            return !Number.isNaN(n) && n > 0;
        };

        const filasAplicacionSegunFiltro = (filas, filtro) => {
            if (filtro === "aplicados") return filas.filter((f) => filaEstaAplicada(f));
            if (filtro === "pendientes") return filas.filter((f) => !filaEstaAplicada(f));
            return filas.slice();
        };

        const pintarSoloTablaAplicacion = () => {
            const tbody = document.getElementById("tablaAplicarPagosBody");
            const sel = document.getElementById("filtroEstadoAplicacion");
            const filtro = sel ? (sel.value || "pendientes") : "pendientes";
            const filas = cacheFilasAplicacion;

            destruirDataTableSiExiste("#tablaAplicarPagos");

            if (!filas.length) {
                tbody.innerHTML = "";
                aplicarDataTableCierreDia("#tablaAplicarPagos", "No hay datos para la fecha seleccionada.");
                return;
            }
            const mostrar = filasAplicacionSegunFiltro(filas, filtro);
            if (!mostrar.length) {
                const msg = filtro === "aplicados" ? "No hay pagos aplicados con el filtro actual." : (filtro === "pendientes" ? "No hay pagos pendientes con el filtro actual." : "No hay registros.");
                tbody.innerHTML = "";
                aplicarDataTableCierreDia("#tablaAplicarPagos", msg);
                return;
            }
            tbody.innerHTML = mostrar.map((f) => {
                const monto = typeof f.MONTO === "number" ? f.MONTO : (parseFloat(f.MONTO) || 0);
                const aplicado = filaEstaAplicada(f);
                const estado = aplicado ? "Aplicado" : "Pendiente";
                return "<tr><td>" + (f.FECHA || "-") + "</td><td>" + (f.REFERENCIA || "-") + "</td><td>" + formateaMoneda(monto) + "</td><td>" + (f.MONEDA || "MN") + "</td><td>" + estado + "</td></tr>";
            }).join("");
            aplicarDataTableCierreDia("#tablaAplicarPagos", "No hay datos disponibles");
        };

        const pintarResumenPagos = (resumen, meta) => {
            const r = resumen || {};
            const m = meta || {};
            const totalReg = r.totalRegistros != null ? Number(r.totalRegistros) : cacheFilasAplicacion.length;
            const totalImp = r.totalImporte != null ? Number(r.totalImporte) : 0;
            const yaProcesado = !!m.yaProcesado;
            const pendientes = r.totalPendientes != null ? Number(r.totalPendientes) : (yaProcesado ? 0 : totalReg);
            const aplicados = r.totalAplicados != null ? Number(r.totalAplicados) : (yaProcesado ? totalReg : 0);
            const impPend = r.importePendientes != null ? Number(r.importePendientes) : (yaProcesado ? 0 : totalImp);
            const impApl = r.importeAplicados != null ? Number(r.importeAplicados) : (yaProcesado ? totalImp : 0);

            const rp = r.registrosPagos != null ? Number(r.registrosPagos) : 0;
            const rg = r.registrosGarantias != null ? Number(r.registrosGarantias) : 0;
            const ri = r.registrosIncidencias != null ? Number(r.registrosIncidencias) : 0;
            const impPag = r.importePagos != null ? Number(r.importePagos) : 0;
            const impGar = r.importeGarantias != null ? Number(r.importeGarantias) : 0;
            const impInc = r.importeIncidencias != null ? Number(r.importeIncidencias) : 0;

            document.getElementById("totalPagos").textContent = totalReg;
            document.getElementById("importeTotal").textContent = formateaMoneda(totalImp);
            document.getElementById("totalPagosPendientes").textContent = pendientes;
            document.getElementById("importePendientes").textContent = formateaMoneda(impPend);
            document.getElementById("totalPagosAplicados").textContent = aplicados;
            document.getElementById("importeAplicados").textContent = formateaMoneda(impApl);
            document.getElementById("cntResumenPagos").textContent = rp;
            document.getElementById("impResumenPagos").textContent = formateaMoneda(impPag);
            document.getElementById("cntResumenGarantias").textContent = rg;
            document.getElementById("impResumenGarantias").textContent = formateaMoneda(impGar);
            document.getElementById("cntResumenIncidencias").textContent = ri;
            document.getElementById("impResumenIncidencias").textContent = formateaMoneda(impInc);
        };

        const renderPagos = (filas, resumen, meta) => {
            cacheFilasAplicacion = Array.isArray(filas) ? filas : [];
            cacheResumenPagos = { resumen: resumen || {}, meta: meta || {} };
            pintarResumenPagos(cacheResumenPagos.resumen, cacheResumenPagos.meta);
            pintarSoloTablaAplicacion();
        };

        const renderConciliacion = (filas, resumen) => {
            cacheFilasConciliacion = Array.isArray(filas) ? filas : [];
            cacheResumenConciliacion = resumen || {};
            pintarResumenConciliacion(cacheResumenConciliacion);
            pintarSoloTablaConciliacion();
        };

        const cerrarEsperaBuscar = () => {
            try {
                if (typeof swal !== "undefined" && swal.close) swal.close();
            } catch (e) { /* ignore */ }
        };

        const mostrarEsperaBuscar = () => {
            if (typeof showWait === "function") {
                showWait("Procesando la solicitud, espere un momento...");
            } else if (typeof swal !== "undefined") {
                swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
            }
        };

        const buscarPorFecha = () => {
            const fecha = (document.getElementById("fecha").value || "").trim();
            if (!fecha) return;

            mostrarEsperaBuscar();

            $.ajax({
                url: "/Operaciones/ProcesarAplicarPagos/",
                type: "POST",
                dataType: "json",
                data: { fecha: fecha, ejecutar: 0 }
            }).done((resp) => {
                if (resp && resp.success) {
                    const datos = resp.datos || {};
                    renderPagos(datos.filas || [], datos.resumen || {}, datos);
                } else {
                    const datos = (resp && resp.datos) ? resp.datos : {};
                    const filas = Array.isArray(datos.filas) ? datos.filas : [];
                    const msg = (resp && resp.mensaje) ? resp.mensaje : "";
                    if (filas.length === 0 && msg.indexOf("No hay datos para la fecha") !== -1) {
                        renderPagos([], { totalRegistros: 0, totalImporte: 0, totalPendientes: 0, totalAplicados: 0, importePendientes: 0, importeAplicados: 0, estado: "Sin movimientos", fechaEjecucion: "-" }, { yaProcesado: true });
                    } else if (typeof showError === "function") {
                        showError(msg || "No fue posible cargar pagos para la fecha.");
                    } else {
                        renderPagos([], {}, {});
                    }
                }
            }).fail(() => {
                if (typeof showError === "function") showError("Error de conexión al consultar pagos.");
            }).always(() => {
                $.ajax({
                    url: "/Operaciones/ConsultarConciliacion/",
                    type: "POST",
                    dataType: "json",
                    data: { fecha: fecha, codigo: "", ciclo: "", ctaBancaria: "", modoConciliado: "importados" }
                }).done((resp) => {
                    if (resp && resp.success) {
                        const datos = resp.datos || {};
                        renderConciliacion(datos.filas || [], datos.resumen || {});
                    } else {
                        renderConciliacion([], { totalNoConciliados: 0, importeNoConciliados: 0, totalConciliados: 0, importeConciliados: 0 });
                        const msg = (resp && resp.mensaje) ? resp.mensaje : "";
                        if (msg && typeof showError === "function" && msg.indexOf("No hay pagos pendientes") === -1 && msg.indexOf("Se encontraron") === -1) {
                            showError(msg);
                        }
                    }
                }).fail(() => {
                    if (typeof showError === "function") showError("Error de conexión al consultar conciliación.");
                }).always(() => {
                    cerrarEsperaBuscar();
                });
            });
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

        const escHtml = (s) => {
            const d = document.createElement("div");
            d.textContent = s == null ? "" : String(s);
            return d.innerHTML;
        };

        const pintarTablaInfoDia = (tbodyId, filas) => {
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;
            if (!filas || !filas.length) {
                tbody.innerHTML = '<tr><td colspan="2">Sin registros para este día.</td></tr>';
                return;
            }
            tbody.innerHTML = filas.map((r) => {
                const f = r.fecha != null ? r.fecha : (r.FECHA != null ? r.FECHA : "-");
                const c = r.registros != null ? r.registros : (r.CNT != null ? r.CNT : (r.cnt != null ? r.cnt : "0"));
                return "<tr><td>" + escHtml(f) + "</td><td>" + escHtml(c) + "</td></tr>";
            }).join("");
        };

        const abrirInformacionDia = () => {
            const fecha = (document.getElementById("fecha").value || "").trim();
            if (!fecha) {
                if (typeof showError === "function") showError("Seleccione la fecha operativa.");
                return;
            }
            const lbl = document.getElementById("modalInfoDiaFechaLabel");
            if (lbl) lbl.textContent = "(solo " + fecha + ")";
            $("#modalInfoDiaCierre").modal("show");
            const tbodies = ["tbodyInfoDiaPagosdia", "tbodyInfoDiaTblCierre", "tbodyInfoDiaDevengo", "tbodyInfoDiaMpPd"];
            tbodies.forEach((id) => {
                const el = document.getElementById(id);
                if (el) el.innerHTML = '<tr><td colspan="2"><span class="text-muted">Cargando…</span></td></tr>';
            });
            $.ajax({
                url: "/Operaciones/InformacionDiaCierre/",
                type: "POST",
                dataType: "json",
                data: { fecha: fecha }
            }).done((resp) => {
                if (!resp || !resp.success || !resp.datos) {
                    const msg = (resp && resp.mensaje) ? resp.mensaje : "No fue posible cargar la información.";
                    if (typeof showError === "function") showError(msg);
                    tbodies.forEach((id) => {
                        const el = document.getElementById(id);
                        if (el) el.innerHTML = '<tr><td colspan="2">—</td></tr>';
                    });
                    return;
                }
                const d = resp.datos;
                pintarTablaInfoDia("tbodyInfoDiaPagosdia", d.cobranza_del_dia || d.pagosdia || []);
                pintarTablaInfoDia("tbodyInfoDiaTblCierre", d.cierre_de_cartera || d.tbl_cierre_dia || []);
                pintarTablaInfoDia("tbodyInfoDiaDevengo", d.devengo_registrado || d.devengo_diario || []);
                pintarTablaInfoDia("tbodyInfoDiaMpPd", d.depositos_cuenta || d.mp_pd || []);
            }).fail(() => {
                if (typeof showError === "function") showError("Error de conexión al consultar información del día.");
                tbodies.forEach((id) => {
                    const el = document.getElementById(id);
                    if (el) el.innerHTML = '<tr><td colspan="2">—</td></tr>';
                });
            });
        };

        const btnBuscar = document.getElementById("btnBuscarFecha");
        if (btnBuscar) btnBuscar.addEventListener("click", buscarPorFecha);
        const btnInfoDia = document.getElementById("btnInfoDiaCierre");
        if (btnInfoDia) btnInfoDia.addEventListener("click", abrirInformacionDia);
        const filtroAplicacion = document.getElementById("filtroEstadoAplicacion");
        if (filtroAplicacion) filtroAplicacion.addEventListener("change", pintarSoloTablaAplicacion);
        const filtroConciliacion = document.getElementById("filtroEstadoConciliacion");
        if (filtroConciliacion) filtroConciliacion.addEventListener("change", pintarSoloTablaConciliacion);

        document.addEventListener("DOMContentLoaded", function () {
            if (typeof $ === "undefined" || !$.fn.DataTable) return;
            $(document).on("shown.bs.tab", 'a[data-toggle="tab"]', function () {
                try {
                    if ($.fn.DataTable.isDataTable("#tablaAplicarPagos")) {
                        $("#tablaAplicarPagos").DataTable().columns.adjust();
                    }
                    if ($.fn.DataTable.isDataTable("#tablaConciliacion")) {
                        $("#tablaConciliacion").DataTable().columns.adjust();
                    }
                } catch (e) { /* ignore */ }
            });
        });
    })();
</script>

<?= $footer; ?>
