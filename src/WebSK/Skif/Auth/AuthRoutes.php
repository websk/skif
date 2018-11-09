<?php

namespace WebSK\Skif\Auth;

use Skif\UrlManager;
use Slim\App;
use WebSK\Skif\Auth\RequestHandlers\ConfirmRegistrationHandler;
use WebSK\Skif\Auth\RequestHandlers\ForgotPasswordFormHandler;
use WebSK\Skif\Auth\RequestHandlers\ForgotPasswordHandler;
use WebSK\Skif\Auth\RequestHandlers\LoginFormHandler;
use WebSK\Skif\Auth\RequestHandlers\LoginHandler;
use WebSK\Skif\Auth\RequestHandlers\LogoutHandler;
use WebSK\Skif\Auth\RequestHandlers\RegistrationFormHandler;
use WebSK\Skif\Auth\RequestHandlers\RegistrationHandler;

/**
 * Class AuthRoutes
 * @package WebSK\Skif\Auth
 */
class AuthRoutes
{
    const ROUTE_NAME_AUTH_LOGIN_FORM = 'auth:login_form';
    const ROUTE_NAME_AUTH_LOGIN = 'auth:login';
    const ROUTE_NAME_AUTH_LOGOUT = 'auth:logout';
    const ROUTE_NAME_AUTH_FORGOT_PASSWORD_FORM = 'auth:forgot_password_form';
    const ROUTE_NAME_AUTH_FORGOT_PASSWORD = 'auth:forgot_password';
    const ROUTE_NAME_AUTH_REGISTRATION_FORM = 'auth:registration_form';
    const ROUTE_NAME_AUTH_REGISTRATION = 'auth:registration';
    const ROUTE_NAME_AUTH_CONFIRM_REGISTRATION = 'auth:confirm_registration';

    public static function route()
    {
        UrlManager::route('@^/user/send_confirm_code@', AuthController::class, 'sendConfirmCodeAction');
        UrlManager::route('@^/user/send_confirm_code_form@', AuthController::class, 'sendConfirmCodeFormAction');
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

            $app->post('/login', LoginHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_LOGIN);

            $app->get('/logout', LogoutHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_LOGOUT);

            $app->get('/forgot_password_form', ForgotPasswordFormHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_FORGOT_PASSWORD_FORM);

            $app->post('/forgot_password', ForgotPasswordHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_FORGOT_PASSWORD);

            $app->get('/registration_form', RegistrationFormHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_REGISTRATION_FORM);

            $app->post('/registration', RegistrationHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_REGISTRATION);

            $app->post('/confirm_registration/{confirm_code:\d+}', ConfirmRegistrationHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_CONFIRM_REGISTRATION);
        });
    }
}
