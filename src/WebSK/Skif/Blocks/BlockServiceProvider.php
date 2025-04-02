<?php

namespace WebSK\Skif\Blocks;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;

class BlockServiceProvider
{

    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container): void
    {
        /**
         * @param ContainerInterface $container
         * @return BlockService
         */
        $container->set(BlockService::class, function (ContainerInterface $container) {
            return new BlockService(
                Block::class,
                $container->get(BlockRepository::class),
                CacheServiceProvider::getCacheService($container),
                $container->get(BlockRoleService::class),
            );
        });

        /**
         * @param ContainerInterface $container
         * @return BlockRepository
         */
        $container->set(BlockRepository::class, function (ContainerInterface $container) {
            return new BlockRepository(
                Block::class,
                SkifServiceProvider::getDBService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return BlockRoleService
         */
        $container->set(BlockRoleService::class, function (ContainerInterface $container) {
            return new BlockRoleService(
                BlockRole::class,
                $container->get(BlockRoleRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return BlockRoleRepository
         */
        $container->set(BlockRoleRepository::class, function (ContainerInterface $container) {
            return new BlockRoleRepository(
                BlockRole::class,
                SkifServiceProvider::getDBService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return PageRegionService
         */
        $container->set(PageRegionService::class, function (ContainerInterface $container) {
            return new PageRegionService(
                PageRegion::class,
                $container->get(PageRegionRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return PageRegionRepository
         */
        $container->set(PageRegionRepository::class, function (ContainerInterface $container) {
            return new PageRegionRepository(
                PageRegion::class,
                SkifServiceProvider::getDBService($container)
            );
        });
    }
}