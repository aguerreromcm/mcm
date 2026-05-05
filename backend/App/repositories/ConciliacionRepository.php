<?php

namespace App\repositories;

defined("APPPATH") or die("Access denied");

use Core\App;
use Core\Database;
use App\repositories\PagosAplicacionRepository;

/**
 * Repository para Conciliación de pagos (solo consulta).
 * Réplica de la consulta BuscarPagos del VB6 (tabla MP + CL/PRC/NS/PRN).
 * También ofrece getMovimientosPorFecha (PAGOSDIA) para compatibilidad.
 */
class ConciliacionRepository
{
    const EMPRESA_DEFAULT = 'EMPFIN';

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
     * Convierte una fecha de entrada a Y/m/d para llamadas de SP (formato VB6).
     *
     * @param string $fecha
     * @return string
     */
    private function normalizarFechaYmSlashd($fecha)
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return '';
        }
        $timestamp = strtotime($fecha);
        if ($timestamp === false) {
            return $fecha;
        }
        return date('Y/m/d', $timestamp);
    }

    /**
     * Obtiene los movimientos de pagos por fecha (PAGOSDIA, misma fuente que Aplicar Pagos).
     *
     * @param string $fecha Fecha en Y-m-d
     * @return array Lista de filas con FECHA, REFERENCIA, MONTO, MONEDA, F_IMPORTACION, etc.
     */
    public function getMovimientosPorFecha($fecha)
    {
        $repo = new PagosAplicacionRepository();
        return $repo->getDatosLayoutPorFecha($fecha);
    }

    /**
     * Pagos por conciliar desde MP (réplica exacta de BuscarPagos VB6).
     * Filtros opcionales: empresa, fechaPago, tipoCliente (I/G), codigo, ciclo, ctaBancaria.
     *
     * @param string $empresa Ej: EMPFIN o vacío para (Todas)
     * @param string $fechaPago Y-m-d o vacío
     * @param string $tipoCliente I, G o vacío (Todos)
     * @param string $codigo Código cliente
     * @param string $ciclo Ciclo
     * @param string $ctaBancaria Cuenta bancaria
     * @return array Lista de filas con cdgem, cdgns, cdgclns, clns, tipocte, referencia, frealdep, cantidad, conciliado, nombre, etc.
     */
    public function getPagosPorConciliarMP($empresa, $fechaPago, $tipoCliente, $codigo, $ciclo, $ctaBancaria)
    {
        $db = new Database();
        if ($db->db_activa === null) {
            return [];
        }

        $empresa = trim((string) $empresa);
        if ($empresa === '' || strtoupper($empresa) === '(TODAS)') {
            $empresa = self::EMPRESA_DEFAULT;
        }
        $fechaPago = $this->normalizarFechaYmd($fechaPago);
        $tipoCliente = trim((string) $tipoCliente);
        if (strtoupper($tipoCliente) === '(TODOS)' || $tipoCliente === '') {
            $tipoCliente = '';
        } else {
            $tipoCliente = strtoupper(substr($tipoCliente, 0, 1)) === 'G' ? 'G' : 'I';
        }
        $codigo = trim((string) $codigo);
        $ciclo = trim((string) $ciclo);
        $ctaBancaria = trim((string) $ctaBancaria);

        $params = ['empresa' => $empresa];
        $condEmpresa = " AND a.CDGEM = :empresa ";
        $condFecha = '';
        if ($fechaPago !== '') {
            $params['fecha'] = $fechaPago;
            // Si FREALDEP tiene hora, la comparación exacta por datetime deja pagos fuera.
            $condFecha = " AND TRUNC(a.frealdep) = TO_DATE(:fecha, 'YYYY-MM-DD') ";
        }
        $condTipo = '';
        if ($tipoCliente !== '') {
            $params['clns'] = $tipoCliente;
            $condTipo = " AND a.clns = :clns ";
        }
        $condCodigo = '';
        if ($codigo !== '') {
            $params['cdgclns'] = $codigo;
            $condCodigo = " AND a.cdgclns = :cdgclns ";
        }
        $condCiclo = '';
        if ($ciclo !== '') {
            $params['ciclo'] = $ciclo;
            $condCiclo = " AND c.ciclo = :ciclo ";
        }
        $condCta = '';
        if ($ctaBancaria !== '') {
            $params['cdgcb'] = $ctaBancaria;
            $condCta = " AND a.cdgcb = :cdgcb ";
        }

        $whereInd = $condEmpresa . $condFecha . $condTipo . $condCodigo . $condCiclo . $condCta;

        $sel = "SELECT a.cdgem, a.cdgns, a.cdgclns, a.clns,
                DECODE(a.clns, 'G', 'Grupal', 'I', 'Individual') tipocte,
                a.cdgcl, a.ciclo, a.periodo, c.tasa, c.plazo, c.periodicidad,
                a.secuencia, a.referencia, a.cdgcb, a.tipo,
                TO_CHAR(a.frealdep, 'YYYY/MM/DD') AS frealdep,
                a.cantidad, a.modo, a.conciliado, a.estatus, a.actualizarpe,
                a.secuenciaim, a.fechaim,
                NVL(RTRIM(LTRIM(b.nombre1 || ' ' || b.nombre2)), '') || ' ' || NVL(RTRIM(LTRIM(b.primape || ' ' || b.segape)), '') AS nombre
                FROM mp a
                LEFT JOIN cl b ON b.cdgem = a.cdgem AND b.codigo = a.cdgclns
                LEFT JOIN prc c ON a.cdgem = c.cdgem AND a.cdgclns = c.cdgcl AND a.ciclo = c.ciclo AND a.clns = c.clns
                WHERE a.conciliado IN ('N','C') AND a.estatus = 'B' AND a.clns = 'I'
                AND fnRegresaSdoIndividual(a.CDGEM,a.CDGCLNS,a.CICLO) > 0 ";
        $q1 = $sel . $whereInd;

        $sel2 = "SELECT a.cdgem, a.cdgns, a.cdgclns, a.clns,
                DECODE(a.clns, 'G', 'Grupal', 'I', 'Individual') tipocte,
                a.cdgcl, a.ciclo, a.periodo, c.tasa, c.plazo, c.periodicidad,
                a.secuencia, a.referencia, a.cdgcb, a.tipo,
                TO_CHAR(a.frealdep, 'YYYY/MM/DD') AS frealdep,
                a.cantidad, a.modo, a.conciliado, a.estatus, a.actualizarpe,
                a.secuenciaim, a.fechaim,
                RTRIM(LTRIM(b.nombre)) AS nombre
                FROM mp a
                LEFT JOIN ns b ON b.cdgem = a.cdgem AND b.codigo = a.cdgclns
                LEFT JOIN prn c ON a.cdgem = c.cdgem AND a.cdgclns = c.cdgns AND a.ciclo = c.ciclo AND a.clns = 'G'
                WHERE a.conciliado IN ('N','C') AND a.estatus = 'B' AND a.clns = 'G' ";
        $q2 = $sel2 . $whereInd;

        $query = "(" . $q1 . ") UNION ALL (" . $q2 . ") ORDER BY frealdep, cdgclns, ciclo, secuencia";

        try {
            $stmt = $db->db_activa->prepare($query);
            $stmt->execute($params);
            $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!is_array($filas)) {
                return [];
            }
            foreach ($filas as $i => $row) {
                if (isset($row['CANTIDAD'])) {
                    $filas[$i]['CANTIDAD'] = (float) $row['CANTIDAD'];
                }
                if (isset($row['TASA'])) {
                    $filas[$i]['TASA'] = $row['TASA'] !== null ? (string) $row['TASA'] : '';
                }
                if (isset($row['FECHAIM']) && $row['FECHAIM'] !== null && is_object($row['FECHAIM'])) {
                    $filas[$i]['FECHAIM'] = method_exists($row['FECHAIM'], 'format') ? $row['FECHAIM']->format('Y-m-d H:i:s') : (string) $row['FECHAIM'];
                }
            }
            return $filas;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Ejecuta spRedistribucionPagos para un pago (réplica VB6 cmdConciliacion).
     * Debe llamarse dentro de una transacción usando el mismo $db.
     *
     * @param array $pago Claves: CDGEM, CDGCLNS, CICLO, CLNS (I/G), FREALDEP (Y-m-d), PERIODO, SECUENCIA, CANTIDAD, CDGCB
     * @param string $usuario Usuario que ejecuta
     * @param string $identificador DDMMYYYYHHNNSS
     * @param \Core\Database|null $db Conexión con transacción iniciada; si es null se usa conexión nueva (sin transacción)
     * @throws \Throwable Si el SP falla
     */
    public function ejecutarSpRedistribucionPagos(array $pago, $usuario, $identificador, $db = null)
    {
        $database = $db !== null ? $db : new Database();
        if ($database->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }

        $empresa = trim((string) ($pago['CDGEM'] ?? ''));
        $cdgclns = trim((string) ($pago['CDGCLNS'] ?? ''));
        $ciclo = trim((string) ($pago['CICLO'] ?? ''));
        $tipo = trim((string) ($pago['CLNS'] ?? ''));
        if ($tipo === '' && isset($pago['TIPOCTE'])) {
            $tipo = strtoupper(substr(trim((string) $pago['TIPOCTE']), 0, 1)) === 'G' ? 'G' : 'I';
        }
        $fecha = $this->normalizarFechaYmSlashd($pago['FREALDEP'] ?? '');
        $periodo = isset($pago['PERIODO']) ? (int) $pago['PERIODO'] : 0;
        $secuencia = trim((string) ($pago['SECUENCIA'] ?? ''));
        $monto = isset($pago['CANTIDAD']) ? (float) $pago['CANTIDAD'] : 0;
        $cuenta = trim((string) ($pago['CDGCB'] ?? ''));

        if ($empresa === '' || $cdgclns === '' || $ciclo === '' || $tipo === '' || $fecha === '' || $secuencia === '' || $cuenta === '') {
            throw new \InvalidArgumentException('Faltan datos obligatorios del pago para conciliar.');
        }

        $config = App::getConfig();
        $valor = $config['CONCILIACION_SOLO_FLUJO'] ?? ($config['conciliacion']['CONCILIACION_SOLO_FLUJO'] ?? null);
        $valorNorm = $valor === null ? null : strtolower(trim((string) $valor));
        // Solo consideramos "true" (tolerando variantes). Cualquier valor no parseable cae en false.
        $soloFlujo = $valorNorm !== null ? (filter_var($valorNorm, FILTER_VALIDATE_BOOLEAN) === true) : false;

        if ($soloFlujo) {
            $database->spRedistribucionPagosPrueba($empresa, $cdgclns, $ciclo, $tipo, $fecha, $periodo, $secuencia, $monto, $cuenta, $usuario, $identificador);
        } else {
            $database->spRedistribucionPagos($empresa, $cdgclns, $ciclo, $tipo, $fecha, $periodo, $secuencia, $monto, $cuenta, $usuario, $identificador);
        }
    }

    /**
     * Obtiene el estado actual en MP (conciliado/estatus) para los pagos dados.
     * Se usa para validar "afectación real" antes/después de ejecutar la conciliación.
     *
     * @param array $pagos
     * @param \Core\Database|null $db
     * @return array Lista en el mismo orden del input:
     *   ['encontrado'=>bool,'conciliado'=>string|null,'estatus'=>string|null]
     */
    public function obtenerEstadosConciliacionPagos(array $pagos, $db = null)
    {
        $database = $db !== null ? $db : new Database();
        if ($database->db_activa === null) {
            throw new \RuntimeException('No hay conexión a la base de datos.');
        }

        $result = [];
        foreach ($pagos as $pago) {
            if (!is_array($pago)) {
                $result[] = ['encontrado' => false, 'conciliado' => null, 'estatus' => null];
                continue;
            }

            $cdgem = trim((string) ($pago['CDGEM'] ?? ''));
            $cdgclns = trim((string) ($pago['CDGCLNS'] ?? ''));
            $ciclo = trim((string) ($pago['CICLO'] ?? ''));
            $clns = trim((string) ($pago['CLNS'] ?? ($pago['TIPOCTE'] ?? '')));
            $frealdep = $this->normalizarFechaYmSlashd($pago['FREALDEP'] ?? '');
            $periodoSet = array_key_exists('PERIODO', $pago);
            $periodo = $periodoSet ? (int) $pago['PERIODO'] : null;
            $secuencia = trim((string) ($pago['SECUENCIA'] ?? ''));

            // Si falta cualquier clave, no intentamos consultar para evitar errores de parseo.
            if ($cdgem === '' || $cdgclns === '' || $ciclo === '' || $clns === '' || $frealdep === '' || $periodo === null || $secuencia === '') {
                $result[] = ['encontrado' => false, 'conciliado' => null, 'estatus' => null];
                continue;
            }

            $sql = "SELECT conciliado, estatus
                    FROM mp
                    WHERE cdgem = :cdgem
                      AND cdgclns = :cdgclns
                      AND ciclo = :ciclo
                      AND clns = :clns
                      AND TRUNC(frealdep) = TO_DATE(:frealdep, 'YYYY/MM/DD')
                      AND periodo = :periodo
                      AND secuencia = :secuencia";

            $stmt = $database->db_activa->prepare($sql);
            $stmt->execute([
                'cdgem' => $cdgem,
                'cdgclns' => $cdgclns,
                'ciclo' => $ciclo,
                'clns' => $clns,
                'frealdep' => $frealdep,
                'periodo' => $periodo,
                'secuencia' => $secuencia,
            ]);

            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (is_array($row) && count($row) > 0) {
                $result[] = [
                    'encontrado' => true,
                    'conciliado' => isset($row['CONCILIADO']) ? trim((string) $row['CONCILIADO']) : null,
                    'estatus' => isset($row['ESTATUS']) ? trim((string) $row['ESTATUS']) : null,
                ];
            } else {
                $result[] = ['encontrado' => false, 'conciliado' => null, 'estatus' => null];
            }
        }

        return $result;
    }
}
