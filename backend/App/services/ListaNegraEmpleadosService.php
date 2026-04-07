<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

/**
 * Lista negra de empleados por CURP (CL_MARCA, TIPOMARCA = 'LN').
 * Validación de formato, duplicados, altas y bajas con transacciones Oracle.
 */
class ListaNegraEmpleadosService
{
    private const CDGEM = 'EMPFIN';
    private const TIPO_LN = 'LN';
    private const CAUSA_ALTA = 11;

    /** Nombre del database link hacia la base Cultiva (INSERT ... CL_MARCA@DB_CULTIVA). */
    private const DB_LINK_CULTIVA = 'DB_CULTIVA';

    /** @var string */
    private static $sqlInsert = <<<'SQL'
INSERT INTO CL_MARCA (
    CDGEM, SECUENCIA, CDGCL, CURP, TIPOMARCA, ESTATUS, MONTOMAX, ALTAPE, ALTA, BAJAPE, BAJA, FREGISTRO, CAUSA, CAUSABAJA, CDGCLNS, CICLO, CLNS
) VALUES (
    :cdgem,
    (SELECT NVL(MAX(SECUENCIA), 0) + 1 FROM CL_MARCA WHERE TO_CHAR(ALTA, 'YYYYMMDD') = TO_CHAR(SYSDATE, 'YYYYMMDD')),
    NULL,
    :curp,
    :tipomarca,
    'A',
    NULL,
    :altape,
    TRUNC(SYSDATE),
    NULL,
    NULL,
    SYSDATE,
    :causa,
    NULL,
    NULL,
    NULL,
    NULL
)
SQL;

    /** Misma estructura que el INSERT local; la subconsulta de SECUENCIA usa CL_MARCA en Cultiva vía DB link. */
    private static $sqlInsertCultiva = <<<'SQL'
INSERT INTO CL_MARCA@DB_CULTIVA (
    CDGEM, SECUENCIA, CDGCL, CURP, TIPOMARCA, ESTATUS, MONTOMAX, ALTAPE, ALTA, BAJAPE, BAJA, FREGISTRO, CAUSA, CAUSABAJA, CDGCLNS, CICLO, CLNS
) VALUES (
    :cdgem,
    (SELECT NVL(MAX(SECUENCIA), 0) + 1 FROM CL_MARCA@DB_CULTIVA WHERE TO_CHAR(ALTA, 'YYYYMMDD') = TO_CHAR(SYSDATE, 'YYYYMMDD')),
    NULL,
    :curp,
    :tipomarca,
    'A',
    NULL,
    :altape,
    TRUNC(SYSDATE),
    NULL,
    NULL,
    SYSDATE,
    :causa,
    NULL,
    NULL,
    NULL,
    NULL
)
SQL;

    /**
     * Normaliza CURP (mayúsculas, sin espacios).
     * Usa Unicode: la Ñ cuenta como un carácter (strlen en UTF-8 cuenta bytes y fallaba la validación).
     */
    public static function normalizarCurp(string $curp): string
    {
        $curp = preg_replace('/[\x{FEFF}\x{200B}-\x{200D}\x{2060}]/u', '', $curp);
        $curp = preg_replace('/\s+/u', '', trim($curp));

        return mb_strtoupper($curp, 'UTF-8');
    }

