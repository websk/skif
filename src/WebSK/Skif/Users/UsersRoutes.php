<?php

namespace WebSK\Skif\Users;

use Slim\App;
use WebSK\Skif\Users\RequestHandlers\Admin\UserEditHandler as AdminUserEditHandler;
use WebSK\Skif\Users\RequestHandlers\UserEditHandler;
use WebSK\Skif\Users\RequestHandlers\UserSaveHandler;

/**
 * Class UsersRoutes
 * @package WebSK\Skif\Users
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
        });
    }
}
