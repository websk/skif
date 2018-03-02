<?php

namespace Skif\DB;

/**
 * Class DBWrapper
 * @package DB
 */
class DBWrapper
{
    /**
     *
     * @param string $query
     * @param array $params_arr
     * @return \PDOStatement
     */
    static public function query($query, $params_arr = array())
    {
        $db_obj = DBFactory::getDB();
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        try {
            return $db_obj->query($query, $params_arr);
        }
        catch(\PDOException $e) {
            throw new \PDOException("\r\nUrl: ".$_SERVER['REQUEST_URI']."\r\n".$e->getMessage());
        }
    }

    static public function readObjects($query, $params_arr = array(), $field_name_for_keys = '')
    {
        $statement_obj = self::query($query, $params_arr);

        $output_arr = array();

        while (($row_obj = $statement_obj->fetchObject()) !== false) {
            if ($field_name_for_keys != '') {
                $key = $row_obj->$field_name_for_keys;
                $output_arr[$key] = $row_obj;
            }
            else {
                $output_arr[] = $row_obj;
            }
        }

        return $output_arr;
    }

    static public function readObject($query, $params_arr = array()) {
        $statement_obj = self::query($query, $params_arr);

        return $statement_obj->fetch(\PDO::FETCH_OBJ);
    }


    /**
     * @param $query
     * @param array $params_arr
     * @return array
     */
    static public function readAssoc($query, $params_arr = array())
    {
        $statement_obj = self::query($query, $params_arr);

        $output_arr = array();

        while (($row_arr = $statement_obj->fetch(\PDO::FETCH_ASSOC)) !== false) {
            $output_arr[] = $row_arr;
        }

        return $output_arr;
    }

    static public function readColumn($query, $params_arr = array())
    {
        $statement_obj = self::query($query, $params_arr);

        $output_arr = [];

        while (($field = $statement_obj->fetch(\PDO::FETCH_COLUMN)) !== false) {
            $output_arr[] = $field;
        }

        return $output_arr;
    }

    static public function readAssocRow($query, $params_arr = array())
    {
        $statement_obj = self::query($query, $params_arr);

        return $statement_obj->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Возвращает false при ошибке или если нет записей.
     * @param $query
     * @param array $params_arr
     * @return mixed
     */
    static public function readField($query, $params_arr = array())
    {
        $statement_obj = self::query($query, $params_arr);
        return $statement_obj->fetch(\PDO::FETCH_COLUMN);
    }

    static public function lastInsertId()
    {
        $db_obj = DBFactory::getDB();
        if (!$db_obj) {
            throw new \Exception('getDB failed');
        }

        return $db_obj->lastInsertId();
   }
}
