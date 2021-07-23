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
        $container[ContentService::class] = function (ContainerInterface $container) {
            return new ContentService(
                Content::class,
                $container->get(ContentRepository::class),
                CacheServiceProvider::getCacheService($container),
                self::getContentTypeService($container),
                self::getRubricService($container),
                self::getContentRubricService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return ContentRepository
         */
        $container[ContentRepository::class] = function (ContainerInterface $container) {
            return new ContentRepository(
                Content::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return ContentPhotoService
         */
        $container[ContentPhoto::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new ContentPhotoService(
                ContentPhoto::class,
                $container[ContentPhoto::ENTITY_REPOSITORY_CONTAINER_ID],
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
                SkifServiceProvider::getDBService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return ContentTypeService
         */
        $container[ContentType::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new ContentTypeService(
                ContentType::class,
                $container[ContentType::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return ContentTypeRepository
         */
        $container[ContentType::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new ContentTypeRepository(
                ContentType::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return TemplateService
         */
        $container[Template::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new TemplateService(
                Template::class,
                $container[Template::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return TemplateRepository
         */
        $container[Template::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new TemplateRepository(
                Template::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return RubricService
         */
        $container[Rubric::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new RubricService(
                Rubric::class,
                $container[Rubric::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container),
                self::getContentTypeService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return RubricRepository
         */
        $container[Rubric::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new RubricRepository(
                Rubric::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return ContentRubricService
         */
        $container[ContentRubric::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new ContentRubricService(
                ContentRubric::class,
                $container[ContentRubric::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return ContentRubricRepository
         */
        $container[ContentRubric::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new ContentRubricRepository(
                ContentRubric::class,
                SkifServiceProvider::getDBService($container)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return ContentService
     */
    public static function getContentService(ContainerInterface $container): ContentService
    {
        return $container->get(ContentService::class);
    }

    /**
     * @param ContainerInterface $container
     * @return ContentPhotoService
     */
    public static function getContentPhotoService(ContainerInterface $container): ContentPhotoService
    {
        return $container[ContentPhoto::ENTITY_SERVICE_CONTAINER_ID];
    }

    /**
     * @param ContainerInterface $container
     * @return ContentTypeService
     */
    public static function getContentTypeService(ContainerInterface $container): ContentTypeService
    {
        return $container[ContentType::ENTITY_SERVICE_CONTAINER_ID];
    }

    /**
     * @param ContainerInterface $container
     * @return TemplateService
     */
    public static function getTemplateService(ContainerInterface $container): TemplateService
    {
        return $container[Template::ENTITY_SERVICE_CONTAINER_ID];
    }

    /**
     * @param ContainerInterface $container
     * @return RubricService
     */
    public static function getRubricService(ContainerInterface $container): RubricService
    {
        return $container[Rubric::ENTITY_SERVICE_CONTAINER_ID];
    }

    /**
     * @param ContainerInterface $container
     * @return ContentRubricService
     */
    public static function getContentRubricService(ContainerInterface $container): ContentRubricService
    {
        return $container[ContentRubric::ENTITY_SERVICE_CONTAINER_ID];
    }
}
