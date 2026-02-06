<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\Herramientas as HerramientasDao;

class Herramientas extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    /**
     * Vista del reporte Día de Atraso (tabla + botones Excel y CSV).
     * Los datos se cargan por AJAX. Opcional: filtrar desde mes y año.
     */
    public function RepDiaAtraso()
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $anio_actual = (int) date('Y');
        $options_mes = '<option value="">Todos</option>';
        foreach ($meses as $n => $nombre) {
            $options_mes .= '<option value="' . $n . '">' . $nombre . '</option>';
        }
        $options_anio = '<option value="">Todos</option>';
        for ($a = $anio_actual; $a >= $anio_actual - 15; $a--) {
            $options_anio .= '<option value="' . $a . '">' . $a . '</option>';
        }

        $extraFooter = <<<HTML
            <script>
                {$this->mensajes}
                {$this->configuraTabla}

                const idTablaRepDia = "muestra-rep-dia-atraso";

                function consultarRepDiaAtraso() {
                    var mes = $("#filtro_mes").val();
                    var anio = $("#filtro_anio").val();
                    var params = [];
                    if (mes) params.push("mes=" + mes);
                    if (anio) params.push("anio=" + anio);
                    var url = "/Herramientas/GetRepDiaAtraso/" + (params.length ? "?" + params.join("&") : "");

                    swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
                    $.ajax({
                        type: "GET",
                        url: url,
                        timeout: 600000,
                        success: function(res) {
                            swal.close();
                            try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { showError("Error al procesar la respuesta"); actualizaDatosTabla(idTablaRepDia, []); return; }
                            if (!res.success) {
                                showError(res.mensaje || "Error al cargar el reporte");
                                actualizaDatosTabla(idTablaRepDia, []);
                                return;
                            }
                            actualizaDatosTabla(idTablaRepDia, res.datos || []);
                        },
                        error: function() {
                            swal.close();
                            showError("La consulta tardó demasiado o hubo un error. Tiempo máximo: 10 minutos.");
                            actualizaDatosTabla(idTablaRepDia, []);
                        }
                    });
                }

                $(document).ready(function(){
                    $("#" + idTablaRepDia).DataTable({
                        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
                        order: [[4, "desc"]],
                        language: {
                            emptyTable: "Seleccione filtros y pulse Consultar para cargar el reporte.",
                            paginate: { previous: "Anterior", next: "Siguiente" },
                            info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                            infoEmpty: "Sin registros",
                            zeroRecords: "No se encontraron registros",
                            lengthMenu: "Mostrar _MENU_ registros",
                            search: "Buscar:"
                        }
                    });

                    $("#btn_consultar").click(consultarRepDiaAtraso);

                    $("#btn_excel").click(function(){
                        var mes = $("#filtro_mes").val();
                        var anio = $("#filtro_anio").val();
                        var qs = (mes || anio) ? "?" + [mes ? "mes=" + mes : "", anio ? "anio=" + anio : ""].filter(Boolean).join("&") : "";
                        window.open("/Herramientas/RepDiaAtraso_excel/" + qs, "_blank");
                    });
                    $("#btn_csv").click(function(){
                        var mes = $("#filtro_mes").val();
                        var anio = $("#filtro_anio").val();
                        var qs = (mes || anio) ? "?" + [mes ? "mes=" + mes : "", anio ? "anio=" + anio : ""].filter(Boolean).join("&") : "";
                        window.location.href = "/Herramientas/RepDiaAtraso_csv/" + qs;
                    });
                });
            </script>
        HTML;

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Rep Dia de Atraso")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', '');
        View::set('options_mes', $options_mes);
        View::set('options_anio', $options_anio);
        View::render('herramientas_rep_dia_atraso');
    }

    /**
     * Devuelve el reporte en JSON para la carga por AJAX.
     * Parámetros opcionales GET: mes (1-12), anio (ej. 2025).
     * Tiempo máximo de ejecución: 10 minutos (para consultas pesadas).
     */
    public function GetRepDiaAtraso()
    {
        set_time_limit(600);
        $datos = array_filter([
            'mes'  => isset($_GET['mes']) ? $_GET['mes'] : null,
            'anio' => isset($_GET['anio']) ? $_GET['anio'] : null,
        ]);
        echo json_encode(HerramientasDao::GetRepDiaAtraso($datos));
    }

    /**
     * Descarga del reporte en Excel.
     */
    public function RepDiaAtraso_excel()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $centrado = ['estilo' => $estilos['centrado']];

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('COD_CTE', 'Código Cliente', $centrado),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'Ciclo', $centrado),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE', 'Nombre'),
            \PHPSpreadsheet::ColumnaExcel('INICIO', 'Inicio', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('DIAS_ATRASO', 'Días Atraso', $centrado),
        ];

        $datos = array_filter([
            'mes'  => isset($_GET['mes']) ? $_GET['mes'] : null,
            'anio' => isset($_GET['anio']) ? $_GET['anio'] : null,
        ]);
        $resultado = HerramientasDao::GetRepDiaAtraso($datos);
        $filas = ($resultado['success'] && isset($resultado['datos'])) ? $resultado['datos'] : [];

        \PHPSpreadsheet::DescargaExcel('Rep Dia de Atraso', 'Reporte', 'Días de atraso', $columnas, $filas);
    }

    /**
     * Descarga del reporte en CSV.
     * Parámetros opcionales GET: mes (1-12), anio (ej. 2025).
     */
    public function RepDiaAtraso_csv()
    {
        $datos = array_filter([
            'mes'  => isset($_GET['mes']) ? $_GET['mes'] : null,
            'anio' => isset($_GET['anio']) ? $_GET['anio'] : null,
        ]);
        $resultado = HerramientasDao::GetRepDiaAtraso($datos);
        $filas = ($resultado['success'] && isset($resultado['datos'])) ? $resultado['datos'] : [];

        $nombre = 'rep_dia_atraso_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

        fputcsv($out, ['COD_CTE', 'CICLO', 'NOMBRE', 'INICIO', 'DIAS_ATRASO'], ',');
        foreach ($filas as $fila) {
            fputcsv($out, [
                $fila['COD_CTE'] ?? '',
                $fila['CICLO'] ?? '',
                $fila['NOMBRE'] ?? '',
                $fila['INICIO'] ?? '',
                $fila['DIAS_ATRASO'] ?? '',
            ], ',');
        }
        fclose($out);
        exit;
    }
}
