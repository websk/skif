<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Captcha\Captcha;
use WebSK\Config\ConfWrapper;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Router;
use WebSK\Auth\Auth;
use WebSK\Auth\Users\User;
use WebSK\Auth\Users\UserRole;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Auth\Users\UsersUtils;

/**
 * Class RegistrationHandler
 * @package WebSK\Auth\RequestHandlers
 */
class RegistrationHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response)
    {
        $destination = $request->getParam('destination', Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM));

        $name = trim($request->getParam('name', ''));
        $first_name = trim($request->getParam('first_name', ''));
        $last_name = trim($request->getParam('last_name', ''));
        $email = trim($request->getParam('email', ''));
        $new_password_first = $request->getParam('new_password_first', '');
        $new_password_second = $request->getParam('new_password_second', '');

        $error_destination = Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_REGISTRATION_FORM);

        if (!$request->getParam('captcha')) {
            return $response->withRedirect($error_destination);
        }

        if (!Captcha::checkWithMessage()) {
            return $response->withRedirect($error_destination);
        }

        if (empty($email)) {
            Messages::setError('Ошибка! Не указан Email.');
            return $response->withRedirect($error_destination);
        }

        if (empty($name)) {
            Messages::setError('Ошибка! Не указано Имя.');
            return $response->withRedirect($error_destination);
        }

        $has_user_id = UsersUtils::hasUserByEmail($email);
        if ($has_user_id) {
            Messages::setError(
                'Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже зарегистрирован.'
            );
            return $response->withRedirect($error_destination);
        }

        if (!$new_password_first && !$new_password_second) {
            Messages::setError('Ошибка! Не введен пароль.');
            return $response->withRedirect($error_destination);
        }

        if ($new_password_first || $new_password_second) {
            if ($new_password_first != $new_password_second) {
                Messages::setError('Ошибка! Пароль не подтвержден, либо подтвержден неверно.');
                return $response->withRedirect($error_destination);
            }
        }

        $user_service = UsersServiceProvider::getUserService($this->container);

        $user_obj = new User();

        $user_obj->setName($name);
        if ($first_name) {
            $user_obj->setFirstName($first_name);
        }
        if ($last_name) {
            $user_obj->setLastName($last_name);
        }
        $user_obj->setEmail($email);
        $user_obj->setPassw(Auth::getHash($new_password_first));

        $confirm_code = UsersUtils::generateConfirmCode();
        $user_obj->setConfirmCode($confirm_code);

        $user_service->save($user_obj);

        // Roles
        $role_id = ConfWrapper::value('user.default_role_id', 0);

        $user_role_service = UsersServiceProvider::getUserRoleService($this->container);

        $user_role_obj = new UserRole();
        $user_role_obj->setUserId($user_obj->getId());
        $user_role_obj->setRoleId($role_id);
        $user_role_service->save($user_role_obj);

        $auth_service = AuthServiceProvider::getAuthService($this->container);
        $auth_service->sendConfirmMail($name, $email, $confirm_code);

        $message = 'Вы успешно зарегистрированы на сайте. ';
        $message .= 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        Messages::setMessage($message);

        return $response->withRedirect($destination);
    }
}
