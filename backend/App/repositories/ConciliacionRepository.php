<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\Database;

/**
 * Repository de conciliación de pagos (solo consulta).
 * Resumen de pagos importados por estado de conciliación (tabla MP) para el resumen de cierre.
 */
class ConciliacionRepository
{
    /**
     * Convierte una fecha de entrada a Y-m-d para usarla en binds TO_DATE.
     *
     * @param string $fecha
     * @return string
     */
    private function normalizarFechaYmd($fecha)
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return '';
        }
        $timestamp = strtotime($fecha);
        if ($timestamp === false) {
            return $fecha;
        }
        return date('Y-m-d', $timestamp);
    }

    /**
     * Resumen por CONCILIADO: C=pendiente, D=conciliado, N=ignorado.
     *
     * @return array{totalNoConciliados: int, importeNoConciliados: float, totalConciliados: int, importeConciliados: float}
     */
    public function getResumenConciliacionImportados($fecha)
    {
        $fecha = $this->normalizarFechaYmd($fecha);
        if ($fecha === '') {
            return [
                'totalNoConciliados' => 0,
                'importeNoConciliados' => 0.0,
                'totalConciliados' => 0,
                'importeConciliados' => 0.0,
            ];
        }

        $db = new Database();
        if ($db->db_activa === null) {
            return [
                'totalNoConciliados' => 0,
                'importeNoConciliados' => 0.0,
                'totalConciliados' => 0,
                'importeConciliados' => 0.0,
            ];
        }

        $sql = <<<'SQL'
SELECT CONCILIADO,
       COUNT(*) AS TOTAL_REG,
       SUM(CANTIDAD) AS TOTAL_MONTO
FROM MP
WHERE TRUNC(FDEPOSITO) = TO_DATE(:fecha, 'YYYY-MM-DD')
  AND TIPO = 'PD'
  AND MODO = 'I'
GROUP BY CONCILIADO
SQL;

        try {
            $stmt = $db->db_activa->prepare($sql);
            $stmt->execute(['fecha' => $fecha]);
            $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [
                'totalNoConciliados' => 0,
                'importeNoConciliados' => 0.0,
                'totalConciliados' => 0,
                'importeConciliados' => 0.0,
            ];
        }

        $pendReg = 0;
        $pendImp = 0.0;
        $concReg = 0;
        $concImp = 0.0;

        foreach (is_array($filas) ? $filas : [] as $row) {
            $est = strtoupper(trim((string) ($row['CONCILIADO'] ?? '')));
            $cnt = (int) ($row['TOTAL_REG'] ?? 0);
            $monto = (float) ($row['TOTAL_MONTO'] ?? 0);
            if ($est === 'C') {
                $pendReg += $cnt;
                $pendImp += $monto;
            } elseif ($est === 'D') {
                $concReg += $cnt;
                $concImp += $monto;
            }
        }

        return [
            'totalNoConciliados' => $pendReg,
            'importeNoConciliados' => round($pendImp, 2),
            'totalConciliados' => $concReg,
            'importeConciliados' => round($concImp, 2),
        ];
    }
}
