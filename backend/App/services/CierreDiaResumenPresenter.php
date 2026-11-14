<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use App\repositories\CierreDiaRepository;
use App\repositories\PagosAplicacionRepository;
use App\repositories\ConciliacionRepository;

/**
 * Datos del resumen de cierre que usa el job de prueba de correo (JobPruebaMail).
 * El correo real y la pantalla de cierre usan CierreDiaCorreoPresenter.
 */
class CierreDiaResumenPresenter
{
    /**
     * @param string $fechaCierre Y-m-d
     * @return array
     */
    public static function construir($fechaCierre)
    {
        $fechaCierre = trim((string) $fechaCierre);
        $ts = strtotime($fechaCierre);
        if ($fechaCierre === '' || $ts === false) {
            throw new \InvalidArgumentException('Fecha de cierre no válida.');
        }
        $fechaCierre = date('Y-m-d', $ts);
        $fechaDevengo = date('Y-m-d', strtotime($fechaCierre . ' +1 day'));

        $repoCierre = new CierreDiaRepository();
        $repoPagos = new PagosAplicacionRepository();
        $repoConc = new ConciliacionRepository();

        $bitacora = $repoCierre->getBitacoraCierrePorFecha($fechaCierre);
        $resCierre = $repoCierre->getResumenCierre($fechaCierre);
        $resDevengo = $repoCierre->getResumenDevengo($fechaCierre);
        $imp = $repoPagos->getResumenPorEstatusImportacion($fechaCierre);
        $conc = $repoConc->getResumenConciliacionImportados($fechaCierre);

        $rp = (int) ($imp['registrosPagos'] ?? 0);
        $rg = (int) ($imp['registrosGarantias'] ?? 0);
        $ri = (int) ($imp['registrosIncidencias'] ?? 0);
        $ip = (float) ($imp['importePagos'] ?? 0);
        $ig = (float) ($imp['importeGarantias'] ?? 0);
        $ii = (float) ($imp['importeIncidencias'] ?? 0);

        $totalReg = $rp + $rg + $ri;
        $totalImp = round($ip + $ig + $ii, 2);

        $cierreFinalizado = self::bitacoraFinalizadaConExito($bitacora);
        if ($cierreFinalizado) {
            $pendReg = 0;
            $aplReg = $totalReg;
            $impPend = 0.0;
            $impApl = $totalImp;
        } else {
            $pendReg = 0;
            $aplReg = 0;
            $impPend = 0.0;
            $impApl = 0.0;
            $filas = $repoPagos->getDatosLayoutPorFecha($fechaCierre);
            foreach ($filas as $f) {
                if (!is_array($f)) {
                    continue;
                }
                $monto = (float) ($f['MONTO'] ?? $f['monto'] ?? 0);
                if (PagosAplicacionRepository::filaMarcadaImportada($f)) {
                    $aplReg++;
                    $impApl += $monto;
                } else {
                    $pendReg++;
                    $impPend += $monto;
                }
            }
            $impPend = round($impPend, 2);
            $impApl = round($impApl, 2);
            if ($totalReg === 0 && ($pendReg + $aplReg) > 0) {
                $totalReg = $pendReg + $aplReg;
                $totalImp = round($impPend + $impApl, 2);
            }
        }

        $proceso = self::mapearProceso($bitacora, (int) ($resCierre['registros'] ?? 0));

        return [
            'fechaCierre' => $fechaCierre,
            'fechaCierreFmt' => date('d/m/Y', $ts),
            'fechaDevengoFmt' => date('d/m/Y', strtotime($fechaDevengo)),
            'proceso' => $proceso,
            'pagos' => [
                'total' => self::metrica($totalReg, $totalImp),
                'pendientes' => self::metrica($pendReg, $impPend),
                'aplicados' => self::metrica($aplReg, $impApl),
                'pagos' => self::metrica($rp, $ip),
                'garantias' => self::metrica($rg, $ig),
                'incidencias' => self::metrica($ri, $ii),
            ],
            'conciliacion' => [
                'pendientes' => self::metrica(
                    (int) ($conc['totalNoConciliados'] ?? 0),
                    (float) ($conc['importeNoConciliados'] ?? 0)
                ),
                'conciliados' => self::metrica(
                    (int) ($conc['totalConciliados'] ?? 0),
                    (float) ($conc['importeConciliados'] ?? 0)
                ),
            ],
            'devengo' => [
                'creditos' => (int) ($resDevengo['creditos'] ?? 0),
                'monto' => round((float) ($resDevengo['monto'] ?? 0), 2),
            ],
        ];
    }

    private static function metrica($registros, $importe)
    {
        return [
            'registros' => (int) $registros,
            'importe' => round((float) $importe, 2),
        ];
    }

    private static function bitacoraFinalizadaConExito($bitacora)
    {
        if (!is_array($bitacora) || empty($bitacora)) {
            return false;
        }
        $fin = isset($bitacora['FIN']) ? trim((string) $bitacora['FIN']) : '';
        if ($fin === '' || $fin === '-') {
            return false;
        }

        return (int) ($bitacora['EXITO'] ?? 0) === 1;
    }

    private static function mapearProceso($bitacora, $registrosCierre)
    {
        if (!is_array($bitacora) || empty($bitacora)) {
            return [
                'usuario' => '-',
                'inicio' => '-',
                'fin' => '-',
                'registros' => $registrosCierre,
                'estatus' => 'Sin cierre registrado',
            ];
        }
        $enProceso = !empty($bitacora['EN_PROCESO']) && (int) $bitacora['EN_PROCESO'] === 1;
        $exito = (int) ($bitacora['EXITO'] ?? 0);
        $estatus = $enProceso ? 'Procesando' : ($exito === 1 ? 'Finalizado' : 'Error');

        return [
            'usuario' => (string) ($bitacora['USUARIO'] ?? '-'),
            'inicio' => (string) ($bitacora['INICIO'] ?? '-'),
            'fin' => (string) ($bitacora['FIN'] ?? '-'),
            'registros' => $registrosCierre,
            'estatus' => $estatus,
        ];
    }
}
