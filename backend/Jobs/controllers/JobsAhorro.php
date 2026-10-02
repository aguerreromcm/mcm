<?php

namespace Jobs\controllers;

include_once dirname(__DIR__) . '/../Core/Job.php';
include_once dirname(__DIR__) . '/models/JobsAhorro.php';

use Core\Job;
use Jobs\models\JobsAhorro as JobsDao;

class JobsAhorro extends Job
{
    const BASE_DIAS = 365;

    public function __construct()
    {
        parent::__construct("JobsAhorro");
    }

    public function InteresAhorro($fecha = null)
    {
        self::SaveLog("Inicio -> Interés Ahorro");
        $resumen = [];
        $hoy = new \DateTime($fecha ?? "today");
        $cuentas = JobsDao::GetCuentasAhorro($hoy->format("Y-m-d"));
        if (!$cuentas["success"]) return self::SaveLog("Error al obtener las cuentas de ahorro: " . $cuentas["error"]);
        if (count($cuentas["datos"]) == 0) return self::SaveLog("No se encontraron cuentas de ahorro que cumplan el aniversario.");

        $totales = ["cuentas" => count($cuentas["datos"]), "devengos" => 0, "intereses" => 0, "errores" => 0];

        foreach ($cuentas["datos"] as $key => $cuenta) {
            $datos = [
                "cdgns" => $cuenta["CDGNS"],
                "desde" => $cuenta["INICIO"],
                "hasta" => $cuenta["FIN"],
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

            $datos = [
                "cdgns" => $cuenta["CDGNS"],
                "inicio" => $cuenta["INICIO"],
                "fin" => $cuenta["FIN"],
                "dias" => $cuenta["DIAS"],
                "tasa" => $cuenta["TASA"],
            ];

            $res = JobsDao::RegistraInteres($datos);
            $totales[$res["success"] ? "intereses" : "errores"]++;
            $resumen[] = [
                "fecha" => date("Y-m-d H:i:s"),
                "datos" => $datos,
                "RES_REGISTRA_INTERES" => $res,
            ];
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
