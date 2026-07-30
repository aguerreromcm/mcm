<?php echo $header; ?>
<?php
$credito = isset($credito) ? (string) $credito : '';
$registros = isset($registros) && is_array($registros) ? $registros : [];
$asesores = isset($asesores) && is_array($asesores) ? $asesores : [];
$sucursales = isset($sucursales) && is_array($sucursales) ? $sucursales : [];
$resultadoMasivo = isset($resultado_masivo) && is_array($resultado_masivo) ? $resultado_masivo : null;
$masivoActualizados = is_array($resultadoMasivo)
    ? (isset($resultadoMasivo['actualizados']) && is_array($resultadoMasivo['actualizados'])
        ? $resultadoMasivo['actualizados']
        : [])
    : [];
$masivoErrores = is_array($resultadoMasivo)
    ? (isset($resultadoMasivo['errores']) && is_array($resultadoMasivo['errores'])
        ? $resultadoMasivo['errores']
        : [])
    : [];
$hayResultadoMasivo = $resultadoMasivo !== null;
$totalRegistros = count($registros);
?>

<div class="right_col">
    <style>
        .reasignacion-wrap {
            --reasignacion-borde: #2f3a4f;
            --reasignacion-control-h: 34px;
        }
        .reasignacion-wrap .x_title h3 {
            margin: 4px 0 8px;
            font-size: 28px;
            font-weight: 600;
            line-height: 1.15;
        }
        .reasignacion-wrap .bloque {
            border: 1px solid var(--reasignacion-borde);
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 14px;
            background: rgba(127, 143, 166, 0.06);
        }
        .reasignacion-wrap .seccion-titulo {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px 0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            opacity: 0.7;
        }
        .reasignacion-wrap .reasignacion-descripcion {
            margin: 0 0 16px 0;
        }
        .reasignacion-wrap .reasignacion-acciones {
            display: flex;
            flex-wrap: wrap;
            margin-left: 0;
            margin-right: 0;
        }
        .reasignacion-wrap .reasignacion-acciones > [class*="col-"] {
            display: flex;
            padding-left: 0;
            padding-right: 0;
        }
        .reasignacion-wrap .reasignacion-acciones .bloque {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
        }
        /* Los dos bloques quedan pegados: se colapsa el borde compartido. */
        @media (min-width: 992px) {
            .reasignacion-wrap .reasignacion-acciones > [class*="col-"]:first-child .bloque {
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
                border-right: 0;
            }
            .reasignacion-wrap .reasignacion-acciones > [class*="col-"]:last-child .bloque {
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
                border-left: 0;
            }
        }
        .reasignacion-wrap .reasignacion-grupo-control {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 8px;
        }
        .reasignacion-wrap .reasignacion-campo {
            flex: 1 1 14rem;
            min-width: 12rem;
        }
        .reasignacion-wrap .reasignacion-campo-credito {
            flex: 0 0 11rem;
            min-width: 9rem;
            max-width: 11rem;
        }
        .reasignacion-wrap .reasignacion-campo label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
            opacity: 0.8;
        }
        .reasignacion-wrap .reasignacion-grupo-control .form-control {
            height: var(--reasignacion-control-h);
            min-height: var(--reasignacion-control-h);
            width: 100%;
            box-sizing: border-box;
        }
        .reasignacion-wrap .input-archivo {
            padding: 5px 10px;
            line-height: 1.4;
        }
        .reasignacion-wrap .reasignacion-grupo-control .btn {
            height: var(--reasignacion-control-h);
            min-height: var(--reasignacion-control-h);
            padding-top: 0;
            padding-bottom: 0;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 0;
            white-space: nowrap;
        }
        .reasignacion-wrap .reasignacion-ayuda {
            margin: 0 0 12px 0;
        }
        .reasignacion-wrap .reasignacion-encabezado-tabla {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            justify-content: space-between;
            gap: 4px 16px;
            margin-bottom: 12px;
        }
        .reasignacion-wrap .reasignacion-encabezado-tabla .titulo {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
        }
        .reasignacion-wrap .table > thead > tr > th {
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom-width: 1px;
        }
        .reasignacion-wrap .table > tbody > tr > td {
            text-align: center;
            vertical-align: middle;
        }
        .reasignacion-wrap .table .grupo-columna {
            border-left: 1px solid var(--reasignacion-borde);
            border-right: 1px solid var(--reasignacion-borde);
        }
        .reasignacion-wrap .celda-principal {
            font-weight: 600;
        }
        .reasignacion-wrap .celda-secundaria {
            display: block;
            font-size: 11px;
            font-weight: 400;
            opacity: 0.7;
        }
        .reasignacion-wrap .valor-nuevo {
            font-weight: 600;
            color: #4cae4c;
        }
        .reasignacion-wrap .valor-vacio {
            opacity: 0.4;
        }
        .reasignacion-wrap .reasignacion-vacio {
            text-align: center;
            padding: 28px 16px;
            opacity: 0.65;
        }
        .reasignacion-wrap .reasignacion-vacio i {
            font-size: 26px;
            display: block;
            margin-bottom: 8px;
        }
        .reasignacion-wrap .reasignacion-vacio p {
            margin: 0;
        }
        #modal_reasignacion .reasignacion-credito-modal {
            border: 1px solid #2f3a4f;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 16px;
            font-size: 13px;
            background: rgba(127, 143, 166, 0.06);
        }
    </style>

    <div class="panel panel-default reasignacion-wrap">
        <div class="panel-body">
            <div class="x_title">
                <h3>Reasignación</h3>
                <div class="clearfix"></div>
            </div>

            <p class="text-muted reasignacion-descripcion">
                Cambia el asesor, la sucursal o ambos de un crédito individual o mediante la carga de un Excel.
            </p>

            <?php if (!empty($mensaje_error)) : ?>
                <div class="alert alert-warning" role="alert">
                    <?php echo htmlspecialchars((string) $mensaje_error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($hayResultadoMasivo) : ?>
                <div class="alert alert-<?php echo !empty($resultadoMasivo['exito']) ? 'success' : 'warning'; ?>" role="alert">
                    <?php echo htmlspecialchars((string) ($resultadoMasivo['resumen'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    <?php if (!empty($resultadoMasivo['detalle_error'])) : ?>
                        <br><small><?php echo htmlspecialchars((string) $resultadoMasivo['detalle_error'], ENT_QUOTES, 'UTF-8'); ?></small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row reasignacion-acciones">
                <div class="col-md-6 col-sm-12">
                    <div class="bloque">
                        <h5 class="seccion-titulo"><i class="fa fa-user"></i> Reasignación individual</h5>
                        <p class="text-muted small reasignacion-ayuda">
                            Consulta el crédito para ver su asesor y sucursal actuales.
                        </p>
                        <form action="/Creditos/Reasignacion/" method="GET">
                            <div class="reasignacion-grupo-control">
                                <div class="reasignacion-campo reasignacion-campo-credito">
                                    <label for="Credito">Número de crédito</label>
                                    <input type="text" class="form-control" id="Credito"
                                           name="Credito" maxlength="12" autocomplete="off"
                                           value=""
                                           placeholder="Ej. 029535" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12">
                    <div class="bloque">
                        <h5 class="seccion-titulo"><i class="fa fa-upload"></i> Carga masiva</h5>
                        <p class="text-muted small reasignacion-ayuda">
                            Descarga el layout, captura el asesor, la sucursal o ambos en cada fila y súbelo.
                        </p>
                        <form id="form_reasignacion_masiva" action="/Creditos/ReasignacionCargaMasiva/"
                              method="POST" enctype="multipart/form-data">
                            <div class="reasignacion-grupo-control">
                                <div class="reasignacion-campo">
                                    <label for="archivo_reasignacion">Archivo Excel</label>
                                    <input type="file" id="archivo_reasignacion" class="form-control input-archivo"
                                           name="archivo"
                                           accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                                           required>
                                </div>
                                <button type="button" class="btn btn-primary" id="btn_procesar_reasignacion">
                                    <i class="fa fa-cogs"></i> Procesar
                                </button>
                                <a class="btn btn-default" href="/Creditos/ReasignacionLayout/"
                                   target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> Descargar layout
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php if ($masivoErrores !== []) : ?>
                <div class="bloque">
                    <div class="reasignacion-encabezado-tabla">
                        <h5 class="titulo">Filas no procesadas</h5>
                        <span class="text-muted small">
                            <?php echo count($masivoErrores); ?> fila(s)
                        </span>
                    </div>
                    <div class="dataTable_wrapper">
                        <table id="tablaReasignacionErrores"
                               class="table table-striped table-bordered table-hover"
                               style="width:100%">
                            <thead>
                                <tr>
                                    <th>Fila Excel</th>
                                    <th>Crédito</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($masivoErrores as $error) : ?>
                                    <tr>
                                        <td><?php echo (int) ($error['fila'] ?? 0); ?></td>
                                        <td><?php echo htmlspecialchars((string) ($error['grupo'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars((string) ($error['motivo'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            $filasTabla = $registros !== [] ? $registros : $masivoActualizados;
            $tituloTabla = $registros !== []
                ? ('Crédito ' . $credito)
                : 'Créditos procesados';
            $subtituloTabla = $registros !== []
                ? ($totalRegistros . ' registro(s) encontrado(s)')
                : (count($masivoActualizados) . ' crédito(s) actualizado(s)');
            $idTabla = $registros !== [] ? 'tablaReasignacion' : 'tablaReasignacionMasiva';
            ?>

            <?php if ($filasTabla !== []) : ?>
                <div class="bloque">
                    <div class="reasignacion-encabezado-tabla">
                        <h5 class="titulo">
                            <?php echo htmlspecialchars($tituloTabla, ENT_QUOTES, 'UTF-8'); ?>
                        </h5>
                        <span class="text-muted small">
                            <?php echo htmlspecialchars($subtituloTabla, ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>

                    <div class="dataTable_wrapper">
                        <table id="<?php echo htmlspecialchars($idTabla, ENT_QUOTES, 'UTF-8'); ?>"
                               class="table table-striped table-bordered table-hover"
                               style="width:100%">
                            <thead>
                                <tr>
                                    <th rowspan="2">Crédito / Ciclo</th>
                                    <th rowspan="2">Cliente</th>
                                    <th rowspan="2">Préstamo</th>
                                    <th rowspan="2">Situación</th>
                                    <th colspan="2" class="grupo-columna">Asesor</th>
                                    <th colspan="2" class="grupo-columna">Sucursal</th>
                                    <th rowspan="2">Acción</th>
                                </tr>
                                <tr>
                                    <th>Anterior</th>
                                    <th>Nuevo</th>
                                    <th>Anterior</th>
                                    <th>Nuevo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($filasTabla as $registro) : ?>
                                    <?php
                                    $noCredito = (string) ($registro['NO_CREDITO'] ?? '');
                                    $ciclo = (string) ($registro['CICLO'] ?? '');
                                    $asesorNuevo = trim((string) ($registro['ASESOR_NUEVO'] ?? ''));
                                    $sucursalNueva = trim((string) ($registro['SUCURSAL_NUEVA'] ?? ''));
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="celda-principal"><?php echo htmlspecialchars($noCredito, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="celda-secundaria">Ciclo <?php echo htmlspecialchars($ciclo, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars((string) ($registro['CLIENTE'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>$ <?php echo number_format((float) ($registro['MONTO'] ?? 0), 2); ?></td>
                                        <td><?php echo htmlspecialchars((string) ($registro['SITUACION'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(
                                            (string) ($registro['ASESOR_ANTERIOR'] ?? $registro['EJECUTIVO'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?></td>
                                        <td>
                                            <?php if ($asesorNuevo !== '') : ?>
                                                <span class="valor-nuevo"><?php echo htmlspecialchars($asesorNuevo, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php else : ?>
                                                <span class="valor-vacio">Sin cambio</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(
                                            (string) ($registro['SUCURSAL_ANTERIOR'] ?? $registro['SUCURSAL'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?></td>
                                        <td>
                                            <?php if ($sucursalNueva !== '') : ?>
                                                <span class="valor-nuevo"><?php echo htmlspecialchars($sucursalNueva, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php else : ?>
                                                <span class="valor-vacio">Sin cambio</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-xs"
                                                    onclick='abrirReasignacion(
                                                        <?php echo json_encode($noCredito); ?>,
                                                        <?php echo json_encode($ciclo); ?>,
                                                        <?php echo json_encode((string) ($registro['CLIENTE'] ?? '')); ?>,
                                                        <?php echo json_encode((string) ($registro['ID_ASESOR'] ?? '')); ?>,
                                                        <?php echo json_encode((string) ($registro['ID_SUCURSAL'] ?? '')); ?>,
                                                        <?php echo json_encode((string) ($registro['EJECUTIVO'] ?? '')); ?>,
                                                        <?php echo json_encode((string) ($registro['SUCURSAL'] ?? '')); ?>
                                                    )'>
                                                <i class="fa fa-exchange"></i> Reasignar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif ($credito === '' && !$hayResultadoMasivo) : ?>
                <div class="bloque">
                    <div class="reasignacion-vacio">
                        <i class="fa fa-search"></i>
                        <p>Busca un crédito para ver su asesor y sucursal actuales, o carga un layout para reasignar en lote.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="modal_reasignacion" tabindex="-1" role="dialog" aria-labelledby="modalReasignacionTitle">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form_reasignacion_individual" onsubmit="return false;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalReasignacionTitle">Reasignar crédito</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="reasignacion_credito" name="credito">
                        <input type="hidden" id="reasignacion_ciclo" name="ciclo">

                        <div class="reasignacion-credito-modal" id="reasignacion_resumen_credito"></div>

                        <div class="form-group">
                            <label for="reasignacion_sucursal">Nueva sucursal</label>
                            <select class="form-control" id="reasignacion_sucursal" name="sucursal">
                                <?php foreach ($sucursales as $sucursal) : ?>
                                    <option value="<?php echo htmlspecialchars((string) ($sucursal['ID_SUCURSAL'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars(
                                            (string) ($sucursal['ID_SUCURSAL'] ?? '') . ' - ' . (string) ($sucursal['SUCURSAL'] ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="reasignacion_asesor">Nuevo asesor</label>
                            <select class="form-control" id="reasignacion_asesor" name="asesor">
                                <option value="">Selecciona un asesor</option>
                            </select>
                        </div>

                        <p class="text-muted small" style="margin-bottom: 0;">Selecciona al menos un valor para cambiar.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btn_guardar_reasignacion">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
<?php
$catalogoAsesores = [];
foreach ($asesores as $asesorCatalogo) {
    $catalogoAsesores[] = [
        'id' => trim((string) ($asesorCatalogo['ID_ASESOR'] ?? '')),
        'nombre' => trim((string) ($asesorCatalogo['ASESOR'] ?? '')),
        'sucursal' => trim((string) ($asesorCatalogo['ID_SUCURSAL'] ?? '')),
    ];
}
?>
var reasignacionActual = { asesor: '', sucursal: '', nombreAsesor: '' };
var asesoresReasignacion = <?php echo json_encode($catalogoAsesores, JSON_UNESCAPED_UNICODE); ?>;

function escaparHtml(texto) {
    return $('<div>').text(texto == null ? '' : String(texto)).html();
}

function sucursalDestinoModal() {
    return String($('#reasignacion_sucursal').val() || '');
}

function cambiaSucursalModal() {
    var sucursal = sucursalDestinoModal();
    return sucursal !== '' && sucursal !== reasignacionActual.sucursal;
}

function refrescarAsesoresModal(asesorPreferido) {
    var $select = $('#reasignacion_asesor');
    var sucursal = sucursalDestinoModal();
    var cambiaSucursal = cambiaSucursalModal();
    var preferido = String(asesorPreferido || '');

    $select.empty();
    if (!cambiaSucursal) {
        $select.append($('<option>', { value: '', text: 'Sin cambio de asesor' }));
    } else {
        $select.append($('<option>', { value: '', text: 'Selecciona un asesor de la sucursal' }));
    }

    asesoresReasignacion.forEach(function (asesor) {
        if (String(asesor.sucursal || '') !== sucursal) {
            return;
        }
        $select.append($('<option>', {
            value: asesor.id,
            text: asesor.id + ' - ' + asesor.nombre
        }));
    });

    // Si el asesor actual no viene en PE.CDGCO de la sucursal del crédito, igual lo mostramos
    // para poder dejar "sin cambio" al abrir el modal.
    if (!cambiaSucursal
        && reasignacionActual.asesor
        && $select.find('option').filter(function () {
            return String($(this).val()) === reasignacionActual.asesor;
        }).length === 0) {
        $select.append($('<option>', {
            value: reasignacionActual.asesor,
            text: reasignacionActual.asesor
                + (reasignacionActual.nombreAsesor ? ' - ' + reasignacionActual.nombreAsesor : '')
        }));
    }

    if (preferido && $select.find('option').filter(function () {
        return String($(this).val()) === preferido;
    }).length) {
        $select.val(preferido);
    } else if (!cambiaSucursal) {
        $select.val(reasignacionActual.asesor);
    } else {
        $select.val('');
    }
}

function abrirReasignacion(credito, ciclo, cliente, idAsesor, idSucursal, nombreAsesor, nombreSucursal) {
    document.getElementById('reasignacion_credito').value = credito;
    document.getElementById('reasignacion_ciclo').value = ciclo;
    reasignacionActual = {
        asesor: String(idAsesor || ''),
        sucursal: String(idSucursal || ''),
        nombreAsesor: String(nombreAsesor || '')
    };
    document.getElementById('reasignacion_sucursal').value = reasignacionActual.sucursal;
    refrescarAsesoresModal(reasignacionActual.asesor);
    $('#reasignacion_resumen_credito').html(
        '<strong>Crédito ' + escaparHtml(credito) + '</strong>'
        + ' &middot; Ciclo ' + escaparHtml(ciclo)
        + '<br><span class="text-muted">' + escaparHtml(cliente || '') + '</span>'
        + '<br><span class="text-muted">Asesor actual: '
        + escaparHtml(nombreAsesor || '—')
        + ' &middot; Sucursal actual: '
        + escaparHtml(nombreSucursal || '—')
        + '</span>'
    );
    $('#btn_guardar_reasignacion').prop('disabled', false);
    $('#modal_reasignacion').modal('show');
}

// jQuery se carga en el footer, por lo que este bloque no puede usar $ de forma
// inmediata: DOMContentLoaded se dispara cuando esas librerías ya se evaluaron.
document.addEventListener('DOMContentLoaded', function () {
    var opcionesTabla = {
        pageLength: 20,
        lengthMenu: [[10, 20, 40, -1], [10, 20, 40, 'Todos']],
        order: [],
        autoWidth: false,
        columnDefs: [
            { targets: -1, orderable: false, searchable: false },
            { targets: '_all', className: 'text-center' }
        ],
        language: {
            emptyTable: 'No hay datos disponibles',
            info: 'Mostrando de _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'Sin registros para mostrar',
            zeroRecords: 'No se encontraron registros',
            lengthMenu: 'Mostrar _MENU_ registros por página',
            search: 'Buscar:',
            paginate: { previous: 'Anterior', next: 'Siguiente' }
        }
    };

    ['#tablaReasignacion', '#tablaReasignacionMasiva'].forEach(function (selector) {
        if ($.fn.DataTable && $(selector).length && !$.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable(opcionesTabla);
        }
    });

    if ($.fn.DataTable && $('#tablaReasignacionErrores').length
        && !$.fn.DataTable.isDataTable('#tablaReasignacionErrores')) {
        $('#tablaReasignacionErrores').DataTable({
            pageLength: 20,
            lengthMenu: [[10, 20, 40, -1], [10, 20, 40, 'Todos']],
            order: [],
            autoWidth: false,
            columnDefs: [{ targets: '_all', className: 'text-center' }],
            language: opcionesTabla.language
        });
    }

    $('#reasignacion_sucursal').on('change', function () {
        refrescarAsesoresModal('');
    });

    $('#form_reasignacion_individual').on('submit', function (event) {
        event.preventDefault();
        var asesorSeleccionado = String($('#reasignacion_asesor').val() || '');
        var sucursalSeleccionada = String($('#reasignacion_sucursal').val() || '');
        var cambiaSucursal = sucursalSeleccionada !== ''
            && sucursalSeleccionada !== reasignacionActual.sucursal;
        var cambiaAsesor = asesorSeleccionado !== ''
            && asesorSeleccionado !== reasignacionActual.asesor;

        if (cambiaSucursal && !cambiaAsesor) {
            swal('Si cambias la sucursal, también debes seleccionar un asesor de esa sucursal.', { icon: 'warning' });
            return;
        }

        if (!cambiaSucursal && !cambiaAsesor) {
            swal('Selecciona un asesor distinto al actual, o cambia de sucursal y de asesor.', { icon: 'warning' });
            return;
        }

        if (cambiaAsesor) {
            var pertenece = asesoresReasignacion.some(function (asesor) {
                return String(asesor.id) === asesorSeleccionado
                    && String(asesor.sucursal) === sucursalSeleccionada;
            });
            if (!pertenece && !(asesorSeleccionado === reasignacionActual.asesor && !cambiaSucursal)) {
                swal('El asesor seleccionado no pertenece a la sucursal elegida.', { icon: 'warning' });
                return;
            }
        }

        var asesor = cambiaAsesor ? asesorSeleccionado : '';
        var sucursal = cambiaSucursal ? sucursalSeleccionada : '';
        var credito = $('#reasignacion_credito').val();
        var ciclo = $('#reasignacion_ciclo').val();

        swal({
            title: '¿Autorizar reasignación?',
            text: 'Se aplicarán los cambios al crédito ' + credito + ', ciclo ' + ciclo + '.',
            icon: 'warning',
            buttons: {
                cancel: 'Cancelar',
                confirm: {
                    text: 'Autorizar',
                    value: true
                }
            },
            dangerMode: true
        }).then(function (autorizado) {
            if (!autorizado) {
                return;
            }

            $('#btn_guardar_reasignacion').prop('disabled', true);
            $.post('/Creditos/UpdateReasignacion/', {
                credito: credito,
                ciclo: ciclo,
                asesor: asesor,
                sucursal: sucursal
            }, function (respuesta) {
                if (String(respuesta.estatus).toUpperCase() === 'OK') {
                    var destino = respuesta.redirect || window.location.href;
                    $('#modal_reasignacion').modal('hide');
                    var aviso = swal(respuesta.resultado || 'Reasignación realizada.', { icon: 'success' });
                    if (aviso && typeof aviso.then === 'function') {
                        aviso.then(function () {
                            window.location.href = destino;
                        });
                    } else {
                        setTimeout(function () {
                            window.location.href = destino;
                        }, 1500);
                    }
                } else {
                    $('#btn_guardar_reasignacion').prop('disabled', false);
                    swal(respuesta.resultado || 'No fue posible realizar la reasignación.', { icon: 'error' });
                }
            }, 'json').fail(function () {
                $('#btn_guardar_reasignacion').prop('disabled', false);
                swal('No fue posible comunicarse con el servidor.', { icon: 'error' });
            });
        });
    });

    $('#btn_procesar_reasignacion').on('click', function () {
        var archivo = document.getElementById('archivo_reasignacion');
        if (!archivo.files || !archivo.files[0]) {
            swal('Selecciona un archivo Excel.', { icon: 'warning' });
            return;
        }
        swal({
            title: '¿Autorizar reasignación masiva?',
            text: 'Se procesará el archivo \"' + archivo.files[0].name
                + '\". Esta acción puede tardar varios minutos.',
            icon: 'warning',
            buttons: {
                cancel: 'Cancelar',
                confirm: {
                    text: 'Autorizar',
                    value: true
                }
            },
            dangerMode: true
        }).then(function (autorizado) {
            if (autorizado) {
                $('#btn_procesar_reasignacion').prop('disabled', true);
                if (typeof showWait === 'function') {
                    showWait('Procesando reasignaciones, espere un momento...');
                } else {
                    swal({
                        text: 'Procesando reasignaciones, espere un momento...',
                        icon: '/img/wait.gif',
                        button: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                }
                document.getElementById('form_reasignacion_masiva').submit();
            }
        });
    });
});
</script>

<?php echo $footer; ?>
