<?php
$current_url_no_query = \Skif\UrlManager::getUriNoQueryString();

\Skif\UrlManager::route('@^/errors/(.+)$@', '\Skif\Http', 'errorPageAction');

\Skif\UrlManager::route('@^@', '\Skif\Redirect\RedirectController', 'redirectAction');


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
    '/admin/task' => '\Skif\Task\TaskController',
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

    \Skif\UrlManager::route('@^' . $base_url . '/add$@', $controller, 'addAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '/create$@', $controller, 'createAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '/edit/(.+)$@', $controller, 'editAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '/save/(.+)$@i', $controller, 'saveAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '/delete/(\d+)$@i', $controller, 'deleteAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '$@i', $controller, 'listAction', 0);
}

// Admin
\Skif\AdminRouter::route();

// Captcha
\Skif\UrlManager::route('@^/captcha/(.+)$@i', '\Skif\Captcha\CaptchaController', 'mainAction');


// User
\Skif\UrlManager::route('@^/user/edit/(.+)@', '\Skif\Users\UserController', 'editAction');
\Skif\UrlManager::route('@^/user/save/(.+)@', '\Skif\Users\UserController', 'saveAction');
\Skif\UrlManager::route('@^/user/delete/(.+)@', '\Skif\Users\UserController', 'deleteAction');
\Skif\UrlManager::route('@^/user/create_password/(\d+)@', '\Skif\Users\UserController', 'createPasswordAction');
\Skif\UrlManager::route('@^/user/add_photo/(.+)@', '\Skif\Users\UserController', 'addPhotoAction');
\Skif\UrlManager::route('@^/user/delete_photo/(.+)@', '\Skif\Users\UserController', 'deletePhotoAction');

\Skif\UrlManager::route('@^/user/forgot_password$@', '\Skif\Users\AuthController', 'forgotPasswordAction');
\Skif\UrlManager::route('@^/user/forgot_password_form@', '\Skif\Users\AuthController', 'forgotPasswordFormAction');
\Skif\UrlManager::route('@^/user/registration_form@', '\Skif\Users\AuthController', 'registrationFormAction');
\Skif\UrlManager::route('@^/user/registration@', '\Skif\Users\AuthController', 'registrationAction');
\Skif\UrlManager::route('@^/user/confirm_registration/(.+)@', '\Skif\Users\AuthController', 'confirmRegistrationAction');
\Skif\UrlManager::route('@^/user/send_confirm_code@', '\Skif\Users\AuthController', 'sendConfirmCodeAction');
\Skif\UrlManager::route('@^/user/send_confirm_code_form@', '\Skif\Users\AuthController', 'sendConfirmCodeFormAction');
\Skif\UrlManager::route('@^/user/login_form@', '\Skif\Users\AuthController', 'loginFormAction');
\Skif\UrlManager::route('@^/user/logout@', '\Skif\Users\AuthController', 'logoutAction');
\Skif\UrlManager::route('@^/user/login@', '\Skif\Users\AuthController', 'loginAction');
\Skif\UrlManager::route('@^/user/social_login/(.+)@', '\Skif\Users\AuthController', 'socialAuthAction');
\Skif\UrlManager::route('@^/auth/gate$@i', '\Skif\Users\AuthController', 'gateAction');

// Comment
\Skif\UrlManager::route('@^/comments/list$@', '\Skif\Comment\CommentController', 'listWebAction');
\Skif\UrlManager::route('@^/comments/add$@', '\Skif\Comment\CommentController', 'saveWebAction');
\Skif\UrlManager::route('@^/comments/delete/(\d+)$@', '\Skif\Comment\CommentController', 'deleteWebAction');

// Poll
\Skif\UrlManager::route('@^/poll/(\d+)$@', '\Skif\Poll\PollController', 'viewAction');
\Skif\UrlManager::route('@^/poll/(\d+)/vote$@', '\Skif\Poll\PollController', 'voteAction');

// Rating
\Skif\UrlManager::route('@^/rating/(\d+)/rate$@', '\Skif\Rating\RatingController', 'rateAction');


// Form
\Skif\UrlManager::route('@^@', '\Skif\Form\FormController', 'viewAction');
\Skif\UrlManager::route('@^/form/(\d+)/send$@', '\Skif\Form\FormController', 'sendAction');


// Country
\Skif\UrlManager::route('@^/autocomplete/countries$@', '\Skif\CountryController', 'CountriesAutoCompleteAction');


\Skif\UrlManager::route('@^/files/images/cache/(.+)/(.+)$@', '\Skif\Image\ControllerIndex', 'indexAction');
\Skif\UrlManager::route('@^/images/upload$@', '\Skif\Image\ImageController', 'uploadAction');
//\Skif\UrlManager::route('@^/images/upload_to_files$@', '\Skif\Image\ImageController', 'uploadToFilesAction');
//\Skif\UrlManager::route('@^/images/upload_to_images$@', '\Skif\Image\ImageController', 'uploadToImagesAction');

\Skif\UrlManager::route('@^@', '\Skif\Content\ContentController', 'viewAction');
\Skif\UrlManager::route('@^@', '\Skif\Content\RubricController', 'listAction');
\Skif\UrlManager::route('@^/(.+)$@i', '\Skif\Content\ContentController', 'listAction');
