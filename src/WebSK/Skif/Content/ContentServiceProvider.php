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
    public static function register(ContainerInterface $container): void
    {
        /**
         * @param ContainerInterface $container
         * @return ContentService
         */
        $container->set(ContentService::class, function (ContainerInterface $container) {
            return new ContentService(
                Content::class,
                $container->get(ContentRepository::class),
                CacheServiceProvider::getCacheService($container),
                $container->get(ContentTypeService::class),
                $container->get(RubricService::class),
                $container->get(ContentRubricService::class),
            );
        });

        /**
         * @param ContainerInterface $container
         * @return ContentRepository
         */
        $container->set(ContentRepository::class, function (ContainerInterface $container) {
            return new ContentRepository(
                Content::class,
                SkifServiceProvider::getDBService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return ContentPhotoService
         */
        $container->set(ContentPhotoService::class, function (ContainerInterface $container) {
            return new ContentPhotoService(
                ContentPhoto::class,
                $container->get(ContentPhotoRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return ContentPhotoRepository
         */
        $container->set(ContentPhotoRepository::class, function (ContainerInterface $container) {
            return new ContentPhotoRepository(
                ContentPhoto::class,
                SkifServiceProvider::getDBService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return ContentTypeService
         */
        $container->set(ContentTypeService::class, function (ContainerInterface $container) {
            return new ContentTypeService(
                ContentType::class,
                $container->get(ContentTypeRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return ContentTypeRepository
         */
        $container->set(ContentTypeRepository::class, function (ContainerInterface $container) {
            return new ContentTypeRepository(
                ContentType::class,
                SkifServiceProvider::getDBService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return TemplateService
         */
        $container->set(TemplateService::class, function (ContainerInterface $container) {
            return new TemplateService(
                Template::class,
                $container->get(TemplateRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return TemplateRepository
         */
        $container->set(TemplateRepository::class, function (ContainerInterface $container) {
            return new TemplateRepository(
                Template::class,
                SkifServiceProvider::getDBService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return RubricService
         */
        $container->set(RubricService::class, function (ContainerInterface $container) {
            return new RubricService(
                Rubric::class,
                $container->get(RubricRepository::class),
                CacheServiceProvider::getCacheService($container),
                $container->get(ContentTypeService::class)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return RubricRepository
         */
        $container->set(RubricRepository::class, function (ContainerInterface $container) {
            return new RubricRepository(
                Rubric::class,
                SkifServiceProvider::getDBService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return ContentRubricService
         */
        $container->set(ContentRubricService::class, function (ContainerInterface $container) {
            return new ContentRubricService(
                ContentRubric::class,
                $container->get(ContentRubricRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return ContentRubricRepository
         */
        $container->set(ContentRubricRepository::class, function (ContainerInterface $container) {
            return new ContentRubricRepository(
                ContentRubric::class,
                SkifServiceProvider::getDBService($container)
            );
        });
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
     * @return ContentTypeService
     */
    public static function getContentTypeService(ContainerInterface $container): ContentTypeService
    {
        return $container->get(ContentTypeService::class);
    }

    /**
     * @param ContainerInterface $container
     * @return TemplateService
     */
    public static function getTemplateService(ContainerInterface $container): TemplateService
    {
        return $container->get(TemplateService::class);
    }

    /**
     * @param ContainerInterface $container
     * @return RubricService
     */
    public static function getRubricService(ContainerInterface $container): RubricService
    {
        return $container->get(RubricService::class);
    }

    /**
     * @param ContainerInterface $container
     * @return ContentRubricService
     */
    public static function getContentRubricService(ContainerInterface $container): ContentRubricService
    {
        return $container->get(ContentRubricService::class);
    }
}
