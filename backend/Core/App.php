<?php

namespace Core;

class App
{
    /**
     * @var
     */
    private $_controller;

    /**
     * @var
     */
    private $_method = "index";

    /**
     * @var
     */
    private $_params = [];

    /**
     * [$config description]
     * @var [type]
     */
    public $config = [];

    /**
     * @var
     */
    const NAMESPACE_CONTROLLERS = "App\controllers\\";

    /**
     * @var
     */
    const CONTROLLERS_PATH = "../App/controllers/";

    /**
     * [__construct description]
     */
    public function __construct()
    {
        //obtenemos la url parseada
        $url = $this->parseUrl();

        //comprobamos que exista el archivo en el directorio controllers
        if (file_exists(self::CONTROLLERS_PATH . ucfirst($url[0]) . ".php")) {
            //nombre del archivo a llamar
            $this->_controller = ucfirst($url[0]);
            //eliminamos el controlador de url, así sólo nos quedaran los parámetros del método
            unset($url[0]);
        } else {
            //include APPPATH . "/views/errors/404.php";
            header('Location: /Principal/');
            exit;
        }

        //obtenemos la clase con su espacio de nombres
        $fullClass = self::NAMESPACE_CONTROLLERS . $this->_controller;

        //asociamos la instancia a $this->_controller
        try {
            $this->_controller = new $fullClass;
        } catch (\Exception $e) {
        }

        //si existe el segundo segmento comprobamos que el método exista en esa clase
        if (isset($url[1])) {
            //aquí tenemos el método
            $this->_method = $url[1];
            if (method_exists($this->_controller, $url[1])) {
                //eliminamos el método de url, así sólo nos quedaran los parámetros del método
                unset($url[1]);
            } else {
                View::render("Principal/principal");
            }
        }
        //asociamos el resto de segmentos a $this->_params para pasarlos al método llamado, por defecto será un array vacío
        $this->_params = $url ? array_values($url) : [];
    }

    /**
     * [parseUrl Parseamos la url en trozos]
     * @return [type] [description]
     */
    public function parseUrl()
    {
        if (isset($_GET["url"])) {
            return explode("/", filter_var(rtrim($_GET["url"], "/"), FILTER_SANITIZE_URL));
        }
    }

    /**
     * [render  lanzamos el controlador/método que se ha llamado con los parámetros]
     */
    public function render()
    {
        call_user_func_array([$this->_controller, $this->_method], $this->_params);
    }

    /**
     * [getConfig Obtenemos la configuración de la app]
     * Carga por entorno (APP_ENV): si existe App/config/env/{APP_ENV}.ini se usa; si no, configuracion.ini.
     * @return [Array] [Array con la config]
     */
    public static function getConfig()
    {
        $base = dirname(__DIR__) . '/App/config';
        $env = (function () {
            if (getenv('APP_ENV') !== false) return getenv('APP_ENV');
            if (isset($_ENV['APP_ENV'])) return $_ENV['APP_ENV'];
            return '';
        })();
        if ($env !== '' && $env !== '0') {
            $envFile = $base . '/env/' . $env . '.ini';
            if (is_file($envFile)) {
                return parse_ini_file($envFile);
            }
        }
        return parse_ini_file($base . '/configuracion.ini');
    }

    /**
     * [getController Devolvemos el controlador actual]
     * @return [type] [String]
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * [getMethod Devolvemos el método actual]
     * @return [type] [String]
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * [getParams description]
     * @return [type] [Array]
     */
    public function getParams()
    {
        return $this->_params;
    }
}
