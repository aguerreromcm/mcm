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

                <style>
                    .ln-formas-empleados--grid {
                        margin-bottom: 20px;
                    }
                    .ln-formas-empleados .ln-h4 {
                        margin-top: 0;
                        margin-bottom: 10px;
                    }
                    .ln-formas-empleados .ln-alta-manual-row,
                    .ln-formas-empleados .ln-carga-masiva-row {
                        display: flex;
                        align-items: center;
                        flex-wrap: nowrap;
                        gap: 8px;
                        margin-bottom: 10px;
                    }
                    .ln-formas-empleados .ln-alta-instruccion,
                    .ln-formas-empleados .ln-carga-instruccion {
                        line-height: 1.35;
                    }
                    .ln-formas-empleados .ln-carga-masiva-row input[type="file"].form-control {
                        flex: 1 1 auto;
                        min-width: 0;
                        width: auto;
                        max-width: 100%;
                    }
                    .ln-formas-empleados .ln-carga-masiva-row .btn,
                    .ln-formas-empleados .ln-carga-masiva-row .btn-sm {
                        flex-shrink: 0;
                    }
                    /* Dos columnas: misma altura para fila de instrucción → botones alineados */
                    @media (min-width: 992px) {
                        .ln-formas-empleados--grid {
                            display: grid;
                            grid-template-columns: repeat(2, minmax(0, 1fr));
                            grid-template-rows: auto auto auto;
                            column-gap: 30px;
                            row-gap: 0;
                        }
                        .ln-formas-empleados--grid .ln-h4-alta {
                            grid-column: 1;
                            grid-row: 1;
                        }
                        .ln-formas-empleados--grid .ln-h4-carga {
                            grid-column: 2;
                            grid-row: 1;
                        }
                        .ln-formas-empleados--grid .ln-alta-instruccion {
                            grid-column: 1;
                            grid-row: 2;
                            margin: 0 0 8px 0;
                        }
                        .ln-formas-empleados--grid .ln-carga-instruccion {
                            grid-column: 2;
                            grid-row: 2;
                            margin: 0 0 8px 0;
                        }
                        .ln-formas-empleados--grid .ln-alta-manual-row {
                            grid-column: 1;
                            grid-row: 3;
                        }
                        .ln-formas-empleados--grid .ln-carga-masiva-row {
                            grid-column: 2;
                            grid-row: 3;
                        }
                    }
                    @media (max-width: 991px) {
                        .ln-formas-empleados--grid {
                            display: flex;
                            flex-direction: column;
                        }
                        .ln-formas-empleados--grid .ln-alta-instruccion,
                        .ln-formas-empleados--grid .ln-carga-instruccion {
                            margin-bottom: 8px;
                        }
                    }
                </style>
                <div class="ln-formas-empleados ln-formas-empleados--grid">
                    <h4 class="ln-h4 ln-h4-alta">Alta manual</h4>
                    <h4 class="ln-h4 ln-h4-carga">Carga masiva</h4>
                    <p class="small text-muted ln-alta-instruccion">
                        Escribe el CURP y pulsa <strong>Guardar</strong> para registrarlo en la lista.
                    </p>
                    <p class="small text-muted ln-carga-instruccion">
                        Carga un archivo con CURPs usando el layout descargable que se encuentra a la derecha. La primera columna debe contener CURPs.
                    </p>
                    <div class="form-inline ln-alta-manual-row">
                        <label for="curp_manual" class="sr-only">CURP</label>
                        <input type="text" id="curp_manual" class="form-control" maxlength="18" placeholder="CURP (18 caracteres)" style="min-width: 220px; text-transform: uppercase;" autocomplete="off">
                        <button type="button" id="btn_guardar_curp" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
                    </div>
                    <div class="ln-carga-masiva-row">
                        <input type="file" id="archivo_excel_ln" class="form-control" accept=".xlsx,.xls,.csv,.txt,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                        <button type="button" id="btn_subir_excel" class="btn btn-success"><i class="fa fa-upload"></i> Importar</button>
                        <a class="btn btn-default btn-sm" href="/Administracion/ListaNegraEmpleadosLayout/" target="_blank" rel="noopener">
                            <i class="fa fa-download"></i> Layout Excel (.xlsx)
                        </a>
                    </div>
                </div>

                <hr style="border-top: 1px solid #ddd;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="tabla-lista-negra-empleados" style="width:100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>CURP</th>
                                <th>Estatus</th>
                                <th>Alta</th>
                                <th>Baja</th>
                                <th>Usuario Alta</th>
                                <th>Usuario Baja</th>
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
