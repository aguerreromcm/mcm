<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\services\ListaNegraClientesService;

/**
 * Consultas de clientes (menú Clientes).
 */
class Clientes extends Controller
{
    private $_contenedor;

    public function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    /**
     * Vista: consulta de registros en CL_MARCA por número de cliente o CURP.
     */
    public function ConsultaListaNegra()
    {
        $extraCss = <<<HTML
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="/css/consulta-lista-negra.css">
        HTML;

        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}

                const secciones = [
                    {
                        titulo: "Cliente",
                        campos: [
                            { key: "CDGCL", label: "No. cliente", mono: true },
                            { key: "NOMBRE_CLIENTE", label: "Nombre", full: true },
                            { key: "CURP", label: "CURP", mono: true, full: true },
                            { key: "CREDITOS_ACTIVOS_FMT", label: "Grupos asignados", mono: true, full: true }
                        ]
                    },
                    {
                        titulo: "Lista negra",
                        campos: [
                            { key: "ESTATUS_FMT", label: "Estatus" },
                            { key: "MONTOMAX_FMT", label: "Monto máximo" },
                            { key: "CAUSA_FMT", label: "Causa de alta", full: true },
                            { key: "CAUSABAJA_FMT", label: "Causa de baja", full: true }
                        ]
                    },
                    {
                        titulo: "Crédito / NS",
                        campos: [
                            { key: "CDGCLNS", label: "No. crédito / NS", mono: true },
                            { key: "CICLO", label: "Ciclo" },
                            { key: "NOMBRE_CREDITO", label: "Nombre del grupo", full: true },
                            { key: "CLNS_FMT", label: "Tipo crédito" }
                        ]
                    },
                    {
                        titulo: "Historial del registro",
                        campos: [
                            { key: "ALTA_FMT", label: "Fecha de alta" },
                            { key: "USUARIO_ALTA_FMT", label: "Registró" },
                            { key: "BAJA_FMT", label: "Fecha de baja" },
                            { key: "USUARIO_BAJA_FMT", label: "Dio de baja" },
                            { key: "FREGISTRO_FMT", label: "Captura en sistema", mono: true, full: true }
                        ]
                    }
                ];

                const esc = (texto) => {
                    if (texto === null || texto === undefined) return "";
                    return String(texto)
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;");
                };

                const tieneValor = (valor) => {
                    if (valor === null || valor === undefined) return false;
                    return String(valor).trim() !== "";
                };

                const iniciales = (nombre) => {
                    const partes = String(nombre || "").trim().split(/\s+/).filter(Boolean);
                    if (partes.length === 0) return "?";
                    if (partes.length === 1) return partes[0].substring(0, 2).toUpperCase();
                    return (partes[0][0] + partes[partes.length - 1][0]).toUpperCase();
                };

                const etiquetaEstatus = (registro) => {
                    const val = String(registro.ESTATUS || "").trim().toUpperCase();
                    if (val === "A") {
                        return '<span class="ln-badge ln-badge-activo">Bloqueado</span>';
                    }
                    if (val === "B") {
                        return '<span class="ln-badge ln-badge-baja">Sin bloqueo</span>';
                    }
                    return '<span class="ln-badge ln-badge-baja">' + esc(registro.ESTATUS_FMT || val || "Sin estatus") + '</span>';
                };

                const contarActivos = (datos) => {
                    if (!Array.isArray(datos)) return 0;
                    return datos.filter((r) => String(r.ESTATUS || "").trim().toUpperCase() === "A").length;
                };

                const ariaEtiquetaRegistro = (nombre, indice, total) => {
                    let texto = "Registro de " + nombre;
                    if (total > 1) texto += ", " + indice + " de " + total;
                    texto += ". Clic para ver el detalle.";
                    return texto;
                };

                const crearDato = (campo, registro) => {
                    const valor = registro[campo.key];
                    if (!tieneValor(valor)) return "";

                    const clases = ["ln-dato-valor"];
                    if (campo.mono) clases.push("mono");

                    const datoClases = ["ln-dato"];
                    if (campo.full) datoClases.push("ln-dato--full");

                    return (
                        '<div class="' + datoClases.join(" ") + '">' +
                            '<span class="ln-dato-label">' + esc(campo.label) + '</span>' +
                            '<span class="' + clases.join(" ") + '">' + esc(valor) + '</span>' +
                        '</div>'
                    );
                };

