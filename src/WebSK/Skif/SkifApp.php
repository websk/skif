<?php

namespace WebSK\Skif;

use Psr\Container\ContainerInterface;
use Skif\AdminRoutes;
use Skif\Content\ContentRoutes;
use Skif\Form\FormRoutes;
use Skif\Users\UserRoutes;
use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
use WebSK\Cache\CacheServerSettings;
use WebSK\Cache\CacheService;
use WebSK\Cache\Engines\CacheEngineInterface;

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

        AdminRoutes::route();
        UserRoutes::route();
        ContentRoutes::route();
        FormRoutes::route();
    }
}
