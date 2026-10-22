<?= $header; ?>

<div class="right_col">
    <div class="panel panel-default folios-tarjeta-page pagos-gl-page pagos-post-cierre-page">
        <div class="panel-body">
            <div class="x_title">
                <h3>Consulta de retiros de ahorro</h3>
                <div class="clearfix"></div>
            </div>

            <div class="panel-card ft-toolbar">
                <div class="ft-toolbar-inner">
                    <div class="ft-search">
                        <label class="ft-tb-lbl" for="fechaI">
                            <i class="fa fa-calendar"></i> Fecha inicial
                        </label>
                        <div class="ft-search-line">
                            <input class="form-control" type="date" id="fechaI">
                        </div>
                    </div>
                    <div class="ft-search">
                        <label class="ft-tb-lbl" for="fechaF">
                            <i class="fa fa-calendar"></i> Fecha final
                        </label>
                        <div class="ft-search-line">
                            <input class="form-control" type="date" id="fechaF">
                        </div>
                    </div>
                    <div class="ft-toolbar-sep" aria-hidden="true"></div>
                    <div class="ft-toolbar-hint">
                        <p>Muestra las solicitudes registradas en el rango de fechas. Cada retiro toma primero el devengo disponible del cliente y el resto de sus depósitos.</p>
                        <small id="avisoHorarioCaptura" class="text-muted" style="display: block; margin-top: 5px;"></small>
                    </div>
                    <div class="ft-toolbar-actions">
                        <button type="button" class="btn btn-success" id="btnNuevaSolicitud" title="">
                            <i class="fa fa-plus"></i> Registrar Nueva Solicitud
                        </button>
                    </div>
                </div>
            </div>

            <div class="resultado">
                <div class="panel-card" style="overflow: visible;">
                    <div class="head">
                        <h4><i class="fa fa-money"></i> Solicitudes de retiro</h4>
                        <div class="pgl-head-badges">
                            <span class="ft-conteo" id="lblRegistros"></span>
                        </div>
                    </div>
                    <div class="body">
                        <div class="ppc-tabla">
                            <table class="table table-striped table-bordered table-hover" id="tablaRetiros" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Crédito</th>
                                        <th>Cantidad Solicitada</th>
                                        <th>Fechas</th>
                                        <th>Estatus</th>
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
</div>

