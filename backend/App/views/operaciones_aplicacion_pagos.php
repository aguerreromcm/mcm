<?= $header; ?>

<div class="right_col">
    <div class="panel panel-default folios-tarjeta-page pagos-gl-page pagos-post-cierre-page">
        <div class="panel-body">
            <div class="x_title">
                <h3>Aplicación de pagos</h3>
                <div class="clearfix"></div>
            </div>

            <div class="panel-card ft-toolbar">
                <div class="ft-toolbar-inner">
                    <div class="ft-search">
                        <label class="ft-tb-lbl" for="fechaBuscar">
                            <i class="fa fa-calendar"></i> Fecha de los pagos
                        </label>
                        <div class="ft-search-line">
                            <input class="form-control"
                                   type="date"
                                   id="fechaBuscar"
                                   max="<?= $fechaMaxima; ?>"
                                   value="<?= $fechaDefault; ?>">
                            <button type="button" class="btn btn-primary" id="buscar">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <div class="ft-toolbar-sep" aria-hidden="true"></div>
                    <div class="ft-toolbar-hint">
                        <p>Detecta los pagos registrados después del cierre de día y los importa y concilia igual que el cierre, sin regenerar el cierre de cartera ni el devengo.</p>
                    </div>
                </div>
            </div>

            <div class="panel-card">
                <div class="head">
                    <h4><i class="fa fa-bell"></i> Fechas con pagos pendientes (últimos 30 días)</h4>
                    <div class="pgl-head-badges">
                        <span class="ft-conteo" id="lblFechasPendientes"></span>
                    </div>
                </div>
                <div class="body">
                    <div id="listaFechasPendientes" class="ppc-fechas"></div>
                </div>
            </div>

            <div id="estadoInicial" class="ft-vacio panel-card pgl-vacio">
                <div class="pgl-vacio-icon"><i class="fa fa-exchange"></i></div>
                <h4>Sin consulta activa</h4>
                <p>Seleccione una fecha o elija una de las fechas con pagos pendientes.</p>
            </div>

            <div class="resultado">
                <div class="panel-card" id="panelPendientes">
                    <div class="head">
                        <h4><i class="fa fa-info-circle"></i> Pagos pendientes del <span id="lblFecha">—</span></h4>
                        <div class="pgl-head-badges">
                            <span class="ft-conteo" id="lblEstadoCierre"></span>
                        </div>
                    </div>
                    <div class="body">
                        <div id="alertaBloqueo" class="pgl-alerta pgl-alerta-aviso" style="display: none;">
                            <span class="pgl-alerta-icon"><i class="fa fa-lock"></i></span>
                            <div>
                                <strong>No es posible procesar los pagos</strong>
                                <p id="txtBloqueo"></p>
                            </div>
                        </div>

                        <div id="alertaSinPendientes" class="pgl-alerta ppc-alerta-ok" style="display: none;">
                            <span class="pgl-alerta-icon"><i class="fa fa-check"></i></span>
                            <div>
                                <strong>Sin pagos pendientes</strong>
                                <p>Todos los pagos de esta fecha ya fueron importados.</p>
                            </div>
                        </div>

                        <div class="pgl-operacion">
                            <div class="pgl-money-card pgl-money-garantia">
                                <div class="pgl-money-top">
                                    <span class="pgl-money-label">Total pendiente</span>
                                    <i class="fa fa-list"></i>
                                </div>
                                <div class="pgl-money-value" id="lblTotalMonto">—</div>
                                <p class="pgl-money-note"><span id="lblTotalPagos">0</span> registro(s)</p>
                            </div>
                            <div class="pgl-money-card pgl-money-saldo">
                                <div class="pgl-money-top">
                                    <span class="pgl-money-label">Pagos / Garantías</span>
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="ppc-desglose">
                                    <span><b id="lblPagos">0</b> pagos · <span id="lblMontoPagos">$0.00</span></span>
                                    <span><b id="lblGarantias">0</b> garantías · <span id="lblMontoGarantias">$0.00</span></span>
                                </div>
                            </div>
                            <div class="pgl-accion">
                                <p class="pgl-money-note ppc-accion-nota">Importa y concilia los pagos y garantías pendientes igual que en el cierre de día.</p>
                                <button type="button" class="btn btn-success pgl-btn-aplicar" id="btnProcesar" disabled>
                                    <i class="fa fa-check"></i>
                                    <span>Importar y conciliar</span>
                                </button>
                                <p class="pgl-money-note ppc-seleccion" id="lblSeleccion"></p>
                            </div>
                        </div>

                        <div class="ppc-tabla">
                            <table class="table table-striped table-bordered table-hover" id="tablaPendientes" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="ppc-col-sel" data-orderable="false">
                                            <input type="checkbox" id="chkTodosPendientes" title="Seleccionar todos">
                                        </th>
                                        <th>Fecha</th>
                                        <th>Crédito</th>
                                        <th>Ciclo</th>
                                        <th>Nombre</th>
                                        <th>Referencia</th>
                                        <th>Tipo</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal fade" id="modalResultadoProceso" tabindex="-1" role="dialog" aria-labelledby="modalResultadoProcesoTitle">
            <div class="modal-dialog modal-lg ppc-modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title ppc-modal-title" id="modalResultadoProcesoTitle">
                            <i class="fa fa-check-square-o"></i> Resultado del proceso
                            <span class="ppc-modal-fecha"><i class="fa fa-calendar"></i> Registros del <span id="lblResFecha">—</span></span>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="alertaResultadoProceso" class="pgl-alerta ppc-alerta-ok">
                            <span class="pgl-alerta-icon"><i id="iconoResultadoProceso" class="fa fa-check"></i></span>
                            <div>
                                <strong id="txtResultadoProceso"></strong>
                                <div class="pgl-head-badges ppc-modal-badges">
                                    <span class="ft-conteo">Importación <b id="lblResImportacion">—</b></span>
                                    <span class="ft-conteo">Procesados <b id="lblResProcesados">0</b></span>
                                    <span class="ft-conteo pgl-badge-ok">Correctos <b id="lblResCorrectos">0</b></span>
                                    <span class="ft-conteo pgl-badge-warn">Con observaciones <b id="lblResObservacion">0</b></span>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="tablaResultado" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Crédito</th>
                                    <th>Ciclo</th>
                                    <th>Tipo</th>
                                    <th>Referencia</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>
