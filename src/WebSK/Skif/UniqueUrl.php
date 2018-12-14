<?php

namespace WebSK\Skif;

use WebSK\Config\ConfWrapper;

/**
 * Class UniqueUrl
 * @package WebSK\Skif
 */
class UniqueUrl
{
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
}
