<?php

namespace App\controllers;

defined("APPPATH") or die("Access denied");

use \Core\View;
use \Core\Controller;
use \Core\MasterDom;
use \App\models\Operaciones as OperacionesDao;

class Cultiva extends Controller
{
    private $_contenedor;

    function __construct()
    {
        parent::__construct();
        $this->_contenedor = new Contenedor;
        View::set('header', $this->_contenedor->header());
        View::set('footer', $this->_contenedor->footer());
    }

    public function generarExcel()
    {
        $columnas = [
            \PHPSpreadsheet::ColumnaExcel('SUCURSAL', 'Sucursal'),
            \PHPSpreadsheet::ColumnaExcel('NOMBRE_GRUPO', 'Grupo'),
            \PHPSpreadsheet::ColumnaExcel('CLIENTE', 'Cliente'),
            \PHPSpreadsheet::ColumnaExcel('DOMICILIO', 'Domicilio'),
        ];

        $fecha = date('Y-m-d');
        $fecha1 = $_GET['Inicial'];
        $fecha2 = $_GET['Final'];
        if ($fecha1 != '') $filas = OperacionesDao::ConsultaGruposCultiva($fecha1, $fecha2);
        else $filas = OperacionesDao::ConsultaGruposCultiva($fecha, $fecha);

        \PHPSpreadsheet::DescargaExcel('Cultiva Reporte Clientes', 'Reporte', 'Solicitudes Cultiva', $columnas, $filas);
    }

    public function index()
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

        if ($Fecha != '') {
            $Consulta = OperacionesDao::ConsultaGruposCultiva($Fecha, $FechaFinal);
            $tabla = '';

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
            $tabla = '';

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

    public function ReingresarClientesCredito()
    {
        $extraHeader = <<<html
        <title>Reingresar Clientes Cultiva</title>
        <link rel="shortcut icon" href="/img/logo.png">
html;
        $extraFooter = <<<html
      <script>
       
       ponerElCursorAlFinal('Credito');
       
       function ActivarCredito(cdgcl, fecha, motivo){	
           
            if(motivo == '')
                {
                     swal("Atención", "Ingrese un monto mayor a $0", "warning");
                     document.getElementById("monto_e").focus();
                  
                }
            else
                {
                    $.ajax({
                    type: 'POST',
                    url: '/Cultiva/ReactivarCredito/',
                    data: "cdgcl="+cdgcl,
                    success: function(respuesta) {
                         if(respuesta=='1'){
                    
                                swal("Registro guardado exitosamente", {
                                      icon: "success",
                                    });
                        location.reload();
                        }else 
                            {
                                swal(respuesta, {
                                      icon: "error",
                                    });
                            }
                    }
                    });
                }
    }
    
       $(document).ready(function(){
            $("#muestra-cupones").tablesorter();
          var oTable = $('#muestra-cupones').DataTable({
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
            
        });
      
      </script>
html;

        $credito = $_GET['Credito'];

        if ($credito != '') {

            $Clientes = OperacionesDao::ReingresarClientesCredito($credito);
            $tabla = '';

            foreach ($Clientes[0] as $key => $value) {

                $tabla .= <<<html
                <tr style="padding: 0px !important;">
                    <td style="padding: 10px !important;">{$value['CDGNS']}</td>
                    <td style="padding: 10px !important;">{$value['CDGCL']}</td>
                    <td style="padding: 10px !important;">{$value['NOMBRE_CLIENTE']}</td>
                    <td style="padding: 10px !important;">{$value['FECHA_BAJA']}</td>
                    <td style="padding: 10px !important;">{$value['MOTIVO_BAJA']}</td>
                    <td> <button type="button" class="btn btn-danger btn-circle" onclick="ActivarCredito('{$value['CDGCL']}', '{$value['FECHA_BAJA_REAL']}', '{$value['CODIGO_MOTIVO']}');"><i class="fa fa-check"></i></button></td>
                </tr>
html;
            }
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::set('tabla', $tabla);
            View::set('Nombre', $Clientes[1]['NOMBRE']);
            View::render("Cultiva/reingresar_clientes_cultiva_sec");
        } else {
            View::set('header', $this->_contenedor->header($extraHeader));
            View::set('footer', $this->_contenedor->footer($extraFooter));
            View::render("Cultiva/reingresar_clientes_cultiva_ini");
        }
    }

    public function ReactivarCredito()
    {
        $cliente = new \stdClass();

        $cdgcl = MasterDom::getDataAll('cdgcl');
        $cliente->_cdgcl = $cdgcl;


        // $id = OperacionesDao::updateCliente($cliente);
        // return $id;
    }
}
