<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\Operaciones as OperacionesDao;

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
        View::render('Operaciones/operaciones_cierre_diario');
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

        View::render('Operaciones/operaciones_reporte_pc');
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
