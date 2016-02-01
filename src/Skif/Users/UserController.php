<?php

namespace Skif\Users;


class UserController
{

    /**
     * URL формы редактирования профиля
     * @param $user_id
     * @return string
     */
    public static function getEditProfileUrl($user_id)
    {
        return '/user/edit/' . $user_id;
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
                'title' => 'Редактирование профиля',
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
        $first_name = array_key_exists('first_name', $_REQUEST) ? $_REQUEST['first_name'] : '';
        $last_name = array_key_exists('last_name', $_REQUEST) ? $_REQUEST['last_name'] : '';
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

            $user_obj->setCreatedAt(date('Y-m-d H:i:s'));
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
        $user_obj->setFirstName($first_name);
        $user_obj->setLastName($last_name);
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

    public function createPasswordAction($user_id)
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : self::getEditProfileUrl($user_id);

        $new_password = \Skif\Users\UserController::createAndSendPasswordToUser($user_id);

        \Skif\Messages::setMessage('Новый пароль' . $new_password);

        \Skif\Http::redirect($destination);
    }

    /**
     * Смена и отправка пароля пользователю
     * @param $user_id
     * @return string
     */
    public static function createAndSendPasswordToUser($user_id)
    {
        $new_password = \Skif\Users\UsersUtils::generatePassword(8);

        $user_obj = \Skif\Users\User::factory($user_id);
        $user_obj->setPassw(\Skif\Users\AuthUtils::getHash($new_password));
        $user_obj->save();

        if ($user_obj->getEmail()) {
            $site_email = \Skif\Conf\ConfWrapper::value('site_email');
            $site_url = \Skif\Conf\ConfWrapper::value('site_url');
            $site_name = \Skif\Conf\ConfWrapper::value('site_name');

            $message = "<p>Добрый день, " . $user_obj->getName() . "</p>";
            $message .= "<p>Вы воспользовались формой восстановления пароля на сайте " . $site_name. "</p>";
            $message .= "<p>Ваш новый пароль: " . $new_password  . "<br>";
            $message .= "Ваш email для входа: ".  $user_obj->getEmail() . "</p>";
            $message .= "<p>Рекомендуем сменить пароль после входа на сайт.</p>";
            $message .= '<p>http://' . $site_url . "</p>";

            $subj = "Смена пароля на сайте " . \Skif\Conf\ConfWrapper::value('site_name');

            \Skif\SendMail::mailToUtf8($user_obj->getEmail(), $site_email, $site_name, $subj, $message);
        }

        return $new_password;
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