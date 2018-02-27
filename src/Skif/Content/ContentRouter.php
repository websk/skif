<?php

namespace Skif\Content;

use Skif\Router;

class ContentRouter
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
