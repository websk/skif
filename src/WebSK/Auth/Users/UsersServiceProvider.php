<?php

namespace WebSK\Auth\Users;

use Psr\Container\ContainerInterface;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Cache\CacheServiceProvider;

/**
 * Class UsersServiceProvider
 * @package WebSK\Auth\Users
 */
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
                $container->get(AuthServiceProvider::DB_SERVICE_CONTAINER_ID)
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
                $container->get(AuthServiceProvider::DB_SERVICE_CONTAINER_ID)
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
                $container->get(AuthServiceProvider::DB_SERVICE_CONTAINER_ID)
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
}
