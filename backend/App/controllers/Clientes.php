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
                        titulo: "Identificación",
                        campos: [
                            { key: "CDGCL", label: "No. cliente" },
                            { key: "NOMBRE_CLIENTE", label: "Nombre" },
                            { key: "CURP", label: "CURP", mono: true }
                        ]
                    },
                    {
                        titulo: "Marca y estatus",
                        campos: [
                            { key: "TIPOMARCA_FMT", label: "Tipo marca" },
                            { key: "ESTATUS_FMT", label: "Estatus" },
                            { key: "MONTOMAX_FMT", label: "Monto máximo" },
                            { key: "CAUSA_FMT", label: "Causa de alta" },
                            { key: "CAUSABAJA_FMT", label: "Causa de baja" }
                        ]
                    },
                    {
                        titulo: "Crédito / NS",
                        campos: [
                            { key: "CDGCLNS", label: "No. crédito / NS" },
                            { key: "NOMBRE_CREDITO", label: "Nombre del grupo" },
                            { key: "CICLO", label: "Ciclo" },
                            { key: "CLNS_FMT", label: "Tipo crédito" }
                        ]
                    },
                    {
                        titulo: "Auditoría",
                        campos: [
                            { key: "ALTA_FMT", label: "Fecha alta" },
                            { key: "BAJA_FMT", label: "Fecha baja" },
                            { key: "USUARIO_ALTA_FMT", label: "Usuario alta" },
                            { key: "USUARIO_BAJA_FMT", label: "Usuario baja" },
                            { key: "FREGISTRO_FMT", label: "F. registro" },
                            { key: "SECUENCIA", label: "Secuencia" },
                            { key: "CDGEM_FMT", label: "Empresa" }
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
                    const tipo = String(registro.TIPOMARCA || "").trim().toUpperCase();
                    if (val === "A") {
                        const txt = tipo === "LN" ? "Activo en lista negra" : "Marca activa";
                        return '<span class="ln-badge ln-badge-activo">' + esc(txt) + '</span>';
                    }
                    if (val === "B") {
                        const txt = tipo === "LN" ? "Baja de lista negra" : "Marca dada de baja";
                        return '<span class="ln-badge ln-badge-baja">' + esc(txt) + '</span>';
                    }
                    return '<span class="ln-badge ln-badge-baja">' + esc(registro.ESTATUS_FMT || val || "Sin estatus") + '</span>';
                };

                const etiquetaTipoMarca = (registro) => {
                    if (!tieneValor(registro.TIPOMARCA)) return "";
                    const desc = String(registro.TIPOMARCA_FMT || registro.TIPOMARCA || "");
                    const corto = desc.indexOf(" — ") >= 0 ? desc.split(" — ")[1] : desc;
                    return '<span class="ln-badge ln-badge-tipo" title="' + esc(desc) + '">' + esc(registro.TIPOMARCA) + ': ' + esc(corto) + '</span>';
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

                    return (
                        '<div class="ln-dato">' +
                            '<span class="ln-dato-label">' + esc(campo.label) + '</span>' +
                            '<span class="' + clases.join(" ") + '">' + esc(valor) + '</span>' +
                        '</div>'
                    );
                };

                const crearSeccion = (seccion, registro) => {
                    const datos = seccion.campos.map((campo) => crearDato(campo, registro)).join("");
                    if (!datos) return "";

                    return (
                        '<div class="ln-seccion">' +
                            '<h5 class="ln-seccion-titulo">' + esc(seccion.titulo) + '</h5>' +
                            '<div class="ln-datos">' + datos + '</div>' +
                        '</div>'
                    );
                };

                const crearRegistro = (registro, indice, total) => {
                    const estatus = String(registro.ESTATUS || "").trim().toUpperCase();
                    const nombre = [registro.NOMBRE_CLIENTE, registro.NOMBRE_CREDITO]
                        .find((val) => tieneValor(val)) || "Registro sin nombre";
                    const tipoMarca = etiquetaTipoMarca(registro);
                    const cuerpo = secciones.map((seccion) => crearSeccion(seccion, registro)).join("");
                    const metaPartes = [];
                    if (total > 1) {
                        metaPartes.push("<span>Registro <strong>" + indice + "</strong> de <strong>" + total + "</strong></span>");
                    }
                    if (tieneValor(registro.CDGCL)) {
                        metaPartes.push("<span>Cliente <strong>" + esc(registro.CDGCL) + "</strong></span>");
                    }
                    if (tieneValor(registro.CURP)) {
                        metaPartes.push("<span>CURP <strong>" + esc(registro.CURP) + "</strong></span>");
                    }

                    return (
                        '<article class="ln-registro ' + (estatus === "B" ? "estatus-baja" : "estatus-activo") + '">' +
                            '<button type="button" class="ln-registro-cabecera" aria-expanded="false" aria-label="' + esc(ariaEtiquetaRegistro(nombre, indice, total)) + '">' +
                                '<span class="ln-registro-avatar" aria-hidden="true">' + esc(iniciales(nombre)) + '</span>' +
                                '<span class="ln-registro-identidad">' +
                                    '<span class="ln-registro-nombre">' + esc(nombre) + '</span>' +
                                    (metaPartes.length ? '<span class="ln-registro-meta">' + metaPartes.join("") + '</span>' : "") +
                                    '<span class="ln-registro-hint">Clic para ver detalle</span>' +
                                '</span>' +
                                '<span class="ln-registro-etiquetas">' +
                                    etiquetaEstatus(registro) +
                                    tipoMarca +
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

                const mensajeBannerResultados = (datos) => {
                    const total = datos.length;
                    const activos = contarActivos(datos);
                    if (total === 1) {
                        return activos === 1
                            ? "Se encontró un registro activo en lista negra."
                            : "Se encontró un registro en lista negra (sin marcas activas).";
                    }
                    if (activos > 0) {
                        return "Se encontraron " + total + " registros; " + activos + " con marca activa.";
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
                    mostrarBanner("resultados", mensajeBannerResultados(datos), datos.length);
                    mostrarEstado("datos");
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
            echo json_encode(ListaNegraClientesService::consultar($cdgcl, $curp));
        } catch (\Throwable $e) {
            echo json_encode(\Core\Model::Responde(false, 'No se pudo consultar la lista negra.', null, $e->getMessage()));
        }
    }
}
