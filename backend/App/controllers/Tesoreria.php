<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\Tesoreria as TesoreriaDao;

class Tesoreria extends Controller
{

    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

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
                    consultaServidor("/Tesoreria/GetReportePC", getPerametros(), (res) => {
                        if (!res.success) return resultadoError(res.mensaje)
                        resultadoOK(res.datos)
                    })
                }

                const getPerametros = () => {
                    const fechaI = $("#fechaI").val()
                    const fechaF = $("#fechaF").val()
                    const sucursal = $("#sucursal").val()

                    return { fechaI, fechaF, sucursal }
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
                    descargaExcel("/Tesoreria/GetReportePC_excel/?" + $.param(getPerametros()))
                }

                $(document).ready(() => {
                    $("#fechaI").change(consultaReporte)
                    $("#fechaF").change(consultaReporte)
                    $("#sucursal").change(consultaReporte)
                    $("#excel").click(getExcel)

                    configuraTabla(idTabla)
                    consultaReporte()
                })
            </script>
        HTML;

        $suc = TesoreriaDao::GetSucursales();
        $sucursales = '<option value="*">Todas</option>';
        if ($suc['success']) {
            foreach ($suc['datos'] as $sucursal) {
                $sucursales .= '<option value="' . $sucursal['ID_SUCURSAL'] . '">' . $sucursal['SUCURSAL'] . '</option>';
            }
        }

        View::set('header', $this->_contenedor->header($this->getExtraHeader("Reporte Productora Cultiva")));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('sucursales', $sucursales);
        View::render('Tesoreria/tesoreria_reporte_pc');
    }

    public function GetReportePC($datos = null)
    {
        echo json_encode(TesoreriaDao::GetReportePC($_POST));
    }

    public function GetReportePC_excel()
    {
        $estilos = \PHPSpreadsheet::GetEstilosExcel();
        $centrado = ['estilo' => $estilos['centrado']];
        $texto = ['estilo' => $estilos['texto_centrado']];

        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('FECHA_SOLICITUD', 'Fecha de solicitud', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('CREDITO', 'Crédito', $texto),
            \PHPSpreadsheet::ColumnaExcel('CICLO', 'Ciclo', $texto),
            \PHPSpreadsheet::ColumnaExcel('CLIENTE', 'Cliente', $texto),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_CLIENTE', 'Nombre cliente', $texto),
            \PHPSpreadsheet::ColumnaExcel('RFC', 'RFC', $centrado),
            \PHPSpreadsheet::ColumnaExcel('FECHA_INICIO', 'Fecha inicio', ['estilo' => $estilos['fecha']]),
            \PHPSpreadsheet::ColumnaExcel('TIPO_OPERACION', 'Tipo operación', ['estilo' => $estilos['centrado'], 'total' => true, 'operacion' => 'CONTARA']),
            \PHPSpreadsheet::ColumnaExcel('SUCURSAL', 'Sucursal'),
            \PHPSpreadsheet::ColumnaExcel('REGION', 'Región'),
            \PHPSpreadsheet::ColumnaExcel('MONTO', 'Monto', ['estilo' => $estilos['moneda'], 'total' => true]),
            \PHPSpreadsheet::ColumnaExcel('BANCO', 'Banco'),
            \PHPSpreadsheet::ColumnaExcel('CLABE', 'CLABE', $texto),
        ];

        $filas = TesoreriaDao::GetReportePC($_GET);
        $filas = $filas['success'] ? $filas['datos'] : [];

        \PHPSpreadsheet::DescargaExcel('Reporte Productora Cultiva', 'Reporte', 'Créditos solicitados para Productora Cultiva', $columnas, $filas);
    }
}
