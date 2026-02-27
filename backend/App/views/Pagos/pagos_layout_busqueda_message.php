<?php echo $header; ?>

<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body" style="margin-bottom: 0px;">
        <div class="x_title">
            <h3> Generador de Layouts</h3>
            <div class="clearfix"></div>
        </div>
        <div class="card card-danger col-md-5" >
            <div class="card-header">
                <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
            </div>
            <div class="card-body">
                <form class="" action="/Pagos/Layout/" method="GET">
                    <div class="row">
                        <div class="col-md-4">
                            <input class="form-control mr-sm-4" autofocus type="date" id="Inicial" name="Inicial" placeholder="000000" aria-label="Search" value="<?php echo $fecha_i; ?>">
                            <span id="availability1"></span>
                        </div>
                        <div class="col-md-4">
                            <input class="form-control mr-sm-4" autofocus type="date" id="Final" name="Final" placeholder="000000" aria-label="Search" value="<?php echo $fecha_f; ?>">
                            <span id="availability1"></span>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-default" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card col-md-12">
            <hr style="border-top: 1px solid #787878; margin-top: 5px;">
            <div class="row" >
                <div class="tile_count float-right col-sm-12" style="margin-bottom: 1px; margin-top: 1px">
                    <div class="x_content">
                        <br />
                        <div class="alert alert-warning alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <label style="font-size: 14px; color: black;">Los parametros no arrojan resultados:</label> <li style="color: black;">Valide que las fechas que ingresaste sean correctas. </li> <li style="color: black;">Si el problema persiste, comuníquese con soporte técnico.</li>
                            <br>
                            <a href="/Pagos/Layout/" class="alert-link">Regresar</a>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>
