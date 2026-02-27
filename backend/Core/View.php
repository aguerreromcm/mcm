<?php

namespace Core;

defined("APPPATH") or die("Access denied");

class View
{
    /**
     * @var
     */
    protected static $data;

    /**
     * Ruta base de vistas (absoluta si PROJECTPATH está definido, para no depender del CWD).
     * @var string
     */
    protected static function getViewsPath()
    {
        return defined('PROJECTPATH') ? (PROJECTPATH . '/App/views/') : '../App/views/';
    }

    /**
     * @var
     */
    const EXTENSION_TEMPLATES = "php";

    /**
     * [render views with data]
     * @param  [String]  [template name]
     * @return [html]    [render html]
     */
    public static function render($template)
    {
        $path = self::getViewsPath() . $template . "." . self::EXTENSION_TEMPLATES;
        if (!file_exists($path)) {
            throw new \Exception("Error: El archivo " . $path . " no existe", 1);
        }

        ob_start();
        extract(self::$data);
        include($path);
        $str = ob_get_contents();
        ob_end_clean();
        echo $str;
    }

    /**
     * [set Set Data form views]
     * @param [string] $name  [key]
     * @param [mixed] $value [value]
     */
    public static function set($name, $value)
    {
        self::$data[$name] = $value;
    }

    public static function fetch($template)
    {
        $path = self::getViewsPath() . $template . "." . self::EXTENSION_TEMPLATES;
        if (!file_exists($path)) {
            throw new \Exception("Error: El archivo " . $path . " no existe", 1);
        }

        ob_start();
        extract(self::$data);
        include($path);
        // $str = ob_get_contents();
        $str = ob_get_clean();
        return $str;
    }

    public static function getPath($template)
    {
        return self::getViewsPath() . $template . "." . self::EXTENSION_TEMPLATES;
    }
}
