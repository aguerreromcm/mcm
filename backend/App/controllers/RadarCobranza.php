<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\RadarCobranza as RadarCobranzaDao;

class RadarCobranza extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function DashboardDia()
    {
        $extraFooter = <<<HTML
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                {$this->mensajes}
                {$this->consultaServidor}
                {$this->confirmarMovimiento}

                const validarToken = () => {
                    const authData = localStorage.getItem("radar_auth")
                    if (!authData) {
                        mostrarModalLogin()
                        return false
                    }

                    try {
                        const data = JSON.parse(authData)
                        if (!data.access_token) {
                            mostrarModalLogin()
                            return false
                        }
                        return data.access_token
                    } catch (e) {
                        mostrarModalLogin()
                        return false
                    }
                }

                const mostrarModalLogin = () => {
                    $("#loginModal").modal("show")
                }

                // Proceso de login
                const realizarLogin = () => {
                    const usuario = $("#usuario").val().trim()
                    const password = $("#password").val().trim()

                    if (!usuario || !password) {
                        showError("Por favor ingrese usuario y contraseña")
                        return
                    }

                    consultaServidor("/RadarCobranza/Login", { usuario, password }, (res) => {
                        if (!res.success) {
                            showError(res.mensaje)
                            return
                        }

                        // Guardar datos en localStorage
                        localStorage.setItem("radar_auth", JSON.stringify(res.datos))
                        $("#loginModal").modal("hide")
                        $("#btnCerrarSesion").show() // Mostrar botón de cerrar sesión
                        cargarDashboard()
                    })
                }

                // Cargar dashboard principal
                const cargarDashboard = () => {
                    const token = validarToken()
                    if (!token) return

                    consultaServidor("/RadarCobranza/GetResumenCobranza", { token }, (res) => {
                        if (!res.success) {
                            if (res.codigo === "TOKEN_EXPIRED") {
                                localStorage.removeItem("radar_auth")
                                mostrarModalLogin()
                            } else {
                                showError(res.mensaje)
                            }
                            return
                        }

                        renderizarDashboard(res.datos)
                    })
                }

                // Renderizar dashboard
                const renderizarDashboard = (data) => {
                    // Guardar datos globalmente para uso en otras funciones
                    window.dashboardData = data
                    
                    // Limpiar charts anteriores al renderizar nuevo dashboard
                    Object.keys(chartInstances).forEach(chartId => {
                        if (chartInstances[chartId]) {
                            chartInstances[chartId].destroy()
                            delete chartInstances[chartId]
                        }
                    })
                    
                    if (!data.por_dia) {
                        showError("No hay datos disponibles")
                        return
                    }

                    const diasSemana = ["LUNES", "MARTES", "MIERCOLES", "JUEVES", "VIERNES"]
                    const fechaActual = new Date()
                    let diaActual = ""

                    // Obtener el día actual en español
                    switch (fechaActual.getDay()) {
                        case 1:
                            diaActual = "LUNES"
                            break
                        case 2:
                            diaActual = "MARTES"
                            break
                        case 3:
                            diaActual = "MIERCOLES"
                            break
                        case 4:
                            diaActual = "JUEVES"
                            break
                        case 5:
                            diaActual = "VIERNES"
                            break
                        default:
                            diaActual = "" // Fin de semana
                    }

                    let accordionHTML = ""

                    diasSemana.forEach((dia, index) => {
                        const datosDia = data.por_dia[dia] || {}
                        const tieneDatos = Object.keys(datosDia).length > 0

                        let totales = 0,
                            cobrados = 0,
                            pendientes = 0

                        if (tieneDatos) {
                            Object.values(datosDia).forEach((sucursal) => {
                                if (sucursal.global) {
                                    // Calcular totales correctamente
                                    const pagosCobrados = Math.abs(sucursal.global.PAGOS_COBRADOS)
                                    const pagosPendientes = sucursal.global.PAGOS_PENDIENTES
                                    const totalDelDia = pagosCobrados + pagosPendientes

                                    totales += totalDelDia
                                    cobrados += pagosCobrados
                                    pendientes += pagosPendientes
                                }
                            })
                        }

                        // Determinar clase del badge
                        let badgeClass = "badge-secondary"
                        if (dia === diaActual) {
                            badgeClass = "badge-success"
                        } else {
                            const indiceDiaActual = diasSemana.indexOf(diaActual)
                            if (indiceDiaActual !== -1) {
                                if (index < indiceDiaActual) {
                                    badgeClass = "badge-danger"
                                } else if (index > indiceDiaActual) {
                                    badgeClass = "badge-warning"
                                }
                            }
                        }

                        const collapseId = "collapse" + dia
                        const headingId = "heading" + dia

                        accordionHTML +=
                            "<div class='card'>" +
                            "<div class='card-header' id='" +
                            headingId +
                            "'>" +
                            "<h2 class='mb-0'>" +
                            "<button class='btn btn-link btn-block text-left collapsed d-flex justify-content-between align-items-center' " +
                            "type='button' data-toggle='collapse' data-target='#" +
                            collapseId +
                            "' " +
                            "aria-expanded='false' aria-controls='" +
                            collapseId +
                            "' " +
                            "onclick=\"toggleDia('" +
                            dia +
                            "', " +
                            JSON.stringify(datosDia).replace(/"/g, "&quot;") +
                            ')">' +
                            "<div>" +
                            "<span class='badge " +
                            badgeClass +
                            " mr-2'>" +
                            dia +
                            "</span>" +
                            "<span>Totales: " +
                            totales +
                            " | Cobrados: " +
                            cobrados +
                            " | Pendientes: " +
                            pendientes +
                            "</span>" +
                            "</div>" +
                            "<i class='fa fa-chevron-down'></i>" +
                            "</button>" +
                            "</h2>" +
                            "</div>" +
                            "<div id='" +
                            collapseId +
                            "' class='collapse' aria-labelledby='" +
                            headingId +
                            "' data-parent='#accordionDias'>" +
                            "<div class='card-body' id='content" +
                            dia +
                            "'>" +
                            "</div>" +
                            "</div>" +
                            "</div>"
                    })

                    $("#accordionDias").html(accordionHTML)
                }

                // Toggle día y cargar contenido
                const toggleDia = (dia, datos) => {
                    const contentDiv = document.getElementById("content" + dia)

                    if (contentDiv.innerHTML.trim() === "") {
                        cargarContenidoDia(dia, datos)
                    }
                }

                // Variable para almacenar instancias de charts
                const chartInstances = {}

                // Cargar contenido del día
                const cargarContenidoDia = (dia, datos) => {
                    if (!datos || Object.keys(datos).length === 0) {
                        document.getElementById("content" + dia).innerHTML =
                            "<p class='text-center'>No hay datos para este día</p>"
                        return
                    }

                    // Destruir chart anterior si existe
                    const chartId = "chart" + dia
                    if (chartInstances[chartId]) {
                        chartInstances[chartId].destroy()
                        delete chartInstances[chartId]
                    }

                    let totalCobrados = 0,
                        totalPendientes = 0
                    let ejecutivos = []

                    Object.values(datos).forEach((sucursal) => {
                        if (sucursal.global) {
                            totalCobrados += Math.abs(sucursal.global.PAGOS_COBRADOS)
                            totalPendientes += sucursal.global.PAGOS_PENDIENTES
                        }

                        if (sucursal.detalle) {
                            sucursal.detalle.forEach((ejecutivo) => {
                                ejecutivos.push({
                                    nombre: ejecutivo.NOMBRE_ASESOR,
                                    pagosDia: ejecutivo.PAGOS_DEL_DIA,
                                    pagosCobrados: Math.abs(ejecutivo.PAGOS_COBRADOS),
                                    pagosPendientes: ejecutivo.PAGOS_PENDIENTES,
                                    efectivo: ejecutivo.POR_RECOLECTAR_EFECTIVO || 0,
                                    sucursal: ejecutivo.SUCURSAL
                                })
                            })
                        }
                    })

                    // Ordenar ejecutivos alfabéticamente por nombre
                    ejecutivos.sort((a, b) => a.nombre.localeCompare(b.nombre))

                    let ejecutivosHTML = ""
                    ejecutivos.forEach((ejecutivo) => {
                        ejecutivosHTML +=
                            "<div class='ejecutivo-card'>" +
                            "<div class='card'>" +
                            "<div class='card-body'>" +
                            "<h6 class='card-title'>" +
                            ejecutivo.nombre +
                            "</h6>" +
                            "<div class='card-text'>" +
                            "<small>" +
                            "Del día: " +
                            ejecutivo.pagosDia +
                            "<br>" +
                            "Cobrados: " +
                            ejecutivo.pagosCobrados +
                            "<br>" +
                            "Pendientes: " +
                            ejecutivo.pagosPendientes +
                            "<br>" +
                            "Efectivo: $" +
                            ejecutivo.efectivo.toLocaleString() +
                            "</small>" +
                            "</div>" +
                            "<button class='btn btn-sm btn-primary' onclick=\"mostrarDetalleEjecutivo('" +
                            ejecutivo.nombre +
                            "', '" +
                            dia +
                            "', '" +
                            ejecutivo.sucursal +
                            "')\">" +
                            "Ver Detalle" +
                            "</button>" +
                            "</div>" +
                            "</div>" +
                            "</div>"
                    })

                    const totalGeneral = totalCobrados + totalPendientes

                    let chartHTML = ""
                    if (totalGeneral > 0) {
                        const porcentajeCobrados = ((totalCobrados / totalGeneral) * 100).toFixed(1)
                        const porcentajePendientes = ((totalPendientes / totalGeneral) * 100).toFixed(1)

                        chartHTML =
                            "<div class='text-center mb-3' style='flex: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%;'>" +
                            "<h5>Resumen del día</h5>" +
                            "<div style='height: 85%; display: flex; justify-content: center; align-items: center;'>" +
                            "<canvas id='" +
                            chartId +
                            "'></canvas>" +
                            "</div>" +
                            "<div class='mt-2'>" +
                            "<small>" +
                            "<span class='text-success'>■ Cobrados: " +
                            totalCobrados +
                            " (" +
                            porcentajeCobrados +
                            "%)</span><br>" +
                            "<span class='text-danger'>■ Pendientes: " +
                            totalPendientes +
                            " (" +
                            porcentajePendientes +
                            "%)</span>" +
                            "</small>" +
                            "</div>" +
                            "</div>"
                    } else {
                        chartHTML = "<div class='text-center'><p>No hay datos para mostrar</p></div>"
                    }

                    const contenidoHTML =
                        "<div class='row' style='display: flex;'>" +
                        "<div class='col-md-6' style='flex: 1; display: flex; justify-content: center; align-items: center;'>" +
                        chartHTML +
                        "</div>" +
                        "<div class='col-md-6'>" +
                        "<h5>Ejecutivos</h5>" +
                        "<div class='ejecutivos-container'>" +
                        ejecutivosHTML +
                        "</div>" +
                        "</div>" +
                        "</div>"

                    document.getElementById("content" + dia).innerHTML = contenidoHTML

                    // Configurar gráfico si hay datos
                    if (totalGeneral > 0) {
                        setTimeout(() => {
                            const ctx = document.getElementById(chartId)
                            if (ctx && ctx.getContext) {
                                chartInstances[chartId] = new Chart(ctx.getContext("2d"), {
                                    type: "pie",
                                    data: {
                                        labels: ["Cobrados", "Pendientes"],
                                        datasets: [
                                            {
                                                data: [totalCobrados, totalPendientes],
                                                backgroundColor: ["#28a745", "#dc3545"],
                                                borderWidth: 2,
                                                borderColor: "#fff"
                                            }
                                        ]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: true,
                                        aspectRatio: 1,
                                        plugins: {
                                            legend: {
                                                display: false
                                            }
                                        },
                                        animation: {
                                            duration: 500
                                        }
                                    }
                                })
                            }
                        }, 100)
                    }
                }

                // Variables globales para el ejecutivo actual
                let ejecutivoActual = null

                // Mostrar detalle del ejecutivo
                const mostrarDetalleEjecutivo = (nombreEjecutivo, dia, sucursal) => {
                    const fechaActual = new Date()

                    const mapaDias = {
                        LUNES: 1,
                        MARTES: 2,
                        MIERCOLES: 3,
                        JUEVES: 4,
                        VIERNES: 5
                    }

                    const diaNumerico = mapaDias[dia]
                    const diaActual = fechaActual.getDay()
                    let fechaDelDia = new Date(fechaActual)

                    if (diaNumerico) {
                        const diferencia = diaNumerico - diaActual
                        fechaDelDia.setDate(fechaActual.getDate() + diferencia)
                    }

                    const fechaFormateada = fechaDelDia.toLocaleDateString("es-MX", {
                        year: "numeric",
                        month: "long", 
                        day: "2-digit"
                    })

                    // Guardar información del ejecutivo actual para usar en otras funciones
                    ejecutivoActual = {
                        nombre: nombreEjecutivo,
                        dia: dia,
                        sucursal: sucursal,
                        fecha: fechaDelDia.toISOString().split('T')[0] // Formato yyyy-mm-dd
                    }

                    // Buscar datos del ejecutivo en el dashboard actual
                    const datosEjecutivo = buscarDatosEjecutivo(nombreEjecutivo, dia)
                    
                    if (datosEjecutivo) {
                        // Llenar cards de información
                        $("#codigoEjecutivo").text(datosEjecutivo.ASESOR || "-")
                        $("#efectivoRecolectado").text("$" + (datosEjecutivo.RECOLECTADO || 0).toLocaleString())
                        $("#porRecolectar").text("$" + (datosEjecutivo.POR_RECOLECTAR_EFECTIVO || 0).toLocaleString())
                        $("#pendienteEfectivo").text("$" + (datosEjecutivo.PENDIENTE_EFECTIVO || 0).toLocaleString())
                        
                        // Estadísticas del día
                        const cobrados = Math.abs(datosEjecutivo.PAGOS_COBRADOS || 0)
                        const pendientes = datosEjecutivo.PAGOS_PENDIENTES || 0
                        const total = cobrados + pendientes
                        const porcentaje = total > 0 ? ((cobrados / total) * 100).toFixed(1) : 0
                        
                        $("#cobradosDetalle").text(cobrados)
                        $("#pendientesDetalle").text(pendientes)
                        $("#totalDetalle").text(total)
                        $("#progresoBar").css("width", porcentaje + "%").attr("aria-valuenow", porcentaje)
                        $("#porcentajeCobrado").text(porcentaje + "%")
                    } else {
                        // Valores por defecto si no se encuentran datos
                        $("#codigoEjecutivo").text("-")
                        $("#efectivoRecolectado").text("$0.00")
                        $("#porRecolectar").text("$0.00")
                        $("#pendienteEfectivo").text("$0.00")
                        $("#cobradosDetalle").text("0")
                        $("#pendientesDetalle").text("0")
                        $("#totalDetalle").text("0")
                        $("#progresoBar").css("width", "0%").attr("aria-valuenow", 0)
                        $("#porcentajeCobrado").text("0%")
                    }

                    $("#modalDetalleSubtitle").html(
                        "<div class='row'>" +
                            "<div class='col-md-6'><strong>Ejecutivo:</strong> " +
                            nombreEjecutivo +
                            "</div>" +
                            "<div class='col-md-6'><strong>Sucursal:</strong> " +
                            sucursal +
                            "</div>" +
                            "<div class='col-md-6'><strong>Día:</strong> " +
                            dia +
                            "</div>" +
                            "<div class='col-md-6'><strong>Fecha:</strong> " +
                            fechaFormateada +
                            "</div>" +
                        "</div>"
                    )
                    $("#modalDetalle").modal("show")
                }

                // Buscar datos del ejecutivo en el dashboard cargado
                const buscarDatosEjecutivo = (nombreEjecutivo, dia) => {
                    // Esta función busca en los datos ya cargados del dashboard
                    // Necesitamos acceso a los datos del dashboard, los almacenaremos globalmente
                    if (window.dashboardData && window.dashboardData.por_dia && window.dashboardData.por_dia[dia]) {
                        const datosDia = window.dashboardData.por_dia[dia]
                        for (const sucursalKey in datosDia) {
                            const sucursal = datosDia[sucursalKey]
                            if (sucursal.detalle) {
                                const ejecutivo = sucursal.detalle.find(e => e.NOMBRE_ASESOR === nombreEjecutivo)
                                if (ejecutivo) return ejecutivo
                            }
                        }
                    }
                    return null
                }

                // Función para mostrar modal de ruta de cobranza
                const verRutaCobranza = () => {
                    if (!ejecutivoActual) {
                        showError("No hay información del ejecutivo seleccionado")
                        return
                    }

                    const authData = localStorage.getItem("radar_auth")
                    if (!authData) {
                        showError("No hay información de autenticación")
                        return
                    }

                    let mapsKey = ""
                    try {
                        const data = JSON.parse(authData)
                        mapsKey = data["key-maps"] || ""
                    } catch (e) {
                        showError("Error al obtener la clave de Google Maps")
                        return
                    }

                    if (!mapsKey) {
                        showError("No se encontró la clave de Google Maps en la sesión")
                        return
                    }

                    // Mostrar modal y cargar datos
                    $("#ejecutivoRutaNombre").text(ejecutivoActual.nombre)
                    $("#modalRutaCobranza").modal("show")
                    $("#map").hide()

                    // Cargar script de Google Maps si no está cargado
                    if (!window.google) {
                        const script = document.createElement("script")
                        script.src = "https://maps.googleapis.com/maps/api/js?key=" + mapsKey
                        script.async = true
                        script.defer = true
                        document.head.appendChild(script)
                        // Inicializar mapa una vez que el script se haya cargado
                        script.onload = () => {
                            inicializarMapa()
                        }
                    } else {
                        inicializarMapa()
                    }
                }

                // Inicializar el mapa de Google Maps
                const inicializarMapa = () => {
                    const token = validarToken()
                    if (!token) return

                    // Buscar código del ejecutivo
                    let codigoEjecutivo = ""
                    const datosEjecutivo = buscarDatosEjecutivo(ejecutivoActual.nombre, ejecutivoActual.dia)
                    if (datosEjecutivo && datosEjecutivo.ASESOR) {
                        codigoEjecutivo = datosEjecutivo.ASESOR
                    }

                    const requestData = {
                        token: token,
                        ejecutivo: codigoEjecutivo,
                        fecha: ejecutivoActual.fecha
                    }

                    consultaServidor("/RadarCobranza/GetRutaCobranza", requestData, (res) => {                        
                        if (!res.success) {
                            $("#map").html("<div class='alert alert-danger text-center'><p>Error al cargar la ruta: " + res.mensaje + "</p></div>").show()
                            return
                        }

                        mostrarMapaConRuta(res.datos)
                    })
                }

                // Mostrar mapa con los datos de la ruta
                const mostrarMapaConRuta = (geoJsonData) => {
                    $("#map").show()

                    if (!geoJsonData || !geoJsonData.features || geoJsonData.features.length === 0) {
                        $("#map").html("<div class='alert alert-info text-center'><p>No hay datos de ruta para mostrar</p></div>")
                        return
                    }

                    // Procesar datos para el resumen
                    const puntos = geoJsonData.features.filter(f => f.geometry.type === "Point")
                    let montoTotal = 0
                    let fechaRuta = "-"

                    puntos.forEach(punto => {
                        if (punto.properties.monto) {
                            montoTotal += punto.properties.monto
                        }
                        if (punto.properties.fecha && fechaRuta === "-") {
                            fechaRuta = punto.properties.fecha
                        }
                    })

                    // Actualizar resumen
                    $("#totalPuntos").text(puntos.length)
                    $("#puntosPago").text(puntos.filter(p => p.properties.tipo === "PAGO").length)
                    $("#montoTotal").text("$" + montoTotal.toLocaleString())
                    $("#fechaRuta").text(fechaRuta)

                    // Configurar mapa
                    const mapOptions = {
                        zoom: 12,
                        center: { lat: 19.4326, lng: -99.1332 },
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    }

                    const map = new google.maps.Map(document.getElementById("map"), mapOptions)
                    const bounds = new google.maps.LatLngBounds()

                    // Agregar marcadores
                    puntos.forEach((punto, index) => {
                        const coords = punto.geometry.coordinates
                        const position = new google.maps.LatLng(parseFloat(coords[1]), parseFloat(coords[0]))
                        
                        const marker = new google.maps.Marker({
                            position: position,
                            map: map,
                            title: punto.properties.nombre + " - $" + punto.properties.monto,
                            label: (index + 1).toString(),
                            icon: {
                                url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(
                                    "<svg width='30' height='30' xmlns='http://www.w3.org/2000/svg'>" +
                                    "<circle cx='15' cy='15' r='12' fill='" + punto.properties.color + "' stroke='white' stroke-width='2'/>" +
                                    "<text x='15' y='20' text-anchor='middle' fill='white' font-size='12' font-weight='bold'>" + (index + 1) + "</text>" +
                                    "</svg>"
                                )
                            }
                        })

                        // Info window
                        const infoWindow = new google.maps.InfoWindow({
                            content: "<div><strong>" + punto.properties.nombre + "</strong><br>" +
                                     "Tipo: " + punto.properties.tipo + "<br>" +
                                     "Monto: $" + punto.properties.monto + "<br>" +
                                     "Fecha: " + punto.properties.fecha + "</div>"
                        })

                        marker.addListener("click", () => {
                            infoWindow.open(map, marker)
                        })

                        bounds.extend(position)
                    })

                    // Dibujar ruta si existe
                    const rutaFeature = geoJsonData.features.find(f => f.geometry.type === "LineString")
                    if (rutaFeature) {
                        const rutaPath = rutaFeature.geometry.coordinates.map(coord => 
                            new google.maps.LatLng(parseFloat(coord[1]), parseFloat(coord[0]))
                        )

                        const ruta = new google.maps.Polyline({
                            path: rutaPath,
                            geodesic: true,
                            strokeColor: rutaFeature.properties.color || "#0000FF",
                            strokeOpacity: 1.0,
                            strokeWeight: 3
                        })

                        ruta.setMap(map)
                    }

                    // Ajustar vista del mapa
                    if (puntos.length > 0) {
                        map.fitBounds(bounds)
                    }
                }

                // Cerrar sesión
                const cerrarSesion = () => {
                    // Limpiar todos los charts antes de cerrar sesión
                    Object.keys(chartInstances).forEach(chartId => {
                        if (chartInstances[chartId]) {
                            chartInstances[chartId].destroy()
                            delete chartInstances[chartId]
                        }
                    })
                    
                    localStorage.removeItem("radar_auth")
                    $("#btnCerrarSesion").hide()
                    $("#accordionDias").html("")
                    mostrarModalLogin()
                }

                // Inicialización
                $(document).ready(() => {
                    $("#loginBtn").click(realizarLogin)
                    $("#usuario, #password").keypress((e) => {
                        if (e.which === 13) realizarLogin()
                    })

                    // Manejo de iconos del accordion
                    $(document).on('show.bs.collapse', '#accordionDias .collapse', function () {
                        // Rotar íconos cuando se abre
                        const button = $(this).prev('.card-header').find('.btn-link')
                        button.find('i').css('transform', 'rotate(180deg)')
                        button.removeClass('collapsed').attr('aria-expanded', 'true')
                    })

                    $(document).on('hide.bs.collapse', '#accordionDias .collapse', function () {
                        // Rotar íconos cuando se cierra
                        const button = $(this).prev('.card-header').find('.btn-link')
                        button.find('i').css('transform', 'rotate(0deg)')
                        button.addClass('collapsed').attr('aria-expanded', 'false')
                    })

                    // Validar token al cargar
                    const token = validarToken()
                    if (token) {
                        $("#btnCerrarSesion").show()
                        cargarDashboard()
                    }
                })
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Radar de Cobranza - Dashboard Día")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('RadarCobranza/rc_dashboard_dia');
    }

    public function Login()
    {
        echo json_encode(RadarCobranzaDao::Login($_POST));
    }

    public function GetResumenCobranza()
    {
        echo json_encode(RadarCobranzaDao::GetResumenCobranza($_POST['token']));
    }

    public function GetRutaCobranza()
    {
        if (!isset($_POST['token'])) {
            echo json_encode(['success' => false, 'mensaje' => 'Token requerido']);
            return;
        }

        echo json_encode(RadarCobranzaDao::GetRutaCobranza($_POST['token'], $_POST));
    }
}
