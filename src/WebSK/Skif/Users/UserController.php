<?php

namespace WebSK\Skif\Users;

use WebSK\Skif\ConfWrapper;
use Skif\Http;
use Skif\Image\ImageConstants;
use Skif\Image\ImageController;
use Websk\Skif\Container;
use Websk\Skif\Messages;
use Skif\PhpTemplate;

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
