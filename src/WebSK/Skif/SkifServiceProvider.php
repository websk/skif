<?php

namespace WebSK\Skif;

use Psr\Container\ContainerInterface;
use WebSK\DB\DBService;
use WebSK\DB\DBServiceFactory;

/**
 * Class SkifServiceProvider
 * @package WebSK\Skif
 */
class SkifServiceProvider
{
    const DUMP_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dumps' . DIRECTORY_SEPARATOR . 'db_skif.sql';
    public const DB_SERVICE_CONTAINER_ID = 'skif.db_service';
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
        $container[self::DB_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            $db_config = $container['settings']['db'][self::DB_ID];

            return DBServiceFactory::factoryMySQL($db_config);
        };
    }

    /**
     * @param ContainerInterface $container
     * @return DBService
     */
    public static function getDBService(ContainerInterface $container): DBService
    {
        return $container->get(SkifServiceProvider::DB_SERVICE_CONTAINER_ID);
    }
}
