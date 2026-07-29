<?php

namespace App\services;

defined('APPPATH') or die('Access denied');

use App\models\Creditos as CreditosDao;
use Core\Model;

class ReasignacionService
{
    private const ENCABEZADOS = ['GRUPO', 'CICLO', 'ASESOR', 'EMPRESA', 'NOM_SUCURSAL'];

    private const ALIAS_GRUPO = ['GRUPO', 'CREDITO', 'NO_CREDITO', 'CDGNS', 'CODIGO_GRUPO'];
    private const ALIAS_CICLO = ['CICLO'];
    private const ALIAS_ASESOR = ['ASESOR', 'EJECUTIVO', 'COD_ASESOR'];
    private const ALIAS_EMPRESA = ['EMPRESA', 'CDGEM'];
    private const ALIAS_NOM_SUCURSAL = [
        'NOM_SUCURSAL',
        'NOMBRE_SUCURSAL',
        'SUCURSAL',
        'NOMBRE SUCURSAL',
        'ID_SUCURSAL',
        'CDGCO',
        'COD_SUCURSAL',
        'CODIGO_SUCURSAL',
    ];

    /**
     * Procesa el layout compatible:
     * [GRUPO], CICLO, [ASESOR], [EMPRESA], [NOM_SUCURSAL].
     *
     * ASESOR y NOM_SUCURSAL son opcionales individualmente, pero al menos uno
     * debe tener valor. EMPRESA se conserva por compatibilidad y debe ser EMPFIN
     * cuando se captura.
     */
    public static function cargaMasivaDesdeArchivo(string $rutaArchivo, string $usuario): array
    {
        if (!is_readable($rutaArchivo)) {
            return Model::Responde(false, 'No se pudo leer el archivo.');
        }

        $extraido = self::extraerFilas($rutaArchivo);
        if ($extraido['fatal'] !== null) {
            return Model::Responde(false, $extraido['fatal']);
        }

        $errores = $extraido['errores'];
        $actualizados = [];
        $catalogo = self::catalogoSucursales();

        foreach ($extraido['filas'] as $fila) {
            $numeroFila = (int) ($fila['fila'] ?? 0);
            $grupo = trim((string) ($fila['grupo'] ?? ''));

            try {
                $ciclo = trim((string) ($fila['ciclo'] ?? ''));
                $asesor = trim((string) ($fila['asesor'] ?? ''));
                $empresa = trim((string) ($fila['empresa'] ?? ''));
                $nomSucursal = trim((string) ($fila['nom_sucursal'] ?? ''));

                if ($grupo === '' || $ciclo === '') {
                    $errores[] = self::error($numeroFila, $grupo, 'Grupo y ciclo son obligatorios.');
                    continue;
                }

                if ($empresa !== '' && self::normalizarTexto($empresa) !== 'EMPFIN') {
                    $errores[] = self::error($numeroFila, $grupo, 'La empresa debe ser EMPFIN.');
                    continue;
                }

                $sucursal = '';
                if ($nomSucursal !== '') {
                    $sucursal = self::resolverIdSucursal(
                        $nomSucursal,
                        $catalogo['por_id'],
                        $catalogo['por_nombre']
                    );
                    if ($sucursal === '') {
                        $errores[] = self::error(
                            $numeroFila,
                            $grupo,
                            'No se pudo identificar la sucursal destino en NOM_SUCURSAL.'
                        );
                        continue;
                    }
                }

                if ($asesor === '' && $sucursal === '') {
                    $errores[] = self::error(
                        $numeroFila,
                        $grupo,
                        'Debe indicar ASESOR, NOM_SUCURSAL o ambos.'
                    );
                    continue;
                }

                $antes = CreditosDao::SelectCreditoReasignacion($grupo, $ciclo);
                if (!is_array($antes) || trim((string) ($antes['CLIENTE'] ?? '')) === '') {
                    $errores[] = self::error(
                        $numeroFila,
                        $grupo,
                        'Crédito y ciclo no encontrados o no elegibles para reasignación.'
                    );
                    continue;
                }

                $resultado = CreditosDao::EjecutarReasignacion(
                    $grupo,
                    $ciclo,
                    $asesor,
                    $sucursal,
                    $usuario
                );

                if (strtoupper(trim((string) ($resultado['ESTATUS'] ?? ''))) !== 'OK') {
                    $errores[] = self::error(
                        $numeroFila,
                        $grupo,
                        trim((string) ($resultado['RESULTADO'] ?? 'No fue posible realizar la reasignación.'))
                    );
                    continue;
                }

                $despues = CreditosDao::SelectCreditoReasignacion($grupo, $ciclo);
                if (!is_array($despues) || trim((string) ($despues['CLIENTE'] ?? '')) === '') {
                    $errores[] = self::error(
                        $numeroFila,
                        $grupo,
                        'La reasignación se aplicó, pero no fue posible consultar el crédito actualizado.'
                    );
                    continue;
                }

                $despues['FILA_EXCEL'] = $numeroFila;
                $despues['ASESOR_ANTERIOR'] = trim((string) ($antes['EJECUTIVO'] ?? ''));
                $despues['ASESOR_NUEVO'] = $asesor !== ''
                    ? trim((string) ($despues['EJECUTIVO'] ?? $asesor))
                    : '';
                $despues['SUCURSAL_ANTERIOR'] = trim((string) ($antes['SUCURSAL'] ?? ''));
                $despues['SUCURSAL_NUEVA'] = $sucursal !== ''
                    ? trim((string) ($despues['SUCURSAL'] ?? $nomSucursal))
                    : '';
                $despues['MENSAJE_ACTUALIZACION'] = trim((string) ($resultado['RESULTADO'] ?? ''));
                $actualizados[] = $despues;
            } catch (\Throwable $e) {
                $errores[] = self::error($numeroFila, $grupo, $e->getMessage());
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
     * @return array{por_id: array<string, string>, por_nombre: array<string, string>}
     */
    private static function catalogoSucursales(): array
    {
        $porId = [];
        $porNombre = [];
        foreach (CreditosDao::ListaSucursalesReasignacion() as $sucursal) {
            $codigo = trim((string) ($sucursal['ID_SUCURSAL'] ?? ''));
            $nombre = self::normalizarTexto((string) ($sucursal['SUCURSAL'] ?? ''));
            if ($codigo !== '') {
                $porId[$codigo] = $codigo;
            }
            if ($nombre !== '' && $codigo !== '') {
                $porNombre[$nombre] = $codigo;
            }
        }

        return [
            'por_id' => $porId,
            'por_nombre' => $porNombre,
        ];
    }

    /**
     * @param array<string, string> $sucursalesPorId
     * @param array<string, string> $sucursalesPorNombre
     */
    private static function resolverIdSucursal(
        string $valor,
        array $sucursalesPorId,
        array $sucursalesPorNombre
    ): string {
        $valor = trim($valor);
        if ($valor === '') {
            return '';
        }

        if (isset($sucursalesPorId[$valor])) {
            return $valor;
        }

        $normalizado = self::normalizarTexto($valor);
        if ($normalizado !== '' && isset($sucursalesPorNombre[$normalizado])) {
            return $sucursalesPorNombre[$normalizado];
        }

        if ($normalizado !== '' && isset($sucursalesPorId[$normalizado])) {
            return $normalizado;
        }

        return '';
    }

    private static function extraerFilas(string $ruta): array
    {
        $magic = @file_get_contents($ruta, false, null, 0, 4);
        $esZip = $magic !== false && substr($magic, 0, 2) === 'PK';

        if ($esZip && !extension_loaded('zip')) {
            $matriz = XlsxSinZipReader::matrizPrimeraHoja($ruta);
            if ($matriz !== null) {
                return self::procesarMatriz($matriz);
            }

            return [
                'filas' => [],
                'errores' => [],
                'fatal' => 'No se pudo leer el archivo .xlsx. Verifique que el servidor tenga habilitada la extensión zlib.',
            ];
        }

        require_once dirname(__DIR__) . '/../libs/PhpSpreadsheet/PhpSpreadsheet.php';
        try {
            $spreadsheet = self::cargarSpreadsheet($ruta);
            $sheet = $spreadsheet->getSheet(0);
            $maxFila = max(
                (int) $sheet->getHighestRow(),
                (int) $sheet->getHighestDataRow(),
                1
            );
            $maxColumna = max(
                (int) \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(
                    $sheet->getHighestDataColumn()
                ),
                5
            );
            $matriz = [];

            for ($fila = 1; $fila <= $maxFila; $fila++) {
                $renglon = [];
                for ($columna = 1; $columna <= $maxColumna; $columna++) {
                    $celda = $sheet->getCell(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columna) . $fila
                    );
                    $texto = trim(self::valorCeldaComoTexto($celda));
                    if ($texto !== '') {
                        $renglon[$columna] = $texto;
                    }
                }
                if ($renglon !== []) {
                    $matriz[$fila] = $renglon;
                }
            }

            return self::procesarMatriz($matriz);
        } catch (\Throwable $e) {
            if ($esZip) {
                $matriz = XlsxSinZipReader::matrizPrimeraHoja($ruta);
                if ($matriz !== null) {
                    return self::procesarMatriz($matriz);
                }
            }

            return [
                'filas' => [],
                'errores' => [],
                'fatal' => 'No se pudo leer el archivo Excel: ' . $e->getMessage(),
            ];
        }
    }

    private static function cargarSpreadsheet(string $ruta): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $io = \PhpOffice\PhpSpreadsheet\IOFactory::class;
        $soloDatos = \PhpOffice\PhpSpreadsheet\Reader\IReader::READ_DATA_ONLY;

        try {
            return $io::load($ruta, $soloDatos);
        } catch (\Throwable $e) {
            try {
                return $io::load($ruta, 0);
            } catch (\Throwable $e2) {
                return $io::createReaderForFile($ruta)->load($ruta);
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
     * @param array<int, array<int, string>>|null $matriz
     * @return array{filas: list<array<string, mixed>>, errores: list<array<string, mixed>>, fatal: string|null}
     */
    private static function procesarMatriz(?array $matriz): array
    {
        if (!$matriz) {
            return [
                'filas' => [],
                'errores' => [],
                'fatal' => 'El archivo está vacío o no pudo interpretarse.',
            ];
        }

        ksort($matriz);
        $encabezado = self::localizarEncabezado($matriz);
        if ($encabezado === null) {
            return [
                'filas' => [],
                'errores' => [],
                'fatal' => 'El layout debe conservar este orden: [GRUPO], CICLO, [ASESOR], [EMPRESA], [NOM_SUCURSAL].',
            ];
        }

        $filaEncabezado = $encabezado['fila'];
        $mapa = $encabezado['mapa'];
        $filas = [];
        $errores = [];

        foreach ($matriz as $numero => $columnas) {
            if ((int) $numero <= $filaEncabezado) {
                continue;
            }

            $grupo = self::normalizarCodigo((string) ($columnas[$mapa['grupo']] ?? ''), 6);
            $ciclo = self::normalizarCodigo((string) ($columnas[$mapa['ciclo']] ?? ''), 2);
            $asesor = strtoupper(trim((string) ($columnas[$mapa['asesor']] ?? '')));
            $empresa = strtoupper(trim((string) ($columnas[$mapa['empresa']] ?? '')));
            $nomSucursal = trim((string) ($columnas[$mapa['nom_sucursal']] ?? ''));

            if ($grupo === '' && $ciclo === '' && $asesor === '' && $empresa === '' && $nomSucursal === '') {
                continue;
            }

            if ($grupo === '' || $ciclo === '') {
                $errores[] = self::error((int) $numero, $grupo, 'Grupo y ciclo son obligatorios.');
                continue;
            }

            $filas[] = [
                'fila' => (int) $numero,
                'grupo' => $grupo,
                'ciclo' => $ciclo,
                'asesor' => $asesor,
                'empresa' => $empresa,
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
     * @return array{fila: int, mapa: array{grupo: int, ciclo: int, asesor: int|null, empresa: int|null, nom_sucursal: int|null}}|null
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

            // Preferencia: orden oficial de las 5 columnas.
            $oficial = [];
            for ($columna = 1; $columna <= 5; $columna++) {
                $oficial[] = $encabezados[$columna] ?? '';
            }
            if ($oficial === self::ENCABEZADOS) {
                return [
                    'fila' => (int) $fila,
                    'mapa' => [
                        'grupo' => 1,
                        'ciclo' => 2,
                        'asesor' => 3,
                        'empresa' => 4,
                        'nom_sucursal' => 5,
                    ],
                ];
            }

            $mapa = self::mapearColumnas($encabezados);
            if ($mapa['grupo'] !== null && $mapa['ciclo'] !== null) {
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
     * @return array{grupo: int|null, ciclo: int|null, asesor: int|null, empresa: int|null, nom_sucursal: int|null}
     */
    private static function mapearColumnas(array $encabezados): array
    {
        $mapa = [
            'grupo' => null,
            'ciclo' => null,
            'asesor' => null,
            'empresa' => null,
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
            if ($mapa['asesor'] === null && in_array($titulo, self::ALIAS_ASESOR, true)) {
                $mapa['asesor'] = $indice;
            }
            if ($mapa['empresa'] === null && in_array($titulo, self::ALIAS_EMPRESA, true)) {
                $mapa['empresa'] = $indice;
            }
            if ($mapa['nom_sucursal'] === null && in_array($titulo, self::ALIAS_NOM_SUCURSAL, true)) {
                $mapa['nom_sucursal'] = $indice;
            }
        }

        return $mapa;
    }

    private static function normalizarCodigo(string $valor, int $longitud): string
    {
        $valor = trim($valor);
        if ($valor === '') {
            return '';
        }

        return ctype_digit($valor) && strlen($valor) < $longitud
            ? str_pad($valor, $longitud, '0', STR_PAD_LEFT)
            : $valor;
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

        return function_exists('mb_strtoupper')
            ? mb_strtoupper($valor, 'UTF-8')
            : strtoupper($valor);
    }

    private static function error(int $fila, string $grupo, string $motivo): array
    {
        return ['fila' => $fila, 'grupo' => $grupo, 'motivo' => $motivo];
    }
}
