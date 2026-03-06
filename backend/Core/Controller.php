<?php

namespace Core;

defined("APPPATH") or die("Access denied");

class Controller
{
    public $socket = '<script src="/libs/socket.io.min.js"></script>';
    public $swal2 = '<script src="/libs/sweetalert2/sweetalert2.all.min.js"></script><link href="/libs/sweetalert2/sweetalert2-tema-bootstrap-4.css" rel="stylesheet" />';
    public $mensajes = <<<JAVASCRIPT
        const tipoMensaje = (mensaje, icono, config = null) => {
            let configMensaje = (typeof mensaje === "object") ? { content: mensaje } : { text: mensaje }
            configMensaje.icon = icono
            if (config) Object.assign(configMensaje, config)
            return swal(configMensaje)
        }

        const showError = (mensaje) =>  tipoMensaje(mensaje, "error")
        const showSuccess = (mensaje) => tipoMensaje(mensaje, "success")
        const showInfo = (mensaje) => tipoMensaje(mensaje, "info")
        const showWarning = (mensaje) => tipoMensaje(mensaje, "warning")
        const showWait = (mensaje) => {
            const config = {
                button: false,
                closeOnClickOutside: false,
                closeOnEsc: false
            }
            return tipoMensaje(mensaje, "/img/wait.gif", config)
        }
    JAVASCRIPT;
    public $confirmarMovimiento = <<<JAVASCRIPT
        const confirmarMovimiento = async (titulo, mensaje, html = null) => {
            return await swal({ title: titulo, content: html, text: mensaje, icon: "warning", buttons: ["No", "Si, continuar"], dangerMode: true })
        }
    JAVASCRIPT;
    public $conectaSocket = <<<JAVASCRIPT
        const conectaSocket = (url, modulo, datos = {}) => {
            showWait("Conectando con el servidor...")
            return io(url, {
                query: {
                    servidor: window.location.origin,
                    sesionPHP: "sessionID",
                    modulo: modulo,
                    configuracion: JSON.stringify(datos)
                }
            })
        }
    JAVASCRIPT;
    public $consultaServidor = <<<JAVASCRIPT
        const consultaServidor = (url, datos, fncOK, metodo = "POST", tipo = "JSON", tipoContenido = null, procesar = null) => {
            swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
            const configuracion = {
                type: metodo,
                url: url,
                data: datos,
                dataType: tipo === "JSON" ? "json" : (tipo === "blob" ? "blob" : undefined),
                success: (res) => {
                    if (tipo === "JSON" && typeof res === "string") {
                        try { res = JSON.parse(res) } catch (e) { res = { success: false, mensaje: "La respuesta del servidor no es JSON válido." } }
                    }
                    if (tipo === "blob" && !(res instanceof Blob)) res = new Blob([res], { type: "application/pdf" })

                    swal.close()
                    fncOK(res)
                },
                error: (xhr, textStatus, errorThrown) => {
                    console.error("consultaServidor error:", textStatus, errorThrown, xhr.responseText)
                    const msg = textStatus === "parsererror" ? "La respuesta del servidor no es JSON válido. Revise que no haya errores en el servidor." : "Ocurrió un error al procesar la solicitud."
                    showError(msg)
                }
            }

            if (tipoContenido != null) configuracion.contentType = tipoContenido 
            if (procesar != null) configuracion.processData = procesar

            $.ajax(configuracion)
        }
    JAVASCRIPT;
    public $parseaNumero = 'const parseaNumero = (numero) => parseFloat(numero.replace(/[^0-9.-]/g, "")) || 0';
    public $formatoMoneda = 'const formatoMoneda = (numero) => parseFloat(numero).toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 })';
    public $configuraTabla = <<<JAVASCRIPT
        const configuraTabla = (id, {noRegXvista = true} = {}) => {
            const configuracion = {
                lengthMenu: [
                    [10, 40, -1],
                    [10, 40, "Todos"]
                ],
                order: [],
                autoWidth: false,
                language: {
                    emptyTable: "No hay datos disponibles",
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente",
                    },
                    info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Sin registros para mostrar",
                    zeroRecords: "No se encontraron registros",
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    search: "Buscar:",
                },
                createdRow: (row) => {
                    $(row).find('td').css('vertical-align', 'middle');
                }
            }

            configuracion.lengthChange = noRegXvista

