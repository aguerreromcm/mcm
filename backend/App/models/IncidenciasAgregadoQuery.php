<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

/**
 * Agregación de incidencias alineada con Indicadores::GetIncidenciasUsuarios.
 * Usada en el dashboard (totales por usuario / mes). El detalle sigue en IncidenciasDetalleQuery (Q1).
 */
class IncidenciasAgregadoQuery
{
    public static function codigosPeIn(): string
    {
        return IncidenciasDetalleQuery::codigosPeIn();
    }

    /**
     * CTE DATOS: mismas fuentes y reglas de conteo que Productividad Operaciones en Indicadores.
     */
    public static function sqlDatosCte(): string
    {
        return <<<'SQL'
            DATOS AS (
                SELECT
                    PD.CDGPE,
                    TO_CHAR(PD.FREGISTRO, 'YYYY') AS ANO,
                    TO_CHAR(PD.FREGISTRO, 'MM') AS MES,
                    COUNT(PD.CDGPE) AS TOTAL_INCIDENCIAS,
                    'PAGO DÍA' AS TIPO,
                    'ACTUALIZACIÓN' AS REFERENCIA
                FROM
                    PAGOSDIA PD
                WHERE
                    PD.FREGISTRO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                GROUP BY
                    PD.CDGPE,
                    TO_CHAR(PD.FREGISTRO, 'YYYY'),
                    TO_CHAR(PD.FREGISTRO, 'MM')

                UNION

                SELECT
                    m.ACTUALIZARPE AS CDGPE,
                    TO_CHAR(m.FDEPOSITO, 'YYYY') AS ANO,
                    TO_CHAR(m.FDEPOSITO, 'MM') AS MES,
                    COUNT(m.ACTUALIZARPE) AS TOTAL_INCIDENCIAS,
                    m.TIPO,
                    m.REFERENCIA
                FROM
                    MP m
                WHERE
                    m.FDEPOSITO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                    AND m.ACTUALIZARPE IS NOT NULL
                    AND m.TIPO != 'PD'
                GROUP BY
                    m.ACTUALIZARPE,
                    TO_CHAR(m.FDEPOSITO, 'YYYY'),
                    TO_CHAR(m.FDEPOSITO, 'MM'),
                    m.TIPO,
                    m.REFERENCIA

                UNION

                SELECT
                    pgs.CDGPE,
                    TO_CHAR(pgs.FREGISTRO, 'YYYY') AS ANO,
                    TO_CHAR(pgs.FREGISTRO, 'MM') AS MES,
                    COUNT(pgs.CDGPE) AS TOTAL_INCIDENCIAS,
                    'APLICACION GARANTIA' AS TIPO,
                    '' AS REFERENCIA
                FROM
                    PAG_GAR_SIM pgs
                WHERE
                    pgs.FREGISTRO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                    AND pgs.ESTATUS = 'CP'
                GROUP BY
                    pgs.CDGPE,
                    TO_CHAR(pgs.FREGISTRO, 'YYYY'),
                    TO_CHAR(pgs.FREGISTRO, 'MM')

                UNION

                SELECT
                    scc.CDGPE,
                    TO_CHAR(scc.FECHA_TRA_CL, 'YYYY') AS ANO,
                    TO_CHAR(scc.FECHA_TRA_CL, 'MM') AS MES,
                    COUNT(scc.CDGPE) AS TOTAL_INCIDENCIAS,
                    CASE SN.SITUACION
                        WHEN 'S' THEN 'CREDITO PENDIENTE'
                        WHEN 'A' THEN 'CREDITO APROBADO'
                        WHEN 'R' THEN 'CREDITO RECHAZADO'
                        ELSE 'OTRO'
                    END AS TIPO,
                    'CALL CENTER APROBACIONES/RECHAZOS' AS REFERENCIA
                FROM
                    SOL_CALL_CENTER scc
                    JOIN SN ON SN.CDGNS = scc.CDGNS AND SN.CICLO = scc.CICLO AND SN.SOLICITUD = scc.FECHA_SOL
                    JOIN CO ON CO.CODIGO = scc.CDGCO
                    JOIN RG ON RG.CODIGO = CO.CDGRG
                WHERE
                    scc.FECHA_TRA_CL BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                GROUP BY
                    scc.CDGPE,
                    TO_CHAR(scc.FECHA_TRA_CL, 'YYYY'),
                    TO_CHAR(scc.FECHA_TRA_CL, 'MM'),
                    SN.SITUACION
            )
SQL;
    }

