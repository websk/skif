<?php

namespace WebSK\Skif\Blocks;

use Fig\Http\Message\RequestMethodInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockChangePositionInRegionHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockChangeTemplateHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorChooseRegionHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockDeleteHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockDisableHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorCachingHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorContentHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorDeleteHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockListHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorPositionInRegionHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockSaveCachingHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockSaveContentHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockSearchHandler;

/**
 * Class BlockRoutes
 * @package WebSK\Skif\Blocks
 */
class BlockRoutes
{

    /**
     * @param
     * RouteCollectorProxyInterface $route_collector_proxy
     */
    public static function registerAdmin(RouteCollectorProxyInterface $route_collector_proxy): void
    {
        $route_collector_proxy->group('/blocks', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->get('', BlockListHandler::class)
                ->setName(BlockListHandler::class);

            $route_collector_proxy->get('/search', BlockSearchHandler::class)
                ->setName(BlockSearchHandler::class);

            $route_collector_proxy->post('/change_template/{template_id:\d+}', BlockChangeTemplateHandler::class)
                ->setName(BlockChangeTemplateHandler::class);

            $route_collector_proxy->group('/{block_id:\d+}', function (RouteCollectorProxyInterface $route_collector_proxy) {
                $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', BlockEditorContentHandler::class)
                    ->setName(BlockEditorContentHandler::class);
                $route_collector_proxy->post('/save_content', BlockSaveContentHandler::class)
                    ->setName(BlockSaveContentHandler::class);

                $route_collector_proxy->post('/disable', BlockDisableHandler::class)
                    ->setName(BlockDisableHandler::class);

                $route_collector_proxy->get('/edit_caching', BlockEditorCachingHandler::class)
                    ->setName(BlockEditorCachingHandler::class);
                $route_collector_proxy->post('/save_caching', BlockSaveCachingHandler::class)
                    ->setName(BlockSaveCachingHandler::class);

                $route_collector_proxy->get('/edit_position', BlockEditorPositionInRegionHandler::class)
                    ->setName(BlockEditorPositionInRegionHandler::class);
                $route_collector_proxy->post('/change_position', BlockChangePositionInRegionHandler::class)
                    ->setName(BlockChangePositionInRegionHandler::class);

                $route_collector_proxy->get('/choose_region', BlockEditorChooseRegionHandler::class)
                    ->setName(BlockEditorChooseRegionHandler::class);

                $route_collector_proxy->get('/edit_delete', BlockEditorDeleteHandler::class)
                    ->setName(BlockEditorDeleteHandler::class);
                $route_collector_proxy->post('/delete', BlockDeleteHandler::class)
                    ->setName(BlockDeleteHandler::class);
            });
        });
    }

}
