<?php

namespace WebSK\Skif\Blocks;

use Psr\Container\ContainerInterface;
use WebSK\Auth\User\UserService;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;

class BlockServiceProvider
{

    const string SETTINGS_CONTAINER_ID = 'settings';
    const string PARAM_CACHE = 'cache';

    const string PARAM_EXPIRE = 'expire';

    /** @param ContainerInterface&\ArrayAccess<string, mixed> $container */
    public static function register(ContainerInterface&\ArrayAccess $container): void
    {
        $container[BlockService::class] = function (ContainerInterface $container) {
            return new BlockService(
                Block::class,
                $container->get(BlockRepository::class),
                CacheServiceProvider::getCacheService($container),
                $container->get(BlockRoleService::class),
                $container->get(UserService::class),
                $container->get(self::SETTINGS_CONTAINER_ID . '.' . self::PARAM_CACHE . '.' . self::PARAM_EXPIRE)
            );
        };

        $container[BlockRepository::class] = function (ContainerInterface $container) {
            return new BlockRepository(
                Block::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        $container[BlockRoleService::class] = function (ContainerInterface $container) {
            return new BlockRoleService(
                BlockRole::class,
                $container->get(BlockRoleRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        };

        $container[BlockRoleRepository::class] = function (ContainerInterface $container) {
            return new BlockRoleRepository(
                BlockRole::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        $container[PageRegionService::class] = function (ContainerInterface $container) {
            return new PageRegionService(
                PageRegion::class,
                $container->get(PageRegionRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        };

        $container[PageRegionRepository::class] = function (ContainerInterface $container) {
            return new PageRegionRepository(
                PageRegion::class,
                SkifServiceProvider::getDBService($container)
            );
        };
    }
}
