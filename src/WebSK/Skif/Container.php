<?php

namespace Websk\Skif;

use Psr\Container\ContainerInterface;

/**
 * Class Container
 * @package Websk\Skif
 */
class Container extends Facade
{
    /**
     * Overriding Facades::self() to set Slim\App instance is in order to tell
     * Facades to proxy it.
     * @return ContainerInterface
     */
    public static function self()
    {
        return self::$app->getContainer();
    }
}
