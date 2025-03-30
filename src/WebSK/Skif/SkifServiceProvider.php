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
    const string DUMP_FILE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dumps' . DIRECTORY_SEPARATOR . 'db_skif.sql';
    public const string DB_SERVICE_CONTAINER_ID = 'skif.db_service';
    public const string DB_ID = 'db_skif';

    const string SETTINGS_CONTAINER_ID = 'settings';
    const string PARAM_DB = 'db';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container): void
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container->set(self::DB_SERVICE_CONTAINER_ID, function (ContainerInterface $container) {
            $db_config = $container->get(
                self::SETTINGS_CONTAINER_ID . '.' . self::PARAM_DB . '.' . self::DB_ID
            );

            return DBServiceFactory::factoryMySQL($db_config);
        });
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
