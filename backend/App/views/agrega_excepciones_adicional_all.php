<?php echo $header; ?>

<div class="right_col">
    <div class="exc-mxt-page">
        <div class="page">
            <div class="page-header">
                <div>
                    <h1>Excepciones MXT</h1>
                    <p>Configura excepciones de políticas para créditos adicionales Más por Ti</p>
                </div>
            </div>

            <div class="search-layout">
                <div class="panel-card">
                    <div class="head">
                        <h4><i class="fa fa-search" style="color:var(--accent)"></i> Buscar crédito tradicional</h4>
                        <small>Cuenta eje con crédito activo</small>
                    </div>
                    <div class="body">
                        <p class="ayuda">
                            Introduce el código del crédito tradicional. Si ya tiene excepciones registradas se actualizarán;
                            si no, se creará un nuevo registro.
                        </p>
                        <form action="/AhorroSimple/ExepcionesMXT/" method="GET">
                            <div class="search-group">
                                <div class="search-field">
                                    <label for="cdgns">Número de crédito</label>
                                    <input type="text"
                                           class="form-control"
                                           id="cdgns"
                                           name="cdgns"
                                           maxlength="12"
                                           autocomplete="off"
                                           autofocus
                                           required
                                           placeholder="Ej. 006592"
                                           value="<?= isset($CDGNS) ? htmlspecialchars((string) $CDGNS, ENT_QUOTES, 'UTF-8') : '' ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
