<div class="modal-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="cliente">Código de cliente SICAFIN</label>
                    <input class="form-control" id="cliente" name="cliente" value="<?= $cliente ?>" disabled>
                </div>
            </div>
            <div class="col-md-7">
                <div class="form-group">
                    <label for="nombre">Nombre del cliente</label>
                    <input class="form-control" id="nombre" name="nombre" value="<?= $nombre ?>" disabled>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="montoTrn">Segmento</label>
                    <select class="form-control" id="segmento" name="segmento">
                        <?= $opcSegmentos; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fechaI">Fecha de inicio</label>
                    <input type="date" class="form-control" id="fechaI" name="fechaI" value="<?= $fecha ?>" max="<?= $fecha; ?>" onchange="validaFechaFIF('fechaI', 'fechaF')">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fechaF">Fecha final</label>
                    <input type="date" class="form-control" id="fechaF" name="fechaF" value="<?= $fecha ?>" max="<?= $fecha; ?>" onchange="validaFechaFIF('fechaI', 'fechaF')">
                </div>
            </div>
            <div class="col-md-3" style="margin-top: 25px;">
                <button class="btn btn-primary" id="btnBuscar" onclick=buscarRendimientos()><i class="fa fa-search"></i> Buscar</button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <form name="all" id="all" method="POST">
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="tablaRendimiento">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Contrato</th>
                                    <th>Saldo</th>
                                    <th>Tasa</th>
                                    <th>Interés Devengado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= $filas; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">

</div>

<?php echo $script; ?>