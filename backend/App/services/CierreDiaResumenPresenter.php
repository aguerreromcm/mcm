<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use App\repositories\CierreDiaRepository;
use App\repositories\PagosAplicacionRepository;
use App\repositories\ConciliacionRepository;

/**
 * Arma el resumen unificado de cierre (modal y correo) con los mismos datos y criterios de consulta.
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

    /**
     * HTML interior para Mensajero::Notificaciones (misma estructura que el modal).
     *
     * @param array $datos Salida de construir()
     * @return string
     */
    public static function htmlCorreo(array $datos)
    {
        $titulo = 'Resumen de cierre del día ' . self::esc($datos['fechaCierreFmt'] ?? '');
        $proceso = $datos['proceso'] ?? [];
        $pagos = $datos['pagos'] ?? [];
        $conc = $datos['conciliacion'] ?? [];
        $devengo = $datos['devengo'] ?? [];
        $titDevengo = 'Devengo para el día ' . self::esc($datos['fechaDevengoFmt'] ?? '');

        $html = '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-spacing:0;border-collapse:separate">';
        $html .= self::filaBanner($titulo);
        $html .= self::filaTituloSeccion('Proceso');
        $html .= '<tr>'
            . self::celdaTarjetaProceso('Usuario', $proceso['usuario'] ?? '-')
            . self::celdaTarjetaProceso('Inicio', $proceso['inicio'] ?? '-')
            . self::celdaTarjetaProceso('Fin', $proceso['fin'] ?? '-')
            . '</tr>';
        $html .= '<tr>'
            . self::celdaTarjetaProceso('Registros', self::fmtEntero($proceso['registros'] ?? 0))
            . self::celdaTarjetaProceso('Estatus', $proceso['estatus'] ?? '-', 2)
            . '</tr>';
        $html .= self::filaSeparador();
        $html .= self::filaTituloSeccion('Pagos del día');
        $html .= '<tr>'
            . self::celdaTarjetaMetrica('Total', $pagos['total'] ?? [])
            . self::celdaTarjetaMetrica('Pendientes', $pagos['pendientes'] ?? [])
            . self::celdaTarjetaMetrica('Aplicados', $pagos['aplicados'] ?? [])
            . '</tr>';
        $html .= '<tr>'
            . self::celdaTarjetaMetrica('Pagos', $pagos['pagos'] ?? [])
            . self::celdaTarjetaMetrica('Garantías', $pagos['garantias'] ?? [])
            . self::celdaTarjetaMetrica('Incidencias', $pagos['incidencias'] ?? [])
            . '</tr>';
        $html .= self::filaSeparador();
        $html .= self::filaTituloSeccion('Conciliación');
        $html .= '<tr>'
            . self::celdaTarjetaMetrica('Pendientes', $conc['pendientes'] ?? [])
            . self::celdaTarjetaMetrica('Conciliados', $conc['conciliados'] ?? [])
            . '<td style="width:33.33%;padding:0 0 12px 6px"></td>'
            . '</tr>';
        $html .= self::filaSeparador();
        $html .= self::filaTituloSeccion($titDevengo);
        $html .= '<tr>'
            . self::celdaTarjetaMetrica('Créditos', [
                'registros' => (int) ($devengo['creditos'] ?? 0),
                'importe' => (float) ($devengo['monto'] ?? 0),
            ])
            . '<td style="width:33.33%;padding:0 3px 12px 3px"></td>'
            . '<td style="width:33.33%;padding:0 0 12px 6px"></td>'
            . '</tr>';
        $html .= '</table>';

        return $html;
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

    private static function fmtEntero($n)
    {
        return number_format((int) $n, 0, '.', ',');
    }

    private static function fmtMoneda($n)
    {
        return '$' . number_format((float) $n, 2, '.', ',');
    }

    private static function esc($s)
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }

    private static function filaBanner($texto)
    {
        return '<tr><td colspan="3"><div style="background:linear-gradient(180deg,#f8fbff 0%,#eef4fb 100%);border:1px solid #dbe3ef;border-radius:14px;padding:16px;margin-bottom:18px;font-size:15px;font-weight:700;color:#0f172a">'
            . self::esc($texto) . '</div></td></tr>';
    }

    private static function filaTituloSeccion($titulo)
    {
        return '<tr><td colspan="3"><div style="font-size:13px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">'
            . self::esc($titulo) . '</div></td></tr>';
    }

    private static function filaSeparador()
    {
        return '<tr><td colspan="3" style="padding:6px 0 18px"><div style="height:1px;background:#dbe3ef"></div></td></tr>';
    }

    private static function celdaTarjetaProceso($etiqueta, $valor, $colspan = 1)
    {
        $pad = $colspan > 1 ? 'padding:0 3px 12px 3px' : 'padding:0 6px 12px 0';
        $col = $colspan > 1 ? ' colspan="' . (int) $colspan . '"' : '';
        $width = $colspan > 1 ? '' : 'width:33.33%;';

        return '<td style="' . $width . $pad . '"' . $col . '><div style="background:#f8fafc;border:1px solid #dbe3ef;border-radius:12px;padding:12px 14px">'
            . '<div style="font-size:12px;color:#64748b;margin-bottom:4px;font-weight:600">' . self::esc($etiqueta) . '</div>'
            . '<div style="font-size:14px;color:#0f172a;font-weight:700">' . self::esc($valor) . '</div></div></td>';
    }

    private static function celdaTarjetaMetrica($etiqueta, array $metrica)
    {
        $reg = self::fmtEntero($metrica['registros'] ?? 0);
        $imp = self::fmtMoneda($metrica['importe'] ?? 0);

        return '<td style="width:33.33%;padding:0 6px 12px 0"><div style="background:#f8fafc;border:1px solid #dbe3ef;border-radius:14px;padding:14px">'
            . '<div style="font-size:12px;color:#64748b;margin-bottom:6px;font-weight:600">' . self::esc($etiqueta) . '</div>'
            . '<div style="font-size:28px;line-height:1;color:#0f172a;font-weight:800;letter-spacing:-0.02em">' . $reg . '</div>'
            . '<div style="font-size:12px;color:#334155;margin-top:8px;font-weight:600">' . $imp . '</div></div></td>';
    }
}
