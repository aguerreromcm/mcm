<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="margin-bottom: 0px;">
            <div class="x_title">
                <h3> Administración de Pagos</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5" >
                <div class="card-header">
                    <h5 class="card-title">Seleccione el tipo de busqueda e ingrese el número de crédito </h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Pagos/" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <select class="form-control sm-3 mr-sm-3" style="font-size: 18px;" autofocus type="select" id="" name="" placeholder="000000" aria-label="Search">
                                    <option value="credito">Crédito</option>
                                </select>
                                <span id="availability1"></span>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control mr-sm-2" style="font-size: 24px;" autofocus type="text" onKeypress="if (event.keyCode < 9 || event.keyCode > 57) event.returnValue = false;" id="Credito" name="Credito" placeholder="000000" aria-label="Search" value="<?php echo $credito; ?>">
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
</div>
</div>

<?php echo $footer; ?>
