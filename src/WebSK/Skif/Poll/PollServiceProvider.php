<?php

namespace WebSK\Skif\Poll;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;

/**
 * Class PollServiceProvider
 * @package WebSK\Skif\Poll
 */
class PollServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return PollService
         */
        $container[Poll::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new PollService(
                Poll::class,
                $container[Poll::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return PollRepository
         */
        $container[Poll::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new PollRepository(
                Poll::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return PollQuestionService
         */
        $container[PollQuestion::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new PollQuestionService(
                PollQuestion::class,
                $container[PollQuestion::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return PollQuestionRepository
         */
        $container[PollQuestion::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new PollQuestionRepository(
                PollQuestion::class,
                SkifServiceProvider::getDBService($container)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return PollService
     */
    public static function getPollService(ContainerInterface $container): PollService
    {
        return $container[Poll::ENTITY_SERVICE_CONTAINER_ID];
    }

    /**
     * @param ContainerInterface $container
     * @return PollQuestionService
     */
    public static function getPollQuestionService(ContainerInterface $container): PollQuestionService
    {
        return $container[PollQuestion::ENTITY_SERVICE_CONTAINER_ID];
    }
}
