<?= $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="overflow: auto;">
        <div class="contenedor-card">
            <div class="card-header">
                <div class="x_title">
                    <h3>Administración de correos</h3>
                    <div class="clearfix"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <div class="form-group" style="min-height: 68px; display: flex; align-items: center; justify-content: space-between;">
                            <button class="btn btn-primary" type="button" id="addCorreo"><i class="glyphicon glyphicon-user">&nbsp;</i>Añadir dirección de correo</button>
                            <button class="btn btn-primary" type="button" id="addGrupo"><i class="glyphicon glyphicon-envelope">&nbsp;</i>Añadir grupo</button>
                        </div>
                    </div>
                </div>
                <div class="row" style="display: flex;">
                    <div class=" col-md-7">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Correos</strong>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="areaFiltro">Área/Puesto</label>
                                            <select class="form-control" id="areaFiltro">
                                                <?= $opcArea ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="sucursalFiltro">Sucursal</label>
                                            <select class="form-control" id="sucursalFiltro">
                                                <?= $opcSucursal ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <table id="tblCorreos" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Nombre</th>
                                                    <th>Correo</th>
                                                    <th>Área/Puesto</th>
                                                    <th>Sucursal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-1" style="display: flex; justify-content: center; align-items: center;">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <button class="btn btn-success btn-block" id="btnAgregar">Agregar &gt;&gt;</button>
                                <button class="btn btn-danger btn-block" id="btnQuitar">&lt;&lt; Quitar</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Grupos</strong>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Grupo</label>
                                            <div class="dropdown">
                                                <button class="btn btn-default dropdown-toggle" type="button" id="menuGrupos" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width: 100%; display: flex; justify-content: space-between;">
                                                    <span id="grupoSeleccionado">Seleccionar grupo</span>
                                                    <span class="glyphicon glyphicon-menu-down"></span>
                                                </button>
                                                <input type="hidden" id="idGrupoSeleccionado">
                                                <div class="dropdown-menu" aria-labelledby="menuGrupos" style="width: 100%; font-size: medium;">
                                                    <input type="search" class="form-control" id="buscarGrupo" placeholder="Buscar" autofocus="autofocus" style="width: 90%; margin: 5px auto;">
                                                    <div id="sinResultados" class="dropdown-header" style="display: none; font-size: medium;">Sin coincidencias</div>
                                                    <ul id="grupoFiltro" style="list-style-type:none; max-height: 200px; overflow: auto;">
                                                        <?= $opcGrupo ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <table id="tblGrupo" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Correo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Los datos se rellenarán dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCorreo" tabindex="-1" role="dialog" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="text-align:center;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h2 class="modal-title" id="registroModalLabel">Registrar nueva dirección de correo</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" placeholder="Ingresa el nombre">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="correo">Correo</label>
                            <input type="email" class="form-control" id="correo" placeholder="a.b@" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="empresa">Área/Puesto</label>
                            <select class="form-control" id="area">
                                <option value="">Selecciona una opción</option>
                                <option value="Gerente Sucursal">Gerente Sucursal</option>
                                <option value="Administradora Sucursal">Administradora Sucursal</option>
                                <option value="Operaciones Ofic. Central">Operaciones Ofic. Central</option>
                                <option value="Call Center">Call Center</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="empresa">Sucursal</label>
                            <select class="form-control" id="sucursal">
                                <?= $opcSucursales ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" style="text-align:center ;">
                    <p>Si el área o sucursal que desea no se encuentra en la lista correspondiente, comuníquese con soporte.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="guardarDireccion" disabled>Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGrupo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="text-align:center;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title" id="modalCDCLabel">Registrar nuevo grupo de correos</h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="form-group">
                            <label for="nombreGrupo">Nombre</label>
                            <input type="text" class="form-control" id="nombreGrupo" placeholder="Ingresa el nombre del grupo">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarGrupo">Crear</button>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>