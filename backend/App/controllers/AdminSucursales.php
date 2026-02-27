<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use \App\models\AdminSucursales as AdminSucursalesDao;
use \App\models\CajaAhorro as CajaAhorroDao;

class AdminSucursales extends Controller
{
    private $_contenedor;
    private $XLSX = '<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js" integrity="sha512-r22gChDnGvBylk90+2e/ycr3RVrDi8DIOkIGNhJlKfuyQM4tIRAI062MaV8sfjQKYVGjOBaZBOA87z+IhZE9DA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>';
    private $noSubmit = 'const noSUBMIT = (e) => e.preventDefault()';
    private $validarYbuscar = 'const validarYbuscar = (e) => {
        if (e.keyCode < 9 || e.keyCode > 57) e.preventDefault()
        if (e.keyCode === 13) buscar()
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
    private $addParametro = 'const addParametro = (parametros, newParametro, newValor) => {
        parametros.push({ name: newParametro, value: newValor })
    }';
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
    private $buscaCliente = 'const buscaCliente = (t) => {
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
                 
                llenaDatosCliente(respuesta.datos)
            })
        
        document.querySelector("#btnBskClnt").disabled = false
    }';
    private $muestraPDF = <<<script
    const muestraPDF = (titulo, ruta) => {
        let plantilla = '<!DOCTYPE html>'
            plantilla += '<html lang="es">'
            plantilla += '<head>'
            plantilla += '<meta charset="UTF-8">'
            plantilla += '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
            plantilla += '<link rel="shortcut icon" href="" + host + "/img/logo.png">'
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
    const imprimeTicket = (ticket, sucursal = '', copia = true) => {
        const host = window.location.origin
        const titulo = 'Ticket: ' + ticket
        const ruta = host + '/Ahorro/Ticket/?'
        + 'ticket=' + ticket
        + '&sucursal=' + sucursal
        + (copia ? '&copiaCliente=true' : '')
        
        muestraPDF(titulo, ruta)
    }
    script;
    private $exportaExcel = 'const exportaExcel = (id, nombreArchivo, nombreHoja = "Reporte") => {
        const tabla = document.querySelector("#" + id)
        const wb = XLSX.utils.book_new()
        const ws = XLSX.utils.table_to_sheet(tabla)
        XLSX.utils.book_append_sheet(wb, ws, nombreHoja)
        XLSX.writeFile(wb, nombreArchivo + ".xlsx")
    }';
    private $getFecha = 'const getFecha = (fecha) => {
        const f = new Date(fecha + "T06:00:00Z")
        return f.toLocaleString("es-MX", { year: "numeric", month:"2-digit", day:"2-digit" })
    }';

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    //********************Saldos y movimientos de efectivo en sucursal********************//
    // Reporte de saldos diarios por sucursal
    public function SaldosDiarios()
    {
        $extraFooter = <<<HTML
        <script>
            {$this->mensajes}
            {$this->configuraTabla}
            {$this->exportaExcel}
            {$this->consultaServidor}
            {$this->validaFIF}
            {$this->addParametro}
         
            $(document).ready(() => {
                configuraTabla("saldos")
            })
             
            const imprimeExcel = () => exportaExcel("saldos", "Saldos de sucursales")
             
            const consultaSaldos = () => {
                const fechaI = document.querySelector("#fechaI").value
                const fechaF = document.querySelector("#fechaF").value
                const datos = []
                addParametro(datos, "fechaI", fechaI)
                addParametro(datos, "fechaF", fechaF)
                
                consultaServidor(
                    "/AdminSucursales/GetSaldosSucursal/",
                    $.param(datos),
                    (respuesta) => {
                        $("#saldos").DataTable().destroy()
                     
                        if (respuesta.datos == "") showError("No se encontraron saldos para el rango de fechas seleccionado.")
                         
                        $("#saldos tbody").html(respuesta.datos)
                        configuraTabla("saldos")
                    }
                )
            }
        </script>
        HTML;

        // $filas = self::GetSaldosSucursal();
        $filas = ""; //$filas['success'] ? $filas['datos'] : "";

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Saldos de sucursales")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        // View::set('filas', $filas);
        // View::set('fechaI', date('Y-m-d'));
        // View::set('fechaF', date('Y-m-d'));
        View::render("AdminSucursales/caja_admin_saldos_dia");
    }

    public function GetSaldosSucursal()
    {
        $fechaI = $_POST['fechaI'] ?? date('Y-m-d');
        $fechaF = $_POST['fechaF'] ?? date('Y-m-d');

        $saldos = AdminSucursalesDao::GetSaldosSucursales(['fechaI' => $fechaI, 'fechaF' => $fechaF]);
        $filas = "";
        foreach ($saldos as $sucursal) {
            $filas .= "<tr>";
            foreach ($sucursal as $key => $valor) {
                if ($key === "PORCENTAJE") {
                    $rgb = "";

                    if ($valor > 100) $valor = "Requiere un retiro por saldo excedente.";
                    else if ($valor < 0) $valor = "Requiere un fondeo por saldo insuficiente.";
                    else {
                        $p = max(0, min(100, $sucursal['PORCENTAJE']));
                        if ($p <= 50) $rgb = "color: rgb(255, " . (255 * $p / 50) . ", 0)";
                        else $rgb = "color: rgb(" . (255 - 255 * ($p - 50) / 50) . ", 255, 0)";
                        $valor = number_format($valor, 2) . "%";
                    }

                    $filas .= "<th style='font-weight: bold; " . $rgb . "'>" . $valor . "</th>";
                } else $filas .= "<th>{$valor}</th>";
            }

            $filas .= "</tr>";
        }

        $r = ["success" => true, "datos" => $filas];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') echo json_encode($r);
        else return $r;
    }

    // Validar Transacciones Día
    public function CierreDia()
    {
        $extraFooter = <<<script
       
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Arqueo de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("AdminSucursales/caja_admin_cierre_dia");
    }

    // Ingreso de efectivo a sucursal
    public function FondearSucursal()
    {
        $extraFooter = <<<script
        <script>
            let montoMaximo = 0
            let montoMinimo = 0
            let valKD = false
            let codigoSEA = 0
            {$this->mensajes}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->validarYbuscar}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
         
            const buscar = () => {
                const sucursal = document.querySelector("#sucursalBuscada").value
                if (sucursal === "0") return showError("Seleccione una sucursal")
                consultaServidor(
                    "/AdminSucursales/GetDatos/",
                    { sucursal },
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        if (parseFloat(res.datos.SALDO) >= parseFloat(res.datos.MONTO_MAX)) return showError("La sucursal " + sucursal + " cuenta con el saldo máximo permitido (" + parseFloat(res.datos.MONTO_MAX).toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + ") para su operación.")
                        document.querySelector("#sucursalBuscada").value = ""
                        document.querySelector("#codigoSuc").value = res.datos.CODIGO_SUCURSAL
                        document.querySelector("#nombreSuc").value = res.datos.NOMBRE_SUCURSAL
                        document.querySelector("#codigoCajera").value = res.datos.CODIGO_CAJERA
                        document.querySelector("#nombreCajera").value = res.datos.NOMBRE_CAJERA
                        document.querySelector("#fechaCierre").value = res.datos.FECHA_CIERRE
                        document.querySelector("#saldoActual").value = parseFloat(res.datos.SALDO).toFixed(2)
                        document.querySelector("#montoOperacion").value = "0.00"
                        document.querySelector("#saldoFinal").value = parseFloat(res.datos.SALDO).toFixed(2)
                        document.querySelector("#monto").disabled = false
                        document.querySelector("#monto").focus()
                        montoMinimo = parseFloat(res.datos.MONTO_MIN)
                        montoMaximo = parseFloat(res.datos.MONTO_MAX)
                        codigoSEA = res.datos.CODIGO
                    }
                )
            }
             
            const limpiarCampos = () => {
                document.querySelector("#codigoSuc").value = ""
                document.querySelector("#nombreSuc").value = ""
                document.querySelector("#codigoCajera").value = ""
                document.querySelector("#nombreCajera").value = ""
                document.querySelector("#fechaCierre").value = ""
                document.querySelector("#saldoActual").value = "0.00"
                document.querySelector("#montoOperacion").value = "0.00"
                document.querySelector("#saldoFinal").value = "0.00"
                document.querySelector("#monto").value = ""
                document.querySelector("#monto").disabled = true
            }
             
            const validaMonto = () => {
                const montoIngresado = document.querySelector("#monto")
                if (!parseFloat(montoIngresado.value)) {
                    document.querySelector("#btnFondear").disabled = true
                    document.querySelector("#saldoFinal").value = document.querySelector("#saldoActual").value
                    document.querySelector("#montoOperacion").value = "0.00"
                    return
                }
                 
                let monto = parseFloat(montoIngresado.value) || 0
                let disponible = montoMaximo - parseFloat(document.querySelector("#saldoActual").value)
                 
                if (monto > disponible) {
                    monto = disponible
                    showError("La sucursal no puede tener un saldo mayor a " + montoMaximo.toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + ", si requiere un monto mayor comuníquese con el administrador.")
                    montoIngresado.value = monto
                }
                 
                const valor = montoIngresado.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    montoIngresado.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                 
                document.querySelector("#montoOperacion").value = monto.toFixed(2)
                const nuevoSaldo = (monto + parseFloat(document.querySelector("#saldoActual").value)).toFixed(2)
                document.querySelector("#saldoFinal").value = nuevoSaldo > 0 ? nuevoSaldo : "0.00"
                document.querySelector("#monto_letra").value = numeroLetras(parseFloat(montoIngresado.value))
                document.querySelector("#btnFondear").disabled = !(nuevoSaldo <= montoMaximo && nuevoSaldo >= montoMinimo)
                document.querySelector("#tipSaldo").innerText = ""
                if (nuevoSaldo > montoMaximo) document.querySelector("#tipSaldo").innerText = "El saldo final no puede ser mayor a " + montoMaximo.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                if (nuevoSaldo < montoMinimo) document.querySelector("#tipSaldo").innerText = "El saldo final no puede ser menor a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
            }
             
            const fondear = () => {
                const monto = parseFloat(document.querySelector("#saldoFinal").value)
                if (monto < montoMinimo) return showError("El saldo final debe ser mayor o igual a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" }))
                
                let datos = $("#datos").serializeArray()
                addParametro(datos, "codigoSEA", codigoSEA)
                addParametro(datos, "usuario", '{$_SESSION["usuario"]}')
                 
                consultaServidor(
                    "/AdminSucursales/AplicarFondeo/",
                    datos,
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        showSuccess(res.mensaje).then(() => {
                            window.location.reload()
                        })
                    }
                )
            }
        </script>
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Arqueo de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("AdminSucursales/caja_admin_fondeo");
    }

    public function AplicarFondeo()
    {
        $res = AdminSucursalesDao::AplicarFondeo($_POST);
        echo $res;
    }

    // Egreso de efectivo de sucursal
    public function RetiroSucursal()
    {
        $extraFooter = <<<script
        <script>
            let montoMaximo = 0
            let montoMinimo = 0
            let valKD = false
            let codigoSEA = 0
            {$this->mensajes}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->validarYbuscar}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
         
            const buscar = () => {
                const sucursal = document.querySelector("#sucursalBuscada").value
                if (sucursal === "0") return showError("Seleccione una sucursal")
                consultaServidor(
                    "/AdminSucursales/GetDatos/",
                    { sucursal },
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        document.querySelector("#sucursalBuscada").value = ""
                        document.querySelector("#codigoSuc").value = res.datos.CODIGO_SUCURSAL
                        document.querySelector("#nombreSuc").value = res.datos.NOMBRE_SUCURSAL
                        document.querySelector("#codigoCajera").value = res.datos.CODIGO_CAJERA
                        document.querySelector("#nombreCajera").value = res.datos.NOMBRE_CAJERA
                        document.querySelector("#fechaCierre").value = res.datos.FECHA_CIERRE
                        document.querySelector("#saldoActual").value = parseFloat(res.datos.SALDO).toFixed(2)
                        document.querySelector("#montoOperacion").value = "0.00"
                        document.querySelector("#saldoFinal").value = parseFloat(res.datos.SALDO).toFixed(2)
                        document.querySelector("#monto").disabled = false
                        document.querySelector("#monto").focus()
                        montoMinimo = parseFloat(res.datos.MONTO_MIN)
                        montoMaximo = parseFloat(res.datos.MONTO_MAX)
                        codigoSEA = res.datos.CODIGO
                    }
                )
            }
             
            const limpiarCampos = () => {
                document.querySelector("#codigoSuc").value = ""
                document.querySelector("#nombreSuc").value = ""
                document.querySelector("#codigoCajera").value = ""
                document.querySelector("#nombreCajera").value = ""
                document.querySelector("#fechaCierre").value = ""
                document.querySelector("#saldoActual").value = "0.00"
                document.querySelector("#montoOperacion").value = "0.00"
                document.querySelector("#saldoFinal").value = "0.00"
                document.querySelector("#monto").value = ""
                document.querySelector("#monto").disabled = true
                document.querySelector("#tipSaldo").innerText = ""
            }
             
            const validaMonto = () => {
                const montoIngresado = document.querySelector("#monto")
                if (!parseFloat(montoIngresado.value)) {
                    document.querySelector("#btnFondear").disabled = true
                    document.querySelector("#montoOperacion").value = "0.00"
                    return
                }
                
                let monto = parseFloat(montoIngresado.value) || 0
                const saldoActual = parseFloat(document.querySelector("#saldoActual").value)
                let nuevoSaldo = saldoActual - monto
                 
                if (nuevoSaldo < montoMinimo) {
                    monto = saldoActual - montoMinimo
                    nuevoSaldo = saldoActual - monto
                    showError("La sucursal no puede tener un saldo menor a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" }) + ", si requiere que la sucursal tenga un monto menor comuníquese con el administrador.")
                    montoIngresado.value = monto
                }
                 
                const valor = montoIngresado.value.split(".")
                if (valor[1] && valor[1].length > 2) {
                    montoIngresado.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
                }
                 
                document.querySelector("#montoOperacion").value = monto.toFixed(2)
                document.querySelector("#saldoFinal").value = nuevoSaldo > 0 ? nuevoSaldo.toFixed(2) : "0.00"
                document.querySelector("#monto_letra").value = numeroLetras(parseFloat(montoIngresado.value))
                document.querySelector("#btnFondear").disabled = !(nuevoSaldo <= montoMaximo && nuevoSaldo >= montoMinimo)
                document.querySelector("#tipSaldo").innerText = ""
                if (nuevoSaldo > montoMaximo) document.querySelector("#tipSaldo").innerText = "El saldo final no puede ser mayor a " + montoMaximo.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
                if (nuevoSaldo < montoMinimo) document.querySelector("#tipSaldo").innerText = "El saldo final no puede ser menor a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
            }
             
            const retirar = () => {
                const monto = parseFloat(document.querySelector("#saldoFinal").value)
                if (monto < montoMinimo) return showError("El saldo final debe ser mayor o igual a " + montoMinimo.toLocaleString("es-MX", { style: "currency", currency: "MXN" }))
                
                let datos = $("#datos").serializeArray()
                addParametro(datos, "codigoSEA", codigoSEA)
                addParametro(datos, "usuario", '{$_SESSION["usuario"]}')
                 
                consultaServidor(
                    "/AdminSucursales/AplicarRetiro/",
                    datos,
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        showSuccess(res.mensaje).then(() => {
                            window.location.reload()
                        })
                    }
                )
            }
        </script>
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Retiro de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("AdminSucursales/caja_admin_retiro");
    }

    public function AplicarRetiro()
    {
        $res = AdminSucursalesDao::AplicarRetiro($_POST);
        echo $res;
    }

    public function GetDatos()
    {
        $datos = AdminSucursalesDao::GetDatosFondeoRetiro($_POST);
        echo $datos;
    }

    //********************Log de transacciones de ahorro********************//
    // Reporte de trnasacciones 
    public function Log()
    {
        $extraFooter = <<<script
        <script>
            {$this->mensajes}
            {$this->crearFilas}
         
            const getLog = () => {
                const datos = {
                    fecha_inicio: $("#fInicio").val(),
                    fecha_fin: $("#fFin").val()
                }
                 
                const op = document.querySelector("#operacion")
                const us = document.querySelector("#usuario")
                
                if (op.value !== "0") datos.operacion = op.options[op.selectedIndex].text
                if (us.value !== "0") datos.usuario = us.options[us.selectedIndex].text
                 
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/GetLogTransacciones/",
                    data: datos,
                    success: (log) => {
                        $("#log").DataTable().destroy()
                         
                        log = JSON.parse(log)
                        let datos = log.datos
                         
                        if (!log.success) {
                            showError(log.mensaje)
                            datos = []
                        }
                        
                        $("#log tbody").html(creaFilas(datos))
                        $("#log").DataTable({
                            lengthMenu: [
                                [10, 40, -1],
                                [10, 40, "Todos"]
                            ],
                            columnDefs: [
                                {
                                    orderable: false,
                                    targets: 0
                                }
                            ],
                            order: false
                        })
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al buscar el log de transacciones.")
                    }
                })
                 
                return false
            }
             
            $(document).ready(() => {
                getLog()
            })
        </script>
        script;

        $operaciones = CajaAhorroDao::GetOperacionesLog();
        $usuarios = CajaAhorroDao::GetUsuariosLog();
        $sucursales = CajaAhorroDao::GetSucursalesLog();

        $opcOperaciones = "<option value='0'>Todas</option>";
        foreach ($operaciones as $key => $operacion) {
            $i = $key + 1;
            $opcOperaciones .= "<option value='{$i}'>{$operacion['TIPO']}</option>";
        }

        $opcUsuarios = "<option value='0'>Todos</option>";
        foreach ($usuarios as $key => $usuario) {
            $i = $key + 1;
            $opcUsuarios .= "<option value='{$i}'>{$usuario['USUARIO']}</option>";
        }

        $opcSucursales = "<option value='0'>Todas</option>";
        foreach ($sucursales as $key => $sucursal) {
            $i = $key + 1;
            $opcSucursales .= "<option value='{$i}'>{$sucursal['NOMBRE']}</option>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Log Transacciones Ahorro")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcOperaciones', $opcOperaciones);
        View::set('opcUsuarios', $opcUsuarios);
        View::set('opcSucursales', $opcSucursales);
        View::set(('fecha'), date('Y-m-d'));
        View::render("AdminSucursales/caja_admin_log");
    }

    public function GetLogTransacciones()
    {
        $log = CajaAhorroDao::GetLogTransacciones($_POST);
        echo $log;
    }

    //********************Activación de sucursales y cajeras********************//
    // Permite activar una sucursal y configurar los horarios de cajeras
    public function Configuracion()
    {
        $extraFooter = <<<script
        <script>
            {$this->mensajes}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->parseaNumero}
            {$this->addParametro}
         
            $(document).ready(configuraTabla("sucursalesActivas"))
         
            const cambioSucursal = () => {
                consultaServidor(
                    "/AdminSucursales/GetCajeras/",
                    { sucursal: $("#sucursal").val() },
                    (datos) => {
                        if (!datos.success) return showError(datos.mensaje)
                        if (datos.datos.length === 0) {
                            $("#cajera").html("<option value='0' disabled selected>No hay cajeras en esta sucursal</option>")
                            $("#cajera").prop("disabled", true)
                        } else {
                            let opciones = "<option value='0' disabled selected>Seleccione una cajera</option>"
                            datos.datos.forEach((cajera) => {
                                opciones += "<option value='" + cajera.CODIGO + "'>" + cajera.NOMBRE + "</option>"
                            })
                            $("#cajera").html(opciones)
                            $("#cajera").prop("disabled", false)
                        }
                    }
                )
                     
                consultaServidor(
                    "/AdminSucursales/GetMontoSucursal/",
                    { sucursal: $("#sucursal").val() },
                    (datos) => {
                        if (!datos.success) return
                        if (datos.datos.length === 0) {
                            $("#montoMin").val("")
                            $("#montoMax").val("")
                        } else {
                            $("#montoMin").val(datos.datos[0].MONTO_MIN)
                            $("#montoMax").val(datos.datos[0].MONTO_MAX)
                        }
                    }
                )
            }
             
            const cambioCajera = () => {
                consultaServidor(
                    "/AdminSucursales/GetHorarioCajera/",
                    { cajera: $("#cajera").val() },
                    (datos) => {
                        if (datos.datos && datos.datos.length === 0) {
                            $("#horaA").val(datos.datos[0].HORA_APERTURA)
                            $("#horaC").val(datos.datos[0].HORA_CIERRE)
                            $("#montoMin").val(datos.datos[0].MONTO_MIN)
                            $("#montoMax").val(datos.datos[0].MONTO_MAX)
                        } else {
                            $("#horaA").select(0)
                            $("#horaC").select(0)
                            $("#montoMin").val("")
                            $("#montoMax").val("")
                        }
                    }
                )
                
                $("#horaA").prop("disabled", false)
                $("#horaC").prop("disabled", false)
                $("#montoMin").prop("disabled", false)
                $("#montoMax").prop("disabled", false)
                $("#saldo").prop("disabled", false)
            }
             
            const cambioMonto = () => {
                const min = parseFloat(document.querySelector("#montoMin").value) || 0
                const max = parseFloat(document.querySelector("#montoMax").value) || 0
                const inicial = parseFloat(document.querySelector("#saldo").value) || 0
                document.querySelector("#guardar").disabled = !(min > 0 && max > 0 && max >= min && inicial <= max)
            }
             
            const validaMaxMin = () => {
                const min = parseFloat(document.querySelector("#montoMin").value) || 0
                const max = parseFloat(document.querySelector("#montoMax").value) || 0
                if (min > max) document.querySelector("#montoMax").value = min
            }
             
            const activarSucursal = () => {
                const datos = $("#datos").serializeArray()
                addParametro(datos, "usuario", '{$_SESSION["usuario"]}')
                 
                consultaServidor(
                        "/AdminSucursales/ActivarSucursal/",
                        datos,
                        (res) => {
                            if (!res.success) return showError(res.mensaje)                            
                            showSuccess(res.mensaje).then(() => {
                                swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                                window.location.reload()
                            })
                        }
                    )
            }
             
            const llenarModal = () => {
                document.querySelector("#configMontos").reset()
                const fila = event.target.parentElement.parentElement
                document.querySelector("#codSucMontos").value = fila.children[1].innerText
                document.querySelector("#nomSucMontos").value = fila.children[2].innerText
                consultaServidor(
                    "/AdminSucursales/GetMontosApertura/",
                    { sucursal: fila.children[1].innerText },
                    (datos) => {
                        if (!datos.success) return
                        document.querySelector("#codigo").value = datos.datos.CODIGO
                        document.querySelector("#minimoApertura").value = datos.datos.MONTO_MINIMO
                        document.querySelector("#maximoApertura").value = datos.datos.MONTO_MAXIMO
                    }
                )
            }
             
            const validaMontoMinMax = (e) => {
                const m = parseFloat(e.target.value)
                if (m < 0) e.target.value = ""
                if (m > 1000000) e.target.value = "1000000.00"
                const valor = e.target.value.split(".")
                valor[1] = valor[1] || "00"
                if (valor[1] && valor[1].length > 2) e.target.value = parseFloat(valor[0] + "." + valor[1].substring(0, 2))
            }
             
            const guardarMontos = () => {
                consultaServidor(
                    "/AdminSucursales/GuardarParametrosSucursal/",
                    $("#configMontos").serialize(),
                    (res) => {
                        if (!res.success) return showError(res.mensaje)
                        showSuccess(res.mensaje).then(() => {
                            swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                            window.location.reload()
                        })
                    }
                )
            }
        </script>
        script;

        $sucursales = AdminSucursalesDao::GetSucursales();
        $opcSucursales = "<option value='0' disabled selected>Seleccione una sucursal</option>";
        foreach ($sucursales as $key => $val2) {
            $opcSucursales .= "<option  value='" . $val2['CODIGO'] . "'>(" . $val2['CODIGO'] . ") " . $val2['NOMBRE'] . "</option>";
        }

        $sucActivas = AdminSucursalesDao::GetSucursalesActivas();
        $tabla = "";
        foreach ($sucActivas as $key => $val) {
            $tabla .= "<tr>";
            foreach ($val as $key2 => $val2) {
                if ($key2 === "ACCIONES") {
                    $tabla .= "<td style='vertical-align: middle; text-align: center;'><i class='fa fa-usd' title='Configurar montos' data-toggle='modal' data-target='#modal_configurar_montos' style='cursor: pointer;' onclick=llenarModal(event)></i></td>";
                } else {
                    $tabla .= "<td style='vertical-align: middle;'>{$val2}</td>";
                }
            }
            $tabla .= "</tr>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Configuración de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcSucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("AdminSucursales/caja_admin_configurar");
    }

    public function GetMontoSucursal()
    {
        $monto = AdminSucursalesDao::GetMontoSucursal($_POST['sucursal']);
        echo $monto;
    }

    public function GetCajeras()
    {
        $cajeras = AdminSucursalesDao::GetCajeras($_POST['sucursal']);
        echo $cajeras;
    }

    public function GetHorarioCajera()
    {
        $horario = AdminSucursalesDao::GetHorarioCajera($_POST);
        echo $horario;
    }

    public function ActivarSucursal()
    {
        echo AdminSucursalesDao::ActivarSucursal($_POST);
    }

    public function GetMontosApertura()
    {
        echo AdminSucursalesDao::GetMontosApertura($_POST['sucursal']);
    }

    public function GuardarParametrosSucursal()
    {
        echo AdminSucursalesDao::GuardarParametrosSucursal($_POST);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    public function EstadoCuentaCliente()
    {
        $extraFooter = <<<script
        <script>
            let infoCliente = {}
            let vistaActiva = ""
         
            {$this->mensajes}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->validarYbuscar}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->sinContrato}
            {$this->buscaCliente}
            {$this->parseaNumero}
            {$this->formatoMoneda}
            {$this->configuraTabla}
            {$this->muestraPDF}
            {$this->getFecha}
         
            const buscar = () => buscaCliente()
         
            const getVista = (vista) => {
                if (vista === "") return
                consultaServidor("/AdminSucursales/" + vista + "/", infoCliente, (res) => {
                    const cuerpo = document.querySelector("#cuerpoModal")
                    while(cuerpo.firstChild) {
                        cuerpo.firstChild.remove()
                    }
                     
                    const fragmento = document.createElement("template");
                    fragmento.innerHTML = res || ""
                     
                    const contenido = fragmento.content.querySelector(".modal-body")
                    const script = fragmento.content.querySelector("script")
                    
                    document.querySelector("#cuerpoModal").innerHTML = contenido.innerHTML
                    if (script) {
                        const nuevoScript = document.createElement("script")
                        nuevoScript.innerHTML = script.innerHTML
                        document.querySelector("#cuerpoModal").appendChild(nuevoScript)
                    }
                }, "POST", "html")
            }
             
            const llenaDatosCliente = (datos) => {
                infoCliente = datos
                if (vistaActiva) return getVista(vistaActiva)
                const opciones = document.querySelector("#opcionesCat").querySelectorAll("li")
                opciones.forEach((opcion) => {
                    if (!opcion.classList.contains("linea")) vistaActiva = opcion.children[0].id
                })
                getVista(vistaActiva)
            }
             
            const limpiaDatosCliente = () => {
                infoCliente = {}
                document.querySelector("#cuerpoModal").innerHTML = ""
            }
             
            const actualizaVista = (e) => {
                if (infoCliente.CDGCL === undefined) return showError("No se ha realizado la búsqueda de un cliente.")
                if (vistaActiva === e.target.id) return
                 
                vistaActiva = e.target.id
                reiniciaOpciones()
                e.target.parentElement.classList.remove("linea")
                e.target.style.fontWeight = "bold"
                getVista(vistaActiva)
            }
             
            const reiniciaOpciones = () => {
                const opciones = document.querySelector("#opcionesCat").querySelectorAll("li")
                opciones.forEach((opcion) => {
                    opcion.classList.add("linea")
                    opcion.children[0].style.fontWeight = "normal"
                })
            }
        </script>
        script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Catalogo de Clientes")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render("AdminSucursales/caja_admin_clientes");
    }

    public function ResumenCuenta()
    {
        $script = <<<script
        <script>
            $(document).ready(() => configuraTabla("tablaResumenCta"))
         
            const mPDF = () => {
                const host = window.location.origin
                const fInicio = "01/01/2024" // getFecha(document.querySelector("#fechaInicio").value)
                const fFin = new Date().toLocaleDateString("es-MX", { year: "numeric", month:"2-digit", day:"2-digit" }) // getFecha(document.querySelector("#fechaFin").value)
                const segmmeto = document.querySelector("#segmento").value
                
                const url = host + '/Ahorro/EdoCta/?'
                + 'cliente=' + infoCliente.CDGCL
                + '&segmento=' + segmmeto
                 
                muestraPDF("Resumen de Cuenta", url)
            }
        </script>
        script;

        $movimientos = self::ListaMovimientos($_POST);
        $segmentos = AdminSucursalesDao::GetSegmentos($_POST);
        $opcSegmentos = "";
        if ($segmentos['INVERSIÓN'] > 0) $opcSegmentos .= "<option value='2'>INVERSIÓN</option>";
        if ($segmentos['PEQUES'] > 0) $opcSegmentos .= "<option value='3'>PEQUES</option>";

        if ($opcSegmentos === "") $opcSegmentos = "<option value='1'>AHORRO</option>";
        else $opcSegmentos = "<option value='0'>TODOS</option><option value='1'>AHORRO</option>" . $opcSegmentos;


        View::set('script', $script);
        View::set('cliente', $_POST['CDGCL']);
        View::set('nombre', $_POST['NOMBRE']);
        View::set('filas', $movimientos['filas']);
        View::set('conteoAbonos', $movimientos['conteoAbonos']);
        View::set('conteoCargos', $movimientos['conteoCargos']);
        View::set('conteoTotal', $movimientos['conteoTotal']);
        View::set('conteoTransferencias', $movimientos['conteoTransferencias']);
        View::set('montoAbonos', $movimientos['montoAbonos']);
        View::set('montoCargos', $movimientos['montoCargos']);
        View::set('montoTransferencias', $movimientos['montoTransferencias']);
        View::set('saldoFinal', $movimientos['saldoFinal']);
        View::set('filas', $movimientos['filas']);
        View::set('opcSegmentos', $opcSegmentos);
        echo View::fetch("AdminSucursales/caja_admin_clientes_resumenCta");
    }

    public function ListaMovimientos($d = null)
    {
        $datos = $d ? $d : $_POST;
        $registros = AdminSucursalesDao::ResumenCuenta($datos);
        $conteoCargos = 0;
        $conteoAbonos = 0;
        $montoCargos = 0;
        $montoAbonos = 0;
        $conteoTotal = 0;
        $conteoTransferencias = 0;
        $montoTransferencias = 0;
        $saldoFinal = null;

        $filas = "";
        foreach ($registros as $key => $registro) {
            $filas .= "<tr>";
            if ($registro['CUENTA'] === "AHORRO") {
                $conteoTotal++;
                $saldoFinal = $registro['SALDO'];
                if ($registro['ABONO'] > 0) {
                    if ($registro['TIPO'] != 11) {
                        $conteoAbonos++;
                        $montoAbonos += $registro['ABONO'];
                    } else {
                        $conteoTransferencias--;
                        $montoTransferencias -= $registro['ABONO'];
                    }
                } else {
                    if ($registro['TIPO'] == 5) {
                        $conteoTransferencias++;
                        $montoTransferencias += $registro['CARGO'];
                    } else if ($registro['TIPO'] != 2 && $registro['TIPO'] !== 13 && $registro['TIPO'] !== 14) {
                        $conteoCargos++;
                        $montoCargos += $registro['CARGO'];
                    }
                }
            }
            $filas .= "<td style='vertical-align: middle;'>{$registro['FECHA']}</td>";
            $filas .= "<td style='vertical-align: middle;'>{$registro['CUENTA']}</td>";
            $filas .= "<td style='vertical-align: middle;'>{$registro['DESCRIPCION']}</td>";
            $filas .= "<td style='vertical-align: middle; text-align: right;'>$ " . number_format($registro['TRANSITO'], 2, '.', ',') . "</td>";
            $filas .= "<td style='vertical-align: middle; text-align: right;'>$ " . number_format($registro['ABONO'], 2, '.', ',') . "</td>";
            $filas .= "<td style='vertical-align: middle; text-align: right;'>$ " . number_format($registro['CARGO'], 2, '.', ',') . "</td>";
            $filas .= "<td style='vertical-align: middle; text-align: right;'>$ " . number_format($registro['SALDO'], 2, '.', ',') . "</td>";
            $filas .= "<td style='vertical-align: middle;'>{$registro['USUARIO']}</td>";
            $filas .= "</tr>";
        }

        $respuesta = [
            "conteoAbonos" => $conteoAbonos,
            "conteoCargos" => $conteoCargos,
            "conteoTransferencias" => $conteoTransferencias,
            "conteoTotal" => $conteoTotal,
            "montoAbonos" => $montoAbonos,
            "montoCargos" => $montoCargos,
            "montoTransferencias" => $montoTransferencias,
            "saldoFinal" => $saldoFinal,
            "filas" => $filas
        ];

        if ($d !== null) return $respuesta;
        echo json_encode($respuesta);
    }

    public function Rendimiento()
    {
        $script = <<<script
        <script>
            $(document).ready(() => configuraTabla("tablaRendimiento"))
         
            const buscarRendimientos = () => {
                const datos = {
                    CDGCL: infoCliente.CDGCL,
                    fechaI: getFecha(document.querySelector("#fechaI").value),
                    fechaF: getFecha(document.querySelector("#fechaF").value),
                    producto: document.querySelector("#segmento").value
                }
                 
                consultaServidor("/AdminSucursales/GetRendimientos/", datos, (respuesta) => {
                    $("#tablaRendimiento").DataTable().destroy()
                    
                    if (respuesta.datos == "") showError("No se encontraron intereses devengados en el rango de fechas seleccionado.")
                     
                    $("#tablaRendimiento tbody").html(respuesta.datos)
                    configuraTabla("tablaRendimiento")
                })
            }
        </script>
        script;

        $datos = [
            "CDGCL" => $_POST['CDGCL'],
            "fechaI" => date('d/m/Y'),
            "fechaF" => date('d/m/Y')
        ];
        $filas = self::GetRendimientos($datos);
        $segmentos = AdminSucursalesDao::GetSegmentos($_POST);
        $opcSegmentos = "";
        if ($segmentos['PEQUES'] > 0) $opcSegmentos .= "<option value='2'>PEQUES</option>";

        if ($opcSegmentos === "") $opcSegmentos = "<option value='1'>AHORRO</option>";
        else $opcSegmentos = "<option value='0'>TODOS</option><option value='1'>AHORRO</option>" . $opcSegmentos;

        View::set('script', $script);
        View::set('cliente', $_POST['CDGCL']);
        View::set('nombre', $_POST['NOMBRE']);
        View::set('fecha', date('Y-m-d'));
        View::set('opcSegmentos', $opcSegmentos);
        View::set('filas', $filas);
        echo View::fetch("AdminSucursales/caja_admin_clientes_rendimiento");
    }

    public function GetRendimientos($d = null)
    {
        $datos = $d ? $d : $_POST;
        $rendimientos = AdminSucursalesDao::GetRendimientos($datos);

        $filas = "";
        foreach ($rendimientos as $key => $rendimiento) {
            $filas .= "<tr>";
            foreach ($rendimiento as $key2 => $val) {
                if ($key2 === "FECHA") $filas .= "<td style='vertical-align: middle;'>{$val}</td>";
                elseif ($key2 === "SALDO" || $key2 === "DEVENGO") $filas .= "<td style='vertical-align: middle; text-align: right;'>$ " . number_format($val, 2, '.', ',') . "</td>";
                elseif ($key2 === "TASA") $filas .= "<td style='vertical-align: middle;'>" . ($val * 100) . "%</td>";
                else $filas .= "<td style='vertical-align: middle;'>{$val}</td>";
            }
            $filas .= "</tr>";
        }

        if ($d !== null) return $filas;
        echo json_encode(["success" => true, "datos" => $filas]);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    public function Reporteria()
    {
        $extraFooter = <<<script
        <script>
        
        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
             
         $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
                  "lengthMenu": [
                    [10, 50, -1],
                    [10, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            operacion = getParameterByName('Operacion');
            producto = getParameterByName('Producto');
            sucursal = getParameterByName('Sucursal');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/AdminSucursales/generarExcelPagosTransaccionesAll/?Inicial='+fecha1+'&Final='+fecha2+'&Operacion='+operacion+'&Producto='+producto+'&Sucursal='+sucursal);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
             
        });
        
          
        </script>
script;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        //$Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];


        $sucursales = CajaAhorroDao::GetSucursalAsignadaCajeraAhorro('');
        $opcSucursales = "";
        foreach ($sucursales as $sucursales) {
            if ($sucursales['CODIGO'] == $Sucursal) {
                $sel_suc = 'Selected';
            } else {
                $sel_suc = '';
            }
            $opcSucursales .= "<option value='{$sucursales['CODIGO']}' $sel_suc>{$sucursales['NOMBRE']} ({$sucursales['CODIGO']})</option>";
        }



        //////////////////////////////////////////////////////
        if ($Operacion == 0 || $Operacion == '') {
            $sel_op0 = 'Selected';
        } else if ($Operacion == 1) {
            $sel_op1 = 'Selected';
        } else if ($Operacion == 2) {
            $sel_op2 = 'Selected';
        } else if ($Operacion == 3) {
            $sel_op3 = 'Selected';
        } else if ($Operacion == 4) {
            $sel_op4 = 'Selected';
        } else if ($Operacion == 5) {
            $sel_op5 = 'Selected';
        } else if ($Operacion == 6) {
            $sel_op6 = 'Selected';
        } else if ($Operacion == 7) {
            $sel_op7 = 'Selected';
        } else if ($Operacion == 8) {
            $sel_op8 = 'Selected';
        } else if ($Operacion == 9) {
            $sel_op9 = 'Selected';
        } else if ($Operacion == 10) {
            $sel_op10 = 'Selected';
        }


        $opcOperaciones = <<<html
            <option value="0" $sel_op0>TODAS LAS OPERACIONES CON EFECTIVO</option>
            
            
            <option value="1" $sel_op1>APERTURA DE CUENTA - INSCRIPCIÓN</option>
            <option value="2" $sel_op2>CAPITAL INICIAL - CUENTA CORRIENTE</option>
            <option value="3" $sel_op3>DEPOSITO</option>
            <option value="4" $sel_op4>RETIRO</option>
html;


        //////////////////////////////////////////////////////

        if ($Producto == 0 || $Producto == '') {
            $sel_pro0 = 'Selected';
        } else if ($Producto == 1) {
            $sel_pro1 = 'Selected';
        } else if ($Producto == 2) {
            $sel_pro2 = 'Selected';
        } else if ($Producto == 3) {
            $sel_pro3 = 'Selected';
        }


        $opcProductos = <<<html
            <option value="0" $sel_pro0>TODOS LOS PRODUCTOS QUE MANEJAN EFECTIVO</option>
            <option value="1" $sel_pro1>AHORRO CUENTA - CORRIENTE</option>
            <option value="2" $sel_pro2>AHORRO CUENTA - PEQUES</option>
html;


        if ($Inicial == '') {
            $Inicial = $fechaActual;
            $Final = $fechaActual;
        }
        //$Transacciones = CajaAhorroDao::GetAllTransacciones($Inicial, $Final, $Operacion, $Producto, $Sucursal);
        $Transacciones = CajaAhorroDao::GetAllTransacciones($Inicial, $Inicial, $Operacion, $Producto, $Sucursal);

        $tabla = "";
        foreach ($Transacciones as $key => $value) {
            $monto = number_format($value['MONTO'], 2);
            $ingreso = number_format($value['INGRESO'], 2);
            $egreso = number_format($value['EGRESO'], 2);
            $saldo = number_format($value['SALDO'], 2);

            if ($value['CONCEPTO'] == 'TRANSFERENCIA INVERSION') {
                $concepto = '<i class="fa fa-minus" style="color: #0000ac;"></i>';
            } else if ($value['CONCEPTO'] == 'RETIRO' || $value['CONCEPTO'] == 'ENTREGA RETIRO PROGRAMADO' || $value['CONCEPTO'] == 'ENTREGA RETIRO EXPRESS' || $value['CONCEPTO'] == 'RETIRO DE EFECTIVO') {
                $concepto = '<i class="fa fa-arrow-up" style="color: #ac0000;"></i>';
            } else if ($value['CONCEPTO'] == 'SALDO FINAL AL CIERRE DE LA SUCURSAL (DIARIO)') {
                $concepto = '<i class="fa fa-dollar" style="color: #ff8600;"></i><i class="fa fa-dollar" style="color: #ff8600;"></i>';
            } else if ($value['CONCEPTO'] == 'SALDO INICIAL DEL DIA (DIARIO)') {
                $concepto = '<i class="fa fa-dollar" style="color: #ff8600;"></i>';
            } else {
                $concepto = '<i class="fa fa-arrow-down" style="color: #00ac00;"></i>';
            }
            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                
                    <td style="padding: 10px !important;">
                        
                         <div>CODIGO CLIENTE SICAFIN: <b>{$value['CLIENTE']}</b></div>
                         <br>
                          <div>NOMBRE CLIENTE: <b>{$value['TITULAR_CUENTA_EJE']}</b></div>
                    </td>
                    
                    <td style="padding: 10px !important;">
                         <div style="margin-bottom: 5px;"><b>FECHA:</b> {$value['FECHA_MOV']}</div>
                    </td>
                    
                    <td style="padding: 10px !important;">
                         <div style="margin-bottom: 5px;"><b>SUCURSAL:</b> {$value['CDGCO']} - {$value['SUCURSAL']}</div>
                         <div style="margin-bottom: 5px;"><b>USUARIO:</b> {$value['USUARIO_CAJA']}</div>
                    </td>
                    <td style="padding: 10px !important;">
                        <div style="margin-bottom: 5px; font-size: 15px;">{$concepto} $ {$monto}</div>
                        <div style="margin-bottom: 5px;"><b>CONCEPTO:</b> {$value['CONCEPTO']}</div>
                        <div style="margin-bottom: 5px;"><b>PRODUCTO:</b> {$value['PRODUCTO']}</div>
                    </td>
                    <td style="padding: 10px !important;">$ {$ingreso} </td>
                    <td style="padding: 10px !important;">$ {$egreso} </td>
                    <td style="padding: 10px !important;">$ {$saldo} </td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial de Transacciones")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha_inicial', $Inicial);
        View::set('fecha_final', $Final);
        View::set('fechaActual', $fechaActual);
        view::set('sucursales', $opcSucursales);
        view::set('productos', $opcProductos);
        view::set('operacion', $opcOperaciones);
        View::set('tabla', $tabla);
        View::render("AdminSucursales/caja_admin_reporteria_transacciones");
    }

    public function Transacciones()
    {
        $extraFooter = <<<script
        <script>
            {$this->configuraTabla}
        
        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
             
         $(document).ready(function(){
            configuraTabla("muestra-cupones")
            var checkAll = 0;
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            operacion = getParameterByName('Operacion');
            producto = getParameterByName('Producto');
            sucursal = getParameterByName('Sucursal');
            
             $("#export_excel_con_transacciones").click(function(){
              $('#all').attr('action', '/AdminSucursales/generarExcelPagosTransaccionesDetalleAll/?Inicial='+fecha1+'&Final='+fecha2+'&Operacion='+operacion+'&Producto='+producto+'&Sucursal='+sucursal);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
        });
        
          
        </script>
script;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];


        $sucursales = CajaAhorroDao::GetSucursalAsignadaCajeraAhorro('');
        $opcSucursales = "";
        foreach ($sucursales as $sucursales) {
            if ($sucursales['CODIGO'] == $Sucursal) {
                $sel_suc = 'Selected';
            } else {
                $sel_suc = '';
            }
            $opcSucursales .= "<option value='{$sucursales['CODIGO']}' $sel_suc>{$sucursales['NOMBRE']} ({$sucursales['CODIGO']})</option>";
        }



        //////////////////////////////////////////////////////
        if ($Operacion == 0 || $Operacion == '') {
            $sel_op0 = 'Selected';
        } else if ($Operacion == 1) {
            $sel_op1 = 'Selected';
        } else if ($Operacion == 2) {
            $sel_op2 = 'Selected';
        } else if ($Operacion == 3) {
            $sel_op3 = 'Selected';
        } else if ($Operacion == 4) {
            $sel_op4 = 'Selected';
        } else if ($Operacion == 5) {
            $sel_op5 = 'Selected';
        } else if ($Operacion == 6) {
            $sel_op6 = 'Selected';
        } else if ($Operacion == 7) {
            $sel_op7 = 'Selected';
        } else if ($Operacion == 8) {
            $sel_op8 = 'Selected';
        } else if ($Operacion == 9) {
            $sel_op9 = 'Selected';
        } else if ($Operacion == 10) {
            $sel_op10 = 'Selected';
        }


        $opcOperaciones = <<<html
            <option value="0" $sel_op0>TODAS LAS OPERACIONES CON EFECTIVO</option>
            
            
            <option value="1" $sel_op1>APERTURA DE CUENTA - INSCRIPCIÓN</option>
            <option value="2" $sel_op2>CAPITAL INICIAL - CUENTA CORRIENTE</option>
            <option value="3" $sel_op3>DEPOSITO</option>
            <option value="4" $sel_op4>RETIRO</option>
            <option value="5" $sel_op5>INVERSION</option>
html;


        //////////////////////////////////////////////////////

        if ($Producto == 0 || $Producto == '') {
            $sel_pro0 = 'Selected';
        } else if ($Producto == 1) {
            $sel_pro1 = 'Selected';
        } else if ($Producto == 2) {
            $sel_pro2 = 'Selected';
        } else if ($Producto == 3) {
            $sel_pro3 = 'Selected';
        }


        $opcProductos = <<<html
            <option value="0" $sel_pro0>TODOS LOS PRODUCTOS QUE MANEJAN EFECTIVO</option>
            <option value="1" $sel_pro1>AHORRO CUENTA - CORRIENTE</option>
            <option value="2" $sel_pro2>AHORRO CUENTA - PEQUES</option>
html;


        if ($Inicial == '' || $Final == '') {
            $Inicial = $fechaActual;
            $Final = $fechaActual;
        }

        $Transacciones = CajaAhorroDao::GetAllTransaccionesDetalle($Inicial, $Final, $Operacion, $Producto, $Sucursal);

        $tabla = "";
        foreach ($Transacciones as $key => $value) {
            $monto = number_format($value['MONTO'], 2);
            $ingreso = number_format($value['INGRESO'], 2);
            $egreso = number_format($value['EGRESO'], 2);


            if ($value['CONCEPTO'] == 'TRANSFERENCIA INVERSIÓN (ENVIO)') {
                $concepto = '<i class="fa fa-minus" style="color: #ac0000;"></i>';
            } else if ($value['CONCEPTO'] == 'TRANSFERENCIA INVERSIÓN (RECEPCIÓN)') {
                $concepto = '<i class="fa fa-minus" style="color: #00ac00;"></i>';
            } else if ($value['CONCEPTO'] == 'RETIRO' || $value['CONCEPTO'] == 'ENTREGA RETIRO PROGRAMADO' || $value['CONCEPTO'] == 'ENTREGA RETIRO EXPRESS') {
                $concepto = '<i class="fa fa-arrow-up" style="color: #ac0000;"></i>';
            } else if ($value['CONCEPTO'] == 'SALDO FINAL AL CIERRE DE LA SUCURSAL (DIARIO)') {
                $concepto = '<i class="fa fa-dollar" style="color: #ff8600;"></i><i class="fa fa-dollar" style="color: #ff8600;"></i>';
            } else if ($value['CONCEPTO'] == 'SALDO INICIAL DEL DIA (DIARIO)') {
                $concepto = '<i class="fa fa-dollar" style="color: #ff8600;"></i>';
            } else if ($value['TIPO_MOVIMIENTO'] == 'MOVIMIENTO VIRTUAL') {
                $concepto = '<i class="fa fa-asterisk" style="color: #005dac;"></i>';
            } else {
                $concepto = '<i class="fa fa-arrow-down" style="color: #00ac00;"></i>';
            }
            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                
                   <td style="padding: 10px !important;">
                         <div style="margin-bottom: 5px;"><b>FECHA:</b> {$value['FECHA_MOV_APLICA']}</div>
                    </td>
                    
                    <td style="padding: 10px !important;">
                         <div style="margin-bottom: 5px;"><b>SUCURSAL:</b> {$value['CDGCO']} - {$value['SUCURSAL']}</div>
                         <div style="margin-bottom: 5px;"><b>USUARIO REGISTRA TRANSACCION:</b> {$value['USUARIO_CAJA']} - {$value['NOMBRE_CAJERA']} </div>
                         <hr>
                          <div style="margin-bottom: 5px;"><b>FECHA LARGA:</b> {$value['FECHA_MOV']}</div>
                    </td>
                    
                    <td style="padding: 10px !important;">
                        
                         <div>CODIGO CLIENTE SICAFIN: <b>{$value['CLIENTE']}</b></div>
                         <br>
                          <div>NOMBRE CLIENTE: <b>{$value['TITULAR_CUENTA_EJE']}</b></div>
                    </td>
                    
                   
                    
                    
                    <td style="padding: 10px !important;">
                        <div style="margin-bottom: 5px; font-size: 15px;">{$concepto} $ {$monto}</div>
                        <div style="margin-bottom: 5px;"><b>CONCEPTO:</b> {$value['CONCEPTO']}</div>
                        <div style="margin-bottom: 5px;"><b>PRODUCTO:</b> {$value['PRODUCTO']}</div>
                    </td>
                    <td style="padding: 10px !important;">$ {$ingreso} </td>
                    <td style="padding: 10px !important;">$ {$egreso} </td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial de Transacciones")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha_inicial', $Inicial);
        View::set('fecha_final', $Final);
        View::set('fechaActual', $fechaActual);
        view::set('sucursales', $opcSucursales);
        view::set('productos', $opcProductos);
        view::set('operacion', $opcOperaciones);
        View::set('tabla', $tabla);
        View::render("AdminSucursales/caja_admin_reporteria_transacciones_saldo");
    }

    public function TransaccionesOperaciones()
    {
        $extraFooter = <<<script
        <script>
            {$this->configuraTabla}
        
        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
             
         $(document).ready(function(){
            configuraTabla("muestra-cupones")
            var checkAll = 0;
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            operacion = getParameterByName('Operacion');
            producto = getParameterByName('Producto');
            sucursal = getParameterByName('Sucursal');
            
             $("#export_excel_con_transacciones").click(function(){
              $('#all').attr('action', '/AdminSucursales/generarExcelPagosTransaccionesDetalleAll/?Inicial='+fecha1+'&Final='+fecha2+'&Operacion='+operacion+'&Producto='+producto+'&Sucursal='+sucursal);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
        });
        
          
        </script>
script;

        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];


        $sucursales = CajaAhorroDao::GetSucursalAsignadaCajeraAhorro('');
        $opcSucursales = "";
        foreach ($sucursales as $sucursales) {
            if ($sucursales['CODIGO'] == $Sucursal) {
                $sel_suc = 'Selected';
            } else {
                $sel_suc = '';
            }
            $opcSucursales .= "<option value='{$sucursales['CODIGO']}' $sel_suc>{$sucursales['NOMBRE']} ({$sucursales['CODIGO']})</option>";
        }



        //////////////////////////////////////////////////////
        if ($Operacion == 0 || $Operacion == '') {
            $sel_op0 = 'Selected';
        } else if ($Operacion == 1) {
            $sel_op1 = 'Selected';
        } else if ($Operacion == 2) {
            $sel_op2 = 'Selected';
        } else if ($Operacion == 3) {
            $sel_op3 = 'Selected';
        } else if ($Operacion == 4) {
            $sel_op4 = 'Selected';
        } else if ($Operacion == 5) {
            $sel_op5 = 'Selected';
        } else if ($Operacion == 6) {
            $sel_op6 = 'Selected';
        } else if ($Operacion == 7) {
            $sel_op7 = 'Selected';
        } else if ($Operacion == 8) {
            $sel_op8 = 'Selected';
        } else if ($Operacion == 9) {
            $sel_op9 = 'Selected';
        } else if ($Operacion == 10) {
            $sel_op10 = 'Selected';
        }


        $opcOperaciones = <<<html
            <option value="0" $sel_op0>TODAS LAS OPERACIONES CON EFECTIVO</option>
            
            
            <option value="1" $sel_op1>APERTURA DE CUENTA - INSCRIPCIÓN</option>
            <option value="2" $sel_op2>CAPITAL INICIAL - CUENTA CORRIENTE</option>
            <option value="3" $sel_op3>DEPOSITO</option>
            <option value="4" $sel_op4>RETIRO</option>
            <option value="5" $sel_op5>INVERSION</option>
html;


        //////////////////////////////////////////////////////

        if ($Producto == 0 || $Producto == '') {
            $sel_pro0 = 'Selected';
        } else if ($Producto == 1) {
            $sel_pro1 = 'Selected';
        } else if ($Producto == 2) {
            $sel_pro2 = 'Selected';
        } else if ($Producto == 3) {
            $sel_pro3 = 'Selected';
        }


        $opcProductos = <<<html
            <option value="0" $sel_pro0>TODOS LOS PRODUCTOS QUE MANEJAN EFECTIVO</option>
            <option value="1" $sel_pro1>AHORRO CUENTA - CORRIENTE</option>
            <option value="2" $sel_pro2>AHORRO CUENTA - PEQUES</option>
html;


        if ($Inicial == '' || $Final == '') {
            $Inicial = $fechaActual;
            $Final = $fechaActual;
        }

        $Transacciones = CajaAhorroDao::GetAllTransaccionesDetalle($Inicial, $Final, $Operacion, $Producto, $Sucursal);

        $tabla = "";
        foreach ($Transacciones as $key => $value) {
            $monto = number_format($value['MONTO'], 2);
            $ingreso = number_format($value['INGRESO'], 2);
            $egreso = number_format($value['EGRESO'], 2);


            if ($value['CONCEPTO'] == 'TRANSFERENCIA INVERSIÓN (ENVIO)') {
                $concepto = '<i class="fa fa-minus" style="color: #ac0000;"></i>';
            } else if ($value['CONCEPTO'] == 'TRANSFERENCIA INVERSIÓN (RECEPCIÓN)') {
                $concepto = '<i class="fa fa-minus" style="color: #00ac00;"></i>';
            } else if ($value['CONCEPTO'] == 'RETIRO' || $value['CONCEPTO'] == 'ENTREGA RETIRO PROGRAMADO' || $value['CONCEPTO'] == 'ENTREGA RETIRO EXPRESS') {
                $concepto = '<i class="fa fa-arrow-up" style="color: #ac0000;"></i>';
            } else if ($value['CONCEPTO'] == 'SALDO FINAL AL CIERRE DE LA SUCURSAL (DIARIO)') {
                $concepto = '<i class="fa fa-dollar" style="color: #ff8600;"></i><i class="fa fa-dollar" style="color: #ff8600;"></i>';
            } else if ($value['CONCEPTO'] == 'SALDO INICIAL DEL DIA (DIARIO)') {
                $concepto = '<i class="fa fa-dollar" style="color: #ff8600;"></i>';
            } else if ($value['TIPO_MOVIMIENTO'] == 'MOVIMIENTO VIRTUAL') {
                $concepto = '<i class="fa fa-asterisk" style="color: #005dac;"></i>';
            } else {
                $concepto = '<i class="fa fa-arrow-down" style="color: #00ac00;"></i>';
            }

            //////////////////////////////////////////////////////////////////////////////////// SE AGREGAN ESTOS CAMBIOS EL 26/06/2024 SON PARA IDENTIFICAR PAGO DE COMISIONES
            if ($value['CDGPE_COMISIONA'] != NULL) {
                $comisiona = <<<html
                <div style="margin-bottom: 5px;"><b>COMISIÓN:</b> {$value['CDGPE_COMISIONA']}-  {$value['NOMBRE_COMISIONA']}</div>
html;
            } else {
                $comisiona = <<<html
                <div style="margin-bottom: 5px;"><b>COMISIÓN:</b> NO APLICA</div>
html;
            }
            ////////////////////////////////////////////////////////////////////////////////////

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                
                   <td style="padding: 10px !important;">
                         <div style="margin-bottom: 5px;"><b>FECHA:</b> {$value['FECHA_MOV_APLICA']}</div>
                    </td>
                    
                    <td style="padding: 10px !important;">
                         <div style="margin-bottom: 5px;"><b>SUCURSAL:</b> {$value['CDGCO']} - {$value['SUCURSAL']}</div>
                         <div style="margin-bottom: 5px;"><b>USUARIO REGISTRA TRANSACCION:</b> {$value['USUARIO_CAJA']} - {$value['NOMBRE_CAJERA']} </div>
                         <hr>
                          <div style="margin-bottom: 5px;"><b>FECHA LARGA:</b> {$value['FECHA_MOV']}</div>
                    </td>
                    
                    <td style="padding: 10px !important;">
                        
                         <div>CODIGO CLIENTE SICAFIN: <b>{$value['CLIENTE']}</b></div>
                         <br>
                          <div>NOMBRE CLIENTE: <b>{$value['TITULAR_CUENTA_EJE']}</b></div>
                    </td>
                    
                    <td style="padding: 10px !important;">
                        <div style="margin-bottom: 5px; font-size: 15px;">{$concepto} $ {$monto}</div>
                        <div style="margin-bottom: 5px;"><b>CONCEPTO:</b> {$value['CONCEPTO']}</div>
                        <div style="margin-bottom: 5px;"><b>PRODUCTO:</b> {$value['PRODUCTO']}</div>
                        {$comisiona}
                        
                    </td>
                    <td style="padding: 10px !important;">$ {$ingreso} </td>
                    <td style="padding: 10px !important;">$ {$egreso} </td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial de Transacciones")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha_inicial', $Inicial);
        View::set('fecha_final', $Final);
        View::set('fechaActual', $fechaActual);
        view::set('sucursales', $opcSucursales);
        view::set('productos', $opcProductos);
        view::set('operacion', $opcOperaciones);
        View::set('tabla', $tabla);
        View::render("AdminSucursales/caja_admin_reporteria_transacciones_saldo_operaciones");
    }


    public function ReporteriaTransacciones()
    {
        $extraFooter = <<<script
        <script>
            {$this->mensajes}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->buscaCliente}
        </script>
script;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::render("AdminSucursales/caja_admin_reporteria_transacciones");
        //View::render("caja_admin_reporteria");
    }


    public function SolicitudesReimpresionTicket()
    {
        $extraFooter = <<<script
        <script>
        
        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
             
         $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
                  "lengthMenu": [
                    [3, 50, -1],
                    [3, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
               $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
                  "lengthMenu": [
                    [10, 50, -1],
                    [10, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
        
        });
        
         
         function ReimpresionEstatus(valor, ticket)
         {
             if(valor == 1)
                 {
                      accion = 'AUTORIZAR';
                 }
             else if(valor == 2)
                 {
                      accion = 'RECHAZAR';
                 }
                 
                 swal({
                         title: "¿Está segur(a) de " + accion +" la solicitud de reimpresión del ticket?",
                         text: 'No podrá deshacer está acción. ',
                         icon: "warning",
                         buttons: ["Cancelar", "Continuar"],
                         dangerMode: false
                         })
                         .then((willDelete) => {
                         if (willDelete) {
                                     
                          $.ajax({
                                type: 'POST',
                                url: '/AdminSucursales/TicketSolicitudUpdate/',
                                data: {"valor" : valor, "ticket" : ticket},
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
                                });
                         
             
             
         }
        

        </script>
script;


        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];


        $Transacciones = CajaAhorroDao::GetSolicitudesPendientesAdminAll();
        $tabla = "";
        foreach ($Transacciones as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 15px !important;"><span class="fa fa-barcode"></span> {$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">
                        <div style="text-align: left; margin-left: 10px; margin-top: 5px;">
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div style="text-align: left; margin-left: 10px;">
                            <b>CLIENTE: </b>{$value['NOMBRE_CLIENTE']}
                        </div>
                        
                        <hr style="margin-bottom: 8px; margin-top: 8px;">
                        
                         <div style="text-align: left; margin-left: 10px;">
                            <b>MOTIVO: </b>{$value['MOTIVO']}
                        </div>
                         <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-female"></span> CAJERA QUE REALIZA SOLICITUD: </b>{$value['NOMBRE_CAJERA']}
                        </div>
                         <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-female"></span> DESCRIPCION CAJERA: </b>{$value['DESCRIPCION_MOTIVO']}
                        </div>
                        <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-calendar-check-o"></span> FECHA DE SOLICITUD: </b>{$value['FREGISTRO']}
                        </div> 
                        
                    </td>
                    <td style="padding: 10px!important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="ReimpresionEstatus('1','{$value['CODIGO_REIMPRIME']}')"><i class="fa fa-check-circle"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="ReimpresionEstatus('2','{$value['CODIGO_REIMPRIME']}');"><i class="fa fa-close"></i></button>
                    </td>
                </tr>
html;
        }

        $TransaccionesHistorial = CajaAhorroDao::GetSolicitudesHistorialAdminAll();
        $tabla_his = "";
        foreach ($TransaccionesHistorial as $key_ => $valueh) {
            if ($valueh['AUTORIZA'] == '1') {
                $estatus = 'ACEPTADO';
                $color = '#31BD16';
            } else {
                $estatus = 'RECHAZADO';
                $color = '#9C1508';
            }

            $tabla_his .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 15px !important;"><span class="fa fa-barcode"></span> {$valueh['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">
                        <div style="text-align: left; margin-left: 10px; margin-top: 5px;">
                            <b>CONTRATO:</b> {$valueh['CONTRATO']}
                        </div>
                        <div style="text-align: left; margin-left: 10px;">
                            <b>CLIENTE: </b>{$valueh['NOMBRE_CLIENTE']}
                        </div>
                        
                        <hr style="margin-bottom: 8px; margin-top: 8px;">
                        
                         <div style="text-align: left; margin-left: 10px;">
                            <b>MOTIVO: </b>{$valueh['MOTIVO']}
                        </div>
                         <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-female"></span> CAJERA QUE REALIZA SOLICITUD: </b>{$valueh['NOMBRE_CAJERA']}
                        </div>
                         <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-female"></span> DESCRIPCION CAJERA: </b>{$valueh['DESCRIPCION_MOTIVO']}
                        </div>
                        <div style="text-align: left; margin-left: 10px;">
                            <b><span class="fa fa-calendar-check-o"></span> FECHA DE SOLICITUD: </b>{$valueh['FREGISTRO']}
                        </div> 
                        
                    </td>
                    <td style="padding: 15px !important;"> 
                    
                        <div> <b>ESTATUS:</b> <b style="color: {$color};">{$estatus}</b> </div>
                        <div> <b>AUTORIZA:</b> ({$valueh['CDGPE_AUTORIZA']}) {$valueh['TESORERIA']}</div>
                        <br>
                        <div><b><span class="fa fa-calendar-check-o"></span> FECHA DE AUTORIZACIÓN:</b> ({$valueh['FAUTORIZA']})</div>
                        
                    </td>
                  
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::set('tabla', $tabla);
        View::set('tabla_his', $tabla_his);
        View::render("AdminSucursales/caja_admin_solicitudes");
    }

    public function SolicitudResumenMovimientos()
    {
        $extraFooter = <<<script
        <script>
        
        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
             
         $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
               $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
        
        });
        
        
        
            {$this->mensajes}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->buscaCliente}
            
            
        </script>
script;


        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];
        $opcSucursales = "";
        $situacion_credito = 0;

        $Transacciones = CajaAhorroDao::GetSolicitudesPendientesAdminAll();
        $tabla = "";
        foreach ($Transacciones as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">
                        <div>
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value['NOMBRE_CLIENTE']}
                        </div>
                    </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        view::set('sucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::render("AdminSucursales/caja_admin_solicitudes_resumen_movimientos");
    }

    public function SolicitudRetiroOrdinario()
    {
        $maxFecha = date('Y-m-d', strtotime('+15 day'));

        $extraFooter = <<<script
        <script>         
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            
            const getParameterByName = (name) => {
                name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
                return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
            }
                
            $(document).ready(() => {
                configuraTabla("muestra-cupones", 2)
                var checkAll = 0
                fecha1 = getParameterByName('Inicial')
                fecha2 = getParameterByName('Final')
                
                $("#export_excel_consulta").click(() => {
                    $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2)
                    $('#all').attr('target', '_blank')
                    $("#all").submit()
                })
                
                configuraTabla("muestra-cupones1", 2)
                var checkAll = 0
                fecha1 = getParameterByName('Inicial')
                fecha2 = getParameterByName('Final')
            })
             
            const actualizaSolicitud = (valor, idSolicitud, fa = null) => {
                if (valor === 3) return modificarSolicitud(idSolicitud, fa)
                const accion = valor === 1 ?  "AUTORIZAR" : "RECHAZAR"
                const mensaje = document.createElement("div")
                mensaje.style.color = "black"
                mensaje.style.fontSize = "15px"
                mensaje.innerHTML = "<p>¿Está seguro de <b>" + accion + "</b> la solicitud de retiro programado?</p><p style='font-weight: bold'>Esta acción no se puede deshacer.</p>"
                 
                confirmarMovimiento("Solicitudes de retiro Programado", null, mensaje)
                    .then((confirmacion) => {
                        if (!confirmacion) return
                         
                        consultaServidor(
                            "/AdminSucursales/ActualizaSolicitudRetiro/",
                            { idSolicitud, estatus: valor, ejecutivo: "{$_SESSION['usuario']}" },
                            (respuesta) => {
                                if (!respuesta.success) return showError(respuesta.mensaje)
                                showSuccess(respuesta.mensaje).then(() => {
                                    if (valor === 2) {
                                        return consultaServidor("/Ahorro/ResumenEntregaRetiro", $.param({id: idSolicitud}), (respuesta) => {
                                            if (respuesta.success) return devuelveRetiro(respuesta.datos)
                                             
                                            console.log(respuesta.error)
                                            showError(respuesta.mensaje)
                                        })
                                    }
                                    swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                                    window.location.reload()
                                })
                            })
                    })
            }
             
            const devuelveRetiro = (datos) => {
                const datosDev = {
                    cliente: datos.CLIENTE,
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
                        // imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco_ahorro']}", false)
                        swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                        window.location.reload()
                    })
                })
            }
             
            const modificarSolicitud = (idSolicitud, fechaAnterior) => {
                const fa = fechaAnterior.split("/")
                const fn = new Date(fa[2], fa[1] - 1, fa[0], 0, 0, 0, 0)
                 
                document.querySelector("#id_solicitud").value = idSolicitud
                document.querySelector("#fecha_anterior").value = fa[2] + "-" + fa[1] + "-" + fa[0]
                 
                const nuevaFecha = document.querySelector("#fecha_nueva")
                nuevaFecha.value = fa[2] + "-" + fa[1] + "-" + fa[0]
                nuevaFecha.min = fa[2] + "-" + fa[1] + "-" + fa[0]
                nuevaFecha.max = new Date(fn.setDate(fn.getDate() + 15)).toISOString().split("T")[0]
                $("#modal_cambio_fecha").modal("show")
            }
             
            const cambiaFecha = () => {
                const fechaNueva = document.querySelector("#fecha_nueva").valueAsDate.toISOString().split("T")[0]
                const fechaAnterior = document.querySelector("#fecha_anterior").value
                const idSolicitud = document.querySelector("#id_solicitud").value                 
                if (fechaNueva === "") return showError("Debe seleccionar una fecha")
                if (fechaNueva === fechaAnterior) return showError("La fecha seleccionada es igual a la anterior")
                if (new Date(fechaNueva) < new Date(fechaAnterior)) return showError("La fecha seleccionada no puede ser menor a la anterior")
                if (new Date(fechaNueva).getDay() === 0) return showError("La nueva fecha fecha de entrega no se puede agendar para un domingo")
                if (new Date(fechaNueva).getDay() === 6) return showError("La nueva fecha fecha de entrega no se puede agendar para un sábado")
                 
                consultaServidor(
                    "/AdminSucursales/ModificaSolicitudRetiro/",
                    $.param({ idSolicitud, fechaNueva, fechaAnterior }),
                    (respuesta) => {
                        if (!respuesta.success) return showError(respuesta.mensaje)
                        showSuccess(respuesta.mensaje).then(() => {
                            swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                            window.location.reload()
                        })
                    })
            }
        </script>
        script;

        $tabla =  "";
        $SolicitudesOrdinarias = CajaAhorroDao::GetSolicitudesRetiroAhorroOrdinario();

        foreach ($SolicitudesOrdinarias as $key => $value) {
            $cantidad_formateada = number_format($value['CANTIDAD_SOLICITADA'], 2, '.', ',');
            $img =  '<img src="https://cdn-icons-png.flaticon.com/512' . ($value['TIPO_PRODUCTO'] == 'AHORRO CUENTA CORRIENTE' ? '/5575/5575939' : '/2995/2995467') . '.png" style="border-radius: 3px; padding-top: 5px;" width="33" height="35">';

            $tabla .= <<<html
                <tr style="padding: 15px!important;">
                    <td style="padding: 15px!important;">
                        <div>
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value['CLIENTE']}
                        </div>
                         <div>
                            <b>SUCURSAL: </b>{$value['SUCURSAL']}
                        </div>
                    </td>
                    <td style="padding: 15px!important;">
                     <div>
                            <b>FECHA PREVISTA ENTREGA:</b> {$value['FECHA_SOLICITUD']}
                        </div>
                        <div>
                            <b>CANTIDAD SOLICITADA: </b>$ {$cantidad_formateada}
                        </div>
                        <div>
                            <b>TIPO DE PRODUCTO: </b>{$value['TIPO_PRODUCTO']} {$img}
                        </div>
                        <hr>
                         <div>
                            <b>ESTATUS DE LA SOLICITUD: </b>{$value['SOLICITUD_VENCIDA']}
                        </div>
                         <div>
                            <b>CAJERA SOLICITA: </b>{$value['CDGPE_NOMBRE']}
                        </div>
                     </td>
                     <td style="padding: 10px!important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="actualizaSolicitud(1,{$value['ID_SOL_RETIRO_AHORRO']});"><i class="fa fa-check-circle"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="actualizaSolicitud(2,{$value['ID_SOL_RETIRO_AHORRO']});"><i class="fa fa-close"></i></button>
                        <button type="button" class="btn btn-info btn-circle" onclick="actualizaSolicitud(3,{$value['ID_SOL_RETIRO_AHORRO']},'{$value['FECHA_SOLICITUD_EXCEL']}');"=><i class="fa fa-edit"></i></button>
                    </td>
                </tr>
            html;
        }


        ///// Aqui es en donde se van a parametrizar las fechas de busqueda, spolo para el historial
        $tabla_historial =  "";
        $SolicitudesOrdinarias_Historial = CajaAhorroDao::GetSolicitudesRetiroAhorroOrdinariaHistorial();

        foreach ($SolicitudesOrdinarias_Historial as $key => $value_historial) {
            $cantidad_formateada = number_format($value_historial['CANTIDAD_SOLICITADA'], 2, '.', ',');
            $img =  '<img src="https://cdn-icons-png.flaticon.com/512' . ($value['TIPO_PRODUCTO'] == 'AHORRO CUENTA CORRIENTE' ? '/5575/5575939' : '/2995/2995467') . '.png" style="border-radius: 3px; padding-top: 5px;" width="33" height="35">';

            $tabla_historial .= <<<html
                <tr style="padding: 15px!important;">
                    <td style="padding: 15px!important;">
                        <div>
                            <b>CONTRATO:</b> {$value_historial['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value_historial['CLIENTE']}
                        </div>
                         <div>
                            <b>SUCURSAL: </b>{$value['SUCURSAL']}
                        </div>
                    </td>
                    <td style="padding: 15px!important;">
                     <div>
                            <b>FECHA ENTREGA:</b> {$value_historial['FECHA_SOLICITUD']}
                        </div>
                        <div>
                            <b>CANTIDAD SOLICITADA: </b>$ {$cantidad_formateada}
                        </div>
                        <div>
                            <b>TIPO DE PRODUCTO: </b>{$value_historial['TIPO_PRODUCTO']} {$img}
                        </div>
                        <hr>
                         <div>
                            <b>ESTATUS DE LA SOLICITUD: </b>{$value_historial['SOLICITUD_VENCIDA']}
                        </div>
                         <div>
                            <b>CAJERA SOLICITA: </b>{$value_historial['CDGPE_NOMBREE']}
                        </div>
                     </td>
                </tr>
            html;
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Solicitudes Pendientes Retiros Programados")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::set('tabla', $tabla);
        View::set('tabla_historial', $tabla_historial);
        View::render("AdminSucursales/caja_admin_solicitudes_retiro_ordinario");
    }

    public function ActualizaSolicitudRetiro()
    {
        echo CajaAhorroDao::ActualizaSolicitudRetiro($_POST);
    }

    public function ModificaSolicitudRetiro()
    {
        echo CajaAhorroDao::ModificaSolicitudRetiro($_POST);
    }

    public function SolicitudRetiroExpress()
    {
        $extraFooter = <<<script
        <script>
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->muestraPDF}
            {$this->imprimeTicket}
            
            const getParameterByName = (name) => {
                name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
                return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
            }
                
            $(document).ready(() => {
                configuraTabla("muestra-cupones", 2)
                var checkAll = 0
                fecha1 = getParameterByName('Inicial')
                fecha2 = getParameterByName('Final')
                
                $("#export_excel_consulta").click(() => {
                    $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2)
                    $('#all').attr('target', '_blank')
                    $("#all").submit()
                })
                
                configuraTabla("muestra-cupones1", 2)
                var checkAll = 0
                fecha1 = getParameterByName('Inicial')
                fecha2 = getParameterByName('Final')
            })
             
            const actualizaSolicitud = (valor, idSolicitud) => {
                const accion = valor === 1 ?  'AUTORIZAR' : 'RECHAZAR'
                const mensaje = document.createElement("div")
                mensaje.style.color = "black"
                mensaje.style.fontSize = "15px"
                mensaje.innerHTML = "<p>¿Está seguro de <b>" + accion + "</b> la solicitud de retiro Express?</p><p style='font-weight: bold'>Esta acción no se puede deshacer.</p>"
                 
                confirmarMovimiento("Solicitudes de retiro Express", null, mensaje)
                    .then((confirmacion) => {
                        if (!confirmacion) return
                         
                        consultaServidor(
                            "/AdminSucursales/ActualizaSolicitudRetiro/",
                            { idSolicitud, estatus: valor, ejecutivo: "{$_SESSION['usuario']}" },
                            (respuesta) => {
                                if (!respuesta.success) return showError(respuesta.mensaje)
                                showSuccess(respuesta.mensaje).then(() => {
                                    if (valor === 2) {
                                        return consultaServidor("/Ahorro/ResumenEntregaRetiro", $.param({id: idSolicitud}), (respuesta) => {
                                            if (respuesta.success) return devuelveRetiro(respuesta.datos)
                                             
                                            console.log(respuesta.error)
                                            return showError(respuesta.mensaje)
                                        })
                                    }
                                     
                                    swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                                    window.location.reload()
                                })
                            })
                    })
            }
             
            const devuelveRetiro = (datos) => {
                const datosDev = {
                    cliente: datos.CLIENTE,
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
                        // imprimeTicket(respuesta.datos.ticket, "{$_SESSION['cdgco_ahorro']}", false)
                        swal({ text: "Actualizando pagina...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false })
                        window.location.reload()
                    })
                })
            }
        </script>
        script;

        $tabla =  "";
        $SolicitudesOrdinarias = CajaAhorroDao::GetSolicitudesRetiroAhorroExpress();

        foreach ($SolicitudesOrdinarias as $key => $value) {
            $cantidad_formateada = number_format($value['CANTIDAD_SOLICITADA'], 2, '.', ',');
            $img =  '<img src="https://cdn-icons-png.flaticon.com/512' . ($value['TIPO_PRODUCTO'] == 'AHORRO CUENTA CORRIENTE' ? '/5575/5575939' : '/2995/2995467') . '.png" style="border-radius: 3px; padding-top: 5px;" width="33" height="35">';

            $tabla .= <<<html
                <tr style="padding: 15px!important;">
                    <td style="padding: 15px!important;">
                        <div>
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value['CLIENTE']}
                        </div>
                         <div>
                            <b>SUCURSAL: </b>{$value['SUCURSAL']}
                        </div>
                    </td>
                    <td style="padding: 15px!important;">
                     <div>
                            <b>FECHA PREVISTA ENTREGA:</b> {$value['FECHA_SOLICITUD']}
                        </div>
                        <div>
                            <b>CANTIDAD SOLICITADA: </b>$ {$cantidad_formateada}
                        </div>
                        <div>
                            <b>TIPO DE PRODUCTO: </b>{$value['TIPO_PRODUCTO']} {$img}
                        </div>
                        <hr>
                         <div>
                            <b>ESTATUS DE LA SOLICITUD: </b>{$value['SOLICITUD_VENCIDA']}
                        </div>
                         <div>
                            <b>CAJERA SOLICITA: </b>{$value['CDGPE_NOMBRE']}
                        </div>
                     </td>
                    <td style="padding: 15px !important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="actualizaSolicitud(1, {$value['ID_SOL_RETIRO_AHORRO']})"><i class="fa fa-check-circle"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="actualizaSolicitud(2, {$value['ID_SOL_RETIRO_AHORRO']});"><i class="fa fa-close"></i></button>
                    </td>
                </tr>
html;
        }

        $SolicitudesExpressHistorial = CajaAhorroDao::GetSolicitudesRetiroAhorroExpressHistorial();
        $tabla_historial =  "";

        foreach ($SolicitudesExpressHistorial as $key => $value_historial) {
            $cantidad_formateada = number_format($value_historial['CANTIDAD_SOLICITADA'], 2, '.', ',');
            $img =  '<img src="https://cdn-icons-png.flaticon.com/512' . ($value_historial['TIPO_PRODUCTO'] == 'AHORRO CUENTA CORRIENTE' ? '/5575/5575939' : '/2995/2995467') . '.png" style="border-radius: 3px; padding-top: 5px;" width="33" height="35">';

            if ($value_historial['ESTATUS_ASIGNA_ACEPTA'] == 'APROBADO') {
                $estatus = 'ACEPTADO';
                $color = '#31BD16';
            } else {
                $estatus = 'RECHAZADO';
                $color = '#9C1508';
            }


            $tabla_historial .= <<<html
                <tr style="padding: 15px!important;">
                    <td style="padding: 15px!important;">
                        <div>
                            <b>CONTRATO:</b> {$value_historial['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value_historial['CLIENTE']}
                        </div>
                         <div>
                            <b>SUCURSAL: </b>{$value_historial['SUCURSAL']}
                        </div>
                         <div style="padding: 10px;">
                            <b>CAJERA SOLICITA: </b>{$value_historial['CDGPE_NOMBRE']}
                        </div>
                    </td>
                    <td style="padding: 15px!important;">
                         <div>
                            <b>FECHA DE LA SOLICITUD EN CAJA: <br></b> {$value_historial['FECHA_SOLICITUD']}
                        </div>
                        <br>
                        
                        <div style="padding-top: 18px;">
                            <b>CANTIDAD SOLICITADA: </b>$ {$cantidad_formateada}
                        </div>
                        <div>
                            <b>TIPO DE PRODUCTO: </b>{$value_historial['TIPO_PRODUCTO']} {$img}
                        </div>
                     
                     </td>
                     <td style="padding: 15px!important;">
                         <div>
                            <b>ESTATUS FINAL: </b><b style="color: {$color};">{$value_historial['ESTATUS_ASIGNA_ACEPTA']}</b>
                        </div>
                         <div style="padding: 10px;">
                            <b>ADMIN AUTORIZA: </b>{$value_historial['CDGPE_NOMBRE_AUTORIZA']}
                        </div>
                        <div>
                            <b>FECHA MAXIMA DE ENTREGA: <br></b> {$value_historial['FECHA_SOLICITUD']}
                        </div>
                     </td>
                </tr>
html;
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Admin retiros express")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        View::set('tabla', $tabla);
        View::set('tabla_historial', $tabla_historial);
        View::render("AdminSucursales/caja_admin_solicitudes_retiro_express");
    }

    public function SolicitudRetiroEfectivoCaja()
    {
        $extraFooter = <<<script
        <script>
        
        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
             
         $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
               $("#muestra-cupones1").tablesorter();
          var oTable = $('#muestra-cupones1').DataTable({
                  "lengthMenu": [
                    [6, 50, -1],
                    [6, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
        
        });
        
            {$this->mensajes}
            {$this->noSubmit}
            {$this->soloNumeros}
            {$this->consultaServidor}
            {$this->numeroLetras}
            {$this->primeraMayuscula}
            {$this->addParametro}
            {$this->buscaCliente}
            
            
        </script>
script;


        $fechaActual = date('Y-m-d');
        $Inicial = $_GET['Inicial'];
        $Final = $_GET['Final'];
        $Operacion = $_GET['Operacion'];
        $Producto = $_GET['Producto'];
        $Sucursal = $_GET['Sucursal'];
        $opcSucursales = "";
        $situacion_credito = "";


        $Transacciones = CajaAhorroDao::GetSolicitudesPendientesAdminAll();
        $tabla = "";
        foreach ($Transacciones as $key => $value) {

            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">
                        <div>
                            <b>CONTRATO:</b> {$value['CONTRATO']}
                        </div>
                        <div>
                            <b>CLIENTE: </b>{$value['NOMBRE_CLIENTE']}
                        </div>
                        
                        <div>
                            <b>SUCURSAL: </b> FALTA CORREGIR
                        </div>
                    </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">{$value['CDGTICKET_AHORRO']} </td>
                    <td style="padding: 0px !important;">  
                        <button type="button" class="btn btn-success btn-circle" onclick="EditarPago('{$value['FECHA']}', '{$value['CDGNS']}', '{$value['NOMBRE']}', '{$value['CICLO']}', '{$value['TIP']}', '{$value['MONTO']}', '{$value['CDGOCPE']}', '{$value['SECUENCIA']}', '{$situacion_credito}');"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-danger btn-circle" onclick="FunDelete_Pago('{$value['SECUENCIA']}', '{$value['FECHA']}', '{$this->__usuario}');"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
html;
        }


        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Reporteria")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', date('Y-m-d'));
        view::set('sucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::render("AdminSucursales/caja_admin_solicitudes_retirar_efectivo_sucursal");
    }

    public function TicketSolicitudUpdate()
    {
        $solicitud = new \stdClass();

        $solicitud->_valor = MasterDom::getDataAll('valor');
        $solicitud->_ticket = MasterDom::getData('ticket');

        $id = CajaAhorroDao::AutorizaSolicitudtICKET($solicitud, $this->__usuario);

        echo $id;
    }

    public function ConfiguracionUsuarios()
    {
        $extraFooter = <<<script
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
                    "targets": 0,
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
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
             $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Operaciones/generarExcelPagos/?Inicial='+fecha1+'&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });        
        });
        </script>
script;



        $userAdmin = AdminSucursalesDao::GetUsuariosAdminAhorro();
        $tabla = "";
        foreach ($userAdmin as $key => $value) {
            if ($value['ESTADO'] == 0) {
                $estatus = 'DADO DE BAJA';
            } else if ($value['ESTADO'] == 1) {
                $estatus = 'ACTIVO';
            } else if ($value['ESTADO'] == 2) {
                $estatus = 'EN ESPERA';
            }
            $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 10px !important;">{$value['CODIGO']}</td>
                    <td style="padding: 10px !important;">{$value['EMPLEADO']}</td>
                    <td style="padding: 10px !important;">{$value['NOMBRE_PUESTO']}</td>
                    <td style="padding: 10px !important;">{$value['NOMBRE_SUCURSAL']} - ({$value['SUCURSAL']})</td>
                     <td style="padding: 10px !important;">{$estatus}</td>
                   
                </tr>
html;
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Configuración de Caja Usuarios")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::render("AdminSucursales/caja_admin_configurar_usuarios");
    }

    public function ConfiguracionParametros()
    {
        $extraFooter = <<<script
        <script>
         
        </script>
script;

        $opcSucursales = "";
        $tabla = "";
        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Configuración de Caja")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcSucursales', $opcSucursales);
        View::set('tabla', $tabla);
        View::set('fecha', date('d/m/Y H:i:s'));
        View::render("AdminSucursales/caja_admin_configurar_parametros");
    }

    public function HistorialFondeoSucursal()
    {
        $extraFooter = <<<script
        <script>
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->noSubmit}
            {$this->exportaExcel}
            {$this->crearFilas}
         
            $(document).ready(() => {
                configuraTabla("fondeos")
                $("#export_excel_consulta").click(() => imprimeExcel("fondeos"))
            })
             
            const imprimeExcel = (id) => exportaExcel(id, 'Historial Fondeo Sucursal')
             
            const validaFechas = () => {
                const fechaI = $("#fechaI").val()
                const fechaF = $("#fechaF").val()
                 
                if (fechaI === "" || fechaF === "") return showError("Debe seleccionar ambas fechas.")
                if (fechaI > fechaF) {
                    $("#fechaI").val(fechaF)
                    return showError("La fecha inicial no puede ser mayor a la fecha final.")
                }
            }
             
            const buscarFondeos = () => {
                const datos = {
                    fechaI: $("#fechaI").val(),
                    fechaF: $("#fechaF").val()
                }
                 
                if ($("#sucursal").val() !== "0") datos.sucursal = $("#sucursal").val()
                 
                consultaServidor(
                    "/AdminSucursales/GetHistorialFondeosSucursal/",
                    $.param(datos),
                    (resultado) => {
                        $("#fondeos").DataTable().destroy()
                        $("#fondeos tbody").html("")
                         
                        if (!resultado.success) showError(resultado.mensaje)
                        else $("#fondeos tbody").html(creaFilas(resultado.datos))
                        
                        configuraTabla("fondeos")
                    })
            }
        </script>
        script;

        $fechaI = date('Y-m-d');
        $fechaF = date('Y-m-d');
        $param = [
            'fechaI' => $fechaI,
            'fechaF' => $fechaF
        ];

        if ($_SESSION['usuario'] !== 'AMGM') $param['sucursal'] = $_SESSION['cdgco_ahorro'];
        $datos = AdminSucursalesDao::GetHistorialFondeosSucursal($param);
        $datos = json_decode($datos, true);

        $filas = "";
        if ($datos['success']) {
            foreach ($datos['datos'] as $key => $value) {
                $filas .= "<tr>";
                foreach ($value as $key2 => $value2) {
                    $filas .= "<td>{$value2}</td>";
                }
                $filas .= "</tr>";
            }
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial Fondeo Sucursal", [$this->XLSX])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fechaI', $fechaI);
        View::set('fechaF', $fechaF);
        View::set('filas', $filas);
        View::set('opcSucursales', self::GetSucursalesReporteria());
        View::render("AdminSucursales/caja_admin_historial_fondeo");
    }

    public function HistorialRetiroSucursal()
    {
        $extraFooter = <<<script
        <script>
            {$this->mensajes}
            {$this->confirmarMovimiento}
            {$this->consultaServidor}
            {$this->configuraTabla}
            {$this->noSubmit}
            {$this->exportaExcel}
            {$this->crearFilas}
         
            $(document).ready(() => {
                configuraTabla("retiros")
                $("#export_excel_consulta").click(() => imprimeExcel("retiros"))
            })
             
            const imprimeExcel = (id) => exportaExcel(id, 'Historial Retiros Sucursal')
             
            const validaFechas = () => {
                const fechaI = $("#fechaI").val()
                const fechaF = $("#fechaF").val()
                 
                if (fechaI === "" || fechaF === "") return showError("Debe seleccionar ambas fechas.")
                if (fechaI > fechaF) {
                    $("#fechaI").val(fechaF)
                    return showError("La fecha inicial no puede ser mayor a la fecha final.")
                }
            }
             
            const buscarRetirosSucursal  = () => {
                const datos = {
                    fechaI: $("#fechaI").val(),
                    fechaF: $("#fechaF").val()
                }
                 
                if ($("#sucursal").val() !== "0") datos.sucursal = $("#sucursal").val()
                
                consultaServidor(
                    "/AdminSucursales/GetHistorialRetirosSucursal/",
                    $.param(datos),
                    (resultado) => {
                        $("#retiros").DataTable().destroy()
                        $("#retiros tbody").html("")
                        
                        if (!resultado.success) showError(resultado.mensaje)
                        else $("#retiros tbody").html(creaFilas(resultado.datos))
                        
                        configuraTabla("retiros")
                    })
            }
        </script>
script;

        $fechaI = date('Y-m-d');
        $fechaF = date('Y-m-d');
        $datos = AdminSucursalesDao::GetHistorialRetirosSucursal(['fechaI' => $fechaI, 'fechaF' => $fechaF, 'sucursal' => $_SESSION['cdgco_ahorro']]);
        $datos = json_decode($datos, true);

        $filas = "";
        if ($datos['success']) {
            foreach ($datos['datos'] as $key => $value) {
                $filas .= "<tr>";
                foreach ($value as $key2 => $value2) {
                    $filas .= "<td>{$value2}</td>";
                }
                $filas .= "</tr>";
            }
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Historial Retiro Sucursal", [$this->XLSX])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fechaI', $fechaI);
        View::set('fechaF', $fechaF);
        View::set('filas', $filas);
        View::set('opcSucursales', self::GetSucursalesReporteria());
        View::render("AdminSucursales/caja_admin_historial_retiro_sucursal");
    }

    public function GetSucursalesReporteria()
    {
        $sucursales = CajaAhorroDao::GetSucursalAsignadaCajeraAhorro();
        $opcSucursales = "";
        foreach ($sucursales as $sucursales) {
            $opcSucursales .= "<option value='{$sucursales['CODIGO']}'" . ($sucursales['CODIGO'] === $_SESSION['cdgco_ahorro'] ? 'Selected' : '') . ">{$sucursales['NOMBRE']} ({$sucursales['CODIGO']})</option>";
        }
        return $opcSucursales;
    }

    public function LogConfiguracion()
    {
        $extraFooter = <<<script
        <script>
            {$this->mensajes}
            {$this->crearFilas}
         
            const getLog = () => {
                const datos = {
                    fecha_inicio: $("#fInicio").val(),
                    fecha_fin: $("#fFin").val()
                }
                 
                const op = document.querySelector("#operacion")
                const us = document.querySelector("#usuario")
                
                if (op.value !== "0") datos.operacion = op.options[op.selectedIndex].text
                if (us.value !== "0") datos.usuario = us.options[us.selectedIndex].text
                 
                $.ajax({
                    type: "POST",
                    url: "/Ahorro/GetLogTransacciones/",
                    data: datos,
                    success: (log) => {
                        $("#log").DataTable().destroy()
                         
                        log = JSON.parse(log)
                        let datos = log.datos
                         
                        if (!log.success) {
                            showError(log.mensaje)
                            datos = []
                        }
                        
                        $("#log tbody").html(creaFilas(datos))
                        $("#log").DataTable({
                            lengthMenu: [
                                [10, 40, -1],
                                [10, 40, "Todos"]
                            ],
                            columnDefs: [
                                {
                                    orderable: false,
                                    targets: 0
                                }
                            ],
                            order: false
                        })
                    },
                    error: (error) => {
                        console.error(error)
                        showError("Ocurrió un error al buscar el log de transacciones.")
                    }
                })
                 
                return false
            }
             
            $(document).ready(() => {
                getLog()
            })
        </script>
        script;

        $operaciones = CajaAhorroDao::GetOperacionesLog();
        $usuarios = CajaAhorroDao::GetUsuariosLog();
        $sucursales = CajaAhorroDao::GetSucursalesLog();

        $opcOperaciones = "<option value='0'>Todas</option>";
        foreach ($operaciones as $key => $operacion) {
            $i = $key + 1;
            $opcOperaciones .= "<option value='{$i}'>{$operacion['TIPO']}</option>";
        }

        $opcUsuarios = "<option value='0'>Todos</option>";
        foreach ($usuarios as $key => $usuario) {
            $i = $key + 1;
            $opcUsuarios .= "<option value='{$i}'>{$usuario['USUARIO']}</option>";
        }

        $opcSucursales = "<option value='0'>Todas</option>";
        foreach ($sucursales as $key => $sucursal) {
            $i = $key + 1;
            $opcSucursales .= "<option value='{$i}'>{$sucursal['NOMBRE']}</option>";
        }

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Log Transacciones Configuración")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcOperaciones', $opcOperaciones);
        View::set('opcUsuarios', $opcUsuarios);
        View::set('opcSucursales', $opcSucursales);
        View::set(('fecha'), date('Y-m-d'));
        View::render("AdminSucursales/caja_admin_log_configuracion");
    }

    public function genExcelSolsRetOrdPendiente()
    {
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('ID_SOL_RETIRO_AHORRO'),
            \PHPSpreadsheet::ColumnaExcel('CONTRATO'),
            \PHPSpreadsheet::ColumnaExcel('CLIENTE'),
            \PHPSpreadsheet::ColumnaExcel('FECHA_SOLICITUD'),
            \PHPSpreadsheet::ColumnaExcel('DAYS_SINCE_ORDER'),
            \PHPSpreadsheet::ColumnaExcel('SOLICITUD_VENCIDA'),
            \PHPSpreadsheet::ColumnaExcel('CANTIDAD_SOLICITADA'),
            \PHPSpreadsheet::ColumnaExcel('CDGPE'),
            \PHPSpreadsheet::ColumnaExcel('CDGPE_NOMBRE'),
            \PHPSpreadsheet::ColumnaExcel('TIPO_RETIRO'),
            \PHPSpreadsheet::ColumnaExcel('FECHA_ENTREGA'),
            \PHPSpreadsheet::ColumnaExcel('TIPO_PRODUCTO')
        ];

        $filas = CajaAhorroDao::GetSolicitudesRetiroAhorroOrdinario();
        \PHPSpreadsheet::DescargaExcel('Reporte Solicitudes Pendientes Ordinaria', 'Reporte', 'Solicitudes Pendientes', $columnas, $filas);
    }

    public function generarExcelPagosTransaccionesAll()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('CLIENTE', 'Cliente', ['estilo' => $estilos['centrado']]),
            \PHPSpreadsheet::ColumnaExcel('TITULAR_CUENTA_EJE', 'Titular'),
            \PHPSpreadsheet::ColumnaExcel('FECHA_MOV', 'Fecha Movimiento', ['estilo' => $estilos['fecha_hora']]),
            \PHPSpreadsheet::ColumnaExcel('SUCURSAL', 'Sucursal'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_CAJERA', 'Cajera'),
            \PHPSpreadsheet::ColumnaExcel('MONTO', 'Monto', ['estilo' => $estilos['moneda']]),
            \PHPSpreadsheet::ColumnaExcel('CONCEPTO', 'Concepto'),
            \PHPSpreadsheet::ColumnaExcel('PRODUCTO', 'Producto'),
            \PHPSpreadsheet::ColumnaExcel('REPORTE', 'Saldo Inicial', ['estilo' => $estilos['moneda']]),
            \PHPSpreadsheet::ColumnaExcel('INGRESO', 'Ingreso', ['estilo' => $estilos['moneda'], 'total' => true]),
            \PHPSpreadsheet::ColumnaExcel('EGRESO', 'Egreso', ['estilo' => $estilos['moneda'], 'total' => true]),
            \PHPSpreadsheet::ColumnaExcel('SALDO', 'Saldo', ['estilo' => $estilos['moneda']])
        ];

        $fecha_inicio = $_GET['Inicial'];
        $operacion = $_GET['Operacion'];
        $producto = $_GET['Producto'];
        $sucursal = $_GET['Sucursal'];
        $filas = CajaAhorroDao::GetAllTransacciones($fecha_inicio, $fecha_inicio, $operacion, $producto, $sucursal);

        \PHPSpreadsheet::DescargaExcel('Reporte Movimientos Caja', 'Reporte', 'Flujo de Efectivo', $columnas, $filas);
    }

    public function generarExcelPagosTransaccionesDetalleAll()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('FECHA_MOV', 'FECHA MOVIMIENTO', ['estilo' => $estilos['fecha_hora']]),
            \PHPSpreadsheet::ColumnaExcel('CDGCO', 'COD SUCURSAL', ['estilo' => $estilos['centrado']]),
            \PHPSpreadsheet::ColumnaExcel('SUCURSAL', 'NOM SUCURSAL', ['estilo' => $estilos['centrado']]),
            \PHPSpreadsheet::ColumnaExcel('FECHA_MOV_APLICA', 'FECHA', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('USUARIO_CAJA', 'USUARIO', ['estilo' => $estilos['centrado']]),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_CAJERA', 'NOMBRE CAJERA'),
            // \PHPSpreadsheet::ColumnaExcel('NOMBRE_PROMOTOR', 'PROMOTOR'),
            \PHPSpreadsheet::ColumnaExcel('CLIENTE', 'CLIENTE', ['estilo' => $estilos['centrado']]),
            \PHPSpreadsheet::ColumnaExcel('TITULAR_CUENTA_EJE', 'TITULAR CUENTA'),
            // \PHPSpreadsheet::ColumnaExcel('ID_MENOR', 'ID MENOR', ['estilo' => $estilos['centrado']]),
            // \PHPSpreadsheet::ColumnaExcel('NOMBRE_MENOR', 'NOMBRE MENOR'),
            \PHPSpreadsheet::ColumnaExcel('MONTO', 'MONTO', ['estilo' => $estilos['moneda']]),
            \PHPSpreadsheet::ColumnaExcel('CONCEPTO'),
            // \PHPSpreadsheet::ColumnaExcel('PLAZO_INVERSION', 'PLAZO INVERSION', ['estilo' => $estilos['centrado']]),
            // \PHPSpreadsheet::ColumnaExcel('FECHA_FIN_INVERSION', 'FECHA FIN INVERSION', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('PRODUCTO'),
            \PHPSpreadsheet::ColumnaExcel('TIPO_MOVIMIENTO', 'MOVIMIENTO', ['estilo' => $estilos['centrado']]),
            \PHPSpreadsheet::ColumnaExcel('INGRESO', 'INGRESO', ['estilo' => $estilos['moneda'], 'total' => true]),
            \PHPSpreadsheet::ColumnaExcel('EGRESO', 'EGRESO', ['estilo' => $estilos['moneda'], 'total' => true]),
        ];

        $fecha_inicio = $_GET['Inicial'];
        $fecha_fin = $_GET['Final'];
        $operacion = $_GET['Operacion'];
        $producto = $_GET['Producto'];
        $sucursal = $_GET['Sucursal'];
        $filas = CajaAhorroDao::GetAllTransaccionesDetalle($fecha_inicio, $fecha_fin, $operacion, $producto, $sucursal);

        \PHPSpreadsheet::DescargaExcel('Reporte Movimientos Caja', 'Reporte', 'Consulta de Movimientos de Ahorro a Detalle (incluye transacciones virtuales)', $columnas, $filas);
    }

    public function GetHistorialFondeosSucursal()
    {
        echo AdminSucursalesDao::GetHistorialFondeosSucursal($_POST);
    }

    public function GetHistorialRetirosSucursal()
    {
        echo AdminSucursalesDao::GetHistorialRetirosSucursal($_POST);
    }
}
