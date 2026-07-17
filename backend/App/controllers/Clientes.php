<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\services\ListaNegraClientesService;
use App\services\ListaNegraRegistroService;

/**
 * Consultas y registro de clientes (menú Clientes).
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

    /**
     * Vista: registro de acreditados en lista negra (alta manual + carga masiva).
     */
    public function RegistroListaNegra()
    {
        $extraCss = <<<HTML
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="/css/consulta-lista-negra.css">
            <link rel="stylesheet" href="/css/registro-lista-negra.css">
        HTML;

        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}

                let previewListo = false;

                function labelPorTipo(tipo) {
                    if (tipo === "CLIENTE") return "Número de cliente";
                    if (tipo === "CREDITO") return "Número de crédito";
                    return "CURP";
                }

                function placeholderPorTipo(tipo) {
                    if (tipo === "CLIENTE") return "Ej. 015572";
                    if (tipo === "CREDITO") return "Ej. 207615";
                    return "18 caracteres";
                }

                function maxLengthPorTipo(tipo) {
                    if (tipo === "CURP") return 18;
                    if (tipo === "CLIENTE") return 10;
                    return 20;
                }

                function inicialNombre(nombre) {
                    var n = (nombre || "").trim();
                    return n ? n.charAt(0).toUpperCase() : "?";
                }

                function escapeHtml(texto) {
                    return String(texto == null ? "" : texto)
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;");
                }

                function cargarCausas() {
                    $.ajax({
                        type: "GET",
                        url: "/Clientes/RegistroListaNegraCausas/",
                        dataType: "json",
                        success: function(res) {
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { return; }
                            if (!res.success) return;
                            var \$sel = $("#causa_ln");
                            var actual = \$sel.val();
                            \$sel.find("option:not(:first)").remove();
                            (res.datos || []).forEach(function(c) {
                                var texto = c.DESCRIPCION || c.DESCRIPCION_FMT || "";
                                \$sel.append(\$("<option></option>").attr("value", c.CODIGO).text(texto));
                            });
                            if (actual) \$sel.val(actual);
                        }
                    });
                }

                function limpiarPreview() {
                    previewListo = false;
                    $("#btn_guardar_ln").prop("disabled", true);
                    $("#preview_ln").removeClass("is-visible").attr("hidden", true).empty();
                }

                function renderPreview(datos) {
                    if (!datos || !datos.length) {
                        limpiarPreview();
                        return;
                    }
                    var algunoNuevo = datos.some(function(c) { return !c.YA_EN_LISTA && c.CURP; });
                    var html = '<p class="ln-reg-preview-title">Acreditado(s) encontrado(s)</p>';
                    if (!algunoNuevo) {
                        html += '<p class="ln-reg-preview-note">Ya está en lista negra o no tiene CURP; no se puede volver a registrar.</p>';
                    }
                    html += '<div class="ln-reg-preview-list">';
                    datos.forEach(function(c) {
                        var ya = !!c.YA_EN_LISTA;
                        var sinCurp = !(c.CURP || "");
                        var clase = (ya || sinCurp) ? " is-bloqueado" : "";
                        var badge = ya
                            ? '<span class="ln-reg-badge ln-reg-badge--warn">Ya en lista negra</span>'
                            : (sinCurp
                                ? '<span class="ln-reg-badge ln-reg-badge--warn">Sin CURP</span>'
                                : '<span class="ln-reg-badge ln-reg-badge--ok">Listo para registrar</span>');
                        html += '<div class="ln-reg-preview-card' + clase + '">' +
                            '<div class="ln-reg-preview-avatar" aria-hidden="true">' + escapeHtml(inicialNombre(c.NOMBRE)) + '</div>' +
                            '<div>' +
                                '<p class="ln-reg-preview-nombre">' + escapeHtml(c.NOMBRE || "Sin nombre") + '</p>' +
                                '<p class="ln-reg-preview-meta">' +
                                    '<span class="ln-reg-mono">' + escapeHtml(c.CDGCL || "—") + '</span>' +
                                    '<span class="ln-reg-mono">' + escapeHtml(c.CURP || "Sin CURP") + '</span>' +
                                '</p>' +
                            '</div>' +
                            badge +
                        '</div>';
                    });
                    html += '</div>';
                    $("#preview_ln").html(html).addClass("is-visible").removeAttr("hidden");
                    previewListo = algunoNuevo;
                    $("#btn_guardar_ln").prop("disabled", !algunoNuevo);
                }

                function renderResumenMasivo(res) {
                    var regs = res.registrados || [];
                    var errs = res.errores || [];
                    var nOk = typeof res.insertados !== "undefined" ? res.insertados : regs.length;
                    var nOm = typeof res.omitidos !== "undefined" ? res.omitidos : errs.length;

                    var html = '<p class="ln-reg-preview-title">Resultado de carga masiva</p>';
                    html += '<p class="ln-reg-preview-note">Registrados: <strong>' + nOk + '</strong> · No procesados: <strong>' + nOm + '</strong></p>';

                    if (regs.length) {
                        html += '<p class="ln-reg-preview-subtitle">Registrados</p>';
                        html += '<div class="ln-reg-preview-list">';
                        regs.forEach(function(c) {
                            html += '<div class="ln-reg-preview-card">' +
                                '<div class="ln-reg-preview-avatar" aria-hidden="true">' + escapeHtml(inicialNombre(c.NOMBRE)) + '</div>' +
                                '<div>' +
                                    '<p class="ln-reg-preview-nombre">' + escapeHtml(c.NOMBRE || "Sin nombre") + '</p>' +
                                    '<p class="ln-reg-preview-meta">' +
                                        (c.fila ? '<span>Fila ' + escapeHtml(String(c.fila)) + '</span>' : '') +
                                        '<span class="ln-reg-mono">' + escapeHtml(c.CDGCL || "—") + '</span>' +
                                        '<span class="ln-reg-mono">' + escapeHtml(c.CURP || "—") + '</span>' +
                                    '</p>' +
                                '</div>' +
                                '<span class="ln-reg-badge ln-reg-badge--ok">Registrado</span>' +
                            '</div>';
                        });
                        html += '</div>';
                    }

                    if (errs.length) {
                        html += '<p class="ln-reg-preview-subtitle">No procesados</p>';
                        html += '<div class="ln-reg-preview-list">';
                        errs.forEach(function(e) {
                            html += '<div class="ln-reg-preview-card is-bloqueado">' +
                                '<div class="ln-reg-preview-avatar" aria-hidden="true">' + escapeHtml(inicialNombre(e.nombre || e.curp || "?")) + '</div>' +
                                '<div>' +
                                    '<p class="ln-reg-preview-nombre">' + escapeHtml(e.nombre || e.curp || "Sin dato") + '</p>' +
                                    '<p class="ln-reg-preview-meta">' +
                                        (e.fila ? '<span>Fila ' + escapeHtml(String(e.fila)) + '</span>' : '') +
                                        (e.cdgcl ? '<span class="ln-reg-mono">' + escapeHtml(String(e.cdgcl)) + '</span>' : '') +
                                        (e.curp ? '<span class="ln-reg-mono">' + escapeHtml(String(e.curp)) + '</span>' : '') +
                                    '</p>' +
                                    '<p class="ln-reg-preview-note" style="margin:6px 0 0;">' + escapeHtml(e.motivo || "Omitido") + '</p>' +
                                '</div>' +
                                '<span class="ln-reg-badge ln-reg-badge--warn">Omitido</span>' +
                            '</div>';
                        });
                        html += '</div>';
                    }

                    if (!regs.length && !errs.length) {
                        html += '<p class="ln-reg-preview-note">No hubo filas para mostrar.</p>';
                    }

                    $("#preview_ln").html(html).addClass("is-visible").removeAttr("hidden");
                    previewListo = false;
                    $("#btn_guardar_ln").prop("disabled", true);
                }

                function buscarAntesDeGuardar() {
                    var tipo = $("#tipo_busqueda").val();
                    var valor = ($("#valor_busqueda").val() || "").trim();
                    if (!valor) {
                        showWarning("Capture el dato a buscar.");
                        return;
                    }
                    showWait("Buscando...");
                    $.ajax({
                        type: "POST",
                        url: "/Clientes/RegistroListaNegraResolver/",
                        contentType: "application/json; charset=UTF-8",
                        data: JSON.stringify({ tipo: tipo, valor: valor }),
                        dataType: "json",
                        success: function(res) {
                            swal.close();
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                                showError("Respuesta inválida");
                                return;
                            }
                            if (!res.success) {
                                limpiarPreview();
                                showError(res.mensaje || "No se encontró");
                                return;
                            }
                            renderPreview(res.datos || []);
                        },
                        error: function() {
                            swal.close();
                            showError("Error al buscar.");
                        }
                    });
                }

                function guardarRegistro() {
                    var tipo = $("#tipo_busqueda").val();
                    var valor = ($("#valor_busqueda").val() || "").trim();
                    var causa = ($("#causa_ln").val() || "").trim();
                    if (!valor) {
                        showWarning("Capture el dato a registrar.");
                        return;
                    }
                    if (!causa) {
                        showWarning("Seleccione la causa de lista negra.");
                        return;
                    }
                    if (!previewListo) {
                        showWarning("Primero pulse Buscar y confirme el acreditado.");
                        return;
                    }
                    var causaTxt = ($("#causa_ln option:selected").text() || "").trim();
                    swal({
                        title: "¿Registrar en lista negra?",
                        text: "Se registrará el acreditado con la causa: " + causaTxt,
                        icon: "warning",
                        buttons: ["Cancelar", "Sí, registrar"],
                        dangerMode: true
                    }).then(function(ok) {
                        if (!ok) return;
                        swal({
                            text: "Procesando la solicitud, espere un momento...",
                            icon: "/img/wait.gif",
                            button: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        $.ajax({
                            type: "POST",
                            url: "/Clientes/RegistroListaNegraGuardar/",
                            contentType: "application/json; charset=UTF-8",
                            data: JSON.stringify({ tipo: tipo, valor: valor, causa: causa }),
                            dataType: "json",
                            success: function(res) {
                                swal.close();
                                try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                                    showError("Respuesta inválida");
                                    return;
                                }
                                if (res.success) {
                                    $("#valor_busqueda").val("");
                                    limpiarPreview();
                                    showSuccess(res.mensaje || "Registrado");
                                } else {
                                    showError(res.mensaje || "Error");
                                }
                            },
                            error: function() {
                                swal.close();
                                showError("Error al guardar.");
                            }
                        });
                    });
                }

                function importarArchivo() {
                    var f = document.getElementById("archivo_ln_reg").files[0];
                    if (!f) {
                        showWarning("Seleccione un archivo.");
                        return;
                    }
                    swal({
                        title: "¿Registrar carga masiva?",
                        text: "Se procesará el archivo \"" + f.name + "\" y se registrarán los acreditados válidos en lista negra.",
                        icon: "warning",
                        buttons: ["Cancelar", "Sí, registrar"],
                        dangerMode: true
                    }).then(function(ok) {
                        if (!ok) return;
                        // Misma carga que en el resto del sistema (rueda /img/wait.gif)
                        swal({
                            text: "Procesando la solicitud, espere un momento...",
                            icon: "/img/wait.gif",
                            button: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        var fd = new FormData();
                        fd.append("archivo", f);
                        $.ajax({
                            type: "POST",
                            url: "/Clientes/RegistroListaNegraCargaMasiva/",
                            data: fd,
                            processData: false,
                            contentType: false,
                            dataType: "json",
                            success: function(res) {
                                swal.close();
                                try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                                    showError("Respuesta inválida");
                                    return;
                                }
                                var msg = res.mensaje || "Importación finalizada";
                                $("#archivo_ln_reg").val("");
                                renderResumenMasivo(res || {});
                                if (res.success) {
                                    var omit = typeof res.omitidos !== "undefined" ? res.omitidos : 0;
                                    if (omit > 0) showWarning(msg); else showSuccess(msg);
                                } else {
                                    showError(msg);
                                }
                            },
                            error: function(xhr) {
                                swal.close();
                                var detalle = "";
                                try {
                                    var j = JSON.parse(xhr.responseText || "");
                                    if (j && j.mensaje) detalle = j.mensaje;
                                } catch (e) {}
                                if (!detalle && xhr.responseText) {
                                    detalle = String(xhr.responseText).replace(/<[^>]+>/g, " ").trim().substring(0, 240);
                                }
                                showError(detalle || ("Error al subir el archivo" + (xhr.status ? " (HTTP " + xhr.status + ")" : "") + "."));
                            }
                        });
                    });
                }

                function aplicarTipoBusqueda() {
                    var t = $("#tipo_busqueda").val();
                    $("#label_valor_busqueda").text(labelPorTipo(t));
                    $("#valor_busqueda")
                        .attr("placeholder", placeholderPorTipo(t))
                        .attr("maxlength", maxLengthPorTipo(t))
                        .val("");
                    limpiarPreview();
                }

                $(document).ready(function() {
                    cargarCausas();

                    $("#tipo_busqueda").on("change", aplicarTipoBusqueda);
                    $("#valor_busqueda").on("input", function() {
                        limpiarPreview();
                        var t = $("#tipo_busqueda").val();
                        if (t === "CURP") this.value = this.value.toUpperCase();
                        if (t === "CLIENTE") this.value = this.value.replace(/\\D/g, "");
                    });

                    $("#btn_buscar_ln").click(buscarAntesDeGuardar);
                    $("#btn_guardar_ln").click(guardarRegistro);
                    $("#btn_importar_ln").click(importarArchivo);
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->GetExtraHeader('Registro Lista Negra', [$extraCss])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('clientes_registro_lista_negra');
    }

    /** JSON: catálogo de causas TIPO = A. */
    public function RegistroListaNegraCausas()
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(ListaNegraRegistroService::listarCausas(), JSON_UNESCAPED_UNICODE);
    }

    /** JSON: resolver crédito / cliente / CURP. */
    public function RegistroListaNegraResolver()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?: [];
        $tipo = isset($body['tipo']) ? (string) $body['tipo'] : '';
        $valor = isset($body['valor']) ? (string) $body['valor'] : '';
        echo json_encode(ListaNegraRegistroService::resolver($tipo, $valor), JSON_UNESCAPED_UNICODE);
    }

    /** JSON: alta manual. */
    public function RegistroListaNegraGuardar()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?: [];
        $tipo = isset($body['tipo']) ? (string) $body['tipo'] : '';
        $valor = isset($body['valor']) ? (string) $body['valor'] : '';
        $causa = isset($body['causa']) ? (string) $body['causa'] : '';
        $usuario = $this->__usuario ?? '';
        echo json_encode(ListaNegraRegistroService::guardarUno($tipo, $valor, $causa, $usuario), JSON_UNESCAPED_UNICODE);
    }

    /** Carga masiva desde Excel/CSV. */
    public function RegistroListaNegraCargaMasiva()
    {
        header('Content-Type: application/json; charset=UTF-8');
        try {
            if (!isset($_FILES['archivo']) || !is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                echo json_encode(\Core\Model::Responde(false, 'No se recibió el archivo.'), JSON_UNESCAPED_UNICODE);
                return;
            }
            if ($_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(\Core\Model::Responde(false, 'Error al subir el archivo (código ' . (int) $_FILES['archivo']['error'] . ').'), JSON_UNESCAPED_UNICODE);
                return;
            }
            $tmp = $_FILES['archivo']['tmp_name'];
            $usuario = $this->__usuario ?? '';
            $nombre = isset($_FILES['archivo']['name']) ? (string) $_FILES['archivo']['name'] : '';
            $ext = strtolower((string) pathinfo($nombre, PATHINFO_EXTENSION));
            if (!in_array($ext, ['xlsx', 'xls', 'xlsm', 'csv', 'txt'], true)) {
                $ext = 'xlsx';
            }
            $dirTmp = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'tmp';
            if (!is_dir($dirTmp)) {
                @mkdir($dirTmp, 0755, true);
            }
            $dest = $dirTmp . DIRECTORY_SEPARATOR . 'ln_reg_' . uniqid('', true) . '.' . $ext;
            $okMove = @move_uploaded_file($tmp, $dest);
            if (!$okMove) {
                $okMove = @copy($tmp, $dest);
            }
            if (!$okMove || !is_readable($dest)) {
                $fallback = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ln_reg_' . uniqid('', true) . '.' . $ext;
                $okMove = @copy($tmp, $fallback);
                if ($okMove && is_readable($fallback)) {
                    $dest = $fallback;
                } else {
                    echo json_encode(\Core\Model::Responde(false, 'No se pudo guardar el archivo recibido.'), JSON_UNESCAPED_UNICODE);
                    return;
                }
            }
            try {
                echo json_encode(ListaNegraRegistroService::cargaMasivaDesdeArchivo($dest, $usuario), JSON_UNESCAPED_UNICODE);
            } finally {
                @unlink($dest);
            }
        } catch (\Throwable $e) {
            echo json_encode(
                \Core\Model::Responde(false, 'Error al procesar el archivo: ' . $e->getMessage()),
                JSON_UNESCAPED_UNICODE
            );
        }
    }

    /** Layout Excel: CREDITO | CLIENTE | CURP | CAUSA (dropdown de causas). */
    public function RegistroListaNegraLayout()
    {
        $resCausas = ListaNegraRegistroService::listarCausas();
        $causas = (!empty($resCausas['success']) && is_array($resCausas['datos'] ?? null))
            ? $resCausas['datos']
            : [];

        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $centrado = ['estilo' => $estilos['centrado']];
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('CREDITO', 'No. crédito', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CLIENTE', 'No. cliente', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CURP', 'CURP', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CAUSA', 'Causa lista negra', $centrado),
        ];

        // Filas vacías preformateadas para captura
        $filas = [];
        for ($i = 0; $i < 50; $i++) {
            $filas[] = ['CREDITO' => '', 'CLIENTE' => '', 'CURP' => '', 'CAUSA' => ''];
        }

        $libro = \PHPSpreadsheet::GeneraExcel(
            'ListaNegra',
            'Capture una sola identificación por fila (crédito, cliente o CURP) y seleccione la causa del desplegable.',
            $columnas,
            $filas
        );

        $hoja = $libro->getSheetByName('ListaNegra');
        if ($hoja === null) {
            $hoja = $libro->getSheet(0);
        }

        // Título y encabezados legibles
        $hoja->getRowDimension(1)->setRowHeight(36);
        $hoja->getStyle('A1:D1')->getAlignment()
            ->setWrapText(true)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $hoja->getRowDimension(2)->setRowHeight(22);
        $hoja->getStyle('A2:D2')->getAlignment()
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setWrapText(true);

        // Anchos fijos (evita autoSize irregular)
        $hoja->getColumnDimension('A')->setAutoSize(false)->setWidth(16);
        $hoja->getColumnDimension('B')->setAutoSize(false)->setWidth(14);
        $hoja->getColumnDimension('C')->setAutoSize(false)->setWidth(24);
        $hoja->getColumnDimension('D')->setAutoSize(false)->setWidth(52);

        for ($r = 3; $r <= 52; $r++) {
            $hoja->getRowDimension($r)->setRowHeight(20);
            $hoja->getStyle("A{$r}:D{$r}")->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $hoja->getStyle("D{$r}")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        }

        // Catálogo en hoja oculta (solo para el dropdown)
        $hojaCausas = $libro->createSheet();
        $hojaCausas->setTitle('Causas');
        $hojaCausas->setCellValue('A1', 'Código');
        $hojaCausas->setCellValue('B1', 'Descripción');
        $hojaCausas->setCellValue('C1', 'Opción');
        $hojaCausas->getStyle('A1:C1')->applyFromArray($estilos['encabezado'] ?? [
            'font' => ['bold' => true],
        ]);
        $hojaCausas->getRowDimension(1)->setRowHeight(22);

        $n = 0;
        foreach ($causas as $c) {
            $codigo = trim((string) ($c['CODIGO'] ?? ''));
            $desc = trim((string) ($c['DESCRIPCION'] ?? $c['DESCRIPCION_FMT'] ?? ''));
            if ($codigo === '') {
                continue;
            }
            $n++;
            $fila = $n + 1;
            // Dropdown: solo descripción (sin "4 - …"); el código queda en col. A para referencia
            $opcion = $desc !== '' ? $desc : $codigo;
            $hojaCausas->setCellValueExplicit('A' . $fila, $codigo, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $hojaCausas->setCellValue('B' . $fila, $desc);
            $hojaCausas->setCellValue('C' . $fila, $opcion);
            $hojaCausas->getRowDimension($fila)->setRowHeight(18);
            $hojaCausas->getStyle("A{$fila}:C{$fila}")->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }

        $hojaCausas->getColumnDimension('A')->setWidth(12);
        $hojaCausas->getColumnDimension('B')->setWidth(48);
        $hojaCausas->getColumnDimension('C')->setWidth(56);
        $hojaCausas->getStyle('A2:A' . max(2, $n + 1))->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $hojaCausas->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

        if ($n > 0) {
            $rangoLista = 'Causas!$C$2:$C$' . ($n + 1);
            $validation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Causa inválida');
            $validation->setError('Selecciona un valor válido de la lista');
            $validation->setPromptTitle('Causa lista negra');
            $validation->setPrompt('Seleccione una causa de la lista desplegable');
            $validation->setFormula1($rangoLista);
            $hoja->setDataValidation('D3:D52', $validation);
        }

        $libro->setActiveSheetIndex($libro->getIndex($hoja));
        $hoja->setSelectedCell('A3');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="layout_registro_lista_negra.xlsx"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Pragma: public');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($libro);
        $writer->save('php://output');
        exit;
    }
}
