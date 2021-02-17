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
        $container[FormService::class] = function (ContainerInterface $container) {
            return new FormService(
                Form::class,
                $container[FormRepository::class],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return FormRepository
         */
        $container[FormRepository::class] = function (ContainerInterface $container) {
            return new FormRepository(
                Form::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return FormFieldService
         */
        $container[FormFieldService::class] = function (ContainerInterface $container) {
            return new FormFieldService(
                FormField::class,
                $container[FormFieldRepository::class],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return FormFieldRepository
         */
        $container[FormFieldRepository::class] = function (ContainerInterface $container) {
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
        return $container[FormService::class];
    }

    /**
     * @param ContainerInterface $container
     * @return FormFieldService
     */
    public static function getFormFieldService(ContainerInterface $container): FormFieldService
    {
        return $container[FormFieldService::class];
    }
}
