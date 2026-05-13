<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3>Cambio de Sucursal</h3>
            <div class="clearfix"></div>
        </div>

        <?php include __DIR__ . '/partials/cambio_sucursal_carga_masiva.php'; ?>
        <?php
        $registros = [$Administracion];
        $mostrarAccion = true;
        include __DIR__ . '/partials/cambio_sucursal_tabla_registros.php';
        ?>
    </div>
    </div>
</div>
</div>

<div class="modal fade" id="modal_cambio_sucursal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <center><h4 class="modal-title" id="myModalLabel">Cambiar Crédito de Sucursal </h4></center>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form onsubmit="enviar_add('<?php echo $Administracion['CICLO']; ?>'); return false" id="Add">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ejecutivo">Sucursal *</label>
                                    <select class="form-control" autofocus type="select" id="sucursal" name="sucursal" aria-label="Search">
                                        <?php echo $sucursal; ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                <button type="submit" name="agregar" class="btn btn-primary" value="enviar"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    function EditarSucursal(id_suc)
    {
        credito = getParameterByName('Credito');
        id_sucursal = id_suc;

        $('#modal_cambio_sucursal').modal('show'); // abrir

    }
</script>


<?php echo $footer; ?>
