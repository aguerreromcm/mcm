<?php

namespace App\services;

defined('APPPATH') or die('Access denied');

use App\models\Creditos as CreditosDao;
use Core\Model;

class CambioSucursalService
{
    private const ALIAS_GRUPO = ['GRUPO', 'CREDITO', 'NO_CREDITO', 'CDGNS', 'CODIGO_GRUPO'];
    private const ALIAS_CICLO = ['CICLO'];
    private const ALIAS_ASESOR = ['ASESOR', 'EJECUTIVO', 'COD_ASESOR'];
    private const ALIAS_EMPRESA = ['EMPRESA', 'CDGEM'];
    private const ALIAS_ID_SUCURSAL = ['ID_SUCURSAL', 'CDGCO', 'COD_SUCURSAL', 'CODIGO_SUCURSAL'];
    private const ALIAS_NOM_SUCURSAL = ['NOM_SUCURSAL', 'NOMBRE_SUCURSAL', 'SUCURSAL', 'NOMBRE SUCURSAL'];

    /**
     * @return array<string, mixed>
     */
    public static function cargaMasivaDesdeArchivo(string $rutaArchivo): array
    {
        if (!is_readable($rutaArchivo)) {
            return Model::Responde(false, 'No se pudo leer el archivo.');
        }

        $extraido = self::extraerFilasDesdeArchivo($rutaArchivo);
        if (!empty($extraido['fatal'])) {
            $hint = ' Si el archivo es .xlsx, verifique que el servidor tenga habilitadas las extensiones PHP zip y zlib, o guarde el archivo como .xls.';
            return Model::Responde(false, 'No se pudo leer el archivo Excel.' . $hint, null, $extraido['fatal']);
        }

        $filas = $extraido['filas'] ?? [];
        $errores = $extraido['errores'] ?? [];
        $actualizados = [];

        if (empty($filas) && empty($errores)) {
            return Model::Responde(false, 'El archivo no contiene filas válidas para procesar.');
        }

        $sucursalesPorId = [];
        $sucursalesPorNombre = [];
        foreach (CreditosDao::ListaSucursales() as $sucursal) {
            $id = trim((string) ($sucursal['ID_SUCURSAL'] ?? ''));
            $nombre = self::normalizarTexto($sucursal['SUCURSAL'] ?? '');
            if ($id !== '') {
                $sucursalesPorId[$id] = $id;
            }
            if ($nombre !== '') {
                $sucursalesPorNombre[$nombre] = $id;
            }
        }

        foreach ($filas as $fila) {
            $numeroFila = (int) ($fila['fila'] ?? 0);
            $grupo = trim((string) ($fila['grupo'] ?? ''));
            $ciclo = trim((string) ($fila['ciclo'] ?? ''));
            $idSucursal = trim((string) ($fila['id_sucursal'] ?? ''));
            $nomSucursal = self::normalizarTexto($fila['nom_sucursal'] ?? '');

            if ($grupo === '' || $ciclo === '') {
                $errores[] = [
                    'fila' => $numeroFila,
                    'grupo' => $grupo,
                    'motivo' => 'Grupo y ciclo son obligatorios.',
                ];
                continue;
            }

            $nuevaSucursal = self::resolverIdSucursal($idSucursal, $nomSucursal, $sucursalesPorId, $sucursalesPorNombre);
            if ($nuevaSucursal === '') {
                $errores[] = [
                    'fila' => $numeroFila,
                    'grupo' => $grupo,
                    'motivo' => 'No se pudo identificar la sucursal destino en NOM_SUCURSAL.',
                ];
                continue;
            }

            try {
                $credito = CreditosDao::SelectSucursalAllCreditoCambioSuc($grupo, $ciclo);
                if (!is_array($credito) || trim((string) ($credito['CLIENTE'] ?? '')) === '') {
                    $errores[] = [
                        'fila' => $numeroFila,
                        'grupo' => $grupo,
                        'motivo' => 'Crédito no encontrado o no elegible para cambio de sucursal.',
                    ];
                    continue;
                }

                $payload = new \stdClass();
                $payload->_credito = $grupo;
                $payload->_ciclo = $ciclo;
                $payload->_nueva_sucursal = $nuevaSucursal;

                $resultado = CreditosDao::UpdateSucursal($payload);
                if (!self::actualizacionExitosa($resultado)) {
                    $errores[] = [
                        'fila' => $numeroFila,
                        'grupo' => $grupo,
                        'motivo' => self::mensajeActualizacion($resultado),
                    ];
                    continue;
                }

                $credito = CreditosDao::SelectSucursalAllCreditoCambioSuc($grupo, $ciclo);
                if (!is_array($credito) || trim((string) ($credito['CLIENTE'] ?? '')) === '') {
                    $errores[] = [
                        'fila' => $numeroFila,
                        'grupo' => $grupo,
                        'motivo' => 'La reasignación se aplicó, pero no fue posible consultar el crédito actualizado.',
                    ];
                    continue;
                }

                $credito['FILA_EXCEL'] = $numeroFila;
                $credito['MENSAJE_ACTUALIZACION'] = self::mensajeActualizacion($resultado);
                $actualizados[] = $credito;
            } catch (\Throwable $e) {
                $errores[] = [
                    'fila' => $numeroFila,
                    'grupo' => $grupo,
                    'motivo' => $e->getMessage(),
                ];
            }
        }

        $procesados = count($actualizados);
        $omitidos = count($errores);
        $mensaje = $procesados > 0
            ? "Se actualizaron $procesados crédito(s)."
            : 'No se actualizó ningún crédito.';

        if ($omitidos > 0) {
            $mensaje .= " $omitidos fila(s) con incidencias.";
        }

        return Model::Responde($procesados > 0, $mensaje, [
            'actualizados' => $actualizados,
            'errores' => $errores,
            'procesados' => $procesados,
            'omitidos' => $omitidos,
        ]);
    }