                const crearSeccion = (seccion, registro, fila, columna, colspan) => {
                    const datos = seccion.campos.map((campo) => crearDato(campo, registro)).join("");
                    if (!datos) return "";

                    let gridStyle = "";
                    if (fila && columna) {
                        const col = colspan ? columna + " / span " + colspan : String(columna);
                        gridStyle = ' style="grid-row:' + fila + ";grid-column:" + col + ';"';
                    }

                    return (
                        '<section class="ln-seccion"' + gridStyle + '>' +
                            '<h5 class="ln-seccion-titulo">' + esc(seccion.titulo) + '</h5>' +
                            '<div class="ln-datos">' + datos + '</div>' +
                        '</section>'
                    );
                };

                const seccionTieneDatos = (seccion, registro) => {
                    return seccion.campos.some((campo) => tieneValor(registro[campo.key]));
                };

                const crearPaneles = (registro) => {
                    const creditoTieneDatos = seccionTieneDatos(secciones[2], registro);
                    let partes;

                    if (creditoTieneDatos) {
                        partes = [
                            crearSeccion(secciones[0], registro, 1, 1),
                            crearSeccion(secciones[2], registro, 1, 2),
                            crearSeccion(secciones[1], registro, 2, 1),
                            crearSeccion(secciones[3], registro, 2, 2)
                        ];
                    } else {
                        partes = [
                            crearSeccion(secciones[0], registro, 1, 1),
                            crearSeccion(secciones[1], registro, 1, 2),
                            crearSeccion(secciones[3], registro, 2, 1, 2)
                        ];
                    }

                    return '<div class="ln-registro-paneles">' + partes.join("") + '</div>';
                };

                const esSoloCliente = (registro) => registro.SOLO_CLIENTE === true;

                const esResultadoSoloCliente = (datos) => {
                    return Array.isArray(datos) && datos.length > 0 && datos.every((r) => esSoloCliente(r));
                };

                const seccionSoloCliente = {
                    titulo: "Cliente",
                    campos: [
                        { key: "CDGCL", label: "No. cliente", mono: true },
                        { key: "NOMBRE_CLIENTE", label: "Nombre", full: true },
                        { key: "CURP", label: "CURP", mono: true, full: true }
                    ]
                };

                const crearRegistroSoloCliente = (registro) => {
                    const nombre = registro.NOMBRE_CLIENTE || "Cliente";
                    const cuerpo = (
                        '<div class="ln-registro-paneles ln-registro-paneles--solo-cliente">' +
                            crearSeccion(seccionSoloCliente, registro, 1, 1, 2) +
                        '</div>'
                    );

                    return (
                        '<article class="ln-registro ln-registro--solo-cliente estatus-baja">' +
                            '<button type="button" class="ln-registro-cabecera" aria-expanded="false" aria-label="' + esc("Cliente " + nombre + ". Clic para ver el detalle.") + '">' +
                                '<span class="ln-registro-avatar" aria-hidden="true">' + esc(iniciales(nombre)) + '</span>' +
                                '<span class="ln-registro-identidad">' +
                                    '<span class="ln-registro-nombre">' + esc(nombre) + '</span>' +
                                    '<span class="ln-registro-hint">Clic para ver detalle</span>' +
                                '</span>' +
                                '<span class="ln-registro-etiquetas">' +
                                    '<span class="ln-badge ln-badge-baja">Sin registro en lista negra</span>' +
                                '</span>' +
                                '<span class="ln-registro-toggle" aria-hidden="true">' +
                                    '<i class="glyphicon glyphicon-chevron-down"></i>' +
                                '</span>' +
                            '</button>' +
                            '<div class="ln-registro-cuerpo" aria-hidden="true">' + cuerpo + '</div>' +
                        '</article>'
                    );
                };

