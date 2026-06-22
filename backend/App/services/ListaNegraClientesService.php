<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

/**
 * Consulta de registros en CL_MARCA por número de cliente o CURP (solo lectura).
 */
class ListaNegraClientesService
{
    private const CDGEM = 'EMPFIN';

    /** @var array<string, string>|null */
    private static $catalogoCausas = null;

    /**
     * @return array{success: bool, mensaje: string, datos?: array|null, error?: string|null}
     */
    public static function consultar(string $cdgcl, string $curp): array
    {
        $cdgcl = trim($cdgcl);
        $curp = trim($curp);

        if ($cdgcl === '' && $curp === '') {
            return Model::Responde(false, 'Ingrese número de cliente o CURP.');
        }

        if ($cdgcl !== '' && !preg_match('/^\d+$/', $cdgcl)) {
            return Model::Responde(false, 'El número de cliente debe ser numérico.');
        }

        $curpNorm = '';
        if ($curp !== '') {
            $curpNorm = ListaNegraEmpleadosService::normalizarCurp($curp);
            if ($curpNorm === '') {
                return Model::Responde(false, 'El CURP ingresado no es válido.');
            }
        }

        $cdgclNorm = self::normalizarCdgcl($cdgcl);
        $db = new Database();

        if ($cdgclNorm !== '' && $curpNorm !== '') {
            $clCurp = $db->queryOne(
                "SELECT UPPER(TRIM(CURP)) AS CURP
                 FROM CL
                 WHERE CODIGO = :cdgcl
                   AND CURP IS NOT NULL
                   AND LENGTH(TRIM(CURP)) > 0
                   AND ROWNUM = 1",
                ['cdgcl' => $cdgclNorm]
            );
            $curpCl = is_array($clCurp) ? trim((string) ($clCurp['CURP'] ?? '')) : '';
            if ($curpCl !== '' && $curpCl !== $curpNorm) {
                return Model::Responde(false, 'El CURP no coincide con el número de cliente indicado.');
            }
        }

        $condiciones = [];
        $params = ['cdgem' => self::CDGEM];

        if ($cdgclNorm !== '' && $curpNorm !== '') {
            $condiciones[] = '(M.CDGCL = :cdgcl AND UPPER(TRIM(M.CURP)) = :curp)';
            $params['cdgcl'] = $cdgclNorm;
            $params['curp'] = $curpNorm;
        } elseif ($cdgclNorm !== '') {
            $condiciones[] = 'M.CDGCL = :cdgcl';
            $params['cdgcl'] = $cdgclNorm;
            $condiciones[] = 'EXISTS (
                SELECT 1
                FROM CL
                WHERE CL.CODIGO = :cdgcl_curp
                  AND CL.CURP IS NOT NULL
                  AND LENGTH(TRIM(CL.CURP)) > 0
                  AND UPPER(TRIM(M.CURP)) = UPPER(TRIM(CL.CURP))
            )';
            $params['cdgcl_curp'] = $cdgclNorm;
        } elseif ($curpNorm !== '') {
            $condiciones[] = 'UPPER(TRIM(M.CURP)) = :curp';
            $params['curp'] = $curpNorm;
        }

