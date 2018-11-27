<?php

namespace Websk\Skif;

use Websk\DB\DBService;
use Websk\Slim\Container;

/**
 * Class DBWrapper
 * @package DB
 */
class DBWrapper
{
    /**
     * @param string $query
     * @param array $params_arr
     * @return \PDOStatement
     * @throws \Exception
     */
    public static function query(string $query, $params_arr = array())
    {
        $container = Container::self();

        /** @var DBService $db_service */
        $db_service = $container->get(SkifServiceProvider::SKIF_DB_SERVICE);

        return $db_service->query($query, $params_arr);
    }

    /**
     * @param string $query
     * @param array $params_arr
     * @param string $field_name_for_keys
     * @return array
     * @throws \Exception
     */
    public static function readObjects(string $query, array $params_arr = [], string $field_name_for_keys = '')
    {
        $container = Container::self();

        /** @var DBService $db_service */
        $db_service = $container->get(SkifServiceProvider::SKIF_DB_SERVICE);

        return $db_service->readObjects($query, $params_arr, $field_name_for_keys);
    }

    /**
     * @param string $query
     * @param array $params_arr
     * @return mixed
     * @throws \Exception
     */
    public static function readObject(string $query, array $params_arr = [])
    {
        $container = Container::self();

        /** @var DBService $db_service */
        $db_service = $container->get(SkifServiceProvider::SKIF_DB_SERVICE);

        return $db_service->readObject($query, $params_arr);
    }

    /**
     * @param string $query
     * @param array $params_arr
     * @return array
     * @throws \Exception
     */
    public static function readAssoc(string $query, array $params_arr = [])
    {
        $container = Container::self();

        /** @var DBService $db_service */
        $db_service = $container->get(SkifServiceProvider::SKIF_DB_SERVICE);

        return $db_service->readAssoc($query, $params_arr);
    }

    /**
     * @param string $query
     * @param array $params_arr
     * @return array
     * @throws \Exception
     */
    public static function readColumn(string $query, array $params_arr = [])
    {
        $container = Container::self();

        /** @var DBService $db_service */
        $db_service = $container->get(SkifServiceProvider::SKIF_DB_SERVICE);

        return $db_service->readColumn($query, $params_arr);
    }

    /**
     * @param string $query
     * @param array $params_arr
     * @return mixed
     * @throws \Exception
     */
    public static function readAssocRow(string $query, array $params_arr = [])
    {
        $container = Container::self();

        /** @var DBService $db_service */
        $db_service = $container->get(SkifServiceProvider::SKIF_DB_SERVICE);

        return $db_service->readAssocRow($query, $params_arr);
    }

    /**
     * @param string $query
     * @param array $params_arr
     * @return false|mixed
     * @throws \Exception
     */
    public static function readField(string $query, array $params_arr = [])
    {
        $container = Container::self();

        /** @var DBService $db_service */
        $db_service = $container->get(SkifServiceProvider::SKIF_DB_SERVICE);

        return $db_service->readField($query, $params_arr);
    }

    /**
     * @param string $db_sequence_name
     * @return string
     * @throws \Exception
     */
    public static function lastInsertId(string $db_sequence_name = '')
    {
        $container = Container::self();

        /** @var DBService $db_service */
        $db_service = $container->get(SkifServiceProvider::SKIF_DB_SERVICE);

        return $db_service->lastInsertId($db_sequence_name);
    }
}
