<?php

namespace Skif\Users;

use Skif\UrlManager;

class UserRoutes
{
    public static function route()
    {
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
    }
}
