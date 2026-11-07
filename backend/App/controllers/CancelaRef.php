<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\CancelaRef as CancelaRefDao;



class CancelaRef extends Controller
{

    private $_contenedor;


    function __construct()
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
                {$this->configuraTabla}
                {$this->consultaServidor}
                {$this->mensajes}
                {$this->confirmarMovimiento}
                {$this->formatoMoneda}

                const crearTD = (contenido) => {
                    const td = document.createElement("td")

                    if (typeof contenido === "object") td.appendChild(contenido)
                    else td.textContent = contenido

                    return td
                }

                const situaciones = {
                    "L": "Liquidado",
                    "E": "Entregado"
                }

                const crearSpan = (texto, clase) => {
                    const span = document.createElement("span")
                    if (clase) span.className = clase
                    span.textContent = texto
                    return span
                }

                const capitaliza = (texto) => texto.charAt(0).toUpperCase() + texto.slice(1).toLowerCase()

                const getBadgeOperacion = (tipo, operacion) => crearSpan(capitaliza(operacion), "cr-badge " + (tipo === "R" ? "badge-mov-alta" : "badge-mov-reasigna"))

                const getBadgeSituacion = (situacion) => crearSpan(situaciones[situacion] || situacion, "cr-badge " + (situacion === "L" ? "badge-vigente" : "badge-historico"))

                const getBotonReversar = (movimiento) => {
                    const btnReversar = document.createElement("button")

                    btnReversar.classList.add("btn", "btn-danger", "btn-xs")
                    btnReversar.innerHTML = "<i class='fa fa-undo'></i> Reversar"
                    btnReversar.addEventListener("click", () => {
                        reversarMovimiento(movimiento)
                    })

                    return btnReversar
                }

                const getMotivo = (motivo) => {
                    const texto = crearSpan(motivo, "cr-motivo-texto")
                    texto.insertAdjacentHTML("afterbegin", "<i class='fa fa-info-circle'></i>")
                    return texto
                }

                const limpiarResultado = () => {
                    $(".resultado").toggleClass("conDatos", false)
                    $("#estadoInicial").show()
                    $("#movimientos").DataTable().destroy()
                    $("#movimientos tbody").empty()
                    configuraTabla("movimientos")
                }

                const inputError = (mensaje) => {
                    $("#noCredito").toggleClass("incorrecto", true)
                    resultadoError(mensaje)
                }

                const resultadoError = (mensaje) => {
                    limpiarResultado()
                    showError(mensaje)
                }

                const resultadoOK = (datos) => {
                    $("#movimientos").DataTable().destroy()
                    $("#movimientos tbody").empty()

                    datos.forEach((dato) => {
                        const { CLIENTE, CREDITO, CICLO, SITUACION, SALDO_TOTAL, FECHA, NOMBRE, EJECUTIVO, MONTO, TIPO, OPERACION, REGISTRO, MOTIVO } = dato

                        const tr = document.createElement("tr")
                        tr.appendChild(crearTD(CLIENTE))
                        tr.appendChild(crearTD(NOMBRE))
                        tr.appendChild(crearTD(CREDITO))
                        tr.appendChild(crearTD(CICLO))
                        tr.appendChild(crearTD(getBadgeSituacion(SITUACION)))
                        tr.appendChild(crearTD("$ " + formatoMoneda(SALDO_TOTAL)))
                        tr.appendChild(crearTD(getBadgeOperacion(TIPO, OPERACION)))
                        tr.appendChild(crearTD(FECHA))
                        tr.appendChild(crearTD("$ " + formatoMoneda(MONTO)))
                        tr.appendChild(crearTD(EJECUTIVO))
                        tr.appendChild(crearTD(REGISTRO))
                        tr.appendChild(crearTD(MOTIVO ? getMotivo(MOTIVO) : getBotonReversar(dato)))

                        $("#movimientos tbody").append(tr)
                    })

                    configuraTabla("movimientos")
                    $("#estadoInicial").hide()
                    $(".resultado").toggleClass("conDatos", true)
                }

                const buscarMovimientos = (recarga = false) => {
                    $("#noCredito").toggleClass("incorrecto", false)
                    const credito = $("#noCredito").val()

                    if (credito === "") return inputError("Debe ingresar un número de crédito.")
                    if (credito.length !== 6) return inputError("El número de crédito debe tener 6 dígitos.")

                    consultaServidor("/cancelaRef/GetMovimientos", { credito }, (resultado) => {
                        if (!resultado.success) return resultadoError(resultado.mensaje)
                        
                        const { datos } = resultado
                        if (datos.length === 0 && recarga) return limpiarResultado()
                        if (datos.length === 0) return resultadoError("No se encontraron refinanciamientos ni descuentos activos para el crédito " + credito + ".")

                        resultadoOK(datos)
                    })
                }

