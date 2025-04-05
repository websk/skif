<?php

namespace WebSK\Skif\Blocks;

use Fig\Http\Message\RequestMethodInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockDisableHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditCachingHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockListHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockSaveCachingHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockSaveContentHandler;

/**
 * Class BlockRoutes
 * @package WebSK\Skif\Blocks
 */
class BlockRoutes
{
    public static function route(): void
    {
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)/position@i', ControllerBlocks::class,
            'placeInRegionTabAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)/region@i', ControllerBlocks::class,
            'chooseRegionTabAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)/ace@i', ControllerBlocks::class, 'aceTabAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)/delete@i', ControllerBlocks::class, 'deleteTabAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/search$@i', ControllerBlocks::class, 'searchAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/change_template/(\d+)@i', ControllerBlocks::class,
            'changeTemplateAction', 0);
    }

    /**
     * @param
     * RouteCollectorProxyInterface $route_collector_proxy
     */
    public static function registerAdmin(RouteCollectorProxyInterface $route_collector_proxy): void
    {
        $route_collector_proxy->group('/blocks', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->get('/', BlockListHandler::class)
                ->setName(BlockListHandler::class);
            $route_collector_proxy->group('/{block_id:\d+}', function (RouteCollectorProxyInterface $route_collector_proxy) {
                $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/', BlockEditHandler::class)
                    ->setName(BlockEditHandler::class);
                $route_collector_proxy->post('/disable', BlockDisableHandler::class)
                    ->setName(BlockDisableHandler::class);
                $route_collector_proxy->get('/edit_caching', BlockEditCachingHandler::class)
                    ->setName(BlockEditCachingHandler::class);
                $route_collector_proxy->post('/save_caching', BlockSaveCachingHandler::class)
                    ->setName(BlockSaveCachingHandler::class);
                $route_collector_proxy->post('/save_content', BlockSaveContentHandler::class)
                    ->setName(BlockSaveContentHandler::class);
            });

        });
    }

    /**
     * @param int|null $block_id
     * @return string
     */
    public static function getEditorUrl(?int $block_id): string
    {
        if (!$block_id) {
            return '/admin/blocks/edit/new';
        }

        return '/admin/blocks/edit/' . $block_id;
    }
}
