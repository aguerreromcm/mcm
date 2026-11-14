<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\Database;

/**
 * Repository: consultas de pagos del día para el resumen de cierre (modal y correo).
 * Usa la misma consulta exacta que Layout Contable (Pagos → Layout Contable).
 * No contiene lógica de negocio; solo acceso a datos.
 */
class PagosAplicacionRepository
{
    /**
     * Normaliza fecha enviada desde input date (Y-m-d) o texto d/m/Y, d-m-Y.
     */
    public static function coerceFechaYmD($fecha): ?string
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return null;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return $fecha;
        }
        if (preg_match('#^(\d{1,2})[/-](\d{1,2})[/-](\d{4})$#', $fecha, $m)) {
            $d = (int) $m[1];
            $mo = (int) $m[2];
            $y = (int) $m[3];
            if (!checkdate($mo, $d, $y)) {
                return null;
            }

            return sprintf('%04d-%02d-%02d', $y, $mo, $d);
        }

        return null;
    }

    /**
     * Pago ya procesado por importación: VB6 usa F_IMPORTACION; SP_PAGOS_CIERRE_DEVENGO usa ID_IMPORTACION.
     */
    public static function filaMarcadaImportada(array $f): bool
    {
        $fi = $f['F_IMPORTACION'] ?? $f['f_importacion'] ?? null;
        if ($fi !== null && trim((string) $fi) !== '') {
            return true;
        }
        $idImp = $f['ID_IMPORTACION'] ?? $f['id_importacion'] ?? null;
        if ($idImp === null || $idImp === '') {
            return false;
        }
        if (is_numeric($idImp)) {
            return ((float) $idImp) > 0;
        }

        return trim((string) $idImp) !== '';
    }

    /**
     * SELECT layout sin depender de columnas opcionales (p. ej. si F_IMPORTACION no existe en BD).
     */
    private function ejecutarSelectLayout(Database $db, string $sql): ?array
    {
        if ($db->db_activa === null) {
            return null;
        }
        try {
            $stmt = $db->db_activa->query($sql);
            if ($stmt === false) {
                return null;
            }
            $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return is_array($filas) ? $filas : [];
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * Obtiene los datos con la misma consulta exacta que Layout Contable (Pagos).
     * Consulta copiada de App\models\Pagos::GeneraLayoutContable.
     *
     * @param string $fecha Fecha en Y-m-d
     * @return array Lista de filas con FECHA, REFERENCIA, MONTO, MONEDA
     */
    public function getDatosLayoutPorFecha($fecha)
    {
        $fechaNorm = self::coerceFechaYmD($fecha);
        if ($fechaNorm === null) {
            return [];
        }
        $f1 = $fechaNorm;

        $db = new Database();
        if ($db->db_activa === null) {
            return [];
        }

        // Misma lógica que Layout Contable / SQL Developer: día completo con BETWEEN.
        // Sin usar Database::queryAll aquí: si falla el SQL (p. ej. columna inexistente),
        // queryAll escribe en salida y rompe JSON; además devolvía [] igual que “sin filas”.
        $condFecha = "PGD.FECHA BETWEEN TO_DATE('$f1 00:00:00', 'YYYY-MM-DD HH24:MI:SS') "
            . "AND TO_DATE('$f1 23:59:59', 'YYYY-MM-DD HH24:MI:SS')";

        $selectBase = <<<SQL
		PGD.CDGEM,
		PGD.CDGNS,
		PGD.CICLO,
		PGD.SECUENCIA,
		PGD.TIPO,
		PGD.FECHA,
		CASE
			WHEN (PGD.TIPO = 'P' OR PGD.TIPO = 'X') THEN 'P' || PRN.CDGNS || PRN.CDGTPC || FN_DV('P' || PRN.CDGNS || PRN.CDGTPC)
			WHEN PGD.TIPO = 'G' THEN '0' || PRN.CDGNS || PRN.CDGTPC || FN_DV('0' || PRN.CDGNS || PRN.CDGTPC)
			ELSE 'NO IDENTIFICADO'
		END REFERENCIA,
		PGD.MONTO,
		'MN' MONEDA
SQL;

        $queryConFi = <<<sql
	SELECT
		{$selectBase},
		PGD.F_IMPORTACION,
		PGD.ID_IMPORTACION
	FROM
		PAGOSDIA PGD, PRN
	WHERE
		PGD.CDGEM = PRN.CDGEM
		AND PGD.CDGNS = PRN.CDGNS
		AND PGD.CICLO = PRN.CICLO
		AND PGD.CDGEM = 'EMPFIN'
		AND PGD.ESTATUS = 'A'
		AND PGD.TIPO IN('P','G', 'X')
		AND PGD.MONTO != 0
		AND {$condFecha}
	ORDER BY
		PGD.FECHA
sql;

        $querySinFi = <<<sql
	SELECT
		{$selectBase},
		PGD.ID_IMPORTACION
	FROM
		PAGOSDIA PGD, PRN
	WHERE
		PGD.CDGEM = PRN.CDGEM
		AND PGD.CDGNS = PRN.CDGNS
		AND PGD.CICLO = PRN.CICLO
		AND PGD.CDGEM = 'EMPFIN'
		AND PGD.ESTATUS = 'A'
		AND PGD.TIPO IN('P','G', 'X')
		AND PGD.MONTO != 0
		AND {$condFecha}
	ORDER BY
		PGD.FECHA
sql;

        $querySoloLayout = <<<sql
	SELECT
		{$selectBase}
	FROM
		PAGOSDIA PGD, PRN
	WHERE
		PGD.CDGEM = PRN.CDGEM
		AND PGD.CDGNS = PRN.CDGNS
		AND PGD.CICLO = PRN.CICLO
		AND PGD.CDGEM = 'EMPFIN'
		AND PGD.ESTATUS = 'A'
		AND PGD.TIPO IN('P','G', 'X')
		AND PGD.MONTO != 0
		AND {$condFecha}
	ORDER BY
		PGD.FECHA
sql;

        $filas = $this->ejecutarSelectLayout($db, $queryConFi);
        if ($filas === null) {
            $filas = $this->ejecutarSelectLayout($db, $querySinFi);
            if ($filas === null) {
                $filas = $this->ejecutarSelectLayout($db, $querySoloLayout);
                if ($filas === null) {
                    return [];
                }
                foreach ($filas as $i => $_) {
                    $filas[$i]['F_IMPORTACION'] = null;
                    $filas[$i]['ID_IMPORTACION'] = null;
                }
            } else {
                foreach ($filas as $i => $_) {
                    $filas[$i]['F_IMPORTACION'] = null;
                }
            }
        }

        foreach ($filas as $i => $row) {
            $row = array_change_key_case((array) $row, CASE_UPPER);
            if (isset($row['FECHA'])) {
                $row['FECHA'] = $this->fechaOracleAString($row['FECHA']);
            }
            if (isset($row['F_IMPORTACION']) && $row['F_IMPORTACION'] !== null && $row['F_IMPORTACION'] !== '') {
                $row['F_IMPORTACION'] = $this->fechaOracleAString($row['F_IMPORTACION']);
            } else {
                $row['F_IMPORTACION'] = null;
            }
            if (isset($row['ID_IMPORTACION']) && $row['ID_IMPORTACION'] !== null && $row['ID_IMPORTACION'] !== '') {
                $row['ID_IMPORTACION'] = (int) (float) $row['ID_IMPORTACION'];
            } else {
                $row['ID_IMPORTACION'] = null;
            }
            if (isset($row['MONTO'])) {
                $row['MONTO'] = (float) $row['MONTO'];
            }
            if (isset($row['TIPO'])) {
                $row['TIPO'] = strtoupper(trim((string) $row['TIPO']));
            } else {
                $row['TIPO'] = '';
            }
            if (!isset($row['MONEDA']) || $row['MONEDA'] === null) {
                $row['MONEDA'] = 'MN';
            }
            $filas[$i] = $row;
        }

        return $filas;
    }

    /**
     * Totales por ESTATUS en IMPORTACIONPAGDET (1=Pagos, 2=Garantías, 3=Incidencias).
     * Misma lógica que el resumen operativo: fecha del pago en cabecera (TRUNC(P.FEC_PAGO)), no FEC_CARGA.
     */
    public function getResumenPorEstatusImportacion($fecha)
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return self::resumenPorEstatusVacio();
        }
        $db = new Database();
        if ($db->db_activa === null) {
            return self::resumenPorEstatusVacio();
        }

        $sql = <<<'sql'
SELECT D.ESTATUS AS ESTATUS,
       SUM(D.NO_REGISTROS) AS SUMA_REG,
       SUM(D.MONTO) AS SUMA_MONTO
FROM IMPORTACIONPAGDET D
INNER JOIN IMPORTACIONPAG P ON D.ID_IMPORTACION = P.ID_IMPORTACION
WHERE TRUNC(P.FEC_PAGO) = TO_DATE(:f1, 'YYYY-MM-DD')
GROUP BY D.ESTATUS
ORDER BY D.ESTATUS
sql;

        try {
            $stmt = $db->db_activa->prepare($sql);
            $stmt->execute(['f1' => $fecha]);
            $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $this->acumularGrupoImportacion(is_array($filas) ? $filas : []);
        } catch (\Throwable $e) {
            return self::resumenPorEstatusVacio();
        }
    }

    /** @return array{registrosPagos: int, registrosGarantias: int, registrosIncidencias: int, importePagos: float, importeGarantias: float, importeIncidencias: float} */
    private static function resumenPorEstatusVacio(): array
    {
        return [
            'registrosPagos' => 0,
            'registrosGarantias' => 0,
            'registrosIncidencias' => 0,
            'importePagos' => 0.0,
            'importeGarantias' => 0.0,
            'importeIncidencias' => 0.0,
        ];
    }

    private function fechaOracleAString($valor): string
    {
        if (is_object($valor) && method_exists($valor, 'format')) {
            return $valor->format('Y/m/d H:i:s');
        }
        $str = trim(is_object($valor) ? (string) $valor : (string) $valor);
        $ts = strtotime($str);

        return $ts !== false ? date('Y/m/d H:i:s', $ts) : $str;
    }

    private function acumularGrupoImportacion(array $filas): array
    {
        $out = self::resumenPorEstatusVacio();
        foreach ($filas as $row) {
            if (!is_array($row)) {
                continue;
            }
            $estRaw = $row['ESTATUS'] ?? $row['estatus'] ?? 0;
            $est = (int) round((float) (is_string($estRaw) ? trim($estRaw) : $estRaw));
            $cnt = (int) round((float) ($row['SUMA_REG'] ?? $row['suma_reg'] ?? 0));
            $monto = (float) ($row['SUMA_MONTO'] ?? $row['suma_monto'] ?? 0);
            if ($est === 1) {
                $out['registrosPagos'] += $cnt;
                $out['importePagos'] += $monto;
            } elseif ($est === 2) {
                $out['registrosGarantias'] += $cnt;
                $out['importeGarantias'] += $monto;
            } elseif ($est === 3) {
                $out['registrosIncidencias'] += $cnt;
                $out['importeIncidencias'] += $monto;
            }
        }
        $out['importePagos'] = round($out['importePagos'], 2);
        $out['importeGarantias'] = round($out['importeGarantias'], 2);
        $out['importeIncidencias'] = round($out['importeIncidencias'], 2);

        return $out;
    }
}
