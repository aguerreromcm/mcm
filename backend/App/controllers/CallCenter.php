<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

include_once dirname(__DIR__) . '/../libs/PHPMailer/Mensajero.php';

use \Core\View;
use \Core\App;
use \Core\MasterDom;
use \Core\Controller;
use \App\models\CallCenter as CallCenterDao;
use Mensajero;

class CallCenter extends Controller
{
    private $_contenedor;
    private $configuracion;

    function __construct()
    {
        parent::__construct();
        $this->configuracion = App::getConfig();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function Pendientes()
    {
        $tabla = "";
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->confirmarMovimiento}
                {$this->consultaServidor}

                const APROBADO = "aprobado";
                const RECHAZADO = "rechazado";
                const PENDIENTE = "pendiente";

                function getParameterByName(name) {
                    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]")
                    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                        results = regex.exec(location.search)
                    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "))
                }

                $("#doce_cl").on("change", function () {
                    if (this.value == "N") {
                        swal(
                            "Atención",
                            "Al finalizar la encuesta cancele la solicitud, no cumple con la política de seguridad de la pregunta #12",
                            "warning"
                        )
                    }
                })

                $(document).ready(function () {
                    $("#muestra-cupones").tablesorter()
                    var oTable = $("#muestra-cupones").DataTable({
                        lengthMenu: [
                            [6, 10, 20, 30, -1],
                            [6, 10, 20, 30, "Todos"]
                        ],
                        columnDefs: [
                            {
                                orderable: false,
                                targets: 0
                            }
                        ],
                        order: false
                    })
                    // Remove accented character from search input as well
                    $("#muestra-cupones input[type=search]").keyup(function () {
                        var table = $("#example").DataTable()
                        table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw()
                    })
                    var checkAll = 0

                    $("#guarda_internos").on("click", () => {
                        guardaComentariosRetiro()
                    })
                    $("#guarda_externos").on("click", () => {
                        guardaComentariosRetiro(false)
                    })
                    $("#guarda_encuesta_retiro").on("click", guardaEncuestaRetiro)
                    $("#termina_retiro").on("click", finalizaSolicitudRetiro)
                })

                function InfoDesactivaEncuesta() {
                    swal(
                        "Atención",
                        "Para continuar con la ENCUESTA del AVAL por favor, es nesesario completar la PRIMER LLAMADA del cliente. ",
                        "warning"
                    )
                }

                function enviar_add_cl(adicional = 0) {
                    fecha_trabajo = document.getElementById("fecha_cl").value
                    ciclo = document.getElementById("ciclo_cl").value
                    num_telefono = document.getElementById("movil_cl").value
                    tipo_cl = document.getElementById("tipo_llamada_cl").value
                    uno = document.getElementById("uno_cl").value
                    dos = document.getElementById("dos_cl").value
                    tres = document.getElementById("tres_cl").value
                    cuatro = document.getElementById("cuatro_cl").value
                    cinco = document.getElementById("cinco_cl").value
                    seis = document.getElementById("seis_cl").value
                    siete = document.getElementById("siete_cl").value
                    ocho = document.getElementById("ocho_cl").value
                    nueve = document.getElementById("nueve_cl").value
                    diez = document.getElementById("diez_cl").value
                    once = document.getElementById("once_cl").value
                    doce = document.getElementById("doce_cl").value

                    completo = $('input[name="completo"]:checked').val()
                    llamada = document.getElementById("titulo")
                    contenido = llamada.innerHTML

                    if (contenido == "2") {
                        mensaje = ""
                    } else {
                        if (completo == "1") {
                            mensaje =
                                "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro."
                        } else {
                            mensaje = ""
                        }
                    }

                    if (completo == "0") {
                        if (tipo_cl == "") {
                            swal("Seleccione el tipo de llamada que realizo", { icon: "warning" })
                        } else {
                            swal({
                                title: "¿Está segura de continuar con una llamada incompleta?",
                                text: mensaje,
                                icon: "warning",
                                buttons: ["Cancelar", "Continuar"],
                                dangerMode: false
                            }).then((willDelete) => {
                                if (willDelete) {
                                    const agregar_CL = document.getElementById("agregar_CL")
                                    agregar_CL.disabled = true

                                    $.ajax({
                                        type: "POST",
                                        url: "/CallCenter/PagosAddEncuestaCL/",
                                        data: $("#Add_cl").serialize() + "&contenido=" + contenido,
                                        success: function (respuesta) {
                                            if (respuesta == "1") {
                                                swal("Registro guardado exitosamente", {
                                                    icon: "success"
                                                })
                                                location.reload()
                                            } else {
                                                $("#modal_encuesta_cliente").modal("hide")
                                                swal(respuesta, {
                                                    icon: "error"
                                                })
                                            }
                                        }
                                    })
                                } else {
                                    swal("Continúe con su registro", { icon: "success" })
                                }
                            })
                        }
                    } else {
                        if (tipo_cl == "") {
                            swal("Seleccione el tipo de llamada que realizo", { icon: "warning" })
                        } else if (uno == "") {
                            swal("Seleccione una opción para la pregunta #1", { icon: "warning" })
                        } else if (dos == "") {
                            swal("Seleccione una opción para la pregunta #2", { icon: "warning" })
                        } else if (tres == "") {
                            swal("Seleccione una opción para la pregunta #3", { icon: "warning" })
                        } else if (cuatro == "") {
                            swal("Seleccione una opción para la pregunta #4", { icon: "warning" })
                        } else if (cinco == "" && adicional == 0) {
                            swal("Seleccione una opción para la pregunta #5", { icon: "warning" })
                        } else if (seis == "" && adicional == 0) {
                            swal("Seleccione una opción para la pregunta #6", { icon: "warning" })
                        } else if (siete == "" && adicional == 0) {
                            swal("Seleccione una opción para la pregunta #7", { icon: "warning" })
                        } else if (ocho == "" && adicional == 0) {
                            swal("Seleccione una opción para la pregunta #8", { icon: "warning" })
                        } else if (nueve == "" && adicional == 0 ) {
                            swal("Seleccione una opción para la pregunta #9", { icon: "warning" })
                        } else if (diez == "" && adicional == 0) {
                            swal("Seleccione una opción para la pregunta #11", { icon: "warning" })
                        } else if (once == "" && adicional == 0) {
                            swal("Seleccione una opción para la pregunta #11", { icon: "warning" })
                        } else if (doce == "" && adicional == 0) {
                            swal("Seleccione una opción para la pregunta #12", { icon: "warning" })
                        } else {
                            swal({
                                title: "¿Está segura de continuar?",
                                text: mensaje,
                                icon: "warning",
                                buttons: ["Cancelar", "Continuar"],
                                dangerMode: false
                            }).then((willDelete) => {
                                if (willDelete) {
                                    const agregar_CL = document.getElementById("agregar_CL")
                                    agregar_CL.disabled = true

                                    $.ajax({
                                        type: "POST",
                                        url: "/CallCenter/PagosAddEncuestaCL/",
                                        data: $("#Add_cl").serialize() + "&contenido=" + contenido,
                                        success: function (respuesta) {
                                            if (respuesta == "1") {
                                                swal("Registro guardado exitosamente", {
                                                    icon: "success"
                                                })
                                                location.reload()
                                            } else {
                                                $("#modal_encuesta_cliente").modal("hide")
                                                swal(respuesta, {
                                                    icon: "error"
                                                })
                                            }
                                        }
                                    })
                                } else {
                                    swal("Continúe con su registro", { icon: "info" })
                                }
                            })
                        }
                    }
                }

                function enviar_add_av(id) {
                    fecha_trabajo = document.getElementById("fecha_solicitud_av_" + id).value
                    num_telefono = document.getElementById("movil_av_" + id).value
                    tipo_av = document.getElementById("tipo_llamada_av_" + id).value
                    uno = document.getElementById("uno_av_" + id).value
                    dos = document.getElementById("dos_av_" + id).value
                    tres = document.getElementById("tres_av_" + id).value
                    cuatro = document.getElementById("cuatro_av_" + id).value
                    cinco = document.getElementById("cinco_av_" + id).value
                    seis = document.getElementById("seis_av_" + id).value
                    siete = document.getElementById("siete_av_" + id).value
                    ocho = document.getElementById("ocho_av_" + id).value
                    nueve = document.getElementById("nueve_av_" + id).value
                    completo = $("input[name='completo_av_" + id + "]:checked").val()
                    llamada = document.getElementById("titulo_av_" + id)
                    contenido = llamada.innerHTML

                    if (contenido == "2") {
                        mensaje = ""
                    } else {
                        if (completo == "1") {
                            mensaje =
                                "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro."
                        } else {
                            mensaje = ""
                        }
                    }

                    if (completo == "0") {
                        if (tipo_av == "") {
                            swal("Seleccione el tipo de llamada que realizo", { icon: "warning" })
                        } else {
                            swal({
                                title: "¿Está segura de continuar con una llamada incompleta?",
                                text: mensaje,
                                icon: "warning",
                                buttons: ["Cancelar", "Continuar"],
                                dangerMode: false
                            }).then((willDelete) => {
                                if (willDelete) {
                                    const agregar_AV = document.getElementById("agregar_av_" + id)
                                    agregar_AV.disabled = true
                                    $.ajax({
                                        type: "POST",
                                        url: "/CallCenter/PagosAddEncuestaAV/",
                                        data: $("#Add_av_" + id).serialize() + "&contenido_av_" + id + "=" + contenido + "&no_av=" + id,
                                        success: function (respuesta) {
                                            if (respuesta == "1") {
                                                swal("Registro guardado exitosamente", {
                                                    icon: "success"
                                                })
                                                location.reload()
                                            } else {
                                                $("#modal_encuesta_cliente").modal("hide")
                                                swal(respuesta, {
                                                    icon: "error"
                                                })
                                                document.getElementById("monto").value = ""
                                            }
                                        }
                                    })
                                } else {
                                    swal("Continúe con su registro", { icon: "info" })
                                }
                            })
                        }
                    } else {
                        if (tipo_av == "") {
                            swal("Seleccione el tipo de llamada que realizo", { icon: "warning" })
                        } else if (uno == "") {
                            swal("Seleccione una opción para la pregunta #1", { icon: "warning" })
                        } else if (dos == "") {
                            swal("Seleccione una opción para la pregunta #2", { icon: "warning" })
                        } else if (tres == "") {
                            swal("Seleccione una opción para la pregunta #3", { icon: "warning" })
                        } else if (cuatro == "") {
                            swal("Seleccione una opción para la pregunta #4", { icon: "warning" })
                        } else if (cinco == "") {
                            swal("Seleccione una opción para la pregunta #5", { icon: "warning" })
                        } else if (seis == "") {
                            swal("Seleccione una opción para la pregunta #6", { icon: "warning" })
                        } else if (siete == "") {
                            swal("Seleccione una opción para la pregunta #7", { icon: "warning" })
                        } else if (ocho == "") {
                            swal("Seleccione una opción para la pregunta #8", { icon: "warning" })
                        } else if (nueve == "") {
                            swal("Seleccione una opción para la pregunta #9", { icon: "warning" })
                        } else {
                            swal({
                                title: "¿Está segura de continuar?",
                                text: mensaje,
                                icon: "warning",
                                buttons: ["Cancelar", "Continuar"],
                                dangerMode: false
                            }).then((willDelete) => {
                                if (willDelete) {
                                    const agregar_AV = document.getElementById("agregar_av_" + id)
                                    agregar_AV.disabled = true

                                    $.ajax({
                                        type: "POST",
                                        url: "/CallCenter/PagosAddEncuestaAV/",
                                        data: $("#Add_av_" + id).serialize() + "&contenido_av_" + id + "=" + contenido + "&no_av=" + id,
                                        success: function (respuesta) {
                                            if (respuesta == "1") {
                                                swal("Registro guardado exitosamente", {
                                                    icon: "success"
                                                })
                                                location.reload()
                                            } else {
                                                $("#modal_encuesta_cliente").modal("hide")
                                                swal(respuesta, {
                                                    icon: "error"
                                                })
                                                document.getElementById("monto").value = ""
                                            }
                                        }
                                    })
                                } else {
                                    swal("Continúe con su registro", { icon: "info" })
                                }
                            })
                        }
                    }
                }

                function enviar_comentarios_add() {
                    cliente_encuesta = document.getElementById("cliente_encuesta").value
                    cliente_id = document.getElementById("cliente_id").value

                    cdgco_res = getParameterByName("Suc")
                    ciclo_cl_res = getParameterByName("Ciclo")
                    cliente_id_res = getParameterByName("Credito")

                    if (cliente_encuesta != "PENDIENTE") {
                        ///////
                        //Puede guardar comentarios iniciales pero no finales
                        ////

                        $.ajax({
                            type: "POST",
                            url: "/CallCenter/Resumen/",
                            data:
                                $("#Add_comentarios").serialize() +
                                "&cdgco_res=" +
                                cdgco_res +
                                "&ciclo_cl_res=" +
                                ciclo_cl_res +
                                "&cliente_id_res=" +
                                cliente_id,
                            success: function (respuesta) {
                                if (respuesta == "1") {
                                    swal("Registro guardado exitosamente", {
                                        icon: "success"
                                    })
                                    location.reload()
                                } else {
                                    $("#modal_encuesta_cliente").modal("hide")
                                    swal(respuesta, {
                                        icon: "error"
                                    })
                                    document.getElementById("monto").value = ""
                                }
                            }
                        })
                    } else {
                        swal(
                            "Usted debe responder la encuesta del CLIENTE para poder guardar sus comentarios iniciales y poder continuar.",
                            { icon: "warning" }
                        )
                    }
                }

                const mostrarAdvertencia = (tipo) => {
                    const textos = {
                        aprobado: {
                            titulo: "Aprobación de solicitud de crédito",
                            mensaje: '"Sí, aprobar solicitud", dará inicio a el proceso de autorización del crédito.',
                            advertencia: "¿estas segura de aprobar el crédito?",
                            boton: "Sí, aprobar solicitud"
                        },
                        rechazado: {
                            titulo: "Rechazo de solicitud de crédito",
                            mensaje: "El crédito se rechazara y se notificara al área correspondiente del rechazo",
                            advertencia: "¿Estas segura de rechazar la solicitud?",
                            boton: "Sí, rechazar solicitud"
                        },
                        pendiente: {
                            titulo: "Corrección de datos de solicitud de crédito",
                            mensaje: "El crédito quedara pendiente para la corrección de datos.",
                            advertencia: "¿Desea continuar?",
                            boton: "Sí, dejar pendiente"
                        }
                    }

                    const texto = textos[tipo]
                    const contenedor = document.createElement("div")
                    const mensaje = document.createElement("p")
                    const advertencia = document.createElement("p")

                    mensaje.innerHTML = texto.mensaje
                    mensaje.style.fontSize = "15px"
                    mensaje.style.color = "black"

                    advertencia.textContent = texto.advertencia
                    advertencia.style.color = "red"
                    advertencia.style.fontWeight = "bold"
                    advertencia.style.marginTop = "20px"
                    advertencia.style.fontSize = "18px"

                    contenedor.appendChild(mensaje)
                    contenedor.appendChild(advertencia)

                    const configuracion = {
                        title: texto.titulo,
                        content: contenedor,
                        icon: "warning",
                        buttons: ["No, volver", tipo === PENDIENTE ? texto.boton : "Lea con atención (3)"],
                        closeOnClickOutside: false,
                        dangerMode: true
                    }

                    return new Promise((resolve) => {
                        swal(configuracion)
                        .then((continuar) => resolve(continuar))

                        if (tipo === PENDIENTE) return

                        let tiempoRestante = 3
                        const botonConfirmar = document.querySelector(".swal-button--danger")
                        botonConfirmar.disabled = true
                        const intervalo = setInterval(() => {
                            tiempoRestante--

                            if (tiempoRestante > 0)
                                botonConfirmar.textContent = "Lea con atención (" + tiempoRestante + ")"
                            else {
                                clearInterval(intervalo)
                                botonConfirmar.disabled = false
                                botonConfirmar.textContent = texto.boton
                            }
                        }, 1000)
                    })
                }

                const enviar_resumen_add = async () => {
                    estatus_solicitud = document.getElementById("estatus_solicitud").value
                    cliente_encuesta = document.getElementById("cliente_encuesta").value
                    cliente_aval = document.getElementById("cliente_aval").value
                    comentarios_iniciales = document.getElementById("comentarios_iniciales").value
                    comentarios_finales = document.getElementById("comentarios_finales").value
                    vobo_gerente = document.getElementById("vobo_gerente").value
                    cliente_id = document.getElementById("cliente_id").value
                    cdgco_res = getParameterByName("Suc")
                    ciclo_cl_res = getParameterByName("Ciclo")

                    if (comentarios_iniciales == "") {
                        swal("Necesita ingresar los comentarios iniciales para la solicitud del cliente", {
                            icon: "warning"
                        })
                    } else {
                        if (comentarios_finales == "") {
                            swal("Necesita ingresar los comentarios finales para la solicitud del cliente", {
                                icon: "warning"
                            })
                        } else {
                            if (cliente_encuesta == "PENDIENTE") {
                                swal("La encuesta del cliente no está marcada como validada", {
                                    icon: "danger"
                                })
                            } else {
                                if (estatus_solicitud == "") {
                                    swal("Necesita seleccionar el estatus final de la solicitud", {
                                        icon: "warning"
                                    })
                                } else {
                                    if (estatus_solicitud != "PENDIENTE") {
                                        let tipo = ""
                                        if (estatus_solicitud.toLowerCase().includes("lista")) tipo = APROBADO
                                        else if (estatus_solicitud.toLowerCase().includes("pendiente")) tipo = PENDIENTE
                                        else if (estatus_solicitud.toLowerCase().includes("cancelada")) tipo = RECHAZADO
                                        else {
                                            swal("El estatus seleccionado no es válido", { icon: "error" })
                                            return
                                        }

                                        const continuar = await mostrarAdvertencia(tipo)
                                        if (!continuar) return
                                    }

                                    const agregar_TS = document.getElementById("terminar_solicitud")
                                    agregar_TS.disabled = true

                                    $.ajax({
                                        type: "POST",
                                        url: "/CallCenter/ResumenEjecutivo/",
                                        data:
                                            $("#Add_comentarios").serialize() +
                                            "&cdgco_res=" +
                                            cdgco_res +
                                            "&ciclo_cl_res=" +
                                            ciclo_cl_res +
                                            "&cliente_id_res=" +
                                            cliente_id +
                                            "&comentarios_iniciales=" +
                                            comentarios_iniciales +
                                            "&comentarios_finales=" +
                                            comentarios_finales +
                                            "&estatus_solicitud=" +
                                            estatus_solicitud +
                                            "&vobo_gerente=" +
                                            vobo_gerente,
                                        success: function (respuesta) {
                                            if (respuesta == "1") {
                                                swal("Se guardo correctamente la información.", {
                                                    icon: "success",
                                                    buttons: {
                                                        catch: {
                                                            text: "Aceptar",
                                                            value: "catch"
                                                        }
                                                    }
                                                }).then((value) => {
                                                    switch (value) {
                                                        case "catch":
                                                            window.location.href = "/CallCenter/Pendientes/"
                                                            break
                                                    }
                                                })
                                            } else {
                                                $("#modal_encuesta_cliente").modal("hide")
                                                swal(respuesta, {
                                                    icon: "error"
                                                })
                                                document.getElementById("monto").value = ""
                                            }
                                        }
                                    })
                                }
                            }
                        }
                    }
                }

