<?php

namespace Skif\Users;

use WebSK\Skif\ConfWrapper;
use Skif\Http;
use Skif\Image\ImageConstants;
use Skif\Image\ImageController;
use Websk\Skif\Messages;
use Skif\PhpTemplate;
use Skif\Utils;

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
        Http::exit403If(!AuthUtils::currentUserIsAdmin());

        $content = PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'users_list.tpl.php'
        );

        $breadcrumbs_arr = array();

        echo PhpTemplate::renderTemplate(
            ConfWrapper::value('layout.admin'),
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
            $layout_file = ConfWrapper::value('layout.main');
        }

        if ($user_id != 'new') {
            $user_obj = User::factory($user_id);

            if (!$user_obj) {
                Http::exit404();
            }

            $current_user_id = AuthUtils::getCurrentUserId();

            if (($current_user_id != $user_id) && !AuthUtils::currentUserIsAdmin()) {
                Http::exit403();
            }
        }

        $content = '';

        $editor_nav_arr = [];
        /*
        if (AuthUtils::currentUserIsAdmin()) {
            $editor_nav_arr = array($user_obj->getEditorUrl() => 'Редактировать');
        }
        */

        $content .= PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'profile_form_edit.tpl.php',
            array('user_id' => $user_id)
        );

        $breadcrumbs_arr = array();

        if ($layout_file == ConfWrapper::value('layout.admin')) {
            $breadcrumbs_arr = array(
                'Пользователи' => '/admin/users'
            );
        }

        echo PhpTemplate::renderTemplate(
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
        $current_user_id = AuthUtils::getCurrentUserId();

        if ($user_id != 'new') {
            $user_obj = User::factory($user_id, false);

            Http::exit403If(!$user_obj);

            if (($current_user_id != $user_id) && !AuthUtils::currentUserIsAdmin()) {
                Http::exit403();
            }
        } else {
            $user_obj = new User();
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
            Messages::setError('Ошибка! Не указан Email.');
            Http::redirect($destination);
        }

        if (empty($name)) {
            Messages::setError('Ошибка! Не указаны Фамилия Имя Отчество.');
            Http::redirect($destination);
        }

        /*
        if (!\Skif\Users\UsersUtils::checkBirthDay::checkBirthDay($birthday)) {
            \Websk\Skif\Messages::setError('Указана неверная дата рождения');
            \Skif\Http::redirect($destination);
        }
        */

        if ($user_id == 'new') {
            $has_user_id = UsersUtils::hasUserByEmail($email);
            if ($has_user_id) {
                Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
                Http::redirect($destination);
            }

            if (!$new_password_first && !$new_password_second) {
                Messages::setError('Ошибка! Не введен пароль.');
                Http::redirect($destination);
            }

            $user_obj->setCreatedAt(date('Y-m-d H:i:s'));
        } else {
            $has_user_id = UsersUtils::hasUserByEmail($email, $user_id);
            if ($has_user_id) {
                Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
                Http::redirect($destination);
            }
        }

        // Пароль
        if ($new_password_first || $new_password_second) {
            if ($new_password_first != $new_password_second) {
                Messages::setError('Ошибка! Пароль не подтвержден, либо подтвержден неверно.');
                Http::redirect($destination);
            }

            $user_obj->setPassw(AuthUtils::getHash($new_password_first));
        }

        if (AuthUtils::currentUserIsAdmin()) {
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
        // TODO: убрать
        if (AuthUtils::currentUserIsAdmin()) {
            $user_obj->deleteUserRoles();

            if ($roles_ids_arr) {
                foreach ($roles_ids_arr as $role_id) {
                    $user_role_obj = new UserRole();
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
                $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;
                $file_name = ImageController::processUpload($file, 'user', $root_images_folder);
                if (!$file_name) {
                    Messages::setError('Не удалось загрузить фотографию.');
                    Http::redirect('/user/edit/' . $user_obj->getId());
                }

                $user_obj = User::factory($user_id);
                $user_obj->setPhoto($file_name);
                $user_obj->save();
            }
        }

        Messages::setMessage('Информация о пользователе была успешно сохранена');

        $destination = str_replace('/new', '/' . $user_obj->getId(), $destination);

        Http::redirect($destination);
    }

    /**
     * @param int $user_id
     */
    public function createPasswordAction($user_id)
    {
        Http::exit403if(!AuthUtils::currentUserIsAdmin());

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : self::getEditProfileUrl($user_id);

        $new_password = self::createAndSendPasswordToUser($user_id);

        Messages::setMessage('Новый пароль' . $new_password);

        Http::redirect($destination);
    }

    /**
     * Смена и отправка пароля пользователю
     * @param $user_id
     * @return string
     */
    public static function createAndSendPasswordToUser($user_id)
    {
        $new_password = UsersUtils::generatePassword(8);

        $user_obj = User::factory($user_id);
        $user_obj->setPassw(AuthUtils::getHash($new_password));
        $user_obj->save();

        if ($user_obj->getEmail()) {
            $site_email = ConfWrapper::value('site_email');
            $site_domain = ConfWrapper::value('site_domain');
            $site_name = ConfWrapper::value('site_name');

            $mail_message = "<p>Добрый день, " . $user_obj->getName() . "</p>";
            $mail_message .= "<p>Вы воспользовались формой восстановления пароля на сайте " . $site_name. "</p>";
            $mail_message .= "<p>Ваш новый пароль: " . $new_password  . "<br>";
            $mail_message .= "Ваш email для входа: ".  $user_obj->getEmail() . "</p>";
            $mail_message .= "<p>Рекомендуем сменить пароль после входа на сайт.</p>";
            $mail_message .= '<p>' . $site_domain . "</p>";

            $subject = "Смена пароля на сайте " . ConfWrapper::value('site_name');

            $mail = new \PHPMailer;
            $mail->CharSet = "utf-8";
            $mail->setFrom($site_email, $site_name);
            $mail->addAddress($user_obj->getEmail());
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $mail_message;
            $mail->AltBody = Utils::checkPlain($mail_message);
            $mail->send();
        }

        return $new_password;
    }

    /**
     * Добавление фотографии пользователя
     * @param $user_id
     */
    public static function addPhotoAction($user_id)
    {
        $user_obj = User::factory($user_id);

        if (!$user_obj) {
            Http::exit404();
        }

        $current_user_id = AuthUtils::getCurrentUserId();

        if (($current_user_id != $user_id) && !AuthUtils::currentUserIsAdmin()) {
            Http::exit403();
        }

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/user/edit/' . $user_id;

        $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;
        $file = $_FILES['image_file'];
        $file_name = ImageController::processUpload($file, 'user', $root_images_folder);
        if (!$file_name) {
            Messages::setError('Не удалось загрузить фотографию.');
            Http::redirect('/user/edit/' . $user_obj->getId());
        }

        $user_obj = User::factory($user_id);
        $user_obj->setPhoto($file_name);
        $user_obj->save();

        Messages::setMessage('Фотография была успешно добавлена');

        Http::redirect($destination);
    }

    /**
     * Удаление фотографии пользователя
     * @param $user_id
     */
    public static function deletePhotoAction($user_id)
    {
        $user_obj = User::factory($user_id);

        if (!$user_obj) {
            Http::exit404();
        }

        $current_user_id = AuthUtils::getCurrentUserId();

        if (($current_user_id != $user_id) && !AuthUtils::currentUserIsAdmin()) {
            Http::exit403();
        }

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/user/edit/' . $user_id;

        if (!$user_obj->deletePhoto()) {
            Messages::setError('Не удалось удалить фотографию.');
            Http::redirect($destination);
        }

        Messages::setMessage('Фотография была успешно удалена');

        Http::redirect($destination);
    }

    /**
     * Удаление пользователя
     * @param $user_id
     */
    public function deleteAction($user_id)
    {
        $current_user_id = AuthUtils::getCurrentUserId();

        if (($current_user_id != $user_id) && !AuthUtils::currentUserIsAdmin()) {
            Http::exit403();
        }

        $user_obj = User::factory($user_id, false);
        Http::exit404If(!$user_obj);

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/';

        $user_obj->delete();

        Messages::setMessage('Пользователь ' . $user_obj->getName() . ' был успешно удален');

        Http::redirect($destination);
    }

    /**
     * Список ролей
     */
    public function listUsersRolesAction()
    {
        Http::exit403if(!AuthUtils::currentUserIsAdmin());

        $content = PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'roles_list.tpl.php'
        );

        $breadcrumbs_arr = array(
            'Пользователи' => '/admin/users'
        );

        echo PhpTemplate::renderTemplate(
            ConfWrapper::value('layout.admin'),
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
        Http::exit403if(!AuthUtils::currentUserIsAdmin());

        $content = PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'role_form_edit.tpl.php',
            array('role_id' => $role_id)
        );

        $breadcrumbs_arr = array(
            'Пользователи' => '/admin/users',
            'Роли пользователей' => '/admin/users/roles',
        );

        echo PhpTemplate::renderTemplate(
            ConfWrapper::value('layout.admin'),
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
        Http::exit403if(!AuthUtils::currentUserIsAdmin());


        if ($role_id == 'new') {
            $role_obj = new Role;
        } else {
            $role_obj = Role::factory($role_id);
        }

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $designation = array_key_exists('designation', $_REQUEST) ? $_REQUEST['designation'] : '';

        $role_obj->setName($name);
        $role_obj->setDesignation($designation);
        $role_obj->save();

        Messages::setMessage('Изменения сохранены');

        Http::redirect('/admin/users/roles');
    }

    /**
     * Удаление роли
     * @param $role_id
     */
    public function deleteUsersRoleAction($role_id)
    {
        Http::exit403if(!AuthUtils::currentUserIsAdmin());


        $role_obj = Role::factory($role_id);

        $user_ids_arr = UsersUtils::getUsersIdsArr($role_id);

        if (!empty($user_ids_arr)) {
            Messages::setError('Нельзя удалить роль ' . $role_obj->getName() . ', т.к. она назначена пользователям');
            Http::redirect('/admin/users/roles');
        }

        $role_obj->delete();

        Messages::setMessage('Роль ' . $role_obj->getName() . ' была успешно удалена');

        Http::redirect('/admin/users/roles');
    }
}
