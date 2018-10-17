<?php

namespace Skif;

use Skif\Conf\ConfWrapper;
use Skif\DB\DBWrapper;

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
            Http::redirect($target_url);
        }
    }

    public static function getCurrentControllerObj()
    {
        return self::$current_controller_obj;
    }

    /**
     * @param $url string Url to check uniqueness
     * @return bool | string Unique url or false if there is some error
     */
    public static function getUniqueUrl($url)
    {
        $url_data_tables_arr = array('content', 'rubrics', 'form');

        $config_url_data_tables_arr = ConfWrapper::value('url_data_tables_arr');
        if ($config_url_data_tables_arr) {
            $url_data_tables_arr = array_merge($url_data_tables_arr, $config_url_data_tables_arr);
            $url_data_tables_arr = array_unique($url_data_tables_arr);
        }

        $unique_id = '';

        $new_url = $url;

        for ($i = 0; $i < 20; $i++) {
            $new_url .= $unique_id;
            $not_found_counter = 0;
            foreach ($url_data_tables_arr as $data_table) {
                $query = 'SELECT url FROM ' . $data_table . ' WHERE url = ?';
                $found_urls = DBWrapper::readField($query, array($new_url));
                if ($found_urls) {
                    $unique_id = '-' . substr(uniqid(), 6);
                    break; // we found duplicate go to unique id generation
                }

                $not_found_counter++;
            }

            if ($not_found_counter == count($url_data_tables_arr)) {//url not found in database
                return $new_url;
            }

        }

        return false;
    }

    /**
     * @param $base_url
     * @param $controller_class_name
     */
    public static function routeBasedCrud($base_url, $controller_class_name)
    {
        $current_url_no_query = UrlManager::getUriNoQueryString();

        if (!preg_match('@^' . $base_url . '?(.+)@i', $current_url_no_query, $matches_arr)) {
            return;
        }

        UrlManager::route('@^' . $base_url . '/add$@', $controller_class_name, 'addAction', 0);
        UrlManager::route('@^' . $base_url . '/create$@', $controller_class_name, 'createAction', 0);
        UrlManager::route('@^' . $base_url . '/edit/(.+)$@', $controller_class_name, 'editAction', 0);
        UrlManager::route('@^' . $base_url . '/save/(.+)$@i', $controller_class_name, 'saveAction', 0);
        UrlManager::route('@^' . $base_url . '/delete/(\d+)$@i', $controller_class_name, 'deleteAction', 0);
        UrlManager::route('@^' . $base_url . '$@i', $controller_class_name, 'listAction', 0);
    }
}