                function check_2610() {
                    llamada = document.getElementById("titulo")
                    contenido = llamada.innerHTML

                    swal({
                        title: "¿Está segura de continuar con el registro de una solicitud con Información Inconsistente?",
                        text: "",
                        icon: "warning",
                        buttons: ["Cancelar", "Continuar"],
                        dangerMode: false
                    }).then((willDelete) => {
                        if (willDelete) {
                            const agregar_CL = document.getElementById("agregar_CL")
                            agregar_CL.disabled = true

                            $.ajax({
                                type: "POST",
                                url: "/CallCenter/PagosAddEncuestaCL/",
                                data: $("#Add_cl").serialize() + "&contenido=" + contenido + "&completo=0",
                                success: function (respuesta) {
                                    if (respuesta == "1") {
                                        swal("Registro guardado exitosamente", {
                                            icon: "success"
                                        })
                                        location.reload()
                                    } else {
                                        $("#modal_encuesta_cliente").modal("hide")
                                        swal(respuesta, {
                                            icon: "error"
                                        })
                                    }
                                }
                            })
                        } else {
                            swal("Continúe con su registro", { icon: "success" })
                            document.getElementById("check_2610").checked = false
                            return false
                        }
                    })
                }

                const showEncuestaAval = (id) => {
                    $("#modal_encuesta_aval_"+id).modal("show")
                }

                const guardaComentariosRetiro = (internos = true) => {
                    if (internos && $("#estatus").val() == "P") return showError("No se pueden guardar comentarios iniciales hasta que registre su primera llamada.");
                    if (!internos && $("#estatus").val() != "C") return showError("No se pueden guardar comentarios finales hasta que finalice la encuesta.");
                    
                    const datos = {
                        retiro: "{$_GET['retiro']}",
                        usuario: "{$_SESSION['usuario']}",
                    }

                    if (internos) datos.interno = $("#comentarios_internos").val()
                    else datos.externo = $("#comentarios_externos").val()


                    confirmarMovimiento("Guardar comentarios", "¿Desea guardar los comentarios " + (internos ? "iniciales" : "finales") + "?")
                    .then(continuar => {
                        if (!continuar) return
                        consultaServidor("/CallCenter/ComentariosRetiro/", datos, (respuesta) => {
                            if (!respuesta.success) return showError(respuesta.mensaje)

                            showSuccess("Se guardaron correctamente los comentarios.")
                            .then(() => {
                                showWait("Actualizando información...")
                                location.reload()
                            })
                        })
                    })
                }

                const guardaEncuestaRetiro = () => {
                    const retiro = "{$_GET['retiro']}"
                    const tipo = document.getElementById("tipo_llamada").value
                    if (tipo == "") return showError("Seleccione el tipo de llamada que realizo")

                    const r1 = $("#p1").val()
                    const r2 = $("#p2").val()
                    const completo = $('input[name="completo"]:checked').val()
                    let titulo = "Llamada completa"
                    let mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro."
                    
                    if (completo == "1") {
                        if (r1 == "") return showError("Debe seleccionar una respuesta para la pregunta 1.")
                        if (r2 == "") return showError("Debe seleccionar una respuesta para la pregunta 2.")
                    } else {
                        titulo = "Llamada incompleta"
                        mensaje = "¿Desea registrar un intento de llamada como incompleta?"
                    }

                    confirmarMovimiento(titulo, mensaje)
                    .then(continuar => {
                        if (!continuar) return
                        consultaServidor("/CallCenter/RegistraLlamadaRetiro/", {
                            retiro,
                            tipo,
                            r1,
                            r2,
                            completo
                        }, (respuesta) => {
                            if (!respuesta.success) return showError(respuesta.mensaje)

                            showSuccess("Registro guardado exitosamente")
                            .then(() => {
                                showWait("Actualizando información...")
                                location.reload()
                            })
                        })

                    })
                }

