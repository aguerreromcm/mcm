<?php
$creditoInicial = isset($credito) ? (string) $credito : '';
echo $header;
?>

<div class="right_col">
    <div class="panel panel-default folios-tarjeta-page">
        <div class="panel-body">
            <div class="x_title">
                <h3>Folios de Tarjeta</h3>
                <div class="clearfix"></div>
            </div>

            <p class="text-muted ft-descripcion">
                Consulte el histórico de folios y asesores por ciclo. Solo el último ciclo con situación
                <strong>Entregado</strong> permite cambiar o agregar una segunda tarjeta (máximo 2),
                justificando el motivo. Los movimientos no se editan ni eliminan.
            </p>

            <div class="panel-card ft-toolbar">
                <div class="ft-toolbar-inner">
                    <div class="ft-search">
                        <label class="ft-tb-lbl" for="creditoBuscar">
                            <i class="fa fa-search"></i> Número de crédito
                        </label>
                        <div class="ft-search-line">
                            <input class="form-control"
                                   type="text"
                                   id="creditoBuscar"
                                   placeholder="Ej. 019692"
                                   maxlength="6"
                                   autocomplete="off"
                                   value="<?php echo htmlspecialchars($creditoInicial, ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="button" class="btn btn-primary" id="buscar">
                                <i class="fa fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <div class="ft-toolbar-sep" aria-hidden="true"></div>
                    <div class="ft-toolbar-hint">
                        <p>Ingrese el crédito para consultar el histórico de folios y gestionar el ciclo entregado vigente.</p>
                    </div>
                </div>
            </div>

            <div id="estadoInicial" class="ft-vacio panel-card">
                <i class="fa fa-credit-card"></i>
                <p>Busque un crédito para consultar el histórico de tarjetas de pago.</p>
            </div>

            <div class="resultado">
                <div class="panel-card ft-resumen">
                    <div class="head">
                        <h4><i class="fa fa-info-circle"></i> Resumen del crédito</h4>
                        <span class="ft-conteo" id="lblConteoFolios"></span>
                    </div>
                    <div class="body">
                        <div class="ft-resumen-grid">
                            <div class="ft-resumen-item ft-resumen-credito">
                                <label>Crédito</label>
                                <span id="lblCredito"></span>
                            </div>
                            <div class="ft-resumen-item ft-resumen-cliente">
                                <label>Cliente</label>
                                <span id="lblCliente">—</span>
                            </div>
                            <div class="ft-resumen-item">
                                <label>Ciclo en gestión</label>
                                <span id="lblCicloGestion">—</span>
                            </div>
                            <div class="ft-resumen-item">
                                <label>Asesor actual</label>
                                <span id="lblAsesorGestion">—</span>
                            </div>
                            <div class="ft-resumen-item">
                                <label>Sucursal actual</label>
                                <span id="lblSucursalGestion">—</span>
                            </div>
                        </div>

                        <div id="bloqueFoliosActivos" class="ft-bloque-folios" style="display:none;">
                            <div class="ft-bloque-folios-head">
                                <i class="fa fa-credit-card"></i>
                                <span>Tarjetas vigentes del ciclo</span>
                            </div>
                            <div class="ft-folios-activos" id="listaFoliosActivos"></div>
                        </div>

                        <div id="accionesGestion" class="ft-acciones" style="display:none;">
                            <button type="button" class="btn btn-warning" id="btnCambiar">
                                <i class="fa fa-exchange"></i> Cambiar tarjeta
                            </button>
                            <button type="button" class="btn btn-success" id="btnAdicional">
                                <i class="fa fa-plus"></i> Agregar segunda tarjeta
                            </button>
                        </div>
                        <div id="sinAcciones" class="ft-sin-gestion" style="display:none;"></div>
                        <div id="sinGestion" class="ft-sin-gestion" style="display:none;">
                            No hay ciclo con situación Entregado disponible para gestionar folios.
                        </div>
                    </div>
                </div>

                <div class="panel-card">
                    <div class="body">
                        <div class="ft-encabezado-tabla">
                            <h4 class="titulo"><i class="fa fa-history"></i> Histórico de folios</h4>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="tablaHistorico">
                            <thead>
                                <tr>
                                    <th>Ciclo</th>
                                    <th>Folio</th>
                                    <th>Asesor</th>
                                    <th>Sucursal</th>
                                    <th>Movimiento</th>
                                    <th>Motivo</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="panel-card">
                    <div class="body">
                        <div class="ft-encabezado-tabla">
                            <h4 class="titulo"><i class="fa fa-list-ol"></i> Ciclos del crédito</h4>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="tablaCiclos">
                            <thead>
                                <tr>
                                    <th>Ciclo</th>
                                    <th>Situación</th>
                                    <th>Monto</th>
                                    <th>Plazo</th>
                                    <th>Periodicidad</th>
                                    <th>Asesor</th>
                                    <th>Sucursal</th>
                                    <th>Inicio</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_folio_tarjeta" tabindex="-1" role="dialog" aria-labelledby="modalFolioTarjetaTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
                <h4 class="modal-title" id="modalFolioTarjetaTitle">Registrar folio</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tipoMov" value="">
                <div class="ft-credito-modal" id="resumenModalFolio"></div>

                <div class="form-group" id="grupoReemplazo" style="display:none;">
                    <label for="idReemplazo">Tarjeta a reemplazar *</label>
                    <select class="form-control" id="idReemplazo"></select>
                </div>
                <div class="form-group">
                    <label for="folioNuevo">Número de folio *</label>
                    <input type="text" class="form-control" id="folioNuevo" maxlength="30" autocomplete="off" placeholder="Folio de la tarjeta física">
                </div>
                <div class="form-group">
                    <label for="motivoMov">Motivo *</label>
                    <textarea class="form-control" id="motivoMov" rows="3" maxlength="4000" placeholder="Ej. extravío, espacio agotado, tarjeta dañada, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarFolio">
                    <i class="fa fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var creditoInicial = <?php echo json_encode($creditoInicial, JSON_UNESCAPED_UNICODE); ?>;
