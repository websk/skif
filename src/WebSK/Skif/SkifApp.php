<?php

namespace WebSK\Skif;

use Psr\Container\ContainerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Factory\ResponseFactory;
use WebSK\Auth\Middleware\CurrentUserIsAdmin;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Cache\CacheWrapper;
use WebSK\DB\DBWrapper;
use WebSK\Skif\Blocks\BlockRoutes;
use WebSK\Skif\Blocks\BlockServiceProvider;
use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Skif\Comment\CommentServiceProvider;
use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Form\FormRoutes;
use WebSK\Cache\CacheServiceProvider;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Image\ImageRoutes;
use WebSK\Skif\Form\FormServiceProvider;
use WebSK\Skif\Poll\PollRoutes;
use WebSK\Skif\Poll\PollServiceProvider;
use WebSK\Skif\Redirect\RedirectRoutes;
use WebSK\Skif\Redirect\RedirectServiceProvider;
use WebSK\Skif\RequestHandlers\SkifErrorHandler;
use WebSK\Skif\RequestHandlers\NotFoundHandler;
use WebSK\Skif\SiteMenu\SiteMenuRoutes;
use WebSK\Auth\AuthRoutes;
use Slim\App;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\KeyValue\KeyValueRoutes;
use WebSK\KeyValue\KeyValueServiceProvider;
use WebSK\Logger\LoggerRoutes;
use WebSK\Logger\LoggerServiceProvider;
use WebSK\Skif\RequestHandlers\AdminHandler;
use WebSK\Skif\SiteMenu\SiteMenuServiceProvider;
use WebSK\Slim\Facade;

/**
 * Class SkifApp
 * @package WebSK\Skif
 */
class SkifApp extends App
{
    const string ROUTE_NAME_ADMIN = 'admin:main';

    /**
     * SkifApp constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct(new ResponseFactory(), $container);

        $this->registerRouterSettings($container);

        CacheServiceProvider::register($container);
        SkifServiceProvider::register($container);
        UserServiceProvider::register($container);
        AuthServiceProvider::register($container);
        CRUDServiceProvider::register($container);
        KeyValueServiceProvider::register($container);
        LoggerServiceProvider::register($container);
        ContentServiceProvider::register($container);
        BlockServiceProvider::register($container);
        CommentServiceProvider::register($container);
        FormServiceProvider::register($container);
        RedirectServiceProvider::register($container);
        PollServiceProvider::register($container);
        SiteMenuServiceProvider::register($container);

        /** Set DBWrapper db service */
        DBWrapper::setDbService(SkifServiceProvider::getDBService($container));

        CacheWrapper::setContainer($container);

        Facade::setFacadeApplication($this);

        $this->registerRoutes();

        $error_middleware = $this->addErrorMiddleware($container->get('settings.displayErrorDetails'), true, true);
        $error_middleware->setDefaultErrorHandler(SkifErrorHandler::class);
        $error_middleware->setErrorHandler(HttpNotFoundException::class, NotFoundHandler::class);
    }

    /**
     * @param ContainerInterface $container
     */
    protected function registerRouterSettings(ContainerInterface $container): void
    {
        $route_collector = $this->getRouteCollector();
        $route_collector->setDefaultInvocationStrategy($container->get(InvocationStrategyInterface::class));
        $route_parser = $route_collector->getRouteParser();

        $container->set(RouteParserInterface::class, $route_parser);
    }

    protected function registerRoutes(): void
    {
        $this->get('/admin', AdminHandler::class)
            ->setName(self::ROUTE_NAME_ADMIN);

        $this->group('/admin', function (RouteCollectorProxyInterface $route_collector_proxy) {
            KeyValueRoutes::registerAdmin($route_collector_proxy);
            UserRoutes::registerAdmin($route_collector_proxy);
            LoggerRoutes::registerAdmin($route_collector_proxy);
            ContentRoutes::registerAdmin($route_collector_proxy);
            BlockRoutes::registerAdmin($route_collector_proxy);
            CommentRoutes::registerAdmin($route_collector_proxy);
            FormRoutes::registerAdmin($route_collector_proxy);
            PollRoutes::registerAdmin($route_collector_proxy);
            RedirectRoutes::registerAdmin($route_collector_proxy);
            SiteMenuRoutes::registerAdmin($route_collector_proxy);
        })->add(new CurrentUserIsAdmin());

        CaptchaRoutes::register($this);
        UserRoutes::register($this);
        AuthRoutes::register($this);
        CommentRoutes::register($this);
        FormRoutes::register($this);
        PollRoutes::register($this);
        SiteMenuRoutes::register($this);
        ContentRoutes::register($this);

        ContentRoutes::registerSimpleRoute($this);
        FormRoutes::registerSimpleRoute($this);
        RedirectRoutes::registerSimpleRoute($this);

        ImageRoutes::routes();
    }
}
