<?php

namespace Skif\Users;


class UserController
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
     * Вход на сайт
     */
    public function loginFormAction()
    {
        \Skif\Http::exit403if(\Skif\Users\AuthUtils::getCurrentUserId());

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'login_form.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.main'),
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
     * Регистрация на сайте
     */
    public function registrationFormAction()
    {
        \Skif\Http::exit403if(\Skif\Users\AuthUtils::getCurrentUserId());

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'registration_form.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.main'),
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
        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/';

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $email = array_key_exists('email', $_REQUEST) ? $_REQUEST['email'] : '';
        $new_password_first = array_key_exists('new_password_first', $_REQUEST) ? $_REQUEST['new_password_first'] : '';
        $new_password_second = array_key_exists('new_password_second', $_REQUEST) ? $_REQUEST['new_password_second'] : '';

        if (!array_key_exists('captcha', $_REQUEST)) {
            \Skif\Http::redirect($destination);
        }

        if (!\Skif\Captcha\Captcha::checkWithMessage()) {
            \Skif\Http::redirect($destination);
        }

        if (empty($email)) {
            \Skif\Messages::setError('Ошибка! Не указан Email.');
            \Skif\Http::redirect($destination);
        }

        if (empty($name)) {
            \Skif\Messages::setError('Ошибка! Не указано Имя.');
            \Skif\Http::redirect($destination);
        }

        $has_user_id = \Skif\Users\UsersUtils::hasUserByEmail($email);
        if ($has_user_id) {
            \Skif\Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже зарегистрирован.');
            \Skif\Http::redirect($destination);
        }

        if (!$new_password_first && !$new_password_second) {
            \Skif\Messages::setError('Ошибка! Не введен пароль.');
            \Skif\Http::redirect($destination);
        }

        if ($new_password_first || $new_password_second) {
            if ($new_password_first != $new_password_second) {
                \Skif\Messages::setError('Ошибка! Пароль не подтвержден, либо подтвержден неверно.');
                \Skif\Http::redirect($destination);
            }
        }


        $user_obj = new \Skif\Users\User();

        $user_obj->setName($name);
        $user_obj->setEmail($email);
        $user_obj->setPassw(\Skif\Users\AuthUtils::getHash($new_password_first));

        $confirm_code = \Skif\Users\UsersUtils::generateConfirmCode();
        $user_obj->setConfirmCode($confirm_code);

        $user_obj->save();

        // Roles
        $role_id = \Skif\Conf\ConfWrapper::value('user.default_role_id', 0);

        $user_role_obj = new \Skif\Users\UserRole();
        $user_role_obj->setUserId($user_obj->getId());
        $user_role_obj->setRoleId($role_id);
        $user_role_obj->save();

        self::sendConfirmMail($name, $email, $confirm_code);

        $message = 'Вы успешно зарегистрированы на сайте. ';
        $message .= 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        \Skif\Messages::setMessage($message);

        \Skif\Http::redirect($destination);
    }

    protected static function sendConfirmMail($name, $email, $confirm_code)
    {
        $site_email = \Skif\Conf\ConfWrapper::value('site_email');
        $site_url = \Skif\Conf\ConfWrapper::value('site_url');
        $site_name = \Skif\Conf\ConfWrapper::value('site_name');

        $mail_message = 'Здравствуйте, ' . $name . '!<br />';
        $mail_message .= '<p>На сайте ' .  $site_url . ' была создана регистрационная запись, в которой был указал ваш электронный адрес (e-mail).</p>';
        $mail_message .= '<p>Если вы не регистрировались на данном сайте, просто проигнорируйте это сообщение! Аккаунт будет автоматически удален через некоторое время.</p>';
        $mail_message .= '<p>Если это были вы, то для завершения процедуры регистрации, пожалуйста перейдите по ссылке ' . \Skif\Users\UserController::getConfirmUrl($confirm_code) .  ' </p>';

        $mail_message .= '<p>С уважением, администрация сайта' . $site_name . ', ' . $site_url . '</p>';

        $subject = 'Подтверждение регистрации на сайте' . $site_name;
        \Skif\SendMail::mailToUtf8($email, $site_email, $site_name, $subject, $mail_message);
    }

    /**
     * Подтверждение регистрации на сайте
     * @param $confirm_code
     */
    public function confirmRegistrationAction($confirm_code)
    {
        $user_id = \Skif\Users\UsersUtils::getUserIdByConfirmCode($confirm_code);

        $destination = self::getLoginFormUrl();

        if (!$user_id) {
            \Skif\Messages::setError('Ошибка! Неверный код подтверждения. <a href="' . self::getSendConfirmCodeUrl() . '">Выслать код подтверждения повторно.</a>');
            \Skif\Http::redirect($destination);
        }

        $user_obj = \Skif\Users\User::factory($user_id);
        $user_obj->setConfirm(1);
        $user_obj->setConfirmCode('');
        $user_obj->save();

        $message = 'Поздравляем! Процесс регистрации успешно завершен. Теперь вы можете войти на сайт.';

        \Skif\Messages::setMessage($message);
        \Skif\Http::redirect($destination);
    }

    /**
     * Отправка повторно ссылки для подтверждения регистрации на сайте
     */
    public function sendConfirmCodeFormAction()
    {
        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'send_confirm_code_form.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.main'),
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
            \Skif\Http::redirect($destination);
        }

        if (!\Skif\Captcha\Captcha::checkWithMessage()) {
            \Skif\Http::redirect($destination);
        }

        if (empty($email)) {
            \Skif\Messages::setError('Ошибка! Не указан адрес электронной почты (Email).');
            \Skif\Http::redirect($destination);
        }

        if (!\Skif\Users\UsersUtils::hasUserByEmail($email)) {
            \Skif\Messages::setError('Ошибка! Пользователь с таким адресом электронной почты не зарегистрирован на сайте.');
            \Skif\Http::redirect($destination);
        }

        $user_id = \Skif\Users\UsersUtils::getUserIdByEmail($email);

        $user_obj = \Skif\Users\User::factory($user_id);

        if ($user_obj->isConfirm()) {
            \Skif\Messages::setError('Ошибка! Пользователь с таким адресом электронной почты уже зарегистрирован.');
            \Skif\Http::redirect($destination);
        }

        $confirm_code = \Skif\Users\UsersUtils::generateConfirmCode();

        self::sendConfirmMail($user_obj->getName(), $email, $confirm_code);

        $message = 'Для завершения процедуры регистрации, на указанный вами адрес электронной почты, отправлено письмо с ссылкой для подтверждения.';

        \Skif\Messages::setMessage($message);

        \Skif\Http::redirect($destination);
    }

    /**
     * Список пользователей
     */
    public function listAction()
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'users_list.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'content' => $content,
                'title' => 'Пользователи',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    /**
     * Редактирование профиля пользователя
     * @param $user_id
     * @param null $layout_file
     */
    public function editAction($user_id, $layout_file = null)
    {
        if (!$layout_file) {
            $layout_file = \Skif\Conf\ConfWrapper::value('layout.main');
        }

        if ($user_id != 'new') {
            $user_obj = \Skif\Users\User::factory($user_id);

            if (!$user_obj) {
                \Skif\Http::exit404();
            }

            $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();

            if (($current_user_id != $user_id) && !\Skif\Users\AuthUtils::currentUserIsAdmin()) {
                \Skif\Http::exit403();
            }
        }

        $content = '';

        $editor_nav_arr = array();
        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            //$editor_nav_arr = array($user_obj->getEditorUrl() => 'Редактировать');
        }

        $content .= \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'profile_form_edit.tpl.php',
            array('user_id' => $user_id)
        );

        $breadcrumbs_arr = array();

        if ($layout_file == \Skif\Conf\ConfWrapper::value('layout.admin')) {
            $breadcrumbs_arr = array(
                'Пользователи' => '/admin/users'
            );
        }

        echo \Skif\PhpTemplate::renderTemplate(
            $layout_file,
            array(
                'content' => $content,
                'editor_nav_arr' => $editor_nav_arr,
                'title' => 'Редактирование профиля пользователя',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    /**
     * Сохранение данных пользователя
     * @param $user_id
     */
    public function saveAction($user_id)
    {
        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if ($user_id != 'new') {
            $user_obj = \Skif\Users\User::factory($user_id);

            if (!$user_obj) {
                \Skif\Http::exit403();
            }

            if (($current_user_id != $user_id) && !\Skif\Users\AuthUtils::currentUserIsAdmin()) {
                \Skif\Http::exit403();
            }
        } else {
            $user_obj = new \Skif\Users\User();
        }

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/user/edit/' . $user_id;

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $roles_ids_arr = array_key_exists('roles', $_REQUEST) ? $_REQUEST['roles'] : null;
        $confirm = array_key_exists('confirm', $_REQUEST) ? $_REQUEST['confirm'] : '';
        $birthday = array_key_exists('birthday', $_REQUEST) ? $_REQUEST['birthday'] : '';
        $email = array_key_exists('email', $_REQUEST) ? $_REQUEST['email'] : '';
        $phone = array_key_exists('phone', $_REQUEST) ? $_REQUEST['phone'] : '';
        $city = array_key_exists('city', $_REQUEST) ? $_REQUEST['city'] : '';
        $address = array_key_exists('address', $_REQUEST) ? $_REQUEST['address'] : '';
        $comment = array_key_exists('comment', $_REQUEST) ? $_REQUEST['comment'] : '';
        $new_password_first = array_key_exists('new_password_first', $_REQUEST) ? $_REQUEST['new_password_first'] : '';
        $new_password_second = array_key_exists('new_password_second', $_REQUEST) ? $_REQUEST['new_password_second'] : '';

        if (empty($email)) {
            \Skif\Messages::setError('Ошибка! Не указан Email.');
            \Skif\Http::redirect($destination);
        }

        if (empty($name)) {
            \Skif\Messages::setError('Ошибка! Не указаны Фамилия Имя Отчество.');
            \Skif\Http::redirect($destination);
        }

        /*
        if (!\Skif\Users\UsersUtils::checkBirthDay::checkBirthDay($birthday)) {
            \Skif\Messages::setError('Указана неверная дата рождения');
            \Skif\Http::redirect($destination);
        }
        */

        if ($user_id == 'new') {
            $has_user_id = \Skif\Users\UsersUtils::hasUserByEmail($email);
            if ($has_user_id) {
                \Skif\Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
                \Skif\Http::redirect($destination);
            }

            if (!$new_password_first && !$new_password_second) {
                \Skif\Messages::setError('Ошибка! Не введен пароль.');
                \Skif\Http::redirect($destination);
            }
        } else {
            $has_user_id = \Skif\Users\UsersUtils::hasUserByEmail($email, $user_id);
            if ($has_user_id) {
                \Skif\Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
                \Skif\Http::redirect($destination);
            }
        }

        // Пароль
        if ($new_password_first || $new_password_second) {
            if ($new_password_first != $new_password_second) {
                \Skif\Messages::setError('Ошибка! Пароль не подтвержден, либо подтвержден неверно.');
                \Skif\Http::redirect($destination);
            }

            $user_obj->setPassw(\Skif\Users\AuthUtils::getHash($new_password_first));
        }

        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            $user_obj->setConfirm($confirm);
        }

        $user_obj->setName($name);
        $user_obj->setBirthday($birthday);
        $user_obj->setPhone($phone);
        $user_obj->setEmail($email);
        $user_obj->setCity($city);
        $user_obj->setAddress($address);
        $user_obj->setComment($comment);
        $user_obj->save();


        // Roles
        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            $user_obj->deleteUserRoles();

            if ($roles_ids_arr) {
                foreach ($roles_ids_arr as $role_id) {
                    $user_role_obj = new \Skif\Users\UserRole();
                    $user_role_obj->setUserId($user_obj->getId());
                    $user_role_obj->setRoleId($role_id);
                    $user_role_obj->save();
                }
            }
        }

        // Image
        if (array_key_exists('image_file', $_FILES)) {
            $file = $_FILES['image_file'];
            if (array_key_exists('name', $file) && !empty($file['name'])) {
                $root_images_folder = \Skif\Image\ImageConstants::IMG_ROOT_FOLDER;
                $file_name = \Skif\Image\ImageController::processUpload($file, 'user', $root_images_folder);
                if (!$file_name) {
                    \Skif\Messages::setError('Не удалось загрузить фотографию.');
                    \Skif\Http::redirect('/user/edit/' . $user_obj->getId());
                }

                $user_obj = \Skif\Users\User::factory($user_id);
                $user_obj->setPhoto($file_name);
                $user_obj->save();
            }
        }

        \Skif\Messages::setMessage('Информация о пользователе была успешно сохранена');

        $destination = str_replace('/new', '/' . $user_obj->getId(), $destination);

        \Skif\Http::redirect($destination);
    }

    /**
     * Отправка пароля пользователю
     * @param $user_id
     */
    public static function createAndSendPasswordToUserAction($user_id)
    {
        $user_obj = \Skif\Users\User::factory($user_id);

        if (!$user_obj) {
            \Skif\Http::exit404();
        }

        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if (($current_user_id != $user_id) && !\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            \Skif\Http::exit403();
        }

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/user/edit/' . $user_id;

        $new_password = \Skif\Users\UsersUtils::generatePassword(8);

        $user_obj = \Skif\Users\User::factory($user_id);
        $user_obj->setPassw(\Skif\Users\AuthUtils::getHash($new_password));
        $user_obj->save();

        if ($user_obj->getEmail()) {
            $message = "Добрый день, " . $user_obj->getName() . "\n";
            $message .= "Ваш новый пароль на " . \Skif\Conf\ConfWrapper::value('site_name'). " " . $new_password .". Ваш email для входа".  $user_obj->getEmail() . "\n";
            $message .= 'http://' . \Skif\Conf\ConfWrapper::value('site_url');

            $subj = "Смена пароля на " . \Skif\Conf\ConfWrapper::value('site_name');

            \Skif\SendMail::mailToUtf8($user_obj->getEmail(), \Skif\Conf\ConfWrapper::value('site_email'), \Skif\Conf\ConfWrapper::value('site_name'), $subj, $message);
        }

        \Skif\Messages::setMessage('Новый пароль: ' .  $new_password);

        \Skif\Http::redirect($destination);
    }

    /**
     * Добавление фотографии пользователя
     * @param $user_id
     */
    public static function addPhotoAction($user_id)
    {
        $user_obj = \Skif\Users\User::factory($user_id);

        if (!$user_obj) {
            \Skif\Http::exit404();
        }

        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if (($current_user_id != $user_id) && !\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            \Skif\Http::exit403();
        }

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/user/edit/' . $user_id;

        $root_images_folder = \Skif\Image\ImageConstants::IMG_ROOT_FOLDER;
        $file = $_FILES['image_file'];
        $file_name = \Skif\Image\ImageController::processUpload($file, 'user', $root_images_folder);
        if (!$file_name) {
            \Skif\Messages::setError('Не удалось загрузить фотографию.');
            \Skif\Http::redirect('/user/edit/' . $user_obj->getId());
        }

        $user_obj = \Skif\Users\User::factory($user_id);
        $user_obj->setPhoto($file_name);
        $user_obj->save();

        \Skif\Messages::setMessage('Фотография была успешно добавлена');

        \Skif\Http::redirect($destination);
    }

    /**
     * Удаление фотографии пользователя
     * @param $user_id
     */
    public static function deletePhotoAction($user_id)
    {
        $user_obj = \Skif\Users\User::factory($user_id);

        if (!$user_obj) {
            \Skif\Http::exit404();
        }

        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if (($current_user_id != $user_id) && !\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            \Skif\Http::exit403();
        }

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/user/edit/' . $user_id;

        if (!$user_obj->deletePhoto()) {
            \Skif\Messages::setError('Не удалось удалить фотографию.');
            \Skif\Http::redirect($destination);
        }

        \Skif\Messages::setMessage('Фотография была успешно удалена');

        \Skif\Http::redirect($destination);
    }

    /**
     * Удаление пользователя
     * @param $user_id
     */
    public function deleteAction($user_id)
    {
        $user_obj = \Skif\Users\User::factory($user_id);

        if (!$user_obj) {
            \Skif\Http::exit404();
        }

        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if (($current_user_id != $user_id) && !\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            \Skif\Http::exit403();
        }

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/';

        $user_obj->delete();

        \Skif\Messages::setMessage('Пользователь ' . $user_obj->getName() . ' был успешно удален');

        \Skif\Http::redirect($destination);
    }

    /**
     * Список ролей
     */
    public function listUsersRolesAction()
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'roles_list.tpl.php'
        );

        $breadcrumbs_arr = array(
            'Пользователи' => '/admin/users'
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'content' => $content,
                'title' => 'Роли пользователей',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    /**
     * Редактирование роли
     * @param $role_id
     */
    public function editUsersRoleAction($role_id)
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'role_form_edit.tpl.php',
            array('role_id' => $role_id)
        );

        $breadcrumbs_arr = array(
            'Пользователи' => '/admin/users',
            'Роли пользователей' => '/admin/users/roles',
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'content' => $content,
                'title' => 'Редактирование роли пользователей',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    /**
     * Сохранение роли
     * @param $role_id
     */
    public function saveUsersRoleAction($role_id)
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());


        if ($role_id == 'new') {
            $role_obj = new \Skif\Users\Role;
        } else {
            $role_obj = \Skif\Users\Role::factory($role_id);
        }

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $designation = array_key_exists('designation', $_REQUEST) ? $_REQUEST['designation'] : '';

        $role_obj->setName($name);
        $role_obj->setDesignation($designation);
        $role_obj->save();

        \Skif\Messages::setMessage('Изменения сохранены');

        \Skif\Http::redirect('/admin/users/roles');
    }

    /**
     * Удаление роли
     * @param $role_id
     */
    public function deleteUsersRoleAction($role_id)
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());


        $role_obj = \Skif\Users\Role::factory($role_id);

        $user_ids_arr = \Skif\Users\UsersUtils::getUsersIdsArr($role_id);

        if (!empty($user_ids_arr)) {
            \Skif\Messages::setError('Нельзя удалить роль ' . $role_obj->getName() . ', т.к. она назначена пользователям');
            \Skif\Http::redirect('/admin/users/roles');
        }

        $role_obj->delete();

        \Skif\Messages::setMessage('Роль ' . $role_obj->getName() . ' была успешно удалена');

        \Skif\Http::redirect('/admin/users/roles');
    }
}