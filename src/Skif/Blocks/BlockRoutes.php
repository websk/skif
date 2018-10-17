<?php

namespace Skif\Blocks;

use Skif\Router;
use Skif\UrlManager;

class BlockRoutes
{
    public static function route()
    {
        if (!Router::matchGroup('@/admin@')) {
            return;
        }

        UrlManager::route('@^/admin/blocks$@i', ControllerBlocks::class, 'listAction', 0);
        UrlManager::route('@^/admin/blocks/list$@i', ControllerBlocks::class, 'listAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/position@i', ControllerBlocks::class, 'placeInRegionTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/region@i', ControllerBlocks::class, 'chooseRegionTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/caching@i', ControllerBlocks::class, 'cachingTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/ace@i', ControllerBlocks::class, 'aceTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/delete@i', ControllerBlocks::class, 'deleteTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)@i', ControllerBlocks::class, 'editAction', 0);
        UrlManager::route('@^/admin/blocks/search$@i', ControllerBlocks::class, 'searchAction', 0);
        UrlManager::route('@^/admin/blocks/change_template/(\d+)@i', ControllerBlocks::class, 'changeTemplateAction', 0);
    }
}
