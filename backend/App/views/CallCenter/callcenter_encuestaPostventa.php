<?php echo $header; ?>

<div class="right_col">
    <div class="panel panel-body" style="overflow: auto;">
        <div class="x_title">
            <h3>Postventa</h3>
            <div class="clearfix"></div>
        </div>
        <div class="contenedor-card">
            <div class="card">
                <div class="imagen">
                    <img id="fotoCliente" src="" alt="Foto">
                </div>
                <div class="details">
                    <div class="row">
                        <div class="col-md-12">
                            <span id="nombre" style="display: block; width: 100%; font-size: xx-large;"></span>
                        </div>
                        <div class="col-md-12">
                            <span id="telefono" style="display: block; width: 100%; font-size: x-large;"></span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-2">
                            <label>No. Cliente:</label>
                            <span id="cliente" style="display: block; width: 100%; font-size: large;"></span>
                        </div>
                        <div class="col-md-5">
                            <label>Sucursal</label>
                            <span id="sucursal" style="display: block; width: 100%; font-size: large;"></span>
                        </div>
                        <div class="col-md-2">
                            <label>Ciclo</label>
                            <span id="ciclo" style="display: block; width: 100%; font-size: large;"></span>
                        </div>
                        <div class="col-md-3">
                            <label>Monto del Crédito</label>
                            <span id="monto" style="display: block; width: 100%; font-size: large;"></span>
                        </div>
                    </div>
                </div>
                <div class="boton">
                    <button type="button" id="inicio" disabled>
                        <i class="fa fa-phone" id="icono" aria-hidden="true" style="font-size: xx-large;"></i>
                        <span id="textoAuxiliar" style="display: block; font-size: medium;">Iniciar</span>
                    </button>
                </div>
            </div>
            <div class="modal-content" style="display: none; width: 80%; margin: auto;">
                <div class="modal-header">
                    <center>
                        <h4 class="modal-title" id="myModalLabel">Registro de información</h4>
                    </center>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Pregunta</th>
                                <th style="text-align: center;"><i class="fa fa-check" style="color: green;"></i> Si</th>
                                <th style="text-align: center;"><i class="fa fa-times" style="color: red;"></i> No</th>
                                <th style="text-align: center;">Comentario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="vertical-align: middle !important; text-align: center;"><span class="pregunta">¿Liquido el crédito anterior?</span></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_1" class="respuesta" value="1"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_1" class="respuesta" value="0"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="text" name="comentario_1" class="form-control comentario" maxlength="500"></td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle !important; text-align: center;"><span class="pregunta">¿Conserva la tarjeta del crédito?</span></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_2" class="respuesta" value="1"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_2" class="respuesta" value="0"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="text" name="comentario_2" class="form-control comentario" maxlength="500"></td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle !important; text-align: center;"><span class="pregunta">¿Pago multas en el crédito anterior?</span></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_3" class="respuesta" value="1"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_3" class="respuesta" value="0"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="text" name="comentario_3" class="form-control comentario" maxlength="500"></td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle !important; text-align: center;"><span class="pregunta">¿Registraron las multas en su tarjeta?</span></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_4" class="respuesta" value="1"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_4" class="respuesta" value="0"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="text" name="comentario_4" class="form-control comentario" maxlength="500"></td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle !important; text-align: center;"><span class="pregunta">¿Le gustaría hacer algún?</span></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_5" class="respuesta" value="1"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="radio" name="respuesta_5" class="respuesta" value="0"></td>
                                <td style="vertical-align: middle !important; text-align: center;"><input type="text" name="comentario_5" class="form-control comentario" maxlength="500"></td>
                            </tr>
                            <tr>
                                <td colspan="4" style="vertical-align: middle !important; text-align: center;">
                                    <div class="form-group">
                                        <span>Comentarios finales del cliente</span>
                                        <textarea name="comentario_general" id="comentario_general" class="form-control" rows="5" style="resize: none;"></textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="guardaEncuesta" onclick="guardaEncuesta('OK')" disabled><i class="glyphicon glyphicon-floppy-disk"></i> Guardar Encuesta</button>
                </div>
            </div>
        </div>
    </div>
</div>


<?php echo $footer; ?>

<style>
    .contenedor-card {
        position: relative;
        z-index: 1;
    }

    .card {
        display: flex;
        align-items: center;
        border: 2px solid #ccc;
        border-radius: 110px;
        padding: 20px;
        width: 80%;
        position: relative;
        background-color: white;
        margin: 25px auto;
    }

    .card .imagen {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin-right: 20px;
    }

    .card .imagen img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }

    .card .details {
        flex-grow: 1;
        text-align: center;
    }

    .card .details h2 {
        font-size: 24px;
        margin: 0;
    }

    .card .details p {
        margin: 5px 0;
    }

    .card .details .title {
        font-weight: bold;
    }

    .card .boton {
        margin-left: 20px;
    }

    .card .boton button {
        transition: all 1s ease;
        width: 95px;
        height: 95px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        display: flex;
        flex-direction: column;
    }

    .card .boton button:hover {
        opacity: 0.8;
        transform: scale(0.95);
        transition: all 0.3s ease;
    }

    .card .boton .active {
        background-color: #dc3545;
    }
</style>