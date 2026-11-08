<?php

namespace WebSK\Skif;

class Router extends \WebSK\Slim\Router
{
    /**
     * @param array<string, scalar|null> $data
     * @param array<string, scalar|null|array<array-key, scalar|null>> $queryParams
     */
    public static function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        return parent::urlFor(
            $routeName,
            RouteArguments::path($data),
            RouteArguments::query($queryParams)
        );
    }
}
