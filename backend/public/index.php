<?php
// Solo se reportan los errores y se ignoran las advertencias
error_reporting(E_ERROR | E_PARSE);
// error_reporting(E_ALL);

// Buffer para poder enviar solo JSON en endpoints que lo requieran
ob_start();

// Configuración de la zona horaria para contemplar horario de verano
$validaHV = new DateTime('now', new DateTimeZone('America/Mexico_City'));
if ($validaHV->format('I')) date_default_timezone_set('America/Mazatlan');
else date_default_timezone_set('America/Mexico_City');

//directorio del proyecto
define("PROJECTPATH", dirname(__DIR__));

//directorio app
define("APPPATH", PROJECTPATH . '/App');

//autoload con namespaces
function autoload_classes($class_name)
{
    $filename = PROJECTPATH . '/' . str_replace('\\', '/', $class_name) . '.php';
    if (is_file($filename)) include_once $filename;
}

//registramos el autoload autoload_classes
spl_autoload_register('autoload_classes');

//instancia de la app
$app = new \Core\App;

//lanzamos la app
$app->render();
