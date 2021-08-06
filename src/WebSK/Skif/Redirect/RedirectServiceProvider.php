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
        $container[RedirectService::class] = function (ContainerInterface $container) {
            return new RedirectService(
                Redirect::class,
                $container->get(RedirectRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return RedirectRepository
         */
        $container[RedirectRepository::class] = function (ContainerInterface $container) {
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
        return $container->get(RedirectService::class);
    }
}
