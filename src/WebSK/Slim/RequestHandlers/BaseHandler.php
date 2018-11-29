<?php

namespace WebSK\Slim\RequestHandlers;

use Psr\Container\ContainerInterface;
use WebSK\Slim\Router;

/**
 * Class BaseHandler
 * @package WebSK\Slim\RequestHandlers
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
