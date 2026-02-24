<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Auditoría de Devengos</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">
                <div style="margin-bottom: 15px;">
                    <label style="margin-right: 8px;">Crédito:</label>
                    <input type="text" id="filtro_credito" placeholder="Crédito" maxlength="20" style="margin-right: 15px; padding: 5px; width: 100px;">
                    <label style="margin-right: 8px;">Ciclo:</label>
                    <input type="text" id="filtro_ciclo" placeholder="Ciclo" maxlength="20" style="margin-right: 15px; padding: 5px; width: 80px;">
                    <label style="margin-right: 8px;">Fecha desde:</label>
                    <input type="date" id="filtro_fecha_desde" style="margin-right: 15px; padding: 5px;">
                    <label style="margin-right: 8px;">Fecha hasta:</label>
                    <input type="date" id="filtro_fecha_hasta" style="margin-right: 15px; padding: 5px;">
                    <button id="btn_consultar" type="button" class="btn btn-primary btn-circle"><i class="fa fa-search"></i> Consultar</button>
                </div>
                <div style="margin-bottom: 10px;">
                    <button id="btn_procesar_masivo" type="button" class="btn btn-warning btn-circle" disabled><i class="fa fa-cogs"></i> <b>Procesar todos los filtrados</b></button>
                </div>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-devengos-faltantes">
                        <thead>
                            <tr>
                                <th>CREDITO</th>
                                <th>CICLO</th>
                                <th>FECHA_FALTANTE</th>
                                <th>FECHA_CALC</th>
                                <th>NOMBRE</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
