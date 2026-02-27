
<?php echo $header; ?>
<div class="right_col">
    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <div class="panel panel-body">
            <div class="x_title">
                <h3> Reingresar Clientes a Credito</h3>
                <div class="clearfix"></div>
            </div>

            <div class="card card-danger col-md-5" >
                <div class="card-header">
                    <h5 class="card-title">Ingrese el número de crédito </h5>
                </div>

                <div class="card-body">
                    <form class="" action="/Cultiva/ReingresarClientesCredito/" method="GET">
                        <div class="row">
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
                        <div class="col-md-3 col-sm-4  tile_stats_count">
                            <span class="count_top" style="font-size: 15px"><i class="fa fa-user"></i> Nombre del Grupo</span>

                            <div class="count" style="font-size: 14px"><?php echo $Nombre; ?></div>

                        </div>

                    </div>
                </div>
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="muestra-cupones">
                        <thead>
                        <tr>
                            <th>CDGNS</th>
                            <th>CDGCL</th>
                            <th>Nombre del Cliente</th>
                            <th>Fecha de Baja</th>
                            <th>Motivo Baja</th>
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
<?php echo $footer; ?>
