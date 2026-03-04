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
        $fechaPago = trim((string) $fechaPago);
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
            $condFecha = " AND a.frealdep = TO_DATE(:fecha, 'YYYY-MM-DD') ";
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
                TO_CHAR(a.frealdep, 'YYYY-MM-DD') AS frealdep,
                a.cantidad, a.modo, a.conciliado, a.estatus, a.actualizarpe,
                a.secuenciaim, a.fechaim, a.periodo AS periodo2,
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
                TO_CHAR(a.frealdep, 'YYYY-MM-DD') AS frealdep,
                a.cantidad, a.modo, a.conciliado, a.estatus, a.actualizarpe,
                a.secuenciaim, a.fechaim, a.periodo AS periodo2,
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
        $fecha = trim((string) ($pago['FREALDEP'] ?? ''));
        $periodo = isset($pago['PERIODO']) ? (int) $pago['PERIODO'] : 0;
        $secuencia = trim((string) ($pago['SECUENCIA'] ?? ''));
        $monto = isset($pago['CANTIDAD']) ? (float) $pago['CANTIDAD'] : 0;
        $cuenta = trim((string) ($pago['CDGCB'] ?? ''));

        if ($empresa === '' || $cdgclns === '' || $ciclo === '' || $tipo === '' || $fecha === '' || $secuencia === '' || $cuenta === '') {
            throw new \InvalidArgumentException('Faltan datos obligatorios del pago para conciliar.');
        }

        $config = App::getConfig();
        $valor = $config['CONCILIACION_SOLO_FLUJO'] ?? ($config['conciliacion']['CONCILIACION_SOLO_FLUJO'] ?? null);
        $soloFlujo = $valor !== null && (filter_var($valor, FILTER_VALIDATE_BOOLEAN) || $valor === 'true' || $valor === '1');

        if ($soloFlujo) {
            $database->spRedistribucionPagosPrueba($empresa, $cdgclns, $ciclo, $tipo, $fecha, $periodo, $secuencia, $monto, $cuenta, $usuario, $identificador);
        } else {
            $database->spRedistribucionPagos($empresa, $cdgclns, $ciclo, $tipo, $fecha, $periodo, $secuencia, $monto, $cuenta, $usuario, $identificador);
        }
    }
}
