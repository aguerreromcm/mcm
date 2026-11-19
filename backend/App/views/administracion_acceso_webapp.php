<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Acceso Webapp</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">
                <p class="text-muted" style="margin-bottom: 15px;">
                    Contraseñas, teléfonos y sesiones de la webapp de pagos. Las bajas, los perfiles y las sucursales se siguen gestionando en SICAFIN.
                </p>

                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab_usuarios" role="tab" data-toggle="tab">Usuarios</a></li>
                    <li role="presentation"><a href="#tab_bitacora" role="tab" data-toggle="tab">Bitácora de accesos</a></li>
                </ul>

                <div class="tab-content" style="padding-top: 15px;">
                    <div role="tabpanel" class="tab-pane active" id="tab_usuarios">
                        <div style="margin-bottom: 10px;">
                            <button type="button" id="btn_recargar_usuarios" class="btn btn-default btn-sm">
                                <i class="fa fa-refresh"></i> Actualizar
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="tabla-acceso-webapp-usuarios" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Nombre</th>
                                        <th>Perfil</th>
                                        <th>Sucursal</th>
                                        <th>Contraseña</th>
                                        <th>Teléfono</th>
                                        <th>Sesiones</th>
                                        <th>Último acceso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="tab_bitacora">
                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-3 col-sm-4">
                                <label for="bitacora_usuario">Usuario (opcional)</label>
                                <input type="text" id="bitacora_usuario" class="form-control" maxlength="6" style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <label for="bitacora_desde">Desde</label>
                                <input type="date" id="bitacora_desde" class="form-control">
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <label for="bitacora_hasta">Hasta</label>
                                <input type="date" id="bitacora_hasta" class="form-control">
                            </div>
                            <div class="col-md-3 col-sm-12">
                                <label>&nbsp;</label>
                                <button type="button" id="btn_buscar_bitacora" class="btn btn-primary form-control">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                        <p class="text-muted"><small>Rango máximo de 31 días. Las horas se muestran en hora de México.</small></p>
                        <div class="table-responsive">
                            <table id="tabla-acceso-webapp-bitacora" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Fecha UTC</th>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Nombre</th>
                                        <th>Resultado</th>
                                        <th>IP</th>
                                        <th>Teléfono nuevo</th>
                                        <th>Versión app</th>
                                        <th>Navegador</th>
                                        <th>Sistema</th>
                                        <th>Tipo</th>
                                        <th>Marca y modelo</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
