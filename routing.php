<?php

use Skif\Comment\CommentRoutes;
use Skif\Content\ContentRoutes;
use Skif\Form\FormRoutes;
use Skif\Poll\PollRoutes;
use Skif\UrlManager;
use Skif\Users\UserRoutes;

$current_url_no_query = UrlManager::getUriNoQueryString();

UrlManager::route('@^/errors/(.+)$@', '\Skif\Http', 'errorPageAction');

UrlManager::route('@^@', '\Skif\Redirect\RedirectController', 'redirectAction');


// CRUD
$default_route_based_crud_arr = array(
    '/crud/[\d\w\%]+' => '\Skif\CRUD\CRUDController',
    '/admin/key_value' => '\Skif\KeyValue\KeyValueController',
    '/admin/redirect' => '\Skif\Redirect\RedirectController',
    '/admin/rating' => '\Skif\Rating\RatingController',
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
UrlManager::route('@^/captcha/(.+)$@i', '\Skif\Captcha\CaptchaController', 'mainAction');


// User
UserRoutes::route();

// Comment
CommentRoutes::route();

// Rating
UrlManager::route('@^/rating/(\d+)/rate$@', '\Skif\Rating\RatingController', 'rateAction');

// Form
FormRoutes::route();

// Poll
PollRoutes::route();

// Country
UrlManager::route('@^/autocomplete/countries$@', '\Skif\CountryController', 'CountriesAutoCompleteAction');


UrlManager::route('@^/files/images/cache/(.+)/(.+)$@', '\Skif\Image\ControllerIndex', 'indexAction');
UrlManager::route('@^/images/upload$@', '\Skif\Image\ImageController', 'uploadAction');
//\Skif\UrlManager::route('@^/images/upload_to_files$@', '\Skif\Image\ImageController', 'uploadToFilesAction');
//\Skif\UrlManager::route('@^/images/upload_to_images$@', '\Skif\Image\ImageController', 'uploadToImagesAction');

ContentRoutes::route();
