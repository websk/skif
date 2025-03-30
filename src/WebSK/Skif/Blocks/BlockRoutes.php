<?php

namespace WebSK\Skif\Blocks;

use WebSK\SimpleRouter\SimpleRouter;

/**
 * Class BlockRoutes
 * @package WebSK\Skif\Blocks
 */
class BlockRoutes
{
    public static function route(): void
    {
        SimpleRouter::staticRoute('@^/admin/blocks$@i', ControllerBlocks::class, 'listAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/list$@i', ControllerBlocks::class, 'listAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)/position@i', ControllerBlocks::class,
            'placeInRegionTabAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)/region@i', ControllerBlocks::class,
            'chooseRegionTabAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)/caching@i', ControllerBlocks::class, 'cachingTabAction',
            0);
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)/ace@i', ControllerBlocks::class, 'aceTabAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)/delete@i', ControllerBlocks::class, 'deleteTabAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/edit/(.+)@i', ControllerBlocks::class, 'editAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/search$@i', ControllerBlocks::class, 'searchAction', 0);
        SimpleRouter::staticRoute('@^/admin/blocks/change_template/(\d+)@i', ControllerBlocks::class,
            'changeTemplateAction', 0);
    }
}
