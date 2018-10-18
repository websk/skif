<?php

use Skif\Captcha\CaptchaController;
use Skif\Content\ContentRoutes;
use Skif\CountryController;
use Skif\CRUD\CRUDController;
use Skif\Image\ControllerIndex;
use Skif\Image\ImageController;
use Skif\UrlManager;

$current_url_no_query = UrlManager::getUriNoQueryString();

// CRUD
$default_route_based_crud_arr = array(
    '/crud/[\d\w\%]+' => CRUDController::class,
);

if (isset($route_based_crud_arr)) {
    $route_based_crud_arr = array_merge($default_route_based_crud_arr, $route_based_crud_arr);
} else {
    $route_based_crud_arr = $default_route_based_crud_arr;
}

foreach ($route_based_crud_arr as $base_url => $controller) {
    if (!preg_match('@^' . $base_url . '?(.+)@i', $current_url_no_query, $matches_arr)) {
        continue;
    }

    UrlManager::route('@^' . $base_url . '/add$@', $controller, 'addAction', 0);
    UrlManager::route('@^' . $base_url . '/create$@', $controller, 'createAction', 0);
    UrlManager::route('@^' . $base_url . '/edit/(.+)$@', $controller, 'editAction', 0);
    UrlManager::route('@^' . $base_url . '/save/(.+)$@i', $controller, 'saveAction', 0);
    UrlManager::route('@^' . $base_url . '/delete/(\d+)$@i', $controller, 'deleteAction', 0);
    UrlManager::route('@^' . $base_url . '$@i', $controller, 'listAction', 0);
}

// Captcha
UrlManager::route('@^/captcha/(.+)$@i', CaptchaController::class, 'mainAction');

// Country
UrlManager::route('@^/autocomplete/countries$@', CountryController::class, 'CountriesAutoCompleteAction');


UrlManager::route('@^/files/images/cache/(.+)/(.+)$@', ControllerIndex::class, 'indexAction');
UrlManager::route('@^/images/upload$@', ImageController::class, 'uploadAction');
//\Skif\UrlManager::route('@^/images/upload_to_files$@', '\Skif\Image\ImageController', 'uploadToFilesAction');
//\Skif\UrlManager::route('@^/images/upload_to_images$@', '\Skif\Image\ImageController', 'uploadToImagesAction');

ContentRoutes::route();