    /**
     * @return array{filas: list<array<string, mixed>>, errores: list<array<string, mixed>>, fatal: string|null}
     */
    private static function extraerFilasDesdeArchivo(string $ruta): array
    {
        $ext = strtolower((string) pathinfo($ruta, PATHINFO_EXTENSION));
        $magic = @file_get_contents($ruta, false, null, 0, 4);
        $pareceXlsxZip = ($magic !== false && strlen($magic) >= 2 && $magic[0] === 'P' && $magic[1] === 'K');

        if (in_array($ext, ['csv', 'txt'], true)) {
            return self::extraerFilasDesdeCsv($ruta);
        }

        if ($pareceXlsxZip && !extension_loaded('zip')) {
            $extraido = self::extraerFilasDesdeMatriz(XlsxSinZipReader::matrizPrimeraHoja($ruta));
            if ($extraido !== null) {
                return $extraido;
            }

            return [
                'filas' => [],
                'errores' => [],
                'fatal' => 'No se pudo leer el .xlsx sin extensión zip (lector interno falló). Active php_zip o guarde el archivo como .xls.',
            ];
        }

        $extraido = self::extraerFilasDesdePhpSpreadsheet($ruta);
        if (empty($extraido['fatal'])) {
            return $extraido;
        }

        if ($pareceXlsxZip) {
            $desdeMatriz = self::extraerFilasDesdeMatriz(XlsxSinZipReader::matrizPrimeraHoja($ruta));
            if ($desdeMatriz !== null) {
                return $desdeMatriz;
            }
        }

        if (!$pareceXlsxZip) {
            $desdeCsv = self::extraerFilasDesdeCsv($ruta);
            if (empty($desdeCsv['fatal'])) {
                return $desdeCsv;
            }
        }

        return $extraido;
    }

