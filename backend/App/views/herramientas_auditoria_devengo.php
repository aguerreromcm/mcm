<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Auditoría Devengo</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12">
                <div style="margin-bottom: 15px;">
                    <label style="margin-right: 8px;">Crédito:</label>
                    <input type="text" id="credito" placeholder="Crédito" style="margin-right: 15px; padding: 5px; width: 120px;">
                    <label style="margin-right: 8px;">Ciclo:</label>
                    <input type="text" id="ciclo" placeholder="Ciclo" style="margin-right: 15px; padding: 5px; width: 80px;">
                    <label style="margin-right: 8px;">Fecha desde:</label>
                    <input type="text" id="fecha_desde" placeholder="dd/mm/aaaa" readonly style="margin-right: 5px; padding: 5px; width: 110px;">
                    <i class="fa fa-calendar" style="margin-right: 15px; cursor: pointer;" id="icon_fecha_desde"></i>
                    <label style="margin-right: 8px;">Fecha hasta:</label>
                    <input type="text" id="fecha_hasta" placeholder="dd/mm/aaaa" readonly style="margin-right: 5px; padding: 5px; width: 110px;">
                    <i class="fa fa-calendar" style="margin-right: 15px; cursor: pointer;" id="icon_fecha_hasta"></i>
                    <button id="btnConsultar" type="button" class="btn btn-primary btn-circle"><i class="fa fa-search"></i> Consultar</button>
                </div>
                <div style="margin-bottom: 15px;">
                    <button id="btn_masivo" type="button" class="btn btn-warning btn-circle"><i class="fa fa-list"></i> Procesar seleccionados</button>
                </div>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-auditoria-devengo">
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>CREDITO</th>
                                <th>CICLO</th>
                                <th>FECHA FALTANTE</th>
                                <th>FECHA CALC</th>
                                <th>NOMBRE</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $tabla; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
