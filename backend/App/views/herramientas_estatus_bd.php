<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Estatus de Base de Datos</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12 estatus-page">
                <div class="estatus-toolbar">
                    <button id="btn-actualizar" type="button" class="btn btn-primary btn-circle">
                        <i class="fa fa-refresh"></i> <b>Actualizar</b>
                    </button>
                    <span id="estatus-cargando" class="estatus-cargando" style="display: none;">
                        <i class="fa fa-spinner fa-spin"></i> Consultando...
                    </span>
                    <span id="estatus-actualizado" class="estatus-actualizado-badge" title="Hora de la última consulta en servidor"></span>
                </div>

                <hr class="estatus-hr" />

                <div class="estatus-grid">
                    <!-- DB_CULTIVA -->
                    <div id="cultiva-card" class="estatus-card border-warn">
                        <div class="estatus-card-kpi">
                            <span class="estatus-card-titulo">DB_CULTIVA</span>
                            <div class="estatus-kpi-main" id="cultiva-kpi-wrap" aria-live="polite">
                                <span class="estatus-kpi-ico" id="cultiva-archive-ico" aria-hidden="true"></span>
                                <span id="cultiva-archive-status" class="estatus-kpi-text">--</span>
                            </div>
                        </div>

                        <div class="estatus-block">
                            <div id="cultiva-archive-error" class="estatus-alert" style="display: none;" role="alert"></div>
                        </div>

                        <div class="estatus-block estatus-block-almacen">
                            <div id="cultiva-rec-metricas" class="estatus-metricas-wrap" title="Límite, usado y espacio reutilizable">--</div>
                            <div id="cultiva-rec-error" class="estatus-alert" style="display: none;" role="alert"></div>
                            <div class="estatus-bar-row">
                                <div class="estatus-bar-wrap" title="Porcentaje usado del límite">
                                    <div id="cultiva-rec-bar" class="estatus-bar estatus-warn" style="width: 0%;"></div>
                                </div>
                                <span id="cultiva-rec-pct" class="estatus-bar-pct-label">--</span>
                            </div>
                        </div>
                    </div>

                    <!-- DB_MCM -->
                    <div id="mcm-card" class="estatus-card border-warn">
                        <div class="estatus-card-kpi">
                            <span class="estatus-card-titulo">DB_MCM</span>
                            <div class="estatus-kpi-main" id="mcm-kpi-wrap">
                                <span class="estatus-kpi-ico" id="mcm-archive-ico" aria-hidden="true"></span>
                                <span id="mcm-archive-status" class="estatus-kpi-text">--</span>
                            </div>
                        </div>

                        <div class="estatus-block">
                            <div id="mcm-archive-error" class="estatus-alert" style="display: none;" role="alert"></div>
                        </div>

                        <div class="estatus-block estatus-block-almacen">
                            <div id="mcm-rec-metricas" class="estatus-metricas-wrap" title="Límite, usado y espacio reutilizable">--</div>
                            <div id="mcm-rec-error" class="estatus-alert" style="display: none;" role="alert"></div>
                            <div class="estatus-bar-row">
                                <div class="estatus-bar-wrap" title="Porcentaje usado del límite">
                                    <div id="mcm-rec-bar" class="estatus-bar estatus-warn" style="width: 0%;"></div>
                                </div>
                                <span id="mcm-rec-pct" class="estatus-bar-pct-label">--</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="estatus-leyenda" aria-label="Leyenda de colores de uso de almacenamiento">
                    <span class="estatus-leyenda-item"><span class="estatus-leyenda-cuadro estatus-ok"></span> Uso &lt; 85&nbsp;%</span>
                    <span class="estatus-leyenda-item"><span class="estatus-leyenda-cuadro estatus-warn"></span> 85–90&nbsp;%</span>
                    <span class="estatus-leyenda-item"><span class="estatus-leyenda-cuadro estatus-error"></span> &gt; 90&nbsp;%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* —— Toolbar y timestamp —— */
