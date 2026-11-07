<?php
$diasReversoHtml = isset($diasReverso) ? (int) $diasReverso : 15;
echo $header;
?>

<div class="right_col">
    <div class="panel panel-default folios-tarjeta-page pagos-gl-page cancela-ref-page">
        <div class="panel-body">
            <div class="x_title">
                <h3>Cancelación de refinanciamientos y descuentos</h3>
                <div class="clearfix"></div>
            </div>

            <p class="text-muted ft-descripcion">
                Reverse los refinanciamientos y descuentos aplicados en el último ciclo de un crédito liquidado, dentro de los
                <?php echo $diasReversoHtml; ?> días posteriores a su registro. El reverso registra el ajuste, regresa el ciclo a
                estatus entregado y cancela el movimiento.
            </p>

            <div class="panel-card ft-toolbar">
                <div class="ft-toolbar-inner">
                    <div class="ft-search">
                        <label class="ft-tb-lbl" for="noCredito">
                            <i class="fa fa-search"></i> Número de crédito
                        </label>
                        <div class="ft-search-line">
                            <input class="form-control"
                                   type="text"
                                   id="noCredito"
                                   placeholder="Ej. 019692"
                                   maxlength="6"
                                   autocomplete="off">
                            <button type="button" class="btn btn-primary" id="buscarRef">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <div class="ft-toolbar-sep" aria-hidden="true"></div>
                    <div class="ft-toolbar-hint">
                        <p>Ingrese el número de crédito para consultar sus refinanciamientos y descuentos activos.</p>
                    </div>
                </div>
            </div>

            <div id="estadoInicial" class="ft-vacio panel-card pgl-vacio">
                <div class="pgl-vacio-icon"><i class="fa fa-undo"></i></div>
                <h4>Sin consulta activa</h4>
            </div>

            <div class="resultado">
                <div class="panel-card">
                    <div class="body">
                        <table class="table table-striped table-bordered table-hover" id="movimientos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Nombre</th>
                                    <th>Crédito</th>
                                    <th>Ciclo</th>
                                    <th>Situación</th>
                                    <th>Saldo</th>
                                    <th>Operación</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Ejecutivo</th>
                                    <th>Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>

<style>
    .cancela-ref-page #movimientos > thead > tr > th,
    .cancela-ref-page #movimientos > tbody > tr > td {
        padding: 6px 3px;
        font-size: 12px;
        vertical-align: middle;
    }

    .cancela-ref-page .resultado .panel-card .body {
        padding: 12px;
    }

    .cancela-ref-page .dataTables_wrapper > .row {
        margin-left: 0;
        margin-right: 0;
    }

    .cancela-ref-page .dataTables_wrapper > .row > [class*="col-"] {
        padding-left: 0;
        padding-right: 0;
    }

    .cancela-ref-page .dataTables_filter input {
        max-width: 140px;
    }

    .cancela-ref-page #movimientos > thead > tr > th {
        font-size: 10px;
        letter-spacing: 0;
    }

    .cancela-ref-page #movimientos > thead > tr > th:last-child,
    .cancela-ref-page #movimientos > tbody > tr > td:last-child {
        width: 115px;
        text-align: center;
    }

    .cancela-ref-page .cr-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
    }

    .cancela-ref-page .cr-motivo-texto {
        display: block;
        font-size: 11px;
        line-height: 1.3;
        color: var(--ft-muted);
        text-align: center;
    }

    .cancela-ref-page .cr-motivo-texto i {
        margin-right: 3px;
    }
</style>
