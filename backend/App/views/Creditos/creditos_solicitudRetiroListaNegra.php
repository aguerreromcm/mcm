<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="overflow: auto;">
        <div class="x_title">
            <h3>Solicitud retiro de cliente de lista negra</h3>
            <div class="clearfix"></div>
        </div>
        <div class="contenedor-card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <span class="card-title">Ingrese el CURP del cliente</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <input class="form-control mr-sm-2" type="text" id="curp" value="">
                        <span>CURP</span>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="button" id="buscar"><i class="glyphicon glyphicon-search"></i> Buscar</button>
                    </div>
                </div>
                <hr style="border-top: 1px solid #e5e5e5; margin-top: 5px;">

            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>