.estatus-page .estatus-toolbar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px 16px;
    margin-bottom: 6px;
}
.estatus-cargando { color: #555; font-size: 13px; }
.estatus-actualizado-badge {
    display: inline-block;
    margin-left: auto;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 6px;
    color: #3d4a5c;
    background: #eef1f4;
    border: 1px solid #d8dee4;
    letter-spacing: 0.02em;
    min-height: 30px;
    line-height: 1.4;
    max-width: 100%;
    text-align: right;
    word-break: break-all;
}
.estatus-actualizado-badge:empty { display: none; }
.estatus-actualizado-badge.is-live { font-weight: 700; border-color: #c5ccd3; }
.estatus-hr { border-top: 1px solid #787878; margin: 10px 0 4px; }

/* —— Grid y altura de cards —— */
.estatus-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 14px;
    margin-top: 12px;
    align-items: stretch;
}
.estatus-card {
    display: flex;
    flex-direction: column;
    min-height: 190px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.08);
    border-left: 6px solid #f0ad4e;
    padding: 0;
    transition: border-color 0.25s ease, box-shadow 0.2s ease, transform 0.2s ease;
    overflow: hidden;
}
.estatus-card:hover {
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);
    transform: translateY(-1px);
}
.estatus-card.border-ok     { border-left-color: #28a745; }
.estatus-card.border-warn   { border-left-color: #f0ad4e; }
.estatus-card.border-error  { border-left-color: #dc3545; }

/* —— KPI: estado primero (VALID / …) —— */
.estatus-card-kpi {
    padding: 12px 16px 10px;
    background: linear-gradient(180deg, rgba(0,0,0,0.02) 0%, transparent 100%);
    border-bottom: 1px solid #eee;
}
.estatus-kpi-main {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-height: 42px;
    margin-top: 4px;
}
.estatus-kpi-ico { font-size: 24px; line-height: 1; user-select: none; flex-shrink: 0; }
.estatus-kpi-text {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: 0.04em;
    line-height: 1.1;
    text-align: center;
    word-break: break-word;
    text-transform: uppercase;
    font-family: "Segoe UI", system-ui, sans-serif;
}
.estatus-kpi-main.estatus-ok  .estatus-kpi-ico   { color: #198754; }
.estatus-kpi-main.estatus-ok  .estatus-kpi-text { color: #14532d; }
.estatus-kpi-main.estatus-warn .estatus-kpi-ico,
.estatus-kpi-main.estatus-warn .estatus-kpi-text { color: #b45309; }
.estatus-kpi-main.estatus-error .estatus-kpi-ico,
.estatus-kpi-main.estatus-error .estatus-kpi-text { color: #a71d2a; }
.estatus-kpi-main.estatus-ok {
    background: linear-gradient(180deg, rgba(25, 135, 84, 0.14) 0%, rgba(25, 135, 84, 0.05) 100%);
    border: 1px solid rgba(25, 135, 84, 0.28);
    border-radius: 8px;
    padding: 6px 8px;
}
.estatus-kpi-main.estatus-warn {
    background: linear-gradient(180deg, rgba(233, 160, 13, 0.16) 0%, rgba(233, 160, 13, 0.06) 100%);
    border: 1px solid rgba(233, 160, 13, 0.30);
    border-radius: 8px;
    padding: 6px 8px;
}
.estatus-kpi-main.estatus-error {
    background: linear-gradient(180deg, rgba(220, 53, 69, 0.14) 0%, rgba(220, 53, 69, 0.05) 100%);
    border: 1px solid rgba(220, 53, 69, 0.30);
    border-radius: 8px;
    padding: 6px 8px;
}

/* Badge legacy no usada en header principal; mantiene compat si añadimos clases al wrap */
.estatus-kpi-main .estatus-badge { display: none; }

/* —— Título DB —— */
.estatus-card-titulo {
    display: block;
    font-size: 15px;
    font-weight: 800;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    margin: 0 0 2px 0;
    text-align: center;
    line-height: 1.2;
    text-shadow: none;
}
.estatus-block { padding: 0 14px; margin-top: 0; }
.estatus-block + .estatus-block { border-top: 1px solid #eee; }
.estatus-block-titulo {
    font-size: 11px;
    font-weight: 600;
    color: #5a6a7a;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 10px 0 6px;
}
.estatus-hint { font-weight: 500; text-transform: none; color: #8b99a5; }
.estatus-block-almacen { flex: 1; display: flex; flex-direction: column; min-height: 0; padding-top: 8px; }
/* —— Almacenamiento: bloque de 3 métricas (misma data, lectura más rápida) —— */
.estatus-metricas-wrap { margin-bottom: 10px; }
.estatus-metricas-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    align-items: stretch;
    border-radius: 7px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: linear-gradient(180deg, #fbfdff 0%, #f5f8fc 100%);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.85);
}
.estatus-metrica {
    padding: 8px 8px 10px;
    text-align: center;
    border-left: 1px solid #e2e8f0;
    min-width: 0;
}
.estatus-metrica:first-child { border-left: 0; }
.estatus-metrica-lbl {
    display: block;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
    margin-bottom: 4px;
    line-height: 1.2;
}
.estatus-metrica-val {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    font-variant-numeric: tabular-nums;
    line-height: 1.25;
    word-break: break-word;
}
.estatus-metrica-val--muted { color: #94a3b8; font-weight: 600; }
.estatus-metricas-grid--error {
    opacity: 0.88;
    border-style: dashed;
    border-color: #cbd5e1;
    background: #f1f5f9;
}
@media (max-width: 420px) {
    .estatus-metrica { padding: 8px 4px 10px; }
    .estatus-metrica-lbl { font-size: 9px; }
    .estatus-metrica-val { font-size: 14px; }
}

/* —— Alertas compactas (ORA / error fila) —— */
.estatus-alert {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 11.5px;
    line-height: 1.35;
    margin: 6px 0 8px;
    max-height: 4.1em;
    padding: 6px 8px 6px 6px;
    border-radius: 6px;
    word-break: break-word;
    overflow: hidden;
    color: #842029;
    background: #fdecea;
    border: 1px solid #f0b4b0;
    box-shadow: inset 0 0 0 1px rgba(0,0,0,0.02);
}
.estatus-alert::before { content: "\26a0\fe0f"; font-size: 12px; line-height: 1.25; flex-shrink: 0; }
.estatus-alert.estatus-alert--sql::before { content: "\274C\fe0f"; font-size: 11px; }
.estatus-alert .estatus-alert-cuerpo { flex: 1; min-width: 0; }
.estatus-alert[style*="display: none"] { display: none !important; }

/* —— Barra + % al lado (colores: &lt;85 / 85–90 / &gt;90) —— */
.estatus-bar-row { display: flex; align-items: center; gap: 8px; margin: 2px 0 10px; }
.estatus-bar-wrap {
    flex: 1;
    min-width: 0;
    height: 10px;
    background: #e2e8f0;
    border-radius: 6px;
    overflow: hidden;
}
.estatus-bar {
    height: 100%;
    border-radius: 6px;
    transition: width 0.45s ease, background-color 0.25s ease;
}
.estatus-bar.estatus-ok    { background: #28a745; }
.estatus-bar.estatus-warn  { background: #e9a00d; }
.estatus-bar.estatus-error { background: #dc3545; }
.estatus-bar.estatus-ok    { box-shadow: 0 0 6px rgba(40, 167, 69, 0.35); }
.estatus-bar.estatus-warn  { box-shadow: 0 0 6px rgba(233, 160, 13, 0.35); }
.estatus-bar.estatus-error { box-shadow: 0 0 6px rgba(220, 53, 69, 0.35); }
.estatus-bar-pct-label {
    flex-shrink: 0;
    min-width: 3.2em;
    text-align: right;
    font-size: 13px;
    font-weight: 800;
    color: #0b1520;
    font-variant-numeric: tabular-nums;
}

/* —— Leyenda —— */
.estatus-leyenda { margin: 10px 0 4px; display: flex; flex-wrap: wrap; gap: 10px 16px; font-size: 10px; color: #6b7280; }
.estatus-leyenda-item { display: inline-flex; align-items: center; gap: 6px; }
.estatus-leyenda-cuadro { width: 12px; height: 12px; border-radius: 2px; display: inline-block; flex-shrink: 0; }
.estatus-leyenda-cuadro.estatus-ok    { background: #28a745; }
.estatus-leyenda-cuadro.estatus-warn  { background: #e9a00d; }
.estatus-leyenda-cuadro.estatus-error { background: #dc3545; }

/* —— Modo oscuro (prefers-color-scheme) —— */
@media (prefers-color-scheme: dark) {
    .estatus-cargando { color: #8b949e; }
    .estatus-actualizado-badge {
        color: #c9d1d9;
        background: #1f242b;
        border-color: #3d444d;
    }
    .estatus-actualizado-badge.is-live { border-color: #8b949e; color: #e6edf3; }
    .estatus-hr { border-color: #3d444d; }
    .estatus-card { background: #2d333b; box-shadow: 0 2px 10px rgba(0,0,0,0.38); }
    .estatus-card:hover { box-shadow: 0 7px 20px rgba(0,0,0,0.46); }
    .estatus-card-kpi { background: linear-gradient(180deg, rgba(255,255,255,0.04) 0%, transparent 100%); border-bottom-color: #3d444d; }
    .estatus-block + .estatus-block { border-color: #3d444d; }
    .estatus-card-titulo {
        color: #d1d9e0;
        text-shadow: none;
    }
    .estatus-block-titulo { color: #9ca8b3; }
    .estatus-hint { color: #6e7a86; }
    .estatus-metricas-grid {
        border-color: #3d444d;
        background: linear-gradient(180deg, #2a3038 0%, #232a32 100%);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.04);
    }
    .estatus-metrica { border-color: #3d444d; }
    .estatus-metrica-lbl { color: #8b949e; }
    .estatus-metrica-val { color: #f0f6fc; }
    .estatus-metrica-val--muted { color: #6e7a86; }
    .estatus-metricas-grid--error {
        border-color: #4a5568;
        background: #1a1f25;
    }
    .estatus-bar-wrap { background: #1a2027; border: 1px solid #2f3742; }
    .estatus-bar-pct-label { color: #ffffff; font-weight: 800; }
    .estatus-leyenda { color: #8b949e; }
    .estatus-kpi-main.estatus-ok  .estatus-kpi-text   { color: #86efac; }
    .estatus-kpi-main.estatus-ok  .estatus-kpi-ico   { color: #4ade80; }
    .estatus-kpi-main.estatus-warn .estatus-kpi-text,
    .estatus-kpi-main.estatus-warn .estatus-kpi-ico  { color: #e9a00d; }
    .estatus-kpi-main.estatus-error .estatus-kpi-text,
    .estatus-kpi-main.estatus-error .estatus-kpi-ico { color: #ff6b6b; }
    .estatus-kpi-main.estatus-ok {
        background: linear-gradient(180deg, rgba(74, 222, 128, 0.16) 0%, rgba(74, 222, 128, 0.05) 100%);
        border-color: rgba(74, 222, 128, 0.35);
    }
    .estatus-kpi-main.estatus-warn {
        background: linear-gradient(180deg, rgba(233, 160, 13, 0.20) 0%, rgba(233, 160, 13, 0.06) 100%);
        border-color: rgba(233, 160, 13, 0.35);
    }
    .estatus-kpi-main.estatus-error {
        background: linear-gradient(180deg, rgba(255, 107, 107, 0.18) 0%, rgba(255, 107, 107, 0.06) 100%);
        border-color: rgba(255, 107, 107, 0.38);
    }
    .estatus-alert { color: #ffc9c0; background: #3a1c1c; border-color: #5a2a2a; }
}

@media (prefers-color-scheme: light) {
    .estatus-page {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 8px;
    }
    .estatus-kpi-main.estatus-ok {
        background: linear-gradient(180deg, rgba(22, 163, 74, 0.12) 0%, rgba(22, 163, 74, 0.04) 100%);
        border-color: rgba(22, 163, 74, 0.24);
    }
    .estatus-kpi-main.estatus-warn {
        background: linear-gradient(180deg, rgba(202, 138, 4, 0.14) 0%, rgba(202, 138, 4, 0.05) 100%);
        border-color: rgba(202, 138, 4, 0.24);
    }
    .estatus-kpi-main.estatus-error {
        background: linear-gradient(180deg, rgba(220, 38, 38, 0.12) 0%, rgba(220, 38, 38, 0.04) 100%);
        border-color: rgba(220, 38, 38, 0.26);
    }
}

@media (max-width: 420px) {
    .estatus-metrica-val { font-size: 13px; }
}
</style>

<?php echo $footer; ?>
