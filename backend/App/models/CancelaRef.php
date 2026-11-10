<?php

namespace App\models;

defined("APPPATH") or die("Access denied");

use Core\Database;
use Core\Model;

class CancelaRef extends Model
{
    const DIAS_REVERSO = 15;
    const TOLERANCIA_MONTO = 1;

    public static function GetMovimientos($datos)
    {
        try {
            $db = new Database();
            $res = self::ConsultaMovimientos($db, ['credito' => $datos['credito']]);
            if ($res === false) return self::Responde(false, 'Error al obtener movimientos');

            $res = array_map([self::class, 'EvaluaPoliticas'], $res);
            return self::Responde(true, 'Movimientos obtenidos', $res);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al obtener movimientos', null, $e->getMessage());
        }
    }

    public static function ConsultaMovimientos($db, $prm)
    {
        $filtro = isset($prm['secuencia']) ? <<<SQL
            AND PD.CICLO = :ciclo
            AND PD.SECUENCIA = :secuencia
            AND PD.TIPO = :tipo
            AND TRUNC(PD.FECHA) = TO_DATE(:fecha, 'DD/MM/YYYY')
        SQL : '';

        $qry = <<<SQL
            SELECT
                PRC.CDGCL AS CLIENTE,
                PD.NOMBRE,
                PD.CDGNS AS CREDITO,
                PD.CICLO,
                SALDOTOTALPRN(
                        'EMPFIN', 
                        PD.CDGNS, 
                        PD.CICLO, 
                        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL
                    ) AS SALDO_TOTAL,
                PRN.SITUACION,
                TIPO_OPERACION(PD.TIPO) AS OPERACION,
                PD.TIPO,
                TO_CHAR(PD.FECHA, 'DD/MM/YYYY') AS FECHA,
                TRUNC(SYSDATE) - TRUNC(PD.FREGISTRO) AS DIAS_REGISTRO,
                PD.MONTO,
                PD.CDGOCPE AS EJECUTIVO,
                PD.CDGPE AS REGISTRO,
                PD.SECUENCIA,
                (
                    SELECT
                        MAX(CICLO)
                    FROM
                        PRC
                    WHERE
                        CDGNS = PD.CDGNS
                    AND CICLO NOT IN ('D1', 'D2', 'D3', 'D4', 'D5', 'D6', 'D7')
                ) AS ULTIMO_CICLO,
                (
                    SELECT
                        COUNT(*)
                    FROM
                        TBL_CIERRE_DIA TCD
                    WHERE
                        TCD.CDGCLNS = PD.CDGNS
                        AND TCD.CICLO = PD.CICLO
                        AND TCD.FECHA_LIQUIDA IS NOT NULL
                ) AS FECHAS_LIQUIDA,
                (
                    SELECT
                        COUNT(*)
                    FROM
                        MPR
                    WHERE
                        MPR.CDGEM = 'EMPFIN'
                        AND MPR.CDGNS = PD.CDGNS
                        AND MPR.CICLO = PD.CICLO
                        AND MPR.RAZON = '05'
                        AND TRUNC(MPR.FECHA) = TRUNC(PD.FECHA)
                ) AS REVERSOS_PREVIOS
            FROM
                PAGOSDIA PD
                JOIN PRC ON PRC.CDGNS = PD.CDGNS AND PRC.CICLO = PD.CICLO
                JOIN PRN ON PRN.CDGNS = PD.CDGNS AND PRN.CICLO = PD.CICLO
            WHERE
                PD.CDGNS = :credito 
                AND PD.TIPO IN ('R', 'D')
                AND PD.ESTATUS = 'A'
                $filtro
            ORDER BY
                PD.FECHA DESC
        SQL;

        return $db->queryAll($qry, $prm);
    }

    public static function EvaluaPoliticas($mov)
    {
        $monto = (float) $mov['MONTO'];
        $motivo = null;

        if ($mov['CICLO'] !== $mov['ULTIMO_CICLO']) $motivo = "El movimiento no está en el último ciclo ({$mov['ULTIMO_CICLO']}).";
        else if ($mov['SITUACION'] !== 'L') $motivo = 'El último ciclo no está liquidado.';
        else if ((int) $mov['DIAS_REGISTRO'] > self::DIAS_REVERSO) $motivo = "Tiene {$mov['DIAS_REGISTRO']} días de registrado, el máximo es " . self::DIAS_REVERSO . '.';
        else if ((int) $mov['FECHAS_LIQUIDA'] === 0) $motivo = 'El ciclo no tiene fecha de liquidación en el cierre diario.';
        else if ((int) $mov['REVERSOS_PREVIOS'] > 0) $motivo = 'Ya existe un ajuste de reverso para la fecha del movimiento, revise el crédito.';
       
        $mov['MOTIVO'] = $motivo;
        return $mov;
    }

