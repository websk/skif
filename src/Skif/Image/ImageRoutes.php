<?php

namespace Skif\Image;

use Skif\UrlManager;

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
