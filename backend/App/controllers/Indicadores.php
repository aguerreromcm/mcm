<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\Controller;
use Core\View;
use App\models\Indicadores as IndicadoresDao;

class Indicadores extends Controller
{
    private $_contenedor;
    private $graficas = '<script src="/js/chart.min.js"></script>';

    public function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
    }

    public function ProductividadOP()
    {
        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                
                {$this->configuraTabla}
                {$this->consultaServidor}
                {$this->formatoMoneda}
                {$this->descargaExcel}
                
                const datosGraficoIncidencias = {
                    labels: [],
                    datasets: [
                        {
                            label: "Sin datos",
                            data: []
                        }
                    ]
                }
                const graficoIncidencias = new Chart(document.getElementById("chrtIncidencias"), {
                    type: "line",
                    data: datosGraficoIncidencias,
                    options: {
                        hoverRadius: 10,
                        responsive: true,
                        interaction: {
                            mode: "nearest",
                            intersect: false
                        },
                        stacked: false,
                        plugins: {
                            legend: {
                                position: "right"
                            }
                        }
                    }
                })

                const datosGraficoConteo = {
                    labels: [],
                    datasets: [
                        {
                            label: "Sin datos",
                            data: []
                        }
                    ]
                }
                const graficoConteo = new Chart(document.getElementById("chrtUConteo"), {
                    type: "bar",
                    data: datosGraficoConteo,
                    options: {
                        responsive: true,
                        interaction: {
                            mode: "nearest",
                            intersect: false
                        },
                        stacked: false,
                        plugins: {
                            title: {
                                display: true,
                                text: "Incidencias"
                            },
                            legend: {
                                display: false
                            }
                        }
                    }
                })
                const datosGraficoMonto = {
                    labels: [],
                    datasets: [
                        {
                            label: "Sin datos",
                            data: []
                        }
                    ]
                }
                const graficoMonto = new Chart(document.getElementById("chrtMonto"), {
                    type: "bar",
                    data: datosGraficoMonto,
                    options: {
                        responsive: true,
                        interaction: {
                            mode: "nearest",
                            intersect: false
                        },
                        stacked: false,
                        plugins: {
                            title: {
                                display: true,
                                text: "Monto"
                            },
                            legend: {
                                display: false
                            }
                        }
                    }
                })

                $(document).ready(() => {
                    configuraTabla("tblIncidencias")

                    consultaServidor("/Indicadores/GetIncidenciasUsuarios/", {}, (resultado) => {
                        if (!resultado.success) showError(resultado.mensaje)

                        actualizaTablaIncidencias(resultado.datos)
                        actualizaGraficoInicidencias(resultado.datos)
                    })

                    $("#btnDescargaExcel").on("click", getExcelDetalle)

                    $("#detalleUsuario").on("hidden.bs.modal", () => {
                        actualizaDetalleUsuario([])
                        actualizaTablaUsuario([])
                    })
                })

                const getPeriodos = () => {
                    const periodos = []
                    const fechaActual = new Date()

                    for (let i = 0; i < 13; i++) {
                        const fecha = new Date(fechaActual.getFullYear(), fechaActual.getMonth() - i, 1)
                        const mes = fecha.toLocaleString("default", { month: "long" })
                        const ano = fecha.getFullYear()

                        periodos.push({
                            mes: fecha.getMonth() + 1,
                            mesLetra: mes,
                            ano: ano
                        })
                    }

                    return periodos.reverse()
                }

                const getTD = (texto) => {
                    const td = document.createElement("td")
                    td.innerText = texto
                    return td
                }

                const getAcciones = (usuario, ano, mes, nombre) => {
                    const td = document.createElement("td")
                    const btn = document.createElement("button")
                    const i = document.createElement("i")

                    btn.classList.add("btn", "btn-primary", "btn-xs")
                    btn.onclick = () => verDetalle(usuario, ano, mes, nombre)

                    i.classList.add("fa", "fa-eye")

                    btn.appendChild(i)
                    td.appendChild(btn)
                    return td
                }

                const actualizaTablaIncidencias = (datos) => {
                    $("#tblIncidencias").DataTable().destroy()
                    $("#tblIncidencias tbody").empty()

                    datos.forEach((incidencia) => {
                        const tr = document.createElement("tr")
                        tr.appendChild(getTD(incidencia.ANO))
                        tr.appendChild(getTD(incidencia.MES_LETRA))
                        tr.appendChild(getTD(incidencia.CDGPE))
                        tr.appendChild(getTD(incidencia.NOMBRE))
                        tr.appendChild(getTD(incidencia.TOTAL_INCIDENCIAS))
                        tr.appendChild(
                            getAcciones(incidencia.CDGPE, incidencia.ANO, incidencia.MES, incidencia.NOMBRE)
                        )

                        $("#tblIncidencias tbody").append(tr)
                    })

                    configuraTabla("tblIncidencias")
                }

                const actualizaGraficoInicidencias = (datos) => {
                    datosGraficoIncidencias.labels = []
                    datosGraficoIncidencias.datasets = []

                    if (datos.length === 0) {
                        datosGraficoIncidencias.datasets.push({
                            label: "Sin datos",
                            data: []
                        })
                    } else {
                        const periodos = getPeriodos()
                        const usuarios = [...new Set(datos.map((incidencia) => incidencia.CDGPE))]

                        periodos.forEach((periodo) => {
                            datosGraficoIncidencias.labels.push(periodo.mesLetra + " " + periodo.ano)

                            usuarios.forEach((usuario) => {
                                const incidencias = datos.filter(
                                    (incidencia) =>
                                        incidencia.CDGPE === usuario &&
                                        incidencia.ANO == periodo.ano &&
                                        incidencia.MES == periodo.mes
                                )
                                const totalIncidencias =
                                    incidencias.length > 0 ? incidencias[0].TOTAL_INCIDENCIAS : 0

                                if (
                                    datosGraficoIncidencias.datasets.filter((dataset) => dataset.label === usuario)
                                        .length === 0
                                ) {
                                    datosGraficoIncidencias.datasets.push({
                                        label: usuario,
                                        data: []
                                    })
                                }

                                datosGraficoIncidencias.datasets
                                    .find((dataset) => dataset.label === usuario)
                                    .data.push(totalIncidencias)
                            })
                        })
                    }

                    graficoIncidencias.update()
                }

                const verDetalle = (usuario, ano, mes, nombre) => {
                    const mesLetra = new Date(ano, mes - 1, 1).toLocaleString("default", { month: "long" })
                    $("#ttlNombre").html("<b>Total de incidencias atendidas por " + nombre + " en " + mesLetra + " de " + ano + "</b>")
                    $("#detalleUsuario").modal("show")  

                    const datos = {
                        usuario,
                        fechaI: new Date(ano, mes - 1, 1).toISOString().split("T")[0],
                        fechaF: new Date(ano, mes, 0).toISOString().split("T")[0]
                    }

                    consultaServidor("/Indicadores/GetIncidenciasUsuario/", datos, (resultado) => {
                        if (!resultado.success) showError(resultado.mensaje)

                        $("#xsl_usuario").val(datos.usuario)
                        $("#xsl_fechaI").val(datos.fechaI)
                        $("#xsl_fechaF").val(datos.fechaF) 
                        actualizaDetalleUsuario(resultado.datos)
                        actualizaTablaUsuario(resultado.datos)
                    })
                }

                const actualizaDetalleUsuario = (datos) => {
                    datosGraficoConteo.labels = []
                    datosGraficoConteo.datasets = []
                    datosGraficoMonto.labels = []
                    datosGraficoMonto.datasets = []

                    if (datos.length === 0) {
                        datosGraficoConteo.datasets.push({
                            label: "Sin datos",
                            data: []
                        })
                    } else {
                        const etiquetas = []
                        const conteo = []
                        const totales = []
                        datos.forEach((incidencia) => {
                            const fecha = incidencia.FECHA.split(" ")[0]
                            if (!etiquetas.includes(fecha)) {
                                etiquetas.push(fecha)
                                conteo.push(1)
                                totales.push(parseFloat(incidencia.MONTO))
                            } else {
                                const idx = etiquetas.indexOf(fecha)
                                conteo[idx]++
                                totales[idx] += parseFloat(incidencia.MONTO)
                            }
                        })

                        etiquetas.reverse()
                        conteo.reverse()
                        totales.reverse()

                        datosGraficoConteo.labels = etiquetas
                        datosGraficoConteo.datasets.push({
                            label: "Incidencias",
                            data: conteo,
                            borderColor: "green",
                            backgroundColor: "rgba(0, 255, 0, 0.5)",
                            borderWidth: 2,
                            borderRadius: 5,
                            borderSkipped: false
                        })

                        datosGraficoMonto.labels = etiquetas
                        datosGraficoMonto.datasets.push({
                            label: "Monto",
                            data: totales,
                            borderColor: "yellow",
                            backgroundColor: "rgba(255, 255, 0, 0.5)",
                            borderWidth: 2,
                            borderRadius: 5,
                            borderSkipped: false
                        })
                    }

                    graficoConteo.update()
                    graficoMonto.update()
                }

                const actualizaTablaUsuario = (datos) => {
                    $("#tblUsuario").DataTable().destroy()
                    $("#tblUsuario tbody").empty()

                    datos.forEach((incidencia) => {
                        const tr = document.createElement("tr")
                        tr.appendChild(getTD(incidencia.FECHA))
                        tr.appendChild(getTD(incidencia.CDGNS))
                        tr.appendChild(getTD(incidencia.CICLO))
                        tr.appendChild(getTD("$ " + formatoMoneda(incidencia.MONTO)))
                        tr.appendChild(getTD(incidencia.DESCRIPCION))
                        tr.appendChild(getTD(incidencia.TIPO))
                        tr.appendChild(getTD(incidencia.REGION))
                        tr.appendChild(getTD(incidencia.SUCURSAL))

                        $("#tblUsuario tbody").append(tr)
                    })

                    configuraTabla("tblUsuario")
                }

                const getExcelDetalle = () => {
                    const datos = {
                        usuario: $("#xsl_usuario").val(),
                        fechaI: $("#xsl_fechaI").val(),
                        fechaF: $("#xsl_fechaF").val()
                    }
                    const params = new URLSearchParams(datos).toString()
                    descargaExcel("/Indicadores/GetExcelIncidenciasUsuario/?" + params)
                }
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->GetExtraHeader('Productividad Operaciones', [$this->graficas])));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::render('Indicadores/indicadores_productividadOP');
    }

    public function GetIncidenciasUsuarios()
    {
        echo json_encode(IndicadoresDao::GetIncidenciasUsuarios());
    }

    public function GetIncidenciasUsuario()
    {
        echo json_encode(IndicadoresDao::GetIncidenciasUsuario($_POST));
    }

    public function GetExcelIncidenciasUsuario()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('FECHA', 'Fecha', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('CDGNS', 'Crédito', ['estilo' => $estilos['centrado']]),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'Ciclo', ['estilo' => $estilos['centrado']]),
            \PHPSpreadsheet::ColumnaExcel('MONTO', 'Monto', ['estilo' => $estilos['moneda']]),
            \PHPSpreadsheet::ColumnaExcel('DESCRIPCION', 'Descripción'),
            \PHPSpreadsheet::ColumnaExcel('TIPO', 'Tipo'),
            \PHPSpreadsheet::ColumnaExcel('REGION', 'Región'),
            \PHPSpreadsheet::ColumnaExcel('SUCURSAL', 'Sucursal')
        ];

        $filas = IndicadoresDao::GetIncidenciasUsuario($_GET);
        $filas = $filas['success'] ? $filas['datos'] : [];
        $filas = array_map(function ($fila) {
            $fila['FECHA'] = \DateTime::createFromFormat('d/m/y', $fila['FECHA'])->format('d/m/Y');
            return $fila;
        }, $filas);

        \PHPSpreadsheet::DescargaExcel("Incidencias de {$_GET['usuario']}", 'Incidencias', 'Reporte de Incidencias', $columnas, $filas);
    }
}
