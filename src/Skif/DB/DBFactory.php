<?php

namespace Skif\DB;

/**
 * Class DBFactory
 * @package DB
 * A connection pool.
 */
class DBFactory {
    /**
     * @return null|\Skif\DB\DB
     */
    static public function getDB(){
        static $pdo_arr;

        // check static cache
        if (!empty($pdo_arr)){
            return $pdo_arr;
        }

        $databases_conf_arr = \Skif\Conf\ConfWrapper::value('db');

        if (!is_array($databases_conf_arr)){
            return null;
        }
        // connect
        $pdo_arr = new \Skif\DB\DB($databases_conf_arr);

        return $pdo_arr;
    }
}