<?php

namespace App\config;

defined("APPPATH") or die("Access denied");

use App\controllers\Ftp;

class FtpExplorerConfig
{
    /**
     * Permisos agregados para mostrar la opción FTP en el menú Reportes.
     */
    public static function menuPermisos()
    {
        $permisos = [];

        foreach (Ftp::getDirectoriosRaiz() as $raiz) {
            if (!empty($raiz['usuarios']) && is_array($raiz['usuarios'])) {
                $permisos = array_merge($permisos, $raiz['usuarios']);
            }
        }

        return array_values(array_unique($permisos));
    }
}
