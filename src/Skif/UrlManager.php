<?php
/**
 * Created by PhpStorm.
 * User: Кульков
 * Date: 22.02.14
 * Time: 17:04
 */

namespace Skif;


class UrlManager {

    const CONTINUE_ROUTING = 'CONTINUE_ROUTING';

    // текущий (т.е. послдений созданный) объект контроллера
    static public $current_controller_obj = null;

    // текущий адрес запроса
    static public $current_url = '';

    static public function getUriNoQueryString()
    {
        $parts = array_key_exists('REQUEST_URI', $_SERVER) ? explode('?', $_SERVER['REQUEST_URI']) : '';
        $parts_second = explode('&', $parts[0]);
        $uri = $parts_second[0];

        return $uri;
    }

    static public function route($url_regexp, $controller_class_name, $action_method_name, $cache_time = null, $layout_file = null)
    {
        $matches_arr = array();
        self::$current_url = self::getUriNoQueryString();

        if (!preg_match($url_regexp, self::$current_url, $matches_arr)) {
            return;
        }

        if (count($matches_arr)) {
            // убираем первый элемент массива - содержит всю сматченую строку
            array_shift($matches_arr);
        }

        $decoded_matches_arr = array();
        foreach ($matches_arr as $arg_value){
            $decoded_matches_arr[] = urldecode($arg_value);
        }

        if ($layout_file) {
            $decoded_matches_arr[] = $layout_file;
        }

        self::$current_controller_obj = new $controller_class_name();
        $action_result = call_user_func_array(array(self::$current_controller_obj, $action_method_name), $decoded_matches_arr);

        //$action_result = call_user_func_array(array($controller_class_name, $action_method_name), $decoded_matches_arr);

        if ($action_result == null){
            exit;
        }

        if ($action_result != self::CONTINUE_ROUTING){
            exit;
        }

        // сбрасываем текущий контроллер - он больше не актуален
        self::$current_controller_obj = null;
    }

    function routeRedirect($url_mask, $target_url){
        $current_url = $_SERVER['REQUEST_URI'];
        if (preg_match($url_mask, $current_url)){
            \Skif\Http::redirect($target_url);
        }
    }

    static public function getCurrentControllerObj()
    {
        return self::$current_controller_obj;
    }

    /**
     * @param $url string Url to check uniqueness
     * @return bool | string Unique url or false if there is some error
     */
    public static function getUniqueUrl($url)
    {
        $url_data_tables = array('content', 'rubrics');

        $unique_id = '';

        $new_url = $url;

        for ($i = 0; $i < 20; $i++) {
            $new_url .= $unique_id;
            $not_found_counter = 0;
            foreach ($url_data_tables as $data_table) {
                $query = 'SELECT url FROM ' . $data_table . ' WHERE url = ?';
                $found_urls = \Skif\DB\DBWrapper::readField($query, array($new_url));
                if ($found_urls) {
                    $unique_id = '-' . substr(uniqid(), 6);
                    break; // we found duplicate go to unique id generation
                }

                $not_found_counter++;
            }

            if ($not_found_counter == count($url_data_tables)) {//url not found in database
                return $new_url;
            }

        }

        return false;
    }

    public static function appendLeadingSlash($url){

        // append leading slash
        if (substr($url, 0, 5) != 'http:') {
            if (substr($url, 0, 1) != '/') {
                $url = '/' . $url;
            }
        }

        return $url;

    }
}