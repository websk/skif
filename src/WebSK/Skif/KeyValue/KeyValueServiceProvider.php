<?php

namespace WebSK\Skif\KeyValue;

use Psr\Container\ContainerInterface;
use WebSK\DB\DBConnectorMySQL;
use WebSK\DB\DBService;
use WebSK\DB\DBSettings;
use WebSK\Skif\SkifApp;
use WebSK\Skif\SkifServiceProvider;

/**
 * Class KeyValueServiceProvider
 * @package WebSK\Skif\KeyValue
 */
class KeyValueServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container[KeyValueConstants::DB_SERVICE] = function (ContainerInterface $container) {
            $db_config = $container['settings']['db'][KeyValueConstants::DB_ID];

            $db_connector = new DBConnectorMySQL(
                $db_config['host'],
                $db_config['db_name'],
                $db_config['user'],
                $db_config['password']
            );

            $db_settings_arr = new DBSettings(
                'mysql',
                __DIR__ . DIRECTORY_SEPARATOR . 'dumps' . DIRECTORY_SEPARATOR . 'db_keyvalue.sql'
            );

            return new DBService($db_connector, $db_settings_arr);
        };

        $container[KeyValue::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new KeyValueService(
                KeyValue::class,
                $container->get(KeyValue::ENTITY_REPOSITORY_CONTAINER_ID),
                $container->get(SkifServiceProvider::SKIF_CACHE_SERVICE)
            );
        };

        $container[KeyValue::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new KeyValueRepository(
                KeyValue::class,
                self::getDbService($container)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return DBService
     */
    public static function getDbService(ContainerInterface $container): DBService
    {
        return $container[KeyValueConstants::DB_SERVICE];
    }

    /**
     * @param ContainerInterface $container
     * @return KeyValueService
     */
    public static function getKeyValueService(ContainerInterface $container): KeyValueService
    {
        return $container[KeyValue::ENTITY_SERVICE_CONTAINER_ID];
    }
}