                const crearRegistro = (registro, indice, total) => {
                    if (esSoloCliente(registro)) {
                        return crearRegistroSoloCliente(registro);
                    }

                    const estatus = String(registro.ESTATUS || "").trim().toUpperCase();
                    const nombre = [registro.NOMBRE_CLIENTE, registro.NOMBRE_CREDITO]
                        .find((val) => tieneValor(val)) || "Registro sin nombre";
                    const cuerpo = crearPaneles(registro);
                    const metaPartes = [];
                    if (total > 1) {
                        metaPartes.push(
                            '<span class="ln-meta-item">' +
                                '<span class="ln-meta-label">Registro</span>' +
                                '<span class="ln-meta-valor">' + indice + ' de ' + total + '</span>' +
                            '</span>'
                        );
                    }
                    if (tieneValor(registro.CDGCL)) {
                        metaPartes.push(
                            '<span class="ln-meta-item">' +
                                '<span class="ln-meta-label">Cliente</span>' +
                                '<span class="ln-meta-valor mono">' + esc(registro.CDGCL) + '</span>' +
                            '</span>'
                        );
                    }
                    if (tieneValor(registro.CURP)) {
                        metaPartes.push(
                            '<span class="ln-meta-item">' +
                                '<span class="ln-meta-label">CURP</span>' +
                                '<span class="ln-meta-valor mono">' + esc(registro.CURP) + '</span>' +
                            '</span>'
                        );
                    }
                    if (estatus === "A" && tieneValor(registro.CAUSA_FMT)) {
                        metaPartes.push(
                            '<span class="ln-meta-item ln-meta-item--causa">' +
                                '<span class="ln-meta-label">Causa</span>' +
                                '<span class="ln-meta-valor">' + esc(registro.CAUSA_FMT) + '</span>' +
                            '</span>'
                        );
                    }

                    const metaHtml = metaPartes.length
                        ? '<span class="ln-registro-meta">' + metaPartes.join("") + '</span>'
                        : "";

                    return (
                        '<article class="ln-registro ' + (estatus === "B" ? "estatus-baja" : "estatus-activo") + '">' +
                            '<button type="button" class="ln-registro-cabecera" aria-expanded="false" aria-label="' + esc(ariaEtiquetaRegistro(nombre, indice, total)) + '">' +
                                '<span class="ln-registro-avatar" aria-hidden="true">' + esc(iniciales(nombre)) + '</span>' +
                                '<span class="ln-registro-identidad">' +
                                    '<span class="ln-registro-nombre">' + esc(nombre) + '</span>' +
                                    metaHtml +
                                    '<span class="ln-registro-hint">Clic para ver detalle</span>' +
                                '</span>' +
                                '<span class="ln-registro-etiquetas">' +
                                    etiquetaEstatus(registro) +
                                '</span>' +
                                '<span class="ln-registro-toggle" aria-hidden="true">' +
                                    '<i class="glyphicon glyphicon-chevron-down"></i>' +
                                '</span>' +
                            '</button>' +
                            '<div class="ln-registro-cuerpo" aria-hidden="true">' + cuerpo + '</div>' +
                        '</article>'
                    );
                };

                const alternarRegistro = (btn) => {
                    const card = btn.closest(".ln-registro");
                    const cuerpo = card.find(".ln-registro-cuerpo");
                    const expandido = card.hasClass("ln-registro--expandido");

                    card.toggleClass("ln-registro--expandido", !expandido);
                    btn.attr("aria-expanded", !expandido);
                    cuerpo.attr("aria-hidden", expandido);
                };

                const ocultarBanner = () => {
                    $("#ln-banner").removeClass("visible ln-banner--resultados ln-banner--vacio").attr("hidden", true);
                    $("#ln-banner-badge").attr("hidden", true);
                };

                const mostrarBanner = (tipo, texto, conteo) => {
                    const icono = tipo === "resultados"
                        ? '<i class="fa fa-check-circle"></i>'
                        : '<i class="fa fa-exclamation-triangle"></i>';

                    $("#ln-banner")
                        .removeClass("ln-banner--resultados ln-banner--vacio")
                        .addClass("visible ln-banner--" + tipo)
                        .removeAttr("hidden");
                    $("#ln-banner-icon").html(icono);
                    $("#ln-banner-texto").text(texto);

                    if (tipo === "resultados" && conteo > 0) {
                        $("#ln-banner-badge")
                            .text(conteo + (conteo === 1 ? " registro" : " registros"))
                            .removeAttr("hidden");
                    } else {
                        $("#ln-banner-badge").attr("hidden", true);
                    }
                };

                const mostrarEstado = (estado) => {
                    $("#ln-estado-inicial").toggleClass("visible", estado === "inicial");
                    $("#ln-estado-vacio").toggleClass("visible", estado === "vacio");
                    $("#ln-resultados").toggle(estado === "datos");
                    $("#ln-resultados-ayuda").prop("hidden", estado !== "datos");
                    if (estado === "inicial") {
                        ocultarBanner();
                        $("#ln-estado-vacio").removeClass("visible");
                    }
                };

                const mostrarEstadoVacio = (mensaje) => {
                    $("#ln-estado-vacio-texto").text(mensaje || "No se encontraron coincidencias para los criterios indicados.");
                    mostrarEstado("vacio");
                };

                const setLoading = (loading) => {
                    const btn = $("#btn_buscar");
                    const inputs = $("#cdgcl, #curp");
                    if (loading) {
                        btn.prop("disabled", true);
                        inputs.prop("disabled", true);
                        btn.find(".ln-btn-content").html('<span class="ln-spinner"></span> Buscando...');
                    } else {
                        btn.prop("disabled", false);
                        inputs.prop("disabled", false);
                        btn.find(".ln-btn-content").html('<i class="glyphicon glyphicon-search"></i> Buscar');
                    }
                };

