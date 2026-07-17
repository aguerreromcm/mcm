<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

/**
 * Registro de lista negra de acreditados (CL_MARCA, TIPOMARCA = LN).
 * Alta vía SPACCIONLISTANEGRA (mismo flujo que SICAFIN setAccionListaNegra).
 * Causas: CAT_CAUSA_LISTA_NEGRA TIPO = 'A'.
 */
class ListaNegraRegistroService
{
    private const CDGEM = 'EMPFIN';
    private const TIPO_LN = 'LN';
    /** Alta de acreditado en lista negra (SICAFIN prmTIPO = 1). */
    private const SP_TIPO_ALTA = 1;

    /**
     * Causas de alta de acreditados (TIPO = A), mismas opciones que SICAFIN.
     *
     * @return array{success: bool, mensaje: string, datos?: array}
     */
    public static function listarCausas(): array
    {
        $db = new Database();
        $rows = $db->queryAll(
            "SELECT TRIM(CODIGO) AS CODIGO, TRIM(DESCRIPCION) AS DESCRIPCION
             FROM CAT_CAUSA_LISTA_NEGRA
             WHERE CDGEM = :cdgem
               AND TIPO = 'A'
             ORDER BY
                CASE
                    WHEN REGEXP_LIKE(TRIM(CODIGO), '^[0-9]+$') THEN TO_NUMBER(TRIM(CODIGO))
                    ELSE NULL
                END NULLS LAST,
                TRIM(CODIGO)",
            ['cdgem' => self::CDGEM]
        );

        if ($rows === false) {
            return Model::Responde(false, 'No se pudieron cargar las causas de lista negra.');
        }

        $datos = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            $codigo = trim((string) ($row['CODIGO'] ?? ''));
            $desc = trim((string) ($row['DESCRIPCION'] ?? ''));
            if ($codigo === '' || $desc === '') {
                continue;
            }
            $datos[] = [
                'CODIGO' => $codigo,
                'DESCRIPCION' => $desc,
                'DESCRIPCION_FMT' => self::limpiarDescripcionCausa($desc),
            ];
        }

