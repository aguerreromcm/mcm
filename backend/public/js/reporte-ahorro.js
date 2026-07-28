/* Reporte de Ahorro - dashboard */
(function () {
    let chartMensual = null
    let chartSucursal = null
    let saldoTotalGlobal = 0

    const moneda = (n) => "$ " + formatoMoneda(n || 0)
    const num = (n) => parseFloat(n) || 0
    const pct = (parte, total) => (total > 0 ? (parte / total) * 100 : 0)

    const catalogo = window.RA_CATALOGO || { regiones: [], sucursales: [] }
    let detalleEjecutada = false
    let tipoContratoDetalle = "all"

    const getParams = () => {
        const datos = { fechaCorte: $("#fechaCorte").val() }
        const region = $("#region").val()
        const sucursal = $("#sucursal").val()
        if (region) datos.region = region
        if (sucursal) datos.sucursal = sucursal
        return datos
    }

    const getParamsDetalle = () => {
        const datos = { fechaCorte: $("#fFechaCorte").val() || $("#fechaCorte").val() }
        const region = $("#fRegion").val()
        const sucursal = $("#fSucursal").val()
        const ejecutivo = $("#fEjecutivo").val()
        if (region) datos.region = region
        if (sucursal) datos.sucursal = sucursal
        if (ejecutivo) datos.ejecutivo = ejecutivo
        if (tipoContratoDetalle && tipoContratoDetalle !== "all") datos.tipoContrato = tipoContratoDetalle
        return datos
    }

    const fillSelectRegion = (id, preserve) => {
        const el = document.getElementById(id)
        if (!el) return
        const cur = preserve ? el.value : ""
        el.innerHTML =
            '<option value="">Todas</option>' +
            (catalogo.regiones || [])
                .map((r) => '<option value="' + r.id + '">' + r.nombre + "</option>")
                .join("")
        if (cur) el.value = cur
    }

    const fillSelectSucursal = (idSelect, idRegion, preserve) => {
        const el = document.getElementById(idSelect)
        if (!el) return
        const region = $("#" + idRegion).val()
        const prev = preserve ? el.value : ""
        let items = catalogo.sucursales || []
        if (region) items = items.filter((s) => s.region === region)
        el.innerHTML =
            '<option value="">Todas</option>' +
            items.map((s) => '<option value="' + s.id + '">' + s.nombre + "</option>").join("")
        if (prev && items.some((s) => s.id === prev)) el.value = prev
        else el.value = ""
    }

    const fillRegiones = () => {
        fillSelectRegion("region", true)
        fillSelectRegion("fRegion", true)
    }

    const fillSucursales = (preserve) => {
        fillSelectSucursal("sucursal", "region", preserve)
    }

    const fillSucursalesDetalle = (preserve) => {
        fillSelectSucursal("fSucursal", "fRegion", preserve)
    }

    const etiquetaEjecutivo = (codigo, nombre) => {
        const cod = (codigo || "").toString().trim()
        const nom = (nombre || "").toString().trim()
        if (cod && nom && cod !== nom) return cod + " (" + nom + ")"
        return nom || cod || ""
    }

    const fillEjecutivosDetalle = (lista, preserve) => {
        const el = document.getElementById("fEjecutivo")
        if (!el) return
        const prev = preserve ? el.value : ""
        el.innerHTML =
            '<option value="">Todos</option>' +
            (lista || [])
                .map((e) => {
                    const id = e.ID || e.id || ""
                    const nom = e.NOMBRE || e.nombre || id
                    return '<option value="' + id + '">' + etiquetaEjecutivo(id, nom) + "</option>"
                })
                .join("")
        if (prev && (lista || []).some((e) => String(e.ID || e.id) === String(prev))) el.value = prev
        else if (!preserve) el.value = ""
        else if (prev && !(lista || []).some((e) => String(e.ID || e.id) === String(prev))) el.value = ""
    }

    const ajaxSilencioso = (url, datos, fncOK) => {
        $.ajax({
            type: "POST",
            url: url,
            data: datos,
            dataType: "json",
            success: (res) => {
                if (typeof res === "string") {
                    try {
                        res = JSON.parse(res)
                    } catch (e) {
                        res = { success: false }
                    }
                }
                fncOK(res)
            },
            error: () => fncOK({ success: false })
        })
    }

    const cargarEjecutivosDetalle = (preserve) => {
        const fechaCorte = $("#fFechaCorte").val() || $("#fechaCorte").val()
        if (!fechaCorte) {
            fillEjecutivosDetalle([], false)
            return
        }
        const params = { fechaCorte }
        const region = $("#fRegion").val()
        const sucursal = $("#fSucursal").val()
        if (region) params.region = region
        if (sucursal) params.sucursal = sucursal

        // Sin modal: no debe interferir con la carga del dashboard
        ajaxSilencioso("/AdminSucursales/GetEjecutivosReporteAhorro/", params, (resultado) => {
            if (!resultado.success) {
                fillEjecutivosDetalle([], false)
                return
            }
            fillEjecutivosDetalle(resultado.datos || [], !!preserve)
        })
    }

    const onRegionDetalleChange = () => {
        fillSucursalesDetalle(false)
        cargarEjecutivosDetalle(false)
    }

    const onSucursalDetalleChange = () => {
        cargarEjecutivosDetalle(false)
    }

    const setTexto = (id, valor) => {
        const el = document.getElementById(id)
        if (el) el.textContent = valor
    }

    const setLoading = (on) => {
        $(".reporte-ahorro-page").toggleClass("is-loading", !!on)
    }

    const DT_ES = {
        emptyTable: "No hay datos disponibles",
        paginate: { previous: "Anterior", next: "Siguiente" },
        info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
        infoFiltered: "(filtrado de un total de _MAX_ registros)",
        zeroRecords: "No se encontraron resultados",
        lengthMenu: "Mostrar _MENU_ registros",
        search: "Buscar:",
        loadingRecords: "Cargando...",
        processing: "Procesando..."
    }

    const actualizaResumen = (r) => {
        const sinContrato = parseInt(r.SIN_CONTRATO || 0, 10)
        setTexto("kpiContratos", r.CONTRATOS || 0)
        setTexto("kpiSinContrato", sinContrato)
        setTexto("kpiClientes", r.CLIENTES || 0)
        setTexto("kpiSaldo", moneda(r.SALDO_ACTUAL))
        setTexto("kpiAbonos", moneda(r.ABONOS))
        setTexto("kpiRetiros", moneda(r.RETIROS))
        setTexto("kpiTasa", "Tasa ponderada " + num(r.TASA_PROMEDIO).toFixed(2) + "%")
        setTexto("kpiTransito", moneda(r.TRANSITO))
        saldoTotalGlobal = num(r.SALDO_ACTUAL)
    }

    const llenaTabla = (id, filasHtml, simple = false) => {
        if ($.fn.DataTable.isDataTable("#" + id)) {
            $("#" + id).DataTable().clear().destroy()
        }
        $("#" + id + " tbody").html(filasHtml)
        const opts = {
            language: DT_ES,
            ordering: true,
            order: []
        }
        if (simple) {
            opts.paging = false
            opts.searching = false
            opts.info = false
            opts.lengthChange = false
            opts.dom = "t"
        } else {
            opts.pageLength = 10
            opts.dom = '<"row"<"col-sm-6"l><"col-sm-6"f>>t<"row"<"col-sm-6"i><"col-sm-6"p>>'
        }
        $("#" + id).DataTable(opts)
    }

    const escHtml = (s) =>
        String(s == null ? "" : s)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")

    const celdaCodigoNombre = (codigo, nombre) => {
        const cod = escHtml(codigo)
        const nom = escHtml(nombre)
        return (
            '<td class="cell-user" data-order="' +
            (nom || cod) +
            '"><div><code>' +
            (cod || "—") +
            '</code></div><div class="cell-sub">' +
            (nom || "") +
            "</div></td>"
        )
    }

    const celdaCredito = (credito) =>
        '<td data-order="' +
        escHtml(credito) +
        '"><div class="cell-credito cell-main">' +
        escHtml(credito || "—") +
        "</div></td>"

    const celdaSucursal = (codigo, nombre) => {
        const cod = escHtml(codigo)
        const nom = escHtml(nombre)
        return (
            '<td data-order="' +
            (nom || cod) +
            '"><div class="cell-main">' +
            (nom || cod || "—") +
            "</div>" +
            (cod && nom && cod !== nom ? '<div class="cell-sub">' + cod + "</div>" : "") +
            "</td>"
        )
    }

    const celdaAperturaTasa = (apertura, tasa) => {
        const ap = escHtml(apertura || "—")
        const t = tasa != null && tasa !== "" ? num(tasa).toFixed(2) + "%" : ""
        return (
            '<td data-order="' +
            ap +
            '"><div class="cell-main">' +
            ap +
            "</div>" +
            (t ? '<div class="cell-sub">Tasa ' + t + "</div>" : "") +
            "</td>"
        )
    }

    const celdaRank = (i) =>
        '<td class="ra-rank" data-order="' + (i + 1) + '"><span class="ra-rank__n">' + (i + 1) + "</span></td>"

    const celdaMoneda = (valor, opts) => {
        const v = num(valor)
        const strong = opts && opts.strong
        const inner = strong ? "<strong>" + moneda(v) + "</strong>" : moneda(v)
        return '<td class="cell-monto" data-order="' + v + '">' + inner + "</td>"
    }

    const celdaPct = (valor, total) => {
        const p = pct(num(valor), total)
        return '<td class="cell-pct" data-order="' + p + '">' + p.toFixed(1) + "%</td>"
    }

    const tablaSucursales = (datos) => {
        const empty = !datos || datos.length === 0
        $("#emptyTblSuc").toggle(empty)
        $("#tblSucursales").toggle(!empty)
        if (empty) {
            if ($.fn.DataTable.isDataTable("#tblSucursales")) $("#tblSucursales").DataTable().destroy()
            $("#tblSucursales tbody").html("")
            return
        }
        let html = ""
        datos.forEach((d) => {
            const suc = (d.SUCURSAL || "").toString().replace(/"/g, "&quot;")
            html +=
                '<tr class="ra-row-click" data-drill="sucursal" data-sucursal="' +
                suc +
                '" title="Ver detalle de sucursal">' +
                '<td class="cell-credito" data-order="' +
                escHtml(d.SUCURSAL) +
                '">' +
                escHtml(d.SUCURSAL || "") +
                "</td>" +
                '<td data-order="' +
                escHtml(d.SUCURSAL_NOMBRE || d.SUCURSAL) +
                '"><div class="cell-main">' +
                escHtml(d.SUCURSAL_NOMBRE || d.SUCURSAL || "") +
                "</div></td>" +
                '<td class="cell-num" data-order="' +
                num(d.CONTRATOS) +
                '">' +
                (d.CONTRATOS || 0) +
                "</td>" +
                celdaMoneda(d.ABONOS) +
                celdaMoneda(d.AJUSTES) +
                celdaMoneda(d.RETIROS) +
                celdaMoneda(d.SALDO_ACTUAL, { strong: true }) +
                celdaPct(d.SALDO_ACTUAL, saldoTotalGlobal) +
                "</tr>"
        })
        llenaTabla("tblSucursales", html)
    }

    const tablaTop = (id, emptyId, datos, tipo) => {
        const empty = !datos || datos.length === 0
        $("#" + emptyId).toggle(empty)
        $("#" + id).toggle(!empty)
        if (empty) {
            if ($.fn.DataTable.isDataTable("#" + id)) $("#" + id).DataTable().destroy()
            $("#" + id + " tbody").html("")
            return
        }
        let html = ""
        datos.forEach((d, i) => {
            if (tipo === "ejecutivo") {
                const cod = (d.EJECUTIVO || "").toString().replace(/"/g, "&quot;")
                const nom = (d.EJECUTIVO_NOMBRE || d.EJECUTIVO || "").toString().replace(/"/g, "&quot;")
                html +=
                    '<tr class="ra-row-click" data-drill="ejecutivo" data-codigo="' +
                    cod +
                    '" data-nombre="' +
                    nom +
                    '" title="Ver cuentas del ejecutivo">' +
                    celdaRank(i) +
                    celdaCodigoNombre(d.EJECUTIVO, d.EJECUTIVO_NOMBRE) +
                    '<td class="cell-num" data-order="' +
                    num(d.CONTRATOS) +
                    '">' +
                    (d.CONTRATOS || 0) +
                    "</td>" +
                    celdaMoneda(d.SALDO_ACTUAL, { strong: true }) +
                    celdaPct(d.SALDO_ACTUAL, saldoTotalGlobal) +
                    "</tr>"
            } else {
                html +=
                    "<tr>" +
                    celdaRank(i) +
                    celdaCodigoNombre(d.CLIENTE, d.CLIENTE_NOMBRE) +
                    celdaSucursal(d.SUCURSAL, d.SUCURSAL_NOMBRE) +
                    celdaMoneda(d.SALDO_ACTUAL, { strong: true }) +
                    celdaPct(d.SALDO_ACTUAL, saldoTotalGlobal) +
                    "</tr>"
            }
        })
        llenaTabla(id, html, true)
    }

    const pluginValoresBarra = {
        id: "valoresBarra",
        afterDatasetsDraw(chart) {
            const { ctx } = chart
            const meta = chart.getDatasetMeta(0)
            if (!meta || meta.hidden) return
            ctx.save()
            ctx.font = "11px Segoe UI, sans-serif"
            ctx.fillStyle = "#4b5563"
            ctx.textAlign = "left"
            ctx.textBaseline = "middle"
            meta.data.forEach((bar, i) => {
                const val = chart.data.datasets[0].data[i]
                if (val == null) return
                const pos = bar.tooltipPosition()
                ctx.fillText(moneda(val), pos.x + 6, pos.y)
            })
            ctx.restore()
        }
    }

    const colorPorMagnitud = (valores) => {
        const max = Math.max(...valores.map(num), 1)
        return valores.map((v) => {
            const t = num(v) / max
            const r = Math.round(42 + (26 - 42) * t)
            const g = Math.round(120 + (175 - 120) * t)
            const b = Math.round(214 + (122 - 214) * t)
            return "rgb(" + r + "," + g + "," + b + ")"
        })
    }

    const formatMesCorto = (periodo) => {
        const p = (periodo || "").toString()
        const m = p.match(/^(\d{4})-(\d{2})$/)
        if (!m) return p
        const d = new Date(parseInt(m[1], 10), parseInt(m[2], 10) - 1, 1)
        const abbr = d.toLocaleString("es-MX", { month: "short" }).replace(".", "")
        const label = abbr.charAt(0).toUpperCase() + abbr.slice(1).toLowerCase()
        return label + " '" + String(m[1]).slice(-2)
    }

    const formatMesLargo = (periodo) => {
        const p = (periodo || "").toString()
        const m = p.match(/^(\d{4})-(\d{2})$/)
        if (!m) return p
        const d = new Date(parseInt(m[1], 10), parseInt(m[2], 10) - 1, 1)
        const mes = d.toLocaleString("es-MX", { month: "long" })
        const label = mes.charAt(0).toUpperCase() + mes.slice(1).toLowerCase()
        return label + " " + m[1]
    }

    /** Rellena los últimos 12 meses hasta fechaCorte (incluye meses en cero). */
    const completarMeses = (datos, fechaCorte) => {
        const map = {}
        ;(datos || []).forEach((d) => {
            const key = (d.PERIODO || "").toString()
            if (!key) return
            map[key] = {
                PERIODO: key,
                ABONOS: num(d.ABONOS),
                RETIROS: num(d.RETIROS)
            }
        })
        const fin = fechaCorte ? new Date(fechaCorte + "T00:00:00") : new Date()
        if (isNaN(fin.getTime())) return datos || []
        const out = []
        for (let i = 11; i >= 0; i--) {
            const d = new Date(fin.getFullYear(), fin.getMonth() - i, 1)
            const key =
                d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0")
            out.push(map[key] || { PERIODO: key, ABONOS: 0, RETIROS: 0 })
        }
        return out
    }

    const graficoMensual = (datos) => {
        const serie = completarMeses(datos, $("#fechaCorte").val())
        const empty = !serie.length
        $("#emptyMensual").toggle(empty)
        $("#chrtMensual").toggle(!empty)
        if (chartMensual) {
            chartMensual.destroy()
            chartMensual = null
        }
        if (empty) return

        const labels = serie.map((d) => formatMesCorto(d.PERIODO))
        const titles = serie.map((d) => formatMesLargo(d.PERIODO))

        chartMensual = new Chart(document.getElementById("chrtMensual"), {
            type: "line",
            data: {
                labels,
                datasets: [
                    {
                        label: "Abonos",
                        data: serie.map((d) => num(d.ABONOS)),
                        borderColor: "#1baf7a",
                        backgroundColor: "rgba(27,175,122,0.12)",
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: true,
                        tension: 0.35
                    },
                    {
                        label: "Retiros",
                        data: serie.map((d) => num(d.RETIROS)),
                        borderColor: "#c0392b",
                        backgroundColor: "rgba(192,57,43,0.10)",
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: true,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 4, right: 8, bottom: 0, left: 4 } },
                interaction: { mode: "index", intersect: false },
                plugins: {
                    legend: {
                        position: "top",
                        align: "end",
                        labels: { boxWidth: 10, usePointStyle: true, pointStyle: "circle", padding: 12 }
                    },
                    tooltip: {
                        callbacks: {
                            title: (items) => {
                                const i = items && items[0] ? items[0].dataIndex : -1
                                return i >= 0 ? titles[i] : ""
                            },
                            label: (ctx) => {
                                const label = ctx.dataset.label || ""
                                return " " + label + ": " + moneda(ctx.parsed.y)
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: {
                            color: "#898781",
                            font: { size: 10 },
                            maxRotation: 0,
                            minRotation: 0,
                            autoSkip: false
                        }
                    },
                    y: {
                        border: { display: false },
                        beginAtZero: true,
                        grid: { color: "rgba(0,0,0,0.06)", drawTicks: false },
                        ticks: {
                            color: "#898781",
                            font: { size: 10 },
                            padding: 6,
                            callback: (v) => formatoMoneda(v)
                        }
                    }
                }
            }
        })
    }

    const acorta = (txt, max = 18) => {
        const t = (txt || "N/D").toString()
        return t.length > max ? t.slice(0, max - 1) + "…" : t
    }

    const graficoSucursal = (datos) => {
        const top = (datos || []).slice(0, 10)
        const empty = top.length === 0
        $("#emptySucursal").toggle(empty)
        $("#chrtSucursal").toggle(!empty)
        if (chartSucursal) {
            chartSucursal.destroy()
            chartSucursal = null
        }
        if (empty) return

        const labels = top.map((d) => acorta(d.SUCURSAL_NOMBRE || d.SUCURSAL))
        const saldos = top.map((d) => num(d.SALDO_ACTUAL))

        chartSucursal = new Chart(document.getElementById("chrtSucursal"), {
            type: "bar",
            data: {
                labels,
                datasets: [
                    {
                        label: "Saldo actual",
                        data: saldos,
                        backgroundColor: colorPorMagnitud(saldos),
                        borderRadius: 4,
                        maxBarThickness: 28
                    }
                ]
            },
            options: {
                indexAxis: "y",
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { right: 90 } },
                onClick: (_evt, elements) => {
                    if (!elements || !elements.length) return
                    const i = elements[0].index
                    const row = top[i]
                    if (row && row.SUCURSAL) irConsulta({ sucursal: row.SUCURSAL })
                },
                onHover: (evt, elements) => {
                    const el = (evt.native && evt.native.target) || (evt.chart && evt.chart.canvas)
                    if (el) el.style.cursor = elements && elements.length ? "pointer" : "default"
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: (items) => {
                                const i = items[0].dataIndex
                                return top[i].SUCURSAL_NOMBRE || top[i].SUCURSAL || ""
                            },
                            label: (ctx) => " Saldo: " + moneda(ctx.parsed.x)
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: "rgba(0,0,0,0.05)" },
                        beginAtZero: true,
                        ticks: { callback: (v) => formatoMoneda(v) }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            },
            plugins: [pluginValoresBarra]
        })
    }

    let lastDrill = null
    let chartEjeConteo = null
    let chartEjeSaldo = null

    const formateaFecha = (iso) => {
        if (!iso) return ""
        const p = iso.split("-")
        if (p.length !== 3) return iso
        return p[2] + "/" + p[1] + "/" + p[0]
    }

    const isDarkMode = () => window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches

    const setTipoContratoUI = (tipo) => {
        tipoContratoDetalle = tipo || "all"
        $("#modFilters .mod-btn").removeClass("active")
        $('#modFilters .mod-btn[data-tipo="' + tipoContratoDetalle + '"]').addClass("active")
    }

    const irConsulta = (filtros) => {
        filtros = filtros || {}
        switchTab("detalle")
        $("#fFechaCorte").val($("#fechaCorte").val())

        const regionResumen = $("#region").val() || ""
        const region = filtros.region != null ? filtros.region : regionResumen
        $("#fRegion").val(region || "")
        fillSucursalesDetalle(false)

        if (filtros.sucursal) {
            $("#fSucursal").val(filtros.sucursal)
        } else if (Object.prototype.hasOwnProperty.call(filtros, "sucursal")) {
            $("#fSucursal").val("")
        } else if ($("#sucursal").val()) {
            $("#fSucursal").val($("#sucursal").val())
        } else {
            $("#fSucursal").val("")
        }

        setTipoContratoUI(filtros.tipoContrato || "all")

        const applyEjecutivoAndSearch = () => {
            if (filtros.ejecutivo) $("#fEjecutivo").val(String(filtros.ejecutivo))
            else $("#fEjecutivo").val("")
            cargarDetalle()
        }

        const fechaCorte = $("#fFechaCorte").val()
        const params = { fechaCorte }
        if ($("#fRegion").val()) params.region = $("#fRegion").val()
        if ($("#fSucursal").val()) params.sucursal = $("#fSucursal").val()

        ajaxSilencioso("/AdminSucursales/GetEjecutivosReporteAhorro/", params, (resultado) => {
            fillEjecutivosDetalle(resultado.success ? resultado.datos || [] : [], false)
            applyEjecutivoAndSearch()
        })
    }

    const destroyModalEjecutivoTabla = () => {
        if ($.fn.DataTable.isDataTable("#tblEjecutivoAhorro")) {
            $("#tblEjecutivoAhorro").DataTable().destroy()
        }
        $("#tblEjecutivoAhorro tbody").empty()
    }

    const modalChartOpts = (title) => {
        const dark = isDarkMode()
        const tick = dark ? "#8b949e" : "#738091"
        const grid = dark ? "rgba(255,255,255,0.06)" : "rgba(0,0,0,0.07)"
        const titleColor = dark ? "#e6edf3" : "#2b2b2b"
        return {
            responsive: true,
            interaction: { mode: "nearest", intersect: false },
            plugins: {
                title: { display: true, text: title, color: titleColor, font: { size: 13, weight: "600" } },
                legend: { display: false }
            },
            scales: {
                x: {
                    ticks: {
                        color: tick,
                        maxRotation: 45,
                        minRotation: 0,
                        autoSkip: false,
                        font: { size: 10 }
                    },
                    grid: { color: grid }
                },
                y: { ticks: { color: tick }, grid: { color: grid }, beginAtZero: true }
            }
        }
    }

    const initModalEjeChart = (canvasId, key, title, labels, data, border, bg) => {
        const ctx = document.getElementById(canvasId)
        if (!ctx) return
        const ds = { label: title, data, borderColor: border, backgroundColor: bg, borderWidth: 2, borderRadius: 5 }
        const opts = modalChartOpts(title)
        if (key === "conteo" && chartEjeConteo) {
            chartEjeConteo.data.labels = labels
            chartEjeConteo.data.datasets = [ds]
            chartEjeConteo.options = opts
            chartEjeConteo.update()
            return
        }
        if (key === "saldo" && chartEjeSaldo) {
            chartEjeSaldo.data.labels = labels
            chartEjeSaldo.data.datasets = [ds]
            chartEjeSaldo.options = opts
            chartEjeSaldo.update()
            return
        }
        const chart = new Chart(ctx, { type: "bar", data: { labels, datasets: [ds] }, options: opts })
        if (key === "conteo") chartEjeConteo = chart
        else chartEjeSaldo = chart
    }

    const parseAperturaDia = (apertura) => {
        const s = (apertura || "").toString().trim()
        const m = s.match(/^(\d{2})\/(\d{2})\/(\d{4})$/)
        if (!m) return null
        return m[3] + "-" + m[2] + "-" + m[1]
    }

    const labelDiaCorto = (iso) => {
        const p = (iso || "").split("-")
        if (p.length !== 3) return iso
        return p[2] + "/" + p[1] + "/" + p[0]
    }

    const actualizaModalEjecutivoCharts = (filas) => {
        const map = {}
        ;(filas || []).forEach((f) => {
            const dia = parseAperturaDia(f.APERTURA)
            if (!dia) return
            if (!map[dia]) map[dia] = { n: 0, monto: 0 }
            map[dia].n += 1
            map[dia].monto += num(f.SALDO_ACTUAL)
        })
        const dias = Object.keys(map).sort()
        const labels = dias.map(labelDiaCorto)
        const conteo = dias.map((d) => map[d].n)
        const montos = dias.map((d) => map[d].monto)
        initModalEjeChart("chrtEjeConteo", "conteo", "Registros", labels, conteo, "#1baf7a", "rgba(27,175,122,0.35)")
        initModalEjeChart("chrtEjeSaldo", "saldo", "Monto", labels, montos, "#eda100", "rgba(237,161,0,0.35)")
    }

    const actualizaModalEjecutivoTabla = (filas) => {
        destroyModalEjecutivoTabla()
        let html = ""
        ;(filas || []).forEach((d) => {
            html +=
                "<tr>" +
                celdaCredito(d.CREDITO) +
                celdaCodigoNombre(d.CLIENTE, d.CLIENTE_NOMBRE) +
                celdaSucursal(d.SUCURSAL, d.SUCURSAL_NOMBRE) +
                celdaAperturaTasa(d.APERTURA, d.TASA) +
                celdaMoneda(d.ABONOS) +
                celdaMoneda(d.RETIROS) +
                celdaMoneda(d.SALDO_ACTUAL, { strong: true }) +
                celdaMoneda(d.TRANSITO) +
                "</tr>"
        })
        $("#tblEjecutivoAhorro tbody").html(html)
        $("#tblEjecutivoAhorro").DataTable({
            language: DT_ES,
            pageLength: 10,
            order: [[6, "desc"]]
        })
    }

    const abrirModalEjecutivo = (codigo, nombre) => {
        const fechaCorte = $("#fechaCorte").val()
        const fechaTxt = formateaFecha(fechaCorte)
        const titulo =
            "Cuentas de ahorro de <b>" +
            (nombre || codigo) +
            "</b> al corte del <b>" +
            fechaTxt +
            "</b>"
        $("#ttlEjecutivoAhorro").html(titulo)
        $("#xsl_ejecutivo").val(codigo)
        $("#xsl_fechaCorte").val(fechaCorte)
        destroyModalEjecutivoTabla()
        $("#detalleEjecutivoAhorro").modal("show")

        const params = { fechaCorte, ejecutivo: codigo }
        const region = $("#region").val()
        const sucursalToolbar = $("#sucursal").val()
        if (sucursalToolbar) params.sucursal = sucursalToolbar
        else if (region) params.region = region

        consultaServidor("/AdminSucursales/GetConsultaDetalleReporteAhorro/", params, (resultado) => {
            let errorMsg = null
            try {
                if (!resultado.success) {
                    errorMsg = resultado.mensaje || "No se pudo cargar el detalle."
                    return
                }
                const filas = (resultado.datos && resultado.datos.filas) || []
                actualizaModalEjecutivoCharts(filas)
                actualizaModalEjecutivoTabla(filas)
            } catch (e) {
                console.error(e)
                errorMsg = "Error al actualizar el detalle del ejecutivo."
            } finally {
                setLoading(false)
            }
            if (errorMsg) showError(errorMsg)
        })
    }

    const abrirModalDrill = (opts) => {
        opts = opts || {}
        lastDrill = opts
        const titulo = opts.titulo || "Detalle"
        $("#modalDrillTitulo").text(titulo)
        setTexto("modalDrillCount", "…")
        setTexto("modalDrillSaldo", "…")

        const params = {
            fechaCorte: $("#fechaCorte").val()
        }
        const region = $("#region").val()
        const sucursalToolbar = $("#sucursal").val()
        if (opts.sucursal) params.sucursal = opts.sucursal
        else if (sucursalToolbar) params.sucursal = sucursalToolbar
        else if (region) params.region = region
        if (opts.ejecutivo) params.ejecutivo = opts.ejecutivo
        if (opts.cliente) params.cliente = opts.cliente
        if (opts.tipoContrato && opts.tipoContrato !== "all") params.tipoContrato = opts.tipoContrato

        consultaServidor("/AdminSucursales/GetConsultaDetalleReporteAhorro/", params, (resultado) => {
            let errorMsg = null
            try {
                if (!resultado.success) {
                    errorMsg = resultado.mensaje || "No se pudo cargar el detalle."
                    return
                }
                const datos = resultado.datos || {}
                const filas = datos.filas || []
                const t = datos.totales || {}
                setTexto("modalDrillCount", t.REGISTROS || filas.length || 0)
                setTexto("modalDrillSaldo", moneda(t.SALDO_TOTAL))

                if ($.fn.DataTable.isDataTable("#tblDrillAhorro")) $("#tblDrillAhorro").DataTable().destroy()
                let html = ""
                filas.forEach((d) => {
                    html +=
                        "<tr>" +
                        celdaCredito(d.CREDITO) +
                        celdaCodigoNombre(d.CLIENTE, d.CLIENTE_NOMBRE) +
                        celdaSucursal(d.SUCURSAL, d.SUCURSAL_NOMBRE) +
                        celdaAperturaTasa(d.APERTURA, d.TASA) +
                        celdaMoneda(d.ABONOS) +
                        celdaMoneda(d.SALDO_ACTUAL, { strong: true }) +
                        "</tr>"
                })
                $("#tblDrillAhorro tbody").html(html || '<tr><td colspan="6" class="text-center">Sin registros</td></tr>')
                if (filas.length) {
                    $("#tblDrillAhorro").DataTable({
                        language: DT_ES,
                        pageLength: 10,
                        order: [[5, "desc"]]
                    })
                }
                $("#modalDrillAhorro").modal("show")
            } catch (e) {
                console.error(e)
                errorMsg = "Error al actualizar el detalle."
            } finally {
                setLoading(false)
            }
            if (errorMsg) showError(errorMsg)
        })
    }

    const cargarDashboard = () => {
        const fechaCorte = $("#fechaCorte").val()
        if (!fechaCorte) return showError("Seleccione la fecha de corte.")

        setLoading(true)
        consultaServidor("/AdminSucursales/GetDashboardReporteAhorro/", getParams(), (resultado) => {
            let errorMsg = null
            try {
                if (!resultado.success) {
                    errorMsg = resultado.mensaje || "No se pudo cargar el dashboard."
                    return
                }
                const d = resultado.datos || {}
                actualizaResumen(d.resumen || {})
                tablaSucursales(d.porSucursal || [])
                tablaTop("tblEjecutivos", "emptyTblEje", d.topEjecutivos || [], "ejecutivo")
                tablaTop("tblClientes", "emptyTblCli", d.topClientes || [], "cliente")
                graficoMensual(d.mensual || [])
                graficoSucursal(d.porSucursal || [])
            } catch (e) {
                console.error(e)
                errorMsg = "Error al actualizar la vista del reporte."
            } finally {
                setLoading(false)
            }
            if (errorMsg) showError(errorMsg)
        })
        $(document).one("ajaxError", (_e, _xhr, settings) => {
            if (settings && settings.url && settings.url.indexOf("GetDashboardReporteAhorro") !== -1) {
                setLoading(false)
            }
        })
    }

    const descargarExcel = () => {
        const paramsObj = $("#tab-detalle").hasClass("active") ? getParamsDetalle() : getParams()
        if (!paramsObj.fechaCorte) return showError("Seleccione la fecha de corte.")
        const params = new URLSearchParams(paramsObj).toString()
        descargaExcel("/AdminSucursales/excelReporteAhorro/?" + params)
    }

    const destroyDetalleTabla = () => {
        if ($.fn.DataTable.isDataTable("#tblDetalle")) {
            $("#tblDetalle").DataTable().destroy()
        }
        $("#tblDetalle tbody").empty()
        $("#tblDetalle").css("width", "")
    }

    const showDetalleEstado = (modo) => {
        if (modo === "idle") detalleEjecutada = false
        destroyDetalleTabla()
        $("#tblDetalle").hide()
        if (modo === "idle") {
            setTexto("resCount", "0")
            setTexto("resSaldo", "$0")
            setTexto("resProm", "$0")
        }
        const empty = document.getElementById("emptyStateDetalle")
        if (!empty) return
        empty.style.display = "block"
        if (modo === "idle") {
            empty.innerHTML =
                '<i class="fa fa-search"></i>' +
                "<p><strong>Consulta no ejecutada</strong></p>" +
                '<p style="font-size:13px">Ajusta los filtros y pulsa <strong>Buscar</strong>.</p>'
        } else {
            empty.innerHTML =
                '<i class="fa fa-inbox"></i>' +
                "<p><strong>Sin resultados</strong></p>" +
                '<p style="font-size:13px">Prueba ajustando los filtros.</p>'
        }
    }

    const renderDetalle = (datos) => {
        const filas = datos.filas || []
        const t = datos.totales || {}
        setTexto("resCount", t.REGISTROS || filas.length || 0)
        setTexto("resSaldo", moneda(t.SALDO_TOTAL))
        setTexto("resProm", moneda(t.PROMEDIO))

        if (!filas.length) {
            showDetalleEstado("vacio")
            return
        }

        destroyDetalleTabla()
        let html = ""
        filas.forEach((d) => {
            html +=
                "<tr>" +
                celdaCredito(d.CREDITO) +
                celdaCodigoNombre(d.CLIENTE, d.CLIENTE_NOMBRE) +
                celdaSucursal(d.SUCURSAL, d.SUCURSAL_NOMBRE) +
                celdaCodigoNombre(d.EJECUTIVO, d.EJECUTIVO_NOMBRE) +
                celdaAperturaTasa(d.APERTURA, d.TASA) +
                celdaMoneda(d.ABONOS) +
                celdaMoneda(d.RETIROS) +
                celdaMoneda(d.SALDO_ACTUAL, { strong: true }) +
                celdaMoneda(d.TRANSITO) +
                "</tr>"
        })
        $("#emptyStateDetalle").hide()
        $("#tblDetalle tbody").html(html)
        $("#tblDetalle").show()
        $("#tblDetalle").DataTable({
            language: DT_ES,
            pageLength: 25,
            order: [[7, "desc"]],
            autoWidth: false,
            orderClasses: false
        })
        if ($.fn.DataTable.isDataTable("#tblDetalle")) {
            $("#tblDetalle").DataTable().columns.adjust()
        }
        detalleEjecutada = true
    }

    const cargarDetalle = () => {
        const fechaCorte = $("#fFechaCorte").val()
        if (!fechaCorte) return showError("Seleccione la fecha de corte.")

        consultaServidor("/AdminSucursales/GetConsultaDetalleReporteAhorro/", getParamsDetalle(), (resultado) => {
            let errorMsg = null
            try {
                if (!resultado.success) {
                    errorMsg = resultado.mensaje || "No se pudo cargar la consulta."
                    return
                }
                renderDetalle(resultado.datos || {})
            } catch (e) {
                console.error(e)
                errorMsg = "Error al actualizar la consulta detallada."
            } finally {
                setLoading(false)
            }
            if (errorMsg) showError(errorMsg)
        })
    }

    const limpiarDetalle = (e) => {
        if (e) e.preventDefault()
        $("#fFechaCorte").val($("#fechaCorte").val())
        $("#fRegion").val("")
        fillSucursalesDetalle(false)
        $("#fEjecutivo").val("")
        tipoContratoDetalle = "all"
        $("#modFilters .mod-btn").removeClass("active")
        $('#modFilters .mod-btn[data-tipo="all"]').addClass("active")
        cargarEjecutivosDetalle(false)
        showDetalleEstado("idle")
    }

    const switchTab = (tab) => {
        $(".tab-link").each(function () {
            $(this).toggleClass("active", $(this).data("tab") === tab)
        })
        $(".tab-pane").each(function () {
            $(this).toggleClass("active", this.id === "tab-" + tab)
        })
        $(".reporte-ahorro-page").toggleClass("is-detalle", tab === "detalle")
        if (tab === "detalle") {
            if (!$("#fFechaCorte").val()) $("#fFechaCorte").val($("#fechaCorte").val())
            if (!$("#fRegion").val() && $("#region").val()) {
                $("#fRegion").val($("#region").val())
                fillSucursalesDetalle(false)
                if ($("#sucursal").val()) $("#fSucursal").val($("#sucursal").val())
            }
            cargarEjecutivosDetalle(true)
        }
    }

    $(document).ready(() => {
        fillRegiones()
        fillSucursales(false)
        fillSucursalesDetalle(false)
        cargarEjecutivosDetalle(false)
        showDetalleEstado("idle")

        $("#btnBuscar").on("click", cargarDashboard)
        $("#btnBuscarDetalle").on("click", cargarDetalle)
        $("#btnExcelDetalle").on("click", descargarExcel)
        $("#btnLimpiarDetalle").on("click", limpiarDetalle)
        $("#region").on("change", () => fillSucursales(false))
        $("#fRegion").on("change", onRegionDetalleChange)
        $("#fSucursal").on("change", onSucursalDetalleChange)
        $("#fFechaCorte").on("change", () => cargarEjecutivosDetalle(true))

        $(document).on("click", ".tab-link", function (e) {
            e.preventDefault()
            switchTab($(this).data("tab"))
        })

        $(document).on("click", ".kpi--click[data-ir-tipo]", function (e) {
            e.preventDefault()
            irConsulta({ tipoContrato: $(this).data("ir-tipo") || "all" })
        })

        $("#kpiSinBox").on("click", function () {
            const cant = parseInt($("#kpiSinContrato").text(), 10) || 0
            if (cant <= 0) return showError("No hay casos sin contrato en el corte seleccionado.")
            abrirModalDrill({
                titulo: "Casos con abonos sin contrato",
                tipoContrato: "sin"
            })
        })

        $(document).on("click", "#tblSucursales tbody tr.ra-row-click", function () {
            const suc = $(this).data("sucursal")
            if (suc) irConsulta({ sucursal: String(suc) })
        })

        $(document).on("click", "#tblEjecutivos tbody tr.ra-row-click", function () {
            const codigo = $(this).data("codigo")
            const nombre = $(this).data("nombre") || codigo
            if (!codigo) return
            abrirModalEjecutivo(String(codigo), nombre)
        })

        $("#btnExcelEjecutivo").on("click", function () {
            const ejecutivo = $("#xsl_ejecutivo").val()
            const fechaCorte = $("#xsl_fechaCorte").val() || $("#fechaCorte").val()
            if (!fechaCorte) return showError("Seleccione la fecha de corte.")
            const params = { fechaCorte }
            if (ejecutivo) params.ejecutivo = ejecutivo
            const region = $("#region").val()
            const sucursal = $("#sucursal").val()
            if (sucursal) params.sucursal = sucursal
            else if (region) params.region = region
            descargaExcel("/AdminSucursales/excelReporteAhorro/?" + new URLSearchParams(params).toString())
        })

        $("#detalleEjecutivoAhorro").on("hidden.bs.modal", function () {
            destroyModalEjecutivoTabla()
            if (chartEjeConteo) {
                chartEjeConteo.destroy()
                chartEjeConteo = null
            }
            if (chartEjeSaldo) {
                chartEjeSaldo.destroy()
                chartEjeSaldo = null
            }
        })

        $("#btnDrillIrConsulta").on("click", function () {
            $("#modalDrillAhorro").modal("hide")
            const f = lastDrill || {}
            irConsulta({
                sucursal: f.sucursal || undefined,
                tipoContrato: f.tipoContrato || "all"
            })
        })

        $("#modFilters").on("click", ".mod-btn", function () {
            $("#modFilters .mod-btn").removeClass("active")
            $(this).addClass("active")
            tipoContratoDetalle = $(this).data("tipo") || "all"
            if (detalleEjecutada) cargarDetalle()
        })

        cargarDashboard()
    })
})()
