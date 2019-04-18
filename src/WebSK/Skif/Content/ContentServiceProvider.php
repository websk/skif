<?php

namespace WebSK\Skif\Content;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;

class ContentServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return ContentService
         */
        $container[Content::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new ContentService(
                Content::class,
                $container[Content::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return ContentRepository
         */
        $container[Content::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new ContentRepository(
                Content::class,
                $container->get(SkifServiceProvider::SKIF_DB_SERVICE_CONTAINER_ID)
            );
        };
    }
}
