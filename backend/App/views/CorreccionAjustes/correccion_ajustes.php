<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;">Corrección razón de ajuste</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-header" style="margin: 20px 0;">
                    <span class="card-title">Ingrese el numero de crédito y el ciclo</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="noCredito">Crédito:</label>
                                <input class="form-control" style="font-size: 24px;" type="text" id="creditoBuscar" placeholder="000000" maxlength="6">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ciclo">Ciclo:</label>
                                <input class="form-control" style="font-size: 24px;" type="text" id="cicloBuscar" placeholder="00" maxlength="2">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group" style="min-height: 68px; display: flex; align-items: center; justify-content: space-between;">
                                <button type="button" class="btn btn-primary" id="buscar">Buscar</button>
                                <input type="hidden" id="datosEdit" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body resultado">
            <div class="row">
                <table class="table table-striped table-bordered table-hover" id="refinanciamientos">
                    <thead>
                        <tr>
                            <th>Crédito</th>
                            <th>Ciclo</th>
                            <th>Razón</th>
                            <th>Observaciones</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Concepto</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRazones" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <center>
                    <h2 class="modal-title" id="modalCDCLabel">Cambio de razón</h2>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="credito">Crédito</label>
                            <input type="text" class="form-control" id="credito" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ciclo">Ciclo</label>
                            <input type="text" class="form-control" id="ciclo" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="monto">Monto total del ajuste</label>
                            <input type="text" class="form-control" id="monto" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="text" class="form-control" id="fecha" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="razonActual">Razón actual (solo lectura)</label>
                            <input type="text" class="form-control" id="razonActual" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="razon">Nueva Razón</label>
                            <select class="form-control" id="razon">
                                <?= $razones; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="modificar">Modificar</button>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>