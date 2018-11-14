<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Container\ContainerInterface;
use Websk\Skif\Router;

/**
 * Class BaseHandler
 * @package VitrinaTV\Core\RequestHandlers
 */
abstract class BaseHandler
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @param array $data
     * @param array $queryParams
     * @return string
     */
    public function pathFor(string $name, array $data = [], array $queryParams = [])
    {
        return Router::pathFor($name, $data, $queryParams);
    }
}