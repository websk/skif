<?php

namespace WebSK\Skif\Redirect;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;

/**
 * Class RedirectServiceProvider
 * @package WebSK\Skif\Redirect
 */
class RedirectServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return RedirectService
         */
        $container[Redirect::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new RedirectService(
                Redirect::class,
                $container[Redirect::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return RedirectRepository
         */
        $container[Redirect::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new RedirectRepository(
                Redirect::class,
                SkifServiceProvider::getDBService($container)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return RedirectService
     */
    public static function getRedirectService(ContainerInterface $container): RedirectService
    {
        return $container[Redirect::ENTITY_SERVICE_CONTAINER_ID];
    }
}
