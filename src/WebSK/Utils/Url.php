<?php

namespace WebSK\Utils;

use WebSK\Skif\ConfWrapper;
use Websk\Skif\DBWrapper;

/**
 * Class Url
 * @package WebSK\Utils
 */
class Url
{

    /**
     * @return string
     */
    public static function getUriNoQueryString()
    {
        $parts = array_key_exists('REQUEST_URI', $_SERVER) ? explode('?', $_SERVER['REQUEST_URI']) : '';
        $parts_second = explode('&', $parts[0]);
        $uri = $parts_second[0];

        return $uri;
    }

    /**
     * @param string $url Url to check uniqueness
     * @return bool|string Unique url or false if there is some error
     */
    public static function getUniqueUrl(string $url)
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
     * @param string $url
     * @return string
     */
    public static function appendLeadingSlash(string $url)
    {
        // append leading slash
        if (substr($url, 0, 5) != 'http:') {
            if (substr($url, 0, 1) != '/') {
                $url = '/' . $url;
            }
        }

        return $url;
    }

    /**
     * @param string $url
     * @return string
     */
    public static function appendHttp(string $url)
    {
        $parsed = parse_url($url);
        if (empty($parsed['scheme'])) {
            $url = 'http://' . ltrim($url, '/');
        }

        return $url;
    }
}
