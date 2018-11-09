<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\Auth\AuthRoutes;
use Websk\Skif\Messages;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Skif\Users\UsersUtils;

/**
 * Class ConfirmRegistrationHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class ConfirmRegistrationHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $confirm_code
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, int $confirm_code)
    {
        $user_id = UsersUtils::getUserIdByConfirmCode($confirm_code);

        $destination = $this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM);

        if (!$user_id) {
            Messages::setError(
                'Ошибка! Неверный код подтверждения. <a href="' . $this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_SEND_CONFIRM_CODE_FORM) . '">Выслать код подтверждения повторно.</a>'
            );
            return $response->withRedirect($destination);
        }

        $user_service = UsersServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id);
        $user_obj->setConfirm(1);
        $user_obj->setConfirmCode('');
        $user_service->save($user_obj);

        $message = 'Поздравляем! Процесс регистрации успешно завершен. Теперь вы можете войти на сайт.';

        Messages::setMessage($message);

        return $response->withRedirect($destination);
    }
}
