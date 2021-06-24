<?php

namespace WebSK\Skif\Comment;

use Psr\Container\ContainerInterface;
use WebSK\Auth\User\UserServiceProvider;
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
        $container[CommentService::class] = function (ContainerInterface $container) {
            return new CommentService(
                Comment::class,
                $container->get(CommentRepository::class),
                CacheServiceProvider::getCacheService($container),
                UserServiceProvider::getUserService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return CommentRepository
         */
        $container[CommentRepository::class] = function (ContainerInterface $container) {
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
    public static function getCommentService(ContainerInterface $container): CommentService
    {
        return $container->get(CommentService::class);
    }
}