<!-- Modal para Nueva Solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" role="dialog" aria-labelledby="modalNuevaSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title" id="modalNuevaSolicitudLabel">Nueva Solicitud de Retiro</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="formNuevaSolicitud">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Crédito (CDGNS) <span class="text-danger">*</span></label>
                                    <div class="form-group" style="display: flex; gap: 10px;">
                                        <input type="text" class="form-control" id="cdgns_buscar" maxlength="6" placeholder="Ingrese el crédito" required>
                                        <input type="hidden" id="saldo_ahorro_disponible">
                                        <input type="hidden" id="devengo_ahorro_disponible">
                                        <input type="hidden" id="solo_devengo">
                                        <input type="hidden" id="nueva_cdgns">
                                        <input type="hidden" id="nueva_ciclo">
                                        <input type="hidden" id="aniversario_ahorro">
                                        <button type="button" class="btn btn-primary" id="btnBuscarCredito">
                                            <span class="fa fa-search">&nbsp;</span>Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <input type="text" class="form-control" id="nombre_cliente" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Saldo Disponible</label>
                                    <input type="text" class="form-control" id="info_saldo_disponible" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Devengo Disponible</label>
                                    <input type="text" class="form-control" id="info_devengo_disponible" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Retiro de Devengo</label>
                                    <input type="text" class="form-control" id="info_retiro_devengo" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Retiro de Depósitos</label>
                                    <input type="text" class="form-control" id="info_retiro_depositos" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="aviso_solo_devengo" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-warning" style="padding: 8px;">
                                    El cliente cuenta con un crédito adicional activo, solo puede retirar su devengo disponible.
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Cantidad Solicitada <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="nueva_cantidad_solicitada" placeholder="0.00" required disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha Solicitud <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="nueva_fecha_solicitud" required readonly disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fecha Entrega</label>
                                    <input type="date" class="form-control" id="nueva_fecha_entrega" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Foto/Comprobante</label>
                                    <input type="file" class="form-control" id="nueva_foto" accept="image/*, application/pdf" disabled>
                                    <small class="form-text text-muted">Formatos aceptados: JPG, PNG, PDF (Máx. 5MB)</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Observaciones</label>
                                    <textarea class="form-control" id="nueva_observaciones_administradora" rows="3" placeholder="Ingrese observaciones (opcional)" disabled></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarNuevaSolicitud" disabled>
                    <span class="glyphicon glyphicon-floppy-disk"></span> Guardar Solicitud
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver detalle del retiro -->
<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title" id="modalDetalleLabel">Detalle de la solicitud de Retiro</h4>
                </center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-4 col-md-offset-8" style="display: flex; justify-content: flex-end;">
                            <button type="button" class="btn btn-info" id="btnVerComprobante">
                                <span class="glyphicon glyphicon-paperclip">&nbsp;</span>Ver Comprobante de Retiro
                            </button>
                        </div>
                    </div>

                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tabGeneral" aria-controls="tabGeneral" role="tab" data-toggle="tab">
                                <span class="glyphicon glyphicon-file"></span> Información General
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tabAdministradora" aria-controls="tabAdministradora" role="tab" data-toggle="tab">
                                <span class="glyphicon glyphicon-user"></span> Administradora
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tabCallCenter" aria-controls="tabCallCenter" role="tab" data-toggle="tab">
                                <span class="glyphicon glyphicon-earphone"></span> Call Center
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content tab-content-custom">
                        <!-- Información General -->
                        <div role="tabpanel" class="tab-pane active" id="tabGeneral">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ID Retiro</label>
                                        <input type="text" class="form-control" id="detalle_id_retiro" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Crédito</label>
                                        <input type="text" class="form-control" id="detalle_credito" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Creación</label>
                                        <input type="text" class="form-control" id="detalle_fecha_creacion" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Solicitud</label>
                                        <input type="text" class="form-control" id="detalle_fecha_solicitud" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Entrega Programada</label>
                                        <input type="text" class="form-control" id="detalle_fecha_entrega" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Entrega Real</label>
                                        <input type="text" class="form-control" id="detalle_fecha_entrega_real" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Cantidad Solicitada</label>
                                        <input type="text" class="form-control" id="detalle_cantidad_solicitada" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Retiro de Devengo</label>
                                        <input type="text" class="form-control" id="detalle_monto_devengo" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Retiro de Depósitos</label>
                                        <input type="text" class="form-control" id="detalle_monto_depositos" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Estatus</label>
                                        <input type="text" class="form-control" id="detalle_estatus" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: none;" id="grupo_motivo_cancelacion">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label id="detalle_titulo_motivo">Motivo</label>
                                        <input type="text" class="form-control" id="detalle_motivo" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Administradora -->
                        <div role="tabpanel" class="tab-pane" id="tabAdministradora">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>CDGPE</label>
                                        <input type="text" class="form-control" id="detalle_cdgpe_administradora" readonly>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" id="detalle_nombre_administradora" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Observaciones</label>
                                        <textarea class="form-control" id="detalle_observaciones_administradora" style="cursor: default; resize: none;" rows="4" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Call Center -->
                        <div role="tabpanel" class="tab-pane" id="tabCallCenter">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Estatus</label>
                                        <input type="text" class="form-control" id="detalle_estatus_call_center" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>CDGPE</label>
                                        <input type="text" class="form-control" id="detalle_cdgpe_call_center" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Fecha Proceso</label>
                                        <input type="text" class="form-control" id="detalle_fecha_procesa_call_center" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Observaciones</label>
                                        <textarea class="form-control" id="detalle_observaciones_call_center" style="cursor: default; resize: none;" rows="4" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1" role="dialog" aria-labelledby="modalComprobanteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <center>
                    <h4 class="modal-title" id="modalComprobanteLabel">Comprobante de Retiro</h4>
                </center>
            </div>
            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                <div class="container-fluid">
                    <div id="comprobanteContainer" class="text-center">
                        <img src="/img/wait.gif" alt="Descargando..." id="loadingImg">
                        <img src="" alt="Comprobante" class="img-fluid" id="comprobanteImg" style="display:none; max-width: 100%; height: auto;" />
                        <iframe src="" id="comprobantePdf" style="display:none; width: 100%; height: 75vh;" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" data-toggle="modal" data-target="#modalDetalle">
                    <span class="glyphicon glyphicon-remove"></span> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $footer; ?>

<style>
    .alert-dark {
        background-color: #000;
        border-color: #000;
        color: #fff;
    }

    .nav-tabs-custom {
        border-bottom: 2px solid #e5e5e5;
        margin-bottom: 20px;
    }

    .nav-tabs-custom>li>a {
        color: #5f5b5b;
        font-weight: 500;
        border-radius: 4px 4px 0 0;
        transition: all 0.3s ease;
    }

    .nav-tabs-custom>li>a:hover {
        background-color: #f5f5f5;
        border-color: #e3e3e3 #e3e3e3 transparent;
    }

    .nav-tabs-custom>li.active>a,
    .nav-tabs-custom>li.active>a:hover,
    .nav-tabs-custom>li.active>a:focus {
        border: 1px solid #e5e5e5;
        border-bottom-color: transparent;
        font-weight: 600;
    }

    .tab-content-custom {
        padding: 20px;
        border-radius: 0 0 4px 4px;
        min-height: 300px;
    }

    .tab-pane {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>