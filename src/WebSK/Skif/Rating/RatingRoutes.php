<?php

namespace WebSK\Skif\Rating;

use Skif\UrlManager;

class RatingRoutes
{
    public static function route()
    {
        UrlManager::routeBasedCrud('/admin/rating', RatingController::class);

        UrlManager::route('@^/rating/(\d+)/rate$@', RatingController::class, 'rateAction');
    }
}
