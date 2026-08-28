<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\MasterDom;
use \App\controllers\Contenedor;
use \Core\Controller;
use \App\models\Creditos as CreditosDao;
use \App\services\ReasignacionService;

class Creditos extends Controller
{

    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function getUsuario()
    {
        return $this->__usuario;
    }


    public function ControlGarantias()
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

                const idTabla = "garantias"
                let credito = ""

                const consultaGarantias = (cr = null) => {
                    credito = cr || $("#creditoBuscar").val()
                    if (credito === "") return inputError("creditoBuscar", "Debe ingresar un número de crédito.")

                    consultaServidor("/Creditos/ConsultaGarantias/", { credito }, (resultado) => {
                        if (!resultado.success) return resultadoError(resultado.mensaje)
                        resultadoOK(resultado.datos)
                    })
                }

                const buscarEnter = (e) => {
                    if (e.key === "Enter") consultaGarantias()
                }

                const inputError = (id, mensaje) => {
                    $("#" + id).toggleClass("incorrecto", true)
                    $("#" + id).focus()
                    resultadoError(mensaje)
                }

                const resultadoError = (mensaje) => {
                    $(".resultado").toggleClass("conDatos", false)
                    showError(mensaje).then(() => actualizaDatosTabla(idTabla, []))
                }

                const resultadoOK = (datos) => {
                    datos = datos.map((dato) => {
                        dato.MONTO = formatoMoneda(dato.MONTO)
                        const botones = $("<div>")
                            .append($("<button>").addClass("btn btn-success btn-circle").attr("onclick", "mostrarModal(" + dato.SECUENCIA + ", 'edit')").append($("<i>").addClass("glyphicon glyphicon-edit")))
                            .append($("<button>").addClass("btn btn-danger btn-circle").attr("onclick", "mostrarModal(" + dato.SECUENCIA + ", 'delete')").append($("<i>").addClass("glyphicon glyphicon-trash")))
                            .css("display", "flex")
                            .css("justify-content", "space-around")
                            .css("width", "100%")
                            .prop("outerHTML")

                        return [
                            dato.SECUENCIA,
                            dato.FECHA,
                            dato.ARTICULO,
                            dato.MARCA,
                            dato.MODELO,
                            dato.NO_SERIE,
                            dato.MONTO,
                            dato.FACTURA,
                            botones
                        ]
                    })

                    actualizaDatosTabla(idTabla, datos)
                    $(".resultado").toggleClass("conDatos", true)
                }

                const validaCampos = (edicion = false) => {
                    if ($("#articulo").val() == "") return showWarning("Ingrese el nombre del articulo")
                    if ($("#marca").val() == "") return showWarning("Ingrese el nombre de la marca")
                    if ($("#modelo").val() == "") return showWarning("Ingrese el modelo")
                    if ($("#serie").val() == "") return showWarning("Ingrese el número de serie")
                    if ($("#valor").val() == "" || $("#valor").val() < 1) return showWarning("Ingrese el valor del artículo")
                    if ($("#factura").val() == "") return showWarning("Ingrese la serie de la factura")
                
                    return true
                }

                const addGarantia = () => {
                    if (validaCampos() !== true) return

                    consultaServidor("/Creditos/InsertGarantia/", getParametros(), (resultado) => {
                        if (!resultado.success) return showError(resultado.mensaje)
                        $("#articuloGarantia").modal("hide")
                        showSuccess("Registro guardado exitosamente")
                        .then(() => consultaGarantias(credito))
                        
                    })
                }

                const updateGarantia = () => {
                    if (validaCampos(true) !== true) return

                    confirmarMovimiento("¿Seguro desea actualizar el registro seleccionado?")
                    .then((continuar) => {
                        if (!continuar) return
                        consultaServidor("/Creditos/UpdateGarantia/", getParametros(), (resultado) => {
                            if (!resultado.success) return showError(resultado.mensaje)
                            $("#articuloGarantia").modal("hide")
                            showSuccess("Registro guardado exitosamente")
                            .then(() => consultaGarantias(credito))
                        })
                    })
                }

                const deleteGarantia = () => {
                    confirmarMovimiento("¿Seguro desea eliminar el registro seleccionado?")
                    .then((continuar) => {
                        if (!continuar) return
                            consultaServidor("/Creditos/DeleteGarantia/", getParametros(), (resultado) => {
                                if (!resultado.success) return showError(resultado.mensaje)
                                $("#articuloGarantia").modal("hide")
                                showSuccess("Registro eliminado exitosamente")
                                .then(() => consultaGarantias(credito))
                            })
                    })
                }

                const getParametros = () => {
                    const modal = $("#articuloGarantia")

                    return {
                        usuario: "{$this->__usuario}",
                        credito: modal.find("#credito").val(),
                        secuencia: modal.find("#secuencia").val(),
                        articulo: modal.find("#articulo").val(),
                        marca: modal.find("#marca").val(),
                        modelo: modal.find("#modelo").val(),
                        serie: modal.find("#serie").val(),
                        valor: modal.find("#valor").val(),
                        factura: modal.find("#factura").val()
                    }
                }

                const mostrarBotonModal = (btn) => {
                    const botones = $("#articuloGarantia").find(".modal-footer").find("button")
                    botones.each((index, boton) => {
                        if (boton.className.includes("btn-secondary")) return
                        if (boton.id === btn) $(boton).css("display", "inline")
                        else $(boton).css("display", "none")
                    })
                }

