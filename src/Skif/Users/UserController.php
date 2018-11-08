<?php

namespace Skif\Users;

use WebSK\Skif\ConfWrapper;
use Skif\Http;
use Skif\Image\ImageConstants;
use Skif\Image\ImageController;
use Websk\Skif\Container;
use Websk\Skif\Messages;
use Skif\PhpTemplate;
use Skif\Utils;
use WebSK\Skif\Users\AuthUtils;
use WebSK\Skif\Users\Role;
use WebSK\Skif\Users\User;
use WebSK\Skif\Users\UserRole;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Skif\Users\UsersUtils;

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

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = $user_service->getById($user_id);
        $user_obj->setPassw(AuthUtils::getHash($new_password));
        $user_service->save($user_obj);

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
        if (!$user_id) {
            Http::exit404();
        }

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = $user_service->getById($user_id);

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

        $user_obj = $user_service->getById($user_id);
        $user_obj->setPhoto($file_name);

        $user_service->save($user_obj);

        Messages::setMessage('Фотография была успешно добавлена');

        Http::redirect($destination);
    }

    /**
     * Удаление фотографии пользователя
     * @param $user_id
     */
    public static function deletePhotoAction($user_id)
    {
        if (!$user_id) {
            Http::exit404();
        }

        $current_user_id = AuthUtils::getCurrentUserId();

        if (($current_user_id != $user_id) && !AuthUtils::currentUserIsAdmin()) {
            Http::exit403();
        }

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = $user_service->getById($user_id);

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/user/edit/' . $user_id;

        if (!$user_service->deletePhoto($user_obj)) {
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

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = $user_service->getById($user_id, false);
        Http::exit404If(!$user_obj);

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/';

        $user_service->delete($user_obj);

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

        $container = Container::self();

        $role_service = UsersServiceProvider::getRoleService($container);

        if ($role_id == 'new') {
            $role_obj = new Role;
        } else {
            $role_obj = $role_service->getById($role_id);
        }

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $designation = array_key_exists('designation', $_REQUEST) ? $_REQUEST['designation'] : '';

        $role_obj->setName($name);
        $role_obj->setDesignation($designation);
        $role_service->save($role_obj);

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

        $container = Container::self();

        $role_service = UsersServiceProvider::getRoleService($container);

        $role_obj = $role_service->getById($role_id);

        $user_ids_arr = UsersUtils::getUsersIdsArr($role_id);

        if (!empty($user_ids_arr)) {
            Messages::setError('Нельзя удалить роль ' . $role_obj->getName() . ', т.к. она назначена пользователям');
            Http::redirect('/admin/users/roles');
        }

        $role_service->delete($role_obj);

        Messages::setMessage('Роль ' . $role_obj->getName() . ' была успешно удалена');

        Http::redirect('/admin/users/roles');
    }
}
