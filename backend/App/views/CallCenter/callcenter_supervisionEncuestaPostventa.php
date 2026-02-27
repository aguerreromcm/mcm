<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="overflow: auto;">
        <div class="x_title">
            <h3>Supervisión Postventa</h3>
            <div class="clearfix"></div>
        </div>
        <div class="contenedor-card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <span class="card-title">Ultima actualización:</span>
                        <span id="ultimaActualizacion" class="card-title"></span>
                    </div>
                </div>
            </div>
            <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">
            <div class="card-body">
                <div class="row" style="text-align: center;">
                    <div class="col-md-2">
                        <label>Asesores en linea</label>
                        <span id="noAsesores" style="display: block; width: 100%; font-size: large;">0</span>
                    </div>
                    <div class="col-md-2">
                        <label>Clientes asignados</label>
                        <span id="noClientes" style="display: block; width: 100%; font-size: large;">0</span>
                    </div>
                    <div class="col-md-2">
                        <label>Encuestas completadas</label>
                        <span id="noCompletados" style="display: block; width: 100%; font-size: large;">0</span>
                    </div>
                    <div class="col-md-2">
                        <label>Encuestas abandonadas</label>
                        <span id="noAbandonados" style="display: block; width: 100%; font-size: large;">0</span>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" type="button" id="mensajeGrl"><i class="glyphicon glyphicon-bullhorn"></i> Enviar mensaje general</button>
                    </div>
                </div>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <span class="card-title">Actividad</span>
                    </div>
                </div>
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="tblActividad">
                        <thead>
                            <tr>
                                <th class="asesor" style="vertical-align: middle !important; text-align: center;">Asesor</th>
                                <th class="conexion" style="vertical-align: middle !important; text-align: center;">Conexión</th>
                                <th class="asignados" style="vertical-align: middle !important; text-align: center;">Clientes asignados</th>
                                <th class="completados" style="vertical-align: middle !important; text-align: center;">Encuestas completadas</th>
                                <th class="abandonados" style="vertical-align: middle !important; text-align: center;">Encuestas abandonadas</th>
                                <th class="cliente" style="vertical-align: middle !important; text-align: center;">Cliente asignado</th>
                                <th class="estatus" style="vertical-align: middle !important; text-align: center;">Estatus</th>
                                <th class="tiempo" style="vertical-align: middle !important; text-align: center;">Tiempo</th>
                                <th class="acciones" style="vertical-align: middle !important; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="bdTblActividad">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>