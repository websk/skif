<?php

namespace Skif\Users;


class UserController
{
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

        $admin_nav_arr = array();
        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            //$admin_nav_arr = array($user_obj->getEditorUrl() => 'Редактировать');
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
                'admin_nav_arr' => $admin_nav_arr,
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
            $query_true = "SELECT id FROM users WHERE email=? LIMIT 1";
            $has_user_id = \Skif\DB\DBWrapper::readField($query_true, array($email));
            if ($has_user_id) {
                \Skif\Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
                \Skif\Http::redirect($destination);
            }

            if (!$new_password_first && !$new_password_second) {
                \Skif\Messages::setError('Ошибка! Не введен пароль.');
                \Skif\Http::redirect($destination);
            }
        } else {
            $query_true = "SELECT id FROM users WHERE id!=? and email=? LIMIT 1";
            $has_user_id = \Skif\DB\DBWrapper::readField($query_true, array($user_id, $email));
            if ($has_user_id) {
                \Skif\Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
                \Skif\Http::redirect($destination);
            }
        }

        $user_obj->setName($name);
        $user_obj->setBirthday($birthday);
        $user_obj->setPhone($phone);
        $user_obj->setEmail($email);
        $user_obj->setCity($city);
        $user_obj->setAddress($address);
        $user_obj->setComment($comment);

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

        $user_obj->save();


        // Roles
        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            \Skif\DB\DBWrapper::query(
                "DELETE FROM users_roles WHERE user_id = ?",
                array($user_obj->getId())
            );

            if ($roles_ids_arr) {
                foreach ($roles_ids_arr as $role_id) {
                    $query = "INSERT INTO users_roles SET role_id=?, user_id=?";
                    \Skif\DB\DBWrapper::query($query, array($role_id, $user_obj->getId()));
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

    public function saveUsersRoleAction($role_id)
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());


        if ($role_id == 'new') {
            $users_role_obj = new \Skif\Users\Role;
        } else {
            $users_role_obj = \Skif\Users\Role::factory($role_id);
        }

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $designation = array_key_exists('designation', $_REQUEST) ? $_REQUEST['designation'] : '';

        $users_role_obj->setName($name);
        $users_role_obj->setDesignation($designation);
        $users_role_obj->save();

        \Skif\Messages::setMessage('Изменения сохранены');

        \Skif\Http::redirect('/admin/users/roles');
    }

    public function deleteUsersRoleAction($role_id)
    {
        \Skif\Http::exit403if(!\Skif\Users\AuthUtils::currentUserIsAdmin());


        $users_role_obj = \Skif\Users\Role::factory($role_id);

        $query = "SELECT user_id FROM users_roles WHERE role_id=? LIMIT 1";
        $has_users = \Skif\DB\DBWrapper::readField($query, array($role_id));

        if ($has_users) {
            \Skif\Messages::setError('Нельзя удалить роль ' . $users_role_obj->getName() . ', т.к. она назначена пользователям');
            \Skif\Http::redirect('/admin/users/roles');
        }

        $users_role_obj->delete();

        \Skif\Messages::setMessage('Роль ' . $users_role_obj->getName() . ' была успешно удалена');

        \Skif\Http::redirect('/admin/users/roles');
    }
}