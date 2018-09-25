<?php

use Skif\Content\ContentRouter;
use Skif\UrlManager;

$current_url_no_query = UrlManager::getUriNoQueryString();

UrlManager::route('@^/errors/(.+)$@', '\Skif\Http', 'errorPageAction');

UrlManager::route('@^@', '\Skif\Redirect\RedirectController', 'redirectAction');


// CRUD
$default_route_based_crud_arr = array(
    '/crud/[\d\w\%]+' => '\Skif\CRUD\CRUDController',
    '/admin/key_value' => '\Skif\KeyValue\KeyValueController',
    '/admin/redirect' => '\Skif\Redirect\RedirectController',
    '/admin/comments' => '\Skif\Comment\CommentController',
    '/admin/poll' => '\Skif\Poll\PollController',
    '/admin/poll_question' => '\Skif\Poll\PollQuestionController',
    '/admin/form' => '\Skif\Form\FormController',
    '/admin/form_field' => '\Skif\Form\FormFieldController',
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
UrlManager::route('@^/user/edit/(.+)@', '\Skif\Users\UserController', 'editAction');
UrlManager::route('@^/user/save/(.+)@', '\Skif\Users\UserController', 'saveAction');
UrlManager::route('@^/user/delete/(.+)@', '\Skif\Users\UserController', 'deleteAction');
UrlManager::route('@^/user/create_password/(\d+)@', '\Skif\Users\UserController', 'createPasswordAction');
UrlManager::route('@^/user/add_photo/(.+)@', '\Skif\Users\UserController', 'addPhotoAction');
UrlManager::route('@^/user/delete_photo/(.+)@', '\Skif\Users\UserController', 'deletePhotoAction');

UrlManager::route('@^/user/forgot_password$@', '\Skif\Users\AuthController', 'forgotPasswordAction');
UrlManager::route('@^/user/forgot_password_form@', '\Skif\Users\AuthController', 'forgotPasswordFormAction');
UrlManager::route('@^/user/registration_form@', '\Skif\Users\AuthController', 'registrationFormAction');
UrlManager::route('@^/user/registration@', '\Skif\Users\AuthController', 'registrationAction');
UrlManager::route('@^/user/confirm_registration/(.+)@', '\Skif\Users\AuthController', 'confirmRegistrationAction');
UrlManager::route('@^/user/send_confirm_code@', '\Skif\Users\AuthController', 'sendConfirmCodeAction');
UrlManager::route('@^/user/send_confirm_code_form@', '\Skif\Users\AuthController', 'sendConfirmCodeFormAction');
UrlManager::route('@^/user/login_form@', '\Skif\Users\AuthController', 'loginFormAction');
UrlManager::route('@^/user/logout@', '\Skif\Users\AuthController', 'logoutAction');
UrlManager::route('@^/user/login@', '\Skif\Users\AuthController', 'loginAction');
UrlManager::route('@^/user/social_login/(.+)@', '\Skif\Users\AuthController', 'socialAuthAction');
UrlManager::route('@^/auth/gate$@i', '\Skif\Users\AuthController', 'gateAction');

// Comment
UrlManager::route('@^/comments/list$@', '\Skif\Comment\CommentController', 'listWebAction');
UrlManager::route('@^/comments/add$@', '\Skif\Comment\CommentController', 'saveWebAction');
UrlManager::route('@^/comments/delete/(\d+)$@', '\Skif\Comment\CommentController', 'deleteWebAction');

// Poll
UrlManager::route('@^/poll/(\d+)$@', '\Skif\Poll\PollController', 'viewAction');
UrlManager::route('@^/poll/(\d+)/vote$@', '\Skif\Poll\PollController', 'voteAction');

// Rating
UrlManager::route('@^/rating/(\d+)/rate$@', '\Skif\Rating\RatingController', 'rateAction');


// Form
UrlManager::route('@^@', '\Skif\Form\FormController', 'viewAction');
UrlManager::route('@^/form/(\d+)/send$@', '\Skif\Form\FormController', 'sendAction');


// Country
UrlManager::route('@^/autocomplete/countries$@', '\Skif\CountryController', 'CountriesAutoCompleteAction');


UrlManager::route('@^/files/images/cache/(.+)/(.+)$@', '\Skif\Image\ControllerIndex', 'indexAction');
UrlManager::route('@^/images/upload$@', '\Skif\Image\ImageController', 'uploadAction');
//\Skif\UrlManager::route('@^/images/upload_to_files$@', '\Skif\Image\ImageController', 'uploadToFilesAction');
//\Skif\UrlManager::route('@^/images/upload_to_images$@', '\Skif\Image\ImageController', 'uploadToImagesAction');

ContentRouter::route();

//UrlManager::route('@^@', '\Skif\Content\ContentController', 'viewAction');
UrlManager::route('@^@', '\Skif\Content\RubricController', 'listAction');
UrlManager::route('@^/(.+)$@i', '\Skif\Content\ContentController', 'listAction');