    /**
     * Misma lógica que sqlDatosCte, con granularidad diaria para agrupar por día de la semana.
     */
    public static function sqlDatosDiaCte(): string
    {
        return <<<'SQL'
            DATOS AS (
                SELECT
                    PD.CDGPE,
                    TRUNC(PD.FREGISTRO) AS FECHA,
                    COUNT(PD.CDGPE) AS TOTAL_INCIDENCIAS
                FROM
                    PAGOSDIA PD
                WHERE
                    PD.FREGISTRO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                GROUP BY
                    PD.CDGPE,
                    TRUNC(PD.FREGISTRO)

                UNION ALL

                SELECT
                    m.ACTUALIZARPE AS CDGPE,
                    TRUNC(m.FDEPOSITO) AS FECHA,
                    COUNT(m.ACTUALIZARPE) AS TOTAL_INCIDENCIAS
                FROM
                    MP m
                WHERE
                    m.FDEPOSITO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                    AND m.ACTUALIZARPE IS NOT NULL
                    AND m.TIPO != 'PD'
                GROUP BY
                    m.ACTUALIZARPE,
                    TRUNC(m.FDEPOSITO),
                    m.TIPO,
                    m.REFERENCIA

                UNION ALL

                SELECT
                    pgs.CDGPE,
                    TRUNC(pgs.FREGISTRO) AS FECHA,
                    COUNT(pgs.CDGPE) AS TOTAL_INCIDENCIAS
                FROM
                    PAG_GAR_SIM pgs
                WHERE
                    pgs.FREGISTRO BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                    AND pgs.ESTATUS = 'CP'
                GROUP BY
                    pgs.CDGPE,
                    TRUNC(pgs.FREGISTRO)

                UNION ALL

                SELECT
                    scc.CDGPE,
                    TRUNC(scc.FECHA_TRA_CL) AS FECHA,
                    COUNT(scc.CDGPE) AS TOTAL_INCIDENCIAS
                FROM
                    SOL_CALL_CENTER scc
                    JOIN SN ON SN.CDGNS = scc.CDGNS AND SN.CICLO = scc.CICLO AND SN.SOLICITUD = scc.FECHA_SOL
                    JOIN CO ON CO.CODIGO = scc.CDGCO
                    JOIN RG ON RG.CODIGO = CO.CDGRG
                WHERE
                    scc.FECHA_TRA_CL BETWEEN TO_DATE(:fechaI, 'YYYY-MM-DD') AND TO_DATE(:fechaF, 'YYYY-MM-DD')
                GROUP BY
                    scc.CDGPE,
                    TRUNC(scc.FECHA_TRA_CL),
                    SN.SITUACION
            )
SQL;
    }

    public static function sqlDiaSemanaLabel(string $fechaExpr): string
    {
        return "CASE UPPER(TRIM(TO_CHAR({$fechaExpr}, 'fmDAY', 'NLS_DATE_LANGUAGE=SPANISH')))
    WHEN 'LUNES' THEN 'Lun'
    WHEN 'MARTES' THEN 'Mar'
    WHEN 'MIÉRCOLES' THEN 'Mié'
    WHEN 'MIERCOLES' THEN 'Mié'
    WHEN 'JUEVES' THEN 'Jue'
    WHEN 'VIERNES' THEN 'Vie'
    WHEN 'SÁBADO' THEN 'Sáb'
    WHEN 'SABADO' THEN 'Sáb'
    WHEN 'DOMINGO' THEN 'Dom'
    ELSE NULL
END";
    }

    public static function sqlDiaSemanaOrd(string $fechaExpr): string
    {
        return "CASE UPPER(TRIM(TO_CHAR({$fechaExpr}, 'fmDAY', 'NLS_DATE_LANGUAGE=SPANISH')))
    WHEN 'LUNES' THEN 1
    WHEN 'MARTES' THEN 2
    WHEN 'MIÉRCOLES' THEN 3
    WHEN 'MIERCOLES' THEN 3
    WHEN 'JUEVES' THEN 4
    WHEN 'VIERNES' THEN 5
    WHEN 'SÁBADO' THEN 6
    WHEN 'SABADO' THEN 6
    WHEN 'DOMINGO' THEN 7
    ELSE 8
END";
    }

    /** Carga por día de la semana alineada con el KPI agregado de Indicadores. */
    public static function sqlCargaPorDiaSemana(): string
    {
        $peIn = self::codigosPeIn();
        $diaLabel = self::sqlDiaSemanaLabel('D.FECHA');
        $diaOrd = self::sqlDiaSemanaOrd('D.FECHA');

        return 'WITH ' . self::sqlDatosDiaCte() . "
            SELECT
                {$diaLabel} AS DIA,
                {$diaOrd} AS ORD,
                SUM(D.TOTAL_INCIDENCIAS) AS TOTAL
            FROM
                DATOS D
                JOIN PE ON PE.CODIGO = D.CDGPE
            WHERE
                PE.ACTIVO = 'S'
                AND PE.CODIGO IN ({$peIn})
            GROUP BY
                {$diaLabel},
                {$diaOrd}
            ORDER BY
                ORD";
    }

