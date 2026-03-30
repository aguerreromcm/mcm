<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Lista Negra (empleados)</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">
                <p class="text-muted" style="margin-bottom: 15px;">
                    Registro de CURP de empleados o exempleados que no deben figurar como cliente o aval.
                </p>

                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-6">
                        <h4 style="margin-top: 0;">Alta manual</h4>
                        <div class="form-inline" style="margin-bottom: 10px;">
                            <label for="curp_manual" class="sr-only">CURP</label>
                            <input type="text" id="curp_manual" class="form-control" maxlength="18" placeholder="CURP (18 caracteres)" style="min-width: 220px; text-transform: uppercase;" autocomplete="off">
                            <button type="button" id="btn_guardar_curp" class="btn btn-primary" style="margin-left: 8px;"><i class="fa fa-save"></i> Guardar</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4 style="margin-top: 0;">Carga masiva</h4>
                        <p class="small text-muted" style="margin-bottom: 8px; line-height: 1.45;">
                            Coloque un <strong>CURP válido</strong> por fila en la <strong>primera columna (A)</strong>: exactamente <strong>18 caracteres</strong> alfanuméricos (incluye la letra Ñ si aplica).
                            Puede dejar la fila 1 como encabezado con el texto <strong>CURP</strong>; las filas de ayuda del layout se ignoran.
                        </p>
                        <div class="form-inline" style="margin-bottom: 8px;">
                            <input type="file" id="archivo_excel_ln" class="form-control" accept=".xlsx,.xls,.csv,.txt,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" style="display: inline-block; max-width: 100%;">
                            <button type="button" id="btn_subir_excel" class="btn btn-success" style="margin-left: 8px;"><i class="fa fa-upload"></i> Importar</button>
                        </div>
                        <a class="btn btn-default btn-sm" href="/Administracion/ListaNegraEmpleadosLayout/" target="_blank" rel="noopener">
                            <i class="fa fa-download"></i> Layout Excel (.xlsx)
                        </a>
                        <a class="btn btn-default btn-sm" href="/Administracion/ListaNegraEmpleadosLayoutCsv/" target="_blank" rel="noopener" style="margin-left: 6px;">
                            <i class="fa fa-download"></i> Layout CSV (UTF-8)
                        </a>
                    </div>
                </div>

                <hr style="border-top: 1px solid #ddd;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="tabla-lista-negra-empleados" style="width:100%">
                        <thead>
                            <tr>
                                <th>Secuencia</th>
                                <th>CURP</th>
                                <th>Estatus</th>
                                <th>Alta</th>
                                <th>Baja</th>
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
