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

                const getBotonCanelar = (credito, ciclo, secuencia) => {
                    const btnCancelar = document.createElement("button")

                    btnCancelar.classList.add("btn", "btn-danger")
                    btnCancelar.textContent = "Cancelar"
                    btnCancelar.addEventListener("click", () => {
                        cancelarRefinanciamiento(credito, ciclo, secuencia)
                    })

                    return btnCancelar
                }

                const inputError = (mensaje) => {
                    $("#noCredito").toggleClass("incorrecto", true)
                    resultadoError(mensaje)
                }

                const resultadoError = (mensaje) => {
                    $(".resultado").toggleClass("conDatos", false)
                    
                    showError(mensaje).then(() => {
                        $("#refinanciamientos").DataTable().destroy()
                        $("#refinanciamientos tbody").empty()
                        configuraTabla("refinanciamientos")
                    })
                }

                const resultadoOK = (datos) => {
                    const situaciones = {
                        "L": "Liquidado",
                        "E": "Entregado"
                    }

                    $("#refinanciamientos").DataTable().destroy()
                    $("#refinanciamientos tbody").empty()

                    datos.forEach((dato) => {
                        const { CLIENTE, CREDITO, CICLO, SITUACION, SALDO_TOTAL, FECHA, NOMBRE, EJECUTIVO, MONTO, OPERACION, REGISTRO, SECUENCIA, ESTATUS, ULTIMO_CICLO } = dato

                        const tr = document.createElement("tr")
                        tr.appendChild(crearTD(CLIENTE))
                        tr.appendChild(crearTD(NOMBRE))
                        tr.appendChild(crearTD(CREDITO))
                        tr.appendChild(crearTD(CICLO))
                        tr.appendChild(crearTD(situaciones[SITUACION]))
                        tr.appendChild(crearTD("$ " + formatoMoneda(SALDO_TOTAL)))
                        tr.appendChild(crearTD(OPERACION))
                        tr.appendChild(crearTD(FECHA))
                        tr.appendChild(crearTD("$ " + formatoMoneda(MONTO)))
                        tr.appendChild(crearTD(EJECUTIVO))
                        tr.appendChild(crearTD(REGISTRO))
                        const btn = (ESTATUS === "A" && SITUACION === "L" && CICLO === ULTIMO_CICLO && SALDO_TOTAL > 0) ? getBotonCanelar(CREDITO, CICLO, SECUENCIA) : document.createTextNode("")
                        tr.appendChild(crearTD(btn))
                        
                        $("#refinanciamientos tbody").append(tr)
                    })

                    configuraTabla("refinanciamientos")

                    $(".resultado").toggleClass("conDatos", true)
                }

                const buscarRefinanciemientos = () => {
                    $("#noCredito").toggleClass("incorrecto", false)
                    const credito = $("#noCredito").val()

                    if (credito === "") return inputError("Debe ingresar un número de crédito.")
                    if (credito.length !== 6) return inputError("El número de crédito debe tener 6 dígitos.")

                    consultaServidor("/cancelaRef/GetRefinanciamientos", { credito }, (resultado) => {
                        if (!resultado.success) return resultadoError(resultado.mensaje)
                        
                        const { datos } = resultado
                        if (datos.length === 0) return resultadoError("No se encontraron refinanciamientos para el crédito " + credito + ".")

                        resultadoOK(datos)
                    })
                }

                const cancelarRefinanciamiento = (credito, ciclo, secuencia) => {
                    const advertencia = document.createElement("p")
                    advertencia.innerHTML = "Se ajustara el devengo del periodo y se reactivara el crédito.<br><b>¿Está seguro de continuar con la cancelación del refinanciamiento del crédito " + credito + " para el ciclo " + ciclo + "?</b>"

                    confirmarMovimiento("Cancelación de refinanciamiento", null, advertencia)
                    .then((continuar) => {
                        if (!continuar) return

                        consultaServidor("/cancelaRef/CancelaRefinanciamiento", { credito, ciclo, secuencia }, (resultado) => {
                            if (!resultado.success) return showError(resultado.mensaje)

                            showSuccess(resultado.mensaje).then(() => {
                                $("#buscarRef").click()
                            })
                        })
                    })
                }

                $(document).ready(() => {
                    configuraTabla("refinanciamientos")

                    $("#noCredito").on("keypress", (e) => {
                        if (e.key < "0" || e.key > "9") e.preventDefault()
                        if (e.key === "Enter") buscarRefinanciemientos()
                    })
                    $("#buscarRef").on("click", buscarRefinanciemientos)
                })
            </script>
        HTML;


        View::set('header', $this->_contenedor->header($this->GetExtraHeader('Cancelación de Refinanciamientos')));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("CancelaRef/cancela_refinanciamientos");
    }

    public function GetRefinanciamientos()
    {
        echo json_encode(CancelaRefDao::GetRefinanciamientos($_POST));
    }

    public function CancelaRefinanciamiento()
    {
        echo json_encode(CancelaRefDao::CancelaRefinanciamiento($_POST));
    }
}
