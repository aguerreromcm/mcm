<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \App\models\Reportes as ReportesDao;
use DateTime;

class Reportes extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function UsuariosMCM()
    {
        $extraHeader = <<<html
        <title>Reporte Usuarios SICAFIN MCM</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
      function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
      
       $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
           "lengthMenu": [
                    [30, 50, -1],
                    [30, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
            
            $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Reportes/generarExcel/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
        });
       

      </script>
html;

        $Consulta = ReportesDao::ConsultaUsuariosSICAFINMCM();
        $tabla = "";

        foreach ($Consulta as $key => $fila) {
            $tabla .= "<tr style='padding: 0px !important;'>";
            foreach ($fila as $key => $columna) {
                if ($key == 'ACTIVO') $columna = self::ValidaSN($columna);
                if ($key == 'PUESTO') $columna = self::QuitaDuplicados($columna);
                if ($key == 'FECHA_ALTA') $columna = self::FechaCompleta($columna);

                $tabla .= "<td style='padding: 0px !important;'>{$columna}</td>";
            }
            $tabla .= "</tr>";
        }
        //         foreach ($Consulta as $key => $value) {

        //             $tabla = <<<html
        //                 <tr style="padding: 0px !important;">
        //                     <td style="padding: 0px !important;">{$value['COD_USUARIO']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMBRE_COMPLETO']}</td>
        //                     <td style="padding: 0px !important;">{$value['FECHA_ALTA']}</td>
        //                     <td style="padding: 0px !important;">{$value['COD_SUCURSAL']}</td>
        //                     <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMINA']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMINA_JEFE']}</td>
        //                     <td style="padding: 0px !important;">{$value['ACTIVO']}</td>
        //                     <td style="padding: 0px !important;">{$value['PUESTO']}</td>
        //                 </tr>
        // html;
        //         }

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::render("Reportes/usuarios_SICAFIN_Reporte");
    }

    public function UsuariosCultiva()
    {
        $extraHeader = <<<html
        <title>Reporte Usuarios SICAFIN Cultiva</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;

        $extraFooter = <<<html
      <script>
      
      function getParameterByName(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
            return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }
      
       $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
           "lengthMenu": [
                    [30, 50, -1],
                    [30, 50, 'Todos'],
                ],
                "columnDefs": [{
                    "orderable": false,
                    "targets": 0
                }],
                 "order": false
            });
            // Remove accented character from search input as well
            $('#muestra-cupones input[type=search]').keyup( function () {
                var table = $('#example').DataTable();
                table.search(
                    jQuery.fn.DataTable.ext.type.search.html(this.value)
                ).draw();
            });
            var checkAll = 0;
            
            
            $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Reportes/generarExcelCultiva/');
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
        });
       

      </script>
html;

        $Consulta = ReportesDao::ConsultaUsuariosSICAFINCultiva();
        $tabla = "";

        foreach ($Consulta as $key => $fila) {
            $tabla .= "<tr style='padding: 0px !important;'>";
            foreach ($fila as $key => $columna) {
                if ($key == 'ACTIVO') $columna = self::ValidaSN($columna);
                if ($key == 'PUESTO') $columna = self::QuitaDuplicados($columna);
                if ($key == 'FECHA_ALTA') $columna = self::FechaCompleta($columna);

                $tabla .= "<td style='padding: 0px !important;'>{$columna}</td>";
            }
            $tabla .= "</tr>";
        }

        //         foreach ($Consulta as $key => $value) {

        //             $tabla = <<<html
        //                 <tr style="padding: 0px !important;">
        //                     <td style="padding: 0px !important;">{$value['COD_USUARIO']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMBRE_COMPLETO']}</td>
        //                     <td style="padding: 0px !important;">{$value['FECHA_ALTA']}</td>
        //                     <td style="padding: 0px !important;">{$value['COD_SUCURSAL']}</td>
        //                     <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMINA']}</td>
        //                     <td style="padding: 0px !important;">{$value['NOMINA_JEFE']}</td>
        //                     <td style="padding: 0px !important;">{$value['ACTIVO']}</td>
        //                     <td style="padding: 0px !important;">{$value['PUESTO']}</td>
        //                 </tr>
        // html;
        //         }

        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::render("Reportes/usuarios_SICAFIN_Reporte_Cultiva");
    }

    public function generarExcel()
    {
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('COD_USUARIO', 'Usuario'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_COMPLETO', 'Nombre'),
            \PHPSpreadsheet::ColumnaExcel('FECHA_ALTA', 'Fecha Alta'),
            \PHPSpreadsheet::ColumnaExcel('COD_SUCURSAL', 'Sucursal'),
            \PHPSpreadsheet::ColumnaExcel('SUCURSAL', 'Nombre Sucursal'),
            \PHPSpreadsheet::ColumnaExcel('NOMINA', 'Nomina'),
            \PHPSpreadsheet::ColumnaExcel('NOMINA_JEFE', 'Jefe'),
            \PHPSpreadsheet::ColumnaExcel('ACTIVO', 'Estatus'),
            \PHPSpreadsheet::ColumnaExcel('PUESTO', 'Puesto')
        ];

        $filas = ReportesDao::ConsultaUsuariosSICAFINMCM();

        \PHPSpreadsheet::DescargaExcel('Reporte de Usuarios SICAFIN MCM', 'Reporte', 'Usuarios MCM', $columnas, $filas);
    }

    public function generarExcelCultiva()
    {
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('COD_USUARIO', 'Usuario'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_COMPLETO', 'Nombre'),
            \PHPSpreadsheet::ColumnaExcel('FECHA_ALTA', 'Fecha Alta'),
            \PHPSpreadsheet::ColumnaExcel('COD_SUCURSAL', 'Sucursal'),
            \PHPSpreadsheet::ColumnaExcel('SUCURSAL', 'Nombre Sucursal'),
            \PHPSpreadsheet::ColumnaExcel('NOMINA', 'Nomina'),
            \PHPSpreadsheet::ColumnaExcel('NOMINA_JEFE', 'Jefe'),
            \PHPSpreadsheet::ColumnaExcel('ACTIVO', 'Estatus'),
            \PHPSpreadsheet::ColumnaExcel('PUESTO', 'Puesto')
        ];

        $filas = ReportesDao::ConsultaUsuariosSICAFINCultiva();

        \PHPSpreadsheet::DescargaExcel('Reporte de Usuarios SICAFIN CULTIVA', 'Reporte', 'Usuarios CULTIVA', $columnas, $filas);
    }

    public function ValidaSN($dato)
    {
        if ($dato == 'S') return 'SI';
        if ($dato == 'N') return 'NO';
        return $dato;
    }

    public function FechaCompleta($fecha)
    {
        $fecha_objeto = DateTime::createFromFormat('d/m/y', $fecha);

        if ($fecha_objeto && $fecha_objeto->format('d/m/y') === $fecha) return $fecha_objeto->format('d/m/Y');
        return $fecha;
    }

    public function QuitaDuplicados($lista)
    {
        $arreglo = explode(",", $lista);
        $arreglo = array_unique($arreglo);
        return implode(", ", $arreglo);
    }
}