                const finalizaSolicitudRetiro = () => {
                    const r1 = $("#r1").val()
                    const r2 = $("#r2").val()
                    const retiro = "{$_GET['retiro']}"
                    const usuario = "{$_SESSION['usuario']}"
                    const estatus = $("#estatus_solicitud").val()

                    if (estatus == "V" && r1 != "S" && r2 != "S") return showError("No se puede finalizar la solicitud como válida si no se respondió correctamente a todas las preguntas.");

                    const estatus_label = $("#estatus_solicitud option:selected").text()
                    
                    if ($("#estatus").val() == "P") return showError("No se puede finalizar la solicitud hasta que llene la encuesta.");
                    if (!estatus) return showError("Seleccione el estatus final de la solicitud")

                    confirmarMovimiento("Finalizar solicitud de retiro", "¿Desea finalizar la solicitud de retiro con estatus " + estatus_label + "?")
                    .then(continuar => {
                        if (!continuar) return

                        consultaServidor("/CallCenter/FinalizaSolicitudRetiro/", { retiro, estatus, usuario }, (respuesta) => {
                            if (!respuesta.success) return showError(respuesta.mensaje)

                            showSuccess("La solicitud de retiro se finalizó correctamente.")
                            .then(() => {
                                showWait("Redirigiendo a la lista de pendientes...")
                                window.location.href = "/CallCenter/Pendientes/"
                            })
                        })
                    })
                }
            </script>
        HTML;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];
        $suc = $_GET['Suc'] ?? '';
        $reg = $_GET['Reg'];
        $fec = $_GET['Fec'];
        $opciones_suc = '';
        $cdgco_all = array();
        $cdgco_suc = array();
        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);
        $opciones_suc .= '<option  value="000">(000) TODAS MIS SUCURSALES</option>';

        if ($ComboSucursales['success'] && isset($ComboSucursales['datos'])) {
            if (count($ComboSucursales['datos']) > 0) {
                foreach ($ComboSucursales['datos'] as $key => $val2) {
                    $sel = $suc == $val2['CODIGO'] ? 'selected' : '';

                    $opciones_suc .= <<<html
                        <option {$sel} value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
                    html;
                    array_push($cdgco_all, $val2['CODIGO']);
                }
            }
        }

        View::set('header', $this->_contenedor->header($this->GetExtraHeader('Consulta de Clientes Call Center')));
        View::set('footer', $this->_contenedor->footer($extraFooter));

        if (isset($_GET['retiro'])) {
            $inicializar = CallCenterDao::iniciaRetiroCallCenter($_GET);
            if ($inicializar['success'] == false) {
                View::set('mensaje', $inicializar['mensaje']);
                View::render("callcenter_retiros_message");
                exit;
            }

            $datos_retiro = CallCenterDao::getInfoRetiro($_GET);
            if ($datos_retiro['success'] == false) {
                View::set('mensaje', $datos_retiro['mensaje']);
                View::render("callcenter_retiros_message");
                exit;
            }

            View::set('datos_retiro', $datos_retiro['datos']);
            View::render("callcenter_retiros");
        } elseif ($credito != '' && $ciclo != '' && $fec != '') {
            $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo, $fec);

            if ($AdministracionOne[0] == '') {
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::render("callcenter_cliente_message_all");
            } else {
                View::set('Administracion', $AdministracionOne);
                View::set('visible', $AdministracionOne[0]['CREDITO_ADICIONAL'] == '1' ? 'none' : 'block');
                View::set('suc', $suc);
                View::set('reg', $reg);
                View::set('cdgpe', $this->__usuario);
                View::set('pendientes', 'Mis ');
                View::render($AdministracionOne[0]['CREDITO_ADICIONAL'] == 1 ? "callcenter_cliente_all_mas" : "callcenter_cliente_all");
            }
        } else {
            $param = null;
            if ($this->__perfil === 'ADMIN' || $this->__perfil === 'ACALL') {
                $param = [];
            } elseif ($suc === '000' || $suc === '' || $suc === null) {
                $param = $cdgco_all;
            } else {
                $cdgco_suc[] = $suc;
                $param = $cdgco_suc;
            }

            $Solicitudes = CallCenterDao::getAllSolicitudes($param);

            $filas = [];
            foreach ($Solicitudes as $key => $value) {
                $orden = \DateTime::createFromFormat(
                    'd/m/Y H:i:s',
                    $value['FECHA_SOL']
                );

                if ($value['ESTATUS_CL'] == 'PENDIENTE') {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                } else if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO') {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                } else {
                    $color = 'success';
                    $icon = 'fa-check';
                }

                if ($value['ESTATUS_AV'] == 'PENDIENTE') {
                    if ($value['CREDITO_ADICIONAL']) {
                        $color_a = '';
                        $icon_a = '';
                    } else {
                        $color_a = 'primary';
                        $icon_a = 'fa-frown-o';
                    }
                } else if ($value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $color_a = 'warning';
                    $icon_a = 'fa-clock-o';
                } else {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $titulo_boton = 'Seguir';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                } else if ($value['FIN_CL'] != '' || $value['FIN_AV'] != '') {
                    $titulo_boton = 'Acabar';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                } else {
                    $titulo_boton = 'Iniciar';
                    $color_boton = '#029f3f';
                    $fuente = '';
                }

                if ($value['COMENTARIO_INICIAL'] == '') {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                } else {
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }

                if ($value['COMENTARIO_FINAL'] == '') {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                } else {
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }

                if ($value['ESTATUS_FINAL'] == '') {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                } else {
                    $icon_ef = 'fa-clock-o';
                    $color_ef = 'warning';
                }

                if ($value['COMENTARIO_PRORROGA'] == '') {
                    $icon_cp_a = 'fa-close';
                    $color_cp_a = 'danger';
                } else {
                    $icon_cp_a = 'fa-check';
                    $color_cp_a = 'success';
                }

                if ($value['VOBO_REG'] == NULL) {
                    $vobo = '';
                } else {
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if ($value['PRORROGA'] == 2) {
                    $prorroga = '<hr><div><b>TIENE ACTIVA LA PRORROGA </b><span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #9101b2"><span class="fa fa-bell"> </span> </span></div><hr>';
                    $comentario_prorroga = '<div><span class="label label-' . $color_cp_a . '"><span class="fa ' . $icon_cp_a . '"></span></span> Comentarios Prorroga</div>';
                } else {
                    $prorroga = '';
                    $comentario_prorroga = '';
                }

                if ($value['REACTIVACION'] != '400') {
                    $reactivacion = '';
                } else {
                    $reactivacion = '<hr><div><b>SE REACTIVO LA SOLICITUD </b><span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #006c75"><span class="fa fa-bell"> </span> </span></div><hr>';
                }

                if (substr($value['TEL_CL'], 0, 1) == '(') {
                    $format = $value['TEL_CL'];
                } else {
                    $format = "(" . substr($value['TEL_CL'], 0, 3) . ")" . " " . substr($value['TEL_CL'], 3, 3) . " - " . substr($value['TEL_CL'], 6, 4);
                }

                if ($value['RECOMENDADO'] != '') {
                    $recomendado = '<div><b>CAMPAÑA ACTIVA</b> <span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #6a0013"><span class="fa fa-yelp"> </span> </span></div><b><em>RECOMIENDA MÁS Y PAGA MENOS <em></em></b><hr>';
                }

                if ($value['CREDITO_ADICIONAL'] == 1) {
                    $adicional = '<div><b>TIPO:</b> <span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #6a0013"><span class="fa fa-yelp"> </span> </span></div><b><em>MÁS POR TI (ADICIONAL) <em></em></b><hr>';
                    $aval_r = 'NO APLICA';
                } else {
                    $adicional = '<div><b>TIPO:</b> <span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #6a0013"><span class="fa fa-yelp"> </span> </span></div><b><em>CRÉDITO TRADICIONAL <em></em></b><hr>';
                    $aval_r = $value['ESTATUS_AV'];
                }

                $fila = <<<HTML
                <tr style="padding: 0px !important; ">
                    <td style="padding: 5px !important; width:65px !important;">
                    <div><span class="label label-success" style="color: #0D0A0A">MCM - {$value['ID_SCALL']}</span></div>
                    <hr>
                    <div><label>{$value['CDGNS']}-{$value['CICLO']}</label></div>
                    </td>
                    <td style="padding: 10px !important; text-align: left">
                        <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                        <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;">
                        <span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label> <br><label><span class="fa fa-phone"></span> {$format}</label>
                        <hr>
                        $adicional
                    </td>
                    <td style="padding-top: 22px !important; text-align: left">
                        <div>
                        <b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span>
                         
                        </div>
                        <div><b>AVAL:</b> {$aval_r}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                        <br>
                        $prorroga
                        $reactivacion
                        {$recomendado}
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span>&nbsp;Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span>&nbsp;Comentarios Finales</div>
                    $comentario_prorroga
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span>&nbsp;Estatus Final Solicitud</div>
                    $vobo
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=S&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>$titulo_boton</b>
                        </a>
                    </td>
                </tr>
                HTML;

                $filas[] = [
                    'fecha' => $orden->getTimestamp(),
                    'fila' => $fila
                ];
            }

            $retiros = CallCenterDao::getSolicitudesRetiro($param);

            if ($retiros['success']) {
                foreach ($retiros['datos'] as $key => $retiro) {
                    $orden = \DateTime::createFromFormat(
                        'd/m/Y H:i:s',
                        $retiro['FECHA_CREACION']
                    );

                    $telefono = $retiro['TELEFONO'];
                    $telefono = sprintf(
                        '(%s) %s - %s',
                        substr($telefono, 0, 3),
                        substr($telefono, 3, 3),
                        substr($telefono, 6, 4)
                    );

                    $color = 'success';
                    $icon = 'fa-check';
                    $icon_ci = $retiro['COMENTARIO_INTERNO'] == '' ? 'fa-close' : 'fa-check';
                    $color_ci = $retiro['COMENTARIO_INTERNO'] == '' ? 'danger' : 'success';
                    $icon_cf = $retiro['COMENTARIO_EXTERNO'] == '' ? 'fa-close' : 'fa-check';
                    $color_cf = $retiro['COMENTARIO_EXTERNO'] == '' ? 'danger' : 'success';
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                    $titulo_boton = 'Iniciar';
                    $color_boton = '#029f3f';
                    $fuente = '';


                    if ($retiro['ESTATUS'] == 'P') {
                        $color = 'primary';
                        $icon = 'fa-frown-o';
                        $icon_ef = 'fa-close';
                        $color_ef = 'danger';
                    } else if ($retiro['ESTATUS'] == 'I') {
                        $color = 'warning';
                        $icon = 'fa-clock-o';
                        $icon_ef = 'fa-clock-o';
                        $color_ef = 'warning';
                        $titulo_boton = 'Seguir';
                        $color_boton = '#F0AD4E';
                        $fuente = '#0D0A0A';
                    } else if ($retiro['ESTATUS'] == 'C') {
                        $titulo_boton = 'Acabar';
                        $color_boton = '#000';
                        $fuente = '#fff';
                        $icon_ef = 'fa-clock-o';
                        $color_ef = 'warning';
                    }

                    $fila = <<<HTML
                        <tr style="vertical-align: middle;">
                            <td>
                                <div><span class="label label-success" style="color: #0D0A0A">MCM - {$retiro['ID']}</span></div>
                                <hr>
                                <div><label>{$retiro['CREDITO']}-{$retiro['CICLO']}</label></div>
                            </td>
                            <td style="text-align: left; vertical-align: middle;">
                                <span class="fa fa-building">&nbsp;</span>GERENCIA REGIONAL: ({$retiro['REGION']}) {$retiro['NOMBRE_REGION']}
                                <br>
                                <span class="fa fa-map-marker">&nbsp;</span>SUCURSAL: ({$retiro['SUCURSAL']}) {$retiro['NOMBRE_SUCURSAL']}
                                <br>
                                <span class="fa fa-briefcase">&nbsp;</span>EJECUTIVO: {$retiro['NOMBRE_EJECUTIVO']}
                            </td>
                            <td style="vertical-align: middle;">
                                <div>
                                    <span class="fa fa-user"></span> <label style="color: #1c4e63">{$retiro['NOMBRE_CLIENTE']}</label><br><label><span class="fa fa-phone">&nbsp;</span>{$telefono}</label>
                                    <hr>
                                    <div><b>TIPO:</b> <span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #25895b"><span class="fa fa-university"></span></span></div><b><em>RETIRO DE AHORRO<em></em></b><hr>
                                </div>
                            </td>
                            <td style="text-align: left; vertical-align: middle;">
                                <div>
                                    <b>CLIENTE: </b>{$retiro['ESTATUS_ETIQUETA']} <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span>
                                </div>
                            </td>
                            <td  style="text-align: left; vertical-align: middle;">
                                {$retiro['FECHA_CREACION']}
                            </td>
                            <td style="text-align: left; vertical-align: middle;">
                                <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span>&nbsp;Comentarios Iniciales</div>
                                <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span>&nbsp;Comentarios Finales</div>
                                <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span>&nbsp;Estatus Final Solicitud</div>
                            </td>
                            <td  style="vertical-align: middle;">
                                <a type="button" href="/CallCenter/Pendientes/?credito={$retiro['CREDITO']}&ciclo={$retiro['CICLO']}&usuario={$_SESSION['usuario']}&retiro={$retiro['ID']}" class="btn btn-primary btn-circle" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>$titulo_boton</b>
                                </a>
                            </td>
                        </tr>
                    HTML;

                    $filas[] = [
                        'fecha' => $orden->getTimestamp(),
                        'fila' => $fila
                    ];
                }
            }

            usort($filas, function ($a, $b) {
                return $a['fecha'] <=> $b['fecha'];
            });

            $filas = array_column($filas, 'fila');
            $tabla = $filas != [] ? implode("", $filas) : '';
            View::set('tabla', $tabla);
            View::set('cdgpe', $this->__usuario);
            View::set('sucursal', $opciones_suc);
            View::set('pendientes', 'Mis ');
            View::render("callcenter_pendientes_all");
        }
    }

    public function RegistraLlamadaRetiro()
    {
        echo json_encode(CallCenterDao::RegistraLlamadaRetiro($_POST));
    }

    public function FinalizaSolicitudRetiro()
    {
        $registro = CallCenterDao::FinalizaSolicitudRetiro($_POST);
        if ($registro['success']) {
            $destinatarios = $this->GetDestinatarios(CallCenterDao::GetDestinatarios_Aplicacion(3));

            if (count($destinatarios) > 0) {
                $datos = \App\models\AhorroConsulta::getInfoCorreoCC($_POST);
                if ($datos['success']) {
                    $plantilla = self::Plantilla_Retiro_Finalizado($datos['datos']);
                    $tipo = $_POST['estatus'] == 'V' ? 'Validación' : ($_POST['estatus'] == 'C' ? 'Cancelación' : 'Rechazo');
                    Mensajero::EnviarCorreo(
                        $destinatarios,
                        $tipo . ' de solicitud de crédito por Call Center',
                        Mensajero::Notificaciones($plantilla)
                    );
                }
            }
        }

        echo json_encode($registro);
    }

    public function Busqueda()
    {
        $tabla = '';
        $extraHeader = <<<html
        <title>Consulta de Clientes Call Center</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
   
       function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
        
        $('#doce_cl').on('change', function() {
          if(this.value == 'N')
              {
                  swal("Atención", "Al finalizar la encuesta cancele la solicitud, no cumple con la política de seguridad de la pregunta #12", "warning");
              }
        });
      
        $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
          "lengthMenu": [
                    [6, 10, 20, 30, -1],
                    [6, 10, 20, 30, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
        });
         
      </script>
html;

        $credito = $_GET['Credito'];

        if ($credito != '') {

            $Administracion = CallCenterDao::getAllSolicitudesBusquedaRapida($credito);
            foreach ($Administracion as $key => $value) {

                if ($value['ESTATUS_GENERAL'] == "SIN HISTORIAL" && $value['ID_SCALL'] == "") {
                    $ver_resumen = '';
                    $comentarios = '';
                    $ciclo = $value['CICLO'];
                } else {
                    $ver_resumen = <<<html
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
html;
                    $comentarios = <<<html
                        <br>
                        <br>
                        <span></span> COMENTARIOS INTERNOS: <br><b>{$value['COMENTARIO_INICIAL']}</b>
                        <br>
                        <br>
                        <span></span> COMENTARIOS SUCURSAL: <br><b>{$value['COMENTARIO_FINAL']}</b>
html;
                    if ($value['CICLOR'] == '') {
                        $ciclo = $value['CICLO'];
                    } else {
                        $ciclo = <<<html
                        <span  class="label label-warning" style="color: #0D0A0A; font-sice;  font-size: 12px;"> Rechazado</span>
html;
                    }
                }

                $monto = number_format($value['MONTO'], 2);
                if ($value['CREDITO_ADICIONAL'] == 1) {
                    $valor_c = 'Más Por Ti (Adicional)';
                    $aval_r = 'NO APLICA';
                } else {
                    $valor_c = 'Tradicional';
                    $aval_r = $value['ESTATUS_AV'];
                }
                $tabla .= <<<html
                     <tr style="padding: 0px !important; ">
                    <td style="padding: 5px !important; width:65px !important; width:125px !important;">
                        <div><span class="label label-success" style="color: #0D0A0A">MCM - {$value['ID_SCALL']}</span></div>
                        <hr>
                        <div><label>Crédito: {$value['CDGNS']}</label></div>
                        <div><label>Ciclo: {$ciclo}</label></div>
                    </td>
                    
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CDGCO']}) <b>{$value['NOMBRE_SUCURSAL']}</b>
                        <br>
                        <span class="fa fa-briefcase"></span> CLAVE EJECUTIVO: {$value['ID_EJECUTIVO']}
                        <br>
                        <span class="fa fa-briefcase"></span> FECHA DE CAPTURA ADMINISTRADORA: <b>{$value['FECHA_SOL']}</b>
                        <br>
                        <span class="fa fa-briefcase"></span> TIPO DE CRÉDITO: <b>{$valor_c}</b>
                    </td>
                    
                    <td style="padding: 10px !important; text-align: left; width:225px !important;">
                         ESTATUS CLIENTE:  <br><b>{$value['ESTATUS_CL']}</b>
                        <br>
                        <br>
                         ESTATUS AVAL: <br><b>{$aval_r}</b>
                    </td>
                    
                    <td style="padding: 10px !important; text-align: left; width:225px !important;">
                         <span class="fa fa-calendar"></span> FECHA DE VALIDACIÓN: <br> <b>{$value['FECHA_TRABAJO']}</b>
                          {$comentarios}
                       
                       
                    </td>
                    
                    <td style="padding: 10px !important; text-align: left">
                         <span></span> ESTATUS GENERAL:  <br><b>{$value['ESTATUS_GENERAL']}</b>
                        <br>
                        <br>
                         <span></span> LOCALÍCELA EN: <br><b>{$value['BANDEJA']}</b>
                         <br>
                        <br>
                        
                    </td>
                  
                    
                    <td style="padding-top: 22px !important;">
                        {$ver_resumen}
                    </td>
                </tr>
html;
            }

            View::set('tabla', $tabla);
            View::set('credito', $credito);
            View::set('usuario', $this->__usuario);
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("busqueda_registro_rapida");
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("callcenter_busqueda_rapida");
        }
    }

    public function Prorroga()
    {
        $tabla = '';
        $extraHeader = <<<html
        <title>Consulta de Clientes Call Center</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
      
      function ProrrogaAutorizar(id_call)
         {
              swal({
              title: "¿Está segura de autorizar la prorroga?",
              text: '',
              icon: "warning",
              buttons: ["Denegar Solicitud", "Autorizar"],
              dangerMode: false
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ProrrogaUpdate/',
                        data: 'prorroga=2'+'&id_call='+id_call,
                        success: function(respuesta) {
                             if(respuesta=='1'){
                             swal("Prorroga Autorizada", {
                                          icon: "success",
                                        });
                             location.reload();
                            }
                            else {
                           
                             swal(respuesta, {
                                          icon: "error",
                                        });
                            }
                        }
                        });
              }
              else {
                 $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ProrrogaUpdate/',
                        data: 'prorroga=3'+'&id_call='+id_call,
                        success: function(respuesta) {
                             if(respuesta=='1'){
                             swal("Prorroga Denegada", {
                                          icon: "success",
                                        });
                             location.reload();
                            }
                            else {
                           
                             swal(respuesta, {
                                          icon: "error",
                                        });
                            }
                        }
                        });
              }
            });
         }
      
       function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
        
        $('#doce_cl').on('change', function() {
          if(this.value == 'N')
              {
                  swal("Atención", "Al finalizar la encuesta cancele la solicitud, no cumple con la política de seguridad de la pregunta #12", "warning");
              }
        });
      
        $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
          "lengthMenu": [
                    [6, 10, 20, 30, -1],
                    [6, 10, 20, 30, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
        });
         
        function InfoDesactivaEncuesta()
        {
             swal("Atención", "Para continuar con la ENCUESTA del AVAL por favor, es nesesario completar la PRIMER LLAMADA del cliente. ", "warning");
        }
         
        function enviar_add_cl(){	
             fecha_trabajo = document.getElementById("fecha_cl").value; 
             cdgns = document.getElementById("cdgns").value; 
             ciclo = document.getElementById("ciclo_cl").value; 
             num_telefono = document.getElementById("movil_cl").value;  
             tipo_cl = document.getElementById("tipo_llamada_cl").value; 
             uno = document.getElementById("uno_cl").value; 
             dos = document.getElementById("dos_cl").value; 
             tres = document.getElementById("tres_cl").value; 
             cuatro = document.getElementById("cuatro_cl").value; 
             cinco = document.getElementById("cinco_cl").value; 
             seis = document.getElementById("seis_cl").value; 
             siete = document.getElementById("siete_cl").value; 
             ocho = document.getElementById("ocho_cl").value; 
             nueve = document.getElementById("nueve_cl").value; 
             diez = document.getElementById("diez_cl").value; 
             once = document.getElementById("once_cl").value; 
             doce = document.getElementById("doce_cl").value; 
             completo = $('input[name="completo"]:checked').val();
             llamada = document.getElementById("titulo");
             contenido = llamada.innerHTML;
             
             
             if(contenido == '2')
                 {
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
                         }
                     
                 }
             
             
             
             if(completo == '0')
                 {
                     
                      if(tipo_cl == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }
                      else 
                          {
                                  swal({
                                  title: "¿Está segura de continuar con una llamada incompleta?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                            type: 'POST',
                                            url: '/CallCenter/PagosAddEncuestaCL/',
                                            data: $('#Add_cl').serialize()+'&contenido='+contenido,
                                            success: function(respuesta) {
                                                 if(respuesta=='1'){
                                                 swal("Registro guardado exitosamente", {
                                                              icon: "success",
                                                            });
                                                 location.reload();
                                                }
                                                else {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                 swal(respuesta, {
                                                              icon: "error",
                                                            });
                                                  
                                                }
                                            }
                                            });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "success",});
                                  }
                                });
                         }
                 }
             else 
                 {
                      if(tipo_cl == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }else if(uno  == '') {
                             swal("Seleccione una opción para la pregunta #1", {icon: "warning",});
                        }else if(dos  == '') {
                             swal("Seleccione una opción para la pregunta #2", {icon: "warning",});
                        }else if(tres  == '') {
                             swal("Seleccione una opción para la pregunta #3", {icon: "warning",});
                        }else if(cuatro  == '') {
                             swal("Seleccione una opción para la pregunta #4", {icon: "warning",});
                        }else if(cinco  == '') {
                             swal("Seleccione una opción para la pregunta #5", {icon: "warning",});
                        }else if(seis  == '') {
                             swal("Seleccione una opción para la pregunta #6", {icon: "warning",});
                        }else if(siete  == '') {
                             swal("Seleccione una opción para la pregunta #7", {icon: "warning",});
                        }else if(ocho  == '') {
                             swal("Seleccione una opción para la pregunta #8", {icon: "warning",});
                        }else if(nueve  == '') {
                             swal("Seleccione una opción para la pregunta #9", {icon: "warning",});
                        }else if(diez  == '') {
                             swal("Seleccione una opción para la pregunta #11", {icon: "warning",});
                        }else if(once  == '') {
                             swal("Seleccione una opción para la pregunta #11", {icon: "warning",});
                        }else if(doce  == '') {
                             swal("Seleccione una opción para la pregunta #12", {icon: "warning",});
                        }else
                        {
                            
                            ////////////////////////////////////777
                            swal({
                                  title: "¿Está segura de continuar?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/PagosAddEncuestaCL/',
                                        data: $('#Add_cl').serialize()+'&contenido='+contenido,
                                        success: function(respuesta) {
                                             if(respuesta=='1'){
                                          
                                             swal("Registro guardado exitosamente", {
                                                          icon: "success",
                                                        });
                                             location.reload();
                                            
                                            }
                                            else {
                                            $('#modal_encuesta_cliente').modal('hide')
                                             swal(respuesta, {
                                                          icon: "error",
                                                        });
                                                
                                            }
                                        }
                                        });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "info",});
                                  }
                                });
                            //////////////////////////////777
                        }
                 }
            
           
    }
        function enviar_add_av(){	
             fecha_trabajo = document.getElementById("fecha_av").value; 
             num_telefono = document.getElementById("movil_av").value;  
             tipo_av = document.getElementById("tipo_llamada_av").value; 
             uno = document.getElementById("uno_av").value; 
             dos = document.getElementById("dos_av").value; 
             tres = document.getElementById("tres_av").value; 
             cuatro = document.getElementById("cuatro_av").value; 
             cinco = document.getElementById("cinco_av").value; 
             seis = document.getElementById("seis_av").value; 
             siete = document.getElementById("siete_av").value; 
             ocho = document.getElementById("ocho_av").value; 
             nueve = document.getElementById("nueve_av").value; 
             completo = $('input[name="completo_av"]:checked').val();
             llamada = document.getElementById("titulo_av");
             contenido = llamada.innerHTML;
             
             
             if(contenido == '2')
                 {
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
                         }
                     
                 }
             
             
             
             if(completo == '0')
                 {
                     
                      if(tipo_av == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }
                      else 
                          {
                                  swal({
                                  title: "¿Está segura de continuar con una llamada incompleta?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                            type: 'POST',
                                            url: '/CallCenter/PagosAddEncuestaAV/',
                                            data: $('#Add_av').serialize()+'&contenido_av='+contenido,
                                            success: function(respuesta) {
                                                 if(respuesta=='1'){
                                                 swal("Registro guardado exitosamente", {
                                                              icon: "success",
                                                            });
                                                 location.reload();
                                                }
                                                else {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                 swal(respuesta, {
                                                              icon: "error",
                                                            });
                                                    document.getElementById("monto").value = "";
                                                }
                                            }
                                            });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "info",});
                                  }
                                });
                         }
                 }
             else 
                 {
                      if(tipo_av == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }else if(uno  == '') {
                             swal("Seleccione una opción para la pregunta #1", {icon: "warning",});
                        }else if(dos  == '') {
                             swal("Seleccione una opción para la pregunta #2", {icon: "warning",});
                        }else if(tres  == '') {
                             swal("Seleccione una opción para la pregunta #3", {icon: "warning",});
                        }else if(cuatro  == '') {
                             swal("Seleccione una opción para la pregunta #4", {icon: "warning",});
                        }else if(cinco  == '') {
                             swal("Seleccione una opción para la pregunta #5", {icon: "warning",});
                        }else if(seis  == '') {
                             swal("Seleccione una opción para la pregunta #6", {icon: "warning",});
                        }else if(siete  == '') {
                             swal("Seleccione una opción para la pregunta #7", {icon: "warning",});
                        }else if(ocho  == '') {
                             swal("Seleccione una opción para la pregunta #8", {icon: "warning",});
                        }else if(nueve  == '') {
                             swal("Seleccione una opción para la pregunta #9", {icon: "warning",});
                        }else 
                        {
                            
                            ////////////////////////////////////777
                            swal({
                                  title: "¿Está segura de continuar?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/PagosAddEncuestaAV/',
                                        data: $('#Add_av').serialize()+'&contenido_av='+contenido,
                                        success: function(respuesta) {
                                             if(respuesta=='1'){
                                          
                                             swal("Registro guardado exitosamente", {
                                                          icon: "success",
                                                        });
                                             location.reload();
                                            
                                            }
                                            else {
                                            $('#modal_encuesta_cliente').modal('hide')
                                             swal(respuesta, {
                                                          icon: "error",
                                                        });
                                                document.getElementById("monto").value = "";
                                            }
                                        }
                                        });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "info",});
                                  }
                                });
                            //////////////////////////////777
                        }
                 }
            
           
    }
        function enviar_comentarios_add(){	
             cliente_encuesta = document.getElementById("cliente_encuesta").value; 
             cliente_id = document.getElementById("cliente_id").value; 
             
             cdgco_res = getParameterByName('Suc');
             ciclo_cl_res = getParameterByName('Ciclo');
             cliente_id_res = getParameterByName('Credito');
             
            if(cliente_encuesta != 'PENDIENTE'){
                ///////
                //Puede guardar comentarios iniciales pero no finales
                ////
                $.ajax({
                type: 'POST',
                url: '/CallCenter/Resumen/',
                data: $('#Add_comentarios').serialize()+ "&cdgco_res="+cdgco_res+ "&ciclo_cl_res="+ciclo_cl_res+ "&cliente_id_res="+cliente_id,
                success: function(respuesta) 
                {
                    if(respuesta=='1')
                    {               
                       swal("Registro guardado exitosamente", {
                                icon: "success",
                           });
                       location.reload();
                    }
                    else 
                    {
                        $('#modal_encuesta_cliente').modal('hide')
                        swal(respuesta, {
                        icon: "error",
                        });
                        document.getElementById("monto").value = "";
                    }
                }
               });
                
            }
            else
            {
                swal("Usted debe responder la encuesta del CLIENTE para poder guardar sus comentarios iniciales y poder continuar.", {icon: "warning",});
            }
            
           
    }
        function enviar_resumen_add(){	
             cliente_encuesta = document.getElementById("cliente_encuesta").value; 
             cliente_aval = document.getElementById("cliente_aval").value;
             comentarios_iniciales = document.getElementById("comentarios_iniciales").value;
             comentarios_finales = document.getElementById("comentarios_finales").value;
             estatus_solicitud = document.getElementById("estatus_solicitud").value;
             vobo_gerente = document.getElementById("vobo_gerente").value;
            
             
             
             cliente_id = document.getElementById("cliente_id").value; 
             cdgco_res = getParameterByName('Suc');
             ciclo_cl_res = getParameterByName('Ciclo');
             
             if(comentarios_iniciales == ''){
                swal("Necesita ingresar los comentarios inicales para la solicitud del cliente", {icon: "warning",});
             }
            else
                {
                     if(comentarios_finales == '')
                     {
                        swal("Necesita ingresar los comentarios finales para la solicitud del cliente", {icon: "warning",});
                     }
                    else
                    {
                        if(cliente_encuesta == 'PENDIENTE'){
                            swal("La encuesta del cliente no está marcada como validada", {icon: "danger",});
                        }
                        else
                        {
                            if(cliente_aval == 'PENDIENTE')
                                {
                                    swal("La encuesta del aval no está marcada como validada", {icon: "warning",});
                                }
                                else
                                {
                                    if(estatus_solicitud == '')
                                    {
                                        swal("Necesita seleccionar el estatus final de la solicitud", {icon: "warning",});
                                    }
                                    else
                                    {
                                        $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/ResumenEjecutivo/',
                                        data: $('#Add_comentarios').serialize()+ "&cdgco_res="+cdgco_res+ "&ciclo_cl_res="+ciclo_cl_res+ "&cliente_id_res="+cliente_id+ "&comentarios_iniciales="+comentarios_iniciales+ "&comentarios_finales="+comentarios_finales+ "&estatus_solicitud="+estatus_solicitud+ "&vobo_gerente="+vobo_gerente ,
                                        success: function(respuesta) 
                                        {
                                            if(respuesta=='1')
                                            {               
                                              swal("Se guardo correctamente la información.",
                                              {
                                              icon: "success",
                                              buttons: {
                                                catch: {
                                                  text: "Aceptar",
                                                  value: "catch",
                                                }
                                              },
                                              
                                            })
                                            .then((value) => {
                                              switch (value) {
                                                case "catch":
                                                 window.location.href = '/CallCenter/Pendientes/'; //Will take you to Google.
                                                 break;
                                              }
                                            });
                                            }
                                            else 
                                            {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                swal(respuesta, {
                                                icon: "error",
                                                });
                                                document.getElementById("monto").value = "";
                                            }
                                        }
                                       });
                                    }                    
                                }
                        }    
                    }
                }
             
            
            
            
            
            
            
             
           
    }
    
      </script>
html;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];
        $suc = $_GET['Suc'];
        $fec = $_GET['Fec'];
        $opciones_suc = '';
        $cdgco_all = array();
        $cdgco_suc = array();

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);
        //var_dump($ComboSucursales);

        $opciones_suc .= <<<html
                <option  value="000">(000) TODAS LAS SUCURSALES</option>
