<?php

namespace WebSK\Skif\KeyValue;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;

/**
 * Class KeyValueServiceProvider
 * @package WebSK\Skif\KeyValue
 */
class KeyValueServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        $container[KeyValue::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new KeyValueService(
                KeyValue::class,
                $container->get(KeyValue::ENTITY_REPOSITORY_CONTAINER_ID),
                CacheServiceProvider::getCacheService($container)
            );
        };

        $container[KeyValue::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new KeyValueRepository(
                KeyValue::class,
                $container->get(SkifServiceProvider::SKIF_DB_SERVICE_CONTAINER_ID)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return KeyValueService
     */
    public static function getKeyValueService(ContainerInterface $container): KeyValueService
    {
        return $container[KeyValue::ENTITY_SERVICE_CONTAINER_ID];
    }
}
