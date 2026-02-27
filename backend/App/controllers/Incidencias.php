<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use Core\View;
use Core\Controller;
use App\models\Incidencias as IncidenciasDao;
use App\models\Operaciones as OperacionesDao;

class Incidencias extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;



        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }


    public function AutorizaRechazaSolicitud()
    {
        $extraHeader = <<<html
        <title>Consulta Altas Grupo Cultiva</title>
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
            
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
            $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Cultiva/generarExcel/?Inicial='+fecha1 + '&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
        });
       

      </script>
html;


        $fechaActual = date('Y-m-d');
        $Fecha = $_GET['Inicial'];
        $FechaFinal = $_GET['Final'];
        $tabla = '';

        if ($Fecha != '') {
            $Consulta = OperacionesDao::ConsultaGruposCultiva($Fecha, $FechaFinal);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                    <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_GRUPO']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">{$value['CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['DOMICILIO']}</td>
                    <td style="padding: 0px !important;">{$value['SOLICITUD']}</td>
                </tr>
html;
                View::set('Inicial', $Fecha);
                View::set('Final', $FechaFinal);
            }
        } else {
            $Consulta = OperacionesDao::ConsultaGruposCultiva($fechaActual, $fechaActual);

            foreach ($Consulta as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                   <td style="padding: 0px !important;">{$value['SUCURSAL']}</td>
                   <td style="padding: 0px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 0px !important;">{$value['NOMBRE_GRUPO']}</td>
                    <td style="padding: 0px !important;">{$value['CICLO']}</td>
                    <td style="padding: 0px !important;">{$value['CLIENTE']}</td>
                    <td style="padding: 0px !important;">{$value['DOMICILIO']}</td>
                    <td style="padding: 0px !important;">{$value['SOLICITUD']}</td>
                </tr>
html;
            }
            View::set('Inicial', date("Y-m-d"));
            View::set('Final', date("Y-m-d"));
        }
        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
        View::set('tabla', $tabla);
        View::render("Cultiva/zz_cultiva_consulta_clientes");
    }

    public function CancelarRefinanciamiento()
    {
        $extraHeader = <<<html
        <title>Consulta Altas Grupo Cultiva</title>
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
            
            
            fecha1 = getParameterByName('Inicial');
            fecha2 = getParameterByName('Final');
            
            $("#export_excel_consulta").click(function(){
              $('#all').attr('action', '/Cultiva/generarExcel/?Inicial='+fecha1 + '&Final='+fecha2);
              $('#all').attr('target', '_blank');
              $("#all").submit();
            });
             
        });
       

      </script>
html;


        $fechaActual = date('Y-m-d');



        /// 1.- Validar en tabla SN que no tengamos SITUACION != A - ok
        /// 2.- Validar con un COUNT la tabla de DEVENGO_DIARIO con la fecha actual y comparar si es mayor a 1000, se realizo el cierre
        /// 3.- Obtener el ultimo refinanciamiento de la tabla PAGOSDIA
        /// 4.- Actualizar el registro de la tabla PAGOSDIA CON SITUACION E

        /// 5.- Obtener la fecha de liquidacion de la tabla TBL_CIERRE_DIA en donde la columna FECHA_LIQUIDA IS NOT NULL
        /// 6.- Actualizar la columna FECHA_LIQUIDA A NULL
        /// 7.- Ciclo For para generar insert a la tabla DEVENGO_DIARIO
        /// 8.- Si las consultas anteriores son OK, proceder a realizar el commit


        $Proceso = IncidenciasDao::ProcesoCancelarRefinanciamiento('030195');

        var_dump($Proceso[0]['EXISTE']);
        if ($Proceso[0]['EXISTE'] >= 1) {
            echo "El cliente tiene una solicitud activa, solicite borrarla";
        }


        //////////////////
        if ($Proceso[1]['DEVENGO_DIARIO'] >= 2500) {
            echo "<br>El cierre del dia de hoy ya se realizo";
        } else {
            echo "<br>El cierre no se ha realizado";
        }
        //////////////////
        if ($Proceso[1]['DEVENGO_DIARIO'] >= 2500) {
            echo "<br>El cierre del dia de hoy ya se realizo";
        } else {
            echo "<br>El cierre no se ha realizado";
        }


        View::set('header', $this->_contenedor->header($extraHeader));
        View::set('footer', $this->_contenedor->footer($extraFooter));
    }
}
