<?php

namespace Skif\DB;

/**
 * Class DB
 * @package DB
 * Represents a single database connection.
 */
class DB
{
    /**
     * Throws PDOException on failure.
     * @var PDO|null
     */
    public $pdo = null;

    public function __construct($db_conf_arr)
    {
        $this->pdo = new \PDO('mysql:host=' . $db_conf_arr['host'] . ';dbname=' . $db_conf_arr['db_name'], $db_conf_arr['user'], $db_conf_arr['pass']);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->pdo->query('SET NAMES utf8');
    }

    /**
     * Throws PDOException on failure.
     * @param $query
     * @param array $params_arr
     */
    public function query($query, $params_arr = array())
    {
        $statement_obj = $this->pdo->prepare($query);

        if (!$statement_obj->execute($params_arr)) {
            throw new \Exception('query execute failed');
        }

        return $statement_obj;
    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}