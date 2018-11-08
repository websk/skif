<?php

namespace WebSK\Skif\Users;

use Skif\Router;
use Skif\UrlManager;

class AuthRoutes
{
    public static function route()
    {
        if (Router::matchGroup('@/admin@')) {
            UrlManager::route('@^/admin/users/roles/edit/(.+)@', UserController::class, 'editUsersRoleAction');
            UrlManager::route('@^/admin/users/roles/save/(.+)@', UserController::class, 'saveUsersRoleAction');
            UrlManager::route('@^/admin/users/roles/delete/(.+)@', UserController::class, 'deleteUsersRoleAction');
        }

        UrlManager::route('@^/user/forgot_password$@', AuthController::class, 'forgotPasswordAction');
        UrlManager::route('@^/user/forgot_password_form@', AuthController::class, 'forgotPasswordFormAction');
        UrlManager::route('@^/user/registration_form@', AuthController::class, 'registrationFormAction');
        UrlManager::route('@^/user/registration@', AuthController::class, 'registrationAction');
        UrlManager::route('@^/user/confirm_registration/(.+)@', AuthController::class, 'confirmRegistrationAction');
        UrlManager::route('@^/user/send_confirm_code@', AuthController::class, 'sendConfirmCodeAction');
        UrlManager::route('@^/user/send_confirm_code_form@', AuthController::class, 'sendConfirmCodeFormAction');
        UrlManager::route('@^/user/login_form@', AuthController::class, 'loginFormAction');
        UrlManager::route('@^/user/logout@', AuthController::class, 'logoutAction');
        UrlManager::route('@^/user/login@', AuthController::class, 'loginAction');
        UrlManager::route('@^/user/social_login/(.+)@', AuthController::class, 'socialAuthAction');
        UrlManager::route('@^/auth/gate$@i', AuthController::class, 'gateAction');
    }
}
