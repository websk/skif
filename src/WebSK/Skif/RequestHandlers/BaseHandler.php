<?php

namespace WebSK\Skif\RequestHandlers;

use WebSK\Skif\RouteArguments;

abstract class BaseHandler extends \WebSK\Slim\RequestHandlers\BaseHandler
{
    /**
     * @param array<string, scalar|null> $data
     * @param array<string, scalar|null|array<array-key, scalar|null>> $queryParams
     */
    public function urlFor(string $name, array $data = [], array $queryParams = []): string
    {
        return parent::urlFor(
            $name,
            RouteArguments::path($data),
            RouteArguments::query($queryParams)
        );
    }
}
