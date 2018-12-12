<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\Auth\AuthRoutes;
use WebSK\Skif\Captcha\Captcha;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\UsersUtils;

/**
 * Class ForgotPasswordHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class ForgotPasswordHandler extends \WebSK\Slim\RequestHandlers\BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $email = $request->getParam('email', '');

        $destination = $request->getParam('destination', $this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_FORGOT_PASSWORD));

        if (!$request->getParam('captcha')) {
            return $response->withRedirect($destination);
        }

        if (!Captcha::checkWithMessage()) {
            return $response->withRedirect($destination);
        }

        if (empty($email)) {
            Messages::setError('Ошибка! Не указан адрес электронной почты (Email).');
            return $response->withRedirect($destination);
        }

        if (!UsersUtils::hasUserByEmail($email)) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты не зарегистрирован на сайте.');
            return $response->withRedirect($destination);
        }

        $user_id = UsersUtils::getUserIdByEmail($email);

        UsersUtils::createAndSendPasswordToUser($user_id);

        $message = 'Временный пароль отправлен на указанный вами адрес электронной почты.';

        Messages::setMessage($message);

        return $response->withRedirect($this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM));
    }
}
