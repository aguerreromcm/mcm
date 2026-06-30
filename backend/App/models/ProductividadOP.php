<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Model;
use Core\Database;

class ProductividadOP extends Model
{
    private static function validarPeriodo(array $datos): ?array
    {
        $fechaI = trim((string) ($datos['fechaI'] ?? ''));
        $fechaF = trim((string) ($datos['fechaF'] ?? ''));
        if ($fechaI === '' || $fechaF === '') {
            return self::Responde(false, 'Debe indicar fecha inicial y final del periodo');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaI) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaF)) {
            return self::Responde(false, 'Formato de fechas inválido (use YYYY-MM-DD)');
        }
        if ($fechaI > $fechaF) {
            return self::Responde(false, 'La fecha inicial no puede ser posterior a la final');
        }
        return null;
    }

    /** @return array|false */
    private static function queryOne(Database $db, string $sql, array $prm, string $contexto)
    {
        $res = $db->queryOne($sql, $prm);
        if ($res === false) {
            throw new \RuntimeException("Error al consultar {$contexto}");
        }
        return $res;
    }

    /** @return array */
    private static function queryAll(Database $db, string $sql, array $prm, string $contexto)
    {
        $res = $db->queryAll($sql, $prm);
        if ($res === false) {
            throw new \RuntimeException("Error al consultar {$contexto}");
        }
        return $res;
    }

    private static function normalizaFilas(array $filas): array
    {
        return array_map(static function ($fila) {
            if (!is_array($fila)) {
                return $fila;
            }
            foreach ($fila as $k => $v) {
                if ($v === null || is_bool($v) || is_int($v) || is_float($v) || is_string($v)) {
                    continue;
                }
                $fila[$k] = is_object($v) && method_exists($v, 'format')
                    ? $v->format('Y-m-d H:i:s')
                    : (string) $v;
            }
            return $fila;
        }, $filas);
    }

    private static function exprCnt(): string
    {
        return IncidenciasAgregadoQuery::exprConteoQ1();
    }

    /** Sin filtros de detalle → misma agregación que Indicadores (gráfica/tabla mensual). */
    private static function usaAgregadoIndicadores(array $datos): bool
    {
        return trim($datos['region'] ?? '') === ''
            && empty($datos['usuario'])
            && empty($datos['sucursal'])
            && empty($datos['tipo'])
            && (empty($datos['modulo']) || $datos['modulo'] === 'all')
            && empty($datos['search']);
    }

    private static function prmTendencia12Meses(): array
    {
        $ini = new \DateTime('first day of this month');
        $ini->modify('-12 months');
        $fin = new \DateTime('last day of this month');
        return ['fechaI' => $ini->format('Y-m-d'), 'fechaF' => $fin->format('Y-m-d')];
    }

    private static function kpisIncidencias(Database $db, array $datos, array $prm, string $base, bool $agregado, string $ctx): array
    {
        $cnt = self::exprCnt();
        if ($agregado) {
            $inc = self::queryOne($db, 'SELECT NVL(SUM(AG.TOTAL), 0) AS TOTAL, COUNT(*) AS USUARIOS_ACTIVOS '
                . IncidenciasAgregadoQuery::sqlFromUsuarioPeriodo(), $prm, $ctx . ' (incidencias)');
            $extra = self::queryOne($db, "SELECT NVL(SUM(Q1.MONTO), 0) AS MONTO, COUNT(DISTINCT Q1.SUCURSAL) AS SUCURSALES {$base}", $prm, $ctx . ' (montos)');
            return array_merge($inc ?: [], $extra ?: []);
        }
        return self::queryOne($db, "SELECT
                NVL(SUM({$cnt}), 0) AS TOTAL,
                NVL(SUM(Q1.MONTO), 0) AS MONTO,
                COUNT(DISTINCT Q1.CDGPE) AS USUARIOS_ACTIVOS,
                COUNT(DISTINCT Q1.SUCURSAL) AS SUCURSALES
            {$base}", $prm, $ctx);
    }

    private static function sqlRankingUsuarios(bool $agregado, string $base, string $limitSql): string
    {
        $cnt = self::exprCnt();
        if ($agregado) {
            return 'SELECT AG.CDGPE, AG.NOMBRE, AG.TOTAL, NVL(M.MONTO, 0) AS MONTO '
                . IncidenciasAgregadoQuery::sqlFromUsuarioPeriodo() . '
                LEFT JOIN (
                    SELECT Q1.CDGPE, SUM(Q1.MONTO) AS MONTO ' . $base . ' GROUP BY Q1.CDGPE
                ) M ON M.CDGPE = AG.CDGPE
                ORDER BY AG.TOTAL DESC ' . $limitSql;
        }
        return "SELECT
                Q1.CDGPE,
                CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE,
                SUM({$cnt}) AS TOTAL,
                NVL(SUM(Q1.MONTO), 0) AS MONTO
            {$base}
            GROUP BY Q1.CDGPE, PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE
            ORDER BY SUM({$cnt}) DESC
            {$limitSql}";
    }

    private static function baseFrom(string $extraWhere = ''): string
    {
        $q1 = IncidenciasDetalleQuery::sqlQ1();
        $peIn = IncidenciasDetalleQuery::codigosPeIn();
        return "FROM ({$q1}) Q1
            INNER JOIN PE ON Q1.CDGPE = PE.CODIGO
            WHERE PE.ACTIVO = 'S'
              AND PE.CODIGO IN ({$peIn})
              AND Q1.FECHA BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
              {$extraWhere}";
    }

    /** Día de la semana en español (Lun–Dom), independiente del NLS de la sesión. */
    private static function sqlDiaSemanaLabel(): string
    {
        return IncidenciasAgregadoQuery::sqlDiaSemanaLabel('Q1.FECHA');
    }

    private static function sqlDiaSemanaOrd(): string
    {
        return IncidenciasAgregadoQuery::sqlDiaSemanaOrd('Q1.FECHA');
    }

    private static function filtroRegion(array $datos, array &$prm): string
    {
        $region = trim($datos['region'] ?? '');
        if ($region === '') {
            return '';
        }
        $prm['region'] = $region;
        return ' AND Q1.REGION = :region';
    }

    /** Todas las regiones con sucursal (RG), sin depender del periodo ni del filtro activo. */
    private static function catalogoRegiones(Database $db): array
    {
        $rows = self::queryAll($db, "
            SELECT DISTINCT RG.NOMBRE AS REGION
            FROM RG
            INNER JOIN CO ON CO.CDGRG = RG.CODIGO
            WHERE RG.NOMBRE IS NOT NULL
            ORDER BY RG.NOMBRE", [], 'catálogo regiones');
        return array_column($rows, 'REGION');
    }

    private static function filtrosConsulta(array $datos, array &$prm): string
    {
        $parts = [self::filtroRegion($datos, $prm)];

        if (!empty($datos['usuario'])) {
            $parts[] = ' AND Q1.CDGPE = :usuario';
            $prm['usuario'] = $datos['usuario'];
        }
        if (!empty($datos['sucursal'])) {
            $parts[] = ' AND Q1.SUCURSAL = :sucursal';
            $prm['sucursal'] = $datos['sucursal'];
        }
        if (!empty($datos['tipo'])) {
            $parts[] = ' AND Q1.TIPO = :tipo';
            $prm['tipo'] = $datos['tipo'];
        }
        if (!empty($datos['modulo']) && $datos['modulo'] !== 'all') {
            $mod = IncidenciasDetalleQuery::sqlModuloOrigen();
            $parts[] = " AND ({$mod}) = :modulo";
            $prm['modulo'] = $datos['modulo'];
        }
        if (!empty($datos['search'])) {
            $parts[] = " AND (
                TO_CHAR(Q1.CDGNS) LIKE :search
                OR UPPER(Q1.CDGPE) LIKE :search
                OR UPPER(Q1.TIPO) LIKE :search
                OR UPPER(Q1.SUCURSAL) LIKE :search
                OR UPPER(Q1.REGION) LIKE :search
                OR UPPER(Q1.REFERENCIA) LIKE :search
            )";
            $prm['search'] = '%' . strtoupper(trim($datos['search'])) . '%';
        }

        return implode('', $parts);
    }

    private static function fechasPeriodoAnterior(string $fechaI, string $fechaF): array
    {
        $ini = new \DateTime($fechaI);
        $fin = new \DateTime($fechaF);
        $dias = (int) $ini->diff($fin)->days + 1;
        $finAnt = (clone $ini)->modify('-1 day');
        $iniAnt = (clone $finAnt)->modify('-' . ($dias - 1) . ' days');
        return [$iniAnt->format('Y-m-d'), $finAnt->format('Y-m-d')];
    }

    public static function GetResumen(array $datos)
    {
        if ($err = self::validarPeriodo($datos)) {
            return $err;
        }

        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF'],
        ];
        $extra = self::filtrosConsulta($datos, $prm);
        $base = self::baseFrom($extra);
        $modSql = IncidenciasDetalleQuery::sqlModuloOrigen();
        $cnt = self::exprCnt();
        $agregado = self::usaAgregadoIndicadores($datos);

        try {
            $db = new Database();

            $kpis = self::kpisIncidencias($db, $datos, $prm, $base, $agregado, 'KPIs del periodo');

            [$fechaIAnt, $fechaFAnt] = self::fechasPeriodoAnterior($datos['fechaI'], $datos['fechaF']);
            $prmAnt = array_merge($prm, ['fechaI' => $fechaIAnt, 'fechaF' => $fechaFAnt]);
            $kpisAnt = self::kpisIncidencias($db, $datos, $prmAnt, $base, $agregado, 'KPIs periodo anterior');
            $prm['fechaI'] = $datos['fechaI'];
            $prm['fechaF'] = $datos['fechaF'];

            $dias = max(1, (int) ((strtotime($datos['fechaF']) - strtotime($datos['fechaI'])) / 86400) + 1);

            $tendencia = self::queryAll(
                $db,
                IncidenciasAgregadoQuery::sqlTendencia12Meses(),
                self::prmTendencia12Meses(),
                'tendencia mensual'
            );

            $modulos = self::queryAll($db, "SELECT
                    {$modSql} AS MODULO,
                    SUM({$cnt}) AS TOTAL
                {$base}
                GROUP BY {$modSql}
                ORDER BY SUM({$cnt}) DESC", $prm, 'distribución por módulo');

            $diaLabel = self::sqlDiaSemanaLabel();
            $diaOrd = self::sqlDiaSemanaOrd();
            if ($agregado) {
                $semana = self::queryAll(
                    $db,
                    IncidenciasAgregadoQuery::sqlCargaPorDiaSemana(),
                    $prm,
                    'carga por día de semana'
                );
            } else {
                $semana = self::queryAll($db, "SELECT
                        {$diaLabel} AS DIA,
                        {$diaOrd} AS ORD,
                        SUM({$cnt}) AS TOTAL
                    {$base}
                    GROUP BY {$diaLabel}, {$diaOrd}
                    ORDER BY ORD", $prm, 'carga por día de semana');
            }

            $tipos = self::queryAll($db, "SELECT Q1.TIPO, SUM({$cnt}) AS TOTAL
                {$base}
                GROUP BY Q1.TIPO
                ORDER BY SUM({$cnt}) DESC
                FETCH FIRST 5 ROWS ONLY", $prm, 'top tipos');

            $topRegiones = self::queryAll($db, "SELECT
                    Q1.REGION,
                    SUM({$cnt}) AS TOTAL,
                    COUNT(DISTINCT Q1.SUCURSAL) AS SUCURSALES
                {$base}
                GROUP BY Q1.REGION
                ORDER BY SUM({$cnt}) DESC
                FETCH FIRST 5 ROWS ONLY", $prm, 'top regiones');

            $topSucursales = self::queryAll($db, "SELECT
                    Q1.SUCURSAL,
                    Q1.REGION,
                    SUM({$cnt}) AS TOTAL
                {$base}
                GROUP BY Q1.SUCURSAL, Q1.REGION
                ORDER BY SUM({$cnt}) DESC
                FETCH FIRST 5 ROWS ONLY", $prm, 'top sucursales');

            if ($agregado) {
                $tablaUsuarios = self::queryAll($db, 'SELECT * FROM (
                        SELECT
                            AG.CDGPE,
                            AG.NOMBRE,
                            AG.TOTAL,
                            NVL(M.MONTO, 0) AS MONTO,
                            ROW_NUMBER() OVER (ORDER BY AG.TOTAL DESC) AS RN
                        ' . IncidenciasAgregadoQuery::sqlFromUsuarioPeriodo() . '
                        LEFT JOIN (
                            SELECT Q1.CDGPE, SUM(Q1.MONTO) AS MONTO ' . $base . ' GROUP BY Q1.CDGPE
                        ) M ON M.CDGPE = AG.CDGPE
                    ) WHERE RN <= 50', $prm, 'tabla usuarios');
            } else {
                $tablaUsuarios = self::queryAll($db, "SELECT * FROM (
                        SELECT
                            Q1.CDGPE,
                            CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE,
                            SUM({$cnt}) AS TOTAL,
                            NVL(SUM(Q1.MONTO), 0) AS MONTO,
                            ROW_NUMBER() OVER (ORDER BY SUM({$cnt}) DESC) AS RN
                        {$base}
                        GROUP BY Q1.CDGPE, PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE
                    ) WHERE RN <= 50", $prm, 'tabla usuarios');
            }

            $modulosPorUsuario = self::queryAll($db, "SELECT CDGPE, MODULO, CNT FROM (
                    SELECT Q1.CDGPE, {$modSql} AS MODULO, SUM({$cnt}) AS CNT,
                        ROW_NUMBER() OVER (PARTITION BY Q1.CDGPE ORDER BY SUM({$cnt}) DESC) AS RN
                    {$base}
                    GROUP BY Q1.CDGPE, {$modSql}
                ) WHERE RN = 1", $prm, 'módulo principal por usuario');
            $modMap = [];
            foreach ($modulosPorUsuario as $m) {
                $modMap[$m['CDGPE']] = [
                    'MODULO' => $m['MODULO'],
                    'CNT' => (int) ($m['CNT'] ?? 0),
                ];
            }
            foreach ($tablaUsuarios as &$u) {
                $info = $modMap[$u['CDGPE']] ?? ['MODULO' => 'otro', 'CNT' => 0];
                $totalUsuario = (int) ($u['TOTAL'] ?? 0);
                $u['MODULO'] = $info['MODULO'];
                $u['MODULO_PCT'] = $totalUsuario > 0
                    ? round(($info['CNT'] / $totalUsuario) * 100, 1)
                    : 0;
            }
            unset($u);

            $destUsuario = $tablaUsuarios[0] ?? null;
            $destSucursal = $topSucursales[0] ?? null;
            $destTipo = $tipos[0] ?? null;
            $mayorMonto = self::queryOne($db, "SELECT
                    TO_CHAR(Q1.FECHA, 'DD/MM/YY') AS FECHA,
                    Q1.CDGNS,
                    Q1.CICLO,
                    Q1.MONTO,
                    Q1.TIPO,
                    Q1.CDGPE,
                    CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE
                {$base}
                ORDER BY Q1.MONTO DESC
                FETCH FIRST 1 ROWS ONLY", $prm, 'mayor monto');

            $regionTop = self::queryOne($db, "SELECT Q1.REGION, SUM({$cnt}) AS TOTAL
                {$base}
                GROUP BY Q1.REGION
                ORDER BY SUM({$cnt}) DESC
                FETCH FIRST 1 ROWS ONLY", $prm, 'región principal');

            $totalPe = count(explode(',', str_replace("'", '', IncidenciasDetalleQuery::codigosPeIn())));

            $totalActual = (int) ($kpis['TOTAL'] ?? 0);
            $totalAnt = (int) ($kpisAnt['TOTAL'] ?? 0);
            $pctCambio = $totalAnt > 0 ? round((($totalActual - $totalAnt) / $totalAnt) * 100, 1) : 0;

            $montoActual = (float) ($kpis['MONTO'] ?? 0);
            $montoAnt = (float) ($kpisAnt['MONTO'] ?? 0);
            $pctMonto = $montoAnt > 0 ? round((($montoActual - $montoAnt) / $montoAnt) * 100, 1) : 0;

            $modPrincipal = $modulos[0] ?? null;
            $pctModulo = ($modPrincipal && $totalActual > 0)
                ? round(((int) $modPrincipal['TOTAL'] / $totalActual) * 100)
                : 0;

            return self::Responde(true, 'Resumen obtenido', [
                'kpis' => [
                    'total' => $totalActual,
                    'monto' => $montoActual,
                    'promedio_diario' => round($totalActual / $dias, 1),
                    'usuarios_activos' => (int) ($kpis['USUARIOS_ACTIVOS'] ?? 0),
                    'total_usuarios' => $totalPe,
                    'sucursales' => (int) ($kpis['SUCURSALES'] ?? 0),
                    'pct_total' => $pctCambio,
                    'pct_monto' => $pctMonto,
                    'dias' => $dias,
                ],
                'insight' => [
                    'region_top' => $regionTop['REGION'] ?? '',
                    'pct_modulo' => $pctModulo,
                    'modulo_label' => self::labelModulo($modPrincipal['MODULO'] ?? ''),
                ],
                'destacados' => [
                    'usuario' => $destUsuario,
                    'sucursal' => $destSucursal,
                    'tipo' => $destTipo,
                    'mayor_monto' => $mayorMonto,
                ],
                'tendencia' => $tendencia,
                'modulos' => $modulos,
                'semana' => $semana,
                'tipos' => $tipos,
                'top_regiones' => $topRegiones,
                'top_sucursales' => $topSucursales,
                'tabla_usuarios' => $tablaUsuarios,
            ]);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener resumen de productividad', null, $e->getMessage());
        }
    }

    public static function GetConsulta(array $datos)
    {
        if ($err = self::validarPeriodo($datos)) {
            return $err;
        }

        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF'],
        ];
        $extra = self::filtrosConsulta($datos, $prm);
        $modSql = IncidenciasDetalleQuery::sqlModuloOrigen();
        $q1 = IncidenciasDetalleQuery::sqlQ1();
        $peIn = IncidenciasDetalleQuery::codigosPeIn();
        $cnt = self::exprCnt();

        $where = "PE.ACTIVO = 'S' AND PE.CODIGO IN ({$peIn})
            AND Q1.FECHA BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD') {$extra}";

        try {
            $db = new Database();

            $totales = self::queryOne($db, "SELECT
                    NVL(SUM({$cnt}), 0) AS TOTAL_INCIDENCIAS,
                    COUNT(*) AS TOTAL_FILAS,
                    NVL(SUM(Q1.MONTO), 0) AS MONTO
                FROM ({$q1}) Q1
                INNER JOIN PE ON Q1.CDGPE = PE.CODIGO
                WHERE {$where}", $prm, 'totales de consulta detallada');

            $filas = self::queryAll($db, "SELECT
                    TO_CHAR(Q1.FECHA, 'DD/MM/YY HH24:MI') AS FECHA,
                    Q1.CDGNS,
                    Q1.CICLO,
                    Q1.MONTO,
                    Q1.TIPO,
                    Q1.REFERENCIA AS DESCRIPCION,
                    Q1.REGION,
                    Q1.SUCURSAL,
                    Q1.CDGPE,
                    CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE,
                    {$modSql} AS MODULO
                FROM ({$q1}) Q1
                INNER JOIN PE ON Q1.CDGPE = PE.CODIGO
                WHERE {$where}
                ORDER BY Q1.FECHA DESC", $prm, 'consulta detallada');

            $filas = self::normalizaFilas($filas);
            $totalFilas = count($filas);

            return self::Responde(true, 'Consulta obtenida', [
                'filas' => $filas,
                'total' => $totalFilas,
                'total_filas' => $totalFilas,
                'monto' => (float) ($totales['MONTO'] ?? 0),
            ]);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error en consulta detallada', null, $e->getMessage());
        }
    }

    /** Lista de usuarios para filtros; misma fuente que el KPI «usuarios activos». */
    private static function catalogoUsuarios(Database $db, array $datos): array
    {
        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF'],
        ];

        if (self::usaAgregadoIndicadores($datos)) {
            return self::queryAll($db, 'SELECT AG.CDGPE, AG.NOMBRE '
                . IncidenciasAgregadoQuery::sqlFromUsuarioPeriodo()
                . ' ORDER BY AG.NOMBRE', $prm, 'catálogo usuarios');
        }

        $extra = self::filtrosConsulta($datos, $prm);
        $base = self::baseFrom($extra);

        return self::queryAll($db, "SELECT DISTINCT Q1.CDGPE,
                CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE
            {$base}
            ORDER BY NOMBRE", $prm, 'catálogo usuarios');
    }

    public static function GetCatalogos(array $datos)
    {
        if ($err = self::validarPeriodo($datos)) {
            return $err;
        }

        $prm = [
            'fechaI' => $datos['fechaI'],
            'fechaF' => $datos['fechaF'],
        ];
        $prmFiltrado = $prm;
        $extra = self::filtroRegion($datos, $prmFiltrado);
        $baseFiltrado = self::baseFrom($extra);

        try {
            $db = new Database();
            $usuarios = self::catalogoUsuarios($db, $datos);
            $regiones = self::catalogoRegiones($db);
            $sucursales = self::queryAll($db, "SELECT DISTINCT Q1.SUCURSAL {$baseFiltrado} ORDER BY Q1.SUCURSAL", $prmFiltrado, 'catálogo sucursales');
            $tipos = self::queryAll($db, "SELECT Q1.TIPO, SUM(" . self::exprCnt() . ") AS TOTAL {$baseFiltrado} GROUP BY Q1.TIPO ORDER BY SUM(" . self::exprCnt() . ") DESC FETCH FIRST 30 ROWS ONLY", $prmFiltrado, 'catálogo tipos');

            return self::Responde(true, 'Catálogos obtenidos', [
                'usuarios' => $usuarios,
                'regiones' => $regiones,
                'sucursales' => array_column($sucursales, 'SUCURSAL'),
                'tipos' => array_column($tipos, 'TIPO'),
            ]);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener catálogos', null, $e->getMessage());
        }
    }

    private static function labelModulo(string $mod): string
    {
        return self::labelModuloTabla($mod);
    }

    public static function labelModuloTabla(string $mod): string
    {
        $labels = [
            'pagos' => 'PAGOS DÍA',
            'ajuste' => 'AJUSTE',
            'gar' => 'GARANTÍAS',
            'call' => 'CALL CENTER',
        ];
        return $labels[strtolower($mod)] ?? 'OTROS';
    }

    public static function fechaConsultaParaExcel(?string $fecha): ?string
    {
        $raw = trim((string) $fecha);
        if ($raw === '') {
            return null;
        }
        $dt = \DateTime::createFromFormat('d/m/y H:i', $raw)
            ?: \DateTime::createFromFormat('d/m/y', explode(' ', $raw)[0] ?? '');
        return $dt ? $dt->format('d/m/Y H:i:s') : $raw;
    }
}
