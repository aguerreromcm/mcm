<?php
$registros = isset($registros) && is_array($registros) ? $registros : [];
$mostrarAccion = !empty($mostrarAccion);
$idTabla = isset($idTabla) && $idTabla !== '' ? (string) $idTabla : 'tabla-cambio-sucursal-creditos';
?>
<hr style="border-top: 1px solid #ddd;">

<div class="dataTable_wrapper">
    <table class="table table-striped table-bordered table-hover" id="<?php echo htmlspecialchars($idTabla, ENT_QUOTES, 'UTF-8'); ?>" data-con-accion="<?php echo $mostrarAccion ? '1' : '0'; ?>" style="width:100%">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Ciclo</th>
                <th>Prestamo</th>
                <th>Situación</th>
                <th>Sucursal</th>
                <th>Ejecutivo</th>
                <?php if ($mostrarAccion) : ?>
                    <th>Acción</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $registro) : ?>
                <tr>
                    <td><?php echo htmlspecialchars((string) ($registro['CLIENTE'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string) ($registro['CICLO'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>$ <?php echo number_format((float) ($registro['MONTO'] ?? 0)); ?></td>
                    <td><?php echo htmlspecialchars((string) ($registro['SITUACION'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string) ($registro['SUCURSAL'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string) ($registro['EJECUTIVO'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                    <?php if ($mostrarAccion) : ?>
                        <td class="text-center">
                            <button type="button" class="btn btn-success btn-xs" onclick="EditarSucursal('<?php echo htmlspecialchars((string) ($registro['ID_EJECUTIVO'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>');">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
