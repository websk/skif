<?php

namespace WebSK\Skif\SiteMenu;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\Content\ContentService;
use WebSK\Skif\SkifServiceProvider;

/**
 * Class SiteMenuServiceProvider
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return SiteMenuService
         */
        $container->set(SiteMenuService::class, function (ContainerInterface $container) {
            return new SiteMenuService(
                SiteMenu::class,
                $container->get(SiteMenuRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return SiteMenuRepository
         */
        $container->set(SiteMenuRepository::class, function (ContainerInterface $container) {
            return new SiteMenuRepository(
                SiteMenu::class,
                SkifServiceProvider::getDBService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return SiteMenuItemService
         */
        $container->set(SiteMenuItemService::class, function (ContainerInterface $container) {
            return new SiteMenuItemService(
                SiteMenuItem::class,
                $container->get(SiteMenuItemRepository::class),
                CacheServiceProvider::getCacheService($container),
                $container->get(ContentService::class)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return SiteMenuItemRepository
         */
        $container->set(SiteMenuItemRepository::class, function (ContainerInterface $container) {
            return new SiteMenuItemRepository(
                SiteMenuItem::class,
                SkifServiceProvider::getDBService($container)
            );
        });
    }

    /**
     * @param ContainerInterface $container
     * @return SiteMenuItemService
     */
    public static function getSiteMenuItemService(ContainerInterface $container): SiteMenuItemService
    {
        return $container->get(SiteMenuItemService::class);
    }
}