    /**
     * Texto del layout descargado (título/subtítulo de Excel) — no es un CURP.
     */
    private static function esTextoInstruccionLayout(string $normalizado): bool
    {
        if ($normalizado === '') {
            return false;
        }
        if ($normalizado === 'CURP') {
            return true;
        }
        if (mb_strlen($normalizado, 'UTF-8') > 24) {
            if (stripos($normalizado, 'CAPTURE') !== false
                || stripos($normalizado, 'COLUMNA') !== false
                || stripos($normalizado, 'OMITIR') !== false
                || stripos($normalizado, 'TÍTULO') !== false
                || stripos($normalizado, 'TITULO') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Valida longitud y caracteres del CURP (18 caracteres alfanuméricos).
     *
     * @return string|null Mensaje de error o null si es válido
     */
    public static function validarFormatoCurp(string $curp): ?string
    {
        $c = self::normalizarCurp($curp);
        if ($c === '') {
            return 'El CURP es obligatorio.';
        }
        $len = mb_strlen($c, 'UTF-8');
        if ($len !== 18) {
            return 'El CURP debe tener exactamente 18 caracteres.';
        }
        if (!preg_match('/^[A-ZÑ0-9]{18}$/u', $c)) {
            return 'El CURP solo puede contener letras y números (18 posiciones).';
        }
        return null;
    }

    /**
     * Indica si ya existe un registro activo o cualquier LN con el mismo CURP.
     */
    public static function existeCurpEnListaNegra(Database $db, string $curpNormalizado): bool
    {
        $sql = <<<SQL
            SELECT COUNT(*) AS CNT
            FROM CL_MARCA
            WHERE CDGEM = :cdgem
              AND TIPOMARCA = :tipo
              AND UPPER(TRIM(CURP)) = :curp
SQL;
        $row = $db->queryOne($sql, [
            'cdgem' => self::CDGEM,
            'tipo'  => self::TIPO_LN,
            'curp'  => $curpNormalizado,
        ]);
        return $row && (int) ($row['CNT'] ?? 0) > 0;
    }

    /**
     * Inserta un CURP en CL_MARCA (MCM) y en CL_MARCA@DB_CULTIVA (Cultiva).
     * Debe ejecutarse dentro de una transacción abierta: si falla Cultiva, el rollback revierte también MCM.
     */
    public static function insertar(Database $db, string $curpNormalizado, string $altaPe): void
    {
        $params = [
            'cdgem'     => self::CDGEM,
            'curp'      => $curpNormalizado,
            'tipomarca' => self::TIPO_LN,
            'altape'    => $altaPe !== '' ? $altaPe : 'SYSTEM',
            'causa'     => self::CAUSA_ALTA,
        ];

        // 1) Alta en base local (MCM)
        $stmtLocal = $db->db_activa->prepare(self::$sqlInsert);
        $okLocal = $stmtLocal->execute($params);
        if (!$okLocal) {
            $err = $stmtLocal->errorInfo();
            throw new \RuntimeException($err[2] ?? 'Error al insertar en CL_MARCA (MCM).');
        }

        // 2) Alta en Cultiva vía database link (misma transacción lógica en el sesión Oracle)
        $stmtCultiva = $db->db_activa->prepare(self::$sqlInsertCultiva);
        $okCultiva = $stmtCultiva->execute($params);
        if (!$okCultiva) {
            $err = $stmtCultiva->errorInfo();
            throw new \RuntimeException($err[2] ?? 'Error al insertar en CL_MARCA@' . self::DB_LINK_CULTIVA . ' (Cultiva).');
        }
    }

    /**
     * Alta individual con transacción.
     */
    public static function guardarUno(string $curp, string $usuarioSesion): array
    {
        $err = self::validarFormatoCurp($curp);
        if ($err !== null) {
            return Model::Responde(false, $err);
        }
        $norm = self::normalizarCurp($curp);
        $db = new Database();
        try {
            $db->AutoCommitOff();
            $db->IniciaTransaccion();
            if (self::existeCurpEnListaNegra($db, $norm)) {
                $db->CancelaTransaccion();
                return Model::Responde(false, 'El CURP ya está registrado en la lista negra.');
            }
            self::insertar($db, $norm, $usuarioSesion);
            $db->ConfirmaTransaccion();
            return Model::Responde(true, 'CURP registrado correctamente.');
        } catch (\Throwable $e) {
            $db->CancelaTransaccion();
            return Model::Responde(false, 'No se pudo registrar el CURP.', null, $e->getMessage());
        }
    }

    /**
     * Lista registros LN con filtro opcional por CURP.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function listar(?string $filtro = null): array
    {
        $sql = <<<SQL
            SELECT
                SECUENCIA,
                TRIM(CURP) AS CURP,
                ESTATUS,
                TO_CHAR(TRUNC(ALTA), 'YYYY-MM-DD') AS ALTA_DIA,
                TO_CHAR(ALTA, 'DD/MM/YYYY') AS ALTA_FMT,
                TO_CHAR(TRUNC(BAJA), 'DD/MM/YYYY') AS BAJA_FMT,
                FREGISTRO
            FROM CL_MARCA
            WHERE CDGEM = :cdgem
              AND TIPOMARCA = :tipo
              AND CAUSA = 11
SQL;
        $prm = ['cdgem' => self::CDGEM, 'tipo' => self::TIPO_LN];
        if ($filtro !== null && trim($filtro) !== '') {
            $sql .= ' AND UPPER(TRIM(CURP)) LIKE UPPER(:filtro)';
            $prm['filtro'] = '%' . trim($filtro) . '%';
        }
        $sql .= ' ORDER BY FREGISTRO DESC NULLS LAST, ALTA DESC';

        $db = new Database();
        $res = $db->queryAll($sql, $prm);
        if ($res === false) {
            throw new \RuntimeException('Error al consultar CL_MARCA.');
        }
        return is_array($res) ? $res : [];
    }

    /**
     * Baja lógica: ESTATUS = 'B', BAJA y BAJAPE.
     */
    public static function darBaja(int $secuencia, string $altaDiaYmd, string $curp, string $usuarioSesion): array
    {
        $norm = self::normalizarCurp($curp);
        $err = self::validarFormatoCurp($norm);
        if ($err !== null) {
            return Model::Responde(false, $err);
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $altaDiaYmd)) {
            return Model::Responde(false, 'Fecha de alta inválida.');
        }

        $sql = <<<SQL
            UPDATE CL_MARCA
            SET ESTATUS = 'B',
                BAJA = TRUNC(SYSDATE),
                BAJAPE = :bajape
            WHERE CDGEM = :cdgem
              AND TIPOMARCA = :tipo
              AND SECUENCIA = :secuencia
              AND TRUNC(ALTA) = TO_DATE(:alta_dia, 'YYYY-MM-DD')
              AND UPPER(TRIM(CURP)) = :curp
              AND ESTATUS = 'A'
SQL;

        $db = new Database();
        try {
            $db->AutoCommitOff();
            $db->IniciaTransaccion();
            $stmt = $db->db_activa->prepare($sql);
            $ok = $stmt->execute([
                'bajape'    => $usuarioSesion !== '' ? $usuarioSesion : 'SYSTEM',
                'cdgem'     => self::CDGEM,
                'tipo'      => self::TIPO_LN,
                'secuencia' => $secuencia,
                'alta_dia'  => $altaDiaYmd,
                'curp'      => $norm,
            ]);
            if (!$ok) {
                throw new \RuntimeException($stmt->errorInfo()[2] ?? 'Error al actualizar.');
            }
            $afectados = $stmt->rowCount();
            if ($afectados === 0) {
                $chk = $db->queryOne(
                    <<<SQL
                        SELECT COUNT(*) AS CNT FROM CL_MARCA
                        WHERE CDGEM = :cdgem AND TIPOMARCA = :tipo AND SECUENCIA = :secuencia
                          AND TRUNC(ALTA) = TO_DATE(:alta_dia, 'YYYY-MM-DD')
                          AND UPPER(TRIM(CURP)) = :curp
                    SQL,
                    [
                        'cdgem' => self::CDGEM,
                        'tipo' => self::TIPO_LN,
                        'secuencia' => $secuencia,
                        'alta_dia' => $altaDiaYmd,
                        'curp' => $norm,
                    ]
                );
                if (!$chk || (int) ($chk['CNT'] ?? 0) === 0) {
                    $db->CancelaTransaccion();
                    return Model::Responde(false, 'No se encontró el registro indicado.');
                }
                $db->CancelaTransaccion();
                return Model::Responde(false, 'El registro ya estaba dado de baja o no está activo.');
            }
            $db->ConfirmaTransaccion();
            return Model::Responde(true, 'Baja registrada correctamente.');
        } catch (\Throwable $e) {
            $db->CancelaTransaccion();
            return Model::Responde(false, 'No se pudo dar de baja el registro.', null, $e->getMessage());
        }
    }

    /**
     * Abre el libro: IOFactory identifica el tipo por contenido (xlsx/xls/ods, etc.).
     *
     * @throws \Throwable
     */
    private static function cargarSpreadsheetListaNegra(string $ruta): \PhpOffice\PhpSpreadsheet\Spreadsheet
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

    /**
     * Texto de celda para CURP (compatible con valor en texto, número o fórmula).
     */
    private static function valorCeldaComoTexto(\PhpOffice\PhpSpreadsheet\Cell\Cell $cell): string
    {
        foreach (['getFormattedValue', 'getValue', 'getCalculatedValue'] as $getter) {
            $v = $cell->{$getter}();
            if ($v === null || $v === '') {
                continue;
            }
            if (is_object($v) && method_exists($v, '__toString')) {
                $s = trim((string) $v);
            } elseif (is_scalar($v)) {
                $s = trim((string) $v);
            } else {
                continue;
            }
            if ($s !== '') {
                return $s;
            }
        }

        return '';
    }

    /**
     * CURPs desde CSV/TXT: primera columna, un CURP por línea (también admite separador coma/punto y coma).
     * No requiere extensión zip de PHP (alternativa cuando .xlsx falla en el servidor).
     *
     * @return array{items_validos: list<array{fila:int,curp:string}>, formato_errores: array<int, array{fila:int,curp:string,motivo:string}>, duplicados_archivo: array<int, array{fila:int,curp:string,motivo:string}>, fatal: string|null}
     */
    private static function extraerCurpsDesdeCsv(string $ruta): array
    {
        $contenido = @file_get_contents($ruta);
        if ($contenido === false) {
            return [
                'items_validos' => [],
                'formato_errores' => [],
                'duplicados_archivo' => [],
                'fatal' => 'No se pudo leer el archivo CSV.',
            ];
        }
        if (strncmp($contenido, "\xEF\xBB\xBF", 3) === 0) {
            $contenido = substr($contenido, 3);
        }
        $lineas = preg_split("/\r\n|\n|\r/", $contenido);
        $vistos = [];
        $itemsValidos = [];
        $duplicadosArchivo = [];
        $formatoErrores = [];
        $fila = 0;
        foreach ($lineas as $linea) {
            $fila++;
            $linea = trim($linea);
            if ($linea === '') {
                continue;
            }
            $partes = str_getcsv($linea, ',', '"');
            if (count($partes) < 1) {
                continue;
            }
            $raw = trim((string) ($partes[0] ?? ''));
            if ($raw === '' && isset($partes[1])) {
                $raw = trim((string) $partes[1]);
            }
            if ($raw === '') {
                $partesSc = str_getcsv($linea, ';', '"');
                $raw = trim((string) ($partesSc[0] ?? ''));
            }
            if ($raw === '') {
                continue;
            }
            $val = self::normalizarCurp($raw);
            if (self::esTextoInstruccionLayout($val)) {
                continue;
            }
            if ($fila === 1 && stripos($val, 'CURP') !== false && mb_strlen($val, 'UTF-8') <= 12) {
                continue;
            }
            $err = self::validarFormatoCurp($val);
            if ($err !== null) {
                $formatoErrores[] = ['fila' => $fila, 'curp' => $raw, 'motivo' => $err];
                continue;
            }
            $norm = self::normalizarCurp($val);
            if (isset($vistos[$norm])) {
                $duplicadosArchivo[] = [
                    'fila' => $fila,
                    'curp' => $raw,
                    'motivo' => 'CURP repetido en el archivo (se conservó la fila ' . $vistos[$norm] . ').',
                ];
                continue;
            }
            $vistos[$norm] = $fila;
            $itemsValidos[] = ['fila' => $fila, 'curp' => $norm];
        }

        return [
            'items_validos' => $itemsValidos,
            'formato_errores' => $formatoErrores,
            'duplicados_archivo' => $duplicadosArchivo,
            'fatal' => null,
        ];
    }

    /**
     * Valida y deduplica CURPs a partir de textos por fila (índice 0 = fila 1).
     *
     * @param list<string> $textosPorFila
     * @return array{items_validos: list<array{fila:int,curp:string}>, formato_errores: array<int, array{fila:int,curp:string,motivo:string}>, duplicados_archivo: array<int, array{fila:int,curp:string,motivo:string}>, fatal: null}
     */
    private static function procesarCurpsDesdeTextosColumnaA(array $textosPorFila): array
    {
        $vistos = [];
        $itemsValidos = [];
        $duplicadosArchivo = [];
        $formatoErrores = [];

        foreach ($textosPorFila as $idx => $raw) {
            $row = $idx + 1;
            $raw = trim((string) $raw);
            if ($raw === '') {
                continue;
            }
            $val = self::normalizarCurp($raw);
            if (self::esTextoInstruccionLayout($val)) {
                continue;
            }
            if ($row === 1 && stripos($val, 'CURP') !== false && mb_strlen($val, 'UTF-8') <= 12) {
                continue;
            }
            $err = self::validarFormatoCurp($val);
            if ($err !== null) {
                $formatoErrores[] = ['fila' => $row, 'curp' => $raw, 'motivo' => $err];
                continue;
            }
            $norm = self::normalizarCurp($val);
            if (isset($vistos[$norm])) {
                $duplicadosArchivo[] = [
                    'fila' => $row,
                    'curp' => $raw,
                    'motivo' => 'CURP repetido en el archivo (se conservó la fila ' . $vistos[$norm] . ').',
                ];
                continue;
            }
            $vistos[$norm] = $row;
            $itemsValidos[] = ['fila' => $row, 'curp' => $norm];
        }

        return [
            'items_validos' => $itemsValidos,
            'formato_errores' => $formatoErrores,
            'duplicados_archivo' => $duplicadosArchivo,
            'fatal' => null,
        ];
    }

    /**
     * CURPs desde Excel (columna A) usando iteración de filas.
     * Si falta la extensión PHP zip, los .xlsx se leen con un lector ZIP/XML propio (solo zlib).
     *
     * @return array{items_validos: list<array{fila:int,curp:string}>, formato_errores: array<int, array{fila:int,curp:string,motivo:string}>, duplicados_archivo: array<int, array{fila:int,curp:string,motivo:string}>, fatal: string|null}
     */
    private static function extraerCurpsDesdeExcel(string $ruta): array
    {
        $magic = @file_get_contents($ruta, false, null, 0, 4);
        $pareceXlsxZip = ($magic !== false && strlen($magic) >= 2 && $magic[0] === 'P' && $magic[1] === 'K');

        if ($pareceXlsxZip && !extension_loaded('zip')) {
            $textos = XlsxSinZipReader::valoresColumnaA($ruta);
            if ($textos !== null) {
                return self::procesarCurpsDesdeTextosColumnaA($textos);
            }

            return [
                'items_validos' => [],
                'formato_errores' => [],
                'duplicados_archivo' => [],
                'fatal' => 'No se pudo leer el .xlsx sin extensión zip (lector interno falló). Active php_zip o zlib, o use un archivo .csv.',
            ];
        }

        require_once dirname(__DIR__) . '/../libs/PhpSpreadsheet/PhpSpreadsheet.php';

        try {
            $spreadsheet = self::cargarSpreadsheetListaNegra($ruta);
        } catch (\Throwable $e) {
            if ($pareceXlsxZip) {
                $textos = XlsxSinZipReader::valoresColumnaA($ruta);
                if ($textos !== null) {
                    return self::procesarCurpsDesdeTextosColumnaA($textos);
                }
            }

            return [
                'items_validos' => [],
                'formato_errores' => [],
                'duplicados_archivo' => [],
                'fatal' => $e->getMessage(),
            ];
        }

        $sheet = $spreadsheet->getSheet(0);
        $maxFila = (int) $sheet->getHighestRow();
        if ($maxFila < 1) {
            $maxFila = 1;
        }
        $maxDatosA = (int) $sheet->getHighestDataRow('A');
        $tope = min(65535, max($maxFila, $maxDatosA, 1) + 10);

        $textosPorFila = [];
        for ($row = 1; $row <= $tope; $row++) {
            $cell = $sheet->getCell('A' . $row);
            $textosPorFila[] = self::valorCeldaComoTexto($cell);
        }

        return self::procesarCurpsDesdeTextosColumnaA($textosPorFila);
    }

    /**
     * Procesa archivo: Excel (.xlsx, .xls, …) o CSV (.csv/.txt) con CURP en la primera columna.
     * Si Excel falla (p. ej. sin extensión zip en PHP), se puede usar CSV exportado desde Excel.
     *
     * Inserta CURPs válidos uno a uno; no se detiene por filas con error (formato, duplicado, etc.).
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
        $leyoComoCsv = $esCsv;

        if ($esCsv) {
            $extra = self::extraerCurpsDesdeCsv($rutaArchivo);
            if (!empty($extra['fatal'])) {
                return Model::Responde(false, 'No se pudo leer el CSV.', null, $extra['fatal']);
            }
        } else {
            $extra = self::extraerCurpsDesdeExcel($rutaArchivo);
            if (!empty($extra['fatal'])) {
                $magic = @file_get_contents($rutaArchivo, false, null, 0, 4);
                $pareceZipXlsx = ($magic !== false && strlen($magic) >= 2 && $magic[0] === 'P' && $magic[1] === 'K');
                if (!$pareceZipXlsx) {
                    $extra = self::extraerCurpsDesdeCsv($rutaArchivo);
                    $leyoComoCsv = empty($extra['fatal']);
                }
            }
            if (!empty($extra['fatal'])) {
                $hint = ' Pruebe guardar como CSV UTF-8 o use el layout CSV de la pantalla. Si es administrador del servidor, habilite las extensiones PHP zip y zlib.';
                return Model::Responde(false, 'No se pudo leer el archivo Excel.' . $hint, null, $extra['fatal']);
            }
        }

        $itemsValidos = $extra['items_validos'] ?? [];
        $formatoErrores = $extra['formato_errores'] ?? [];
        $duplicadosArchivo = $extra['duplicados_archivo'] ?? [];

        $omitidosPrevios = count($formatoErrores) + count($duplicadosArchivo);
        if (empty($itemsValidos) && $omitidosPrevios === 0) {
            $msg = $leyoComoCsv
                ? 'El archivo no contiene CURPs válidos en la primera columna.'
                : 'El archivo no contiene CURPs en la columna A.';

            return Model::Responde(false, $msg);
        }

        $altaPe = $usuarioSesion !== '' ? $usuarioSesion : 'SYSTEM';
        $db = new Database();
        $erroresDb = [];
        $insertados = 0;

        foreach ($itemsValidos as $item) {
            $norm = $item['curp'];
            $fila = $item['fila'];
            try {
                $db->AutoCommitOff();
                $db->IniciaTransaccion();
                if (self::existeCurpEnListaNegra($db, $norm)) {
                    $db->CancelaTransaccion();
                    $erroresDb[] = ['fila' => $fila, 'curp' => $norm, 'motivo' => 'Ya existe en lista negra'];
                    continue;
                }
                self::insertar($db, $norm, $altaPe);
                $db->ConfirmaTransaccion();
                $insertados++;
            } catch (\Throwable $e) {
                try {
                    $db->CancelaTransaccion();
                } catch (\Throwable $e2) {
                }
                $erroresDb[] = ['fila' => $fila, 'curp' => $norm, 'motivo' => $e->getMessage()];
            }
        }

        $todosErrores = array_merge($formatoErrores, $duplicadosArchivo, $erroresDb);
        $omitidos = count($todosErrores);

        if ($omitidos === 0 && $insertados > 0) {
            $mensaje = $insertados === 1
                ? 'Importación finalizada. Se registró 1 CURP.'
                : sprintf('Importación finalizada. Se registraron %d CURP.', $insertados);
        } elseif ($omitidos > 0) {
            $mensaje = sprintf(
                'Importación finalizada. Registrados: %d. No procesados: %d.',
                $insertados,
                $omitidos
            );
        } else {
            $mensaje = 'Importación finalizada.';
        }

        $exito = $insertados > 0 || $omitidos > 0;

        return array_merge(
            Model::Responde($exito, $mensaje),
            [
                'insertados' => $insertados,
                'omitidos' => $omitidos,
                'omitidos_formato' => count($formatoErrores),
                'omitidos_duplicado_archivo' => count($duplicadosArchivo),
                'omitidos_base' => count($erroresDb),
                'errores' => $todosErrores,
            ]
        );
    }
}