                const getContenidoReverso = (movimiento, comentario) => {
                    const { CREDITO, CICLO, OPERACION, FECHA, MONTO, CAPITAL, INTERES } = movimiento
                    const contenido = document.createElement("div")
                    const total = parseFloat(CAPITAL) + parseFloat(INTERES)
                    const diferencia = parseFloat(MONTO) - total

                    contenido.innerHTML = "Se registrará el ajuste de reverso, se reactivará el ciclo y se cancelará el movimiento.<br><br>"
                        + "<b>Crédito:</b> " + CREDITO + " &nbsp; <b>Ciclo:</b> " + CICLO + "<br>"
                        + "<b>Operación:</b> " + OPERACION + " &nbsp; <b>Fecha:</b> " + FECHA + "<br>"
                        + "<b>Monto del movimiento:</b> $ " + formatoMoneda(MONTO) + "<br>"
                        + "<b>Capital a reversar:</b> $ " + formatoMoneda(CAPITAL) + " &nbsp; <b>Interés a reversar:</b> $ " + formatoMoneda(INTERES) + "<br>"
                        + "<b>Total a reversar:</b> $ " + formatoMoneda(total) + "<br>"
                        + (diferencia > 1 ? "<span class='text-danger'>El total a reversar es $ " + formatoMoneda(diferencia) + " menor al monto del movimiento.</span><br>" : "")
                        + "<br><b>¿Está seguro de continuar con el reverso?</b><br><br>"

                    const txtComentario = document.createElement("textarea")
                    txtComentario.classList.add("form-control")
                    txtComentario.rows = 3
                    txtComentario.maxLength = 1000
                    txtComentario.placeholder = "Comentario (obligatorio)"
                    txtComentario.value = comentario
                    contenido.appendChild(txtComentario)

                    return contenido
                }

                const reversarMovimiento = (movimiento, comentarioPrevio = "") => {
                    const { CREDITO, CICLO, SECUENCIA, TIPO, FECHA, OPERACION } = movimiento
                    const contenido = getContenidoReverso(movimiento, comentarioPrevio)

                    confirmarMovimiento("Reverso de " + OPERACION.toLowerCase(), null, contenido)
                    .then((continuar) => {
                        if (!continuar) return

                        const comentario = contenido.querySelector("textarea").value.trim()
                        if (comentario === "") return showError("El comentario es obligatorio.").then(() => reversarMovimiento(movimiento))
                        if (new TextEncoder().encode(comentario).length > 1000) return showError("El comentario es demasiado largo, el máximo es de 1000 caracteres (las letras con acento y la ñ cuentan como 2).").then(() => reversarMovimiento(movimiento, comentario))

                        consultaServidor("/cancelaRef/ReversaMovimiento", { credito: CREDITO, ciclo: CICLO, secuencia: SECUENCIA, tipo: TIPO, fecha: FECHA, comentario }, (resultado) => {
                            if (!resultado.success) return showError(resultado.mensaje).then(() => buscarMovimientos(true))

                            showSuccess(resultado.mensaje).then(() => buscarMovimientos(true))
                        })
                    })
                }

                $(document).ready(() => {
                    configuraTabla("movimientos")

                    $("#noCredito").on("keypress", (e) => {
                        if (e.key < "0" || e.key > "9") e.preventDefault()
                        if (e.key === "Enter") buscarMovimientos()
                    })
                    $("#buscarRef").on("click", () => buscarMovimientos())
                })
            </script>
        HTML;


        $extraHeader = $this->GetExtraHeader('Cancelación de Refinanciamientos y Descuentos', [
            '<link href="/css/folios-tarjeta.css" rel="stylesheet">',
            '<link href="/css/pagos-con-gl.css" rel="stylesheet">'
        ]);

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('diasReverso', CancelaRefDao::DIAS_REVERSO);
        View::render("cancela_refinanciamientos");
    }

    public function GetMovimientos()
    {
        echo json_encode(CancelaRefDao::GetMovimientos($_POST));
    }

    public function ReversaMovimiento()
    {
        $datos = [
            'credito' => $_POST['credito'] ?? '',
            'ciclo' => $_POST['ciclo'] ?? '',
            'secuencia' => $_POST['secuencia'] ?? '',
            'tipo' => $_POST['tipo'] ?? '',
            'fecha' => $_POST['fecha'] ?? '',
            'comentario' => $_POST['comentario'] ?? '',
            'usuario' => $this->__usuario
        ];
        echo json_encode(CancelaRefDao::ReversaMovimiento($datos));
    }
}
