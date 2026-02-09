<?php
/**
 * Router para el servidor PHP integrado (php -S).
 * Emula el RewriteRule de .htaccess: redirige peticiones no estáticas a index.php?url=...
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($uri, '/');
$file = __DIR__ . '/' . $path;

// Si existe un archivo o directorio real en public/, dejar que el servidor lo sirva
if ($path !== '' && (is_file($file) || (is_dir($file) && file_exists($file . '/index.html')))) {
    return false;
}

if ($path === '') {
    $path = 'Principal';
}
$_GET['url'] = $path;
include __DIR__ . '/index.php';
