<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\services\ListaNegraEmpleadosService;

/**
 * Pantallas de administración del sistema (menú Administración).
 */
class Administracion extends Controller
{
    private $_contenedor;

    public function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    /**
     * Lista negra de empleados (CURP en CL_MARCA, TIPOMARCA LN).
     */
    public function ListaNegraEmpleados()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->configuraTabla}

                const idTablaLn = "tabla-lista-negra-empleados";

                /**
                 * @param {{ silencioso?: boolean, alListo?: function(): void }} [opciones]
                 */
                function cargarLista(opciones) {
                    opciones = opciones || {};
                    var silencioso = opciones.silencioso === true;
                    var alListo = opciones.alListo;
                    var url = "/Administracion/ListaNegraEmpleadosConsultar/";
                    if (!silencioso) {
                        showWait("Cargando...");
                    }
                    $.ajax({
                        type: "GET",
                        url: url,
                        dataType: "json",
                        success: function(res) {
                            if (!silencioso) {
                                swal.close();
                            }
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                                showError("Error al interpretar la respuesta");
                                if (typeof alListo === "function") alListo();
                                return;
                            }
                            if (!res.success) {
                                showError(res.mensaje || "Error al cargar");
                                if (typeof alListo === "function") alListo();
                                return;
                            }
                            var datos = res.datos || [];
                            var filas = datos.map(function(r) {
                                var curp = r.CURP || r.curp || "";
                                var est = r.ESTATUS || r.estatus || "";
                                var alta = r.ALTA_FMT || "";
                                var baja = r.BAJA_FMT || "";
                                var sec = r.SECUENCIA != null ? r.SECUENCIA : "";
                                var altaDia = r.ALTA_DIA || "";
                                var btn = "";
                                if (est === "A") {
                                    btn = '<button type="button" class="btn btn-warning btn-xs btn-baja-ln" data-secuencia="' + sec + '" data-alta-dia="' + altaDia + '" data-curp="' + curp.replace(/"/g, "&quot;") + '">Baja</button>';
                                } else {
                                    btn = '<span class="text-muted">—</span>';
                                }
                                return [sec, curp, est, alta, baja, btn];
                            });
                            var tabla = $("#" + idTablaLn).DataTable();
                            tabla.clear();
                            if (filas.length) tabla.rows.add(filas);
                            tabla.draw();
                            if (typeof alListo === "function") {
                                alListo();
                            }
                        },
                        error: function() {
                            if (!silencioso) {
                                swal.close();
                            }
                            showError("Error de red al cargar la lista.");
                            if (typeof alListo === "function") {
                                alListo();
                            }
                        }
                    });
                }

                function guardarCurp() {
                    var curp = $("#curp_manual").val() ? $("#curp_manual").val().trim() : "";
                    if (!curp) {
                        showWarning("Capture un CURP.");
                        return;
                    }
                    showWait("Guardando...");
                    $.ajax({
                        type: "POST",
                        url: "/Administracion/ListaNegraEmpleadosGuardar/",
                        contentType: "application/json; charset=UTF-8",
                        data: JSON.stringify({ curp: curp }),
                        dataType: "json",
                        success: function(res) {
                            swal.close();
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                                showError("Respuesta inválida");
                                return;
                            }
                            if (res.success) {
                                var okMsg = res.mensaje || "OK";
                                $("#curp_manual").val("");
                                showSuccess(okMsg);
                                cargarLista({ silencioso: true });
                            } else {
                                showError(res.mensaje || "Error");
                            }
                        },
                        error: function() {
                            swal.close();
                            showError("Error al guardar.");
                        }
                    });
                }

                function subirExcel() {
                    var f = document.getElementById("archivo_excel_ln").files[0];
                    if (!f) {
                        showWarning("Seleccione un archivo Excel (.xlsx).");
                        return;
                    }
                    var fd = new FormData();
                    fd.append("archivo", f);
                    showWait("Importando...");
                    $.ajax({
                        type: "POST",
                        url: "/Administracion/ListaNegraEmpleadosCargaMasiva/",
                        data: fd,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function(res) {
                            swal.close();
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                                showError("Respuesta inválida");
                                return;
                            }
                            if (res.success) {
                                var msg = res.mensaje || "Importación finalizada";
                                if (res.errores && res.errores.length) {
                                    var lines = res.errores.map(function(e) {
                                        return (e.fila ? "Fila " + e.fila + ": " : "") + (e.curp || "") + " — " + (e.motivo || "");
                                    });
                                    msg += "\\n\\nDetalle (no procesados):\\n" + lines.join("\\n");
                                }
                                var omit = typeof res.omitidos !== "undefined" ? res.omitidos : (res.errores ? res.errores.length : 0);
                                $("#archivo_excel_ln").val("");
                                if (omit > 0) {
                                    showWarning(msg);
                                } else {
                                    showSuccess(msg);
                                }
                                cargarLista({ silencioso: true });
                            } else {
                                var msg = res.mensaje || "Error en la importación";
                                if (res.errores && res.errores.length) {
                                    var lines2 = res.errores.map(function(e) {
                                        return (e.fila ? "Fila " + e.fila + ": " : "") + (e.curp || "") + " — " + (e.motivo || "");
                                    });
                                    msg += "\\n" + lines2.join("\\n");
                                }
                                showError(msg);
                            }
                        },
                        error: function() {
                            swal.close();
                            showError("Error al subir el archivo.");
                        }
                    });
                }

                $(document).ready(function() {
                    if (!$.fn.DataTable.isDataTable("#" + idTablaLn)) {
                        $("#" + idTablaLn).DataTable({
                            order: [[0, "desc"]],
                            columnDefs: [
                                { orderable: false, targets: [5] },
                                {
                                    targets: 5,
                                    createdCell: function(td, cellData) {
                                        $(td).css("white-space", "nowrap");
                                        $(td).html(cellData || "");
                                    }
                                }
                            ],
                            language: {
                                emptyTable: "Sin registros",
                                paginate: { previous: "Anterior", next: "Siguiente" },
                                info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                                zeroRecords: "No se encontraron registros",
                                lengthMenu: "Mostrar _MENU_ registros",
                                search: "Buscar en página:"
                            }
                        });
                    }
                    cargarLista();
                    $("#btn_guardar_curp").click(guardarCurp);
                    $("#btn_subir_excel").click(subirExcel);

                    $(document).on("click", ".btn-baja-ln", function() {
                        var btn = $(this);
                        var sec = parseInt(btn.data("secuencia"), 10);
                        var altaDia = btn.data("alta-dia");
                        var curp = btn.data("curp");
                        if (!sec || !altaDia || !curp) return;
                        swal({
                            title: "¿Dar de baja este CURP?",
                            text: curp,
                            icon: "warning",
                            buttons: ["No", "Sí"],
                            dangerMode: true
                        }).then(function(ok) {
                            if (!ok) return;
                            showWait("Procesando baja...");
                            $.ajax({
                                type: "POST",
                                url: "/Administracion/ListaNegraEmpleadosBaja/",
                                contentType: "application/json; charset=UTF-8",
                                data: JSON.stringify({ secuencia: sec, alta_dia: altaDia, curp: curp }),
                                dataType: "json",
                                success: function(res) {
                                    swal.close();
                                    try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                                        showError("Respuesta inválida");
                                        return;
                                    }
                                    if (res.success) {
                                        showSuccess(res.mensaje || "Baja registrada");
                                        cargarLista();
                                    } else {
                                        showError(res.mensaje || "Error");
                                    }
                                },
                                error: function() {
                                    swal.close();
                                    showError("Error al dar de baja.");
                                }
                            });
                        });
                    });
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Lista Negra (empleados)")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('administracion_lista_negra_empleados');
    }

    /**
     * JSON: listado de CL_MARCA LN.
     */
    public function ListaNegraEmpleadosConsultar()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
        try {
            $datos = ListaNegraEmpleadosService::listar($q !== '' ? $q : null);
            echo json_encode(\Core\Model::Responde(true, 'OK', $datos));
        } catch (\Throwable $e) {
            echo json_encode(\Core\Model::Responde(false, 'No se pudo cargar la lista.', null, $e->getMessage()));
        }
    }

    /**
     * JSON: alta manual de un CURP.
     */
    public function ListaNegraEmpleadosGuardar()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?: [];
        $curp = isset($body['curp']) ? (string) $body['curp'] : '';
        $usuario = $this->__usuario ?? '';
        echo json_encode(ListaNegraEmpleadosService::guardarUno($curp, $usuario));
    }

    /**
     * JSON: baja de un registro activo.
     */
    public function ListaNegraEmpleadosBaja()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?: [];
        $sec = isset($body['secuencia']) ? (int) $body['secuencia'] : 0;
        $altaDia = isset($body['alta_dia']) ? trim((string) $body['alta_dia']) : '';
        $curp = isset($body['curp']) ? (string) $body['curp'] : '';
        $usuario = $this->__usuario ?? '';
        echo json_encode(ListaNegraEmpleadosService::darBaja($sec, $altaDia, $curp, $usuario));
    }

    /**
     * Carga masiva desde Excel (columna A = CURP).
     */
    public function ListaNegraEmpleadosCargaMasiva()
    {
        header('Content-Type: application/json; charset=UTF-8');
        if (!isset($_FILES['archivo']) || !is_uploaded_file($_FILES['archivo']['tmp_name'])) {
            echo json_encode(\Core\Model::Responde(false, 'No se recibió el archivo.'));
            return;
        }
        if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(\Core\Model::Responde(false, 'Error al subir el archivo (código ' . (int) $_FILES['archivo']['error'] . ').'));
            return;
        }
        $tmp = $_FILES['archivo']['tmp_name'];
        $usuario = $this->__usuario ?? '';
        $nombre = isset($_FILES['archivo']['name']) ? (string) $_FILES['archivo']['name'] : '';
        $ext = strtolower((string) pathinfo($nombre, PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx', 'xls', 'xlsm', 'xltx', 'xltm', 'ods', 'csv', 'txt'], true)) {
            $ext = 'xlsx';
        }
        $dirTmp = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'tmp';
        if (!is_dir($dirTmp)) {
            @mkdir($dirTmp, 0755, true);
        }
        $dest = $dirTmp . DIRECTORY_SEPARATOR . 'ln_curp_' . uniqid('', true) . '.' . $ext;
        $okMove = @move_uploaded_file($tmp, $dest);
        if (!$okMove) {
            $okMove = @copy($tmp, $dest);
        }
        if (!$okMove || !is_readable($dest)) {
            $fallback = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ln_curp_' . uniqid('', true) . '.' . $ext;
            $okMove = @copy($tmp, $fallback);
            if ($okMove && is_readable($fallback)) {
                $dest = $fallback;
            } else {
                echo json_encode(\Core\Model::Responde(false, 'No se pudo guardar el archivo recibido para procesarlo.'));
                return;
            }
        }
        try {
            echo json_encode(ListaNegraEmpleadosService::cargaMasivaDesdeArchivo($dest, $usuario));
        } finally {
            @unlink($dest);
        }
    }

    /**
     * Descarga layout Excel (columna CURP).
     */
    public function ListaNegraEmpleadosLayout()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $centrado = ['estilo' => $estilos['centrado']];
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('CURP', 'CURP', $centrado),
        ];
        $filas = [
            ['CURP' => ''],
        ];
        \PHPSpreadsheet::DescargaExcel('layout_lista_negra_empleados', 'ListaNegra', 'Capture un CURP por fila (columna A). Puede omitir el título.', $columnas, $filas);
    }

    /**
     * Layout CSV (UTF-8): primera columna CURP — funciona sin extensión zip en el servidor.
     */
    public function ListaNegraEmpleadosLayoutCsv()
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="layout_lista_negra_empleados.csv"');
        echo "\xEF\xBB\xBF";
        echo "CURP\n";
        exit;
    }
}
