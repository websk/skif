<?php

namespace WebSK\Skif;

use Psr\Container\ContainerInterface;
use Skif\Blocks\BlockRoutes;
use Skif\Comment\CommentRoutes;
use Skif\Conf\ConfWrapper;
use Skif\Content\ContentRoutes;
use Skif\CRUD\CRUDRoutes;
use Skif\Form\FormRoutes;
use Skif\Image\ImageRoutes;
use Skif\KeyValue\KeyValueRoutes;
use Skif\Logger\LoggerRoutes;
use Skif\Poll\PollRoutes;
use Skif\Rating\RatingRoutes;
use Skif\Redirect\RedirectRoutes;
use Skif\SiteMenu\SiteMenuRoutes;
use Skif\UrlManager;
use Skif\Users\UserRoutes;
use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
use WebSK\Cache\CacheServerSettings;
use WebSK\Cache\CacheService;
use WebSK\Cache\Engines\CacheEngineInterface;
use WebSK\Skif\Captcha\CaptchaRoutes;
use WebSK\Skif\RequestHandlers\AdminHandler;
use WebSK\Skif\RequestHandlers\ErrorHandler;
use WebSK\Skif\RequestHandlers\NotFoundHandler;

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

        /**
         * @param ContainerInterface $container
         * @return CacheService
         */
        $container['skif.cache_service'] = function (ContainerInterface $container) {
            $cache_config = $container['settings']['cache'];

            $cache_servers_arr = [];
            foreach ($cache_config['servers'] as $server_config) {
                $cache_servers_arr[] = new CacheServerSettings($server_config['host'], $server_config['port']);
            }

            /** @var CacheEngineInterface $cache_engine_class_name */
            $cache_engine_class_name = $cache_config['engine'];
            $cache_engine = new $cache_engine_class_name($cache_servers_arr, $cache_config['cache_key_prefix']);

            return new CacheService($cache_engine);
        };

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

        });

        RedirectRoutes::route();
        KeyValueRoutes::route();
        LoggerRoutes::route();
        ImageRoutes::routes();

        CRUDRoutes::route();

        $route_based_crud_arr = ConfWrapper::value('route_based_crud_arr', []);
        if ($route_based_crud_arr) {
            foreach ($route_based_crud_arr as $base_url => $controller_class_name) {
                UrlManager::routeBasedCrud($base_url, $controller_class_name);
            }
        }

        UserRoutes::route();

        BlockRoutes::route();
        ContentRoutes::route();
        SiteMenuRoutes::route();
        FormRoutes::route();

        CommentRoutes::route();

        PollRoutes::route();
        RatingRoutes::route();

        CaptchaRoutes::route($this);

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
