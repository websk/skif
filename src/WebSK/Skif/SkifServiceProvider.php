<?php

namespace WebSK\Skif;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServerSettings;
use WebSK\Cache\CacheService;
use Websk\Cache\Engines\CacheEngineInterface;
use Websk\DB\DBConnectorMySQL;
use Websk\DB\DBService;
use Websk\DB\DBSettings;

class SkifServiceProvider
{
    public const SKIF_CACHE_SERVICE = 'skif.cache_service';
    public const SKIF_DB_SERVICE = 'skif.db_service';
    public const DB_ID = 'db_skif';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return CacheService
         */
        $container[self::SKIF_CACHE_SERVICE] = function (ContainerInterface $container) {
            $cache_config = $container['settings']['cache'];

            $cache_servers_arr = [];
            foreach ($cache_config['servers'] as $server_config) {
                $cache_servers_arr[] = new CacheServerSettings($server_config['host'], $server_config['port']);
            }

            /** @var CacheEngineInterface $cache_engine_class_name */
            $cache_engine_class_name = $cache_config['engine'];
            $cache_engine = new $cache_engine_class_name($cache_servers_arr, $cache_config['cache_key_prefix']);

            return new CacheService($cache_engine);
        };

        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container[self::SKIF_DB_SERVICE] = function (ContainerInterface $container) {
            $db_config = $container['settings']['db'][self::DB_ID];

            $db_connector = new DBConnectorMySQL(
                $db_config['host'],
                $db_config['db_name'],
                $db_config['user'],
                $db_config['password']
            );

            $db_settings = new DBSettings(
                'mysql',
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'dump.sql'
            );

            return new DBService($db_connector, $db_settings);
        };
    }
}
