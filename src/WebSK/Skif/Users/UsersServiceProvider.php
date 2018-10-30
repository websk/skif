<?php

namespace WebSK\Skif\Users;

use Psr\Container\ContainerInterface;
use WebSK\Skif\SkifServiceProvider;

class UsersServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return UserRoleService
         */
        $container[UserRole::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new UserRoleService(
                User::class,
                $container[User::ENTITY_REPOSITORY_CONTAINER_ID],
                $container->get(SkifServiceProvider::SKIF_CACHE_SERVICE)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return UserService
         */
        $container[User::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new UserService(
                User::class,
                $container[User::ENTITY_REPOSITORY_CONTAINER_ID],
                $container->get(SkifServiceProvider::SKIF_CACHE_SERVICE),
                $container->get(Role::ENTITY_SERVICE_CONTAINER_ID),
                $container->get(UserRole::ENTITY_SERVICE_CONTAINER_ID)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return UserRepository
         */
        $container[User::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new UserRepository(
                User::class,
                $container->get(User::ENTITY_REPOSITORY_CONTAINER_ID)
            );
        };
    }
}
