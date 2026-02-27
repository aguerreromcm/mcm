<?php

namespace Core;

defined("APPPATH") or die("Access denied");

use \Core\App;

class MasterDom
{

    static $_dominio = 'coinkcoink.com';
    static $_data;
    static $_imgProductos = '/img/';
    static $_imgTiendas = '/tiendas/';
    static $_imgMarca = 'https://s3-us-west-2.amazonaws.com/ecommercee/';

    public function curlPost($data)
    {

        $url = 'localhost/testing.php';

        $dato = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dato);
        //execute
        $output = curl_exec($ch);
        if ($output === FALSE) {
            echo "cURL Error: " . curl_error($ch);
            return -1;
        }
        curl_close($ch);
        return $output;
    }

    public static function procesoExcel($method = 'getColumna', $nombre = false, $columna = false)
    {
        $complemento = '';
        if ($method == 'getColumna' || $method == 'completeArray')
            //$complemento = "ProcesoExcel::".$method."(\"".$nombre."\",\"".$columna."\")";
            $complemento = "ProcesoExcel::$method('$nombre','$columna')";
        else
            //$complemento = "ProcesoExcel::".$method."(\"".$nombre."\")";
            $complemento = "ProcesoExcel::$method.('$nombre')";

        $ruta = dirname(__FILE__) . '\..\App\controllers\ProcesoExcel.php';
        //$comando = "php -r"." 'include \"/home/smsmkt/App/PHPExcel/ProcesoExcel.php\"; ".$complemento.";'";
        //$comando = "C:\wamp\bin\php\php5.6.31\php.exe -r"." 'include \"$ruta\"; ".$complemento.";'";
        $comando = "C:\wamp\bin\php\php5.6.31\php.exe -r" . " \"include '$ruta'; " . $complemento . ";\"";
        //echo $comando;
        $excel = shell_exec($comando);

        return $excel;
    }

    public static function moverDirectorio($file, $customer, $preijo = 'cso')
    {

        $filename = $file['name'];
        if (empty($filename) || empty($customer))
            return false;

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $ext = strtolower($ext);

        $name = $preijo . '-' . strtotime("now") . '-' . $customer . '.' . $ext;
        $target = dirname(__FILE__) . '/../App/PHPExcel/archivos/' . $name;
        //$target = self::$_target.$name;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return array('ext' => $ext, 'nombre' => $name);
        } else {
            return false;
        }
    }

    function getIPClient()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }

    public static function getSesion($campo)
    {
        return $_SESSION[$campo];
    }

    public static function getPathImage()
    {
        return "http://{$_SERVER['HTTP_HOST']}/GetImage/format/";
    }

    public static function userAgent($redireccin = 'movil')
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {

            $uri = ($_SERVER['REQUEST_URI'] == '') ? '/ofertas/' : $_SERVER['REQUEST_URI'];
            header("Location: http://m.coinkcoink.com.mx$uri");
            exit();
        }
    }

    public static function userAgentMovil($redireccin = 'movil')
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        if (!preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {

            $uri = ($_SERVER['REQUEST_URI'] == '') ? '/ofertas/' : $_SERVER['REQUEST_URI'];
            header("Location: http://www.coinkcoink.com.mx$uri");
            exit();
        }
    }

    public static function mensajeError()
    {
        $html = <<<HTML
        <META HTTP-EQUIV="Refresh" Content="2; URL=/">
        <section class="container">
                <section class="row">
                <section class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                    <h1>Lo sentimos ocurrió un error. Entre las causas el registro puede estar repetido, algun error en la base de datos, etc;  Favor de contactar al departamento de sistemas.</h1>
                    <img class="img-responsive center-block" src="/img/not_found.png" alt=""/>
                    <h2><a href="/">Regresar</a></h2>
                </section>
            </section>
        </section>
        HTML;
        return $html;
    }

    public static function setTituloIdWeb($titulo, $id)
    {
        return trim(self::setTituloWeb($titulo) . '-' . $id);
    }

    public static function getIdTitle($titulo)
    {
        return array_pop(explode('-', $titulo));
    }

    public static function noAcentos($string)
    {
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array(
                "\\",
                "¨",
                "º",
                "~",
                "#",
                "@",
                "|",
                "!",
                "\"",
                "·",
                "$",
                "%",
                "&",
                "/",
                "(",
                ")",
                "?",
                "'",
                "¡",
                "¿",
                "[",
                "^",
                "`",
                "]",
                "+",
                "}",
                "{",
                "¨",
                "´",
                ">",
                "< ",
                ";",
                ",",
                ":",
                "."
            ),
            '',
            $string
        );

        return $string;
    }

    public static function limpiaCadena($string)
    {

        $string = preg_replace(
            array(
                '/\bante\b/',
                '/\bbajo\b/',
                '/\bcabe\b/',
                '/\bcon\b/',
                '/\bcontra\b/',
                '/\bde\b/',
                '/\bdesde\b/',
                '/\ben\b/',
                '/\bentra\b/',
                '/\bhacia\b/',
                '/\bhasta\b/',
                '/\bpara\b/',
                '/\bpor\b/',
                '/\bsegun\b/',
                '/\bsin\b/',
                '/\bsobre\b/',
                '/\btras\b/',
                '/\bnada\b/'
            ),
            '',
            $string
        );

        $string = preg_replace(
            array(
                '/\bel\b/',
                '/\bla\b/',
                '/\blo\b/',
                '/\bal\b/',
                '/\blos\b/',
                '/\blas\b/',
                '/\bdel\b/',
                '/\bun\b/',
                '/\bunos\b/',
                '/\buna\b/',
                '/\bunas\b/',
                '/\bpor\b/',
                '/\bsegun\b/',
                '/\bsin\b/',
                '/\bsobre\b/',
                '/\besta\b/',
                '/\bestas\b/',
                '/\bese\b/',
                '/\besos\b/'
            ),
            '',
            $string
        );

        $string = preg_replace(
            array(
                '/\by\b/',
                '/\bcomo\b/',
                '/\bpara\b/',
                '/\bcon\b/',
                '/\bdonde\b/',
                '/\bquien\b/',
                '/\bcuando\b/',
                '/\bque\b/',
                '/\bcual\b/',
                '/\bcuales\b/',
                '/\btodo\b/',
                '/\bpara que\b/',
                '/\bporque\b/',
                '/\bpor que\b/',
                '/\bsobre\b/',
                '/\ba\b/',
                '/\be\b/',
                '/\bi\b/',
                '/\bo\b/',
                '/\bu\b/'
            ),
            '',
            $string
        );

        return $string;
    }

    public static function noSoloAcentos($string)
    {
        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        return $string;
    }

    public static function getTituloWeb($value)
    {
        return str_replace('-', ' ', $value);
    }

    public static function setTituloWeb($value)
    {
        return strtolower(self::noAcentos(str_replace(' ', '-', $value)));
    }

    public static function getUriPage()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function getParams()
    {
        return self::$_data;
    }

    /*SHOW DATA VIEW*/
    public static function setParams($key, $value)
    {
        self::$_data[$key] = $value;
    }

    public static function getData($value)
    {
        if (!self::whiteListeIp())
            return false;

        $data = '';
        if (isset($_GET[$value])) {
            $data = self::cleanData($_GET[$value]);
        } else if (isset($_POST[$value])) {
            $data = self::cleanData($_POST[$value]);
        } else {
            if (isset($_FILES[$value])) {
                $data = $_FILES[$value];
            } else {
                $data = '';
            }
        }
        return $data;
    }

    public static function getDataAll($value)
    {
        if (!self::whiteListeIp())
            return false;

        $data = '';
        if (isset($_GET[$value]))
            $data = $_GET[$value];
        else if (isset($_POST[$value]))
            $data = $_POST[$value];
        else
            $data = '';

        return $data;
    }

    public static function cleanData($value)
    {
        $clean = strip_tags($value);
        return htmlentities($clean);
    }

    public static function setCookies($name, $value, $dia = 10)
    {
        if (!self::whiteListeIp())
            return false;

        $dias = (86400 * $dia);
        try {
            setcookie($name, $value, time() + ($dias), "/", $_SERVER["HTTP_HOST"], isset($_SERVER["HTTPS"]), true);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getCookies($value)
    {
        if (!self::whiteListeIp())
            return false;

        if (isset($_COOKIE[$value]))
            return $_COOKIE[$value];

        return false;
    }

    public static function deleteCookies($value)
    {
        if (!self::whiteListeIp())
            return false;

        try {
            unset($_COOKIE[$value]);
            setcookie($value, '', time() - 86400, "/", $_SERVER["HTTP_HOST"], isset($_SERVER["HTTPS"]), true);
            unset($_COOKIE[$value]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function setParamSecure($value)
    {
        $string = 'COINK' . $value . 'COINK' . date('Y-m-d') . 'COINK';
        $str = base64_encode($string);

        return $str;
    }

    public static function getParamSecure($value)
    {
        if ($value == '')
            return false;
        $string = base64_decode($value);
        $str = explode('COINK', $string);
        $key = (int)$str[1];
        $fecha = (string)$str[2];
        if (!$key)
            return false;

        if ((strtotime($fecha) >= strtotime('-1 month')) && strtotime($fecha) <= strtotime(date('Y-m-d')))
            $validate = true;
        else
            return false;

        return $key;
    }

    /**
     * Unaccent the input string string. An example string like `ÀØėÿᾜὨζὅБю`
     * will be translated to `AOeyIOzoBY`. More complete than :
     *   strtr( (string)$str,
     *          "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
     *          "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn" );
     *
     * @param $str input string
     * @param $utf8 if null, function will detect input string encoding
     * @return string input string without accent
     */
    public static function removeAccents($str, $utf8 = true)
    {
        $str = (string)$str;
        if (is_null($utf8)) {
            if (!function_exists('mb_detect_encoding')) {
                $utf8 = (strtolower(mb_detect_encoding($str)) == 'utf-8');
            } else {
                $length = strlen($str);
                $utf8 = true;
                for ($i = 0; $i < $length; $i++) {
                    $c = ord($str[$i]);
                    if ($c < 0x80) $n = 0; # 0bbbbbbb
                    elseif (($c & 0xE0) == 0xC0) $n = 1; # 110bbbbb
                    elseif (($c & 0xF0) == 0xE0) $n = 2; # 1110bbbb
                    elseif (($c & 0xF8) == 0xF0) $n = 3; # 11110bbb
                    elseif (($c & 0xFC) == 0xF8) $n = 4; # 111110bb
                    elseif (($c & 0xFE) == 0xFC) $n = 5; # 1111110b
                    else return false; # Does not match any model
                    for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
                        if ((++$i == $length)
                            || ((ord($str[$i]) & 0xC0) != 0x80)
                        ) {
                            $utf8 = false;
                            break;
                        }
                    }
                }
            }
        }

        if (!$utf8)
            $str = utf8_encode($str);
        $transliteration = array(
            'Ĳ' => 'I',
            'Ö' => 'O',
            'Œ' => 'O',
            'Ü' => 'U',
            'ä' => 'a',
            'æ' => 'a',
            'ĳ' => 'i',
            'ö' => 'o',
            'œ' => 'o',
            'ü' => 'u',
            'ß' => 's',
            'ſ' => 's',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ā' => 'A',
            'Ą' => 'A',
            'Ă' => 'A',
            'Ç' => 'C',
            'Ć' => 'C',
            'Č' => 'C',
            'Ĉ' => 'C',
            'Ċ' => 'C',
            'Ď' => 'D',
            'Đ' => 'D',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ē' => 'E',
            'Ę' => 'E',
            'Ě' => 'E',
            'Ĕ' => 'E',
            'Ė' => 'E',
            'Ĝ' => 'G',
            'Ğ' => 'G',
            'Ġ' => 'G',
            'Ģ' => 'G',
            'Ĥ' => 'H',
            'Ħ' => 'H',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ī' => 'I',
            'Ĩ' => 'I',
            'Ĭ' => 'I',
            'Į' => 'I',
            'İ' => 'I',
            'Ĵ' => 'J',
            'Ķ' => 'K',
            'Ľ' => 'K',
            'Ĺ' => 'K',
            'Ļ' => 'K',
            'Ŀ' => 'K',
            'Ł' => 'L',
            'Ñ' => 'N',
            'Ń' => 'N',
            'Ň' => 'N',
            'Ņ' => 'N',
            'Ŋ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ø' => 'O',
            'Ō' => 'O',
            'Ő' => 'O',
            'Ŏ' => 'O',
            'Ŕ' => 'R',
            'Ř' => 'R',
            'Ŗ' => 'R',
            'Ś' => 'S',
            'Ş' => 'S',
            'Ŝ' => 'S',
            'Ș' => 'S',
            'Š' => 'S',
            'Ť' => 'T',
            'Ţ' => 'T',
            'Ŧ' => 'T',
            'Ț' => 'T',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ū' => 'U',
            'Ů' => 'U',
            'Ű' => 'U',
            'Ŭ' => 'U',
            'Ũ' => 'U',
            'Ų' => 'U',
            'Ŵ' => 'W',
            'Ŷ' => 'Y',
            'Ÿ' => 'Y',
            'Ý' => 'Y',
            'Ź' => 'Z',
            'Ż' => 'Z',
            'Ž' => 'Z',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ā' => 'a',
            'ą' => 'a',
            'ă' => 'a',
            'å' => 'a',
            'ç' => 'c',
            'ć' => 'c',
            'č' => 'c',
            'ĉ' => 'c',
            'ċ' => 'c',
            'ď' => 'd',
            'đ' => 'd',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ē' => 'e',
            'ę' => 'e',
            'ě' => 'e',
            'ĕ' => 'e',
            'ė' => 'e',
            'ƒ' => 'f',
            'ĝ' => 'g',
            'ğ' => 'g',
            'ġ' => 'g',
            'ģ' => 'g',
            'ĥ' => 'h',
            'ħ' => 'h',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ī' => 'i',
            'ĩ' => 'i',
            'ĭ' => 'i',
            'į' => 'i',
            'ı' => 'i',
            'ĵ' => 'j',
            'ķ' => 'k',
            'ĸ' => 'k',
            'ł' => 'l',
            'ľ' => 'l',
            'ĺ' => 'l',
            'ļ' => 'l',
            'ŀ' => 'l',
            'ñ' => 'n',
            'ń' => 'n',
            'ň' => 'n',
            'ņ' => 'n',
            'ŉ' => 'n',
            'ŋ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ø' => 'o',
            'ō' => 'o',
            'ő' => 'o',
            'ŏ' => 'o',
            'ŕ' => 'r',
            'ř' => 'r',
            'ŗ' => 'r',
            'ś' => 's',
            'š' => 's',
            'ť' => 't',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ū' => 'u',
            'ů' => 'u',
            'ű' => 'u',
            'ŭ' => 'u',
            'ũ' => 'u',
            'ų' => 'u',
            'ŵ' => 'w',
            'ÿ' => 'y',
            'ý' => 'y',
            'ŷ' => 'y',
            'ż' => 'z',
            'ź' => 'z',
            'ž' => 'z',
            'Α' => 'A',
            'Ά' => 'A',
            'Ἀ' => 'A',
            'Ἁ' => 'A',
            'Ἂ' => 'A',
            'Ἃ' => 'A',
            'Ἄ' => 'A',
            'Ἅ' => 'A',
            'Ἆ' => 'A',
            'Ἇ' => 'A',
            'ᾈ' => 'A',
            'ᾉ' => 'A',
            'ᾊ' => 'A',
            'ᾋ' => 'A',
            'ᾌ' => 'A',
            'ᾍ' => 'A',
            'ᾎ' => 'A',
            'ᾏ' => 'A',
            'Ᾰ' => 'A',
            'Ᾱ' => 'A',
            'Ὰ' => 'A',
            'ᾼ' => 'A',
            'Β' => 'B',
            'Γ' => 'G',
            'Δ' => 'D',
            'Ε' => 'E',
            'Έ' => 'E',
            'Ἐ' => 'E',
            'Ἑ' => 'E',
            'Ἒ' => 'E',
            'Ἓ' => 'E',
            'Ἔ' => 'E',
            'Ἕ' => 'E',
            'Ὲ' => 'E',
            'Ζ' => 'Z',
            'Η' => 'I',
            'Ή' => 'I',
            'Ἠ' => 'I',
            'Ἡ' => 'I',
            'Ἢ' => 'I',
            'Ἣ' => 'I',
            'Ἤ' => 'I',
            'Ἥ' => 'I',
            'Ἦ' => 'I',
            'Ἧ' => 'I',
            'ᾘ' => 'I',
            'ᾙ' => 'I',
            'ᾚ' => 'I',
            'ᾛ' => 'I',
            'ᾜ' => 'I',
            'ᾝ' => 'I',
            'ᾞ' => 'I',
            'ᾟ' => 'I',
            'Ὴ' => 'I',
            'ῌ' => 'I',
            'Θ' => 'T',
            'Ι' => 'I',
            'Ί' => 'I',
            'Ϊ' => 'I',
            'Ἰ' => 'I',
            'Ἱ' => 'I',
            'Ἲ' => 'I',
            'Ἳ' => 'I',
            'Ἴ' => 'I',
            'Ἵ' => 'I',
            'Ἶ' => 'I',
            'Ἷ' => 'I',
            'Ῐ' => 'I',
            'Ῑ' => 'I',
            'Ὶ' => 'I',
            'Κ' => 'K',
            'Λ' => 'L',
            'Μ' => 'M',
            'Ν' => 'N',
            'Ξ' => 'K',
            'Ο' => 'O',
            'Ό' => 'O',
            'Ὀ' => 'O',
            'Ὁ' => 'O',
            'Ὂ' => 'O',
            'Ὃ' => 'O',
            'Ὄ' => 'O',
            'Ὅ' => 'O',
            'Ὸ' => 'O',
            'Π' => 'P',
            'Ρ' => 'R',
            'Ῥ' => 'R',
            'Σ' => 'S',
            'Τ' => 'T',
            'Υ' => 'Y',
            'Ύ' => 'Y',
            'Ϋ' => 'Y',
            'Ὑ' => 'Y',
            'Ὓ' => 'Y',
            'Ὕ' => 'Y',
            'Ὗ' => 'Y',
            'Ῠ' => 'Y',
            'Ῡ' => 'Y',
            'Ὺ' => 'Y',
            'Φ' => 'F',
            'Χ' => 'X',
            'Ψ' => 'P',
            'Ω' => 'O',
            'Ώ' => 'O',
            'Ὠ' => 'O',
            'Ὡ' => 'O',
            'Ὢ' => 'O',
            'Ὣ' => 'O',
            'Ὤ' => 'O',
            'Ὥ' => 'O',
            'Ὦ' => 'O',
            'Ὧ' => 'O',
            'ᾨ' => 'O',
            'ᾩ' => 'O',
            'ᾪ' => 'O',
            'ᾫ' => 'O',
            'ᾬ' => 'O',
            'ᾭ' => 'O',
            'ᾮ' => 'O',
            'ᾯ' => 'O',
            'Ὼ' => 'O',
            'ῼ' => 'O',
            'α' => 'a',
            'ά' => 'a',
            'ἀ' => 'a',
            'ἁ' => 'a',
            'ἂ' => 'a',
            'ἃ' => 'a',
            'ἄ' => 'a',
            'ἅ' => 'a',
            'ἆ' => 'a',
            'ἇ' => 'a',
            'ᾀ' => 'a',
            'ᾁ' => 'a',
            'ᾂ' => 'a',
            'ᾃ' => 'a',
            'ᾄ' => 'a',
            'ᾅ' => 'a',
            'ᾆ' => 'a',
            'ᾇ' => 'a',
            'ὰ' => 'a',
            'ᾰ' => 'a',
            'ᾱ' => 'a',
            'ᾲ' => 'a',
            'ᾳ' => 'a',
            'ᾴ' => 'a',
            'ᾶ' => 'a',
            'ᾷ' => 'a',
            'β' => 'b',
            'γ' => 'g',
            'δ' => 'd',
            'ε' => 'e',
            'έ' => 'e',
            'ἐ' => 'e',
            'ἑ' => 'e',
            'ἒ' => 'e',
            'ἓ' => 'e',
            'ἔ' => 'e',
            'ἕ' => 'e',
            'ὲ' => 'e',
            'ζ' => 'z',
            'η' => 'i',
            'ή' => 'i',
            'ἠ' => 'i',
            'ἡ' => 'i',
            'ἢ' => 'i',
            'ἣ' => 'i',
            'ἤ' => 'i',
            'ἥ' => 'i',
            'ἦ' => 'i',
            'ἧ' => 'i',
            'ᾐ' => 'i',
            'ᾑ' => 'i',
            'ᾒ' => 'i',
            'ᾓ' => 'i',
            'ᾔ' => 'i',
            'ᾕ' => 'i',
            'ᾖ' => 'i',
            'ᾗ' => 'i',
            'ὴ' => 'i',
            'ῂ' => 'i',
            'ῃ' => 'i',
            'ῄ' => 'i',
            'ῆ' => 'i',
            'ῇ' => 'i',
            'θ' => 't',
            'ι' => 'i',
            'ί' => 'i',
            'ϊ' => 'i',
            'ΐ' => 'i',
            'ἰ' => 'i',
            'ἱ' => 'i',
            'ἲ' => 'i',
            'ἳ' => 'i',
            'ἴ' => 'i',
            'ἵ' => 'i',
            'ἶ' => 'i',
            'ἷ' => 'i',
            'ὶ' => 'i',
            'ῐ' => 'i',
            'ῑ' => 'i',
            'ῒ' => 'i',
            'ῖ' => 'i',
            'ῗ' => 'i',
            'κ' => 'k',
            'λ' => 'l',
            'μ' => 'm',
            'ν' => 'n',
            'ξ' => 'k',
            'ο' => 'o',
            'ό' => 'o',
            'ὀ' => 'o',
            'ὁ' => 'o',
            'ὂ' => 'o',
            'ὃ' => 'o',
            'ὄ' => 'o',
            'ὅ' => 'o',
            'ὸ' => 'o',
            'π' => 'p',
            'ρ' => 'r',
            'ῤ' => 'r',
            'ῥ' => 'r',
            'σ' => 's',
            'ς' => 's',
            'τ' => 't',
            'υ' => 'y',
            'ύ' => 'y',
            'ϋ' => 'y',
            'ΰ' => 'y',
            'ὐ' => 'y',
            'ὑ' => 'y',
            'ὒ' => 'y',
            'ὓ' => 'y',
            'ὔ' => 'y',
            'ὕ' => 'y',
            'ὖ' => 'y',
            'ὗ' => 'y',
            'ὺ' => 'y',
            'ῠ' => 'y',
            'ῡ' => 'y',
            'ῢ' => 'y',
            'ῦ' => 'y',
            'ῧ' => 'y',
            'φ' => 'f',
            'χ' => 'x',
            'ψ' => 'p',
            'ω' => 'o',
            'ώ' => 'o',
            'ὠ' => 'o',
            'ὡ' => 'o',
            'ὢ' => 'o',
            'ὣ' => 'o',
            'ὤ' => 'o',
            'ὥ' => 'o',
            'ὦ' => 'o',
            'ὧ' => 'o',
            'ᾠ' => 'o',
            'ᾡ' => 'o',
            'ᾢ' => 'o',
            'ᾣ' => 'o',
            'ᾤ' => 'o',
            'ᾥ' => 'o',
            'ᾦ' => 'o',
            'ᾧ' => 'o',
            'ὼ' => 'o',
            'ῲ' => 'o',
            'ῳ' => 'o',
            'ῴ' => 'o',
            'ῶ' => 'o',
            'ῷ' => 'o',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'E',
            'Ж' => 'Z',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'I',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'K',
            'Ц' => 'T',
            'Ч' => 'C',
            'Ш' => 'S',
            'Щ' => 'S',
            'Ы' => 'Y',
            'Э' => 'E',
            'Ю' => 'Y',
            'Я' => 'Y',
            'а' => 'A',
            'б' => 'B',
            'в' => 'V',
            'г' => 'G',
            'д' => 'D',
            'е' => 'E',
            'ё' => 'E',
            'ж' => 'Z',
            'з' => 'Z',
            'и' => 'I',
            'й' => 'I',
            'к' => 'K',
            'л' => 'L',
            'м' => 'M',
            'н' => 'N',
            'о' => 'O',
            'п' => 'P',
            'р' => 'R',
            'с' => 'S',
            'т' => 'T',
            'у' => 'U',
            'ф' => 'F',
            'х' => 'K',
            'ц' => 'T',
            'ч' => 'C',
            'ш' => 'S',
            'щ' => 'S',
            'ы' => 'Y',
            'э' => 'E',
            'ю' => 'Y',
            'я' => 'Y',
            'ð' => 'd',
            'Ð' => 'D',
            'þ' => 't',
            'Þ' => 'T',
            'ა' => 'a',
            'ბ' => 'b',
            'გ' => 'g',
            'დ' => 'd',
            'ე' => 'e',
            'ვ' => 'v',
            'ზ' => 'z',
            'თ' => 't',
            'ი' => 'i',
            'კ' => 'k',
            'ლ' => 'l',
            'მ' => 'm',
            'ნ' => 'n',
            'ო' => 'o',
            'პ' => 'p',
            'ჟ' => 'z',
            'რ' => 'r',
            'ს' => 's',
            'ტ' => 't',
            'უ' => 'u',
            'ფ' => 'p',
            'ქ' => 'k',
            'ღ' => 'g',
            'ყ' => 'q',
            'შ' => 's',
            'ჩ' => 'c',
            'ც' => 't',
            'ძ' => 'd',
            'წ' => 't',
            'ჭ' => 'c',
            'ხ' => 'k',
            'ჯ' => 'j',
            'ჰ' => 'h'
        );
        $str = str_replace(
            array_keys($transliteration),
            array_values($transliteration),
            $str
        );
        return $str;
    }

    public static function ipCoink()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(' ', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = count($ips) > 0 ? $ips[1] : $ips;
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public static function onlyUri($string)
    {
        if (preg_match("/\?(.*)/i", $string)) {
            preg_match("/(.*)\?(.*)/i", $string, $nuevo);
            return $nuevo[1];
        } else
            return $string;
    }

    public static function whiteListeIp()
    {
        return true;

        $form_uris = array(
            'ecommerce.coinkcoink.com'
        );

        if (isset($_SERVER['HTTP_REFERER']) or isset($_SERVER['SERVER_NAME'])) {
            if (!in_array($_SERVER['HTTP_REFERER'], $form_uris))
                return false;
        }
        return true;
    }

    public static function procesoAcentos($string)
    {
        if (!self::whiteListeIp())
            return false;

        $str = utf8_encode(self::noSoloAcentos(self::getDataAll($string)));
        $str = htmlentities(self::getDataAll($string), ENT_QUOTES, 'UTF-8');

        return $str;
    }

    public static function procesoAcentosNormal($string)
    {
        if (!self::whiteListeIp())
            return false;

        $str = htmlentities($string, ENT_QUOTES, 'UTF-8');

        return $str;
    }

    public static function regresoAcentos($param)
    {
        return html_entity_decode($param);
    }

    public function reformatDate($date)
    {
        $arr = explode('/', $date);
        $newDate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        return $newDate;
    }

    /*
        Obtener la fecha para procesarla texto 
        Ejm: 2017-11-01 a Lunes 01 de septiembre 2017 
    */
    public static function getFecha($fecha)
    {
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado');
        $meses = array(1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre');
        $getAnio = date('o', strtotime($fecha));
        $getMes = date("m", strtotime($fecha));
        $getDia = $dias[date('N', strtotime($fecha))];
        $dia = explode("-", $fecha);

        foreach ($meses as $key => $value) {
            if ($key == $getMes)
                $mes = $value;
        }

        return " {$getDia} {$dia[2]} de {$mes} del {$getAnio} ";
    }

    public static function alertas($caso = 'error_general', $url = '/menu', $titulo = 'Carrier', $texto = '')
    {
        $class = 'danger';
        $mensaje = '';
        if ($caso == 'success_add') {
            $mensaje = 'Success.';
            $class = 'success';
        } elseif ($caso == 'error_general')
            $mensaje = 'Lo sentimos ocurri&oacute; un error. Entre las causas el registro puede estar repetido, algun error en la base de datos, etc;  Favor de contactar al departamento de sistemas';
        elseif ($caso == 'error_carrier')
            $mensaje = 'Ocurri&oacute; un error en reinicio de plataforma.';
        elseif ($caso == 'personal')
            $mensaje = $texto;
        else
            $mensaje = 'Ocurri&oacute; algo inesperado.';

        View::set('regreso', $url);
        View::set('class', $class);
        View::set('titulo', $titulo);
        View::set('mensaje', $mensaje);
        View::render("Principal/alertas");
        exit();
    }
}
