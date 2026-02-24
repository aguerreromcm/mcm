<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use Core\App;
use App\models\Herramientas as HerramientasDao;
use App\services\AuditoriaDevengoService;

class Herramientas extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        // Solo permitir acceso si en configuracion.ini la clave es exactamente "accesoh"
        $config = App::getConfig();
        $claveHerramientas = isset($config['HERRAMIENTAS_CLAVE']) ? trim((string) $config['HERRAMIENTAS_CLAVE']) : '';
        if ($claveHerramientas !== 'accesoh') {
            header('Location: /Principal/');
            exit;
        }
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    /**
     * Vista del reporte Día de Atraso (tabla + botones Excel y CSV).
     * Los datos se cargan por AJAX. Opcional: filtrar desde mes y año.
     */
    public function RepDiaAtraso()
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $anio_actual = (int) date('Y');
        $options_mes = '<option value="">Todos</option>';
        foreach ($meses as $n => $nombre) {
            $options_mes .= '<option value="' . $n . '">' . $nombre . '</option>';
        }
        $options_anio = '<option value="">Todos</option>';
        for ($a = $anio_actual; $a >= $anio_actual - 15; $a--) {
            $options_anio .= '<option value="' . $a . '">' . $a . '</option>';
        }

        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->configuraTabla}

                const idTablaRepDia = "muestra-rep-dia-atraso";

                function consultarRepDiaAtraso() {
                    var mes = $("#filtro_mes").val();
                    var anio = $("#filtro_anio").val();
                    var params = [];
                    if (mes) params.push("mes=" + mes);
                    if (anio) params.push("anio=" + anio);
                    var url = "/Herramientas/GetRepDiaAtraso/" + (params.length ? "?" + params.join("&") : "");

                    swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                    $.ajax({
                        type: "GET",
                        url: url,
                        timeout: 600000,
                        success: function(res) {
                            swal.close();
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { showError("Error al procesar la respuesta"); actualizaDatosTabla(idTablaRepDia, []); return; }
                            if (!res.success) {
                                showError(res.mensaje || "Error al cargar el reporte");
                                actualizaDatosTabla(idTablaRepDia, []);
                                return;
                            }
                            actualizaDatosTabla(idTablaRepDia, res.datos || []);
                        },
                        error: function() {
                            swal.close();
                            showError("La consulta tardó demasiado o hubo un error. Tiempo máximo: 10 minutos.");
                            actualizaDatosTabla(idTablaRepDia, []);
                        }
                    });
                }

                $(document).ready(function(){
                    $("#" + idTablaRepDia).DataTable({
                        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
                        order: [[4, "desc"]],
                        language: {
                            emptyTable: "Seleccione filtros y pulse Consultar para cargar el reporte.",
                            paginate: { previous: "Anterior", next: "Siguiente" },
                            info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                            infoEmpty: "Sin registros",
                            zeroRecords: "No se encontraron registros",
                            lengthMenu: "Mostrar _MENU_ registros",
                            search: "Buscar:"
                        }
                    });

                    $("#btn_consultar").click(consultarRepDiaAtraso);

                    $("#btn_excel").click(function(){
                        var mes = $("#filtro_mes").val();
                        var anio = $("#filtro_anio").val();
                        var qs = (mes || anio) ? "?" + [mes ? "mes=" + mes : "", anio ? "anio=" + anio : ""].filter(Boolean).join("&") : "";
                        window.open("/Herramientas/RepDiaAtraso_excel/" + qs, "_blank");
                    });
                    $("#btn_csv").click(function(){
                        var mes = $("#filtro_mes").val();
                        var anio = $("#filtro_anio").val();
                        var qs = (mes || anio) ? "?" + [mes ? "mes=" + mes : "", anio ? "anio=" + anio : ""].filter(Boolean).join("&") : "";
                        window.location.href = "/Herramientas/RepDiaAtraso_csv/" + qs;
                    });
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Rep Dia de Atraso")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', '');
        View::set('options_mes', $options_mes);
        View::set('options_anio', $options_anio);
        View::render('herramientas_rep_dia_atraso');
    }

    /**
     * Devuelve el reporte en JSON para la carga por AJAX.
     * Parámetros opcionales GET: mes (1-12), anio (ej. 2025).
     * Tiempo máximo de ejecución: 10 minutos (para consultas pesadas).
     */
    public function GetRepDiaAtraso()
    {
        set_time_limit(600);
        $datos = array_filter([
            'mes'  => isset($_GET['mes']) ? $_GET['mes'] : null,
            'anio' => isset($_GET['anio']) ? $_GET['anio'] : null,
        ]);
        echo json_encode(HerramientasDao::GetRepDiaAtraso($datos));
    }

    /**
     * Descarga del reporte en Excel.
     */
    public function RepDiaAtraso_excel()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $centrado = ['estilo' => $estilos['centrado']];

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('COD_CTE', 'Código Cliente', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'Ciclo', $centrado),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE', 'Nombre'),
            \PHPSpreadsheet::ColumnaExcel('INICIO', 'Inicio', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('DIAS_ATRASO', 'Días Atraso', $centrado),
        ];

        $datos = array_filter([
            'mes'  => isset($_GET['mes']) ? $_GET['mes'] : null,
            'anio' => isset($_GET['anio']) ? $_GET['anio'] : null,
        ]);
        $resultado = HerramientasDao::GetRepDiaAtraso($datos);
        $filas = ($resultado['success'] && isset($resultado['datos'])) ? $resultado['datos'] : [];

        \PHPSpreadsheet::DescargaExcel('Rep Dia de Atraso', 'Reporte', 'Días de atraso', $columnas, $filas);
    }

    /**
     * Descarga del reporte en CSV.
     * Parámetros opcionales GET: mes (1-12), anio (ej. 2025).
     */
    public function RepDiaAtraso_csv()
    {
        $datos = array_filter([
            'mes'  => isset($_GET['mes']) ? $_GET['mes'] : null,
            'anio' => isset($_GET['anio']) ? $_GET['anio'] : null,
        ]);
        $resultado = HerramientasDao::GetRepDiaAtraso($datos);
        $filas = ($resultado['success'] && isset($resultado['datos'])) ? $resultado['datos'] : [];

        $nombre = 'rep_dia_atraso_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

        fputcsv($out, ['COD_CTE', 'CICLO', 'NOMBRE', 'INICIO', 'DIAS_ATRASO'], ',');
        foreach ($filas as $fila) {
            fputcsv($out, [
                $fila['COD_CTE'] ?? '',
                $fila['CICLO'] ?? '',
                $fila['NOMBRE'] ?? '',
                $fila['INICIO'] ?? '',
                $fila['DIAS_ATRASO'] ?? '',
            ], ',');
        }
        fclose($out);
        exit;
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

                function procesarIndividual(idx, $btn) {
                    var fila = Array.isArray(datosActuales) && datosActuales[idx] ? datosActuales[idx] : null;
                    if (!fila) { mostrarMensaje('error', "No se pudo obtener la fila a procesar."); return; }
                    swal({ title: "¿Deseas procesar este devengo?", icon: "warning", buttons: ["No", "Sí"], dangerMode: true }).then(function(ok) {
                        if (!ok) return;
                        $btn.prop("disabled", true);
                        swal({ text: "Procesando...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                        $.ajax({
                            type: "POST",
                            url: "/Herramientas/ProcesarIndividual/",
                            contentType: "application/json",
                            data: JSON.stringify({ fila: fila }),
                            success: function(res) {
                                swal.close();
                                $btn.prop("disabled", false);
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
                                $btn.prop("disabled", false);
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
                        var $btnMasivo = $("#btn_masivo");
                        $btnMasivo.prop("disabled", true);
                        swal({ text: "Procesando " + registros.length + " registro(s)...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                        $.ajax({
                            type: "POST",
                            url: "/Herramientas/ProcesarMasivo/",
                            contentType: "application/json",
                            data: JSON.stringify({ registros: registros }),
                            timeout: 300000,
                            success: function(res) {
                                swal.close();
                                $btnMasivo.prop("disabled", false);
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
                                $btnMasivo.prop("disabled", false);
                                mostrarMensaje('error', "Error de conexión o tiempo agotado.");
                            }
                        });
                    });
                }

                $(document).ready(function(){

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

                    $("#btn_consultar").click(consultarDevengosFaltantes);
                    $("#btn_masivo").click(procesarMasivo);

                    $(document).on("click", ".btn-procesar", function() {
                        var $btn = $(this);
                        if ($btn.prop("disabled")) return;
                        var idx = $btn.data("index");
                        if (typeof idx === "undefined") { mostrarMensaje('error', "No se pudo obtener la fila."); return; }
                        procesarIndividual(idx, $btn);
                    });
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Auditoría Devengo")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
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
}
