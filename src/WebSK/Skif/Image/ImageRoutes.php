<?php

namespace WebSK\Skif\Image;

use WebSK\SimpleRouter\SimpleRouter;

/**
 * Class ImageRoutes
 * @package WebSK\Skif\Image\Image
 */
class ImageRoutes
{
    public static function routes()
    {
        SimpleRouter::staticRoute('@^/files/images/cache/(.+)/(.+)$@', ControllerIndex::class, 'indexAction');
        SimpleRouter::staticRoute('@^/images/upload$@', ImageController::class, 'uploadAction');
    }
}
