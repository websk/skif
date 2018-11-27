<?php

namespace WebSK\Skif\Users;

use Psr\Container\ContainerInterface;
use WebSK\Cache\CacheServiceProvider;
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
         * @return RoleService
         */
        $container[Role::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new RoleService(
                Role::class,
                $container->get(Role::ENTITY_REPOSITORY_CONTAINER_ID),
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return RoleRepository
         */
        $container[Role::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new RoleRepository(
                Role::class,
                $container->get(SkifServiceProvider::SKIF_DB_SERVICE)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return UserRoleService
         */
        $container[UserRole::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new UserRoleService(
                UserRole::class,
                $container->get(UserRole::ENTITY_REPOSITORY_CONTAINER_ID),
                CacheServiceProvider::getCacheService($container)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return UserRoleRepository
         */
        $container[UserRole::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new UserRoleRepository(
                UserRole::class,
                $container->get(SkifServiceProvider::SKIF_DB_SERVICE)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return UserService
         */
        $container[User::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new UserService(
                User::class,
                $container->get(User::ENTITY_REPOSITORY_CONTAINER_ID),
                CacheServiceProvider::getCacheService($container),
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
                $container->get(SkifServiceProvider::SKIF_DB_SERVICE)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return SessionsService
         */
        $container[Sessions::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new SessionsService(
                Sessions::class,
                $container->get(User::ENTITY_REPOSITORY_CONTAINER_ID),
                CacheServiceProvider::getCacheService($container)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return RoleService
     */
    public static function getRoleService(ContainerInterface $container)
    {
        return $container->get(Role::ENTITY_SERVICE_CONTAINER_ID);
    }

    /**
     * @param ContainerInterface $container
     * @return UserRoleService
     */
    public static function getUserRoleService(ContainerInterface $container)
    {
        return $container->get(UserRole::ENTITY_SERVICE_CONTAINER_ID);
    }

    /**
     * @param ContainerInterface $container
     * @return UserService
     */
    public static function getUserService(ContainerInterface $container)
    {
        return $container->get(User::ENTITY_SERVICE_CONTAINER_ID);
    }

    /**
     * @param ContainerInterface $container
     * @return SessionsService
     */
    public static function getSessionService(ContainerInterface $container)
    {
        return $container->get(Sessions::ENTITY_SERVICE_CONTAINER_ID);
    }
}
