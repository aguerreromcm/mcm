<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Control de Garantías</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-header" style="margin: 20px 0;">
                    <span class="card-title">Ingrese el numero de crédito</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="noCredito">Crédito:</label>
                                <input class="form-control" style="font-size: 24px;" type="text" id="creditoBuscar" placeholder="000000" maxlength="6">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group" style="min-height: 68px; display: flex; align-items: center; justify-content: space-between;">
                                <button type="button" class="btn btn-primary" id="buscar">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultado">
            <div class="botones">
                <button type="button" class="btn btn-primary" id="agregar">
                    <span class="glyphicon glyphicon-plus">&nbsp;</span>Agregar Artículo
                </button>
                <button type="button" class="btn btn-success" id="excel" >
                    <span class="fa fa-file-excel-o">&nbsp;</span>Exportar a Excel
                </button>
            </div>
            <hr>
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="garantias">
                    <thead>
                        <tr>
                            <th>Secuencia</th>
                            <th>Fecha de Registro</th>
                            <th>Articulo</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Serie</th>
                            <th>Monto</th>
                            <th>Factura</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="articuloGarantia" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <center>
                    <h2 class="modal-title" id="modalCDCLabel">Información del articulo en garantía</h2>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="col-md-6">
                        <div class="form-group" >
                            <label for="exampleInputEmail1">Crédito</label>
                            <input type="text" class="form-control" id="credito" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="display: none;">
                            <label for="exampleInputEmail1">Secuencia</label>
                            <input type="text" class="form-control" id="secuencia" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Nombre del Artículo *</label>
                            <input type="text" class="form-control" id="articulo" onkeyup="mayusculas(this)" placeholder="Ingrese el nombre del artículo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="marca">Marca *</label>
                            <input type="text" class="form-control" id="marca" onkeyup="mayusculas(this)" placeholder="Ingrese la marca del artículo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="modelo">Modelo *</label>
                            <input type="text" class="form-control" id="modelo" onkeyup="mayusculas(this)" placeholder="Ingrese el modelo del artículo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="serie">Número de Serie *</label>
                            <input type="text" class="form-control" id="serie" onkeyup="mayusculas(this)" placeholder="Ingrese el número de serie del artículo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="valor">Valor *</label>
                            <input type="number" class="form-control" id="valor" placeholder="Ingrese el valor del artículo">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="factura">Factura *</label>
                            <input type="text" class="form-control" id="factura" onkeyup="mayusculas(this)" placeholder="Escribe el número de factura">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="agregarGarantia">Agregar</button>
                <button type="button" class="btn btn-primary" id="editarGarantia">Editar</button>
                <button type="button" class="btn btn-primary" id="eliminarGarantia">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>