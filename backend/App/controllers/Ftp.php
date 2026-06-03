<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\services\FtpExplorerService;

class Ftp extends Controller
{
    private $_contenedor;

    /**
     * Directorios raíz del explorador de archivos.
     * Cada entrada: id, etiqueta (nombre visible), ruta (absoluta) y usuarios (perfiles o códigos de usuario).
     */
    private static $directoriosRaiz = [
        [
            'id'       => 'reportes',
            'etiqueta' => 'Reportes',
            'ruta'     => 'C:/reportes',
            'usuarios' => ['ADMIN', 'AMGM', 'FECR', 'ORHM'],
        ],
    ];

    public static function getDirectoriosRaiz()
    {
        return self::$directoriosRaiz;
    }

    public function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function Explorer()
    {
        $raices = FtpExplorerService::obtenerRaicesParaUsuario(
            self::getDirectoriosRaiz(),
            $this->__usuario,
            $this->__perfil
        );

        if (empty($raices)) {
            header('Location: /Principal/');
            exit;
        }

        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}

                const raices = {$this->jsonRaices($raices)};
                const idTablaFtp = "ftp-tabla";
                let raizActual = null;
                let rutaActual = "";
                let tablaFtp = null;
                let itemsTablaFtp = [];

                const formateaTamano = (bytes) => {
                    if (bytes === null || bytes === undefined) return "—";
                    const n = Number(bytes);
                    if (isNaN(n) || n < 0) return "—";
                    if (n < 1024) return n + " B";
                    if (n < 1048576) return (n / 1024).toFixed(1) + " KB";
                    if (n < 1073741824) return (n / 1048576).toFixed(1) + " MB";
                    return (n / 1073741824).toFixed(2) + " GB";
                };

                const formateaFecha = (iso) => {
                    if (!iso) return "—";
                    const d = new Date(iso);
                    return isNaN(d.getTime()) ? iso : d.toLocaleString("es-MX");
                };

                const nombreSinExtension = (nombre) => {
                    if (!nombre) return "—";
                    const idx = nombre.lastIndexOf(".");
                    if (idx <= 0) return nombre;
                    return nombre.slice(0, idx);
                };

                const actualizarBreadcrumb = () => {
                    const contenedor = $("#ftp-breadcrumb");
                    contenedor.empty();

                    if (!raizActual) {
                        contenedor.hide();
                        return;
                    }

                    contenedor.show();

                    if (!rutaActual) {
                        contenedor.append('<li class="active">' + raizActual.etiqueta + '</li>');
                        return;
                    }

                    contenedor.append(
                        '<li><a href="#" data-nivel="raiz">' + raizActual.etiqueta + '</a></li>'
                    );

                    const partes = rutaActual.split("/").filter(Boolean);
                    let acumulado = "";
                    partes.forEach((parte, idx) => {
                        acumulado += (acumulado ? "/" : "") + parte;
                        const esUltimo = idx === partes.length - 1;
                        if (esUltimo) {
                            contenedor.append('<li class="active">' + parte + '</li>');
                        } else {
                            contenedor.append(
                                '<li><a href="#" data-ruta="' + acumulado + '">' + parte + '</a></li>'
                            );
                        }
                    });
                };

                const mostrarRaices = () => {
                    raizActual = null;
                    rutaActual = "";
                    limpiarTabla();
                    $("#ftp-toolbar").hide();
                    $("#ftp-tabla-wrap").hide();
                    $("#ftp-raices").show();

                    const lista = $("#ftp-lista-raices");
                    lista.empty();

                    raices.forEach((raiz) => {
                        lista.append(
                            '<div class="col-md-6 col-sm-6 ftp-raiz-card">' +
                                '<a href="#" class="ftp-raiz-link" data-id="' + raiz.id + '">' +
                                    '<i class="fa fa-folder-open fa-3x"></i>' +
                                    '<span>' + raiz.etiqueta + '</span>' +
                                '</a>' +
                            '</div>'
                        );
                    });

                    actualizarBreadcrumb();
                };

                const initTablaFtp = () => {
                    if ($.fn.DataTable.isDataTable("#" + idTablaFtp)) {
                        $("#" + idTablaFtp).DataTable().clear().destroy();
                    }

                    tablaFtp = $("#" + idTablaFtp).DataTable({
                        autoWidth: false,
                        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
                        order: [[1, "asc"]],
                        language: {
                            emptyTable: "No hay datos disponibles",
                            paginate: {
                                previous: "Anterior",
                                next: "Siguiente"
                            },
                            info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                            infoEmpty: "Sin registros para mostrar",
                            zeroRecords: "No se encontraron registros",
                            lengthMenu: "Mostrar _MENU_ registros por página",
                            search: "Buscar:"
                        },
                        columnDefs: [
                            { targets: 0, orderable: false, searchable: false },
                            { targets: [2, 3, 4], orderable: true }
                        ],
                        createdRow: (row, data, dataIndex) => {
                            $(row).find("td").css("vertical-align", "middle");
                            const item = itemsTablaFtp[dataIndex];
                            if (item && item.tipo === "carpeta") {
                                $(row).addClass("ftp-fila-carpeta").attr("data-ruta", item.ruta);
                            }
                        }
                    });
                };

                const limpiarTabla = () => {
                    itemsTablaFtp = [];
                    if (tablaFtp) {
                        tablaFtp.clear().draw();
                    } else {
                        $("#" + idTablaFtp + " tbody").empty();
                    }
                    $("#ftp-check-all").prop("checked", false);
                };

