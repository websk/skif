<?php

namespace WebSK\Skif\Auth;

use Skif\UrlManager;
use Slim\App;
use WebSK\Skif\Auth\RequestHandlers\ForgotPasswordFormHandler;
use WebSK\Skif\Auth\RequestHandlers\ForgotPasswordHandler;
use WebSK\Skif\Auth\RequestHandlers\LoginFormHandler;

/**
 * Class AuthRoutes
 * @package WebSK\Skif\Auth
 */
class AuthRoutes
{
    const ROUTE_NAME_AUTH_LOGIN_FORM = 'auth:login_form';
    const ROUTE_NAME_AUTH_FORGOT_PASSWORD = 'user:forgot_password';
    const ROUTE_NAME_AUTH_FORGOT_PASSWORD_FORM = 'user:forgot_password_form';

    public static function route()
    {
        UrlManager::route('@^/user/registration_form@', AuthController::class, 'registrationFormAction');
        UrlManager::route('@^/user/registration@', AuthController::class, 'registrationAction');
        UrlManager::route('@^/user/confirm_registration/(.+)@', AuthController::class, 'confirmRegistrationAction');
        UrlManager::route('@^/user/send_confirm_code@', AuthController::class, 'sendConfirmCodeAction');
        UrlManager::route('@^/user/send_confirm_code_form@', AuthController::class, 'sendConfirmCodeFormAction');
        UrlManager::route('@^/user/logout@', AuthController::class, 'logoutAction');
        UrlManager::route('@^/user/login@', AuthController::class, 'loginAction');
        UrlManager::route('@^/user/social_login/(.+)@', AuthController::class, 'socialAuthAction');
        UrlManager::route('@^/auth/gate$@i', AuthController::class, 'gateAction');
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/auth', function (App $app) {
            $app->get('/login_form', LoginFormHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_LOGIN_FORM);

            $app->get('/forgot_password_form', ForgotPasswordFormHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_FORGOT_PASSWORD_FORM);

            $app->post('/forgot_password', ForgotPasswordHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_FORGOT_PASSWORD);
        });
    }
}
