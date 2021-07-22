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
        $container[PollService::class] = function (ContainerInterface $container) {
            return new PollService(
                Poll::class,
                $container->get(PollRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return PollRepository
         */
        $container[PollRepository::class] = function (ContainerInterface $container) {
            return new PollRepository(
                Poll::class,
                SkifServiceProvider::getDBService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return PollQuestionService
         */
        $container[PollQuestionService::class] = function (ContainerInterface $container) {
            return new PollQuestionService(
                PollQuestion::class,
                $container->get(PollQuestionRepository::class),
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return PollQuestionRepository
         */
        $container[PollQuestionRepository::class] = function (ContainerInterface $container) {
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
        return $container->get(PollService::class);
    }

    /**
     * @param ContainerInterface $container
     * @return PollQuestionService
     */
    public static function getPollQuestionService(ContainerInterface $container): PollQuestionService
    {
        return $container->get(PollQuestionService::class);
    }
}