<script>
(function(){
    console.log('Auditoría Devengo: init helper script');

    $(document).on('click', '#btnConsultar', function (e) {
        e.preventDefault();
        console.log('btnConsultar clicked');
        if (typeof consultarDevengos === 'function') {
            consultarDevengos();
        } else {
            // fallback: call legacy function if present
            if (typeof consultarDevengosFaltantes === 'function') {
                console.log('Llamando a consultarDevengosFaltantes (legacy)');
                consultarDevengosFaltantes();
            } else {
                console.log('No se encontró función de consulta.');
            }
        }
    });

    // inicializar datepickers para los nuevos campos (si moment y daterangepicker están disponibles)
    $(function(){
        try {
            var cfgDrp = { singleDatePicker: true, locale: { format: "DD/MM/YYYY" }, autoUpdateInput: false };
            if ($.fn.daterangepicker) {
                $("#fecha_desde").daterangepicker(cfgDrp, function(start) { $("#fecha_desde").val(start.format("DD/MM/YYYY")); });
                $("#icon_fecha_desde").on("click", function() { $("#fecha_desde").focus(); });
                $("#fecha_hasta").daterangepicker(cfgDrp, function(start) { $("#fecha_hasta").val(start.format("DD/MM/YYYY")); });
                $("#icon_fecha_hasta").on("click", function() { $("#fecha_hasta").focus(); });
            }
        } catch (e) {
            console.log('Datepicker init error', e);
        }
    });

    // obtener instancia única de DataTable (no re-inicializar)
    var tabla;
    try {
        tabla = $("#muestra-auditoria-devengo").DataTable();
    } catch (e) {
        // si falla, intentar inicializar mínimamente
        tabla = $("#muestra-auditoria-devengo").DataTable({
            lengthMenu: [[25,50,100,-1],[25,50,100,"Todos"]],
            order: [[1, "asc"]]
        });
    }

    $(document).on('click', '.btn-procesar', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var credito = $(this).data('credito');
        var ciclo = $(this).data('ciclo');
        console.log('Procesar click detectado:', credito, ciclo);
        if (!credito || !ciclo) { showError("No se pudo obtener crédito o ciclo."); return; }
        swal({ title: "¿Deseas procesar este devengo?", icon: "warning", buttons: ["No", "Sí"], dangerMode: true }).then(function(ok) {
            if (!ok) return;
            swal({ text: "Procesando...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
            $.ajax({
                type: "POST",
                url: "/Herramientas/ProcesarIndividual/",
                contentType: "application/json",
                data: JSON.stringify({ credito: credito, ciclo: ciclo, fecha_corte: null }),
                timeout: 120000,
                success: function(res) {
                    swal.close();
                    try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { showError("Error al procesar"); return; }
                    if (res.success) {
                        showSuccess(res.mensaje);
                        if (typeof window.consultarDevengos === 'function') window.consultarDevengos();
                    } else {
                        showError(res.mensaje || res.error || "Error al procesar");
                    }
                },
                error: function() {
                    swal.close();
                    showError("Error de conexión o tiempo agotado.");
                }
            });
        });
    });

    window.consultarDevengos = function() {
        console.log('consultarDevengos called');
        var credito = $("#credito").val() ? $("#credito").val().trim() : "";
        var ciclo = $("#ciclo").val() ? $("#ciclo").val().trim() : "";
        var fecha_desde = $("#fecha_desde").val() ? (window.moment ? moment($("#fecha_desde").val(), "DD/MM/YYYY").format("YYYY-MM-DD") : $("#fecha_desde").val()) : "";
        var fecha_hasta = $("#fecha_hasta").val() ? (window.moment ? moment($("#fecha_hasta").val(), "DD/MM/YYYY").format("YYYY-MM-DD") : $("#fecha_hasta").val()) : "";

        var data = {};
        if (credito) data.credito = credito;
        if (ciclo) data.ciclo = ciclo;
        if (fecha_desde) data.fecha_desde = fecha_desde;
        if (fecha_hasta) data.fecha_hasta = fecha_hasta;

        console.log('consultarDevengos params', data);
        swal({ text: "Procesando la solicitud, espere un momento...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });

        $.ajax({
            type: "GET",
            url: "/Herramientas/GetDevengosFaltantes/",
            data: data,
            timeout: 120000,
            success: function(res) {
                swal.close();
                try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) {
                    showError("Error al procesar la respuesta");
                    if (tabla) { tabla.clear().draw(); }
                    return;
                }
                if (!res.success) {
                    showError(res.mensaje || "Error al cargar");
                    if (tabla) { tabla.clear().draw(); }
                    return;
                }
                var datos = Array.isArray(res.datos) ? res.datos : [];
                var rows = datos.map(function(item) {
                    var c = String(item.CREDITO || item.credito || item.CDGNS || "").trim();
                    var ci = String(item.CICLO || item.ciclo || "").trim();
                    var chk = '<input type="checkbox" class="chk-procesar" data-credito="' + c + '" data-ciclo="' + ci + '">';
                    var btn = '<button type="button" class="btn btn-primary btn-sm btn-procesar" data-credito="' + c + '" data-ciclo="' + ci + '">Procesar</button>';
                    return [chk, c, ci, item.FECHA_FALTANTE || item.FECHA_FALT || "", item.FECHA_CALC || "", item.NOMBRE || "", btn];
                });

                if (tabla) {
                    tabla.clear();
                    if (rows.length) {
                        tabla.rows.add(rows).draw();
                    } else {
                        tabla.draw();
                        if (credito || ciclo || fecha_desde || fecha_hasta) {
                            showInfo("No se encontraron devengos faltantes para los filtros aplicados.");
                        }
                    }
                } else {
                    console.log('DataTable no disponible para actualizar');
                }
            },
            error: function() {
                swal.close();
                showError("La consulta tardó demasiado o hubo un error.");
                if (tabla) { tabla.clear().draw(); }
            }
        });
    };
})();
</script>
