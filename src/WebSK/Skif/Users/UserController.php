<?php

namespace WebSK\Skif\Users;

use WebSK\Skif\ConfWrapper;
use Skif\Http;
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
