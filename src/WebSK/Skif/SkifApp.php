<?php

namespace WebSK\Skif;

use WebSK\Auth\Middleware\CurrentUserIsAdmin;
use WebSK\DB\DBWrapper;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Blocks\BlockRoutes;
use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\CRUD\CRUDRoutes;
use WebSK\Skif\Form\FormRoutes;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Image\ImageRoutes;
use WebSK\Skif\Poll\PollRoutes;
use WebSK\Skif\Rating\RatingRoutes;
use WebSK\Skif\Redirect\RedirectRoutes;
use WebSK\Skif\SiteMenu\SiteMenuRoutes;
use WebSK\Auth\AuthRoutes;
use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\KeyValue\KeyValueRoutes;
use WebSK\KeyValue\KeyValueServiceProvider;
use WebSK\Logger\LoggerRoutes;
use WebSK\Logger\LoggerServiceProvider;
use WebSK\Skif\RequestHandlers\AdminHandler;
use WebSK\Skif\RequestHandlers\ErrorHandler;
use WebSK\Skif\RequestHandlers\NotFoundHandler;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Slim\Facade;

/**
 * Class SkifApp
 * @package WebSK\Skif
 */
class SkifApp extends App
{
    const ROUTE_NAME_ADMIN = 'admin:main';

    /**
     * SkifApp constructor.
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
        ContentServiceProvider::register($container);

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        $container = $this->getContainer();
        $container['foundHandler'] = function () {
            return new RequestResponseArgs();
        };

        $this->get('/admin', AdminHandler::class)
            ->setName(self::ROUTE_NAME_ADMIN);

        $this->group('/admin', function (App $app) {
            KeyValueRoutes::registerAdmin($app);
            UsersRoutes::registerAdmin($app);
            LoggerRoutes::registerAdmin($app);
            ContentRoutes::registerAdmin($app);
        })->add(new CurrentUserIsAdmin());

        CaptchaRoutes::register($this);

        UsersRoutes::register($this);
        AuthRoutes::register($this);

        /** Use facade */
        Facade::setFacadeApplication($this);

        /** Set DBWrapper db service */
        DBWrapper::setDbService(SkifServiceProvider::getDBService($container));

        RedirectRoutes::route();
        ImageRoutes::routes();
        CRUDRoutes::route();

        $route_based_crud_arr = $container['settings']['route_based_crud_arr'] ?? [];
        if ($route_based_crud_arr) {
            foreach ($route_based_crud_arr as $base_url => $controller_class_name) {
                SimpleRouter::routeBasedCrud($base_url, $controller_class_name);
            }
        }

        CommentRoutes::register($this);
        //ContentRoutes::register($this);

        BlockRoutes::route();
        ContentRoutes::route();
        SiteMenuRoutes::route();
        FormRoutes::route();

        CommentRoutes::route();

        PollRoutes::route();
        RatingRoutes::route();

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
