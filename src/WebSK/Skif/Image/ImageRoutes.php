<?php

namespace WebSK\Skif\Image;

use WebSK\Skif\UrlManager;

/**
 * Class ImageRoutes
 * @package WebSK\Skif\Image\Image
 */
class ImageRoutes
{
    public static function routes()
    {
        UrlManager::route('@^/files/images/cache/(.+)/(.+)$@', ControllerIndex::class, 'indexAction');
        UrlManager::route('@^/images/upload$@', ImageController::class, 'uploadAction');

        // UrlManager::route('@^/images/upload_to_files$@', ImageController::class, 'uploadToFilesAction');
        // UrlManager::route('@^/images/upload_to_images$@', ImageController::class, 'uploadToImagesAction');
    }
}
