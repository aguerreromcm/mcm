<?php

namespace Jobs\controllers;

include_once dirname(__DIR__) . '/../Core/Job.php';
include_once dirname(__DIR__) . '/models/JobsAhorro.php';

use Core\Job;
use Jobs\models\JobsAhorro as JobsDao;

class JobsAhorro extends Job
{
    const BASE_DIAS = 365;
    // Los depósitos se capturan hasta varios días después de su fecha de aplicación
    const DIAS_RECALCULO = 30;

    public function __construct()
    {
        parent::__construct("JobsAhorro");
    }

    public function InteresAhorro($fecha = null)
    {
        self::SaveLog("Inicio -> Interés Ahorro");
        $resumen = [];
        $cuentas = JobsDao::GetCuentasAhorro();
        if (!$cuentas["success"]) return self::SaveLog("Error al obtener las cuentas de ahorro: " . $cuentas["error"]);
        if (count($cuentas["datos"]) == 0) return self::SaveLog("No se encontraron cuentas de ahorro para calcular intereses.");

        $hoy = new \DateTime($fecha ?? "today");
        $hoy->setTime(0, 0);
        $ayer = (clone $hoy)->modify("-1 day");
        $ventana = (clone $hoy)->modify("-" . self::DIAS_RECALCULO . " days");
        $totales = ["cuentas" => count($cuentas["datos"]), "devengos" => 0, "intereses" => 0, "errores" => 0];

        foreach ($cuentas["datos"] as $key => $cuenta) {
            $inicio = new \DateTime($cuenta["INICIO"]);
            $aniversario = (clone $inicio)->modify("+1 year");
            $fin = (clone $aniversario)->modify("-1 day");
            $hasta = min($ayer, $fin);

            $desde = $inicio;
            if ($cuenta["ULTIMO_DEVENGO"]) {
                $siguiente = (new \DateTime($cuenta["ULTIMO_DEVENGO"]))->modify("+1 day");
                $desde = max($inicio, min($siguiente, $ventana));
            }

            if ($desde <= $hasta) {
                $datos = [
                    "cdgns" => $cuenta["CDGNS"],
                    "desde" => $desde->format("Y-m-d"),
                    "hasta" => $hasta->format("Y-m-d"),
                    "tasa" => $cuenta["TASA"],
                    "base" => self::BASE_DIAS,
                ];

                $res = JobsDao::RegistraDevengos($datos);
                if (!$res["success"]) {
                    $totales["errores"]++;
                    $resumen[] = [
                        "fecha" => date("Y-m-d H:i:s"),
                        "datos" => $datos,
                        "RES_REGISTRA_DEVENGOS" => $res,
                    ];
                    continue;
                }
                $totales["devengos"]++;
            }

            if ($hoy >= $aniversario) {
                $datos = [
                    "cdgns" => $cuenta["CDGNS"],
                    "inicio" => $inicio->format("Y-m-d"),
                    "fin" => $fin->format("Y-m-d"),
                    "dias" => $inicio->diff($aniversario)->days,
                    "tasa" => $cuenta["TASA"],
                ];

                $res = JobsDao::RegistraInteres($datos);
                $totales[$res["success"] ? "intereses" : "errores"]++;
                $resumen[] = [
                    "fecha" => date("Y-m-d H:i:s"),
                    "datos" => $datos,
                    "RES_REGISTRA_INTERES" => $res,
                ];
            }
        };

        self::SaveLog(json_encode($resumen)); //, JSON_PRETTY_PRINT));
        self::SaveLog(json_encode($totales));
        self::SaveLog("Finalizado -> Interés Ahorro");
    }
}

if (isset($argv[1])) {
    $jobs = new JobsAhorro();

    switch ($argv[1]) {
        case 'InteresAhorro':
            // Programar a las 12:00 pm, todos los días
            $jobs->InteresAhorro($argv[2] ?? null);
            break;
        case 'prueba_horario':
            echo date("Y-m-d H:i:s") . "\n";
            break;
        case 'help':
            echo "Los jobs disponibles son: \n";
            echo "InteresAhorro: Se recomienda se ejecute a las 12:00 pm, todos los días, después de la captura de depósitos del día anterior.\n";
            echo "    Opcional: InteresAhorro AAAA-MM-DD para procesar como si fuera esa fecha.\n";
            break;
        default:
            echo "No se encontró el job solicitado.\nEjecute 'php JobsAhorro.php help' para ver los jobs disponibles.\n";
            break;
    }
} else echo "Debe especificar el job a ejecutar.\nEjecute 'php JobsAhorro.php help' para ver los jobs disponibles.\n";