    /**
     * @return array{filas: list<array<string, mixed>>, errores: list<array<string, mixed>>, fatal: string|null}
     */
    private static function extraerFilasDesdePhpSpreadsheet(string $ruta): array
    {
        require_once dirname(__DIR__) . '/../libs/PhpSpreadsheet/PhpSpreadsheet.php';

        try {
            $spreadsheet = self::cargarSpreadsheet($ruta);
        } catch (\Throwable $e) {
            return [
                'filas' => [],
                'errores' => [],
                'fatal' => $e->getMessage(),
            ];
        }

        $sheet = $spreadsheet->getSheet(0);
        $maxFila = max(
            (int) $sheet->getHighestRow(),
            (int) $sheet->getHighestDataRow(),
            1
        );
        $maxColumna = max(
            (int) \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestDataColumn()),
            5
        );

        $matriz = [];
        for ($fila = 1; $fila <= $maxFila; $fila++) {
            $renglon = [];
            for ($col = 1; $col <= $maxColumna; $col++) {
                $letra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $texto = trim(self::valorCeldaComoTexto($sheet->getCell($letra . $fila)));
                if ($texto !== '') {
                    $renglon[$col] = $texto;
                }
            }
            if ($renglon !== []) {
                $matriz[$fila] = $renglon;
            }
        }

        $procesado = self::procesarMatriz($matriz);

