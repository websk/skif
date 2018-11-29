<?php

namespace WebSK\Skif\Rating;

use WebSK\SimpleRouter\SimpleRouter;

class RatingRoutes
{
    public static function route()
    {
        SimpleRouter::routeBasedCrud('/admin/rating', RatingController::class);

        SimpleRouter::staticRoute('@^/rating/(\d+)/rate$@', RatingController::class, 'rateAction');
    }
}
