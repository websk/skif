<?php

namespace WebSK\Skif\Content;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;

/**
 * Class ContentServiceProvider
 * @package WebSK\Skif\Content
 */
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

        /**
         * @param ContainerInterface $container
         * @return ContentPhotoService
         */
        $container[ContentPhoto::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new ContentPhotoService(
                Content::class,
                $container[Content::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return ContentPhotoRepository
         */
        $container[ContentPhoto::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new ContentPhotoRepository(
                ContentPhoto::class,
                $container->get(SkifServiceProvider::SKIF_DB_SERVICE_CONTAINER_ID)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return ContentService
     */
    public static function getContentService(ContainerInterface $container)
    {
        return $container[Content::ENTITY_SERVICE_CONTAINER_ID];
    }

    /**
     * @param ContainerInterface $container
     * @return ContentPhotoService
     */
    public static function getContentPhotoService(ContainerInterface $container)
    {
        return $container[ContentPhoto::ENTITY_SERVICE_CONTAINER_ID];
    }
}
