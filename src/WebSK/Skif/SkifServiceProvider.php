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
    public const SKIF_DB_SERVICE = 'skif.db_service';
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
        $container[self::SKIF_DB_SERVICE] = function (ContainerInterface $container) {
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
}
