<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Corte de Caja</h3>
                <div class="clearfix"></div>
            </div>

        <div class="panel-body">
          <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
              <thead>
                <tr>
                  <th>Medio</th>
                  <th>Pagos Registrados</th>
                  <th><Nombre del Ejecutivo</th>
                  <th>Monto Total</th>
                  <th>Total a Pagos</th>
                  <th>Total a Garantía</th>
                  <th>Total a Descuentos</th>
                  <th>Total a Refinanciamiento</th>
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
<?php echo $footer; ?>