            $("#" + id).DataTable(configuracion)
        }

        const actualizaDatosTabla = (id, datos) => {
            const tabla = $("#" + id).DataTable()
            tabla.clear()
            if (Array.isArray(datos)) {
                datos.forEach((item) => {
                    if (Array.isArray(item)) tabla.row.add(item).draw(false)
                    else tabla.row.add(Object.values(item)).draw(false)
                })
            }
            tabla.draw()
        }

        const buscarEnTabla = (id, columna, texto) => {
            const tabla = $("#" + id).DataTable()
            return tabla.rows().data().toArray().filter((dato) => dato[columna] == texto)
        }
    JAVASCRIPT;
    public $crearFilas = <<<JAVASCRIPT
        const creaFilas = (datos) => {
            const filas = document.createDocumentFragment()
            datos.forEach((dato) => {
                const fila = document.createElement("tr")
                Object.keys(dato).forEach((key) => {
                    const celda = document.createElement("td")
                    celda.style.verticalAlign = "middle"
                    celda.innerText = dato[key]
                    fila.appendChild(celda)
                })
                filas.appendChild(fila)
            })
            return filas
        }
    JAVASCRIPT;
    public $validaFIF = <<<JAVASCRIPT
        const validaFIF = (idI, idF) => {
            const fechaI = document.getElementById(idI).value
            const fechaF = document.getElementById(idF).value
            if (fechaI && fechaF && fechaI > fechaF) {
                document.getElementById(idI).value = fechaF
            }
        }
    JAVASCRIPT;
    public $descargaExcel = <<<JAVASCRIPT
        const descargaExcel = (url) => {
            swal({ text: "Generando archivo, espere un momento...", icon: "/img/wait.gif", closeOnClickOutside: false, closeOnEsc: false })
            const ventana = window.open(url, "_blank")
            const intervalo = setInterval(() => {
                if (ventana.closed) {
                    clearInterval(intervalo)
                    swal.close()
                }
            }, 1000)

            window.focus()
        }
    JAVASCRIPT;
    public $soloNumeros = <<<JAVASCRIPT
        const soloNumeros = (e) => {
            valKD = false
            if (
                !(e.key >= "0" && e.key <= "9") &&
                e.key !== "." &&
                e.key !== "Backspace" &&
                e.key !== "Delete" &&
                e.key !== "ArrowLeft" &&
                e.key !== "ArrowRight" &&
                e.key !== "ArrowUp" &&
                e.key !== "ArrowDown" &&
                e.key !== "Tab"
            ) e.preventDefault()
            if (e.key === "." && e.target.value.includes(".")) e.preventDefault()
            valKD = true
        }
    JAVASCRIPT;

    public $__usuario = '';
    public $__nombre = '';
    public $__puesto = '';
    public $__cdgco = '';
    public $__cdgco_ahorro = '';
    public $__perfil = '';
    public $__ahorro = '';
    public $__hora_inicio_ahorro = '';
    public $__hora_fin_ahorro = '';

    public function __construct()
    {
        session_start();
        $this->conectaSocket = str_replace('sessionID', session_id(), $this->conectaSocket);
        if ($_SESSION['usuario'] == '' || empty($_SESSION['usuario'])) {
            unset($_SESSION);
            session_unset();
            session_destroy();
            header("Location: /Login/");
            exit();
        } else {
            $this->__usuario = $_SESSION['usuario'];
            $this->__nombre = $_SESSION['nombre'];
            $this->__puesto = $_SESSION['puesto'];
            $this->__cdgco = $_SESSION['cdgco'];
            $this->__perfil = $_SESSION['perfil'];
            $this->__ahorro = $_SESSION['ahorro'];
            $this->__cdgco_ahorro = $_SESSION['cdgco_ahorro'];
            $this->__hora_inicio_ahorro = $_SESSION['inicio'];
            $this->__hora_fin_ahorro = $_SESSION['fin'];
        }
    }

    public function GetExtraHeader($titulo, $elementos = [])
    {
        $html = <<<HTML
        <title>$titulo</title>
        HTML;

        if (!empty($elementos)) {
            foreach ($elementos as $elemento) {
                $html .= "\n" . $elemento;
            }
        }

        return $html;
    }

    public function GetDestinatarios($respuestas, $destinatarios = [])
    {
        $respuestas = array_key_exists('success', $respuestas) ? [$respuestas] : $respuestas;

        foreach ($respuestas as $respuesta) {
            if ($respuesta['success'] && count($respuesta['datos']) > 0) {
                $destinatarios = array_merge($destinatarios, array_map(function ($d) {
                    return $d['CORREO'];
                }, $respuesta['datos']));
            }
        }

        sort($destinatarios);
        return array_unique($destinatarios);
    }
}
