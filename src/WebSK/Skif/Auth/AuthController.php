<?php

namespace WebSK\Skif\Auth;

use Websk\Skif\Captcha\Captcha;
use WebSK\Skif\ConfWrapper;
use Skif\Http;
use Websk\Skif\Container;
use Websk\Skif\Messages;
use Skif\Utils;
use WebSK\Skif\PhpRender;
use WebSK\Skif\Router;
use WebSK\Skif\Users\AuthUtils;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Skif\Users\UsersUtils;

/**
 * Class AuthController
 * @package WebSK\Skif\Users
 */
class AuthController
{
    /**
     * URL авторизации на сайте через внешнего провайдера социальной сети
     * @param $provider
     * @return string
     */
    public static function getSocialLoginUrl($provider)
    {
        return '/user/social_login/' . $provider;
    }

    /**
     * URL подтверждения регистрации на сайте
     * @param $confirm_code
     * @return string
     */
    public static function getConfirmUrl($confirm_code)
    {
        return '/user/confirm_registration/' . $confirm_code;
    }

    /**
     * URL отправки повторно кода подтверждения регистрации на сайте
     * @return string
     */
    public static function getSendConfirmCodeUrl()
    {
        return '/user/send_confirm_code';
    }

    /**
     * URL формы отправки повторно кода подтверждения регистрации на сайте
     * @return string
     */
    public static function getSendConfirmCodeFormUrl()
    {
        return '/user/send_confirm_code_form';
    }

    /**
     * Подтверждение регистрации на сайте
     * @param $confirm_code
     */
    public function confirmRegistrationAction($confirm_code)
    {
        $user_id = UsersUtils::getUserIdByConfirmCode($confirm_code);

        $destination = Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM);

        if (!$user_id) {
            Messages::setError('Ошибка! Неверный код подтверждения. <a href="' . self::getSendConfirmCodeUrl() . '">Выслать код подтверждения повторно.</a>');
            Http::redirect($destination);
        }

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = $user_service->getById($user_id);
        $user_obj->setConfirm(1);
        $user_obj->setConfirmCode('');
        $user_service->save($user_obj);

        $message = 'Поздравляем! Процесс регистрации успешно завершен. Теперь вы можете войти на сайт.';

        Messages::setMessage($message);
        Http::redirect($destination);
    }

    /**
     * Отправка повторно ссылки для подтверждения регистрации на сайте
     */
    public function sendConfirmCodeFormAction()
    {
        $content = PhpRender::renderTemplateBySkifModule(
            'Users',
            'send_confirm_code_form.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo PhpRender::renderTemplate(
            ConfWrapper::value('layout.main'),
            array(
                'content' => $content,
                'title' => 'Подтверждение регистрации на сайте',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function sendConfirmCodeAction()
    {
        $email = array_key_exists('email', $_REQUEST) ? $_REQUEST['email'] : '';

        $destination = self::getSendConfirmCodeFormUrl();

        if (!array_key_exists('captcha', $_REQUEST)) {
            Http::redirect($destination);
        }

        if (!Captcha::checkWithMessage()) {
            Http::redirect($destination);
        }

        if (empty($email)) {
            Messages::setError('Ошибка! Не указан адрес электронной почты (Email).');
            Http::redirect($destination);
        }

        if (!UsersUtils::hasUserByEmail($email)) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты не зарегистрирован на сайте.');
            Http::redirect($destination);
        }

        $user_id = UsersUtils::getUserIdByEmail($email);

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = $user_service->getById($user_id);

        if ($user_obj->isConfirm()) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты уже зарегистрирован.');
            Http::redirect($destination);
        }

        $confirm_code = UsersUtils::generateConfirmCode();

        $auth_service = AuthServiceProvider::getAuthService($container);
        $auth_service->sendConfirmMail($user_obj->getName(), $email, $confirm_code);

        $message = 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        Messages::setMessage($message);

        Http::redirect($destination);
    }

    public function socialAuthAction($request_provider)
    {
        $destination = '/';
        if (array_key_exists('destination', $_REQUEST)) {
            $destination = $_REQUEST['destination'];
        }


        $provider = AuthUtils::socialLogin($request_provider, $destination);
        if (!$provider) {
            Http::redirect($destination);
        }

        $is_connected = $provider->isUserConnected();
        if (!$is_connected) {
            Messages::setError("Не удалось соединиться с " . $request_provider);
            Http::redirect($destination);
        }

        /**
         * @var \Hybrid_User_Profile $user_profile
         */
        $user_profile = $provider->getUserProfile();

        $user_id = AuthUtils::getUserIdIfExistByProvider(
            $request_provider,
            $user_profile->identifier
        );

        // Пользователь не найден в базе, регистрируем
        if (!$user_id) {
            if ($user_profile->email) {
                $user_id = UsersUtils::getUserIdByEmail($user_profile->email);

                if ($user_id) {
                    Messages::setError("Пользователь с таким адресом электронной почты " . $user_profile->email . ' уже зарегистрирован');
                    Http::redirect($destination);
                }
            }

            $user_id = AuthUtils::registerUserByHybridAuthProfile(
                $user_profile,
                $request_provider
            );

            if (!$user_id) {
                Messages::setError("Не удалось зарегистрировать нового пользователя");
                Http::redirect($destination);
            }
        }

        $session = sha1(time() . $user_id);
        $delta = time() + AuthUtils::SESSION_LIFE_TIME;

        AuthUtils::storeUserSession($user_id, $session, $delta);

        Http::redirect($destination);
    }

    public function gateAction()
    {
        \Hybrid_Endpoint::process();
    }

    /*
    public function sessionAction()
    {
        \Skif\CRUDUtils::sendJsonHeaders();

        $current_user_obj = \Skif\Auth\AuthHelper::getCurrentUser();
        if (!$current_user_obj) {
            return '{}';
        }

        echo json_encode($current_user_obj);
        return;
    }
    */
}
