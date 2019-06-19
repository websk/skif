<?php

namespace WebSK\Skif\Comment;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\SkifServiceProvider;

/**
 * Class CommentServiceProvider
 * @package WebSK\Skif\Comment
 */
class CommentServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return CommentService
         */
        $container[Comment::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new CommentService(
                Comment::class,
                $container[Comment::ENTITY_REPOSITORY_CONTAINER_ID],
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return CommentRepository
         */
        $container[Comment::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new CommentRepository(
                Comment::class,
                SkifServiceProvider::getDBService($container)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return CommentService
     */
    public static function getContentService(ContainerInterface $container)
    {
        return $container[Comment::ENTITY_SERVICE_CONTAINER_ID];
    }
}
