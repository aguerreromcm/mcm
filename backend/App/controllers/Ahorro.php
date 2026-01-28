<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

include_once dirname(__DIR__) . '/../libs/PHPMailer/Mensajero.php';

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use Core\App;
use \App\models\CajaAhorro as CajaAhorroDao;
use \App\models\Ahorro as AhorroDao;
use \App\components\TarjetaDedo;
use DateTime;
use Mensajero;

class Ahorro extends Controller
{
    private $_contenedor;
    private $configuracion;
    private $operacionesNulas = [2, 5]; // [Comisión, Transferencia]
    private $XLSX = '<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js" integrity="sha512-r22gChDnGvBylk90+2e/ycr3RVrDi8DIOkIGNhJlKfuyQM4tIRAI062MaV8sfjQKYVGjOBaZBOA87z+IhZE9DA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
    private $huellas = '<script src="/js/huellas/es6-shim.js"></script><script src="/js/huellas/fingerprint.sdk.min.js"></script><script src="/js/huellas/huellas.js"></script><script src="/js/huellas/websdk.client.bundle.min.js"></script>';
    private $showBloqueo = 'const showBloqueo = (mensaje) => {
        Swal.fire({
            html: mensaje,
            icon: "warning",
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            target: document.getElementById("bloqueoAhorro"),
            customClass: {
                container: "sweet-bloqueoAhorro-container",
                popup: "sweet-bloqueoAhorro-popup",
            }
        })
    }';
    private $validarYbuscar = 'const validarYbuscar = (e, t) => {
        if (e.keyCode < 9 || e.keyCode > 57) e.preventDefault()
        if (e.keyCode === 13) buscaCliente(t)
    }';
    private $buscaCliente = <<<script
    const buscaCliente = (t) => {
        document.querySelector("#btnBskClnt").disabled = true
        const noCliente = document.querySelector("#clienteBuscado").value
         
        if (!noCliente) {
            limpiaDatosCliente()
            document.querySelector("#btnBskClnt").disabled = false
            return showError("Ingrese un número de cliente a buscar.")
        }
        
        consultaServidor("/Ahorro/BuscaContratoAhorro/", { cliente: noCliente }, (respuesta) => {
                limpiaDatosCliente()
                if (!respuesta.success) {
                    if (respuesta.datos && !sinContrato(respuesta.datos)) return
                     
                    limpiaDatosCliente()
                    return showError(respuesta.mensaje)
                }
                 
                if (respuesta.datos.SUCURSAL !== noSucursal) {
                    limpiaDatosCliente()
                    return showError("El cliente " + noCliente + " no puede realizar transacciones en esta sucursal, su contrato esta asignado a la sucursal " + respuesta.datos.NOMBRE_SUCURSAL + ", contacte a la gerencia de Administración.")
                }
                 
                llenaDatosCliente(respuesta.datos)
            })
        
        document.querySelector("#btnBskClnt").disabled = false
    }
    script;
    private $getHoy = 'const getHoy = (completo = true) => {
        const hoy = new Date()
        const dd = String(hoy.getDate()).padStart(2, "0")
        const mm = String(hoy.getMonth() + 1).padStart(2, "0")
        const yyyy = hoy.getFullYear()
        const r = dd + "/" + mm + "/" + yyyy
        return completo ? r  + " " + hoy.getHours().toString().padStart(2, "0") + ":" + hoy.getMinutes().toString().padStart(2, "0") + ":" + hoy.getSeconds().toString().padStart(2, "0") : r
    }';
    private $numeroLetras = 'const numeroLetras = (numero) => {
        if (!numero) return ""
        const unidades = ["", "un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"]
        const especiales = [
            "",
            "once",
            "doce",
            "trece",
            "catorce",
            "quince",
            "dieciséis",
            "diecisiete",
            "dieciocho",
            "diecinueve",
            "veinte",
            "veintiún",
            "veintidós",
            "veintitrés",
            "veinticuatro",
            "veinticinco",
            "veintiséis",
            "veintisiete",
            "veintiocho",
            "veintinueve"
        ]
        const decenas = [
            "",
            "diez",
            "veinte",
            "treinta",
            "cuarenta",
            "cincuenta",
            "sesenta",
            "setenta",
            "ochenta",
            "noventa"
        ]
        const centenas = [
            "cien",
            "ciento",
            "doscientos",
            "trescientos",
            "cuatrocientos",
            "quinientos",
            "seiscientos",
            "setecientos",
            "ochocientos",
            "novecientos"
        ]
    
        const convertirMenorA1000 = (numero) => {
            let letra = ""
            if (numero >= 100) {
                letra += centenas[(numero === 100 ? 0 : Math.floor(numero / 100))] + " "
                numero %= 100
            }
            if (numero === 10 || numero === 20 || (numero > 29 && numero < 100)) {
                letra += decenas[Math.floor(numero / 10)]
                numero %= 10
                letra += numero > 0 ? " y " : " "
            }
            if (numero != 20 && numero >= 11 && numero <= 29) {
                letra += especiales[numero % 10 + (numero > 20 ? 10 : 0)] + " "
                numero = 0
            }
            if (numero > 0) {
                letra += unidades[numero] + " "
            }
            return letra.trim()
        }
    
        const convertir = (numero) => {
            if (numero === 0) {
                return "cero"
            }
        
            let letra = ""
        
            if (numero >= 1000000) {
                letra += convertirMenorA1000(Math.floor(numero / 1000000)) + (numero === 1000000 ? " millón " : " millones ")
                numero %= 1000000
            }
        
            if (numero >= 1000) {
                letra += (numero === 1000 ? "" : convertirMenorA1000(Math.floor(numero / 1000))) + " mil "
                numero %= 1000
            }
        
            letra += convertirMenorA1000(numero)
            return letra.trim()
        }
    
        const parteEntera = Math.floor(numero)
        const parteDecimal = Math.round((numero - parteEntera) * 100).toString().padStart(2, "0")
        return primeraMayuscula(convertir(parteEntera)) + (numero == 1 ? " peso " : " pesos ") + parteDecimal + "/100 M.N."
    }';
    private $primeraMayuscula = 'const primeraMayuscula = (texto) => texto.charAt(0).toUpperCase() + texto.slice(1)';
    private $muestraPDF = <<<script
    const muestraPDF = (titulo, ruta) => {
        const host = window.location.origin

        let plantilla = '<!DOCTYPE html>'
            plantilla += '<html lang="es">'
            plantilla += '<head>'
            plantilla += '<meta charset="UTF-8">'
            plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            plantilla += '<link rel="shortcut icon" href="' + host + '/img/logo_ico.png">'
            plantilla += '<title>' + titulo + '</title>'
            plantilla += '</head>'
            plantilla += '<body style="margin: 0; padding: 0; background-color: #333333;">'
            plantilla += '<iframe src="' + ruta + '" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
            plantilla += '</body>'
            plantilla += '</html>'
        
            const blob = new Blob([plantilla], { type: 'text/html' })
            const url = URL.createObjectURL(blob)
            window.open(url, '_blank')
    }
    script;
    private $imprimeTicket = <<<script
    const imprimeTicket = async (ticket, sucursal = '', copia = true) => {
        const espera = swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
        const rutaImpresion = 'http://127.0.0.1:5005/api/impresora/ticket'
        const host = window.location.origin
        const titulo = 'Ticket: ' + ticket
        const ruta = host + '/Ahorro/Ticket/?'
        + 'ticket=' + ticket
        + '&sucursal=' + sucursal
        + (copia ? '&copiaCliente=true' : '')
         
        // muestraPDF(titulo, ruta)
        fetch(ruta, {
            method: 'GET'
        })
        .then(resp => resp.blob())
        .then(blob => {
            const datos = new FormData()
            datos.append('ticket', blob)
             
            fetch(rutaImpresion, {
                method: 'POST',
                body: datos
            })
            .then(resp => resp.json())
            .then(res => {
                if (!res.success) return showError(res.mensaje)
                showSuccess(res.mensaje)
            })
            .catch(error => {
                console.error(error)
                showError('El servicio de impresión no está disponible.')
            })
        })
        .catch(error => {
            console.error(error)
            showError('Ocurrió un error al generar el ticket.')
        })
    }
    script;
    private $valida_MCM_Complementos = 'const valida_MCM_Complementos = async () => {
        swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
        
        let resultado = false
        try {
            const res = await fetch("http://localhost:5005/api/impresora/verificar")
            if (res.ok) {
                swal.close()
                resultado = true
            } else {
                const r = await res.json()
                showError(r.estatus.impresora.mensaje.replaceAll("<br>", "\\n", "g"))
            }
        } catch (error) {
            showError("El servicio de impresión no está disponible.")
        }

        return resultado
    }';
    private $imprimeContrato = <<<script
    const imprimeContrato = (numero_contrato, producto = 1) => {
        if (!numero_contrato) return
        const host = window.location.origin
        const titulo = 'Contrato ' + numero_contrato
        const ruta = host
            + '/Ahorro/Contrato/?'
            + 'contrato=' + numero_contrato
            + '&producto=' + producto
         
        muestraPDF(titulo, ruta)
    }
    script;
    private $sinContrato = <<<script
    const sinContrato = (datosCliente) => {
        if (datosCliente["NO_CONTRATOS"] == 0) {
            swal({
                title: "Cuenta de ahorro corriente",
                text: "El cliente " + datosCliente['CDGCL'] + " no tiene una cuenta de ahorro.\\n¿Desea aperturar una cuenta de ahorro en este momento?",
                icon: "info",
                buttons: ["No", "Sí"],
                dangerMode: true
            }).then((abreCta) => {
                if (abreCta) {
                    window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + datosCliente['CDGCL']
                    return
                }
            })
            return false
        }
        const msj2 = (typeof mEdoCta !== 'undefined') ? "No podemos generar un estado de cuenta para el cliente  " + datosCliente['CDGCL'] + ", porque este no ha concluido con su proceso de apertura de la cuenta de ahorro corriente.\\n¿Desea completar el proceso en este momento?" 
        : "El cliente " + datosCliente['CDGCL'] + " no ha completado el proceso de apertura de la cuenta de ahorro.\\n¿Desea completar el proceso en este momento?"
        if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 0) {
            swal({
                title: "Cuenta de ahorro corriente",
                text: msj2,
                icon: "info",
                buttons: ["No", "Sí"],
                dangerMode: true
            }).then((abreCta) => {
                if (abreCta) {
                    window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + datosCliente['CDGCL']
                    return
                }
            })
            return false
        }
        return true
    }
    script;
    private $addParametro = 'const addParametro = (parametros, newParametro, newValor) => {
        parametros.push({ name: newParametro, value: newValor })
    }';
    private $limpiaMontos = 'const limpiaMontos = (datos, campos = []) => {
        datos.forEach(dato => {
            if (campos.includes(dato.name)) {
                dato.value = parseaNumero(dato.value)
            }
        })
    }';
    private $noSubmit = 'const noSUBMIT = (e) => e.preventDefault()';
    private $validaHorarioOperacion = 'const validaHorarioOperacion = (inicio, fin, sinMsj = false) => {
        if ("__PERFIL__" === "ADMIN" || "__USUARIO__" === "AMGM") return

        const horaActual = new Date()
        const horaInicio = new Date()
        const horaFin = new Date()
        const [hi, mi, si] = inicio.split(":")
        const [hf, mf, sf] = fin.split(":")
        
        horaInicio.setHours(hi, mi, si)
        horaFin.setHours(hf, mf, sf)
        if (sinMsj) return horaActual >= horaInicio && horaActual <= horaFin

        if (!(horaActual >= horaInicio && horaActual <= horaFin)) showBloqueo("No es posible realizar operaciones fuera del horario establecido (de " + inicio + " a " + fin + ").<br><br><b>Consulte con la gerencia de administración.</b>")
    }';
    private $showHuella = 'const showHuella = (autorizacion = false, datos =  null) => {
        Swal.fire({
            html: `HTML_HUELLA<span id="mensajeHuella" style="height: 50px;"></span>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            showCloseButton: true,
            target: document.getElementById("bloqueoAhorro"),
            customClass: {
                container: "sweet-bloqueoAhorro-container",
                popup: "sweet-bloqueo-mano-popup",
                htmlContainer: "sweet-bloqueo-mano-htmlContainer",
            }
        })

        const lector = new LectorHuellas({
            notificacion: (mensaje, error = false) => {
                const huella = document.querySelector("#mensajeHuella")
                huella.style.color = error ? "red": ""
                huella.innerText = mensaje
            }
        })
        mano = new Mano("manoIzquierda", lector, document.querySelector(".sweet-bloqueo-mano-htmlContainer"))
        mano.modoAutorizacion()
        mano.datosCliente = datos
        lector.estatus.lecturaOK = "Validando huella.."
        if (autorizacion) {
            document.querySelector(".sweet-bloqueo-mano-htmlContainer").addEventListener("validaHuella", autorizaOperacion)
            lector.estatus.lecturaI = "Autorización del cliente."
        } else {
            document.querySelector(".sweet-bloqueo-mano-htmlContainer").addEventListener("validaHuella", validaHuella)
            lector.estatus.lecturaI = "Identificación del cliente."
            lector.estatus.lecturaOK = "Validando huella..."
        }
    }';
    private $validaHuella = 'const validaHuella = (e) => {
        const datos = {
            muestra: e.detail.muestra
        }
    
        consultaServidor("/Ahorro/ValidaHuella/", datos, (respuesta) => {
            e.detail.colorImagen(respuesta.success ? "green" : "red")
			
            if (!respuesta.success) {
                e.detail.conteoErrores()
                e.detail.mensajeLector("Haz clic en la imagen para intentar nuevamente.")
                return showError(respuesta.mensaje)
            }

            if (respuesta.cliente) {
                document.querySelector("#clienteBuscado").value = respuesta.cliente
                buscaCliente()
            }
            Swal.close()
        })
    }';
    private $autorizaOperacion = 'const autorizaOperacion = (e) => {
        if (e.detail.erroresValidacion >= 5) {
            showError("Se ha alcanzado el límite de intentos, la operación no se puede completar.")
            .then(() => {
                Swal.close()
                limpiaDatosCliente()
                return
            })
            return
        }

        const datos = {
            muestra: e.detail.muestra
        }

        consultaServidor("/Ahorro/ValidaHuella/", datos, (respuesta) => {
            e.detail.colorImagen(respuesta.success ? "green" : "red")
            if (!respuesta.success) {
                e.detail.conteoErrores()
                e.detail.mensajeLector("Haz clic en la imagen para intentar nuevamente.")
                return showError(respuesta.mensaje)
            }

            Swal.close()
            if (respuesta.cliente && document.querySelector("#cliente").value !== respuesta.cliente) {
                showError("La huella corresponde a otro cliente, comuníquese con su administrador.").
                then(() => {
                    limpiaDatosCliente()
                })
            }
            showSuccess(respuesta.mensaje).then(() => enviaRegistroOperacion(mano.datosCliente))
        })
    }';

    function __construct()
    {
        parent::__construct();
        $this->configuracion = App::getConfig();
        $this->_contenedor = new Contenedor;
        $tarjetaDedo = new TarjetaDedo("derecha", 1);
        $this->showHuella = str_replace("HTML_HUELLA", $tarjetaDedo->mostrar(), $this->showHuella);
        $this->validaHorarioOperacion = str_replace("__PERFIL__", $_SESSION['perfil'], $this->validaHorarioOperacion);
        $this->validaHorarioOperacion = str_replace("__USUARIO__", $_SESSION['usuario'], $this->validaHorarioOperacion);
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    //********************AHORRO CORRIENTE********************//
    // Apertura de contratos para cuentas de ahorro corriente
    public function ContratoCuentaCorriente()
    {
        $saldosMM = CajaAhorroDao::GetSaldoMinimoApertura($_SESSION['cdgco_ahorro']);
        $saldoMinimoApertura = $saldosMM['MONTO_MINIMO'];
        $costoInscripcion = 200;
        $mensajeCaptura = "Capture las huellas del cliente haciendo clic sobre una imagen.";

        $extraFooter = <<<HTML
            <script>
                const saldoMinimoApertura = $saldoMinimoApertura
                const costoInscripcion = $costoInscripcion
                const montoMaximo = 1000000
                const txtGuardaContrato = "GUARDAR DATOS Y PROCEDER AL COBRO"
                const txtGuardaPago = "REGISTRAR DEPÓSITO DE APERTURA"
                let valKD = false
                let manoIzquierda
                let manoDerecha
            
                window.onload = () => {
                    const lector = new LectorHuellas(
                    {
                        notificacion: (mensaje, error = false) => {
                            const huella = document.querySelector("#mensajeHuella")
                            huella.style.color = error ? "red": ""
            
                            huella.innerText = mensaje
                        }
                    })
                        
                    manoIzquierda = new Mano("izquierda", lector, document.querySelector("#manoizquierda"))
                    manoDerecha = new Mano("derecha", lector, document.querySelector("#manoderecha"))
            
                    document.querySelector("#manoizquierda").addEventListener("muestraObtenida", huellasCompletas)
                    document.querySelector("#manoderecha").addEventListener("muestraObtenida", huellasCompletas)

                    document.querySelector("#manoderecha").addEventListener("validaHuella", validaHuella)
                    document.querySelector("#manoizquierda").addEventListener("validaHuella", validaHuella)

                    document.querySelector("#manoderecha").addEventListener("actualizaHuella", actualizaHuella)
                    document.querySelector("#manoizquierda").addEventListener("actualizaHuella", actualizaHuella)
                    
                    if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
                }
            
                {$this->mensajes}
                {$this->confirmarMovimiento}
                {$this->validarYbuscar}
                {$this->getHoy}
                {$this->soloNumeros}
                {$this->numeroLetras}
                {$this->primeraMayuscula}
                {$this->muestraPDF}
                {$this->imprimeTicket}
                {$this->imprimeContrato}
                {$this->addParametro}
                {$this->consultaServidor}
                {$this->parseaNumero}
                {$this->formatoMoneda}
                {$this->limpiaMontos}
                {$this->valida_MCM_Complementos}
                
                const buscaCliente = () => {
                    if (document.querySelector("#sucursal").value === "") {
                        showError("Usted no tiene una sucursal asignada.\\n\\nNo es posible continuar con la operación, consulte a su administrador.")
                        return
                    }
                    
                    const noCliente = document.querySelector("#clienteBuscado").value
                    limpiaDatosCliente()
                    
                    if (!noCliente) return showError("Ingrese un número de cliente a buscar.")
                    
                    consultaServidor("/Ahorro/BuscaCliente/", { cliente: noCliente }, async (respuesta) => {
                        document.querySelector("#lnkContrato").innerText = "Creación del contrato"
                        if (!respuesta.success) {
                            if (!respuesta.datos) {
                                limpiaDatosCliente()
                                return showError(respuesta.mensaje)
                            }
                            
                            const datosCliente = respuesta.datos
                            document.querySelector("#btnGeneraContrato").style.display = "none"
                            document.querySelector("#contratoOK").value = datosCliente.CONTRATO
                            document.querySelector("#fecha").value = datosCliente.FECHA_CONTRATO
                            document.querySelector("#lnkContrato").innerText = "Creación del contrato (" + datosCliente.FECHA_CONTRATO.split(" ")[0] + ")"
                            if (Array.from(document.querySelector("#ejecutivo_comision").options).some(option => option.value === datosCliente.EJECUTIVO_COMISIONA)) {
                                document.querySelector("#ejecutivo_comision").value = datosCliente.EJECUTIVO_COMISIONA
                            } else {
                                document.querySelector("#ejecutivo_comision").appendChild(new Option(datosCliente.NOMBRE_EJECUTIVO_COMISIONA, "tmp", true, true))
                            }
                            
                            if (datosCliente['NO_CONTRATOS'] >= 0 && datosCliente.CONTRATO_COMPLETO == 0) {
                                await showInfo("La apertura del contrato no ha concluido, realice el depósito de apertura.")
                                document.querySelector("#fecha_pago").value = getHoy()
                                document.querySelector("#contrato").value = datosCliente.CONTRATO
                                document.querySelector("#codigo_cl").value = datosCliente.CDGCL
                                document.querySelector("#nombre_cliente").value = datosCliente.NOMBRE
                                document.querySelector("#mdlCurp").value = datosCliente.CURP
                                $("#modal_agregar_pago").modal("show")
                                document.querySelector("#chkCreacionContrato").classList.add("green")
                                document.querySelector("#chkCreacionContrato").classList.add("fa-check")
                                document.querySelector("#chkCreacionContrato").classList.remove("red")
                                document.querySelector("#chkCreacionContrato").classList.remove("fa-times")
                                document.querySelector("#lnkContrato").style.cursor = "pointer"
                                document.querySelector("#chkPagoApertura").classList.remove("green")
                                document.querySelector("#chkPagoApertura").classList.remove("fa-check")
                                document.querySelector("#chkPagoApertura").classList.add("fa-times")
                                document.querySelector("#chkPagoApertura").classList.add("red")
                                document.querySelector("#btnGuardar").innerText = txtGuardaPago
                                document.querySelector("#btnGeneraContrato").style.display = "block"
                            }
                            
                            if (datosCliente['NO_CONTRATOS'] >= 0 && datosCliente.CONTRATO_COMPLETO == 1) {
                                await showInfo("El cliente " + datosCliente.CDGCL + " ya cuenta con un contrato de ahorro corriente aperturada el " + datosCliente.FECHA_CONTRATO + ".")
                                document.querySelector("#chkCreacionContrato").classList.remove("red")
                                document.querySelector("#chkCreacionContrato").classList.remove("fa-times")
                                document.querySelector("#chkCreacionContrato").classList.add("green")
                                document.querySelector("#chkCreacionContrato").classList.add("fa-check")
                                document.querySelector("#lnkContrato").style.cursor = "pointer"
                                document.querySelector("#chkPagoApertura").classList.remove("red")
                                document.querySelector("#chkPagoApertura").classList.remove("fa-times")
                                document.querySelector("#chkPagoApertura").classList.add("green")
                                document.querySelector("#chkPagoApertura").classList.add("fa-check")
                            }
                            
                            consultaServidor("/Ahorro/GetBeneficiarios/", { contrato: datosCliente.CONTRATO }, (respuesta) => {
                                if (!respuesta.success) return showError(respuesta.mensaje)
                                
                                const beneficiarios = respuesta.datos
                                for (let i = 0; i < beneficiarios.length; i++) {
                                    document.querySelector("#beneficiario_" + (i + 1)).value = beneficiarios[i].NOMBRE
                                    document.querySelector("#parentesco_" + (i + 1)).value = beneficiarios[i].CDGCT_PARENTESCO
                                    document.querySelector("#porcentaje_" + (i + 1)).value = beneficiarios[i].PORCENTAJE
                                    document.querySelector("#btnBen" + (i + 1)).disabled = true
                                    document.querySelector("#parentesco_" + (i + 1)).disabled = true
                                    document.querySelector("#porcentaje_" + (i + 1)).disabled = true
                                    document.querySelector("#ben" + (i + 1)).style.opacity = "1"
                                }
                            })

                            consultaServidor("/Ahorro/ValidaRegistroHuellas", { cliente: datosCliente.CDGCL }, (respuesta) => {
                                if (!respuesta.success) {
                                    console.error(respuesta.error)
                                    return showError(respuesta.mensaje)
                                }
                                
                                if (respuesta.datos.HUELLAS == 1) {
                                    document.querySelector("#chkRegistroHuellas").classList.remove("red")
                                    document.querySelector("#chkRegistroHuellas").classList.remove("fa-times")
                                    document.querySelector("#chkRegistroHuellas").classList.add("green")
                                    document.querySelector("#chkRegistroHuellas").classList.add("fa-check")
                                    document.querySelector("#lnkHuellas").style.cursor = "default"
                                }
                                
                                if (respuesta.datos.HUELLAS == 0) {
                                    document.querySelector("#chkRegistroHuellas").classList.remove("green")
                                    document.querySelector("#chkRegistroHuellas").classList.remove("fa-check")
                                    document.querySelector("#chkRegistroHuellas").classList.add("red")
                                    document.querySelector("#chkRegistroHuellas").classList.add("fa-times")
                                    document.querySelector("#lnkHuellas").style.cursor = "pointer"
                                }
                            })
                        }
                        
                        const datosCL = respuesta.datos
                        
                        document.querySelector("#fechaRegistro").value = datosCL.FECHA_REGISTRO
                        document.querySelector("#noCliente").value = noCliente
                        document.querySelector("#nombre").value = datosCL.NOMBRE
                        document.querySelector("#curp").value = datosCL.CURP
                        document.querySelector("#edad").value = datosCL.EDAD
                        document.querySelector("#direccion").value = datosCL.DIRECCION
                        document.querySelector("#marcadores").style.opacity = "1"
                        document.querySelector("#codigo_cl_huellas").value = noCliente
                        document.querySelector("#nombre_cliente_huellas").value = datosCL.NOMBRE
                        noCliente.value = ""
                        manoIzquierda.limpiarMano()
                        manoDerecha.limpiarMano()
                        if (respuesta.success) habilitaBeneficiario(1, true)
                    })
                }
                
                const habilitaBeneficiario = (numBeneficiario, habilitar) => {
                    document.querySelector("#beneficiario_" + numBeneficiario).disabled = !habilitar
                    document.querySelector("#tasa").disabled = false
                    document.querySelector("#sucursal").disabled = false
                }
                
                const limpiaDatosCliente = () => {
                    manoIzquierda.modoCaptura()
                    manoDerecha.modoCaptura()
                    document.querySelector("#AddPagoApertura").reset()
                    document.querySelector("#registroInicialAhorro").reset()
                    document.querySelector("#chkCreacionContrato").classList.remove("green")
                    document.querySelector("#chkCreacionContrato").classList.remove("fa-check")
                    document.querySelector("#chkCreacionContrato").classList.add("red")
                    document.querySelector("#chkCreacionContrato").classList.add("fa-times")
                    document.querySelector("#lnkContrato").style.cursor = "default"
                    document.querySelector("#chkPagoApertura").classList.remove("green")
                    document.querySelector("#chkPagoApertura").classList.remove("fa-check")
                    document.querySelector("#chkPagoApertura").classList.add("red")
                    document.querySelector("#chkPagoApertura").classList.add("fa-times")
                    document.querySelector("#chkRegistroHuellas").classList.remove("green")
                    document.querySelector("#chkRegistroHuellas").classList.remove("fa-check")
                    document.querySelector("#chkRegistroHuellas").classList.add("red")
                    document.querySelector("#chkRegistroHuellas").classList.add("fa-times")
                    document.querySelector("#lnkHuellas").style.cursor = "pointer"
                    document.querySelector("#fechaRegistro").value = ""
                    document.querySelector("#noCliente").value = ""
                    document.querySelector("#nombre").value = ""
                    document.querySelector("#curp").value = ""
                    document.querySelector("#edad").value = ""
                    document.querySelector("#direccion").value = ""
                    habilitaBeneficiario(1, false)
                    document.querySelector("#ben2").style.opacity = "0"
                    document.querySelector("#ben3").style.opacity = "0"
                    document.querySelector("#btnGeneraContrato").style.display = "none"
                    document.querySelector("#btnGuardar").innerText = txtGuardaContrato
                    document.querySelector("#marcadores").style.opacity = "0"
                    document.querySelector("#tasa").disabled = true
                    document.querySelector("#sucursal").disabled = true
                    document.querySelector("#contratoOK").value = ""
                    document.querySelector("#ejecutivo_comision").childNodes.forEach((option) => {
                        if (option.value === "tmp") option.remove()
                    })
                }
                
                const generaContrato = async (e) => {
                    e.preventDefault()
                    const btnGuardar = document.querySelector("#btnGuardar")
                    if (btnGuardar.innerText === txtGuardaPago) return $("#modal_agregar_pago").modal("show")
                    
                    document.querySelector("#fecha_pago").value = getHoy()
                    document.querySelector("#contrato").value = ""
                    document.querySelector("#codigo_cl").value = document.querySelector("#noCliente").value
                    document.querySelector("#nombre_cliente").value = document.querySelector("#nombre").value
                    document.querySelector("#mdlCurp").value = document.querySelector("#curp").value
                        
                    await showInfo("Debe registrar el depósito por apertura de cuenta.")
                    btnGuardar.innerText = txtGuardaPago
                    $("#modal_agregar_pago").modal("show")
                }
                            
                const pagoApertura = async (e) => {
                    if (!await valida_MCM_Complementos()) return
                    
                    e.preventDefault()
                    if (parseaNumero(document.querySelector("#deposito").value) < saldoMinimoApertura) return showError("El saldo inicial no puede ser menor a " + saldoMinimoApertura.toLocaleString("es-MX", {style:"currency", currency:"MXN"}) + ".")
                    
                    confirmarMovimiento(
                        "Cuenta de ahorro corriente",
                        "¿Está segura de continuar con la apertura de la cuenta de ahorro del cliente: " +
                            document.querySelector("#nombre").value +
                            "?"
                    ).then((continuar) => {
                        if (!continuar) return
                    
                        const noCredito = document.querySelector("#noCliente").value
                        const datosContrato = $("#registroInicialAhorro").serializeArray()
                        addParametro(datosContrato, "credito", noCredito)
                        addParametro(datosContrato, "ejecutivo", "{$_SESSION['usuario']}")
                        
                        if (document.querySelector("#contrato").value !== "") return regPago(document.querySelector("#contrato").value)
                        
                        consultaServidor("/Ahorro/AgregaContratoAhorro/", $.param(datosContrato), (respuesta) => {
                            if (!respuesta.success) {
                                console.error(respuesta.error)
                                return showError(respuesta.mensaje)
                            }
                            
                            regPago(respuesta.datos.contrato)
                        })
                    })
                }
                
                const regPago = (contrato) => {
                    const datos = $("#AddPagoApertura").serializeArray()
                    limpiaMontos(datos, ["deposito", "inscripcion", "saldo_inicial"])
                    addParametro(datos, "sucursal", "{$_SESSION['cdgco_ahorro']}")
                    addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                    addParametro(datos, "contrato", contrato)
                    
                    consultaServidor("/Ahorro/PagoApertura/", $.param(datos), (respuesta) => {
                        if (!respuesta.success) return showError(respuesta.mensaje)
                    
                        showSuccess(respuesta.mensaje)
                        .then(() => {
                            document.querySelector("#registroInicialAhorro").reset()
                            document.querySelector("#AddPagoApertura").reset()
                            $("#modal_agregar_pago").modal("hide")
                            limpiaDatosCliente()
                            
                            showSuccess("Se ha generado el contrato: " + contrato + ".")
                            .then(() => {
                                imprimeContrato(contrato, 1)
                                imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco_ahorro']}")
                            })
                        })
                    })
                }
                
                const validaDeposito = (e) => {
                    if (!valKD) return
                    
                    let monto = parseaNumero(e.target.value)
                    if (monto <= 0) {
                        e.preventDefault()
                        e.target.value = ""
                        showError("El monto a depositar debe ser mayor a 0.")
                    }
                    
                    if (monto > montoMaximo) {
                        e.preventDefault()
                        monto = montoMaximo
                        e.target.value = monto
                    }
                    
                    const valor = e.target.value.split(".")
                    if (valor[1] && valor[1].length > 2) {
                        e.preventDefault()
                        e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                    }
                    
                    document.querySelector("#monto_letra").value = numeroLetras(parseFloat(e.target.value))
                    calculaSaldoFinal(e)
                }
                
                const calculaSaldoFinal = (e) => {
                    const monto = parseaNumero(e.target.value)
                    document.querySelector("#deposito").value = formatoMoneda(monto)
                    const saldoInicial = (monto - parseaNumero(document.querySelector("#inscripcion").value))
                    document.querySelector("#saldo_inicial").value = formatoMoneda(saldoInicial > 0 ? saldoInicial : 0)
                    document.querySelector("#monto_letra").value = primeraMayuscula(numeroLetras(monto))
                        
                    if (saldoInicial < (saldoMinimoApertura - costoInscripcion)) {
                        document.querySelector("#saldo_inicial").setAttribute("style", "color: red")
                        document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                        document.querySelector("#registraDepositoInicial").disabled = true
                    } else {
                        document.querySelector("#saldo_inicial").removeAttribute("style")
                        document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                        document.querySelector("#registraDepositoInicial").disabled = false
                    }
                }
                
                const camposLlenos = (e) => {
                    if (document.querySelector("#sucursal").value === "") return
                    const val = () => {
                        let porcentaje = 0
                        for (let i = 1; i <= 3; i++) {
                            document.querySelector("#beneficiario_" + i).value = document.querySelector("#beneficiario_" + i).value.toUpperCase()
                            porcentaje += parseFloat(document.querySelector("#porcentaje_" + i).value) || 0
                            if (document.querySelector("#ben" + i).style.opacity === "1") {
                                if (!document.querySelector("#beneficiario_" + i).value) {
                                    document.querySelector("#parentesco_" + i).disabled = true
                                    document.querySelector("#porcentaje_" + i).disabled = true
                                    document.querySelector("#btnBen" + i).disabled = true
                                    return false
                                }
                                document.querySelector("#parentesco_" + i).disabled = false
                                
                                if (document.querySelector("#parentesco_" + i).selectedIndex === 0) {
                                    document.querySelector("#porcentaje_" + i).disabled = true
                                    document.querySelector("#btnBen" + i).disabled = true
                                    return false
                                }
                                document.querySelector("#porcentaje_" + i).disabled = false
                                
                                if (!document.querySelector("#porcentaje_" + i).value) {
                                    document.querySelector("#btnBen" + i).disabled = true
                                    return false
                                }
                                document.querySelector("#btnBen" + i).disabled = porcentaje >= 100 && document.querySelector("#btnBen1").querySelector("i").classList.contains("fa-plus")
                            }
                        }
                        
                        if (porcentaje > 100) {
                            e.preventDefault()
                            e.target.value = ""
                            showError("La suma de los porcentajes no puede ser mayor a 100%.")
                        }
                        
                        return porcentaje === 100
                    }
                    
                    if (e.target.tagName === "SELECT") actualizarOpciones(e.target)
                    
                    document.querySelector("#btnGeneraContrato").style.display = !val() ? "none" : "block"
                }
                
                const validaPorcentaje = (e) => {
                    let porcentaje = 0
                    for (let i = 1; i <= 3; i++) {
                        if (i == 1 || document.querySelector("#ben" + i).style.opacity === "1") {
                            const porcentajeBeneficiario = parseFloat(document.querySelector("#porcentaje_" + i).value) || 0
                            porcentaje += porcentajeBeneficiario
                        }
                    }
                    if (porcentaje > 100) {
                        e.preventDefault()
                        e.target.value = ""
                        return showError("La suma de los porcentajes no puede ser mayor a 100%")
                    }
                    
                    document.querySelector("#btnGeneraContrato").style.display = porcentaje !== 100 ? "none" : "block"
                }
                
                const toggleBeneficiario = (numBeneficiario) => {
                    const ben = document.getElementById(`ben` + numBeneficiario)
                    ben.style.opacity = ben.style.opacity === "0" ? "1" : "0"
                }
                
                const toggleButtonIcon = (btnId, show) => {
                    const btn = document.getElementById("btnBen" + btnId)
                    btn.innerHTML = show ? '<i class="fa fa-minus"></i>' : '<i class="fa fa-plus"></i>'
                }
                
                const addBeneficiario = (event) => {
                    const btn = event.target === event.currentTarget ? event.target : event.target.parentElement
                    
                    if (btn.innerHTML.trim() === '<i class="fa fa-plus"></i>') {
                        const noID = parseInt(btn.id.split("btnBen")[1])
                        habilitaBeneficiario(noID+1, true)
                        toggleBeneficiario(noID+1)
                        toggleButtonIcon(noID, true)
                    } else {
                        const noID = parseInt(btn.id.split("btnBen")[1])
                        for (let j = noID; j < 3; j++) {
                            moveData(j+1, j)
                        }
                        for (let i = 3; i > 0; i--) {
                            if (document.getElementById(`ben` + i).style.opacity === "1") {
                                habilitaBeneficiario(i, false)
                                toggleButtonIcon(i-1, false)
                                toggleBeneficiario(i)
                                break
                            }
                        }
                    }
                    camposLlenos(event)
                }
                
                const moveData = (from, to) => {
                    const beneficiarioFrom = document.getElementById(`beneficiario_` + from)
                    const parentescoFrom = document.getElementById(`parentesco_` + from)
                    const porcentajeFrom = document.getElementById(`porcentaje_` + from)
                    
                    const beneficiarioTo = document.getElementById(`beneficiario_` + to)
                    const parentescoTo = document.getElementById(`parentesco_` + to)
                    const porcentajeTo = document.getElementById(`porcentaje_` + to)
                    
                    beneficiarioTo.value = beneficiarioFrom.value
                    parentescoTo.value = parentescoFrom.value
                    porcentajeTo.value = porcentajeFrom.value
                    
                    beneficiarioFrom.value = ""
                    parentescoFrom.value = ""
                    porcentajeFrom.value = ""
                }
                
                const actualizarOpciones = (select) => {
                    const valoresUnicos = [
                        "CÓNYUGE",
                        "PADRE",
                        "MADRE",
                    ]
                        
                    const valorSeleccionado = select.value
                    const selects = document.querySelectorAll("#parentesco_1, #parentesco_2, #parentesco_3")
                    const valoresSeleccionados = [
                        document.querySelector("#parentesco_1").value,
                        document.querySelector("#parentesco_2").value,
                        document.querySelector("#parentesco_3").value
                    ]     
                    
                    selects.forEach(element => {
                        if (element !== select) {
                            element.querySelectorAll("option").forEach(opcion => {
                                if (!valoresUnicos.includes(opcion.text)) return
                                if (valoresUnicos.includes(opcion.text) &&
                                valoresSeleccionados.includes(opcion.value)) return opcion.style.display = "none"
                                opcion.style.display = opcion.value === valorSeleccionado ? "none" : "block"
                            })
                        }
                    })
                }
                
                const reImprimeContrato = (e) => {
                    const c = document.querySelector('#contratoOK').value
                    if (!c) {
                        e.preventDefault()
                        return
                    }
                    
                    imprimeContrato(c)
                }

                const mostrarModalHuellas = () => {
                    const valContrato = document.querySelector("#chkCreacionContrato").classList.contains("red")
                    const valPago = document.querySelector("#chkPagoApertura").classList.contains("red")
                    const valHuellas = document.querySelector("#chkRegistroHuellas").classList.contains("green")

                    if (valHuellas) return
                    if (valContrato) return showError("Debe completar el proceso de creación del contrato.")
                    if (valPago) return showError("Debe completar el proceso de pago de apertura.")

                    $("#modal_registra_huellas").modal("show")
                }
            
                const huellasCompletas = (e) => {
                    if (e.detail.modo === "captura") {
                        if (manoDerecha.manoLista() && manoIzquierda.manoLista()) {
                            document.querySelector("#registraHuellas").disabled = false
                            document.querySelector("#mensajeHuella").innerText = "Huellas capturadas correctamente."
                            return
                        }
                
                        document.querySelector("#registraHuellas").disabled = true
                        document.querySelector("#mensajeHuella").innerText = "$mensajeCaptura"
                    }

                    if (e.detail.modo === "actualizacion" && e.detail.muestrasOK) {
                        e.detail.evento()
                    }
                }
            
                const guardarHuellas = async () => {
                    if (!manoDerecha.manoLista() || !manoIzquierda.manoLista()) return showError("Debe capturar las muestras necesarias para ambas manos.")
                    
                    const manos = {}
                    Object.assign(manos, manoIzquierda.getMano())
                    Object.assign(manos, manoDerecha.getMano())
            
                    const datos = {
                        cliente: document.querySelector("#noCliente").value,
                        ejecutivo: "{$_SESSION['usuario']}",
                        manos: JSON.stringify(manos)
                    }
            
                    consultaServidor("/Ahorro/RegistraHuellas/", datos, (respuesta) => {
                        if (!respuesta.success) return showError(respuesta.mensaje)
                        showSuccess(respuesta.mensaje)
                        .then(() => {
                            manoIzquierda.modoValidacion()
                            manoDerecha.modoValidacion()
                            document.querySelector("#mensajeHuella").innerText = "Huellas registradas correctamente, valide y confirme."
                            document.querySelector("#registraHuellas").style.display = "none"
                            document.querySelector("#cerrar_modal").style.display = "none"
                        })
                    })
                }

                const validaHuella = (e) => {         
                    const datos = {
                        cliente: document.querySelector("#noCliente").value,
                        dedo: e.detail.dedo,
                        muestra: e.detail.muestra
                    }
            
                    consultaServidor("/Ahorro/ValidaHuella/", datos, (respuesta) => {
                        e.detail.colorImagen(respuesta.success ? "green" : "red")
                        if (!respuesta.success) {
                            e.detail.conteoErrores()
                            return showError(respuesta.mensaje)
                        }
            
                        e.detail.conteoErrores(0)
                        e.detail.boton.style.display = "none"
            
                        showSuccess(respuesta.mensaje).then(() => {
                            const botones = document.querySelectorAll(".btnHuella")
                            if (Array.from(botones).every(boton => boton.style.display === "none")) {
                                manoIzquierda.limpiarMano()
                                manoDerecha.limpiarMano()
                                document.querySelector("#registraHuellas").style.display = null
                                document.querySelector("#mensajeHuella").innerText = "Huellas registradas correctamente."
                                document.querySelector("#chkRegistroHuellas").classList.remove("red")
                                document.querySelector("#chkRegistroHuellas").classList.remove("fa-times")
                                document.querySelector("#chkRegistroHuellas").classList.add("green")
                                document.querySelector("#chkRegistroHuellas").classList.add("fa-check")
                                document.querySelector("#lnkHuellas").style.cursor = "default"
                                document.querySelector("#cerrar_modal").style.display = null
                                showSuccess("Huellas validadas correctamente.").then(() => {
                                    $("#modal_registra_huellas").modal("hide")
                                })
                            }
                        })
                    })
                }

                const actualizaHuella = (e) => {
                    const manos = {}
                    manos[e.detail.mano] = {}
                    manos[e.detail.mano][e.detail.dedo] = e.detail.muestras

                    const datos = {
                        cliente: document.querySelector("#noCliente").value,
                        manos: JSON.stringify(manos)
                    }

                    consultaServidor("/Ahorro/ActualizaHuella/", datos, (respuesta) => {
                        if (!respuesta.success) {
                            e.detail.limpiar()
                            e.detail.mensajeLector("Haz clic en la imagen para intentar nuevamente.")
                            return showError(respuesta.mensaje)
                        }
            
                        e.detail.valida()
                    })
                }
            </script>
        HTML;

        $sucursales = CajaAhorroDao::GetSucursalAsignadaCajeraAhorro($this->__usuario);
        $opcSucursales = "";
        foreach ($sucursales as $sucursales) {
            $opcSucursales .= "<option value='{$sucursales['CODIGO']}'>{$sucursales['NOMBRE']}</option>";
            $suc_eje = $sucursales['CODIGO'];
        }

        $ejecutivos = CajaAhorroDao::GetEjecutivosSucursal($suc_eje);
        $opcEjecutivos = "";
        foreach ($ejecutivos as $ejecutivos) {
            $opcEjecutivos .= "<option value='{$ejecutivos['ID_EJECUTIVO']}'>{$ejecutivos['EJECUTIVO']}</option>";
        }
        $opcEjecutivos .= "<option value='{$this->__usuario}' selected>{$this->__nombre} - CAJER(A)</option>";

        $parentescos = CajaAhorroDao::GetCatalogoParentescos();
        $opcParentescos = "<option value='' disabled selected>Seleccionar</option>";
        if ($parentescos['success']) {
            foreach ($parentescos['datos'] as $parentesco) {
                $opcParentescos .= "<option value='{$parentesco['CODIGO']}'>{$parentesco['DESCRIPCION']}</option>";
            }
        }


        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);
        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Contrato Ahorro Corriente", [$this->huellas])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        view::set('saldoMinimoApertura', $saldoMinimoApertura);
        view::set('costoInscripcion', $costoInscripcion);
        View::set('fecha', date('d/m/Y H:i:s'));
        view::set('opcParentescos', $opcParentescos);
        view::set('sucursales', $opcSucursales);
        view::set('ejecutivos', $opcEjecutivos);
        View::set('mensajeCaptura', $mensajeCaptura);
        View::render("caja_menu_contrato_ahorro");
    }

    public function BuscaCliente()
    {
        if (self::ValidaHorario()) {
            echo CajaAhorroDao::BuscaClienteNvoContrato($_POST);
            return;
        }
        echo self::FueraHorario();
    }

    public function GetBeneficiarios()
    {
        echo CajaAhorroDao::GetBeneficiarios($_POST['contrato']);
    }

    public function AgregaContratoAhorro()
    {
        echo CajaAhorroDao::AgregaContratoAhorro($_POST);
    }

    public function PagoApertura()
    {
        $pago = CajaAhorroDao::AddPagoApertura($_POST);
        echo $pago;
        return $pago;
    }

    public function RegistraHuellas()
    {
        $datosEngine = [
            "manos" => $_POST['manos'],
        ];

        $huellas = self::EngineHuellas("preregistro.php", $datosEngine);

        if (!$huellas['success']) {
            echo json_encode($huellas);
            exit;
        }

        $datos = [
            "cliente" => $_POST['cliente'],
            "ejecutivo" => $_POST['ejecutivo'],
            "izquierda" => $huellas['datos']['izquierda'],
            "derecha" => $huellas['datos']['derecha']
        ];

        echo CajaAhorroDao::RegistraHuellas($datos);
    }

    public function ActualizaHuella()
    {
        $datosEngine = [
            "manos" => $_POST['manos'],
        ];

        $huellas = self::EngineHuellas("preregistro.php", $datosEngine);

        if (!$huellas['success']) {
            echo json_encode($huellas);
            exit;
        }

        $dedos = [];
        foreach ($huellas['datos'] as $mano => $dedo) {
            foreach ($dedo as $dedo => $huella) {
                $d = $dedo . "_" . $mano[0];
                $dedos[$d] = $huella;
            }
        }

        $datos = [
            "cliente" => $_POST['cliente'],
            "dedos" => $dedos,
        ];

        echo CajaAhorroDao::ActualizaHuella($datos);
    }

    public function ValidaHuella()
    {
        echo json_encode([
            "success" => true,
            "mensaje" => "Validada."
        ]);
        return;

        $repuesta = [
            "success" => false,
            "mensaje" => "No se ha podido validar la huella."
        ];

        $huellas = CajaAhorroDao::GetHuellas($_POST);

        if (count($huellas) == 0) {
            $repuesta['mensaje'] = "No se encontraron registros en la base de datos.";
            echo json_encode($repuesta);
            return;
        }

        $huellasEngine = [];
        foreach ($huellas as $huella => $valor) {
            array_push($huellasEngine, $valor["HUELLA"]);
        }

        $datosEngine = [
            "dedo" => $_POST['muestra'],
            "huellas" => json_encode($huellasEngine)
        ];

        $resultado = self::EngineHuellas("identifica.php", $datosEngine);
        $repuesta["resultado"] = $resultado;

        $repuesta["success"] = $resultado["success"];
        $repuesta["mensaje"] = $resultado["mensaje"];
        if ($resultado["success"]) {
            $repuesta["cliente"] = $huellas[$resultado["coincidencia"]]["CLIENTE"];
        }

        echo json_encode($repuesta);
    }

    public function EngineHuellas($endpoint, $datos)
    {
        $ci = curl_init($this->configuracion['API_HUELLAS'] . $endpoint);
        curl_setopt($ci, CURLOPT_POST, true);
        curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($datos));
        curl_setopt($ci, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ci);
        curl_close($ci);
        return json_decode($response, true);
    }

    public function ValidaRegistroHuellas()
    {
        echo CajaAhorroDao::ValidaRegistroHuellas($_POST);
    }

    public function EliminaHuellas()
    {
        echo CajaAhorroDao::EliminaHuellas($_POST);
    }

    // Movimientos sobre cuentas de ahorro corriente //
    public function CuentaCorriente()
    {
        $saldoMinimoApertura = 100;
        $montoMaximoRetiro = 50000;
        $montoMaximoDeposito = 1000000;
        $maximoRetiroDia = 50000;

        $extraFooter = <<<script
        <script>
            const saldoMinimoApertura = $saldoMinimoApertura
            const montoMaximoRetiro = $montoMaximoRetiro
            const montoMaximoDeposito = $montoMaximoDeposito
            const maximoRetiroDia = $maximoRetiroDia
            const noSucursal = "{$_SESSION['cdgco_ahorro']}"
            let huellas = 0
            let retiroDispobible = maximoRetiroDia
            let mano

            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->sinContrato}
            {$this->addParametro}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->consultaServidor}
            {$this->limpiaMontos}
            {$this->showBloqueo}
            {$this->validaHorarioOperacion}
            {$this->valida_MCM_Complementos}
            {$this->showHuella}
            {$this->validaHuella}
            {$this->autorizaOperacion}
 
            window.onload = () => {
                validaHorarioOperacion("{$_SESSION['inicio']}", "{$_SESSION['fin']}")
                if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }

            const llenaDatosCliente = (datosCliente) => {
                retiroDispobible = maximoRetiroDia
                let blkRetiro = false
                consultaServidor("/Ahorro/ValidaRetirosDia/", { contrato: datosCliente.CONTRATO }, (respuesta) => {
                    if (!respuesta.success && respuesta.datos.RETIROS >= maximoRetiroDia) {
                        showWarning("El cliente " + datosCliente.CDGCL + " ha alcanzado el límite de retiros diarios.")
                        blkRetiro = true
                        retiroDispobible = maximoRetiroDia - respuesta.datos.RETIROS
                    }
                    huellas = datosCliente.HUELLAS
                    document.querySelector("#nombre").value = datosCliente.NOMBRE
                    document.querySelector("#curp").value = datosCliente.CURP
                    document.querySelector("#contrato").value = datosCliente.CONTRATO
                    document.querySelector("#cliente").value = datosCliente.CDGCL
                    document.querySelector("#saldoActual").value = formatoMoneda(datosCliente.SALDO)
                    document.querySelector("#deposito").disabled = false
                    document.querySelector("#retiro").disabled = blkRetiro
                })
            }
             
            const limpiaDatosCliente = () => {
                huellas = 0
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#monto").disabled = true
                document.querySelector("#btnRegistraOperacion").disabled = true
                document.querySelector("#retiro").disabled = true
                document.querySelector("#deposito").disabled = true
            }
             
            const validaMonto = () => {
                if (!valKD) return
                const montoIngresado = document.querySelector("#monto")
                 
                let monto = parseaNumero(montoIngresado.value)
                 
                if (!document.querySelector("#deposito").checked && monto > montoMaximoRetiro) {
                    monto = montoMaximoRetiro
                    swal({
                        title: "Cuenta de ahorro corriente",
                        text: "Para retiros mayores a " + montoMaximoRetiro.toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + " es necesario realizar una solicitud de retiro\\nDesea generar una solicitud de retiro ahora?.",
                        icon: "info",
                        buttons: ["No", "Sí"],
                        dangerMode: true
                    }).then((regRetiro) => {
                        if (regRetiro) {
                            window.location.href = "/Ahorro/SolicitudRetiroCuentaCorriente/?cliente=" + document.querySelector("#cliente").value
                            return
                        }
                    })
                    montoIngresado.value = monto
                }
                 
                if (document.querySelector("#deposito").checked && monto > montoMaximoDeposito) {
                    monto = montoMaximoDeposito
                    montoIngresado.value = monto
                }
                 
                const valor = montoIngresado.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    montoIngresado.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                if (montoIngresado.id === "mdlDeposito_inicial") return calculaSaldoInicial(e)
                 
                document.querySelector("#monto_letra").value = numeroLetras(parseFloat(montoIngresado.value))
                if (document.querySelector("#deposito").checked || document.querySelector("#retiro").checked) calculaSaldoFinal()
            }
             
            const calculaSaldoInicial = (e) => {
                const monto = parseaNumero(e.target.value)
                document.querySelector("#mdlDeposito").value = formatoMoneda(monto)
                const saldoInicial = (monto - parseaNumero(document.querySelector("#mdlInscripcion").value)).toFixed(2)
                document.querySelector("#mdlSaldo_inicial").value = formatoMoneda(saldoInicial > 0 ? saldoInicial : 0)
                document.querySelector("#mdlDeposito_inicial_letra").value = primeraMayuscula(numeroLetras(monto))
                    
                if (saldoInicial < saldoMinimoApertura) {
                    document.querySelector("#mdlSaldo_inicial").setAttribute("style", "color: red")
                    document.querySelector("#mdlTipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#mdlRegistraDepositoInicial").disabled = true
                } else {
                    document.querySelector("#mdlSaldo_inicial").removeAttribute("style")
                    document.querySelector("#mdlTipSaldo").setAttribute("style", "opacity: 0%;")
                    document.querySelector("#mdlRegistraDepositoInicial").disabled = false
                }
            }
             
            const calculaSaldoFinal = () => {
                const esDeposito = document.querySelector("#deposito").checked
                const saldoActual = parseaNumero(document.querySelector("#saldoActual").value)
                const monto = parseaNumero(document.querySelector("#monto").value)
                document.querySelector("#montoOperacion").value = formatoMoneda(monto)
                document.querySelector("#saldoFinal").value = formatoMoneda(esDeposito ? saldoActual + monto : saldoActual - monto)
                compruebaSaldoFinal()
            }
             
            const cambioMovimiento = (e) => {
                document.querySelector("#monto").disabled = false
                const esDeposito = document.querySelector("#deposito").checked
                document.querySelector("#simboloOperacion").innerText = esDeposito ? "+" : "-"
                document.querySelector("#descOperacion").innerText = (esDeposito ? "Depósito" : "Retiro") + " a cuenta ahorro corriente"
                document.querySelector("#monto").max = esDeposito ? montoMaximoDeposito : montoMaximoRetiro
                valKD = true
                validaMonto()
                calculaSaldoFinal()
            }
             
            const compruebaSaldoFinal = () => {
                const saldoFinal = parseaNumero(document.querySelector("#saldoFinal").value)
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#tipSaldo").innerText = "El monto a retirar no puede ser mayor al saldo de la cuenta."
                    document.querySelector("#btnRegistraOperacion").disabled = true
                    return
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                if (document.querySelector("#retiro").checked && retiroDispobible < parseaNumero(document.querySelector("#montoOperacion").value)) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#tipSaldo").innerText = "El monto a retirar excede el límite de retiros diarios, disponible para retirar el día de hoy: " + retiroDispobible.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    document.querySelector("#btnRegistraOperacion").disabled = true
                    return
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(saldoFinal >= 0 && parseaNumero(document.querySelector("#montoOperacion").value) > 0)
            }
             
            const registraOperacion = async (e) => {
                if (!await valida_MCM_Complementos()) return
                 
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                
                limpiaMontos(datos, ["saldoActual", "montoOperacion", "saldoFinal"])
                addParametro(datos, "sucursal", "{$_SESSION['cdgco_ahorro']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                addParametro(datos, "producto", "cuenta de ahorro corriente")
                 
                if (!document.querySelector("#deposito").checked && !document.querySelector("#retiro").checked) return showError("Seleccione el tipo de operación a realizar.")
                
                datos.forEach((dato) => {
                    if (dato.name === "esDeposito") dato.value = document.querySelector("#deposito").checked
                })
                 
                confirmarMovimiento(
                    "Confirmación de movimiento de ahorro corriente",
                    "¿Está segur(a) de continuar con el registro de un "
                    + (document.querySelector("#deposito").checked ? "depósito" : "retiro")
                    + " de cuanta ahorro corriente por la cantidad de "
                    + parseaNumero(document.querySelector("#montoOperacion").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    + " (" + document.querySelector("#monto_letra").value + ")?"
                ).then((continuar) => {
                    if (!continuar) return
                    if (!document.querySelector("#deposito").checked && huellas > 0) return showHuella(true, datos)
                    enviaRegistroOperacion(datos)
                })
            }

            const enviaRegistroOperacion = (datos) => {
                consultaServidor("/Ahorro/RegistraOperacion/", $.param(datos), (respuesta) => {
                    if (!respuesta.success){
                        if (respuesta.error) return showError(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                    showSuccess(respuesta.mensaje).then(() => {
                        imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco_ahorro']}")
                        limpiaDatosCliente()
                    })
                })
            }
        </script>
        script;

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Ahorro Corriente", [$this->swal2, $this->huellas])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        view::set('saldoMinimoApertura', $saldoMinimoApertura);
        view::set('montoMaximoRetiro', $montoMaximoRetiro);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_menu_ahorro");
    }

    public function BuscaContratoAhorro()
    {
        if (self::ValidaHorario()) {
            echo CajaAhorroDao::BuscaContratoAhorro($_POST);
            return;
        }
        echo self::FueraHorario();
    }

    public function RegistraOperacion()
    {
        $resutado =  CajaAhorroDao::RegistraOperacion($_POST);
        echo $resutado;
    }

    public function ValidaRetirosDia()
    {
        echo CajaAhorroDao::ValidaRetirosDia($_POST);
    }

    // Registro de solicitudes de retiros mayores de cuentas de ahorro //
    public function SolicitudRetiroCuentaCorriente()
    {
        $montoMinimoRetiro = 10000;
        $montoMaximoExpress = 1000000;
        $montoMaximoRetiro = 1000000;

        $extraFooter = <<<html
        <script>
            window.onload = () => {
                if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }
         
            const montoMinimo = $montoMinimoRetiro
            const montoMaximoExpress = $montoMaximoExpress
            const montoMaximoRetiro = $montoMaximoRetiro
            const noSucursal = "{$_SESSION['cdgco_ahorro']}"
            let valKD = false
            let huellas = 0
         
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->soloNumeros}
            {$this->primeraMayuscula}
            {$this->numeroLetras}
            {$this->muestraPDF}
            {$this->addParametro}
            {$this->sinContrato}
            {$this->getHoy}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->limpiaMontos}
            {$this->consultaServidor}
            {$this->valida_MCM_Complementos}
            {$this->showHuella}
            {$this->validaHuella}
            {$this->autorizaOperacion}
             
            const llenaDatosCliente = (datosCliente) => {
                if (parseaNumero(datosCliente.SALDO) < montoMinimo) {
                    swal({
                        title: "Retiro de cuenta corriente",
                        text: "El saldo de la cuenta es menor al monto mínimo para retiros express (" + montoMinimo.toLocaleString("es-MX", {style:"currency", currency:"MXN"}) + ").\\n¿Desea realizar un retiro simple?",
                        icon: "info",
                        buttons: ["No", "Sí"]
                    }).then((retSimple) => {
                        if (retSimple) {
                            window.location.href = "/Ahorro/CuentaCorriente/?cliente=" + datosCliente.CDGCL
                            return
                        }
                    })
                    return
                }
                 
                huellas = datosCliente.HUELLAS
                document.querySelector("#nombre").value = datosCliente.NOMBRE
                document.querySelector("#curp").value = datosCliente.CURP
                document.querySelector("#contrato").value = datosCliente.CONTRATO
                document.querySelector("#cliente").value = datosCliente.CDGCL
                document.querySelector("#saldoActual").value = formatoMoneda(datosCliente.SALDO)
                document.querySelector("#monto").disabled = false
                document.querySelector("#saldoFinal").value = formatoMoneda(datosCliente.SALDO)
                document.querySelector("#express").disabled = false
                document.querySelector("#programado").disabled = false
            }
             
            const limpiaDatosCliente = () => {
                huellas = 0
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#monto").disabled = true
                document.querySelector("#btnRegistraOperacion").disabled = true
                document.querySelector("#express").disabled = true
                document.querySelector("#programado").disabled = true
                document.querySelector("#fecha_retiro_hide").setAttribute("style", "display: none;")
                document.querySelector("#fecha_retiro").removeAttribute("style")
            }
             
            const validaMonto = () => {
                document.querySelector("#express").disabled = false
                const montoIngresado = document.querySelector("#monto")
                 
                let monto = parseaNumero(montoIngresado.value) || 0
                 
                if (monto > montoMaximoExpress) {
                    document.querySelector("#programado").checked = true
                    document.querySelector("#express").disabled = true
                    cambioMovimiento()
                }
                 
                if (monto > montoMaximoRetiro) {
                    monto = montoMaximoRetiro
                    montoIngresado.value = monto
                }
                                  
                document.querySelector("#monto_letra").value = primeraMayuscula(numeroLetras(monto))
                const saldoActual = parseaNumero(document.querySelector("#saldoActual").value)
                document.querySelector("#montoOperacion").value = formatoMoneda(monto)
                const saldoFinal = (saldoActual - monto)
                document.querySelector("#saldoFinal").value = formatoMoneda(saldoFinal)
                compruebaSaldoFinal()
            }
             
            const valSalMin = () => {
                const montoIngresado = document.querySelector("#monto")
                 
                let monto = parseFloat(montoIngresado.value) || 0
                 
                if (monto < montoMinimo) {
                    monto = montoMinimo
                    swal({
                        title: "Retiro de cuenta corriente",
                        text: "El monto mínimo para retiros express es de " + montoMinimo.toLocaleString("es-MX", {
                            style: "currency",
                            currency: "MXN"
                        }) + ", para un monto menor debe realizar el retiro de manera simple.\\n¿Desea realizar el retiro de manera simple?",
                        icon: "info",
                        buttons: ["No", "Sí"]
                    }).then((retSimple) => {
                        if (retSimple) {
                            window.location.href = "/Ahorro/CuentaCorriente/?cliente=" + document.querySelector("#cliente").value
                            return
                        }
                    })
                }
            }
             
            const cambioMovimiento = (e) => {
                const express = document.querySelector("#express").checked
                
                if (express) {
                    document.querySelector("#fecha_retiro").removeAttribute("style")
                    document.querySelector("#fecha_retiro_hide").setAttribute("style", "display: none;")
                    document.querySelector("#fecha_retiro").value = getHoy()
                    return
                }
                
                document.querySelector("#fecha_retiro_hide").removeAttribute("style")
                document.querySelector("#fecha_retiro").setAttribute("style", "display: none;")
                pasaFecha({ target: document.querySelector("#fecha_retiro") })
            }
             
            const compruebaSaldoFinal = () => {
                const saldoFinal = parseaNumero(document.querySelector("#saldoFinal").value)
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#btnRegistraOperacion").disabled = true
                    return
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(saldoFinal >= 0 && parseaNumero(document.querySelector("#montoOperacion").value) >= montoMinimo && parseaNumero(document.querySelector("#montoOperacion").value) < montoMaximoRetiro)
            }
             
            const pasaFecha = (e) => {
                const fechaSeleccionada = new Date(e.target.value)
                if (fechaSeleccionada.getDay() === 5 || fechaSeleccionada.getDay() === 6) {
                    showError("No se pueden realizar retiros los fines de semana.")
                    const f = getHoy(false).split("/")
                    e.target.value = f[2] + "-" + f[1] + "-" + f[0]
                    return
                }
                const f = document.querySelector("#fecha_retiro_hide").value.split("-")
                document.querySelector("#fecha_retiro").value = f[2] + "/" + f[1] + "/" + f[0]
            }
             
            const registraSolicitud = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                
                limpiaMontos(datos, ["saldoActual", "montoOperacion", "saldoFinal"])
                addParametro(datos, "sucursal", "{$_SESSION['cdgco_ahorro']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                addParametro(datos, "retiroExpress", document.querySelector("#express").checked)
                 
                confirmarMovimiento(
                    "Confirmación de movimiento ahorro corriente",
                    "¿Está segur(a) de continuar con el registro de un retiro "
                    + (document.querySelector("#express").checked ? "express" : "programado")
                    + ", por la cantidad de "
                    + parseaNumero(document.querySelector("#montoOperacion").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    + " (" + document.querySelector("#monto_letra").value + ")?"
                ).then((continuar) => {
                    if (!continuar) return
                    if (huellas > 0) return showHuella(true, datos)
                    enviaRegistroOperacion(datos)
                })
            }

            const enviaRegistroOperacion = (datos) => {
                consultaServidor("/Ahorro/RegistraSolicitud/", $.param(datos), (respuesta) => {
                    if (!respuesta.success) {
                        console.log(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                    showSuccess(respuesta.mensaje).then(() => {
                        document.querySelector("#registroOperacion").reset()
                        limpiaDatosCliente()
                    })
                })
            }
        </script>
        html;

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Solicitud de Retiro", [$this->swal2, $this->huellas])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('montoMinimoRetiro', $montoMinimoRetiro);
        View::set('montoMaximoExpress', $montoMaximoExpress);
        View::set('montoMaximoRetiro', $montoMaximoRetiro);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::set('fechaInput', date('Y-m-d', strtotime('+1 day')));
        View::set('fechaInputMax', date('Y-m-d', strtotime('+30 day')));
        View::render("caja_menu_retiro_ahorro");
    }

    public function RegistraSolicitud()
    {
        $datos = CajaAhorroDao::RegistraSolicitud($_POST);
        echo $datos;
    }

    // Historial de solicitudes de retiros de cuentas de ahorro //
    public function HistorialSolicitudRetiroCuentaCorriente()
    {
        $extraFooter = <<<html
        <script>
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->imprimeTicket}
            {$this->muestraPDF}
            {$this->addParametro}
            {$this->validaFIF}
            {$this->valida_MCM_Complementos}
         
            $(document).ready(() => {
                configuraTabla("hstSolicitudes")
            })
            
            const imprimeExcel = () => exportaExcel("hstSolicitudes", "Historial solicitudes de retiro")
             
            const actualizaEstatus = async (estatus, id) => {
                if (!await valida_MCM_Complementos()) return
                 
                const accion = estatus === 3 ? "entrega" : "cancelación"
                 
                consultaServidor("/Ahorro/ResumenEntregaRetiro", $.param({id}), (respuesta) => {
                    if (!respuesta.success) {
                        if (respuesta.error) return showError(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                     
                    const resumen = respuesta.datos
                    confirmarMovimiento(
                        "Seguimiento solicitudes de retiro",
                        null,
                        resumenRetiro(resumen, accion)
                    ).then((continuar) => {
                        if (!continuar) return
                        const datos = {
                            estatus, 
                            id, 
                            ejecutivo: "{$_SESSION['usuario']}", 
                            sucursal: "{$_SESSION['cdgco_ahorro']}", 
                            monto: resumen.MONTO, 
                            contrato: resumen.CONTRATO,
                            cliente: resumen.CLIENTE,
                            tipo: resumen.TIPO_RETIRO
                        }
                         
                        consultaServidor("/Ahorro/EntregaRetiro/", $.param(datos), (respuesta) => {
                            if (!respuesta.success) {
                                if (respuesta.error) return showError(respuesta.error)
                                return showError(respuesta.mensaje)
                            }
                             
                            showSuccess(respuesta.mensaje).then(() => {
                                if (estatus === 3) {
                                    imprimeTicket(respuesta.datos.CODIGO, "{$_SESSION['cdgco_ahorro']}")
                                    swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                                    window.location.reload()
                                }
                                if (estatus === 4) devuelveRetiro(resumen)
                            })
                        })
                    })
                })
            }
             
            const resumenRetiro = (datos, accion) => {
                const resumen = document.createElement("div")
                resumen.setAttribute("style", "color: rgba(0, 0, 0, .65); text-align: left;")
                
                const tabla = document.createElement("table")
                tabla.setAttribute("style", "width: 100%;")
                tabla.innerHTML = "<thead><tr><th colspan='2' style='font-size: 25px; text-align: center;'>Retiro " + (datos.TIPO_RETIRO == 1 ? "express" : "programado") + "</th></tr></thead>"
                 
                const tbody = document.createElement("tbody")
                tbody.setAttribute("style", "width: 100%;")
                tbody.innerHTML += "<tr><td><strong>Cliente:</strong></td><td style='text-align: center;'>" + datos.NOMBRE + "</td></tr>"
                tbody.innerHTML += "<tr><td><strong>Contrato:</strong></td><td style='text-align: center;'>" + datos.CONTRATO + "</td></tr>"
                tbody.innerHTML += "<tr><td><strong>Monto:</strong></td><td style='text-align: center;'>" + parseFloat(datos.MONTO).toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + "</td></tr>"
                
                const tInterno = document.createElement("table")
                tInterno.setAttribute("style", "width: 100%; margin-top: 20px;")
                const tbodyI = document.createElement("tbody")
                tbodyI.innerHTML += "<tr><td><strong>Autorizado por:</strong></td style='text-align: center;'><td>" + datos.APROBADO_POR + "</td></tr>"
                tbodyI.innerHTML += "<tr><td><strong>A " + (accion === "entrega" ? "entregar" : "cancelar") + " por:</strong></td style='text-align: center;'><td>{$_SESSION['nombre']}</td></tr>"
                tInterno.appendChild(tbodyI)
                 
                const tFechas = document.createElement("table")
                tFechas.setAttribute("style", "width: 100%; margin-top: 20px;")
                const tbodyF = document.createElement("tbody")
                tbodyF.innerHTML += "<tr><td style='text-align: center; width: 50%;'><strong>Fecha entrega solicitada</strong></td><td style='text-align: center; width: 50%;'><strong>Fecha " + (accion === "entrega" ? accion + " real" : accion) + "</strong></td></tr>"
                tbodyF.innerHTML += "<tr><td style='text-align: center; width: 50%;'>" + datos.FECHA_ESPERADA + "</td><td style='text-align: center; width: 50%;'>" + new Date().toLocaleString("es-MX", { day: "2-digit", month: "2-digit", year: "numeric"}) + "</td></tr>"
                tFechas.appendChild(tbodyF)
                 
                tabla.appendChild(tbody)
                resumen.appendChild(tabla)
                resumen.appendChild(tInterno)
                resumen.appendChild(tFechas)
                 
                const pregunta = document.createElement("label")
                pregunta.setAttribute("style", "width: 100%; font-size: 20px; text-align: center; font-weight: bold; margin-top: 20px;")
                pregunta.innerText = "¿Desea continuar con la " + accion + " del retiro?"
                 
                const advertencia = document.createElement("label")
                advertencia.setAttribute("style", "width: 100%; color: red; font-size: 15px; text-align: center;")
                advertencia.innerText = "Esta acción no se puede deshacer."
                 
                resumen.appendChild(pregunta)
                resumen.appendChild(advertencia)
                return resumen
            }
             
            const devuelveRetiro = (datos) => {
                const datosDev = {
                    contrato: datos.CONTRATO,
                    monto: datos.MONTO,
                    ejecutivo: "{$_SESSION['usuario']}",
                    sucursal: "{$_SESSION['cdgco_ahorro']}",
                    tipo: datos.TIPO_RETIRO
                }
                 
                consultaServidor("/Ahorro/DevolucionRetiro/", $.param(datosDev), (respuesta) => {
                    if (!respuesta.success) {
                        console.log(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                     
                    showSuccess(respuesta.mensaje).then(() => {
                        imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco_ahorro']}", false)
                        swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                        window.location.reload()
                    })
                })
            }
             
            const buscar = () => {
                const datos = []
                addParametro(datos, "producto", 1)
                addParametro(datos, "fechaI", document.querySelector("#fechaI").value)
                addParametro(datos, "fechaF", document.querySelector("#fechaF").value)
                addParametro(datos, "estatus", document.querySelector("#estatus").value)
                addParametro(datos, "tipo", document.querySelector("#tipo").value)
                 
                consultaServidor("/Ahorro/HistoricoSolicitudRetiro/", $.param(datos), (respuesta) => {
                    $("#hstSolicitudes").DataTable().destroy()
                     
                    if (respuesta.datos == "") showError("No se encontraron solicitudes de retiro en el rango de fechas seleccionado.")
                     
                    $("#hstSolicitudes tbody").html(respuesta.datos)
                    configuraTabla("hstSolicitudes")
                })
            }
             
            const validaFechaEntrega = (fecha) => showError("La solicitud no está disponible para entrega, la fecha programada de entrega es el " + fecha + ".")
        </script>
        html;

        $tabla = self::HistoricoSolicitudRetiro(1);
        $tabla = $tabla['success'] ? $tabla['datos'] : "";

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial de solicitudes de retiro", [$this->XLSX])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::set('fecha', date('Y-m-d'));
        View::render("caja_menu_solicitud_retiro_historial");
    }

    public function ResumenEntregaRetiro()
    {
        echo CajaAhorroDao::ResumenEntregaRetiro($_POST);
    }

    public function EntregaRetiro()
    {
        echo CajaAhorroDao::EntregaRetiro($_POST);
    }

    public function DevolucionRetiro()
    {
        echo CajaAhorroDao::DevolucionRetiro($_POST);
    }

    public function HistoricoSolicitudRetiro($p = 1)
    {
        $producto = $_POST['producto'] ?? $p;
        $fi = $_POST['fechaI'] ?? date('Y-m-d');
        $ff = $_POST['fechaF'] ?? date('Y-m-d');
        $estatus = $_POST['estatus'] ?? "1";
        $tipo = $_POST['tipo'] ?? "";

        $historico = json_decode(CajaAhorroDao::HistoricoSolicitudRetiro(["producto" => $producto, "fechaI" => $fi, "fechaF" => $ff, "estatus" => $estatus, "tipo" => $tipo]));
        $detalles = $historico->success ? $historico->datos : [];

        $tabla = "";
        foreach ($detalles as $key1 => $detalle) {
            $tabla .= "<tr>";
            $acciones = "";
            foreach ($detalle as $key2 => $valor) {
                if ($key2 === "ID") continue;
                $v = $valor;
                if ($key2 === "MONTO") $v = "$ " . number_format($valor, 2);

                $tabla .= "<td style='vertical-align: middle;'>$v</td>";

                if ($key2 === "ESTATUS" && $valor === "APROBADO") {
                    $acciones .= "<button type='button' class='btn btn-success btn-circle' onclick='" .
                        ($detalle->FECHA_SOLICITUD == date("d/m/Y") ? "actualizaEstatus(3, {$detalle->ID})" :
                            "validaFechaEntrega(\"{$detalle->FECHA_SOLICITUD}\")") .
                        "'><i class='glyphicon glyphicon-transfer'></i></button>";
                    $acciones .= "<button type='button' class='btn btn-danger btn-circle' onclick='actualizaEstatus(4, {$detalle->ID})'><i class='fa fa-trash'></i></button>";
                }
            }

            $tabla .= "<td style='vertical-align: middle;'>" . $acciones . "</td>";
            $tabla .= "</tr>";
        }

        $r = ["success" => true, "datos" => $tabla];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') echo json_encode($r);
        else return $r;
    }

    //********************INVERSIONES********************//
    // Apertura de contratos para cuentas de inversión
    public function ContratoInversion()
    {
        $saldoMinimoApertura = CajaAhorroDao::GetSaldoMinimoInversion();
        $tasas = CajaAhorroDao::GetTasas();
        $tasas = $tasas ? json_encode($tasas) : "[]";
        $suc = $_SESSION['cdgco_ahorro'] !== "NULL" ? $_SESSION['cdgco_ahorro'] : CajaAhorroDao::GetSucCajeraAhorro($_SESSION['usuario'])['CDGCO_AHORRO'];
        $usr = $_SESSION['usuario'];

        $extraFooter = <<<html
        <script>
            const saldoMinimoApertura = $saldoMinimoApertura
            const montoMaximo = 1000000
            const sucursal_ahorro = "$suc"
            const usuario_ahorro = "$usr"
            const noSucursal = "{$_SESSION['cdgco_ahorro']}"
            let tasasDisponibles
            let huellas = 0
            let mano
         
            try {
                tasasDisponibles = JSON.parse('$tasas')
            } catch (error) {
                console.error(error)
                tasasDisponibles = []
            }
            let valKD = false
         
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->imprimeContrato}
            {$this->sinContrato}
            {$this->addParametro}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->limpiaMontos}
            {$this->consultaServidor}
            {$this->showBloqueo}
            {$this->validaHorarioOperacion}
            {$this->valida_MCM_Complementos}
            {$this->showHuella}
            {$this->validaHuella}
            {$this->autorizaOperacion}
         
            window.onload = () => {
                validaHorarioOperacion("{$_SESSION['inicio']}", "{$_SESSION['fin']}")
            }
             
            const llenaDatosCliente = (datos) => {
                const saldoActual = parseaNumero(datos.SALDO)
                         
                huellas = datos.HUELLAS
                document.querySelector("#nombre").value = datos.NOMBRE
                document.querySelector("#curp").value = datos.CURP
                document.querySelector("#contrato").value = datos.CONTRATO
                document.querySelector("#cliente").value = datos.CDGCL
                document.querySelector("#saldoActual").value = formatoMoneda(saldoActual)
                document.querySelector("#saldoFinal").value = formatoMoneda(saldoActual)
                if (saldoActual >= saldoMinimoApertura) return document.querySelector("#monto").disabled = false
                
                showError("No es posible hacer la apertura de inversión.\\nEl saldo mínimo de apertura es de " + saldoMinimoApertura.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' }) + 
                "\\nEl saldo actual del cliente es de " + saldoActual.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' }))
            }
            
            const limpiaDatosCliente = () => {
                huellas = 0
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#monto").disabled = true
                document.querySelector("#btnRegistraOperacion").disabled = true
                document.querySelector("#plazo").innerHTML = ""
                document.querySelector("#plazo").disabled = true
                habiltaEspecs()
            }
            
            const validaDeposito = (e) => {
                if (!valKD) return
                
                let monto = parseaNumero(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                }
                
                if (monto > montoMaximo) {
                    e.preventDefault()
                    monto = montoMaximo
                    e.target.value = monto
                }
                
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseaNumero(valor[0] + "." + valor[1].substring(0, 2))
                }
                 
                const saldoFinal = parseaNumero(document.querySelector("#saldoActual").value) - monto
                document.querySelector("#montoOperacion").value = formatoMoneda(monto)
                document.querySelector("#saldoFinal").value = formatoMoneda(saldoFinal < 0 ? 0 : saldoFinal)
                document.querySelector("#monto_letra").value = numeroLetras(monto)
                compruebaSaldoFinal(saldoFinal)
                habiltaEspecs(monto)
                compruebaSaldoMinimo()
            }
            
            const compruebaSaldoMinimo = () => {
                const monto = parseaNumero(document.querySelector("#monto").value)
                let mMax = 0
                
                const tasas =  tasasDisponibles
                .filter(tasa => {
                    const r = monto >= saldoMinimoApertura && tasa.MONTO_MINIMO <= monto 
                    mMax = r ? tasa.MONTO_MINIMO : mMax
                    return r
                })
                .filter(tasa => tasa.MONTO_MINIMO == mMax)
                 
                if (tasas.length > 0) {
                    document.querySelector("#plazo").innerHTML = tasas.map(tasa => "<option value='" + tasa.CODIGO + "'>" + tasa.PLAZO + "</option>").join("")
                    document.querySelector("#plazo").disabled = false
                    cambioPlazo()
                    return 
                }
                 
                document.querySelector("#plazo").innerHTML = ""
                document.querySelector("#plazo").disabled = true
                document.querySelector("#rendimiento").value = ""
            }
             
            const cambioPlazo = () => {
                const info = tasasDisponibles.find(tasa => tasa.CODIGO == document.querySelector("#plazo").value)
                const plazo = parseaNumero(info.PLAZO_NUMERO)
                const tasa = parseaNumero(info.TASA)
                const monto = parseaNumero(document.querySelector("#monto").value) 
                if (tasa) {
                    document.querySelector("#rendimiento").value = formatoMoneda(monto * plazo * ((tasa/100) / 12))
                    document.querySelector("#leyendaRendimiento").innerText = "* Rendimiento calculado con una tasa anual fija del " + info.TASA + "%"
                    return
                }
                 
                document.querySelector("#rendimiento").value = ""
                document.querySelector("#leyendaRendimiento").innerText = ""
            }
             
            const compruebaSaldoFinal = saldoFinal => {
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                habilitaBoton()
            }
             
            const habilitaBoton = (e) => {
                if (e && e.target.id === "plazo") cambioPlazo()
                document.querySelector("#btnRegistraOperacion").disabled = !(parseaNumero(document.querySelector("#saldoFinal").value) >= 0 && parseaNumero(document.querySelector("#montoOperacion").value) >= saldoMinimoApertura)
            }
             
            const habiltaEspecs = (monto = parseaNumero(document.querySelector("#monto").value)) => {
                document.querySelector("#plazo").disabled = !(monto >= saldoMinimoApertura)
                document.querySelector("#renovacion").disabled = !(monto >= saldoMinimoApertura)
                 
                if (monto < saldoMinimoApertura) {
                    document.querySelector("#plazo").innerHTML = ""
                    document.querySelector("#rendimiento").value = ""
                    document.querySelector("#renovacion").selectedIndex = 0
                }
            }
            
            const registraOperacion = async (e) => {
                if (!await valida_MCM_Complementos()) return
                 
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                 
                limpiaMontos(datos, ["saldoActual", "montoOperacion", "saldoFinal"])
                addParametro(datos, "sucursal", sucursal_ahorro)
                addParametro(datos, "ejecutivo", usuario_ahorro)
                 
                datos.push({ name: "tasa", value: document.querySelector("#plazo").value })
                 
                const plazo = document.querySelector("#plazo")
                confirmarMovimiento(
                    "Apertura de cuenta de inversión",
                    "¿Está segur(a) de continuar con la apertura de la cuenta de inversión por la cantidad de "
                    + parseaNumero(document.querySelector("#montoOperacion").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    + " (" + document.querySelector("#monto_letra").value + ")" 
                    + " a un plazo de " + plazo.options[plazo.selectedIndex].text + "?"
                ).then((continuar) => {
                    if (!continuar) return
                    if (huellas > 0) return showHuella(true, datos)
                    enviaRegistroOperacion(datos)
                })
            }

            const enviaRegistroOperacion = (datos) => {
                consultaServidor("/Ahorro/RegistraInversion/", $.param(datos), (respuesta) => {
                    if (!respuesta.success){
                        console.log(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                    showSuccess(respuesta.mensaje).then(() => {
                        imprimeContrato(respuesta.datos.codigo, 2)
                        imprimeTicket(respuesta.datos.ticket, sucursal_ahorro)
                        limpiaDatosCliente()
                    })
                })
            }
             
            const validaBlur = (e) => {
                const monto = parseaNumero(e.target.value)
                 
                if (monto < saldoMinimoApertura) {
                    e.target.value = ""
                    return showError("El monto mínimo de apertura es de " + saldoMinimoApertura.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' }))
                }
            }
        </script>
        html;

        $sucursales = CajaAhorroDao::GetSucursalAsignadaCajeraAhorro($this->__usuario);
        $opcSucursales = "";
        foreach ($sucursales as $sucursales) {
            $opcSucursales .= "<option value='{$sucursales['CODIGO']}'>{$sucursales['NOMBRE']}</option>";
            $suc_eje = $sucursales['CODIGO'];
        }

        $ejecutivos = CajaAhorroDao::GetEjecutivosSucursal($suc_eje);
        $opcEjecutivos = "";
        foreach ($ejecutivos as $ejecutivos) {
            $opcEjecutivos .= "<option value='{$ejecutivos['ID_EJECUTIVO']}'>{$ejecutivos['EJECUTIVO']}</option>";
        }
        $opcEjecutivos .= "<option value='{$this->__usuario}'>{$this->__nombre} - CAJER(A)</option>";

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Contrato Inversión", [$this->swal2, $this->huellas])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('d/m/Y H:i:s'));
        view::set('ejecutivos', $opcEjecutivos);
        View::render("caja_menu_contrato_inversion");
    }

    public function RegistraInversion()
    {
        $contrato = CajaAhorroDao::RegistraInversion($_POST);
        echo $contrato;
    }

    // Visualización de cuentas de inversión
    public function ConsultaInversion()
    {
        $extraFooter = <<<html
        <script>
            const noSucursal = "{$_SESSION['cdgco_ahorro']}"
         
            {$this->mensajes}
            {$this->sinContrato}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->soloNumeros}
            {$this->primeraMayuscula}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->showHuella}
            {$this->validaHuella}
             
            $(document).ready(configuraTabla("muestra-cupones"))
             
            const llenaDatosCliente = (datosCliente) => {
                consultaServidor("/Ahorro/GetInversiones/", { contrato: datosCliente.CONTRATO }, (respuesta) => {
                    if (!respuesta.success) return showError(respuesta.mensaje)
                    const inversiones = respuesta.datos
                    if (!inversiones) return
                    let inversionesTotal = 0
                    
                    const tTMP = $("#muestra-cupones").DataTable()
                    if (tTMP) tTMP.destroy()
                    
                    const filas = document.createDocumentFragment()
                    inversiones.forEach((inversion) => {
                        const fila = document.createElement("tr")
                        Object.keys(inversion).forEach((key) => {
                            let dato = inversion[key]
                            if (["RENDIMIENTO", "MONTO"].includes(key))
                                dato = parseFloat(dato).toLocaleString("es-MX", {
                                    style: "currency",
                                    currency: "MXN"
                                })
            
                            inversionesTotal += key === "MONTO" ? parseFloat(inversion[key]) : 0
                            const celda = document.createElement("td")
                            celda.innerText = dato
                            fila.appendChild(celda)
                        })
                        filas.appendChild(fila)
                    })
                    
                    document.querySelector("#datosTabla").appendChild(filas)
                    document.querySelector("#inversion").value = inversionesTotal.toLocaleString("es-MX", {
                        style: "currency",
                        currency: "MXN"
                    })
                    document.querySelector("#cliente").value = datosCliente.CDGCL
                    document.querySelector("#contrato").value = datosCliente.CONTRATO
                    document.querySelector("#nombre").value = datosCliente.NOMBRE
                    document.querySelector("#curp").value = datosCliente.CURP
                    configuraTabla("muestra-cupones")
                }, "GET")
            }
                 
            const limpiaDatosCliente = () => {
                const tTMP = $("#muestra-cupones").DataTable()
                if (tTMP) tTMP.destroy()
                document.querySelector("#datosTabla").innerHTML = ""
                document.querySelector("#cliente").value = ""
                document.querySelector("#contrato").value = ""
                document.querySelector("#inversion").value = ""
                document.querySelector("#nombre").value = ""
                document.querySelector("#curp").value = ""
                configuraTabla("muestra-cupones")
            }
        </script>
        html;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Consulta Inversiones", [$this->swal2, $this->huellas])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_estatus_inversion");
    }

    public function GetInversiones()
    {
        $inversiones = CajaAhorroDao::GetInversiones($_GET);
        echo $inversiones;
    }

    //********************CUENTA PEQUES********************//
    // Apertura de contratos para cuentas de ahorro Peques
    public function ContratoCuentaPeque()
    {
        $extraFooter = <<<html
        <script>
            window.onload = () => {
                if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }
        
            const noSucursal = "{$_SESSION['cdgco_ahorro']}"
            let valKD = false
             
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->muestraPDF}
            {$this->imprimeContrato}
            {$this->addParametro}
            {$this->consultaServidor}
            {$this->showHuella}
            {$this->validaHuella}
             
            const buscaCliente = () => {
                const noCliente = document.querySelector("#clienteBuscado")
                 
                if (!noCliente.value) {
                    limpiaDatosCliente()
                    return showError("Ingrese un número de cliente a buscar.")
                }
                
                consultaServidor("/Ahorro/BuscaClientePQ/", { cliente: noCliente.value }, (respuesta) => {
                    if (!respuesta.success) {
                        if (respuesta.datos) {
                            const datosCliente = respuesta.datos
                            if (datosCliente["NO_CONTRATOS"] == 0) {
                                swal({
                                    title: "Cuenta de ahorro Peques™",
                                    text: "El cliente " + noCliente.value + " no tiene una cuenta de ahorro.\\nDesea aperturar una cuenta de ahorro en este momento?",
                                    icon: "info",
                                    buttons: ["No", "Sí"],
                                    dangerMode: true
                                }).then((abreCta) => {
                                    if (abreCta) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente.value
                                })
                                return
                            }
                            if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 0) {
                                swal({
                                    title: "Cuenta de ahorro Peques™",
                                    text: "El cliente " + noCliente.value + " no ha completado el proceso de apertura de la cuenta de ahorro.\\nDesea completar el proceso en este momento?",
                                    icon: "info",
                                    buttons: ["No", "Sí"],
                                    dangerMode: true
                                }).then((abreCta) => {
                                    if (abreCta) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente.value
                                })
                                return
                            }
                        }
                            
                        limpiaDatosCliente()
                        return showError(respuesta.mensaje)
                    }
                        
                    const datosCliente = respuesta.datos
                     
                    document.querySelector("#nombre1").disabled = false
                    document.querySelector("#nombre2").disabled = false
                    document.querySelector("#apellido1").disabled = false
                    document.querySelector("#apellido2").disabled = false
                    document.querySelector("#fecha_nac").disabled = false
                    document.querySelector("#ciudad").disabled = false
                    document.querySelector("#curp").disabled = false
                        
                    document.querySelector("#fechaRegistro").value = datosCliente.FECHA_REGISTRO
                    document.querySelector("#noCliente").value = noCliente.value
                    document.querySelector("#nombre").value = datosCliente.NOMBRE
                    document.querySelector("#direccion").value = datosCliente.DIRECCION
                    noCliente.value = ""
                })
            }
             
            const limpiaDatosCliente = () => {
                document.querySelector("#registroInicialAhorro").reset()
                 
                document.querySelector("#fechaRegistro").value = ""
                document.querySelector("#noCliente").value = ""
                document.querySelector("#nombre").value = ""
                document.querySelector("#curp").value = ""
                document.querySelector("#edad").value = ""
                document.querySelector("#direccion").value = ""
                 
                document.querySelector("#nombre1").disabled = true
                document.querySelector("#nombre2").disabled = true
                document.querySelector("#apellido1").disabled = true
                document.querySelector("#apellido2").disabled = true
                document.querySelector("#fecha_nac").disabled = true
                document.querySelector("#ciudad").disabled = true
                document.querySelector("#curp").disabled = true
                document.querySelector("#btnGeneraContrato").disabled = true
            }
            
            const generaContrato = async (e) => {
                e.preventDefault()
                 
                if (document.querySelector("#curp").value.length !== 18) {
                    showError("La CURP debe tener 18 caracteres.")
                    return
                }
                 
                if (document.querySelector("#edad").value > 17) {
                    showError("El peque a registrar debe tener menos de 18 años.")
                    return 
                }
                 
                if (document.querySelector("#apellido2").value === "") {
                    const respuesta = await swal({
                        title: "Cuenta de ahorro Peques™",
                        text: "No se ha capturado el segundo apellido.\\n¿Desea continuar con el registro?",
                        icon: "info",
                        buttons: ["No", "Sí"]
                    })
                    if (!respuesta) return
                }
                 
                const cliente = document.querySelector("#nombre").value
                 
                confirmarMovimiento("Cuenta de ahorro Peques™",
                    "¿Está segura de continuar con la apertura de la cuenta Peques™ asociada al cliente "
                    + cliente
                    + "?"
                ).then((continuar) => {
                    if (!continuar) return
                    const noCredito = document.querySelector("#noCliente").value
                    const datos = $("#registroInicialAhorro").serializeArray()
                    addParametro(datos, "credito", noCredito)
                    addParametro(datos, "sucursal", noSucursal)
                    addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                    addParametro(datos, "tasa", document.querySelector("#tasa").value)
                    
                    datos.forEach((dato) => {
                        if (dato.name === "sexo") {
                            dato.value = document.querySelector("#sexoH").checked
                        }
                    })
                    
                    consultaServidor("/Ahorro/AgregaContratoAhorroPQ/", $.param(datos), (respuesta) => {
                        if (!respuesta.success) {
                            console.error(respuesta.error)
                            limpiaDatosCliente()
                            return showError(respuesta.mensaje)
                        }
                    
                        const contrato = respuesta.datos
                        limpiaDatosCliente()
                        showSuccess("Se ha generado el contrato: " + contrato.contrato).then(() => {
                            imprimeContrato(contrato.contrato, 3)
                        })
                    })
                })
            }
             
            const validaDeposito = (e) => {
                if (!valKD) return
                 
                const monto = parseFloat(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    showError("El monto a depositar debe ser mayor a 0")
                }
                 
                if (monto > 1000000) {
                    e.preventDefault()
                    e.target.value = 1000000.00
                }
                 
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                document.querySelector("#deposito_inicial_letra").value = numeroLetras(parseFloat(e.target.value))
                calculaSaldoFinal(e)
            }
             
            const calculaSaldoFinal = (e) => {
                const monto = parseFloat(e.target.value)
                document.querySelector("#deposito").value = monto.toFixed(2)
                const saldoInicial = (monto - parseFloat(document.querySelector("#inscripcion").value)).toFixed(2)
                document.querySelector("#saldo_inicial").value = saldoInicial > 0 ? saldoInicial : "0.00"
                document.querySelector("#deposito_inicial_letra").value = primeraMayuscula(numeroLetras(monto))
                    
                if (saldoInicial < saldoMinimoApertura) {
                    document.querySelector("#saldo_inicial").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#registraDepositoInicial").disabled = true
                } else {
                    document.querySelector("#saldo_inicial").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                    document.querySelector("#registraDepositoInicial").disabled = false
                }
            }
             
            const iniveCambio = (e) => e.preventDefault()
             
            const camposLlenos = (e) => {
                document.querySelector("#nombre1").value = document.querySelector("#nombre1").value.toUpperCase()
                document.querySelector("#nombre2").value = document.querySelector("#nombre2").value.toUpperCase()
                document.querySelector("#apellido1").value = document.querySelector("#apellido1").value.toUpperCase()
                document.querySelector("#apellido2").value = document.querySelector("#apellido2").value.toUpperCase()
                 
                const val = () => {
                    const campos = [
                        document.querySelector("#nombre1").value,
                        document.querySelector("#apellido1").value,
                        document.querySelector("#fecha_nac").value,
                        document.querySelector("#ciudad").value,
                        document.querySelector("#curp").value,
                        document.querySelector("#edad").value,
                        document.querySelector("#direccion").value,
                        document.querySelector("#confirmaDir").checked,
                        document.querySelector("#edad").value <= 17
                    ]
                    
                    return campos.every((campo) => campo)
                }
                if (e.target.id === "fecha_nac") calculaEdad(e)
                if (e.target.id !== "curp") generaCURP({
                    nombre1: document.querySelector("#nombre1").value,
                    nombre2: document.querySelector("#nombre1").value,
                    apellido1: document.querySelector("#apellido1").value,
                    apellido2: document.querySelector("#apellido2").value,
                    fecha: document.querySelector("#fecha_nac").value,
                    sexo: document.querySelector("#sexoH").checked ? "H" : "M",
                    entidad: document.querySelector("#ciudad").value
                })
                document.querySelector("#btnGeneraContrato").disabled = !val()
            }
             
            const calculaEdad = (e) => {
                const fecha = new Date(e.target.value)
                const hoy = new Date()
                let edad = hoy.getFullYear() - fecha.getFullYear()
                 
                const mesActual = hoy.getMonth()
                const diaActual = hoy.getDate()
                const mesNacimiento = fecha.getMonth()
                const diaNacimiento = fecha.getDate()
                if (mesActual < mesNacimiento || (mesActual === mesNacimiento && diaActual < diaNacimiento)) edad--
                 
                document.querySelector("#edad").value = edad
                if (edad > 17) {
                    document.querySelector("#edad").setAttribute("style", "color: red")
                    showError("El peque a registrar debe tener menos de 18 años.")
                } else document.querySelector("#edad").removeAttribute("style")
            }
             
            const generaCURP = (datos) => {
                datos.apellido1 = datos.apellido1.toUpperCase()
                datos.apellido2 = datos.apellido2.toUpperCase()
                datos.nombre1 = datos.nombre1.toUpperCase()
                datos.nombre2 = datos.nombre2.toUpperCase()
                 
                const CURP = []
                CURP[0] = datos.apellido1 ? datos.apellido1.charAt(0) : "X"
                CURP[1] = datos.apellido1 ? datos.apellido1.slice(1).replace(/\a\e\i\o\u/gi, "").charAt(0) : "X"
                CURP[2] = datos.apellido2 ? datos.apellido2.charAt(0) : "X"
                CURP[3] = datos.nombre1 ? datos.nombre1.charAt(0) : "X"
                CURP[4] = datos.fecha ? datos.fecha.slice(2, 4) : "00"
                CURP[5] = datos.fecha ? datos.fecha.slice(5, 7) : "00"
                CURP[6] = datos.fecha ? datos.fecha.slice(8, 10) : "00"
                CURP[7] = datos.sexo ? datos.sexo : "X"
                CURP[8] = datos.entidad ? datos.entidad : "NE"
                CURP[9] = datos.apellido1 ? datos.apellido1.slice(1).replace(/[aeiou]/gi, "").charAt(0) : "X"
                CURP[10] = datos.apellido2 ? datos.apellido2.slice(1).replace(/[aeiou]/gi, "").charAt(0) : "X"
                CURP[11] = datos.nombre1 ? datos.nombre1.slice(1).replace(/[aeiou]/gi, "").charAt(0) : "X"
                CURP[12] = "00"
                
                document.querySelector("#curp").value = CURP.join("")
            }
        </script>
        html;

        $ComboEntidades = CajaAhorroDao::GetEFed();

        $opciones_ent = "";
        foreach ($ComboEntidades as $key => $val2) {
            $opciones_ent .= <<<html
                <option  value="{$val2['CDGCURP']}"> {$val2['NOMBRE']}</option>
            html;
        }

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);
        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Contrato Cuenta Peque", [$this->swal2, $this->huellas])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::set('opciones_ent', $opciones_ent);
        View::render("caja_menu_contrato_peque");
    }

    public function BuscaClientePQ()
    {
        if (self::ValidaHorario()) {
            echo CajaAhorroDao::BuscaClienteNvoContratoPQ($_POST);
            return;
        }
        echo self::FueraHorario();
    }

    public function AgregaContratoAhorroPQ()
    {
        $contrato = CajaAhorroDao::AgregaContratoAhorroPQ($_POST);
        echo $contrato;
    }

    public function BuscaContratoPQ()
    {
        if (self::ValidaHorario()) {
            echo CajaAhorroDao::BuscaClienteContratoPQ($_POST);
            return;
        }
        echo self::FueraHorario();
    }

    // Movimientos sobre cuentas de ahorro Peques
    public function CuentaPeque()
    {
        $maximoRetiroDia = 50000;
        $montoMaximoRetiro = 1000000;

        $extraFooter = <<<html
        <script>
            const noSucursal = "{$_SESSION['cdgco_ahorro']}"
            const maximoRetiroDia = $maximoRetiroDia
            const montoMaximoRetiro = $montoMaximoRetiro
            let retiroDispobible = maximoRetiroDia
            let retiroBloqueado = false
            let valKD = false
            let huellas = 0
            let mano
         
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->addParametro}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->limpiaMontos}
            {$this->consultaServidor}
            {$this->showBloqueo}
            {$this->validaHorarioOperacion}
            {$this->valida_MCM_Complementos}
            {$this->showHuella}
            {$this->validaHuella}
            {$this->autorizaOperacion}
         
            window.onload = () => {
                validaHorarioOperacion("{$_SESSION['inicio']}", "{$_SESSION['fin']}")
                if (document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }
            
            const buscaCliente = () => {
                retiroBloqueado = false
                const noCliente = document.querySelector("#clienteBuscado").value
                
                if (!noCliente) {
                    limpiaDatosCliente()
                    return showError("Ingrese un número de cliente a buscar.")
                }
                 
                consultaServidor("/Ahorro/BuscaContratoPQ/", { cliente: noCliente }, (respuesta) => {
                    limpiaDatosCliente()
                    if (!respuesta.success) {
                        if (!respuesta.datos) return showError(respuesta.mensaje)
                        const datosCliente = respuesta.datos
                            
                        if (datosCliente["NO_CONTRATOS"] == 0) {
                            swal({
                                title: "Cuenta de ahorro Peques™",
                                text: "La cuenta " + noCliente + " no tiene una cuenta de ahorro.\\nDesea realizar la apertura en este momento?",
                                icon: "info",
                                buttons: ["No", "Sí"],
                                dangerMode: true
                            }).then((realizarDeposito) => {
                                if (realizarDeposito) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente
                            })
                            return
                        }
                        if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 0) {
                            swal({
                                title: "Cuenta de ahorro Peques™",
                                text: "La cuenta " + noCliente + " no ha concluido con el proceso de apertura de la cuenta de ahorro.\\nDesea completar el contrato en este momento?",
                                icon: "info",
                                buttons: ["No", "Sí"],
                                dangerMode: true
                            }).then((realizarDeposito) => {
                                if (realizarDeposito) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente
                            })
                        }
                        if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 1) {
                            swal({
                                title: "Cuenta de ahorro Peques™",
                                text: "La cuenta " + noCliente + " no tiene asignadas cuentas Peques™.\\nDesea aperturar una cuenta Peques™ en este momento?",
                                icon: "info",
                                buttons: ["No", "Sí"],
                                dangerMode: true
                            }).then((realizarDeposito) => {
                                if (realizarDeposito) return window.location.href = "/Ahorro/ContratoCuentaPeque/?cliente=" + noCliente
                            })
                            return
                        }
                    }
                 
                    if (respuesta.datos[0].SUCURSAL !== noSucursal) {
                        limpiaDatosCliente()
                        return showError("El cliente " + noCliente + " no puede realizar transacciones en esta sucursal, su contrato esta asignado a la sucursal " + respuesta.datos[0].NOMBRE_SUCURSAL + ", contacte a la gerencia de Administración.")
                    }
                     
                    const datosCliente = respuesta.datos
                    const contratos = document.createDocumentFragment()
                    const seleccionar = document.createElement("option")
                    seleccionar.value = ""
                    seleccionar.disabled = true
                    seleccionar.innerText = "Seleccionar"
                    contratos.appendChild(seleccionar)
                        
                    datosCliente.forEach(cliente => {
                        const opcion = document.createElement("option")
                        opcion.value = cliente.CDG_CONTRATO
                        opcion.innerText = cliente.NOMBRE
                        contratos.appendChild(opcion)
                        huellas = cliente.HUELLAS
                    })
                        
                    document.querySelector("#contrato").appendChild(contratos)
                    if (document.querySelector("#contrato").options.length == 2) {
                        document.querySelector("#contrato").selectedIndex = 1
                        pqSeleccionado(datosCliente, document.querySelector("#contrato").value)
                    } else {
                        document.querySelector("#contrato").selectedIndex = 0
                        document.querySelector("#contrato").addEventListener("change", (e) => {
                            pqSeleccionado(datosCliente, e.target.value)
                        })
                    }
                    
                    if (document.querySelector("#contratoSel").value !== "") {
                        document.querySelector("#contrato").selectedIndex = document.querySelector("#contratoSel").value
                        document.querySelector("#contrato").dispatchEvent(new Event("change"))
                        document.querySelector("#retiro").checked = true
                        document.querySelector("#retiro").dispatchEvent(new Event("change"))
                    }
                    
                    document.querySelector("#clienteBuscado").value = ""
                    document.querySelector("#contrato").disabled = false
                })
            }
             
            const pqSeleccionado = (datosCliente, pq) => {
                retiroDispobible = maximoRetiroDia
                retiroBloqueado = false
                datosCliente.forEach(contrato => {
                    if (contrato.CDG_CONTRATO == pq) {
                        consultaServidor("/Ahorro/ValidaRetirosDia/", $.param({ contrato: contrato.CDG_CONTRATO }), (respuesta) => {
                            if (!respuesta.success && respuesta.datos.RETIROS >= maximoRetiroDia) {
                                showWarning("El peque " + contrato.NOMBRE + " ha alcanzado el límite de retiros diarios.")
                                retiroBloqueado = true   
                                retiroDispobible = maximoRetiroDia - respuesta.datos.RETIROS
                            }
                             
                            document.querySelector("#nombre").value = contrato.CDG_CONTRATO
                            document.querySelector("#curp").value = contrato.CURP
                            document.querySelector("#cliente").value = contrato.CDGCL
                            document.querySelector("#saldoActual").value = formatoMoneda(contrato.SALDO)
                            document.querySelector("#deposito").disabled = false
                            document.querySelector("#retiro").disabled = retiroBloqueado
                        })
                    }
                })
            }
             
            const limpiaDatosCliente = () => {
                huellas = 0
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#fecha_pago").value = getHoy()
                document.querySelector("#monto").disabled = true
                document.querySelector("#deposito").disabled = true
                document.querySelector("#retiro").disabled = true
                document.querySelector("#contrato").innerHTML = ""
                document.querySelector("#contrato").disabled = true
            }
             
            const boton_contrato = (numero_contrato) => {
                const host = window.location.origin
                
                let plantilla = "<!DOCTYPE html>"
                plantilla += '<html lang="es">'
                plantilla += '<head>'
                plantilla += '<meta charset="UTF-8">'
                plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
                plantilla += '<link rel="shortcut icon" href="' + host + '/img/logo.png">'
                plantilla += '<title>Contrato ' + numero_contrato + '</title>'
                plantilla += '</head>'
                plantilla += '<body style="margin: 0; padding: 0; background-color: #333333;">'
                plantilla +=
                    '<iframe src="' + host + '/Ahorro/ImprimeContrato/' +
                    numero_contrato +
                    '/" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
                plantilla += "</body>"
                plantilla += "</html>"
            
                const blob = new Blob([plantilla], { type: "text/html" })
                const url = URL.createObjectURL(blob)
                window.open(url, "_blank")
            }
             
            const validaDeposito = (e) => {
                if (!valKD) return
                 
                let monto = parseaNumero(e.target.value) || 0
                if (monto <= 0) {
                    e.preventDefault()
                    e.target.value = ""
                    showError("El monto a depositar debe ser mayor a 0")
                }
                 
                if (!document.querySelector("#deposito").checked && monto > montoMaximoRetiro) {
                    monto = montoMaximoRetiro
                    swal({
                        title: "Cuenta de ahorro Peques™",
                        text: "Para retiros mayores a " + montoMaximoRetiro.toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + " es necesario realizar una solicitud de retiro.\\nDesea generar una solicitud de retiro ahora?.",
                        icon: "info",
                        buttons: ["No", "Sí"],
                        dangerMode: true
                    }).then((regRetiro) => {
                        if (regRetiro) {
                            window.location.href = "/Ahorro/SolicitudRetiroCuentaPeque/?cliente=" + document.querySelector("#cliente").value + "&contrato=" + document.querySelector("#contrato").selectedIndex
                            return
                        }
                    })
                    e.target.value = monto
                }
                 
                if (monto > 1000000) {
                    monto = 1000000
                    e.preventDefault()
                    e.target.value = 1000000.00
                }
                 
                const valor = e.target.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    e.preventDefault()
                    e.target.value = parseaNumero(valor[0] + "." + valor[1].substring(0, 2))
                }
                
                document.querySelector("#monto_letra").value = numeroLetras(parseaNumero(e.target.value))
                if (document.querySelector("#deposito").checked || document.querySelector("#retiro").checked) calculaSaldoFinal()
            }
             
            const calculaSaldoFinal = () => {
                const esDeposito = document.querySelector("#deposito").checked
                const saldoActual = parseaNumero(document.querySelector("#saldoActual").value)
                const monto = parseaNumero(document.querySelector("#monto").value)
                document.querySelector("#montoOperacion").value = formatoMoneda(monto)
                document.querySelector("#saldoFinal").value = formatoMoneda(esDeposito ? saldoActual + monto : saldoActual - monto)
                compruebaSaldoFinal(document.querySelector("#saldoFinal").value)
            }
             
            const cambioMovimiento = (e) => {
                document.querySelector("#monto").disabled = false
                const esDeposito = document.querySelector("#deposito").checked
                document.querySelector("#simboloOperacion").innerText = esDeposito ? "+" : "-"
                document.querySelector("#descOperacion").innerText = (esDeposito ? "Depósito" : "Retiro") + " a cuenta ahorro corriente"
                calculaSaldoFinal()
            }
             
            const compruebaSaldoFinal = () => {
                const saldoFinal = parseaNumero(document.querySelector("#saldoFinal").value)
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#tipSaldo").innerText = "El monto a retirar no puede ser mayor al saldo de la cuenta."
                    document.querySelector("#btnRegistraOperacion").disabled = true
                    return
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                if (document.querySelector("#retiro").checked && retiroDispobible < parseaNumero(document.querySelector("#montoOperacion").value)) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#tipSaldo").innerText = "El monto a retirar excede el límite de retiros diarios, disponible para retirar el día de hoy: " + retiroDispobible.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    document.querySelector("#btnRegistraOperacion").disabled = true
                    return
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(saldoFinal >= 0 && parseaNumero(document.querySelector("#montoOperacion").value) > 0)
            }
             
            const registraOperacion = async (e) => {
                if (!await valida_MCM_Complementos()) return
                 
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                 
                limpiaMontos(datos, ["saldoActual", "montoOperacion", "saldoFinal"])
                addParametro(datos, "sucursal", noSucursal)
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                addParametro(datos, "producto", "cuenta de ahorro Peques")
                 
                if (!document.querySelector("#deposito").checked && !document.querySelector("#retiro").checked) {
                    return showError("Seleccione el tipo de operación a realizar.")
                }
                
                datos.forEach((dato) => {
                    if (dato.name === "esDeposito") {
                        dato.value = document.querySelector("#deposito").checked
                    }
                })
                 
                confirmarMovimiento(
                    "Confirmación de movimiento de cuenta ahorro Peques™",
                    "¿Está segur(a) de continuar con el registro de un "
                    + (document.querySelector("#deposito").checked ? "depósito" : "retiro")
                    + " de cuenta ahorro peque, por la cantidad de "
                    + parseaNumero(document.querySelector("#montoOperacion").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    + " (" + document.querySelector("#monto_letra").value + ")?"
                ).then((continuar) => {
                    if (!continuar) return
                    if (!document.querySelector("#deposito").checked && huellas > 0) return showHuella(true, datos)
                    enviaRegistroOperacion(datos)
                })
            }

            const enviaRegistroOperacion = (datos) => {
                consultaServidor("/Ahorro/registraOperacion/", $.param(datos), (respuesta) => {
                    if (!respuesta.success){
                        if (respuesta.error) return showError(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                    showSuccess(respuesta.mensaje).then(() => {
                        imprimeTicket(respuesta.datos.ticket, noSucursal)
                        limpiaDatosCliente()
                    })
                })
            }
        </script>
        html;

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);
        if ($_GET['contrato']) View::set('contratoSel', $_GET['contrato']);

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Cuenta Peque", [$this->swal2, $this->huellas])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("caja_menu_peque");
    }

    public function SolicitudRetiroCuentaPeque()
    {
        $montoMinimoRetiro = 10000;
        $montoMaximoExpress = 49999.99;
        $montoMaximoRetiro = 1000000;

        $extraFooter = <<<html
        <script>
            window.onload = () => {
                if(document.querySelector("#clienteBuscado").value !== "") buscaCliente()
            }
            
            const noSucursal = "{$_SESSION['cdgco_ahorro']}"
            const montoMinimoRetiro = $montoMinimoRetiro
            const montoMaximoExpress = $montoMaximoExpress
            const montoMaximoRetiro = $montoMaximoRetiro
            let valKD = false
            let huellas = 0
            let mano
         
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->validarYbuscar}
            {$this->soloNumeros}
            {$this->primeraMayuscula}
            {$this->numeroLetras}
            {$this->muestraPDF}
            {$this->addParametro}
            {$this->sinContrato}
            {$this->getHoy}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->limpiaMontos}
            {$this->consultaServidor}
            {$this->showHuella}
            {$this->validaHuella}
            {$this->autorizaOperacion}
             
            const buscaCliente = () => {
                const noCliente = document.querySelector("#clienteBuscado").value
                
                if (!noCliente) {
                    limpiaDatosCliente()
                    return showError("Ingrese un número de cliente a buscar.")
                }
                 
                consultaServidor("/Ahorro/BuscaContratoPQ/", { cliente: noCliente }, (respuesta) => {
                    limpiaDatosCliente()
                    if (!respuesta.success) {
                        if (!respuesta.datos) return showError(respuesta.mensaje)
                        const datosCliente = respuesta.datos
                            
                        if (datosCliente["NO_CONTRATOS"] == 0) {
                            swal({
                                title: "Cuenta de ahorro Peques™",
                                text: "La cuenta " + noCliente + " no tiene una cuenta de ahorro.\\nDesea realizar la apertura en este momento?",
                                icon: "info",
                                buttons: ["No", "Sí"],
                                dangerMode: true
                            }).then((realizarDeposito) => {
                                if (realizarDeposito) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente
                            })
                            return
                        }
                        if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 0) {
                            swal({
                                title: "Cuenta de ahorro Peques™",
                                text: "La cuenta " + noCliente + " no ha concluido con el proceso de apertura de la cuenta de ahorro.\\nDesea completar el contrato en este momento?",
                                icon: "info",
                                buttons: ["No", "Sí"],
                                dangerMode: true
                            }).then((realizarDeposito) => {
                                if (realizarDeposito) return window.location.href = "/Ahorro/ContratoCuentaCorriente/?cliente=" + noCliente
                            })
                        }
                        if (datosCliente["NO_CONTRATOS"] == 1 && datosCliente["CONTRATO_COMPLETO"] == 1) {
                            swal({
                                title: "Cuenta de ahorro Peques™",
                                text: "La cuenta " + noCliente + " no tiene asignadas cuentas Peques™.\\nDesea aperturar una cuenta Peques™ en este momento?",
                                icon: "info",
                                buttons: ["No", "Sí"],
                                dangerMode: true
                            }).then((realizarDeposito) => {
                                if (realizarDeposito) return window.location.href = "/Ahorro/ContratoCuentaPeque/?cliente=" + noCliente
                            })
                            return
                        }
                    }
                 
                    if (respuesta.datos[0].SUCURSAL !== noSucursal) {
                        limpiaDatosCliente()
                        return showError("El cliente " + noCliente + " no puede realizar transacciones en esta sucursal, su contrato esta asignado a la sucursal " + respuesta.datos[0].NOMBRE_SUCURSAL + ", contacte a la gerencia de Administración.")
                    }
                     
                    const datosCliente = respuesta.datos
                    const contratos = document.createDocumentFragment()
                    const seleccionar = document.createElement("option")
                    seleccionar.value = ""
                    seleccionar.disabled = true
                    seleccionar.innerText = "Seleccionar"
                    contratos.appendChild(seleccionar)
                        
                    datosCliente.forEach(cliente => {
                        hue = cliente.HUELLAS
                        const opcion = document.createElement("option")
                        opcion.value = cliente.CDG_CONTRATO
                        opcion.innerText = cliente.NOMBRE
                        contratos.appendChild(opcion)
                    })
                        
                    document.querySelector("#contrato").appendChild(contratos)
                    document.querySelector("#contrato").selectedIndex = 0
                    document.querySelector("#contrato").disabled = false
                    document.querySelector("#contrato").addEventListener("change", (e) => {
                        datosCliente.forEach(contrato => {
                            if (contrato.CDG_CONTRATO == e.target.value) {
                                document.querySelector("#nombre").value = contrato.CDG_CONTRATO
                                document.querySelector("#curp").value = contrato.CURP
                                document.querySelector("#cliente").value = contrato.CDGCL
                                document.querySelector("#saldoActual").value = formatoMoneda(contrato.SALDO)
                                document.querySelector("#express").disabled = false
                                document.querySelector("#programado").disabled = false
                                document.querySelector("#monto").disabled = !(contrato.SALDO > montoMinimoRetiro)
                                if (contrato.SALDO < montoMinimoRetiro) {
                                    swal({
                                        title: "Retiro de cuenta corriente peques™",
                                        text: "El saldo actual de la cuenta del Peque es menor al monto mínimo para retiros express.\\n¿Desea realizar un retiro simple?",
                                        icon: "info",
                                        buttons: ["No", "Sí"]
                                    }).then((retSimple) => {
                                        if (retSimple) {
                                            window.location.href = "/Ahorro/CuentaPeque/?cliente=" + document.querySelector("#cliente").value + "&contrato=" + e.target.selectedIndex
                                            return
                                        }
                                    })
                                }
                                
                            }
                        })
                    })
                    
                    if (document.querySelector("#contratoSel").value !== "") {
                        document.querySelector("#contrato").selectedIndex = document.querySelector("#contratoSel").value
                        document.querySelector("#contrato").dispatchEvent(new Event("change"))
                    }
                    document.querySelector("#clienteBuscado").value = ""
                })
            }
             
            const limpiaDatosCliente = () => {
                huellas = 0
                document.querySelector("#registroOperacion").reset()
                document.querySelector("#fecha_retiro").value = getHoy()
                document.querySelector("#monto").disabled = true
                document.querySelector("#express").disabled = true
                document.querySelector("#programado").disabled = true
                document.querySelector("#contrato").innerHTML = ""
                document.querySelector("#contrato").disabled = true
                document.querySelector("#monto").disabled = true
            }
             
            const validaMonto = () => {
                document.querySelector("#express").disabled = false
                const montoIngresado = document.querySelector("#monto")
                 
                let monto = parseaNumero(montoIngresado.value) || 0
                 
                if (monto > montoMaximoExpress) {
                    document.querySelector("#programado").checked = true
                    document.querySelector("#express").disabled = true
                    cambioMovimiento()
                }
                 
                if (monto > montoMaximoRetiro) {
                    monto = montoMaximoRetiro
                    montoIngresado.value = monto
                }
                                  
                document.querySelector("#monto_letra").value = primeraMayuscula(numeroLetras(monto))
                const saldoActual = parseaNumero(document.querySelector("#saldoActual").value)
                document.querySelector("#montoOperacion").value = formatoMoneda(monto)
                const saldoFinal = (saldoActual - monto)
                compruebaSaldoFinal(saldoFinal)
                document.querySelector("#saldoFinal").value = formatoMoneda(saldoFinal)
            }
             
            const valSalMin = () => {
                const montoIngresado = document.querySelector("#monto")
                 
                let monto = parseFloat(montoIngresado.value) || 0
                 
                if (monto < montoMinimoRetiro) {
                    monto = montoMinimoRetiro
                    swal({
                        title: "Retiro de cuenta corriente",
                        text: "El monto mínimo para retiros express es de " + montoMinimoRetiro.toLocaleString("es-MX", {
                            style: "currency",
                            currency: "MXN"
                        }) + ", para un monto menor debe realizar el retiro de manera simple.\\n¿Desea realizar el retiro de manera simple?",
                        icon: "info",
                        buttons: ["No", "Sí"]
                    }).then((retSimple) => {
                        if (retSimple) {
                            window.location.href = "/Ahorro/CuentaCorriente/?cliente=" + document.querySelector("#cliente").value
                            return
                        }
                    })
                }
            }
             
            const cambioMovimiento = (e) => {
                const express = document.querySelector("#express").checked
                
                if (express) {
                    document.querySelector("#fecha_retiro").removeAttribute("style")
                    document.querySelector("#fecha_retiro_hide").setAttribute("style", "display: none;")
                    document.querySelector("#fecha_retiro").value = getHoy()
                    return
                }
                
                document.querySelector("#fecha_retiro_hide").removeAttribute("style")
                document.querySelector("#fecha_retiro").setAttribute("style", "display: none;")
                pasaFecha({ target: document.querySelector("#fecha_retiro") })
            }
             
            const compruebaSaldoFinal = () => {
                const saldoFinal = parseaNumero(document.querySelector("#saldoFinal").value)
                if (saldoFinal < 0) {
                    document.querySelector("#saldoFinal").setAttribute("style", "color: red")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 100%;")
                    document.querySelector("#btnRegistraOperacion").disabled = true
                    return
                } else {
                    document.querySelector("#saldoFinal").removeAttribute("style")
                    document.querySelector("#tipSaldo").setAttribute("style", "opacity: 0%;")
                }
                document.querySelector("#btnRegistraOperacion").disabled = !(saldoFinal >= 0 && parseaNumero(document.querySelector("#montoOperacion").value) >= montoMinimoRetiro && parseaNumero(document.querySelector("#montoOperacion").value) < montoMaximoRetiro)
            }
             
            const pasaFecha = (e) => {
                const fechaSeleccionada = new Date(e.target.value)
                if (fechaSeleccionada.getDay() === 5 || fechaSeleccionada.getDay() === 6) {
                    showError("No se pueden realizar retiros los fines de semana.")
                    const f = getHoy(false).split("/")
                    e.target.value = f[2] + "-" + f[1] + "-" + f[0]
                    return
                }
                const f = document.querySelector("#fecha_retiro_hide").value.split("-")
                document.querySelector("#fecha_retiro").value = f[2] + "/" + f[1] + "/" + f[0]
            }
             
            const registraSolicitud = (e) => {
                e.preventDefault()
                const datos = $("#registroOperacion").serializeArray()
                
                limpiaMontos(datos, ["saldoActual", "montoOperacion", "saldoFinal"])
                addParametro(datos, "sucursal", "{$_SESSION['cdgco_ahorro']}")
                addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                addParametro(datos, "retiroExpress", document.querySelector("#express").checked)
                 
                confirmarMovimiento(
                    "Confirmación de movimiento ahorro corriente",
                    "¿Está segur(a) de continuar con el registro de un retiro "
                    + (document.querySelector("#express").checked ? "express" : "programado")
                    + ", por la cantidad de "
                    + parseaNumero(document.querySelector("#montoOperacion").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                    + " (" + document.querySelector("#monto_letra").value + ")?"
                ).then((continuar) => {
                    if (!continuar) return
                    if (huellas > 0) return showHuella(true, datos)
                    enviaRegistroOperacion(datos)
                })
            }

            const enviaRegistroOperacion = (datos) => {
                consultaServidor("/Ahorro/RegistraSolicitud/", $.param(datos), (respuesta) => {
                    if (!respuesta.success) {
                        console.log(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                    showSuccess(respuesta.mensaje).then(() => {
                        document.querySelector("#registroOperacion").reset()
                        limpiaDatosCliente()
                    })
                })
            }
        </script>
        html;

        $fechaMax = new DateTime();
        for ($i = 0; $i < 7; $i++) {
            $fechaMax->modify('+1 day');
            if ($fechaMax->format('N') >= 6 || $fechaMax->format('N') === 0) $fechaMax->modify('+1 day');
        }

        if ($_GET['cliente']) View::set('cliente', $_GET['cliente']);
        if ($_GET['contrato']) View::set('contratoSel', $_GET['contrato']);

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Solicitud de Retiro Peque", [$this->swal2, $this->huellas])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('montoMinimoRetiro', $montoMinimoRetiro);
        View::set('montoMaximoExpress', $montoMaximoExpress);
        View::set('montoMaximoRetiro', $montoMaximoRetiro);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::set('fechaInput', date('Y-m-d', strtotime('+1 day')));
        View::set('fechaInputMax', $fechaMax->format('Y-m-d'));
        View::render("caja_menu_retiro_peque");
    }

    public function HistorialSolicitudRetiroCuentaPeque()
    {
        $extraFooter = <<<html
        <script>
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->imprimeTicket}
            {$this->muestraPDF}
            {$this->addParametro}
            {$this->valida_MCM_Complementos}
         
            $(document).ready(() => {
                configuraTabla("hstSolicitudes")
            })
             
            const validaFIF = (idI, idF) => {
                const fechaI = document.getElementById(idI).value
                const fechaF = document.getElementById(idF).value
                if (fechaI && fechaF && fechaI > fechaF) {
                    document.getElementById(idI).value = fechaF
                }
            }
            
            const imprimeExcel = () => exportaExcel("hstSolicitudes", "Historial solicitudes de retiro")
             
            const actualizaEstatus = async (estatus, id) => {
                if (!await valida_MCM_Complementos()) return
                 
                const accion = estatus === 3 ? "entrega" : "cancelación"
                 
                consultaServidor("/Ahorro/ResumenEntregaRetiro", $.param({id}), (respuesta) => {
                    if (!respuesta.success) {
                        console.log(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                     
                    const resumen = respuesta.datos
                    confirmarMovimiento(
                        "Seguimiento solicitudes de retiro",
                        null,
                        resumenRetiro(resumen, accion)
                    ).then((continuar) => {
                        if (!continuar) return
                        const datos = {
                            estatus, 
                            id, 
                            ejecutivo: "{$_SESSION['usuario']}", 
                            sucursal: "{$_SESSION['cdgco_ahorro']}", 
                            monto: resumen.MONTO, 
                            contrato: resumen.CONTRATO,
                            cliente: resumen.CLIENTE,
                            tipo: resumen.TIPO_RETIRO
                        }
                        
                        consultaServidor("/Ahorro/EntregaRetiro/", $.param(datos), (respuesta) => {
                            if (!respuesta.success) {
                                if (respuesta.error) return showError(respuesta.error)
                                return showError(respuesta.mensaje)
                            }
                             
                            showSuccess(respuesta.mensaje).then(() => {
                                if (estatus === 3) {
                                    imprimeTicket(respuesta.datos.CODIGO, "{$_SESSION['cdgco_ahorro']}")
                                    swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                                    window.location.reload()
                                }
                                if (estatus === 4) devuelveRetiro(resumen)
                            })
                        })
                    })
                })
            }
             
            const resumenRetiro = (datos, accion) => {
                const resumen = document.createElement("div")
                resumen.setAttribute("style", "color: rgba(0, 0, 0, .65); text-align: left;")
                
                const tabla = document.createElement("table")
                tabla.setAttribute("style", "width: 100%;")
                tabla.innerHTML = "<thead><tr><th colspan='2' style='font-size: 25px; text-align: center;'>Retiro " + (datos.TIPO_RETIRO == 1 ? "express" : "programado") + "</th></tr></thead>"
                 
                const tbody = document.createElement("tbody")
                tbody.setAttribute("style", "width: 100%;")
                tbody.innerHTML += "<tr><td><strong>Cliente:</strong></td><td style='text-align: center;'>" + datos.NOMBRE + "</td></tr>"
                tbody.innerHTML += "<tr><td><strong>Contrato:</strong></td><td style='text-align: center;'>" + datos.CONTRATO + "</td></tr>"
                tbody.innerHTML += "<tr><td><strong>Monto:</strong></td><td style='text-align: center;'>" + parseFloat(datos.MONTO).toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + "</td></tr>"
                
                const tInterno = document.createElement("table")
                tInterno.setAttribute("style", "width: 100%; margin-top: 20px;")
                const tbodyI = document.createElement("tbody")
                tbodyI.innerHTML += "<tr><td><strong>Autorizado por:</strong></td style='text-align: center;'><td>" + datos.APROBADO_POR + "</td></tr>"
                tbodyI.innerHTML += "<tr><td><strong>A " + (accion === "entrega" ? "entregar" : "cancelar") + " por:</strong></td style='text-align: center;'><td>{$_SESSION['nombre']}</td></tr>"
                tInterno.appendChild(tbodyI)
                 
                const tFechas = document.createElement("table")
                tFechas.setAttribute("style", "width: 100%; margin-top: 20px;")
                const tbodyF = document.createElement("tbody")
                tbodyF.innerHTML += "<tr><td style='text-align: center; width: 50%;'><strong>Fecha entrega solicitada</strong></td><td style='text-align: center; width: 50%;'><strong>Fecha " + (accion === "entrega" ? accion + " real" : accion) + "</strong></td></tr>"
                tbodyF.innerHTML += "<tr><td style='text-align: center; width: 50%;'>" + datos.FECHA_ESPERADA + "</td><td style='text-align: center; width: 50%;'>" + new Date().toLocaleString("es-MX", { day: "2-digit", month: "2-digit", year: "numeric"}) + "</td></tr>"
                tFechas.appendChild(tbodyF)
                 
                tabla.appendChild(tbody)
                resumen.appendChild(tabla)
                resumen.appendChild(tInterno)
                resumen.appendChild(tFechas)
                 
                const pregunta = document.createElement("label")
                pregunta.setAttribute("style", "width: 100%; font-size: 20px; text-align: center; font-weight: bold; margin-top: 20px;")
                pregunta.innerText = "¿Desea continuar con la " + accion + " del retiro?"
                 
                const advertencia = document.createElement("label")
                advertencia.setAttribute("style", "width: 100%; color: red; font-size: 15px; text-align: center;")
                advertencia.innerText = "Esta acción no se puede deshacer."
                 
                resumen.appendChild(pregunta)
                resumen.appendChild(advertencia)
                return resumen
            }
             
            const devuelveRetiro = (datos) => {
                const datosDev = {
                    contrato: datos.CONTRATO,
                    monto: datos.MONTO,
                    ejecutivo: "{$_SESSION['usuario']}",
                    sucursal: "{$_SESSION['cdgco_ahorro']}",
                    tipo: datos.TIPO_RETIRO
                }
                 
                consultaServidor("/Ahorro/DevolucionRetiro/", $.param(datosDev), (respuesta) => {
                    if (!respuesta.success) {
                        console.log(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                     
                    showSuccess(respuesta.mensaje).then(() => {
                        imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco_ahorro']}", false)
                        swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                        window.location.reload()
                    })
                })
            }
             
            const buscar = () => {
                const datos = []
                addParametro(datos, "producto", 2)
                addParametro(datos, "fechaI", document.querySelector("#fechaI").value)
                addParametro(datos, "fechaF", document.querySelector("#fechaF").value)
                addParametro(datos, "estatus", document.querySelector("#estatus").value)
                addParametro(datos, "tipo", document.querySelector("#tipo").value)
                 
                consultaServidor("/Ahorro/HistoricoSolicitudRetiro/", $.param(datos), (respuesta) => {
                    $("#hstSolicitudes").DataTable().destroy()
                     
                    if (respuesta.datos == "") showError("No se encontraron solicitudes de retiro en el rango de fechas seleccionado.")
                     
                    $("#hstSolicitudes tbody").html(respuesta.datos)
                    configuraTabla("hstSolicitudes")
                })
            }
             
            const validaFechaEntrega = (fecha) => showError("La solicitud no está disponible para entrega, la fecha programada de entrega es el " + fecha + ".")
        </script>
        html;

        $tabla = self::HistoricoSolicitudRetiro(2);
        $tabla = $tabla['success'] ? $tabla['datos'] : "";

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial de solicitudes de retiro", [$this->XLSX])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::set('fecha', date('Y-m-d'));
        View::render("caja_menu_solicitud_retiro_peque_historial");
    }

    //******************REPORTE DE SALDO EN CAJA******************//
    // Muestra un reporte para el segimiento de los saldos en caja
    public function SaldosDia()
    {
        $extraFooter = <<<html
        <script>
            {$this->noSubmit}
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->limpiaMontos}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->muestraPDF}
            {$this->validaHorarioOperacion}
         
            $(document).ready(() => configuraTabla("tblArqueos"))
             
            const imprimeExcel = () => exportaExcel("tblArqueos", "Reporte de arqueos de caja al " + getHoy(false))
             
            const mostrarModal = () => {
                validaHorarioOperacion("{$_SESSION['inicio']}", "{$_SESSION['fin']}")
                document.querySelector("#frmModal").reset()
                $("#modalArqueo").modal("show")
                $("#fechaArqueo").val(getHoy())
            }
         
            const calculaTotal = (e) => {
                const id = e.target.id.replace("cant", "")
                if (!id) return
                 
                const maximo = e.target.max
                let cantidad = parseaNumero(e.target.value)
                if (cantidad > maximo) {
                    e.preventDefault()
                    e.target.value = maximo
                    cantidad = maximo
                }
                 
                const valor = parseaNumero(id.substring(0, 1) === "0" ? id.replace("0", ".", 1) : id)
                document.querySelector("#total" + id).value = formatoMoneda(cantidad * valor)
                
                const totalEfectivo = Array.from(document.querySelectorAll(".efectivo")).reduce((total, input) => total + parseaNumero(input.value), 0)
                
                document.querySelector("#totalEfectivo").value = formatoMoneda(totalEfectivo)
                document.querySelector("#btnRegistrarArqueo").disabled = !(totalEfectivo >= 1000)
            }
             
            const registraArqueo = () => {
                const totalEfectivo = parseaNumero(document.querySelector("#totalEfectivo").value)
                if (totalEfectivo < 1000) return showError("El total de efectivo debe ser mayor o igual a $1,000.00")
                
                confirmarMovimiento(
                    "Confirmación de arqueo de caja",
                    null,
                    tablaResumenArqueo(),
                ).then((continuar) => {
                    if (!continuar) return
                     
                    const datos = []
                    addParametro(datos, "sucursal", "{$_SESSION['cdgco_ahorro']}")
                    addParametro(datos, "ejecutivo", "{$_SESSION['usuario']}")
                    addParametro(datos, "monto", totalEfectivo)
                     
                    addCantidades(datos, "billete")
                    addCantidades(datos, "moneda")
                     
                    consultaServidor("/Ahorro/RegistraArqueo/", $.param(datos), (respuesta) => {
                        if (!respuesta.success) {
                            console.log(respuesta.error)
                            return showError(respuesta.mensaje)
                        }
                            
                        showSuccess(respuesta.mensaje).then(() => {
                            const host = window.location.origin
                            const titulo = 'Comprobante arqueo de caja'
                            const ruta = host + '/Ahorro/TicketArqueo/?'
                            + 'sucursal=' + "{$_SESSION['cdgco_ahorro']}"
                            
                            muestraPDF(titulo, ruta)
                             
                            swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                            window.location.reload()
                        })
                    })
                })
            }
                 
            const addCantidades = (datos, tipo) => {
                const t = tipo === "billete" ? "b" : "m"
                 
                Array.from(document.querySelectorAll("." + tipo)).forEach((input) => {
                    const id = input.id.replace("cant", "")
                    if (!id) return
                    const cantidad = parseaNumero(input.value)
                    addParametro(datos, t + "_" + id, cantidad)
                })
            }
             
            const tablaResumenArqueo = () => {
                const tabla = document.createElement("table")
                tabla.setAttribute("style", "width: 100%;")
                const thead = document.createElement("thead")
                const tr0 = document.createElement("tr")
                tr0.style.height = "40px"
                 
                const th0 = document.createElement("th")
                th0.setAttribute("colspan", "3")
                th0.style.textAlign = "center"
                th0.style.fontSize = "25px"
                th0.innerText = "Resumen"
                tr0.appendChild(th0)
                thead.appendChild(tr0)
                 
                const tr1 = document.createElement("tr")
                const th1 = document.createElement("th")
                const th2 = document.createElement("th")
                const th3 = document.createElement("th")
                 
                th1.style.textAlign = "center"
                th2.style.textAlign = "center"
                th3.style.textAlign = "center"
                 
                th1.innerText = "Denominación"
                th2.innerText = "Cantidad"
                th3.innerText = "Total"
                 
                tr1.appendChild(th1)
                tr1.appendChild(th2)
                tr1.appendChild(th3)
                 
                thead.appendChild(tr1)
                tabla.appendChild(thead)
                const tbody = document.createElement("tbody")
                 
                const filasB = Array.from(document.querySelector("#tbl_billete").querySelectorAll("tr"))
                filasResumenArqueo(filasB, tbody)
                 
                const filasM = Array.from(document.querySelector("#tbl_moneda").querySelectorAll("tr"))
                filasResumenArqueo(filasM, tbody)
                tabla.appendChild(tbody)
                 
                const tf = document.createElement("tfoot")
                const trf = document.createElement("tr")
                const tdf = document.createElement("td")
                tdf.setAttribute("colspan", "2")
                tdf.style.textAlign = "right"
                tdf.style.fontSize = "20px"
                tdf.style.fontWeight = "bold"
                tdf.innerText = "Total efectivo:"
                trf.appendChild(tdf)
                const tdf2 = document.createElement("td")
                tdf2.style.textAlign = "center"
                tdf2.style.fontSize = "20px"
                tdf2.style.fontWeight = "bold"
                tdf2.innerText = parseaNumero(document.querySelector("#totalEfectivo").value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                trf.appendChild(tdf2)
                trf.style.borderTop = "2px solid black"
                trf.style.height = "40px"
                tf.appendChild(trf)
                 
                const trf2 = document.createElement("tr")
                const tdf3 = document.createElement("td")
                tdf3.style.color = "black"
                tdf3.setAttribute("colspan", "3")
                tdf3.style.textAlign = "center"
                tdf3.innerText = "¿Está segur(a) de continuar con el registro del arqueo de caja?"
                trf2.appendChild(tdf3)
                tf.appendChild(trf2)
                tabla.appendChild(tf)
                 
                return tabla
            }
             
            const filasResumenArqueo = (filas, tbody) => {
                filas.forEach((fila) => {
                    const entradas = fila.querySelectorAll("input")
                    if (entradas[1].value === "0.00") return
                    const tr = document.createElement("tr")
                     
                    const d = document.createElement("td")
                    d.style.textAlign = "center"
                    d.innerText = fila.querySelectorAll("td")[0].innerText
                    tr.appendChild(d)
                     
                    Array.from(entradas).forEach((celda, i) => {
                        const td = document.createElement("td")
                        td.style.textAlign = "center"
                        td.innerText = i === 0 ? celda.value : parseaNumero(celda.value).toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                        tr.appendChild(td)
                    })
                    tbody.appendChild(tr)
                })
            }

            const buscarArqueos = () => {
                const datos = []
                addParametro(datos, "fecha_inicio", document.querySelector("#fechaInicio").value)
                addParametro(datos, "fecha_fin", document.querySelector("#fechaFin").value)

                consultaServidor("/Ahorro/HistoricoArqueos/", $.param(datos), (respuesta) => {
                    $("#tblArqueos").DataTable().destroy()
                    if (!respuesta.success) {
                        console.log(respuesta.error)
                        return showError(respuesta.mensaje)
                    }
                    $("#tblArqueos tbody").html(respuesta.datos)
                    configuraTabla("tblArqueos")
                })
            }
        </script>
        html;

        $d = CajaAhorroDao::HistoricoArqueo(["fecha_inicio" => date('Y-m-d', strtotime('-7 day')), "fecha_fin" => date('Y-m-d'), "sucursal" => $_SESSION['cdgco_ahorro'], "ejecutivo" => $_SESSION['usuario']]);

        $d = json_decode($d, true);
        $detalles = $d['datos'];

        $tabla = "";

        foreach ($detalles as $key => $detalle) {
            $tabla .= "<tr>";
            foreach ($detalle as $key => $valor) {
                if ($key == 'MONTO') $valor = "$ " . number_format($valor, 2);
                $tabla .= "<td style='vertical-align: middle;'>$valor</td>";
            }
            $tabla .= "</tr>";
        }

        $billetes = [
            ["simbolo" => "$", "valor" => "1,000.00", "id" => "1000"],
            ["simbolo" => "$", "valor" => "500.00", "id" => "500"],
            ["simbolo" => "$", "valor" => "200.00", "id" => "200"],
            ["simbolo" => "$", "valor" => "100.00", "id" => "100"],
            ["simbolo" => "$", "valor" => "50.00", "id" => "50"],
            ["simbolo" => "$", "valor" => "20.00", "id" => "20"],
        ];

        $monedas = [
            ["simbolo" => "$", "valor" => "10.00", "id" => "10"],
            ["simbolo" => "$", "valor" => "5.00", "id" => "5"],
            ["simbolo" => "$", "valor" => "2.00", "id" => "2"],
            ["simbolo" => "$", "valor" => "1.00", "id" => "1"],
            ["simbolo" => "¢", "valor" => "0.50", "id" => "050"],
            ["simbolo" => "¢", "valor" => "0.20", "id" => "020"],
            ["simbolo" => "¢", "valor" => "0.10", "id" => "010"]
        ];

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Saldos del día", [$this->XLSX])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::set('fecha', date('d/m/Y'));
        View::set('fechaInicio', date('Y-m-d', strtotime('-7 day')));
        View::set('fechaFin', date('Y-m-d'));
        View::set('tablaBilletes', self::generaTabla($billetes, "billete"));
        View::set('tablaMonedas', self::generaTabla($monedas, "moneda"));
        View::set('nomSucursal', CajaAhorroDao::getSucursal($_SESSION['cdgco_ahorro'])['NOMBRE']);
        View::render("caja_menu_saldos_dia");
    }

    public function generaTabla($denominaciones, $tipo)
    {
        $max = $tipo === "billete" ? 5000 : 1000;
        $filas = <<<html
        <table style="width: 100%;">
        <thead>
            <tr>
                <th style="text-align: center;">Denominación</th>
                <th style="text-align: center; width: 28%;">Cantidad</th>
                <th style="text-align: center; width: 37%;">Total</th>
            </tr>
        </thead>
        <tbody id="tbl_$tipo">
        html;

        foreach ($denominaciones as $denominacion) {
            $simbolo = $denominacion["simbolo"];
            $valor = $denominacion["valor"];
            $id = $denominacion["id"];

            $filas .= "<tr>";
            $filas .= "<td style='text-align: center;'>" . $simbolo . $valor . "</td>";
            $filas .= "<td><input class='form-control " . $tipo . "' id='cant" . $id . "' name='cant" . $id . "' type='number' min='0' max='" . $max . "' value='0' oninput=calculaTotal(event) onkeydown=soloNumeros(event) /></td>";
            $filas .= "<td><input style='text-align: right;' class='form-control efectivo' id='total" . $id . "' name='total" . $id . "' value='0.00' disabled /></td>";
            $filas .= "</tr>";
        }

        $filas .= "</tbody></table>";
        return $filas;
    }

    public function HistoricoArqueos()
    {
        echo CajaAhorroDao::HistoricoArqueo($_POST);
    }

    public function RegistraArqueo()
    {
        if (self::ValidaHorario()) {
            echo CajaAhorroDao::RegistraArqueo($_POST);
            return;
        }
        echo self::FueraHorario();
    }

    public function IconoOperacion($movimiento, $operacion)
    {
        if (in_array($operacion, $this->operacionesNulas)) return '<i class="fa fa-minus" style="color: #0000ac;"></i>';
        if ($movimiento == 1) return '<i class="fa fa-arrow-down" style="color: #00ac00;"></i>';
        if ($movimiento == 0) return '<i class="fa fa-arrow-up" style="color: #ac0000;"></i>';
    }

    public function SeparaMontos($movimiento, $operacion, $monto)
    {
        if (in_array($operacion, $this->operacionesNulas)) return [0, 0];
        if ($movimiento == 0) return [0, $monto];
        if ($movimiento == 1) return [$monto, 0];
    }

    public function ExportaExcel()
    {
        $tabla = $_POST['tabla'];
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=DetallesMovimientos.xlsx");
        echo $tabla;
    }

    public function GetLogTransacciones()
    {
        $log = CajaAhorroDao::GetLogTransacciones($_POST);
        echo $log;
    }

    public function EstadoCuenta()
    {
        $fecha = date('Y-m-d');
        $fechaInicio =  date('Y-m-d', strtotime('-1 month'));

        $extraFooterAnterior = <<<script
        <script>
            const mEdoCta = true
            let datosCliente = {}
            {$this->mensajes}
            {$this->validarYbuscar}
            {$this->buscaCliente}
            {$this->sinContrato}
            {$this->getHoy}
            {$this->soloNumeros}
            {$this->consultaServidor}
         
            const limpiaDatosCliente = () => {
                datosCliente = {}
                document.querySelector("#cliente").value = ""
                document.querySelector("#nombre").value = ""
                document.querySelector("#contrato").value = ""
                document.querySelector("#fechaInicio").value = "{$fechaInicio}"
                document.querySelector("#fechaFin").value = "{$fecha}"
                document.querySelector("#cliente").disabled = true
                document.querySelector("#nombre").disabled = true
                document.querySelector("#contrato").disabled = true
                document.querySelector("#fechaInicio").disabled = true
                document.querySelector("#fechaFin").disabled = true
                document.querySelector("#generarEdoCta").disabled = true
            }
             
            const llenaDatosCliente = (datos) => {
                if (!datos) return
                datosCliente = datos
                document.querySelector("#clienteBuscado").value = ""
                document.querySelector("#nombre").value = datos.NOMBRE
                document.querySelector("#cliente").value = datos.CDGCL
                document.querySelector("#contrato").value = datos.CONTRATO
                document.querySelector("#fechaInicio").disabled = false
                document.querySelector("#fechaFin").disabled = false
                document.querySelector("#generarEdoCta").disabled = false
            }
             
            const imprimeEdoCta = () => {
                const cliente = document.querySelector("#cliente").value
                if (!cliente) return showError("Ingrese un código de cliente.")
                mostrar(cliente)
            }
             
            const mostrar = (cliente) => {
                const host = window.location.origin
                fInicio = getFecha(document.querySelector("#fechaInicio").value)
                fFin = getFecha(document.querySelector("#fechaFin").value)
            
                let plantilla = '<!DOCTYPE html>'
                plantilla += '<html lang="es">'
                plantilla += '<head>'
                plantilla += '<meta charset="UTF-8">'
                plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
                plantilla += '<link rel="shortcut icon" href="" + host + "/img/logo.png">'
                plantilla += '<title>Estado de Cuenta: ' + cliente + '</title>'
                plantilla += '</head>'
                plantilla += '<body style="margin: 0; padding: 0; background-color: #333333;">'
                plantilla += '<iframe src="'
                    + host + '/Ahorro/EdoCta/?'
                    + 'cliente=' + cliente
                    + '&fInicio=' + fInicio
                    + '&fFin=' + fFin
                    + '" style="width: 100%; height: 99vh; border: none; margin: 0; padding: 0;"></iframe>'
                plantilla += '</body>'
                plantilla += '</html>'
            
                const blob = new Blob([plantilla], { type: 'text/html' })
                const url = URL.createObjectURL(blob)
                window.open(url, '_blank')
            }
             
            const getFecha = (fecha) => {
                const f = new Date(fecha + 'T06:00:00Z')
                return f.toLocaleString("es-MX", { year: "numeric", month:"2-digit", day:"2-digit" })
            }
        </script>
        script;

        $extraFooter = <<<html
        <script>
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->configuraTabla}
            {$this->consultaServidor}
             
            $(document).ready(function(){
                configuraTabla("muestra-cupones")
                configuraTabla("muestra-cupones1")
            })
           
            Reimprime_ticket = (folio) => {
                $('#modal_ticket').modal('show');
                document.getElementById("folio").value = folio;
            }
        
            enviar_add_sol = () =>  {             
                $('#modal_ticket').modal('hide');
                confirmarMovimiento("Resumen de movimientos", null, "¿Está segura de continuar?").then((continuar) => {
                    if (!continuar) return $('#modal_ticket').modal('show')
                    
                    consultaServidor("/Ahorro/AddSolicitudReimpresion/", $.param($('#Add').serializeArray()), (respuesta) => {
                        if (respuesta == '1') return showSuccess("Solicitud enviada a tesorería.");
                        
                        $('#modal_encuesta_cliente').modal('hide')
                        swal(respuesta, { icon: "error" })
                    },
                    "POST",
                    "Text")
                })
            }
        </script>
        html;

        $registros = CajaAhorroDao::GetMovimientosSucursal(["sucursal" => $_SESSION['cdgco_ahorro']]);
        $tabla = "";

        foreach ($registros as $key => $value) {
            $tabla .= "<tr>";
            foreach ($value as $key2 => $valor) {
                $estilo = "";
                if ($key2 === 'MONTO') $valor = "$ " . number_format($valor, 2);
                if ($key2 === 'CONCEPTO' || $key2 === 'CLIENTE') $estilo .= " text-align: left;";

                $tabla .= "<td style='vertical-align: middle;" . $estilo . "'>$valor</td>";
            }

            $tabla .= "<td style='vertical-align: middle;'><button type='button' class='btn btn-success btn-circle' onclick='Reimprime_ticket(\"{$value['CODIGO']}\");'><i class='fa fa-print'></i></button></td>";
            $tabla .= "</tr>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Estado de Cuenta")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::set('fecha_actual', date("Y-m-d H:i:s"));
        View::render("caja_menu_resumen_movimientos");
        // View::set('fecha', $fecha);
        // View::set('fechaInicio', date('Y-m-d', strtotime('-1 month')));
        // View::render("caja_menu_estado_cuenta");
    }

    //********************UTILS********************//
    // Generación de ticket's de operaciones realizadas
    public function ValidaHorario()
    {
        if ($_SESSION['perfil'] == 'ADMIN' || $_SESSION['usuario'] == 'AMGM' || $_SESSION['usuario'] == 'LGFR') return true;
        $ahora = new DateTime();
        $inicio = DateTime::createFromFormat('H:i:s', $_SESSION['inicio']);
        $fin = DateTime::createFromFormat('H:i:s', $_SESSION['fin']); // "19:00:00"); //

        return $ahora >= $inicio && $ahora <= $fin;
    }

    public function FueraHorario()
    {
        return json_encode(['success' => false, 'mensaje' => 'No es posible realizar operaciones fuera del horario establecido (' . $_SESSION['inicio'] . ' - ' . $_SESSION['fin'] . ')']);
    }

    public function Contrato()
    {
        $productos = [
            1 => 'Cuenta de Ahorro Corriente',
            2 => 'Cuenta de Inversión',
            3 => 'Cuenta de Ahorro Peque',
        ];

        if (!isset($_GET['contrato'])) exit('No se ha especificado el número de contrato');
        if (!isset($_GET['producto'])) exit('No se ha especificado el producto:<br>1 = Cuenta de Ahorro Corriente<br>2 = Cuenta de Inversión<br>3 = Cuenta de Ahorro Peque');
        if (!array_key_exists($_GET['producto'], $productos)) exit('El producto especificado no es válido');

        $noContrato = $_GET['contrato'];

        $style = <<<html
        <style>
            body {
                margin: 0;
                padding: 0;
            }
            h3 {
                text-align: center;
                margin-top: 20px;
            }
            .listaLetras {
                list-style-type: lower-alpha;
            }
            .fechaTitulo {
                text-align: right;
                padding-top: 50px;
                margin-bottom: 180px;
                font-weight: normal;
            }
            li {
                font-size: 11pt;
            }
        </style>  
        html;

        $contrato = "";
        if ($_GET['producto'] == 1) $contrato = self::GetContratoAhorro($noContrato);
        if ($_GET['producto'] == 2) $contrato = self::GetContratoInversion($noContrato);
        if ($_GET['producto'] == 3) $contrato = self::GetContratoPeque($noContrato);

        $nombreArchivo = "Contrato de " . $productos[$_GET['producto']];

        $mpdf = new \mPDF([
            'mode' => 'utf-8',
            'format' => 'Letter',
            'default_font_size' => 11.5,
            'default_font' => 'Arial',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 0,
            'margin_footer' => 5,
        ]);
        $mpdf->SetDefaultBodyCSS('text-align', 'justify');
        $fi = date('d/m/Y H:i:s');
        $pie = <<< html
        <table style="width: 100%; font-size: 10px">
            <tr>
            <td style="text-align: left; width: 50%;">
                Fecha de impresión  {$fi}
            </td>
            <td style="text-align: right; width: 50%;">
                Página {PAGENO} de {nb}
            </td>
            </tr>
        </table>
        html;
        $mpdf->SetHTMLFooter($pie);
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->WriteHTML($style, 1);
        $mpdf->WriteHTML($contrato, 2);

        $mpdf->Output($nombreArchivo . '.pdf', 'I');
    }

    public function Ticket()
    {
        $ticket = $_GET['ticket'];
        $sucursal = $_GET['sucursal'] ?? "";
        $datos = CajaAhorroDao::DatosTicket($ticket);
        if (!$datos) {
            echo "No se encontró información para el ticket: " . $ticket;
            return;
        }

        $nombreArchivo = "Ticket " . $ticket;
        $mensajeImpresion = 'Fecha de impresión:<br>' . date('d/m/Y H:i:s');
        if ($sucursal) {
            $datosImpresion = CajaAhorroDao::getSucursal($sucursal);
            $mensajeImpresion = 'Fecha y sucursal de impresión:<br>' . date('d/m/Y H:i:s') . ' - ' . $datosImpresion['NOMBRE'] . ' (' . $datosImpresion['CODIGO'] . ')';
        }

        $mpdf = new \mPDF([
            'mode' => 'utf-8',
            'format' => [80, 190],
            'default_font_size' => 10,
            'default_font' => 'Arial',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 5,
        ]);
        // PIE DE PAGINA
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:10px;font-family:Arial;">' . $mensajeImpresion . '</div>');
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->SetMargins(0, 0, 5);

        $tktEjecutivo = $datos['COD_EJECUTIVO'] ? "<label>" . $datos['RECIBIO'] . ": " . $datos['NOM_EJECUTIVO'] . " (" . $datos['COD_EJECUTIVO'] . ")</label><br>" : "";
        $tktSucursal = $datos['CDG_SUCURSAL'] ? '<label>Sucursal: ' . $datos['NOMBRE_SUCURSAL'] . ' (' . $datos['CDG_SUCURSAL'] . ')</label>' : "";
        $tktMontoLetra = self::NumeroLetras($datos['MONTO']);
        $tktSaldoA = number_format($datos['SALDO_ANTERIOR'], 2, '.', ',');
        $tktMontoOP = number_format($datos['MONTO'], 2, '.', ',');
        $tktSaldoN = number_format($datos['SALDO_NUEVO'], 2, '.', ',');
        $tktComision =  $datos['COMISION'] > 0 ?  '<tr><td style="text-align: left; width: 60%;">COMISION:</td><td style="text-align: right; width: 40%;">$ ' . number_format($datos['COMISION'], 2, '.', ',') . '</td></tr>' : "";

        $detalleMovimientos = "";
        if ($datos['COMPROBANTE'] == 'DEPÓSITO' && !$tktComision) {
            $detalleMovimientos = <<<html
            <tr>
                <td style="text-align: left; width: 60%;">
                    {$datos['ES_DEPOSITO']}:
                </td>
                <td style="text-align: right; width: 40%;">
                    $ {$tktMontoOP}
                </td>
            </tr>
            html;
        } else if ($datos['TIPO_PAGO'] == '6' || $datos['TIPO_PAGO'] == '7') {
            $detalleMovimientos = <<<html
            <tr>
                <td style="text-align: left; width: 60%;">
                    {$datos['ES_DEPOSITO']}:
                </td>
                <td style="text-align: right; width: 40%;">
                    $ {$tktMontoOP}
                </td>
            </tr>
            html;
        } else {
            $detalleMovimientos = <<<html
            <tr>
                <td style="text-align: center; font-weight: bold; font-size: 12px;" colspan="2">
                    SALDOS EN CUENTA DE AHORRO
                </td>
            <tr>
                <td style="text-align: left; width: 60%;">
                    SALDO ANTERIOR:
                </td>
                <td style="text-align: right; width: 40%;">
                    $ {$tktSaldoA}
                </td>
            </tr>
            <tr>
                <td style="text-align: left; width: 60%;">
                    {$datos['ES_DEPOSITO']}:
                </td>
                <td style="text-align: right; width: 40%;">
                    $ {$tktMontoOP}
                </td>
            </tr>
            $tktComision
            <tr>
                <td style="text-align: left; width: 60%;">
                    SALDO FINAL:
                </td>
                <td style="text-align: right; width: 40%;">
                    $ {$tktSaldoN}
                </td>
            </tr>
            html;
        }

        $ticketHTML = <<<html
        <body style="font-family:Helvetica; padding: 0; margin: 0">
            <div>
                <div style="text-align:center; font-size: 20px; font-weight: bold;">
                    <label>Más con Menos</label>
                </div>
                <div style="text-align:center; font-size: 15px;">
                    <label>COMPROBANTE DE {$datos['COMPROBANTE']}</label>
                </div>
                <div style="text-align:center; font-size: 14px;margin-top:5px; margin-bottom: 5px">
                    ***********************************************
                </div>
                <div style="font-size: 11px;">
                    <label>Fecha de la operación: {$datos['FECHA']}</label>
                    <br>
                    <label>Método de pago: {$datos['METODO']}</label>
                    <br>
                    $tktEjecutivo
                    $tktSucursal
                </div>
                <div style="text-align:center; font-size: 10px;margin-top:5px; margin-bottom: 5px; font-weight: bold;">
                    ___________________________________________________________________
                </div>
                <div style="font-size: 11px;">
                    <label>Nombre del cliente: {$datos['NOMBRE_CLIENTE']}</label>
                    <br>
                    <label>Código de cliente: {$datos['CODIGO']}</label>
                    <br>
                    <label>Código de contrato: {$datos['CONTRATO']}</label>
                </div>
                <div style="text-align:center; font-size: 10px;margin-top:5px; margin-bottom: 5px; font-weight: bold;">
                ___________________________________________________________________
                </div>
                <div style="text-align:center; font-size: 13px; font-weight: bold;">
                    <label>{$datos['PRODUCTO']}</label>
                </div>
                <div style="text-align:center; font-size: 14px;margin-top:5px; margin-bottom: 5px">
                ***********************************************
                </div>
                <div style="text-align:center; font-size: 15px; font-weight: bold;">
                    <label>{$datos['ENTREGA']} $ {$tktMontoOP}</label>
                </div>
                <div style="text-align:center; font-size: 11px;">
                    <label>($tktMontoLetra)</label>
                </div>
                <div style="text-align:center; font-size: 14px;margin-top:5px; margin-bottom: 5px">
                ***********************************************
                </div>
                <div style="text-align:center; font-size: 13px;">
                    <table style="width: 100%; font-size: 11spx">
                        $detalleMovimientos
                    </table>
                </div>
                <div style="text-align:center; font-size: 14px;margin-top:5px; margin-bottom: 5px">
                ***********************************************
                </div>
                <div style="text-align:center; font-size: 15px; margin-top:25px; font-weight: bold;">
                    <label>Firma de conformidad del cliente</label>
                    <div style="text-align:center; font-size: 15px; margin-top:25px; margin-bottom: 5px">
                        ______________________
                    </div>
                </div>
                <div style="text-align:center; font-size: 12px; font-weight: bold;">
                    <label>FOLIO DE LA OPERACIÓN</label>
                    <barcode code="$ticket-{$datos['CODIGO']}-{$datos['MONTO']}-{$datos['COD_EJECUTIVO']}" type="C128A" size=".60" height="1" />
                </div>
            </div>
        </body>
        html;

        // Agregar contenido al PDF
        $mpdf->WriteHTML($ticketHTML);

        if ($_GET['copiaCliente']) {
            $mpdf->WriteHTML('<div style="text-align:center; font-size: 15px;"><label><b>COPIA SUCURSAL</b></label></div>');
            $mpdf->AddPage();
            $mpdf->WriteHTML($ticketHTML);
            $mpdf->WriteHTML('<div style="text-align:center; font-size: 15px;"><label><b>COPIA CLIENTE</b></label></div>');
        }

        $mpdf->Output($nombreArchivo . '.pdf', 'I');
        exit;
    }

    public function TicketArqueo()
    {
        $datos = CajaAhorroDao::DatosTicketArqueo($_GET);
        if (!$datos) {
            echo "No se encontró el número de arqueo: " . ($_GET['arqueo'] ? $_GET['arqueo'] : "No indicado") . ", para la sucursal: " . $_GET['sucursal'];
            return;
        }

        $nombreArchivo = "Ticket Arqueo " . $datos['CDG_ARQUEO'];

        $mpdf = new \mPDF([
            'mode' => 'utf-8',
            'format' => [90, 190],
            'default_font_size' => 10,
            'default_font' => 'Arial',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 5,
        ]);
        // PIE DE PAGINA
        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:10px;font-family:Arial;">Fecha de impresión:<br>' . date('d/m/Y H:i:s') . '</div>');
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->SetMargins(0, 0, 5);

        $filasDetalle = "";
        $totalEfectivo = number_format($datos['MONTO'], 2, '.', ',');

        foreach ($datos as $key => $detalle) {
            if (strpos($key, "B_") === 0 || strpos($key, "M_") === 0) {
                $denominacion = str_replace("M_", "", str_replace("B_", "", $key));
                $denominacion = strpos($denominacion, "0") === 0 ? $denominacion / 100 : $denominacion;
                $monto = number_format($denominacion * $datos[$key], 2, '.', ',');
                $filasDetalle .= "<tr>";
                $filasDetalle .= "<td style='text-align: center;'>" . ($denominacion < 1 ? "¢" : "$") . number_format($denominacion, 2, '.', ',') . "</td>";
                $filasDetalle .= "<td style='text-align: center;'>" . $datos[$key] . "</td>";
                $filasDetalle .= "<td style='text-align: right;'>" . ($monto < 1 && $monto > 0 ? "¢" : "$") . $monto . "</td>";
                $filasDetalle .= "</tr>";
            }
        }

        $ticketHTML = <<<html
        <body style="font-family:Helvetica; padding: 0; margin: 0">
            <div>
                <div style="text-align:center; font-size: 20px; font-weight: bold;">
                    <label>Más con Menos</label>
                </div>
                <div style="text-align:center; font-size: 15px;">
                    <label>COMPROBANTE DE ARQUEO</label>
                </div>
                <div style="text-align:center; font-size: 14px; margin-top:5px; margin-bottom: 5px">
                    *****************************************
                </div>
                <div style="font-size: 11px;">
                    <label>Fecha de creación: {$datos['FECHA']}</label>
                    <br>
                    <label>Sucursal: {$datos['SUCURSAL']} ({$datos['CDG_SUCURSAL']})</label>
                    <br>
                    <label>Cajera: {$datos['USUARIO']} ({$datos['CDG_USUARIO']})</label>
                </div>
                <div style="text-align:center; font-size: 14px; margin-top:5px; margin-bottom: 5px">
                    *****************************************
                </div>
                <div style="text-align:center; font-size: 15px; font-weight: bold;">
                    <label>ARQUEO DE CAJA</label>
                </div>
                <div style="text-align:center; font-size: 10px; margin-top:5px; margin-bottom: 5px">
                    __________________________________________________________
                </div>
                <div style="text-align:center; font-size: 15px; font-weight: bold; margin-top:5px; margin-bottom: 5px">
                    <label>DETALLE</label>
                </div>
                <div style="text-align:center;">
                    <table style="width: 100%; font-size: 15px;">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 60%;">Denominación</th>
                                <th style="text-align: center; width: 40%;">Cantidad</th>
                                <th style="text-align: center; width: 40%;">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            $filasDetalle
                        </tbody>
                    </table>
                </div>
                <div style="text-align:center; font-size: 10px; margin-top:5px; margin-bottom: 5px; font-weight: bold;">
                    __________________________________________________________
                </div>
                <div style="text-align:center; font-size: 15px; margin-top:15px; font-weight: bold;">
                    <label>Total de efectivo: $ {$totalEfectivo}</label>
                </div>
                <div style="text-align:center; font-size: 14px; margin-top:5px; margin-bottom: 5px">
                    *****************************************
                </div>
                <div style="text-align:center; font-size: 15px; margin-top:25px; font-weight: bold;">
                    <label>Firma de conformidad</label>
                    <div style="text-align:center; font-size: 15px; margin-top:25px; margin-bottom: 5px">
                        ______________________
                    </div>
                </div>
            </div>
        </body>
        html;

        // Configurar copira
        if ($_GET['copia']) {
            $mpdf->WriteHTML('<div style="text-align:center; font-size: 15px;"><label><b>COPIA SUCURSAL</b></label></div>');
            $mpdf->AddPage();
            $mpdf->WriteHTML($ticketHTML);
            $mpdf->WriteHTML('<div style="text-align:center; font-size: 15px;"><label><b>COPIA CAJERA</b></label></div>');
        }

        // Agregar contenido al PDF
        $mpdf->WriteHTML($ticketHTML);
        $mpdf->Output($nombreArchivo . '.pdf', 'I');

        exit;
    }

    public function EdoCta()
    {
        if (!isset($_GET['cliente'])) {
            echo "No se especificó el cliente para generar el estado de cuenta.";
            return;
        }

        $dtsGrls = CajaAhorroDao::GetDatosEdoCta($_GET['cliente']);
        if (!$dtsGrls) {
            echo "No se encontró información para el cliente: " . $_GET['cliente'];
            return;
        }

        $fInicio = $_GET['fInicio'] ?? $dtsGrls['FECHA_APERTURA'];
        $fFin = $_GET['fFin'] ?? date('d/m/Y');
        $segmento = $_GET['segmento'] ?? 0;

        $fi = DateTime::createFromFormat('d/m/Y', $fInicio);
        $ff = DateTime::createFromFormat('d/m/Y', $fFin);
        $msjError = !($fi && $fi->format('d/m/Y') === $fInicio) ? "La fecha de inicio no es válida.<br>" : "";
        $msjError .= !($ff && $ff->format('d/m/Y') === $fFin) ? "La fecha de final no es válida.<br>" : "";
        $msjError .= ($fi > $ff) ? "La fecha de inicio no puede ser mayor a la fecha de final.<br>" : "";
        if ($msjError) {
            echo $msjError;
            return;
        }


        $estilo = <<<css
        <style>
            body {
                margin: 0;
                padding: 0;
            }
            .datosGenerales {
                margin-bottom: 20px;
            }
            .tablaTotales {
                margin: 5px 0;
            }
            .tituloTablas {
                font-size: 20px;
                font-weight: bold;
            }
            .datosCliente {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
            }
            .datosCliente td {
                text-align: center;
                margin: 15px 0;
            }
            .contenedorTotales {
                margin: 10px 0;
            }
            .tablaTotales {
                width: 100%;
                border-collapse: collapse;
            }
            .contenedorDetalle {
                margin: 5px 0;
            }
            .tablaDetalle {
                border-collapse: collapse;
                width: 100%;
                margin: 0 0 20px 0;
            }
            .tablaDetalle th {
                background-color: #f2f2f2;
            }
            .tablaDetalle th, .tablaDetalle td {
                border: 1px solid #ddd;
            }
        </style>
        css;

        $cuerpo = <<<html
        <body>
            <div class="datosGenerales" style="text-align:center;">
                <h1>Estado de Cuenta</h1>
                <table class="datosCliente">
                    <tr>
                        <td colspan="6">
                            <b>Nombre del Cliente: </b>{$dtsGrls['NOMBRE']}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="width: 50%;">
                            <b>Número de Contrato: </b>{$dtsGrls['CONTRATO']}
                        </td>
                        <td colspan="3" style="width: 50%;">
                            <b>Número de Cliente: </b>{$dtsGrls['CLIENTE']}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="width: 50%;">
                            <b>Inicio del Período: </b>{$fInicio}
                        </td>
                        <td colspan="3" style="width: 50%;">
                            <b>Fin del Período: </b>{$fFin}
                        </td>
                    </tr>
                </table>
            </div>
        html;

        if ($segmento == 0 || $segmento == 1) $cuerpo .= self::TablaMovimientosAhorro($dtsGrls['CONTRATO'], $fInicio, $fFin);
        if ($segmento == 0 || $segmento == 2) $cuerpo .= self::TablaMovimientosInversion($dtsGrls['CONTRATO']);
        if ($segmento == 0 || $segmento == 3) $cuerpo .= self::TablaMovimientosPeque($_GET['cliente'], $fInicio, $fFin);

        $cuerpo .= <<<html
            <div class="notices">
                <h2>Avisos y Leyendas</h2>
                <p>[Avisos y Leyendas Legales]</p>
            </div>
        </body>
        html;

        $nombreArchivo = "Estado de Cuenta: " . $_GET['cliente'];

        $mpdf = new \mPDF([
            'mode' => 'utf-8',
            'format' => 'Letter',
            'default_font_size' => 10
        ]);
        $fi = date('d/m/Y H:i:s');
        $pie = <<< html
        <table style="width: 100%; font-size: 10px">
            <tr>
            <td style="text-align: left; width: 50%;">
                Fecha de impresión  {$fi}
            </td>
            <td style="text-align: right; width: 50%;">
                Página {PAGENO} de {nb}
            </td>
            </tr>
        </table>
        html;

        $mpdf->SetHTMLFooter($pie);
        $mpdf->SetTitle($nombreArchivo);
        $mpdf->WriteHTML($estilo, 1);
        $mpdf->WriteHTML($cuerpo, 2);

        $mpdf->Output($nombreArchivo . '.pdf', 'I');
    }

    public function TablaMovimientosAhorro($contrato, $fIni, $fFin)
    {
        $datos = CajaAhorroDao::GetMovimientosAhorro($contrato, $fIni, $fFin);
        $cargos = 0;
        $abonos = 0;
        $transito = 0;
        $filas = "<tr><td colspan='6' style='text-align: center;'>Sin movimientos en el periodo.</td></tr>";
        $salto = false;
        if ($datos || count($datos) > 0) {
            $filas = "";
            foreach ($datos as $dato) {
                $transito = number_format($dato['TRANSITO'], 2, '.', ',');
                $abono = number_format($dato['ABONO'], 2, '.', ',');
                $cargo = number_format($dato['CARGO'], 2, '.', ',');
                $saldo = number_format($dato['SALDO'], 2, '.', ',');
                $cargos += $dato['CARGO'];
                $abonos += $dato['ABONO'];

                $filas .= <<<html
                <tr>
                    <td style="text-align: center;">{$dato['FECHA']}</td>
                    <td>{$dato['DESCRIPCION']}</td>
                    <td style="text-align: right;">$ $transito</td>
                    <td style="text-align: right;">$ $abono</td>
                    <td style="text-align: right;">$ $cargo</td>
                    <td style="text-align: right;">$ $saldo</td>
                </tr>
                html;
            }
            $salto = true;
        }

        $sf = number_format($datos[count($datos) - 1]['SALDO'], 2, '.', ',');
        $c = number_format($cargos, 2, '.', ',');
        $a = number_format($abonos, 2, '.', ',');
        $tabla = <<<html
        <span class="tituloTablas">Cuenta Ahorro Corriente</span>
        <div class="contenedorTotales">
            <table class="tablaTotales">
                <thead>
                    <tr>
                        <th>Abonos</th>
                        <th>Cargos</th>
                        <th>Saldo Final</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; width: 33%;">
                            $ $a
                        </td>
                        <td style="text-align: center; width: 33%;">
                            $ $c
                        </td>
                        <td style="text-align: center; width: 33%;">
                            $ $sf
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="contenedorDetalle">
            <table class="tablaDetalle">
                <thead>
                    <tr>
                        <th style="width: 80px;">Fecha</th>
                        <th>Descripción</th>
                        <th style="width: 100px;">En transito</th>
                        <th style="width: 100px;">Abono</th>
                        <th style="width: 100px;">Cargo</th>
                        <th style="width: 100px;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    $filas
                </tbody>
            </table>
        </div>
        html;

        return $tabla . ($salto ? "<div style='page-break-after: always;'></div>" : "");
    }

    public function TablaMovimientosInversion($contrato)
    {
        $datos = CajaAhorroDao::GetMovimientosInversion($contrato);
        if ($datos || count($datos) > 0) {
            $inversionTotal = 0;
            $rendimientoTotal = 0;
            $salto = false;
            // $filas = "<tr><td colspan='8' style='text-align: center;'>Sin movimientos en el periodo.</td></tr>";
            $filas = "";
            foreach ($datos as $dato) {
                $inversion = number_format($dato['MONTO'], 2, '.', ',');
                $rendimiento = number_format($dato['RENDIMIENTO'], 2, '.', ',');
                $inversionTotal += $dato['MONTO'];
                $rendimientoTotal += $dato['RENDIMIENTO'];

                $filas .= <<<html
                <tr>
                    <td style="text-align: center;">{$dato['FECHA_APERTURA']}</td>
                    <td style="text-align: center;">{$dato['FECHA_VENCIMIENTO']}</td>
                    <td style="text-align: right;">$ {$inversion}</td>
                    <td style="text-align: center;">{$dato['PLAZO']}</td>
                    <td style="text-align: center;">{$dato['TASA']} %</td>
                    <td style="text-align: center;">{$dato['ESTATUS']}</td>
                    <td style="text-align: center;">{$dato['FECHA_LIQUIDACION']}</td>
                    <td style="text-align: right;">$ {$rendimiento}</td>
                    <td style="text-align: center;">{$dato['ACCION']}</td>
                </tr>
                html;
            }
            $salto = true;

            $it = number_format($inversionTotal, 2, '.', ',');
            $rt = number_format($rendimientoTotal, 2, '.', ',');

            $tabla = <<<html
            <span class="tituloTablas">Cuenta Inversión</span>
            <div class="contenedorTotales">
                <table class="tablaTotales">
                    <thead>
                        <tr>
                            <th>Monto Total Invertido</th>
                            <th>Rendimientos Recibidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center; width: 50%;">
                                $ $it
                            </td>
                            <td style="text-align: center; width: 50%;">
                                $ $rt
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="contenedorDetalle">
                <table class="tablaDetalle">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Fecha Apertura</th>
                            <th style="width: 80px;">Fecha Cierre</th>
                            <th style="width: 100px;">Monto</th>
                            <th>Plazo</th>
                            <th style="width: 60px;">Tasa Anual</th>
                            <th>Estatus</th>
                            <th style="width: 100px;">Fecha Liquidación</th>
                            <th>Rendimiento</th>
                            <th>Destino</th>
                        </tr>
                    </thead>
                    <tbody>
                        $filas
                    </tbody>
                </table>
            </div>
            html;

            return $tabla . ($salto ? "<div style='page-break-after: always;'></div>" : "");
        }
    }

    public function TablaMovimientosPeque($clPadre, $fIni, $fFin)
    {
        $cuentas = CajaAhorroDao::GetCuentasPeque($clPadre);
        if ($cuentas || count($cuentas) > 0) {
            $tabla = "<span class='tituloTablas'>Cuenta Ahorro Peque</span>";
            $salto = false;
            foreach ($cuentas as $cuenta) {
                $transito = 0;
                $cargos = 0;
                $abonos = 0;
                $filas = "";
                $datos = CajaAhorroDao::GetMovimientosPeque($cuenta['CONTRATO'], $fIni, $fFin);
                if ($datos || count($datos) > 0) {
                    foreach ($datos as $dato) {
                        $transito = number_format($dato['TRANSITO'], 2, '.', ',');
                        $abono = number_format($dato['ABONO'], 2, '.', ',');
                        $cargo = number_format($dato['CARGO'], 2, '.', ',');
                        $saldo = number_format($dato['SALDO'], 2, '.', ',');
                        $cargos += $dato['CARGO'];
                        $abonos += $dato['ABONO'];

                        $filas .= <<<html
                        <tr>
                            <td style="text-align: center;">{$dato['FECHA']}</td>
                            <td>{$dato['DESCRIPCION']}</td>
                            <td style="text-align: right;">$ $transito</td>
                            <td style="text-align: right;">$ $abono</td>
                            <td style="text-align: right;">$ $cargo</td>
                            <td style="text-align: right;">$ $saldo</td>
                        </tr>
                        html;
                    }
                    $salto = true;
                }
                $filas = $filas ? $filas : "<tr><td colspan='6' style='text-align: center;'>Sin movimientos en el periodo.</td></tr>";

                $sf = number_format($datos[count($datos) - 1]['SALDO'], 2, '.', ',');
                $c = number_format($cargos, 2, '.', ',');
                $a = number_format($abonos, 2, '.', ',');
                $tabla .= <<<html
                <div class="contenedorTotales">
                    <table class="tablaTotales">
                        <tr>
                            <td colspan="2" style="text-align: center; width: 50%;">
                                <b>Nombre: </b>{$cuenta['NOMBRE']}
                            </td>
                            <td colspan="2" style="text-align: center; width: 50%;">
                                <b>No. Cuenta: </b>{$cuenta['CONTRATO']}
                            </td>
                        </tr>
                        <tr>
                            <th>Abonos</th>
                            <th>Cargos</th>
                            <th>Saldo Final</th>
                        </tr>
                        <tr>
                            <td style="text-align: center; width: 33%;">
                                $ $a
                            </td>
                            <td style="text-align: center; width: 33%;">
                                $ $c
                            </td>
                            <td style="text-align: center; width: 33%;">
                                $ $sf
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="contenedorDetalle">
                    <table class="tablaDetalle">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Fecha</th>
                                <th>Descripción</th>
                                <th style="width: 100px;">Transito</th>
                                <th style="width: 100px;">Abono</th>
                                <th style="width: 100px;">Cargo</th>
                                <th style="width: 100px;">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            $filas
                        </tbody>
                    </table>
                </div>
                html;
            }

            return $tabla . ($salto ? "<div style='page-break-after: always;'></div>" : "");
        }
    }

    public function toLetras($numero)
    {
        $cifras = array(
            0 => 'cero',
            1 => 'uno',
            2 => 'dos',
            3 => 'tres',
            4 => 'cuatro',
            5 => 'cinco',
            6 => 'seis',
            7 => 'siete',
            8 => 'ocho',
            9 => 'nueve',
            11 => 'once',
            12 => 'doce',
            13 => 'trece',
            14 => 'catorce',
            15 => 'quince',
            16 => 'dieciséis',
            17 => 'diecisiete',
            18 => 'dieciocho',
            19 => 'diecinueve',
            21 => 'veintiuno',
            22 => 'veintidós',
            23 => 'veintitrés',
            24 => 'veinticuatro',
            25 => 'veinticinco',
            26 => 'veintiséis',
            27 => 'veintisiete',
            28 => 'veintiocho',
            29 => 'veintinueve',
            10 => 'diez',
            20 => 'veinte',
            30 => 'treinta',
            40 => 'cuarenta',
            50 => 'cincuenta',
            60 => 'sesenta',
            70 => 'setenta',
            80 => 'ochenta',
            90 => 'noventa',
            100 => 'cien',
            200 => 'doscientos',
            300 => 'trescientos',
            400 => 'cuatrocientos',
            500 => 'quinientos',
            600 => 'seiscientos',
            700 => 'setecientos',
            800 => 'ochocientos',
            900 => 'novecientos'
        );

        $letra = '';

        if ($numero >= 1000000) {
            $letra .= floor($numero / 1000000) == 1 ? 'un' : $cifras[floor($numero / 1000000)];
            $numero %= 1000000;
            $letra .= (floor($numero / 1000000) > 1 ? ' millones' : ' millón') . ($numero > 0 ? ' ' : '');
            $letra .= $letra == 'un millón' ? ' de' : '';
        }

        if ($numero >= 100000) {
            $letra .= floor($numero / 100000) == 1 ? ' cien' : $cifras[floor($numero / 100000) * 100];
            $numero %= 100000;
            $letra .= $numero > 1000 ? ' ' : ' mil ';
        }

        if ($numero >= 1000) {
            $letra .= floor($numero / 1000) == 1 ? ' un' : $cifras[floor($numero / 1000)];
            $numero %= 1000;
            $letra .= ' mil' . ($numero > 0 ? ' ' : '');
        }

        if ($numero >= 100) {
            $letra .= $cifras[floor($numero / 100) * 100];
            $letra .= ($cifras[floor($numero / 100) * 100] === "cien" && $numero % 100 != 0) ? 'to' : '';
            $numero %= 100;
            $letra .= $numero > 0 ? ' ' : '';
        }

        if ($numero >= 30) {
            $letra .= $cifras[floor($numero / 10) * 10];
            $numero %= 10;
            $letra .= $numero > 0 ? ' y' : '';
        }


        if ($numero == 1) $letra .= ' un';
        else if ($numero == 21) $letra .= ' veintiún';
        else if ($numero > 0) $letra .= ' ' . $cifras[$numero];

        return trim($letra);
    }

    public function NumeroLetras($numero, $soloLetras = false)
    {
        if (!is_numeric($numero)) return "No es un número válido";
        $letra = '';
        $letra = ($numero == 0) ? 'cero' : self::toLetras(floor($numero));

        $tmp = [
            ucfirst($letra),
            (floor($numero) == 1 ? "peso" : "pesos"),
            str_pad(round(($numero - floor($numero)) * 100), 2, "0", STR_PAD_LEFT) . "/100 M.N."
        ];

        if ($soloLetras) return $tmp[0];
        return implode(" ", $tmp);
    }

    public function GetContratoAhorro($contrato)
    {
        $datos = CajaAhorroDao::DatosContratoAhorro($contrato);
        if (!$datos) exit("No se encontró información para el contrato: " . $contrato);

        $monto = "$" . number_format($datos['MONTO_APERTURA'], 2, '.', ',');
        $monto_letra = self::NumeroLetras($datos['MONTO_APERTURA']);
        $firma = "/img/firma_1.jpg";

        return <<<html
        <div class="contenedor">
            <p>
                CONTRATO PRIVADO DE MUTUO A PLAZO INDETERMINADO QUE CELEBRAN POR UNA PARTE EL (LA)
                <b>C. {$datos['NOMBRE']}</b>, EN LO SUCESIVO COMO EL “MUTUANTE Y/O PRESTAMISTA” Y POR LA OTRA
                PARTE EL <b>C. ANTONIO LORENZO HERNÁNDEZ</b>, EN LO SUCESIVO EL “MUTUARIO Y/O PRESTATARIO”, DE
                CONFORMIDAD CON LAS SIGUIENTES:
            </p>
            <h3>DECLARACIONES</h3>
            <div calss="decalraciones">
                <ol>
                    <li>Declara <b>"EL MUTUARIO Y/O PRESTATARIO"</b> bajo protesta de decir verdad:</li>
                    <ol class="listaLetras">
                        <li>
                            Ser persona física con plena capacidad jurídica para la celebración del presente
                            contrato y para obligarse individualmente a todos sus términos con pleno
                            conocimiento de su objetivo y efectos jurídicos.
                        </li>
                        <li>
                            Tener su domicilio en <b>AMBAR MANZANA 29 L31CA LOMA DE SAN FRANCISCO ALMOLOYA DE JUAREZ,  ALMOLOYA DE JUAREZ, CP. 50940, MEXICO</b>, mismo que 
                            señala para todos sus efectos derivados de este contrato.
                        </li>
                        <li>
                            Que cuenta con la capacidad y solvencia económica suficiente para cumplir con las
                            obligaciones a su cargo derivadas del presente contrato.
                        </li>
                    </ol>
                    <li>Declara el <b>“MUTUANTE Y/O PRESTAMISTA”:</b></li>
                    <ol class="listaLetras">
                        <li>Contar con la capacidad suficiente para la celebración del presente contrato.</li>
                        <li>
                            Que su domicilio para los efectos de este contrato es el ubicado en <b>{$datos['DIRECCION']}</b>.
                        </li>
                    </ol>
                    <li><b>LAS PARTES</b> declaran:</li>
                    <ol class="listaLetras">
                        <li>
                            Que reconocen recíprocamente la capacidad jurídica con la que comparecen a la
                            celebración de este contrato, manifestando que el mismo está libre de cualquier
                            vicio del consentimiento que pudiera afectar su plena validez.
                        </li>
                        <li>
                            Que manifiestan su consentimiento para celebrar el presente contrato de mutuo con
                            interés.
                        </li>
                        <li>
                            Que reconocen en forma mutua la personalidad con que actúan en la celebración del
                            presente instrumento.
                        </li>
                    </ol>
                </ol>
            </div>
            <h3>CLAUSULAS</h3>
            <p>
                <b>PRIMERA.-</b> <b>OBJETO DEL CONTRATO.</b> Que las partes tienen pleno conocimiento que el
                objeto del presente contrato es el préstamo de dinero con interés a un plazo indeterminado.
            </p>
            <p>
                <b>SEGUNDA.-</b> <b>MONTO DEL PRESTAMO.</b> Será variable conforme a los depósitos o
                exhibiciones que haga el “MUTUANTE Y/O PRESTAMISTA” al “MUTUARIO Y/O PRESTATARIO”.
            </p>
            <p>
                <b>TERCERA.-</b> <b>PLAZO.</b> Las partes convienen que el préstamo no contará con un plazo
                determinado, por lo que una vez que el “MUTUANTE Y/O PRESTAMISTA” reclame la devolución del
                monto mutuado le será devuelto en un plazo de siete días hábiles después de su solicitud de
                devolución, la cual deberá hacer por escrito al “MUTUARIO Y/O PRESTATARIO”; el interés
                ordinario que obtendrá el “MUTUARIO Y/O PRESTATARIO” será del 5% anualizado.
            </p>
            <p>
                <b>CUARTA.-</b> <b>RECIBO DE DINERO.</b> “MUTUARIO Y/O PRESTATARIO” recibe del “MUTUANTE Y/O
                PRESTAMISTA” a su más entera satisfacción la cantidad de <b>{$monto} ({$monto_letra})</b>,
                otorgando como el recibo más amplio y eficaz de la recepción de dicho dinero la firma del
                presente contrato; dicha cantidad será el mínimo que se podrá exhibir o entregar para
                celebrar el presente contrato.
            </p>
            <p>
                <b>QUINTA.-</b> <b>LUGAR DE PAGO.</b> “MUTUANTE Y/O PRESTAMISTA” acudirá al domicilio del
                “MUTUARIO Y/O PRESTATARIO” o al lugar que éste le indique a recibir el pago, conforme a lo
                señalado en la cláusula TERCERA del presente contrato, el “MUTUANTE Y/O PRESTAMISTA” deberá
                acudir personalmente.
            </p>
            <p>
                <b>SEXTA.-</b> <b>INTERÉS MORATORIO.</b> Si el “MUTUARIO Y/O PRESTATARIO” incumpliera en el pago de las amortizaciones pactadas,
                 “MUTUANTE Y/O PRESTAMISTA” aplicará intereses moratorios a razón de 4.5% 
                mensual sobre el capital devengado y no pagado conforme a la cláusula tercera del presente instrumento.
            </p>
            <p>
                <b>SEPTIMA.-</b> <b>INCUMPLIMIENTO.</b> En caso de incumplimiento de pago del “MUTUARIO Y/O
                PRESTATARIO”, el “MUTUANTE Y/O PRESTAMISTA” podrá reclamar el cumplimiento forzoso del
                presente contrato mediante los procesos legales que la Ley vigente determine.
            </p>
            <p>
                <b>OCTAVA.-</b> <b>OTROS CONTRATOS.</b> En caso de que el “MUTUANTE Y/O PRESTAMISTA” tuviera
                algún otro tipo de negociación con el “MUTUARIO Y/O PRESTATARIO”; autoriza desde este
                momento que en caso de cualquier tipo de incumplimiento referente a pagos, se pueda aplicar
                del presente contrato el pago pendiente a los otros instrumentos o contratos que existan.
            </p>
            <p>
                <b>NOVENA.-</b> En caso de fallecimiento del “MUTUANTE Y/O PRESTAMISTA”, el adeudo que
                exista en esa fecha deberá ser cubierto a la persona que haya señalado como beneficiario;
                para que esto proceda, se deberá acreditar en forma fehaciente el hecho con el acta de
                defunción correspondiente.
            </p>
            <p>
                <b>DECIMA.-</b> Para la celebración del presente Contrato el “MUTUANTE Y/O PRESTAMISTA”
                acepta cubrir al “MUTUARIO Y/O PRESTATARIO” a la firma del presente la cantidad de $200.00
                (DOSCIENTOS PESOS 00/100 M. N.) por concepto de gastos de papelería.
            </p>
            <p>
                <b>DECIMA PRIMERA.-</b> <b>LAS PARTES</b> manifiestan que no existe dolo ni cláusula
                contraria a derecho, no dándose los supuestos de ignorancia ni extrema necesidad,
                conscientes de su alcance y valor jurídico lo firman de conformidad.
            </p>
            <p>
                <b>DECIMA SEGUNDA.-</b> <b>COMPETENCIA.</b> Para el cumplimiento y resolución del presente
                contrato las partes se someten a la jurisdicción y competencia de los Juzgados de la Ciudad
                de México, renunciando expresamente a la jurisdicción de futuro domicilio.
            </p>
            <table style="width: 100%">
                <tr>
                    <td colspan="3" style="text-align: center; height: 90px">
                        <b>Ciudad de México, a {$datos['FECHA_F_LEGAL']}</b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 45%">
                        <b>EL MUTUANTE Y/O PRESTAMISTA</b>
                    </td>
                    <td style="width: 10%"></td>
                    <td style="text-align: center; width: 45%">
                        <b>EL MUTUARIO Y/O PRESTATARIO</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 80px"></td>
                    <td style="height: 80px; text-align: center; width: 45%">
                        <img src="{$firma}" alt="Firma" style="width: 150px; height: 100px">
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 45%; border-top: 1px solid">
                        <b>{$datos['NOMBRE']}</b>
                    </td>
                    <td style="width: 10%"></td>
                    <td style="text-align: center; width: 45%; border-top: 1px solid">
                        <b>ANTONIO LORENZO HERNÁNDEZ</b>
                    </td>
                </tr>
            </table>
        </div>
        <div style="page-break-after: always"></div>
        <div>
            <h3 class="fechaTitulo">Ciudad de México a {$datos['FECHA_F_LEGAL']}</h3>
            <p>
                El suscrito <b>{$datos['NOMBRE']}</b>, a través de la presente y bajo
                protesta de decir verdad, manifiesto que los recursos que he exhibido y que se señalan a
                detalle en el <b>CONTRATO DE MUTUO</b> de fecha {$datos['FECHA_F_LEGAL']}, celebrado en mi carácter de
                <b>“MUTUANTE Y/O PRESTAMISTA”</b> con el <b>C. ANTONIO LORENZO HERNÁNDEZ</b> en su carácter de “MUTUARIO
                Y/O PRESTATARIO” provienen de un <b>ORIGEN LÍCITO</b>, por lo que desde este momento señalo que no
                me encuentro en ninguno de los supuestos referidos en el artículo 400 Bis del Código Penal
                Federal en vigor.
            </p>
            <p>
                De la misma forma, <b>DESLINDO al “MUTUARIO Y/O PRESTATARIO”</b> de cualquier tema que pueda
                presentarse en el futuro y que sea relacionado con los recursos económicos del suscrito en
                los diversos actos jurídicos que se celebren.
            </p>
            <table style="width: 100%; padding-top: 150px">
                <tr>
                    <td style="text-align: center; width: 33%"></td>
                    <td style="text-align: center; width: 33%">
                        <b>ATENTAMENTE</b>
                    </td>
                    <td style="text-align: center; width: 33%"></td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 100px"></td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 25%"></td>
                    <td style="text-align: center; width: 50%; border-top: 1px solid">
                        <b>{$datos['NOMBRE']}</b>
                    </td>
                    <td style="text-align: center; width: 25%"></td>
                </tr>
            </table>
        </div>    
        html;
    }

    public function GetContratoInversion($codigoInversion)
    {
        $datos = CajaAhorroDao::DatosContratoInversion($codigoInversion);
        if (!$datos) exit("No se encontró información para el codigo de inversion: " . $codigoInversion);

        $monto = "$" . number_format($datos['MONTO'], 2, '.', ',');
        $monto_letra = self::NumeroLetras($datos['MONTO']);
        $dias_letra = self::NumeroLetras($datos['DIAS'], true);
        $firma = "/img/firma_1.jpg";

        return <<<html
        <div class="contenedor">
            <p>
                CONTRATO PRIVADO DE MUTUO A PLAZO DETERMINADO QUE CELEBRAN POR UNA PARTE EL (LA) <b>C.
                {$datos['NOMBRE']}</b>, EN LO SUCESIVO COMO EL “MUTUANTE Y/O PRESTAMISTA” Y POR LA OTRA PARTE EL
                C. ANTONIO LORENZO HERNÁNDEZ, EN LO SUCESIVO EL “MUTUARIO Y/O PRESTATARIO”, DE CONFORMIDAD
                CON LAS SIGUIENTES:
            </p>
            <h3>DECLARACIONES</h3>
            <div calss="decalraciones">
                <ol>
                    <li>Declara <b>"EL MUTUARIO Y/O PRESTATARIO"</b> bajo protesta de decir verdad:</li>
                    <ol class="listaLetras">
                        <li>
                            Ser persona física con plena capacidad jurídica para la celebración del presente
                            contrato y para obligarse individualmente a todos sus términos con pleno
                            conocimiento de su objetivo y efectos jurídicos.
                        </li>
                        <li>
                            Tener su domicilio en <b>AMBAR MANZANA 29 L31CA LOMA DE SAN FRANCISCO ALMOLOYA DE JUAREZ,  ALMOLOYA DE JUAREZ, CP. 50940, MEXICO</b>, mismo que 
                            señala para todos sus efectos derivados de este contrato.
                        </li>
                        <li>
                            Que cuenta con la capacidad y solvencia económica suficiente para cumplir con las
                            obligaciones a su cargo derivadas del presente contrato.
                        </li>
                    </ol>
                    <li>Declara el <b>“MUTUANTE Y/O PRESTAMISTA”:</b></li>
                    <ol class="listaLetras">
                        <li>Contar con la capacidad suficiente para la celebración del presente contrato.</li>
                        <li>
                            Que su domicilio para los efectos de este contrato es el ubicado en <b>{$datos['DIRECCION']}</b>.
                        </li>
                    </ol>
                    <li><b>LAS PARTES</b> declaran:</li>
                    <ol class="listaLetras">
                        <li>
                            Que reconocen recíprocamente la capacidad jurídica con la que comparecen a la
                            celebración de este contrato, manifestando que el mismo está libre de cualquier
                            vicio del consentimiento que pudiera afectar su plena validez.
                        </li>
                        <li>
                            Que manifiestan su consentimiento para celebrar el presente contrato de mutuo
                            con interés.
                        </li>
                        <li>
                            Que reconocen en forma mutua la personalidad con que actúan en la celebración
                            del presente instrumento.
                        </li>
                    </ol>
                </ol>
            </div>
            <h3>CLAUSULAS</h3>
            <p>
                <b>PRIMERA.-</b> <b>OBJETO DEL CONTRATO.</b> Que las partes tienen pleno conocimiento que el
                objeto del presente contrato es el préstamo de dinero con interés a un plazo determinado.
            </p>
            <p>
                <b>SEGUNDA.-</b> <b>MONTO DEL PRESTAMO.</b> Es la cantidad de <b>{$monto} ({$monto_letra})</b>.
            </p>
            <p>
                <b>TERCERA.-</b> <b>PLAZO.</b> Las partes convienen que el préstamo será cubierto en una
                sola exhibición en un término de <b>{$datos['DIAS']} ({$dias_letra} días naturales)</b>, junto con los
                intereses generados por el mismo, los días antes señalados empezarán a correr a partir de la
                firma del presente contrato; el interés ordinario que obtendrá el “MUTUARIO Y/O PRESTATARIO”
                será del <b>{$datos['TASA']}%</b> anualizado. En caso de que el “MUTUANTE Y/O PRESTAMISTA” requiera la cantidad
                señalada en la Cláusula PRIMERA del presente instrumento antes del vencimiento pactado, se
                hará acreedor a una penalización por parte del “MUTUARIO Y/O PRESTATARIO” del 10% sobre la
                cantidad señalada en la cláusula SEGUNDA del presente instrumento.
            </p>
            <p>
                <b>CUARTA.-</b> <b>RECIBO DE DINERO.</b> “MUTUARIO Y/O PRESTATARIO” recibe del “MUTUANTE Y/O
                PRESTAMISTA” a su más entera satisfacción la cantidad de <b>{$monto} ({$monto_letra})</b>,
                otorgando como el recibo más amplio y eficaz de la recepción de dicho dinero la firma del
                presente contrato.
            </p>
            <p>
                <b>QUINTA.-</b> <b>LUGAR DE PAGO.</b> “MUTUANTE Y/O PRESTAMISTA” acudirá al domicilio del
                “MUTUARIO Y/O PRESTATARIO” o al lugar que éste le indique a recibir el pago, conforme a lo
                señalado en la cláusula TERCERA del presente contrato, el “MUTUANTE Y/O PRESTAMISTA” deberá
                acudir personalmente.
            </p>
            <p>
                <b>SEXTA.-</b> <b>INTERÉS MORATORIO.</b> Si el “MUTUARIO Y/O PRESTATARIO” incumpliera en el
                pago de las amortizaciones pactadas, “MUTUANTE Y/O PRESTAMISTA” aplicará intereses
                moratorios a razón de 4.5% mensual sobre el capital devengado y no pagado conforme a la
                cláusula tercera del presente instrumento.
            </p>
            <p>
                <b>SEPTIMA.-</b> <b>INCUMPLIMIENTO.</b> En caso de incumplimiento de pago del “MUTUARIO Y/O
                PRESTATARIO”, el “MUTUANTE Y/O PRESTAMISTA” podrá reclamar el cumplimiento forzoso del
                presente contrato mediante los procesos legales que la Ley vigente determine.
            </p>
            <p>
                <b>OCTAVA.-</b> <b>OTROS CONTRATOS.</b> En caso de que el “MUTUANTE Y/O PRESTAMISTA” tuviera
                algún otro tipo de negociación con el “MUTUARIO Y/O PRESTATARIO”; autoriza desde este
                momento que en caso de cualquier tipo de incumplimiento referente a pagos, se pueda aplicar
                del presente contrato el pago pendiente a los otros instrumentos o contratos que existan.
            </p>
            <p>
                <b>NOVENA.-</b> En caso de fallecimiento del “MUTUANTE Y/O PRESTAMISTA”, el adeudo que
                exista en esa fecha deberá ser cubierto a la persona que haya señalado como beneficiario;
                para que esto proceda, se deberá acreditar en forma fehaciente el hecho con el acta de
                defunción correspondiente.
            </p>
            <p>
                <b>DECIMA.-</b> Para la celebración del presente Contrato el “MUTUANTE Y/O PRESTAMISTA”
                acepta cubrir al “MUTUARIO Y/O PRESTATARIO” a la firma del presente la cantidad de $200.00
                (DOSCIENTOS PESOS 00/100 M. N.) por concepto de gastos de papelería.
            </p>
            <p>
                <b>DECIMA PRIMERA.-</b> <b>LAS PARTES</b> manifiestan que no existe dolo ni cláusula
                contraria a derecho, no dándose los supuestos de ignorancia ni extrema necesidad,
                conscientes de su alcance y valor jurídico lo firman de conformidad.
            </p>
            <p>
                <b>DECIMA SEGUNDA.-</b> <b>COMPETENCIA.</b> Para el cumplimiento y resolución del presente
                contrato las partes se someten a la jurisdicción y competencia de los Juzgados de la Ciudad
                de México, renunciando expresamente a la jurisdicción de futuro domicilio.
            </p>
            <table style="width: 100%">
                <tr>
                    <td colspan="3" style="text-align: center; height: 90px">
                        <b>Ciudad de México, a {$datos['FECHA_F_LEGAL']}</b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 45%">
                        <b>EL MUTUANTE Y/O PRESTAMISTA</b>
                    </td>
                    <td style="width: 10%"></td>
                    <td style="text-align: center; width: 45%">
                        <b>EL MUTUARIO Y/O PRESTATARIO</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="height: 80px"></td>
                    <td style="height: 80px; text-align: center; width: 45%">
                        <img src="{$firma}" alt="Firma" style="width: 150px; height: 100px">
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 45%; border-top: 1px solid">
                        <b>{$datos['NOMBRE']}</b>
                    </td>
                    <td style="width: 10%"></td>
                    <td style="text-align: center; width: 45%; border-top: 1px solid">
                        <b>ANTONIO LORENZO HERNÁNDEZ</b>
                    </td>
                </tr>
            </table>
        </div>
        <div style="page-break-after: always"></div>
        <div>
            <h3 class="fechaTitulo">Ciudad de México a {$datos['FECHA_F_LEGAL']}</h3>
            <p>
                El suscrito <b>{$datos['NOMBRE']}</b>, a través de la presente y bajo protesta de decir
                verdad, manifiesto que los recursos que he exhibido y que se señalan a detalle en el
                <b>CONTRATO DE MUTUO</b> de fecha {$datos['FECHA_F_LEGAL']}, celebrado en mi carácter de
                <b>“MUTUANTE Y/O PRESTAMISTA”</b> con el <b>C. ANTONIO LORENZO HERNÁNDEZ</b> en su carácter
                de “MUTUARIO Y/O PRESTATARIO” provienen de un <b>ORIGEN LÍCITO</b>, por lo que desde este
                momento señalo que no me encuentro en ninguno de los supuestos referidos en el artículo 400
                Bis del Código Penal Federal en vigor.
            </p>
            <p>
                De la misma forma, <b>DESLINDO al “MUTUARIO Y/O PRESTATARIO”</b> de cualquier tema que pueda
                presentarse en el futuro y que sea relacionado con los recursos económicos del suscrito en
                los diversos actos jurídicos que se celebren.
            </p>
            <table style="width: 100%; padding-top: 150px">
                <tr>
                    <td style="text-align: center; width: 33%"></td>
                    <td style="text-align: center; width: 33%">
                        <b>ATENTAMENTE</b>
                    </td>
                    <td style="text-align: center; width: 33%"></td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 80px"></td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 25%"></td>
                    <td style="text-align: center; width: 50%; border-top: 1px solid">
                        <b>{$datos['NOMBRE']}</b>
                    </td>
                    <td style="text-align: center; width: 25%"></td>
                </tr>
            </table>
        </div>
        html;
    }

    public function GetContratoPeque($contrato)
    {
        $datos = CajaAhorroDao::DatosContratoPeque($contrato);
        if (!$datos) exit("No se encontró información para el contrato: " . $contrato);

        $monto = "$" . number_format($datos['MONTO_APERTURA'], 2, '.', ',');
        $monto_letra = self::NumeroLetras($datos['MONTO_APERTURA']);

        return <<<html
        <div class="contenedor">
            <p>
                CONTRATO PRIVADO DE MUTUO A PLAZO INDETERMINADO QUE CELEBRAN POR UNA PARTE EL (LA)
                <b>C. {$datos['NOMBRE']}</b>, EN LO SUCESIVO COMO EL “MUTUANTE Y/O PRESTAMISTA” Y POR LA OTRA
                PARTE EL <b>C. ANTONIO LORENZO HERNÁNDEZ</b>, EN LO SUCESIVO EL “MUTUARIO Y/O PRESTATARIO”, DE
                CONFORMIDAD CON LAS SIGUIENTES:
            </p>
            <h3>DECLARACIONES</h3>
            <div calss="decalraciones">
                <ol>
                    <li>Declara <b>"EL MUTUARIO Y/O PRESTATARIO"</b> bajo protesta de decir verdad:</li>
                    <ol class="listaLetras">
                        <li>
                            Ser persona física con plena capacidad jurídica para la celebración del presente
                            contrato y para obligarse individualmente a todos sus términos con pleno
                            conocimiento de su objetivo y efectos jurídicos.
                        </li>
                        <li>
                            Tener su domicilio en <b>Avenida Melchor Ocampo, número 416 Interior 1, Colonia 
                            Cuauhtémoc, Alcaldía Cuauhtémoc, Ciudad de México, C.P. 06500</b>, mismo que 
                            señala para todos sus efectos derivados de este contrato.
                        </li>
                        <li>
                            Que cuenta con la capacidad y solvencia económica suficiente para cumplir con las
                            obligaciones a su cargo derivadas del presente contrato.
                        </li>
                    </ol>
                    <li>Declara el <b>“MUTUANTE Y/O PRESTAMISTA”:</b></li>
                    <ol class="listaLetras">
                        <li>Contar con la capacidad suficiente para la celebración del presente contrato.</li>
                        <li>
                            Que su domicilio para los efectos de este contrato es el ubicado en <b>{$datos['DIRECCION']}</b>.
                        </li>
                    </ol>
                    <li><b>LAS PARTES</b> declaran:</li>
                    <ol class="listaLetras">
                        <li>
                            Que reconocen recíprocamente la capacidad jurídica con la que comparecen a la
                            celebración de este contrato, manifestando que el mismo está libre de cualquier
                            vicio del consentimiento que pudiera afectar su plena validez.
                        </li>
                        <li>
                            Que manifiestan su consentimiento para celebrar el presente contrato de mutuo con
                            interés.
                        </li>
                        <li>
                            Que reconocen en forma mutua la personalidad con que actúan en la celebración del
                            presente instrumento.
                        </li>
                    </ol>
                </ol>
            </div>
            <h3>CLAUSULAS</h3>
            <p>
                <b>PRIMERA.-</b> <b>OBJETO DEL CONTRATO.</b> Que las partes tienen pleno conocimiento que el
                objeto del presente contrato es el préstamo de dinero con interés a un plazo indeterminado.
            </p>
            <p>
                <b>SEGUNDA.-</b> <b>MONTO DEL PRESTAMO.</b> Será variable conforme a los depósitos o
                exhibiciones que haga el “MUTUANTE Y/O PRESTAMISTA” al “MUTUARIO Y/O PRESTATARIO”.
            </p>
            <p>
                <b>TERCERA.-</b> <b>PLAZO.</b> Las partes convienen que el préstamo no contará con un plazo
                determinado, por lo que una vez que el “MUTUANTE Y/O PRESTAMISTA” reclame la devolución del
                monto mutuado le será devuelto en un plazo de siete días hábiles después de su solicitud de
                devolución, la cual deberá hacer por escrito al “MUTUARIO Y/O PRESTATARIO”; el interés
                ordinario que obtendrá el “MUTUARIO Y/O PRESTATARIO” será del 5% anualizado.
            </p>
            <p>
                <b>CUARTA.-</b> <b>RECIBO DE DINERO.</b> “MUTUARIO Y/O PRESTATARIO” recibe del “MUTUANTE Y/O
                PRESTAMISTA” a su más entera satisfacción la cantidad de <b>{$monto} ({$monto_letra})</b>,
                otorgando como el recibo más amplio y eficaz de la recepción de dicho dinero la firma del
                presente contrato; dicha cantidad será el mínimo que se podrá exhibir o entregar para
                celebrar el presente contrato.
            </p>
            <p>
                <b>QUINTA.-</b> <b>LUGAR DE PAGO.</b> “MUTUANTE Y/O PRESTAMISTA” acudirá al domicilio del
                “MUTUARIO Y/O PRESTATARIO” o al lugar que éste le indique a recibir el pago, conforme a lo
                señalado en la cláusula TERCERA del presente contrato, el “MUTUANTE Y/O PRESTAMISTA” deberá
                acudir personalmente.
            </p>
            <p>
                <b>SEXTA.-</b> <b>INTERÉS MORATORIO.</b> “MUTUARIO Y/O PRESTATARIO” pagará al “MUTUANTE Y/O
                PRESTAMISTA” una comisión del 10% sobre el monto total del préstamo.
            </p>
            <p>
                <b>SEPTIMA.-</b> <b>INCUMPLIMIENTO.</b> En caso de incumplimiento de pago del “MUTUARIO Y/O
                PRESTATARIO”, el “MUTUANTE Y/O PRESTAMISTA” podrá reclamar el cumplimiento forzoso del
                presente contrato mediante los procesos legales que la Ley vigente determine.
            </p>
            <p>
                <b>OCTAVA.-</b> <b>OTROS CONTRATOS.</b> En caso de que el “MUTUANTE Y/O PRESTAMISTA” tuviera
                algún otro tipo de negociación con el “MUTUARIO Y/O PRESTATARIO”; autoriza desde este
                momento que en caso de cualquier tipo de incumplimiento referente a pagos, se pueda aplicar
                del presente contrato el pago pendiente a los otros instrumentos o contratos que existan.
            </p>
            <p>
                <b>NOVENA.-</b> En caso de fallecimiento del “MUTUANTE Y/O PRESTAMISTA”, el adeudo que
                exista en esa fecha deberá ser cubierto a la persona que haya señalado como beneficiario;
                para que esto proceda, se deberá acreditar en forma fehaciente el hecho con el acta de
                defunción correspondiente.
            </p>
            <p>
                <b>DECIMA.-</b> Para la celebración del presente Contrato el “MUTUANTE Y/O PRESTAMISTA”
                acepta cubrir al “MUTUARIO Y/O PRESTATARIO” a la firma del presente la cantidad de $200.00
                (DOSCIENTOS PESOS 00/100 M. N.) por concepto de gastos de papelería.
            </p>
            <p>
                <b>DECIMA PRIMERA.-</b> <b>LAS PARTES</b> manifiestan que no existe dolo ni cláusula
                contraria a derecho, no dándose los supuestos de ignorancia ni extrema necesidad,
                conscientes de su alcance y valor jurídico lo firman de conformidad.
            </p>
            <p>
                <b>DECIMA SEGUNDA.-</b> <b>COMPETENCIA.</b> Para el cumplimiento y resolución del presente
                contrato las partes se someten a la jurisdicción y competencia de los Juzgados de la Ciudad
                de México, renunciando expresamente a la jurisdicción de futuro domicilio.
            </p>
            <table style="width: 100%">
                <tr>
                    <td colspan="3" style="text-align: center; height: 90px">
                        <b>Ciudad de México, a {$datos['FECHA_F_LEGAL']}</b>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 45%">
                        <b>EL MUTUANTE Y/O PRESTAMISTA</b>
                    </td>
                    <td style="width: 10%"></td>
                    <td style="text-align: center; width: 45%">
                        <b>EL MUTUARIO Y/O PRESTATARIO</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 80px"></td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 45%; border-top: 1px solid">
                        <b>{$datos['NOMBRE']}</b>
                    </td>
                    <td style="width: 10%"></td>
                    <td style="text-align: center; width: 45%; border-top: 1px solid">
                        <b>ANTONIO LORENZO HERNÁNDEZ</b>
                    </td>
                </tr>
            </table>
        </div>
        <div style="page-break-after: always"></div>
        <div>
            <h3 class="fechaTitulo">Ciudad de México a {$datos['FECHA_F_LEGAL']}</h3>
            <p>
                El suscrito <b>{$datos['NOMBRE']}</b>, a través de la presente y bajo
                protesta de decir verdad, manifiesto que los recursos que he exhibido y que se señalan a
                detalle en el <b>CONTRATO DE MUTUO</b> de fecha {$datos['FECHA_F_LEGAL']}, celebrado en mi carácter de
                <b>“MUTUANTE Y/O PRESTAMISTA”</b> con el <b>C. ANTONIO LORENZO HERNÁNDEZ</b> en su carácter de “MUTUARIO
                Y/O PRESTATARIO” provienen de un <b>ORIGEN LÍCITO</b>, por lo que desde este momento señalo que no
                me encuentro en ninguno de los supuestos referidos en el artículo 400 Bis del Código Penal
                Federal en vigor.
            </p>
            <p>
                De la misma forma, <b>DESLINDO al “MUTUARIO Y/O PRESTATARIO”</b> de cualquier tema que pueda
                presentarse en el futuro y que sea relacionado con los recursos económicos del suscrito en
                los diversos actos jurídicos que se celebren.
            </p>
            <table style="width: 100%; padding-top: 150px">
                <tr>
                    <td style="text-align: center; width: 33%"></td>
                    <td style="text-align: center; width: 33%">
                        <b>ATENTAMENTE</b>
                    </td>
                    <td style="text-align: center; width: 33%"></td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 100px"></td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 25%"></td>
                    <td style="text-align: center; width: 50%; border-top: 1px solid">
                        <b>{$datos['NOMBRE']}</b>
                    </td>
                    <td style="text-align: center; width: 25%"></td>
                </tr>
            </table>
        </div>    
        html;
    }

    //********************BORRAR????********************//
    public function SolicitudRetiroHistorial()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_solicitud_retiro_historial");
    }

    //////////////////////////////////////////////////
    public function ReimprimeTicketSolicitudes()
    {
        $extraFooter = <<<html
        <script>
            {$this->mensajes}
            {$this->configuraTabla}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            {$this->addParametro}
            {$this->validaFIF}
            {$this->consultaServidor}
            {$this->valida_MCM_Complementos}
         
            $(document).ready(() => {
                configuraTabla("solicitudes");
            })
             
            const buscar = () => {
                const datos = []
                addParametro(datos, "usuario", "{$_SESSION['usuario']}")
                addParametro(datos, "fechaI", document.querySelector("#fechaI").value)
                addParametro(datos, "fechaF", document.querySelector("#fechaF").value)
                addParametro(datos, "estatus", document.querySelector("#estatus").value)
                 
                consultaServidor("/Ahorro/GetSolicitudesTickets/", $.param(datos), (respuesta) => {
                    $("#solicitudes").DataTable().destroy()
                     
                    if (respuesta.datos == "") showError("No se encontraron solicitudes de retiro en el rango de fechas seleccionado.")
                     
                    $("#solicitudes tbody").html(respuesta.datos)
                    configuraTabla("solicitudes")
                })
            }
             
            const impTkt = async (tkt) => {
                if (!await valida_MCM_Complementos()) return
                 
                imprimeTicket(tkt)
            }
        </script>
        html;

        $tabla = self::GetSolicitudesTickets();
        $tabla = $tabla['success'] ? $tabla['datos'] : "";

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Solicitudes de reimpresión Tickets", $this->XLSX)));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::set('fecha', date("Y-m-d"));
        View::set('fecha_actual', date("Y-m-d H:i:s"));
        View::render("caja_menu_reimprime_ticket_historial");
    }

    public function GetSolicitudesTickets()
    {
        $usuario = $_POST['usuario'] ?? $this->__usuario;
        $fi = $_POST['fechaI'] ?? "2024-05-17"; //date('Y-m-d');
        $ff = $_POST['fechaF'] ?? "2024-05-17"; //date('Y-m-d');
        $estatus = $_POST['estatus'] ?? "";

        $Consulta = AhorroDao::ConsultaSolicitudesTickets([
            'usuario' => $usuario,
            'fechaI' => $fi,
            'fechaF' => $ff,
            'estatus' => $estatus
        ]);

        $tabla = "";
        foreach ($Consulta as $key => $value) {
            if ($value['AUTORIZA'] == 0) {
                $autoriza = "PENDIENTE";

                $imprime = "<span class='count_top' style='font-size: 22px'><i class='fa fa-clock-o' style='color: #ac8200'></i></span>";
            } else if ($value['AUTORIZA'] == 1) {
                $autoriza = "ACEPTADO";

                $imprime = <<<html
                    <button type="button" class="btn btn-success btn-circle" onclick="impTkt('{$value['CDGTICKET_AHORRO']}');"><i class="fa fa-print"></i></button>
                html;
            } else if ($value['AUTORIZA'] == 2) {
                $imprime = '<span class="count_top" style="font-size: 22px"><i class="fa fa-close" style="color: #ac1d00"></i></span>';
                $autoriza = "RECHAZADO";
            }

            if ($value['CDGPE_AUTORIZA'] == '') {
                $autoriza_nombre = "-";
            } else if ($value['CDGPE_AUTORIZA'] != '') {
                $autoriza_nombre = $value['CDGPE_AUTORIZA'];
            }

            $tabla .= <<<html
            <tr style="padding: 0px !important;">
                <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                <td style="padding: 0px !important;" width="45" nowrap=""><span class="count_top" style="font-size: 14px"> &nbsp;&nbsp;<i class="fa fa-barcode" style="color: #787b70"></i> </span>{$value['CDG_CONTRATO']} &nbsp;</td>
                <td style="padding: 0px !important;">{$value['FREGISTRO']} </td>
                <td style="padding: 0px !important;">{$value['MOTIVO']}</td>
                <td style="padding: 0px !important;"> {$autoriza}</td>
                <td style="padding: 0px !important;">{$autoriza_nombre}</td>
                <td style="padding: 0px !important;" class="center">
                {$imprime}
                </td>
            </td>
            html;
        }

        $r = ["success" => true, "datos" => $tabla];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') echo json_encode($r);
        else return $r;
    }

    public function ReimprimeTicket()
    {
        $extraHeader = <<<html
        <title>Reimprime Tickets</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
           "lengthMenu": [
                    [10, 50, -1],
                    [10, 50, 'Todos'],
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
           
            $(document).ready(function(){
            $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
           "lengthMenu": [
                    [10, 50, -1],
                    [10, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones1 input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
        });
           
        function Reimprime_ticket(folio)
        {
              
              $('#modal_ticket').modal('show');
              document.getElementById("folio").value = folio;
             
        }
        
        function enviar_add_sol()
        {
             const showSuccess = (mensaje) => swal(mensaje, { icon: "success" } )
             
             $('#modal_ticket').modal('hide');
             swal({
                   title: "¿Está segura de continuar?",
                   text: "",
                   icon: "warning",
                   buttons: ["Cancelar", "Continuar"],
                   dangerMode: false
                   })
                   .then((willDelete) => {
                   if (willDelete) {
                        $.ajax({
                        type: 'POST',
                        url: '/Ahorro/AddSolicitudReimpresion/',
                        data: $('#Add').serialize(),
                        success: function(respuesta) {
                        if(respuesta=='1')
                        {
                           return showSuccess("Solicitud enviada a tesorería." );
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
                                    $('#modal_ticket').modal('show');
                              }
                        });
        }
        </script>
html;

        $Consulta = AhorroDao::ConsultaTickets($this->__usuario);
        $tabla = "";

        foreach ($Consulta as $key => $value) {
            $monto = number_format($value['MONTO'], 2);

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                   <td style="padding: 0px !important;">{$value['CODIGO']} </td>
                    <td style="padding: 0px !important;" width="45" nowrap=""><span class="count_top" style="font-size: 14px"> &nbsp;&nbsp;<i class="fa fa-barcode" style="color: #787b70"></i> </span>{$value['CDG_CONTRATO']} &nbsp;</td>
                    <td style="padding: 0px !important;">{$value['FECHA_ALTA']} </td>
                    <td style="padding: 0px !important;">$ {$monto}</td>
                    <td style="padding: 0px !important;">{$value['TIPO_AHORRO']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['CDGPE']}</td>
                    <td style="padding: 0px !important;" class="center">
                         <button type="button" class="btn btn-success btn-circle" onclick="Reimprime_ticket('{$value['CODIGO']}');"><i class="fa fa-print"></i></button>
                    </td>
                </td>
html;
        }

        $fecha_y_hora = date("Y-m-d H:i:s");



        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::set('fecha_actual', $fecha_y_hora);
        View::render("caja_menu_reimprime_ticket");
    }

    public function AddSolicitudReimpresion()
    {
        $solicitud = new \stdClass();

        $solicitud->_folio = MasterDom::getData('folio');
        $solicitud->_descripcion = MasterDom::getData('descripcion');
        $solicitud->_motivo = MasterDom::getData('motivo');
        $solicitud->_cdgpe = $this->__usuario;


        $id = AhorroDao::insertSolicitudAhorro($solicitud);

        return $id;
    }

    //////////////////////////////////////////////////
    public function Calculadora()
    {
        $extraHeader = <<<html
        <title>Caja Cobrar</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
        <script>
           
        </script>
html;

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("caja_menu_calculadora");
    }

    public function CalculadoraView()
    {
        View::render("calculadora_view");
    }

    public function Retiros()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
                {$this->configuraTabla}
                {$this->descargaExcel}
                {$this->parseaNumero}
                {$this->formatoMoneda}

                const idTabla = "retiros"

                const reporteSolicitudesRetiro = () => {
                    const fechaI = $("#fechaI").val()
                    const fechaF = $("#fechaF").val()

                    if (new Date(fechaI) > new Date(fechaF)) return showError("La fecha inicial no puede ser mayor a la fecha final.")

                    consultaServidor("/Ahorro/reporteSolicitudesRetiro/", { fechaI, fechaF }, (resultado) => {
                        if (!resultado.success) return showError(resultado.mensaje)
                        resultadoOK(resultado.datos)
                    })
                }

                const resultadoOK = (datos) => {
                    datos = datos.map((dato) => {
                        dato.CANT_SOLICITADA = "$ " + formatoMoneda(dato.CANT_SOLICITADA)
                        const region = dato.REGION + " - " + dato.NOMBRE_REGION
                        const sucursal = dato.SUCURSAL + " - " + dato.NOMBRE_SUCURSAL

                        return [
                            dato.ID,
                            region,
                            sucursal,
                            dato.CDGNS,
                            dato.CANT_SOLICITADA,
                            dato.FECHA_CREACION,
                            dato.FECHA_ENTREGA,
                            dato.FECHA_DEVOLUCION || "-",
                            dato.ESTATUS,
                            dato.CDGPE_ADMINISTRADORA
                        ]
                    })

                    actualizaDatosTabla(idTabla, datos)
                    $(".resultado").toggleClass("conDatos", true)
                }

                const descargaReporte = () => {
                    const fechaF = $("#fechaF").val()
                    const fechaI = $("#fechaI").val()

                    const datos = {
                        fechaI,
                        fechaF
                    }

                    const params = new URLSearchParams(datos).toString()
                    descargaExcel("/Ahorro/excelReporteSolicitudesRetiro/?" + params)
                }

                $(document).ready(() => {
                    const hoy = new Date().getDate()
                    const fechaI = new Date().setDate(hoy - 7);
                    const fechaF = new Date().setDate(hoy + 7);
                    $("#fechaI").val(new Date(fechaI).toISOString().split("T")[0]);
                    $("#fechaF").val(new Date(fechaF).toISOString().split("T")[0]);
                    $("#fechaI").on("change", reporteSolicitudesRetiro)
                    $("#fechaF").on("change", reporteSolicitudesRetiro)

                    $("#btnDescargarReporte").on("click", descargaReporte)

                    reporteSolicitudesRetiro()
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Retiros Ahorro")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("ahorro_retiros");
    }

    public function reporteSolicitudesRetiro()
    {
        echo json_encode(AhorroDao::ReporteSolicitudesRetiro($_POST));
    }

    public function excelReporteSolicitudesRetiro()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('ID', 'ID retiro'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_REGION', 'Región'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_SUCURSAL', 'Sucursal'),
            \PHPSpreadsheet::ColumnaExcel('CDGNS', 'No. Crédito'),
            \PHPSpreadsheet::ColumnaExcel('CANT_SOLICITADA', 'Monto', ['estilo' => $estilos['moneda'], 'total' => true]),
            \PHPSpreadsheet::ColumnaExcel('FECHA_CREACION', 'Fecha Registro', ['estilo' => $estilos['fecha_hora']]),
            \PHPSpreadsheet::ColumnaExcel('FECHA_SOLICITUD', 'Fecha Solicitud', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('FECHA_ENTREGA', 'Fecha Entrega Programada', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('FECHA_DEVOLUCION', 'Fecha Devolución', ['estilo' => $estilos['fecha_hora']]),
            \PHPSpreadsheet::ColumnaExcel('CDGPE_ADMINISTRADORA', 'Administradora'),
            \PHPSpreadsheet::ColumnaExcel('ESTATUS', 'Estatus'),
        ];

        $filas = AhorroDao::ReporteSolicitudesRetiro($_GET);
        if ($filas['success']) $filas = $filas['datos'];
        else $filas = [];

        \PHPSpreadsheet::DescargaExcel('Reporte Solicitudes Retiro', 'Reporte', 'Retiros', $columnas, $filas);
    }

    public function SolicitudesRetiroAdmin()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
                {$this->configuraTabla}
                {$this->descargaExcel}
                {$this->parseaNumero}
                {$this->formatoMoneda}

                const idTabla = "retiros"

                const getRetiros = () => {
                    const params = { 
                        fechaF: $("#fechaF").val(),
                        fechaI: $("#fechaI").val()
                     }

                    consultaServidor("/Ahorro/getRetirosAdmin/", params, (resultado) => {
                        if (!resultado.success) return showError(resultado.mensaje)
                        resultadoOK(resultado.datos)
                    })
                }

                const resultadoOK = (datos) => {
                    datos = datos.map((dato) => {
                        dato.CANT_SOLICITADA = "$ " + formatoMoneda(dato.CANT_SOLICITADA)
                        const acciones = [
                            {
                                icono: "fa-eye",
                                texto: "Ver detalle",
                                funcion: "verDetalle(" + dato.ID + ")"
                            }
                        ]

                        if (dato.ESTATUS === "V") acciones.push(
                            {
                                icono: "fa-check-circle text-success",
                                texto: "Confirmar Entrega",
                                funcion: "confirmarEntrega(" + dato.ID + ")"
                            },
                            {
                                icono: "fa-undo text-warning",
                                texto: "Devolver Retiro",
                                funcion: "comentarioDevolucion(" + dato.ID + ")"
                            }
                        )

                        if (dato.ESTATUS === "P" && !dato.ESTATUS_TESORERIA) acciones.push(
                            "divisor",
                            {
                                icono: "fa-times-circle text-danger",
                                texto: "Cancelar solicitud",
                                funcion: "capturarComentario(" + dato.ID + ")"
                            }
                        )

                        return [
                            dato.ID,
                            dato.CDGNS,
                            dato.CANT_SOLICITADA,
                            dato.FECHA_SOLICITUD,
                            dato.FECHA_ENTREGA,
                            dato.FECHA_ENTREGA_REAL || "-",
                            dato.FECHA_DEVOLUCION || "-",
                            dato.REGION,
                            dato.SUCURSAL,
                            dato.CDGPE_ADMINISTRADORA,
                            getBadge(dato.ESTATUS),
                            menuAcciones(acciones)
                        ]
                    })

                    actualizaDatosTabla(idTabla, datos)
                    $(".resultado").toggleClass("conDatos", true)
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
                                if (opcion === "divisor") return `<hr class="dropdown-divider" style="margin: 0; padding: 0;">`
                                return opcion
                            }

                            return '<li><a href="' + (opcion.href || "javascript:;") + 
                            '" onclick="' + opcion.funcion + '">' +
                            '<i class="fa ' + opcion.icono + '">&nbsp;</i>' + opcion.texto + '</a></li>'
                        })
                        .join("")

                    return '<div class="dropdown"><button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></button><ul class="dropdown-menu" style="left: -100%;">' + acciones + '</ul></div>'
                }

                const verDetalle = (idRetiro) => {
                    consultaServidor("/AhorroConsulta/GetRetiroById", { id: idRetiro }, (res) => {
                        if (!res.success) {
                            return Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: res.mensaje
                            });
                        }
                        
                        const datos = res.datos;
                        
                        $("#detalle_id_retiro").val(datos.ID || "");
                        $("#detalle_credito").val(datos.CDGNS || "");
                        $("#detalle_fecha_creacion").val(datos.FECHA_CREACION || "");
                        $("#detalle_fecha_solicitud").val(datos.FECHA_SOLICITUD || "");
                        $("#detalle_fecha_entrega").val(datos.FECHA_ENTREGA || "");
                        $("#detalle_fecha_entrega_real").val(datos.FECHA_ENTREGA_REAL || "");
                        $("#detalle_cantidad_solicitada").val("$" + formatoMoneda(datos.CANT_SOLICITADA || 0));
                        $("#detalle_estatus").val(datos.ESTATUS_ETIQUETA || "")

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
                        
                        $("#btnVerComprobante").off("click").on("click", function() {
                            verComprobante(idRetiro);
                        });
                        
                        $('#modalDetalle .nav-tabs a[href="#tabGeneral"]').tab('show');
                        $("#modalDetalle").modal("show");
                    });
                }

                const verComprobante = (idRetiro) => {
                    $("#modalDetalle").modal("hide");
                    $("#comprobanteImg").hide();
                    $("#loadingImg").show();
                    $("#comprobanteImg").attr("src", "/AhorroConsulta/GetImgSolicitud/?id=" + idRetiro + "&tipo=comprobante");

                    $("#modalComprobante").modal("show");
                }

                const capturarComentario = (retiro) => {
                    $("#idRetiroCancelar").val(retiro)
                    $("#motivoCancelacion").val("")
                    
                    $("#modalCancelarSolicitud").modal("show")
                }

                const cancelarSolicitud = () => {
                    const comentario = $("#motivoCancelacion").val().trim()
                    if (comentario === "") return showError("Debe capturar el motivo para cancelar la solicitud de retiro.")

                    const params = { 
                        retiro: $("#idRetiroCancelar").val(),
                        comentario,
                        usuario: "{$_SESSION['usuario']}"
                     }

                    consultaServidor("/Ahorro/CancelarRetiro/",
                        params,
                        (resultado) => {
                            if (!resultado.success) return showError(resultado.mensaje)
                            $("#modalCancelarSolicitud").modal("hide")
                            showSuccess("La solicitud de retiro ha sido cancelada.")
                            .then(getRetiros)
                        }
                    )
                }

                const confirmarEntrega = (id) => {
                    confirmarMovimiento("Confirmar entrega al cliente", "¿Desea confirmar la entrega de este retiro?")
                    .then((continuar) => {
                        if (continuar) {
                            consultaServidor("/Ahorro/ConfirmarEntregaRetiroAhorro", { id }, (res) => {
                                if (!res.success) return showError(res.mensaje)
                                showSuccess(res.mensaje)
                                getRetiros()
                            })
                        }
                    });
                }

                const comentarioDevolucion = (retiro) => {
                    $("#idRetiroDevolucion").val(retiro)
                    $("#motivoDevolucion").val("")
                    
                    $("#modalDevolverRetiro").modal("show")
                }

                const devolverRetiro = () => {
                    const comentario = $("#motivoDevolucion").val().trim()
                    if (comentario === "") return showError("Debe capturar el motivo que justifique la devolución.")

                    const params = { 
                        id: $("#idRetiroDevolucion").val(),
                        comentario,
                        usuario: "{$_SESSION['usuario']}"
                     }

                    consultaServidor("/Ahorro/DevolverRetiro/",
                        params,
                        (resultado) => {
                            if (!resultado.success) return showError(resultado.mensaje)

                            $("#modalDevolverRetiro").modal("hide")
                            showSuccess("El retiro ha sido devuelto.")
                            .then(getRetiros)
                        }
                    )
                }

                $(document).ready(() => {
                    const hoy = new Date().getDate()
                    const fechaI = new Date().setDate(hoy - 7)
                    $("#fechaI").val(new Date(fechaI).toISOString().split("T")[0])
                    $("#fechaF").val(new Date().toISOString().split("T")[0])

                    $("#fechaI, #fechaF").change(getRetiros)
                    $("#btnCancelarSolicitud").click(cancelarSolicitud)
                    $("#btnDevolverRetiro").click(devolverRetiro)
                    
                    $("#comprobanteImg").on("load", function() {
                        $("#loadingImg").hide()
                        $(this).show()
                    })  
                    
                    configuraTabla(idTabla)
                    getRetiros()
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Cancelar Solicitudes de Retiro")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("ahorro_consulta_admin");
    }

    public function getRetirosAdmin()
    {
        $r = AhorroDao::getRetirosAdmin($_POST);
        echo json_encode($r);
    }

    public function CancelarRetiro()
    {
        $r = AhorroDao::CancelarRetiro($_POST);
        echo json_encode($r);
    }


    public function ConfirmarEntregaRetiroAhorro()
    {
        $registro = AhorroDao::confirmarEntregaRetiroAhorro($_POST);
        if ($registro['success']) {
            $destinatarios = $this->GetDestinatarios(AhorroDao::GetDestinatarios_Aplicacion(4));

            if (count($destinatarios) > 0) {
                $datos = AhorroDao::getInfoCorreoRetiroFinalizado($_POST);
                if ($datos['success']) {
                    $plantilla = self::Plantilla_Retiro_Finalizado($datos['datos']);
                    Mensajero::EnviarCorreo(
                        $destinatarios,
                        'Entrega de retiro de ahorro confirmada',
                        Mensajero::Notificaciones($plantilla)
                    );
                }
            }
        }
        echo json_encode($registro);
    }

    public function DevolverRetiro()
    {
        $registro = AhorroDao::devolverRetiro($_POST);

        if ($registro['success']) {
            $destinatarios = $this->GetDestinatarios(AhorroDao::GetDestinatarios_Aplicacion(4));

            if (count($destinatarios) > 0) {
                $datos = AhorroDao::getInfoCorreoRetiroFinalizado($_POST);
                if ($datos['success']) {
                    $plantilla = self::Plantilla_Retiro_Finalizado($datos['datos']);
                    Mensajero::EnviarCorreo(
                        $destinatarios,
                        'Devolución de retiro de ahorro',
                        Mensajero::Notificaciones($plantilla)
                    );
                }
            }
        }

        echo json_encode($registro);
    }

    public function Plantilla_Retiro_Finalizado($datos)
    {
        $titulo = $datos['ESTATUS'] == 'E' ? '✅ Retiro ENTREGADO.' : '❌ Retiro DEVUELTO.';
        $motivo = '';

        if ($datos['ESTATUS'] == 'D') {
            $motivo = "<li>🔸<b>Motivo de devolución:</b> {$datos['COMENTARIO_DEVOLUCION']}</li>";
        }

        return <<<HTML
            <!-- Encabezado -->
            <h2 style="text-align: center">{$titulo}</h2>
            <!-- Información General -->
            <div style="margin: 30px 0">
                <h3 style="color: #007bff; border-bottom: 1px solid #ddd; padding-bottom: 5px">
                    📄 Información del retiro
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; color: #555">
                    <li>🔸<b>ID:</b> {$datos['ID']}</li>
                    <li>🔸<b>Cliente:</b> {$datos['CLIENTE']} - {$datos['NOMBRE_CLIENTE']}</li>
                    <li>🔸<b>Crédito:</b> {$datos['CREDITO']}</li>
                    <li>🔸<b>Fecha de captura:</b> {$datos['FECHA_CREACION']}</li>
                    <li>🔸<b>Fecha de entrega programada:</b> {$datos['FECHA_ENTREGA_PROGRAMADA']}</li>
                    <li>🔸<b>Región:</b> {$datos['REGION']} - {$datos['NOMBRE_REGION']}</li>
                    <li>🔸<b>Agencia:</b> {$datos['SUCURSAL']} - {$datos['NOMBRE_SUCURSAL']}</li>
                    {$motivo}
                </ul>
            </div>
        HTML;
    }
}
