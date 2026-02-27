<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Cambio de Sucursal</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Creditos/CambioSucursal/" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control mr-sm-3" style="font-size: 18px;" autofocus type="select" placeholder="000000" aria-label="Search">
                                    <option value="credito">Crédito</option>
                                </select>
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="number" id="Credito" name="Credito" placeholder="000000" aria-label="Search">
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
