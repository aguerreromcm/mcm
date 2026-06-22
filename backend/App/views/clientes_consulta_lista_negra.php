<?php echo $header; ?>
<div class="right_col ln-page">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body ln-panel" style="margin-bottom: 0;">
            <div class="ln-consulta">
                <header class="ln-page-header">
                    <div class="ln-page-header-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div class="ln-page-header-text">
                        <h1 class="ln-page-title">Consulta lista negra</h1>
                        <p class="ln-page-subtitle">Verifique si un cliente se encuentra en lista negra antes de continuar con la operación en caja.</p>
                    </div>
                </header>

                <div class="ln-page-divider" role="presentation"></div>

                <div class="ln-consulta-busqueda">
                    <div class="ln-form-grid">
                        <div class="ln-consulta-campo ln-consulta-campo--cdgcl">
                            <label for="cdgcl">Número de cliente</label>
                            <input type="text" id="cdgcl" class="form-control ln-input" maxlength="20" placeholder="Ej. 015572" autocomplete="off" inputmode="numeric">
                        </div>
                        <div class="ln-consulta-campo ln-consulta-campo--curp">
                            <label for="curp">CURP</label>
                            <input type="text" id="curp" class="form-control ln-input" maxlength="18" placeholder="18 caracteres" autocomplete="off">
                        </div>
                        <div class="ln-form-actions">
                            <button type="button" id="btn_buscar" class="ln-btn ln-btn-primary">
                                <span class="ln-btn-content">
                                    <i class="glyphicon glyphicon-search"></i> Buscar
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="ln-banner" class="ln-banner" hidden>
                    <span class="ln-banner-icon" id="ln-banner-icon" aria-hidden="true"></span>
                    <span class="ln-banner-text" id="ln-banner-texto"></span>
                    <span class="ln-banner-badge" id="ln-banner-badge" hidden></span>
                </div>

                <div id="ln-estado-inicial" class="ln-estado-inicial visible">
                    <div class="ln-estado-inicial-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>
                    <h4>Realice una búsqueda</h4>
                    <p>Capture el número de cliente, el CURP o ambos y pulse <strong>Buscar</strong>.</p>
                </div>

                <div id="ln-estado-vacio" class="ln-estado-inicial">
                    <div class="ln-estado-inicial-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 11 12 14 16 9"/>
                        </svg>
                    </div>
                    <h4>Sin registros en lista negra</h4>
                    <p id="ln-estado-vacio-texto">No se encontraron coincidencias para los criterios indicados.</p>
                </div>

                <p id="ln-resultados-ayuda" class="ln-resultados-ayuda" hidden>Haga clic en un registro para ver el detalle completo.</p>

                <div id="ln-resultados" class="ln-resultados"></div>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
