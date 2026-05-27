<?php

namespace App\services;

defined("APPPATH") or die("Access denied");

use Core\Model;
use ZipStream\ZipStream;

class FtpExplorerService
{
    public static function obtenerRaicesParaUsuario(array $directorios, $usuario, $perfil)
    {
        $raices = [];

        foreach ($directorios as $raiz) {
            if (!self::usuarioTieneAcceso($raiz, $usuario, $perfil)) {
                continue;
            }

            $raices[] = [
                'id'       => $raiz['id'],
                'etiqueta' => $raiz['etiqueta'],
            ];
        }

        return $raices;
    }

    public static function listarContenido(array $directorios, $raizId, $rutaRelativa, $usuario, $perfil)
    {
        $resolucion = self::resolverDirectorio($directorios, $raizId, $rutaRelativa, $usuario, $perfil);
        if (!$resolucion['success']) {
            return $resolucion;
        }

        $directorio = $resolucion['datos']['ruta'];

        if (!is_dir($directorio)) {
            return Model::Responde(false, 'La carpeta no existe o no es accesible.');
        }

        $directoriosLista = [];
        $archivosLista = [];

        $elementos = @scandir($directorio);
        if ($elementos === false) {
            return Model::Responde(false, 'No se pudo leer el contenido de la carpeta.');
        }

        foreach ($elementos as $elemento) {
            if ($elemento === '.' || $elemento === '..') {
                continue;
            }

            $rutaCompleta = $directorio . DIRECTORY_SEPARATOR . $elemento;
            $rutaRel = self::rutaRelativaDesdeRaiz($resolucion['datos']['raiz'], $rutaCompleta);

            if (is_dir($rutaCompleta)) {
                $directoriosLista[] = [
                    'nombre' => $elemento,
                    'ruta'   => $rutaRel,
                ];
                continue;
            }

            if (!is_file($rutaCompleta)) {
                continue;
            }

            $archivosLista[] = [
                'nombre'    => $elemento,
                'ruta'      => $rutaRel,
                'extension' => pathinfo($elemento, PATHINFO_EXTENSION),
                'tamano'    => filesize($rutaCompleta),
                'modificado'=> date('c', filemtime($rutaCompleta)),
            ];
        }

        usort($directoriosLista, function ($a, $b) {
            return strcasecmp($a['nombre'], $b['nombre']);
        });

        usort($archivosLista, function ($a, $b) {
            return strcasecmp($a['nombre'], $b['nombre']);
        });

        return Model::Responde(true, 'OK', [
            'raiz'        => $raizId,
            'ruta'        => self::normalizarRutaRelativa($rutaRelativa),
            'directorios' => $directoriosLista,
            'archivos'    => $archivosLista,
        ]);
    }

    public static function descargarArchivos(array $directorios, $raizId, array $archivosRelativos, $usuario, $perfil)
    {
        while (ob_get_level()) {
            ob_end_clean();
        }

        $archivosRelativos = array_values(array_filter(array_map(function ($ruta) {
            return self::normalizarRutaRelativa($ruta);
        }, $archivosRelativos)));

        if ($raizId === '' || empty($archivosRelativos)) {
            self::responderErrorDescarga('Solicitud de descarga inválida.');
        }

        $raiz = self::obtenerRaiz($directorios, $raizId);
        if ($raiz === null || !self::usuarioTieneAcceso($raiz, $usuario, $perfil)) {
            self::responderErrorDescarga('No tiene permiso para descargar archivos de esta ubicación.');
        }

        $baseReal = self::rutaReal($raiz['ruta']);
        if ($baseReal === null) {
            self::responderErrorDescarga('La carpeta raíz configurada no existe.');
        }

        $rutasAbsolutas = [];
        foreach ($archivosRelativos as $relativa) {
            $absoluta = self::construirRutaAbsoluta($baseReal, $relativa);
            if ($absoluta === null || !is_file($absoluta)) {
                self::responderErrorDescarga('Uno o más archivos seleccionados no existen o no son accesibles.');
            }
            $rutasAbsolutas[] = $absoluta;
        }

        $rutasAbsolutas = array_values(array_unique($rutasAbsolutas));

        if (count($rutasAbsolutas) === 1) {
            self::enviarArchivo($rutasAbsolutas[0]);
        }

        self::enviarZipMultiple($rutasAbsolutas, $raiz);
    }

    private static function enviarZipMultiple(array $rutasAbsolutas, array $raiz)
    {
        if (!self::cargarDependenciaZipStream()) {
            self::responderErrorDescarga('No se pudo preparar la descarga múltiple.');
        }

        $nombreZip = self::construirNombreZip($raiz, count($rutasAbsolutas));
        $zip = new ZipStream(outputName: $nombreZip);

        $nombresUsados = [];
        foreach ($rutasAbsolutas as $ruta) {
            $nombre = basename($ruta);
            $nombreFinal = self::nombreUnicoEnZip($nombre, $nombresUsados);
            $nombresUsados[] = $nombreFinal;
            $zip->addFileFromPath(
                fileName: $nombreFinal,
                path: str_replace('\\', '/', $ruta)
            );
        }

        $zip->finish();
        exit;
    }

    private static function construirNombreZip(array $raiz, $totalArchivos)
    {
        $base = '';
        if (!empty($raiz['etiqueta'])) {
            $base = (string) $raiz['etiqueta'];
        } elseif (!empty($raiz['id'])) {
            $base = (string) $raiz['id'];
        } else {
            $base = 'archivos';
        }

        $base = self::normalizarParaNombreArchivo($base);
        if ($base === '') {
            $base = 'archivos';
        }

        $cantidad = max(1, (int) $totalArchivos);
        $sufijoCantidad = $cantidad . ($cantidad === 1 ? '_archivo' : '_archivos');
        $fecha = date('Y-m-d_H-i');

        return 'descarga_' . $base . '_' . $sufijoCantidad . '_' . $fecha . '.zip';
    }

