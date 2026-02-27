<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\AhorroConsulta as AhorroConsultaDao;

class AhorroConsulta extends Controller
{
    private $_contenedor;

    public function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function index()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
                {$this->configuraTabla}
                {$this->descargaExcel}
                {$this->formatoMoneda}
                {$this->parseaNumero}

                const idTabla = "tablaRetiros"
                
                const consultaSolicitudes = () => {
                    consultaServidor("/AhorroConsulta/GetRetirosAhorro", getPerametros(), (res) => {
                        if (!res.success) return resultadoError(res.mensaje)
                        resultadoOK(res.datos)
                    })
                }

                const getPerametros = () => {
                    const fechaI = $("#fechaI").val()
                    const fechaF = $("#fechaF").val()

                    return { fechaI, fechaF }
                }

                const resultadoOK = (datos) => {
                    datos = datos.map((item) => {
                        const acciones = [
                            {
                                icono: "fa-eye text-info",
                                texto: "Ver detalle",
                                funcion: "verDetalle(" + item.ID + ")"
                            }
                        ]

                        return [
                            item.ID,
                            item.CDGNS,
                            "$ " + formatoMoneda(item.CANT_SOLICITADA),
                            getFechas(item.FECHA_CREACION, item.FECHA_SOLICITUD, item.FECHA_ENTREGA, ["V", "E"].includes(item.ESTATUS) ? item.ULTIMA_LLAMADA : null, item.ESTATUS === "C" ? item.FECHA_CANCELACION : null, item.ESTATUS === "R" ? item.FECHA_CANCELACION : null, item.ESTATUS === "E" ? item.FECHA_ENTREGA_REAL : null, item.ESTATUS === "D" ? item.FECHA_DEVOLUCION : null),
                            getBadge(item.ESTATUS),
                            menuAcciones(acciones)
                        ]
                    })

                    actualizaDatosTabla(idTabla, datos)
                    $(".resultado").toggleClass("conDatos", true)
                }

                const getFechas = (fechaCreacion, fechaSolicitud, fechaEntrega, fechaValidacion = null, fechaCancelacion = null, fechaRechazo = null, fechaEntregaReal = null, fechaDevolucion = null) => {
                    const titulos = {
                        "Creación": fechaCreacion,
                        "Solicitud": fechaSolicitud,
                        "Entrega Programada": fechaEntrega
                    }
                    
                    if (fechaValidacion) titulos["Validación"] = fechaValidacion
                    if (fechaCancelacion) titulos["Cancelación"] = fechaCancelacion
                    if (fechaRechazo) titulos["Rechazo"] = fechaRechazo
                    if (fechaEntregaReal) titulos["Entrega Real"] = fechaEntregaReal
                    if (fechaDevolucion) titulos["Devolución"] = fechaDevolucion
                    
                    let resultado = "<div style='text-align: left;'>"
                    Object.entries(titulos).forEach(([key, value]) => {
                        resultado += "<strong>" + key + ":</strong> " + value + "<br/>"
                    })
                    resultado += "</div>"

                    return resultado
                }

                const getBadge = (estatus) => {
                    const badges = {
                        P: {
                            clase: "default",
                            texto: "PENDIENTE",
                        },
                        E: {
                            clase: "success",
                            texto: "ENTREGADO",
                        },
                        R: {
                            clase: "danger",
                            texto: "RECHAZADO",
                        },
                        C: {
                            clase: "warning",
                            texto: "CANCELADO",
                        },
                        V: {
                            clase: "info",
                            texto: "VALIDADO",
                        },
                        D: {
                            clase: "dark",
                            texto: "DEVUELTO",
                        }
                    }

                    const {clase, texto} = badges[estatus] || badges.P
                    return "<span class='badge alert-" + clase + "'>" + texto + "</span>"
                }

                const menuAcciones = (opciones) => {
                    const acciones = opciones
                        .map((opcion) => {
                            if (opcion === null || opcion === undefined) return ""
                            if (opcion instanceof HTMLElement) return opcion.outerHTML
                            if (opcion instanceof jQuery) return opcion[0].outerHTML
                            if (typeof opcion === "string") {
                                if (opcion === "divisor") return `<div class="dropdown-divider"></div>`
                                return opcion
                            }

                            return '<li><a href="' + (opcion.href || "javascript:;") + 
                            '" onclick="' + opcion.funcion + '">' +
                            '<i class="fa ' + opcion.icono + '">&nbsp;</i>' + opcion.texto + '</a></li>'
                        })
                        .join("")

                    return '<div class="dropdown"><button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button><ul class="dropdown-menu">' + acciones + '</ul></div>'
                }

