<?php echo $header; ?>

<?php
$cdgnsBusqueda = isset($cdgns) ? (string) $cdgns : '';
$ConsultaDatos = (isset($ConsultaDatos) && is_array($ConsultaDatos)) ? $ConsultaDatos : null;
$ConsultaActivos = (isset($ConsultaActivos) && is_array($ConsultaActivos)) ? $ConsultaActivos : [];
$mensajeError = isset($mensaje_error) ? (string) $mensaje_error : '';
$creditoEncontrado = !empty($ConsultaDatos['NO_CREDITO']);
$tieneExcepciones = !empty($ConsultaActivos['ID_EXCEPCION']);
$exc = [
    'uno'    => !empty($ConsultaActivos['EXC_UNO']) && $ConsultaActivos['EXC_UNO'] === 'S',
    'dos'    => !empty($ConsultaActivos['EXC_DOS']) && $ConsultaActivos['EXC_DOS'] === 'S',
    'tres'   => !empty($ConsultaActivos['EXC_TRES']) && $ConsultaActivos['EXC_TRES'] === 'S',
    'cuatro' => !empty($ConsultaActivos['EXC_CUATRO']) && $ConsultaActivos['EXC_CUATRO'] === 'S',
    'cinco'  => !empty($ConsultaActivos['EXC_CINCO']) && $ConsultaActivos['EXC_CINCO'] === 'S',
    'seis'   => !empty($ConsultaActivos['EXC_SEIS']) && $ConsultaActivos['EXC_SEIS'] === 'S',
];
$politicas = [
    ['id' => 'exc_ciclo',   'name' => 'exc_ciclo',   'checked' => $exc['uno'],    'texto' => 'Política de ciclo mayor a 04.'],
    ['id' => 'exc_semanas', 'name' => 'exc_semanas', 'checked' => $exc['dos'],    'texto' => 'No cumple con las semanas necesarias para continuar.'],
    ['id' => 'exc_rango',   'name' => 'exc_rango',   'checked' => $exc['tres'],   'texto' => 'Cliente fuera del rango de semanas para crédito adicional.'],
    ['id' => 'exc_atraso',  'name' => 'exc_atraso',  'checked' => $exc['cuatro'], 'texto' => 'Días de atraso mayores a lo permitido.'],
    ['id' => 'exc_5pagos',  'name' => 'exc_5pagos',  'checked' => $exc['cinco'],  'texto' => 'No cumple con los 5 pagos requeridos.'],
    ['id' => 'exc_ahorro',  'name' => 'exc_ahorro',  'checked' => $exc['seis'],   'texto' => 'No cumple con la política de ahorro (> $ 2,500.00).'],
];
$marcadas = count(array_filter($politicas, function ($p) {
    return !empty($p['checked']);
}));
?>

<div class="right_col">
    <div class="exc-mxt-page">
        <div class="page">
            <div class="page-header">
                <div>
                    <h1>Excepciones MXT</h1>
                    <p>Configura excepciones para créditos adicionales Más por Ti</p>
                </div>
            </div>

            <div class="toolbar<?= $creditoEncontrado ? ' toolbar--con-meta' : '' ?>">
                <form action="/AhorroSimple/ExepcionesMXT/" method="GET" class="tb-search">
                    <label for="cdgns" class="tb-lbl">Número de crédito eje</label>
                    <div class="tb-search-line">
                        <input type="text"
                            class="form-control"
                            id="cdgns"
                            name="cdgns"
                            maxlength="12"
                            autocomplete="off"
                            autofocus
                            required
                            placeholder="Ej. 006592"
                            value="<?= htmlspecialchars($cdgnsBusqueda, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Buscar
                        </button>
                    </div>
                </form>

                <?php if ($creditoEncontrado) : ?>
                    <div class="tb-sep" aria-hidden="true"></div>
                    <div class="tb-meta">
                        <div class="tb-cell">
                            <span class="tb-lbl"><i class="fa fa-user"></i> Cliente</span>
                            <span class="tb-val"><?= htmlspecialchars((string) $ConsultaDatos['CLIENTE'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="tb-cell">
                            <span class="tb-lbl"><i class="fa fa-building"></i> Sucursal</span>
                            <span class="tb-val"><?= htmlspecialchars((string) $ConsultaDatos['SUCURSAL'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="tb-cell">
                            <span class="tb-lbl"><i class="fa fa-id-badge"></i> Ejecutivo</span>
                            <span class="tb-val"><?= htmlspecialchars((string) $ConsultaDatos['EJECUTIVO'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($mensajeError !== '') : ?>
                <div class="alert alert-warning" role="alert">
                    <?= htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if (!$creditoEncontrado && $cdgnsBusqueda === '' && $mensajeError === '') : ?>
                <div class="exc-empty">
                    <div class="exc-empty-icon"><i class="fa fa-search"></i></div>
                    <p>Busca un crédito tradicional para configurar sus excepciones.</p>
                    <small>Si ya tiene excepciones registradas se actualizarán; si no, se creará un nuevo registro.</small>
                </div>
            <?php endif; ?>

            <?php if ($creditoEncontrado) : ?>
                <form onsubmit="enviar_add(); return false" id="Add">
                    <input type="hidden" name="no_credito" id="no_credito" value="<?= htmlspecialchars((string) $ConsultaDatos['NO_CREDITO'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="cliente" id="cliente" value="<?= htmlspecialchars((string) $ConsultaDatos['CLIENTE'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="sucursal" id="sucursal" value="<?= htmlspecialchars((string) $ConsultaDatos['SUCURSAL'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="ejecutivo_nombre" id="ejecutivo_nombre" value="<?= htmlspecialchars((string) $ConsultaDatos['EJECUTIVO'], ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="ciclo" id="ciclo" value="<?= htmlspecialchars((string) $ConsultaDatos['CICLO'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="panel-card">
                        <div class="head">
                            <div class="head-left">
                                <h4>Políticas a exceptuar</h4>
                                <span class="head-badge" id="excCountBadge"><?= (int) $marcadas ?> de 6</span>
                            </div>
                        </div>
                        <div class="body">
                            <div class="exc-list" id="excList">
                                <?php foreach ($politicas as $i => $p) : ?>
                                    <label class="exc-item<?= !empty($p['checked']) ? ' is-on' : '' ?>" for="<?= htmlspecialchars($p['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="checkbox"
                                            class="exc-check"
                                            name="<?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?>"
                                            id="<?= htmlspecialchars($p['id'], ENT_QUOTES, 'UTF-8') ?>"
                                            <?= !empty($p['checked']) ? 'checked' : '' ?>>
                                        <span class="exc-num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                                        <span class="exc-box" aria-hidden="true"><i class="fa fa-check"></i></span>
                                        <span class="exc-item-title"><?= htmlspecialchars($p['texto'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="foot">
                            <span class="foot-hint" id="excCountHint">
                                <?= $marcadas > 0 ? $marcadas . ' excepción(es) seleccionada(s)' : 'Ninguna excepción seleccionada' ?>
                            </span>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa <?= $tieneExcepciones ? 'fa-save' : 'fa-plus' ?>"></i>
                                <?= $tieneExcepciones ? 'Actualizar excepciones' : 'Guardar excepciones' ?>
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo $footer; ?>