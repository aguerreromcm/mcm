<?php echo $header;?>
<div class="right_col">
  <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
    <div class="x_panel tile fixed_height_240">
      <div class="x_title">
          <h3> Resumen de cobros realizados por el ejecutivo (<?php echo $CorteCajaById['EJECUTIVO']; ?>)</h3>
      </div>
      <div class="x_content">
          <a href="/Pagos/PagosRegistro/?Credito=003011" type="button" class="btn btn-primary" >
              Agregar Pago
          </a>
          <hr style="border-top: 1px solid #787878; margin-top: 5px;">
          <div class="row" >
              <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                  <div class="col-md-2 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i class="fa fa-calendar"></i> Fechas de Corte</span>

                      <div class="count" style="font-size: 14px"><?php echo $CorteCajaById['CLIENTE']; ?></div>
                  </div>
                  <div class="col-md-2 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i class="fa fa-dollar"></i> Pagos Registrados </span>
                      <div class="count" style="font-size: 20px"><b> <?php echo $CorteCajaById['NUM_PAG']; ?> Pagos </b></div>
                  </div>
                  <div class="col-md-2 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i></i><i class="fa fa-dollar"></i> Monto Total</span>
                      <div class="count" style="font-size: 20px">  <b>$ <?php echo number_format($CorteCajaById['MONTO_TOTAL']); ?></b></div>
                  </div>
                  <div class="col-md-2 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i></i><i class="fa fa-dollar"></i> Total a Pagos</span>
                      <div class="count" style="font-size: 20px">  <b>$ <?php echo number_format($CorteCajaById['MONTO_PAGO']); ?></b></div>
                  </div>
                  <div class="col-md-2 col-sm-4  tile_stats_count">
                      <span class="count_top" style="font-size: 15px"><i></i><i class="fa fa-dollar"></i> Monto a Garantias</span>
                      <div class="count" style="font-size: 20px"> <b>$ <?php echo number_format($CorteCajaById['MONTO_GARANTIA']); ?></b></div>
                  </div>
                  <div class="col-md-2 col-sm-4  tile_stats_count">
                      <button style="background: #109d0e !important; border-radius: 25px;" type="submit" name="agregar" class="btn btn-success btn-lg" value="enviar" onclick="FunprecesarPagos()"><span class="fa fa-check"></span> Procesar Pagos</button>
                  </div>


              </div>
          </div>

          <div class="form-group ">
              <div class="panel-body">
                  <div class="dataTable_wrapper">
                      <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                          <thead>
                          <tr>
                              <th>Medio</th>
                              <th>Fecha</th>
                              <th>CDGNS</th>
                              <th>Nombre Cliente</th>
                              <th>Ciclo</th>
                              <th>Tipo de Pago</th>
                              <th>Monto</th>
                              <th>Estatus</th>
                              <th>Ejecutivo</th>
                              <th>Acciones</th>
                          </tr>
                          </thead>
                          <tbody>
                          <?= $tabla; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_editar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Editar Registro de Pago (App Móvil)</h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="Fecha">Fecha</label>
                                    <input type="text" class="form-control" id="Fecha" aria-describedby="Fecha" disabled placeholder="" value="<?php echo $fechaActual; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Fecha de registro en la app.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="movil">Medio de Registro</label>
                                    <input type="text" class="form-control" id="movil" aria-describedby="movil" disabled placeholder="" value="MÓVIL">
                                    <small id="emailHelp" class="form-text text-muted">Medio de registro del pago.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cdgns">CDGNS</label>
                                    <input type="number" class="form-control" id="cdgns" name="cdgns" readonly>
                                    <small id="emailHelp" class="form-text text-muted">Número del crédito.</small>
                                </div>
                            </div>

                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" readonly>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ciclo">Ciclo</label>
                                    <input type="number" class="form-control" id="ciclo" name="ciclo" readonly>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pago">Tipo de Operación</label>
                                    <input type="text" class="form-control" id="pago" name="pago" readonly>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="estatus">Estatus</label>
                                    <input type="text" class="form-control" id="estatus" name="estatus" readonly>
                                </div>
                            </div>





                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="monto">Monto *</label>
                                    <input type="text" class="form-control" id="monto" name="monto">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="monto">Motivo de la Incidencia *</label>
                                    <textarea class="form-control" id="incidencia" name="incidencia"></textarea>
                                </div>
                            </div>



                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo">Nombre del Ejecutivo</label>
                                    <input type="text" class="form-control" id="ejecutivo" name="ejecutivo" readonly>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                </form>
            </div>

        </div>
    </div>
</div>
<script>
    function EditarPago(fecha, cdgns, nombre, ciclo, tipo_pago, monto, estatus, ejecutivo)
    {
        document.getElementById("Fecha").value = fecha;
        document.getElementById("cdgns").value = cdgns;
        document.getElementById("nombre").value = nombre;
        document.getElementById("ciclo").value = ciclo;
        document.getElementById("pago").value = tipo_pago;
        document.getElementById("monto").value = monto;
        document.getElementById("estatus").value = estatus;
        document.getElementById("ejecutivo").value = ejecutivo;
        $('#modal_editar_pago').modal('show');
    }
</script>

<?php echo $footer;?>
