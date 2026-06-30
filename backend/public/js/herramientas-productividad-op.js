(function () {
    'use strict';

    const API = '/Herramientas/';
    const MOD_LABELS = { pagos: 'PAGOS DÍA', ajuste: 'AJUSTE', gar: 'GARANTÍAS', call: 'CALL CENTER', otro: 'OTROS' };
    const MOD_BADGE = { pagos: 'bm-pagos', ajuste: 'bm-ajuste', gar: 'bm-gar', call: 'bm-call', otro: 'bm-otro' };
    const TIPOS_BAR_COLORS = ['#2a78d6', '#eda100', '#e34948', '#1baf7a', '#4a3aa7'];
    const CHART_BLUE = '#2a78d6';
    const CHART_BAR_DIM = '#c8c6bc';
    const DIAS_KEYS = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    const DIAS_LABELS = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

    let state = {
        fechaI: '', fechaF: '', region: '',
        detalle: { usuario: '', region: '', sucursal: '', tipo: '', modulo: 'all', ejecutada: false },
        lastResumen: null,
        lastModalDatos: null
    };

    let charts = {};
    let modalCharts = { conteo: null, monto: null };

    async function descargaArchivo(url) {
        if (typeof swal === 'function') {
            swal({
                text: 'Generando archivo, espere un momento...',
                icon: '/img/wait.gif',
                button: false,
                closeOnClickOutside: false,
                closeOnEsc: false
            });
        }
        const cerrarEspera = () => {
            if (typeof swal === 'function') swal.close();
        };
        try {
            const res = await fetch(url, { credentials: 'same-origin' });
            if (!res.ok) throw new Error('No se pudo generar el archivo');
            const blob = await res.blob();
            let filename = 'descarga.xlsx';
            const disposition = res.headers.get('Content-Disposition');
            if (disposition) {
                const m = /filename[^;=\n]*=["']?([^"';\n]+)["']?/i.exec(disposition);
                if (m) filename = decodeURIComponent(m[1].trim());
            }
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(link.href);
            cerrarEspera();
        } catch (err) {
            cerrarEspera();
            if (typeof showError === 'function') {
                showError(err.message || 'Error al descargar el archivo');
            } else if (typeof swal === 'function') {
                swal({ text: err.message || 'Error al descargar el archivo', icon: 'error' });
            }
        }
    }

    function pad(n) { return String(n).padStart(2, '0'); }
    function fmtYMD(d) { return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`; }
    function fmtMonto(n) { return '$ ' + (typeof formatoMoneda === 'function' ? formatoMoneda(n) : Number(n).toLocaleString('es-MX')); }
    function fmtMontoCorto(n) {
        if (n >= 1e6) return '$' + (n / 1e6).toFixed(1) + 'M';
        if (n >= 1e3) return '$' + Math.round(n / 1e3).toLocaleString('es-MX') + 'K';
        return fmtMonto(n);
    }
    function esc(s) { return String(s || '').replace(/'/g, "\\'").replace(/"/g, '&quot;'); }

    function isDarkMode() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    function chartGridColor() {
        return isDarkMode() ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.07)';
    }

    function cardSurfaceColor() {
        const page = document.querySelector('.prod-op-page');
        if (!page) return '#fff';
        const c = getComputedStyle(page).getPropertyValue('--surface-1').trim();
        return c || '#fff';
    }

    function chartTextSecondary() {
        const page = document.querySelector('.prod-op-page');
        if (!page) return '#5a6570';
        const c = getComputedStyle(page).getPropertyValue('--text-secondary').trim();
        return c || '#5a6570';
    }

    function chartAxisTicks(extra) {
        return Object.assign({ color: '#898781', font: { size: 10 }, maxTicksLimit: 5 }, extra || {});
    }

    function chartScalesBarSemana() {
        return {
            x: {
                border: { display: false },
                grid: { display: false },
                ticks: { color: '#898781', font: { size: 9 }, autoSkip: false, maxRotation: 45, minRotation: 0 }
            },
            y: {
                border: { display: false },
                grid: { color: chartGridColor() },
                ticks: chartAxisTicks()
            }
        };
    }

    function chartScalesHorizontalBar() {
        return {
            x: { border: { display: false }, grid: { color: chartGridColor() }, ticks: chartAxisTicks() },
            y: { border: { display: false }, grid: { display: false }, ticks: chartAxisTicks() }
        };
    }

    function chartScalesLine() {
        return {
            x: { border: { display: false }, grid: { display: false }, ticks: chartAxisTicks() },
            y: { border: { display: false }, grid: { color: chartGridColor() }, ticks: chartAxisTicks() }
        };
    }

    function chartScalesLineTendencia() {
        return {
            x: {
                border: { display: false },
                grid: { display: false },
                ticks: { color: '#898781', font: { size: 10 }, maxRotation: 0, autoSkip: false }
            },
            y: {
                border: { display: false },
                grid: { color: chartGridColor(), drawTicks: false },
                ticks: Object.assign(chartAxisTicks(), {
                    padding: 6,
                    callback: (v) => {
                        const n = +v;
                        if (n >= 1000000) return (n / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
                        if (n >= 1000) return (n / 1000).toFixed(n >= 10000 ? 0 : 1).replace(/\.0$/, '') + 'k';
                        return n;
                    }
                })
            }
        };
    }

    function chartTooltipTendencia(avg, tooltipTitles) {
        return {
            backgroundColor: 'rgba(11,11,11,0.88)',
            cornerRadius: 8,
            padding: 10,
            titleColor: '#fff',
            bodyColor: '#fff',
            borderWidth: 0,
            displayColors: false,
            filter: (item) => item.datasetIndex === 0,
            callbacks: {
                title(items) {
                    const i = items[0]?.dataIndex;
                    return (tooltipTitles && tooltipTitles[i]) || items[0]?.label || '';
                },
                label(ctx) {
                    const v = +ctx.parsed.y || 0;
                    const lines = [`Incidencias: ${v.toLocaleString('es-MX')}`];
                    if (avg) {
                        const diff = Math.round(((v - avg) / avg) * 100);
                        const pct = Math.abs(diff);
                        if (diff > 0) lines.push(`${pct}% por arriba del promedio`);
                        else if (diff < 0) lines.push(`${pct}% por debajo del promedio`);
                        else lines.push('En el promedio');
                    }
                    return lines;
                }
            }
        };
    }

    function chartTooltipDark() {
        return {
            backgroundColor: 'rgba(11,11,11,0.88)',
            cornerRadius: 8,
            padding: 10,
            titleColor: '#fff',
            bodyColor: '#fff',
            borderWidth: 0
        };
    }

    function lineAreaGradient(chart) {
        const { ctx, chartArea } = chart;
        if (!chartArea) return 'rgba(42,120,214,0.12)';
        const g = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
        g.addColorStop(0, 'rgba(42,120,214,0.28)');
        g.addColorStop(0.55, 'rgba(42,120,214,0.08)');
        g.addColorStop(1, 'rgba(42,120,214,0)');
        return g;
    }

    function setChartLegend(containerId, items) {
        const el = document.getElementById(containerId);
        if (!el) return;
        el.innerHTML = items.map(it => {
            const mark = it.dash
                ? `<span class="chart-legend__dash" style="border-color:${it.color || '#eda100'}"></span>`
                : `<span class="chart-legend__swatch" style="background:${it.color}"></span>`;
            return `<span class="chart-legend__item">${mark}${escHtml(it.label)}</span>`;
        }).join('');
    }

    function registerChartPlugins() {
        if (registerChartPlugins.done || typeof Chart === 'undefined') return;
        registerChartPlugins.done = true;
        Chart.register({
            id: 'prodOpChartOverlays',
            afterDraw(chart) {
                const id = chart.canvas.id;
                const ctx = chart.ctx;
                if (id === 'chartTendencia') {
                    const data = chart.data.datasets[0]?.data || [];
                    if (!data.length) return;
                    let peakIdx = 0;
                    data.forEach((v, i) => { if (+v > +data[peakIdx]) peakIdx = i; });
                    const el = chart.getDatasetMeta(0).data[peakIdx];
                    if (!el || !+data[peakIdx]) return;
                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(el.x, el.y, 11, 0, Math.PI * 2);
                    ctx.strokeStyle = 'rgba(42,120,214,0.28)';
                    ctx.lineWidth = 3;
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.arc(el.x, el.y, 7, 0, Math.PI * 2);
                    ctx.strokeStyle = 'rgba(42,120,214,0.55)';
                    ctx.lineWidth = 1.5;
                    ctx.stroke();
                    ctx.restore();
                }
                if (id === 'chartSemana') {
                    const data = chart.data.datasets[0]?.data || [];
                    if (!data.length) return;
                    let peakIdx = 0;
                    data.forEach((v, i) => { if (+v > +data[peakIdx]) peakIdx = i; });
                    const el = chart.getDatasetMeta(0).data[peakIdx];
                    if (!el || !+data[peakIdx]) return;
                    ctx.save();
                    ctx.font = '600 10px "Segoe UI", "Helvetica Neue", Arial, sans-serif';
                    ctx.fillStyle = '#185fa5';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    ctx.fillText(Number(data[peakIdx]).toLocaleString('es-MX'), el.x, el.y - 6);
                    ctx.restore();
                }
                if (id === 'chartTipos') {
                    const data = chart.data.datasets[0]?.data || [];
                    const total = data.reduce((a, b) => a + (+b || 0), 0);
                    const meta = chart.getDatasetMeta(0);
                    ctx.save();
                    ctx.font = '10px "Segoe UI", "Helvetica Neue", Arial, sans-serif';
                    ctx.fillStyle = chartTextSecondary();
                    ctx.textAlign = 'left';
                    ctx.textBaseline = 'middle';
                    meta.data.forEach((bar, i) => {
                        if (!bar || data[i] == null) return;
                        const v = +data[i];
                        const pct = total ? Math.round((v / total) * 100) : 0;
                        ctx.fillText(`${v.toLocaleString('es-MX')} (${pct}%)`, bar.x + 6, bar.y);
                    });
                    ctx.restore();
                }
            }
        });
    }

    function chartScaleOpts() {
        const dark = isDarkMode();
        return {
            x: {
                ticks: { color: dark ? '#8b949e' : '#738091', maxRotation: 45 },
                grid: { color: dark ? '#3d444d' : '#eef2f6' }
            },
            y: {
                ticks: { color: dark ? '#8b949e' : '#738091' },
                grid: { color: dark ? '#3d444d' : '#eef2f6' }
            }
        };
    }

    function mergeChartOpts(extra, type) {
        const radial = type === 'doughnut' || type === 'pie';
        const base = { responsive: true, plugins: { legend: { display: false } } };
        if (!radial) base.scales = chartScaleOpts();
        const merged = Object.assign({}, base, extra || {});
        merged.plugins = Object.assign({}, base.plugins, (extra && extra.plugins) || {});
        if (!radial) {
            merged.scales = Object.assign({}, base.scales, (extra && extra.scales) || {});
        } else {
            delete merged.scales;
        }
        return merged;
    }

    function setPeriodo(preset) {
        const hoy = new Date();
        let ini, fin;
        if (preset === 'current') {
            ini = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        } else if (preset === 'prev') {
            ini = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
            fin = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
        } else if (preset === '3m') {
            fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
            ini = new Date(hoy.getFullYear(), hoy.getMonth() - 2, 1);
        } else {
            fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
            ini = new Date(hoy.getFullYear(), hoy.getMonth() - 11, 1);
        }
        state.fechaI = fmtYMD(ini);
        state.fechaF = fmtYMD(fin);
        document.getElementById('fechaDesde').value = state.fechaI;
        document.getElementById('fechaHasta').value = state.fechaF;
    }

    function aplicarBusquedaToolbar() {
        state.fechaI = document.getElementById('fechaDesde').value;
        state.fechaF = document.getElementById('fechaHasta').value;
        state.region = document.getElementById('filtroRegionToolbar').value;
        state.detalle.region = state.region;
        state.detalle.ejecutada = false;
        document.getElementById('fRegion').value = state.region;
        loadResumen();
        if (document.getElementById('tab-detalle').classList.contains('active')) showDetalleIdle();
    }

    function syncDetalleFiltrosDesdeDom() {
        state.detalle.usuario = document.getElementById('fUsuario').value;
        state.detalle.region = document.getElementById('fRegion').value;
        state.detalle.sucursal = document.getElementById('fSucursal').value;
        state.detalle.tipo = document.getElementById('fTipo').value;
        const activeMod = document.querySelector('.mod-btn.active');
        state.detalle.modulo = activeMod ? activeMod.dataset.mod : 'all';
    }

    function destroyDetalleTabla() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tblDetalle')) {
            $('#tblDetalle').DataTable().destroy();
        }
        $('#tblDetalle tbody').empty();
    }

    function showDetalleIdle() {
        destroyDetalleTabla();
        document.getElementById('resCount').textContent = '0';
        document.getElementById('resMonto').textContent = '$0';
        document.getElementById('resProm').textContent = '$0';
        const empty = document.getElementById('emptyState');
        empty.style.display = 'block';
        empty.innerHTML =
            '<i class="fa fa-search"></i>' +
            '<p><strong>Consulta no ejecutada</strong></p>' +
            '<p style="font-size:13px">Ajusta los filtros y pulsa <strong>Aplicar filtros</strong>.</p>';
        $('#tblDetalle').hide();
        updateChips();
    }

    function paramsBase() {
        const p = { fechaI: state.fechaI, fechaF: state.fechaF };
        if (state.region) p.region = state.region;
        return p;
    }

    function getParams() {
        return new URLSearchParams(paramsBase()).toString();
    }

    function showApiError(r) {
        showError((r && (r.error || r.mensaje)) || 'Error en la solicitud');
    }

    function switchTab(tab) {
        document.querySelectorAll('.tab-link').forEach(l => l.classList.toggle('active', l.dataset.tab === tab));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.toggle('active', p.id === 'tab-' + tab));
    }

    function loadResumen() {
        consultaServidor(API + 'GetProductividadResumen/?' + getParams(), {}, (r) => {
            if (!r.success) return showApiError(r);
            renderResumen(r.datos);
            loadCatalogos();
        });
    }

    function loadCatalogos() {
        consultaServidor(API + 'GetProductividadCatalogos/?' + getParams(), {}, (r) => {
            if (!r.success) return;
            fillSelect('filtroRegionToolbar', r.datos.regiones, state.region, 'Todas');
            fillSelect('fRegion', r.datos.regiones, state.detalle.region, 'Todas');
            fillSelect('fSucursal', r.datos.sucursales, state.detalle.sucursal, 'Todas');
            fillSelect('fTipo', r.datos.tipos, state.detalle.tipo, 'Todos');
            const fu = document.getElementById('fUsuario');
            const cur = fu.value;
            fu.innerHTML = '<option value="">Todos los usuarios</option>' +
                r.datos.usuarios.map(u => `<option value="${esc(u.CDGPE)}">${escHtml(u.CDGPE)} (${escHtml(u.NOMBRE)})</option>`).join('');
            fu.value = cur;
        });
    }

    function fillSelect(id, items, selected, allLabel) {
        const el = document.getElementById(id);
        if (!el) return;
        el.innerHTML = `<option value="">${allLabel}</option>` + (items || []).map(i => {
            const v = typeof i === 'string' ? i : i;
            return `<option value="${esc(v)}"${v === selected ? ' selected' : ''}>${v}</option>`;
        }).join('');
    }

    function renderResumen(d) {
        state.lastResumen = d;
        const k = d.kpis;
        const mesLabel = formatPeriodoLabel();
        document.getElementById('insightText').innerHTML =
            `En <strong>${mesLabel}</strong> se atendieron <strong>${k.total.toLocaleString('es-MX')} incidencias</strong> ` +
            `(${k.pct_total >= 0 ? '+' : ''}${k.pct_total}% vs periodo anterior). ` +
            `El <strong>${k.pct_modulo || d.insight.pct_modulo}%</strong> provino de <strong>${d.insight.modulo_label}</strong>` +
            (d.insight.region_top ? ` y la región <strong>${d.insight.region_top}</strong> concentró actividad relevante.` : '.');

        document.getElementById('kpiRow').innerHTML = `
            <div class="kpi"><div class="lbl">Total incidencias</div><div class="num">${k.total.toLocaleString('es-MX')}</div>
                <div class="delta ${k.pct_total >= 0 ? 'up' : 'down'}"><i class="fa fa-arrow-${k.pct_total >= 0 ? 'up' : 'down'}"></i> ${k.pct_total >= 0 ? '+' : ''}${k.pct_total}% vs anterior</div></div>
            <div class="kpi"><div class="lbl">Monto involucrado</div><div class="num">${fmtMonto(k.monto)}</div>
                <div class="delta ${k.pct_monto >= 0 ? 'up' : 'down'}">${k.pct_monto >= 0 ? '+' : ''}${k.pct_monto}%</div></div>
            <div class="kpi"><div class="lbl">Promedio diario</div><div class="num">${k.promedio_diario}</div>
                <div class="delta neutral">${k.dias} días en el periodo</div></div>
            <div class="kpi"><div class="lbl">Usuarios activos</div><div class="num">${k.usuarios_activos} <span style="font-size:14px;font-weight:400;color:var(--muted)">/ ${k.total_usuarios}</span></div>
                <div class="delta neutral">${k.sucursales} sucursales con actividad</div></div>`;

        document.getElementById('tablaResumenPeriodo').textContent = 'Periodo: ' + mesLabel;
        renderSpotlight(d.destacados, k.total);
        renderTendencia(d.tendencia);
        renderSemana(d.semana);
        renderTipos(d.tipos);
        renderModulos(d.modulos, k.total);
        updateModuloFilters(d.modulos);
        renderRankRegiones(d.top_regiones, k.total);
        renderRankSucursales(d.top_sucursales, k.total);
        renderTablaUsuarios(d.tabla_usuarios, k.total);
    }

    function formatPeriodoLabel() {
        const a = new Date(state.fechaI + 'T00:00:00');
        const b = new Date(state.fechaF + 'T00:00:00');
        const ma = a.toLocaleString('es-MX', { month: 'long', year: 'numeric' });
        if (a.getMonth() === b.getMonth() && a.getFullYear() === b.getFullYear()) return ma;
        return `${a.toLocaleDateString('es-MX')} – ${b.toLocaleDateString('es-MX')}`;
    }

    function escHtml(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function bindSpotlightClicks() {
        document.getElementById('spotlightRow').querySelectorAll('[data-spot-action]').forEach(el => {
            el.addEventListener('click', () => {
                const action = el.dataset.spotAction;
                if (action === 'usuario') {
                    verDetalleUsuario(el.dataset.codigo, el.dataset.nombre);
                } else if (action === 'sucursal') {
                    irConsulta({ sucursal: el.dataset.valor });
                } else if (action === 'tipo') {
                    irConsulta({ tipo: el.dataset.valor });
                }
            });
        });
    }

    function renderSpotlight(dest, total) {
        const u = dest.usuario;
        const s = dest.sucursal;
        const t = dest.tipo;
        const m = dest.mayor_monto;
        const pctU = u && total ? ((u.TOTAL / total) * 100).toFixed(1) : 0;
        const pctS = s && total ? ((s.TOTAL / total) * 100).toFixed(1) : 0;
        const pctT = t && total ? ((t.TOTAL / total) * 100).toFixed(1) : 0;
        const modTipo = t ? (t.TIPO.includes('PAGO') ? 'Caja Pagos Día' : 'Movimiento') : '';
        let fechaM = '';
        if (m && m.FECHA) {
            const parts = String(m.FECHA).split(' ')[0].split('/');
            if (parts.length >= 3) fechaM = `${parts[0]} ${mesNum(parts[1])} 20${parts[2]}`;
        }
        document.getElementById('spotlightRow').innerHTML = `
            ${u ? `<div class="spot" data-spot-action="usuario" data-codigo="${escHtml(u.CDGPE)}" data-nombre="${escHtml(u.NOMBRE)}"><span class="go"><i class="fa fa-angle-right"></i></span>
                <div class="cat">Usuario #1</div><div class="winner"><div class="medal medal-gold"><i class="fa fa-user"></i></div>
                <div class="winner-text"><div class="code">${escHtml(u.CDGPE)}</div><div class="name">${escHtml(u.NOMBRE)}</div></div></div>
                <div class="stat"><span class="val">${u.TOTAL}</span><span class="pct">${pctU}% del total</span></div></div>` : ''}
            ${s ? `<div class="spot" data-spot-action="sucursal" data-valor="${escHtml(s.SUCURSAL)}"><span class="go"><i class="fa fa-angle-right"></i></span>
                <div class="cat">Sucursal #1</div><div class="winner"><div class="medal medal-blue"><i class="fa fa-building"></i></div>
                <div class="winner-text"><div class="code">Región ${escHtml(s.REGION)}</div><div class="name">${escHtml(s.SUCURSAL)}</div></div></div>
                <div class="stat"><span class="val">${s.TOTAL}</span><span class="pct">${pctS}% del total</span></div></div>` : ''}
            ${t ? `<div class="spot" data-spot-action="tipo" data-valor="${escHtml(t.TIPO)}"><span class="go"><i class="fa fa-angle-right"></i></span>
                <div class="cat">Tipo de movimiento #1</div><div class="winner"><div class="medal medal-green"><i class="fa fa-exchange"></i></div>
                <div class="winner-text"><div class="code">${escHtml(modTipo)}</div><div class="name">${escHtml(t.TIPO)}</div></div></div>
                <div class="stat"><span class="val">${t.TOTAL}</span><span class="pct">${pctT}% del total</span></div></div>` : ''}
            ${m ? `<div class="spot spot-static"><div class="cat">Mayor monto atendido</div><div class="winner"><div class="medal medal-orange"><i class="fa fa-money"></i></div>
                <div class="winner-text"><div class="code">${escHtml(m.CDGPE)} · Crédito ${escHtml(m.CDGNS)}</div><div class="name">${escHtml(m.TIPO)}</div></div></div>
                <div class="stat"><span class="val">${fmtMonto(m.MONTO)}</span><span class="pct">${fechaM}</span></div></div>` : ''}`;
        bindSpotlightClicks();
    }

    function mesNum(mm) {
        const n = parseInt(mm, 10) - 1;
        return new Date(2000, n, 1).toLocaleString('es-MX', { month: 'short' });
    }

    function formatMesTendencia(r) {
        const mes = (r.MES_LETRA || '').trim();
        const abbr = mes.length >= 3 ? mes.substring(0, 3) : mes;
        const label = abbr.charAt(0).toUpperCase() + abbr.slice(1).toLowerCase();
        return `${label} '${String(r.ANO || '').slice(-2)}`;
    }

    function formatMesTendenciaCompleto(r) {
        const mes = (r.MES_LETRA || '').trim();
        const label = mes.charAt(0).toUpperCase() + mes.slice(1).toLowerCase();
        return `${label} ${r.ANO || ''}`;
    }

    function renderTendencia(rows) {
        rows = rows || [];
        const labels = rows.map(formatMesTendencia);
        const tooltipTitles = rows.map(formatMesTendenciaCompleto);
        const data = rows.map(r => +r.TOTAL);
        const total = data.reduce((a, b) => a + b, 0);
        const avg = data.length ? total / data.length : 0;
        let peakIdx = 0;
        data.forEach((v, i) => { if (v > data[peakIdx]) peakIdx = i; });
        const last = data.length ? data[data.length - 1] : 0;
        const prev = data.length > 1 ? data[data.length - 2] : 0;
        const momPct = prev ? Math.round(((last - prev) / prev) * 100) : 0;
        const momSign = momPct > 0 ? '+' : '';
        const momClass = momPct > 0 ? 'chart-trend-stat__hint--up' : momPct < 0 ? 'chart-trend-stat__hint--down' : 'chart-trend-stat__hint--neutral';

        const peakEl = document.getElementById('chartTendenciaPeak');
        const statsEl = document.getElementById('chartTendenciaStats');
        const badgeEl = document.getElementById('chartTendenciaBadge');
        if (badgeEl && data.length) {
            badgeEl.textContent = data.length === 1 ? '1 mes' : `${data.length} meses en la gráfica`;
        }
        if (peakEl) {
            peakEl.innerHTML = data.length
                ? `<i class="ti ti-flame"></i> Pico: ${escHtml(labels[peakIdx])} (${data[peakIdx].toLocaleString('es-MX')})`
                : '';
        }
        if (statsEl) {
            const mesesGrafica = data.length;
            const acumuladoHint = mesesGrafica === 1
                ? 'incidencias en el mes mostrado'
                : `incidencias en ${mesesGrafica} meses de la gráfica`;
            statsEl.innerHTML = data.length ? `
                <div class="chart-trend-stat">
                    <div class="chart-trend-stat__lbl">Total acumulado</div>
                    <div class="chart-trend-stat__val">${total.toLocaleString('es-MX')}</div>
                    <div class="chart-trend-stat__hint">${acumuladoHint}</div>
                </div>
                <div class="chart-trend-stat">
                    <div class="chart-trend-stat__lbl">Promedio mensual</div>
                    <div class="chart-trend-stat__val">${Math.round(avg).toLocaleString('es-MX')}</div>
                    <div class="chart-trend-stat__hint">media del periodo</div>
                </div>
                <div class="chart-trend-stat">
                    <div class="chart-trend-stat__lbl">Mes actual</div>
                    <div class="chart-trend-stat__val">${last.toLocaleString('es-MX')}</div>
                    <div class="chart-trend-stat__hint ${momClass}">${prev ? `${momSign}${momPct}% vs mes anterior` : 'primer mes del periodo'}</div>
                </div>` : '';
        }
        setChartLegend('chartTendenciaLegend', [
            { color: CHART_BLUE, label: 'Volumen mensual' },
            { color: '#eda100', label: 'Promedio del periodo', dash: true }
        ]);

        const surface = cardSurfaceColor();
        const lastIdx = data.length - 1;
        upsertChart('chartTendencia', 'line', labels, [
            {
                type: 'line',
                data,
                borderColor: CHART_BLUE,
                backgroundColor: (ctx) => lineAreaGradient(ctx.chart),
                fill: true,
                tension: 0.35,
                borderWidth: 2.5,
                pointRadius: data.map((_, i) => (i === peakIdx ? 6 : i === lastIdx ? 5 : 3)),
                pointBackgroundColor: data.map((_, i) => (i === lastIdx && i !== peakIdx ? '#185fa5' : CHART_BLUE)),
                pointBorderColor: data.map((_, i) => (i === peakIdx || i === lastIdx ? '#fff' : surface)),
                pointBorderWidth: data.map((_, i) => (i === peakIdx ? 3 : 2)),
                pointHoverRadius: 7,
                order: 2
            },
            {
                type: 'line',
                data: data.map(() => avg),
                borderColor: '#eda100',
                borderDash: [6, 4],
                borderWidth: 1.5,
                pointRadius: 0,
                pointHoverRadius: 0,
                fill: false,
                order: 1
            }
        ], {
            maintainAspectRatio: false,
            layout: { padding: { top: 12, right: 10, left: 4, bottom: 0 } },
            interaction: { mode: 'index', intersect: false },
            scales: chartScalesLineTendencia(),
            plugins: { legend: { display: false }, tooltip: chartTooltipTendencia(avg, tooltipTitles) }
        });
    }

    function renderSemana(rows) {
        const map = {};
        (rows || []).forEach(r => { if (r.DIA) map[r.DIA] = +r.TOTAL; });
        const data = DIAS_KEYS.map(d => map[d] || 0);
        const total = data.reduce((a, b) => a + b, 0);
        let pico = -1;
        let picoVal = -1;
        DIAS_KEYS.forEach((d) => {
            const v = map[d] || 0;
            if (v > picoVal) {
                picoVal = v;
                pico = DIAS_KEYS.indexOf(d);
            }
        });
        const avg = DIAS_KEYS.length ? total / DIAS_KEYS.length : 0;
        const peakEl = document.getElementById('chartSemanaPeak');
        const badgeEl = document.getElementById('chartSemanaBadge');
        const subEl = document.getElementById('chartSemanaSub');
        if (badgeEl) badgeEl.textContent = formatPeriodoLabel();
        if (subEl) subEl.textContent = 'Distribución de incidencias por día';
        if (peakEl) {
            peakEl.innerHTML = total && pico >= 0
                ? `<i class="ti ti-flame"></i> ${DIAS_LABELS[pico]} pico (${Math.round((data[pico] / total) * 100)}%)`
                : '';
        }
        setChartLegend('chartSemanaLegend', [
            { color: CHART_BLUE, label: 'Día pico' },
            { color: CHART_BAR_DIM, label: 'Otros días' },
            { color: '#eda100', label: 'Promedio semanal', dash: true }
        ]);
        upsertChart('chartSemana', 'bar', DIAS_LABELS, [
            {
                type: 'bar',
                data,
                backgroundColor: data.map((_, i) => (i === pico ? CHART_BLUE : CHART_BAR_DIM)),
                borderRadius: { topLeft: 4, topRight: 4 },
                borderSkipped: 'bottom',
                barPercentage: 0.65,
                order: 2
            },
            {
                type: 'line',
                data: DIAS_KEYS.map(() => avg),
                borderColor: '#eda100',
                borderDash: [5, 4],
                borderWidth: 1.5,
                pointRadius: 0,
                fill: false,
                order: 1,
                spanGaps: false
            }
        ], {
            maintainAspectRatio: false,
            scales: chartScalesBarSemana(),
            plugins: { legend: { display: false }, tooltip: chartTooltipDark() }
        });
    }

    function renderTipos(rows) {
        const labels = (rows || []).map(r => r.TIPO);
        const data = (rows || []).map(r => +r.TOTAL);
        const total = data.reduce((a, b) => a + b, 0);
        const badgeEl = document.getElementById('chartTiposBadge');
        if (badgeEl) badgeEl.textContent = formatPeriodoLabel();
        setChartLegend('chartTiposLegend', labels.map((label, i) => ({
            color: TIPOS_BAR_COLORS[i % TIPOS_BAR_COLORS.length],
            label: total
                ? `${label} (${Math.round((data[i] / total) * 100)}%)`
                : label
        })));
        upsertChart('chartTipos', 'bar', labels, [{
            data,
            backgroundColor: labels.map((_, i) => TIPOS_BAR_COLORS[i % TIPOS_BAR_COLORS.length]),
            borderRadius: { topRight: 4, bottomRight: 4 },
            borderSkipped: 'left',
            barThickness: 18
        }], {
            indexAxis: 'y',
            maintainAspectRatio: false,
            layout: { padding: { left: 4, right: 80, top: 4, bottom: 0 } },
            scales: {
                x: {
                    border: { display: false },
                    grid: { color: chartGridColor() },
                    ticks: chartAxisTicks()
                },
                y: {
                    display: false
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: Object.assign(chartTooltipDark(), {
                    callbacks: {
                        title(items) {
                            const idx = items[0]?.dataIndex;
                            return idx != null ? labels[idx] : '';
                        },
                        label(ctx) {
                            const v = +ctx.raw;
                            const pct = total ? ((v / total) * 100).toFixed(1) : '0.0';
                            return ` ${v.toLocaleString('es-MX')} (${pct}%)`;
                        }
                    }
                })
            }
        });
    }

    function updateModuloFilters(modulos) {
        const map = {};
        (modulos || []).forEach(r => { map[r.MODULO] = +r.TOTAL; });
        document.querySelectorAll('.mod-btn').forEach(btn => {
            const mod = btn.dataset.mod;
            if (mod === 'all') {
                btn.style.display = '';
                return;
            }
            const visible = (map[mod] || 0) > 0;
            btn.style.display = visible ? '' : 'none';
            if (!visible && btn.classList.contains('active')) {
                document.querySelectorAll('.mod-btn').forEach(b => b.classList.remove('active'));
                document.querySelector('.mod-btn[data-mod="all"]').classList.add('active');
                state.detalle.modulo = 'all';
            }
        });
    }

    function renderModulos(rows, total) {
        const labels = ['Pagos Día', 'Ajuste Manual', 'Garantías', 'Call Center', 'Otros'];
        const keys = ['pagos', 'ajuste', 'gar', 'call', 'otro'];
        const map = {};
        (rows || []).forEach(r => { map[r.MODULO] = +r.TOTAL; });
        const data = keys.map(k => map[k] || 0);
        const colors = ['#1a6fb5', '#e8a020', '#2e7d52', '#7d3c98', '#888'];
        upsertChart('chartModulos', 'doughnut', labels, [{ data, backgroundColor: colors, borderWidth: 0 }], {
            cutout: '65%', plugins: { legend: { display: false } }
        });
        document.getElementById('modLegend').innerHTML = keys.map((k, i) => {
            const v = data[i];
            const pct = total ? Math.round((v / total) * 100) : 0;
            const cls = ['m-pagos', 'm-ajuste', 'm-gar', 'm-call', 'm-otro'][i];
            return v ? `<span class="${cls}"><i></i> ${labels[i]} ${pct}%</span>` : '';
        }).join('');
    }

    function renderRankRegiones(items, total) {
        const el = document.getElementById('rankRegiones');
        if (!items || !items.length) {
            el.innerHTML = '<p class="text-muted" style="padding:8px">Sin datos</p>';
            return;
        }
        el.innerHTML = items.map((it, i) => {
            const pct = total ? +((it.TOTAL / total) * 100).toFixed(1) : 0;
            const pctLabel = pct.toLocaleString('es-MX', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
            const sucursales = +it.SUCURSALES || 0;
            const sub = sucursales === 1 ? '1 sucursal con actividad' : `${sucursales.toLocaleString('es-MX')} sucursales con actividad`;
            return `<div class="rank-item" onclick="ProdOP.irConsulta({region:'${esc(it.REGION)}'})">
                <span class="rank-pos ${i === 0 ? 'top' : ''}">${i + 1}</span>
                <div><div class="rank-name">${it.REGION}</div><div class="rank-sub">${sub}</div></div>
                <span class="rank-val">${it.TOTAL}<span class="rank-pct">${pctLabel}%</span></span>
                <div class="rank-bar-wrap"><div class="rank-bar" style="width:${pct}%"></div></div>
            </div>`;
        }).join('');
    }

    function renderRankSucursales(items, total) {
        const el = document.getElementById('rankSucursales');
        if (!items || !items.length) {
            el.innerHTML = '<p class="text-muted" style="padding:8px">Sin datos</p>';
            return;
        }
        el.innerHTML = items.map((it, i) => {
            const pct = total ? +((it.TOTAL / total) * 100).toFixed(1) : 0;
            const pctLabel = pct.toLocaleString('es-MX', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
            return `<div class="rank-item" onclick="ProdOP.irConsulta({sucursal:'${esc(it.SUCURSAL)}'})">
                <span class="rank-pos ${i === 0 ? 'top' : ''}">${i + 1}</span>
                <div><div class="rank-name">${it.SUCURSAL}</div><div class="rank-sub">${it.REGION}</div></div>
                <span class="rank-val">${it.TOTAL}<span class="rank-pct">${pctLabel}%</span></span>
                <div class="rank-bar-wrap"><div class="rank-bar" style="width:${pct}%"></div></div>
            </div>`;
        }).join('');
    }

    function renderTablaUsuarios(rows, total) {
        document.getElementById('tblResumenUsuarios').innerHTML = (rows || []).map((u, i) => {
            const pct = total ? +((u.TOTAL / total) * 100).toFixed(1) : 0;
            const mod = u.MODULO || 'otro';
            const modPct = u.MODULO_PCT != null ? +u.MODULO_PCT : 0;
            const modPctLabel = modPct.toLocaleString('es-MX', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
            return `<tr>
                <td>${i + 1}</td>
                <td><strong>${u.CDGPE}</strong> <span class="text-muted">${u.NOMBRE}</span></td>
                <td><strong>${u.TOTAL}</strong></td>
                <td>
                    <div class="particip-cell">
                        <div class="particip-bar-wrap"><div class="particip-bar" style="width:${pct}%"></div></div>
                        <span class="particip-pct">${pct.toLocaleString('es-MX', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%</span>
                    </div>
                </td>
                <td>${fmtMonto(u.MONTO)}</td>
                <td>
                    <span class="badge-mod ${MOD_BADGE[mod] || 'bm-ajuste'}">${MOD_LABELS[mod] || mod}</span>
                    <div class="cell-sub">${modPctLabel}% de sus incidencias</div>
                </td>
                <td><button class="btn btn-primary btn-xs" onclick="ProdOP.verDetalleUsuario('${esc(u.CDGPE)}','${esc(u.NOMBRE)}')"><i class="fa fa-eye"></i></button></td>
            </tr>`;
        }).join('');
    }

    function refreshChartsTheme() {
        if (state.lastResumen) {
            const d = state.lastResumen;
            renderTendencia(d.tendencia);
            renderSemana(d.semana);
            renderTipos(d.tipos);
            renderModulos(d.modulos, d.kpis.total);
            updateModuloFilters(d.modulos);
        }
        if (state.lastModalDatos && $('#detalleUsuario').hasClass('in')) {
            actualizaModalCharts(state.lastModalDatos);
        }
    }

    function upsertChart(id, type, labels, datasets, extraOpts) {
        const ctx = document.getElementById(id);
        if (!ctx) return;
        const opts = mergeChartOpts(extraOpts, type);
        if (charts[id]) {
            charts[id].data.labels = labels;
            charts[id].data.datasets = datasets;
            charts[id].options = mergeChartOpts(extraOpts, type);
            charts[id].update();
            return;
        }
        charts[id] = new Chart(ctx, { type, data: { labels, datasets }, options: opts });
    }

    function buildConsultaParams() {
        syncDetalleFiltrosDesdeDom();
        const p = Object.assign({}, paramsBase(), state.detalle);
        delete p.ejecutada;
        return p;
    }

    function loadConsulta() {
        state.detalle.ejecutada = true;
        const p = buildConsultaParams();
        consultaServidor(API + 'GetProductividadConsulta/?' + new URLSearchParams(p).toString(), {}, (r) => {
            if (!r.success) return showApiError(r);
            renderConsulta(r.datos);
        });
    }

    function renderConsulta(d) {
        const filas = d.filas || [];
        const total = filas.length || d.total || 0;
        const monto = d.monto || 0;
        document.getElementById('resCount').textContent = total.toLocaleString('es-MX');
        document.getElementById('resMonto').textContent = fmtMonto(monto);
        document.getElementById('resProm').textContent = total ? fmtMonto(monto / total) : fmtMonto(0);
        const empty = document.getElementById('emptyState');
        destroyDetalleTabla();
        if (!filas.length) {
            $('#tblDetalle').hide();
            empty.style.display = 'block';
            empty.innerHTML =
                '<i class="fa fa-inbox"></i>' +
                '<p><strong>Sin resultados</strong></p>' +
                '<p style="font-size:13px">Prueba ajustando los filtros o la búsqueda.</p>';
            updateChips();
            return;
        }
        empty.style.display = 'none';
        $('#tblDetalle').show();
        const tbody = document.querySelector('#tblDetalle tbody');
        filas.forEach(r => {
            const mod = r.MODULO || 'otro';
            const fecha = String(r.FECHA).split(' ');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><div class="cell-main">${fecha[0]}</div><div class="cell-sub">${fecha[1] || ''}</div></td>
                <td class="cell-user"><div><code>${r.CDGPE}</code></div><div class="cell-sub">${(r.NOMBRE || '').split(' ').slice(0, 2).join(' ')}</div></td>
                <td><div class="cell-credito cell-main">${r.CDGNS}</div><div class="cell-sub">Ciclo ${r.CICLO}</div></td>
                <td><div class="cell-main">${r.TIPO}</div><div class="cell-sub">${r.DESCRIPCION || ''}</div></td>
                <td><span class="badge-mod ${MOD_BADGE[mod]}">${MOD_LABELS[mod]}</span></td>
                <td><div class="cell-main">${r.SUCURSAL}</div><div class="cell-sub">${r.REGION}</div></td>
                <td class="cell-monto">${fmtMonto(r.MONTO)}</td>`;
            tbody.appendChild(tr);
        });
        if (typeof configuraTabla === 'function') {
            configuraTabla('tblDetalle');
        }
        updateChips();
    }

    function updateChips() {
        const chips = document.getElementById('filterChips');
        const d = state.detalle;
        const items = [];
        if (d.usuario) items.push({ k: 'usuario', l: `Usuario: ${d.usuario}` });
        if (d.region) items.push({ k: 'region', l: `Región: ${d.region}` });
        if (d.sucursal) items.push({ k: 'sucursal', l: `Sucursal: ${d.sucursal}` });
        if (d.tipo) items.push({ k: 'tipo', l: `Tipo: ${d.tipo}` });
        if (d.modulo !== 'all') items.push({ k: 'modulo', l: `Módulo: ${MOD_LABELS[d.modulo]}` });
        chips.innerHTML = items.map(it => `<span class="chip">${it.l} <span class="x" data-k="${it.k}">×</span></span>`).join('');
        chips.querySelectorAll('.x').forEach(x => {
            x.onclick = () => {
                const k = x.dataset.k;
                state.detalle[k] = k === 'modulo' ? 'all' : '';
                if (k === 'usuario') document.getElementById('fUsuario').value = '';
                if (k === 'region') document.getElementById('fRegion').value = '';
                if (k === 'sucursal') document.getElementById('fSucursal').value = '';
                if (k === 'tipo') document.getElementById('fTipo').value = '';
                if (k === 'modulo') document.querySelectorAll('.mod-btn').forEach(b => b.classList.toggle('active', b.dataset.mod === 'all'));
                if (state.detalle.ejecutada) loadConsulta();
                else updateChips();
            };
        });
    }

    function verDetalleUsuario(codigo, nombre) {
        document.getElementById('ttlNombre').innerHTML = `<b>Total de incidencias atendidas por ${nombre} en ${formatPeriodoLabel()}</b>`;
        document.getElementById('xsl_usuario').value = codigo;
        document.getElementById('xsl_fechaI').value = state.fechaI;
        document.getElementById('xsl_fechaF').value = state.fechaF;
        $('#detalleUsuario').modal('show');
        consultaServidor(API + 'GetProductividadIncidenciasUsuario/', {
            usuario: codigo, fechaI: state.fechaI, fechaF: state.fechaF
        }, (r) => {
            if (!r.success) return showApiError(r);
            state.lastModalDatos = r.datos;
            actualizaModalCharts(r.datos);
            actualizaModalTabla(r.datos);
        });
    }

    function actualizaModalCharts(datos) {
        const etiquetas = [], conteo = [], totales = [];
        (datos || []).forEach(inc => {
            const fecha = String(inc.FECHA).split(' ')[0];
            const idx = etiquetas.indexOf(fecha);
            if (idx === -1) { etiquetas.push(fecha); conteo.push(1); totales.push(parseFloat(inc.MONTO)); }
            else { conteo[idx]++; totales[idx] += parseFloat(inc.MONTO); }
        });
        etiquetas.reverse(); conteo.reverse(); totales.reverse();
        initModalChart('chrtUConteo', 'conteo', 'Registros', etiquetas, conteo, '#1baf7a', 'rgba(27,175,122,0.35)');
        initModalChart('chrtMonto', 'monto', 'Monto', etiquetas, totales, '#eda100', 'rgba(237,161,0,0.35)');
    }

    function modalChartOpts(title) {
        const dark = isDarkMode();
        const tick = dark ? '#8b949e' : '#738091';
        const grid = dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.07)';
        const titleColor = dark ? '#e6edf3' : '#2b2b2b';
        return {
            responsive: true,
            interaction: { mode: 'nearest', intersect: false },
            plugins: {
                title: { display: true, text: title, color: titleColor, font: { size: 13, weight: '600' } },
                legend: { display: false }
            },
            scales: {
                x: { ticks: { color: tick, maxRotation: 45 }, grid: { color: grid } },
                y: { ticks: { color: tick }, grid: { color: grid } }
            }
        };
    }

    function initModalChart(canvasId, key, title, labels, data, border, bg) {
        const ctx = document.getElementById(canvasId);
        const ds = { label: title, data, borderColor: border, backgroundColor: bg, borderWidth: 2, borderRadius: 5 };
        const opts = modalChartOpts(title);
        if (modalCharts[key]) {
            modalCharts[key].data.labels = labels;
            modalCharts[key].data.datasets = [ds];
            modalCharts[key].options = modalChartOpts(title);
            modalCharts[key].update();
        } else {
            modalCharts[key] = new Chart(ctx, { type: 'bar', data: { labels, datasets: [ds] }, options: opts });
        }
    }

    function destroyModalTabla() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tblUsuario')) {
            $('#tblUsuario').DataTable().destroy();
        }
        $('#tblUsuario tbody').empty();
    }

    function actualizaModalTabla(datos) {
        destroyModalTabla();
        const tbody = document.querySelector('#tblUsuario tbody');
        (datos || []).forEach(inc => {
            const tr = document.createElement('tr');
            [
                inc.FECHA,
                inc.CDGNS,
                inc.CICLO,
                '$ ' + formatoMoneda(inc.MONTO),
                inc.DESCRIPCION,
                inc.TIPO,
                inc.REGION,
                inc.SUCURSAL
            ].forEach(text => {
                const td = document.createElement('td');
                td.textContent = text ?? '';
                tr.appendChild(td);
            });
            tbody.appendChild(tr);
        });
        if (typeof configuraTabla === 'function') {
            configuraTabla('tblUsuario');
        }
    }

    function irConsulta(filtros) {
        switchTab('detalle');
        state.detalle.usuario = '';
        state.detalle.region = '';
        state.detalle.sucursal = '';
        state.detalle.tipo = '';
        state.detalle.modulo = 'all';
        if (filtros.region) state.detalle.region = filtros.region;
        if (filtros.sucursal) state.detalle.sucursal = filtros.sucursal;
        if (filtros.tipo) state.detalle.tipo = filtros.tipo;
        document.getElementById('fUsuario').value = '';
        document.getElementById('fRegion').value = state.detalle.region;
        document.getElementById('fSucursal').value = state.detalle.sucursal;
        document.getElementById('fTipo').value = state.detalle.tipo;
        document.querySelectorAll('.mod-btn').forEach(b => b.classList.toggle('active', b.dataset.mod === 'all'));
        loadConsulta();
    }

    function bindUi() {
        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const tab = link.dataset.tab;
                switchTab(tab);
                if (tab === 'detalle' && !state.detalle.ejecutada) showDetalleIdle();
            });
        });

        document.querySelectorAll('#periodPills button').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('#periodPills button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                setPeriodo(btn.dataset.period);
                aplicarBusquedaToolbar();
            });
        });

        document.getElementById('btnBuscarToolbar').onclick = aplicarBusquedaToolbar;
        document.getElementById('fechaDesde').onchange = document.getElementById('fechaHasta').onchange = function () {
            document.querySelectorAll('#periodPills button').forEach(b => b.classList.remove('active'));
        };
        document.getElementById('btnAplicar').onclick = () => loadConsulta();
        document.getElementById('btnExcelConsulta').onclick = () => {
            descargaArchivo(API + 'GetExcelProductividadConsulta/?' + new URLSearchParams(buildConsultaParams()).toString());
        };
        document.getElementById('btnLimpiar').onclick = e => {
            e.preventDefault();
            state.detalle = { usuario: '', region: '', sucursal: '', tipo: '', modulo: 'all', ejecutada: false };
            ['fUsuario', 'fRegion', 'fSucursal', 'fTipo'].forEach(id => document.getElementById(id).value = '');
            document.querySelectorAll('.mod-btn').forEach(b => b.classList.toggle('active', b.dataset.mod === 'all'));
            showDetalleIdle();
        };
        document.querySelectorAll('.mod-btn').forEach(btn => {
            btn.onclick = () => {
                document.querySelectorAll('.mod-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                state.detalle.modulo = btn.dataset.mod;
                loadConsulta();
            };
        });
        document.getElementById('btnDescargaExcel').onclick = () => {
            const q = new URLSearchParams({
                usuario: document.getElementById('xsl_usuario').value,
                fechaI: document.getElementById('xsl_fechaI').value,
                fechaF: document.getElementById('xsl_fechaF').value
            }).toString();
            descargaArchivo(API + 'GetExcelProductividadIncidenciasUsuario/?' + q);
        };
        $('#detalleUsuario').on('hidden.bs.modal', () => {
            state.lastModalDatos = null;
            actualizaModalCharts([]);
            destroyModalTabla();
        });
    }

    window.ProdOP = { verDetalleUsuario, irConsulta };

    $(document).ready(() => {
        registerChartPlugins();
        setPeriodo('current');
        bindUi();
        aplicarBusquedaToolbar();
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', refreshChartsTheme);
        }
    });
})();
