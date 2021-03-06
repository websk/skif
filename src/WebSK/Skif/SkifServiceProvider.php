<?php

namespace WebSK\Skif;

use Psr\Container\ContainerInterface;
use WebSK\DB\DBConnectorMySQL;
use WebSK\DB\DBService;
use WebSK\DB\DBSettings;

/**
 * Class SkifServiceProvider
 * @package WebSK\Skif
 */
class SkifServiceProvider
{
    const DUMP_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dumps' . DIRECTORY_SEPARATOR . 'db_skif.sql';
    public const SKIF_DB_SERVICE_CONTAINER_ID = 'skif.db_service';
    public const DB_ID = 'db_skif';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container[self::SKIF_DB_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            $db_config = $container['settings']['db'][self::DB_ID];

            $db_connector = new DBConnectorMySQL(
                $db_config['host'],
                $db_config['db_name'],
                $db_config['user'],
                $db_config['password']
            );

            $db_settings = new DBSettings(
                'mysql'
            );

            return new DBService($db_connector, $db_settings);
        };
    }

    /**
     * @param ContainerInterface $container
     * @return DBService
     */
    public static function getDBService(ContainerInterface $container)
    {
        return $container->get(SkifServiceProvider::SKIF_DB_SERVICE_CONTAINER_ID);
    }
}
