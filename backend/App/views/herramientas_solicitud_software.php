<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3>Solicitud de Software</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12 ss-page">

                <!-- Introducción amigable -->
                <div class="ss-intro">
                    <h4><i class="fa fa-info-circle"></i> ¿Para qué sirve este formato?</h4>
                    <p>Si necesita que se <strong>cree</strong>, <strong>modifique</strong> o <strong>corrija</strong> algún programa o sistema de la empresa, llene este formulario con sus palabras. No necesita saber de tecnología: solo explique qué necesita y por qué.</p>
                    <div class="ss-intro-steps">
                        <p class="ss-intro-steps-title">Pasos:</p>
                        <ol>
                            <li>Llene todos los campos marcados con <span class="ss-req">*</span> (obligatorios).</li>
                            <li>Revise que la información sea correcta.</li>
                            <li>Descargue el PDF con el botón correspondiente.</li>
                        </ol>
                    </div>
                </div>

                <!-- Folio, fecha y acciones -->
                <div id="ss-alert-error" class="ss-alert ss-alert-error"></div>
                <div class="ss-top-bar">
                    <div class="ss-top-meta">
                        <div class="ss-folio-item">
                            <span class="ss-folio-lbl"><i class="fa fa-hashtag"></i> Folio</span>
                            <strong id="ss-folio" class="ss-folio-val"><?php echo htmlspecialchars($folio); ?></strong>
                        </div>
                        <div class="ss-top-meta-sep" aria-hidden="true"></div>
                        <div class="ss-folio-item">
                            <span class="ss-folio-lbl"><i class="fa fa-calendar"></i> Fecha</span>
                            <span id="ss-fecha" class="ss-folio-val"><?php echo htmlspecialchars($fecha); ?></span>
                        </div>
                    </div>
                    <div class="ss-toolbar">
                        <button type="button" id="btn-ss-pdf" class="btn btn-primary btn-circle ss-btn-primary">
                            <i class="fa fa-file-pdf-o"></i> <b>Descargar PDF</b>
                        </button>
                        <button type="button" id="btn-ss-limpiar" class="btn btn-default btn-circle ss-btn-secondary">
                            <i class="fa fa-eraser"></i> <b>Limpiar</b>
                        </button>
                    </div>
                </div>

                <form id="form-solicitud-software" autocomplete="off">

                    <!-- SECCIÓN 1: Datos del solicitante -->
                    <div class="ss-section" data-section="1">
                        <div class="ss-section-header">
                            <span class="ss-section-num">1</span>
                            <h4>Datos de quien solicita</h4>
                        </div>
                        <div class="ss-section-body">
                            <div class="ss-row">
                                <div class="ss-field" data-required="true">
                                    <label>Nombre completo <span class="ss-req">*</span></label>
                                    <input type="text" name="nombre" id="ss-nombre" value="<?php echo htmlspecialchars($nombre); ?>" placeholder="Ejemplo: María González Pérez">
                                    <span class="ss-error-msg">Escriba su nombre completo.</span>
                                </div>
                                <div class="ss-field ss-field-catalogo">
                                    <label>Puesto o cargo</label>
                                    <select name="puesto" id="ss-puesto">
                                        <option value="">-- Seleccione --</option>
                                        <?php foreach ($catalogo_puestos as $opcionPuesto): ?>
                                            <option value="<?php echo htmlspecialchars($opcionPuesto); ?>"><?php echo htmlspecialchars($opcionPuesto); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="puesto_otro" id="ss-puesto-otro" class="ss-otro-input" placeholder="Especifique su puesto o cargo" style="display:none">
                                </div>
                            </div>
                            <div class="ss-row">
                                <div class="ss-field ss-field-catalogo" data-required="true">
                                    <label>Área o departamento <span class="ss-req">*</span></label>
                                    <select name="area" id="ss-area">
                                        <option value="">-- Seleccione --</option>
                                        <?php foreach ($catalogo_areas as $opcionArea): ?>
                                            <option value="<?php echo htmlspecialchars($opcionArea); ?>"><?php echo htmlspecialchars($opcionArea); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" name="area_otro" id="ss-area-otro" class="ss-otro-input" placeholder="Especifique su área o departamento" style="display:none">
                                    <span class="ss-error-msg">Seleccione su área o escriba cuál es.</span>
                                </div>
                                <div class="ss-field">
                                    <label>Sucursal</label>
                                    <select name="sucursal" id="ss-sucursal">
                                        <option value="">-- Seleccione --</option>
                                        <?php foreach ($catalogo_sucursales as $opcionSucursal): ?>
                                            <?php
                                                $codigoSucursal = (string) ($opcionSucursal['CODIGO'] ?? '');
                                                $nombreSucursal = (string) ($opcionSucursal['NOMBRE'] ?? '');
                                                $selectedSucursal = ($sucursal !== '' && $sucursal === $codigoSucursal) ? ' selected' : '';
                                            ?>
                                            <option value="<?php echo htmlspecialchars($nombreSucursal); ?>"<?php echo $selectedSucursal; ?>><?php echo htmlspecialchars($nombreSucursal); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="ss-row">
                                <div class="ss-field" data-required="true">
                                    <label>Correo electrónico <span class="ss-req">*</span></label>
                                    <input type="email" name="correo" id="ss-correo" placeholder="Ejemplo: nombre@masconmenos.com.mx">
                                    <span class="ss-error-msg">Escriba un correo válido.</span>
                                </div>
                                <div class="ss-field">
                                    <label>Teléfono o extensión</label>
                                    <input type="tel" name="telefono" id="ss-telefono" placeholder="Ejemplo: (477) 123-4567 ext. 102">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Tipo de solicitud -->
                    <div class="ss-section" data-section="2">
                        <div class="ss-section-header">
                            <span class="ss-section-num">2</span>
                            <h4>¿Qué tipo de solicitud es? <span class="ss-req">*</span></h4>
                        </div>
                        <div class="ss-section-body">
                            <p class="ss-section-hint">Seleccione la opción que mejor describa lo que necesita:</p>
                            <div class="ss-field" data-required="true">
                            <div class="ss-options" id="ss-tipo-options">
                                <label class="ss-option-card">
                                    <input type="radio" name="tipo_solicitud" value="creacion">
                                    <span class="ss-opt-icon ss-opt-icon-creacion"><i class="fa fa-plus-circle"></i></span>
                                    <div>
                                        <div class="ss-opt-title">Crear algo nuevo</div>
                                        <div class="ss-opt-desc">Necesito un programa, reporte o herramienta que hoy no existe en la empresa.</div>
                                    </div>
                                </label>
                                <label class="ss-option-card">
                                    <input type="radio" name="tipo_solicitud" value="modificacion">
                                    <span class="ss-opt-icon ss-opt-icon-mod"><i class="fa fa-pencil-square-o"></i></span>
                                    <div>
                                        <div class="ss-opt-title">Modificar o mejorar algo existente</div>
                                        <div class="ss-opt-desc">Necesito que se le agregue o cambie algo.</div>
                                    </div>
                                </label>
                                <label class="ss-option-card">
                                    <input type="radio" name="tipo_solicitud" value="correccion">
                                    <span class="ss-opt-icon ss-opt-icon-fix"><i class="fa fa-wrench"></i></span>
                                    <div>
                                        <div class="ss-opt-title">Corregir un problema</div>
                                        <div class="ss-opt-desc">Algo no funciona bien, da error o muestra información incorrecta.</div>
                                    </div>
                                </label>
                                <label class="ss-option-card">
                                    <input type="radio" name="tipo_solicitud" value="actualizacion">
                                    <span class="ss-opt-icon ss-opt-icon-upd"><i class="fa fa-refresh"></i></span>
                                    <div>
                                        <div class="ss-opt-title">Actualizar información o datos</div>
                                        <div class="ss-opt-desc">Necesito cambiar textos, catálogos, listas o datos que ya están en el sistema.</div>
                                    </div>
                                </label>
                            </div>
                            <span class="ss-error-msg" id="ss-error-tipo" style="display:none;margin-top:10px;">Seleccione un tipo de solicitud.</span>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: Descripción de la necesidad -->
                    <div class="ss-section" data-section="3">
                        <div class="ss-section-header">
                            <span class="ss-section-num">3</span>
                            <h4>Describa lo que necesita</h4>
                        </div>
                        <div class="ss-section-body">
                            <div class="ss-field" data-required="true">
                                <label>Describa con sus palabras qué necesita <span class="ss-req">*</span></label>
                                <span class="ss-hint">Explique qué quiere lograr, como si se lo contara a un compañero de trabajo.</span>
                                <textarea name="descripcion" id="ss-descripcion" rows="5" placeholder="Ejemplo: Necesito un reporte de pagos del día por sucursal para entregarlo al cierre."></textarea>
                                <span class="ss-error-msg">Describa lo que necesita (mínimo 20 caracteres).</span>
                            </div>
                            <div class="ss-field" data-required="true">
                                <label>¿Qué problema resuelve o qué mejora trae? <span class="ss-req">*</span></label>
                                <span class="ss-hint">¿Por qué es importante? ¿Qué pasa si no se hace?</span>
                                <textarea name="beneficio" id="ss-beneficio" rows="3" placeholder="Ejemplo: Evitaría copiar datos a Excel y reduciría el tiempo de cierre de 1 hora a 10 minutos."></textarea>
                                <span class="ss-error-msg">Explique el beneficio o la razón de la solicitud.</span>
                            </div>
                            <div class="ss-field">
                                <label>¿Cómo lo hace hoy sin el cambio?</label>
                                <span class="ss-hint">Describa paso a paso cómo realiza esa actividad hoy. Si le ayuda, pegue capturas de pantalla (hasta 15 imágenes; cada una, máximo 2 MB).</span>
                                <div id="ss-proceso-actual" class="ss-editor-proceso" contenteditable="true" role="textbox" aria-multiline="true"
                                    data-placeholder="Ejemplo: Descargo el reporte a Excel, lo ordeno manualmente y lo imprimo."></div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: Prioridad -->
                    <div class="ss-section" data-section="4">
                        <div class="ss-section-header">
                            <span class="ss-section-num">4</span>
                            <h4>Prioridad</h4>
                        </div>
                        <div class="ss-section-body">
                            <div class="ss-field ss-field-prioridad" data-required="true">
                                <label>¿Qué tan urgente es? <span class="ss-req">*</span></label>
                                <div class="ss-priority-group" id="ss-prioridad-group">
                                    <label class="ss-priority-btn ss-pri-urgente">
                                        <input type="radio" name="prioridad" value="urgente">
                                        <span class="ss-pri-icon">🔴</span>
                                        <span class="ss-pri-label">Urgente</span>
                                        <span class="ss-pri-sub">Afecta operación diaria</span>
                                    </label>
                                    <label class="ss-priority-btn ss-pri-normal">
                                        <input type="radio" name="prioridad" value="normal">
                                        <span class="ss-pri-icon">🟡</span>
                                        <span class="ss-pri-label">Normal</span>
                                        <span class="ss-pri-sub">Importante pero no bloquea</span>
                                    </label>
                                    <label class="ss-priority-btn ss-pri-baja">
                                        <input type="radio" name="prioridad" value="baja">
                                        <span class="ss-pri-icon">🟢</span>
                                        <span class="ss-pri-label">Puede esperar</span>
                                        <span class="ss-pri-sub">Mejora deseable a futuro</span>
                                    </label>
                                </div>
                                <span class="ss-error-msg" id="ss-error-prioridad" style="display:none;">Seleccione una prioridad.</span>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
