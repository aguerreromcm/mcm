<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

/**
 * Lee columna A de la primera hoja de un .xlsx sin usar extensión PHP zip (ZipArchive).
 * Los .xlsx son ZIP con XML; solo se necesita zlib para DEFLATE.
 */
final class XlsxSinZipReader
{
    /**
     * Extrae textos de la columna A en orden de fila (índice = número de fila 1-based).
     * Omite celdas vacías en A pero conserva la fila para orden al combinar con otras columnas no usadas.
     *
     * @return list<string>|null null si no se pudo leer (no es zip, sin zlib, XML inválido)
     */
    public static function valoresColumnaA(string $rutaArchivo): ?array
    {
        $matriz = self::matrizPrimeraHoja($rutaArchivo);
        if ($matriz === null) {
            return null;
        }

        $textos = [];
        foreach ($matriz as $fila => $columnas) {
            $textos[] = trim((string) ($columnas[1] ?? ''));
        }

        return $textos;
    }

    /**
     * Matriz de la primera hoja: fila 1-based => columna 1-based => texto.
     *
     * @return array<int, array<int, string>>|null
     */
    public static function matrizPrimeraHoja(string $rutaArchivo): ?array
    {
        if (!extension_loaded('zlib')) {
            return null;
        }
        $bin = @file_get_contents($rutaArchivo);
        if ($bin === false || strlen($bin) < 30 || substr($bin, 0, 2) !== 'PK') {
            return null;
        }

        $hoja = self::obtenerXmlPrimeraHoja($bin);
        if ($hoja === null || $hoja === '') {
            return null;
        }

        $ss = self::extraerEntradaZip($bin, 'xl/sharedStrings.xml');
        $shared = $ss !== null && $ss !== '' ? self::parseSharedStrings($ss) : [];

        return self::parseHojaCompleta($hoja, $shared);
    }

    private static function obtenerXmlPrimeraHoja(string $bin): ?string
    {
        $directo = self::extraerEntradaZip($bin, 'xl/worksheets/sheet1.xml');
        if ($directo !== null && $directo !== '') {
            return $directo;
        }

        $wb = self::extraerEntradaZip($bin, 'xl/workbook.xml');
        if ($wb === null || $wb === '') {
            return null;
        }

        $rid = null;
        if (preg_match('/<sheets[^>]*>[\s\S]*?<sheet[^>]+r:id="([^"]+)"[^>]*>/i', $wb, $m)) {
            $rid = $m[1];
        }
        if ($rid === null && preg_match('/<sheet[^>]+r:id="([^"]+)"[^>]*\/?>/i', $wb, $m2)) {
            $rid = $m2[1];
        }
        if ($rid === null) {
            return self::primeraHojaPorNombre($bin);
        }

        $rels = self::extraerEntradaZip($bin, 'xl/_rels/workbook.xml.rels');
        if ($rels === null || $rels === '') {
            return self::primeraHojaPorNombre($bin);
        }

        $target = null;
        $ridQ = preg_quote($rid, '/');
        if (preg_match('/<Relationship[^>]*\bId="' . $ridQ . '"[^>]*\bTarget="([^"]+)"/i', $rels, $rm)) {
            $target = $rm[1];
        }
        if ($target === null && preg_match('/<Relationship[^>]*\bTarget="([^"]+)"[^>]*\bId="' . $ridQ . '"/i', $rels, $rm)) {
            $target = $rm[1];
        }
        if ($target === null && preg_match('/<Relationship[^>]*\bId=\'' . $ridQ . '\'[^>]*\bTarget=\'([^\']+)\'/i', $rels, $rm2)) {
            $target = $rm2[1];
        }
        if ($target === null) {
            return self::primeraHojaPorNombre($bin);
        }

        $target = str_replace('\\', '/', $target);
        if (strpos($target, 'worksheets/') === 0) {
            $path = 'xl/' . $target;
        } elseif (strpos($target, '/xl/') === 0) {
            $path = ltrim($target, '/');
        } else {
            $path = 'xl/worksheets/' . basename($target);
        }

        $xml = self::extraerEntradaZip($bin, $path);

        return ($xml !== null && $xml !== '') ? $xml : self::primeraHojaPorNombre($bin);
    }

