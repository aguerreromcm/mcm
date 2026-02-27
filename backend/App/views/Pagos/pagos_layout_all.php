<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Generador de Layouts</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5">
                <div class="card-header">
                    <h5 class="card-title">Seleccione el tipo de búsqueda e ingrese el número de crédito </h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Pagos/Layout/" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <input class="form-control mr-sm-2" autofocus type="date" id="Inicial" name="Inicial" value="<?php echo $fechaActual; ?>" required="required">
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control mr-sm-2" autofocus type="date" id="Final" name="Final" value="<?php echo $fechaActual; ?>" required="required">
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-default" type="submit">Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>
<?php echo $footer; ?>