    public static function ReversaMovimiento($datos)
    {
        $comentario = trim($datos['comentario'] ?? '');
        if ($comentario === '') return self::Responde(false, 'El comentario es obligatorio.');
        if (strlen($comentario) > 1000) return self::Responde(false, 'El comentario es demasiado largo, el máximo es de 1000 caracteres (las letras con acento y la ñ cuentan como 2).');

        try {
            $db = new Database();
            $movs = self::ConsultaMovimientos($db, [
                'credito' => $datos['credito'],
                'ciclo' => $datos['ciclo'],
                'secuencia' => $datos['secuencia'],
                'tipo' => $datos['tipo'],
                'fecha' => $datos['fecha']
            ]);

            if ($movs === false) return self::Responde(false, 'Error al consultar el movimiento.');
            if (count($movs) === 0) return self::Responde(false, 'El movimiento no existe o ya no está activo.');

            $mov = self::EvaluaPoliticas($movs[0]);
            if ($mov['MOTIVO'] !== null) return self::Responde(false, 'No se puede reversar el movimiento. ' . $mov['MOTIVO']);
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al validar el movimiento.', null, $e->getMessage());
        }

        $r = self::ReactivaCiclo($db, $mov, $comentario, $datos['usuario']);
        if (!$r['success']) return self::Responde(false, "Se registró el ajuste de reverso, pero no se pudo reactivar el ciclo: {$r['mensaje']} El movimiento sigue activo, revise el crédito.", null, $r['error'] ?? null);

        $r = self::DesactivaMovimiento($db, $mov);
        if (!$r['success']) return self::Responde(false, 'Se registró el ajuste de reverso y se reactivó el ciclo, pero no se pudo cancelar el movimiento. Revise el crédito.', null, $r['error'] ?? null);

        return self::Responde(true, 'Movimiento reversado correctamente.');
    }

    public static function ReactivaCiclo($db, $mov, $comentario, $usuario)
    {
        $sp = <<<SQL
            CALL SP_REACTIVA_CICLO(:credito, :ciclo, TO_DATE(:fecha, 'DD/MM/YYYY'), :motivo, :usuario, :output)
        SQL;

        $prm = [
            'credito' => $mov['CREDITO'],
            'ciclo' => $mov['CICLO'],
            'fecha' => $mov['FECHA'],
            'motivo' => $comentario,
            'usuario' => $usuario
        ];

        try {
            $res = trim((string) $db->EjecutaSP($sp, $prm));
            if (preg_match('/^1\b/', $res)) return self::Responde(true, 'Ciclo reactivado.');
            return self::Responde(false, preg_replace('/^[01]\s*/', '', $res));
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al reactivar el ciclo.', null, $e->getMessage());
        }
    }

    public static function DesactivaMovimiento($db, $mov)
    {
        $qry = <<<SQL
            UPDATE
                PAGOSDIA
            SET
                ESTATUS = 'E',
                FACTUALIZA = SYSDATE 
            WHERE
                CDGNS = :credito
                AND CICLO = :ciclo
                AND SECUENCIA = :secuencia
                AND TIPO = :tipo
                AND TRUNC(FECHA) = TO_DATE(:fecha, 'DD/MM/YYYY')
                AND ESTATUS = 'A'
        SQL;

        $prm = [
            'credito' => $mov['CREDITO'],
            'ciclo' => $mov['CICLO'],
            'secuencia' => $mov['SECUENCIA'],
            'tipo' => $mov['TIPO'],
            'fecha' => $mov['FECHA']
        ];

        try {
            if ($db->actualizar($qry, $prm)) return self::Responde(true, 'Movimiento cancelado.');
            return self::Responde(false, 'Error al cancelar el movimiento.');
        } catch (\Exception $e) {
            return self::Responde(false, 'Error al cancelar el movimiento.', null, $e->getMessage());
        }
    }
}