                const mostrarModal = (secuencia = null, tipo = null) => {
                    const modal = $("#articuloGarantia")
                    let informacion = []
                    
                    modal.find("#credito").val(credito)
                    modal.find("#secuencia").val(secuencia)

                    if (secuencia) {
                        modal.find("#secuencia").parent().css("display", "block")
                        informacion = buscarEnTabla(idTabla, 0, secuencia)
                        informacion = informacion.length ? informacion[0] : []
                    } else {
                        modal.find("#secuencia").parent().css("display", "none")
                    }

                    modal.find("#articulo").val(informacion[2] || "")
                    modal.find("#marca").val(informacion[3] || "")
                    modal.find("#modelo").val(informacion[4] || "")
                    modal.find("#serie").val(informacion[5] || "")
                    modal.find("#valor").val(parseaNumero(informacion[6] || ""))
                    modal.find("#factura").val(informacion[7] || "")

                    if (tipo === null) mostrarBotonModal("agregarGarantia")
                    if (tipo === "edit") mostrarBotonModal("editarGarantia")
                    if (tipo === "delete") mostrarBotonModal("eliminarGarantia")

                    modal.modal("show")
                }

                const mayusculas = (elemento) => {
                    elemento.value = elemento.value.toUpperCase()
                }

                const getExcel = () => {
                    descargaExcel("/Creditos/generarExcel/?" + $.param({ credito }))
                }

                $(document).ready(() => {
                    $("#buscar").click(() => consultaGarantias())
                    $("#creditoBuscar").on("keypress", buscarEnter)
                    $("#agregar").click(() => mostrarModal())
                    $("#excel").click(getExcel)
                    $("#agregarGarantia").click(addGarantia)
                    $("#editarGarantia").click(updateGarantia)
                    $("#eliminarGarantia").click(deleteGarantia)

                    configuraTabla(idTabla)
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Control de Garantías")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('controlgarantias');
    }

    public function ConsultaGarantias()
    {
        echo json_encode(CreditosDao::ConsultaGarantias($_POST));
    }


    public function InsertGarantia()
    {
        echo json_encode(CreditosDao::ProcedureGarantias($_POST));
    }

    public function UpdateGarantia()
    {
        echo json_encode(CreditosDao::ProcedureGarantiasUpdate($_POST));
    }

    public function DeleteGarantia()
    {
        echo json_encode(CreditosDao::ProcedureGarantiasDelete($_POST));
    }

    public function ActualizaCredito()
    {
        $extraHeader = <<<html
        <title>Actualizar Crédito</title>
        <link rel="shortcut icon" href="/img/logo.svg" type="image/x-icon">
html;
        $extraFooter = <<<html
      <script>
    
        //////////////////////////////////////
        function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
        //////////////////////////////////////
        function enviar_edit_credito(){
            
             credito = document.getElementById("credito_nuevo").value;
             
            if(credito != '')
            {
                 $.ajax({
                 type: 'POST',
                 url: '/Creditos/UpdateCredito/',
                 data: $('#Add').serialize(),
                 success: function(respuesta) {
                 if(respuesta != '0')
                 {
                     if(respuesta == '1 Proceso realizado exitosamente')
                         {
                             swal("Registro guardado correctamente", {
                                      icon: "success",
                             });
                             location.reload();
                         }
                     else
                         {
                             swal(respuesta, {
                                    icon: "error",
                                });
                             document.getElementById("credito_nuevo").value = "";
                             $('#modal_editar_numero_credito').modal('hide');
                         }
                     
                 }
                 else {
                       swal(respuesta, {
                                    icon: "error",
                                });
                 }
                    }
                 });
            }
            else
            {
                alert("Ingresa el número de credito nuevo");
            }
        }
        function enviar_edit_ciclo(){
            
             ciclo = document.getElementById("ciclo_c_n").value;
             
            if(credito != '')
            {
                 $.ajax({
                 type: 'POST',
                 url: '/Creditos/UpdateCicloCredito/',
                 data: $('#AddCiclo').serialize(),
                 success: function(respuesta) {
                 if(respuesta != '0')
                 {
                     if(respuesta == '1 Proceso realizado exitosamente')
                         {
                            swal("Registro guardado correctamente", {
                                      icon: "success",
                             });
                             location.reload();
                         }
                     else
                         {
                            swal(respuesta, {
                                    icon: "error",
                                });
                             document.getElementById("credito_nuevo").value = "";
                             $('#modal_actualizar_ciclo').modal('hide');
                         }
                     
                 }
                 else {
                       swal(respuesta, {
                                    icon: "error",
                                });
                 }
                    }
                 });
            }
            else
            {
                swal("Ingrese el número del nuevo ciclo", {
                                      icon: "warning",
                             });
            }
        }
        function enviar_edit_situacion(){
            
            
                 $.ajax({
                 type: 'POST',
                 url: '/Creditos/UpdateSituacion/',
                 data: $('#AddSituacion').serialize(),
                 success: function(respuesta) {
                 if(respuesta != '0')
                 {
                     if(respuesta == '1 Proceso realizado exitosamente')
                         {
                             swal("Registro guardado correctamente", {
                                      icon: "success",
                             });
                             location.reload();
                         }
                     else
                         {
                             swal(respuesta, {
                                    icon: "error",
                                });
                             document.getElementById("credito_nuevo").value = "";
                             $('#modal_actualizar_ciclo').modal('hide');
                         }
                     
                 }
                 else {
                       swal(respuesta, {
                                    icon: "error",
                                });
                 }
                    }
                 });
        }
        
      </script>
html;

        $credito = $_GET['Credito'];

        if ($credito != '') {
            $tabla = '';
            $AdministracionOne = CreditosDao::ConsultarPagosAdministracionOne($credito);

            if ($AdministracionOne['NO_CREDITO'] != '') {

                /////////////////////////7
                $ComboSucursal = '';
                if ($AdministracionOne['SITUACION'] == 'E') {
                    $ComboSucursal .= <<<html
                    <option selected value="E">ENTREGADO</option>
                    <option value="L">LIQUIDADO</option>
html;
                } else {
                    $ComboSucursal .= <<<html
                     <option value="E">ENTREGADO</option>
                     <option selected value="L">LIQUIDADO</option>
html;
                }

                ////////////////////////
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('tabla', $tabla);
                View::set('credito', $credito);
                View::set('combo', $ComboSucursal);
                View::set('Administracion', $AdministracionOne);
                View::render("actualizacredito_busqueda_all");
            } else {
                View::set('header', $this->_contenedor->header($extraHeader));
                View::set('footer', $this->_contenedor->footer($extraFooter));
                View::set('credito', $credito);
                View::render("actualizacredito_busqueda_message");
            }
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("actualizacredito_all");
        }
    }

    public function Reasignacion()
    {
        $credito = trim((string) ($_GET['Credito'] ?? ''));
        $registros = [];
        $mensajeError = (string) ($_SESSION['reasignacion_error'] ?? '');
        unset($_SESSION['reasignacion_error']);

        $cambios = isset($_SESSION['reasignacion_cambios']) && is_array($_SESSION['reasignacion_cambios'])
            ? $_SESSION['reasignacion_cambios']
            : [];
        unset($_SESSION['reasignacion_cambios']);

        $resultadoMasivo = isset($_SESSION['reasignacion_resultado']) && is_array($_SESSION['reasignacion_resultado'])
            ? $_SESSION['reasignacion_resultado']
            : null;
        unset($_SESSION['reasignacion_resultado']);

        if ($credito !== '') {
            $consulta = CreditosDao::SelectCreditoReasignacion($credito);
            if (is_array($consulta)) {
                foreach ($consulta as $fila) {
                    if (!is_array($fila) || trim((string) ($fila['CLIENTE'] ?? '')) === '') {
                        continue;
                    }
                    $registros[] = $this->marcarReasignacionAplicada($fila, $cambios);
                }
            }
            if ($registros === []) {
                $mensajeError = 'No se encontró información para el crédito solicitado.';
            }
        }

        $extraHeader = '<title>Reasignación</title><link rel="shortcut icon" href="/img/logo.svg" type="image/x-icon">';
        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer());
        View::set('credito', $credito);
        View::set('registros', $registros);
        View::set('asesores', CreditosDao::ListaAsesoresReasignacion());
        View::set('sucursales', CreditosDao::ListaSucursalesReasignacion());
        View::set('mensaje_error', $mensajeError);
        View::set('resultado_masivo', $resultadoMasivo);
        View::render('reasignacion');
    }