        $sql = <<<SQL
            SELECT
                M.CDGEM,
                M.SECUENCIA,
                M.CDGCL,
                TRIM(M.CURP) AS CURP,
                M.TIPOMARCA,
                M.ESTATUS,
                M.MONTOMAX,
                M.ALTAPE,
                TO_CHAR(TRUNC(M.ALTA), 'DD/MM/YYYY') AS ALTA_FMT,
                TO_CHAR(TRUNC(M.BAJA), 'DD/MM/YYYY') AS BAJA_FMT,
                M.BAJAPE,
                TO_CHAR(M.FREGISTRO, 'DD/MM/YYYY HH24:MI:SS') AS FREGISTRO_FMT,
                M.CAUSA,
                M.CAUSABAJA,
                M.CDGCLNS,
                M.CICLO,
                M.CLNS,
                TRIM(
                    COALESCE(
                        NULLIF(TRIM(NVL(GET_NOMBRE_CLIENTE(M.CDGCL), '')), ''),
                        NULLIF((
                            SELECT TRIM(CONCATENA_NOMBRE(CL.NOMBRE1, CL.NOMBRE2, CL.PRIMAPE, CL.SEGAPE))
                            FROM CL
                            WHERE CL.CDGEM = M.CDGEM
                              AND (
                                    (M.CDGCL IS NOT NULL AND LENGTH(TRIM(M.CDGCL)) > 0 AND CL.CODIGO = M.CDGCL)
                                 OR (M.CURP IS NOT NULL AND LENGTH(TRIM(M.CURP)) > 0
                                     AND UPPER(TRIM(CL.CURP)) = UPPER(TRIM(M.CURP)))
                              )
                              AND ROWNUM = 1
                        ), ''),
                        NULLIF((
                            SELECT TRIM(CONCATENA_NOMBRE(SN.NOMBRE1, SN.NOMBRE2, SN.PRIMAPE, SN.SEGAPE))
                            FROM (
                                SELECT SN2.NOMBRE1, SN2.NOMBRE2, SN2.PRIMAPE, SN2.SEGAPE
                                FROM SEGMENTO_NOMBRE SN2
                                WHERE SN2.CDGEM = M.CDGEM
                                  AND (
                                        (M.CDGCL IS NOT NULL AND LENGTH(TRIM(M.CDGCL)) > 0 AND SN2.CDGCL = M.CDGCL)
                                     OR (M.CURP IS NOT NULL AND LENGTH(TRIM(M.CURP)) > 0
                                         AND UPPER(TRIM(SN2.CURP)) = UPPER(TRIM(M.CURP)))
                                  )
                                ORDER BY SN2.FCONSULTA DESC
                            ) SN
                            WHERE ROWNUM = 1
                        ), ''),
                        NULLIF((
                            SELECT TRIM(RCC.PATERNO || ' ' || RCC.MATERNO || ' ' || RCC.NOMBRES)
                            FROM REP_CIRCULO_CRED RCC
                            WHERE RCC.CDGEM = M.CDGEM
                              AND M.CURP IS NOT NULL
                              AND LENGTH(TRIM(M.CURP)) > 0
                              AND UPPER(TRIM(RCC.CURP)) = UPPER(TRIM(M.CURP))
                              AND ROWNUM = 1
                        ), '')
                    )
                ) AS NOMBRE_CLIENTE,
                CASE
                    WHEN M.CDGCLNS IS NULL OR LENGTH(TRIM(M.CDGCLNS)) = 0 THEN NULL
                    WHEN M.CLNS = 'G' THEN (
                        SELECT TRIM(NS.NOMBRE)
                        FROM NS
                        WHERE NS.CDGEM = M.CDGEM
                          AND NS.CODIGO = M.CDGCLNS
                          AND ROWNUM = 1
                    )
                    ELSE NULL
                END AS NOMBRE_CREDITO,
                TRIM(GET_NOMBRE_EMPLEADO(TRIM(M.ALTAPE))) AS NOMBRE_EMPLEADO_ALTA,
                TRIM(GET_NOMBRE_EMPLEADO(TRIM(M.BAJAPE))) AS NOMBRE_EMPLEADO_BAJA
            FROM CL_MARCA M
            WHERE M.CDGEM = :cdgem
              AND (
SQL;
        $sql .= implode("\n                  OR ", $condiciones);
        $sql .= <<<SQL

              )
            ORDER BY
                CASE WHEN M.ESTATUS = 'A' THEN 0 ELSE 1 END,
                M.FREGISTRO DESC NULLS LAST,
                M.ALTA DESC
SQL;

        $res = $db->queryAll($sql, $params);
        if ($res === false) {
            throw new \RuntimeException('Error al consultar CL_MARCA.');
        }

        $datos = is_array($res) ? $res : [];
        if (count($datos) === 0) {
            return Model::Responde(true, 'No se encontraron registros en lista negra para los criterios indicados.', []);
        }

