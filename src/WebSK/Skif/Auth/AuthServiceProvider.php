<?php

namespace WebSK\Skif\Auth;

use Psr\Container\ContainerInterface;

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
}