                const resultadoError = (mensaje) => {
                    $(".resultado").toggleClass("conDatos", false)
                    showError(mensaje).then(() => actualizaDatosTabla(idTabla, []))
                }

                const verDetalle = (id) => {
                    consultaServidor("/AhorroConsulta/GetRetiroById", { id: id }, (res) => {
                        if (!res.success) return showError(res.mensaje);
                        
                        const datos = res.datos;
                        
                        $("#detalle_id_retiro").val(datos.ID || "");
                        $("#detalle_credito").val(datos.CDGNS || "");
                        $("#detalle_fecha_creacion").val(datos.FECHA_CREACION || "");
                        $("#detalle_fecha_solicitud").val(datos.FECHA_SOLICITUD || "");
                        $("#detalle_fecha_entrega").val(datos.FECHA_ENTREGA || "");
                        $("#detalle_fecha_entrega_real").val(datos.FECHA_ENTREGA_REAL || "");
                        $("#detalle_cantidad_solicitada").val("$" + formatoMoneda(datos.CANT_SOLICITADA || 0));
                        $("#detalle_estatus").val(datos.ESTATUS_ETIQUETA || "");

                        if (["C", "R"].includes(datos.ESTATUS)) {
                            const titulo = "Motivo de " + (datos.ESTATUS === "C" ? "cancelación" : "rechazo");
                            $("#detalle_titulo_motivo").text(titulo);
                            $("#detalle_motivo").val(datos.MOTIVO_CANCELACION || "");
                            $("#grupo_motivo_cancelacion").show();
                        } else if (datos.ESTATUS === "D") {
                            $("#detalle_titulo_motivo").text("Motivo de devolución");
                            $("#detalle_motivo").val(datos.COMENTARIO_DEVOLUCION || "");
                            $("#grupo_motivo_cancelacion").show();
                        } else {
                            $("#detalle_motivo").val("");
                            $("#grupo_motivo_cancelacion").hide();
                        }

                        $("#detalle_cdgpe_administradora").val(datos.CDGPE_ADMINISTRADORA || "");
                        $("#detalle_nombre_administradora").val(datos.NOMBRE_ADMINISTRADORA || "");
                        $("#detalle_observaciones_administradora").val(datos.OBSERVACIONES_ADMINISTRADORA || "");
                        $("#detalle_estatus_call_center").val(datos.ESTATUS_CC_ETIQUETA || "");
                        $("#detalle_cdgpe_call_center").val(datos.CDGPE_CC || "");
                        $("#detalle_fecha_procesa_call_center").val(datos.ULTIMA_LLAMADA || "");
                        $("#detalle_observaciones_call_center").val(datos.COMENTARIO_EXTERNO || "");
                        
                        $("#btnVerComprobante").off("click").on("click", () => verComprobante(datos.ID, datos.TIPO_FOTO))
                        
                        $('#modalDetalle .nav-tabs a[href="#tabGeneral"]').tab('show');
                        $("#modalDetalle").modal("show");
                    });
                }

                const verComprobante = (id, tipo) => {
                    $("#comprobanteImg").attr("src", "").hide();
                    $("#comprobantePdf").attr("src", "").hide();
                    $("#loadingImg").show();
                    $("#modalDetalle").modal("hide");

                    const contendor = tipo === "application/pdf" ? $("#comprobantePdf") : $("#comprobanteImg");
                    contendor.attr("src", "/AhorroConsulta/GetImgSolicitud/?id=" + id + "&tipo=comprobante");
                    
                    $("#modalComprobante").modal("show");
                }

                const nuevaSolicitud = () => {
                    resetFomRetiro();
                    
                    $("#modalNuevaSolicitud").modal("show");
                }

                const resetFomRetiro = () => {
                    const hoy = new Date().toISOString().split("T")[0];

                    $("#formNuevaSolicitud")[0].reset();
                    $("#nueva_cdgns").val("");
                    $("#saldo_ahorro_disponible").val("");
                    $("#nueva_fecha_solicitud").val(hoy);
                    $("#nueva_fecha_entrega").val(calculaFechaEntrega());

                    $("#nueva_cantidad_solicitada").prop("disabled", true);
                    $("#nueva_fecha_solicitud").prop("disabled", true);
                    $("#nueva_observaciones_administradora").prop("disabled", true);
                    $("#nueva_foto").prop("disabled", true);
                    $("#btnGuardarNuevaSolicitud").prop("disabled", true);
                }

