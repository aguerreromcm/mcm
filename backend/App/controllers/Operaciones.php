<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\Operaciones as OperacionesDao;
use App\services\PagosAplicacionService;
use App\services\ConciliacionService;
use App\services\CierreDiaService;
use App\services\CierreDiaJobLock;

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
        $datosPantalla = CierreDiaService::obtenerDatosPantalla();
        $datos = $datosPantalla['success'] && isset($datosPantalla['datos']) ? $datosPantalla['datos'] : [];
        $listaCierres = isset($datos['ultimos5']) ? $datos['ultimos5'] : [];
        $ejecutando = !empty($datos['ejecutando']) ? 1 : 0;
        $inicioEjecucion = isset($datos['inicio']) ? $datos['inicio'] : '';
        $usuarioEjecucion = isset($datos['usuario']) ? $datos['usuario'] : '';
        $segundosTranscurridos = isset($datos['segundos']) ? (int) $datos['segundos'] : 0;
        $estimado = isset($datos['tiempoEstimado']) ? (int) $datos['tiempoEstimado'] : 0;
        $perfil = isset($this->__perfil) ? (string) $this->__perfil : '';

        $ini = @parse_ini_file(dirname(__DIR__) . '/config/configuracion.ini', true);
        $configCierre = isset($ini['cierre_dia']) && is_array($ini['cierre_dia']) ? $ini['cierre_dia'] : [];
        $val = isset($configCierre['CIERRE_DIA_SOLO_FLUJO']) ? trim((string) $configCierre['CIERRE_DIA_SOLO_FLUJO']) : '';
        $soloFlujo = $val !== '' && (filter_var($val, FILTER_VALIDATE_BOOLEAN) || strtolower($val) === 'true' || $val === '1');
        View::set('soloFlujo', $soloFlujo);

        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
                const estimado = $estimado
                let ejecutando = $ejecutando
                let inicioEjecucion = "$inicioEjecucion"
                let usuarioEjecucion = "$usuarioEjecucion"
                let segundosTranscurridos = $segundosTranscurridos
                let esperaSpDesde = 0
                let fechaEnProceso = ""
                const GRACIA_ARRANQUE_JOB_MS = 20000
                let actualiza = null
                let renueva = null

                const escHtml = (s) => {
                    const d = document.createElement("div")
                    d.textContent = s == null ? "" : String(s)
                    return d.innerHTML
                }

                const refrescarPantallaTrasCierre = () => {
                    refrescarTablaUltimosCierres()
                    if (typeof window.refrescarDatosOperativosCierreDia === "function") {
                        window.refrescarDatosOperativosCierreDia(true)
                    }
                }

                const refrescarTablaUltimosCierres = () => {
                    fetch("/operaciones/UltimosCierresCierreDia", {
                        method: "GET",
                        headers: { Accept: "application/json" }
                    })
                        .then((r) => r.json())
                        .then((resp) => {
                            if (!resp || !resp.success || !resp.datos) return
                            const lista = resp.datos.ultimos5 || []
                            const tbody = document.getElementById("tbodyUltimosCierres")
                            if (!tbody) return
                            if (!lista.length) {
                                tbody.innerHTML = '<tr><td colspan="8">No hay cierres registrados.</td></tr>'
                                return
                            }
                            tbody.innerHTML = lista
                                .map((c) => {
                                    const estado = c.ESTADO_TEXTO
                                        ? String(c.ESTADO_TEXTO)
                                        : (c.EN_PROCESO === 1 || c.EN_PROCESO === "1" || !c.FIN || String(c.FIN).trim() === ""
                                            ? "Procesando"
                                            : (Number(c.EXITO) === 1 ? "Finalizado" : "Error"))
                                    const inicio = escHtml(String(c.INICIO ?? "-"))
                                    const fin = escHtml(String(c.FIN ?? "-"))
                                    return (
                                        "<tr>" +
                                        "<td>" + escHtml(String(c.FECHA_CALCULO ?? "-")) + "</td>" +
                                        "<td>" + inicio + "</td>" +
                                        "<td>" + fin + "</td>" +
                                        "<td>" + escHtml(String(c.USUARIO ?? "-")) + "</td>" +
                                        "<td>" + escHtml(estado) + "</td>" +
                                        "<td>" + escHtml(String(c.REGISTROS_PROCESADOS ?? "0")) + "</td>" +
                                        "<td>" + escHtml(String(c.CREDITOS_DEVENGO ?? "0")) + "</td>" +
                                        "<td>" + escHtml(String(c.MONTO_INTERESES_DEVENGADOS ?? "$ 0.00")) + "</td>" +
                                        "</tr>"
                                    )
                                })
                                .join("")
                        })
                        .catch(() => {})
                }

                const iniciaCierreDiario = () => {
                    if (ejecutando) {
                        showError("Ya hay un proceso de cierre diario en ejecución.")
                        return
                    }
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
                    if (!fecha) { showError("Seleccione la fecha de cierre."); return }
                    consultaServidor("/operaciones/ValidacionPreviaCierre", { fecha }, (respuesta) => {
                        if (!respuesta.success) {
                            showError(respuesta.mensaje || "Ya hay un proceso de cierre en ejecución.")
                            if (respuesta.datos && Object.keys(respuesta.datos).length > 0) {
                                ejecutando = true
                                inicioEjecucion = respuesta.datos.INICIO || inicioEjecucion
                                usuarioEjecucion = respuesta.datos.USUARIO || usuarioEjecucion
                                segundosTranscurridos = parseInt(respuesta.datos.SEGUNDOS, 10) || 0
                                validaEjecucionActiva()
                            }
                            return
                        }
                        var d = respuesta.datos || {}
                        if (d.yaEjecutado && !d.puedeRegenerar) {
                            showError("El cierre de ese día ya fue ejecutado. Solo un administrador puede regenerarlo.")
                            return
                        }
                        if (d.yaEjecutado && d.puedeRegenerar) {
                            confirmarMovimiento("Regenerar cierre", "El cierre del día ya fue procesado. ¿Desea regenerar? Se eliminarán los registros y se crearán nuevos. Esta acción no se puede deshacer.", null).then((ok) => {
                                if (!ok) return
                                pedirPasswordYProcesar(true)
                            })
                            return
                        }
                        procesaCierreDiario(false)
                    })
                }

                const pedirPasswordYProcesar = (regenerar) => {
                    var pass = prompt("Ingrese su contraseña para confirmar la regeneración del cierre:")
                    if (pass === null || pass === "") return
                    $.post("/operaciones/ValidarPasswordCierreDiario", { usuario: "{$this->__usuario}", password: pass }, null, "json").done(function (r) {
                        if (r && r.success) procesaCierreDiario(regenerar)
                        else showError(r && r.mensaje ? r.mensaje : "Contraseña incorrecta.")
                    }).fail(function () { showError("Error al validar la contraseña.") })
                }

                const procesaCierreDiario = (regenerar) => {
                    if (ejecutando) {
                        showError("Ya hay un proceso de cierre diario en ejecución.")
                        return
                    }
                    ejecutando = true
                    fechaEnProceso = $("#fecha").val()
                    $("#procesar").attr("disabled", true)
                    var payload = { fecha: $("#fecha").val(), usuario: "{$this->__usuario}" }
                    if (regenerar) payload.regenerar = "1"
                    consultaServidor("/operaciones/ProcesaCierreDiario", payload, (respuesta) => {
                        if (!respuesta || !respuesta.success) {
                            ejecutando = false
                            $("#procesar").attr("disabled", false)
                            return showError((respuesta && respuesta.mensaje) ? respuesta.mensaje : "No fue posible iniciar el cierre.")
                        }
                        inicioEjecucion = null
                        usuarioEjecucion = null
                        segundosTranscurridos = 0
                        esperaSpDesde = Date.now()
                        const mensaje = respuesta.mensaje || "El proceso de cierre diario se ha iniciado. Se enviará un correo al finalizar."
                        showSuccess(mensaje).then(() => {
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
                    clearTimeout(actualiza)
                    clearTimeout(renueva)
                    if (!ejecutando) {
                        $("#procesar").attr("disabled", false)
                        $("#alertaEjecucion").hide()
                        $("#tiempoEstimado").html("")
                        return
                    }
                    if (!inicioEjecucion) {
                        $("#procesar").attr("disabled", true)
                        $("#tiempoEstimado").html("<p>El cierre se está iniciando. El aviso se mostrará cuando el procedimiento registre actividad en bitácora.</p>")
                        $("#alertaEjecucion").show()
                        renuevaEjecucionActiva()
                        return
                    }
                    const diferencia = Math.max(0, parseInt(segundosTranscurridos, 10) || 0)
                    let mensaje = "<p>El proceso de cierre diario se encuentra en ejecución desde el " + escHtml(String(inicioEjecucion)) + " por el usuario <b>" + escHtml(String(usuarioEjecucion || "-")) + "</b>.</p>"
                    if (estimado > 0) {
                        mensaje += "<p>Tiempo estimado de finalización: <b>" + estimado + "</b> minutos.</p>"
                    }
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
                    clearTimeout(actualiza)
                    actualiza = setTimeout(() => {
                        diferencia++
                        actualizaTiempoEstimado(diferencia)
                        $("#transcurrido").html(getTiempoTranscurrido(diferencia))
                    }, 1000)
                }

                const detenerSeguimientoCierre = () => {
                    ejecutando = false
                    inicioEjecucion = null
                    usuarioEjecucion = null
                    segundosTranscurridos = 0
                    clearTimeout(actualiza)
                    clearTimeout(renueva)
                    $("#procesar").attr("disabled", false)
                    $("#alertaEjecucion").hide()
                    $("#tiempoEstimado").html("")
                }

                // El candado del Job puede soltarse mientras el aviso de inicio sigue abierto: el
                // veredicto lo da el renglón que el SP deja en bitácora, no el temporizador del navegador.
                const resuelveEstatusFinalCierre = (lanzadoAqui) => {
                    const fecha = fechaEnProceso || $("#fecha").val()
                    fetch("/operaciones/EstatusCierreDia?fecha=" + encodeURIComponent(fecha), {
                        method: "GET",
                        headers: { Accept: "application/json" }
                    })
                        .then((r) => r.json())
                        .then((resp) => {
                            const d = (resp && resp.datos) || {}
                            if (d.registrado && d.enProceso) {
                                ejecutando = true
                                inicioEjecucion = d.inicio || inicioEjecucion
                                usuarioEjecucion = d.usuario || usuarioEjecucion
                                validaEjecucionActiva()
                                return
                            }
                            fechaEnProceso = ""
                            detenerSeguimientoCierre()
                            refrescarPantallaTrasCierre()
                            if (!d.registrado) {
                                if (lanzadoAqui) {
                                    showError("El proceso no quedó registrado en bitácora: el procedimiento no se ejecutó. Intente de nuevo.")
                                }
                                return
                            }
                            if (d.exito) {
                                showSuccess("El proceso de cierre diario ha finalizado.")
                            } else {
                                showError("El cierre diario terminó con error. Revise la bitácora antes de reintentarlo.")
                            }
                        })
                        .catch(() => {
                            fechaEnProceso = ""
                            detenerSeguimientoCierre()
                            refrescarPantallaTrasCierre()
                        })
                }

                const renuevaEjecucionActiva = () => {
                    clearTimeout(renueva)
                    const intervalo = esperaSpDesde ? 2000 : 5000
                    renueva = setTimeout(() => {
                        fetch("/operaciones/ValidaCierreEnEjecucion", {
                            method: "GET",
                            headers: {
                                Accept: "application/json"
                            }
                        })
                            .then((response) => response.json())
                            .then((respuesta) => {
                                if (!respuesta || !respuesta.success) {
                                    renuevaEjecucionActiva()
                                    return
                                }
                                const ejecutabaAntes = ejecutando
                                const datos = respuesta.datos || {}
                                const haySp = !!(datos && datos.INICIO)
                                const jobActivo = !!respuesta.jobActivo
                                if (haySp) {
                                    esperaSpDesde = 0
                                    ejecutando = true
                                    inicioEjecucion = datos.INICIO || inicioEjecucion
                                    usuarioEjecucion = datos.USUARIO || usuarioEjecucion
                                    segundosTranscurridos = Math.max(0, parseInt(datos.SEGUNDOS, 10) || 0)
                                    clearTimeout(actualiza)
                                    let mensajePoll = "<p>El proceso de cierre diario se encuentra en ejecución desde el " + escHtml(String(inicioEjecucion)) + " por el usuario <b>" + escHtml(String(usuarioEjecucion || "-")) + "</b>.</p>"
                                    if (estimado > 0) {
                                        mensajePoll += "<p>Tiempo estimado de finalización: <b>" + estimado + "</b> minutos.</p>"
                                    }
                                    mensajePoll += "<p>Tiempo transcurrido: <b id='transcurrido'>" + getTiempoTranscurrido(segundosTranscurridos) + "</b></p>"
                                    $("#tiempoEstimado").html(mensajePoll)
                                    $("#alertaEjecucion").show()
                                    $("#procesar").attr("disabled", true)
                                    actualizaTiempoEstimado(segundosTranscurridos)
                                    refrescarTablaUltimosCierres()
                                    renuevaEjecucionActiva()
                                    return
                                }
                                const enGraciaArranque = esperaSpDesde && (Date.now() - esperaSpDesde) < GRACIA_ARRANQUE_JOB_MS
                                if (jobActivo || enGraciaArranque) {
                                    ejecutando = true
                                    $("#procesar").attr("disabled", true)
                                    if (!inicioEjecucion) {
                                        $("#tiempoEstimado").html("<p>El cierre se está iniciando. El aviso se mostrará cuando el procedimiento registre actividad en bitácora.</p>")
                                        $("#alertaEjecucion").show()
                                    }
                                    renuevaEjecucionActiva()
                                    return
                                }
                                const lanzadoAqui = !!esperaSpDesde
                                if (lanzadoAqui || ejecutabaAntes) {
                                    esperaSpDesde = 0
                                    resuelveEstatusFinalCierre(lanzadoAqui)
                                    return
                                }
                                detenerSeguimientoCierre()
                            })
                            .catch(() => {
                                renuevaEjecucionActiva()
                            })
                    }, intervalo)
                }

                $(document).ready(() => {
                    $("#procesar").click(() => iniciaCierreDiario())
                    if (ejecutando && !inicioEjecucion) {
                        esperaSpDesde = Date.now()
                    }
                    validaEjecucionActiva()
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header(self::GetExtraHeader('Cierre diario')));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('listaCierres', $listaCierres);
        View::render('operaciones_cierre_diario');
    }

    function ValidaCierreEnEjecucion()
    {
        $this->limpiaSalidaParaJson();
        $resp = CierreDiaService::validaCierreEnEjecucion();
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resp);
        exit;
    }

    /**
     * GET JSON: últimos 5 cierres (misma data que la tabla al cargar la página).
     */
    function UltimosCierresCierreDia()
    {
        $this->limpiaSalidaParaJson();
        try {
            $resp = CierreDiaService::obtenerDatosPantalla();
            $datos = $resp['success'] && isset($resp['datos']) ? $resp['datos'] : [];
            $ultimos5 = isset($datos['ultimos5']) ? $datos['ultimos5'] : [];
            $out = ['success' => true, 'datos' => ['ultimos5' => $ultimos5]];
        } catch (\Throwable $e) {
            $out = \Core\Model::Responde(false, 'Error al obtener cierres.', null, $e->getMessage());
        }
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($out);
        exit;
    }

    /**
     * GET/POST: fecha (Y-m-d). JSON: estatus que el SP dejó en bitácora para esa fecha.
     */
    function EstatusCierreDia()
    {
        $this->limpiaSalidaParaJson();
        $fecha = isset($_POST['fecha']) ? trim((string) $_POST['fecha']) : (isset($_GET['fecha']) ? trim((string) $_GET['fecha']) : '');
        $resp = CierreDiaService::estatusCierrePorFecha($fecha);
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($resp);
        exit;
    }

    /**
     * POST/GET: fecha (Y-m-d). JSON: resumen unificado (mismo contenido que el correo al finalizar cierre).
     */
    function InformacionDiaCierre()
    {
        $this->limpiaSalidaParaJson();
        $fecha = isset($_POST['fecha']) ? trim((string) $_POST['fecha']) : (isset($_GET['fecha']) ? trim((string) $_GET['fecha']) : '');
        $resp = CierreDiaService::obtenerResumenCierreDia($fecha);
        if (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode($resp, JSON_UNESCAPED_UNICODE);
        exit;
    }

    function ValidacionPreviaCierre()
    {
        $this->limpiaSalidaParaJson();
        try {
            $fecha = isset($_POST['fecha']) ? trim((string) $_POST['fecha']) : '';
            $perfil = isset($this->__perfil) ? (string) $this->__perfil : '';
            // Como en VB6: la fecha seleccionada en el calendario es la fecha de cierre
            $fechaCierre = $fecha !== '' ? date('Y-m-d', strtotime($fecha)) : '';
            $resp = CierreDiaService::validacionPrevia($fechaCierre, $perfil);
        } catch (\Throwable $e) {
            $resp = \Core\Model::Responde(false, 'Error al validar: ' . $e->getMessage(), null, $e->getMessage());
        }
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resp);
        exit;
    }

    /**
     * POST: usuario, password. Valida contraseña del usuario para permitir regenerar cierre (doble verificación).
     */
    function ValidarPasswordCierreDiario()
    {
        $this->limpiaSalidaParaJson();
        $usuario = isset($_POST['usuario']) ? trim((string) $_POST['usuario']) : '';
        $password = isset($_POST['password']) ? (string) $_POST['password'] : '';
        $ok = \App\models\Login::ValidaPassword($usuario, $password);
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => $ok, 'mensaje' => $ok ? 'Contraseña correcta.' : 'Contraseña incorrecta.']);
        exit;
    }

    /**
     * Descarta cualquier salida previa (p. ej. echo de Database::muestraError) para que la respuesta sea JSON válido.
     */
    private function limpiaSalidaParaJson()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
    }

    function ProcesaCierreDiario()
    {
        $this->limpiaSalidaParaJson();
        try {
            $fecha = isset($_POST['fecha']) ? trim((string) $_POST['fecha']) : '';
            $usuario = isset($this->__usuario) ? (string) $this->__usuario : '';
            $perfil = isset($this->__perfil) ? (string) $this->__perfil : '';
            $regenerar = !empty($_POST['regenerar']) ? 1 : 0;

            if (empty($fecha) || empty($usuario)) {
                echo json_encode(\Core\Model::Responde(false, 'No se ha indicado los parámetros necesarios para el cierre diario.'));
                exit;
            }

            $fechaCierre = date('Y-m-d', strtotime($fecha));

            $jobScript = realpath(__DIR__ . '/../../Jobs/Controllers/JobsCredito.php');
            if ($jobScript === false || !is_file($jobScript)) {
                echo json_encode(\Core\Model::Responde(false, 'No se encontró el Job de cierre diario. No se inició el proceso.'));
                exit;
            }

            $phpBin = $this->resolverBinarioPhpCierreDia();
            if ($phpBin === '') {
                echo json_encode(\Core\Model::Responde(false, 'No se encontró el intérprete PHP para lanzar el Job. No se inició el proceso.'));
                exit;
            }

            $lanzamiento = CierreDiaJobLock::adquirirLanzamiento();
            if ($lanzamiento === null) {
                echo json_encode(\Core\Model::Responde(false, 'Ya hay un proceso de cierre diario en ejecución, no es posible iniciar otro.'));
                exit;
            }

            try {
                if (CierreDiaJobLock::jobActivo()) {
                    echo json_encode(\Core\Model::Responde(false, 'Ya hay un proceso de cierre diario en ejecución, no es posible iniciar otro.'));
                    exit;
                }

                $inicio = CierreDiaService::iniciarProcesoCierre($fechaCierre, $usuario, $perfil, $regenerar);
                if (empty($inicio['success'])) {
                    if (ob_get_level()) {
                        ob_end_clean();
                    }
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inicio);
                    exit;
                }

                ignore_user_abort(true);
                $lanzado = $this->lanzarJobCierreDia($phpBin, $jobScript, $fechaCierre, $usuario, $regenerar);
                if (!$lanzado) {
                    if (ob_get_level()) {
                        ob_end_clean();
                    }
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(\Core\Model::Responde(false, 'No fue posible iniciar el Job de cierre diario. Intente de nuevo.'));
                    exit;
                }

                // El Job toma el candado antes de invocar el SP: si no lo hace, murió al arrancar
                // y hay que decirlo aquí, no dejar que la vista lo deduzca minutos después.
                if (!CierreDiaJobLock::esperarHastaActivo(15)) {
                    if (ob_get_level()) {
                        ob_end_clean();
                    }
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(\Core\Model::Responde(
                        false,
                        'El proceso de cierre no arrancó, el procedimiento no se ejecutó y no hay registro en bitácora. Intente de nuevo; si persiste, avise a sistemas.',
                        null,
                        $this->salidaArranqueJobCierreDia() ?: null
                    ));
                    exit;
                }

                if (ob_get_level()) {
                    ob_end_clean();
                }
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(\Core\Model::Responde(
                    true,
                    'El proceso de cierre diario se ha iniciado. Se enviará un correo con el resumen al finalizar.',
                    isset($inicio['datos']) ? $inicio['datos'] : null
                ));
                exit;
            } finally {
                CierreDiaJobLock::liberarLanzamiento($lanzamiento);
            }
        } catch (\Throwable $e) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(\Core\Model::Responde(false, 'Error al procesar: ' . $e->getMessage(), null, $e->getMessage()));
            exit;
        }
    }

    /**
     * Resuelve la ruta del php.exe / php CLI para lanzar Jobs en segundo plano.
     *
     * PHP_BINARY solo apunta al CLI cuando la petición corre bajo SAPI cli; con mod_php
     * devuelve el binario del servidor (httpd.exe) y con CGI devuelve php-cgi. Lanzar
     * cualquiera de esos deja el Job sin ejecutarse y el cierre sin registro en bitácora.
     *
     * @return string Ruta absoluta o vacía si no se encuentra
     */
    private function resolverBinarioPhpCierreDia()
    {
        $nombre = PHP_OS_FAMILY === 'Windows' ? 'php.exe' : 'php';
        $candidatos = [];

        if (PHP_SAPI === 'cli' && defined('PHP_BINARY') && PHP_BINARY !== '') {
            $candidatos[] = PHP_BINARY;
        }

        // Directorios donde suele convivir el CLI con lo que sí conoce el SAPI actual.
        $pistas = [];
        $iniCargado = php_ini_loaded_file();
        if ($iniCargado !== false && $iniCargado !== '') {
            $pistas[] = dirname($iniCargado);
        }
        if (defined('PHP_BINARY') && PHP_BINARY !== '') {
            $pistas[] = dirname(PHP_BINARY);
        }
        if (defined('PHP_BINDIR') && PHP_BINDIR !== '') {
            $pistas[] = PHP_BINDIR;
        }
        foreach ($pistas as $dir) {
            $candidatos[] = $dir . DIRECTORY_SEPARATOR . $nombre;
            // XAMPP/WAMP: el CLI vive en <raíz>\php y el servidor en <raíz>\apache\bin
            $candidatos[] = dirname($dir) . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . $nombre;
            $candidatos[] = dirname($dir, 2) . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . $nombre;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $candidatos[] = 'C:\\xampp\\php\\php.exe';
        } else {
            $candidatos[] = '/usr/bin/php';
            $candidatos[] = '/usr/local/bin/php';
        }

        foreach (explode(PATH_SEPARATOR, (string) getenv('PATH')) as $dir) {
            $dir = trim($dir);
            if ($dir !== '') {
                $candidatos[] = rtrim($dir, '\\/') . DIRECTORY_SEPARATOR . $nombre;
            }
        }

        foreach ($candidatos as $bin) {
            if ($this->esInterpretePhpCli($bin)) {
                return $bin;
            }
        }

        return '';
    }

    /**
     * Acepta únicamente el ejecutable CLI (php / php.exe): httpd, php-cgi y php-fpm
     * arrancan sin error visible pero no ejecutan el Job.
     *
     * @param string $bin
     * @return bool
     */
    private function esInterpretePhpCli($bin)
    {
        $bin = trim((string) $bin);
        if ($bin === '' || !is_file($bin)) {
            return false;
        }
        $nombre = strtolower(basename($bin));
        if (substr($nombre, -4) === '.exe') {
            $nombre = substr($nombre, 0, -4);
        }

        return $nombre === 'php';
    }

    /**
     * Lanza JobsCredito.php CierreDia en segundo plano.
     *
     * @param string $phpBin
     * @param string $jobScript
     * @param string $fechaCierre Y-m-d
     * @param string $usuario
     * @param int $regenerar
     * @return bool true si el SO aceptó el arranque
     */
    private function lanzarJobCierreDia($phpBin, $jobScript, $fechaCierre, $usuario, $regenerar)
    {
        $regenerarArg = $regenerar ? '1' : '0';
        $salida = CierreDiaJobLock::archivoSalidaJob();
        @unlink($salida);

        $argumentos = escapeshellarg($phpBin) . ' '
            . escapeshellarg($jobScript) . ' '
            . escapeshellarg('CierreDia') . ' '
            . escapeshellarg($fechaCierre) . ' '
            . escapeshellarg($usuario) . ' '
            . escapeshellarg($regenerarArg);

        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'start /B "" ' . $argumentos . ' > ' . escapeshellarg($salida) . ' 2>&1';
            $handle = @popen($cmd, 'r');
            if (!is_resource($handle)) {
                return false;
            }
            pclose($handle);
            return true;
        }

        $cmd = 'nohup ' . $argumentos . ' > ' . escapeshellarg($salida) . ' 2>&1 & echo $!';
        $out = [];
        $code = 1;
        @exec($cmd, $out, $code);
        $pid = isset($out[0]) ? trim((string) $out[0]) : '';
        return $code === 0 && $pid !== '' && ctype_digit($pid);
    }

    /**
     * Últimas líneas que dejó el proceso del Job al morir (fatal error, intérprete inválido, etc.).
     *
     * @return string
     */
    private function salidaArranqueJobCierreDia()
    {
        $archivo = CierreDiaJobLock::archivoSalidaJob();
        if (!is_file($archivo)) {
            return '';
        }
        $contenido = @file_get_contents($archivo, false, null, -2048);

        return is_string($contenido) ? trim($contenido) : '';
    }

    ////////////////////////////////////////////////////////////////////

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
     * POST: fecha. Detalle de incidencias RES_IMPOR (VALIDACION = 2) para la fecha operativa.
     */
    public function ConsultarIncidenciasImportacion()
    {
        try {
            $fecha = isset($_POST['fecha']) ? trim((string) $_POST['fecha']) : '';
            $ctaBancaria = isset($_POST['ctaBancaria']) ? trim((string) $_POST['ctaBancaria']) : '';
            $respuesta = PagosAplicacionService::consultarIncidenciasImportacion(
                $fecha,
                $ctaBancaria !== '' ? $ctaBancaria : null
            );
        } catch (\Throwable $e) {
            $respuesta = [
                'success' => false,
                'mensaje' => 'Error al consultar incidencias.',
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
     * POST: empresa, fecha, tipoCliente, codigo, ciclo, ctaBancaria. Devuelve JSON con resumen y filas (consulta MP, solo lectura).
     */
    public function ConsultarConciliacion()
    {
        try {
            $empresa = 'EMPFIN';
            $tipoCliente = '';
            $fecha = isset($_POST['fecha']) ? trim((string) $_POST['fecha']) : '';
            $codigo = isset($_POST['codigo']) ? trim((string) $_POST['codigo']) : '';
            $ciclo = isset($_POST['ciclo']) ? trim((string) $_POST['ciclo']) : '';
            $ctaBancaria = isset($_POST['ctaBancaria']) ? trim((string) $_POST['ctaBancaria']) : '';
            $modo = isset($_POST['modoConciliado']) ? trim((string) $_POST['modoConciliado']) : 'legacy';
            if (!in_array($modo, ['por_fecha', 'importados', 'cierre_dia'], true)) {
                $modo = 'legacy';
            }
            $respuesta = ConciliacionService::buscarPagosConciliacion($empresa, $fecha, $tipoCliente, $codigo, $ciclo, $ctaBancaria, $modo);
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

    /**
     * Operaciones → Reporte Interés Devengado.
     * Filtros: fecha de corte y situación del crédito (Entregado, Liquidado, Ambos).
     * Vista con tabla y botón de exportar a Excel.
     */
    public function ReporteInteresDevengado()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
                {$this->configuraTabla}
                {$this->descargaExcel}
                {$this->formatoMoneda}

                const idTablaDevengo = "reporteDevengo"
                let datosDevengoCompletos = []
                let filtroActivoDevengo = null

                const obtenerParametrosDevengo = () => {
                    const fechaCorte = $("#fechaCorte").val()
                    const situacion = $("#situacionCredito").val()

                    return { fechaCorte, situacion }
                }

                const consultarDevengo = () => {
                    const params = obtenerParametrosDevengo()
                    if (!params.fechaCorte) {
                        showError("Seleccione la fecha de corte.")
                        return
                    }

                    consultaServidor("/Operaciones/GetReporteInteresDevengado", params, (res) => {
                        if (!res.success) return resultadoErrorDevengo(res.mensaje)
                        resultadoOKDevengo(res.datos || [])
                    })
                }

                const resultadoErrorDevengo = (mensaje) => {
                    $(".resultadoDevengo").toggleClass("conDatos", false)
                    datosDevengoCompletos = []
                    filtroActivoDevengo = null
                    actualizarEstadoBotonesFiltroDevengo()
                    showError(mensaje || "No fue posible obtener el reporte.").then(() => {
                        actualizaDatosTabla(idTablaDevengo, [])
                    })
                }

                const parseFechaInicioDevengo = (fecha) => {
                    if (!fecha) return null
                    const partes = String(fecha).split("/")
                    if (partes.length !== 3) return null
                    const dia = parseInt(partes[0], 10)
                    const mes = parseInt(partes[1], 10)
                    const anio = parseInt(partes[2], 10)
                    if (!dia || !mes || !anio) return null
                    return new Date(anio, mes - 1, dia)
                }

                const formatearDatosDevengo = (datos) => {
                    return (datos || []).map((item) => {
                        const fila = Object.assign({}, item)
                        if (fila.INTERES_TOTAL != null) {
                            fila.INTERES_TOTAL = "$ " + formatoMoneda(fila.INTERES_TOTAL)
                        }
                        if (fila.DEVENGO_DIARIO != null) {
                            fila.DEVENGO_DIARIO = "$ " + formatoMoneda(fila.DEVENGO_DIARIO)
                        }
                        if (fila.DEVENGO_TRANSCURRIDO != null) {
                            fila.DEVENGO_TRANSCURRIDO = "$ " + formatoMoneda(fila.DEVENGO_TRANSCURRIDO)
                        }
                        if (fila.DEVENGO_REGISTRADO != null) {
                            fila.DEVENGO_REGISTRADO = "$ " + formatoMoneda(fila.DEVENGO_REGISTRADO)
                        }
                        if (fila.DEVENGO_DIF != null) {
                            fila.DEVENGO_DIF = "$ " + formatoMoneda(fila.DEVENGO_DIF)
                        }
                        return fila
                    })
                }

                const actualizarEstadoBotonesFiltroDevengo = () => {
                    $("#btnFiltroDifMayor").toggleClass("btn-info active", filtroActivoDevengo === "mayor")
                        .toggleClass("btn-default", filtroActivoDevengo !== "mayor")
                    $("#btnFiltroDifMenor").toggleClass("btn-info active", filtroActivoDevengo === "menor")
                        .toggleClass("btn-default", filtroActivoDevengo !== "menor")
                }

                const aplicarFiltroDevengo = () => {
                    let filas = datosDevengoCompletos

                    if (filtroActivoDevengo === "mayor") {
                        filas = filas.filter((item) => {
                            const diasDif = Number(item.DIAS_DIF)
                            const devengoDif = Number(item.DEVENGO_DIF)
                            return diasDif > 0 && devengoDif > 0
                        })
                    } else if (filtroActivoDevengo === "menor") {
                        const hoy = new Date()
                        hoy.setHours(0, 0, 0, 0)
                        filas = filas.filter((item) => {
                            const diasDif = Number(item.DIAS_DIF)
                            const devengoDif = Number(item.DEVENGO_DIF)
                            const inicio = parseFechaInicioDevengo(item.INICIO)
                            return diasDif < 0 && devengoDif < 0 && inicio !== null && inicio < hoy
                        })
                    }

                    actualizaDatosTabla(idTablaDevengo, formatearDatosDevengo(filas))
                    actualizarEstadoBotonesFiltroDevengo()
                }

                const toggleFiltroDevengo = (tipo) => {
                    if (!datosDevengoCompletos.length) return
                    filtroActivoDevengo = filtroActivoDevengo === tipo ? null : tipo
                    swal({
                        text: "Procesando la solicitud, espere un momento...",
                        icon: "/img/wait.gif",
                        button: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    })
                    setTimeout(() => {
                        aplicarFiltroDevengo()
                        try { swal.close() } catch (e) {}
                    }, 50)
                }

                const resultadoOKDevengo = (datos) => {
                    datosDevengoCompletos = datos || []
                    filtroActivoDevengo = null
                    aplicarFiltroDevengo()
                    $(".resultadoDevengo").toggleClass("conDatos", true)
                }

                const descargarExcelDevengo = () => {
                    const params = $.param(obtenerParametrosDevengo())
                    descargaExcel("/Operaciones/GetReporteInteresDevengado_excel/?" + params)
                }

                $(document).ready(() => {
                    $("#btnConsultarDevengo").click(consultarDevengo)
                    $("#btnExcelDevengo").click(descargarExcelDevengo)
                    $("#btnFiltroDifMayor").click(() => toggleFiltroDevengo("mayor"))
                    $("#btnFiltroDifMenor").click(() => toggleFiltroDevengo("menor"))

                    configuraTabla(idTablaDevengo)
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Reporte Interés Devengado")));
        View::set('footer', $this->_contenedor->footer($extraFooter));

        View::render('operaciones_reporte_interes_devengado');
    }

    /**
     * POST: fechaCorte (YYYY-MM-DD), situacion (E|L|AMBOS).
     * Devuelve JSON con el dataset del reporte de interés devengado.
     */
    public function GetReporteInteresDevengado()
    {
        $this->limpiaSalidaParaJson();

        try {
            $fechaCorte = isset($_POST['fechaCorte']) ? trim((string) $_POST['fechaCorte']) : '';
            $situacion = isset($_POST['situacion']) ? strtoupper(trim((string) $_POST['situacion'])) : '*';

            if ($fechaCorte === '') {
                $resp = \Core\Model::Responde(false, 'Debe capturar la fecha de corte.', null);
            } else {
                $resp = OperacionesDao::GetReporteInteresDevengado([
                    'fechaCorte' => $fechaCorte,
                    'situacion'  => $situacion,
                ]);
            }
        } catch (\Throwable $e) {
            $resp = \Core\Model::Responde(false, 'Error al obtener el reporte: ' . $e->getMessage(), null, $e->getMessage());
        }

        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resp);
        exit;
    }

    /**
     * Exporta a Excel el mismo dataset del reporte de interés devengado.
     * Parámetros por GET: fechaCorte, situacion.
     */
    public function GetReporteInteresDevengado_excel()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $texto = ['estilo' => $estilos['texto_centrado']];
        $mondea = ['estilo' => $estilos['moneda']];
        $fecha = ['estilo' => $estilos['fecha']];

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('CDGNS', 'Crédito', $texto),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'Ciclo', $texto),
            \PHPSpreadsheet::ColumnaExcel('SITUACION', 'Situación', $texto),
            \PHPSpreadsheet::ColumnaExcel('INICIO', 'Fecha inicio', $fecha),
            \PHPSpreadsheet::ColumnaExcel('FIN', 'Fecha fin', $fecha),
            \PHPSpreadsheet::ColumnaExcel('PLAZO_DIAS', 'Plazo (días)'),
            \PHPSpreadsheet::ColumnaExcel('DEVENGO_DIARIO', 'Devengo diario', $mondea),
            \PHPSpreadsheet::ColumnaExcel('INTERES_TOTAL', 'Interés total', $mondea),
            \PHPSpreadsheet::ColumnaExcel('DIAS_TRANSCURRIDOS', 'Días transcurridos'),
            \PHPSpreadsheet::ColumnaExcel('DEVENGO_TRANSCURRIDO', 'Devengo transcurrido', $mondea),
            \PHPSpreadsheet::ColumnaExcel('DIAS_REGISTRADOS', 'Días registrados'),
            \PHPSpreadsheet::ColumnaExcel('DEVENGO_REGISTRADO', 'Devengo registrado', $mondea),
            \PHPSpreadsheet::ColumnaExcel('DIAS_DIF', 'Días diferencia'),
            \PHPSpreadsheet::ColumnaExcel('DEVENGO_DIF', 'Devengo diferencia', $mondea),
            \PHPSpreadsheet::ColumnaExcel('FECHA_LIQUIDACION', 'Fecha liquidación', $fecha),
        ];

        $fechaCorte = isset($_GET['fechaCorte']) ? trim((string) $_GET['fechaCorte']) : '';
        $situacion = isset($_GET['situacion']) ? strtoupper(trim((string) $_GET['situacion'])) : 'AMBOS';

        if ($fechaCorte === '') {
            $fechaCorte = date('Y-m-d');
        }
        if ($situacion === '') {
            $situacion = 'AMBOS';
        }

        $resp = OperacionesDao::GetReporteInteresDevengado([
            'fechaCorte' => $fechaCorte,
            'situacion'  => $situacion,
        ]);

        $filas = ($resp && isset($resp['success']) && $resp['success']) ? ($resp['datos'] ?? []) : [];

        \PHPSpreadsheet::DescargaExcel('Reporte Interes Devengado', 'Reporte', 'Interes Devengado', $columnas, $filas);
    }

    /**
     * Operaciones → Reporte Acreditado.
     * Muestra el histórico del acreditado por crédito (CDGNS) en una sola pantalla:
     * ciclos, plazo, tasa, avales, fechas, días de atraso, monto, garantía y cartera.
     */
    public function ReporteAcreditado()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->configuraTabla}
                {$this->descargaExcel}
                {$this->formatoMoneda}

                const idTablaAcreditado = "reporteAcreditado"
                let hayDatosAcreditado = false

                const obtenerParametrosAcreditado = () => {
                    const credito = ($("#creditoAcreditado").val() || "").trim()
                    return { credito }
                }

                const consultarAcreditado = () => {
                    const params = obtenerParametrosAcreditado()
                    if (!params.credito) {
                        showError("Capture el crédito a consultar.")
                        return
                    }

                    consultaServidor("/Operaciones/GetReporteAcreditado", params, (res) => {
                        if (!res.success) return resultadoErrorAcreditado(res.mensaje)
                        resultadoOKAcreditado(res.datos || [])
                    })
                }

                const descargarExcelAcreditado = () => {
                    const params = obtenerParametrosAcreditado()
                    if (!params.credito) {
                        showError("Capture el crédito a consultar.")
                        return
                    }
                    if (!hayDatosAcreditado) {
                        showError("Primero consulte el reporte para descargarlo.")
                        return
                    }
                    descargaExcel("/Operaciones/GetReporteAcreditado_excel/?" + $.param(params))
                }

                const resultadoErrorAcreditado = (mensaje) => {
                    hayDatosAcreditado = false
                    $(".resultadoAcreditado").toggleClass("conDatos", false)
                    showError(mensaje || "No fue posible obtener el reporte.").then(() => {
                        actualizaDatosTabla(idTablaAcreditado, [])
                    })
                }

                const resultadoOKAcreditado = (datos) => {
                    hayDatosAcreditado = datos.length > 0
                    if (!datos.length) {
                        showInfo("No se encontró información para el crédito capturado.")
                    }

                    datos = datos.map((item) => {
                        if (item.MONTO != null) {
                            item.MONTO = "$ " + formatoMoneda(item.MONTO)
                        }
                        if (item.GARANTIA != null) {
                            item.GARANTIA = "$ " + formatoMoneda(item.GARANTIA)
                        } else {
                            item.GARANTIA = "$ 0.00"
                        }
                        if (item.CARTERA != null) {
                            item.CARTERA = "$ " + formatoMoneda(item.CARTERA)
                        } else {
                            item.CARTERA = "$ 0.00"
                        }
                        if (item.LIQUIDACION == null) item.LIQUIDACION = "-"
                        if (item.AVALES == null) item.AVALES = "-"
                        if (item.DIAS_ATRASO == null) item.DIAS_ATRASO = 0
                        return item
                    })

                    actualizaDatosTabla(idTablaAcreditado, datos)
                    $(".resultadoAcreditado").toggleClass("conDatos", true)
                }

                $(document).ready(() => {
                    $("#btnConsultarAcreditado").click(consultarAcreditado)
                    $("#btnExcelAcreditado").click(descargarExcelAcreditado)
                    $("#creditoAcreditado").on("keypress", (e) => {
                        if (e.which === 13) consultarAcreditado()
                    })

                    configuraTabla(idTablaAcreditado)
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Reporte Acreditado")));
        View::set('footer', $this->_contenedor->footer($extraFooter));

        View::render('operaciones_reporte_acreditado');
    }

    /**
     * POST: credito (CDGNS). Devuelve JSON con el histórico del acreditado.
     */
    public function GetReporteAcreditado()
    {
        $this->limpiaSalidaParaJson();

        try {
            $credito = isset($_POST['credito']) ? trim((string) $_POST['credito']) : '';

            if ($credito === '') {
                $resp = \Core\Model::Responde(false, 'Debe capturar el crédito.', null);
            } else {
                $resp = OperacionesDao::GetReporteAcreditado([
                    'credito' => $credito,
                ]);
            }
        } catch (\Throwable $e) {
            $resp = \Core\Model::Responde(false, 'Error al obtener el reporte: ' . $e->getMessage(), null, $e->getMessage());
        }

        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resp);
        exit;
    }

    /**
     * Exporta a Excel el mismo dataset del reporte de acreditado.
     * Parámetro por GET: credito (CDGNS).
     */
    public function GetReporteAcreditado_excel()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $texto = ['estilo' => $estilos['texto_centrado']];
        $moneda = ['estilo' => $estilos['moneda']];

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('CLIENTE', 'Cliente'),
            \PHPSpreadsheet::ColumnaExcel('CREDITO', 'Crédito', $texto),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'Ciclo', $texto),
            \PHPSpreadsheet::ColumnaExcel('PLAZO', 'Plazo', $texto),
            \PHPSpreadsheet::ColumnaExcel('TASA', 'Tasa', $texto),
            \PHPSpreadsheet::ColumnaExcel('AVALES', 'Avales'),
            \PHPSpreadsheet::ColumnaExcel('INICIO', 'Inicio', $texto),
            \PHPSpreadsheet::ColumnaExcel('FIN', 'Fin', $texto),
            \PHPSpreadsheet::ColumnaExcel('LIQUIDACION', 'Liquidación', $texto),
            \PHPSpreadsheet::ColumnaExcel('DIAS_ATRASO', 'Días atraso', $texto),
            \PHPSpreadsheet::ColumnaExcel('MONTO', 'Monto', $moneda),
            \PHPSpreadsheet::ColumnaExcel('GARANTIA', 'Garantía', $moneda),
            \PHPSpreadsheet::ColumnaExcel('CARTERA', 'Cartera', $moneda),
        ];

        $credito = isset($_GET['credito']) ? trim((string) $_GET['credito']) : '';

        $resp = OperacionesDao::GetReporteAcreditado(['credito' => $credito]);
        $filas = ($resp && isset($resp['success']) && $resp['success']) ? ($resp['datos'] ?? []) : [];

        $sufijo = $credito !== '' ? ' ' . $credito : '';
        \PHPSpreadsheet::DescargaExcel('Reporte Acreditado' . $sufijo, 'Reporte', 'Acreditado' . $sufijo, $columnas, $filas);
    }
}
