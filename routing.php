<?php

use Skif\CountryController;
use Skif\Image\ControllerIndex;
use Skif\Image\ImageController;
use Skif\UrlManager;

// Country
UrlManager::route('@^/autocomplete/countries$@', CountryController::class, 'CountriesAutoCompleteAction');


UrlManager::route('@^/files/images/cache/(.+)/(.+)$@', ControllerIndex::class, 'indexAction');
UrlManager::route('@^/images/upload$@', ImageController::class, 'uploadAction');
//\Skif\UrlManager::route('@^/images/upload_to_files$@', '\Skif\Image\ImageController', 'uploadToFilesAction');
//\Skif\UrlManager::route('@^/images/upload_to_images$@', '\Skif\Image\ImageController', 'uploadToImagesAction');