        return Model::Responde(true, 'OK', $datos);
    }

    /**
     * Resuelve crédito / cliente / CURP a uno o más acreditados (CDGCL + CURP + nombre).
     *
     * @return array{success: bool, mensaje: string, datos?: array}
     */
    public static function resolver(string $tipo, string $valor): array
    {
        $tipo = strtoupper(trim($tipo));
        $valor = trim($valor);

        if (!in_array($tipo, ['CREDITO', 'CLIENTE', 'CURP'], true)) {
            return Model::Responde(false, 'Tipo de búsqueda inválido. Use CREDITO, CLIENTE o CURP.');
        }
        if ($valor === '') {
            return Model::Responde(false, 'Capture el valor a buscar.');
        }

        $db = new Database();

        try {
            if ($tipo === 'CREDITO') {
                $clientes = self::buscarPorCredito($db, $valor);
            } elseif ($tipo === 'CLIENTE') {
                $clientes = self::buscarPorCliente($db, $valor);
            } else {
                $err = ListaNegraEmpleadosService::validarFormatoCurp($valor);
                if ($err !== null) {
                    return Model::Responde(false, $err);
                }
                $clientes = self::buscarPorCurp($db, ListaNegraEmpleadosService::normalizarCurp($valor));
            }
        } catch (\Throwable $e) {
            return Model::Responde(false, 'No se pudo resolver el acreditado.', null, $e->getMessage());
        }

        if (count($clientes) === 0) {
            return Model::Responde(false, 'No se encontró acreditado con los datos indicados.');
        }

        foreach ($clientes as &$c) {
            $c['YA_EN_LISTA'] = self::existeActivoPorCurp($db, (string) $c['CURP']);
        }
        unset($c);

        return Model::Responde(true, 'OK', $clientes);
    }

    /**
     * Alta manual: un identificador (crédito, cliente o CURP) + causa.
     * Si el crédito tiene varios integrantes, registra a todos los que tengan CURP y no estén ya activos.
     *
     * @return array{success: bool, mensaje: string, datos?: mixed}
     */
    public static function guardarUno(string $tipo, string $valor, string $causa, string $usuarioSesion): array
    {
        $causa = trim($causa);
        if ($causa === '') {
            return Model::Responde(false, 'Seleccione la causa de lista negra.');
        }

        $resuelto = self::resolver($tipo, $valor);
        if (empty($resuelto['success'])) {
            return $resuelto;
        }

        /** @var list<array<string, mixed>> $clientes */
        $clientes = $resuelto['datos'] ?? [];
        $db = new Database();
        $causa = self::normalizarCodigoCausa($causa, $db);
        if ($causa === '' || !self::causaValida($db, $causa)) {
            return Model::Responde(false, 'La causa seleccionada no es válida.');
        }

        $altaPe = $usuarioSesion !== '' ? $usuarioSesion : 'SYSTEM';
        $ok = 0;
        $omitidos = [];
        $registrados = [];

        foreach ($clientes as $cli) {
            $cdgcl = (string) ($cli['CDGCL'] ?? '');
            $curp = (string) ($cli['CURP'] ?? '');
            $nombre = (string) ($cli['NOMBRE'] ?? '');

            if ($curp === '') {
                $omitidos[] = [
                    'cdgcl' => $cdgcl,
                    'curp' => '',
                    'motivo' => 'El acreditado no tiene CURP en CL.',
                ];
                continue;
            }

            try {
                if (self::existeActivoPorCurp($db, $curp)) {
                    $omitidos[] = [
                        'cdgcl' => $cdgcl,
                        'curp' => $curp,
                        'motivo' => 'Ya está activo en lista negra.',
                    ];
                    continue;
                }
                // CDGCL lo resuelve el SP desde CURP (igual que SICAFIN)
                self::altaViaSp($db, $curp, $causa, $altaPe);
                $ok++;
                $registrados[] = [
                    'CDGCL' => $cdgcl,
                    'CURP' => $curp,
                    'NOMBRE' => $nombre,
                ];
            } catch (\Throwable $e) {
                $omitidos[] = [
                    'cdgcl' => $cdgcl,
                    'curp' => $curp,
                    'motivo' => $e->getMessage(),
                ];
            }
        }

        if ($ok === 0) {
            $motivo = $omitidos[0]['motivo'] ?? 'No se pudo registrar.';
            return array_merge(
                Model::Responde(false, $motivo),
                ['omitidos' => $omitidos, 'registrados' => $registrados]
            );
        }

        $mensaje = $ok === 1
            ? 'Acreditado registrado en lista negra.'
            : sprintf('Se registraron %d acreditados en lista negra.', $ok);
        if (count($omitidos) > 0) {
            $mensaje .= sprintf(' Omitidos: %d.', count($omitidos));
        }

        return array_merge(
            Model::Responde(true, $mensaje),
            ['registrados' => $registrados, 'omitidos' => $omitidos, 'insertados' => $ok]
        );
    }

    /**
     * Carga masiva: columnas CREDITO | CLIENTE | CURP | CAUSA (una identificación + causa por fila).
     *
     * @return array<string, mixed>
     */
    public static function cargaMasivaDesdeArchivo(string $rutaArchivo, string $usuarioSesion): array
    {
        if (!is_readable($rutaArchivo)) {
            return Model::Responde(false, 'No se pudo leer el archivo.');
        }

        $ext = strtolower((string) pathinfo($rutaArchivo, PATHINFO_EXTENSION));
        $esCsv = in_array($ext, ['csv', 'txt'], true);

        if ($esCsv) {
            $extra = self::extraerFilasDesdeCsv($rutaArchivo);
        } else {
            $extra = self::extraerFilasDesdeExcel($rutaArchivo);
            if (!empty($extra['fatal'])) {
                $magic = @file_get_contents($rutaArchivo, false, null, 0, 4);
                $pareceZip = ($magic !== false && strlen($magic) >= 2 && $magic[0] === 'P' && $magic[1] === 'K');
                if (!$pareceZip) {
                    $extra = self::extraerFilasDesdeCsv($rutaArchivo);
                }
            }
            if (!empty($extra['fatal'])) {
                return Model::Responde(
                    false,
                    'No se pudo leer el archivo. Use el layout Excel/CSV o guarde como CSV UTF-8.',
                    null,
                    $extra['fatal']
                );
            }
        }

        $items = $extra['items_validos'] ?? [];
        $formatoErrores = $extra['formato_errores'] ?? [];

        if (empty($items) && empty($formatoErrores)) {
            return Model::Responde(false, 'El archivo no contiene filas válidas (CREDITO, CLIENTE o CURP + CAUSA).');
        }

        $insertados = 0;
        $erroresDb = [];
        $registrados = [];

        foreach ($items as $item) {
            $res = self::guardarUno($item['tipo'], $item['valor'], $item['causa'], $usuarioSesion);
            if (!empty($res['success'])) {
                $insertados += (int) ($res['insertados'] ?? 1);
                foreach ($res['registrados'] ?? [] as $reg) {
                    $registrados[] = [
                        'fila' => $item['fila'],
                        'CDGCL' => (string) ($reg['CDGCL'] ?? ''),
                        'CURP' => (string) ($reg['CURP'] ?? ''),
                        'NOMBRE' => (string) ($reg['NOMBRE'] ?? ''),
                        'CAUSA' => (string) ($item['causa'] ?? ''),
                    ];
                }
                foreach ($res['omitidos'] ?? [] as $om) {
                    $erroresDb[] = [
                        'fila' => $item['fila'],
                        'cdgcl' => $om['cdgcl'] ?? '',
                        'curp' => $om['curp'] ?? $item['valor'],
                        'nombre' => '',
                        'motivo' => $om['motivo'] ?? 'Omitido',
                    ];
                }
            } else {
                $conDetalle = false;
                foreach ($res['omitidos'] ?? [] as $om) {
                    $conDetalle = true;
                    $erroresDb[] = [
                        'fila' => $item['fila'],
                        'cdgcl' => $om['cdgcl'] ?? '',
                        'curp' => $om['curp'] ?? $item['valor'],
                        'nombre' => '',
                        'motivo' => $om['motivo'] ?? 'Omitido',
                    ];
                }
                if (!$conDetalle) {
                    $erroresDb[] = [
                        'fila' => $item['fila'],
                        'cdgcl' => '',
                        'curp' => $item['valor'],
                        'nombre' => '',
                        'motivo' => $res['mensaje'] ?? 'Error al registrar',
                    ];
                }
            }
        }

        $todosErrores = array_merge($formatoErrores, $erroresDb);
        $omitidos = count($todosErrores);
        $mensaje = sprintf('Importación finalizada. Registrados: %d. No procesados: %d.', $insertados, $omitidos);
        $exito = $insertados > 0 || $omitidos > 0;

        return array_merge(
            Model::Responde($exito, $mensaje),
            [
                'insertados' => $insertados,
                'omitidos' => $omitidos,
                'registrados' => $registrados,
                'errores' => $todosErrores,
            ]
        );
    }

    /**
     * Alta en CL_MARCA vía SPACCIONLISTANEGRA (mismo SP que SICAFIN).
     * El SP valida CURP/causa, calcula SECUENCIA, busca CDGCL en CL e inserta TIPOMARCA='LN'.
     */
    private static function altaViaSp(Database $db, string $curp, string $causa, string $altaPe): void
    {
        $curp = ListaNegraEmpleadosService::normalizarCurp($curp);
        if (strlen($curp) !== 18) {
            throw new \InvalidArgumentException('La CURP del cliente es incorrecta. Verifique la información capturada.');
        }

        $sp = 'BEGIN SPACCIONLISTANEGRA(:tipo, :cdgem, :curp, :causa, :cdgpe, :output); END;';
        $msg = trim((string) $db->EjecutaSP($sp, [
            'tipo' => self::SP_TIPO_ALTA,
            'cdgem' => self::CDGEM,
            'curp' => $curp,
            'causa' => $causa,
            'cdgpe' => $altaPe,
        ]));

        // SICAFIN: "1 …” éxito · "0 …” error
        if ($msg === '' || !preg_match('/^1\b/', $msg)) {
            $texto = trim(preg_replace('/^[01]\s*/', '', $msg) ?? '');
            throw new \RuntimeException(
                $texto !== '' ? $texto : 'No se pudo registrar en lista negra (SPACCIONLISTANEGRA).'
            );
        }
    }

    private static function existeActivoPorCurp(Database $db, string $curpNormalizado): bool
    {
        if ($curpNormalizado === '') {
            return false;
        }
        $row = $db->queryOne(
            "SELECT COUNT(*) AS CNT
             FROM CL_MARCA
             WHERE CDGEM = :cdgem
               AND TIPOMARCA = :tipo
               AND ESTATUS = 'A'
               AND UPPER(TRIM(CURP)) = :curp",
            [
                'cdgem' => self::CDGEM,
                'tipo' => self::TIPO_LN,
                'curp' => $curpNormalizado,
            ]
        );

        return $row && (int) ($row['CNT'] ?? 0) > 0;
    }

    /**
     * Resuelve el código de causa desde Excel:
     * - "11 - Colaborador" / "11" → código
     * - solo descripción ("Colaborador") → busca en CAT_CAUSA_LISTA_NEGRA
     */
    public static function normalizarCodigoCausa(string $causa, ?Database $db = null): string
    {
        $causa = trim($causa);
        if ($causa === '') {
            return '';
        }
        if (preg_match('/^(\d+)\s*[-–—]\s*.+/u', $causa, $m)) {
            return $m[1];
        }
        if (preg_match('/^\d+$/', $causa)) {
            return $causa;
        }

        $db = $db ?? new Database();
        $row = $db->queryOne(
            "SELECT TRIM(CODIGO) AS CODIGO
             FROM CAT_CAUSA_LISTA_NEGRA
             WHERE CDGEM = :cdgem
               AND TIPO = 'A'
               AND UPPER(TRIM(DESCRIPCION)) = UPPER(TRIM(:descripcion))
               AND ROWNUM = 1",
            ['cdgem' => self::CDGEM, 'descripcion' => $causa]
        );

        return $row ? trim((string) ($row['CODIGO'] ?? '')) : '';
    }

    private static function causaValida(Database $db, string $causa): bool
    {
        $causa = self::normalizarCodigoCausa($causa, $db);
        if ($causa === '') {
            return false;
        }

        $row = $db->queryOne(
            "SELECT COUNT(*) AS CNT
             FROM CAT_CAUSA_LISTA_NEGRA
             WHERE CDGEM = :cdgem
               AND TIPO = 'A'
               AND TRIM(CODIGO) = TRIM(:causa)",
            ['cdgem' => self::CDGEM, 'causa' => $causa]
        );

        return $row && (int) ($row['CNT'] ?? 0) > 0;
    }

    /**
     * @return list<array{CDGCL: string, CURP: string, NOMBRE: string, CDGNS: string|null}>
     */
    private static function buscarPorCredito(Database $db, string $cdgns): array
    {
        $cdgns = self::normalizarCdgns($cdgns);
        if ($cdgns === '') {
            return [];
        }

        // PRC (tradicional) + CN (adicional): empareja CDGNS/CDGCL con LPAD a 6
        $rows = $db->queryAll(
            "SELECT CDGCL, CURP, NOMBRE, CDGNS FROM (
                SELECT DISTINCT
                    LPAD(TRIM(TO_CHAR(PRC.CDGCL)), 6, '0') AS CDGCL,
                    UPPER(TRIM(CL.CURP)) AS CURP,
                    TRIM(
                        COALESCE(
                            NULLIF(TRIM(NVL(GET_NOMBRE_CLIENTE(LPAD(TRIM(TO_CHAR(PRC.CDGCL)), 6, '0')), '')), ''),
                            NULLIF(TRIM(CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE)), '')
                        )
                    ) AS NOMBRE,
                    LPAD(TRIM(TO_CHAR(PRC.CDGNS)), 6, '0') AS CDGNS
                 FROM PRC
                 INNER JOIN CL
                    ON CL.CDGEM = PRC.CDGEM
                   AND LPAD(TRIM(TO_CHAR(CL.CODIGO)), 6, '0') = LPAD(TRIM(TO_CHAR(PRC.CDGCL)), 6, '0')
                 WHERE PRC.CDGEM = :cdgem
                   AND LPAD(TRIM(TO_CHAR(PRC.CDGNS)), 6, '0') = :cdgns
                   AND (PRC.CICLO IS NULL OR (PRC.CICLO NOT LIKE 'D%' AND PRC.CICLO NOT LIKE 'R%'))
                UNION
                SELECT DISTINCT
                    LPAD(TRIM(TO_CHAR(CN.CDGCL)), 6, '0') AS CDGCL,
                    UPPER(TRIM(CL.CURP)) AS CURP,
                    TRIM(
                        COALESCE(
                            NULLIF(TRIM(NVL(GET_NOMBRE_CLIENTE(LPAD(TRIM(TO_CHAR(CN.CDGCL)), 6, '0')), '')), ''),
                            NULLIF(TRIM(CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE)), '')
                        )
                    ) AS NOMBRE,
                    LPAD(TRIM(TO_CHAR(CN.CDGNS)), 6, '0') AS CDGNS
                 FROM CN
                 INNER JOIN CL
                    ON CL.CDGEM = CN.CDGEM
                   AND LPAD(TRIM(TO_CHAR(CL.CODIGO)), 6, '0') = LPAD(TRIM(TO_CHAR(CN.CDGCL)), 6, '0')
                 WHERE CN.CDGEM = :cdgem2
                   AND LPAD(TRIM(TO_CHAR(CN.CDGNS)), 6, '0') = :cdgns2
                   AND CN.ESTATUS = 'A'
             )
             ORDER BY CDGCL",
            [
                'cdgem' => self::CDGEM,
                'cdgns' => $cdgns,
                'cdgem2' => self::CDGEM,
                'cdgns2' => $cdgns,
            ]
        );

        return self::normalizarClientes($rows);
    }

    /** Normaliza número de crédito a 6 dígitos. */
    private static function normalizarCdgns(string $cdgns): string
    {
        $cdgns = strtoupper(trim($cdgns));
        if ($cdgns === '') {
            return '';
        }
        if (preg_match('/^\d+$/', $cdgns)) {
            return str_pad($cdgns, 6, '0', STR_PAD_LEFT);
        }

        return $cdgns;
    }

    /**
     * @return list<array{CDGCL: string, CURP: string, NOMBRE: string, CDGNS: string|null}>
     */
    private static function buscarPorCliente(Database $db, string $cdgcl): array
    {
        $cdgcl = self::normalizarCdgcl($cdgcl);
        if ($cdgcl === '' || !preg_match('/^\d+$/', $cdgcl)) {
            throw new \InvalidArgumentException('El número de cliente debe ser numérico.');
        }

        $rows = $db->queryAll(
            "SELECT
                LPAD(TRIM(TO_CHAR(CL.CODIGO)), 6, '0') AS CDGCL,
                UPPER(TRIM(CL.CURP)) AS CURP,
                TRIM(
                    COALESCE(
                        NULLIF(TRIM(NVL(GET_NOMBRE_CLIENTE(CL.CODIGO), '')), ''),
                        NULLIF(TRIM(CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE)), '')
                    )
                ) AS NOMBRE,
                NULL AS CDGNS
             FROM CL
             WHERE CL.CDGEM = :cdgem
               AND CL.CODIGO = :cdgcl",
            ['cdgem' => self::CDGEM, 'cdgcl' => $cdgcl]
        );

        return self::normalizarClientes($rows);
    }

    /**
     * @return list<array{CDGCL: string, CURP: string, NOMBRE: string, CDGNS: string|null}>
     */
    private static function buscarPorCurp(Database $db, string $curp): array
    {
        $rows = $db->queryAll(
            "SELECT
                LPAD(TRIM(TO_CHAR(CL.CODIGO)), 6, '0') AS CDGCL,
                UPPER(TRIM(CL.CURP)) AS CURP,
                TRIM(
                    COALESCE(
                        NULLIF(TRIM(NVL(GET_NOMBRE_CLIENTE(CL.CODIGO), '')), ''),
                        NULLIF(TRIM(CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE)), '')
                    )
                ) AS NOMBRE,
                NULL AS CDGNS
             FROM CL
             WHERE CL.CDGEM = :cdgem
               AND UPPER(TRIM(CL.CURP)) = :curp",
            ['cdgem' => self::CDGEM, 'curp' => $curp]
        );

        return self::normalizarClientes($rows);
    }

    /**
     * @param array<int, array<string, mixed>>|false|null $rows
     * @return list<array{CDGCL: string, CURP: string, NOMBRE: string, CDGNS: string|null}>
     */
    private static function normalizarClientes($rows): array
    {
        $out = [];
        $vistos = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            $cdgcl = self::normalizarCdgcl((string) ($row['CDGCL'] ?? ''));
            if ($cdgcl === '' || isset($vistos[$cdgcl])) {
                continue;
            }
            $vistos[$cdgcl] = true;
            $curp = ListaNegraEmpleadosService::normalizarCurp((string) ($row['CURP'] ?? ''));
            $nombre = trim(preg_replace('/\s+/u', ' ', (string) ($row['NOMBRE'] ?? '')));
            $cdgns = trim((string) ($row['CDGNS'] ?? ''));
            $out[] = [
                'CDGCL' => $cdgcl,
                'CURP' => $curp,
                'NOMBRE' => $nombre,
                'CDGNS' => $cdgns !== '' ? $cdgns : null,
            ];
        }

        return $out;
    }

    private static function normalizarCdgcl(string $cdgcl): string
    {
        $cdgcl = trim($cdgcl);
        if ($cdgcl === '') {
            return '';
        }

        return str_pad(preg_replace('/\D/', '', $cdgcl) ?? '', 6, '0', STR_PAD_LEFT);
    }

    private static function limpiarDescripcionCausa(string $desc): string
    {
        return trim(preg_replace('/\s*\(Env[ií]a(?: a)? lista negra\)\s*/iu', '', $desc));
    }

    /**
     * @return array{items_validos: list<array{fila:int,tipo:string,valor:string,causa:string}>, formato_errores: list<array{fila:int,curp:string,motivo:string}>, fatal: string|null}
     */
    private static function extraerFilasDesdeCsv(string $ruta): array
    {
        $contenido = @file_get_contents($ruta);
        if ($contenido === false) {
            return ['items_validos' => [], 'formato_errores' => [], 'fatal' => 'No se pudo leer el CSV.'];
        }
        if (strncmp($contenido, "\xEF\xBB\xBF", 3) === 0) {
            $contenido = substr($contenido, 3);
        }
        $lineas = preg_split("/\r\n|\n|\r/", $contenido) ?: [];
        $matriz = [];
        $fila = 0;
        foreach ($lineas as $linea) {
            $fila++;
            if (trim($linea) === '') {
                continue;
            }
            $partes = str_getcsv($linea, ',', '"');
            if (count($partes) < 2) {
                $partes = str_getcsv($linea, ';', '"');
            }
            $matriz[$fila] = [
                1 => trim((string) ($partes[0] ?? '')),
                2 => trim((string) ($partes[1] ?? '')),
                3 => trim((string) ($partes[2] ?? '')),
                4 => trim((string) ($partes[3] ?? '')),
            ];
        }

        return self::procesarMatrizFilas($matriz);
    }

    /**
     * @return array{items_validos: list<array{fila:int,tipo:string,valor:string,causa:string}>, formato_errores: list<array{fila:int,curp:string,motivo:string}>, fatal: string|null}
     */
    private static function extraerFilasDesdeExcel(string $ruta): array
    {
        $magic = @file_get_contents($ruta, false, null, 0, 4);
        $pareceXlsx = ($magic !== false && strlen($magic) >= 2 && $magic[0] === 'P' && $magic[1] === 'K');

        if ($pareceXlsx && !extension_loaded('zip')) {
            $matrizRaw = XlsxSinZipReader::matrizPrimeraHoja($ruta);
            if ($matrizRaw === null) {
                return [
                    'items_validos' => [],
                    'formato_errores' => [],
                    'fatal' => 'No se pudo leer el .xlsx sin extensión zip.',
                ];
            }
            $matriz = [];
            foreach ($matrizRaw as $f => $cols) {
                $matriz[(int) $f] = [
                    1 => trim((string) ($cols[1] ?? '')),
                    2 => trim((string) ($cols[2] ?? '')),
                    3 => trim((string) ($cols[3] ?? '')),
                    4 => trim((string) ($cols[4] ?? '')),
                ];
            }

            return self::procesarMatrizFilas($matriz);
        }

        require_once dirname(__DIR__) . '/../libs/PhpSpreadsheet/PhpSpreadsheet.php';

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($ruta, \PhpOffice\PhpSpreadsheet\Reader\IReader::READ_DATA_ONLY);
        } catch (\Throwable $e) {
            if ($pareceXlsx) {
                $matrizRaw = XlsxSinZipReader::matrizPrimeraHoja($ruta);
                if ($matrizRaw !== null) {
                    $matriz = [];
                    foreach ($matrizRaw as $f => $cols) {
                        $matriz[(int) $f] = [
                            1 => trim((string) ($cols[1] ?? '')),
                            2 => trim((string) ($cols[2] ?? '')),
                            3 => trim((string) ($cols[3] ?? '')),
                            4 => trim((string) ($cols[4] ?? '')),
                        ];
                    }

                    return self::procesarMatrizFilas($matriz);
                }
            }

            return ['items_validos' => [], 'formato_errores' => [], 'fatal' => $e->getMessage()];
        }

        $sheet = $spreadsheet->getSheet(0);
        // Layout: título + encabezados + ~50 filas; no recorrer hojas enormes
        $highest = (int) $sheet->getHighestDataRow();
        $tope = min(500, max(1, $highest) + 2);
        $matriz = [];
        for ($row = 1; $row <= $tope; $row++) {
            $a = trim((string) $sheet->getCell('A' . $row)->getFormattedValue());
            $b = trim((string) $sheet->getCell('B' . $row)->getFormattedValue());
            $c = trim((string) $sheet->getCell('C' . $row)->getFormattedValue());
            $d = trim((string) $sheet->getCell('D' . $row)->getFormattedValue());
            if ($a === '' && $b === '' && $c === '' && $d === '') {
                continue;
            }
            $matriz[$row] = [1 => $a, 2 => $b, 3 => $c, 4 => $d];
        }

        return self::procesarMatrizFilas($matriz);
    }

    /**
     * @param array<int, array{1?:string,2?:string,3?:string,4?:string}> $matriz
     * @return array{items_validos: list<array{fila:int,tipo:string,valor:string,causa:string}>, formato_errores: list<array{fila:int,curp:string,motivo:string}>, fatal: null}
     */
    private static function procesarMatrizFilas(array $matriz): array
    {
        $items = [];
        $errores = [];

        foreach ($matriz as $fila => $cols) {
            $credito = trim((string) ($cols[1] ?? ''));
            $cliente = trim((string) ($cols[2] ?? ''));
            $curp = trim((string) ($cols[3] ?? ''));
            $causa = trim((string) ($cols[4] ?? ''));

            if ($credito === '' && $cliente === '' && $curp === '' && $causa === '') {
                continue;
            }

            // Fila 1 (título) y fila 2 (encabezados) del layout Excel
            if (self::esFilaMetaLayout($credito, $cliente, $curp, $causa)) {
                continue;
            }

            $llenos = 0;
            $tipo = '';
            $valor = '';
            if ($credito !== '') {
                $llenos++;
                $tipo = 'CREDITO';
                $valor = $credito;
            }
            if ($cliente !== '') {
                $llenos++;
                $tipo = 'CLIENTE';
                $valor = $cliente;
            }
            if ($curp !== '') {
                $llenos++;
                $tipo = 'CURP';
                $valor = $curp;
            }

            if ($llenos === 0) {
                $errores[] = ['fila' => $fila, 'curp' => '', 'motivo' => 'Indique CREDITO, CLIENTE o CURP.'];
                continue;
            }
            if ($llenos > 1) {
                $errores[] = ['fila' => $fila, 'curp' => $valor, 'motivo' => 'Solo una columna de identificación por fila (CREDITO, CLIENTE o CURP).'];
                continue;
            }
            if ($causa === '') {
                $errores[] = ['fila' => $fila, 'curp' => $valor, 'motivo' => 'La causa es obligatoria.'];
                continue;
            }

            $items[] = [
                'fila' => $fila,
                'tipo' => $tipo,
                'valor' => $valor,
                'causa' => $causa,
            ];
        }

        return [
            'items_validos' => $items,
            'formato_errores' => $errores,
            'fatal' => null,
        ];
    }

    /** Omite título/encabezados del layout (no son filas de datos). */
    private static function esFilaMetaLayout(string $credito, string $cliente, string $curp, string $causa): bool
    {
        $joined = strtoupper(strtr(
            $credito . ' ' . $cliente . ' ' . $curp . ' ' . $causa,
            ['á' => 'A', 'é' => 'E', 'í' => 'I', 'ó' => 'O', 'ú' => 'U', 'ñ' => 'N', 'ü' => 'U',
             'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N']
        ));

        if (strpos($joined, 'CAPTURE') !== false
            || strpos($joined, 'DESPLEGABLE') !== false
            || strpos($joined, 'IDENTIFICACION') !== false) {
            return true;
        }

        $esEncabezadoCol = (strpos($joined, 'CREDITO') !== false || strpos($joined, 'CLIENTE') !== false)
            && (strpos($joined, 'CURP') !== false || strpos($joined, 'CAUSA') !== false);
        if ($esEncabezadoCol) {
            return true;
        }

        if (preg_match('/^NO\.?\s*CR/iu', $credito)
            || strcasecmp($curp, 'CURP') === 0
            || stripos($causa, 'Causa lista') === 0) {
            return true;
        }

        return false;
    }
}