        self::cargarCatalogoCausas($db);
        $datos = array_map([self::class, 'enriquecerRegistro'], $datos);

        return Model::Responde(true, 'OK', $datos);
    }

    private static function cargarCatalogoCausas(Database $db): void
    {
        if (self::$catalogoCausas !== null) {
            return;
        }

        self::$catalogoCausas = [];
        $rows = $db->queryAll(
            "SELECT TRIM(CODIGO) AS CODIGO, TRIM(TIPO) AS TIPO, TRIM(DESCRIPCION) AS DESCRIPCION
             FROM CAT_CAUSA_LISTA_NEGRA
             WHERE CDGEM = :cdgem",
            ['cdgem' => self::CDGEM]
        );

        if (!is_array($rows)) {
            return;
        }

        foreach ($rows as $row) {
            $codigo = trim((string) ($row['CODIGO'] ?? ''));
            $tipo = trim((string) ($row['TIPO'] ?? 'A'));
            $desc = trim((string) ($row['DESCRIPCION'] ?? ''));
            if ($codigo === '' || $desc === '') {
                continue;
            }
            self::$catalogoCausas[self::claveCausa($tipo, $codigo)] = $desc;
        }
    }

    private static function claveCausa(string $tipo, string $codigo): string
    {
        return strtoupper($tipo) . ':' . self::normalizarCodigoNumerico($codigo);
    }

    private static function normalizarCodigoNumerico(string $codigo): string
    {
        $codigo = trim($codigo);
        if ($codigo === '') {
            return '';
        }
        if (ctype_digit($codigo)) {
            return (string) ((int) $codigo);
        }

        return $codigo;
    }

    private static function normalizarCdgcl(string $cdgcl): string
    {
        $cdgcl = trim($cdgcl);
        if ($cdgcl === '') {
            return '';
        }

        return str_pad($cdgcl, 6, '0', STR_PAD_LEFT);
    }

    private static function descripcionCausa(?string $codigo, string $tipo = 'A'): ?string
    {
        if ($codigo === null || trim($codigo) === '') {
            return null;
        }

        $norm = self::normalizarCodigoNumerico($codigo);
        $desc = self::$catalogoCausas[self::claveCausa($tipo, $norm)] ?? null;
        if ($desc !== null) {
            return self::limpiarDescripcionCausa($desc, $tipo);
        }

        $desc = self::$catalogoCausas[self::claveCausa($tipo, trim($codigo))] ?? null;

        return $desc !== null ? self::limpiarDescripcionCausa($desc, $tipo) : null;
    }

    private static function limpiarDescripcionCausa(string $desc, string $tipo): string
    {
        if (strtoupper($tipo) !== 'A') {
            return $desc;
        }

        return trim(preg_replace('/\s*\(Env[ií]a(?: a)? lista negra\)\s*/iu', '', $desc));
    }

    private static function descripcionTipoMarca(?string $tipo): ?string
    {
        $map = [
            'LN' => 'Lista negra',
            'BA' => 'Marca temporal de crédito (baja al procesar solicitud)',
            'EN' => 'Enano (límite de monto máximo)',
            'CA' => 'Castigo de cartera',
            'RE' => 'Reestructura de crédito',
            'OP' => 'Marca operativa (fondeo / cartera)',
            'ZE' => 'Castigo especial (tipo Z)',
        ];

        $t = strtoupper(trim((string) $tipo));

        return $map[$t] ?? null;
    }

    private static function descripcionEstatus(?string $estatus, ?string $tipoMarca): string
    {
        $e = strtoupper(trim((string) $estatus));
        $tipo = strtoupper(trim((string) $tipoMarca));

        if ($e === 'A') {
            if ($tipo === 'LN') {
                return 'Activo — el cliente está en lista negra';
            }

            return 'Activo — la marca está vigente';
        }

        if ($e === 'B') {
            if ($tipo === 'LN') {
                return 'Baja — ya no está en lista negra';
            }

            return 'Baja — la marca fue cancelada';
        }

        return $estatus !== null && trim($estatus) !== '' ? trim($estatus) : 'Sin estatus';
    }

    private static function descripcionClns(?string $clns): ?string
    {
        $c = strtoupper(trim((string) $clns));
        $map = [
            'G' => 'Grupal',
            'I' => 'Individual',
        ];

        if (isset($map[$c])) {
            return $map[$c] . ' (' . $c . ')';
        }

        return $clns !== null && trim($clns) !== '' ? trim($clns) : null;
    }

    private static function descripcionEmpresa(?string $cdgem): ?string
    {
        $e = strtoupper(trim((string) $cdgem));
        $map = [
            'EMPFIN' => 'MCM (Empresa Financiera)',
        ];

        if (isset($map[$e])) {
            return $map[$e];
        }

        return $cdgem !== null && trim($cdgem) !== '' ? trim($cdgem) : null;
    }

    private static function formatearMonto($monto): ?string
    {
        if ($monto === null || $monto === '') {
            return null;
        }

        if (!is_numeric($monto)) {
            return (string) $monto;
        }

        return '$' . number_format((float) $monto, 2, '.', ',');
    }

    private static function formatearUsuario(?string $codigo, ?string $nombre): ?string
    {
        $codigo = $codigo !== null ? trim($codigo) : '';
        if ($codigo === '') {
            return null;
        }

        $nombre = $nombre !== null ? trim($nombre) : '';
        if ($nombre === '' || strcasecmp($nombre, $codigo) === 0) {
            return $codigo;
        }

        return $nombre . ' (' . $codigo . ')';
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private static function enriquecerRegistro(array $row): array
    {
        $tipoMarca = trim((string) ($row['TIPOMARCA'] ?? ''));
        $causa = isset($row['CAUSA']) ? trim((string) $row['CAUSA']) : '';
        $causaBaja = isset($row['CAUSABAJA']) ? trim((string) $row['CAUSABAJA']) : '';

        $descTipo = self::descripcionTipoMarca($tipoMarca);
        $row['TIPOMARCA_FMT'] = $descTipo !== null
            ? $tipoMarca . ' — ' . $descTipo
            : $tipoMarca;

        $row['ESTATUS_FMT'] = self::descripcionEstatus($row['ESTATUS'] ?? null, $tipoMarca);

        $descCausa = self::descripcionCausa($causa, 'A');
        $row['CAUSA_FMT'] = $descCausa !== null
            ? self::normalizarCodigoNumerico($causa) . ' — ' . $descCausa
            : ($causa !== '' ? $causa : null);

        $descCausaBaja = self::descripcionCausa($causaBaja, 'B');
        if ($descCausaBaja === null && $causaBaja !== '') {
            $descCausaBaja = self::descripcionCausa($causaBaja, 'A');
        }
        $row['CAUSABAJA_FMT'] = $descCausaBaja !== null
            ? self::normalizarCodigoNumerico($causaBaja) . ' — ' . $descCausaBaja
            : ($causaBaja !== '' ? $causaBaja : null);

        $row['CLNS_FMT'] = self::descripcionClns($row['CLNS'] ?? null);
        $row['CDGEM_FMT'] = self::descripcionEmpresa($row['CDGEM'] ?? null);
        $row['MONTOMAX_FMT'] = self::formatearMonto($row['MONTOMAX'] ?? null);

        $row['USUARIO_ALTA_FMT'] = self::formatearUsuario(
            isset($row['ALTAPE']) ? (string) $row['ALTAPE'] : null,
            isset($row['NOMBRE_EMPLEADO_ALTA']) ? (string) $row['NOMBRE_EMPLEADO_ALTA'] : null
        );
        $row['USUARIO_BAJA_FMT'] = self::formatearUsuario(
            isset($row['BAJAPE']) ? (string) $row['BAJAPE'] : null,
            isset($row['NOMBRE_EMPLEADO_BAJA']) ? (string) $row['NOMBRE_EMPLEADO_BAJA'] : null
        );
        unset($row['NOMBRE_EMPLEADO_ALTA'], $row['NOMBRE_EMPLEADO_BAJA']);

        return $row;
    }
}
