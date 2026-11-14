<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\Database;

/**
 * Repository: pagos no identificados (tabla PDI, ESTATUS = 'RE').
 * Al identificarlos se marcan como en spIdentificaPagos (IP = pago, IG = garantía).
 * Sin lógica de negocio; solo SQL.
 */
class PagosNoIdentificadosRepository
{
    const EMPRESA = 'EMPFIN';

    /**
     * Pagos no identificados con fecha de depósito en el día indicado.
     *
     * @param string $fecha Y-m-d
     * @return array
     */
    public function getPendientes($fecha)
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(P.FDEPOSITO, 'YYYY-MM-DD') AS FECHA,
                TO_CHAR(P.FDEPOSITO, 'DD/MM/YYYY') AS FECHA_FMT,
                P.SECUENCIA,
                P.REFERENCIA,
                P.CDGCB,
                P.CANTIDAD,
                P.DESCRIPCION,
                P.CDGPE_REG,
                TO_CHAR(P.FECHA_REG, 'DD/MM/YYYY HH24:MI') AS FECHA_REG
            FROM
                PDI P
            WHERE
                P.CDGEM = 'EMPFIN'
                AND P.ESTATUS = 'RE'
                AND P.FDEPOSITO >= TO_DATE(:f1, 'YYYY-MM-DD')
                AND P.FDEPOSITO < TO_DATE(:f2, 'YYYY-MM-DD') + 1
            ORDER BY
                P.SECUENCIA
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
     * Registro de PDI por su llave (cualquier estatus).
     *
     * @param string $fecha Y-m-d (FDEPOSITO)
     * @param string $secuencia
     * @return array|null
     */
    public function getPago($fecha, $secuencia)
    {
        $qry = <<<SQL
            SELECT
                TO_CHAR(P.FDEPOSITO, 'YYYY-MM-DD') AS FECHA,
                P.SECUENCIA,
                P.REFERENCIA,
                P.REFERENCIA2,
                P.CDGCB,
                P.CDGCLNS,
                P.CICLO,
                P.CLNS,
                P.CANTIDAD,
                P.ESTATUS,
                TO_CHAR(P.FECHAIM, 'YYYY-MM-DD HH24:MI:SS') AS FECHAIM,
                P.CDGPE_IDEN,
                TO_CHAR(P.FECHA_IDEN, 'YYYY-MM-DD HH24:MI:SS') AS FECHA_IDEN,
                TO_CHAR(P.FECHA_IDEN, 'DD/MM/YYYY HH24:MI') AS FECHA_IDEN_FMT
            FROM
                PDI P
            WHERE
                P.CDGEM = 'EMPFIN'
                AND P.FDEPOSITO = TO_DATE(:fecha, 'YYYY-MM-DD')
                AND P.SECUENCIA = :secuencia
        SQL;

        $db = new Database();
        if ($db->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }

        $stmt = $db->db_activa->prepare($qry);
        $stmt->execute(['fecha' => $fecha, 'secuencia' => (string) $secuencia]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return is_array($row) && !empty($row) ? $row : null;
    }

    /**
     * Crédito en situación Entregado (PRN.SITUACION = 'E') con la referencia que le corresponde al tipo de movimiento
     * (misma regla que el cierre: G → '0', P/X → 'P').
     *
     * @param string $credito
     * @param string $ciclo
     * @param string $tipo P|X|G
     * @return array|null
     */
    public function getCreditoEntregado($credito, $ciclo, $tipo)
    {
        $qry = <<<SQL
            SELECT
                PRN.CDGNS,
                PRN.CICLO,
                NVL(TRIM(NS.NOMBRE), '') AS NOMBRE,
                DECODE(:tipo1, 'G', '0', 'P')
                    || PRN.CDGNS
                    || PRN.CDGTPC
                    || FN_DV(DECODE(:tipo2, 'G', '0', 'P') || PRN.CDGNS || PRN.CDGTPC) AS REFERENCIA
            FROM
                PRN
                LEFT JOIN NS
                    ON NS.CDGEM = PRN.CDGEM
                    AND NS.CODIGO = PRN.CDGNS
            WHERE
                PRN.CDGEM = 'EMPFIN'
                AND PRN.CDGNS = :credito
                AND PRN.CICLO = :ciclo
                AND PRN.SITUACION = 'E'
        SQL;

        $db = new Database();
        if ($db->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }

        $stmt = $db->db_activa->prepare($qry);
        $stmt->execute(['tipo1' => $tipo, 'tipo2' => $tipo, 'credito' => $credito, 'ciclo' => $ciclo]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return is_array($row) && !empty($row) ? $row : null;
    }

    /**
     * Marca el pago como identificado (mismas columnas que spIdentificaPagos). Solo afecta registros aún en 'RE'.
     *
     * @param array $pago Fila de getPago()
     * @param array $credito Fila de getCreditoEntregado()
     * @param string $estatus IP|IG
     * @param string $usuario
     * @param Database $db Conexión con la transacción del proceso
     * @return bool true si se actualizó el registro
     */
    public function marcarIdentificado(array $pago, array $credito, $estatus, $usuario, Database $db)
    {
        $qry = <<<SQL
            UPDATE PDI
            SET
                ESTATUS = :estatus,
                FECHAIM = FDEPOSITO,
                CDGPE_IDEN = :usuario,
                FECHA_IDEN = SYSDATE,
                CDGCLNS = :credito,
                CICLO = :ciclo,
                CLNS = 'G',
                REFERENCIA2 = :referencia
            WHERE
                CDGEM = 'EMPFIN'
                AND FDEPOSITO = TO_DATE(:fecha, 'YYYY-MM-DD')
                AND SECUENCIA = :secuencia
                AND ESTATUS = 'RE'
        SQL;

        $stmt = $db->db_activa->prepare($qry);
        $stmt->execute([
            'estatus' => $estatus,
            'usuario' => (string) $usuario,
            'credito' => (string) $credito['CDGNS'],
            'ciclo' => (string) $credito['CICLO'],
            'referencia' => (string) $credito['REFERENCIA'],
            'fecha' => (string) $pago['FECHA'],
            'secuencia' => (string) $pago['SECUENCIA'],
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Liga el pago identificado con su movimiento en cartera (PDI.SECUENCIAIM), con la misma búsqueda que
     * spIdentificaPagos: la secuencia en MP para pagos (IP) o en PAG_GAR_SIM para garantías (IG).
     * ELIMINAR_PAGOS_MP y SP_POLIZA_PAGOS localizan el registro de PDI por esta secuencia.
     *
     * @param array $pago Fila de getPago()
     * @param Database $db Conexión con la transacción del proceso (el pago ya marcado e importado)
     * @return string|null Secuencia registrada; null si no se encontró el movimiento en cartera
     */
    public function registrarSecuenciaCartera(array $pago, Database $db)
    {
        $qry = <<<SQL
            SELECT
                CASE
                    WHEN P.ESTATUS = 'IP' THEN (
                        SELECT MAX(MP.SECUENCIA)
                        FROM MP
                        WHERE MP.CDGEM = P.CDGEM
                          AND MP.CDGCLNS = P.CDGCLNS
                          AND MP.CICLO = P.CICLO
                          AND MP.CLNS = P.CLNS
                          AND MP.CANTIDAD = P.CANTIDAD
                          AND MP.FDEPOSITO = P.FECHAIM
                          AND MP.CDGCB = P.CDGCB
                          AND MP.ACTUALIZARPE = P.CDGPE_IDEN
                          AND MP.TIPO = 'PD'
                    )
                    ELSE (
                        SELECT TO_CHAR(MAX(G.SECPAGO))
                        FROM PAG_GAR_SIM G
                        WHERE G.CDGEM = P.CDGEM
                          AND G.CDGCLNS = P.CDGCLNS
                          AND G.CLNS = P.CLNS
                          AND G.CANTIDAD = P.CANTIDAD
                          AND G.FPAGO = P.FECHAIM
                          AND G.CDGCB = P.CDGCB
                          AND G.CDGPE = P.CDGPE_IDEN
                          AND G.ESTATUS = 'RE'
                    )
                END AS SECUENCIA_CARTERA
            FROM
                PDI P
            WHERE
                P.CDGEM = 'EMPFIN'
                AND P.FDEPOSITO = TO_DATE(:fecha, 'YYYY-MM-DD')
                AND P.SECUENCIA = :secuencia
                AND P.ESTATUS IN ('IP', 'IG')
        SQL;

        $stmt = $db->db_activa->prepare($qry);
        $stmt->execute(['fecha' => (string) $pago['FECHA'], 'secuencia' => (string) $pago['SECUENCIA']]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $secuencia = trim((string) ($row['SECUENCIA_CARTERA'] ?? ''));
        if ($secuencia === '') {
            return null;
        }

        $qryUpdate = <<<SQL
            UPDATE PDI
            SET SECUENCIAIM = :secuenciaim
            WHERE
                CDGEM = 'EMPFIN'
                AND FDEPOSITO = TO_DATE(:fecha, 'YYYY-MM-DD')
                AND SECUENCIA = :secuencia
                AND ESTATUS IN ('IP', 'IG')
        SQL;

        $stmt = $db->db_activa->prepare($qryUpdate);
        $stmt->execute([
            'secuenciaim' => $secuencia,
            'fecha' => (string) $pago['FECHA'],
            'secuencia' => (string) $pago['SECUENCIA'],
        ]);

        return $secuencia;
    }

    /**
     * Devuelve el registro a 'RE' con sus valores originales cuando la aplicación en cartera no se concretó
     * (los SP de importación pueden confirmar la transacción por su cuenta).
     *
     * @param array $pago Fila de getPago() tomada antes de marcarlo
     * @param string $estatus Estatus con el que se marcó (IP|IG)
     * @param Database $db Conexión del proceso, ya sin transacción activa
     * @return bool
     */
    public function restaurarNoIdentificado(array $pago, $estatus, Database $db)
    {
        $qry = <<<SQL
            UPDATE PDI
            SET
                ESTATUS = 'RE',
                FECHAIM = TO_DATE(:fechaim, 'YYYY-MM-DD HH24:MI:SS'),
                CDGPE_IDEN = :cdgpe_iden,
                FECHA_IDEN = TO_DATE(:fecha_iden, 'YYYY-MM-DD HH24:MI:SS'),
                CDGCLNS = :cdgclns,
                CICLO = :ciclo,
                CLNS = :clns,
                REFERENCIA2 = :referencia2
            WHERE
                CDGEM = 'EMPFIN'
                AND FDEPOSITO = TO_DATE(:fecha, 'YYYY-MM-DD')
                AND SECUENCIA = :secuencia
                AND ESTATUS = :estatus
        SQL;

        try {
            $stmt = $db->db_activa->prepare($qry);
            $stmt->execute([
                'fechaim' => $pago['FECHAIM'],
                'cdgpe_iden' => $pago['CDGPE_IDEN'],
                'fecha_iden' => $pago['FECHA_IDEN'],
                'cdgclns' => $pago['CDGCLNS'],
                'ciclo' => $pago['CICLO'],
                'clns' => $pago['CLNS'],
                'referencia2' => $pago['REFERENCIA2'],
                'fecha' => (string) $pago['FECHA'],
                'secuencia' => (string) $pago['SECUENCIA'],
                'estatus' => $estatus,
            ]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
