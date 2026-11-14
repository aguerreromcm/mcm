<?= $header; ?>

<div class="right_col">
    <div class="panel panel-default folios-tarjeta-page pagos-gl-page pagos-post-cierre-page cierre-dia-page">
        <div class="panel-body">
            <div class="x_title">
                <h3>Cierre de día</h3>
                <div class="clearfix"></div>
            </div>

            <p class="text-muted cd-descripcion">
                Consulte un día para revisar sus ajustes, pagos e histórico de cierres, y genere el cierre de la fecha que corresponda.
                Al finalizar el cierre se envía el resumen por correo.
            </p>

            <div class="cd-toolbar-row">
                <div class="panel-card ft-toolbar cd-toolbar">
                    <div class="ft-toolbar-inner">
                        <div class="cd-field cd-field-fecha">
                            <label class="ft-tb-lbl" for="fechaConsulta">
                                <i class="fa fa-search"></i> Fecha a consultar
                            </label>
                            <input type="date" id="fechaConsulta" class="form-control cd-control"
                                max="<?= date('Y-m-d') ?>"
                                value="<?= date('Y-m-d', strtotime('-1 day')) ?>">
                        </div>
                        <div class="cd-field cd-field-acciones">
                            <span class="ft-tb-lbl cd-lbl-spacer" aria-hidden="true">&nbsp;</span>
                            <div class="cd-acciones-line">
                                <button type="button" id="btnConsultar" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Consultar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-card ft-toolbar cd-toolbar cd-toolbar-cierre">
                    <div class="ft-toolbar-inner">
                        <div class="cd-field cd-field-fecha">
                            <label class="ft-tb-lbl" for="fecha">
                                <i class="fa fa-calendar"></i> Fecha de cierre
                            </label>
                            <input type="date" id="fecha" class="form-control cd-control"
                                min="<?= date('Y-m-d', strtotime('-30 days')) ?>"
                                max="<?= date('Y-m-d', strtotime('1 days')) ?>"
                                value="<?= date('Y-m-d', strtotime('-1 day')) ?>">
                        </div>
                        <div class="cd-field cd-field-acciones">
                            <span class="ft-tb-lbl cd-lbl-spacer" aria-hidden="true">&nbsp;</span>
                            <div class="cd-acciones-line">
                                <button type="button" id="procesar" class="btn btn-success">
                                    <i class="fa fa-cogs"></i> Generar cierre
                                </button>
                        </div>
                    </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning" role="alert" style="display: none;" id="alertaEjecucion">
                <strong>El cierre se está ejecutando.</strong><br>
                <span id="tiempoEstimado"></span>
            </div>

            <div class="panel-card cd-tabs-wrap">
                <div class="head cd-tabs-head">
                    <ul class="nav nav-tabs cd-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tabPrecierre" aria-controls="tabPrecierre" role="tab" data-toggle="tab" data-seccion="precierre">
                                <i class="fa fa-sliders"></i> Pre cierre
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tabPagos" aria-controls="tabPagos" role="tab" data-toggle="tab" data-seccion="pagos">
                                <i class="fa fa-money"></i> Pagos
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tabHistorico" aria-controls="tabHistorico" role="tab" data-toggle="tab" data-seccion="historico">
                                <i class="fa fa-history"></i> Histórico
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tabPostcierre" aria-controls="tabPostcierre" role="tab" data-toggle="tab" data-seccion="postcierre">
                                <i class="fa fa-check-square-o"></i> Post cierre
                            </a>
                        </li>
            </ul>
                </div>

                <div class="body tab-content">
                    <!-- Pre cierre -->
                    <div role="tabpanel" class="tab-pane active" id="tabPrecierre">
                        <div class="ft-vacio pgl-vacio cd-sin-consulta">
                            <div class="pgl-vacio-icon"><i class="fa fa-sliders"></i></div>
                            <h4>Sin consulta activa</h4>
                            <p>Seleccione la fecha a consultar y presione Consultar.</p>
                            </div>
                        <div class="cd-contenido" style="display: none;">
                            <div class="cd-tab-intro-row">
                                <p class="cd-tab-intro">
                                    Descuentos (D) y refinanciamientos (R) del día pendientes de aplicar. El refinanciamiento resta al saldo total
                                    y el descuento solo al interés. Aplíquelos antes de generar el cierre.
                                </p>
                                <div class="pgl-head-badges">
                                    <span class="ft-conteo" id="lblPreCierre"></span>
                            </div>
                            </div>
                            <div class="cd-barra-acciones">
                                <div class="cd-resumen-pre">
                                    <div class="cd-resumen-item">
                                        <span class="cd-resumen-icono"><i class="fa fa-list"></i></span>
                                        <div>
                                            <span class="cd-resumen-etiqueta">Registros</span>
                                            <span class="cd-resumen-valor"><b id="lblPreTotal">0</b><small id="lblPreMonto">$0.00</small></span>
                            </div>
                            </div>
                                    <div class="cd-resumen-item cd-resumen-descuento">
                                        <span class="cd-resumen-icono"><i class="fa fa-tag"></i></span>
                                        <div>
                                            <span class="cd-resumen-etiqueta">Descuentos</span>
                                            <span class="cd-resumen-valor"><b id="lblPreDescuentos">0</b><small id="lblPreMontoDescuentos">$0.00</small></span>
                                        </div>
                                    </div>
                                    <div class="cd-resumen-item cd-resumen-refinanciamiento">
                                        <span class="cd-resumen-icono"><i class="fa fa-refresh"></i></span>
                                        <div>
                                            <span class="cd-resumen-etiqueta">Refinanciamientos</span>
                                            <span class="cd-resumen-valor"><b id="lblPreRefinanciamientos">0</b><small id="lblPreMontoRefinanciamientos">$0.00</small></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="cd-barra-acciones-der">
                                    <span class="cd-seleccion" id="lblSeleccionPrecierre"></span>
                                    <button type="button" class="btn btn-success pgl-btn-aplicar" id="btnProcesarPrecierre" disabled>
                                        <i class="fa fa-check"></i>
                                        <span>Procesar ajustes</span>
                                    </button>
                            </div>
                            </div>
                            <table class="table table-striped table-bordered table-hover" id="tablaPrecierre" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Crédito</th>
                                        <th>Ciclo</th>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Monto</th>
                                        <th>Saldo capital</th>
                                        <th>Saldo interés</th>
                                        <th>Saldo total</th>
                                        <th>Saldo después del ajuste</th>
                                        <th><input type="checkbox" id="chkTodosPrecierre" title="Seleccionar todos"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagos -->
                    <div role="tabpanel" class="tab-pane" id="tabPagos">
                        <div class="ft-vacio pgl-vacio cd-sin-consulta">
                            <div class="pgl-vacio-icon"><i class="fa fa-money"></i></div>
                            <h4>Sin consulta activa</h4>
                            <p>Seleccione la fecha a consultar y presione Consultar.</p>
                        </div>
                        <div class="cd-contenido" style="display: none;">
                            <p class="cd-tab-intro">
                                Resumen de los movimientos del día y cuántos ya se aplicaron. Seleccione una tarjeta para ver el detalle.
                            </p>
                            <div class="cd-cards" id="cardsPagos"></div>
                        </div>
                    </div>

                    <!-- Histórico -->
                    <div role="tabpanel" class="tab-pane" id="tabHistorico">
                        <div class="ft-vacio pgl-vacio cd-sin-consulta">
                            <div class="pgl-vacio-icon"><i class="fa fa-history"></i></div>
                            <h4>Sin consulta activa</h4>
                            <p>Seleccione la fecha a consultar y presione Consultar.</p>
                        </div>
                        <div class="cd-contenido" style="display: none;">
                            <p class="cd-tab-intro">Cierre del día consultado y de los 6 días anteriores, según la bitácora de cierre.</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="tablaHistorico">
                                    <thead>
                                        <tr>
                                            <th>Fecha de cierre</th>
                                            <th>Estado</th>
                                            <th>Usuario</th>
                                            <th>Inicio</th>
                                            <th>Fin</th>
                                            <th class="cd-num">Registros de cierre</th>
                                            <th class="cd-num">Créditos devengo</th>
                                            <th class="cd-num">Monto devengado</th>
                                            <th>Detalle</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyHistorico"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Post cierre -->
                    <div role="tabpanel" class="tab-pane" id="tabPostcierre">
                        <div class="cd-post-opciones">
                            <button type="button" class="cd-post-opcion" data-panel="aplicar">
                                <span class="cd-post-icono"><i class="fa fa-exchange"></i></span>
                                <span class="cd-post-texto">
                                    <strong>Aplicar pagos</strong>
                                    <span>Pagos (P) y pagos electrónicos (X) del día que aún no se aplican. Se procesan solo en cartera, sin regenerar el cierre.</span>
                                </span>
                                    </button>
                            <button type="button" class="cd-post-opcion" data-panel="garantias">
                                <span class="cd-post-icono"><i class="fa fa-shield"></i></span>
                                <span class="cd-post-texto">
                                    <strong>Liquidación de garantías</strong>
                                    <span>Aplica el saldo de garantía líquida a un crédito para liquidarlo.</span>
                                </span>
                            </button>
                            <button type="button" class="cd-post-opcion" data-panel="noIdentificados">
                                <span class="cd-post-icono"><i class="fa fa-question-circle"></i></span>
                                <span class="cd-post-texto">
                                    <strong>Pagos no identificados</strong>
                                    <span>Asigna crédito, ciclo y tipo de movimiento a los pagos no identificados del día y los aplica en cartera.</span>
                                </span>
                            </button>
                            </div>

                        <div class="ft-vacio pgl-vacio" id="postVacio">
                            <div class="pgl-vacio-icon"><i class="fa fa-hand-pointer-o"></i></div>
                            <h4>Seleccione una opción</h4>
                            <p>Elija la acción posterior al cierre que desea realizar.</p>
                        </div>

                        <!-- Aplicar pagos -->
                        <div class="cd-post-panel" id="panelPost-aplicar">
                            <div class="ft-vacio pgl-vacio cd-sin-consulta">
                                <div class="pgl-vacio-icon"><i class="fa fa-exchange"></i></div>
                                <h4>Sin consulta activa</h4>
                                <p>Seleccione la fecha a consultar y presione Consultar.</p>
                            </div>
                            <div class="cd-contenido" style="display: none;">
                                <div class="cd-post-head">
                                    <h4><i class="fa fa-exchange"></i> Pagos pendientes de aplicar del <span id="lblAplFecha">—</span></h4>
                                    <span class="ft-conteo" id="lblAplEstadoCierre"></span>
                    </div>

                                <div id="alertaAplBloqueo" class="pgl-alerta pgl-alerta-aviso" style="display: none;">
                                    <span class="pgl-alerta-icon"><i class="fa fa-lock"></i></span>
                                    <div>
                                        <strong>No es posible aplicar los pagos</strong>
                                        <p id="txtAplBloqueo"></p>
                        </div>
                    </div>

                                <div id="alertaAplSinPendientes" class="pgl-alerta ppc-alerta-ok" style="display: none;">
                                    <span class="pgl-alerta-icon"><i class="fa fa-check"></i></span>
                                    <div>
                                        <strong>Sin pagos pendientes</strong>
                                        <p>Todos los pagos de esta fecha ya fueron aplicados.</p>
                                    </div>
                                </div>

                                <div class="pgl-operacion">
                                    <div class="pgl-money-card pgl-money-garantia">
                                        <div class="pgl-money-top">
                                            <span class="pgl-money-label">Total pendiente</span>
                                            <i class="fa fa-list"></i>
                                        </div>
                                        <div class="pgl-money-value" id="lblAplTotalMonto">—</div>
                                        <p class="pgl-money-note"><span id="lblAplTotalPagos">0</span> registro(s)</p>
                                    </div>
                                    <div class="pgl-money-card pgl-money-saldo">
                                        <div class="pgl-money-top">
                                            <span class="pgl-money-label">Pagos / Pagos electrónicos</span>
                                            <i class="fa fa-money"></i>
                                        </div>
                                        <div class="ppc-desglose">
                                            <span><b id="lblAplPagos">0</b> pagos · <span id="lblAplMontoPagos">$0.00</span></span>
                                            <span><b id="lblAplElectronicos">0</b> electrónicos · <span id="lblAplMontoElectronicos">$0.00</span></span>
                                        </div>
                                    </div>
                                    <div class="pgl-accion">
                                        <p class="pgl-money-note ppc-accion-nota">Importa y concilia los pagos seleccionados igual que el cierre de día, sin regenerar el cierre de cartera ni el devengo.</p>
                                        <button type="button" class="btn btn-success pgl-btn-aplicar" id="btnAplicarPagos" disabled>
                                            <i class="fa fa-check"></i>
                                            <span>Aplicar en cartera</span>
                                        </button>
                                        <p class="pgl-money-note ppc-seleccion" id="lblAplSeleccion"></p>
                                    </div>
                                </div>

                                <div class="ppc-tabla">
                                    <table class="table table-striped table-bordered table-hover" id="tablaAplicarPagos" style="width: 100%;">
                            <thead>
                                <tr>
                                                <th><input type="checkbox" id="chkTodosAplicar" title="Seleccionar todos"></th>
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

                        <!-- Liquidación de garantías -->
                        <div class="cd-post-panel" id="panelPost-garantias">
                            <div class="cd-gl-buscar">
                                <div class="ft-search">
                                    <label class="ft-tb-lbl" for="glCredito">
                                        <i class="fa fa-search"></i> Número de crédito
                                    </label>
                                    <div class="ft-search-line">
                                        <input class="form-control" type="text" id="glCredito" placeholder="Ej. 019692" maxlength="6" autocomplete="off">
                                        <button type="button" class="btn btn-primary" id="glBuscar">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                            </div>
                            </div>
                                <p class="cd-tab-intro">Ingrese el crédito para consultar su saldo pendiente y la garantía líquida que se aplicará para liquidarlo.</p>
                        </div>

                            <div id="glVacio" class="ft-vacio pgl-vacio">
                                <div class="pgl-vacio-icon"><i class="fa fa-shield"></i></div>
                                <h4>Sin crédito consultado</h4>
                    </div>

                            <div id="glResultado" style="display: none;">
                                <div class="pgl-head-badges cd-gl-badges">
                                    <span class="ft-conteo" id="lblGlEstado"></span>
                                    <span class="ft-conteo" id="lblGlRango"></span>
                                </div>

                                <div id="alertaGlLiquidacion" class="pgl-alerta pgl-alerta-peligro" style="display: none;">
                                    <span class="pgl-alerta-icon"><i class="fa fa-exclamation-triangle"></i></span>
                                    <div>
                                        <strong>Este movimiento liquidará el crédito</strong>
                                        <p>La garantía es mayor o igual al saldo pendiente. Al confirmar, el crédito quedará liquidado.</p>
                        </div>
                    </div>

                                <div id="alertaGlSinRango" class="pgl-alerta pgl-alerta-aviso" style="display: none;">
                                    <span class="pgl-alerta-icon"><i class="fa fa-calendar-times-o"></i></span>
                                    <div>
                                        <strong>Sin rango de fechas disponible</strong>
                                        <p id="txtGlSinRango"></p>
                                    </div>
                                </div>

                                <div class="pgl-identidad">
                                    <div class="pgl-id-item pgl-id-credito">
                                        <span class="pgl-id-icon"><i class="fa fa-hashtag"></i></span>
                                        <div class="pgl-id-copy">
                                            <label>Crédito</label>
                                            <span id="lblGlCredito">—</span>
                                        </div>
                                    </div>
                                    <div class="pgl-id-item">
                                        <span class="pgl-id-icon"><i class="fa fa-refresh"></i></span>
                                        <div class="pgl-id-copy">
                                            <label>Ciclo</label>
                                            <span id="lblGlCiclo">—</span>
                                        </div>
                                    </div>
                                    <div class="pgl-id-item">
                                        <span class="pgl-id-icon"><i class="fa fa-calendar"></i></span>
                                        <div class="pgl-id-copy">
                                            <label>Fecha fin</label>
                                            <span id="lblGlFechaFin">—</span>
                                        </div>
                                    </div>
                                    <div class="pgl-id-item pgl-id-ref">
                                        <span class="pgl-id-icon"><i class="fa fa-barcode"></i></span>
                                        <div class="pgl-id-copy">
                                            <label>Referencia</label>
                                            <span id="lblGlReferencia">—</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="pgl-operacion">
                                    <div class="pgl-money-card pgl-money-saldo">
                                        <div class="pgl-money-top">
                                            <span class="pgl-money-label">Saldo pendiente</span>
                                            <i class="fa fa-line-chart"></i>
                                        </div>
                                        <div class="pgl-money-value" id="lblGlSaldo">—</div>
                                        <p class="pgl-money-note">Monto por cobrar del crédito</p>
                                    </div>
                                    <div class="pgl-money-card pgl-money-garantia" id="glCardGarantia">
                                        <div class="pgl-money-top">
                                            <span class="pgl-money-label">Garantía líquida (GL)</span>
                                            <i class="fa fa-shield"></i>
                                        </div>
                                        <div class="pgl-money-value" id="lblGlGarantia">—</div>
                                        <p class="pgl-money-note">Monto que se aplicará al registrar</p>
                                    </div>
                                    <div class="pgl-accion">
                                        <div class="pgl-registro-fecha">
                                            <label class="ft-tb-lbl" for="glFechaAplicacion">
                                                <i class="fa fa-calendar"></i> Fecha de aplicación
                                            </label>
                                            <div class="pgl-accion-controls">
                                                <input type="date" class="form-control" id="glFechaAplicacion">
                                                <button type="button" class="btn btn-success pgl-btn-aplicar" id="glRegistrar" disabled>
                                                    <i class="fa fa-check"></i>
                                                    <span>Aplicar liquidación</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagos no identificados -->
                        <div class="cd-post-panel" id="panelPost-noIdentificados">
                            <div class="ft-vacio pgl-vacio cd-sin-consulta">
                                <div class="pgl-vacio-icon"><i class="fa fa-question-circle"></i></div>
                                <h4>Sin consulta activa</h4>
                                <p>Seleccione la fecha a consultar y presione Consultar.</p>
                            </div>
                            <div class="cd-contenido" style="display: none;">
                                <div class="cd-post-head">
                                    <h4><i class="fa fa-question-circle"></i> Pagos no identificados del <span id="lblPdiFecha">—</span></h4>
                                    <span class="ft-conteo" id="lblPdiResumen"></span>
                                </div>
                                <p class="cd-tab-intro">
                                    Capture el crédito, ciclo y tipo de movimiento de cada pago. El crédito debe estar en situación Entregado;
                                    el pago se identifica y se aplica en cartera.
                                </p>
                                <table class="table table-striped table-bordered table-hover" id="tablaNoIdentificados" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Referencia</th>
                                            <th>Cuenta</th>
                                    <th>Monto</th>
                                            <th>Descripción</th>
                                            <th>Registró</th>
                                            <th>Acción</th>
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

        <div class="modal fade" id="modalDetallePagos" tabindex="-1" role="dialog" aria-labelledby="modalDetallePagosTitle">
            <div class="modal-dialog modal-lg ppc-modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title ppc-modal-title" id="modalDetallePagosTitle">
                            <span id="lblDetalleTitulo">Detalle</span>
                            <span class="ppc-modal-fecha"><i class="fa fa-calendar"></i> <span id="lblDetalleFecha">—</span></span>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="cd-modal-filtro">
                            <span class="text-muted" id="lblDetalleDescripcion"></span>
                            <select id="filtroDetalle" class="form-control input-sm">
                                <option value="todos">Todos</option>
                                <option value="aplicados">Aplicados</option>
                                <option value="pendientes">Sin aplicar</option>
                            </select>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="tablaDetallePagos" style="width: 100%;"></table>
                    </div>
                    <div class="modal-footer">
                        <span class="text-muted pull-left" id="lblDetalleConteo"></span>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
                            <i class="fa fa-check-square-o"></i> <span id="lblResultadoTitulo">Resultado del proceso</span>
                            <span class="ppc-modal-fecha"><i class="fa fa-calendar"></i> Registros del <span id="lblResultadoFecha">—</span></span>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div id="alertaResultadoProceso" class="pgl-alerta ppc-alerta-ok">
                            <span class="pgl-alerta-icon"><i id="iconoResultadoProceso" class="fa fa-check"></i></span>
                            <div>
                                <strong id="txtResultadoProceso"></strong>
                                <div class="pgl-head-badges ppc-modal-badges">
                                    <span class="ft-conteo" id="badgeResImportacion">Importación <b id="lblResImportacion">—</b></span>
                                    <span class="ft-conteo">Procesados <b id="lblResProcesados">0</b></span>
                                    <span class="ft-conteo pgl-badge-ok">Correctos <b id="lblResCorrectos">0</b></span>
                                    <span class="ft-conteo pgl-badge-warn">Con observaciones <b id="lblResObservacion">0</b></span>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="tablaResultadoProceso" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Crédito</th>
                                    <th>Ciclo</th>
                                    <th>Tipo</th>
                                    <th id="thResultadoClave">Referencia</th>
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

        <div class="modal fade" id="modalAplicarPdi" tabindex="-1" role="dialog" aria-labelledby="modalAplicarPdiTitle">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalAplicarPdiTitle">Aplicar pago no identificado</h4>
                </div>
                    <div class="modal-body">
                        <div class="cd-modal-info">
                            <strong>Referencia:</strong> <span id="pdiReferencia"></span><br>
                            <strong>Monto:</strong> <span id="pdiMonto"></span><br>
                            <strong>Motivo:</strong> <span id="pdiDescripcion"></span>
                    </div>
                        <div class="form-group">
                            <label for="pdiTipo">Tipo de movimiento *</label>
                            <select id="pdiTipo" class="form-control">
                                <option value="P" selected>P - Pago</option>
                                <option value="X">X - Pago electrónico</option>
                                <option value="G">G - Garantía</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pdiCredito">Número de crédito *</label>
                            <input type="text" id="pdiCredito" class="form-control" maxlength="6" placeholder="000000" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="pdiCiclo">Ciclo *</label>
                            <input type="text" id="pdiCiclo" class="form-control" maxlength="2" placeholder="00" autocomplete="off">
                        </div>
                        <p class="text-muted small" style="margin-bottom: 0;">
                            Se validará que el crédito y ciclo existan y estén en situación Entregado. Pago y pago electrónico se aplican
                            con la referencia de pago del crédito; garantía, con la referencia de garantía.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="button" id="btnAplicarPdi" class="btn btn-primary">
                            <i class="fa fa-check"></i> Aplicar pago
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalBitacora" tabindex="-1" role="dialog" aria-labelledby="modalBitacoraTitle">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalBitacoraTitle">Bitácora del cierre <small id="lblBitacoraFecha" class="text-muted"></small></h4>
                    </div>
                    <div class="modal-body">
                        <ul class="cd-bitacora" id="listaBitacora"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalInfoDiaCierre" tabindex="-1" role="dialog" aria-labelledby="modalInfoDiaCierreTitle">
            <div class="modal-dialog cd-modal-correo" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalInfoDiaCierreTitle">Resumen de cierre</h4>
                </div>
                    <div class="modal-body">
                    <p id="resumenCierreCargando" class="text-muted text-center" style="margin: 24px 0;">Cargando resumen…</p>
                        <div id="resumenCierreContenido" class="cd-correo-resumen" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const ID_TABLA_PRECIERRE = "tablaPrecierre"
        const ID_TABLA_APLICAR = "tablaAplicarPagos"
        const ID_TABLA_PDI = "tablaNoIdentificados"
        const ID_TABLA_DETALLE = "tablaDetallePagos"
        const ID_TABLA_RESULTADO = "tablaResultadoProceso"

        const estado = {
            fecha: "",
            panelPost: "",
            precierre: null,
            aplicar: null,
            noIdentificados: [],
            pdiActual: null,
            historico: [],
            detalle: null,
            gl: null
        }

        const TIPOS_MOVIMIENTO = {
            P: "Pago",
            X: "Pago electrónico",
            G: "Garantía",
            D: "Descuento",
            R: "Refinanciamiento"
        }

        const CATEGORIAS = [
            { id: "pagos", titulo: "Pagos", icono: "fa-money", descripcion: "Pagos (P) y pagos electrónicos (X) registrados en el día." },
            { id: "garantias", titulo: "Garantías", icono: "fa-shield", descripcion: "Garantías (G) registradas en el día." },
            { id: "ajustes", titulo: "Ajustes", icono: "fa-sliders", descripcion: "Descuentos (D) y refinanciamientos (R); se aplican en el pre cierre." },
            { id: "no_identificados", titulo: "No identificados", icono: "fa-question-circle", descripcion: "Pagos sin crédito identificado; se aplican al identificarlos en post cierre." },
            { id: "incidencias", titulo: "Incidencias", icono: "fa-exclamation-triangle", descripcion: "Pagos que la importación no pudo aplicar; se resuelven al identificar el pago." }
        ]

        const ESTADOS_HISTORICO = {
            finalizado: "cd-badge-ok",
            error: "cd-badge-danger",
            procesando: "cd-badge-warn",
            pendiente: "cd-badge-muted"
        }

        const DIAS_SEMANA = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"]

        const escHtml = (s) => {
            const d = document.createElement("div")
            d.textContent = s == null ? "" : String(s)
            return d.innerHTML
        }

        const moneda = (n) => "$" + formatoMoneda(Number(n) || 0)
        const entero = (n) => (Number(n) || 0).toLocaleString("es-MX", { maximumFractionDigits: 0 })

        const formateaFecha = (ymd) => {
            if (!ymd) return "—"
            const p = String(ymd).split("-")
            return p.length === 3 ? p[2] + "/" + p[1] + "/" + p[0] : ymd
        }

        const diaSemana = (ymd) => {
            const p = String(ymd || "").split("-").map(Number)
            if (p.length !== 3) return ""
            return DIAS_SEMANA[new Date(p[0], p[1] - 1, p[2]).getDay()] || ""
        }

        const setBadge = (selector, texto, tipo) => {
            const el = $(selector)
            el.removeClass("pgl-badge-ok pgl-badge-warn pgl-badge-danger").text(texto || "")
            if (texto && tipo) el.addClass("pgl-badge-" + tipo)
        }

        const badge = (texto, clase) => '<span class="cd-badge ' + clase + '">' + escHtml(texto) + "</span>"

        // Las consultas visibles usan el aviso de espera; los refrescos automáticos tras el cierre van en silencio.
        const peticion = (url, datos, silencioso) => new Promise((resolve) => {
            if (!silencioso) return consultaServidor(url, datos, resolve)
            $.ajax({ url, type: "POST", data: datos, dataType: "json" })
                .done((r) => resolve(r || { success: false, mensaje: "" }))
                .fail(() => resolve({ success: false, mensaje: "" }))
        })

        const opcionesTabla = () => ({
            dom: "<'row'<'col-sm-6'l><'col-sm-6'f>><'cd-tabla-scroll'tr><'row'<'col-sm-5'i><'col-sm-7'p>>",
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            order: [],
            autoWidth: false,
            language: {
                emptyTable: "No hay datos disponibles",
                paginate: { previous: "Anterior", next: "Siguiente" },
                info: "Mostrando de _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Sin registros para mostrar",
                infoFiltered: "(filtrado de _MAX_ registros)",
                zeroRecords: "No se encontraron registros",
                lengthMenu: "Mostrar _MENU_ registros por página",
                search: "Buscar:"
            }
        })

        const llenaTabla = (id, filas) => {
            const tabla = $("#" + id).DataTable()
            tabla.clear()
            tabla.rows.add(filas)
            tabla.draw()
            tabla.columns.adjust()
        }

        const filasTabla = (id) => $($("#" + id).DataTable().rows().nodes())

        const ajustaTablas = () => {
            [ID_TABLA_PRECIERRE, ID_TABLA_APLICAR, ID_TABLA_PDI].forEach((id) => {
                if ($.fn.DataTable.isDataTable("#" + id)) $("#" + id).DataTable().columns.adjust()
            })
        }

        /* ---------------------------------------------------------------- Consulta del día */

        const actualizaEstadoConsulta = () => {
            const hayConsulta = !!estado.fecha
            $(".cd-sin-consulta").toggle(!hayConsulta)
            $(".cd-contenido").toggle(hayConsulta)
        }

        const consultar = () => {
            const fecha = $("#fechaConsulta").val()
            if (!fecha) {
                $("#fechaConsulta").addClass("incorrecto").focus()
                return showWarning("Seleccione la fecha a consultar.")
            }
            $("#fechaConsulta").removeClass("incorrecto")
            estado.fecha = fecha
            actualizaEstadoConsulta()
            cargarPrecierre(false)
            cargarPagos(false)
            cargarAplicar(false)
            cargarNoIdentificados(false)
            cargarHistorico(false)
        }

        // Tras un proceso se actualizan en segundo plano las secciones cuyos datos cambió.
        const refrescarTrasProceso = (...secciones) => {
            if (!estado.fecha) return Promise.resolve()
            const cargadores = {
                precierre: cargarPrecierre,
                pagos: cargarPagos,
                historico: cargarHistorico,
                aplicar: cargarAplicar,
                noIdentificados: cargarNoIdentificados
            }
            return Promise.all(secciones.map((s) => cargadores[s](true)))
        }

        // El seguimiento del cierre (controlador) los invoca cuando el proceso cambia de estado.
        window.refrescarHistoricoCierreDia = () => refrescarTrasProceso("historico")
        window.refrescarOperativosCierreDia = () => refrescarTrasProceso("precierre", "pagos", "aplicar", "noIdentificados")

        /* ---------------------------------------------------------------- Resultado de procesos */

        const mostrarResultado = (cfg) => {
            const conObservacion = (cfg.observaciones || 0) > 0
            $("#lblResultadoTitulo").text(cfg.titulo)
            $("#lblResultadoFecha").text(formateaFecha(cfg.fecha))
            $("#thResultadoClave").text(cfg.columnaClave)
            $("#alertaResultadoProceso")
                .toggleClass("pgl-alerta-aviso", conObservacion)
                .toggleClass("ppc-alerta-ok", !conObservacion)
            $("#iconoResultadoProceso").attr("class", "fa " + (conObservacion ? "fa-exclamation-triangle" : "fa-check"))
            $("#txtResultadoProceso").text(cfg.mensaje || "")
            $("#badgeResImportacion").toggle(!!cfg.idImportacion)
            $("#lblResImportacion").text(cfg.idImportacion || "")
            $("#lblResProcesados").text(cfg.total || 0)
            $("#lblResCorrectos").text(cfg.correctos || 0)
            $("#lblResObservacion").text(cfg.observaciones || 0)
            llenaTabla(ID_TABLA_RESULTADO, cfg.filas || [])
            $("#modalResultadoProceso").modal("show")
        }

        const filaResultado = (d, clave) => [
            escHtml(d.CDGNS),
            escHtml(d.CICLO),
            escHtml(TIPOS_MOVIMIENTO[d.TIPO] || d.TIPO || ""),
            escHtml(clave),
            moneda(d.MONTO),
            '<span class="ft-conteo ppc-estado ' + (d.CORRECTO ? "pgl-badge-ok" : "pgl-badge-warn") + '">' + escHtml(d.ESTADO) + "</span>",
            escHtml(d.MENSAJE || "")
        ]

        /* ---------------------------------------------------------------- Pre cierre */

        const clavePrecierre = (m) => [m.CDGNS, m.CICLO, m.SECUENCIA, m.TIPO].join("|")

        const avisoSaldo = (m) => {
            if (!m.EXCEDE) return ""
            let texto = "El descuento es mayor al interés pendiente del crédito."
            if (m.TIPO === "R") {
                texto = m.SALDO_TOTAL > 0
                    ? "El monto cubre el saldo total: el ajuste liquida el crédito."
                    : "El crédito no tiene saldo pendiente."
            }
            return ' <i class="fa fa-exclamation-triangle cd-aviso-saldo" title="' + escHtml(texto) + '"></i>'
        }

        const cargarPrecierre = (silencioso) => peticion("/Operaciones/ConsultaPrecierre/", { fecha: estado.fecha }, silencioso).then((r) => {
            if (!r.success) {
                if (!silencioso && r.mensaje) showError(r.mensaje)
                return
            }
            pintarPrecierre(r.datos || {})
        })

        const pintarPrecierre = (datos) => {
            estado.precierre = datos
            const resumen = datos.resumen || {}
            const cierre = datos.cierre || {}

            $("#lblPreTotal").text(entero(resumen.total))
            $("#lblPreMonto").text(moneda(resumen.monto))
            $("#lblPreRefinanciamientos").text(entero(resumen.refinanciamientos))
            $("#lblPreMontoRefinanciamientos").text(moneda(resumen.montoRefinanciamientos))
            $("#lblPreDescuentos").text(entero(resumen.descuentos))
            $("#lblPreMontoDescuentos").text(moneda(resumen.montoDescuentos))
            if (cierre.enEjecucion) setBadge("#lblPreCierre", "Cierre en ejecución", "warn")
            else if (cierre.ejecutado) setBadge("#lblPreCierre", "Cierre ya generado", "warn")
            else setBadge("#lblPreCierre", "Cierre pendiente", "ok")

            const filas = (datos.movimientos || []).map((m) => [
                '<span class="celda-principal">' + escHtml(m.CDGNS) + "</span>"
                    + (m.NOMBRE ? '<span class="celda-secundaria">' + escHtml(m.NOMBRE) + "</span>" : ""),
                escHtml(m.CICLO),
                escHtml(m.FECHA_FMT),
                badge(TIPOS_MOVIMIENTO[m.TIPO] || m.TIPO, m.TIPO === "R" ? "cd-badge-info" : "cd-badge-warn"),
                moneda(m.MONTO),
                moneda(m.SALDO_CAPITAL),
                moneda(m.SALDO_INTERES),
                moneda(m.SALDO_TOTAL),
                '<span class="cd-saldo-despues">' + moneda(m.SALDO_DESPUES) + "</span>" + avisoSaldo(m),
                '<input type="checkbox" class="cd-sel-pre" value="' + escHtml(clavePrecierre(m)) + '" checked title="Incluir en el proceso">'
            ])
            llenaTabla(ID_TABLA_PRECIERRE, filas)
            actualizaSeleccionPrecierre()
        }

        const actualizaSeleccionPrecierre = () => {
            const movimientos = estado.precierre ? (estado.precierre.movimientos || []) : []
            const claves = new Set(filasTabla(ID_TABLA_PRECIERRE).find("input.cd-sel-pre:checked").map(function () { return this.value }).get())
            const elegidos = movimientos.filter((m) => claves.has(clavePrecierre(m)))
            const monto = elegidos.reduce((s, m) => s + (Number(m.MONTO) || 0), 0)

            $("#chkTodosPrecierre").prop("checked", movimientos.length > 0 && elegidos.length === movimientos.length)
            $("#lblSeleccionPrecierre").text(
                movimientos.length ? elegidos.length + " de " + movimientos.length + " seleccionado(s) · " + moneda(monto) : ""
            )
            $("#btnProcesarPrecierre").prop("disabled", elegidos.length === 0)
            return { elegidos, monto }
        }

        const procesarPrecierre = () => {
            if (!estado.precierre) return showWarning("Consulte primero una fecha.")
            const seleccion = actualizaSeleccionPrecierre()
            if (!seleccion.elegidos.length) return showWarning("Seleccione al menos un movimiento para procesar.")

            const fecha = estado.precierre.fecha
            let mensaje = "Se aplicarán " + seleccion.elegidos.length + " ajuste(s) por " + moneda(seleccion.monto)
                + " del " + formateaFecha(fecha) + ". Esta acción no se puede deshacer."
            if ((estado.precierre.cierre || {}).ejecutado) {
                mensaje += "\n\nEl cierre de esta fecha ya fue generado; para que refleje estos ajustes deberá regenerarse."
            }

            confirmarMovimiento("¿Procesar ajustes de pre cierre?", mensaje).then((continuar) => {
                if (!continuar) return
                const claves = seleccion.elegidos.map(clavePrecierre)
                consultaServidor("/Operaciones/ProcesaPrecierre/", { fecha, claves }, (r) => {
                    if (!r.success) return showError(r.mensaje)
                    const datos = r.datos || {}
                    const resumen = datos.resumen || {}
                    if (datos.consulta) pintarPrecierre(datos.consulta)
                    refrescarTrasProceso("pagos")
                    mostrarResultado({
                        titulo: "Resultado de los ajustes",
                        fecha,
                        columnaClave: "Secuencia",
                        mensaje: r.mensaje,
                        total: resumen.seleccionados,
                        correctos: resumen.correctos,
                        observaciones: resumen.conError,
                        filas: (datos.detalle || []).map((d) => filaResultado(d, d.SECUENCIA))
                    })
                })
            })
        }

        /* ---------------------------------------------------------------- Pagos */

        const construyeTarjetas = () => {
            $("#cardsPagos").html(CATEGORIAS.map((c) => (
                '<button type="button" class="cd-card cd-card-' + c.id + '" data-categoria="' + c.id + '" title="Ver detalle">'
                + '<span class="cd-card-top">'
                + '<span class="cd-card-icon"><i class="fa ' + c.icono + '"></i></span>'
                + '<span class="cd-card-titulo">' + escHtml(c.titulo) + "</span>"
                + '<i class="fa fa-search-plus cd-card-ver"></i>'
                + "</span>"
                + '<span class="cd-card-numero" data-campo="total">0</span>'
                + '<span class="cd-card-monto" data-campo="monto">$0.00</span>'
                + '<span class="cd-card-barra"><span data-campo="barra"></span></span>'
                + '<span class="cd-card-estados">'
                + '<span><i class="fa fa-check"></i> <b data-campo="aplicados">0</b> aplicados</span>'
                + '<span><i class="fa fa-clock-o"></i> <b data-campo="pendientes">0</b> sin aplicar</span>'
                + "</span>"
                + "</button>"
            )).join(""))
        }

        const cargarPagos = (silencioso) => peticion("/Operaciones/ResumenPagosCierre/", { fecha: estado.fecha }, silencioso).then((r) => {
            if (!r.success) {
                if (!silencioso && r.mensaje) showError(r.mensaje)
                return
            }
            const resumen = (r.datos || {}).resumen || {}
            CATEGORIAS.forEach((c) => {
                const datos = resumen[c.id] || {}
                const tarjeta = $('.cd-card[data-categoria="' + c.id + '"]')
                const total = Number(datos.total) || 0
                tarjeta.find('[data-campo="total"]').text(entero(total))
                tarjeta.find('[data-campo="monto"]').text(moneda(datos.monto))
                tarjeta.find('[data-campo="aplicados"]').text(entero(datos.aplicados))
                tarjeta.find('[data-campo="pendientes"]').text(entero(datos.pendientes))
                tarjeta.find('[data-campo="barra"]').css("width", (total ? Math.round((Number(datos.aplicados) || 0) * 100 / total) : 0) + "%")
            })
        })

        const COLUMNAS_DETALLE = {
            pagosdia: [["Crédito"], ["Ciclo"], ["Nombre"], ["Tipo"], ["Secuencia"], ["Monto", "cd-num"], ["Estado"]],
            no_identificados: [["Secuencia"], ["Referencia"], ["Cuenta"], ["Monto", "cd-num"], ["Descripción"], ["Identificado como"], ["Estado"]],
            incidencias: [["Referencia"], ["Monto", "cd-num"], ["Motivo"], ["Estado"]]
        }

        const ETIQUETAS_ESTADO = {
            no_identificados: ["Identificado", "Sin identificar"],
            incidencias: ["Resuelta", "Pendiente"]
        }

        const filaDetalle = (grupo, categoria, f) => {
            const etiquetas = ETIQUETAS_ESTADO[categoria] || ["Aplicado", "Sin aplicar"]
            const estadoFila = f.APLICADO ? badge(etiquetas[0], "cd-badge-ok") : badge(etiquetas[1], "cd-badge-warn")
            if (grupo === "pagosdia") {
                return [
                    escHtml(f.CDGNS), escHtml(f.CICLO), escHtml(f.NOMBRE || "—"),
                    escHtml(f.OPERACION || TIPOS_MOVIMIENTO[f.TIPO] || f.TIPO), escHtml(f.SECUENCIA), moneda(f.MONTO), estadoFila
                ]
            }
            if (grupo === "no_identificados") {
                return [
                    escHtml(f.SECUENCIA), escHtml(f.REFERENCIA), escHtml(f.CDGCB), moneda(f.MONTO), escHtml(f.DESCRIPCION || ""),
                    f.CDGCLNS ? escHtml(f.CDGCLNS + "-" + (f.CICLO || "")) + (f.CDGPE_IDEN ? '<span class="celda-secundaria">' + escHtml(f.CDGPE_IDEN) + "</span>" : "") : "—",
                    estadoFila
                ]
            }
            return [escHtml(f.REFERENCIA), moneda(f.MONTO), escHtml(f.MSGRESUL || ""), estadoFila]
        }

        const pintarDetalle = () => {
            if (!estado.detalle) return
            const { categoria, filas } = estado.detalle
            const filtro = $("#filtroDetalle").val()
            const grupo = ["pagos", "garantias", "ajustes"].includes(categoria) ? "pagosdia" : categoria
            const visibles = filas.filter((f) => filtro === "todos" || (filtro === "aplicados") === !!f.APLICADO)
            const columnas = COLUMNAS_DETALLE[grupo]
            const selector = "#" + ID_TABLA_DETALLE

            if ($.fn.DataTable.isDataTable(selector)) $(selector).DataTable().destroy()
            $(selector).empty().append(
                "<thead><tr>" + columnas.map((c) => "<th>" + escHtml(c[0]) + "</th>").join("") + "</tr></thead><tbody></tbody>"
            )
            const tabla = $(selector).DataTable(Object.assign(opcionesTabla(), {
                columnDefs: columnas.map((c, i) => (c[1] ? { targets: i, className: c[1] } : null)).filter(Boolean)
            }))
            tabla.rows.add(visibles.map((f) => filaDetalle(grupo, categoria, f))).draw()
            $("#lblDetalleConteo").text(visibles.length + " de " + filas.length + " registro(s)")
        }

        const abrirDetallePagos = (categoria) => {
            if (!estado.fecha) return
            const config = CATEGORIAS.find((c) => c.id === categoria)
            if (!config) return
            consultaServidor("/Operaciones/DetallePagosCierre/", { fecha: estado.fecha, categoria }, (r) => {
                if (!r.success) return showError(r.mensaje)
                estado.detalle = { categoria, filas: (r.datos || {}).filas || [] }
                $("#lblDetalleTitulo").text(config.titulo)
                $("#lblDetalleFecha").text(formateaFecha(estado.fecha))
                $("#lblDetalleDescripcion").text(config.descripcion)
                $("#filtroDetalle").val("todos")
                pintarDetalle()
                $("#modalDetallePagos").modal("show")
            })
        }

        /* ---------------------------------------------------------------- Histórico */

        const cargarHistorico = (silencioso) => peticion("/Operaciones/ConsultaHistoricoCierre/", { fecha: estado.fecha }, silencioso).then((r) => {
            if (!r.success) {
                if (!silencioso && r.mensaje) showError(r.mensaje)
                return
            }
            pintarHistorico((r.datos || {}).dias || [])
        })

        const pintarHistorico = (dias) => {
            estado.historico = dias
            const valor = (v, formato) => (v === null || v === undefined ? "—" : formato(v))
            $("#tbodyHistorico").html(dias.map((d, i) => {
                let acciones = "—"
                if (d.estado !== "pendiente") {
                    acciones = '<button type="button" class="btn btn-default btn-xs cd-ver-bitacora" data-indice="' + i + '" title="Ver bitácora del proceso">'
                        + '<i class="fa fa-list"></i> Bitácora</button>'
                    if (d.estado !== "procesando") {
                        acciones += '<button type="button" class="btn btn-info btn-xs cd-ver-resumen" data-fecha="' + escHtml(d.fecha) + '" title="Resumen del cierre (mismo contenido que el correo)">'
                            + '<i class="fa fa-file-text-o"></i> Resumen</button>'
                    }
                }
                return "<tr" + (d.consultada ? ' class="cd-fila-consultada"' : "") + ">"
                    + "<td>" + escHtml(d.fechaFmt) + '<span class="cd-dia-semana">' + escHtml(diaSemana(d.fecha)) + "</span></td>"
                    + "<td>" + badge(d.estadoTexto, ESTADOS_HISTORICO[d.estado] || "cd-badge-muted") + "</td>"
                    + "<td>" + escHtml(d.usuario || "—") + "</td>"
                    + "<td>" + escHtml(d.inicio || "—") + "</td>"
                    + "<td>" + escHtml(d.fin || "—") + "</td>"
                    + '<td class="cd-num">' + valor(d.registrosCierre, entero) + "</td>"
                    + '<td class="cd-num">' + valor(d.creditosDevengo, entero) + "</td>"
                    + '<td class="cd-num">' + valor(d.montoDevengo, moneda) + "</td>"
                    + '<td class="cd-acciones-fila">' + acciones + "</td>"
                    + "</tr>"
            }).join(""))
        }

        const abrirBitacora = (indice) => {
            const dia = estado.historico[indice]
            if (!dia) return
            const lineas = String(dia.mensaje || "").split(/\r?\n/).filter((l) => l.trim() !== "")
            $("#lblBitacoraFecha").text("(" + dia.fechaFmt + " · " + dia.estadoTexto + ")")
            $("#listaBitacora").html(lineas.length
                ? lineas.map((l) => {
                    const corte = l.indexOf(" - ")
                    const hora = corte > 0 ? l.slice(0, corte) : ""
                    const texto = corte > 0 ? l.slice(corte + 3) : l
                    return '<li><span class="cd-bitacora-hora">' + escHtml(hora) + "</span><span>" + escHtml(texto) + "</span></li>"
                }).join("")
                : '<li><span>El proceso no registró mensajes en la bitácora.</span></li>')
            $("#modalBitacora").modal("show")
        }

        const abrirResumenCierre = (fecha) => {
            $("#resumenCierreContenido").empty().hide()
            $("#modalInfoDiaCierreTitle").text("Resumen de cierre")
            $("#resumenCierreCargando").text("Cargando resumen…").show()
            $("#modalInfoDiaCierre").modal("show")
            peticion("/Operaciones/InformacionDiaCierre/", { fecha }, true).then((r) => {
                if (!r.success || !r.datos) {
                    $("#resumenCierreCargando").text(r.mensaje || "No fue posible cargar el resumen.")
                    return
                }
                $("#modalInfoDiaCierreTitle").text(r.datos.asunto || "Resumen de cierre")
                $("#resumenCierreCargando").hide()
                $("#resumenCierreContenido").html(r.datos.html).show()
            })
        }

        /* ---------------------------------------------------------------- Post cierre */

        const seleccionarPanelPost = (panel) => {
            estado.panelPost = panel
            $(".cd-post-opcion").each(function () {
                $(this).toggleClass("activa", $(this).data("panel") === panel)
            })
            $(".cd-post-panel").removeClass("activo")
            $("#panelPost-" + panel).addClass("activo")
            $("#postVacio").toggle(!panel)
            ajustaTablas()
        }

        // Aplicar pagos
        const claveAplicar = (f) => f.CDGNS + "|" + f.CICLO + "|" + f.SECUENCIA

        const cargarAplicar = (silencioso) => peticion("/Operaciones/ConsultaPagosPostCierre/", { fecha: estado.fecha }, silencioso).then((r) => {
            if (!r.success) {
                if (!silencioso && r.mensaje) showError(r.mensaje)
                return
            }
            pintarAplicar(r.datos || {}, r.mensaje)
        })

        const pintarAplicar = (datos, mensaje) => {
            estado.aplicar = datos
            const resumen = datos.resumen || {}
            const cierre = datos.cierre || {}

            $("#lblAplFecha").text(formateaFecha(datos.fecha))
            $("#lblAplTotalPagos").text(entero(resumen.totalPagos))
            $("#lblAplTotalMonto").text(moneda(resumen.totalMonto))
            $("#lblAplPagos").text(entero(resumen.pagos))
            $("#lblAplMontoPagos").text(moneda(resumen.montoPagos))
            $("#lblAplElectronicos").text(entero(resumen.electronicos))
            $("#lblAplMontoElectronicos").text(moneda(resumen.montoElectronicos))

            if (cierre.enEjecucion) setBadge("#lblAplEstadoCierre", "Cierre en ejecución", "warn")
            else if (!cierre.ejecutado) setBadge("#lblAplEstadoCierre", "Sin cierre", "danger")
            else setBadge("#lblAplEstadoCierre", "Cierre generado", "ok")

            const sinPendientes = !(resumen.totalPagos > 0)
            $("#alertaAplBloqueo").toggle(!sinPendientes && !datos.puedeProcesar)
            $("#txtAplBloqueo").text(mensaje || "")
            $("#alertaAplSinPendientes").toggle(sinPendientes)

            const filas = (datos.filas || []).map((f) => [
                '<input type="checkbox" class="cd-sel-apl" value="' + escHtml(claveAplicar(f)) + '"'
                    + (datos.puedeProcesar ? " checked" : " disabled") + ' title="Incluir en la aplicación">',
                escHtml(f.FECHA_FMT),
                escHtml(f.CDGNS),
                escHtml(f.CICLO),
                escHtml(f.NOMBRE || "—"),
                escHtml(f.REFERENCIA),
                escHtml(TIPOS_MOVIMIENTO[f.TIPO] || f.TIPO),
                moneda(f.MONTO)
            ])
            llenaTabla(ID_TABLA_APLICAR, filas)
            $("#chkTodosAplicar").prop("disabled", !datos.puedeProcesar)
            actualizaSeleccionAplicar()
        }

        const actualizaSeleccionAplicar = () => {
            const filas = estado.aplicar ? (estado.aplicar.filas || []) : []
            const claves = new Set(filasTabla(ID_TABLA_APLICAR).find("input.cd-sel-apl:checked").map(function () { return this.value }).get())
            const elegidas = filas.filter((f) => claves.has(claveAplicar(f)))
            const monto = elegidas.reduce((s, f) => s + (Number(f.MONTO) || 0), 0)

            $("#chkTodosAplicar").prop("checked", filas.length > 0 && elegidas.length === filas.length)
            $("#lblAplSeleccion").text(filas.length ? elegidas.length + " de " + filas.length + " seleccionado(s) · " + moneda(monto) : "")
            $("#btnAplicarPagos").prop("disabled", !(estado.aplicar && estado.aplicar.puedeProcesar) || elegidas.length === 0)
            return { elegidas, monto }
        }

        const aplicarPagos = () => {
            if (!estado.aplicar) return showWarning("Consulte primero una fecha.")
            if (!estado.aplicar.puedeProcesar) return showWarning("Los pagos de esta fecha no se pueden aplicar en este momento.")
            const seleccion = actualizaSeleccionAplicar()
            if (!seleccion.elegidas.length) return showWarning("Seleccione al menos un pago para aplicar.")

            const fecha = estado.aplicar.fecha
            const mensaje = "Se importarán y conciliarán " + seleccion.elegidas.length + " pago(s) por " + moneda(seleccion.monto)
                + " del " + formateaFecha(fecha) + ". Esta acción no se puede deshacer."

            confirmarMovimiento("¿Aplicar pagos en cartera?", mensaje).then((continuar) => {
                if (!continuar) return
                const claves = seleccion.elegidas.map(claveAplicar)
                consultaServidor("/Operaciones/ProcesaPagosPostCierre/", { fecha, seleccion: 1, claves }, (r) => {
                    if (!r.success) return showError(r.mensaje)
                    const datos = r.datos || {}
                    const resumen = datos.resumen || {}
                    if (datos.consulta) pintarAplicar(datos.consulta, "")
                    refrescarTrasProceso("pagos")
                    mostrarResultado({
                        titulo: "Resultado de la aplicación de pagos",
                        fecha,
                        columnaClave: "Referencia",
                        mensaje: r.mensaje,
                        idImportacion: datos.idImportacion,
                        total: resumen.totalPagos,
                        correctos: resumen.correctos,
                        observaciones: resumen.conObservacion,
                        filas: (datos.detalle || []).map((d) => filaResultado(d, d.REFERENCIA))
                    })
                })
            })
        }

        // Liquidación de garantías
        const limpiarGL = () => {
            estado.gl = null
            $("#glResultado").hide()
            $("#glVacio").show()
            $("#glRegistrar").prop("disabled", true)
            $("#glFechaAplicacion").val("").attr("min", "").attr("max", "")
        }

        const sugerirFechaGL = (minima, maxima, consulta) => (consulta && consulta >= minima && consulta <= maxima ? consulta : minima)

        const mostrarGL = (datos, mensaje) => {
            estado.gl = datos
            $("#glVacio").hide()
            $("#lblGlCredito").text(datos.CDGNS || "—")
            $("#lblGlCiclo").text(datos.CICLO || "—")
            $("#lblGlSaldo").text(moneda(datos.SALDO))
            $("#lblGlGarantia").text(moneda(datos.GARANTIA))
            $("#lblGlFechaFin").text(datos.FECHA_FIN_FMT || "—")
            $("#lblGlReferencia").text(datos.REFERENCIA || "—")
            $("#glCardGarantia").toggleClass("es-liquida", !!datos.LIQUIDA)

            if (datos.RANGO_DISPONIBLE) {
                setBadge("#lblGlRango", formateaFecha(datos.FECHA_MINIMA) + " a " + formateaFecha(datos.FECHA_MAXIMA), "ok")
                $("#glFechaAplicacion")
                    .attr("min", datos.FECHA_MINIMA)
                    .attr("max", datos.FECHA_MAXIMA)
                    .val(sugerirFechaGL(datos.FECHA_MINIMA, datos.FECHA_MAXIMA, datos.FECHA_CONSULTA))
                $("#alertaGlSinRango").hide()
                $("#glRegistrar").prop("disabled", (Number(datos.GARANTIA) || 0) <= 0)
            } else {
                setBadge("#lblGlRango", "Sin fechas disponibles", "warn")
                $("#glFechaAplicacion").val("").attr("min", "").attr("max", "")
                $("#txtGlSinRango").text(mensaje || "No hay rango de fechas disponible para aplicar la liquidación.")
                $("#alertaGlSinRango").show()
                $("#glRegistrar").prop("disabled", true)
            }

            $("#alertaGlLiquidacion").toggle(!!datos.LIQUIDA)
            setBadge("#lblGlEstado", datos.LIQUIDA ? "Liquidará el crédito" : "Ciclo 01 · Entregado", datos.LIQUIDA ? "danger" : "ok")
            $("#glResultado").show()
        }

        const consultarGL = () => {
            const credito = ($("#glCredito").val() || "").trim()
            if (!/^\d{1,6}$/.test(credito)) {
                $("#glCredito").addClass("incorrecto").focus()
                return showWarning(credito === "" ? "Debe ingresar un número de crédito." : "El número de crédito debe ser numérico (máximo 6 dígitos).")
            }
            $("#glCredito").removeClass("incorrecto")
            consultaServidor("/Operaciones/ConsultaLiquidacionGL/", { credito }, (r) => {
                if (!r.success) {
                    limpiarGL()
                    return showError(r.mensaje)
                }
                mostrarGL(r.datos, r.mensaje)
            })
        }

        const registrarGL = () => {
            const gl = estado.gl
            if (!gl) return showWarning("Consulte primero un crédito.")
            if (!gl.RANGO_DISPONIBLE) return showWarning("No hay fechas disponibles para aplicar la liquidación.")
            const fechaPago = $("#glFechaAplicacion").val()
            if (!fechaPago) return showWarning("Seleccione la fecha de aplicación.")
            if (fechaPago < gl.FECHA_MINIMA || fechaPago > gl.FECHA_MAXIMA) {
                return showWarning("La fecha debe estar entre " + formateaFecha(gl.FECHA_MINIMA) + " y " + formateaFecha(gl.FECHA_MAXIMA) + ".")
            }
            if ((Number(gl.GARANTIA) || 0) <= 0) return showWarning("El crédito no tiene saldo de garantía líquida para aplicar.")

            let titulo = "¿Aplicar liquidación con GL?"
            let mensaje = "Se aplicará la garantía de " + moneda(gl.GARANTIA) + " al crédito " + gl.CDGNS + " con fecha " + formateaFecha(fechaPago) + "."
            if (gl.LIQUIDA) {
                titulo = "¡Atención! Este movimiento liquidará el crédito"
                mensaje = "La garantía (" + moneda(gl.GARANTIA) + ") es mayor o igual al saldo pendiente (" + moneda(gl.SALDO)
                    + "). Al confirmar, el crédito " + gl.CDGNS + " quedará liquidado. ¿Desea continuar?"
            }

            confirmarMovimiento(titulo, mensaje).then((continuar) => {
                if (!continuar) return
                consultaServidor("/Operaciones/AplicaLiquidacionGL/", { credito: gl.CDGNS, fecha_pago: fechaPago }, (r) => {
                    if (!r.success) return showError(r.mensaje)
                    showSuccess(r.mensaje).then(() => {
                        const datos = r.datos || {}
                        if (datos.consulta) return mostrarGL(datos.consulta, datos.consulta_mensaje || "")
                        limpiarGL()
                        if (datos.liquida) showInfo("El crédito fue liquidado. Ya no aparece en situación Entregado / ciclo 01.")
                    })
                })
            })
        }

        // Pagos no identificados
        const cargarNoIdentificados = (silencioso) => peticion("/Operaciones/ConsultaPagosNoIdentificados/", { fecha: estado.fecha }, silencioso).then((r) => {
            if (!r.success) {
                if (!silencioso && r.mensaje) showError(r.mensaje)
                return
            }
            pintarNoIdentificados(r.datos || {})
        })

        const pintarNoIdentificados = (datos) => {
            estado.noIdentificados = datos.filas || []
            const resumen = datos.resumen || {}
            $("#lblPdiFecha").text(formateaFecha(datos.fecha))
            setBadge(
                "#lblPdiResumen",
                resumen.total ? entero(resumen.total) + " pago(s) · " + moneda(resumen.monto) : "Sin pagos no identificados",
                resumen.total ? "warn" : "ok"
            )
            const filas = estado.noIdentificados.map((f, i) => [
                escHtml(f.FECHA_FMT),
                '<span class="celda-principal">' + escHtml(f.REFERENCIA || "—") + "</span>",
                escHtml(f.CDGCB),
                moneda(f.CANTIDAD),
                escHtml(f.DESCRIPCION || ""),
                escHtml(f.CDGPE_REG || "—") + (f.FECHA_REG ? '<span class="celda-secundaria">' + escHtml(f.FECHA_REG) + "</span>" : ""),
                '<button type="button" class="btn btn-warning btn-xs cd-aplicar-pdi" data-indice="' + i + '"><i class="fa fa-edit"></i> Aplicar</button>'
            ])
            llenaTabla(ID_TABLA_PDI, filas)
        }

        const abrirAplicarPdi = (indice) => {
            const pago = estado.noIdentificados[indice]
            if (!pago) return
            estado.pdiActual = pago
            $("#pdiReferencia").text(pago.REFERENCIA || "—")
            $("#pdiMonto").text(moneda(pago.CANTIDAD))
            $("#pdiDescripcion").text(pago.DESCRIPCION || "—")
            $("#pdiTipo").val("P")
            $("#pdiCredito, #pdiCiclo").val("").removeClass("incorrecto")
            $("#modalAplicarPdi").modal("show")
        }

        const aplicarPdi = () => {
            const pago = estado.pdiActual
            if (!pago) return
            const tipo = $("#pdiTipo").val()
            const credito = ($("#pdiCredito").val() || "").trim()
            const ciclo = ($("#pdiCiclo").val() || "").trim()
            if (!/^\d{1,6}$/.test(credito)) {
                $("#pdiCredito").addClass("incorrecto").focus()
                return showWarning("Capture un número de crédito numérico (máximo 6 dígitos).")
            }
            if (!/^\d{1,2}$/.test(ciclo)) {
                $("#pdiCiclo").addClass("incorrecto").focus()
                return showWarning("Capture un ciclo numérico (máximo 2 dígitos).")
            }
            $("#pdiCredito, #pdiCiclo").removeClass("incorrecto")

            const creditoFmt = credito.padStart(6, "0")
            const cicloFmt = ciclo.padStart(2, "0")
            const mensaje = "El pago de " + moneda(pago.CANTIDAD) + " se identificará como " + (TIPOS_MOVIMIENTO[tipo] || tipo).toLowerCase()
                + " del crédito " + creditoFmt + " ciclo " + cicloFmt + " y se aplicará en cartera. ¿Desea continuar?"

            confirmarMovimiento("¿Aplicar pago no identificado?", mensaje).then((continuar) => {
                if (!continuar) return
                consultaServidor("/Operaciones/AplicaPagoNoIdentificado/", {
                    fecha: pago.FECHA,
                    secuencia: pago.SECUENCIA,
                    credito: creditoFmt,
                    ciclo: cicloFmt,
                    tipo
                }, (r) => {
                    if (!r.success) return showError(r.mensaje)
                    $("#modalAplicarPdi").modal("hide")
                    refrescarTrasProceso("noIdentificados", "pagos")
                    if (r.datos && r.datos.observacion) showWarning(r.mensaje)
                    else showSuccess(r.mensaje)
                })
            })
        }

        /* ---------------------------------------------------------------- Inicialización */

        $("#" + ID_TABLA_PRECIERRE).DataTable(Object.assign(opcionesTabla(), {
            columnDefs: [
                { targets: [4, 5, 6, 7, 8], className: "cd-num" },
                { targets: 9, orderable: false, className: "cd-col-check" }
            ]
        }))
        $("#" + ID_TABLA_APLICAR).DataTable(Object.assign(opcionesTabla(), {
            columnDefs: [
                { targets: 0, orderable: false, className: "cd-col-check" },
                { targets: 7, className: "cd-num" }
            ]
        }))
        $("#" + ID_TABLA_PDI).DataTable(Object.assign(opcionesTabla(), {
            columnDefs: [
                { targets: 3, className: "cd-num" },
                { targets: 6, orderable: false }
            ]
        }))
        $("#" + ID_TABLA_RESULTADO).DataTable(Object.assign(opcionesTabla(), {
            columnDefs: [{ targets: 4, className: "cd-num" }]
        }))
        construyeTarjetas()

        $("#btnConsultar").on("click", consultar)
        $("#fechaConsulta").on("keypress", (e) => {
            if (e.key === "Enter") consultar()
        })

        $('.cd-tabs a[data-toggle="tab"]').on("shown.bs.tab", ajustaTablas)

        $("#btnProcesarPrecierre").on("click", procesarPrecierre)
        $("#" + ID_TABLA_PRECIERRE).on("change", "input.cd-sel-pre", actualizaSeleccionPrecierre)
        $("#chkTodosPrecierre").on("click", (e) => e.stopPropagation()).on("change", function () {
            filasTabla(ID_TABLA_PRECIERRE).find("input.cd-sel-pre").prop("checked", this.checked)
            actualizaSeleccionPrecierre()
        })

        $("#cardsPagos").on("click", ".cd-card", function () {
            abrirDetallePagos($(this).data("categoria"))
        })
        $("#filtroDetalle").on("change", pintarDetalle)
        $("#modalDetallePagos").on("shown.bs.modal", () => {
            if ($.fn.DataTable.isDataTable("#" + ID_TABLA_DETALLE)) $("#" + ID_TABLA_DETALLE).DataTable().columns.adjust()
        })

        $("#tbodyHistorico")
            .on("click", ".cd-ver-bitacora", function () {
                abrirBitacora(Number($(this).data("indice")))
            })
            .on("click", ".cd-ver-resumen", function () {
                abrirResumenCierre(String($(this).data("fecha")))
            })

        $(".cd-post-opcion").on("click", function () {
            seleccionarPanelPost($(this).data("panel"))
        })

        $("#btnAplicarPagos").on("click", aplicarPagos)
        $("#" + ID_TABLA_APLICAR).on("change", "input.cd-sel-apl", actualizaSeleccionAplicar)
        $("#chkTodosAplicar").on("click", (e) => e.stopPropagation()).on("change", function () {
            filasTabla(ID_TABLA_APLICAR).find("input.cd-sel-apl").not(":disabled").prop("checked", this.checked)
            actualizaSeleccionAplicar()
        })

        $("#glBuscar").on("click", consultarGL)
        $("#glCredito").on("keypress", (e) => {
            if (e.key === "Enter") consultarGL()
        })
        $("#glRegistrar").on("click", registrarGL)

        $("#" + ID_TABLA_PDI).on("click", ".cd-aplicar-pdi", function () {
            abrirAplicarPdi(Number($(this).data("indice")))
        })
        $("#btnAplicarPdi").on("click", aplicarPdi)

        $("#modalResultadoProceso").on("shown.bs.modal", () => {
            $("#" + ID_TABLA_RESULTADO).DataTable().columns.adjust()
        })
    })
</script>

<?= $footer; ?>
