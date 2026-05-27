<?php echo $header; ?>

<style>

    .ftp-page .x_title h3 {

        font-size: 24px;

        font-weight: 400;

        margin: 5px 0 6px;

        line-height: 1.1;

    }



    .ftp-raiz-card {

        margin-bottom: 20px;
        padding-left: 10px;
        padding-right: 10px;

    }



    .ftp-raiz-link {

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        min-height: 160px;

        padding: 28px 20px;

        border: 1px solid #e5e5e5;

        border-radius: 8px;

        background: #fafafa;

        color: #2a3f54;

        text-align: center;

        transition: all 0.2s ease;

        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);

    }



    .ftp-raiz-link:hover,

    .ftp-raiz-link:focus {

        text-decoration: none;

        color: #1abb9c;

        border-color: #1abb9c;

        background: #fff;

        box-shadow: 0 4px 14px rgba(26, 187, 156, 0.15);

    }



    .ftp-raiz-link i {

        margin-bottom: 14px;

        color: #1abb9c;

    }



    .ftp-raiz-link span {

        font-size: 30px;

        font-weight: 600;

    }



    .ftp-toolbar {

        margin-bottom: 15px;

    }



    .ftp-fila-carpeta {

        cursor: pointer;

    }



    .ftp-fila-carpeta:hover {

        background: #f7f7f7;

    }



    .ftp-page #ftp-breadcrumb {

        margin-bottom: 15px;

        background: transparent;

        padding-left: 0;

    }



    .ftp-page .ftp-intro {

        margin-bottom: 18px;

    }

    .ftp-hr {
        border-top: 1px solid #787878;
        margin-top: 5px;
        margin-bottom: 12px;
    }

    #ftp-tabla {
        table-layout: fixed;
        width: 100% !important;
    }

    #ftp-tabla th,
    #ftp-tabla td {
        vertical-align: middle !important;
    }

    #ftp-tabla thead th {
        vertical-align: middle !important;
        line-height: 1.2;
    }

    #ftp-tabla thead th.sorting,
    #ftp-tabla thead th.sorting_asc,
    #ftp-tabla thead th.sorting_desc {
        padding-right: 22px;
        background-position: center right;
    }

    #ftp-tabla th:nth-child(1),
    #ftp-tabla td:nth-child(1) {
        width: 42px;
        text-align: center;
    }

    #ftp-tabla th:nth-child(3),
    #ftp-tabla td:nth-child(3) {
        width: 100px;
        text-align: center;
    }

    #ftp-tabla th:nth-child(4),
    #ftp-tabla td:nth-child(4) {
        width: 110px;
        text-align: center;
        white-space: nowrap;
    }

    #ftp-tabla th:nth-child(5),
    #ftp-tabla td:nth-child(5) {
        width: 170px;
        text-align: center;
        white-space: nowrap;
    }

    #ftp-tabla td:nth-child(2) {
        word-break: break-word;
    }

    @media (max-width: 991px) {

        .ftp-raiz-link span {

            font-size: 22px;

        }

    }



    @media (prefers-color-scheme: dark) {

        .ftp-raiz-link {

            background: #2d333b;

            border-color: #3d444d;

            color: #e6edf3;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);

        }



        .ftp-raiz-link:hover,

        .ftp-raiz-link:focus {

            background: #30363d;

            border-color: #1abb9c;

            color: #1abb9c;

            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.42);

        }



        .ftp-raiz-link i {

            color: #1abb9c;

        }



        .ftp-fila-carpeta:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .ftp-hr {
            border-top-color: #3d444d;
        }



        .ftp-page #ftp-breadcrumb {

            background-color: #252930;

            border: 1px solid #3d444d;

            border-radius: 6px;

            padding: 8px 14px;

        }



        .ftp-page #ftp-breadcrumb > li > a {

            color: #58a6ff;

        }



        .ftp-page #ftp-breadcrumb > li > a:hover {

            color: #79c0ff;

        }



        .ftp-page #ftp-breadcrumb > .active {

            color: #8b949e;

        }



        .ftp-page .text-muted {

            color: #8b949e !important;

        }



        .ftp-page input[type="checkbox"] {

            accent-color: #1abb9c;

        }

    }

</style>



<div class="right_col ftp-page">

    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">

        <div class="panel panel-body">

            <div class="x_title">

                <h3>Explorador de archivos</h3>

                <div class="clearfix"></div>

            </div>



            <ol id="ftp-breadcrumb" class="breadcrumb"></ol>



            <div id="ftp-raices">

                <p class="text-muted ftp-intro">Seleccione una carpeta para consultar su contenido y descargar los archivos requeridos.</p>

                <div id="ftp-lista-raices" class="row"></div>

            </div>



            <div id="ftp-toolbar" class="ftp-toolbar" style="display: none;">

                <button id="ftp-btn-volver" type="button" class="btn btn-default">

                    <i class="fa fa-arrow-left"></i> Volver

                </button>

                <button id="ftp-btn-descargar" type="button" class="btn btn-primary">

                    <i class="fa fa-download"></i> Descargar seleccionados

                </button>

            </div>



            <div id="ftp-tabla-wrap" style="display: none;">
                <hr class="ftp-hr">

                <div class="dataTable_wrapper">
                    <table id="ftp-tabla" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="width: 42px;">
                                    <input type="checkbox" id="ftp-check-all" title="Seleccionar todos los archivos">
                                </th>
                                <th>NOMBRE</th>
                                <th>TIPO</th>
                                <th>TAMAÑO</th>
                                <th>MODIFICADO</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>

<?php echo $footer; ?>

