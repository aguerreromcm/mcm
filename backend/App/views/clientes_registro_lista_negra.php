<?php echo $header; ?>
<div class="right_col ln-page">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body ln-panel" style="margin-bottom: 0;">
            <div class="ln-consulta ln-reg">
                <header class="ln-page-header">
                    <div class="ln-page-header-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <line x1="9" y1="12" x2="15" y2="12"/>
                            <line x1="12" y1="9" x2="12" y2="15"/>
                        </svg>
                    </div>
                    <div class="ln-page-header-text">
                        <h1 class="ln-page-title">Registro Lista Negra</h1>
                        <p class="ln-page-subtitle">Registre acreditados en lista negra de forma individual o masiva.</p>
                    </div>
                </header>

                <div class="ln-page-divider" role="presentation"></div>

                <div class="ln-reg-duo">
                    <section class="ln-reg-card" aria-labelledby="ln-reg-alta-title">
                        <h2 id="ln-reg-alta-title" class="ln-reg-section-title">Alta manual</h2>
                        <p class="ln-reg-section-hint">Busque por número de crédito, cliente o CURP, confirme la información y elija la causa antes de registrar.</p>

                        <div class="ln-reg-panel">
                            <div class="ln-reg-panel-body">
                                <div class="ln-reg-form-grid">
                                    <div class="ln-reg-campo">
                                        <label for="tipo_busqueda">Buscar por</label>
                                        <select id="tipo_busqueda" class="form-control ln-input">
                                            <option value="CURP">CURP</option>
                                            <option value="CLIENTE">No. cliente</option>
                                            <option value="CREDITO">No. crédito</option>
                                        </select>
                                    </div>
                                    <div class="ln-reg-campo">
                                        <label for="valor_busqueda" id="label_valor_busqueda">CURP</label>
                                        <input type="text" id="valor_busqueda" class="form-control ln-input" maxlength="18" placeholder="18 caracteres" autocomplete="off">
                                    </div>
                                    <div class="ln-reg-campo ln-reg-campo--full">
                                        <label for="causa_ln">Causa lista negra</label>
                                        <select id="causa_ln" class="form-control ln-input">
                                            <option value="">Seleccionar…</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="ln-reg-panel-foot">
                                <button type="button" id="btn_buscar_ln" class="ln-btn ln-btn-ghost">
                                    <i class="glyphicon glyphicon-search"></i> Buscar
                                </button>
                                <button type="button" id="btn_guardar_ln" class="ln-btn ln-btn-primary" disabled>
                                    <i class="glyphicon glyphicon-floppy-disk"></i> Registrar
                                </button>
                            </div>
                        </div>
                    </section>

                    <section class="ln-reg-card" aria-labelledby="ln-reg-carga-title">
                        <h2 id="ln-reg-carga-title" class="ln-reg-section-title">Carga masiva</h2>
                        <p class="ln-reg-section-hint">Una identificación por fila (<strong>CREDITO</strong>, <strong>CLIENTE</strong> o <strong>CURP</strong>). En <strong>CAUSA</strong> elija del desplegable del Excel.</p>

                        <div class="ln-reg-panel">
                            <div class="ln-reg-panel-body">
                                <div class="ln-reg-campo">
                                    <label for="archivo_ln_reg">Archivo</label>
                                    <input type="file" id="archivo_ln_reg" class="form-control ln-input ln-reg-file" accept=".xlsx,.xls,.csv,.txt,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                                </div>
                                <div class="ln-reg-carga-help">
                                    <p>Descargue el layout, llénelo y súbalo aquí para registrar varios acreditados a la vez.</p>
                                </div>
                            </div>
                            <div class="ln-reg-panel-foot ln-reg-panel-foot--split">
                                <div class="ln-reg-carga-links">
                                    <a class="ln-btn ln-btn-ghost" href="/Clientes/RegistroListaNegraLayout/" target="_blank" rel="noopener">
                                        <i class="glyphicon glyphicon-download-alt"></i> Layout Excel
                                    </a>
                                </div>
                                <button type="button" id="btn_importar_ln" class="ln-btn ln-btn-primary">
                                    <i class="glyphicon glyphicon-upload"></i> Registrar
                                </button>
                            </div>
                        </div>
                    </section>
                </div>

                <section id="preview_ln" class="ln-reg-preview ln-reg-resultado" hidden aria-live="polite"></section>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
