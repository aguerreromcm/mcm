<?= $header; ?>

<div class="right_col">
    <div class="panel">
        <div class="panel-header" style="padding: 10px;">
            <div class="x_title">
                <label style="font-size: large;"> Cancelación de refinanciamientos</label>
                <div class="clearfix"></div>
            </div>
            <div class="card">
                <div class="card-header" style="margin: 20px 0;">
                    <span class="card-title">Ingrese el numero de crédito para buscar refinaciamientos </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <input class="form-control" style="font-size: 24px;" type="text" id="noCredito" placeholder="000000" maxlength="6">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary" id="buscarRef">Buscar</button>
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

<?= $footer; ?>