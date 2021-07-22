<?php

namespace WebSK\Skif\SiteMenu;

use Slim\App;
use WebSK\SimpleRouter\SimpleRouter;

class SiteMenuRoutes
{
    public static function route()
    {
        SimpleRouter::staticRoute('@^/admin/site_menu$@i', SiteMenuController::class, 'listAdminAction', 0);
        SimpleRouter::staticRoute('@^/admin/site_menu/edit/(.+)$@i', SiteMenuController::class, 'editAdminAction', 0);
        SimpleRouter::staticRoute('@^/admin/site_menu/save/(.+)$@i', SiteMenuController::class, 'saveAdminAction', 0);
        SimpleRouter::staticRoute('@^/admin/site_menu/delete/(\d+)$@i', SiteMenuController::class, 'deleteAdminAction',
            0);
        SimpleRouter::staticRoute('@^/admin/site_menu/(\d+)/items/list/(\d+)$@i', SiteMenuController::class,
            'listItemsAdminAction', 0);
        SimpleRouter::staticRoute('@^/admin/site_menu/(\d+)/items/list_for_move/(\d+)$@i', SiteMenuController::class,
            'listForMoveItemsAdminAction', 0);
        SimpleRouter::staticRoute('@^/admin/site_menu/(\d+)/item/move/(\d+)$@i', SiteMenuController::class,
            'moveItemAdminAction', 0);
        SimpleRouter::staticRoute('@^/admin/site_menu/(\d+)/item/edit/(.+)$@i', SiteMenuController::class,
            'editItemAdminAction', 0);
        SimpleRouter::staticRoute('@^/admin/site_menu/(\d+)/item/save/(.+)$@i', SiteMenuController::class,
            'saveItemAdminAction', 0);
        SimpleRouter::staticRoute('@^/admin/site_menu/(\d+)/item/delete/(\d+)$@i', SiteMenuController::class,
            'deleteItemAdminAction', 0);
    }

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {

    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {

    }
}
