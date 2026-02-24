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
            <script src="/js/daterangepicker.js"></script>
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

                function consultarDevengosFaltantes() {
                    var credito = $("#filtro_credito").val() ? $("#filtro_credito").val().trim() : "";
                    var ciclo = $("#filtro_ciclo").val() ? $("#filtro_ciclo").val().trim() : "";
                    var fechaDesde = toYMD($("#filtro_fecha_desde").val());
                    var fechaHasta = toYMD($("#filtro_fecha_hasta").val());
                    var params = [];
                    if (credito) params.push("credito=" + encodeURIComponent(credito));
                    if (ciclo) params.push("ciclo=" + encodeURIComponent(ciclo));
                    if (fechaDesde) params.push("fecha_desde=" + encodeURIComponent(fechaDesde));
                    if (fechaHasta) params.push("fecha_hasta=" + encodeURIComponent(fechaHasta));
                    var url = "/Herramientas/GetDevengosFaltantes/" + (params.length ? "?" + params.join("&") : "");

                    swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                    $.ajax({
                        type: "GET",
                        url: url,
                        timeout: 120000,
                        success: function(res) {
                            swal.close();
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                                showError("Error al procesar la respuesta");
                                actualizaDatosTabla(idTablaAuditoria, []);
                                datosActuales = [];
                                return;
                            }
                            if (!res.success) {
                                showError(res.mensaje || "Error al cargar");
                                actualizaDatosTabla(idTablaAuditoria, []);
                                datosActuales = [];
                                return;
                            }
                            datosActuales = Array.isArray(res.datos) ? res.datos : [];
                            var rows = datosActuales.map(function(item) {
                                var c = String(item.CREDITO || item.credito || "").trim();
                                var ci = String(item.CICLO || item.ciclo || "").trim();
                                var chk = '<input type="checkbox" class="chk-procesar" data-credito="' + c + '" data-ciclo="' + ci + '">';
                                var btn = '<button type="button" class="btn btn-primary btn-sm btn-procesar" data-credito="' + c + '" data-ciclo="' + ci + '">Procesar</button>';
                                return [chk, c, ci, item.FECHA_FALTANTE || "", item.FECHA_CALC || "", item.NOMBRE || "", btn];
                            });
                            var tabla = $("#" + idTablaAuditoria).DataTable();
                            tabla.clear();
                            if (rows.length) {
                                tabla.rows.add(rows).draw();
                            } else {
                                tabla.draw();
                                if (credito || ciclo || fechaDesde || fechaHasta) {
                                    showInfo("No se encontraron devengos faltantes para los filtros aplicados.");
                                }
                            }
                        },
                        error: function() {
                            swal.close();
                            showError("La consulta tardó demasiado o hubo un error.");
                            actualizaDatosTabla(idTablaAuditoria, []);
                            datosActuales = [];
                        }
                    });
                }

                function procesarIndividual(credito, ciclo, $btn) {
                    swal({ title: "¿Deseas procesar este devengo?", icon: "warning", buttons: ["No", "Sí"], dangerMode: true }).then(function(ok) {
                        if (!ok) return;
                        $btn.prop("disabled", true);
                        swal({ text: "Procesando...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                        $.ajax({
                            type: "POST",
                            url: "/Herramientas/ProcesarIndividual/",
                            contentType: "application/json",
                            data: JSON.stringify({ credito: credito, ciclo: ciclo, fecha_corte: null }),
                            success: function(res) {
                                swal.close();
                                $btn.prop("disabled", false);
                                try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { showError("Error al procesar"); return; }
                                if (res.success) {
                                    showSuccess(res.mensaje);
                                    consultarDevengosFaltantes();
                                } else {
                                    showError(res.mensaje || res.error || "Error al procesar");
                                }
                            },
                            error: function() {
                                swal.close();
                                $btn.prop("disabled", false);
                                showError("Error de conexión o tiempo agotado.");
                            }
                        });
                    });
                }

                function procesarMasivo() {
                    var checked = $("#" + idTablaAuditoria).find(".chk-procesar:checked");
                    if (!checked.length) {
                        showError("Selecciona al menos un registro.");
                        return;
                    }
                    var seen = {};
                    var registros = [];
                    checked.each(function() {
                        var c = $(this).data("credito");
                        var ci = $(this).data("ciclo");
                        var key = c + "|" + ci;
                        if (c && ci && !seen[key]) { seen[key] = true; registros.push({ credito: c, ciclo: ci, fecha_corte: null }); }
                    });
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
                                try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { showError("Error al procesar"); return; }
                                if (res.success) {
                                    showSuccess(res.mensaje);
                                    consultarDevengosFaltantes();
                                    $("#" + idTablaAuditoria).find(".chk-procesar:checked").prop("checked", false);
                                } else {
                                    showError(res.mensaje || res.error || "Error en procesamiento masivo");
                                }
                            },
                            error: function() {
                                swal.close();
                                $btnMasivo.prop("disabled", false);
                                showError("Error de conexión o tiempo agotado.");
                            }
                        });
                    });
                }

                $(document).ready(function(){
                    var cfgDrp = { singleDatePicker: true, locale: { format: "DD/MM/YYYY" }, autoUpdateInput: false };
                    $("#filtro_fecha_desde").daterangepicker(cfgDrp, function(start) { $("#filtro_fecha_desde").val(start.format("DD/MM/YYYY")); });
                    $("#icon_fecha_desde").on("click", function() { $("#filtro_fecha_desde").focus(); });
                    $("#filtro_fecha_hasta").daterangepicker(cfgDrp, function(start) { $("#filtro_fecha_hasta").val(start.format("DD/MM/YYYY")); });
                    $("#icon_fecha_hasta").on("click", function() { $("#filtro_fecha_hasta").focus(); });

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
                        var credito = $btn.data("credito");
                        var ciclo = $btn.data("ciclo");
                        if (!credito || !ciclo) { showError("No se pudo obtener crédito o ciclo."); return; }
                        procesarIndividual(credito, ciclo, $btn);
                    });
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Auditoría Devengo", ['<link rel="stylesheet" href="/css/daterangepicker.css">'])));
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
            $fechaDesde = isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : null;
            $fechaHasta = isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : null;
            if ($fechaDesde) {
                $d = \DateTime::createFromFormat('Y-m-d', $fechaDesde);
                if (!$d) $fechaDesde = null;
            }
            if ($fechaHasta) {
                $d = \DateTime::createFromFormat('Y-m-d', $fechaHasta);
                if (!$d) $fechaHasta = null;
            }
            $datos = array_filter([
                'credito'      => isset($_GET['credito']) ? trim($_GET['credito']) : null,
                'ciclo'        => isset($_GET['ciclo']) ? trim($_GET['ciclo']) : null,
                'fecha_desde'  => $fechaDesde,
                'fecha_hasta'  => $fechaHasta,
            ], function ($v) { return $v !== null && $v !== ''; });

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
