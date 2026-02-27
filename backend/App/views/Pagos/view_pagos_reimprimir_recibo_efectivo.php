<?= $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Reimprimir Recibo de Efectivo <span class="fa fa-print"></span></h3>
                <div class="clearfix"></div>
            </div>
            <div class="card col-md-12">
                <p class="text-muted">Solo se muestran recibos de efectivo del día anterior y el mismo día.</p>
                <hr style="border-top: 1px solid #787878; margin-top: 5px;">
                <div class="dataTable_wrapper">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <div>
                            <label for="mostrar-registros">Mostrar </label>
                            <select id="mostrar-registros" style="display:inline-block; width:70px;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span> registros</span>
                        </div>
                        <div>
                            <input type="text" id="search-folios" class="form-control" placeholder="Buscar:" style="width:250px; display:inline-block;" />
                        </div>
                    </div>
                    <table class="table table-striped table-bordered table-hover" id="tabla-reimprimir">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Sucursal</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Ejecutivo</th>
                                <th>Registros</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= $tabla; ?>
                        </tbody>
                    </table>
                </div>
                <script>
                    (function() {
                        const input = document.getElementById('search-folios');
                        const tabla = document.getElementById('tabla-reimprimir');
                        const select = document.getElementById('mostrar-registros');

                        function filtrar() {
                            const q = input.value.trim().toLowerCase();
                            const rows = Array.from(tabla.querySelectorAll('tbody tr'));
                            rows.forEach((tr, idx) => {
                                const text = tr.textContent.toLowerCase();
                                tr.style.display = text.indexOf(q) !== -1 ? '' : 'none';
                            });
                        }

                        input.addEventListener('input', filtrar);

                        // Opcional: limitar número de filas visibles (simple paginado client-side)
                        select.addEventListener('change', function() {
                            const perPage = parseInt(this.value, 10);
                            const rows = Array.from(tabla.querySelectorAll('tbody tr'));
                            rows.forEach((tr, idx) => {
                                tr.style.display = (idx < perPage) ? '' : 'none';
                            });
                        });

                        // Inicializar por defecto
                        select.dispatchEvent(new Event('change'));
                    })();
                </script>

                <!-- Modal para reimpresión dentro de un iframe -->
                <div class="modal fade" id="modalReimprimir" tabindex="-1" role="dialog" aria-labelledby="modalReimprimirLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document" style="width:95%; max-width:1200px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalReimprimirLabel">Reimpresión de Recibo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" style="padding:0;">
                                <div id="reimprimirLoader" style="display:flex; align-items:center; justify-content:center; height:80vh;">
                                    <img src="/img/wait.gif" alt="Cargando..." />
                                </div>
                                <iframe id="iframeReimprimir" src="" frameborder="0" style="width:100%; height:80vh; display:none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // Abre la reimpresión en el modal. Acepta <a> o <button> con atributos data-*
                    function handleReimprimir(el) {
                        let url = '';
                        if (!el) return;
                        // Si es un enlace <a> con href, usarlo
                        if (el.tagName && el.tagName.toLowerCase() === 'a' && el.href) {
                            url = el.href;
                        } else {
                            // Botón creado por GetPagosAppHistorico: data-folio, data-sucursal, data-cdgocpe, data-fecha
                            const folio = el.getAttribute('data-folio') || '';
                            const sucursal = el.getAttribute('data-sucursal') || el.getAttribute('data-suc') || '';
                            const cdgocpe = el.getAttribute('data-cdgocpe') || el.getAttribute('data-cdgpe') || '';
                            const fecha = el.getAttribute('data-fecha') || '';
                            const tieneFolio = el.getAttribute('data-tiene-folio') || '';

                            if (folio && tieneFolio === '1') {
                                url = `/Pagos/Ticket/?barcode=${encodeURIComponent(folio)}&sucursal=${encodeURIComponent(sucursal)}&cdgpe=${encodeURIComponent(cdgocpe)}`;
                            } else {
                                // Si no hay folio, pasar cdgocpe+fecha+sucursal (el controlador debe aceptar estos params)
                                url = `/Pagos/Ticket/?cdgocpe=${encodeURIComponent(cdgocpe)}&fecha=${encodeURIComponent(fecha)}&sucursal=${encodeURIComponent(sucursal)}`;
                            }
                        }

                        if (!url) {
                            alert('No se pudo determinar la URL de reimpresión.');
                            return;
                        }

                        const iframe = document.getElementById('iframeReimprimir');
                        const loader = document.getElementById('reimprimirLoader');
                        iframe.style.display = 'none';
                        loader.style.display = 'flex';
                        iframe.src = url;

                        $('#modalReimprimir').modal({ keyboard: true, backdrop: 'static' });

                        // Cuando el iframe cargue, ocultar loader y ajustar el viewer para ocupar todo el espacio (intentos sobre viewer de PDF.js)
                        iframe.onload = function() {
                            try {
                                const doc = iframe.contentDocument || iframe.contentWindow.document;
                                // Ocultar barras/sidebars comunes en visualizadores PDF (PDF.js)
                                const sidebar = doc.getElementById('sidebarContainer') || doc.querySelector('.sidebar') || doc.querySelector('.leftPanel') || null;
                                if (sidebar) sidebar.style.display = 'none';

                                // Ocultar toolbar si existe
                                const toolbar = doc.getElementById('toolbar') || doc.querySelector('.toolbar') || doc.querySelector('.toolbarViewer');
                                if (toolbar) toolbar.style.display = 'none';

                                // Forzar que el contenedor del viewer use 100% de ancho
                                const viewerContainers = [
                                    doc.getElementById('viewerContainer'),
                                    doc.getElementById('mainContainer'),
                                    doc.querySelector('.viewerContainer'),
                                    doc.querySelector('.pdfViewer'),
                                    doc.querySelector('#viewer')
                                ];
                                viewerContainers.forEach(vc => {
                                    if (vc) {
                                        vc.style.width = '100%';
                                        vc.style.margin = '0';
                                    }
                                });

                                // Quitar overflow hidden en el body del iframe para permitir impresión a toda anchura
                                if (doc.body) {
                                    doc.body.style.overflow = 'auto';
                                }
                            } catch (err) {
                                // si no es posible manipular el iframe, continuar silenciosamente
                                console.warn('No se pudo ajustar el iframe internamente:', err);
                            } finally {
                                loader.style.display = 'none';
                                iframe.style.display = 'block';
                            }
                        };
                    }

                    // Delegación: si hay enlaces de reimpresión (vienen de ReimprimirReciboEfectivo), interceptarlos
                    document.addEventListener('click', function(e) {
                        const a = e.target.closest('a[href*="/Pagos/Ticket"]');
                        if (a) {
                            e.preventDefault();
                            handleReimprimir(a);
                        }
                    });

                    // Botón imprimir dentro del modal: si existe, dispara print en el iframe
                    (function(){
                        const btn = document.getElementById('btnImprimirDesdeModal');
                        if (!btn) return;
                        btn.addEventListener('click', function() {
                            const iframe = document.getElementById('iframeReimprimir');
                            if (!iframe || iframe.style.display === 'none') return;
                            try {
                                iframe.contentWindow.focus();
                                iframe.contentWindow.print();
                            } catch (err) {
                                // Algunos navegadores bloquean print en PDFs embebidos; abrir en nueva pestaña como fallback
                                window.open(iframe.src, '_blank');
                            }
                        });
                    })();

                    // Limpiar iframe al cerrar modal
                    $('#modalReimprimir').on('hidden.bs.modal', function () {
                        const iframe = document.getElementById('iframeReimprimir');
                        iframe.src = '';
                        iframe.style.display = 'none';
                        document.getElementById('reimprimirLoader').style.display = 'flex';
                    });
                </script>

            </div>
        </div>
    </div>
</div>
</div>

<?= $footer; ?>