                const mensajeBannerResultados = (datos, mensaje) => {
                    if (esResultadoSoloCliente(datos)) {
                        return mensaje || "El cliente existe en el sistema pero no tiene registros en lista negra.";
                    }

                    const total = datos.length;
                    const activos = contarActivos(datos);
                    if (total === 1) {
                        return activos === 1
                            ? "Se encontró un registro bloqueado."
                            : "Se encontró un registro en lista negra (sin bloqueo activo).";
                    }
                    if (activos > 0) {
                        return "Se encontraron " + total + " registros; " + activos + " bloqueado(s).";
                    }
                    return "Se encontraron " + total + " registros en lista negra (ninguno con marca activa).";
                };

                const pintarResultados = (datos, mensaje) => {
                    const contenedor = $("#ln-resultados");
                    contenedor.empty();

                    if (!Array.isArray(datos) || datos.length === 0) {
                        mostrarEstadoVacio(mensaje || "No se encontraron coincidencias para los criterios indicados.");
                        mostrarBanner("vacio", mensaje || "No se encontraron registros en lista negra para los criterios indicados.");
                        return;
                    }

                    contenedor.html(datos.map((registro, idx) => crearRegistro(registro, idx + 1, datos.length)).join(""));
                    const tipoBanner = esResultadoSoloCliente(datos) ? "vacio" : "resultados";
                    mostrarBanner(tipoBanner, mensajeBannerResultados(datos, mensaje), datos.length);
                    mostrarEstado("datos");
                    $("#ln-resultados-ayuda").prop("hidden", false);
                    $("#cdgcl").val("");
                    $("#curp").val("");
                };

                const manejarErrorBusqueda = (mensaje) => {
                    $("#ln-resultados").empty();
                    ocultarBanner();
                    mostrarEstado("inicial");
                    showWarning(mensaje || "No se pudo realizar la consulta.");
                };

                const buscar = () => {
                    const cdgcl = $("#cdgcl").val().trim();
                    const curp = $("#curp").val().trim().toUpperCase();

                    if (cdgcl === "" && curp === "") {
                        showWarning("Ingrese número de cliente o CURP.");
                        $("#cdgcl").focus();
                        return;
                    }

                    setLoading(true);
                    ocultarBanner();
                    $("#ln-estado-inicial").removeClass("visible");
                    $("#ln-estado-vacio").removeClass("visible");
                    $("#ln-resultados-ayuda").prop("hidden", true);
                    $("#ln-resultados").empty();

                    $.ajax({
                        type: "POST",
                        url: "/Clientes/ConsultaListaNegraBuscar/",
                        data: { cdgcl, curp },
                        dataType: "json",
                        success: (res) => {
                            setLoading(false);
                            if (typeof res === "string") {
                                try { res = JSON.parse(res); } catch (e) {
                                    showError("Error al interpretar la respuesta.");
                                    mostrarEstado("inicial");
                                    return;
                                }
                            }
                            if (!res.success) {
                                manejarErrorBusqueda(res.mensaje || "No se pudo realizar la consulta.");
                                return;
                            }
                            pintarResultados(res.datos || [], res.mensaje);
                        },
                        error: (xhr) => {
                            setLoading(false);
                            let msg = "Ocurrió un error al procesar la solicitud.";
                            try {
                                const j = JSON.parse(xhr.responseText);
                                if (j.mensaje) msg = j.mensaje;
                            } catch (e) { /* noop */ }
                            showError(msg);
                            mostrarEstado("inicial");
                        }
                    });
                };

                const buscarEnter = (e) => {
                    if (e.key === "Enter") {
                        e.preventDefault();
                        buscar();
                    }
                };

                $(document).ready(function () {
                    $("#btn_buscar").on("click", buscar);
                    $("#cdgcl, #curp").on("keydown", buscarEnter);
                    $("#ln-resultados").on("click", ".ln-registro-cabecera", function () {
                        alternarRegistro($(this));
                    });
                    $("#curp").on("input", function () {
                        this.value = this.value.toUpperCase();
                    });
                    $("#cdgcl").on("input", function () {
                        this.value = this.value.replace(/\D/g, "");
                    });
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->GetExtraHeader('Consulta lista negra', [$extraCss])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('clientes_consulta_lista_negra');
    }

    /**
     * JSON: búsqueda en CL_MARCA por número de cliente y/o CURP.
     */
    public function ConsultaListaNegraBuscar()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $cdgcl = isset($_POST['cdgcl']) ? trim((string) $_POST['cdgcl']) : '';
        $curp = isset($_POST['curp']) ? trim((string) $_POST['curp']) : '';

        try {
            echo json_encode(ListaNegraClientesService::consultar($cdgcl, $curp), JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            echo json_encode(\Core\Model::Responde(false, 'No se pudo consultar la lista negra.', null, $e->getMessage()), JSON_UNESCAPED_UNICODE);
        }
    }
}