                const calculaFechaEntrega = () => {
                    const fecha = new Date()
                    const diaSemana = fecha.getDay()

                    const dias = {
                        1: 2,
                        2: 2,
                        3: 5,
                        4: 4,
                        5: 4
                    }

                    if (dias[diaSemana]) fecha.setDate(fecha.getDate() + dias[diaSemana])
                    else fecha.setDate(fecha.getDate() + 3)

                    return fecha.toISOString().split("T")[0]
                }

                const buscarCredito = () => {
                    const cdgns = $("#cdgns_buscar").val().trim();
                    resetFomRetiro()

                    if (!cdgns || cdgns.length !== 6) return showError("El crédito debe tener 6 dígitos");

                    consultaServidor("/AhorroConsulta/BuscarSaldo", { cdgns }, (res) => {
                        if (!res.success) return showError(res.mensaje);
                        const datos = res.datos
                        
                        if (datos.length === 0) return showError("No se encontró el crédito especificado.");
                        // Permisos temporales para pruebas de retiro de ahorro a 'FLHR'
                        if (("{$_SESSION['perfil']}" !== 'ADMIN' && "{$_SESSION['usuario']}" !== 'FLHR') && datos.CDGCO !== "{$_SESSION['cdgco']}") return showError("El crédito no pertenece a su sucursal, no es posible realizar retiros de ahorro.");
                        if (parseInt(datos?.DIAS_MORA_TRADICIONAL) > 1) return showError("El cliente tiene mora en su crédito tradicional, no es posible realizar retiros de ahorro.");
                        if (datos?.SITUACION_ADICIONAL === 'E') return showError("El cliente cuenta con un crédito adicional activo, no es posible realizar retiros de ahorro.");
                        if (parseInt(datos?.DIAS_MORA_ADICIONAL) > 1) return showError("El cliente tiene mora en su crédito adicional, no es posible realizar retiros de ahorro.");
                        
                        const saldo = parseaNumero(datos?.SALDO_ACTUAL);

                        if (saldo <= 0) return showError("El crédito no tiene saldo disponible para retiro.")

                        $("#nombre_cliente").val(datos?.NOMBRE_CLIENTE);
                        $("#nueva_cdgns").val(cdgns);
                        $("#nueva_ciclo").val(datos?.ULTIMO_CICLO_TRADICIONAL);
                        $("#aniversario_ahorro").val(datos?.ANIVERSARIO);
                        $("#saldo_ahorro_disponible").val(saldo);
                        $("#nueva_cantidad_solicitada").prop("disabled", false);
                        $("#nueva_fecha_solicitud").prop("disabled", false);
                        $("#nueva_fecha_entrega").prop("disabled", false);
                        $("#nueva_observaciones_administradora").prop("disabled", false);
                        $("#nueva_foto").prop("disabled", false);
                        $("#btnGuardarNuevaSolicitud").prop("disabled", false);
                    });
                }

