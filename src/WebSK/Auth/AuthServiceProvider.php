<?php

namespace WebSK\Auth;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\DB\DBConnectorMySQL;
use WebSK\DB\DBService;
use WebSK\DB\DBSettings;
use WebSK\Auth\Users\UsersServiceProvider;

/**
 * Class AuthServiceProvider
 * @package WebSK\Auth
 */
class AuthServiceProvider
{
    const AUTH_SERVICE_CONTAINER_ID = 'auth_service_container_id';
    const DB_SERVICE_CONTAINER_ID = 'auth.db_service';
    const DB_ID = 'db_id';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return DBService
         */
        $container[AuthServiceProvider::DB_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            $db_config = $container['settings']['db'][AuthServiceProvider::DB_ID];

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

        /**
         * @param ContainerInterface $container
         * @return AuthService
         */
        $container[self::AUTH_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new AuthService(
                UsersServiceProvider::getUserService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return SessionsService
         */
        $container[Sessions::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new SessionsService(
                Sessions::class,
                $container->get(Sessions::ENTITY_REPOSITORY_CONTAINER_ID),
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return SessionsRepository
         */
        $container[Sessions::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new SessionsRepository(
                Sessions::class,
                $container->get(self::DB_SERVICE_CONTAINER_ID)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return AuthService
     */
    public static function getAuthService(ContainerInterface $container)
    {
        return $container->get(self::AUTH_SERVICE_CONTAINER_ID);
    }

    /**
     * @param ContainerInterface $container
     * @return SessionsService
     */
    public static function getSessionService(ContainerInterface $container)
    {
        return $container->get(Sessions::ENTITY_SERVICE_CONTAINER_ID);
    }
}
