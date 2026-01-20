<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Consulta de Pagos</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-8">
                <div class="card-header">
                    <h5 class="card-title">Seleccione la sucursal y el rango de la fecha a generar el reporte </h5>
                </div>

                <div class="card-body">
                    <form class="" id="consulta" action="/Pagos/PagosConsultaAPP/" method="GET" onsubmit="return Validar()">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <select class="form-control mr-sm-3" autofocus type="select" id="id_sucursal" name="id_sucursal" placeholder="000000" aria-label="Search">
                                        <?php echo $getSucursales; ?>
                                    </select>
                                    <span id="availability1" style="font-size:15px">Sucursales</span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $Inicial; ?>">
                                    <span id="availability1" style="font-size:15px">Desde</span>
                                </div>
                                <div class="col-md-3">
                                    <input class="form-control mr-sm-2" autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $Final; ?>">
                                    <span id="availability1" style="font-size:15px">Hasta</span>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-default" type="submit">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card col-md-12">
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
                <form name="all" id="all" method="POST">
                    <button id="export_excel_consulta" type="button" class="btn btn-success btn-circle"><i class="fa fa-file-excel-o"> </i> <b>Exportar a Excel</b></button>
                    <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                                <tr>
                                    <th>Medio</th>
                                    <th>Region</th>
                                    <th>Sucursal</th>
                                    <th>Consecutivo</th>
                                    <th>Fecha</th>
                                    <th>N.Crédito</th>
                                    <th>Cliente</th>
                                    <th>Ciclo</th>
                                    <th>Monto</th>
                                    <th>Tipo</th>
                                    <th>Ejecutivo</th>
                                    <th>Fecha de Registro</th>
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

<div class="modal fade" id="modal_agregar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Agregar Registro de Pago</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Fecha</label>
                                    <input type="text" class="form-control" id="Fecha" aria-describedby="Fecha" disabled placeholder="" value="<?php $fechaActual = date('d-m-Y H:i:s');
                                                                                                                                                echo $fechaActual; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Aparecera la fecha en la que registras el pago.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monto">Monto *</label>
                                    <input type="number" class="form-control" id="monto" name="monto" placeholder="$1260.10">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Tipo de Pago *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <option value="credito">Pago</option>
                                        <option value="fecha">Garantía</option>
                                        <option value="fecha">Multa</option>
                                        <option value="fecha">Descuento</option>
                                        <option value="fecha">Refinanciamiento</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo">Ejecutivo *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <?php echo $status; ?>
                                    </select>
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

<div class="modal fade" id="modal_editar_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center>
                    <h4 class="modal-title" id="myModalLabel">Editar Registro de Pago</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add(); return false" id="Add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Fecha</label>
                                    <input type="text" class="form-control" id="Fecha" aria-describedby="Fecha" disabled placeholder="" value="<?php $fechaActual = date('d-m-Y H:i:s');
                                                                                                                                                echo $fechaActual; ?>">
                                    <small id="emailHelp" class="form-text text-muted">Aparecera la fecha en la que registras el pago.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monto">Monto *</label>
                                    <input type="number" class="form-control" id="monto" name="monto" placeholder="$1260.10">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Tipo de Pago *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <option value="credito">Pago</option>
                                        <option value="fecha">Garantía</option>
                                        <option value="fecha">Multa</option>
                                        <option value="fecha">Descuento</option>
                                        <option value="fecha">Refinanciamiento</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo">Ejecutivo *</label>
                                    <select class="form-control" autofocus type="select" id="tipo" name="tipo" aria-label="Search">
                                        <?php echo $status; ?>
                                    </select>
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
    const mapTipos = {
        "PAGO": "P",
        "PAGO ELECTRÓNICO": "X",
        "PAGO EXCEDENTE": "Y",
        "PAGO EXCEDENTE ELECTRÓNICO": "O",
        "MULTA": "M",
        "MULTA GESTORES": "Z",
        "MULTA ELECTRÓNICA": "L",
        "GARANTÍA": "G",
        "DESCUENTO": "D",
        "REFINANCIAMIENTO": "R",
        "RECOMIENDA": "H",
        "SEGURO": "S",
        "AHORRO": "B",
        "AHORRO ELECTRÓNICO": "F",
        "ABONO AHORRO (AJUSTE)": "E",
        "RETIRO AHORRO (AJUSTE)": "H"

    };

    const muestraAdmin = (e) => {
        const tr = e.target.tagName === "I" ?
            e.target.parentElement.parentElement.parentElement :
            e.target.parentElement.parentElement;

        const [, secuencia, cdgns, fecha_tabla, ciclo, monto, tipo, ejecutivo] = tr.children;
        const fecha = new Date(fecha_tabla.innerText.split("/").reverse().join("-"));
        const fechaMin = new Date(fecha);
        fechaMin.setDate(fecha.getDate() - 20);

        $("#nombre_admin").val($("#nombreCliente").text());
        $("#secuencia_admin").val(secuencia.innerText);
        $("#cdgns_admin").val(cdgns.innerText);
        $("#Fecha_admin_r").val(fecha.toISOString().split("T")[0]);
        $("#Fecha_admin").val(fecha.toISOString().split("T")[0]);
        $("#Fecha_admin").attr("max", fecha.toISOString().split("T")[0]);
        $("#Fecha_admin").attr("min", fechaMin.toISOString().split("T")[0]);
        $("#ciclo_admin").val(ciclo.innerText);
        $("#monto_admin").val(parseaNumero(monto.innerText));
        $("#tipo_admin").val(mapTipos[tipo.innerText.trim()] || "");
        $("#ejecutivo_admin").val($("#ejecutivo_admin option").filter((i, e) => e.text === ejecutivo.innerText).val());

        $("#modal_admin").modal("show");
    };
</script>

<?php echo $footer; ?>