var creditoActual = '';
var cicloGestion = null;
var foliosActivos = [];

var ETIQUETA_MOV = {
    ALTA: 'Alta',
    CAMBIO: 'Cambio',
    ADICIONAL: 'Adicional'
};

function escHtml(valor) {
    return $('<div>').text(valor == null ? '' : String(valor)).html();
}

function destruirTabla(selector) {
    if ($.fn.DataTable && $(selector).length && $.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().destroy();
    }
}

function opcionesTablaFolios() {
    return {
        pageLength: 10,
        lengthMenu: [[10, 20, 40, -1], [10, 20, 40, 'Todos']],
        order: [],
        autoWidth: false,
        language: {
            emptyTable: 'No hay datos disponibles',
            paginate: { previous: 'Anterior', next: 'Siguiente' },
            info: 'Mostrando de _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'Sin registros para mostrar',
            zeroRecords: 'No se encontraron registros',
            lengthMenu: 'Mostrar _MENU_ registros por pagina',
            search: 'Buscar:'
        }
    };
}

function initTablaFolios(selector) {
    if (!$.fn.DataTable || !$(selector).length) {
        return;
    }
    destruirTabla(selector);
    $(selector).DataTable(opcionesTablaFolios());
}

function etiquetaMovimiento(tipo) {
    var clave = String(tipo || '').toUpperCase();
    var texto = ETIQUETA_MOV[clave] || tipo || '';
    var clase = clave === 'CAMBIO'
        ? 'badge-mov-cambio'
        : (clave === 'ADICIONAL' ? 'badge-mov-adicional' : 'badge-mov-alta');
    return '<span class="' + clase + '" style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;">'
        + escHtml(texto) + '</span>';
}

function celdaUsuario(idUsuario, nombreUsuario) {
    var id = String(idUsuario || '').trim();
    var nombre = String(nombreUsuario || '').trim();
    if (!id && !nombre) return '—';
    if (!id) return escHtml(nombre);
    if (!nombre) return '<span class="celda-principal">' + escHtml(id) + '</span>';
    return '<span class="celda-principal">' + escHtml(id) + '</span>'
        + '<span class="celda-secundaria">' + escHtml(nombre) + '</span>';
}