    public function FoliosTarjeta()
    {
        $credito = trim((string) ($_GET['Credito'] ?? ''));

        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}
            </script>
HTML;

        $extraHeader = '<title>Folios de Tarjeta</title>'
            . '<link rel="shortcut icon" href="/img/logo.svg" type="image/x-icon">'
            . '<link href="/css/folios-tarjeta.css" rel="stylesheet">';
        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('credito', $credito);
        View::render('folios_tarjeta');
    }

    public function ConsultaFoliosTarjeta()
    {
        header('Content-Type: application/json; charset=utf-8');
        $credito = trim((string) ($_POST['credito'] ?? ''));
        echo json_encode(CreditosDao::ConsultaFoliosTarjeta($credito));
    }

    public function RegistrarFolioTarjeta()
    {
        header('Content-Type: application/json; charset=utf-8');
        $datos = [
            'credito' => $_POST['credito'] ?? '',
            'ciclo' => $_POST['ciclo'] ?? '',
            'folio' => $_POST['folio'] ?? '',
            'motivo' => $_POST['motivo'] ?? '',
            'tipo_mov' => $_POST['tipo_mov'] ?? '',
            'id_reemplazo' => $_POST['id_reemplazo'] ?? '',
            'usuario' => $this->__usuario
        ];
        echo json_encode(CreditosDao::RegistrarFolioTarjeta($datos));
    }

    public function UpdateReasignacion()
    {
        header('Content-Type: application/json; charset=utf-8');
        $credito = trim((string) MasterDom::getDataAll('credito'));
        $ciclo = trim((string) MasterDom::getDataAll('ciclo'));
        $asesor = trim((string) MasterDom::getDataAll('asesor'));
        $sucursal = trim((string) MasterDom::getDataAll('sucursal'));

        if ($credito === '' || $ciclo === '') {
            echo json_encode(['estatus' => 'ERROR', 'resultado' => 'Crédito y ciclo son obligatorios.']);
            return;
        }
        if ($asesor === '' && $sucursal === '') {
            echo json_encode(['estatus' => 'ERROR', 'resultado' => 'Debe seleccionar un asesor, una sucursal o ambos.']);
            return;
        }

        $antes = CreditosDao::SelectCreditoReasignacion($credito, $ciclo);
        $resultado = CreditosDao::EjecutarReasignacion(
            $credito,
            $ciclo,
            $asesor,
            $sucursal,
            (string) $this->__usuario
        );

        $estatus = strtoupper(trim((string) ($resultado['ESTATUS'] ?? 'ERROR')));
        if ($estatus === 'OK') {
            if (!isset($_SESSION['reasignacion_cambios']) || !is_array($_SESSION['reasignacion_cambios'])) {
                $_SESSION['reasignacion_cambios'] = [];
            }
            $_SESSION['reasignacion_cambios'][$this->claveReasignacion($credito, $ciclo)] = [
                'asesor' => is_array($antes) ? trim((string) ($antes['EJECUTIVO'] ?? '')) : '',
                'sucursal' => is_array($antes) ? trim((string) ($antes['SUCURSAL'] ?? '')) : '',
            ];
        }

        echo json_encode([
            'estatus' => $estatus,
            'resultado' => trim((string) ($resultado['RESULTADO'] ?? 'No fue posible realizar la reasignación.')),
            'redirect' => '/Creditos/Reasignacion/?Credito=' . rawurlencode($credito),
        ]);
    }

    private function claveReasignacion(string $credito, string $ciclo): string
    {
        return trim($credito) . '|' . trim($ciclo);
    }

    /**
     * Las columnas "nuevo" se derivan del estado actual del crédito: la sesión
     * solo guarda el valor previo para poder contrastarlo tras la reasignación.
     */
    private function marcarReasignacionAplicada(array $fila, array $cambios): array
    {
        $asesorActual = trim((string) ($fila['EJECUTIVO'] ?? ''));
        $sucursalActual = trim((string) ($fila['SUCURSAL'] ?? ''));

        $fila['ASESOR_ANTERIOR'] = $asesorActual;
        $fila['ASESOR_NUEVO'] = '';
        $fila['SUCURSAL_ANTERIOR'] = $sucursalActual;
        $fila['SUCURSAL_NUEVA'] = '';

        $clave = $this->claveReasignacion(
            (string) ($fila['NO_CREDITO'] ?? ''),
            (string) ($fila['CICLO'] ?? '')
        );
        if (!isset($cambios[$clave]) || !is_array($cambios[$clave])) {
            return $fila;
        }

        $asesorPrevio = trim((string) ($cambios[$clave]['asesor'] ?? ''));
        $sucursalPrevia = trim((string) ($cambios[$clave]['sucursal'] ?? ''));

        if ($asesorPrevio !== '' && $asesorPrevio !== $asesorActual) {
            $fila['ASESOR_ANTERIOR'] = $asesorPrevio;
            $fila['ASESOR_NUEVO'] = $asesorActual;
        }
        if ($sucursalPrevia !== '' && $sucursalPrevia !== $sucursalActual) {
            $fila['SUCURSAL_ANTERIOR'] = $sucursalPrevia;
            $fila['SUCURSAL_NUEVA'] = $sucursalActual;
        }

        return $fila;
    }

    public function ReasignacionLayout()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $texto = ['estilo' => $estilos['texto_centrado']];
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('GRUPO', '[GRUPO]', $texto),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'CICLO', $texto),
            \PHPSpreadsheet::ColumnaExcel('ASESOR', '[ASESOR]', $texto),
            \PHPSpreadsheet::ColumnaExcel('EMPRESA', '[EMPRESA]', $texto),
            \PHPSpreadsheet::ColumnaExcel('NOM_SUCURSAL', '[NOM_SUCURSAL]'),
        ];
        \PHPSpreadsheet::DescargaExcel(
            'layout_reasignacion',
            'Reasignacion',
            'Reasignación',
            $columnas,
            []
        );
    }

    public function ReasignacionCargaMasiva()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: /Creditos/Reasignacion/');
            exit;
        }
        if (!isset($_FILES['archivo']) || !is_uploaded_file($_FILES['archivo']['tmp_name'])) {
            $this->redirigirReasignacionConError('No se recibió el archivo Excel.');
        }
        if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            $this->redirigirReasignacionConError(
                'Error al subir el archivo (código ' . (int) $_FILES['archivo']['error'] . ').'
            );
        }

        $extension = strtolower((string) pathinfo((string) $_FILES['archivo']['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['xlsx', 'xls'], true)) {
            $this->redirigirReasignacionConError('El archivo debe ser Excel (.xls o .xlsx).');
        }

        $directorio = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'tmp';
        if (!is_dir($directorio)) {
            @mkdir($directorio, 0755, true);
        }
        $destino = $directorio . DIRECTORY_SEPARATOR . 'reasignacion_' . uniqid('', true) . '.' . $extension;
        $guardado = @move_uploaded_file($_FILES['archivo']['tmp_name'], $destino);
        if (!$guardado) {
            $guardado = @copy($_FILES['archivo']['tmp_name'], $destino);
        }
        if (!$guardado || !is_readable($destino)) {
            $destinoAlterno = sys_get_temp_dir()
                . DIRECTORY_SEPARATOR
                . 'reasignacion_' . uniqid('', true) . '.' . $extension;
            $guardado = @copy($_FILES['archivo']['tmp_name'], $destinoAlterno);
            if ($guardado && is_readable($destinoAlterno)) {
                $destino = $destinoAlterno;
            } else {
                @unlink($destino);
                @unlink($destinoAlterno);
                $this->redirigirReasignacionConError(
                    'No se pudo guardar el archivo recibido para procesarlo.'
                );
            }
        }

        try {
            $resultado = ReasignacionService::cargaMasivaDesdeArchivo(
                $destino,
                (string) $this->__usuario
            );
        } catch (\Throwable $e) {
            $resultado = [
                'success' => false,
                'mensaje' => 'No fue posible procesar el archivo.',
                'error' => $e->getMessage(),
            ];
        } finally {
            @unlink($destino);
        }

        $datos = $resultado['datos'] ?? [];
        $_SESSION['reasignacion_resultado'] = [
            'resumen' => $resultado['mensaje'] ?? '',
            'detalle_error' => $resultado['error'] ?? '',
            'actualizados' => $datos['actualizados'] ?? [],
            'errores' => $datos['errores'] ?? [],
            'procesados' => (int) ($datos['procesados'] ?? 0),
            'omitidos' => (int) ($datos['omitidos'] ?? 0),
            'exito' => !empty($resultado['success']),
        ];
        header('Location: /Creditos/Reasignacion/');
        exit;
    }

    public function ReasignacionResultadoMasivo()
    {
        header('Location: /Creditos/Reasignacion/');
        exit;
    }

    private function redirigirReasignacionConError(string $mensaje): void
    {
        $_SESSION['reasignacion_error'] = $mensaje;
        header('Location: /Creditos/Reasignacion/');
        exit;
    }
    ////////////////////////////////////////////////////
    public function UpdateCredito()
    {
        $up_credito = new \stdClass();

        $credito = MasterDom::getDataAll('credito');
        $up_credito->_credito = $credito;

        $credito_nuevo = MasterDom::getDataAll('credito_nuevo');
        $up_credito->_credito_nuevo = $credito_nuevo;

        $id = CreditosDao::UpdateActulizaCredito($up_credito);

        if ($id >= 1) {
        } else {
            return '0';
        }
    }
    public function UpdateCicloCredito()
    {
        $up_credito = new \stdClass();

        $credito = MasterDom::getDataAll('credito_c');
        $up_credito->_credito = $credito;

        $ciclo_nuevo = MasterDom::getDataAll('ciclo_c_n');
        $up_credito->_ciclo_nuevo = $ciclo_nuevo;

        $id = CreditosDao::UpdateActulizaCiclo($up_credito);

        return $id;
    }
    public function UpdateSituacion()
    {
        $up_credito = new \stdClass();

        $credito = MasterDom::getDataAll('credito_s');
        $up_credito->_credito = $credito;

        $ciclo = MasterDom::getDataAll('ciclo_s');
        $up_credito->_ciclo_nuevo = $ciclo;

        $situacion = MasterDom::getDataAll('situacion_s');
        $up_credito->_situacion = $situacion;

        $id = CreditosDao::UpdateActulizaSituacion($up_credito);

        if ($id >= 1) {
            //var_dump($id['VMENSAJE']);
            return $id['VMENSAJE'];
        } else {
            return '0';
        }
    }

    public function generarExcel()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('SECUENCIA', 'Secuencia', ['estilo' => $estilos['centrado']]),
            \PHPSpreadsheet::ColumnaExcel('FECHA', 'Fecha', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('ARTICULO', 'Articulo'),
            \PHPSpreadsheet::ColumnaExcel('MARCA', 'Marca'),
            \PHPSpreadsheet::ColumnaExcel('MODELO', 'Modelo'),
            \PHPSpreadsheet::ColumnaExcel('NO_SERIE', 'Numero de Serie'),
            \PHPSpreadsheet::ColumnaExcel('MONTO', 'Monto', ['estilo' => $estilos['moneda']]),
            \PHPSpreadsheet::ColumnaExcel('FACTURA', 'Factura'),
        ];

        $filas = CreditosDao::ConsultaGarantias($_GET);
        $filas = $filas['success'] ? $filas['datos'] : [];

        \PHPSpreadsheet::DescargaExcel('Layout Garantías Creditos', 'Reporte', 'Garantías', $columnas, $filas);
    }

    ////////////////////////////////////////////////////

    public function cierreDiario()
    {
        $extraFooter = <<<HTML
        <script>
            {$this->mensajes}
            {$this->descargaExcel}

            const descarga = () => {
                const fecha = document.getElementById('fecha').value
                if (!fecha) return showError("Ingrese una fecha a buscar.")
                
                descargaExcel("/Creditos/excelCierreDiario/?" + $.param({ fecha }))
            }
        </script>
        HTML;

        $ahora = new \DateTime();
        $cierre = new \DateTime('16:00:00');

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Cierres Operativos")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('fecha', $ahora > $cierre ? date('Y-m-d') : date('Y-m-d', strtotime('-1 day')));
        View::render('cierre_diario');
    }

    public function GetCierreDiario($f = null)
    {
        $fecha = $_POST['fecha'] ?? $f;
        $datos = CreditosDao::GetCierreDiario($fecha);
        $datos = $datos['success'] ? $datos['datos'] : [];

        $tabla = "";
        foreach ($datos as $key => $dato) {
            $tabla .= "<tr>";
            foreach ($dato as $key2 => $campo) {
                $tabla .= "<td style='vertical-align: middle;'>{$campo}</td>";
            }
            $tabla .= "</tr>";
        }

        if (!$_SERVER['REQUEST_METHOD'] === 'POST') return $tabla;

        echo json_encode([
            "success" => count($datos) > 0,
            "datos" => $tabla,
            "mensaje" => count($datos) > 0 ? "" : "No se encontraron registros."
        ]);
    }

    public function excelCierreDiario()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $centrado = ['estilo' => $estilos['centrado']];

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('SUCURSAL', 'SUCURSAL'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_ASESOR', 'NOMBRE ASESOR'),
            \PHPSpreadsheet::ColumnaExcel('CODIGO_GRUPO', 'CODIGO GRUPO', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CODIGO_CLIENTE', 'CODIGO CLIENTE', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CURP_CLIENTE', 'CURP CLIENTE', $centrado),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_COMPLETO_CLIENTE', 'NOMBRE CLIENTE'),
            \PHPSpreadsheet::ColumnaExcel('CODIGO_AVAL', 'CODIGO AVAL', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CURP_AVAL', 'CURP AVAL', $centrado),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_COMPLETO_AVAL', 'NOMBRE AVAL'),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'CICLO', $centrado),
            \PHPSpreadsheet::ColumnaExcel('FECHA_INICIO', 'FECHA INICIO', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('SALDO_TOTAL', 'SALDO TOTAL', ['estilo' => $estilos['moneda']]),
            \PHPSpreadsheet::ColumnaExcel('MORA_TOTAL', 'MORA TOTAL', ['estilo' => $estilos['moneda']]),
            \PHPSpreadsheet::ColumnaExcel('DIAS_MORA', 'DIAS MORA', $centrado),
            \PHPSpreadsheet::ColumnaExcel('DIAS_ATRASO', 'DIAS ATRASO', $centrado),
            \PHPSpreadsheet::ColumnaExcel('TIPO_CARTERA', 'TIPO CARTERA', $centrado)
        ];

        $fecha = $_GET['fecha'];
        $filas = CreditosDao::GetCierreDiario($fecha);
        $filas = $filas['success'] ? $filas['datos'] : [];

        \PHPSpreadsheet::DescargaExcel('Situación Cartera MCM', 'Reporte', 'Situación Cartera MCM', $columnas, $filas);
    }

    public function AdminCorreos()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->configuraTabla}
                {$this->confirmarMovimiento}

                $(document).on("ready", () => {
                    $("#addCorreo").on("click", () => $("#modalCorreo").modal("show"))
                    $("#addGrupo").on("click", () => $("#modalGrupo").modal("show"))


                    $("#areaFiltro").on("change", getCorreos)
                    $("#sucursalFiltro").on("change", getCorreos)
                    $("#btnAgregar").on("click", addCorreoGrupo)
                    $("#btnQuitar").on("click", eliminarCorreoGrupo)
                    $("#nombre").on("change", validaCampos)
                    $("#correo").on("keyup", sugerenciasCorreo)
                    $("#correo").on("blur", () => {
                        const correo = $("#correo").val()
                        $("#sugerenciasCorreo").remove()
                        $("#correo").attr("list", "")
                        if (!correo) return showError("Debe ingresar un correo electrónico.")
                        if (!validaCorreo(correo)) return showError("El correo electrónico ingresado no es válido.")
                    })
                    $("#area").on("change", validaCampos)
                    $("#sucursal").on("change", validaCampos)
                    $("#guardarDireccion").on("click", addCorreo)
                    $("#guardarGrupo").on("click", addGrupo)
                    $("#buscarGrupo").on("keyup", buscarGrupos)

                    getCorreos()
                    getCorreoGrupo()
                    configuraTabla("tblCorreos", {noRegXvista: false})
                    configuraTabla("tblGrupo", {noRegXvista: false})
                    
                    $(".dataTables_filter").css("width", "100%")
                    $(".dataTables_filter").css("width", "100%")
                })

                const getCorreos = () => {
                    const parametros = {}

                    if ($("#areaFiltro").val() !== "*") parametros.area = $("#areaFiltro").val()
                    if ($("#sucursalFiltro").val() !== "*") parametros.sucursal = $("#sucursalFiltro").val()

                    consultaServidor("/Creditos/GetCorreos", parametros, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al buscar los correos.")
                        if (respuesta.datos.length === 0) return showError("No se encontraron correos registrados.").then(() => actualizaDatosTabla("tblCorreos", null))

                        const correos = respuesta.datos.map((correo) => {
                            const checador = "<input type='checkbox' name='correo' value='" + correo.ID + "'onchange='compruebaCorreoGrupo(event)'>"

                            return [
                                checador,
                                correo.NOMBRE,
                                correo.CORREO,
                                correo.AREA,
                                correo.SUCURSAL
                            ]

                            $("#tblCorreos tbody").append(fila)
                        })

                        actualizaDatosTabla("tblCorreos", correos)
                    })
                }

                const getCorreoGrupo = () => {
                    const grupo = $("#idGrupoSeleccionado").val() 

                    consultaServidor("/Creditos/GetCorreosGrupo", { grupo }, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al buscar los grupos.")
                        
                        const grupos = respuesta.datos.map((grupo) => {
                            return [
                                grupo.EDITABLE == 1 ? "<input type='checkbox' name='grupo' value='" + grupo.ID_CORREO + "'>" : "",
                                grupo.CORREO
                            ]
                        })

                        actualizaDatosTabla("tblGrupo", grupos)
                    })
                }

                const addCorreoGrupo = () => {
                    const correosNuevos = []
                    $("#tblCorreos tbody input[type='checkbox']:checked").each((index, element) => {
                        if ($("#tblGrupo tbody input[type='checkbox'][value='" + $(element).val() + "']").length === 0)
                            correosNuevos.push($(element).val())
                        else {
                            element.checked = false
                            return showError("El correo " + $(element).val() + " ya está agregado al grupo seleccionado.")
                        }
                    })

                    if (correosNuevos.length === 0) return showError("Seleccione al menos un correo para agregar al grupo.")
                    
                    const grupo = $("#idGrupoSeleccionado").val()
                    if (!grupo) return showError("Selecciones un grupo para agregar los correos.")

                    consultaServidor("/Creditos/AgregaCorreoGrupo", { grupo, correos: correosNuevos, usuario: "{$_SESSION['usuario']}" }, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al agregar los correos al grupo.")
                        
                        correosNuevos.forEach((correo) => {
                            $("#tblCorreos tbody input[type='checkbox'][value='" + correo + "']").prop("checked", false)
                        })
                        actualizaListaGrupos($("#grupoSeleccionado").val())
                        showSuccess("Correos agregados al grupo correctamente.")
                    })
                }

                const eliminarCorreoGrupo = () => {
                    const correos = []
                    $("#tblGrupo tbody input[type='checkbox']:checked").each((index, element) => {
                        correos.push($(element).val())
                    })

                    if (correos.length === 0) return showError("Seleccione al menos un correo para quitar del grupo.")
                    
                    const grupo = $("#idGrupoSeleccionado").val()
                    if (!grupo) return showError("Selecciones un grupo para quitar los correos.")

                    consultaServidor("/Creditos/EliminaCorreoGrupo", { grupo, correos }, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al quitar los correos del grupo.")
                        
                        correos.forEach((correo) => {
                            $("#tblGrupo tbody input[type='checkbox'][value='" + correo + "']").prop("checked", false)
                        })
                        actualizaListaGrupos($("#grupoSeleccionado").val())
                        showSuccess("Correos quitados del grupo correctamente.")
                    })
                }

                const compruebaCorreoGrupo = (e) => {
                    if (!e.target.checked) return
                    e.target.checked = false

                    if ($("#idGrupoSeleccionado").val() === "") return showError("Debe seleccionar un grupo para agregar correos.")
                    if ($("#tblGrupo tbody input[type='checkbox'][value='" + e.target.value + "']").length > 0)
                        return showError("El correo seleccionado ya está agregado al grupo " + $("#grupoFiltro option:selected").text() + ".")

                    e.target.checked = true
                }

                const validaCampos = (e) => {
                    $("#guardarDireccion").prop("disabled", (!$("#nombre").val() || !$("#correo").val() || !$("#area").val() || !$("#sucursal").val()))
                }

                const addCorreo = () => {
                    if (!$("#nombre").val()) return showError("Ingrese el nombre del usuario.")
                    if (!$("#correo").val()) return showError("Ingrese el correo electrónico.")
                    if (!$("#area").val()) return showError("Seleccione un área.")
                    if (!$("#sucursal").val()) return showError("Seleccione una sucursal.")

                    const registro = {
                        nombre: $("#nombre").val(),
                        correo: $("#correo").val(),
                        area: $("#area").val(),
                        sucursal: $("#sucursal").val(),
                        usuario: "{$_SESSION['usuario']}"
                    }

                    consultaServidor("/Creditos/AgregaCorreo", registro, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al registrar el correo.")
                        
                        showSuccess("Correo registrado correctamente.")
                        getCorreos()
                    })

                    $("#nombre").val("")
                    $("#correo").val("")
                    $("#empresa").val("")
                    $("#sucursal").val("")
                    $("#guardarDireccion").prop("disabled", true)

                    $("#modalCorreo").modal("hide")
                }

                const validaCorreo = (correo) => {
                    const regexCorreo = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/
                    return (!correo || !regexCorreo.test(correo)) ? false : true
                }

                const addGrupo = () => {
                    const grupo = $("#nombreGrupo").val().trim()
                    if (!grupo) return showError("Ingrese un nombre para el nuevo grupo.")

                    const coincidencias = $("#grupoFiltro li .nombreGrupo").filter((index, element) => element.innerText.trim().toLowerCase() === grupo.toLowerCase())
                    if (coincidencias.length > 0) return showError("El grupo " + grupo + " ya existe.")

                    consultaServidor("/Creditos/AgregaGrupo", { grupo, usuario: "{$_SESSION['usuario']}" }, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al registrar el grupo.")
                        
                        showSuccess("Grupo registrado correctamente.")
                        actualizaListaGrupos(grupo)
                    })

                    $("#nombreGrupo").val("")
                    $("#modalGrupo").modal("hide")
                }

                const actualizaListaGrupos = (grupo = null) => {
                    consultaServidor("/Creditos/GetParametros", null, (respuesta) => {
                        if (!respuesta.success) return showError("Ocurrio un error al buscar los grupos.")
                        
                        $("#grupoFiltro").empty()
                        $("#grupoFiltro").append(respuesta.datos.grupo)
                        if (grupo) $("#grupoFiltro li .nombreGrupo").filter((index, element) => element.innerText === grupo).click()
                        else {
                            if ($("#grupoSeleccionado").text() === grupo) {
                                $("#grupoSeleccionado").text("Seleccionar grupo")
                                $("#idGrupoSeleccionado").val("")
                            }
                            getCorreoGrupo()
                        }
                    })
                }

                const sugerenciasCorreo = () => {
                    if ($("#correo").val().indexOf("@") !== -1) {
                        const empresas = ["masconmenos.com.mx", "financieracultiva.com"]

                        const correo = $("#correo").val().split("@")[0]
                        const lista = empresas.map((empresa) => {
                            return "<option value='" + correo + "@" + empresa + "'>" + correo + "@" + empresa + "</option>"
                        })

                        const datalist = $("<datalist id='sugerenciasCorreo'>" + lista.join("") + "</datalist>")
                        $("#correo").after(datalist).attr("list", "sugerenciasCorreo")                    
                    } else {
                        $("#sugerenciasCorreo").remove()
                        $("#correo").attr("list", "")
                    }
                    validaCampos()
                }

                const seleccionGrupo = (id, grupo) => {
                    $("#idGrupoSeleccionado").val(id)
                    $("#grupoSeleccionado").text(grupo)
                    getCorreoGrupo()
                }

                const eliminarGrupo = (id, grupo) => {
                    confirmarMovimiento("Administración de correos", "¿Esta seguro de eliminar el grupo " + grupo + "?")
                    .then((continuar) => {
                        if (!continuar) return
                        consultaServidor("/Creditos/EliminaGrupo", { grupo: id }, (respuesta) => {
                            if (!respuesta.success) return showError("Ocurrio un error al eliminar el grupo.")
                            
                            showSuccess("Grupo eliminado correctamente.")
                            actualizaListaGrupos()
                        })
                    })
                }

                const buscarGrupos = () => {
                    $("#sinResultados").hide()
                    const buscar = $("#buscarGrupo").val().toLowerCase()

                    const encontrados = $("#grupoFiltro li").filter((index, element) => {
                        const elemento = $(element).find(".nombreGrupo")
                        const textoOriginal = elemento.text()
                        const textoMinuscula = textoOriginal.toLowerCase()
                        const indexMatch = textoMinuscula.indexOf(buscar)
                        
                        if (indexMatch !== -1) {
                            const parteAntes = textoOriginal.substring(0, indexMatch)
                            const parteCoincidente = textoOriginal.substring(indexMatch, indexMatch + buscar.length)
                            const parteDespues = textoOriginal.substring(indexMatch + buscar.length)

                            if (buscar === "") elemento.text(textoOriginal)
                            else elemento.html(parteAntes + "<mark>" + parteCoincidente + "</mark>" + parteDespues)

                            $(element).show()
                            return true
                        } else {
                            elemento.text(textoOriginal)
                            $(element).hide()
                            return false
                        }
                    })

                    if (encontrados.length === 0) $("#sinResultados").show()
                }

            </script>
        HTML;

        $prm = $this->GetParametros(true);
        $prm = $prm['datos'];

        View::set('header', $this->_contenedor->header(self::GetExtraHeader("Administración de correos")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('opcArea', $prm['area']);
        View::set('opcSucursal', $prm['sucursal']);
        View::set('opcGrupo', $prm['grupo']);
        View::set('opcSucursales', $prm['sucursales']);
        View::render('creditos_adminCorreos');
    }

    public function GetParametros($ret = false)
    {
        $parametros = CreditosDao::GetParametrosCorreos();

        $opcArea = "<option value='*'>Todas</option>";
        $opcSucursal = "<option value='*'>Todas</option>";
        $opcGrupo = "";
        $opcSucursales = "<option value=''>Seleccione una sucursal</option>";

        if ($parametros['success']) {
            foreach ($parametros['datos'] as $parametro) {
                if ($parametro['TIPO'] === 'AREA') $opcArea .= "<option value='{$parametro['VALOR']}'>{$parametro['MOSTRAR']}</option>";
                if ($parametro['TIPO'] === 'SUCURSAL') $opcSucursal .= "<option value='{$parametro['VALOR']}'>{$parametro['MOSTRAR']}</option>";

                if ($parametro['TIPO'] === 'GRUPO') {
                    $boton = '';

                    if ($parametro['USUARIOS'] == 0) $boton = "<button type='button' class='btn btn-danger btn-sm' onclick='eliminarGrupo(\"{$parametro['VALOR']}\", \"{$parametro['MOSTRAR']}\")' style='grid-column: 2;'>
                        <span class='glyphicon glyphicon-trash'></span>
                    </button>";

                    $opcGrupo .= "<li class='dropdown-item d-flex justify-content-between align-items-center'>
                        <div style='display: grid; grid-template-columns: 1fr auto .3fr; width: 100%; gap: 20px; align-items: center; padding: 5px;'>
                            <span style='color: black; grid-column: 1; cursor: pointer;' class='nombreGrupo' onclick='seleccionGrupo(\"{$parametro['VALOR']}\", \"{$parametro['MOSTRAR']}\")'>{$parametro['MOSTRAR']}</span> 
                            $boton
                            <span style='grid-column: 3; text-align: right;'>{$parametro['USUARIOS']}&nbsp;<span class='glyphicon glyphicon-user'></span></span>
                        </div>
                    </li>";
                }
                if ($parametro['TIPO'] === 'SUCURSALES') $opcSucursales .= "<option value='{$parametro['VALOR']}'>{$parametro['MOSTRAR']}</option>";
            }
        }

        $res = [
            'success' => $parametros['success'],
            'mensaje' => $parametros['mensaje'],
            'datos' => [
                'area' => $opcArea,
                'sucursal' => $opcSucursal,
                'grupo' => $opcGrupo,
                'sucursales' => $opcSucursales
            ]
        ];

        if (!$ret) echo json_encode($res);
        else return $res;
    }

    public function GetCorreos()
    {
        echo json_encode(CreditosDao::GetCorreos($_POST));
    }

    public function GetCorreosGrupo()
    {
        if (count($_POST) === 1 && isset($_POST['grupo']) && $_POST['grupo'] !== '')
            echo json_encode(CreditosDao::GetCorreosGrupo($_POST));
        else echo json_encode(["success" => true, "datos" => []]);
    }

    public function AgregaCorreoGrupo()
    {
        echo json_encode(CreditosDao::AgregaCorreoGrupo($_POST));
    }

    public function EliminaCorreoGrupo()
    {
        echo json_encode(CreditosDao::EliminaCorreoGrupo($_POST));
    }

    public function AgregaCorreo()
    {
        echo json_encode(CreditosDao::AgregaCorreo($_POST));
    }

    public function AgregaGrupo()
    {
        echo json_encode(CreditosDao::AgregaGrupo($_POST));
    }

    public function EliminaGrupo()
    {
        echo json_encode(CreditosDao::EliminaGrupo($_POST));
    }
}
