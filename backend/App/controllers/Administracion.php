<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\services\ListaNegraEmpleadosService;
use App\services\AccesoWebappService;

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
                                var usuarioAlta = r.USUARIO_ALTA_FMT || r.usuario_alta_fmt || "";
                                var usuarioBaja = r.USUARIO_BAJA_FMT || r.usuario_baja_fmt || "";
                                if (!usuarioAlta) {
                                    usuarioAlta = '<span class="text-muted">—</span>';
                                }
                                if (!usuarioBaja) {
                                    usuarioBaja = '<span class="text-muted">—</span>';
                                }
                                var estTexto = est === "A"
                                    ? "Bloqueado"
                                    : (est === "B" ? "Desbloqueado" : String(est || ""));
                                var btn = "";
                                if (est === "A") {
                                    btn = '<button type="button" class="btn btn-success btn-xs btn-desbloquear-ln" data-secuencia="' + sec + '" data-alta-dia="' + altaDia + '" data-curp="' + curp.replace(/"/g, "&quot;") + '">Desbloquear</button>';
                                } else if (est === "B") {
                                    btn = '<button type="button" class="btn btn-warning btn-xs btn-bloquear-ln" data-secuencia="' + sec + '" data-alta-dia="' + altaDia + '" data-curp="' + curp.replace(/"/g, "&quot;") + '">Bloquear</button>';
                                } else {
                                    btn = '<span class="text-muted">—</span>';
                                }
                                return [sec, curp, estTexto, alta, baja, usuarioAlta, usuarioBaja, btn];
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
                                { visible: false, searchable: false, targets: [0] },
                                { orderable: false, targets: [7] },
                                {
                                    targets: 7,
                                    createdCell: function(td, cellData) {
                                        $(td).css("white-space", "nowrap");
                                        $(td).css("vertical-align", "middle");
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

                    $(document).on("click", ".btn-desbloquear-ln", function() {
                        var btn = $(this);
                        var sec = parseInt(btn.data("secuencia"), 10);
                        var altaDia = btn.data("alta-dia");
                        var curp = btn.data("curp");
                        if (!sec || !altaDia || !curp) return;
                        swal({
                            title: "¿Desbloquear este CURP?",
                            text: curp,
                            icon: "warning",
                            buttons: ["No", "Sí"],
                            dangerMode: true
                        }).then(function(ok) {
                            if (!ok) return;
                            showWait("Procesando desbloqueo...");
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
                                        showSuccess(res.mensaje || "Desbloqueo registrado");
                                        cargarLista({ silencioso: true });
                                    } else {
                                        showError(res.mensaje || "Error");
                                    }
                                },
                                error: function() {
                                    swal.close();
                                    showError("Error al desbloquear.");
                                }
                            });
                        });
                    });

                    $(document).on("click", ".btn-bloquear-ln", function() {
                        var btn = $(this);
                        var sec = parseInt(btn.data("secuencia"), 10);
                        var altaDia = btn.data("alta-dia");
                        var curp = btn.data("curp");
                        if (!sec || !altaDia || !curp) return;
                        swal({
                            title: "¿Bloquear este CURP en la lista negra?",
                            text: curp,
                            icon: "warning",
                            buttons: ["No", "Sí"],
                            dangerMode: true
                        }).then(function(ok) {
                            if (!ok) return;
                            showWait("Procesando bloqueo...");
                            $.ajax({
                                type: "POST",
                                url: "/Administracion/ListaNegraEmpleadosBloquear/",
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
                                        showSuccess(res.mensaje || "Bloqueo registrado");
                                        cargarLista({ silencioso: true });
                                    } else {
                                        showError(res.mensaje || "Error");
                                    }
                                },
                                error: function() {
                                    swal.close();
                                    showError("Error al bloquear.");
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
     * JSON: bloquear (reactivar) un registro dado de baja.
     */
    public function ListaNegraEmpleadosBloquear()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?: [];
        $sec = isset($body['secuencia']) ? (int) $body['secuencia'] : 0;
        $altaDia = isset($body['alta_dia']) ? trim((string) $body['alta_dia']) : '';
        $curp = isset($body['curp']) ? (string) $body['curp'] : '';
        $usuario = $this->__usuario ?? '';
        echo json_encode(ListaNegraEmpleadosService::darBloquear($sec, $altaDia, $curp, $usuario));
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

    /**
     * Acceso Webapp: credentials, phone and sessions of the payments webapp users.
     */
    public function AccesoWebapp()
    {
        if (!AccesoWebappService::esAdministrador($this->__perfil)) {
            header('Location: /Principal/');
            exit;
        }

        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}

                const idTablaUsuarios = "tabla-acceso-webapp-usuarios";
                const idTablaBitacora = "tabla-acceso-webapp-bitacora";
                const avisoPagosPendientes = "Pide al ejecutivo que sincronice sus pagos pendientes antes de desligar el teléfono";

                const estadosContrasena = {
                    SIN_CONTRASENA: { texto: "Sin contraseña", clase: "default" },
                    ACTIVA: { texto: "Activa", clase: "success" },
                    TEMPORAL: { texto: "Temporal", clase: "info" },
                    TEMPORAL_VENCIDA: { texto: "Temporal vencida", clase: "warning" },
                    BLOQUEADA: { texto: "Bloqueada", clase: "danger" }
                };

                const resultadosAcceso = {
                    CORRECTO: { texto: "Correcto", clase: "success" },
                    CREDENCIALES_INVALIDAS: { texto: "Credenciales inválidas", clase: "danger" },
                    CUENTA_BLOQUEADA: { texto: "Cuenta bloqueada", clase: "danger" },
                    DISPOSITIVO_NO_AUTORIZADO: { texto: "Teléfono no autorizado", clase: "warning" },
                    RETO_INVALIDO: { texto: "Firma inválida", clase: "warning" },
                    CAMBIO_CONTRASENA_REQUERIDO: { texto: "Cambio de contraseña requerido", clase: "info" },
                    CONTRASENA_TEMPORAL_VENCIDA: { texto: "Temporal vencida", clase: "warning" },
                    NO_DISPONIBLE: { texto: "No disponible", clase: "default" },
                    ERROR_INTERNO: { texto: "Error interno", clase: "default" }
                };

                const idiomaTabla = {
                    emptyTable: "Sin registros",
                    paginate: { previous: "Anterior", next: "Siguiente" },
                    info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Sin registros",
                    infoFiltered: "(de _MAX_ en total)",
                    zeroRecords: "No se encontraron registros",
                    lengthMenu: "Mostrar _MENU_ registros",
                    search: "Buscar:"
                };

                let usuariosPorCodigo = {};

                function escaparHtml(valor) {
                    return String(valor === null || valor === undefined ? "" : valor)
                        .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
                }

                function etiqueta(texto, clase) {
                    return '<span class="label label-' + clase + '">' + escaparHtml(texto) + '</span>';
                }

                function interpretar(res) {
                    if (typeof res === "string") {
                        try { return JSON.parse(res); } catch (e) { return null; }
                    }
                    return res;
                }

                function pedirJson(metodo, url, datos, alTerminar, mensajeEspera) {
                    if (mensajeEspera) showWait(mensajeEspera);
                    $.ajax({
                        type: metodo,
                        url: url,
                        data: metodo === "POST" ? JSON.stringify(datos || {}) : datos,
                        contentType: metodo === "POST" ? "application/json; charset=UTF-8" : undefined,
                        dataType: "json",
                        cache: false,
                        success: function(res) {
                            if (mensajeEspera) swal.close();
                            res = interpretar(res);
                            if (!res) {
                                showError("Respuesta inválida del servidor.");
                                return;
                            }
                            alTerminar(res);
                        },
                        error: function(xhr) {
                            if (mensajeEspera) swal.close();
                            const res = interpretar(xhr.responseText);
                            showError((res && res.mensaje) || "No se pudo comunicar con el servidor.");
                        }
                    });
                }

                function celdaContrasena(u) {
                    const estado = estadosContrasena[u.ESTADO_CONTRASENA] || { texto: u.ESTADO_CONTRASENA, clase: "default" };
                    let html = etiqueta(estado.texto, estado.clase);
                    if (u.ESTADO_CONTRASENA === "TEMPORAL" && u.TEMPORAL_VENCE) html += '<br><small>Vence ' + escaparHtml(u.TEMPORAL_VENCE) + '</small>';
                    if (u.ESTADO_CONTRASENA === "TEMPORAL_VENCIDA" && u.TEMPORAL_VENCE) html += '<br><small>Venció ' + escaparHtml(u.TEMPORAL_VENCE) + '</small>';
                    if (u.ESTADO_CONTRASENA === "BLOQUEADA" && u.BLOQUEADO_HASTA) html += '<br><small>Hasta ' + escaparHtml(u.BLOQUEADO_HASTA) + '</small>';
                    if (u.BLOQUEO_SICAFIN === "S") html += '<br>' + etiqueta("Bloqueado en SICAFIN", "danger");
                    return html;
                }

                function celdaUltimoAcceso(u) {
                    if (!u.ULTIMO_ACCESO) return '<span class="text-muted">Nunca</span>';
                    const r = resultadosAcceso[u.ULTIMO_RESULTADO] || { texto: u.ULTIMO_RESULTADO, clase: "default" };
                    return escaparHtml(u.ULTIMO_ACCESO) + '<br>' + etiqueta(r.texto, r.clase) +
                        (u.ULTIMA_VERSION_APP ? ' <small>v' + escaparHtml(u.ULTIMA_VERSION_APP) + '</small>' : '');
                }

                function boton(accion, usuario, clase, icono, titulo) {
                    return '<button type="button" class="btn btn-' + clase + ' btn-xs btn-acceso" data-accion="' + accion +
                        '" data-usuario="' + escaparHtml(usuario) + '" title="' + escaparHtml(titulo) + '">' +
                        '<i class="fa fa-' + icono + '"></i></button>';
                }

                function celdaAcciones(u) {
                    let html = u.ESTADO_CONTRASENA === "SIN_CONTRASENA"
                        ? boton("asignar", u.USUARIO, "primary", "key", "Asignar contraseña temporal")
                        : boton("asignar", u.USUARIO, "warning", "refresh", "Restablecer contraseña temporal");
                    if (u.ESTADO_CONTRASENA === "BLOQUEADA") html += boton("desbloquear", u.USUARIO, "success", "unlock", "Desbloquear");
                    if (u.TELEFONO_DESDE) html += boton("desligar", u.USUARIO, "danger", "mobile", "Desligar teléfono");
                    if (u.SESIONES_ACTIVAS > 0) html += boton("cerrar", u.USUARIO, "default", "sign-out", "Cerrar sesiones");
                    html += boton("bitacora", u.USUARIO, "info", "history", "Ver bitácora de accesos");
                    return html;
                }

                function cargarUsuarios() {
                    pedirJson("GET", "/Administracion/AccesoWebappUsuarios/", null, function(res) {
                        if (!res.success) {
                            showError(res.mensaje || "No se pudo cargar la lista.");
                            return;
                        }
                        usuariosPorCodigo = {};
                        const filas = (res.datos || []).map(function(u) {
                            usuariosPorCodigo[u.USUARIO] = u;
                            return [
                                escaparHtml(u.USUARIO),
                                escaparHtml(u.NOMBRE),
                                escaparHtml(u.PERFILES),
                                escaparHtml(u.SUCURSAL || ""),
                                celdaContrasena(u),
                                u.TELEFONO_DESDE ? 'Desde ' + escaparHtml(u.TELEFONO_DESDE) : '<span class="text-muted">Sin teléfono</span>',
                                u.SESIONES_ACTIVAS,
                                celdaUltimoAcceso(u),
                                celdaAcciones(u)
                            ];
                        });
                        const tabla = $("#" + idTablaUsuarios).DataTable();
                        tabla.clear().rows.add(filas).draw(false);
                    }, "Cargando usuarios...");
                }

                function mostrarTemporal(datos) {
                    const contenido = document.createElement("div");
                    contenido.innerHTML =
                        '<p>Entrégala en persona. No se volverá a mostrar.</p>' +
                        '<div style="font-family: monospace; font-size: 26px; letter-spacing: 3px; padding: 10px; border: 1px dashed #999; user-select: all;">' +
                        escaparHtml(datos.contrasena) + '</div>' +
                        '<p style="margin-top: 10px;">Vence: <b>' + escaparHtml(datos.vence || "") + '</b>. ' +
                        'El usuario deberá cambiarla en su primer inicio de sesión.</p>';
                    swal({
                        title: "Contraseña temporal de " + datos.usuario,
                        content: contenido,
                        icon: "success",
                        button: "Ya la anoté",
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    }).then(cargarUsuarios);
                }

                function asignar(u) {
                    const restablecer = u.ESTADO_CONTRASENA !== "SIN_CONTRASENA";
                    swal({
                        title: (restablecer ? "¿Restablecer" : "¿Asignar") + " contraseña temporal?",
                        text: u.USUARIO + " - " + u.NOMBRE + "\\n\\n" +
                            (restablecer ? "La contraseña actual dejará de funcionar. " : "") +
                            "Se generará una contraseña temporal válida por 72 horas." +
                            (restablecer ? "\\n\\nSi la contraseña se filtró, desliga también el teléfono." : ""),
                        icon: "warning",
                        buttons: ["Cancelar", restablecer ? "Restablecer" : "Asignar"],
                        dangerMode: restablecer
                    }).then(function(ok) {
                        if (!ok) return;
                        pedirJson("POST", "/Administracion/AccesoWebappAsignarTemporal/", { usuario: u.USUARIO }, function(res) {
                            if (!res.success) {
                                showError(res.mensaje || "No se pudo generar la contraseña.");
                                return;
                            }
                            mostrarTemporal(res.datos);
                        }, "Generando contraseña...");
                    });
                }

                function ejecutarAccion(url, usuario, mensajeEspera) {
                    pedirJson("POST", url, { usuario: usuario }, function(res) {
                        if (!res.success) {
                            showError(res.mensaje || "No se pudo completar la acción.");
                            return;
                        }
                        showSuccess(res.mensaje).then(cargarUsuarios);
                    }, mensajeEspera);
                }

                function desbloquear(u) {
                    swal({
                        title: "¿Desbloquear?",
                        text: u.USUARIO + " - " + u.NOMBRE + "\\n\\nSe quitará el bloqueo por intentos sin cambiar la contraseña.",
                        icon: "warning",
                        buttons: ["Cancelar", "Desbloquear"]
                    }).then(function(ok) {
                        if (ok) ejecutarAccion("/Administracion/AccesoWebappDesbloquear/", u.USUARIO, "Desbloqueando...");
                    });
                }

                function desligar(u) {
                    swal({
                        title: "¿Desligar el teléfono?",
                        text: u.USUARIO + " - " + u.NOMBRE + "\\n\\n" + avisoPagosPendientes + ".\\n\\n" +
                            "Los pagos que sigan en el teléfono ya no se podrán enviar. " +
                            "Para confirmar, escribe el usuario " + u.USUARIO + ":",
                        icon: "warning",
                        content: { element: "input", attributes: { placeholder: u.USUARIO } },
                        buttons: ["Cancelar", "Desligar"],
                        dangerMode: true
                    }).then(function(valor) {
                        if (valor === null || valor === undefined || valor === false) return;
                        if (String(valor).trim().toUpperCase() !== u.USUARIO) {
                            showWarning("El usuario escrito no coincide. No se desligó el teléfono.");
                            return;
                        }
                        ejecutarAccion("/Administracion/AccesoWebappDesligar/", u.USUARIO, "Desligando teléfono...");
                    });
                }

                function cerrarSesiones(u) {
                    swal({
                        title: "¿Cerrar sesiones?",
                        text: u.USUARIO + " - " + u.NOMBRE + "\\n\\nEl teléfono sigue ligado; el usuario vuelve a entrar con su contraseña.",
                        icon: "warning",
                        buttons: ["Cancelar", "Cerrar sesiones"]
                    }).then(function(ok) {
                        if (ok) ejecutarAccion("/Administracion/AccesoWebappCerrarSesiones/", u.USUARIO, "Cerrando sesiones...");
                    });
                }

                function fechaLocal(desplazamientoDias) {
                    const d = new Date();
                    d.setDate(d.getDate() + desplazamientoDias);
                    return d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0") + "-" + String(d.getDate()).padStart(2, "0");
                }

                function cargarBitacora() {
                    const datos = {
                        usuario: $("#bitacora_usuario").val().trim(),
                        desde: $("#bitacora_desde").val(),
                        hasta: $("#bitacora_hasta").val()
                    };
                    pedirJson("GET", "/Administracion/AccesoWebappBitacora/", datos, function(res) {
                        const tabla = $("#" + idTablaBitacora).DataTable();
                        if (!res.success) {
                            tabla.clear().draw();
                            showError(res.mensaje || "No se pudo consultar la bitácora.");
                            return;
                        }
                        const filas = (res.datos || []).map(function(a) {
                            const r = resultadosAcceso[a.RESULTADO] || { texto: a.RESULTADO, clase: "default" };
                            return [
                                escaparHtml(a.FECHA_UTC),
                                escaparHtml(a.FECHA),
                                escaparHtml(a.USUARIO || "(desconocido)"),
                                escaparHtml(a.NOMBRE || ""),
                                etiqueta(r.texto, r.clase),
                                escaparHtml(a.IP || ""),
                                a.DISPOSITIVO_NUEVO === "S" ? "Sí" : (a.DISPOSITIVO_NUEVO === "N" ? "No" : ""),
                                escaparHtml(a.VERSION_APP || ""),
                                escaparHtml(a.NAVEGADOR || ""),
                                escaparHtml(a.SISTEMA || ""),
                                escaparHtml(a.TIPO_DISPOSITIVO || ""),
                                escaparHtml([a.MARCA, a.MODELO].filter(Boolean).join(" "))
                            ];
                        });
                        tabla.clear().rows.add(filas).draw();
                        if (res.mensaje && res.mensaje !== "OK") showInfo(res.mensaje);
                    }, "Consultando bitácora...");
                }

                $(document).ready(function() {
                    $("#" + idTablaUsuarios).DataTable({
                        order: [[1, "asc"]],
                        pageLength: 25,
                        columnDefs: [
                            { orderable: false, targets: [8] },
                            { className: "text-center", targets: [6] },
                            { createdCell: function(td) { $(td).css("white-space", "nowrap"); }, targets: [8] }
                        ],
                        language: idiomaTabla
                    });
                    $("#" + idTablaBitacora).DataTable({
                        order: [[0, "desc"]],
                        pageLength: 25,
                        columnDefs: [{ visible: false, searchable: false, targets: [0] }],
                        language: idiomaTabla
                    });

                    $("#bitacora_desde").val(fechaLocal(-7));
                    $("#bitacora_hasta").val(fechaLocal(0));

                    cargarUsuarios();
                    $("#btn_recargar_usuarios").click(cargarUsuarios);
                    $("#btn_buscar_bitacora").click(cargarBitacora);
                    $('a[href="#tab_bitacora"]').on("shown.bs.tab", function() {
                        $("#" + idTablaBitacora).DataTable().columns.adjust();
                    });

                    $(document).on("click", ".btn-acceso", function() {
                        const u = usuariosPorCodigo[$(this).data("usuario")];
                        if (!u) return;
                        switch ($(this).data("accion")) {
                            case "asignar": asignar(u); break;
                            case "desbloquear": desbloquear(u); break;
                            case "desligar": desligar(u); break;
                            case "cerrar": cerrarSesiones(u); break;
                            case "bitacora":
                                $("#bitacora_usuario").val(u.USUARIO);
                                $('a[href="#tab_bitacora"]').tab("show");
                                cargarBitacora();
                                break;
                        }
                    });
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Acceso Webapp")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('administracion_acceso_webapp');
    }

    /**
     * JSON: webapp users with their password, phone, sessions and last access.
     */
    public function AccesoWebappUsuarios()
    {
        if (!$this->accesoWebappAutorizado(false)) return;
        try {
            echo json_encode(\Core\Model::Responde(true, 'OK', AccesoWebappService::listarUsuarios()));
        } catch (\Throwable $e) {
            echo json_encode(\Core\Model::Responde(false, 'No se pudo cargar la lista.', null, $e->getMessage()));
        }
    }

    /**
     * JSON: access log by user and local date range.
     */
    public function AccesoWebappBitacora()
    {
        if (!$this->accesoWebappAutorizado(false)) return;
        echo json_encode(AccesoWebappService::consultarBitacora(
            (string) ($_GET['usuario'] ?? ''),
            (string) ($_GET['desde'] ?? ''),
            (string) ($_GET['hasta'] ?? '')
        ));
    }

    /**
     * JSON: generates a temporary password; the plain value is returned only in this response.
     */
    public function AccesoWebappAsignarTemporal()
    {
        if (!$this->accesoWebappAutorizado(true)) return;
        echo json_encode(AccesoWebappService::asignarTemporal(
            $this->accesoWebappUsuarioSolicitado(),
            (string) $this->__usuario,
            (string) ($_SERVER['REMOTE_ADDR'] ?? '')
        ));
    }

    /**
     * JSON: clears the failed-attempts lock.
     */
    public function AccesoWebappDesbloquear()
    {
        if (!$this->accesoWebappAutorizado(true)) return;
        echo json_encode(AccesoWebappService::desbloquear(
            $this->accesoWebappUsuarioSolicitado(),
            (string) $this->__usuario,
            (string) ($_SERVER['REMOTE_ADDR'] ?? '')
        ));
    }

    /**
     * JSON: unlinks the phone and revokes sessions.
     */
    public function AccesoWebappDesligar()
    {
        if (!$this->accesoWebappAutorizado(true)) return;
        echo json_encode(AccesoWebappService::desligarDispositivo(
            $this->accesoWebappUsuarioSolicitado(),
            (string) $this->__usuario,
            (string) ($_SERVER['REMOTE_ADDR'] ?? '')
        ));
    }

    /**
     * JSON: revokes sessions keeping the phone.
     */
    public function AccesoWebappCerrarSesiones()
    {
        if (!$this->accesoWebappAutorizado(true)) return;
        echo json_encode(AccesoWebappService::cerrarSesiones(
            $this->accesoWebappUsuarioSolicitado(),
            (string) $this->__usuario,
            (string) ($_SERVER['REMOTE_ADDR'] ?? '')
        ));
    }

    /**
     * Sets JSON/no-cache headers and rejects non-admin users; actions also require a JSON POST
     * (cross-site forms cannot send it without a CORS preflight).
     */
    private function accesoWebappAutorizado(bool $esAccion): bool
    {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');

        if (!AccesoWebappService::esAdministrador($this->__perfil)) {
            http_response_code(403);
            echo json_encode(\Core\Model::Responde(false, 'No tienes permiso para esta opción.'));
            return false;
        }
        if ($esAccion) {
            $tipo = strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? ''));
            if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || strpos($tipo, 'application/json') !== 0) {
                http_response_code(405);
                echo json_encode(\Core\Model::Responde(false, 'Solicitud no válida.'));
                return false;
            }
        }
        return true;
    }

    private function accesoWebappUsuarioSolicitado(): string
    {
        $body = json_decode((string) file_get_contents('php://input'), true);
        return is_array($body) ? (string) ($body['usuario'] ?? '') : '';
    }
}
