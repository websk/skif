<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\Auth\AuthRoutes;
use WebSK\Skif\Auth\AuthServiceProvider;
use WebSK\Skif\Captcha\Captcha;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Skif\Users\UsersUtils;

/**
 * Class SendConfirmCodeHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class SendConfirmCodeHandler extends \WebSK\Slim\RequestHandlers\BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \phpmailerException
     */
    public function __invoke(Request $request, Response $response)
    {
        $email = $request->getParam('email', '');

        $destination = $this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM);

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

        $user_service = UsersServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id);

        if ($user_obj->isConfirm()) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты уже зарегистрирован.');
            return $response->withRedirect($destination);
        }

        $confirm_code = UsersUtils::generateConfirmCode();

        $auth_service = AuthServiceProvider::getAuthService($this->container);
        $auth_service->sendConfirmMail($user_obj->getName(), $email, $confirm_code);

        $message = 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        Messages::setMessage($message);

        return $response->withRedirect($destination);
    }
}
