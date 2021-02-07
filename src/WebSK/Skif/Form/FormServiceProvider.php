<?php

namespace WebSK\Skif\Form;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;

/**
 * Class FormServiceProvider
 * @package WebSK\Skif\Form
 */
class FormServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return FormService
         */
        $container[Form::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new FormService(
                Form::class,
                $container[Form::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return FormRepository
         */
        $container[Form::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new FormRepository(
                Form::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return FormFieldService
         */
        $container[FormField::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new FormFieldService(
                FormField::class,
                $container[FormField::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return FormFieldRepository
         */
        $container[FormField::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new FormFieldRepository(
                FormField::class,
                SkifServiceProvider::getDBService($container)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return FormService
     */
    public static function getFormService(ContainerInterface $container): FormService
    {
        return $container[Form::ENTITY_SERVICE_CONTAINER_ID];
    }

    /**
     * @param ContainerInterface $container
     * @return FormFieldService
     */
    public static function getFormFieldService(ContainerInterface $container): FormFieldService
    {
        return $container[FormField::ENTITY_SERVICE_CONTAINER_ID];
    }
}
