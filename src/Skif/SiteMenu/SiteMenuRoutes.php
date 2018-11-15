<?php

namespace Skif\SiteMenu;

use Skif\UrlManager;

class SiteMenuRoutes
{
    public static function route()
    {
        UrlManager::route('@^/admin/site_menu$@i', SiteMenuController::class, 'listAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/edit/(.+)$@i', SiteMenuController::class, 'editAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/save/(.+)$@i', SiteMenuController::class, 'saveAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/delete/(\d+)$@i', SiteMenuController::class, 'deleteAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/items/list/(\d+)$@i', SiteMenuController::class, 'listItemsAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/items/list_for_move/(\d+)$@i', SiteMenuController::class, 'listForMoveItemsAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/item/move/(\d+)$@i', SiteMenuController::class, 'moveItemAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/item/edit/(.+)$@i', SiteMenuController::class, 'editItemAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/item/save/(.+)$@i', SiteMenuController::class, 'saveItemAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/item/delete/(\d+)$@i', SiteMenuController::class, 'deleteItemAdminAction', 0);
    }
}
