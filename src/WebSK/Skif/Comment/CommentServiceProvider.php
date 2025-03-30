<?php

namespace WebSK\Skif\Comment;

use Psr\Container\ContainerInterface;
use WebSK\Auth\User\UserService;
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
    public static function register(ContainerInterface $container): void
    {
        /**
         * @param ContainerInterface $container
         * @return CommentService
         */
        $container->set(CommentService::class, function (ContainerInterface $container) {
            return new CommentService(
                Comment::class,
                $container->get(CommentRepository::class),
                CacheServiceProvider::getCacheService($container),
                $container->get(UserService::class)
            );
        });

        /**
         * @param ContainerInterface $container
         * @return CommentRepository
         */
        $container->set(CommentRepository::class, function (ContainerInterface $container) {
            return new CommentRepository(
                Comment::class,
                SkifServiceProvider::getDBService($container)
            );
        });
    }
}
