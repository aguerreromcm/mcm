<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\Database;

/**
 * Repository: ajustes de pre cierre (descuentos D y refinanciamientos R registrados en PAGOSDIA).
 * SP_AJUSTES_PRECIERRE registra el ajuste en cartera y marca el movimiento con ID_IMPORTACION.
 * Sin lógica de negocio; solo SQL y llamadas a procedimientos.
 */
class AjustesPrecierreRepository
{
    const EMPRESA = 'EMPFIN';

    /**
     * Movimientos D y R de la fecha pendientes de aplicar, con el saldo del crédito a esa fecha.
     *
     * @param string $fecha Y-m-d
     * @return array
     */
    public function getMovimientosPendientes($fecha)
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(PGD.FECHA, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_HORA,
                TO_CHAR(PGD.FECHA, 'DD/MM/YYYY') AS FECHA_FMT,
                PGD.CDGNS,
                PGD.CICLO,
                PGD.SECUENCIA,
                PGD.TIPO,
                PGD.MONTO,
                NVL(TRIM(PGD.NOMBRE), '') AS NOMBRE,
                SALDOTOTALPRN(
                    PGD.CDGEM, PGD.CDGNS, PGD.CICLO,
                    NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
                    PGD.FECHA
                ) AS SALDO_TOTAL,
                SALDOCAPITALPRN(
                    PGD.CDGEM, PGD.CDGNS, PGD.CICLO,
                    NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
                    PGD.FECHA, NULL, 'N'
                ) AS SALDO_CAPITAL
            FROM
                PAGOSDIA PGD
            WHERE
                PGD.CDGEM = 'EMPFIN'
                AND PGD.ESTATUS = 'A'
                AND PGD.TIPO IN ('D', 'R')
                AND PGD.MONTO != 0
                AND PGD.FECHA >= TO_DATE(:f1, 'YYYY-MM-DD')
                AND PGD.FECHA < TO_DATE(:f2, 'YYYY-MM-DD') + 1
                AND PGD.ID_IMPORTACION IS NULL
            ORDER BY
                PGD.SECUENCIA
        SQL;

        $db = new Database();
        if ($db->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }

        $stmt = $db->db_activa->prepare($qry);
        $stmt->execute(['f1' => $fecha, 'f2' => $fecha]);
        $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return is_array($filas) ? $filas : [];
    }

    /**
     * Movimiento pendiente identificado por su llave; null si ya se aplicó o no existe.
     *
     * @param string $fecha Y-m-d
     * @param string $cdgns
     * @param string $ciclo
     * @param string|int $secuencia
     * @param string $tipo D|R
     * @return array|null
     */
    public function getMovimientoPendiente($fecha, $cdgns, $ciclo, $secuencia, $tipo)
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(PGD.FECHA, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_HORA,
                PGD.CDGNS,
                PGD.CICLO,
                PGD.SECUENCIA,
                PGD.TIPO,
                PGD.MONTO
            FROM
                PAGOSDIA PGD
            WHERE
                PGD.CDGEM = 'EMPFIN'
                AND PGD.ESTATUS = 'A'
                AND PGD.TIPO = :tipo
                AND PGD.CDGNS = :cdgns
                AND PGD.CICLO = :ciclo
                AND PGD.SECUENCIA = :secuencia
                AND PGD.FECHA >= TO_DATE(:f1, 'YYYY-MM-DD')
                AND PGD.FECHA < TO_DATE(:f2, 'YYYY-MM-DD') + 1
                AND PGD.ID_IMPORTACION IS NULL
        SQL;

        $db = new Database();
        if ($db->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }

        $stmt = $db->db_activa->prepare($qry);
        $stmt->execute([
            'tipo' => $tipo,
            'cdgns' => $cdgns,
            'ciclo' => $ciclo,
            'secuencia' => (int) $secuencia,
            'f1' => $fecha,
            'f2' => $fecha,
        ]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return is_array($row) && !empty($row) ? $row : null;
    }

    /**
     * Aplica el movimiento con SP_AJUSTES_PRECIERRE.
     * SPREGISTROAJUSTE (invocado por el SP) no propaga sus errores: deja el resultado en DBMS_OUTPUT
     * ("1 ..." éxito, "0 ..." error) y el SP marca el movimiento de todos modos, por eso la marca
     * se revierte cuando el ajuste no se registró.
     *
     * @param array $mov Fila de getMovimientoPendiente()
     * @param string $usuario
     * @return array ['success' => bool, 'confirmado' => bool, 'mensaje' => string]
     */
    public function aplicarAjuste(array $mov, $usuario)
    {
        $sp = <<<SQL
            BEGIN
                SP_AJUSTES_PRECIERRE(
                    p_CDGEM => :cdgem,
                    p_FECHA => TO_DATE(:fecha, 'YYYY-MM-DD HH24:MI:SS'),
                    p_CDGNS => :cdgns,
                    p_CICLO => :ciclo,
                    p_SECUENCIA => :secuencia,
                    p_MONTO => :monto,
                    p_TIPO => :tipo,
                    p_USUARIO => :usuario
                );
            END;
        SQL;

        $db = new Database();
        if ($db->db_activa === null) {
            return ['success' => false, 'confirmado' => false, 'mensaje' => 'No hay conexión a la base de datos.'];
        }

        try {
            $db->IniciaTransaccion();
            $salida = $db->EjecutaSP_DBMS_OUTPUT($sp, [
                'cdgem' => self::EMPRESA,
                'fecha' => (string) $mov['FECHA_HORA'],
                'cdgns' => (string) $mov['CDGNS'],
                'ciclo' => (string) $mov['CICLO'],
                'secuencia' => (string) $mov['SECUENCIA'],
                'monto' => (string) $mov['MONTO'],
                'tipo' => (string) $mov['TIPO'],
                'usuario' => (string) $usuario,
            ]);

            $mensaje = '';
            foreach ((array) $salida as $linea) {
                $texto = trim((string) ($linea['RESULTADO'] ?? ''));
                if ($texto !== '') {
                    $mensaje = $texto;
                    break;
                }
            }

            if (preg_match('/^0\b/', $mensaje)) {
                $db->CancelaTransaccion();
                $detalle = trim(preg_replace('/^0\s*/', '', $mensaje));
                return ['success' => false, 'confirmado' => true, 'mensaje' => $detalle !== '' ? $detalle : 'El ajuste no se registró.'];
            }

            $db->ConfirmaTransaccion();
            return ['success' => true, 'confirmado' => $mensaje !== '', 'mensaje' => $mensaje];
        } catch (\Throwable $e) {
            try {
                $db->CancelaTransaccion();
            } catch (\Throwable $e2) {
                // Sin transacción activa
            }
            return ['success' => false, 'confirmado' => false, 'mensaje' => self::mensajeOracle($e->getMessage())];
        }
    }

    /**
     * Primer error ORA- del mensaje (EjecutaSP_DBMS_OUTPUT incluye el SQL y los parámetros en la excepción).
     *
     * @param string $mensaje
     * @return string
     */
    private static function mensajeOracle($mensaje)
    {
        if (preg_match('/ORA-\d+:[^\n]*/', (string) $mensaje, $m)) {
            return trim($m[0]);
        }
        $lineas = preg_split('/\R/', trim((string) $mensaje));
        return trim((string) ($lineas[0] ?? ''));
    }
}
