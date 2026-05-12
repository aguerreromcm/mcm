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
        /* Misma altura que .form-control (34px); el date nativo a veces pinta más bajo que los .btn */
        .cierre-dia-wrap .cierre-fila-fecha-acciones {
            --cierre-fecha-control-h: 34px;
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 8px;
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
    </style>

    <div class="panel panel-default cierre-dia-wrap">
        <div class="panel-body">
            <div class="x_title">
                <h3 style="margin: 0;">Cierre de día</h3>
                <div class="clearfix"></div>
            </div>

            <p class="text-muted">
                Consulta y ejecuta el cierre de día desde una sola pantalla, con resumen de pagos, conciliación e historial de cierres.
            </p>

            <div class="bloque">
                <div class="cierre-fila-fecha-acciones">
                    <div class="cierre-bloque-fecha">
                        <label for="fecha">Fecha operativa</label>
                        <input type="date" id="fecha" class="form-control input-fecha-operativa" min="<?= date('Y-m-d', strtotime('-30 days')) ?>" max="<?= date('Y-m-d', strtotime('1 days')) ?>" value="<?= date('Y-m-d', strtotime('-1 day')) ?>">
                    </div>
                    <div class="cierre-bloque-botones">
                        <button type="button" class="btn btn-default" id="btnBuscarFecha">Buscar</button>
                        <button type="button" class="btn btn-primary" id="procesar">Generar cierre</button>
                        <button type="button" class="btn btn-info" id="btnInfoDiaCierre" title="Muestra los conteos del día seleccionado en las tablas clave de operación">Resumen de Cierre</button>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning" role="alert" style="display: none;" id="alertaEjecucion">
                <strong>El cierre se está ejecutando.</strong><br>
                <span id="tiempoEstimado"></span>
            </div>

            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tabPagos" aria-controls="tabPagos" role="tab" data-toggle="tab">Aplicación</a></li>
                <li role="presentation"><a href="#tabConciliacion" aria-controls="tabConciliacion" role="tab" data-toggle="tab">Conciliación</a></li>
                <li role="presentation"><a href="#tabCierre" aria-controls="tabCierre" role="tab" data-toggle="tab">Historial cierres</a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tabPagos">
                    <div class="bloque">
                        <h5 class="subtitulo">Resumen de aplicación de pagos</h5>
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

                    <div class="row" style="margin-bottom: 12px;">
                        <div class="col-md-3 col-sm-5">
                            <label for="filtroEstadoAplicacion">Mostrar en tabla</label>
                            <select id="filtroEstadoAplicacion" class="form-control select-filtro-aplicacion">
                                <option value="todos">Todos</option>
                                <option value="pendientes">Pendientes</option>
                                <option value="aplicados">Aplicados</option>
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
                                <option value="todos">Todos</option>
                                <option value="pendientes">Pendientes</option>
                                <option value="conciliados">Conciliados</option>
                            </select>
                        </div>
                    </div>

                    <div class="dataTable_wrapper">
                        <table id="tablaConciliacion" class="table table-striped table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Empresa</th>
                                    <th>Fecha de Pago</th>
                                    <th>Referencia</th>
                                    <th>Tipo Cte.</th>
                                    <th>Crédito (Ind./Gpo.)</th>
                                    <th>Ciclo</th>
                                    <th>Periodo</th>
                                    <th>SecuenciaIM</th>
                                    <th>Nombre (Ind./Gpo.)</th>
                                    <th>Monto</th>
                                    <th>Cta. Bancaria</th>
                                    <th>Código Gpo.</th>
                                    <th>Tasa</th>
                                    <th>SecuenciaMP</th>
                                    <th>Plazo</th>
                                    <th>Periodicidad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tablaConciliacionBody">
                                <tr><td colspan="18">Sin datos cargados.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="tabCierre">
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

    <div class="modal fade" id="modalInfoDiaCierre" tabindex="-1" role="dialog" aria-labelledby="modalInfoDiaCierreTitle">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalInfoDiaCierreTitle">Resumen de Cierre <small id="modalInfoDiaFechaLabel" class="text-muted"></small></h4>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <p class="text-muted small" style="margin-bottom: 14px;">Este resumen muestra, para la fecha operativa seleccionada, cuántos registros existen en PAGOSDIA (P/X/G), TBL_CIERRE_DIA, DEVENGO_DIARIO y MP (tipo PD).</p>

                    <h5 class="subtitulo" style="margin-top: 0;">PAGOSDIA <small class="text-muted">(TIPO IN P, X, G)</small></h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr><th>Fecha</th><th>Registros</th></tr></thead>
                            <tbody id="tbodyInfoDiaPagosdia"><tr><td colspan="2">Sin datos.</td></tr></tbody>
                        </table>
                    </div>

                    <h5 class="subtitulo">TBL_CIERRE_DIA</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr><th>Fecha cálculo</th><th>Registros</th></tr></thead>
                            <tbody id="tbodyInfoDiaTblCierre"><tr><td colspan="2">Sin datos.</td></tr></tbody>
                        </table>
                    </div>

                    <h5 class="subtitulo">DEVENGO_DIARIO</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr><th>Fecha cálculo</th><th>Registros</th></tr></thead>
                            <tbody id="tbodyInfoDiaDevengo"><tr><td colspan="2">Sin datos.</td></tr></tbody>
                        </table>
                    </div>

                    <h5 class="subtitulo">MP <small class="text-muted">(TIPO = PD)</small></h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr><th>F. depósito</th><th>Registros</th></tr></thead>
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
                $(row).find("td").css("vertical-align", "middle");
            },
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
        let cacheFilasConciliacion = [];

        const filaConciliacionPendiente = (f) => {
            const raw = f.CONCILIADO != null ? f.CONCILIADO : f.conciliado;
            const c = raw != null ? String(raw).trim().toUpperCase() : "";
            return c === "" || c === "N";
        };

        const textoEstadoConciliacion = (f) => {
            return filaConciliacionPendiente(f) ? "Pendiente" : "Conciliado";
        };

        const filasConciliacionSegunFiltro = (filas, filtro) => {
            if (filtro === "pendientes") return filas.filter((f) => filaConciliacionPendiente(f));
            if (filtro === "conciliados") return filas.filter((f) => !filaConciliacionPendiente(f));
            return filas.slice();
        };

        const pintarSoloTablaConciliacion = () => {
            const tbody = document.getElementById("tablaConciliacionBody");
            if (!tbody) return;
            const sel = document.getElementById("filtroEstadoConciliacion");
            const filtro = sel ? (sel.value || "todos") : "todos";
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
            tbody.innerHTML = filas.map((f, idx) => {
                const monto = typeof f.CANTIDAD === "number" ? f.CANTIDAD : (parseFloat(f.CANTIDAD) || 0);
                return "<tr>" +
                    "<td>" + (idx + 1) + "</td>" +
                    "<td>" + (f.CDGEM || f.cdgem || "-") + "</td>" +
                    "<td>" + (f.FREALDEP || f.frealdep || "-") + "</td>" +
                    "<td>" + (f.REFERENCIA || f.referencia || "-") + "</td>" +
                    "<td>" + (f.TIPOCTE || f.tipocte || "-") + "</td>" +
                    "<td>" + (f.CDGCLNS || f.cdgclns || "-") + "</td>" +
                    "<td>" + (f.CICLO || f.ciclo || "-") + "</td>" +
                    "<td>" + (f.PERIODO != null ? f.PERIODO : (f.periodo || "-")) + "</td>" +
                    "<td>" + (f.SECUENCIAIM || f.secuenciaim || "-") + "</td>" +
                    "<td>" + (f.NOMBRE || f.nombre || "-") + "</td>" +
                    "<td>" + formateaMoneda(monto) + "</td>" +
                    "<td>" + (f.CDGCB || f.cdgcb || "-") + "</td>" +
                    "<td>" + (f.CDGNS || f.cdgns || "-") + "</td>" +
                    "<td>" + (f.TASA != null ? f.TASA : (f.tasa || "-")) + "</td>" +
                    "<td>" + (f.SECUENCIA || f.secuencia || "-") + "</td>" +
                    "<td>" + (f.PLAZO != null ? f.PLAZO : (f.plazo || "-")) + "</td>" +
                    "<td>" + (f.PERIODICIDAD || f.periodicidad || "-") + "</td>" +
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
            const filtro = sel ? (sel.value || "todos") : "todos";
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

        const renderPagos = (filas, resumen, meta) => {
            cacheFilasAplicacion = Array.isArray(filas) ? filas : [];

            const totalReg = resumen.totalRegistros != null ? Number(resumen.totalRegistros) : cacheFilasAplicacion.length;
            const totalImp = resumen.totalImporte != null ? Number(resumen.totalImporte) : 0;
            const yaProcesado = !!meta.yaProcesado;
            const pendientes = resumen.totalPendientes != null ? Number(resumen.totalPendientes) : (yaProcesado ? 0 : totalReg);
            const aplicados = resumen.totalAplicados != null ? Number(resumen.totalAplicados) : (yaProcesado ? totalReg : 0);
            const impPend = resumen.importePendientes != null ? Number(resumen.importePendientes) : (yaProcesado ? 0 : totalImp);
            const impApl = resumen.importeAplicados != null ? Number(resumen.importeAplicados) : (yaProcesado ? totalImp : 0);

            const rp = resumen.registrosPagos != null ? Number(resumen.registrosPagos) : 0;
            const rg = resumen.registrosGarantias != null ? Number(resumen.registrosGarantias) : 0;
            const ri = resumen.registrosIncidencias != null ? Number(resumen.registrosIncidencias) : 0;
            const impPag = resumen.importePagos != null ? Number(resumen.importePagos) : 0;
            const impGar = resumen.importeGarantias != null ? Number(resumen.importeGarantias) : 0;
            const impInc = resumen.importeIncidencias != null ? Number(resumen.importeIncidencias) : 0;

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

            pintarSoloTablaAplicacion();
        };

        const renderConciliacion = (filas, resumen) => {
            cacheFilasConciliacion = Array.isArray(filas) ? filas : [];
            const pend = resumen.totalNoConciliados != null ? Number(resumen.totalNoConciliados) : 0;
            const conc = resumen.totalConciliados != null ? Number(resumen.totalConciliados) : 0;
            const impPend = resumen.importeNoConciliados != null ? Number(resumen.importeNoConciliados) : 0;
            const impConc = resumen.importeConciliados != null ? Number(resumen.importeConciliados) : 0;
            const elPend = document.getElementById("totalPagosConciliacion");
            const elImpPend = document.getElementById("importeTotalConciliacion");
            const elConc = document.getElementById("totalPagosConciliacionHechos");
            const elImpConc = document.getElementById("importeConciliacionHechos");
            if (elPend) elPend.textContent = pend;
            if (elImpPend) elImpPend.textContent = formateaMoneda(impPend);
            if (elConc) elConc.textContent = conc;
            if (elImpConc) elImpConc.textContent = formateaMoneda(impConc);
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
                    data: { fecha: fecha, codigo: "", ciclo: "", ctaBancaria: "", modoConciliado: "por_fecha" }
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

        const pintarTablaInfoDia = (tbodyId, filas, colFecha) => {
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;
            if (!filas || !filas.length) {
                tbody.innerHTML = '<tr><td colspan="2">Sin registros para este día.</td></tr>';
                return;
            }
            tbody.innerHTML = filas.map((r) => {
                const f = r[colFecha] != null ? r[colFecha] : (r[colFecha.toLowerCase()] != null ? r[colFecha.toLowerCase()] : "-");
                const c = r.CNT != null ? r.CNT : (r.cnt != null ? r.cnt : "0");
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
                pintarTablaInfoDia("tbodyInfoDiaPagosdia", d.pagosdia || [], "FECHA");
                pintarTablaInfoDia("tbodyInfoDiaTblCierre", d.tbl_cierre_dia || [], "FECHA_CALC");
                pintarTablaInfoDia("tbodyInfoDiaDevengo", d.devengo_diario || [], "FECHA_CALC");
                pintarTablaInfoDia("tbodyInfoDiaMpPd", d.mp_pd || [], "FDEPOSITO");
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
