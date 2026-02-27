<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>Dashboard Día</h3>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cerrarSesion()" style="display: none;" id="btnCerrarSesion">
                        <i class="fa fa-sign-out"></i> Cerrar Sesión API
                    </button>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">
                <div class="card-body">
                    <div class="accordion" id="accordionDias">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Login -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">
                    <i class="fa fa-lock"></i> Acceso a Radar de Cobranza
                </h5>
            </div>
            <div class="modal-body">
                <form id="loginForm">
                    <div class="form-group">
                        <label for="usuario">Usuario:</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="loginBtn">
                    <i class="fa fa-sign-in"></i> Iniciar Sesión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalle del Ejecutivo -->
<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog" aria-labelledby="modalDetalleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="modalDetalleTitle">Detalle del Ejecutivo</h4>
            </div>
            <div class="modal-body">
                <div id="modalDetalleSubtitle" class="text-muted mb-3"></div>

                <!-- Cards de información -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card info-card blue-card">
                            <div class="card-body text-center">
                                <i class="fa fa-user text-primary"></i>
                                <h4 id="codigoEjecutivo">-</h4>
                                <h5>Código del Ejecutivo</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card info-card green-card">
                            <div class="card-body text-center">
                                <i class="fa fa-dollar text-success"></i>
                                <h4 id="efectivoRecolectado">$0.00</h4>
                                <h5>Efectivo Recolectado</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card info-card yellow-card">
                            <div class="card-body text-center">
                                <i class="fa fa-clock-o text-warning"></i>
                                <h4 id="porRecolectar">$0.00</h4>
                                <h5>Por Recolectar</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card info-card red-card">
                            <div class="card-body text-center">
                                <i class="fa fa-exclamation-triangle text-danger"></i>
                                <h4 id="pendienteEfectivo">$0.00</h4>
                                <h5>Pendiente de Efectivo</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas del Día -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h3 id="cobradosDetalle" class="text-success">0</h3>
                                <p class="mb-0">Cobrados</p>
                            </div>
                            <div class="col-md-4">
                                <h3 id="pendientesDetalle" class="text-danger">0</h3>
                                <p class="mb-0">Pendientes</p>
                            </div>
                            <div class="col-md-4">
                                <h3 id="totalDetalle" class="text-primary">0</h3>
                                <p class="mb-0">Total</p>
                            </div>
                        </div>
                        <div class="progress mt-3">
                            <div id="progresoBar" class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="text-center mt-2 mb-0">
                            <span class="text-muted">Progreso de Cobranza</span>
                            <strong id="porcentajeCobrado" class="ml-2">0%</strong>
                        </p>
                    </div>
                </div>

                <!-- Botones de acciones -->
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-danger btn-md" onclick="verRutaCobranza()">
                        <i class="fa fa-map-marker"></i> Ver Ruta de Cobranza
                    </button>
                </div>

                <!-- Sección de Detalles de Créditos -->
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fa fa-credit-card fa-3x text-muted mb-3"></i>
                        <h5>Detalles de Créditos</h5>
                        <p class="text-muted">Sin datos disponibles</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ruta de Cobranza -->
<div class="modal fade" id="modalRutaCobranza" tabindex="-1" role="dialog" aria-labelledby="modalRutaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                <h4 class="modal-title" id="modalRutaTitle">
                    <i class="fa fa-map-marker"></i> Ruta de Cobranza - <span id="ejecutivoRutaNombre">-</span>
                </h4>
            </div>
            <div class="modal-body">
                <!-- Resumen de puntos -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 id="totalPuntos" class="text-primary">0</h4>
                                <small>Total Puntos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 id="puntosPago" class="text-success">0</h4>
                                <small>Pagos Registrados</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 id="montoTotal" class="text-warning">$0.00</h4>
                                <small>Monto Total</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h4 id="fechaRuta" class="text-info">-</h4>
                                <small>Fecha</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mapa -->
                <div id="map" style="width: 100%; height: 500px; border: 1px solid #ccc; border-radius: 5px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }

    .card-header .btn-link {
        text-decoration: none;
        color: #333;
    }

    .card-header .btn-link:hover {
        text-decoration: none;
        color: #007bff;
    }

    .card-header .btn-link:focus {
        text-decoration: none;
        box-shadow: none;
    }

    .accordion .card {
        margin-bottom: 0.5rem;
        border: 1px solid #dee2e6;
    }

    .accordion .card-header {
        padding: 0;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .accordion .card-header .btn {
        padding: 1rem 1.25rem;
        width: 100%;
        text-align: left;
        border: none;
        border-radius: 0;
        background: none;
    }

    .accordion .card-body {
        padding: 1.25rem;
    }

    .badge-success {
        background-color: #28a745;
    }

    .badge-danger {
        background-color: #dc3545;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-secondary {
        background-color: #6c757d;
    }

    .card .card-body {
        padding: 1rem;
    }

    .card .card-title {
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .card .card-text {
        margin-bottom: 0.75rem;
    }

    #accordionDias .btn-link i {
        transition: transform 0.2s ease;
    }

    #accordionDias .btn-link.collapsed i {
        transform: rotate(0deg);
    }

    #accordionDias .btn-link:not(.collapsed) i,
    #accordionDias .btn-link[aria-expanded="true"] i {
        transform: rotate(180deg);
    }

    /* Estilos para cards de información */
    .info-card {
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .blue-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    }

    .green-card {
        background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
    }

    .yellow-card {
        background: linear-gradient(135deg, #fff8e1 0%, #fffde7 100%);
    }

    .red-card {
        background: linear-gradient(135deg, #ffebee 0%, #fce4ec 100%);
    }

    .info-card .card-body {
        padding: 1rem 0.5rem;
    }

    .info-card h4 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0.5rem 0;
    }

    .info-card h6 {
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
        color: #666;
        font-weight: 500;
    }

    .info-card i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    /* Modal de ruta de cobranza */
    .modal-xl {
        max-width: 90%;
    }

    #map {
        min-height: 500px;
    }

    #mapLoading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        padding: 2rem;
        border-radius: 10px;
        z-index: 1000;
    }

    /* Solucionar problema de cards con diferentes alturas */
    .ejecutivos-container {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
    }

    .ejecutivo-card {
        width: 240px;
    }

    /* Para pantallas más pequeñas */
    @media (max-width: 992px) {
        .ejecutivo-card {
            flex: 0 0 calc(50% - 7.5px);
        }
    }

    @media (max-width: 576px) {
        .ejecutivo-card {
            flex: 0 0 100%;
        }
    }

    /* Asegurar que todas las cards tengan la misma altura */
    .ejecutivo-card .card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .ejecutivo-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .ejecutivo-card .card-text {
        flex: 1;
    }

    .ejecutivo-card .btn {
        margin-top: auto;
    }
</style>

<?php echo $footer; ?>