    private static function normalizarParaNombreArchivo($texto)
    {
        $texto = trim((string) $texto);
        $texto = strtr($texto, [
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Ã' => 'A',
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Õ' => 'O',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ñ' => 'N', 'ñ' => 'n',
        ]);
        $texto = function_exists('iconv') ? (@iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto) ?: $texto) : $texto;
        $texto = strtolower((string) $texto);
        $texto = preg_replace('/[^a-z0-9]+/', '_', $texto);
        return trim((string) $texto, '_');
    }

    private static function cargarDependenciaZipStream()
    {
        static $cargado = false;

        if ($cargado) {
            return true;
        }

        $autoload = dirname(APPPATH) . '/libs/PhpSpreadsheet/vendor/autoload.php';
        if (!is_file($autoload)) {
            return false;
        }

        require_once $autoload;
        $cargado = true;

        return class_exists(ZipStream::class);
    }

    private static function resolverDirectorio(array $directorios, $raizId, $rutaRelativa, $usuario, $perfil)
    {
        $raiz = self::obtenerRaiz($directorios, $raizId);
        if ($raiz === null) {
            return Model::Responde(false, 'La ubicación seleccionada no está configurada.');
        }

        if (!self::usuarioTieneAcceso($raiz, $usuario, $perfil)) {
            return Model::Responde(false, 'No tiene permiso para acceder a esta ubicación.');
        }

        $baseReal = self::rutaReal($raiz['ruta']);
        if ($baseReal === null) {
            return Model::Responde(false, 'La carpeta raíz configurada no existe.');
        }

        $rutaRelativa = self::normalizarRutaRelativa($rutaRelativa);
        $rutaAbsoluta = self::construirRutaAbsoluta($baseReal, $rutaRelativa);
        if ($rutaAbsoluta === null) {
            return Model::Responde(false, 'La ruta solicitada no es válida.');
        }

        return Model::Responde(true, 'OK', [
            'raiz' => $baseReal,
            'ruta' => $rutaAbsoluta,
        ]);
    }

    private static function obtenerRaiz(array $directorios, $raizId)
    {
        foreach ($directorios as $raiz) {
            if (isset($raiz['id']) && $raiz['id'] === $raizId) {
                return $raiz;
            }
        }

        return null;
    }

    private static function usuarioTieneAcceso(array $raiz, $usuario, $perfil)
    {
        if (empty($raiz['usuarios']) || !is_array($raiz['usuarios'])) {
            return false;
        }

        return in_array($usuario, $raiz['usuarios'], true) || in_array($perfil, $raiz['usuarios'], true);
    }

    private static function normalizarRutaRelativa($ruta)
    {
        $ruta = str_replace('\\', '/', trim((string) $ruta));
        $ruta = preg_replace('#/+#', '/', $ruta);
        return trim($ruta, '/');
    }

    private static function construirRutaAbsoluta($baseReal, $rutaRelativa)
    {
        $rutaRelativa = self::normalizarRutaRelativa($rutaRelativa);
        if ($rutaRelativa === '') {
            $destino = $baseReal;
        } else {
            $partes = explode('/', $rutaRelativa);
            foreach ($partes as $parte) {
                if ($parte === '' || $parte === '.' || $parte === '..') {
                    return null;
                }
            }
            $destino = $baseReal . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rutaRelativa);
        }

        $destinoReal = self::rutaReal($destino);
        if ($destinoReal === null) {
            return null;
        }

        if (strpos($destinoReal, $baseReal) !== 0) {
            return null;
        }

        return $destinoReal;
    }

    private static function rutaReal($ruta)
    {
        $real = realpath($ruta);
        return ($real !== false) ? $real : null;
    }

    private static function rutaRelativaDesdeRaiz($baseReal, $rutaAbsoluta)
    {
        $base = rtrim(str_replace('\\', '/', $baseReal), '/');
        $ruta = str_replace('\\', '/', $rutaAbsoluta);

        if (strpos($ruta, $base) !== 0) {
            return '';
        }

        $relativa = ltrim(substr($ruta, strlen($base)), '/');
        return self::normalizarRutaRelativa($relativa);
    }

    private static function enviarArchivo($ruta)
    {
        $nombre = basename($ruta);
        $mime = self::mimeDesdeExtension($ruta);

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . filesize($ruta));
        header('Cache-Control: private, max-age=0, must-revalidate');
        readfile($ruta);
        exit;
    }

    private static function mimeDesdeExtension($ruta)
    {
        $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
        $map = [
            'pdf'  => 'application/pdf',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls'  => 'application/vnd.ms-excel',
            'csv'  => 'text/csv',
            'txt'  => 'text/plain',
            'zip'  => 'application/zip',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
        ];

        return $map[$ext] ?? 'application/octet-stream';
    }

    private static function nombreUnicoEnZip($nombre, array $nombresUsados)
    {
        if (!in_array($nombre, $nombresUsados, true)) {
            return $nombre;
        }

        $info = pathinfo($nombre);
        $base = $info['filename'] ?? $nombre;
        $ext = isset($info['extension']) && $info['extension'] !== '' ? '.' . $info['extension'] : '';
        $contador = 2;

        do {
            $candidato = $base . '_' . $contador . $ext;
            $contador++;
        } while (in_array($candidato, $nombresUsados, true));

        return $candidato;
    }

    private static function responderErrorDescarga($mensaje)
    {
        header('HTTP/1.0 400 Bad Request');
        header('Content-Type: text/plain; charset=UTF-8');
        echo $mensaje;
        exit;
    }
}
