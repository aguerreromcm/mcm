<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\Herramientas as HerramientasDao;
use App\services\AuditoriaDevengoService;

require_once dirname(__DIR__) . '/../libs/mpdf/mpdf.php';

class Herramientas extends Controller
{
    private const MAX_IMAGENES_PROCESO = 15;

    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    /**
     * Vista Auditoría Devengo (buscar faltantes, procesar individual/masivo).
     */
    public function AuditoriaDevengo()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->configuraTabla}
                {$this->confirmarMovimiento}

                const idTablaAuditoria = "muestra-auditoria-devengo";
                var datosActuales = [];

                function toYMD(dateStr) {
                    if (!dateStr) return "";
                    var m = moment(dateStr, ["DD/MM/YYYY", "YYYY-MM-DD"]);
                    return m.isValid() ? m.format("YYYY-MM-DD") : "";
                }

                // Sistema de mensajes visuales
                function mostrarMensaje(tipo, mensaje, duracion = 5000) {
                    // Remover mensajes anteriores
                    $('.mensaje-sistema').remove();

                    var claseTipo = '';
                    var iconoTipo = '';
                    switch(tipo) {
                        case 'success':
                            claseTipo = 'alert-success';
                            iconoTipo = '✅';
                            break;
                        case 'error':
                            claseTipo = 'alert-danger';
                            iconoTipo = '❌';
                            break;
                        case 'warning':
                            claseTipo = 'alert-warning';
                            iconoTipo = '⚠️';
                            break;
                        case 'info':
                        default:
                            claseTipo = 'alert-info';
                            iconoTipo = 'ℹ️';
                            break;
                    }

                    var mensajeDiv = $('<div class="alert ' + claseTipo + ' alert-dismissible mensaje-sistema" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' +
                        '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                        '<strong>' + iconoTipo + ' ' + mensaje + '</strong>' +
                        '</div>');

                    $('body').append(mensajeDiv);

                    // Auto-ocultar después de la duración especificada
                    if (duracion > 0) {
                        setTimeout(function() {
                            mensajeDiv.fadeOut(500, function() { $(this).remove(); });
                        }, duracion);
                    }

                    // Permitir cerrar manualmente
                    mensajeDiv.find('.close').click(function() {
                        mensajeDiv.fadeOut(300, function() { $(this).remove(); });
                    });
                }

                function consultarDevengosFaltantes() {
                    var credito = $("#filtro_credito").val() ? $("#filtro_credito").val().trim() : "";
                    var ciclo = $("#filtro_ciclo").val() ? $("#filtro_ciclo").val().trim() : "";
                    var params = [];
                    if (credito) params.push("credito=" + encodeURIComponent(credito));
                    if (ciclo) params.push("ciclo=" + encodeURIComponent(ciclo));
                    var url = "/Herramientas/GetDevengosFaltantes/" + (params.length ? "?" + params.join("&") : "");

                    swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                    $.ajax({
                        type: "GET",
                        url: url,
                        timeout: 120000,
                        success: function(res) {
                            swal.close();
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                                mostrarMensaje('error', "Error al procesar la respuesta");
                                actualizaDatosTabla(idTablaAuditoria, []);
                                datosActuales = [];
                                return;
                            }
                            if (!res.success) {
                                mostrarMensaje('error', res.mensaje || "Error al cargar");
                                actualizaDatosTabla(idTablaAuditoria, []);
                                datosActuales = [];
                                return;
                            }
                            datosActuales = Array.isArray(res.datos) ? res.datos : [];
                            var rows = datosActuales.map(function(item, idx) {
                                var c = String(item.CREDITO || item.credito || "").trim();
                                var ci = String(item.CICLO || item.ciclo || "").trim();
                                var chk = '<input type="checkbox" class="chk-procesar" data-index="' + idx + '">';
                                var btn = '<button type="button" class="btn btn-primary btn-sm btn-procesar" data-index="' + idx + '">Procesar</button>';
                                return [chk, c, ci, item.FECHA_FALTANTE || "", item.FECHA_CALC || "", item.NOMBRE || "", btn];
                            });
                            var tabla = $("#" + idTablaAuditoria).DataTable();
                            tabla.clear();
                            if (rows.length) {
                                tabla.rows.add(rows).draw();
                            } else {
                                tabla.draw();
                                if (credito || ciclo) {
                                    mostrarMensaje('info', "No se encontraron devengos faltantes para los filtros aplicados.");
                                }
                            }
                        },
                        error: function() {
                            swal.close();
                            mostrarMensaje('error', "La consulta tardó demasiado o hubo un error.");
                            actualizaDatosTabla(idTablaAuditoria, []);
                            datosActuales = [];
                        }
                    });
                }

                // Función para eliminar filas del grid por crédito y ciclo
                function eliminarFilaPorCreditoCiclo(credito, ciclo) {
                    var tabla = $("#" + idTablaAuditoria).DataTable();
                    var filasVisibles = tabla.rows().nodes();

                    // Buscar filas que coincidan con el crédito y ciclo
                    $(filasVisibles).each(function(index, tr) {
                        var celdas = $(tr).find('td');
                        if (celdas.length >= 3) {
                            var creditoFila = $(celdas[1]).text().trim();
                            var cicloFila = $(celdas[2]).text().trim();

                            if (creditoFila === credito && cicloFila === ciclo) {
                                // Eliminar la fila de la tabla
                                tabla.row(tr).remove().draw();

                                // También actualizar el array de datosActuales
                                if (Array.isArray(datosActuales)) {
                                    datosActuales = datosActuales.filter(function(item) {
                                        if (!item) return false;
                                        var c = String(item.CREDITO || item.credito || "").trim();
                                        var ci = String(item.CICLO || item.ciclo || "").trim();
                                        return !(c === credito && ci === ciclo);
                                    });
                                }
                                return false; // Salir del each
                            }
                        }
                    });
                }

                function procesarIndividual(idx, btn) {
                    var fila = Array.isArray(datosActuales) && datosActuales[idx] ? datosActuales[idx] : null;
                    if (!fila) { mostrarMensaje('error', "No se pudo obtener la fila a procesar."); return; }
                    swal({ title: "¿Deseas procesar este devengo?", icon: "warning", buttons: ["No", "Sí"], dangerMode: true }).then(function(ok) {
                        if (!ok) return;
                        btn.prop("disabled", true);
                        swal({ text: "Procesando...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                        $.ajax({
                            type: "POST",
                            url: "/Herramientas/ProcesarIndividual/",
                            contentType: "application/json",
                            data: JSON.stringify({ fila: fila }),
                            success: function(res) {
                                swal.close();
                                btn.prop("disabled", false);
                                try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { mostrarMensaje('error', 'Error al procesar la respuesta'); return; }

                                if (res.success) {
                                    // Determinar el tipo de mensaje basado en insertados
                                    var tipoMensaje = res.insertados > 0 ? 'success' : 'warning';
                                    mostrarMensaje(tipoMensaje, res.mensaje);

                                    // Si se insertaron registros, eliminar la fila del grid
                                    if (res.insertados > 0 && res.credito && res.ciclo) {
                                        eliminarFilaPorCreditoCiclo(res.credito, res.ciclo);
                                    }
                                } else {
                                    mostrarMensaje('error', res.mensaje || "Error al procesar el crédito");
                                }
                            },
                            error: function() {
                                swal.close();
                                btn.prop("disabled", false);
                                mostrarMensaje('error', "Error de conexión o tiempo agotado.");
                            }
                        });
                    });
                }

                function procesarMasivo() {
                    var checked = $("#" + idTablaAuditoria).find(".chk-procesar:checked");
                    if (!checked.length) {
                        mostrarMensaje("warning", "Selecciona al menos un registro.");
                        return;
                    }
                    var registros = [];
                    var filasSeleccionadas = []; // Guardar las filas seleccionadas
                    checked.each(function() {
                        var idx = $(this).data("index");
                        if (typeof idx !== "undefined" && Array.isArray(datosActuales) && datosActuales[idx]) {
                            registros.push(datosActuales[idx]);
                            filasSeleccionadas.push($(this).closest('tr'));
                        }
                    });
                    if (!registros.length) { mostrarMensaje("error", "No se pudieron obtener los registros seleccionados."); return; }
                    swal({ title: "Se procesarán " + registros.length + " registros. ¿Continuar?", icon: "warning", buttons: ["No", "Sí"], dangerMode: true }).then(function(ok) {
                        if (!ok) return;
                        var btnMasivo = $("#btn_masivo");
                        btnMasivo.prop("disabled", true);
                        swal({ text: "Procesando " + registros.length + " registro(s)...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                        $.ajax({
                            type: "POST",
                            url: "/Herramientas/ProcesarMasivo/",
                            contentType: "application/json",
                            data: JSON.stringify({ registros: registros }),
                            timeout: 300000,
                            success: function(res) {
                                swal.close();
                                btnMasivo.prop("disabled", false);
                                try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { mostrarMensaje('error', 'Error al procesar la respuesta'); return; }

                                if (res.success) {
                                    // Determinar el tipo de mensaje basado en insertados
                                    var tipoMensaje = res.insertados > 0 ? 'success' : 'warning';
                                    mostrarMensaje(tipoMensaje, res.mensaje);

                                    // Si se insertaron registros, eliminar las filas que estaban seleccionadas
                                    if (res.insertados > 0 && filasSeleccionadas.length > 0) {
                                        // Remover las filas que estaban seleccionadas (procesadas)
                                        var tabla = $("#" + idTablaAuditoria).DataTable();
                                        filasSeleccionadas.forEach(function(tr) {
                                            tabla.row(tr).remove();
                                        });
                                        tabla.draw();

                                        // Actualizar el array datosActuales removiendo los elementos procesados
                                        if (Array.isArray(datosActuales)) {
                                            var indicesAEliminar = [];
                                            filasSeleccionadas.forEach(function(tr) {
                                                var checkbox = $(tr).find('.chk-procesar');
                                                var idx = checkbox.data('index');
                                                if (idx !== undefined) {
                                                    indicesAEliminar.push(idx);
                                                }
                                            });
                                            datosActuales = datosActuales.filter(function(item, idx) {
                                                return indicesAEliminar.indexOf(idx) === -1;
                                            });
                                        }
                                    }
                                } else {
                                    mostrarMensaje('error', res.mensaje || "Error en procesamiento masivo");
                                }
                            },
                            error: function() {
                                swal.close();
                                btnMasivo.prop("disabled", false);
                                mostrarMensaje('error', "Error de conexión o tiempo agotado.");
                            }
                        });
                    });
                }

                $(document).ready(function(){
                    // Evitar doble inicialización: la vista ya puede haber inicializado la DataTable
                    if (!$.fn.DataTable.isDataTable("#" + idTablaAuditoria)) {
                        $("#" + idTablaAuditoria).DataTable({
                            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
                            order: [[1, "asc"]],
                            columnDefs: [
                                { orderable: false, targets: [0, 6] },
                                { targets: 0, createdCell: function(td, cellData) { $(td).html(cellData || ''); } },
                                { targets: 6, createdCell: function(td, cellData) { $(td).html(cellData || ''); } }
                            ],
                            language: {
                                emptyTable: "Aplique filtros y pulse Consultar para buscar devengos faltantes.",
                                paginate: { previous: "Anterior", next: "Siguiente" },
                                info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                                zeroRecords: "No se encontraron registros",
                                lengthMenu: "Mostrar _MENU_ registros",
                                search: "Buscar:"
                            }
                        });
                    }

                    $("#btn_consultar").click(consultarDevengosFaltantes);
                    $("#btn_masivo").click(procesarMasivo);

                    $(document).on("click", ".btn-procesar", function() {
                        var btn = $(this);
                        if (btn.prop("disabled")) return;
                        var idx = btn.data("index");
                        if (typeof idx === "undefined") { mostrarMensaje('error', "No se pudo obtener la fila."); return; }
                        procesarIndividual(idx, btn);
                    });
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Auditoría Devengo")));
        View::set('footer', $this->_contenedor->footer());
        View::set('tabla', '');
        View::render('herramientas_auditoria_devengo');
    }

    /**
     * JSON: devengos faltantes.
     */
    public function GetDevengosFaltantes()
    {
        try {
            set_time_limit(120);
            header('Content-Type: application/json; charset=UTF-8');
            $fechaCorte = isset($_GET['fecha_corte']) ? trim($_GET['fecha_corte']) : null;
            if ($fechaCorte !== null && $fechaCorte !== '') {
                $d = \DateTime::createFromFormat('Y-m-d', $fechaCorte);
                if (!$d) {
                    $d = \DateTime::createFromFormat('d/m/Y', $fechaCorte);
                    if ($d) $fechaCorte = $d->format('Y-m-d');
                    else $fechaCorte = null;
                }
            }
            if (empty($fechaCorte)) {
                $fechaCorte = date('Y-m-d');
            }
            $hoy = date('Y-m-d');
            if ($fechaCorte > $hoy) {
                $fechaCorte = $hoy;
            }
            $datos = [
                'credito'      => isset($_GET['credito']) ? trim($_GET['credito']) : null,
                'ciclo'        => isset($_GET['ciclo']) ? trim($_GET['ciclo']) : null,
                'fecha_corte'  => $fechaCorte,
            ];

            $credito = (string) ($datos['credito'] ?? '');
            $ciclo = (string) ($datos['ciclo'] ?? '');
            if ($credito === '' && $ciclo === '') {
                echo json_encode(\Core\Model::Responde(false, 'Captura al menos un filtro: crédito o ciclo.'));
                return;
            }
            if ($credito !== '' && !ctype_digit($credito)) {
                echo json_encode(\Core\Model::Responde(false, 'El crédito debe contener solo números.'));
                return;
            }
            if ($ciclo !== '' && !ctype_digit($ciclo)) {
                echo json_encode(\Core\Model::Responde(false, 'El ciclo debe contener solo números.'));
                return;
            }

            $resp = AuditoriaDevengoService::GetDevengosFaltantes($datos);
            echo json_encode($resp);
        } catch (\Throwable $e) {
            // Log the error for debugging
            @file_put_contents(APPPATH . '/../logs/auditoria_devengo_error.log', date('c') . " GETDevengos Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
            echo json_encode(\Core\Model::Responde(false, 'Ocurrió un error al obtener los devengos faltantes.', null, $e->getMessage()));
        }
    }

    /**
     * JSON: procesamiento individual.
     */
    public function ProcesarIndividual()
    {
        $log = APPPATH . '/../logs/auditoria_devengo_proceso.log';
        try {
            header('Content-Type: application/json; charset=UTF-8');
            $raw = file_get_contents('php://input');
            $datos = json_decode($raw, true) ?: [];
            $usuario = $this->__usuario ?? '';
            $perfil = $this->__perfil ?? '';
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';

            @file_put_contents($log, date('c') . " [CTRL] ProcesarIndividual ENTRADA raw=" . substr($raw, 0, 200) . " | datos=" . json_encode($datos) . " | usuario=$usuario | perfil=$perfil\n", FILE_APPEND);

            $resp = AuditoriaDevengoService::ProcesarIndividual($datos, $usuario, $perfil, $ip);

            @file_put_contents($log, date('c') . " [CTRL] ProcesarIndividual SALIDA success=" . ($resp['success'] ? 'true' : 'false') . " | mensaje=" . ($resp['mensaje'] ?? '') . "\n", FILE_APPEND);
            echo json_encode($resp);
        } catch (\Throwable $e) {
            @file_put_contents($log, date('c') . " [CTRL] ProcesarIndividual EXCEPCION: " . $e->getMessage() . "\n", FILE_APPEND);
            @file_put_contents(APPPATH . '/../logs/auditoria_devengo_error.log', date('c') . " ProcesarIndividual Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
            echo json_encode(\Core\Model::Responde(false, 'Ocurrió un error al procesar el devengo individual.', null, $e->getMessage()));
        }
    }

    /**
     * Vista del monitor de Estatus de Base de Datos.
     * Al cargar dispara la consulta inicial vía AJAX y permite refrescar con un botón.
     */
    public function EstatusBD()
    {
        $extraFooter = <<<HTML
        <script>
            {$this->mensajes}
            (function () {
                const PCT_VERDE   = 85;  // &lt; 85 % verde
                const PCT_AMAR_90 = 90;  // 85&ndash;90 % amarillo; &gt; 90 rojo
                const URL_ESTATUS = "/Herramientas/GetEstatusBD/";

                function hEsc(s) {
                    return \$("<div>").text(s === null || s === undefined ? "" : String(s)).html();
                }
                function gridMetricasFra(lim, u, re) {
                    const cell = function (lbl, v) {
                        return "<div class=\"estatus-metrica\">"
                            + "<span class=\"estatus-metrica-lbl\">" + lbl + "</span>"
                            + "<span class=\"estatus-metrica-val\">" + hEsc(v) + "</span></div>";
                    };
                    return "<div class=\"estatus-metricas-grid\" role=\"group\" aria-label=\"FRA\">"
                        + cell("L" + "\u00ed" + "mite", lim) + cell("Usado", u) + cell("Reutilizable", re)
                        + "</div>";
                }

                function classByPct(pct) {
                    if (pct === null || pct === undefined || pct === "") return "estatus-warn";
                    const p = Number(pct);
                    if (!isFinite(p)) return "estatus-warn";
                    if (p < PCT_VERDE) return "estatus-ok";
                    if (p <= PCT_AMAR_90) return "estatus-warn";
                    return "estatus-error";
                }

                function kpiClassFromArchiveStatus(status) {
                    const u = String(status || "").toUpperCase();
                    if (u === "VALID") return "estatus-ok";
                    if (u === "SIN DATO") return "estatus-warn";
                    return "estatus-error";
                }

                function pintaArchive(prefix, info) {
                    const \$kpi    = $("#" + prefix + "-kpi-wrap");
                    const \$status = $("#" + prefix + "-archive-status");
                    const \$ico     = $("#" + prefix + "-archive-ico");
                    const \$err     = $("#" + prefix + "-archive-error");

                    \$kpi.removeClass("estatus-ok estatus-warn estatus-error");
                    \$err.removeClass("estatus-alert--sql").hide().empty();

                    if (info.archive_error) {
                        \$kpi.addClass("estatus-error");
                        \$ico.html("\u274C");
                        \$status.text("Error de consulta");
                        \$err.addClass("estatus-alert--sql");
                        \$err.append(\$("<span class=\"estatus-alert-cuerpo\">").text((info.archive_error || "").trim()));
                        \$err.show();
                        return false;
                    }
                    const archive  = info.archive_dest || {};
                    const status   = (archive.STATUS || archive.status || "SIN DATO").toString();
                    const errorMsg = (archive.ERROR || archive.error || "").toString();
                    const stUp  = String(status).toUpperCase();
                    const ok    = stUp === "VALID";
                    const kCls  = kpiClassFromArchiveStatus(status);
                    \$kpi.addClass(kCls);
                    if (kCls === "estatus-ok") {
                        \$ico.html("\u2714");
                    } else if (kCls === "estatus-warn") {
                        \$ico.html("\u26A0\ufe0f");
                    } else {
                        \$ico.html("\u2715");
                    }
                    \$status.text(stUp === "VALID" ? "Sincronizado" : stUp);
                    if (errorMsg) {
                        \$err.append(\$("<span class=\"estatus-alert-cuerpo\">").text(errorMsg));
                        \$err.show();
                    }
                    return ok;
                }

                function pintaRecovery(prefix, info) {
                    const \$metric = $("#" + prefix + "-rec-metricas");
                    const \$pct    = $("#" + prefix + "-rec-pct");
                    const \$bar     = $("#" + prefix + "-rec-bar");
                    const \$err    = $("#" + prefix + "-rec-error");

                    \$err.removeClass("estatus-alert--sql").hide().empty();

                    if (info.recovery_error) {
                        const nd = "\u2013";
                        \$metric.html(
                            "<div class=\"estatus-metricas-grid estatus-metricas-grid--error\" role=\"group\">"
                            + "<div class=\"estatus-metrica\"><span class=\"estatus-metrica-lbl\">" + "L" + "\u00ed" + "mite" + "</span>"
                            + "<span class=\"estatus-metrica-val estatus-metrica-val--muted\">" + nd + "</span></div>"
                            + "<div class=\"estatus-metrica\"><span class=\"estatus-metrica-lbl\">Usado</span>"
                            + "<span class=\"estatus-metrica-val estatus-metrica-val--muted\">" + nd + "</span></div>"
                            + "<div class=\"estatus-metrica\"><span class=\"estatus-metrica-lbl\">Reutilizable</span>"
                            + "<span class=\"estatus-metrica-val estatus-metrica-val--muted\">" + nd + "</span></div></div>"
                        );
                        \$pct.text("\u2013");
                        \$bar.css("width", "0%").removeClass().addClass("estatus-bar estatus-error");
                        \$err.addClass("estatus-alert--sql");
                        \$err.append(\$("<span class=\"estatus-alert-cuerpo\">").text((info.recovery_error || "").trim()));
                        \$err.show();
                        return null;
                    }
                    const r = info.recovery_file_dest || {};
                    const limite   = r.LIMITE_GB ?? r.limite_gb ?? null;
                    const usado    = r.USADO_GB ?? r.usado_gb ?? null;
                    const reusable = r.REUTILIZABLE_GB ?? r.reutilizable_gb ?? null;
                    const pct      = r.USADO_PCT ?? r.usado_pct ?? null;

                    const lim = limite === null || limite === "" ? "\u2013" : (String(limite) + " GB");
                    const u   = usado === null || usado === "" ? "\u2013" : (String(usado) + " GB");
                    const re  = reusable === null || reusable === "" ? "\u2013" : (String(reusable) + " GB");
                    \$metric.html(gridMetricasFra(lim, u, re));
                    const pctText = pct === null || pct === "" ? "\u2013" : (Number(pct) + " %");
                    \$pct.text(pctText);
                    const cls   = classByPct(pct);
                    const ancho = pct === null || pct === "" ? 0 : Math.min(100, Math.max(0, Number(pct)));
                    \$bar.css("width", ancho + "%").removeClass().addClass("estatus-bar " + cls);
                    return cls;
                }

                function pintaBase(prefix, info) {
                    const archiveOk = pintaArchive(prefix, info);
                    const recCls    = pintaRecovery(prefix, info);
                    const \$card     = $("#" + prefix + "-card");

                    let borde = "border-ok";
                    if (!archiveOk || info.archive_error || info.recovery_error || recCls === "estatus-error") {
                        borde = "border-error";
                    } else if (recCls === "estatus-warn") {
                        borde = "border-warn";
                    }
                    \$card.removeClass("border-ok border-warn border-error").addClass(borde);
                }

                function consultar() {
                    $("#btn-actualizar").prop("disabled", true);
                    $("#estatus-cargando").show();
                    $("#estatus-actualizado").text("").removeClass("is-live");

                    $.ajax({
                        type: "GET",
                        url: URL_ESTATUS,
                        timeout: 120000,
                        cache: false,
                        success: function (res) {
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { res = null; }
                            if (!res || !res.success) {
                                showError((res && res.mensaje) || "No se pudo obtener el estatus de las bases.");
                                return;
                            }
                            const datos = res.datos || {};
                            if (datos.DB_CULTIVA) pintaBase("cultiva", datos.DB_CULTIVA);
                            if (datos.DB_MCM)     pintaBase("mcm", datos.DB_MCM);
                            const ts = (datos.consultado_en || "").toString();
                            $("#estatus-actualizado").text(ts ? "Actualizado: " + ts : "").addClass("is-live");
                        },
                        error: function () {
                            showError("Error al consultar el estatus de las bases. Intente de nuevo.");
                        },
                        complete: function () {
                            $("#btn-actualizar").prop("disabled", false);
                            $("#estatus-cargando").hide();
                        }
                    });
                }

                $(document).ready(function () {
                    $("#btn-actualizar").on("click", consultar);
                    consultar();
                });
            })();
        </script>
        HTML;
        View::set('header', $this->_contenedor->header($this->GetExtraHeader("Estatus BD")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('herramientas_estatus_bd');
    }

    /**
     * Devuelve el estatus actual de las bases (DB_CULTIVA y DB_MCM) en JSON.
     */
    public function GetEstatusBD()
    {
        set_time_limit(120);
        header('Content-Type: application/json; charset=UTF-8');
        try {
            echo json_encode(HerramientasDao::GetEstatusBD());
        } catch (\Throwable $e) {
            echo json_encode(\Core\Model::Responde(false, 'Ocurrió un error al consultar el estatus de las bases.', null, $e->getMessage()));
        }
    }

    /**
     * JSON: procesamiento masivo.
     */
    public function ProcesarMasivo()
    {
        try {
            set_time_limit(600);
            header('Content-Type: application/json; charset=UTF-8');
            $raw = file_get_contents('php://input');
            $body = json_decode($raw, true) ?: [];
            $registros = $body['registros'] ?? [];
            $usuario = $this->__usuario ?? '';
            $perfil = $this->__perfil ?? '';
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';

            $resp = AuditoriaDevengoService::ProcesarMasivo($registros, $usuario, $perfil, $ip);
            echo json_encode($resp);
        } catch (\Throwable $e) {
            @file_put_contents(APPPATH . '/../logs/auditoria_devengo_error.log', date('c') . " ProcesarMasivo Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);
            echo json_encode(\Core\Model::Responde(false, 'Ocurrió un error al procesar devengos masivos.', null, $e->getMessage()));
        }
    }

    /**
     * Formulario de solicitud de software corporativo (creación, modificación, corrección).
     * Diseñado para usuarios sin conocimiento técnico.
     */
    public function SolicitudSoftware()
    {
        $folio = 'SS-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 5));
        $fecha = date('d/m/Y');

        View::set('nombre', $this->__nombre ?? '');
        View::set('sucursal', $this->__cdgco ?? '');
        View::set('folio', $folio);
        View::set('fecha', $fecha);
        View::set('catalogo_puestos', $this->getCatalogoPuestosSolicitudSoftware());
        View::set('catalogo_areas', $this->getCatalogoAreasSolicitudSoftware());
        View::set('catalogo_sucursales', HerramientasDao::getSucursalesSolicitudSoftware());

        $extraCss = '<link href="/css/solicitud-software.css" rel="stylesheet">';
        $extraFooter = $this->getSolicitudSoftwareScripts();

        View::set('header', $this->_contenedor->header($this->GetExtraHeader('Solicitud de Software', [$extraCss])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('herramientas_solicitud_software');
    }

    /**
     * Catálogo de puestos para el formulario de solicitud de software.
     */
    private function getCatalogoPuestosSolicitudSoftware(): array
    {
        return [
            'Gerente de sucursal',
            'Subgerente',
            'Cajero(a)',
            'Analista',
            'Supervisor(a)',
            'Coordinador(a)',
            'Asistente administrativo(a)',
            'Director(a)',
            'Auxiliar',
            'Agente de call center',
            'Otro',
        ];
    }

    /**
     * Catálogo de áreas para el formulario de solicitud de software.
     */
    private function getCatalogoAreasSolicitudSoftware(): array
    {
        return [
            'Operaciones',
            'Finanzas',
            'Crédito',
            'Cobranza',
            'Call Center',
            'Sistemas',
            'Recursos Humanos',
            'Contabilidad',
            'Auditoría',
            'Comercial',
            'Legal',
            'Administración',
            'Otro',
        ];
    }

    /**
     * Genera PDF de la solicitud de software a partir de los datos del formulario.
     */
    public function PdfSolicitudSoftware()
    {
        try {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $raw = file_get_contents('php://input');
            $datos = json_decode($raw, true) ?: [];
            $datos = $this->normalizarCatalogosSolicitudSoftware($datos);

            $errores = $this->validarDatosSolicitudSoftware($datos);
            if (!empty($errores)) {
                header('Content-Type: application/json; charset=UTF-8');
                http_response_code(422);
                echo json_encode(\Core\Model::Responde(false, implode(' ', $errores)));
                return;
            }

            $html = $this->buildHtmlPdfSolicitudSoftware($datos);
            $nombreArchivo = 'Solicitud_Software_' . preg_replace('/[^A-Za-z0-9_-]/', '', $datos['folio'] ?? date('Ymd'));

            set_time_limit(180);

            $mpdf = new \mPDF([
                'mode' => 'utf-8',
                'format' => 'Letter',
                'default_font_size' => 10,
                'default_font' => 'Arial',
                'margin_left' => 14,
                'margin_right' => 14,
                'margin_top' => 16,
                'margin_bottom' => 18,
                'margin_header' => 8,
                'margin_footer' => 8,
            ]);
            $mpdf->shrink_tables_to_fit = 1;
            $mpdf->use_kwt = false;

            $fi = date('d/m/Y H:i:s');
            $usuario = htmlspecialchars($this->__usuario ?? '', ENT_QUOTES, 'UTF-8');
            $folioPie = htmlspecialchars($datos['folio'] ?? '', ENT_QUOTES, 'UTF-8');

            $mpdf->SetHTMLHeader(<<<HTML
            <table width="100%" style="border-bottom:1px solid #2a3f54;font-size:8pt;color:#555;">
                <tr>
                    <td width="60%" style="padding-bottom:4px;">Solicitud de Software</td>
                    <td width="40%" style="text-align:right;padding-bottom:4px;">Folio: <strong>{$folioPie}</strong></td>
                </tr>
            </table>
            HTML);

            $pie = <<<HTML
            <table width="100%" style="font-size:8pt;color:#666;border-top:1px solid #ddd;">
                <tr>
                    <td width="55%">Generado: {$fi} &nbsp;|&nbsp; Usuario: {$usuario}</td>
                    <td width="45%" style="text-align:right;">Página {PAGENO} de {nb}</td>
                </tr>
            </table>
            HTML;

            $mpdf->SetHTMLFooter($pie);
            $mpdf->SetTitle('Solicitud de Software');
            $mpdf->WriteHTML($html['style'], 1);
            $mpdf->WriteHTML($html['body'], 2);
            $mpdf->Output($nombreArchivo . '.pdf', 'D');
            exit;
        } catch (\Throwable $e) {
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            header('Content-Type: application/json; charset=UTF-8');
            http_response_code(500);
            echo json_encode(\Core\Model::Responde(false, 'No se pudo generar el PDF.', null, $e->getMessage()));
        }
    }

    private function getSolicitudSoftwareScripts(): string
    {
        $maxImagenes = self::MAX_IMAGENES_PROCESO;

        return <<<HTML
        <script>
        {$this->mensajes}
        (function () {
            const URL_PDF = "/Herramientas/PdfSolicitudSoftware/";
            const MAX_IMAGENES = {$maxImagenes};
            const MAX_BYTES_IMAGEN = 2 * 1024 * 1024;
            let imagenSeleccionada = null;

            function getEditorProceso() {
                return document.getElementById("ss-proceso-actual");
            }

            function contarImagenesEditor() {
                return \$(getEditorProceso()).find("img").length;
            }

            function deseleccionarImagen() {
                if (imagenSeleccionada) {
                    \$(imagenSeleccionada).removeClass("ss-img-seleccionada");
                    imagenSeleccionada = null;
                }
            }

            function seleccionarImagen(img) {
                deseleccionarImagen();
                imagenSeleccionada = img;
                \$(img).addClass("ss-img-seleccionada");
            }

            function insertarImagenEnEditor(dataUrl) {
                const editor = getEditorProceso();
                if (!editor) return;
                if (contarImagenesEditor() >= MAX_IMAGENES) {
                    showWarning("Ya agregó el máximo de " + MAX_IMAGENES + " imágenes.");
                    return;
                }
                editor.focus();
                const img = document.createElement("img");
                img.src = dataUrl;
                img.alt = "Imagen del proceso";
                const wrap = document.createElement("div");
                wrap.style.clear = "both";
                wrap.appendChild(img);
                const sel = window.getSelection();
                if (sel && sel.rangeCount > 0) {
                    const range = sel.getRangeAt(0);
                    if (!editor.contains(range.commonAncestorContainer)) {
                        range.selectNodeContents(editor);
                        range.collapse(false);
                    }
                    range.collapse(false);
                    range.insertNode(document.createElement("br"));
                    range.collapse(false);
                    range.insertNode(wrap);
                    range.setStartAfter(wrap);
                    range.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(range);
                } else {
                    editor.appendChild(document.createElement("br"));
                    editor.appendChild(wrap);
                }
                editor.appendChild(document.createElement("br"));
            }

            function procesarArchivoImagen(file) {
                if (!file) return;
                if (!/^image\\/(jpeg|png|gif|webp)\$/i.test(file.type)) {
                    showWarning("Solo se permiten imágenes JPG, PNG, GIF o WEBP.");
                    return;
                }
                if (file.size > MAX_BYTES_IMAGEN) {
                    showWarning('La imagen "' + file.name + '" supera 2 MB.');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (ev) {
                    insertarImagenEnEditor(ev.target.result);
                };
                reader.readAsDataURL(file);
            }

            function initEditorProceso() {
                const editor = getEditorProceso();
                if (!editor) return;

                editor.addEventListener("paste", function (e) {
                    const items = e.clipboardData ? e.clipboardData.items : null;
                    if (!items) return;
                    for (let i = 0; i < items.length; i++) {
                        if (items[i].type && items[i].type.indexOf("image") !== -1) {
                            e.preventDefault();
                            procesarArchivoImagen(items[i].getAsFile());
                            return;
                        }
                    }
                });

                editor.addEventListener("click", function (e) {
                    if (e.target && e.target.tagName === "IMG") {
                        seleccionarImagen(e.target);
                    } else {
                        deseleccionarImagen();
                    }
                });

                editor.addEventListener("keydown", function (e) {
                    if ((e.key === "Delete" || e.key === "Backspace") && imagenSeleccionada) {
                        e.preventDefault();
                        \$(imagenSeleccionada).remove();
                        deseleccionarImagen();
                    }
                });
            }

            function getFormData() {
                const f = document.getElementById("form-solicitud-software");
                const fd = new FormData(f);
                const data = {};
                fd.forEach(function (v, k) { data[k] = v; });
                data.puesto = resolverValorCatalogo(data.puesto, data.puesto_otro);
                data.area = resolverValorCatalogo(data.area, data.area_otro);
                delete data.puesto_otro;
                delete data.area_otro;
                data.folio = \$("#ss-folio").text().trim();
                data.fecha = \$("#ss-fecha").text().trim();
                const editor = getEditorProceso();
                data.proceso_actual_html = editor ? editor.innerHTML : "";
                data.proceso_actual = editor ? editor.innerText.trim() : "";
                return data;
            }

            function resolverValorCatalogo(valorSelect, valorOtro) {
                const seleccion = (valorSelect || "").trim();
                if (seleccion === "Otro") {
                    return (valorOtro || "").trim();
                }
                return seleccion;
            }

            function actualizarCampoOtro(\$select) {
                const \$field = \$select.closest(".ss-field-catalogo");
                const \$otro = \$field.find(".ss-otro-input");
                if (!\$otro.length) return;
                const esOtro = \$select.val() === "Otro";
                \$otro.toggle(esOtro);
                if (!esOtro) \$otro.val("");
            }

            function ocultarCamposOtro() {
                \$(".ss-otro-input").hide().val("");
            }

            function validar() {
                let ok = true;
                \$("#ss-alert-error").hide().empty();
                \$(".ss-field.ss-error").removeClass("ss-error");
                \$("#ss-error-tipo, #ss-error-prioridad").hide();

                \$("#form-solicitud-software .ss-field[data-required='true']").each(function () {
                    const \$f = \$(this);
                    if (!isRequiredFieldComplete(\$f)) {
                        \$f.addClass("ss-error");
                        ok = false;
                    }
                });

                const correo = \$("#ss-correo").val().trim();
                if (correo && !/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+\$/.test(correo)) {
                    \$("#ss-correo").closest(".ss-field").addClass("ss-error");
                    ok = false;
                }

                if (!\$("input[name='tipo_solicitud']:checked").length) {
                    \$("#ss-error-tipo").show();
                    ok = false;
                }
                if (!\$("input[name='prioridad']:checked").length) {
                    \$("#ss-error-prioridad").show();
                    ok = false;
                }

                if (!ok) {
                    \$("#ss-alert-error").html("<strong>Por favor revise el formulario.</strong> Los campos marcados con * son obligatorios.").show();
                    const first = \$(".ss-field.ss-error, #ss-error-tipo:visible, #ss-error-prioridad:visible").first();
                    if (first.length) {
                        \$("html, body").animate({ scrollTop: first.offset().top - 100 }, 400);
                    }
                }
                return ok;
            }

            function descargarBlob(blob, nombre) {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                a.download = nombre;
                a.style.display = "none";
                document.body.appendChild(a);
                a.click();
                setTimeout(function () {
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                }, 200);
            }

            function procesarRespuestaPdf(blob, folio) {
                if (!(blob instanceof Blob)) {
                    showError("Error al generar PDF");
                    return;
                }
                const ct = (blob.type || "").toLowerCase();
                if (ct.indexOf("json") !== -1 || ct.indexOf("text") !== -1 || blob.size < 200) {
                    const reader = new FileReader();
                    reader.onload = function () {
                        try {
                            const res = JSON.parse(reader.result);
                            showError(res.mensaje || "Error al generar PDF");
                        } catch (e) {
                            showError("Error al generar PDF");
                        }
                    };
                    reader.readAsText(blob);
                    return;
                }
                descargarBlob(blob, "Solicitud_Software_" + folio + ".pdf");
                showSuccess("PDF generado correctamente.");
            }

            function generarPdf() {
                if (!validar()) return;
                const data = getFormData();
                showWait("Generando PDF, espere un momento...");

                const xhr = new XMLHttpRequest();
                xhr.open("POST", URL_PDF, true);
                xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                xhr.responseType = "blob";
                xhr.timeout = 180000;

                xhr.onload = function () {
                    swal.close();
                    if (xhr.status >= 200 && xhr.status < 300) {
                        procesarRespuestaPdf(xhr.response, data.folio);
                        return;
                    }
                    if (xhr.response instanceof Blob) {
                        const reader = new FileReader();
                        reader.onload = function () {
                            try {
                                const res = JSON.parse(reader.result);
                                showError(res.mensaje || "Error al generar PDF");
                            } catch (e) {
                                showError("No se pudo generar el PDF.");
                            }
                        };
                        reader.readAsText(xhr.response);
                    } else {
                        showError("No se pudo generar el PDF.");
                    }
                };

                xhr.onerror = function () {
                    swal.close();
                    showError("No se pudo conectar con el servidor.");
                };

                xhr.ontimeout = function () {
                    swal.close();
                    showError("La generación del PDF tardó demasiado. Intente de nuevo.");
                };

                xhr.send(JSON.stringify(data));
            }

            function limpiarFormulario() {
                swal({ title: "¿Limpiar el formulario?", text: "Se borrarán todos los datos capturados.", icon: "warning", buttons: ["Cancelar", "Sí, limpiar"], dangerMode: true })
                    .then(function (ok) {
                        if (!ok) return;
                        document.getElementById("form-solicitud-software").reset();
                        const editor = getEditorProceso();
                        if (editor) editor.innerHTML = "";
                        deseleccionarImagen();
                        \$(".ss-option-card, .ss-priority-btn").removeClass("ss-selected");
                        \$(".ss-field.ss-error").removeClass("ss-error");
                        \$("#ss-alert-error").hide();
                        ocultarCamposOtro();
                        updateSectionStatus();
                    });
            }

            function isRequiredFieldComplete(\$field) {
                const radios = \$field.find("input[type='radio']");
                if (radios.length) {
                    return radios.is(":checked");
                }
                const \$select = \$field.find("select").first();
                if (\$select.length && \$field.hasClass("ss-field-catalogo")) {
                    const val = (\$select.val() || "").trim();
                    if (val === "Otro") {
                        return (\$field.find(".ss-otro-input").val() || "").trim() !== "";
                    }
                    return val !== "";
                }
                const input = \$field.find("input, textarea, select").first();
                if (!input.length) return false;
                const val = (input.val() || "").trim();
                if (input.is("textarea")) {
                    return val.length >= 20;
                }
                if (input.attr("type") === "email") {
                    return val !== "" && /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+\$/.test(val);
                }
                return val !== "";
            }

            function updateSectionStatus() {
                \$(".ss-section").each(function () {
                    const \$sec = \$(this);
                    const \$triggers = \$sec.find(".ss-field[data-triggers-section-complete='true']");

                    if (\$triggers.length) {
                        let hasContent = false;
                        \$triggers.each(function () {
                            const input = \$(this).find("input, textarea, select").first();
                            if (input.length && (input.val() || "").trim() !== "") {
                                hasContent = true;
                            }
                        });
                        \$sec.toggleClass("ss-section-complete", hasContent);
                        return;
                    }

                    const \$required = \$sec.find(".ss-field[data-required='true']");
                    if (!\$required.length) {
                        \$sec.removeClass("ss-section-complete");
                        return;
                    }
                    let complete = true;
                    \$required.each(function () {
                        if (!isRequiredFieldComplete(\$(this))) {
                            complete = false;
                        }
                    });
                    \$sec.toggleClass("ss-section-complete", complete);
                });
            }

            \$(document).ready(function () {
                \$(".ss-option-card input[type='radio']").on("change", function () {
                    \$(".ss-option-card").removeClass("ss-selected");
                    \$(this).closest(".ss-option-card").addClass("ss-selected");
                    updateSectionStatus();
                });
                \$(".ss-priority-btn input[type='radio']").on("change", function () {
                    \$(".ss-priority-btn").removeClass("ss-selected");
                    \$(this).closest(".ss-priority-btn").addClass("ss-selected");
                    updateSectionStatus();
                });
                \$("#ss-puesto, #ss-area").on("change", function () {
                    actualizarCampoOtro(\$(this));
                    updateSectionStatus();
                });
                \$("#form-solicitud-software input, #form-solicitud-software textarea, #form-solicitud-software select").on("input change", updateSectionStatus);
                \$("#ss-proceso-actual").on("input", updateSectionStatus);

                initEditorProceso();

                \$("#btn-ss-pdf").click(generarPdf);
                \$("#btn-ss-limpiar").click(limpiarFormulario);

                updateSectionStatus();
            });
        })();
        </script>
        HTML;
    }

    /**
     * Si el usuario eligió "Otro", usa el texto capturado en el campo adicional.
     */
    private function normalizarCatalogosSolicitudSoftware(array $datos): array
    {
        foreach (['puesto', 'area'] as $campo) {
            $otroKey = $campo . '_otro';
            if (($datos[$campo] ?? '') === 'Otro') {
                $datos[$campo] = trim((string) ($datos[$otroKey] ?? ''));
            }
            unset($datos[$otroKey]);
        }

        return $datos;
    }

    /**
     * Valida los datos mínimos de la solicitud de software.
     */
    private function validarDatosSolicitudSoftware(array $datos): array
    {
        $errores = [];
        $campos = [
            'nombre' => 'El nombre del solicitante es obligatorio.',
            'area' => 'El área o departamento es obligatorio.',
            'correo' => 'El correo electrónico es obligatorio.',
            'descripcion' => 'La descripción de la necesidad es obligatoria.',
            'beneficio' => 'Debe indicar el beneficio o razón de la solicitud.',
        ];
        foreach ($campos as $campo => $msg) {
            if (empty(trim($datos[$campo] ?? ''))) {
                $errores[] = $msg;
            }
        }
        if (!empty($datos['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido.';
        }
        if (empty($datos['tipo_solicitud'])) {
            $errores[] = 'Debe seleccionar un tipo de solicitud.';
        }
        if (empty($datos['prioridad'])) {
            $errores[] = 'Debe seleccionar una prioridad.';
        }
        if (!empty($datos['descripcion']) && mb_strlen(trim($datos['descripcion'])) < 20) {
            $errores[] = 'La descripción debe tener al menos 20 caracteres.';
        }
        return array_merge($errores, $this->validarProcesoActualHtml($datos));
    }

    /**
     * Valida HTML e imágenes embebidas del proceso actual.
     */
    private function validarProcesoActualHtml(array $datos): array
    {
        $html = trim((string) ($datos['proceso_actual_html'] ?? ''));
        if ($html === '') {
            return [];
        }

        $errores = [];
        preg_match_all('#<img\\b[^>]*>#i', $html, $matches);
        $tags = $matches[0] ?? [];

        if (count($tags) > self::MAX_IMAGENES_PROCESO) {
            $errores[] = 'Máximo ' . self::MAX_IMAGENES_PROCESO . ' imágenes permitidas en el proceso actual.';
            return $errores;
        }

        foreach ($tags as $tag) {
            if (!preg_match("/src=[\"'](data:image\\/(jpeg|jpg|png|gif|webp);base64,[^\"']+)[\"']/i", $tag, $srcMatch)) {
                $errores[] = 'Formato de imagen no válido en el proceso actual.';
                break;
            }
            $b64 = preg_replace('#^data:image/[^;]+;base64,#i', '', $srcMatch[1]);
            $b64 = str_replace(["\r", "\n", ' '], '', $b64);
            $decoded = base64_decode($b64, true);
            if ($decoded === false) {
                $errores[] = 'No se pudo leer una de las imágenes del proceso.';
                break;
            }
            if (strlen($decoded) > 2 * 1024 * 1024) {
                $errores[] = 'Cada imagen debe pesar menos de 2 MB.';
                break;
            }
        }

        return $errores;
    }

    /**
     * Sanitiza HTML del proceso actual para incluir en el PDF.
     */
    private function sanitizeProcesoActualHtml(?string $html): string
    {
        $html = trim((string) ($html ?? ''));
        if ($html === '') {
            return '';
        }

        $html = preg_replace('#<(script|style|iframe|object|embed|link|meta)[^>]*>.*?</\\1>#is', '', $html);
        $html = preg_replace('#<(script|style|iframe|object|embed|link|meta)[^>]*/?>#is', '', $html);
        $html = strip_tags($html, '<img><br><p><div><span>');
        $html = preg_replace('/ on\\w+=["\'][^"\']*["\']/i', '', $html);
        $html = $this->normalizarBloquesProcesoPdf($html);

        $imgCount = 0;
        $html = preg_replace_callback('#<img\\s+[^>]*>#i', function ($m) use (&$imgCount) {
            $tag = $m[0];
            if (!preg_match("/src=[\"'](data:image\\/(jpeg|jpg|png|gif|webp);base64,[^\"']+)[\"']/i", $tag, $srcMatch)) {
                return '';
            }
            if ($imgCount >= self::MAX_IMAGENES_PROCESO) {
                return '';
            }
            $src = preg_replace('/\s+/', '', $srcMatch[1]);
            $b64 = preg_replace('#^data:image/[^;]+;base64,#i', '', $src);
            $decoded = base64_decode(str_replace(["\r", "\n", ' '], '', $b64), true);
            if ($decoded === false || strlen($decoded) > 2 * 1024 * 1024) {
                return '';
            }
            $imgCount++;
            $styleImg = $this->estiloImagenProcesoPdf($decoded);
            return '<div style="display:block;width:100%;clear:both;margin:12px 0;text-align:center;">'
                . '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" '
                . 'style="' . $styleImg . '" alt="Imagen del proceso" />'
                . '</div>';
        }, $html);

        $plain = trim(preg_replace('#\s+#u', ' ', strip_tags($html)));
        $hasImg = stripos($html, '<img') !== false;
        if ($plain === '' && !$hasImg) {
            return '';
        }

        return '<div class="ss-pdf-proceso-contenido" style="display:block;width:100%;clear:both;">' . $html . '</div>';
    }

    /**
     * Tamaño de imagen en PDF: ancho natural hasta el ancho útil de la página (sin reducir altura).
     */
    private function estiloImagenProcesoPdf(string $binary): string
    {
        $maxWmm = 178;
        $base = 'display:block;margin:0 auto;clear:both;height:auto;';

        $info = @getimagesizefromstring($binary);
        if ($info && !empty($info[0])) {
            $wMm = ($info[0] * 25.4) / 96;
            if ($wMm > $maxWmm) {
                return $base . 'width:' . $maxWmm . 'mm;';
            }
            return $base . 'width:' . round($wMm, 1) . 'mm;';
        }

        return $base . 'width:100%;';
    }

    /**
     * Convierte el HTML del editor a bloques apilados (evita texto al lado de imágenes en mPDF).
     */
    private function normalizarBloquesProcesoPdf(string $html): string
    {
        $bloque = 'display:block;width:100%;clear:both;margin:0 0 8px 0;'
            . 'font-size:10pt;line-height:1.45;'
            . 'word-break:break-all;overflow-wrap:break-word;';

        $html = preg_replace('#</?span[^>]*>#i', '', $html);
        $html = preg_replace('#</?(table|thead|tbody|tr|td|th)[^>]*>#i', '', $html);

        $html = preg_replace('#<div[^>]*>#i', '<p style="' . $bloque . '">', $html);
        $html = preg_replace('#</div>#i', '</p>', $html);
        $html = preg_replace('#<p(?![^>]*style=)([^>]*)>#i', '<p style="' . $bloque . '"$1>', $html);
        $html = preg_replace('#<br\\s*/?>#i', '<br style="clear:both;" />', $html);

        // Texto suelto sin etiqueta (poco común)
        if ($html !== '' && $html[0] !== '<') {
            $html = '<p style="' . $bloque . '">' . $html . '</p>';
        }

        return $html;
    }

    /**
     * Bloque PDF del proceso actual a ancho completo (permite varias páginas sin encoger).
     */
    private function buildProcesoActualPdfRow(array $d, callable $texto, callable $h): string
    {
        $lbl = $h('¿Cómo lo hace hoy sin el cambio?');
        $html = $this->sanitizeProcesoActualHtml($d['proceso_actual_html'] ?? '');

        if ($html !== '') {
            $contenido = $html;
        } else {
            $contenido = '<div class="ss-pdf-proceso-contenido" style="font-size:10pt;">' . $texto($d['proceso_actual'] ?? '') . '</div>';
        }

        return '<div class="ss-pdf-proceso-bloque">'
            . '<div class="ss-pdf-proceso-lbl">' . $lbl . '</div>'
            . '<div class="ss-pdf-proceso-celda">' . $contenido . '</div>'
            . '</div>';
    }

    /**
     * Construye el HTML del PDF de solicitud de software (estilos + cuerpo separados para mPDF).
     *
     * @return array{style: string, body: string}
     */
    private function buildHtmlPdfSolicitudSoftware(array $d): array
    {
        $h = function ($v) {
            return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
        };

        $vacio = '<span class="ss-pdf-vacio">No especificado</span>';

        $texto = function ($valor) use ($h, $vacio) {
            $t = trim((string) ($valor ?? ''));
            if ($t === '') {
                return $vacio;
            }
            $t = wordwrap($t, 80, "\n", true);
            return nl2br($h($t));
        };

        $tipos = [
            'creacion' => [
                'titulo' => 'Crear algo nuevo',
                'desc' => 'Necesito un programa, reporte o herramienta que hoy no existe en la empresa.',
            ],
            'modificacion' => [
                'titulo' => 'Modificar o mejorar algo existente',
                'desc' => 'Necesito que se le agregue o cambie algo.',
            ],
            'correccion' => [
                'titulo' => 'Corregir un problema',
                'desc' => 'Algo no funciona bien, da error o muestra información incorrecta.',
            ],
            'actualizacion' => [
                'titulo' => 'Actualizar información o datos',
                'desc' => 'Necesito cambiar textos, catálogos, listas o datos que ya están en el sistema.',
            ],
        ];

        $prioridades = [
            'urgente' => ['titulo' => 'Urgente', 'desc' => 'Afecta operación diaria', 'color' => '#c0392b'],
            'normal' => ['titulo' => 'Normal', 'desc' => 'Importante pero no bloquea', 'color' => '#d68910'],
            'baja' => ['titulo' => 'Puede esperar', 'desc' => 'Mejora deseable a futuro', 'color' => '#1e8449'],
        ];

        $tipoKey = $d['tipo_solicitud'] ?? '';
        $prioridadKey = $d['prioridad'] ?? '';

        $folio = $h($d['folio'] ?? '');
        $fecha = $h($d['fecha'] ?? date('d/m/Y'));

        $fila = function ($label, $valor, $ancho = '35%') use ($texto, $h) {
            $lbl = $h($label);
            return '<tr>'
                . '<td class="ss-pdf-lbl" width="' . $ancho . '">' . $lbl . '</td>'
                . '<td class="ss-pdf-val">' . $texto($valor) . '</td>'
                . '</tr>';
        };

        $filaPar = function ($l1, $v1, $l2, $v2) use ($texto, $h) {
            return '<tr>'
                . '<td class="ss-pdf-lbl" width="22%">' . $h($l1) . '</td>'
                . '<td class="ss-pdf-val" width="28%">' . $texto($v1) . '</td>'
                . '<td class="ss-pdf-lbl" width="22%">' . $h($l2) . '</td>'
                . '<td class="ss-pdf-val" width="28%">' . $texto($v2) . '</td>'
                . '</tr>';
        };

        $secTitulo = function ($num, $titulo) use ($h) {
            return '<div class="ss-pdf-seccion">'
                . '<table width="100%" class="ss-pdf-seccion-hdr"><tr>'
                . '<td width="28" class="ss-pdf-num">' . (int) $num . '</td>'
                . '<td class="ss-pdf-seccion-tit">' . $h($titulo) . '</td>'
                . '</tr></table></div>';
        };

        // Opciones de tipo con marca de selección
        $tiposHtml = '';
        foreach ($tipos as $key => $info) {
            $sel = ($key === $tipoKey);
            $marca = $sel ? '&#10003;' : '&nbsp;&nbsp;';
            $cls = $sel ? 'ss-pdf-opc-sel' : 'ss-pdf-opc';
            $tiposHtml .= '<tr class="' . $cls . '">'
                . '<td width="24" class="ss-pdf-marca">' . $marca . '</td>'
                . '<td><strong>' . $h($info['titulo']) . '</strong><br>'
                . '<span class="ss-pdf-opc-desc">' . $h($info['desc']) . '</span></td>'
                . '</tr>';
        }

        // Prioridad seleccionada con detalle
        $prioridadHtml = $vacio;
        if (isset($prioridades[$prioridadKey])) {
            $p = $prioridades[$prioridadKey];
            $prioridadHtml = '<strong style="color:' . $p['color'] . ';">' . $h($p['titulo']) . '</strong>'
                . ' &mdash; ' . $h($p['desc']);
        } elseif ($prioridadKey !== '') {
            $prioridadHtml = $texto($prioridadKey);
        }

        $procesoActualRow = $this->buildProcesoActualPdfRow($d, $texto, $h);

        $style = <<<HTML
        <style>
            body { font-family: Arial, Helvetica, sans-serif; font-size: 10pt; color: #2c3e50; line-height: 1.45; }
            .ss-pdf-vacio { color: #999; font-style: italic; }
            .ss-pdf-banner { background-color: #2a3f54; color: #fff; padding: 14px 16px; margin-bottom: 10px; }
            .ss-pdf-banner-tit { font-size: 17pt; font-weight: bold; margin: 0; letter-spacing: 0.3px; }
            .ss-pdf-banner-sub { font-size: 9pt; margin: 4px 0 0; color: #bdc3c7; }
            .ss-pdf-meta { width: 100%; border: 1px solid #d5dbe0; background: #f4f6f8; margin-bottom: 14px; }
            .ss-pdf-meta td { padding: 8px 12px; font-size: 9.5pt; }
            .ss-pdf-meta-lbl { color: #666; font-size: 8.5pt; text-transform: uppercase; letter-spacing: 0.4px; }
            .ss-pdf-meta-val { font-weight: bold; color: #2a3f54; font-size: 11pt; }
            .ss-pdf-seccion { margin-top: 8px; }
            .ss-pdf-seccion-hdr { background: #2a3f54; color: #fff; border-collapse: collapse; }
            .ss-pdf-seccion-hdr td { padding: 6px 10px; vertical-align: middle; }
            .ss-pdf-num { background: rgba(255,255,255,0.15); text-align: center; font-weight: bold; font-size: 11pt; }
            .ss-pdf-seccion-tit { font-size: 11pt; font-weight: bold; }
            .ss-pdf-tabla { width: 100%; border-collapse: collapse; margin-bottom: 2px; table-layout: fixed; }
            .ss-pdf-tabla td { border: 1px solid #d5dbe0; padding: 5px 8px; vertical-align: top; }
            .ss-pdf-lbl { background: #eef1f4; font-weight: bold; font-size: 9pt; color: #444; width: 35%; }
            .ss-pdf-val {
                font-size: 10pt;
                word-wrap: break-word;
                overflow-wrap: break-word;
                word-break: break-all;
                max-width: 100%;
            }
            .ss-pdf-proceso-bloque {
                width: 100%;
                margin: 0 0 10px 0;
                page-break-inside: auto;
            }
            .ss-pdf-proceso-lbl {
                background: #eef1f4;
                border: 1px solid #d5dbe0;
                border-bottom: none;
                padding: 5px 8px;
                font-weight: bold;
                font-size: 9pt;
                color: #444;
            }
            .ss-pdf-proceso-celda {
                border: 1px solid #d5dbe0;
                padding: 8px 10px;
                font-size: 10pt;
                line-height: 1.45;
                word-break: break-all;
                overflow-wrap: break-word;
            }
            .ss-pdf-proceso-contenido {
                display: block;
                width: 100%;
                clear: both;
                line-height: 1.45;
                font-size: 10pt;
            }
            .ss-pdf-proceso-contenido p {
                display: block;
                width: 100%;
                clear: both;
                margin: 0 0 8px 0;
                font-size: 10pt;
                line-height: 1.45;
                word-break: break-all;
                overflow-wrap: break-word;
            }
            .ss-pdf-proceso-contenido img {
                display: block;
                margin: 12px auto;
                clear: both;
                height: auto;
            }
            .ss-pdf-proceso-contenido div {
                display: block;
                width: 100%;
                clear: both;
            }
            .ss-pdf-opc td { border: 1px solid #d5dbe0; padding: 6px 8px; vertical-align: top; }
            .ss-pdf-opc-sel td { border: 2px solid #2980b9; background: #ebf5fb; padding: 6px 8px; vertical-align: top; }
            .ss-pdf-marca { text-align: center; font-weight: bold; color: #2980b9; font-size: 12pt; }
            .ss-pdf-opc-desc { font-size: 8.5pt; color: #666; }
            .ss-pdf-texto-largo { min-height: 36px; }
        </style>
        HTML;

        $body = <<<HTML
        <div class="ss-pdf-banner">
            <div class="ss-pdf-banner-tit">Solicitud de Software</div>
            <div class="ss-pdf-banner-sub">Formato oficial para solicitar creación, modificación o corrección de sistemas</div>
        </div>

        <table class="ss-pdf-meta">
            <tr>
                <td width="50%">
                    <div class="ss-pdf-meta-lbl">Folio de solicitud</div>
                    <div class="ss-pdf-meta-val">{$folio}</div>
                </td>
                <td width="50%">
                    <div class="ss-pdf-meta-lbl">Fecha de captura</div>
                    <div class="ss-pdf-meta-val">{$fecha}</div>
                </td>
            </tr>
        </table>

        {$secTitulo(1, 'Datos de quien solicita')}
        <table class="ss-pdf-tabla">
            {$filaPar('Nombre completo', $d['nombre'] ?? '', 'Puesto o cargo', $d['puesto'] ?? '')}
            {$filaPar('Área o departamento', $d['area'] ?? '', 'Sucursal', $d['sucursal'] ?? '')}
            {$filaPar('Correo electrónico', $d['correo'] ?? '', 'Teléfono o extensión', $d['telefono'] ?? '')}
        </table>

        {$secTitulo(2, '¿Qué tipo de solicitud es?')}
        <table class="ss-pdf-tabla" style="margin-bottom:6px;">
            {$tiposHtml}
        </table>

        {$secTitulo(3, 'Describa lo que necesita')}
        <table class="ss-pdf-tabla">
            {$fila('Describa con sus palabras qué necesita', $d['descripcion'] ?? '')}
            {$fila('¿Qué problema resuelve o qué mejora trae?', $d['beneficio'] ?? '')}
        </table>
        {$procesoActualRow}

        {$secTitulo(4, 'Prioridad')}
        <table class="ss-pdf-tabla">
            <tr>
                <td class="ss-pdf-lbl" width="35%">¿Qué tan urgente es?</td>
                <td class="ss-pdf-val">{$prioridadHtml}</td>
            </tr>
        </table>
        HTML;

        return ['style' => $style, 'body' => $body];
    }
}
