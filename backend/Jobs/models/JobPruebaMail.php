<?php

namespace Jobs\models;

defined('APPPATH') or define('APPPATH', dirname(__DIR__, 2) . '/App');

class JobPruebaMail
{
    public static function getUsuraio()
    {
        return ['success' => true, 'mensaje' => 'OK'];
    }
}
