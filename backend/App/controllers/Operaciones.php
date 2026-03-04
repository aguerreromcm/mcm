<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\Operaciones as OperacionesDao;
use App\services\PagosAplicacionService;
use App\services\ConciliacionService;

class Operaciones extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    function CierreDiario()
    {
        $tiempoEstimado = OperacionesDao::TiempoEstimadoCierreDiario();
        $estimado = $tiempoEstimado['success'] ? $tiempoEstimado['datos']['ESTIMADO'] : 0;

        $ejecucionActiva = OperacionesDao::ValidaCierreEnEjecucion();
        $ejecutando = $ejecucionActiva['success'] && isset($ejecucionActiva['datos']) ? 1 : 0;
        $inicioEjecucion = $ejecutando ? $ejecucionActiva['datos']['INICIO'] : null;
        $usuarioEjecucion = $ejecutando ? $ejecucionActiva['datos']['USUARIO'] : null;

        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->configuraTabla}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
                const tabla = "correos"
                const estimado = $estimado
                let ejecutando = $ejecutando
                let inicioEjecucion = "$inicioEjecucion"
                let usuarioEjecucion = "$usuarioEjecucion"
                let actualiza = null
                let renueva = null

                const iniciaCierreDiario = () => {
                    confirmarMovimiento(
                        "Iniciar proceso de cierre diario.",
                        "¿Está seguro de querer procesar el cierre del día\\n" + diaMsg() + "?"
                    ).then((continuar) => {
                        if (!continuar) return

                        validacionPreviaCierre()
                    })
                }

                const validacionPreviaCierre = () => {
                    const fecha = $("#fecha").val()

                    consultaServidor("/operaciones/ValidacionPreviaCierre", { fecha }, (respuesta) => {
                        if (!respuesta.success) {
                            if (respuesta.datos.USUARIO) {
                                const mensaje = document.createElement("div")
                                mensaje.innerHTML = "<p>Ya hay un proceso de cierre diario en ejecución iniciado por el usuario <b>" + respuesta.datos.USUARIO + "</b>.</p>"

                                confirmarMovimiento(
                                    "Cierre diario",
                                    null,
                                    mensaje
                                ).then((continuar) => {
                                    if (!continuar) return

                                    procesaCierreDiario()
                                })
                                return
                            }
                            return showError(respuesta.mensaje)
                        }

                        if (respuesta.datos.TOTAL > 0) {
                            const mensaje = document.createElement("div")
                            mensaje.innerHTML = "<p>El cierre diario del día <b>" + diaMsg() + "</b> ya fue procesado generando <b>" + respuesta.datos.TOTAL + "</b> registros.</p>"
                            mensaje.innerHTML += `
                                <br>
                                <p>Si continua, se eliminarán los registros y se crearan nuevos.</p>
                                <p><b>Una vez iniciado el proceso, no se podrá recuperar la información eliminada.</b></p>
                                <br>
                                <h2 style="color: red;">¿Seguro que desea continuar?</h2>
                            `

                            confirmarMovimiento(
                                "Cierre diario",
                                null,
                                mensaje
                            ).then((continuar) => {
                                if (!continuar) return

                                procesaCierreDiario()
                            })
                            return
                        }

                        procesaCierreDiario()
                    })
                }

                const procesaCierreDiario = () => {
                    const fecha = $("#fecha").val()

                    consultaServidor("/operaciones/ProcesaCierreDiario", { fecha, usuario: "{$this->__usuario}" }, (respuesta) => {
                        if (!respuesta.success) return showError(respuesta.mensaje)

                        const mensaje = "El proceso de cierre diario ha sido iniciado, al finalizar, se le notificara a los destinatarios registrados."
                        showSuccess(mensaje).then(() => {
                            ejecutando = true
                            inicioEjecucion = fechaActualFormateada()
                            usuarioEjecucion = "{$this->__usuario}"
                            validaEjecucionActiva()
                        })
                    })
                }

                const diaMsg = () => {
                    let [anio, mes, dia] = $("#fecha").val().split("-")
                    const fecha = new Date(parseInt(anio), parseInt(mes) - 1, parseInt(dia))
                    const diasSemana = ["domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado"]
                    const meses = [
                        "enero",
                        "febrero",
                        "marzo",
                        "abril",
                        "mayo",
                        "junio",
                        "julio",
                        "agosto",
                        "septiembre",
                        "octubre",
                        "noviembre",
                        "diciembre"
                    ]

                    const diaSemana = diasSemana[fecha.getDay()]
                    dia = fecha.getDate()
                    mes = meses[fecha.getMonth()]
                    anio = fecha.getFullYear()

                    return diaSemana + " " + dia + " de " + mes + " del " + anio
                }

                const fechaActualFormateada = () => {
                    const ahora = new Date()

                    const dia = String(ahora.getDate()).padStart(2, '0')
                    const mes = String(ahora.getMonth() + 1).padStart(2, '0')
                    const anio = ahora.getFullYear()

                    const horas = String(ahora.getHours()).padStart(2, '0')
                    const minutos = String(ahora.getMinutes()).padStart(2, '0')
                    const segundos = String(ahora.getSeconds()).padStart(2, '0')

                    return dia + "/" + mes + "/" + anio + " " + horas + ":" + minutos + ":" + segundos
                }

                const validaEjecucionActiva = () => {
                    if (!ejecutando) {
                        clearTimeout(actualiza)
                        clearTimeout(renueva)
                        $("#procesar").attr("disabled", false)
                        $("#alertaEjecucion").hide()
                        $("#tiempoEstimado").html("")
                        return
                    }
                    const inicio = inicioEjecucion.split(" ")
                    const fecha = inicio[0].split("/")
                    const hora = inicio[1].split(":")
                    const fechaInicio = new Date(parseInt(fecha[2]), parseInt(fecha[1]) - 1, parseInt(fecha[0]), parseInt(hora[0]), parseInt(hora[1]), parseInt(hora[2]))
                    const fechaActual = new Date()
                    const diferencia = Math.floor((fechaActual - fechaInicio) / 1000)
                    let mensaje = "<p>El proceso de cierre diario se encuentra en ejecución desde el " + inicioEjecucion + " por el usuario <b>" + usuarioEjecucion + "</b>.</p>"
                    mensaje += "<p>Tiempo estimado de finalización: <b>" + estimado + "</b> minutos.</p>"
                    mensaje += "<p>Tiempo transcurrido: <b id='transcurrido'>" + getTiempoTranscurrido(diferencia) + "</b></p>"
                    actualizaTiempoEstimado(diferencia)
                    renuevaEjecucionActiva()
                    $("#procesar").attr("disabled", true)
                    $("#tiempoEstimado").html(mensaje)
                    $("#alertaEjecucion").show()
                }

                const getTiempoTranscurrido = (diferencia) => {
                    const horas = Math.floor(diferencia / 3600)
                    const minutos = Math.floor((diferencia % 3600) / 60)
                    const segundos = diferencia % 60
                    const h = horas > 0 ? horas.toString().padStart(2,"0") : "00"
                    const m = minutos > 0 ? minutos.toString().padStart(2,"0") : "00"
                    const s = segundos > 0 ? segundos.toString().padStart(2,"0") : "00"

                    return h + ":" + m + ":" + s
                }

                const actualizaTiempoEstimado = (diferencia) => {
                    actualiza = setTimeout(() => {
                        diferencia++
                        actualizaTiempoEstimado(diferencia)
                        $("#transcurrido").html(getTiempoTranscurrido(diferencia))
                    }, 1000)
                }

                const renuevaEjecucionActiva = () => {
                    renueva = setTimeout(() => {
                        fetch("/operaciones/ValidaCierreEnEjecucion", {
                            method: "GET",
                            headers: {
                                "Content-Type": "application/json"
                            }
                        })
                        .then((response) => response.json())
                        .then((respuesta) => {
                            if (!respuesta.success) return showError(respuesta.mensaje)
                            ejecutando = respuesta.datos && Object.keys(respuesta.datos).length > 0
                            inicioEjecucion = ejecutando ? respuesta.datos.INICIO : null
                            usuarioEjecucion = ejecutando ? respuesta.datos.USUARIO : null
                            validaEjecucionActiva()
                        })
                    }, 10000)
                }

                $(document).ready(() => {
                    $("#procesar").click(() => iniciaCierreDiario())

                    $("#agregar").click(() => {
                        $("#modalAgregaCorreo").modal("show")
                    })
                    
                    configuraTabla(tabla)
                    validaEjecucionActiva()
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader('Cierre diario')));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('operaciones_cierre_diario');
    }

    function ValidaCierreEnEjecucion()
    {
        echo json_encode(OperacionesDao::ValidaCierreEnEjecucion());
    }

    function ValidacionPreviaCierre()
    {
        $activo = OperacionesDao::ValidaCierreEnEjecucion($_POST);
        if ($activo['success'] && isset($activo['datos'])) {
            echo json_encode([
                'success' => false,
                'mensaje' => "Ya hay un proceso de cierre diario en ejecución, no es posible iniciar otro.",
                'datos' => $activo['datos']
            ]);
            return;
        }

        echo json_encode(OperacionesDao::ValidacionPreviaCierre($_POST));
    }

    function ProcesaCierreDiario()
    {
        $fecha = $_POST['fecha'] ?? null;
        if (!$fecha) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'No se ha indicado la fecha para el cierre diario.'
            ]);
            return;
        }

        OperacionesDao::RegistraInicioCierreDiario($_POST);
        $cmd = "C:/xampp/php/php.exe " . dirname(__DIR__) . "/../Jobs/controllers/JobsCredito.php CierreDiario $fecha";
        $cmd = str_replace("\\", "/", $cmd);

        pclose(popen("start /B " . $cmd, "r"));
        echo json_encode([
            'success' => true,
            'mensaje' => 'El proceso de cierre diario se ha iniciado correctamente.'
        ]);
    }

    ////////////////////////////////////////////////////////////////////

    /**
     * Operaciones → Aplicar Pagos.
     * Vista: input fecha única, misma tabla/estilo que Layout Contable, resumen debajo.
     */
    public function AplicarPagos()
    {
        $extraHeader = '<title>Aplicar Pagos</title><link rel="shortcut icon" href="/img/logo.png">';
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->confirmarMovimiento}
                const ejecutarAplicarPagos = (ejecutar) => {
                    const fecha = document.getElementById("fechaAplicar").value;
                    if (!fecha) {
                        showError("Seleccione una fecha.");
                        return;
                    }
                    swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                    $.ajax({
                        url: "/Operaciones/ProcesarAplicarPagos/",
                        type: "POST",
                        data: { fecha: fecha, ejecutar: ejecutar ? 1 : 0 },
                        dataType: "json",
                        timeout: 3600000
                    }).done(function (respuesta) {
                        swal.close();
                        if (!respuesta || !respuesta.success) {
                            var msg = (respuesta && respuesta.mensaje) ? respuesta.mensaje : "Error en la respuesta.";
                            if (respuesta && respuesta.error && respuesta.error.length > 0) msg += " Detalle: " + respuesta.error;
                            showError(msg);
                            return;
                        }
                        const d = respuesta.datos || {};
                        const resumen = d.resumen || {};
                        const filas = d.filas || [];
                        const totalReg = resumen.totalRegistros != null ? resumen.totalRegistros : filas.length;
                        const totalImp = resumen.totalImporte != null ? Number(resumen.totalImporte) : 0;
                        const yaProcesado = !!d.yaProcesado;
                        const pendientes = resumen.totalPendientes != null ? resumen.totalPendientes : (yaProcesado ? 0 : totalReg);
                        const aplicados = resumen.totalAplicados != null ? resumen.totalAplicados : (yaProcesado ? totalReg : 0);
                        const importePend = resumen.importePendientes != null ? Number(resumen.importePendientes) : (yaProcesado ? 0 : totalImp);
                        const importeApl = resumen.importeAplicados != null ? Number(resumen.importeAplicados) : (yaProcesado ? totalImp : 0);
                        document.getElementById("totalPagosPendientes").textContent = pendientes;
                        document.getElementById("importePendientes").textContent = "$ " + importePend.toLocaleString("es-MX", { minimumFractionDigits: 2 });
                        document.getElementById("totalPagosAplicados").textContent = aplicados;
                        document.getElementById("importeAplicados").textContent = "$ " + importeApl.toLocaleString("es-MX", { minimumFractionDigits: 2 });
                        document.getElementById("totalPagos").textContent = totalReg;
                        document.getElementById("importeTotal").textContent = "$ " + totalImp.toLocaleString("es-MX", { minimumFractionDigits: 2 });
                        document.getElementById("estadoAplicar").textContent = d.modoPrueba ? "Modo prueba (sin cambios en BD)" : (resumen.estado || (yaProcesado ? "Procesado" : "Pendiente"));
                        document.getElementById("fechaAplicacion").textContent = resumen.fechaEjecucion || "-";
                        var tabla = $("#tablaAplicarPagos");
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable(tabla)) {
                            tabla.DataTable().destroy();
                            tabla.find("tbody").empty();
                        }
                        var tbody = document.getElementById("tablaAplicarPagosBody");
                        tbody.innerHTML = "";
                        filas.forEach(function (f) {
                            var tr = document.createElement("tr");
                            var monto = typeof f.MONTO === "number" ? f.MONTO : parseFloat(f.MONTO) || 0;
                            var aplicado = f.F_IMPORTACION && f.F_IMPORTACION !== null && String(f.F_IMPORTACION).trim() !== "";
                            var estado = aplicado ? "Aplicado" : "Pendiente";
                            var estadoClase = aplicado ? "label-success" : "label-warning";
                            tr.innerHTML = "<td>" + (f.FECHA || "-") + "</td><td>" + (f.REFERENCIA || "-") + "</td><td>$ " + monto.toLocaleString("es-MX", { minimumFractionDigits: 2 }) + "</td><td>" + (f.MONEDA || "MN") + "</td><td><span class=\"label " + estadoClase + "\" style=\"border-radius: 4px; padding: 4px 8px;\">" + estado + "</span></td>";
                            tbody.appendChild(tr);
                        });
                        if ($.fn.DataTable) {
                            tabla.DataTable({
                                lengthMenu: [[20, 50, -1], [20, 50, "Todos"]],
                                pageLength: 20,
                                order: false,
                                language: {
                                    emptyTable: "No hay datos disponibles",
                                    paginate: { previous: "Anterior", next: "Siguiente" },
                                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                                    infoEmpty: "Sin registros",
                                    zeroRecords: "No se encontraron registros",
                                    lengthMenu: "Mostrar _MENU_ registros",
                                    search: "Buscar:"
                                }
                            });
                        }
                        if (ejecutar && !d.yaProcesado) showSuccess(respuesta.mensaje);
                    }).fail(function (xhr, textStatus, errorThrown) {
                        swal.close();
                        var msg = "Ocurrió un error al procesar la solicitud.";
                        if (textStatus === "timeout") {
                            msg = "La solicitud tardó demasiado (timeout). El proceso puede seguir ejecutándose en el servidor. Revise PAGOS_PROCESADOS o intente de nuevo.";
                        } else if (xhr.responseJSON) {
                            if (xhr.responseJSON.mensaje) msg = xhr.responseJSON.mensaje;
                            if (xhr.responseJSON.error) msg += " Detalle: " + xhr.responseJSON.error;
                        } else if (xhr.responseText && xhr.responseText.length < 500) msg = xhr.responseText;
                        showError(msg);
                    });
                };
                document.addEventListener("DOMContentLoaded", function () {
                    document.getElementById("fechaAplicar").max = new Date().toISOString().split("T")[0];
                    document.getElementById("btnConsultarAplicar").onclick = function () { ejecutarAplicarPagos(false); };
                    document.getElementById("btnAplicarPagos").onclick = function () {
                        confirmarMovimiento("Aplicar pagos", "¿Ejecutar aplicación de pagos para la fecha seleccionada? No podrá reprocesar la misma fecha.").then(function (ok) { if (ok) ejecutarAplicarPagos(true); });
                    };
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fechaActual', date('Y-m-d'));
        View::render('operaciones_aplicar_pagos');
    }

    /**
     * POST: fecha, ejecutar (0|1). Valida y opcionalmente ejecuta; devuelve JSON con resumen y filas.
     */
    public function ProcesarAplicarPagos()
    {
        set_time_limit(0);
        try {
            $fecha = isset($_POST['fecha']) ? trim((string) $_POST['fecha']) : '';
            $ejecutar = !empty($_POST['ejecutar']);
            $usuario = isset($this->__usuario) ? (string) $this->__usuario : '';
            $respuesta = PagosAplicacionService::procesarOResumen($fecha, $usuario, $ejecutar);
        } catch (\Throwable $e) {
            $respuesta = [
                'success' => false,
                'mensaje' => 'Error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ];
        }

        // Enviar solo JSON: descartar cualquier salida previa y responder limpio
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        $json = json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = '{"success":false,"mensaje":"Error al generar la respuesta."}';
        }
        echo $json;
        exit;
    }

    /**
     * Pantalla Conciliación de pagos (réplica VB6: filtros + consulta MP).
     */
    public function ConciliacionPagos()
    {
        $extraHeader = '<title>Conciliación de pagos</title><link rel="shortcut icon" href="/img/logo.png">';
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->confirmarMovimiento}
                const consultarConciliacion = () => {
                    const empresa = document.getElementById("empresaConciliacion").value || "";
                    const fecha = document.getElementById("fechaConciliacion").value || "";
                    const tipoCliente = document.getElementById("tipoClienteConciliacion").value || "";
                    const codigo = document.getElementById("codigoConciliacion").value ? document.getElementById("codigoConciliacion").value.trim() : "";
                    const ciclo = document.getElementById("cicloConciliacion").value ? document.getElementById("cicloConciliacion").value.trim() : "";
                    const ctaBancaria = document.getElementById("ctaBancariaConciliacion").value ? document.getElementById("ctaBancariaConciliacion").value.trim() : "";
                    swal({ text: "Consultando, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                    $.ajax({
                        url: "/Operaciones/ConsultarConciliacion/",
                        type: "POST",
                        data: { empresa: empresa, fecha: fecha, tipoCliente: tipoCliente, codigo: codigo, ciclo: ciclo, ctaBancaria: ctaBancaria },
                        dataType: "json",
                        timeout: 60000
                    }).done(function (respuesta) {
                        swal.close();
                        if (!respuesta || !respuesta.success) {
                            var msg = (respuesta && respuesta.mensaje) ? respuesta.mensaje : "Error en la respuesta.";
                            if (respuesta && respuesta.error && respuesta.error.length > 0) msg += " Detalle: " + respuesta.error;
                            showError(msg);
                            return;
                        }
                        const d = respuesta.datos || {};
                        const resumen = d.resumen || {};
                        const filas = d.filas || [];
                        document.getElementById("totalPagos").textContent = resumen.totalRegistros != null ? resumen.totalRegistros : 0;
                        document.getElementById("importeTotal").textContent = "$ " + (resumen.totalImporte != null ? Number(resumen.totalImporte) : 0).toLocaleString("es-MX", { minimumFractionDigits: 2 });
                        document.getElementById("totalPagosConciliados").textContent = resumen.totalConciliados != null ? resumen.totalConciliados : 0;
                        document.getElementById("importeConciliados").textContent = "$ " + (resumen.importeConciliados != null ? Number(resumen.importeConciliados) : 0).toLocaleString("es-MX", { minimumFractionDigits: 2 });
                        document.getElementById("totalPagosPendientes").textContent = resumen.totalNoConciliados != null ? resumen.totalNoConciliados : 0;
                        document.getElementById("importePendientes").textContent = "$ " + (resumen.importeNoConciliados != null ? Number(resumen.importeNoConciliados) : 0).toLocaleString("es-MX", { minimumFractionDigits: 2 });
                        var tabla = $("#tablaConciliacion");
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable(tabla)) {
                            tabla.DataTable().destroy();
                            tabla.find("tbody").empty();
                        }
                        var tbody = document.getElementById("tablaConciliacionBody");
                        tbody.innerHTML = "";
                        var payloadParaSp = function(f) {
                            var tipo = (f.CLNS || "").toString().trim();
                            if (tipo === "" && f.TIPOCTE) tipo = (f.TIPOCTE + "").toUpperCase().indexOf("GRUPAL") >= 0 ? "G" : "I";
                            return { CDGEM: f.CDGEM || "", CDGCLNS: f.CDGCLNS || "", CICLO: f.CICLO || "", CLNS: tipo || "I", FREALDEP: f.FREALDEP || "", PERIODO: f.PERIODO != null ? f.PERIODO : 0, SECUENCIA: f.SECUENCIA || "", CANTIDAD: typeof f.CANTIDAD === "number" ? f.CANTIDAD : parseFloat(f.CANTIDAD) || 0, CDGCB: f.CDGCB || "" };
                        };
                        filas.forEach(function (f, idx) {
                            var tr = document.createElement("tr");
                            var monto = typeof f.CANTIDAD === "number" ? f.CANTIDAD : parseFloat(f.CANTIDAD) || 0;
                            var conciliado = (f.CONCILIADO || "").toString().toUpperCase() === "C";
                            var estado = conciliado ? "Conciliado" : "Pendiente";
                            var estadoClase = conciliado ? "label-success" : "label-warning";
                            var payload = payloadParaSp(f);
                            var chk = conciliado ? "" : "<input type=\"checkbox\" class=\"chkPagoConciliacion\" data-fila='" + JSON.stringify(payload).replace(/'/g, "&#39;") + "' />";
                            tr.innerHTML =
                                "<td>" + chk + "</td>" +
                                "<td>" + (idx + 1) + "</td>" +
                                "<td>" + (f.CDGEM || f.cdgem || "-") + "</td>" +
                                "<td>" + (f.FREALDEP || f.frealdep || "-") + "</td>" +
                                "<td>" + (f.REFERENCIA || f.referencia || "-") + "</td>" +
                                "<td>" + (f.TIPOCTE || f.tipocte || "-") + "</td>" +
                                "<td>" + (f.CDGCLNS || f.cdgclns || "-") + "</td>" +
                                "<td>" + (f.CICLO || f.ciclo || "-") + "</td>" +
                                "<td>" + (f.PERIODO != null ? (f.PERIODO !== undefined ? f.PERIODO : f.periodo) : "-") + "</td>" +
                                "<td>" + (f.SECUENCIAIM || f.secuenciaim || "-") + "</td>" +
                                "<td>" + (f.NOMBRE || f.nombre || "-") + "</td>" +
                                "<td>$ " + monto.toLocaleString("es-MX", { minimumFractionDigits: 2 }) + "</td>" +
                                "<td>" + (f.CDGCB || f.cdgcb || "-") + "</td>" +
                                "<td>" + (f.CDGNS || f.cdgns || "-") + "</td>" +
                                "<td>" + (f.TASA != null ? (f.TASA !== undefined ? f.TASA : f.tasa) : "-") + "</td>" +
                                "<td>" + (f.SECUENCIA || f.secuencia || "-") + "</td>" +
                                "<td>" + (f.PLAZO != null ? (f.PLAZO !== undefined ? f.PLAZO : f.plazo) : "-") + "</td>" +
                                "<td>" + (f.PERIODICIDAD || f.periodicidad || "-") + "</td>" +
                                "<td><span class=\"label " + estadoClase + "\" style=\"border-radius: 4px; padding: 4px 8px;\">" + estado + "</span></td>";
                            tbody.appendChild(tr);
                        });
                        if ($.fn.DataTable) {
                            tabla.DataTable({
                                lengthMenu: [[20, 50, 100, -1], [20, 50, 100, "Todos"]],
                                pageLength: 20,
                                order: [[3, "asc"]],
                                language: {
                                    emptyTable: "No hay datos disponibles",
                                    paginate: { previous: "Anterior", next: "Siguiente" },
                                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                                    infoEmpty: "Sin registros",
                                    zeroRecords: "No se encontraron registros",
                                    lengthMenu: "Mostrar _MENU_ registros",
                                    search: "Buscar:"
                                }
                            });
                        }
                    }).fail(function () {
                        swal.close();
                        showError("Error de conexión al consultar.");
                    });
                };
                const conciliarPagos = () => {
                    var checks = document.querySelectorAll("#tablaConciliacionBody .chkPagoConciliacion:checked");
                    if (!checks || checks.length === 0) {
                        showError("Seleccione al menos un pago para conciliar.");
                        return;
                    }
                    var pagos = [];
                    checks.forEach(function (el) {
                        try {
                            var data = el.getAttribute("data-fila");
                            if (data) pagos.push(JSON.parse(data.replace(/&#39;/g, "'")));
                        } catch (e) {}
                    });
                    if (pagos.length === 0) {
                        showError("No se pudieron leer los datos de los pagos seleccionados.");
                        return;
                    }
                    confirmarMovimiento("Conciliar pagos", "¿Ejecutar conciliación de " + pagos.length + " pago(s) seleccionado(s)?").then(function (ok) {
                        if (!ok) return;
                        swal({ text: "Conciliando, espere...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                        $.ajax({
                            url: "/Operaciones/ConciliarPagos/",
                            type: "POST",
                            data: { pagos: JSON.stringify(pagos) },
                            dataType: "json",
                            timeout: 3600000
                        }).done(function (respuesta) {
                            swal.close();
                            if (respuesta && respuesta.success) {
                                showSuccess(respuesta.mensaje).then(function () {
                                    consultarConciliacion();
                                });
                            } else {
                                var msg = (respuesta && respuesta.mensaje) ? respuesta.mensaje : "Error al conciliar.";
                                if (respuesta && respuesta.error) msg += " " + respuesta.error;
                                showError(msg);
                            }
                        }).fail(function (xhr, textStatus, errorThrown) {
                            swal.close();
                            var msg = "Error de conexión al conciliar.";
                            if (textStatus === "timeout") msg = "La solicitud tardó demasiado (timeout). El proceso puede seguir ejecutándose en el servidor. Revise la conciliación o intente de nuevo.";
                            else if (xhr && xhr.responseJSON) {
                                if (xhr.responseJSON.mensaje) msg = xhr.responseJSON.mensaje;
                                if (xhr.responseJSON.error) msg += " " + xhr.responseJSON.error;
                            } else if (xhr && xhr.responseText && xhr.responseText.length < 500) msg = xhr.responseText;
                            showError(msg);
                        });
                    });
                };
                $(document).ready(function () {
                    document.getElementById("btnConsultarConciliacion").onclick = consultarConciliacion;
                    document.getElementById("btnConciliarPagos").onclick = conciliarPagos;
                    $(document).on("change", "#chkTodosConciliacion", function () {
                        var checked = this.checked;
                        $("#tablaConciliacionBody .chkPagoConciliacion").each(function () { this.checked = checked; });
                    });
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('empresas', ['' => '(Todas)', 'EMPFIN' => 'EMPFIN']);
        View::render('operaciones_conciliacion_pagos');
    }

    /**
     * POST: empresa, fecha, tipoCliente, codigo, ciclo, ctaBancaria. Devuelve JSON con resumen y filas (consulta MP, solo lectura).
     */
    public function ConsultarConciliacion()
    {
        try {
            $empresa = isset($_POST['empresa']) ? trim((string) $_POST['empresa']) : '';
            $fecha = isset($_POST['fecha']) ? trim((string) $_POST['fecha']) : '';
            $tipoCliente = isset($_POST['tipoCliente']) ? trim((string) $_POST['tipoCliente']) : '';
            $codigo = isset($_POST['codigo']) ? trim((string) $_POST['codigo']) : '';
            $ciclo = isset($_POST['ciclo']) ? trim((string) $_POST['ciclo']) : '';
            $ctaBancaria = isset($_POST['ctaBancaria']) ? trim((string) $_POST['ctaBancaria']) : '';
            $respuesta = ConciliacionService::buscarPagosConciliacion($empresa, $fecha, $tipoCliente, $codigo, $ciclo, $ctaBancaria);
        } catch (\Throwable $e) {
            $respuesta = [
                'success' => false,
                'mensaje' => 'Error al consultar la conciliación.',
                'error' => $e->getMessage(),
            ];
        }

        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        $json = json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = '{"success":false,"mensaje":"Error al generar la respuesta."}';
        }
        echo $json;
        exit;
    }

    /**
     * POST: pagos (JSON array de objetos con CDGEM, CDGCLNS, CICLO, CLNS/TIPOCTE, FREALDEP, PERIODO, SECUENCIA, CANTIDAD, CDGCB).
     * Ejecuta conciliación vía ConciliacionService; devuelve JSON.
     */
    public function ConciliarPagos()
    {
        set_time_limit(0);
        try {
            $pagos = isset($_POST['pagos']) ? $_POST['pagos'] : '';
            if (is_string($pagos)) {
                $pagos = json_decode($pagos, true);
            }
            if (!is_array($pagos)) {
                $pagos = [];
            }
            $usuario = isset($this->__usuario) ? (string) $this->__usuario : '';
            $respuesta = ConciliacionService::conciliarPagos($pagos, $usuario);
        } catch (\Throwable $e) {
            $respuesta = [
                'success' => false,
                'mensaje' => 'Error al conciliar pagos.',
                'error' => $e->getMessage(),
            ];
        }

        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        $json = json_encode($respuesta, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = '{"success":false,"mensaje":"Error al generar la respuesta."}';
        }
        echo $json;
        exit;
    }

    ////////////////////////////////////////////////////////////////////

    public function ReportePC()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
                {$this->configuraTabla}
                {$this->descargaExcel}
                {$this->formatoMoneda}

                const idTabla = "reporte"

                const consultaReporte = () => {
                    consultaServidor("/Operaciones/GetReportePC", getPerametros(), (res) => {
                        if (!res.success) return resultadoError(res.mensaje)
                        resultadoOK(res.datos)
                    })
                }

                const getPerametros = () => {
                    const fechaI = $("#fechaI").val()
                    const fechaF = $("#fechaF").val()

                    return { fechaI, fechaF }
                }

                const resultadoError = (mensaje) => {
                    $(".resultado").toggleClass("conDatos", false)
                    showError(mensaje).then(() => actualizaDatosTabla(idTabla, []))
                }

                const resultadoOK = (datos) => {
                    datos = datos.map((item) => {
                        item.MONTO = "$ " + formatoMoneda(item.MONTO)
                        return item
                    })

                    actualizaDatosTabla(idTabla, datos)
                    $(".resultado").toggleClass("conDatos", true)
                }

                const getExcel = () => {
                    descargaExcel("/Operaciones/GetReportePC_excel/?" + $.param(getPerametros()))
                }

                $(document).ready(() => {
                    $("#fechaI").change(consultaReporte)
                    $("#fechaF").change(consultaReporte)
                    $("#excel").click(getExcel)

                    configuraTabla(idTabla)
                    consultaReporte()
                })
            </script>
        HTML;



        View::set('header', $this->_contenedor->header($this->getExtraHeader("Reporte Productora Cultiva")));
        View::set('footer', $this->_contenedor->footer($extraFooter));

        View::render('operaciones_reporte_pc');
    }

    public function GetReportePC($datos = null)
    {
        echo json_encode(OperacionesDao::GetReportePC($_POST));
    }

    public function GetReportePC_excel()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $centrado = ['estilo' => $estilos['centrado']];
        $texto = ['estilo' => $estilos['texto_centrado']];

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('CDGNS', 'Crédito', $texto),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'Ciclo', $texto),
            \PHPSpreadsheet::ColumnaExcel('PLAZO', 'Plazo (semanas)', $texto),
            \PHPSpreadsheet::ColumnaExcel('TASA', 'Tasa', $texto),
            \PHPSpreadsheet::ColumnaExcel('INICIO', 'Fecha inicio', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('FECHA_FIN', 'Fecha fin', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('CANTENTRE', 'Cantidad entregada', $texto),
            \PHPSpreadsheet::ColumnaExcel('TOTAL_CANTIDAD', 'Total prestamo', $texto),

            // Cliente
            \PHPSpreadsheet::ColumnaExcel('CDGCL_CLIENTE', 'Clave cliente', $texto),
            \PHPSpreadsheet::ColumnaExcel('CLIENTE', 'Nombre cliente', $texto),
            \PHPSpreadsheet::ColumnaExcel('TELEFONO_CLIENTE', 'Teléfono cliente', $texto),
            \PHPSpreadsheet::ColumnaExcel('DIRECCION_COMPLETA_CLIENTE', 'Dirección cliente', $texto),

            // Aval 1
            \PHPSpreadsheet::ColumnaExcel('CDGCL_AVAL1', 'Clave aval 1', $texto),
            \PHPSpreadsheet::ColumnaExcel('AVAL1', 'Nombre aval 1', $texto),
            \PHPSpreadsheet::ColumnaExcel('TELEFONO_AVAL1', 'Teléfono aval 1', $texto),
            \PHPSpreadsheet::ColumnaExcel('DIRECCION_COMPLETA_AVAL1', 'Dirección aval 1', $texto),

            // Aval 2
            \PHPSpreadsheet::ColumnaExcel('CDGCL_AVAL2', 'Clave aval 2', $texto),
            \PHPSpreadsheet::ColumnaExcel('AVAL2', 'Nombre aval 2', $texto),
            \PHPSpreadsheet::ColumnaExcel('TELEFONO_AVAL2', 'Teléfono aval 2', $texto),
            \PHPSpreadsheet::ColumnaExcel('DIRECCION_COMPLETA_AVAL2', 'Dirección aval 2', $texto),

            // Aval 3
            \PHPSpreadsheet::ColumnaExcel('CDGCL_AVAL3', 'Clave aval 3', $texto),
            \PHPSpreadsheet::ColumnaExcel('AVAL3', 'Nombre aval 3', $texto),
            \PHPSpreadsheet::ColumnaExcel('TELEFONO_AVAL3', 'Teléfono aval 3', $texto),
            \PHPSpreadsheet::ColumnaExcel('DIRECCION_COMPLETA_AVAL3', 'Dirección aval 3', $texto),
        ];

        $filas = OperacionesDao::GetReportePC($_GET);
        $filas = $filas['success'] ? $filas['datos'] : [];

        \PHPSpreadsheet::DescargaExcel('Consolidado Clientes y Avales', 'Reporte', 'Consolidado Clientes y Avales', $columnas, $filas);
    }





}
