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
                    <button id="btnConsultar" type="button" class="btn btn-primary btn-circle"><i class="fa fa-search"></i> Consultar</button>
                    <button id="btn_masivo" type="button" class="btn btn-warning btn-circle" style="margin-left: 10px;"><i class="fa fa-list"></i> Procesar seleccionados</button>
                </div>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-auditoria-devengo">
                        <thead>
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="checkAll"></th>
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

<!-- Contenedor global de toasts -->
<div id="toast-container"></div>

<style>
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    min-width: 250px;
    margin-bottom: 10px;
    padding: 12px 16px;
    border-radius: 6px;
    color: #fff;
    font-size: 14px;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast.success { background-color: #28a745; }
.toast.warning { background-color: #ffc107; color: #000; }
.toast.error   { background-color: #dc3545; }
</style>

<?php echo $footer; ?>
<script>
(function(){
    console.log('Auditoría Devengo: init helper script');

    // Función para mostrar toast profesional
    function showToast(message, type = "success") {
        const container = document.getElementById("toast-container");

        const toast = document.createElement("div");
        toast.classList.add("toast", type);
        toast.innerText = message;

        container.appendChild(toast);

        setTimeout(() => toast.classList.add("show"), 100);

        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => container.removeChild(toast), 300);
        }, 4000);
    }

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

    // Checkbox maestro: seleccionar/deseleccionar todos (afecta solo filas visibles)
    $(document).on('change', '#checkAll', function () {
        var checked = $(this).is(':checked');
        $('#muestra-auditoria-devengo tbody .checkFila').prop('checked', checked);
    });

    // Procesamiento masivo
    $(document).on('click', '#btn_masivo', function(e) {
        e.preventDefault();
        var checked = $("#muestra-auditoria-devengo").find(".checkFila:checked");
        if (!checked.length) {
            showToast("Selecciona al menos un registro.", "warning");
            return;
        }
        var registros = [];
        checked.each(function() {
            var idx = $(this).data("index");
            if (typeof idx !== "undefined" && Array.isArray(window._devengosFaltantesDatos) && window._devengosFaltantesDatos[idx]) {
                registros.push(window._devengosFaltantesDatos[idx]);
            }
        });
        if (!registros.length) {
            showToast("No se pudieron obtener los registros seleccionados.", "error");
            return;
        }
        swal({ title: "Se procesarán " + registros.length + " registros. ¿Continuar?", icon: "warning", buttons: ["No", "Sí"], dangerMode: true }).then(function(ok) {
            if (!ok) return;
            var $btnMasivo = $("#btn_masivo");
            $btnMasivo.prop("disabled", true);
            swal({ text: "Procesando " + registros.length + " registro(s)...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
            $.ajax({
                type: "POST",
                url: "/Herramientas/ProcesarMasivo/",
                contentType: "application/json",
                data: JSON.stringify({ registros: registros }),
                timeout: 300000,
                success: function(res) {
                    swal.close();
                    $btnMasivo.prop("disabled", false);
                    try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { showToast("Error al procesar la respuesta", "error"); return; }

                    if (res.success) {
                        if (res.insertados > 0) {
                            showToast(res.mensaje, "success");

                            // Eliminar filas procesadas
                            if (res.creditosProcesados && Array.isArray(res.creditosProcesados)) {
                                res.creditosProcesados.forEach(function(item) {
                                    const fila = document.querySelector(
                                        `tr[data-credito="${item.credito}"][data-ciclo="${item.ciclo}"]`
                                    );
                                    if (fila) fila.remove();
                                });
                            }
                        } else {
                            showToast(res.mensaje, "warning");
                        }
                    } else {
                        showToast(res.mensaje, "error");
                    }
                },
                error: function() {
                    swal.close();
                    $btnMasivo.prop("disabled", false);
                    showToast("Error de conexión o tiempo agotado.", "error");
                }
            });
        });
    });

    // Campos de fecha nativos del navegador (igual que Layout Contable)

    // Inicializar DataTable (si ya existe, destruir y volver a inicializar con la configuración correcta)
    var tabla;
    if ($.fn.DataTable.isDataTable("#muestra-auditoria-devengo")) {
        try {
            $("#muestra-auditoria-devengo").DataTable().clear().destroy();
        } catch (e) {
            // ignore
        }
    }
    tabla = $("#muestra-auditoria-devengo").DataTable({
        lengthMenu: [[25,50,100,-1],[25,50,100,"Todos"]],
        order: [[1, "asc"]],
        columnDefs: [
            {
                targets: 0,
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return '<input type="checkbox" class="checkFila chk-procesar" data-index="' + meta.row + '">';
                }
            },
            {
                targets: [6], // Columna de acciones
                orderable: false,
                searchable: false
            }
        ],
        createdRow: function(row, data, dataIndex) {
            // Agregar atributos data-credito y data-ciclo a cada fila
            var c = String(data[1] || "").trim(); // Crédito está en índice 1
            var ci = String(data[2] || "").trim(); // Ciclo está en índice 2
            $(row).attr('data-credito', c);
            $(row).attr('data-ciclo', ci);
        }
    });

    // Checkbox maestro: seleccionar/deseleccionar visibles
    $(document).on('change', '#checkAll', function() {
        var checked = $(this).is(':checked');
        $('#muestra-auditoria-devengo').find('.checkFila').prop('checked', checked);
    });

    $(document).on('click', '.btn-procesar', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        var idx = $btn.data('index');
        var datos = window._devengosFaltantesDatos;
        if (typeof idx === 'undefined' || !Array.isArray(datos) || !datos[idx]) {
            showError("No se pudo obtener la fila a procesar."); return;
        }
        var fila = datos[idx];
        swal({ title: "¿Deseas procesar este devengo?", icon: "warning", buttons: ["No", "Sí"], dangerMode: true }).then(function(ok) {
            if (!ok) return;
            swal({ text: "Procesando...", icon: "/img/wait.gif", button: false, closeOnClickOutside: false, closeOnEsc: false });
            $.ajax({
                type: "POST",
                url: "/Herramientas/ProcesarIndividual/",
                contentType: "application/json",
                data: JSON.stringify({ fila: fila }),
                timeout: 120000,
                success: function(res) {
                    swal.close();
                    try { res = typeof res === "string" ? JSON.parse(res) : res; } catch (e) { showToast("Error al procesar respuesta", "error"); return; }

                    if (res.success) {
                        if (res.insertados > 0) {
                            showToast(res.mensaje, "success");

                            // Eliminar fila del grid usando data attributes
                            const fila = document.querySelector(
                                `tr[data-credito="${res.credito}"][data-ciclo="${res.ciclo}"]`
                            );
                            if (fila) fila.remove();

                        } else {
                            showToast(res.mensaje, "warning");
                        }
                    } else {
                        showToast(res.mensaje, "error");
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

        var data = {};
        if (credito) data.credito = credito;
        if (ciclo) data.ciclo = ciclo;

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
                window._devengosFaltantesDatos = datos;
                var rows = datos.map(function(item, idx) {
                    var c = String(item.CREDITO || item.credito || item.CDGNS || "").trim();
                    var ci = String(item.CICLO || item.ciclo || "").trim();
                    var btn = '<button type="button" class="btn btn-primary btn-sm btn-procesar" data-index="' + idx + '">Procesar</button>';
                    return [
                        null, // La primera columna se renderiza con la función render
                        c,
                        ci,
                        item.FECHA_FALTANTE || item.FECHA_FALT || "",
                        item.FECHA_CALC || "",
                        item.NOMBRE || "",
                        btn
                    ];
                });

                if (tabla) {
                    tabla.clear();
                    if (rows.length) {
                        tabla.rows.add(rows).draw();
                    } else {
                        tabla.draw();
                        if (credito || ciclo) {
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

    // Reconstruye la tabla local a partir de window._devengosFaltantesDatos (elimina entradas null)
    function actualizarTablaLocal() {
        if (!Array.isArray(window._devengosFaltantesDatos)) return;
        var datos = window._devengosFaltantesDatos.filter(function(x) { return x != null; });
        window._devengosFaltantesDatos = datos;
        var rows = datos.map(function(item, idx) {
            var c = String(item.CREDITO || item.credito || item.CDGNS || "").trim();
            var ci = String(item.CICLO || item.ciclo || "").trim();
            var btn = '<button type="button" class="btn btn-primary btn-sm btn-procesar" data-index="' + idx + '">Procesar</button>';
            return [
                null, // La primera columna se renderiza con la función render
                c,
                ci,
                item.FECHA_FALTANTE || item.FECHA_FALT || "",
                item.FECHA_CALC || "",
                item.NOMBRE || "",
                btn
            ];
        });
        if (tabla) {
            tabla.clear();
            if (rows.length) {
                tabla.rows.add(rows).draw();
            } else {
                tabla.draw();
            }
        }
    }
})();
</script>
