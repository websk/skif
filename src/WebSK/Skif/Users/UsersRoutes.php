<?php

namespace WebSK\Skif\Users;

use Slim\App;
use WebSK\Skif\Users\RequestHandlers\Admin\UserEditHandler;

class UsersRoutes
{
    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/users', function (App $app) {
            $app->get('/edit/{user_id:\d+}', UserEditHandler::class)
                ->setName(UserEditHandler::class);
        });
    }
}
