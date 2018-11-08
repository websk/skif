<?php

namespace WebSK\Skif\Users;

use Slim\App;
use WebSK\Skif\Users\Middleware\CurrentUserHasRightToEditUser;
use WebSK\Skif\Users\RequestHandlers\Admin\UserEditHandler as AdminUserEditHandler;
use WebSK\Skif\Users\RequestHandlers\Admin\UserListHandler;
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
    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/users', function (App $app) {
            $app->get('/edit/{user_id:\d+}', AdminUserEditHandler::class)
                ->setName(AdminUserEditHandler::class);

            $app->get('', UserListHandler::class)
                ->setName(UserListHandler::class);
        });
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/user', function (App $app) {
            $app->get('/edit/{user_id:\d+}', UserEditHandler::class)
                ->setName(UserEditHandler::class);

            $app->post('/save/{user_id:\d+}', UserSaveHandler::class)
                ->setName(UserSaveHandler::class);

            $app->post('/delete/{user_id:\d+}', UserDeleteHandler::class)
                ->setName(UserDeleteHandler::class);

            $app->get('/create_password/{user_id:\d+}', UserCreatePasswordHandler::class)
                ->setName(UserCreatePasswordHandler::class);

            $app->get('/add_photo/{user_id:\d+}', UserAddPhotoHandler::class)
                ->setName(UserAddPhotoHandler::class);

            $app->get('/delete_photo/{user_id:\d+}', UserDeletePhotoHandler::class)
                ->setName(UserDeletePhotoHandler::class);
        })->add(new CurrentUserHasRightToEditUser());
    }
}