html;
        if ($ComboSucursales['success']) {
            if (isset($ComboSucursales['datos']) && count($ComboSucursales['datos']) > 0) {
                foreach ($ComboSucursales['datos'] as $key => $val2) {
                    $sel = $suc == $val2['CODIGO'] ? 'selected' : '';

                    $opciones_suc .= <<<html
                        <option {$sel} value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
                    html;
                    array_push($cdgco_all, $val2['CODIGO']);
                }
            }
        }

        $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo, $fec);

        //var_dump($AdministracionOne[4]['NUMERO_INTENTOS_AV']);

        if ($credito != '' && $ciclo != '') {

            if ($AdministracionOne[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::render("callcenter_cliente_message_all");
            } else {

                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('suc', $suc);
                View::set('pendientes', 'Mis ');
                View::render("callcenter_cliente_all");
            }
        } else {

            if ($credito == '' && $ciclo == '' && $suc != '') {
                if ($suc == '000') {
                    $Solicitudes = CallCenterDao::getAllSolicitudesProrroga($cdgco_all);
                } else {
                    array_push($cdgco_suc, $suc);
                    $Solicitudes = CallCenterDao::getAllSolicitudesProrroga($cdgco_suc);
                }
            } else {
                $Solicitudes = CallCenterDao::getAllSolicitudesProrroga($cdgco_all);
                //var_dump($Solicitudes);
            }


            foreach ($Solicitudes as $key => $value) {
                if ($value['ESTATUS_CL'] == 'PENDIENTE') {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                } else if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO') {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                } else {
                    $color = 'success';
                    $icon = 'fa-check';
                }

                if ($value['ESTATUS_AV'] == 'PENDIENTE') {
                    $color_a = 'primary';
                    $icon_a = 'fa-frown-o';
                } else if ($value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $color_a = 'warning';
                    $icon_a = 'fa-clock-o';
                } else {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $titulo_boton = 'Seguir';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                } else if ($value['FIN_CL'] != '' || $value['FIN_AV'] != '') {
                    $titulo_boton = 'Acabar';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                } else {
                    $titulo_boton = 'Iniciar';
                    $color_boton = '#029f3f';
                    $fuente = '';
                }

                if ($value['COMENTARIO_INICIAL'] == '') {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                } else {
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }
                if ($value['COMENTARIO_FINAL'] == '') {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                } else {
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }
                if ($value['ESTATUS_FINAL'] == '') {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                } else {
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if ($value['VOBO_REG'] == NULL) {
                    $vobo = '';
                } else {
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }
                //var_dump($vobo);



                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 5px !important;"><label>{$value['CDGNS']}-{$value['CICLO']}</label></td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important; text-align: left">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" class="btn btn-primary btn-circle" onclick="ProrrogaAutorizar('{$value['ID_SCALL']}');" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>Autorizar Prorroga</b>
                        </a>
                    </td>
                </tr>
html;
            }


            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('sucursal', $opciones_suc);
            View::render("callcenter_prorroga_all");
        }
    }

    public function Reactivar()
    {
        $tabla = '';
        $extraHeader = <<<html
        <title>Consulta de Clientes Call Center</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
      
      function ReactivarAutorizar(id_call)
         {
              swal({
              title: "¿Está segura de autorizar la reactivación de la solicitud?",
              text: '',
              icon: "warning",
              buttons: ["Denegar Solicitud", "Autorizar"],
              dangerMode: false
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ReactivarSolicitudAdminPost/',
                        data: 'id_call='+id_call+'&opcion=SI',
                        success: function(respuesta) {
                             if(respuesta=='1'){
                             swal("Prorroga Autorizada", {
                                          icon: "success",
                                        });
                             location.reload();
                            }
                            else {
                           
                             swal(respuesta, {
                                          icon: "error",
                                        });
                            }
                        }
                        });
              }
              else {
                 $.ajax({
                        type: 'POST',
                        url: '/CallCenter/ReactivarSolicitudAdminPost/',
                       data: 'id_call='+id_call+'&opcion=NO',
                        success: function(respuesta) {
                             if(respuesta=='1'){
                             swal("Prorroga Denegada", {
                                          icon: "success",
                                        });
                             location.reload();
                            }
                            else {
                           
                             swal(respuesta, {
                                          icon: "error",
                                        });
                            }
                        }
                        });
              }
            });
         }
      
       function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
        
        $('#doce_cl').on('change', function() {
          if(this.value == 'N')
              {
                  swal("Atención", "Al finalizar la encuesta cancele la solicitud, no cumple con la política de seguridad de la pregunta #12", "warning");
              }
        });
      
        $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
          "lengthMenu": [
                    [6, 10, 20, 30, -1],
                    [6, 10, 20, 30, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
        });
         
        function InfoDesactivaEncuesta()
        {
             swal("Atención", "Para continuar con la ENCUESTA del AVAL por favor, es nesesario completar la PRIMER LLAMADA del cliente. ", "warning");
        }
         
        function enviar_add_cl(){	
             fecha_trabajo = document.getElementById("fecha_cl").value; 
             ciclo = document.getElementById("ciclo_cl").value; 
             num_telefono = document.getElementById("movil_cl").value;  
             tipo_cl = document.getElementById("tipo_llamada_cl").value; 
             uno = document.getElementById("uno_cl").value; 
             dos = document.getElementById("dos_cl").value; 
             tres = document.getElementById("tres_cl").value; 
             cuatro = document.getElementById("cuatro_cl").value; 
             cinco = document.getElementById("cinco_cl").value; 
             seis = document.getElementById("seis_cl").value; 
             siete = document.getElementById("siete_cl").value; 
             ocho = document.getElementById("ocho_cl").value; 
             nueve = document.getElementById("nueve_cl").value; 
             diez = document.getElementById("diez_cl").value; 
             once = document.getElementById("once_cl").value; 
             doce = document.getElementById("doce_cl").value; 
             completo = $('input[name="completo"]:checked').val();
             llamada = document.getElementById("titulo");
             contenido = llamada.innerHTML;
             
             
             if(contenido == '2')
                 {
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
                         }
                     
                 }
             
             
             
             if(completo == '0')
                 {
                     
                      if(tipo_cl == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }
                      else 
                          {
                                  swal({
                                  title: "¿Está segura de continuar con una llamada incompleta?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                            type: 'POST',
                                            url: '/CallCenter/PagosAddEncuestaCL/',
                                            data: $('#Add_cl').serialize()+'&contenido='+contenido,
                                            success: function(respuesta) {
                                                 if(respuesta=='1'){
                                                 swal("Registro guardado exitosamente", {
                                                              icon: "success",
                                                            });
                                                 location.reload();
                                                }
                                                else {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                 swal(respuesta, {
                                                              icon: "error",
                                                            });
                                                  
                                                }
                                            }
                                            });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "success",});
                                  }
                                });
                         }
                 }
             else 
                 {
                      if(tipo_cl == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }else if(uno  == '') {
                             swal("Seleccione una opción para la pregunta #1", {icon: "warning",});
                        }else if(dos  == '') {
                             swal("Seleccione una opción para la pregunta #2", {icon: "warning",});
                        }else if(tres  == '') {
                             swal("Seleccione una opción para la pregunta #3", {icon: "warning",});
                        }else if(cuatro  == '') {
                             swal("Seleccione una opción para la pregunta #4", {icon: "warning",});
                        }else if(cinco  == '') {
                             swal("Seleccione una opción para la pregunta #5", {icon: "warning",});
                        }else if(seis  == '') {
                             swal("Seleccione una opción para la pregunta #6", {icon: "warning",});
                        }else if(siete  == '') {
                             swal("Seleccione una opción para la pregunta #7", {icon: "warning",});
                        }else if(ocho  == '') {
                             swal("Seleccione una opción para la pregunta #8", {icon: "warning",});
                        }else if(nueve  == '') {
                             swal("Seleccione una opción para la pregunta #9", {icon: "warning",});
                        }else if(diez  == '') {
                             swal("Seleccione una opción para la pregunta #11", {icon: "warning",});
                        }else if(once  == '') {
                             swal("Seleccione una opción para la pregunta #11", {icon: "warning",});
                        }else if(doce  == '') {
                             swal("Seleccione una opción para la pregunta #12", {icon: "warning",});
                        }else
                        {
                            
                            ////////////////////////////////////777
                            swal({
                                  title: "¿Está segura de continuar?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/PagosAddEncuestaCL/',
                                        data: $('#Add_cl').serialize()+'&contenido='+contenido,
                                        success: function(respuesta) {
                                             if(respuesta=='1'){
                                          
                                             swal("Registro guardado exitosamente", {
                                                          icon: "success",
                                                        });
                                             location.reload();
                                            
                                            }
                                            else {
                                            $('#modal_encuesta_cliente').modal('hide')
                                             swal(respuesta, {
                                                          icon: "error",
                                                        });
                                                
                                            }
                                        }
                                        });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "info",});
                                  }
                                });
                            //////////////////////////////777
                        }
                 }
            
           
    }
        function enviar_add_av(){	
             fecha_trabajo = document.getElementById("fecha_av").value; 
             num_telefono = document.getElementById("movil_av").value;  
             tipo_av = document.getElementById("tipo_llamada_av").value; 
             uno = document.getElementById("uno_av").value; 
             dos = document.getElementById("dos_av").value; 
             tres = document.getElementById("tres_av").value; 
             cuatro = document.getElementById("cuatro_av").value; 
             cinco = document.getElementById("cinco_av").value; 
             seis = document.getElementById("seis_av").value; 
             siete = document.getElementById("siete_av").value; 
             ocho = document.getElementById("ocho_av").value; 
             nueve = document.getElementById("nueve_av").value; 
             completo = $('input[name="completo_av"]:checked').val();
             llamada = document.getElementById("titulo_av");
             contenido = llamada.innerHTML;
             
             
             if(contenido == '2')
                 {
                     mensaje = "";
                 }
             else 
                 {
                     if(completo == '1')
                        {
                            mensaje = "Usted va a finalizar y guardar la encuesta, no podrá editar esta información en un futuro.";
                        }
                     else 
                         {
                             mensaje = "";
                         }
                     
                 }
             
             
             
             if(completo == '0')
                 {
                     
                      if(tipo_av == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }
                      else 
                          {
                                  swal({
                                  title: "¿Está segura de continuar con una llamada incompleta?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                            type: 'POST',
                                            url: '/CallCenter/PagosAddEncuestaAV/',
                                            data: $('#Add_av').serialize()+'&contenido_av='+contenido,
                                            success: function(respuesta) {
                                                 if(respuesta=='1'){
                                                 swal("Registro guardado exitosamente", {
                                                              icon: "success",
                                                            });
                                                 location.reload();
                                                }
                                                else {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                 swal(respuesta, {
                                                              icon: "error",
                                                            });
                                                    document.getElementById("monto").value = "";
                                                }
                                            }
                                            });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "info",});
                                  }
                                });
                         }
                 }
             else 
                 {
                      if(tipo_av == '')
                        {
                             swal("Seleccione el tipo de llamada que realizo", {icon: "warning",});
                        }else if(uno  == '') {
                             swal("Seleccione una opción para la pregunta #1", {icon: "warning",});
                        }else if(dos  == '') {
                             swal("Seleccione una opción para la pregunta #2", {icon: "warning",});
                        }else if(tres  == '') {
                             swal("Seleccione una opción para la pregunta #3", {icon: "warning",});
                        }else if(cuatro  == '') {
                             swal("Seleccione una opción para la pregunta #4", {icon: "warning",});
                        }else if(cinco  == '') {
                             swal("Seleccione una opción para la pregunta #5", {icon: "warning",});
                        }else if(seis  == '') {
                             swal("Seleccione una opción para la pregunta #6", {icon: "warning",});
                        }else if(siete  == '') {
                             swal("Seleccione una opción para la pregunta #7", {icon: "warning",});
                        }else if(ocho  == '') {
                             swal("Seleccione una opción para la pregunta #8", {icon: "warning",});
                        }else if(nueve  == '') {
                             swal("Seleccione una opción para la pregunta #9", {icon: "warning",});
                        }else 
                        {
                            
                            ////////////////////////////////////777
                            swal({
                                  title: "¿Está segura de continuar?",
                                  text: mensaje,
                                  icon: "warning",
                                  buttons: ["Cancelar", "Continuar"],
                                  dangerMode: false
                                })
                                .then((willDelete) => {
                                  if (willDelete) {
                                      $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/PagosAddEncuestaAV/',
                                        data: $('#Add_av').serialize()+'&contenido_av='+contenido,
                                        success: function(respuesta) {
                                             if(respuesta=='1'){
                                          
                                             swal("Registro guardado exitosamente", {
                                                          icon: "success",
                                                        });
                                             location.reload();
                                            
                                            }
                                            else {
                                            $('#modal_encuesta_cliente').modal('hide')
                                             swal(respuesta, {
                                                          icon: "error",
                                                        });
                                                document.getElementById("monto").value = "";
                                            }
                                        }
                                        });
                                  }
                                  else {
                                    swal("Continúe con su registro", {icon: "info",});
                                  }
                                });
                            //////////////////////////////777
                        }
                 }
            
           
    }
        function enviar_comentarios_add(){	
             cliente_encuesta = document.getElementById("cliente_encuesta").value; 
             cliente_id = document.getElementById("cliente_id").value; 
             
             cdgco_res = getParameterByName('Suc');
             ciclo_cl_res = getParameterByName('Ciclo');
             cliente_id_res = getParameterByName('Credito');
             
            if(cliente_encuesta != 'PENDIENTE'){
                ///////
                //Puede guardar comentarios iniciales pero no finales
                ////
                $.ajax({
                type: 'POST',
                url: '/CallCenter/Resumen/',
                data: $('#Add_comentarios').serialize()+ "&cdgco_res="+cdgco_res+ "&ciclo_cl_res="+ciclo_cl_res+ "&cliente_id_res="+cliente_id,
                success: function(respuesta) 
                {
                    if(respuesta=='1')
                    {               
                       swal("Registro guardado exitosamente", {
                                icon: "success",
                           });
                       location.reload();
                    }
                    else 
                    {
                        $('#modal_encuesta_cliente').modal('hide')
                        swal(respuesta, {
                        icon: "error",
                        });
                        document.getElementById("monto").value = "";
                    }
                }
               });
                
            }
            else
            {
                swal("Usted debe responder la encuesta del CLIENTE para poder guardar sus comentarios iniciales y poder continuar.", {icon: "warning",});
            }
            
           
    }
        function enviar_resumen_add(){	
             cliente_encuesta = document.getElementById("cliente_encuesta").value; 
             cliente_aval = document.getElementById("cliente_aval").value;
             comentarios_iniciales = document.getElementById("comentarios_iniciales").value;
             comentarios_finales = document.getElementById("comentarios_finales").value;
             estatus_solicitud = document.getElementById("estatus_solicitud").value;
             vobo_gerente = document.getElementById("vobo_gerente").value;
            
             
             
             cliente_id = document.getElementById("cliente_id").value; 
             cdgco_res = getParameterByName('Suc');
             ciclo_cl_res = getParameterByName('Ciclo');
             
             if(comentarios_iniciales == ''){
                swal("Necesita ingresar los comentarios inicales para la solicitud del cliente", {icon: "warning",});
             }
            else
                {
                     if(comentarios_finales == '')
                     {
                        swal("Necesita ingresar los comentarios finales para la solicitud del cliente", {icon: "warning",});
                     }
                    else
                    {
                        if(cliente_encuesta == 'PENDIENTE'){
                            swal("La encuesta del cliente no está marcada como validada", {icon: "danger",});
                        }
                        else
                        {
                            if(cliente_aval == 'PENDIENTE')
                                {
                                    swal("La encuesta del aval no está marcada como validada", {icon: "warning",});
                                }
                                else
                                {
                                    if(estatus_solicitud == '')
                                    {
                                        swal("Necesita seleccionar el estatus final de la solicitud", {icon: "warning",});
                                    }
                                    else
                                    {
                                        $.ajax({
                                        type: 'POST',
                                        url: '/CallCenter/ResumenEjecutivo/',
                                        data: $('#Add_comentarios').serialize()+ "&cdgco_res="+cdgco_res+ "&ciclo_cl_res="+ciclo_cl_res+ "&cliente_id_res="+cliente_id+ "&comentarios_iniciales="+comentarios_iniciales+ "&comentarios_finales="+comentarios_finales+ "&estatus_solicitud="+estatus_solicitud+ "&vobo_gerente="+vobo_gerente ,
                                        success: function(respuesta) 
                                        {
                                            if(respuesta=='1')
                                            {               
                                              swal("Se guardo correctamente la información.",
                                              {
                                              icon: "success",
                                              buttons: {
                                                catch: {
                                                  text: "Aceptar",
                                                  value: "catch",
                                                }
                                              },
                                              
                                            })
                                            .then((value) => {
                                              switch (value) {
                                                case "catch":
                                                 window.location.href = '/CallCenter/Pendientes/'; //Will take you to Google.
                                                 break;
                                              }
                                            });
                                            }
                                            else 
                                            {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                swal(respuesta, {
                                                icon: "error",
                                                });
                                                document.getElementById("monto").value = "";
                                            }
                                        }
                                       });
                                    }                    
                                }
                        }    
                    }
                }
             
            
            
            
            
            
            
             
           
    }
        
    
      </script>
html;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];
        $suc = $_GET['Suc'];
        $fec = $_GET['Fec'];
        $opciones_suc = '';
        $cdgco_all = array();
        $cdgco_suc = array();

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);
        //var_dump($ComboSucursales);

        $opciones_suc .= <<<html
                <option  value="000">(000) TODAS LAS SUCURSALES</option>