function formatoMonto(valor) {
    var num = parseFloat(valor);
    if (isNaN(num)) return '—';
    return num.toLocaleString('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function buscarFolios(cr) {
    creditoActual = String(cr != null ? cr : $('#creditoBuscar').val()).trim();
    if (creditoActual === '') {
        $('#creditoBuscar').focus();
        return showError('Debe ingresar un número de crédito.');
    }

    consultaServidor('/Creditos/ConsultaFoliosTarjeta/', { credito: creditoActual }, function (resultado) {
        if (!resultado.success) {
            $('.resultado').toggleClass('conDatos', false);
            $('#estadoInicial').show();
            return showError(resultado.mensaje);
        }
        pintarResultado(resultado.datos);
    });
}

function pintarFoliosActivos() {
    var $lista = $('#listaFoliosActivos');
    $lista.empty();
    if (!foliosActivos.length) {
        $('#bloqueFoliosActivos').hide();
        return;
    }
    foliosActivos.forEach(function (f) {
        $lista.append(
            '<span class="ft-folio-badge"><i class="fa fa-credit-card"></i> '
            + escHtml(f.FOLIO || '') + '</span>'
        );
    });
    $('#bloqueFoliosActivos').show();
}

function pintarResultado(datos) {
    cicloGestion = datos.ciclo_gestion || null;
    foliosActivos = datos.folios_activos || [];
    creditoActual = datos.credito || creditoActual;

    $('#estadoInicial').hide();
    $('.resultado').toggleClass('conDatos', true);
    $('#lblCredito').text(datos.credito || '');
    $('#lblCliente').text(datos.cliente || '—');

    if (cicloGestion) {
        $('#lblCicloGestion').text(
            'Ciclo ' + (cicloGestion.CICLO || '')
            + ' · ' + (cicloGestion.SITUACION_DESC || cicloGestion.SITUACION || 'ENTREGADO')
        );
        $('#lblAsesorGestion').text(cicloGestion.ASESOR || 'N/D');
        $('#lblSucursalGestion').text(cicloGestion.SUCURSAL || 'N/D');
        $('#sinGestion').hide();
        $('#accionesGestion').show();
        $('#btnCambiar').toggle(!!datos.puede_cambiar);
        $('#btnAdicional').toggle(!!datos.puede_adicional);

        if (foliosActivos.length === 0) {
            $('#btnAdicional').html('<i class="fa fa-plus"></i> Registrar tarjeta');
        } else {
            $('#btnAdicional').html('<i class="fa fa-plus"></i> Agregar segunda tarjeta');
        }

        if (!datos.puede_cambiar && !datos.puede_adicional && foliosActivos.length >= 2) {
            $('#sinAcciones').text('El ciclo ya tiene 2 tarjetas activas.').show();
        } else {
            $('#sinAcciones').hide();
        }
    } else {
        $('#lblCicloGestion').text('—');
        $('#lblAsesorGestion').text('—');
        $('#lblSucursalGestion').text('—');
        $('#accionesGestion').hide();
        $('#sinAcciones').hide();
        $('#sinGestion').show();
    }

    var totalHist = (datos.historico || []).length;
    var activos = foliosActivos.length;
    $('#lblConteoFolios').text(
        totalHist + ' movimiento' + (totalHist === 1 ? '' : 's')
        + (activos ? ' · ' + activos + ' vigente' + (activos === 1 ? '' : 's') : '')
    );

    pintarFoliosActivos();

    destruirTabla('#tablaHistorico');
    destruirTabla('#tablaCiclos');

    var bodyHist = $('#tablaHistorico tbody');
    bodyHist.empty();
    (datos.historico || []).forEach(function (f) {
        var estado = f.ACTIVO === 'S'
            ? '<span class="badge-vigente">Vigente</span>'
            : '<span class="badge-historico">Histórico</span>';
        bodyHist.append(
            '<tr>'
            + '<td class="celda-principal">' + escHtml(f.CICLO) + '</td>'
            + '<td class="celda-principal">' + escHtml(f.FOLIO) + '</td>'
            + '<td>' + celdaUsuario(f.ID_ASESOR, f.ASESOR) + '</td>'
            + '<td>' + escHtml(f.SUCURSAL) + '</td>'
            + '<td>' + etiquetaMovimiento(f.TIPO_MOV) + '</td>'
            + '<td style="text-align:left;max-width:220px;">' + escHtml(f.MOTIVO) + '</td>'
            + '<td>' + celdaUsuario(f.ID_USUARIO, f.USUARIO) + '</td>'
            + '<td>' + escHtml(f.FECHA) + '</td>'
            + '<td>' + estado + '</td>'
            + '</tr>'
        );
    });

    var bodyCiclos = $('#tablaCiclos tbody');
    bodyCiclos.empty();
    (datos.ciclos || []).forEach(function (c) {
        var esGestion = cicloGestion && String(c.CICLO) === String(cicloGestion.CICLO);
        bodyCiclos.append(
            '<tr' + (esGestion ? ' class="fila-gestion"' : '') + '>'
            + '<td class="celda-principal">' + escHtml(c.CICLO) + '</td>'
            + '<td>' + escHtml(c.SITUACION_DESC || c.SITUACION) + '</td>'
            + '<td class="celda-principal">' + formatoMonto(c.MONTO) + '</td>'
            + '<td class="celda-principal">' + escHtml(c.PLAZO || '—') + '</td>'
            + '<td>' + escHtml(c.PERIODICIDAD_DESC || '—') + '</td>'
            + '<td>' + celdaUsuario(c.ID_ASESOR, c.ASESOR) + '</td>'
            + '<td>' + escHtml(c.SUCURSAL) + '</td>'
            + '<td>' + escHtml(c.INICIO) + '</td>'
            + '</tr>'
        );
    });

    window.setTimeout(function () {
        try {
            initTablaFolios('#tablaHistorico');
            initTablaFolios('#tablaCiclos');
        } catch (e) {
            console.error('DataTables folios tarjeta:', e);
        }
    }, 0);
}

function abrirModal(tipo) {
    if (!cicloGestion) {
        return showWarning('No hay ciclo entregado para gestionar.');
    }
    $('#tipoMov').val(tipo);
    $('#folioNuevo').val('');
    $('#motivoMov').val('');
    $('#idReemplazo').empty();

    var titulo = tipo === 'CAMBIO'
        ? 'Cambiar tarjeta'
        : (foliosActivos.length === 0 ? 'Registrar tarjeta' : 'Agregar segunda tarjeta');

    $('#modalFolioTarjetaTitle').text(titulo);
    $('#resumenModalFolio').html(
        '<strong>Crédito:</strong> ' + escHtml(creditoActual)
        + '<br><strong>Ciclo:</strong> ' + escHtml(cicloGestion.CICLO)
        + '<br><strong>Asesor:</strong> ' + escHtml(cicloGestion.ASESOR || 'N/D')
        + '<br><strong>Sucursal:</strong> ' + escHtml(cicloGestion.SUCURSAL || 'N/D')
    );

    if (tipo === 'CAMBIO') {
        $('#grupoReemplazo').show();
        foliosActivos.forEach(function (f) {
            $('#idReemplazo').append(
                $('<option>').val(f.ID).text((f.FOLIO || '') + ' · ' + (f.ASESOR || ''))
            );
        });
    } else {
        $('#grupoReemplazo').hide();
    }

    $('#modal_folio_tarjeta').modal('show');
    window.setTimeout(function () { $('#folioNuevo').focus(); }, 300);
}

function guardarFolio() {
    var folio = $('#folioNuevo').val().trim();
    var motivo = $('#motivoMov').val().trim();
    var tipo = $('#tipoMov').val();

    if (folio === '') return showWarning('Capture el número de folio.');
    if (motivo === '') return showWarning('Capture el motivo del movimiento.');
    if (tipo === 'CAMBIO' && !$('#idReemplazo').val()) {
        return showWarning('Seleccione la tarjeta a reemplazar.');
    }

    var payload = {
        credito: creditoActual,
        ciclo: cicloGestion.CICLO,
        folio: folio,
        motivo: motivo,
        tipo_mov: tipo,
        id_reemplazo: tipo === 'CAMBIO' ? $('#idReemplazo').val() : ''
    };

    confirmarMovimiento('Registrar folio de tarjeta', '¿Confirma registrar el movimiento?')
    .then(function (continuar) {
        if (!continuar) return;
        consultaServidor('/Creditos/RegistrarFolioTarjeta/', payload, function (resultado) {
            if (!resultado.success) return showError(resultado.mensaje);
            $('#modal_folio_tarjeta').modal('hide');
            showSuccess(resultado.mensaje).then(function () {
                buscarFolios(creditoActual);
            });
        });
    });
}

// jQuery se carga en el footer; DOMContentLoaded corre despues de esas librerias.
document.addEventListener('DOMContentLoaded', function () {
    $('.resultado').toggleClass('conDatos', false);

    $('#buscar').on('click', function () { buscarFolios(); });
    $('#creditoBuscar').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarFolios();
        }
    });
    $('#btnCambiar').on('click', function () { abrirModal('CAMBIO'); });
    $('#btnAdicional').on('click', function () { abrirModal('ADICIONAL'); });
    $('#btnGuardarFolio').on('click', guardarFolio);

    if (creditoInicial) {
        $('#creditoBuscar').val(creditoInicial);
        buscarFolios(creditoInicial);
    }
});
</script>

<?php echo $footer; ?>
