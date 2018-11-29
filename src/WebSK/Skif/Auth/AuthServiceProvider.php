<?php

namespace WebSK\Skif\Auth;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;
use WebSK\Skif\Users\UsersServiceProvider;

/**
 * Class AuthServiceProvider
 * @package WebSK\Skif\Auth
 */
class AuthServiceProvider
{
    const AUTH_SERVICE_CONTAINER_ID = 'auth_service_container_id';

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
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
                $container->get(SkifServiceProvider::SKIF_DB_SERVICE)
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
