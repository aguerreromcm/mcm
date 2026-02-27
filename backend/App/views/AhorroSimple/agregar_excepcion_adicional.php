<?php echo $header; ?>

<div class="right_col" style="color: #000;">

    <form onsubmit="enviar_add(); return false" id="Add">

        <!-- Hidden para enviar datos del cliente -->
        <input type="hidden" name="no_credito" id="no_credito" value="<?= $ConsultaDatos['NO_CREDITO'] ?>">
        <input type="hidden" name="cliente" id="cliente" value="<?= $ConsultaDatos['CLIENTE'] ?>">
        <input type="hidden" name="sucursal" id="sucursal" value="<?= $ConsultaDatos['SUCURSAL'] ?>">
        <input type="hidden" name="ejecutivo_nombre" id="ejecutivo_nombre" value="<?= $ConsultaDatos['EJECUTIVO'] ?>">
        <input type="hidden" name="ciclo" id="ciclo" value="<?= $ConsultaDatos['CICLO'] ?>">


        <!-- Panel principal -->
        <div class="panel panel-body"
             style="margin-bottom: 0px; background: #f9f9f9; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); padding: 20px;">

            <div class="x_title">
                <a href="/AhorroSimple/ExepcionesMXT/" style="text-decoration: none; color: inherit;">
                    <label style="font-size: 28px; font-weight: bold; cursor: pointer;">
                        📊 Agregar Excepciones para Créditos Adicionales Más por Ti
                    </label>
                </a>
                <div class="clearfix"></div>
            </div>

            <div class="card col-md-12 mb-3" style="padding: 15px;">

                <div class="row">

                    <!-- INFO -->
                    <div class="tile_count col-sm-12" style="margin-bottom: 10px;">

                        <div class="col-md-4 col-sm-4 tile_stats_count">
                            <span class="count_top" style="font-size: 15px;">
                                <i class="fa fa-user-circle"></i> Cliente
                            </span>
                            <div class="count" style="font-size: 16px; font-weight: bold;">
                                (<?= $ConsultaDatos['NO_CREDITO'] ?>) - <?= $ConsultaDatos['CLIENTE'] ?>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-2 tile_stats_count">
                            <span class="count_top" style="font-size: 15px;">
                                <i class="fa fa-building"></i> Sucursal
                            </span>
                            <div class="count" style="font-size: 16px; font-weight: bold;">
                                <?= $ConsultaDatos['SUCURSAL'] ?>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-2 tile_stats_count">
                            <span class="count_top" style="font-size: 15px;">
                                <i class="fa fa-user-tie"></i> Ejecutivo
                            </span>
                            <div class="count" style="font-size: 16px; font-weight: bold;">
                                <?= $ConsultaDatos['EJECUTIVO']; ?>
                            </div>
                        </div>

                    </div>

                    <!-- CHECKBOXES -->
                    <div class="col-md-12" style="margin-top: 1px;">
                        <div style="
                            background: #ffffff;
                            padding: 20px;
                            border-radius: 12px;
                            border: 1px solid #ccc;
                            font-size: 20px;
                            line-height: 1.5;
                        ">

                            <div class="checkbox" style="margin-bottom: 12px;">
                                <label style="font-size: 20px;">
                                    <input type="checkbox" name="exc_ciclo" id="exc_ciclo"
                                           style="transform: scale(1.3); margin-right: 10px;" <?= (!empty($ConsultaActivos['EXC_UNO']) && $ConsultaActivos['EXC_UNO'] === 'S') ? 'checked' : '' ?>>
                                    Excepción: Política de ciclo mayor a 04.
                                </label>
                            </div>

                            <div class="checkbox" style="margin-bottom: 12px;">
                                <label style="font-size: 20px;">
                                    <input type="checkbox" name="exc_semanas" id="exc_semanas"
                                           style="transform: scale(1.3); margin-right: 10px;" <?= (!empty($ConsultaActivos['EXC_DOS']) && $ConsultaActivos['EXC_DOS'] === 'S') ? 'checked' : '' ?>>
                                    Excepción: No cumple con las semanas necesarias para continuar.
                                </label>
                            </div>

                            <div class="checkbox" style="margin-bottom: 12px;">
                                <label style="font-size: 20px;">
                                    <input type="checkbox" name="exc_rango" id="exc_rango"
                                           style="transform: scale(1.3); margin-right: 10px;"
                                        <?= (!empty($ConsultaActivos['EXC_TRES']) && $ConsultaActivos['EXC_TRES'] === 'S') ? 'checked' : '' ?>>
                                    Excepción: Cliente fuera del rango de semanas para crédito adicional.
                                </label>
                            </div>

                            <div class="checkbox" style="margin-bottom: 12px;">
                                <label style="font-size: 20px;">
                                    <input type="checkbox" name="exc_atraso" id="exc_atraso"
                                           style="transform: scale(1.3); margin-right: 10px;" <?= (!empty($ConsultaActivos['EXC_CUATRO']) && $ConsultaActivos['EXC_CUATRO'] === 'S') ? 'checked' : '' ?>>
                                    Excepción: Días de atraso mayores a lo permitido.
                                </label>
                            </div>

                            <div class="checkbox" style="margin-bottom: 12px;">
                                <label style="font-size: 20px;">
                                    <input type="checkbox" name="exc_5pagos" id="exc_5pagos"
                                           style="transform: scale(1.3); margin-right: 10px;" <?= (!empty($ConsultaActivos['EXC_CINCO']) && $ConsultaActivos['EXC_CINCO'] === 'S') ? 'checked' : '' ?>>
                                    Excepción: No cumple con los 5 pagos requeridos.
                                </label>
                            </div>

                            <div class="checkbox">
                                <label style="font-size: 20px;">
                                    <input type="checkbox" name="exc_ahorro" id="exc_ahorro"
                                           style="transform: scale(1.3); margin-right: 10px;" <?= (!empty($ConsultaActivos['EXC_SEIS']) && $ConsultaActivos['EXC_SEIS'] === 'S') ? 'checked' : '' ?>>
                                    Excepción: No cumple con la política de ahorro.
                                </label>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- BOTÓN ENVIAR -->
                <div class="col-md-12 text-center" style="margin-top: 25px;">
                    <button type="submit" class="btn btn-success btn-lg" style="padding: 10px 25px; font-size: 20px;">
                        ✔ Guardar Excepciones
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

<?php echo $footer; ?>
