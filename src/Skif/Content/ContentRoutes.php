<?php

namespace Skif\Content;

use Skif\Router;

class ContentRoutes
{
    public static function route()
    {
        Router::route(
            '@^@',
            [new ContentController(), 'viewAction'],
            0
        );
    }
}
