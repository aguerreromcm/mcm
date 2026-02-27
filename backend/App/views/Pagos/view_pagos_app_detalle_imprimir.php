<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="card col-md-12">
                <form name="all" id="all" method="POST">
                    <div class="row">
                        <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                            <div class="col-md-4 col-sm-4  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Ejecutivo</span>
                                <div class="count" style="font-size: 18px"><?= $Ejecutivo ?></div>
                            </div>
                            <div class="col-md-2 col-sm-2  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i>#</i> de Pagos Validados</span>
                                <div class="count" style="font-size: 30px; color: #030303"><span style="font-size: 30px; color: #030303" id="validados_r" name="validados_r"><?= $DetalleGlobal['TOTAL_VALIDADOS']; ?></span> DE <span style="font-size: 30px; color: #030303" id="total_r" name="total_r"><?= $DetalleGlobal['TOTAL_PAGOS']; ?></span></div>
                            </div>
                            <div class="col-md-3 col-sm-4  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Monto Validado</span>
                                <div class="count" style="font-size: 35px; color: #368a05">$<?= number_format($DetalleGlobal['TOTAL'], 2); ?></div>
                            </div>
                            <div class="col-md-3 col-sm-4  tile_stats_count">
                                <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Terminar Validación</span>
                                <div class="count" style="font-size: 35px; color: #368a05">
                                    <?php if ($pagos_efectivo > 0) { ?>
                                        <button type="button" id="recibo_pagos" class="btn btn-primary" style="border: 1px solid #338300; background: #40a200;" data-keyboard="false">
                                            <i class="fa fa-print" style="color: #ffffff"></i> <span style="color: #ffffff"> Imprimir Recibo de Pagos </span>
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dataTable_wrapper">
                        <hr>
                        <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                            <thead>
                                <tr>
                                    <th>Secuencia</th>
                                    <th>Cliente</th>
                                    <th>Movimiento</th>
                                    <th>Comentarios</th>
                                    <th>Fecha Captura</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?= $tabla; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver comprobante de pago -->
<div class="modal fade" id="modalComprobantePago" tabindex="-1" role="dialog" aria-labelledby="modalComprobantePagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title" id="modalComprobantePagoLabel">Comprobante de pago</h4>
                </center>
            </div>
            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="container-fluid">
                    <div id="comprobantePagoContainer" class="text-center">
                        <img src="/img/wait.gif" alt="Cargando..." id="loadingComprobantePago">
                        <img src="" alt="Comprobante" class="img-fluid" id="comprobantePagoImg" style="display:none; max-width: 100%; height: auto;" />
                        <p id="comprobantePagoError" class="text-danger" style="display:none;">No se encontró el comprobante para este pago.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>