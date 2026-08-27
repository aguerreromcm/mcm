<?php

namespace App\services;

/**
 * Candado de proceso para el Job de cierre de día.
 * No escribe en BITACORA_CIERRE_DIARIO: flock se libera si el proceso muere.
 */
class CierreDiaJobLock
{
    private static $jobHandle = null;
    private static $ultimoError = '';

    /**
     * Motivo del último adquirirJob() fallido: no poder crear el candado y encontrarlo
     * tomado por otro proceso tienen consecuencias muy distintas para el operador.
     *
     * @return string
     */
    public static function ultimoError()
    {
        return self::$ultimoError;
    }

    /**
     * @return string
     */
    private static function dirLogs()
    {
        $dir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Jobs' . DIRECTORY_SEPARATOR . 'Logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
     * @return string
     */
    public static function archivoJob()
    {
        return self::dirLogs() . DIRECTORY_SEPARATOR . 'cierre_dia.job.lock';
    }

    /**
     * @return string
     */
    public static function archivoLanzamiento()
    {
        return self::dirLogs() . DIRECTORY_SEPARATOR . 'cierre_dia.launch.lock';
    }

    /**
     * Salida (stdout/stderr) del proceso del Job: única evidencia si muere antes de poder loguear.
     *
     * @return string
     */
    public static function archivoSalidaJob()
    {
        return self::dirLogs() . DIRECTORY_SEPARATOR . 'cierre_dia.job.out';
    }

    /**
     * Exclusivo no bloqueante. El Job debe conservar el handle hasta terminar.
     *
     * @return bool
     */
    public static function adquirirJob()
    {
        self::$ultimoError = '';
        if (is_resource(self::$jobHandle)) {
            return true;
        }
        $archivo = self::archivoJob();
        $fp = @fopen($archivo, 'c+');
        if (!is_resource($fp)) {
            self::$ultimoError = 'No se pudo abrir el archivo de candado (revise permisos de escritura): ' . $archivo;
            return false;
        }
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            fclose($fp);
            self::$ultimoError = 'Otro proceso mantiene el candado del Job de cierre de día.';
            return false;
        }
        ftruncate($fp, 0);
        fwrite($fp, (string) getmypid() . ' ' . date('c') . PHP_EOL);
        fflush($fp);
        self::$jobHandle = $fp;
        return true;
    }

    /**
     * @return void
     */
    public static function liberarJob()
    {
        if (!is_resource(self::$jobHandle)) {
            return;
        }
        flock(self::$jobHandle, LOCK_UN);
        fclose(self::$jobHandle);
        self::$jobHandle = null;
    }

    /**
     * True si otro proceso tiene el Job (consulta desde la app web, no desde el Job).
     *
     * @return bool
     */
    public static function jobActivo()
    {
        $path = self::archivoJob();
        if (!is_file($path)) {
            return false;
        }
        $fp = @fopen($path, 'c+');
        if (!is_resource($fp)) {
            return false;
        }
        $libre = flock($fp, LOCK_EX | LOCK_NB);
        if ($libre) {
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return !$libre;
    }

    /**
     * Serializa ProcesaCierreDiario para que dos clics no lancen dos php.exe.
     *
     * @return resource|null
     */
    public static function adquirirLanzamiento()
    {
        $fp = @fopen(self::archivoLanzamiento(), 'c+');
        if (!is_resource($fp)) {
            return null;
        }
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            fclose($fp);
            return null;
        }
        return $fp;
    }

    /**
     * @param resource|null $fp
     * @return void
     */
    public static function liberarLanzamiento($fp)
    {
        if (!is_resource($fp)) {
            return;
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * Espera a que el Job tome el candado (arranque del proceso).
     *
     * @param float $segundosMax
     * @return bool
     */
    public static function esperarHastaActivo($segundosMax = 5.0)
    {
        $limite = microtime(true) + (float) $segundosMax;
        do {
            if (self::jobActivo()) {
                return true;
            }
            usleep(100000);
        } while (microtime(true) < $limite);

        return self::jobActivo();
    }
}
