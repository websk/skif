<?php

namespace WebSK\Skif;

use Skif\Blocks\BlockRoutes;
use Skif\Comment\CommentRoutes;
use Skif\Content\ContentRoutes;
use Skif\CRUD\CRUDRoutes;
use Skif\Form\FormRoutes;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Skif\Auth\AuthServiceProvider;
use WebSK\Skif\Image\ImageRoutes;
use Skif\Poll\PollRoutes;
use Skif\Rating\RatingRoutes;
use Skif\Redirect\RedirectRoutes;
use Skif\SiteMenu\SiteMenuRoutes;
use Skif\UrlManager;
use WebSK\Skif\Auth\AuthRoutes;
use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\Skif\Captcha\CaptchaRoutes;
use WebSK\Skif\KeyValue\KeyValueRoutes;
use WebSK\Skif\KeyValue\KeyValueServiceProvider;
use WebSK\Skif\Logger\LoggerRoutes;
use WebSK\Skif\Logger\LoggerServiceProvider;
use WebSK\Skif\RequestHandlers\AdminHandler;
use WebSK\Skif\RequestHandlers\ErrorHandler;
use WebSK\Skif\RequestHandlers\NotFoundHandler;
use WebSK\Skif\Users\UsersRoutes;
use WebSK\Skif\Users\UsersServiceProvider;
use Websk\Slim\Facade;

class SkifApp extends App
{
    const ROUTE_NAME_ADMIN = 'admin:main';

    /**
     * CoreApp constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $container = $this->getContainer();

        CacheServiceProvider::register($container);
        SkifServiceProvider::register($container);
        UsersServiceProvider::register($container);
        AuthServiceProvider::register($container);
        CRUDServiceProvider::register($container);
        KeyValueServiceProvider::register($container);
        LoggerServiceProvider::register($container);

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        $container = $this->getContainer();
        $container['foundHandler'] = function () {
            return new RequestResponseArgs();
        };

        $this->group('/admin', function (App $app) {
            $app->get('', AdminHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN);

            KeyValueRoutes::registerAdmin($app);
            UsersRoutes::registerAdmin($app);
            LoggerRoutes::registerAdmin($app);
        });

        UsersRoutes::register($this);
        AuthRoutes::register($this);

        Facade::setFacadeApplication($this);

        RedirectRoutes::route();

        ImageRoutes::routes();

        CRUDRoutes::route();

        $route_based_crud_arr = $container['settings']['route_based_crud_arr'] ?? [];
        if ($route_based_crud_arr) {
            foreach ($route_based_crud_arr as $base_url => $controller_class_name) {
                UrlManager::routeBasedCrud($base_url, $controller_class_name);
            }
        }

        BlockRoutes::route();
        ContentRoutes::route();
        SiteMenuRoutes::route();
        FormRoutes::route();

        CommentRoutes::route();

        PollRoutes::route();
        RatingRoutes::route();

        CaptchaRoutes::register($this);

        $container['errorHandler'] = function () {
            return new ErrorHandler();
        };

        $container['phpErrorHandler'] = function () {
            return new ErrorHandler();
        };

        $container['notFoundHandler'] = function () {
            return new NotFoundHandler();
        };
    }
}
