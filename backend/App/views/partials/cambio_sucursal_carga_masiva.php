<?php
$credito = isset($credito) ? (string) $credito : '';
?>
<div class="card col-md-12">
    <p class="text-muted" style="margin-bottom: 15px;">
        Consulta un crédito y reasigna su sucursal de forma individual, o procesa varios registros mediante un archivo Excel.
    </p>

    <style>
        .cs-formas-sucursal--grid {
            margin-bottom: 20px;
        }
        .cs-formas-sucursal .cs-h4 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .cs-formas-sucursal .cs-busqueda-manual-row,
        .cs-formas-sucursal .cs-carga-masiva-row {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 8px;
            margin-bottom: 10px;
            justify-content: flex-start;
        }
        .cs-formas-sucursal .cs-busqueda-instruccion,
        .cs-formas-sucursal .cs-carga-instruccion {
            line-height: 1.35;
        }
        .cs-formas-sucursal .cs-busqueda-manual-row .form-control {
            min-width: 0;
        }
        .cs-formas-sucursal .cs-busqueda-manual-row select.form-control {
            flex: 0 0 120px;
            width: 120px;
        }
        .cs-formas-sucursal .cs-busqueda-manual-row input.form-control {
            flex: 0 1 auto;
            width: 12ch;
            min-width: 10ch;
            max-width: 165px;
        }
        .cs-formas-sucursal .cs-carga-masiva-row input[type="file"].form-control {
            flex: 0 1 auto;
            min-width: 220px;
            max-width: 340px;
            width: 100%;
        }
        .cs-formas-sucursal .cs-busqueda-manual-row .btn,
        .cs-formas-sucursal .cs-carga-masiva-row .btn,
        .cs-formas-sucursal .cs-carga-masiva-row .btn-sm {
            flex-shrink: 0;
        }
        @media (min-width: 992px) {
            .cs-formas-sucursal--grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                grid-template-rows: auto auto auto;
                column-gap: 30px;
                row-gap: 0;
            }
            .cs-formas-sucursal--grid .cs-h4-busqueda {
                grid-column: 1;
                grid-row: 1;
            }
            .cs-formas-sucursal--grid .cs-h4-carga {
                grid-column: 2;
                grid-row: 1;
            }
            .cs-formas-sucursal--grid .cs-busqueda-instruccion {
                grid-column: 1;
                grid-row: 2;
                margin: 0 0 8px 0;
            }
            .cs-formas-sucursal--grid .cs-carga-instruccion {
                grid-column: 2;
                grid-row: 2;
                margin: 0 0 8px 0;
            }
            .cs-formas-sucursal--grid .cs-busqueda-manual-row {
                grid-column: 1;
                grid-row: 3;
            }
            .cs-formas-sucursal--grid .cs-carga-masiva-row {
                grid-column: 2;
                grid-row: 3;
            }
        }
        @media (max-width: 991px) {
            .cs-formas-sucursal--grid {
                display: flex;
                flex-direction: column;
            }
            .cs-formas-sucursal--grid .cs-busqueda-instruccion,
            .cs-formas-sucursal--grid .cs-carga-instruccion {
                margin-bottom: 8px;
            }
        }
    </style>

    <div class="cs-formas-sucursal cs-formas-sucursal--grid">
        <h4 class="cs-h4 cs-h4-busqueda">Búsqueda manual</h4>
        <h4 class="cs-h4 cs-h4-carga">Carga masiva</h4>
        <p class="small text-muted cs-busqueda-instruccion">
            Ingresa el número de crédito y pulsa <strong>Buscar</strong> para localizar el grupo y cambiar su sucursal.
        </p>
        <p class="small text-muted cs-carga-instruccion">
            Carga un archivo Excel para realizar reasignaciones masivas de sucursal.
            Puedes descargar el archivo base desde el botón <strong>Descargar layout</strong>.
            El proceso validará cada registro de forma individual y continuará con las demás filas en caso de encontrar errores.
        </p>
        <form class="cs-busqueda-manual-row" action="/Creditos/CambioSucursal/" method="GET">
            <label for="cs_tipo_busqueda" class="sr-only">Tipo de búsqueda</label>
            <select class="form-control" id="cs_tipo_busqueda" aria-label="Tipo de búsqueda">
                <option value="credito">Crédito</option>
            </select>
            <label for="Credito" class="sr-only">Número de crédito</label>
            <input class="form-control" type="text" inputmode="numeric" maxlength="12" id="Credito" name="Credito" placeholder="Número de crédito" aria-label="Número de crédito" value="<?php echo htmlspecialchars($credito, ENT_QUOTES, 'UTF-8'); ?>" autofocus>
            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Buscar</button>
        </form>
        <form class="cs-carga-masiva-row" action="/Creditos/CambioSucursalCargaMasiva/" method="POST" enctype="multipart/form-data">
            <input type="file" class="form-control" name="archivo" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required>
            <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Importar</button>
            <a class="btn btn-default btn-sm" href="/Creditos/CambioSucursalLayout/" target="_blank" rel="noopener">
                <i class="fa fa-download"></i> Descargar layout
            </a>
        </form>
    </div>
</div>
