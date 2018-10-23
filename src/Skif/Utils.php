<?php

namespace Skif;

use Websk\Skif\DBWrapper;

class Utils
{
    protected static $countries_arr;

    public static function renderPagination($current_page, $count_records, $messages_to_page = 10)
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = str_replace('&', '&amp;', $url);
        $url = str_replace('?p=' . $current_page, '', $url);
        $url = str_replace('&p=' . $current_page, '', $url);
        $url = str_replace('&amp;p=' . $current_page, '', $url);

        if (strpos($url, '?') === false) {
            $url .= '?p=';
        } else {
            $url .= '&amp;p=';
        }

        $all = intval($count_records / $messages_to_page);

        if ($messages_to_page > 1) {
            $all++;
        }

        if ($all < 1) {
            return '';
        }

        $html = '<ul class="pagination pagination-sm">';

        for ($i = 1; $i <= $all; $i++) {
            $html .= '<li ' . ($i == $current_page ? 'class="active"' : '') . '><a href="' . $url . $i . '">' . $i . '</a></li>';
        }

        $html .= '</ul>';


        return $html;
    }

    /**
     * Список стран
     * @return array
     */
    public static function getCountriesArr()
    {
        if (isset(self::$countries_arr)) {
            return self::$countries_arr;
        }

        $query = "SELECT * FROM lands ORDER BY name";
        $countries_arr = DBWrapper::readObjects($query);

        return $countries_arr;
    }

    public static function getCountryNameById($country_id)
    {
        $query = "SELECT name FROM lands WHERE id=?";
        return DBWrapper::readField($query, array($country_id));
    }

    public static function mb_str_ireplace($search, $replace, $subject, $count = -1)
    {
        mb_internal_encoding('utf-8');

        $search = is_array($search) ? array_map(create_function('$s', 'return \'#\'. preg_quote($s) .\'#uis\';'),
            $search) : '#' . preg_quote($search) . '#uis';

        return preg_replace($search, $replace, $subject, $count);
    }

    /**
     * Проверка Email
     * @param $email
     * @return bool
     */
    public static function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    /**
     * @param $obj
     * @return string
     */
    public static function getFullObjectId($obj)
    {
        if (!is_object($obj)) {
            return 'not_object';
        }

        $obj_id_parts = array();
        $obj_id_parts[] = get_class($obj);

        // TODO: заменить на проверку интерфеса?
        if (method_exists($obj, 'getId')) {
            $obj_id_parts[] = $obj->getId();
        }

        return implode('.', $obj_id_parts);
    }

    /**
     * Returns array of slash separated url parts.
     * @return array Array of url parts.
     */
    static public function url_args()
    {
        $uri_no_getform = \Skif\UrlManager::getUriNoQueryString();

        // remove "/" at the beginning to avoid empty first arg and protect from uri without leading "/"

        if (substr($uri_no_getform, 0, 1) == '/')
            $uri_no_getform = substr($uri_no_getform, 1);

        $args = explode('/', $uri_no_getform);
        return $args;
    }

    /**
     * Returns requested url part.
     * @param int $index Index of requested url part.
     * @param string $default Default value - returned when requested url part missed.
     * @return string Requested url part or default value.
     */
    static public function url_arg($index, $default = '')
    {
        $args = self::url_args();

        if (isset($args[$index]))
            return $args[$index];

        return $default;
    }

    /**
     * @param $text
     * @return string
     */
    static public function checkPlain($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Проверка на русские символы в строке
     * @param $text
     * @return int
     */
    public static function checkRussian($text)
    {
        $text = str_replace("\n", "", $text);
        $text = str_replace("\r", "", $text);
        $text = str_replace(",", "", $text);
        $text = str_replace(".", "", $text);
        $text = str_replace("!", "", $text);
        $text = str_replace("?", "", $text);
        $text = str_replace(";", "", $text);
        $text = str_replace(":", "", $text);
        $text = str_replace(")", "", $text);
        $text = str_replace("(", "", $text);
        $text = str_replace("-", "", $text);
        $text = str_replace(" ", "", $text);

        $patern = "|^[-а-я]+$|i";

        if (preg_match($patern, $text)) {
            return true;
        }

        return false;
    }

    public static function rebuildFilesArray($files_arr)
    {
        $output_files_arr = array();
        foreach ($files_arr as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $output_files_arr[$key2][$key1] = $value2;
            }
        }

        return $output_files_arr;
    }

    public static function appendLeadingSlash($url)
    {
        // append leading slash
        if (substr($url, 0, 5) != 'http:') {
            if (substr($url, 0, 1) != '/') {
                $url = '/' . $url;
            }
        }

        return $url;
    }

    public static function appendHttp($url)
    {
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            $url = 'http://' . ltrim($url, '/');
        }

        return $url;
    }

    /**
     * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
     * param  $number Integer Число на основе которого нужно сформировать окончание
     * param  $endingsArray  Array Массив слов или окончаний для чисел (1, 4, 5),
     *         например array('яблоко', 'яблока', 'яблок')
     * return string
     */
    public static function getDeclensionOfNumerals($number, $ending_array)
    {
        $number = $number % 100;
        if ($number >= 11 && $number <= 19) {
            $ending = $ending_array[2];
        } else {
            $i = $number % 10;
            switch ($i) {
                case (1):
                    $ending = $ending_array[0];
                    break;
                case (2):
                case (3):
                case (4):
                    $ending = $ending_array[1];
                    break;
                default:
                    $ending = $ending_array[2];
            }
        }

        return $ending;
    }
}