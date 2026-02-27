<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Rep Dia de Atraso</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">
                <div style="margin-bottom: 15px;">
                    <label style="margin-right: 8px;">Desde mes:</label>
                    <select id="filtro_mes" style="margin-right: 15px; padding: 5px;"><?= $options_mes; ?></select>
                    <label style="margin-right: 8px;">Desde año:</label>
                    <select id="filtro_anio" style="margin-right: 15px; padding: 5px;"><?= $options_anio; ?></select>
                    <button id="btn_consultar" type="button" class="btn btn-primary btn-circle"><i class="fa fa-search"></i> Consultar</button>
                </div>
                <div style="margin-bottom: 10px;">
                    <button id="btn_excel" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"></i> <b>Exportar a Excel</b></button>
                    <button id="btn_csv" type="button" class="btn btn-default btn-circle"><i class="fa fa-file-text-o"></i> <b>Exportar a CSV</b></button>
                </div>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-rep-dia-atraso">
                        <thead>
                            <tr>
                                <th>COD_CTE</th>
                                <th>CICLO</th>
                                <th>NOMBRE</th>
                                <th>INICIO</th>
                                <th>DIAS_ATRASO</th>
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