        return $procesado ?? [
            'filas' => [],
            'errores' => [],
            'fatal' => 'No se pudo interpretar el contenido del archivo Excel.',
        ];
    }

    /**
     * @return array{filas: list<array<string, mixed>>, errores: list<array<string, mixed>>, fatal: string|null}|null
     */
    private static function extraerFilasDesdeMatriz(?array $matriz): ?array
    {
        if ($matriz === null || $matriz === []) {
            return null;
        }

        return self::procesarMatriz($matriz);
    }

    /**
     * @return array{filas: list<array<string, mixed>>, errores: list<array<string, mixed>>, fatal: string|null}
     */
    private static function extraerFilasDesdeCsv(string $ruta): array
    {
        $contenido = @file_get_contents($ruta);
        if ($contenido === false) {
            return [
                'filas' => [],
                'errores' => [],
                'fatal' => 'No se pudo leer el archivo CSV.',
            ];
        }
        if (strncmp($contenido, "\xEF\xBB\xBF", 3) === 0) {
            $contenido = substr($contenido, 3);
        }

        $lineas = preg_split("/\r\n|\n|\r/", $contenido);
        $matriz = [];
        $fila = 0;
        foreach ($lineas as $linea) {
            $fila++;
            $linea = trim($linea);
            if ($linea === '') {
                continue;
            }
            $partes = str_getcsv($linea, ',', '"');
            if (count($partes) < 2) {
                $partes = str_getcsv($linea, ';', '"');
            }
            $renglon = [];
            foreach ($partes as $indice => $valor) {
                $texto = trim((string) $valor);
                if ($texto !== '') {
                    $renglon[$indice + 1] = $texto;
                }
            }
            if ($renglon !== []) {
                $matriz[$fila] = $renglon;
            }
        }

        $procesado = self::procesarMatriz($matriz);

        return $procesado ?? [
            'filas' => [],
            'errores' => [],
            'fatal' => 'No se pudo interpretar el contenido del archivo CSV.',
        ];
    }

    /**
     * @param array<int, array<int, string>> $matriz
     * @return array{filas: list<array<string, mixed>>, errores: list<array<string, mixed>>, fatal: string|null}|null
     */
    private static function procesarMatriz(array $matriz): ?array
    {
        if ($matriz === []) {
            return null;
        }

        ksort($matriz);
        $encabezado = self::localizarEncabezado($matriz);
        if ($encabezado === null) {
            $filaReferencia = (int) array_key_first($matriz);
            $titulosDetectados = [];
            foreach ($matriz[$filaReferencia] ?? [] as $titulo) {
                $normalizado = self::normalizarEncabezado((string) $titulo);
                if ($normalizado !== '') {
                    $titulosDetectados[] = $normalizado;
                }
            }

            $motivo = 'El layout debe incluir las columnas [GRUPO], CICLO y [NOM_SUCURSAL].';
            if ($titulosDetectados !== []) {
                $motivo .= ' Encabezados detectados en la fila ' . $filaReferencia . ': '
                    . implode(', ', $titulosDetectados) . '.';
            }

            return [
                'filas' => [],
                'errores' => [[
                    'fila' => $filaReferencia,
                    'grupo' => '',
                    'motivo' => $motivo,
                ]],
                'fatal' => null,
            ];
        }

        $filaEncabezado = $encabezado['fila'];
        $mapa = $encabezado['mapa'];

        $filas = [];
        $errores = [];
        foreach ($matriz as $fila => $columnas) {
            if ((int) $fila <= $filaEncabezado) {
                continue;
            }

            $grupo = self::normalizarCodigo((string) ($columnas[$mapa['grupo']] ?? ''), 6);
            $ciclo = self::normalizarCodigo((string) ($columnas[$mapa['ciclo']] ?? ''), 2);
            $idSucursal = $mapa['id_sucursal'] !== null
                ? trim((string) ($columnas[$mapa['id_sucursal']] ?? ''))
                : '';
            $nomSucursal = $mapa['nom_sucursal'] !== null
                ? trim((string) ($columnas[$mapa['nom_sucursal']] ?? ''))
                : '';

            if ($grupo === '' && $ciclo === '' && $idSucursal === '' && $nomSucursal === '') {
                continue;
            }

            if ($grupo === '' || $ciclo === '') {
                $errores[] = [
                    'fila' => (int) $fila,
                    'grupo' => $grupo,
                    'motivo' => 'Grupo y ciclo son obligatorios.',
                ];
                continue;
            }

            $filas[] = [
                'fila' => (int) $fila,
                'grupo' => $grupo,
                'ciclo' => $ciclo,
                'id_sucursal' => $idSucursal,
                'nom_sucursal' => $nomSucursal,
            ];
        }

        return [
            'filas' => $filas,
            'errores' => $errores,
            'fatal' => null,
        ];
    }

    /**
     * @param array<int, array<int, string>> $matriz
     * @return array{fila: int, mapa: array{grupo: int|null, ciclo: int|null, id_sucursal: int|null, nom_sucursal: int|null}}|null
     */
    private static function localizarEncabezado(array $matriz): ?array
    {
        foreach ($matriz as $fila => $columnas) {
            if ((int) $fila > 15) {
                break;
            }

            $encabezados = [];
            foreach ($columnas as $indice => $titulo) {
                $encabezados[(int) $indice] = self::normalizarEncabezado((string) $titulo);
            }

            $mapa = self::mapearColumnas($encabezados);
            if ($mapa['grupo'] !== null && $mapa['ciclo'] !== null && ($mapa['id_sucursal'] !== null || $mapa['nom_sucursal'] !== null)) {
                return [
                    'fila' => (int) $fila,
                    'mapa' => $mapa,
                ];
            }
        }

        return null;
    }

    /**
     * @param array<int, string> $encabezados
     * @return array{grupo: int|null, ciclo: int|null, id_sucursal: int|null, nom_sucursal: int|null}
     */
    private static function mapearColumnas(array $encabezados): array
    {
        $mapa = [
            'grupo' => null,
            'ciclo' => null,
            'id_sucursal' => null,
            'nom_sucursal' => null,
        ];

        foreach ($encabezados as $indice => $titulo) {
            if ($titulo === '') {
                continue;
            }
            if ($mapa['grupo'] === null && in_array($titulo, self::ALIAS_GRUPO, true)) {
                $mapa['grupo'] = $indice;
            }
            if ($mapa['ciclo'] === null && in_array($titulo, self::ALIAS_CICLO, true)) {
                $mapa['ciclo'] = $indice;
            }
            if ($mapa['id_sucursal'] === null && in_array($titulo, self::ALIAS_ID_SUCURSAL, true)) {
                $mapa['id_sucursal'] = $indice;
            }
            if ($mapa['nom_sucursal'] === null && in_array($titulo, self::ALIAS_NOM_SUCURSAL, true)) {
                $mapa['nom_sucursal'] = $indice;
            }
        }

        return $mapa;
    }

    private static function cargarSpreadsheet(string $ruta): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $io = \PhpOffice\PhpSpreadsheet\IOFactory::class;
        $readData = \PhpOffice\PhpSpreadsheet\Reader\IReader::READ_DATA_ONLY;

        try {
            return $io::load($ruta, $readData);
        } catch (\Throwable $e) {
            try {
                return $io::load($ruta, 0);
            } catch (\Throwable $e2) {
                $reader = $io::createReaderForFile($ruta);

                return $reader->load($ruta);
            }
        }
    }

    private static function valorCeldaComoTexto(\PhpOffice\PhpSpreadsheet\Cell\Cell $cell): string
    {
        foreach (['getFormattedValue', 'getValue', 'getCalculatedValue'] as $getter) {
            $valor = $cell->{$getter}();
            if ($valor === null || $valor === '') {
                continue;
            }
            if (is_object($valor) && method_exists($valor, '__toString')) {
                $texto = trim((string) $valor);
            } elseif (is_scalar($valor)) {
                $texto = trim((string) $valor);
            } else {
                continue;
            }
            if ($texto !== '') {
                return $texto;
            }
        }

        return '';
    }

    /**
     * @param array<string, string> $sucursalesPorId
     * @param array<string, string> $sucursalesPorNombre
     */
    private static function resolverIdSucursal(
        string $idSucursal,
        string $nomSucursal,
        array $sucursalesPorId,
        array $sucursalesPorNombre
    ): string {
        if ($idSucursal !== '') {
            if (isset($sucursalesPorId[$idSucursal])) {
                return $idSucursal;
            }

            $idNormalizado = self::normalizarTexto($idSucursal);
            if ($idNormalizado !== '' && isset($sucursalesPorNombre[$idNormalizado])) {
                return $sucursalesPorNombre[$idNormalizado];
            }
        }

        if ($nomSucursal !== '') {
            if (isset($sucursalesPorNombre[$nomSucursal])) {
                return $sucursalesPorNombre[$nomSucursal];
            }
            if (isset($sucursalesPorId[$nomSucursal])) {
                return $nomSucursal;
            }
        }

        return '';
    }

    private static function actualizacionExitosa($resultado): bool
    {
        if ($resultado === false || $resultado === null) {
            return false;
        }

        if (is_array($resultado)) {
            $mensaje = trim((string) ($resultado['VMENSAJE'] ?? ''));

            return $mensaje !== '' && $mensaje !== '0';
        }

        $mensaje = trim((string) $resultado);

        return $mensaje !== '' && $mensaje !== '0';
    }

    private static function mensajeActualizacion($resultado): string
    {
        if (is_array($resultado)) {
            return trim((string) ($resultado['VMENSAJE'] ?? 'No fue posible actualizar la sucursal.'));
        }

        $mensaje = trim((string) $resultado);

        return $mensaje !== '' ? $mensaje : 'No fue posible actualizar la sucursal.';
    }

    private static function normalizarCodigo(string $valor, int $longitudMinima): string
    {
        $valor = trim($valor);
        if ($valor === '') {
            return '';
        }

        if (ctype_digit($valor) && strlen($valor) < $longitudMinima) {
            return str_pad($valor, $longitudMinima, '0', STR_PAD_LEFT);
        }

        return $valor;
    }

    private static function normalizarEncabezado(string $valor): string
    {
        $valor = self::normalizarTexto($valor);
        $valor = str_replace(['[', ']'], '', $valor);
        $valor = str_replace(['-', '.'], '_', $valor);

        return $valor;
    }

    private static function normalizarTexto(string $valor): string
    {
        if (strncmp($valor, "\xEF\xBB\xBF", 3) === 0) {
            $valor = substr($valor, 3);
        }

        $valor = trim($valor);
        if ($valor === '') {
            return '';
        }

        if (function_exists('mb_strtoupper')) {
            return mb_strtoupper($valor, 'UTF-8');
        }

        return strtoupper($valor);
    }
}
