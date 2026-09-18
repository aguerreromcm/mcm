<?= $header; ?>

<div class="right_col">
    <div class="panel panel-default folios-tarjeta-page pagos-gl-page">
        <div class="panel-body">
            <div class="x_title">
                <h3>Pagos con GL</h3>
                <div class="clearfix"></div>
            </div>

            <div class="panel-card ft-toolbar">
                <div class="ft-toolbar-inner">
                    <div class="ft-search">
                        <label class="ft-tb-lbl" for="creditoBuscar">
                            <i class="fa fa-search"></i> Número de crédito
                        </label>
                        <div class="ft-search-line">
                            <input class="form-control"
                                   type="text"
                                   id="creditoBuscar"
                                   placeholder="Ej. 019692"
                                   maxlength="6"
                                   autocomplete="off">
                            <button type="button" class="btn btn-primary" id="buscar">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <div class="ft-toolbar-sep" aria-hidden="true"></div>
                    <div class="ft-toolbar-hint">
                        <p>Ingrese el crédito para consultar saldo pendiente, monto de garantía y registrar el pago.</p>
                    </div>
                </div>
            </div>

            <div id="estadoInicial" class="ft-vacio panel-card pgl-vacio">
                <div class="pgl-vacio-icon"><i class="fa fa-money"></i></div>
                <h4>Sin consulta activa</h4>
            </div>

            <div class="resultado">
                <div class="panel-card pgl-unico" id="panelRegistro">
                    <div class="head">
                        <h4><i class="fa fa-info-circle"></i> Resumen del crédito</h4>
                        <div class="pgl-head-badges">
                            <span class="ft-conteo" id="lblEstadoCredito"></span>
                            <span class="ft-conteo" id="lblRango"></span>
                        </div>
                    </div>
                    <div class="body">
                        <div id="alertaLiquidacion" class="pgl-alerta pgl-alerta-peligro" style="display: none;">
                            <span class="pgl-alerta-icon"><i class="fa fa-exclamation-triangle"></i></span>
                            <div>
                                <strong>Este movimiento liquidará el crédito</strong>
                                <p>La garantía es mayor o igual al saldo pendiente. Al confirmar, el crédito quedará liquidado.</p>
                            </div>
                        </div>

                        <div id="alertaSinRango" class="pgl-alerta pgl-alerta-aviso" style="display: none;">
                            <span class="pgl-alerta-icon"><i class="fa fa-calendar-times-o"></i></span>
                            <div>
                                <strong>Sin rango de fechas disponible</strong>
                                <p id="txtSinRango"></p>
                            </div>
                        </div>

                        <div class="pgl-identidad">
                            <div class="pgl-id-item pgl-id-credito">
                                <span class="pgl-id-icon"><i class="fa fa-hashtag"></i></span>
                                <div class="pgl-id-copy">
                                    <label>Crédito</label>
                                    <span id="lblCredito">—</span>
                                </div>
                            </div>
                            <div class="pgl-id-item">
                                <span class="pgl-id-icon"><i class="fa fa-refresh"></i></span>
                                <div class="pgl-id-copy">
                                    <label>Ciclo</label>
                                    <span id="lblCiclo">—</span>
                                </div>
                            </div>
                            <div class="pgl-id-item">
                                <span class="pgl-id-icon"><i class="fa fa-calendar"></i></span>
                                <div class="pgl-id-copy">
                                    <label>Fecha fin</label>
                                    <span id="lblFechaFin">—</span>
                                </div>
                            </div>
                            <div class="pgl-id-item pgl-id-ref">
                                <span class="pgl-id-icon"><i class="fa fa-barcode"></i></span>
                                <div class="pgl-id-copy">
                                    <label>Referencia</label>
                                    <span id="lblReferencia">—</span>
                                </div>
                            </div>
                        </div>

                        <div class="pgl-operacion">
                            <div class="pgl-money-grid">
                                <div class="pgl-money-card pgl-money-saldo">
                                    <div class="pgl-money-top">
                                        <span class="pgl-money-label">Saldo pendiente</span>
                                        <i class="fa fa-line-chart"></i>
                                    </div>
                                    <div class="pgl-money-value" id="lblSaldo">—</div>
                                    <p class="pgl-money-note">Monto por cobrar del crédito</p>
                                </div>
                                <div class="pgl-money-card pgl-money-garantia" id="cardGarantia">
                                    <div class="pgl-money-top">
                                        <span class="pgl-money-label">Garantía líquida (GL)</span>
                                        <i class="fa fa-shield"></i>
                                    </div>
                                    <div class="pgl-money-value" id="lblGarantia">—</div>
                                    <p class="pgl-money-note">Monto que se aplicará al registrar</p>
                                </div>
                            </div>

                            <div class="pgl-accion">
                                <div class="pgl-registro-fecha">
                                    <label class="ft-tb-lbl" for="fechaAplicacion">
                                        <i class="fa fa-calendar"></i> Fecha de aplicación
                                    </label>
                                    <div class="pgl-accion-controls">
                                        <input type="date" class="form-control" id="fechaAplicacion">
                                        <button type="button" class="btn btn-success pgl-btn-aplicar" id="btnRegistrar" disabled>
                                            <i class="fa fa-check"></i>
                                            <span>Registrar pago con GL</span>
                                        </button>
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

<?= $footer; ?>
