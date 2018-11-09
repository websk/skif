<?php

namespace WebSK\Skif\Auth;

use Slim\App;
use WebSK\Skif\Auth\RequestHandlers\ConfirmRegistrationHandler;
use WebSK\Skif\Auth\RequestHandlers\ForgotPasswordFormHandler;
use WebSK\Skif\Auth\RequestHandlers\ForgotPasswordHandler;
use WebSK\Skif\Auth\RequestHandlers\GateHandler;
use WebSK\Skif\Auth\RequestHandlers\LoginFormHandler;
use WebSK\Skif\Auth\RequestHandlers\LoginHandler;
use WebSK\Skif\Auth\RequestHandlers\LogoutHandler;
use WebSK\Skif\Auth\RequestHandlers\RegistrationFormHandler;
use WebSK\Skif\Auth\RequestHandlers\RegistrationHandler;
use WebSK\Skif\Auth\RequestHandlers\SendConfirmCodeFormHandler;
use WebSK\Skif\Auth\RequestHandlers\SendConfirmCodeHandler;
use WebSK\Skif\Auth\RequestHandlers\SocialLoginHandler;

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
    const ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM = 'auth:send_confirm_code_form';
    const ROUTE_NAME_AUTH_SEND_CONFIRM_CODE = 'auth:send_confirm_code';
    const ROUTE_NAME_AUTH_SOCIAL_LOGIN = 'auth:social_login';
    const ROUTE_NAME_AUTH_GATE = 'auth:gate';

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

            $app->get('/send_confirm_code_form', SendConfirmCodeFormHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM);

            $app->post('/send_confirm_code', SendConfirmCodeHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE);

            $app->post('/social_login/{provider_name:\w+}', SocialLoginHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_SOCIAL_LOGIN);

            $app->post('/gate', GateHandler::class)
                ->setName(self::ROUTE_NAME_AUTH_GATE);
        });
    }
}