html;
        if ($ComboSucursales['success']) {
            if (isset($ComboSucursales['datos']) && count($ComboSucursales['datos']) > 0) {
                foreach ($ComboSucursales['datos'] as $key => $val2) {
                    $sel = $suc == $val2['CODIGO'] ? 'selected' : '';

                    $opciones_suc .= <<<html
                        <option {$sel} value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
                    html;
                    array_push($cdgco_all, $val2['CODIGO']);
                }
            }
        }

        $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo, $fec);

        //var_dump($AdministracionOne[4]['NUMERO_INTENTOS_AV']);

        if ($credito != '' && $ciclo != '') {

            if ($AdministracionOne[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::render("callcenter_cliente_message_all");
            } else {

                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('suc', $suc);
                View::set('pendientes', 'Mis ');
                View::render("callcenter_cliente_all");
            }
        } else {

            if ($credito == '' && $ciclo == '' && $suc != '') {
                if ($suc == '000') {
                    $Solicitudes = CallCenterDao::getAllSolicitudesReactivar($cdgco_all);
                } else {
                    array_push($cdgco_suc, $suc);
                    $Solicitudes = CallCenterDao::getAllSolicitudesReactivar($cdgco_suc);
                }
            } else {
                $Solicitudes = CallCenterDao::getAllSolicitudesReactivar($cdgco_all);
                //var_dump($Solicitudes);
            }


            foreach ($Solicitudes as $key => $value) {
                if ($value['ESTATUS_CL'] == 'PENDIENTE') {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                } else if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO') {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                } else {
                    $color = 'success';
                    $icon = 'fa-check';
                }

                if ($value['ESTATUS_AV'] == 'PENDIENTE') {
                    $color_a = 'primary';
                    $icon_a = 'fa-frown-o';
                } else if ($value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $color_a = 'warning';
                    $icon_a = 'fa-clock-o';
                } else {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $titulo_boton = 'Seguir';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                } else if ($value['FIN_CL'] != '' || $value['FIN_AV'] != '') {
                    $titulo_boton = 'Acabar';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                } else {
                    $titulo_boton = 'Iniciar';
                    $color_boton = '#029f3f';
                    $fuente = '';
                }

                if ($value['COMENTARIO_INICIAL'] == '') {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                } else {
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }
                if ($value['COMENTARIO_FINAL'] == '') {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                } else {
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }
                if ($value['ESTATUS_FINAL'] == '') {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                } else {
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if ($value['VOBO_REG'] == NULL) {
                    $vobo = '';
                } else {
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }
                //var_dump($vobo);



                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 5px !important;"><label>{$value['CDGNS']}-{$value['CICLO']}</label></td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important; text-align: left">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" class="btn btn-primary btn-circle" onclick="ReactivarAutorizar('{$value['ID_SCALL']}');" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>Autorizar Reactivación</b>
                        </a>
                    </td>
                </tr>
html;
            }


            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('sucursal', $opciones_suc);
            View::render("callcenter_reactivar_all");
        }
    }

    public function Global()
    {
        $tabla = '';
        $extraHeader = <<<html
        <title>Consulta de Clientes Call Center</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
      
         $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
          "lengthMenu": [
                    [6, 10, 20, 30, -1],
                    [6, 10, 20, 30, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
        });
         
         function InfoDesactivaEncuesta()
         {
             swal("Atención", "Para continuar con la ENCUESTA del AVAL por favor, es nesesario completar la PRIMER LLAMADA del cliente. ", "warning");
         }
         
    
      </script>
html;

        $credito = $_GET['Credito'];
        $ciclo = $_GET['Ciclo'];
        $reg = $_GET['Reg'];
        $suc = $_GET['Suc'];
        $fec = $_GET['Fec'];
        $opciones_suc = '';
        $cdgco = array();

        //var_dump( $this->usuario, $this->__usuario);
        $ComboSucursales = CallCenterDao::getComboSucursalesGlobales();

        $opciones_suc .= <<<html
                <option  value="000">(000) TODAS LAS SUCURSALES</option>
html;
        foreach ($ComboSucursales as $key => $val2) {

            $opciones_suc .= <<<html
                <option  value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
html;
            array_push($cdgco, $val2['CODIGO']);
        }

        $AdministracionOne = CallCenterDao::getAllDescription($credito, $ciclo, $fec);

        if ($credito != '' && $ciclo != '') {

            if ($AdministracionOne[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('credito', $credito);
                View::set('ciclo', $ciclo);
                View::set('pendientes', 'Todos los ');
                View::render("callcenter_cliente_message_all");
            } else {

                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Administracion', $AdministracionOne);
                View::set('reg', $reg);
                View::set('suc', $suc);
                View::set('pendientes', 'Todos los ');
                View::render("callcenter_cliente_all");
            }
        } else {

            $Solicitudes = CallCenterDao::getAllSolicitudes($cdgco);

            foreach ($Solicitudes as $key => $value) {
                if ($value['ESTATUS_CL'] == 'PENDIENTE') {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                } else if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO') {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                } else {
                    $color = 'success';
                    $icon = 'fa-check';
                }

                if ($value['ESTATUS_AV'] == 'PENDIENTE') {
                    $color_a = 'primary';
                    $icon_a = 'fa-frown-o';
                } else if ($value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $color_a = 'warning';
                    $icon_a = 'fa-clock-o';
                } else {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $titulo_boton = 'Seguir';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                } else if ($value['FIN_CL'] != '' || $value['FIN_AV'] != '') {
                    $titulo_boton = 'Acabar';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                } else {
                    $titulo_boton = 'Iniciar';
                    $color_boton = '#029f3f';
                    $fuente = '';
                }

                if ($value['COMENTARIO_INICIAL'] == '') {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                } else {
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }
                if ($value['COMENTARIO_FINAL'] == '') {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                } else {
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }
                if ($value['ESTATUS_FINAL'] == '') {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                } else {
                    $icon_ef = 'fa-clock-o';
                    $color_ef = 'warning';
                }
                if ($value['COMENTARIO_PRORROGA'] == '') {
                    $icon_cp_a = 'fa-close';
                    $color_cp_a = 'danger';
                } else {
                    $icon_cp_a = 'fa-check';
                    $color_cp_a = 'success';
                }

                if ($value['VOBO_REG'] == NULL) {
                    $vobo = '';
                } else {
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if ($value['PRORROGA'] == 2) {
                    $prorroga = '<hr><div><b>TIENE ACTIVA LA PRORROGA </b><span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #9101b2"><span class="fa fa-bell"> </span> </span></div><hr>';
                    $comentario_prorroga = '<div><span class="label label-' . $color_cp_a . '"><span class="fa ' . $icon_cp_a . '"></span></span> Comentarios Prorroga</div>';
                } else {
                    $prorroga = '';
                    $comentario_prorroga = '';
                }
                //var_dump($vobo);



                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 5px !important;"><label>{$value['CDGNS']}-{$value['CICLO']}</label></td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important; text-align: left">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                        $prorroga
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    $comentario_prorroga
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    </td>
                    <td style="padding-top: 22px !important;">
                        <a type="button" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Reg={$value['CODIGO_REGION']}" class="btn btn-primary btn-circle" style="background: $color_boton; color: $fuente "><i class="fa fa-edit"></i> <b>$titulo_boton</b>
                        </a>
                    </td>
                </tr>
html;
            }


            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('sucursal', $opciones_suc);
            View::set('pendientes', 'Todos los ');
            View::render("callcenter_pendientes_all");
        }
    }


    public function Administracion()
    {
        $tabla = '';
        $extraHeader = <<<html
        <title>Administrar Sucursales/Analistas</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
      
         $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
          "lengthMenu": [
                    [30, 50, -1],
                    [30, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
        });
         
         function enviar_add()
         {	
                    fecha_inicio = new Date(document.getElementById("fecha_inicio").value); 
                    fecha_fin =  new Date(document.getElementById("fecha_fin").value);
                    let diferencia = fecha_fin.getTime() - fecha_inicio.getTime();
                    let diasDeDiferencia = diferencia / 1000 / 60 / 60 / 24;
                    console.log(diasDeDiferencia); // resultado: 357
                    
                    if(diasDeDiferencia == 0)
                        {swal("Las fechas no pueden ser iguales", {icon: "warning",});}
                        else if(diasDeDiferencia  <= 0)
                        {swal("Recuerda que la Fecha de Fin no puede ser menor a la Fecha de Inicio, verifique la información.", {icon: "warning",});
                        }else
                            {
                                $.ajax({
                                      type: 'POST',
                                      url: '/CallCenter/AsignarSucursal/',
                                      data: $('#Add_AS').serialize(),
                                      success: function(respuesta) {
                                          if(respuesta=='1'){
                                             swal("Registro guardado exitosamente", {
                                                  icon: "success",
                                                  });
                                                 location.reload();
                                             }
                                          else {
                                                $('#modal_encuesta_cliente').modal('hide')
                                                 swal(respuesta, {
                                                              icon: "error",
                                                            });
                                                    document.getElementById("monto").value = "";
                                                }
                                            }
                                      });
                            }
        }
    
        
        function DeleteCDGCO(cdgco)
        {
            $.ajax({
            type: 'POST',
            url: '/CallCenter/DeleteAsignaSuc/',
            data: "cdgco="+ cdgco,
            success: function(respuesta) 
            {
              if(respuesta=='1')
                {               
                      swal("Registro actualizado exitosamente", {
                                              icon: "success",
                                            });
                      location.reload();
                }
            }
          });
        }
      </script>
html;

        $Analistas = CallCenterDao::getAllAnalistas();
        $Regiones = CallCenterDao::getAllRegiones();
        $getAnalistas = '';
        $getRegiones = '';
        $opciones = '';
        $opciones_region = '';

        foreach ($Analistas as $key => $val2) {

            $opciones .= <<<html
                <option  value="{$val2['USUARIO']}">({$val2['USUARIO']}) {$val2['NOMBRE']}</option>
html;
        }

        foreach ($Regiones as $key_r => $val_R) {

            $opciones_region .= <<<html
                <option  value="{$val_R['CODIGO']}">({$val_R['CODIGO']}) {$val_R['NOMBRE']}</option>
html;
        }


        $getAnalistas = <<<html
         <div class="col-md-12">
                <div class="form-group">
                     <label for="ejecutivo">Ejecutivo *</label>
                     <select class="form-control" autofocus type="select" id="ejecutivo" name="ejecutivo">
                        {$opciones}
                     </select>
                </div>
         </div>
html;
        $getRegiones = <<<html
         <div class="col-md-12">
                <div class="form-group">
                     <label for="region">Sucursal *</label>
                     <select class="form-control" autofocus type="select" id="region" name="region">
                        {$opciones_region}
                     </select>
                     <small id="emailHelp" class="form-text text-muted">Selecciona la sucursal que deseas asignar</small>
                </div>
         </div>
html;


        $AnalistasAsignadas = CallCenterDao::getAllAnalistasAsignadas();

        foreach ($AnalistasAsignadas as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td>{$value['CDGPE']}</td>
                    <td>{$value['NOMBRE_EJEC']}</td>
                    <td>{$value['CDGCO']}</td>
                    <td style="text-align: left;"><b>{$value['NOMBRE']}</b></td>
                    <td>{$value['FECHA_INICIO']}</td>
                    <td>{$value['FECHA_FIN']}</td>
                    <td>{$value['FECHA_ALTA']}</td>
                    <td>{$value['CDGOCPE']}</td>
                    <td style="padding: 0px !important;">
                       <button type="button" class="btn btn-danger btn-circle" onclick="DeleteCDGCO('{$value['CDGCO']}')"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
        }

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('Analistas', $getAnalistas);
        View::set('Regiones', $getRegiones);
        View::set('tabla', $tabla);
        View::render("asignar_sucursales_analistas");
    }

    public function DeleteAsignaSuc()
    {
        $cdgco = MasterDom::getDataAll('cdgco');
        $id = CallCenterDao::DeleteAsignaSuc($cdgco);
    }

    public function Historico()
    {
        $tabla = '';

        $extraFooter = <<<HTML
            <script>
                function getParameterByName(name) {
                    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]")
                    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                        results = regex.exec(location.search)
                    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "))
                }

                $(document).ready(function () {
                    $("#muestra-cupones").tablesorter()
                    var oTable = $("#muestra-cupones").DataTable({
                        lengthMenu: [
                            [13, 50, -1],
                            [13, 50, "Todos"]
                        ],
                        columnDefs: [
                            {
                                orderable: false,
                                targets: 0
                            }
                        ],
                        order: false
                    })
                    // Remove accented character from search input as well
                    $("#muestra-cupones input[type=search]").keyup(function () {
                        var table = $("#example").DataTable()
                        table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw()
                    })
                    var checkAll = 0
                })

                fecha1 = getParameterByName("Inicial")
                fecha2 = getParameterByName("Final")
                cdgco = getParameterByName("Suc")
                usuario = "{$this->__usuario}"

                $("#export_excel_consulta").click(function () {
                    $("#all").attr(
                        "action",
                        "/CallCenter/HistorialGeneraExcel/?Inicial=" +
                            fecha1 +
                            "&Final=" +
                            fecha2 +
                            "&Suc=" +
                            cdgco + ("{$_SESSION['perfil']}" == "ADMIN" ? "" :
                            "&Usuario=" +
                            usuario)
                    )
                    $("#all").attr("target", "_blank")
                    $("#all").submit()
                })

                function ProrrogaPedir(id_call, estatus, reactivacion) {
                    if (reactivacion == "1") {
                        swal("Actualmente tiene una REACTIVACION en espera de validación", { icon: "warning" })
                        return
                    }

                    if (estatus == "1") {
                        swal("Su solicitud de PRORROGA esta siendo validada", { icon: "warning" })
                        return
                    } else if (estatus == "3") {
                        swal("Su solicitud de PRORROGA fue declinada", { icon: "warning" })
                        return
                    }

                    swal({
                        title: "¿Está segura de solicitar a su administradora prorroga para esta solicitud?",
                        text: "",
                        icon: "warning",
                        buttons: ["Cancelar", "Continuar"],
                        dangerMode: false
                    }).then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                type: "POST",
                                url: "/CallCenter/ProrrogaUpdate/",
                                data: "prorroga=1" + "&id_call=" + id_call,
                                success: function (respuesta) {
                                    if (respuesta == "1") {
                                        swal("Registro guardado exitosamente", {
                                            icon: "success"
                                        })
                                        location.reload()
                                    } else {
                                        swal(respuesta, {
                                            icon: "error"
                                        })
                                    }
                                }
                            })
                        } else {
                            swal("Operación Cancelada", { icon: "warning" })
                        }
                    })
                }

                function VerResumen() {
                    alert("Hola")
                }

                function ReactivarSolicitud(id_call, estatus, reactivacion) {
                    if (estatus == "1") {
                        swal("Actualmente tiene una PRORROGA en espera de validación", { icon: "warning" })
                        return
                    }

                    if (reactivacion == "1") {
                        swal("Su solicitud de REACTIVACIÓN esta siendo validada", { icon: "warning" })
                        return
                    } else if (reactivacion == "3") {
                        swal("Su solicitud de REACTIVACIÓN fue declinada", { icon: "warning" })
                        return
                    }

                    swal({
                        title: "¿Está segura de solicitar la reactivación de la solicitud?",
                        text: "Usted podrá seguir editando la solicitud",
                        icon: "warning",
                        buttons: ["Cancelar", "Continuar"],
                        dangerMode: false
                    }).then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                type: "POST",
                                url: "/CallCenter/ReactivarSolicitudEjec/",
                                data: "id_call=" + id_call,
                                success: function (respuesta) {
                                    if (respuesta == "1") {
                                        swal("Registro guardado exitosamente", {
                                            icon: "success"
                                        })
                                        location.reload()
                                    } else {
                                        swal(respuesta, {
                                            icon: "error"
                                        })
                                    }
                                }
                            })
                        } else {
                            swal("Operación Cancelada", { icon: "warning" })
                        }
                    })
                }
            </script>
        HTML;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Sucursal = $_GET['Suc'];
        $cdgco = array();
        $opciones_suc = '';

        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);
        $opciones_suc .= '<option value="000">(000) TODAS MIS SUCURSALES INCLUIDAS OTRAS NO MOSTRADAS EN LA LISTA</option>';
        if ($ComboSucursales['success']) {
            if (isset($ComboSucursales['datos']) && count($ComboSucursales['datos']) > 0) {
                foreach ($ComboSucursales['datos'] as $key => $val2) {
                    $sel = $Sucursal == $val2['CODIGO'] ? 'selected' : '';

                    $opciones_suc .= <<<html
                        <option {$sel} value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
                    html;
                    array_push($cdgco, $val2['CODIGO']);
                }
            }
        }

        if ($Inicial != '' || $Final != '' || $Sucursal != '') {
            $Consulta = CallCenterDao::getAllSolicitudesHistorico($Inicial, $Final, $cdgco, $this->__usuario, $this->__perfil, $Sucursal);
            foreach ($Consulta as $key => $value) {

                if ($value['ESTATUS_CL'] == 'PENDIENTE') {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                } else if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO') {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                } else if ($value['ESTATUS_CL'] == '-') {
                    $color = 'danger';
                    $icon = 'fa-close';
                } else {
                    $color = 'success';
                    $icon = 'fa-check';
                }

                if ($value['ESTATUS_AV'] == 'PENDIENTE') {
                    $color_a = 'primary';
                    $icon_a = 'fa-frown-o';
                } else if ($value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $color_a = 'warning';
                    $icon_a = 'fa-clock-o';
                } else if ($value['ESTATUS_AV'] == '-') {
                    $color_a = 'danger';
                    $icon_a = 'fa-close';
                } else {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                } else if ($value['FIN_CL'] != '' || $value['FIN_AV'] != '') {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                } else {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#029f3f';
                    $fuente = '';
                }

                if ($value['COMENTARIO_INICIAL'] == '') {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                } else {
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }
                if ($value['COMENTARIO_FINAL'] == '') {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                } else {
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }
                if ($value['ESTATUS_FINAL'] == '') {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                } else {
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if ($value['VOBO_REG'] == NULL) {
                    $vobo = '';
                } else {
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if ($value['PRORROGA'] == NULL) {
                    $boton_titulo_prorroga = 'Prorroga';
                    $des_prorroga = '';
                    $boton_reactivar = '';
                } else {
                    if ($value['PRORROGA'] == '1') {
                        $boton_titulo_prorroga = 'Prorroga <br>Pendiente';
                    } else if ($value['PRORROGA'] == '2') {
                        $boton_titulo_prorroga = 'Prorroga <br>Aceptada';
                    } else if ($value['PRORROGA'] == '3') {
                        $boton_titulo_prorroga = 'Prorroga <br>Declinada';
                    } else if ($value['PRORROGA'] == '4') {
                        $boton_titulo_prorroga = 'Prorroga';
                    }
                }

                if ($value['REACTIVACION'] == NULL) {
                    $boton_titulo_reactivar = 'Reactivar';
                } else {

                    if ($value['REACTIVACION'] == '1') {
                        $boton_titulo_reactivar = 'Reactivar <br>Pendiente';
                    } else if ($value['REACTIVACION'] == '2') {
                        $boton_titulo_reactivar = 'Reactivar <br>Aceptado';
                    } else if ($value['REACTIVACION'] == '3') {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    } else if ($value['REACTIVACION'] == '400') {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }
                }

                if ($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-') {
                    $botones_prorroga = <<<html
                <td style="padding-top: 22px !important;">
                </td>
html;
                } else {
                    $botones_prorroga = <<<html
                <td style="padding-top: 22px !important;">
                        <a type="button" class="btn btn-primary btn-circle" onclick="ProrrogaPedir('{$value['ID_SCALL']}','{$value['PRORROGA']}','{$value['REACTIVAR']}');" style="background: $color_boton; color: $fuente " $des_prorroga><i class="fa fa-edit"></i> <b>$boton_titulo_prorroga</b>
                        </a>
                        <br>
                        <a type="button" class="btn btn-warning btn-circle" onclick="ReactivarSolicitud('{$value['ID_SCALL']}','{$value['PRORROGA']}','{$value['REACTIVAR']}');" style="background: #ffbcbc; color: #0D0A0A" ><i class="fa fa-repeat"></i> <b>$boton_titulo_reactivar</b>
                        </a>
                </td>
html;
                }
                if ($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-') {
                    $ver_resumen = '';
                } else {
                    $ver_resumen = <<<html
                        <hr>
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
html;
                }
                if ($value['RECOMENDADO'] != '' && $value['CICLO'] == '01') {
                    $recomendado = '<div><b>CAMPAÑA ACTIVA</b> <span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #6a0013"><span class="fa fa-yelp"> </span> </span></div><b><em>RECOMIENDA MÁS Y PAGA MENOS <em></em></b><hr>';
                } else {
                    $recomendado = '';
                }

                if ($value['CICLOR'] == '') {
                    $ciclo_r = '';
                    $cicloi = $value['CICLO'];;
                } else {
                    $ciclo_r = <<<html
                        <span  class="label label-warning" style="color: #0D0A0A; font-sice;  font-size: 12px;"> Rechazado</span>
html;
                    $cicloi = $value['CICLOR'];
                }


                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                   <td style="padding: 5px !important; width:65px !important;">
                    <div><span class="label label-success" style="color: #0D0A0A">MCM - {$value['ID_SCALL']}</span></div>
                    <hr>
                   <div><label>{$value['CDGNS']}-{$cicloi}</label></div>
                    <div><label>{$ciclo_r}</label></div>
                    </td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important; text-align: left">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                        <hr>
                         {$recomendado}
                        <div><b>VALIDO:</b> {$value['NOMBRE1']} {$value['PRIMAPE']} {$value['SEGAPE']}</div>
                        
                       
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    $ver_resumen
                    </td>
                    $botones_prorroga
                </tr>
html;
            }

            if ($Consulta[0] == '') {
                $vista = "historico_call_center_message_f";
            } else {
                View::set('tabla', $tabla);
                $vista = "Historico_Call_Center";
            }
        } else {
            $Consulta = CallCenterDao::getAllSolicitudesHistorico($fechaActual, $fechaActual, $cdgco, $this->__usuario, $this->__perfil, $Sucursal);
            foreach ($Consulta as $key => $value) {
                if ($value['ESTATUS_CL'] == 'PENDIENTE') {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                } else if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO') {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                } else {
                    $color = 'success';
                    $icon = 'fa-check';
                }

                if ($value['ESTATUS_AV'] == 'PENDIENTE') {
                    $color_a = 'primary';
                    $icon_a = 'fa-frown-o';
                } else if ($value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $color_a = 'warning';
                    $icon_a = 'fa-clock-o';
                } else {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                } else if ($value['FIN_CL'] != '' || $value['FIN_AV'] != '') {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                } else {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#029f3f';
                    $fuente = '';
                }

                if ($value['COMENTARIO_INICIAL'] == '') {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                } else {
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }
                if ($value['COMENTARIO_FINAL'] == '') {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                } else {
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }
                if ($value['ESTATUS_FINAL'] == '') {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                } else {
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if ($value['VOBO_REG'] == NULL) {
                    $vobo = '';
                } else {
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if ($value['PRORROGA'] == NULL) {
                    $boton_titulo_prorroga = 'Prorroga';
                    $des_prorroga = '';
                    $boton_reactivar = '';
                } else {
                    if ($value['PRORROGA'] == '1') {
                        $boton_titulo_prorroga = 'Prorroga <br>Pendiente';
                    } else if ($value['PRORROGA'] == '2') {
                        $boton_titulo_prorroga = 'Prorroga <br>Aceptada';
                    } else if ($value['PRORROGA'] == '3') {
                        $boton_titulo_prorroga = 'Prorroga <br>Declinada';
                    } else if ($value['PRORROGA'] == '4') {
                        $boton_titulo_prorroga = 'Prorroga';
                    }
                }

                if ($value['REACTIVACION'] == NULL) {
                    $boton_titulo_reactivar = 'Reactivar';
                } else {

                    if ($value['REACTIVACION'] == '1') {
                        $boton_titulo_reactivar = 'Reactivar <br>Pendiente';
                    } else if ($value['REACTIVACION'] == '2') {
                        $boton_titulo_reactivar = 'Reactivar <br>Aceptado';
                    } else if ($value['REACTIVACION'] == '3') {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    } else if ($value['REACTIVACION'] == '400') {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }
                }

                if ($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-') {
                    $botones_prorroga = <<<html
                <td style="padding-top: 22px !important;">
                </td>
html;
                } else {
                    $botones_prorroga = <<<html
                <td style="padding-top: 22px !important;">
                        <a type="button" class="btn btn-primary btn-circle" onclick="ProrrogaPedir('{$value['ID_SCALL']}','{$value['PRORROGA']}','{$value['REACTIVAR']}');" style="background: $color_boton; color: $fuente " $des_prorroga><i class="fa fa-edit"></i> <b>$boton_titulo_prorroga</b>
                        </a>
                        <br>
                        <a type="button" class="btn btn-warning btn-circle" onclick="ReactivarSolicitud('{$value['ID_SCALL']}','{$value['PRORROGA']}','{$value['REACTIVAR']}');" style="background: #ffbcbc; color: #0D0A0A" ><i class="fa fa-repeat"></i> <b>$boton_titulo_reactivar</b>
                        </a>
                </td>
html;
                }

                if ($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-') {
                    $ver_resumen = '';
                } else {
                    $ver_resumen = <<<html
                        <hr>
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
html;
                }

                if ($value['CICLOR'] == '') {
                    $ciclo_r = '';
                    $cicloi = $value['CICLO'];;
                } else {
                    $ciclo_r = <<<html
                        <span  class="label label-warning" style="color: #0D0A0A; font-sice;  font-size: 12px;"> Rechazado</span>
html;
                    $cicloi = $value['CICLOR'];
                }


                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                  <td style="padding: 5px !important; width:65px !important;">
                    <div><span class="label label-success" style="color: #0D0A0A">MCM - {$value['ID_SCALL']}</span></div>
                    <hr>
                    <div><label>{$value['CDGNS']}-{$cicloi}</label></div>
                    <div><label>{$ciclo_r}</label></div>
                    </td>
                    <td style="padding: 10px !important; text-align: left">
                         <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                        <br>
                         <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                        <br>
                        <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                    </td>
                    <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                    <td style="padding-top: 22px !important; text-align: left">
                        <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                        <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                        <hr>
                        <div><b>VALIDO:</b> {$value['NOMBRE1']} {$value['PRIMAPE']} {$value['SEGAPE']}</div>
                    </td>
                    <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                    <td style="padding: 10px !important; text-align: left; width:165px !important;">
                    <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                    <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                    <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                    $vobo
                    $ver_resumen
                    </td>
                    $botones_prorroga
                </tr>
html;
            }

            if ($Consulta[0] == '') {
                View::set('fechaActual', $fechaActual);
                $vista = "historico_call_center_message_f";
            } else {
                View::set('tabla', $tabla);
                $vista = "Historico_Call_Center";
            }
        }

        View::set('header', $this->_contenedor->header($this->GetExtraHeader('Histórico de Llamadas')));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('Inicial', $fechaActual);
        View::set('Final', $fechaActual);
        View::set('sucursal', $opciones_suc);
        View::render($vista);
    }

    public function HistoricoAnalistas()
    {
        $tabla = '';
        $extraHeader = <<<HTML
            <title>Histórico de Llamadas Analistas</title>
            <link rel="shortcut icon" href="/img/logo.png">
        HTML;

        $extraFooter = <<<HTML
            <script>
                function getParameterByName(name) {
                    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]")
                    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                        results = regex.exec(location.search)
                    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "))
                }

                $(document).ready(function () {
                    $("#muestra-cupones").tablesorter()
                    var oTable = $("#muestra-cupones").DataTable({
                        lengthMenu: [
                            [13, 50, -1],
                            [13, 50, "Todos"]
                        ],
                        columnDefs: [
                            {
                                orderable: false,
                                targets: 0
                            }
                        ],
                        order: false
                    })
                    // Remove accented character from search input as well
                    $("#muestra-cupones input[type=search]").keyup(function () {
                        var table = $("#example").DataTable()
                        table.search(jQuery.fn.DataTable.ext.type.search.html(this.value)).draw()
                    })
                    var checkAll = 0
                })

                fecha1 = getParameterByName("Inicial")
                fecha2 = getParameterByName("Final")
                cdgco = getParameterByName("Suc")

                $("#export_excel_consulta_analistas").click(function () {
                    $("#all").attr(
                        "action",
                        "/CallCenter/HistorialGeneraExcel/?Inicial=" + fecha1 + "&Final=" + fecha2 + "&Suc=" + cdgco
                    )
                    $("#all").attr("target", "_blank")
                    $("#all").submit()
                })
            </script>
        HTML;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Sucursal = $_GET['Suc'];
        $cdgco = array();
        $ComboSucursales = CallCenterDao::getComboSucursales($this->__usuario);

        if ($ComboSucursales['success']) {
            if (isset($ComboSucursales['datos']) && count($ComboSucursales['datos']) > 0) {
                foreach ($ComboSucursales['datos'] as $key => $val2) {
                    $sel = $Sucursal == $val2['CODIGO'] ? 'selected' : '';

                    $opciones_suc .= <<<html
                        <option {$Sucursal} value="{$val2['CODIGO']}">({$val2['CODIGO']}) {$val2['NOMBRE']}</option>
                    html;
                    array_push($cdgco, $val2['CODIGO']);
                }
            }
        }

        if ($Inicial != '' || $Final != '') {
            /////////////////////////////////
            $Consulta = CallCenterDao::getAllSolicitudesHistorico($Inicial, $Final, '', $this->__usuario, $this->__perfil, $Sucursal);
            foreach ($Consulta as $key => $value) {
                if ($value['ESTATUS_CL'] == 'PENDIENTE') {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                } else if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO') {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                } else if ($value['ESTATUS_CL'] == '-') {
                    $color = 'danger';
                    $icon = 'fa-close';
                } else {
                    $color = 'success';
                    $icon = 'fa-check';
                }

                if ($value['ESTATUS_AV'] == 'PENDIENTE') {
                    $color_a = 'primary';
                    $icon_a = 'fa-frown-o';
                } else if ($value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $color_a = 'warning';
                    $icon_a = 'fa-clock-o';
                } else if ($value['ESTATUS_AV'] == '-') {
                    $color_a = 'danger';
                    $icon_a = 'fa-close';
                } else {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                } else if ($value['FIN_CL'] != '' || $value['FIN_AV'] != '') {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                } else {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#029f3f';
                    $fuente = '';
                }

                if ($value['COMENTARIO_INICIAL'] == '') {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                } else {
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }
                if ($value['COMENTARIO_FINAL'] == '') {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                } else {
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }
                if ($value['ESTATUS_FINAL'] == '') {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                } else {
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if ($value['VOBO_REG'] == NULL) {
                    $vobo = '';
                } else {
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if ($value['PRORROGA'] == NULL) {
                    $boton_titulo_prorroga = 'Prorroga';
                    $des_prorroga = '';
                    $boton_reactivar = '';
                } else {
                    if ($value['PRORROGA'] == '1') {
                        $boton_titulo_prorroga = 'Prorroga <br>Pendiente';
                    } else if ($value['PRORROGA'] == '2') {
                        $boton_titulo_prorroga = 'Prorroga <br>Aceptada';
                    } else if ($value['PRORROGA'] == '3') {
                        $boton_titulo_prorroga = 'Prorroga <br>Declinada';
                    } else if ($value['PRORROGA'] == '4') {
                        $boton_titulo_prorroga = 'Prorroga';
                    }
                }

                if ($value['REACTIVACION'] == NULL) {
                    $boton_titulo_reactivar = 'Reactivar';
                } else {
                    if ($value['REACTIVACION'] == '1') {
                        $boton_titulo_reactivar = 'Reactivar <br>Pendiente';
                    } else if ($value['REACTIVACION'] == '2') {
                        $boton_titulo_reactivar = 'Reactivar <br>Aceptado';
                    } else if ($value['REACTIVACION'] == '3') {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    } else if ($value['REACTIVACION'] == '400') {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }
                }

                if ($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-') {
                    $ver_resumen = '';
                } else {
                    $ver_resumen = <<<HTML
                        <hr>
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
                    HTML;
                }

                if ($value['RECOMENDADO'] != '' && $value['CICLO'] == '01') {
                    $recomendado = '<div><b>CAMPAÑA ACTIVA</b> <span class="label label-success" style=" font-size: 95% !important; border-radius: 50em !important; background: #6a0013"><span class="fa fa-yelp"> </span> </span></div><b><em>RECOMIENDA MÁS Y PAGA MENOS <em></em></b><hr>';
                } else {
                    $recomendado = '';
                }

                if ($value['CICLOR'] == '') {
                    $ciclo_r = '';
                    $cicloi = $value['CICLO'];;
                } else {
                    $ciclo_r = <<<html
                        <span  class="label label-warning" style="color: #0D0A0A; font-sice;  font-size: 12px;"> Rechazado</span>
html;
                    $cicloi = $value['CICLOR'];
                }

                $tabla .= <<<HTML
                    <tr style="padding: 0px !important;">
                       <td style="padding: 5px !important; width:65px !important;">
                    <div><span class="label label-success" style="color: #0D0A0A">MCM - {$value['ID_SCALL']}</span></div>
                    <hr>
                    <div><label>{$value['CDGNS']}-{$cicloi}</label></div>
                    <div><label>{$ciclo_r}</label></div>
                    </td>
                        <td style="padding: 10px !important; text-align: left">
                            <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                            <br>
                            <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                            <br>
                            <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                        </td>
                        <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                        <td style="padding-top: 22px !important; text-align: left">
                            <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                            <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                            <hr>
                            {$recomendado}
                            <div><b>VALIDO:</b> {$value['NOMBRE1']} {$value['PRIMAPE']} {$value['SEGAPE']}</div>
                        </td>
                        <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                        <td style="padding: 10px !important; text-align: left; width:165px !important;">
                        <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                        <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                        <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                        $vobo
                        $ver_resumen
                        </td>
                    </tr>
                HTML;
            }

            if ($Consulta[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('Inicial', $fechaActual);
                View::set('Final', $fechaActual);
                View::render("historico_analistas_message_f");
            } else {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('Inicial', $Inicial);
                View::set('Final', $Final);
                View::render("Historico_Analistas_Center");
            }
        } else {
            $Consulta = CallCenterDao::getAllSolicitudesHistorico($fechaActual, $fechaActual, '', $this->__usuario, $this->__perfil, $Sucursal);
            foreach ($Consulta as $key => $value) {
                if ($value['ESTATUS_CL'] == 'PENDIENTE') {
                    $color = 'primary';
                    $icon = 'fa-frown-o';
                } else if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO') {
                    $color = 'warning';
                    $icon = 'fa-clock-o';
                } else {
                    $color = 'success';
                    $icon = 'fa-check';
                }

                if ($value['ESTATUS_AV'] == 'PENDIENTE') {
                    $color_a = 'primary';
                    $icon_a = 'fa-frown-o';
                } else if ($value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $color_a = 'warning';
                    $icon_a = 'fa-clock-o';
                } else {
                    $color_a = 'success';
                    $icon_a = 'fa-check';
                }

                if ($value['ESTATUS_CL'] == 'REGISTRO INCOMPLETO' || $value['ESTATUS_AV'] == 'REGISTRO INCOMPLETO') {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#F0AD4E';
                    $fuente = '#0D0A0A';
                } else if ($value['FIN_CL'] != '' || $value['FIN_AV'] != '') {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#0D0A0A';
                    $fuente = '';
                } else {
                    $titulo_boton = 'Pedir Prorroga';
                    $color_boton = '#029f3f';
                    $fuente = '';
                }

                if ($value['COMENTARIO_INICIAL'] == '') {
                    $icon_ci = 'fa-close';
                    $color_ci = 'danger';
                } else {
                    $icon_ci = 'fa-check';
                    $color_ci = 'success';
                }
                if ($value['COMENTARIO_FINAL'] == '') {
                    $icon_cf = 'fa-close';
                    $color_cf = 'danger';
                } else {
                    $icon_cf = 'fa-check';
                    $color_cf = 'success';
                }
                if ($value['ESTATUS_FINAL'] == '') {
                    $icon_ef = 'fa-close';
                    $color_ef = 'danger';
                } else {
                    $icon_ef = 'fa-check';
                    $color_ef = 'success';
                }

                if ($value['VOBO_REG'] == NULL) {
                    $vobo = '';
                } else {
                    $vobo = '<div><span class="label label-success"><span class="fa fa-check"></span></span> VoBo Gerente Regional</div>';
                }

                if ($value['PRORROGA'] == NULL) {
                    $boton_titulo_prorroga = 'Prorroga';
                    $des_prorroga = '';
                    $boton_reactivar = '';
                } else {
                    if ($value['PRORROGA'] == '1') {
                        $boton_titulo_prorroga = 'Prorroga <br>Pendiente';
                    } else if ($value['PRORROGA'] == '2') {
                        $boton_titulo_prorroga = 'Prorroga <br>Aceptada';
                    } else if ($value['PRORROGA'] == '3') {
                        $boton_titulo_prorroga = 'Prorroga <br>Declinada';
                    } else if ($value['PRORROGA'] == '4') {
                        $boton_titulo_prorroga = 'Prorroga';
                    }
                }

                if ($value['REACTIVACION'] == NULL) {
                    $boton_titulo_reactivar = 'Reactivar';
                } else {
                    if ($value['REACTIVACION'] == '1') {
                        $boton_titulo_reactivar = 'Reactivar <br>Pendiente';
                    } else if ($value['REACTIVACION'] == '2') {
                        $boton_titulo_reactivar = 'Reactivar <br>Aceptado';
                    } else if ($value['REACTIVACION'] == '3') {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    } else if ($value['REACTIVACION'] == '400') {
                        $boton_titulo_reactivar = 'Reactivar Declinado';
                    }
                }

                if ($value['NOMBRE1'] == 'PENDIENTE DE VALIDAR' || $value['NOMBRE1'] == '-') {
                    $ver_resumen = '';
                } else {
                    $ver_resumen = <<<HTML
                        <hr>
                        <a type="button" target="_blank" href="/CallCenter/Pendientes/?Credito={$value['CDGNS']}&Ciclo={$value['CICLO']}&Suc={$value['CODIGO_SUCURSAL']}&Act=N&Reg={$value['CODIGO_REGION']}&Fec={$value['FECHA_SOL']}" class="btn btn-primary btn-circle"><span class="label label-info"><span class="fa fa-eye"></span></span> Ver Resumen
                        </a>
                    HTML;
                }

                if ($value['CICLOR'] == '') {
                    $ciclo_r = '';
                    $cicloi = $value['CICLO'];;
                } else {
                    $ciclo_r = <<<html
                        <span  class="label label-warning" style="color: #0D0A0A; font-sice;  font-size: 12px;"> Rechazado</span>
html;
                    $cicloi = $value['CICLOR'];
                }

                $tabla .= <<<HTML
                    <tr style="padding: 0px !important;">
                       <td style="padding: 5px !important; width:65px !important;">
                    <div><span class="label label-success" style="color: #0D0A0A">MCM - {$value['ID_SCALL']}</span></div>
                    <hr>
                    <div><label>{$value['CDGNS']}-{$cicloi}</label></div>
                    <div><label>{$ciclo_r}</label></div>
                    
                    </td>
                        <td style="padding: 10px !important; text-align: left">
                            <span class="fa fa-building"></span> GERENCIA REGIONAL: ({$value['CODIGO_REGION']}) {$value['REGION']}
                            <br>
                            <span class="fa fa-map-marker"></span> SUCURSAL: ({$value['CODIGO_SUCURSAL']}) {$value['NOMBRE_SUCURSAL']}
                            <br>
                            <span class="fa fa-briefcase"></span> EJECUTIVO: {$value['EJECUTIVO']}
                        </td>
                        <td style="padding-top: 10px !important;"><span class="fa fa-user"></span> <label style="color: #1c4e63">{$value['NOMBRE']}</label></td>
                        <td style="padding-top: 22px !important; text-align: left">
                            <div><b>CLIENTE:</b> {$value['ESTATUS_CL']}  <span class="label label-$color" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon"></span></span></div>
                            <div><b>AVAL:</b> {$value['ESTATUS_AV']}  <span class="label label-$color_a" style="font-size: 95% !important; border-radius: 50em !important;"><span class="fa $icon_a"></span> </span></div>
                            <hr>
                            <div><b>VALIDO:</b> {$value['NOMBRE1']} {$value['PRIMAPE']} {$value['SEGAPE']}</div>
                        </td>
                        <td style="padding-top: 22px !important;">{$value['FECHA_SOL']}</td>
                        <td style="padding: 10px !important; text-align: left; width:165px !important;">
                        <div><span class="label label-$color_ci" ><span class="fa $icon_ci"></span></span> Comentarios Iniciales</div>
                        <div><span class="label label-$color_cf"><span class="fa $icon_cf"></span></span> Comentarios Finales</div>
                        <div><span class="label label-$color_ef"><span class="fa $icon_ef"></span></span> Estatus Final Solicitud</div>
                        $vobo
                        $ver_resumen
                        </td>
                    </tr>
                HTML;
            }

            if ($Consulta[0] == '') {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('fechaActual', $fechaActual);
                View::set('Inicial', $fechaActual);
                View::set('Final', $fechaActual);
                View::render("historico_analistas_message_f");
            } else {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('Inicial', $fechaActual);
                View::set('Final', $fechaActual);
                View::render("Historico_Analistas_Center");
            }
        }
    }

    public function PagosAddEncuestaCL()
    {
        $encuesta = new \stdClass();

        $encuesta->_uno = $_POST["uno_cl"];
        $encuesta->_dos = $_POST["dos_cl"];
        $encuesta->_tres = $_POST["tres_cl"];
        $encuesta->_cuatro = $_POST["cuatro_cl"];
        $encuesta->_cinco = $_POST["cinco_cl"];
        $encuesta->_seis = $_POST["seis_cl"];
        $encuesta->_siete = $_POST["siete_cl"];
        $encuesta->_ocho = $_POST["ocho_cl"];
        $encuesta->_nueve = $_POST["nueve_cl"];
        $encuesta->_diez = $_POST["diez_cl"];
        $encuesta->_once = $_POST["once_cl"];
        $encuesta->_doce = $_POST["doce_cl"];

        $encuesta->_llamada = $_POST["contenido"];
        $encuesta->_cdgre = $_POST["cdgre"];
        $encuesta->_fecha = $_POST["fecha_cl"];
        $encuesta->_fecha_solicitud = $_POST["fecha_solicitud"];
        $encuesta->_cdgns = $_POST["cdgns"];
        $encuesta->_cdgco = $_POST["cdgco"];
        $encuesta->_cdgpe = $this->__usuario;
        $encuesta->_cliente = $_POST["cliente_id"];
        $encuesta->_ciclo = $_POST["ciclo_cl"];
        $encuesta->_movil = $_POST["movil_cl"];
        $encuesta->_tipo_llamada = $_POST["tipo_llamada_cl"];
        $encuesta->_id_aval_cl = $_POST["id_aval_cl_0"];
        $encuesta->_telefono_aval_cl = $_POST["telefono_aval_cl_0"];
        $encuesta->_id_aval_cl_2 = $_POST["id_aval_cl_1"];
        $encuesta->_telefono_aval_cl_2 = $_POST["telefono_aval_cl_1"];

        $encuesta->_completo = $_POST['completo'];
        CallCenterDao::insertEncuestaCL($encuesta);
    }

    public function PagosAddEncuestaAV()
    {
        $encuesta = new \stdClass();
        $no_av = $_POST['no_av'];
        $encuesta->_no_av = $no_av + 1;

        $encuesta->_uno = $_POST["uno_av_$no_av"];
        $encuesta->_dos = $_POST["dos_av_$no_av"];
        $encuesta->_tres = $_POST["tres_av_$no_av"];
        $encuesta->_cuatro = $_POST["cuatro_av_$no_av"];
        $encuesta->_cinco = $_POST["cinco_av_$no_av"];
        $encuesta->_seis = $_POST["seis_av_$no_av"];
        $encuesta->_siete = $_POST["siete_av_$no_av"];
        $encuesta->_ocho = $_POST["ocho_av_$no_av"];
        $encuesta->_nueve = $_POST["nueve_av_$no_av"];

        $encuesta->_llamada = $_POST["contenido_av_$no_av"];
        $encuesta->_tipo_llamada = $_POST["tipo_llamada_av_$no_av"];
        $encuesta->_completo = $_POST["completo_av_$no_av"];

        $encuesta->_cdgco = $_POST["cdgco_av_$no_av"];
        $encuesta->_cliente = $_POST["cliente_id_av_$no_av"];
        $encuesta->_ciclo = $_POST["ciclo_av_$no_av"];

        CallCenterDao::insertEncuestaAV($encuesta);
    }

    public function Resumen()
    {
        $encuesta = new \stdClass();
        $encuesta->_cdgco = MasterDom::getData('cdgco_res');
        $encuesta->_cliente = MasterDom::getData('cliente_id_res');
        $encuesta->_ciclo = MasterDom::getData('ciclo_cl_res');
        $encuesta->_comentarios_iniciales = MasterDom::getDataAll('comentarios_iniciales');
        $encuesta->_comentarios_finales = MasterDom::getData('comentarios_finales');
        $encuesta->_comentarios_prorroga = MasterDom::getData('comentarios_prorroga');

        $id = CallCenterDao::UpdateResumen($encuesta);
    }

    public function ComentariosRetiro()
    {
        echo json_encode(CallCenterDao::ActualizaComentariosRetiro($_POST));
    }

    public function ProrrogaUpdate()
    {
        $encuesta = new \stdClass();
        $encuesta->_prorroga = MasterDom::getData('prorroga');
        $encuesta->_id_call = MasterDom::getData('id_call');

        $id = CallCenterDao::UpdateProrroga($encuesta);
    }

    public function ReactivarSolicitudEjec()
    {
        $encuesta = new \stdClass();
        $encuesta->_id_call = MasterDom::getData('id_call');
        $encuesta->_opcion = MasterDom::getData('opcion');

        $id = CallCenterDao::ReactivarSolicitud($encuesta);
    }

    public function ReactivarSolicitudAdminPost()
    {
        $encuesta = new \stdClass();
        $encuesta->_id_call = MasterDom::getData('id_call');
        $encuesta->_opcion = MasterDom::getData('opcion');

        $id = CallCenterDao::ReactivarSolicitudAdmin($encuesta);
    }

    public function ResumenEjecutivo()
    {
        $encuesta = new \stdClass();
        $encuesta->_cdgco = MasterDom::getData('cdgco_res');
        $encuesta->_cliente = MasterDom::getData('cliente_id_res');
        $encuesta->_ciclo = MasterDom::getData('ciclo_cl_res');
        $encuesta->_comentarios_iniciales = MasterDom::getDataAll('comentarios_iniciales');
        $encuesta->_comentarios_finales = MasterDom::getData('comentarios_finales');
        $encuesta->_estatus_solicitud = MasterDom::getData('estatus_solicitud');
        $encuesta->_vobo_gerente = MasterDom::getData('vobo_gerente');

        $id = CallCenterDao::UpdateResumenFinal($encuesta);
    }

    public function AsignarSucursal()
    {

        $asigna = new \stdClass();
        $asigna->_fecha_registro = MasterDom::getDataAll('fecha_registro');
        $asigna->_fecha_inicio = MasterDom::getData('fecha_inicio');
        $asigna->_fecha_fin = MasterDom::getData('fecha_fin');
        $asigna->_ejecutivo = MasterDom::getData('ejecutivo');
        $asigna->_region = MasterDom::getData('region');

        $id = CallCenterDao::insertAsignaSucursal($asigna);
    }

    public function HistorialGeneraExcel()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('A', '-'),
            \PHPSpreadsheet::ColumnaExcel('B', 'NOMBRE REGION'),
            \PHPSpreadsheet::ColumnaExcel('C', 'FECHA DE TRABAJO', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('D', 'SOLICITUD', ['estilo' => $estilos['fecha_hora']]),
            \PHPSpreadsheet::ColumnaExcel('E', 'ESTATUS FINAL'),
            \PHPSpreadsheet::ColumnaExcel('F', 'AGENCIA'),
            \PHPSpreadsheet::ColumnaExcel('G', 'EJECUTIVO'),
            \PHPSpreadsheet::ColumnaExcel('H', 'CLIENTE', ['estilo' => $estilos['texto_derecha']]),
            \PHPSpreadsheet::ColumnaExcel('I', 'NOMBRE DE CLIENTE'),
            \PHPSpreadsheet::ColumnaExcel('J', 'CICLO', ['estilo' => $estilos['texto_centrado']]),
            \PHPSpreadsheet::ColumnaExcel('K', 'TELEFONO CLIENTE', ['estilo' => $estilos['texto_derecha']]),
            \PHPSpreadsheet::ColumnaExcel('L', 'TIPO DE LLAMADA'),
            \PHPSpreadsheet::ColumnaExcel('M', '¿Qué edad tiene?'),
            \PHPSpreadsheet::ColumnaExcel('N', '¿Cuál es su fecha de nacimiento?'),
            \PHPSpreadsheet::ColumnaExcel('O', 'Me proporciona su domicilio completo por favor'),
            \PHPSpreadsheet::ColumnaExcel('P', '¿Qué tiempo tiene viviendo en este domicilio?'),
            \PHPSpreadsheet::ColumnaExcel('Q', 'Actualmente ¿cual es su principal fuente de ingresos?'),
            \PHPSpreadsheet::ColumnaExcel('R', '¿Cuál es el nombre de su aval?'),
            \PHPSpreadsheet::ColumnaExcel('S', '¿Que Relación tiene con su aval?'),
            \PHPSpreadsheet::ColumnaExcel('T', '¿Cual es la actividad económica de su aval?'),
            \PHPSpreadsheet::ColumnaExcel('U', 'Por favor me proporciona el número telefónico de su aval'),
            \PHPSpreadsheet::ColumnaExcel('V', '¿Firmó su solicitud? ¿Cuando?'),
            \PHPSpreadsheet::ColumnaExcel('W', 'Me puede indicar ¿para qué utilizará su crédito?'),
            \PHPSpreadsheet::ColumnaExcel('X', '¿Compartirá su crédito con alguna otra persona?'),
            \PHPSpreadsheet::ColumnaExcel('Y', 'NOMBRE DEL AVAL'),
            \PHPSpreadsheet::ColumnaExcel('Z', 'TELEFONO DE AVAL', ['estilo' => $estilos['texto_derecha']]),
            \PHPSpreadsheet::ColumnaExcel('AA', 'TIPO DE LLAMADA'),
            \PHPSpreadsheet::ColumnaExcel('AB', '¿Qué edad tiene?'),
            \PHPSpreadsheet::ColumnaExcel('AC', 'Me indica su fecha de nacimiento por favor'),
            \PHPSpreadsheet::ColumnaExcel('AD', '¿Cuál es su domicilio?'),
            \PHPSpreadsheet::ColumnaExcel('AE', '¿Qué tiempo lleva viviendo en este domicilio?'),
            \PHPSpreadsheet::ColumnaExcel('AF', 'Actualmente  ¿cual es su principal fuente de ingresos?'),
            \PHPSpreadsheet::ColumnaExcel('AG', '¿Hace cuanto conoce a  “Nombre del cliente”?'),
            \PHPSpreadsheet::ColumnaExcel('AH', '¿Qué Relación tiene con “Nombre del cliente”?'),
            \PHPSpreadsheet::ColumnaExcel('AI', '¿Sabe a que se dedica el Sr. (nombre de cliente)?'),
            \PHPSpreadsheet::ColumnaExcel('AJ', 'Me puede proporcionar el numero telefónico de “cliente”'),
            \PHPSpreadsheet::ColumnaExcel('AK', 'DIA/HORA DE LLAMADA 1 CL', ['estilo' => $estilos['fecha_hora']]),
            \PHPSpreadsheet::ColumnaExcel('AL', 'DIA/HORA DE LLAMADA 2 CL'),
            \PHPSpreadsheet::ColumnaExcel('AM', 'DIA/HORA DE LLAMADA 1 AV', ['estilo' => $estilos['fecha_hora']]),
            \PHPSpreadsheet::ColumnaExcel('AN', 'DIA/HORA DE LLAMADA 1 AV'),
            \PHPSpreadsheet::ColumnaExcel('AO', 'COMENTARIO INICIAL'),
            \PHPSpreadsheet::ColumnaExcel('AP', 'COMENTARIO FINAL'),
            \PHPSpreadsheet::ColumnaExcel('AQ', 'ESTATUS'),
            \PHPSpreadsheet::ColumnaExcel('AR', 'INCIDENCIA COMERCIAL - ADMINISTRACION'),
            \PHPSpreadsheet::ColumnaExcel('AS_', 'Vo Bo GERENTE REGIONAL'),
            \PHPSpreadsheet::ColumnaExcel('AT_', 'ANALISTA'),
            \PHPSpreadsheet::ColumnaExcel('AU', 'SEMAFORO'),
            \PHPSpreadsheet::ColumnaExcel('AV', 'FECHA DE DESEMBOLSO'),
            \PHPSpreadsheet::ColumnaExcel('AW', '$ ENTREGADA'),
            \PHPSpreadsheet::ColumnaExcel('AX', '$ PARCIALIDAD'),
            \PHPSpreadsheet::ColumnaExcel('AY', 'MORA AL CORTE'),
            \PHPSpreadsheet::ColumnaExcel('AZ', '#  SEMANAS CON ATRASO'),
            \PHPSpreadsheet::ColumnaExcel('BA', 'MES'),
            \PHPSpreadsheet::ColumnaExcel('BB', 'LLAMADA POSTVENTA'),
            \PHPSpreadsheet::ColumnaExcel('BC', 'RECAPTURADA SI-NO'),
            \PHPSpreadsheet::ColumnaExcel('BD', 'ANALISTA INICIAL')
        ];

        $sucursales = CallCenterDao::getComboSucursalesAllCDGCO($_GET);

        $sucursales = $sucursales['success'] && $sucursales['datos']['SUCURSALES'] ? $sucursales['datos']['SUCURSALES'] : '000';
        $datos = [
            'fechaI' => $_GET['Inicial'] == '' ? date('Y-m-d') : $_GET['Inicial'],
            'fechaF' => $_GET['Final'] == '' ? date('Y-m-d') : $_GET['Final'],
            'sucursales' => $sucursales,
            'usuario' => $_GET['Usuario'],
        ];
        $filas = CallCenterDao::getAllSolicitudesHistoricoExcel($datos);
        $filas = $filas['success'] ? $filas['datos'] : [];

        \PHPSpreadsheet::DescargaExcel('Reporte Llamadas Finalizadas', 'Reporte', 'Reporte de Solicitudes', $columnas, $filas);
    }

    public function EncuestaPostventa()
    {
        $ids = session_id();

        $extraFooter = <<<HTML
            <script>
                {$this->conectaSocket}
                {$this->formatoMoneda}
                {$this->mensajes}
                {$this->confirmarMovimiento}

                let motivos = null
                let tiempo = null
                let vMotivo = null
                let vComentario = null
                const datosEncuesta = {
                    asesor: "{$this->__usuario}",
                    cliente: null,
                    ciclo: null,
                    telefono: null,
                    estatus: null,
                    comentario_asesor: null,
                    respuesta_1: null,
                    comentario_1: null,
                    respuesta_2: null,
                    comentario_2: null,
                    respuesta_3: null,
                    comentario_3: null,
                    respuesta_4: null,
                    comentario_4: null,
                    respuesta_5: null,
                    comentario_5: null,
                    comentario_general: null,
                    duracion: 0,
                    motivo: null
                }

                const solicitaComentario = (abandono = false) => {
                    const contenedor = document.createElement("div")
                    contenedor.setAttribute("style", "width: 100%; display: flex; flex-direction: column; align-items: center;")

                    const titulo = document.createElement("span")
                    titulo.setAttribute("style", "width: 100%; text-align: left;")
                    titulo.textContent = abandono ? "Motivo de abandono" : "Comentario del asesor"
                    contenedor.appendChild(titulo)

                    const comentario = document.createElement("textarea")
                    comentario.setAttribute("style", "resize: none; width: 100%; margin-top: 10px;")
                    comentario.setAttribute("rows", 3)
                    comentario.setAttribute("placeholder", "Escriba aquí " + (abandono ? "el motivo" :  "sus comentarios"))
                    comentario.setAttribute("class", "swal-content__input")
                    comentario.addEventListener("input", () => {
                        vComentario = comentario.value
                    })

                    if (!abandono) contenedor.appendChild(comentario)
                    else {
                        comentario.style.display = "none"
                        comentario.addEventListener("input", () => {
                            $(".continuar").prop("disabled", !comentario.value)
                        })

                        const motivo = document.createElement("select")
                        motivo.setAttribute("style", "width: 100%;")
                        motivo.setAttribute("required", true)
                        Object.entries(motivos).forEach(([clave, valor]) => {
                            const opcion = document.createElement("option")
                            opcion.setAttribute("value", clave)
                            opcion.textContent = valor
                            motivo.appendChild(opcion)
                            if (!vMotivo) vMotivo = clave
                        })
                        motivo.addEventListener("change", () => {
                            if (motivo.value !== "OT") comentario.value = ""
                            comentario.required = motivo.value === "OT"
                            comentario.style.display = motivo.value === "OT" ? "block" : "none"
                            $(".continuar").prop("disabled", motivo.value === "OT" && !comentario.value)
                            vMotivo = motivo.value
                        })

                        contenedor.appendChild(motivo)
                        contenedor.appendChild(comentario)
                    } 
                    
                    return swal("Cierre de encuesta", {
                                content: {
                                    element: contenedor
                                },
                                buttons: {
                                    confirm: {
                                        text: abandono ? "Abandonar" : "Finalizar",
                                        className: "continuar"
                                    }
                                },
                                closeOnClickOutside: false,
                                closeOnEsc: false
                            })
                }

                const abandono = document.createElement("span")
                abandono.setAttribute("style", "width: 100%; font-size: 19px; text-align: center; margin-top: 20px; color: #000000a3;")
                abandono.innerHTML = "<p>¿Segura desea <b>abandonar</b> la encuesta?<br>Se le asignará otro cliente.</p>"

                const confirmacion = document.createElement("span")
                confirmacion.setAttribute("style", "width: 100%; font-size: 19px; text-align: center; margin-top: 20px; color: #000000a3;")
                confirmacion.innerHTML = "<p>¿Segura desea guardar la encuesta y continuar con otro cliente?</p>"

                $(document).ready(() => {
                    $("#fotoCliente").attr("src", "/img/n.gif")
                    $("#inicio").on("click", function () {
                        tiempo = new Date().getTime()
                        if (!$(this).hasClass("active")) return cambiaEstado()

                        guardaEncuesta("ABANDONO", true, abandono)
                    })

                    $(".modal-content").find(":radio").on("change", function () {
                        datosEncuesta[$(this).attr("name")] = $(this).val() === "1" ? 1 : 0
                        $("#guardaEncuesta").prop("disabled", ($('.respuesta:checked').length !== $(".pregunta").length))
                    })

                    $(".modal-content").find(":text").on("input", function () {
                        datosEncuesta[$(this).attr("name")] = $(this).val()
                    })

                    $("#comentario_general").on("input", function () {
                        datosEncuesta["comentario_general"] = $(this).val()
                    })
                })

                const socket = conectaSocket("{$this->configuracion['API_SOCKETGRAL']}", "callcenter", {
                    asesor: datosEncuesta.asesor,
                    datosRequeridos: datosEncuesta
                })

                socket.on("conectado", (datos) => {
                    motivos = datos.motivos
                    swal.close()
                })

                socket.on("connect_error", (error) => {
                    console.log("Error al conectar con el socket:", error)
                    showWait("Hay problemas con la conexión al servidor, reintentando...")
                })

                socket.on("mensajeSuper", (mensaje) => {
                    swal({
                        title: "Mensaje del supervisor",
                        text: mensaje,
                        icon: "info",
                        button: "Aceptar"
                    })
                })

                socket.on("asignando", () => {
                    showWait("Asignando cliente...")
                })

                socket.on("clienteAsignado", (asignacion) => {
                    if (!asignacion.success) {
                        console.log("Error al asignar cliente:", asignacion.error)
                        return showError(asignacion.mensaje)
                    }

                    const datos = asignacion.datos
                    const sexo = ["m", "f"].includes(datos.SEXO.toLowerCase()) ? datos.SEXO.toLowerCase() : "n"
                    datosEncuesta.cliente = datos.CLIENTE
                    datosEncuesta.ciclo = datos.CICLO
                    datosEncuesta.telefono = datos.TELEFONO

                    $("#inicio").prop("disabled", false)
                    $("#nombre").text(datos.NOMBRE)
                    $("#telefono").text("Tel: " + datos.TELEFONO.replace(/(\d{2})(\d{4})(\d{4})/, "$1  $2  $3"))
                    $("#cliente").text(datos.CLIENTE)
                    $("#sucursal").text(datos.SUCURSAL + " - " + datos.NOMBRE_SUCURSAL)
                    $("#ciclo").text(datos.CICLO)
                    $("#monto").text("$ " + formatoMoneda(datos.MONTO))
                    $("#fotoCliente").attr("src", "/img/" + sexo + ".gif")
                    swal.close()
                })

                const cambiaEstado = () => {
                    $("#inicio").toggleClass("active")
                    $("#icono").toggleClass("fa-ban")
                    $("#icono").toggleClass("fa-phone")
                    $(".modal-content").slideToggle("slow", function () {
                        $(this).find("input:text").val("")
                        $(this).find(":radio").prop("checked", false)
                        $(this).find("textarea").val("")
                    })

                    if (!$("#inicio").hasClass("active")) {
                        $("#textoAuxiliar").text("Iniciar")
                        $("#guardaEncuesta").prop("disabled", true)
                        $("#nombre").text("")
                        $("#telefono").text("")
                        $("#cliente").text("")
                        $("#sucursal").text("")
                        $("#ciclo").text("")
                        $("#monto").text("")
                        $("#fotoCliente").attr("src", "/img/n.gif")
                        $("#inicio").prop("disabled", $("#nombre").text() === "")
                    } else {
                        $("#textoAuxiliar").text("Abandonar")
                        socket.emit("cambiaEstatus", "En llamada")
                    }
                    vMotivo = null
                    vComentario = null

                }

                const guardaEncuesta = (stat, abandono = false, pregunta = confirmacion) => {
                    confirmarMovimiento("Encuesta Postventa", null, pregunta)
                    .then((continuar) => {
                        if (!continuar) return
                        solicitaComentario(abandono).then((comentario) => {
                            socket.emit("cambiaEstatus", "Finalizando")
                            datosEncuesta.comentario_asesor = vComentario
                            datosEncuesta.motivo = vMotivo
                            datosEncuesta.estatus = stat
                            datosEncuesta.duracion = Math.round((new Date().getTime() - tiempo) / 1000)
                            socket.emit("guardaEncuesta", {datosEncuesta, abandono})
                            cambiaEstado()
                            limpiaRespuestas()
                        })
                    })
                }

                const limpiaRespuestas = () => {
                    $(".respuesta").each(function() {
                        datosEncuesta[$(this).attr("name")] = null
                    })
                    $(".comentario").each(function() {
                        datosEncuesta[$(this).attr("name")] = null
                    })
                    datosEncuesta.cliente = null
                    datosEncuesta.ciclo = null
                    datosEncuesta.telefono = null
                    datosEncuesta.estatus = null
                    datosEncuesta.duracion = 0
                    datosEncuesta.comentario_asesor = null
                    datosEncuesta.comentario_general = null
                }
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Postventa", [$this->socket])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("callcenter_encuestaPostventa", $extraFooter);
    }

    public function AsignaClienteEncuestaPostventa()
    {
        $r = CallCenterDao::AsignaClienteEncuestaPostventa($_POST);
        echo json_encode($r);
    }

    public function ActualizaClienteEncuestaPostventa()
    {
        $r = CallCenterDao::ActualizaClienteEncuestaPostventa($_POST);
        echo json_encode($r);
    }

    public function GuardaEncuestaPostventa()
    {
        $r = CallCenterDao::GuardaEncuestaPostventa($_POST);
        echo json_encode($r);
    }

    public function ReporteEncuestaPostventa()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->configuraTabla}
                {$this->descargaExcel}
                {$this->consultaServidor}


                $(document).ready(() => {
                    configuraTabla("reporte")

                    $("#buscar").on("click", () => {
                        const parametros = {
                            fechaI: $("#fechaI").val(),
                            fechaF: $("#fechaF").val(),
                            estatus: $("#estatus").val()
                        }
                        consultaServidor("/CallCenter/HTMLReporteEncuestaPostventa", parametros, (respuesta) => {
                            $("#reporte").DataTable().destroy()
                            if (!respuesta.success) showError(respuesta.mensaje)
                            $("#reporte tbody").html(respuesta.datos)
                            configuraTabla("reporte")
                        })
                    })

                    $("#descargar").on("click", () => {
                        const parametros = {
                            fechaI: $("#fechaI").val(),
                            fechaF: $("#fechaF").val(),
                            estatus: $("#estatus").val()
                        }

                        descargaExcel("/CallCenter/ExcelReporteEncuestaPostventa/?" + $.param(parametros))
                    })
                })
            </script>
        HTML;

        $estatus = CallCenterDao::GetEstatusEncuestaPostventa();

        $optEstatus = '<option value="*" selected>Todos</option>';
        if ($estatus["success"]) {
            foreach ($estatus["datos"] as $key => $value) {
                $optEstatus .= '<option value="' . $value['ESTATUS'] . '">' . $value['ESTATUS'] . '</option>';
            }
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporte encuesta Postventa")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set("fecha", date("Y-m-d"));
        View::set("estatus", $optEstatus);
        View::render("callcenter_reporteEncuestaPostventa", $extraFooter);
    }

    public function HTMLReporteEncuestaPostventa()
    {
        $datos = CallCenterDao::GetReporteEncuestaPostventa($_POST);

        if (!$datos["success"] || count($datos["datos"]) === 0) {
            $r = ["success" => false, "mensaje" => "No se encontraron datos para los criterios seleccionados"];
            echo json_encode($r);
            return;
        }


        $filas = "";
        foreach ($datos["datos"] as $dato) {
            $filas .= "<tr>";
            foreach ($dato as $key => $valor) {
                $filas .= "<td>{$valor}</td>";
            }
            $filas .= "</tr>";
        }

        $r = ["success" => true, "datos" => $filas];
        echo json_encode($r);
    }

    public function ExcelReporteEncuestaPostventa()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $centrado = ['estilo' => $estilos['centrado']];

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('CLIENTE', 'Cliente', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'Ciclo', $centrado),
            \PHPSpreadsheet::ColumnaExcel('TELEFONO', 'Teléfono', $centrado),
            \PHPSpreadsheet::ColumnaExcel('FECHA', 'Fecha', ['estilo' => $estilos['fecha_hora']]),
            \PHPSpreadsheet::ColumnaExcel('ASESOR', 'Asesor', $centrado),
            \PHPSpreadsheet::ColumnaExcel('ESTATUS', 'Estatus', $centrado),
            \PHPSpreadsheet::ColumnaExcel('MOTIVO_ABANDONO', 'Motivo', $centrado),
            \PHPSpreadsheet::ColumnaExcel('COMENTARIO_ASESOR', 'Comentario del asesor')
        ];

        $filas = CallCenterDao::GetReporteEncuestaPostventa($_GET);
        $filas = $filas['success'] ? $filas['datos'] : [];

        \PHPSpreadsheet::DescargaExcel('Reporte encuestas Postventa', 'Reporte', 'Encuestas Postventa', $columnas, $filas);
    }

    public function SupervisionEncuestaPostventa()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->conectaSocket}
                {$this->mensajes}

                const actividad = []
                const columnas = $("#tblActividad thead th").map((idx, th) => th.className).get()

                $(document).ready(() => {
                    $("#mensajeGrl").on("click", () => mensaje())
                })

                const socket = conectaSocket("{$this->configuracion['API_SOCKETGRAL']}", "superCallcenter")

                socket.on("connect_error", (error) => {
                    console.log("Error al conectar con el socket:", error)
                    showWait("Hay problemas con la conexión al servidor, reintentando...")
                })

                socket.on("supervisando", (datos) => {
                    datos.forEach((sesion) => {
                        actividad.push(sesion)
                        crearFila(sesion)
                    })
                    actualizaActividad()
                    swal.close()
                })

                socket.on("actInfoSesiones", (datos) => {
                    const idx = actividad.findIndex((fila) => fila.asesor === datos.asesor)
                    if (idx >= 0) actividad[idx] = datos
                    actualizaTabla(datos)
                    actualizaActividad()
                })

                socket.on("asesorIN", (datos) => {
                    actividad.push(datos)
                    crearFila(datos)
                    actualizaActividad()
                })

                socket.on("asesorOUT", (datos) => {
                    const idx = actividad.findIndex((fila) => fila.asesor === datos.asesor)
                    if (idx >= 0) {
                        actividad.splice(idx, 1)
                        $("#" + datos.asesor).remove()
                    }
                    actualizaActividad()
                })
                
                const actualizaActividad = () => {
                    $("#noAsesores").text(actividad.length)
                    let noAsignados = 0, noCompletados = 0, noAbandonados = 0
                    actividad.forEach((fila) => {
                        noAsignados += fila.conteos.asignados
                        noCompletados += fila.conteos.completados
                        noAbandonados += fila.conteos.abandonados
                    })

                    $("#noClientes").text(noAsignados)
                    $("#noCompletados").text(noCompletados)
                    $("#noAbandonados").text(noAbandonados)
                    ultimaActualizacion()
                }

                const mensaje = (datos = {}) => {
                    const para = datos.asesor ? datos.asesor : "los asesores"
                    const emisor = datos.asesor ? "mensajeAsesor" : "mensajeGlobal"
                    swal("Mensaje para " + para, {
                        content: {
                            element:"input",
                            attributes: {
                                placeholder: "Escriba aquí el mensaje",
                                type: "text"
                            }
                        },
                        buttons: {
                            cancel: {
                                text: "Cancelar",
                                visible: true
                            },
                            confirm: {
                                text: "Enviar"
                            },
                        }
                    }).then((mensaje) => {
                        if (!mensaje) return
                        socket.emit(emisor, { mensaje, asesor: datos.asesor })
                    })
                }

                const actualizaTabla = (datos) => {
                   const fila = $("#" + datos.asesor)
                   const estatus = $(".estatus", fila).text()
                    columnas.forEach((columna) => {
                        const celda = $("." + columna, fila)[0]
                        switch (columna) {
                            case "tiempo":
                                celda.textContent = estatus !== datos.estatus ? "00:00:00" : celda.textContent
                                break;
                            case "asignados":
                            case "completados":
                            case "abandonados":
                                celda.textContent = datos.conteos[columna]
                                break;
                            case "acciones":
                                break;
                            default:
                                celda.textContent = datos[columna]
                                break;
                        }
                    })
                }

                const ultimaActualizacion = () => $("#ultimaActualizacion").text(new Date().toLocaleString("es-MX"))

                const crearFila = (datos) => {
                    const fila = document.createElement("tr")
                    fila.setAttribute("id", datos.asesor)
                    columnas.forEach((columna) => {
                        const celda = document.createElement("td")
                        celda.setAttribute("style", "vertical-align: middle !important; text-align: center;")
                        celda.setAttribute("class", columna)
                        switch (columna) {
                            case "tiempo":
                                celda.textContent = calculaTiempo(datos.ultimoCambio)
                                break;
                            case "asignados":
                            case "completados":
                            case "abandonados":
                                celda.textContent = datos.conteos[columna]
                                break;
                            case "acciones":
                                const boton = document.createElement("button")
                                boton.setAttribute("class", "btn btn-info")
                                boton.textContent = "Enviar Mensaje"
                                boton.addEventListener("click", () => mensaje(datos))
                                celda.appendChild(boton)
                                break;
                            default:
                                celda.textContent = datos[columna]
                                break;
                        }
                        fila.appendChild(celda)
                    })
                    $("#bdTblActividad").append(fila)
                }

                const actualizaTiempoAsignacion = () => {
                    $("#bdTblActividad").find("tr").each((idx, fila) => {
                        if ($(".cliente", fila).text() === "") return
                        let [h, m, s] = $(fila).find(".tiempo").text().split(":")
                        s = parseInt(s) + 1
                        if (s === 60) {
                            s = 0
                            m = parseInt(m) + 1
                            if (m === 60) {
                                m = 0
                                h = parseInt(h) + 1
                            }
                        }
                        indicadorTiempos(parseInt(h) * 60 + parseInt(m), fila)
                        $(fila).find(".tiempo").text(h.toString().padStart(2, "0") + ":" + m.toString().padStart(2, "0") + ":" + s.toString().padStart(2, "0"))
                    })
                }

                const indicadorTiempos = (minutos, fila) => {
                    const tiempos = {
                        Asignado: 5,
                        "En llamada": 15,
                    }

                    const estatus = $(".estatus", fila).text()
                    const porcentaje = (minutos / tiempos[estatus]) * 100
                    $(".tiempo", fila).css("background", calcularColor(porcentaje))
                }

                const calcularColor = (porcentaje) => {
                    if (porcentaje > 100) porcentaje = 100
                    const r = porcentaje < 50 ? Math.floor((porcentaje / 50) * 255) : 255; // De verde a amarillo a rojo
                    const g = porcentaje > 50 ? Math.floor((1 - (porcentaje - 50) / 50) * 255) : 255; // De verde a amarillo
                    const b = 0;
                    return "rgb(" + r + ", " + g + ", " + b + ")"
                }

                const calculaTiempo = (fecha) => {
                    const ahora = new Date()
                    const inicio = new Date(fecha)
                    const diferencia = ahora.getTime() - inicio.getTime()
                    const segundos = Math.floor(diferencia / 1000)
                    const minutos = Math.floor(segundos / 60)
                    const horas = Math.floor(minutos / 60)
                    return horas.toString().padStart(2, "0") + ":" + (minutos % 60).toString().padStart(2, "0") + ":" + (segundos % 60).toString().padStart(2, "0")
                }

                setInterval(actualizaTiempoAsignacion, 1000);
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader('Supervisión encuesta Postventa', [$this->socket])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('callcenter_supervisionEncuestaPostventa', $extraFooter);
    }

    public function Plantilla_Retiro_Finalizado($datos)
    {
        if ($datos['ESTATUS'] === 'V') {
            $aprobada = true;
        } else {
            $aprobada = false;
            $tipo = $datos['TIPO_RETIRO'] === 'T' ? 'cancelación' : 'rechazo';
            $tipo_titulo = $datos['ESTATUS'] === 'C' ? 'CANCELADA' : 'RECHAZADA';
        }

        $pasosFinalesA = <<<HTML
            <p style="text-align: center">
                Para completar el proceso entregue la documentación correspondiente, si tiene alguna duda o inconveniente, comuníquese con {$datos['NOMBRE_CALLCENTER']} ({$datos['CALLCENTER']}) o con el gerente de call center.
            </p>
            <p style="text-align: center">
                <b>Asegúrese de seguir todos los protocolos establecidos para la correcta gestión y archivo de los documentos.</b>
            </p>
        HTML;

        $pasosFinalesR = <<<HTML
            <p style="text-align: center">
                Si tiene alguna duda o inconveniente referente al $tipo de la solicitud, comuníquese con {$datos['NOMBRE_CALLCENTER']} ({$datos['CALLCENTER']}) o con el gerente de call center.
            </p>
        HTML;

        $titulo = $aprobada ? '✅ Solicitud de retiro VALIDADA.' : "❌ Solicitud de retiro $tipo_titulo.";
        $pasosFinales = $aprobada ? $pasosFinalesA : $pasosFinalesR;

        return <<<HTML
            <!-- Encabezado -->
            <h2 style="text-align: center">$titulo</h2>
            <!-- Información General -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    📄 Información General
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <li>🔸<b>ID:</b> {$datos['ID']}</li>
                    <li>🔸<b>Cliente:</b> {$datos['CLIENTE']} - {$datos['NOMBRE_CLIENTE']}</li>
                    <li>🔸<b>Crédito:</b> {$datos['CREDITO']}</li>
                    <li>🔸<b>Fecha de captura:</b> {$datos['FECHA_CREACION']}</li>
                    <li>🔸<b>Región:</b> {$datos['REGION']} - {$datos['NOMBRE_REGION']}</li>
                    <li>🔸<b>Agencia:</b> {$datos['SUCURSAL']} - {$datos['NOMBRE_SUCURSAL']}</li>
                    <li>🔸<b>Estatus final:</b> {$datos['ESTATUS_ETIQUETA']}</li>
                </ul>
            </div>

            <!-- Detalle de llamadas realizadas -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    ☎️ Detalle de llamadas realizadas
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <li>🔸<b>Total de llamadas:</b> {$datos['TOTAL_LLAMADAS']}</li>
                    <li>🔸<b>Intentos realizados:</b> {$datos['INTENTOS']}</li>
                    <li>🔸<b>Fecha primera llamada:</b> {$datos['PRIMERA_LLAMADA']}</li>
                    <li>🔸<b>Fecha última llamada:</b> {$datos['ULTIMA_LLAMADA']}</li>
                </ul>
            </div>

            <!-- Comentarios del Call Center -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    📝 Comentarios del Call Center
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <li>🔸<b>Comentario final:</b> {$datos['COMENTARIO_FINAL']}</li>
                </ul>
            </div>

            <!-- Próximos pasos -->
            <div style="padding-top: 14px">
                $pasosFinales
            </div>
        HTML;
    }
}