    public static function sqlModuloFromDatos(): string
    {
        return <<<'SQL'
CASE
    WHEN D.REFERENCIA = 'ACTUALIZACIÓN' OR D.TIPO = 'PAGO DÍA' THEN 'pagos'
    WHEN D.TIPO = 'APLICACION GARANTIA' THEN 'gar'
    WHEN D.REFERENCIA = 'CALL CENTER APROBACIONES/RECHAZOS' THEN 'call'
    WHEN D.REFERENCIA IS NOT NULL AND D.REFERENCIA != '' THEN 'ajuste'
    ELSE 'otro'
END
SQL;
    }

    /** FROM para totales por usuario en un periodo (misma cifra que la tabla de Indicadores al filtrar un mes). */
    public static function sqlFromUsuarioPeriodo(string $extraWhere = ''): string
    {
        $peIn = self::codigosPeIn();
        return "FROM (
                WITH " . self::sqlDatosCte() . ",
                TOTAL AS (
                    SELECT
                        D.CDGPE,
                        SUM(D.TOTAL_INCIDENCIAS) AS TOTAL
                    FROM
                        DATOS D
                    GROUP BY
                        D.CDGPE
                )
                SELECT
                    T.CDGPE,
                    T.TOTAL,
                    CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE
                FROM
                    TOTAL T
                    JOIN PE ON PE.CODIGO = T.CDGPE
                WHERE
                    PE.ACTIVO = 'S'
                    AND PE.CODIGO IN ({$peIn})
                    {$extraWhere}
            ) AG";
    }

    /** Expresión de conteo real sobre filas Q1 (cada grupo puede incluir >1 incidencia). */
    public static function exprConteoQ1(): string
    {
        return 'NVL(Q1.TOTAL_INCIDENCIAS, 1)';
    }

    /**
     * Consulta principal de Indicadores → Productividad Operaciones (por usuario y mes).
     * Requiere :fechaI y :fechaF (p. ej. últimos 12 meses).
     */
    public static function sqlGetIncidenciasUsuarios(): string
    {
        $peIn = self::codigosPeIn();
        return 'WITH ' . self::sqlDatosCte() . ',
            TOTAL AS (
                SELECT
                    D.CDGPE,
                    D.ANO,
                    D.MES,
                    SUM(D.TOTAL_INCIDENCIAS) AS TOTAL
                FROM
                    DATOS D
                GROUP BY
                    D.CDGPE,
                    D.ANO,
                    D.MES
            )
            SELECT
                T.CDGPE,
                T.ANO,
                T.MES,
                T.TOTAL AS TOTAL_INCIDENCIAS,
                CONCATENA_NOMBRE(PE.NOMBRE1, PE.NOMBRE2, PE.PRIMAPE, PE.SEGAPE) AS NOMBRE,
                TO_CHAR(TO_DATE(T.MES, \'MM\'), \'Month\', \'NLS_DATE_LANGUAGE=SPANISH\') AS MES_LETRA
            FROM
                TOTAL T
                JOIN PE ON PE.CODIGO = T.CDGPE
            WHERE
                PE.ACTIVO = \'S\'
                AND PE.CODIGO IN (' . $peIn . ')
            ORDER BY
                T.ANO DESC,
                T.MES DESC,
                T.CDGPE';
    }

    /** Tendencia mensual (12 meses) con la misma lógica que Indicadores. */
    public static function sqlTendencia12Meses(): string
    {
        $peIn = self::codigosPeIn();
        return 'WITH ' . self::sqlDatosCte() . ',
            TOTAL AS (
                SELECT
                    D.CDGPE,
                    D.ANO,
                    D.MES,
                    SUM(D.TOTAL_INCIDENCIAS) AS TOTAL
                FROM
                    DATOS D
                GROUP BY
                    D.CDGPE,
                    D.ANO,
                    D.MES
            )
            SELECT
                T.ANO,
                T.MES,
                TO_CHAR(TO_DATE(T.MES, \'MM\'), \'Month\', \'NLS_DATE_LANGUAGE=SPANISH\') AS MES_LETRA,
                SUM(T.TOTAL) AS TOTAL
            FROM
                TOTAL T
                JOIN PE ON PE.CODIGO = T.CDGPE
            WHERE
                PE.ACTIVO = \'S\'
                AND PE.CODIGO IN (' . $peIn . ')
            GROUP BY
                T.ANO,
                T.MES,
                TO_CHAR(TO_DATE(T.MES, \'MM\'), \'Month\', \'NLS_DATE_LANGUAGE=SPANISH\')
            ORDER BY
                T.ANO,
                T.MES';
    }
}