    private static function primeraHojaPorNombre(string $bin): ?string
    {
        $nombres = self::listarEntradasPrefijo($bin, 'xl/worksheets/');
        sort($nombres, SORT_NATURAL);
        foreach ($nombres as $name) {
            if (preg_match('#^xl/worksheets/sheet\d+\.xml$#i', $name)) {
                $xml = self::extraerEntradaZip($bin, $name);

                return ($xml !== null && $xml !== '') ? $xml : null;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private static function listarEntradasPrefijo(string $bin, string $prefijo): array
    {
        $eocd = self::localizarEocd($bin);
        if ($eocd === null) {
            return [];
        }
        $cdOffset = unpack('V', substr($bin, $eocd + 16, 4))[1];
        $cdSize = unpack('V', substr($bin, $eocd + 12, 4))[1];
        $cd = substr($bin, $cdOffset, $cdSize);
        if ($cd === false || $cd === '') {
            return [];
        }
        $out = [];
        $pos = 0;
        $len = strlen($cd);
        while ($pos + 46 <= $len) {
            if (substr($cd, $pos, 4) !== "PK\x01\x02") {
                break;
            }
            $fnLen = unpack('v', substr($cd, $pos + 28, 2))[1];
            $exLen = unpack('v', substr($cd, $pos + 30, 2))[1];
            $cmLen = unpack('v', substr($cd, $pos + 32, 2))[1];
            $name = substr($cd, $pos + 46, $fnLen);
            if (strncasecmp($name, $prefijo, strlen($prefijo)) === 0) {
                $out[] = str_replace('\\', '/', $name);
            }
            $pos += 46 + $fnLen + $exLen + $cmLen;
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private static function parseSharedStrings(string $xml): array
    {
        $dom = new \DOMDocument();
        if (@$dom->loadXML($xml) === false) {
            return [];
        }
        $xp = new \DOMXPath($dom);
        $items = $xp->query('//*[local-name()="si"]');
        if ($items === false || $items->length === 0) {
            return [];
        }
        $out = [];
        for ($i = 0; $i < $items->length; $i++) {
            $si = $items->item($i);
            if (!$si instanceof \DOMElement) {
                $out[] = '';
                continue;
            }
            $texto = '';
            $ts = $xp->query('.//*[local-name()="t"]', $si);
            if ($ts !== false && $ts->length > 0) {
                for ($j = 0; $j < $ts->length; $j++) {
                    $texto .= $ts->item($j)->textContent;
                }
            } else {
                $texto = trim($si->textContent);
            }
            $out[] = $texto;
        }

        return $out;
    }

    /**
     * Convierte letras de columna Excel (A, B, …, AA) a índice 1-based (A=1).
     */
    private static function columnLettersToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $n = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $c = $letters[$i];
            if ($c < 'A' || $c > 'Z') {
                return 0;
            }
            $n = $n * 26 + (ord($c) - 64);
        }

        return $n;
    }

    /**
     * @param list<string> $sharedStrings
     * @return array<int, array<int, string>>
     */
    private static function parseHojaCompleta(string $sheetXml, array $sharedStrings): array
    {
        $dom = new \DOMDocument();
        if (@$dom->loadXML($sheetXml) === false) {
            return [];
        }
        $xp = new \DOMXPath($dom);
        $rows = $xp->query('//*[local-name()="sheetData"]//*[local-name()="row"]');
        if ($rows === false || $rows->length === 0) {
            return [];
        }

        /** @var array<int, array<int, string>> $porFila */
        $porFila = [];

        $implicitRow = 0;
        for ($ri = 0; $ri < $rows->length; $ri++) {
            $rowEl = $rows->item($ri);
            if (!$rowEl instanceof \DOMElement) {
                continue;
            }
            $implicitRow++;
            $rowNumFromAttr = (int) $rowEl->getAttribute('r');
            $filaFila = $rowNumFromAttr > 0 ? $rowNumFromAttr : $implicitRow;

            $cells = $xp->query('.//*[local-name()="c"]', $rowEl);
            if ($cells === false || $cells->length === 0) {
                continue;
            }

            $prevCol = 0;

            for ($ci = 0; $ci < $cells->length; $ci++) {
                $c = $cells->item($ci);
                if (!$c instanceof \DOMElement) {
                    continue;
                }
                $ref = $c->getAttribute('r');
                if ($ref !== '') {
                    if (!preg_match('/^([A-Z]+)(\d+)$/i', $ref, $m)) {
                        continue;
                    }
                    $colIdx = self::columnLettersToIndex($m[1]);
                    $fila = (int) $m[2];
                    $prevCol = $colIdx;
                } else {
                    $colIdx = $prevCol + 1;
                    $prevCol = $colIdx;
                    $fila = $filaFila;
                }

                if ($colIdx < 1) {
                    continue;
                }

                $texto = trim(self::valorCeldaDom($xp, $c, $sharedStrings));
                if ($texto === '') {
                    continue;
                }

                if (!isset($porFila[$fila])) {
                    $porFila[$fila] = [];
                }
                $porFila[$fila][$colIdx] = $texto;
            }
        }

        return $porFila;
    }

    /**
     * @param list<string> $sharedStrings
     * @return list<string>
     */
    private static function parseColumnaA(string $sheetXml, array $sharedStrings): array
    {
        $dom = new \DOMDocument();
        if (@$dom->loadXML($sheetXml) === false) {
            return [];
        }
        $xp = new \DOMXPath($dom);
        $rows = $xp->query('//*[local-name()="sheetData"]//*[local-name()="row"]');
        if ($rows === false || $rows->length === 0) {
            return [];
        }

        /** @var array<int, string> $porFila fila => texto */
        $porFila = [];

        $implicitRow = 0;
        for ($ri = 0; $ri < $rows->length; $ri++) {
            $rowEl = $rows->item($ri);
            if (!$rowEl instanceof \DOMElement) {
                continue;
            }
            $implicitRow++;
            $rowNumFromAttr = (int) $rowEl->getAttribute('r');
            $filaFila = $rowNumFromAttr > 0 ? $rowNumFromAttr : $implicitRow;

            $cells = $xp->query('.//*[local-name()="c"]', $rowEl);
            if ($cells === false || $cells->length === 0) {
                continue;
            }

            $prevCol = 0;

            for ($ci = 0; $ci < $cells->length; $ci++) {
                $c = $cells->item($ci);
                if (!$c instanceof \DOMElement) {
                    continue;
                }
                $ref = $c->getAttribute('r');
                if ($ref !== '') {
                    if (!preg_match('/^([A-Z]+)(\d+)$/i', $ref, $m)) {
                        continue;
                    }
                    $colIdx = self::columnLettersToIndex($m[1]);
                    $fila = (int) $m[2];
                    $prevCol = $colIdx;
                } else {
                    $colIdx = $prevCol + 1;
                    $prevCol = $colIdx;
                    $fila = $filaFila;
                }

                if ($colIdx !== 1) {
                    continue;
                }

                $texto = self::valorCeldaDom($xp, $c, $sharedStrings);
                $texto = trim($texto);
                if ($texto !== '') {
                    $porFila[$fila] = $texto;
                }
            }
        }

        if ($porFila === []) {
            return [];
        }

        $maxFila = max(array_keys($porFila));
        $ordenados = [];
        for ($i = 1; $i <= $maxFila; $i++) {
            $ordenados[] = $porFila[$i] ?? '';
        }

        return $ordenados;
    }

    private static function valorCeldaDom(\DOMXPath $xp, \DOMElement $c, array $sharedStrings): string
    {
        $tipo = $c->getAttribute('t');

        if ($tipo === 'inlineStr') {
            $texto = '';
            $ts = $xp->query('.//*[local-name()="t"]', $c);
            if ($ts !== false && $ts->length > 0) {
                for ($j = 0; $j < $ts->length; $j++) {
                    $texto .= $ts->item($j)->textContent;
                }
            }

            return $texto;
        }

        $vs = $xp->query('.//*[local-name()="v"]', $c);
        if ($vs === false || $vs->length === 0) {
            return '';
        }
        $raw = trim((string) $vs->item(0)->textContent);

        if ($tipo === 's') {
            $idx = (int) $raw;

            return $sharedStrings[$idx] ?? '';
        }

        return $raw;
    }

    private static function extraerEntradaZip(string $bin, string $nombreBuscado): ?string
    {
        $nombreBuscado = str_replace('\\', '/', $nombreBuscado);
        $eocd = self::localizarEocd($bin);
        if ($eocd === null) {
            return null;
        }

        $cdOffset = unpack('V', substr($bin, $eocd + 16, 4))[1];
        $cdSize = unpack('V', substr($bin, $eocd + 12, 4))[1];
        $cd = substr($bin, $cdOffset, $cdSize);
        if ($cd === false || $cd === '') {
            return null;
        }

        $pos = 0;
        $len = strlen($cd);
        while ($pos + 46 <= $len) {
            if (substr($cd, $pos, 4) !== "PK\x01\x02") {
                break;
            }
            $method = unpack('v', substr($cd, $pos + 10, 2))[1];
            $compSize = unpack('V', substr($cd, $pos + 20, 4))[1];
            $uncompSize = unpack('V', substr($cd, $pos + 24, 4))[1];
            $fnLen = unpack('v', substr($cd, $pos + 28, 2))[1];
            $exLen = unpack('v', substr($cd, $pos + 30, 2))[1];
            $cmLen = unpack('v', substr($cd, $pos + 32, 2))[1];
            $localHdr = unpack('V', substr($cd, $pos + 42, 4))[1];
            $name = substr($cd, $pos + 46, $fnLen);

            $nameNorm = str_replace('\\', '/', $name);
            if (strcasecmp($nameNorm, $nombreBuscado) === 0) {
                $lh = substr($bin, $localHdr, 30 + $fnLen);
                if ($lh === false || strlen($lh) < 30) {
                    return null;
                }
                $extraLocal = unpack('v', substr($lh, 28, 2))[1];
                $dataStart = $localHdr + 30 + $fnLen + $extraLocal;
                $raw = substr($bin, $dataStart, $compSize);
                if ($raw === false) {
                    return null;
                }

                return self::descomprimir($method, $raw, $uncompSize);
            }

            $pos += 46 + $fnLen + $exLen + $cmLen;
        }

        return null;
    }

    private static function descomprimir(int $method, string $raw, int $uncompSize): ?string
    {
        if ($method === 0) {
            return $raw;
        }
        if ($method !== 8) {
            return null;
        }
        $dec = self::inflateRaw($raw);
        if ($dec !== null && $dec !== '') {
            return $dec;
        }

        return null;
    }

    private static function inflateRaw(string $data): ?string
    {
        if (function_exists('inflate_init')) {
            $ctx = @inflate_init(ZLIB_ENCODING_RAW);
            if ($ctx !== false) {
                $out = @inflate_add($ctx, $data, ZLIB_FINISH);
                if ($out !== false && $out !== '') {
                    return $out;
                }
            }
        }

        $try = @gzinflate($data);
        if ($try !== false && $try !== '') {
            return $try;
        }

        return null;
    }

    private static function localizarEocd(string $bin): ?int
    {
        $len = strlen($bin);
        $start = max(0, $len - 65557);
        for ($p = $len - 22; $p >= $start; $p--) {
            if ($bin[$p] === 'P' && $bin[$p + 1] === 'K' && $bin[$p + 2] === "\x05" && $bin[$p + 3] === "\x06") {
                return $p;
            }
        }

        return null;
    }
}
