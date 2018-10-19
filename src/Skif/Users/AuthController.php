<?php

namespace Skif\Users;

use Skif\Captcha\Captcha;
use Skif\Conf\ConfWrapper;
use Skif\Http;
use Websk\Skif\Messages;
use Skif\PhpTemplate;
use Skif\Utils;

class AuthController
{
    /**
     * URL формы входа на сайт
     * @return string
     */
    public static function getLoginFormUrl()
    {
        return '/user/login_form';
    }

    /**
     * URL авторизации на сайте
     * @return string
     */
    public static function getLoginUrl()
    {
        return '/user/login';
    }

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
     * URL формы регистрации на сайте
     * @return string
     */
    public static function getRegistrationFormUrl()
    {
        return '/user/registration_form';
    }

    /**
     * URL регистрации на сайте
     * @return string
     */
    public static function getRegistrationUrl()
    {
        return '/user/registration';
    }

    /**
     * URL деавторизации
     * @return string
     */
    public static function getLogoutUrl()
    {
        return '/user/logout';
    }

    /**
     * URL восстановления пароля
     * @return string
     */
    public static function getForgotPasswordUrl()
    {
        return '/user/forgot_password';
    }

    /**
     * URL формы восстановления пароля
     * @return string
     */
    public static function getForgotPasswordFormUrl()
    {
        return '/user/forgot_password_form';
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
     * Регистрация на сайте
     */
    public function registrationFormAction()
    {
        $current_user_id = AuthUtils::getCurrentUserId();
        if ($current_user_id) {
            Http::redirect(UserController::getEditProfileUrl($current_user_id));
        }

        $content = '';

        if (AuthUtils::useSocialLogin()) {
            $content .= PhpTemplate::renderTemplateBySkifModule(
                'Users',
                'social_buttons.tpl.php'
            );
        }

        $content .= PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'registration_form.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo PhpTemplate::renderTemplate(
            ConfWrapper::value('layout.main'),
            array(
                'content' => $content,
                'title' => 'Регистрация на сайте',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function registrationAction()
    {
        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : self::getLoginFormUrl();

        $name = array_key_exists('name', $_REQUEST) ? trim($_REQUEST['name']) : '';
        $first_name = array_key_exists('first_name', $_REQUEST) ? trim($_REQUEST['first_name']) : '';
        $last_name = array_key_exists('last_name', $_REQUEST) ? trim($_REQUEST['last_name']) : '';
        $email = array_key_exists('email', $_REQUEST) ? trim($_REQUEST['email']) : '';
        $new_password_first = array_key_exists('new_password_first', $_REQUEST) ? $_REQUEST['new_password_first'] : '';
        $new_password_second = array_key_exists('new_password_second', $_REQUEST) ? $_REQUEST['new_password_second'] : '';

        $error_destination = self::getRegistrationFormUrl();

        if (!array_key_exists('captcha', $_REQUEST)) {
            Http::redirect($error_destination);
        }

        if (!Captcha::checkWithMessage()) {
            Http::redirect($error_destination);
        }

        if (empty($email)) {
            Messages::setError('Ошибка! Не указан Email.');
            Http::redirect($error_destination);
        }

        if (empty($name)) {
            Messages::setError('Ошибка! Не указано Имя.');
            Http::redirect($error_destination);
        }

        $has_user_id = UsersUtils::hasUserByEmail($email);
        if ($has_user_id) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже зарегистрирован.');
            Http::redirect($error_destination);
        }

        if (!$new_password_first && !$new_password_second) {
            Messages::setError('Ошибка! Не введен пароль.');
            Http::redirect($error_destination);
        }

        if ($new_password_first || $new_password_second) {
            if ($new_password_first != $new_password_second) {
                Messages::setError('Ошибка! Пароль не подтвержден, либо подтвержден неверно.');
                Http::redirect($error_destination);
            }
        }


        $user_obj = new User();

        $user_obj->setName($name);
        if ($first_name) {
            $user_obj->setFirstName($first_name);
        }
        if ($last_name) {
            $user_obj->setLastName($last_name);
        }
        $user_obj->setEmail($email);
        $user_obj->setPassw(AuthUtils::getHash($new_password_first));

        $confirm_code = UsersUtils::generateConfirmCode();
        $user_obj->setConfirmCode($confirm_code);

        $user_obj->setCreatedAt(date('Y-m-d H:i:s'));

        $user_obj->save();

        // Roles
        $role_id = ConfWrapper::value('user.default_role_id', 0);

        $user_role_obj = new UserRole();
        $user_role_obj->setUserId($user_obj->getId());
        $user_role_obj->setRoleId($role_id);
        $user_role_obj->save();

        self::sendConfirmMail($name, $email, $confirm_code);

        $message = 'Вы успешно зарегистрированы на сайте. ';
        $message .= 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        Messages::setMessage($message);

        Http::redirect($destination);
    }

    protected static function sendConfirmMail($name, $email, $confirm_code)
    {
        $site_email = ConfWrapper::value('site_email');
        $site_domain = ConfWrapper::value('site_domain');
        $site_name = ConfWrapper::value('site_name');

        $confirm_url = $site_domain . self::getConfirmUrl($confirm_code);

        $mail_message = 'Здравствуйте, ' . $name . '!<br />';
        $mail_message .= '<p>На сайте ' .  $site_domain . ' была создана регистрационная запись, в которой был указал ваш электронный адрес (e-mail).</p>';
        $mail_message .= '<p>Если вы не регистрировались на данном сайте, просто проигнорируйте это сообщение! Аккаунт будет автоматически удален через некоторое время.</p>';
        $mail_message .= '<p>Если это были вы, то для завершения процедуры регистрации, пожалуйста перейдите по ссылке <a href="' . $confirm_url .  '">' . $confirm_url .  '</a></p>';

        $mail_message .= '<p>С уважением, администрация сайта ' . $site_name . ', ' . $site_domain . '</p>';

        $subject = 'Подтверждение регистрации на сайте ' . $site_name;

        $mail = new \PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($site_email, $site_name);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $mail_message;
        $mail->AltBody = Utils::checkPlain($mail_message);
        $mail->send();
    }

    /**
     * Подтверждение регистрации на сайте
     * @param $confirm_code
     */
    public function confirmRegistrationAction($confirm_code)
    {
        $user_id = UsersUtils::getUserIdByConfirmCode($confirm_code);

        $destination = self::getLoginFormUrl();

        if (!$user_id) {
            Messages::setError('Ошибка! Неверный код подтверждения. <a href="' . self::getSendConfirmCodeUrl() . '">Выслать код подтверждения повторно.</a>');
            Http::redirect($destination);
        }

        $user_obj = User::factory($user_id);
        $user_obj->setConfirm(1);
        $user_obj->setConfirmCode('');
        $user_obj->save();

        $message = 'Поздравляем! Процесс регистрации успешно завершен. Теперь вы можете войти на сайт.';

        Messages::setMessage($message);
        Http::redirect($destination);
    }

    /**
     * Отправка повторно ссылки для подтверждения регистрации на сайте
     */
    public function sendConfirmCodeFormAction()
    {
        $content = PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'send_confirm_code_form.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo PhpTemplate::renderTemplate(
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

        $user_obj = User::factory($user_id);

        if ($user_obj->isConfirm()) {
            Messages::setError('Ошибка! Пользователь с таким адресом электронной почты уже зарегистрирован.');
            Http::redirect($destination);
        }

        $confirm_code = UsersUtils::generateConfirmCode();

        self::sendConfirmMail($user_obj->getName(), $email, $confirm_code);

        $message = 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        Messages::setMessage($message);

        Http::redirect($destination);
    }

    public function forgotPasswordFormAction()
    {
        $content = PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'forgot_password_form.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo PhpTemplate::renderTemplate(
            ConfWrapper::value('layout.main'),
            array(
                'content' => $content,
                'title' => 'Восстановление пароля',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function forgotPasswordAction()
    {
        $email = array_key_exists('email', $_REQUEST) ? $_REQUEST['email'] : '';

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : self::getForgotPasswordFormUrl();

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

        $user_obj = User::factory($user_id);

        UserController::createAndSendPasswordToUser($user_id);

        $message = 'Временный пароль отправлен на указанный вами адрес электронной почты.';

        Messages::setMessage($message);

        Http::redirect(self::getLoginFormUrl());
    }

    /**
     * Вход на сайт
     */
    public function loginFormAction()
    {
        $current_user_id = AuthUtils::getCurrentUserId();
        if ($current_user_id) {
            Http::redirect(UserController::getEditProfileUrl($current_user_id));
        }

        $content = '';

        if (AuthUtils::useSocialLogin()) {
            $content .= PhpTemplate::renderTemplateBySkifModule(
                'Users',
                'social_buttons.tpl.php'
            );
        }

        $content .= PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'login_form.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo PhpTemplate::renderTemplate(
            ConfWrapper::value('layout.main'),
            array(
                'content' => $content,
                'title' => 'Вход на сайт',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    /**
     * Проверка авторизации
     */
    public static function loginAction()
    {
        if (array_key_exists('email', $_REQUEST) && array_key_exists('password', $_REQUEST)) {
            $save_auth = array_key_exists('save_auth', $_REQUEST) ? true : false;
            AuthUtils::doLogin($_REQUEST['email'], $_REQUEST['password'], $save_auth);

            $destination = '/';
            if (isset($_REQUEST['destination'])) {
                $destination = $_REQUEST['destination'];
            }

            Http::redirect($destination);
        }
    }

    public function logoutAction()
    {
        AuthUtils::logout();

        $destination = '/';
        if (isset($_REQUEST['destination'])) {
            $destination = $_REQUEST['destination'];
        }

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