                const renderContenido = (datos) => {
                    itemsTablaFtp = [];
                    const filas = [];

                    (datos.directorios || []).forEach((dir) => {
                        itemsTablaFtp.push({ tipo: "carpeta", ruta: dir.ruta });
                        filas.push([
                            "",
                            dir.nombre,
                            "Carpeta",
                            "—",
                            "—"
                        ]);
                    });

                    (datos.archivos || []).forEach((archivo) => {
                        itemsTablaFtp.push({ tipo: "archivo", ruta: archivo.ruta });
                        filas.push([
                            '<input type="checkbox" class="ftp-check" value="' + archivo.ruta + '">',
                            nombreSinExtension(archivo.nombre),
                            archivo.extension || "—",
                            formateaTamano(archivo.tamano),
                            formateaFecha(archivo.modificado)
                        ]);
                    });

                    if (!tablaFtp) {
                        initTablaFtp();
                    }

                    tablaFtp.clear();
                    if (filas.length) {
                        tablaFtp.rows.add(filas).draw();
                    } else {
                        tablaFtp.draw();
                    }

                    $("#ftp-check-all").prop("checked", false);
                };

                const cargarContenido = () => {
                    if (!raizActual) return;

                    limpiarTabla();

                    consultaServidor("/Ftp/Listar/", {
                        raiz: raizActual.id,
                        ruta: rutaActual
                    }, (res) => {
                        if (!res.success) {
                            limpiarTabla();
                            showError(res.mensaje || "No se pudo listar el contenido.");
                            if (!rutaActual) {
                                mostrarRaices();
                            }
                            return;
                        }
                        renderContenido(res.datos || {});
                        actualizarBreadcrumb();
                    });
                };

                const abrirRaiz = (id) => {
                    raizActual = raices.find((r) => r.id === id) || null;
                    if (!raizActual) return;

                    rutaActual = "";
                    limpiarTabla();
                    $("#ftp-raices").hide();
                    $("#ftp-toolbar").show();
                    $("#ftp-tabla-wrap").show();
                    cargarContenido();
                };

                const abrirCarpeta = (ruta) => {
                    rutaActual = ruta;
                    cargarContenido();
                };

                const obtenerSeleccionados = () => {
                    return $(".ftp-check:checked").map(function () {
                        return $(this).val();
                    }).get();
                };

                const descargarSeleccionados = async () => {
                    const archivos = obtenerSeleccionados();
                    if (archivos.length === 0) return showWarning("Seleccione al menos un archivo para descargar.");
                    if (!raizActual) return showWarning("No se encontró la ubicación seleccionada.");

                    const plural = archivos.length === 1 ? "archivo" : "archivos";
                    const confirmar = await confirmarMovimiento(
                        "Confirmar descarga",
                        "Se descargarán " + archivos.length + " " + plural + " de \"" + raizActual.etiqueta + "\"."
                    );
                    if (!confirmar) return;

                    const form = $("<form>", {
                        method: "POST",
                        action: "/Ftp/Descargar/"
                    });

                    form.append($("<input>", { type: "hidden", name: "raiz", value: raizActual.id }));
                    archivos.forEach((ruta) => {
                        form.append($("<input>", { type: "hidden", name: "archivos[]", value: ruta }));
                    });

                    $("body").append(form);
                    form.submit();
                    form.remove();
                };

                $(document).ready(function () {
                    initTablaFtp();
                    mostrarRaices();

                    $(document).on("click", ".ftp-raiz-link", function (e) {
                        e.preventDefault();
                        abrirRaiz($(this).data("id"));
                    });

                    $(document).on("click", ".ftp-fila-carpeta", function () {
                        abrirCarpeta($(this).data("ruta"));
                    });

                    $(document).on("click", "#ftp-breadcrumb a", function (e) {
                        e.preventDefault();
                        const ruta = $(this).data("ruta");
                        if ($(this).data("nivel") === "raiz") {
                            rutaActual = "";
                            cargarContenido();
                        } else {
                            abrirCarpeta(ruta);
                        }
                    });

                    $("#ftp-btn-volver").on("click", function () {
                        if (rutaActual) {
                            const partes = rutaActual.split("/").filter(Boolean);
                            partes.pop();
                            rutaActual = partes.join("/");
                            cargarContenido();
                        } else {
                            mostrarRaices();
                        }
                    });

                    $("#ftp-check-all").on("change", function () {
                        $("#" + idTablaFtp).find(".ftp-check").prop("checked", $(this).is(":checked"));
                    });

                    $("#ftp-btn-descargar").on("click", descargarSeleccionados);
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->GetExtraHeader('Explorador de archivos')));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('ftp_explorer');
    }

    public function Listar()
    {
        header('Content-Type: application/json; charset=UTF-8');

        $raizId = isset($_POST['raiz']) ? trim((string) $_POST['raiz']) : '';
        $ruta = isset($_POST['ruta']) ? trim((string) $_POST['ruta']) : '';

        echo json_encode(FtpExplorerService::listarContenido(
            self::getDirectoriosRaiz(),
            $raizId,
            $ruta,
            $this->__usuario,
            $this->__perfil
        ));
    }

    public function Descargar()
    {
        $raizId = isset($_POST['raiz']) ? trim((string) $_POST['raiz']) : '';
        $archivos = isset($_POST['archivos']) && is_array($_POST['archivos']) ? $_POST['archivos'] : [];

        FtpExplorerService::descargarArchivos(
            self::getDirectoriosRaiz(),
            $raizId,
            $archivos,
            $this->__usuario,
            $this->__perfil
        );
    }

    private function jsonRaices(array $raices)
    {
        return json_encode(array_map(function ($raiz) {
            return [
                'id'       => $raiz['id'],
                'etiqueta' => $raiz['etiqueta'],
            ];
        }, $raices), JSON_UNESCAPED_UNICODE);
    }
}
