<?php

namespace WebSK\Skif\Logger;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\DB\DBConnectorMySQL;
use WebSK\DB\DBService;
use WebSK\DB\DBSettings;
use WebSK\Skif\Auth\AuthServiceProvider;
use WebSK\Skif\Logger\Entry\LoggerEntry;
use WebSK\Skif\Logger\Entry\LoggerEntryRepository;
use WebSK\Skif\Logger\Entry\LoggerEntryService;

/**
 * Class LoggerServiceProvider
 * @package WebSK\Skif\Logger
 */
class LoggerServiceProvider
{
    const DB_SERVICE_CONTAINER_ID = 'logger.db_service';
    const DB_ID = 'db_logger';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return LoggerEntryService
         */
        $container[LoggerEntry::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new LoggerEntryService(
                LoggerEntry::class,
                $container[LoggerEntry::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container),
                AuthServiceProvider::getAuthService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return LoggerEntryRepository
         */
        $container[LoggerEntry::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new LoggerEntryRepository(
                LoggerEntry::class,
                $container->get(self::DB_SERVICE_CONTAINER_ID)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container[self::DB_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
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
     * @return LoggerEntryService
     */
    public static function getEntryService(ContainerInterface $container)
    {
        return $container[LoggerEntry::ENTITY_SERVICE_CONTAINER_ID];
    }
}
