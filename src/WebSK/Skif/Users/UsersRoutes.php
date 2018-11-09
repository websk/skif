<?php

namespace WebSK\Skif\Users;

use Slim\App;
use WebSK\Skif\Users\Middleware\CurrentUserHasRightToEditUser;
use WebSK\Skif\Users\Middleware\CurrentUserIsAdmin;
use WebSK\Skif\Users\RequestHandlers\Admin\RoleDeleteHandler;
use WebSK\Skif\Users\RequestHandlers\Admin\RoleEditHandler;
use WebSK\Skif\Users\RequestHandlers\Admin\RoleSaveHandler;
use WebSK\Skif\Users\RequestHandlers\Admin\UserEditHandler as AdminUserEditHandler;
use WebSK\Skif\Users\RequestHandlers\Admin\UserListHandler;
use WebSK\Skif\Users\RequestHandlers\Admin\RoleListHandler;
use WebSK\Skif\Users\RequestHandlers\UserAddPhotoHandler;
use WebSK\Skif\Users\RequestHandlers\UserCreatePasswordHandler;
use WebSK\Skif\Users\RequestHandlers\UserDeleteHandler;
use WebSK\Skif\Users\RequestHandlers\UserDeletePhotoHandler;
use WebSK\Skif\Users\RequestHandlers\UserEditHandler;
use WebSK\Skif\Users\RequestHandlers\UserSaveHandler;

/**
 * Class UsersRoutes
 * @package WebSK\WebSK\Skif\Users
 */
class UsersRoutes
{
    const ROUTE_NAME_ADMIN_USER_CREATE = 'admin:users:create';
    const ROUTE_NAME_ADMIN_USER_EDIT = 'admin:users:edit';
    const ROUTE_NAME_ADMIN_USER_LIST = 'admin:users:list';

    const ROUTE_NAME_USER_CREATE = 'user:create';
    const ROUTE_NAME_USER_EDIT = 'user:edit';
    const ROUTE_NAME_USER_ADD = 'user:add';
    const ROUTE_NAME_USER_UPDATE = 'user:update';
    const ROUTE_NAME_USER_DELETE = 'user:delete';

    const ROUTE_NAME_USER_CREATE_PASSWORD = 'user:create_password';

    const ROUTE_NAME_USER_ADD_PHOTO = 'user:add_photo';
    const ROUTE_NAME_USER_DELETE_PHOTO = 'user:delete_photo';

    const ROUTE_NAME_ADMIN_ROLE_LIST = 'admin:users:role:list';
    const ROUTE_NAME_ADMIN_ROLE_CREATE = 'admin:users:role:create';
    const ROUTE_NAME_ADMIN_ROLE_EDIT = 'admin:users:role:edit';
    const ROUTE_NAME_ADMIN_ROLE_ADD = 'admin:users:role:add';
    const ROUTE_NAME_ADMIN_ROLE_UPDATE = 'admin:users:role:update';
    const ROUTE_NAME_ADMIN_ROLE_DELETE = 'admin:users:role:delete';

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/users', function (App $app) {
            $app->get('', UserListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_LIST);

            $app->get('/create', AdminUserEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_CREATE);

            $app->get('/edit/{user_id:\d+}', AdminUserEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_USER_EDIT);

            $app->group('/roles', function (App $app) {
                $app->get('', RoleListHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_LIST);

                $app->get('/create', RoleEditHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_CREATE);

                $app->get('/edit/{role_id:\d+}', RoleEditHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_EDIT);

                $app->post('/add', RoleSaveHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_ADD);

                $app->post('/update/{role_id:\d+}', RoleSaveHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_UPDATE);

                $app->get('/delete/{role_id:\d+}', RoleDeleteHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_ROLE_DELETE);
            });
        })->add(new CurrentUserIsAdmin());
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/user', function (App $app) {
            $app->get('/create', UserEditHandler::class)
                ->setName(self::ROUTE_NAME_USER_CREATE);

            $app->get('/edit/{user_id:\d+}', UserEditHandler::class)
                ->setName(self::ROUTE_NAME_USER_EDIT);

            $app->post('/add', UserSaveHandler::class)
                ->setName(self::ROUTE_NAME_USER_ADD);

            $app->post('/update/{user_id:\d+}', UserSaveHandler::class)
                ->setName(self::ROUTE_NAME_USER_UPDATE);

            $app->get('/delete/{user_id:\d+}', UserDeleteHandler::class)
                ->setName(self::ROUTE_NAME_USER_DELETE);

            $app->get('/create_password/{user_id:\d+}', UserCreatePasswordHandler::class)
                ->setName(self::ROUTE_NAME_USER_CREATE_PASSWORD);

            $app->get('/add_photo/{user_id:\d+}', UserAddPhotoHandler::class)
                ->setName(self::ROUTE_NAME_USER_ADD_PHOTO);

            $app->get('/delete_photo/{user_id:\d+}', UserDeletePhotoHandler::class)
                ->setName(self::ROUTE_NAME_USER_DELETE_PHOTO);
        })->add(new CurrentUserHasRightToEditUser());
    }
}