                const guardarNuevaSolicitud = () => {
                    const aniversario = new Date($("#aniversario_ahorro").val());
                    const cantidadSolicitada = parseaNumero($("#nueva_cantidad_solicitada").val());
                    const saldo = parseaNumero($("#saldo_ahorro_disponible").val());
                    if (cantidadSolicitada > saldo) return showError("La cantidad solicitada no puede ser mayor al saldo disponible ($" + formatoMoneda(saldo) + ")");
                    
                    const cdgns = $("#nueva_cdgns").val().trim();
                    const fechaSolicitud = $("#nueva_fecha_solicitud").val();
                    const fechaEntrega = $("#nueva_fecha_entrega").val();
                    
                    if (!cdgns || cdgns.length !== 6) return showError("El crédito debe tener 6 dígitos");
                    if (!cantidadSolicitada || parseFloat(cantidadSolicitada) <= 0) return showError("Debe ingresar una cantidad solicitada válida mayor a 0");
                    if (!fechaSolicitud) return showError("Debe seleccionar la fecha de solicitud");
                    if (!fechaEntrega) return showError("Debe seleccionar la fecha de entrega solicitada");    
                    
                    const archivo = $("#nueva_foto")[0].files[0];
                    if (!archivo) return showError("Debe seleccionar un archivo");
                    if (archivo && archivo.size > 5242880) return showError("El archivo no debe superar los 5MB");

                    let mensaje = document.createElement("div");
                    mensaje.innerHTML = "¿Confirma el registro del retiro de ahorro por la cantidad de <strong>$" + formatoMoneda(cantidadSolicitada) + "</strong> para el crédito <strong>" + cdgns + "</strong>?";
                    
                    if (new Date() < aniversario) {
                        mensaje.innerHTML = "El ahorro aún no ha cumplido su aniversario<br/>se aplicaran las penalizaciones establecidas en las políticas.<br/>" + mensaje.innerHTML;
                    }
                    
                    confirmarMovimiento("Registro de retiro de ahorro", null, mensaje)
                        .then((continuar) => {
                        if (continuar) {
                            const formData = new FormData();
                            formData.append("cdgns", cdgns);
                            formData.append("ciclo", $("#nueva_ciclo").val());
                            formData.append("cantidad_solicitada", cantidadSolicitada);
                            formData.append("fecha_solicitud", fechaSolicitud);
                            formData.append("fecha_entrega", fechaEntrega);
                            formData.append("observaciones_administradora", $("#nueva_observaciones_administradora").val() || "");
                            formData.append("cdgpe_administradora", "{$_SESSION['usuario']}");
                            
                            if (archivo) formData.append("foto", archivo)
                            
                            consultaServidor("/AhorroConsulta/InsertRetiro", formData, (res) => {
                                if (!res.success) return showError(res.mensaje)
                                showSuccess(res.mensaje)
                                $("#modalNuevaSolicitud").modal("hide");
                                consultaSolicitudes()
                            }, "POST", "JSON", false, false)
                        }
                    });
                }

                $(document).ready(function() {
                    $("#fechaI").change(consultaSolicitudes)
                    $("#fechaF").change(consultaSolicitudes)
                    
                    $("#btnBuscar").click(consultaSolicitudes)
                    $("#btnNuevaSolicitud").click(nuevaSolicitud)
                    $("#btnGuardarNuevaSolicitud").click(guardarNuevaSolicitud)

                    $("#comprobanteImg, #comprobantePdf").on("load", function() {
                        if (!$(this).attr("src")) return;

                        $("#loadingImg").hide();
                        $(this).show();
                    });

                    const hoy = new Date().getDate()
                    const fechaI = new Date().setDate(hoy - 7);
                    const fechaF = new Date().setDate(hoy + 7);
                    $("#fechaI").val(new Date(fechaI).toISOString().split("T")[0]);
                    $("#fechaF").val(new Date(fechaF).toISOString().split("T")[0]);

                    $("#btnBuscarCredito").click(buscarCredito)

                    configuraTabla(idTabla)
                    consultaSolicitudes()
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Ahorro Consulta")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("AhorroConsulta/ahorro_consulta");
    }

    public function GetRetirosAhorro()
    {
        echo json_encode(AhorroConsultaDao::GetRetirosAhorro($_POST));
    }

    public function GetRetiroById()
    {
        echo json_encode(AhorroConsultaDao::getRetiroById($_POST));
    }

    public function BuscarSaldo()
    {
        echo json_encode(AhorroConsultaDao::BuscarSaldo($_POST));
    }

    public function InsertRetiro()
    {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $_POST['foto'] = fopen($_FILES['foto']['tmp_name'], 'rb');
            $_POST['tipo_foto'] = $_FILES['foto']['type'];
        }

        $result = AhorroConsultaDao::insertRetiro($_POST);
        echo json_encode($result);
        if ($_POST['foto']) fclose($_POST['foto']);
        return true;
    }

    public function GetImgSolicitud()
    {
        $result = AhorroConsultaDao::getImgSolicitud($_GET);

        if (!$result['success'] || !$result['datos'] || !$result['datos']['FOTO']) {
            http_response_code(404);
            echo "Imagen no encontrada";
            return;
        }

        $archivo = $result['datos']['FOTO'];
        $tipoArchivo = $result['datos']['TIPO_FOTO'] ?? 'image/jpeg';
        $contenido = is_resource($archivo) ? stream_get_contents($archivo) : $archivo;

        header('Content-Type: ' . $tipoArchivo);
        header('Content-Length: ' . strlen($contenido));
        if ($tipoArchivo === 'application/pdf') {
            header('Content-Disposition: inline; filename="comprobante.pdf"');
            header('Accept-Ranges: bytes');
        } else {
            header('Content-Disposition: inline');
        }

        ob_clean();
        flush();
        echo $contenido;
        return;
    }
}
