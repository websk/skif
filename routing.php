<?php
$current_url_no_query = \Skif\UrlManager::getUriNoQueryString();

\Skif\UrlManager::route('@^/error$@', '\Skif\Http', 'errorPageAction');

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

    \Skif\UrlManager::route('@^' . $base_url . '/add@', $controller, 'addAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '/create@', $controller, 'createAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '/edit/(.+)$@', $controller, 'editAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '/save/(.+)$@i', $controller, 'saveAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '/delete/(\d+)$@i', $controller, 'deleteAction', 0);
    \Skif\UrlManager::route('@^' . $base_url . '$@i', $controller, 'listAction', 0);
}

// Admin
if (strpos($current_url_no_query, '/admin') !== false) {
    \Skif\UrlManager::route('@^/admin$@i', '\Skif\AdminController', 'indexAction', 0);
    \Skif\UrlManager::route('@^/admin/$@i', '\Skif\AdminController', 'indexAction', 0);

    // Admin Logger
    \Skif\UrlManager::route('@^/admin/logger/list$@i', '\Skif\Logger\ControllerLogger', 'listAction', 0);
    \Skif\UrlManager::route('@^/admin/logger/object_log/@i', '\Skif\Logger\ControllerLogger', 'object_logAction', 0);
    \Skif\UrlManager::route('@^/admin/logger/record/@', '\Skif\Logger\ControllerLogger', 'recordAction', 0);

    // Admin Blocks
    \Skif\UrlManager::route('@^/admin/blocks$@i', '\Skif\Blocks\ControllerBlocks', 'listAction', 0);
    \Skif\UrlManager::route('@^/admin/blocks/list$@i', '\Skif\Blocks\ControllerBlocks', 'listAction', 0);
    \Skif\UrlManager::route('@^/admin/blocks/edit/(.+)/position@i', '\Skif\Blocks\ControllerBlocks', 'placeInRegionTabAction', 0);
    \Skif\UrlManager::route('@^/admin/blocks/edit/(.+)/region@i', '\Skif\Blocks\ControllerBlocks', 'chooseRegionTabAction', 0);
    \Skif\UrlManager::route('@^/admin/blocks/edit/(.+)/caching@i', '\Skif\Blocks\ControllerBlocks', 'cachingTabAction', 0);
    \Skif\UrlManager::route('@^/admin/blocks/edit/(.+)/ace@i', '\Skif\Blocks\ControllerBlocks', 'aceTabAction', 0);
    \Skif\UrlManager::route('@^/admin/blocks/edit/(.+)/delete@i', '\Skif\Blocks\ControllerBlocks', 'deleteTabAction', 0);
    \Skif\UrlManager::route('@^/admin/blocks/edit/(.+)@i', '\Skif\Blocks\ControllerBlocks', 'editAction', 0);
    \Skif\UrlManager::route('@^/admin/blocks/search$@i', '\Skif\Blocks\ControllerBlocks', 'searchAction', 0);
    \Skif\UrlManager::route('@^/admin/blocks/change_template/(\d+)@i', '\Skif\Blocks\ControllerBlocks', 'changeTemplateAction', 0);

    // Материалы
    \Skif\UrlManager::route('@^/admin/content/(.+)/rubrics$@', '\Skif\Content\RubricController', 'listAdminRubricsAction');
    \Skif\UrlManager::route('@^/admin/content/(.+)/rubrics/edit/(.+)@', '\Skif\Content\RubricController', 'editRubricAction');
    \Skif\UrlManager::route('@^/admin/content/(.+)/rubrics/save/(.+)@', '\Skif\Content\RubricController', 'saveRubricAction');
    \Skif\UrlManager::route('@^/admin/content/(.+)/rubrics/delete/(.+)@', '\Skif\Content\RubricController', 'deleteRubricAction');
    \Skif\UrlManager::route('@^/admin/content/autocomplete$@i', '\Skif\Content\ContentController', 'autoCompleteContentAction', 0);
    \Skif\UrlManager::route('@^/admin/content/(.+)/edit/(.+)$@i', '\Skif\Content\ContentController', 'editAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/content/(.+)/save/(.+)$@i', '\Skif\Content\ContentController', 'saveAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/content/(.+)/delete/(.+)$@i', '\Skif\Content\ContentController', 'deleteAction', 0);
    \Skif\UrlManager::route('@^/admin/content/(.+)/delete_image/(.+)$@i', '\Skif\Content\ContentController', 'deleteImageAction', 0);
    \Skif\UrlManager::route('@^/admin/content/(.+)$@i', '\Skif\Content\ContentController', 'listAdminAction', 0);

    // Меню сайта
    \Skif\UrlManager::route('@^/admin/site_menu$@i', '\Skif\SiteMenu\SiteMenuController', 'listAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/site_menu/edit/(.+)$@i', '\Skif\SiteMenu\SiteMenuController', 'editAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/site_menu/save/(.+)$@i', '\Skif\SiteMenu\SiteMenuController', 'saveAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/site_menu/delete/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'deleteAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/site_menu/(\d+)/items/list/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'listItemsAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/site_menu/(\d+)/items/list_for_move/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'listForMoveItemsAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/site_menu/(\d+)/item/move/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'moveItemAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/site_menu/(\d+)/item/edit/(.+)$@i', '\Skif\SiteMenu\SiteMenuController', 'editItemAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/site_menu/(\d+)/item/save/(.+)$@i', '\Skif\SiteMenu\SiteMenuController', 'saveItemAdminAction', 0);
    \Skif\UrlManager::route('@^/admin/site_menu/(\d+)/item/delete/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'deleteItemAdminAction', 0);

    // User
    \Skif\UrlManager::route('@^/admin/users$@', '\Skif\Users\UserController', 'listAction');
    \Skif\UrlManager::route('@^/admin/users/edit/(.+)@', '\Skif\Users\UserController', 'editAction', 0, \Skif\Conf\ConfWrapper::value('layout.admin'));
    \Skif\UrlManager::route('@^/admin/users/roles$@', '\Skif\Users\UserController', 'listUsersRolesAction');
    \Skif\UrlManager::route('@^/admin/users/roles/edit/(.+)@', '\Skif\Users\UserController', 'editUsersRoleAction');
    \Skif\UrlManager::route('@^/admin/users/roles/save/(.+)@', '\Skif\Users\UserController', 'saveUsersRoleAction');
    \Skif\UrlManager::route('@^/admin/users/roles/delete/(.+)@', '\Skif\Users\UserController', 'deleteUsersRoleAction');

    exit;
}

// Captcha
\Skif\UrlManager::route('@^/captcha/(.+)$@i', '\Skif\Captcha\CaptchaController', 'mainAction');


// User
\Skif\UrlManager::route('@^/user/edit/(.+)@', '\Skif\Users\UserController', 'editAction');
\Skif\UrlManager::route('@^/user/save/(.+)@', '\Skif\Users\UserController', 'saveAction');
\Skif\UrlManager::route('@^/user/delete/(.+)@', '\Skif\Users\UserController', 'deleteAction');
\Skif\UrlManager::route('@^/user/create_password/(.+)@', '\Skif\Users\UserController', 'createAndSendPasswordToUserAction');
\Skif\UrlManager::route('@^/user/add_photo/(.+)@', '\Skif\Users\UserController', 'addPhotoAction');
\Skif\UrlManager::route('@^/user/delete_photo/(.+)@', '\Skif\Users\UserController', 'deletePhotoAction');
\Skif\UrlManager::route('@^/user/logout@', '\Skif\Users\AuthController', 'logoutAction');
\Skif\UrlManager::route('@^/user/login@', '\Skif\Users\AuthController', 'loginAction');

// Comment
\Skif\UrlManager::route('@^/comments/list$@', '\Skif\Comment\CommentController', 'listWebAction');
\Skif\UrlManager::route('@^/comments/add$@', '\Skif\Comment\CommentController', 'saveWebAction');
\Skif\UrlManager::route('@^/comments/delete/(\d+)$@', '\Skif\Comment\CommentController', 'deleteWebAction');

// Poll
\Skif\UrlManager::route('@^/poll/(\d+)$@', '\Skif\Poll\PollController', 'viewAction');
\Skif\UrlManager::route('@^/poll/(\d+)/vote$@', '\Skif\Poll\PollController', 'voteAction');

// Form
\Skif\UrlManager::route('@^/form/(\d+)$@', '\Skif\Form\FormController', 'viewAction');
\Skif\UrlManager::route('@^/form/(\d+)/send$@', '\Skif\Form\FormController', 'sendAction');


// Country
\Skif\UrlManager::route('@^/autocomplete/countries$@', '\Skif\CountryController', 'CountriesAutoCompleteAction');

// Regions
\Skif\UrlManager::route('@^/regions/import_from_vk$@', '\Skif\Regions\RegionController', 'importFromVKAction');


\Skif\UrlManager::route('@^/files/images/cache/(.+)/(.+)$@', '\Skif\Image\ControllerIndex', 'indexAction');
//\Skif\UrlManager::route('@^/images/upload$@', '\Skif\Image\ImageController', 'uploadAction');
//\Skif\UrlManager::route('@^/images/upload_to_files$@', '\Skif\Image\ImageController', 'uploadToFilesAction');
//\Skif\UrlManager::route('@^/images/upload_to_images$@', '\Skif\Image\ImageController', 'uploadToImagesAction');

\Skif\UrlManager::route('@^@', '\Skif\Content\ContentController', 'viewAction');
\Skif\UrlManager::route('@^@', '\Skif\Content\RubricController', 'listAction');
\Skif\UrlManager::route('@^/(.+)$@i', '\Skif\Content\ContentController', 'listAction');
