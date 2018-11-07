<?php

namespace WebSK\Skif\Logger;

use Psr\Container\ContainerInterface;
use WebSK\Skif\Logger\Entry\LoggerEntry;
use WebSK\Skif\Logger\Entry\LoggerEntryRepository;
use WebSK\Skif\Logger\Entry\LoggerEntryService;
use WebSK\Skif\SkifServiceProvider;

class LoggerServiceProvider
{
    /**
     * @param ContainerInterface $container
     */
    public static function register(ContainerInterface $container)
    {
        /**
         * @param ContainerInterface $container
         * @return LoggerEntryService
         */
        $container[LoggerEntry::ENTITY_SERVICE_CONTAINER_ID] = function (ContainerInterface $container) {
            return new LoggerEntryService(
                LoggerEntry::class,
                $container[LoggerEntry::ENTITY_REPOSITORY_CONTAINER_ID],
                $container->get(SkifServiceProvider::SKIF_CACHE_SERVICE)
            );
        };

        /**
         * @param ContainerInterface $container
         * @return LoggerEntryRepository
         */
        $container[LoggerEntry::ENTITY_REPOSITORY_CONTAINER_ID] = function (ContainerInterface $container) {
            return new LoggerEntryRepository(
                LoggerEntry::class,
                $container->get(SkifServiceProvider::SKIF_DB_SERVICE)
            );
        };
    }

    /**
     * @param ContainerInterface $container
     * @return LoggerEntryService
     */
    public static function getEntryService(ContainerInterface $container)
    {
        return $container[LoggerEntry::ENTITY_SERVICE_CONTAINER_ID];
    }
}
