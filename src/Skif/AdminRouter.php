<?php

namespace Skif;

class AdminRouter
{
    public static function route()
    {
        if (!Router::matchGroup('@/admin@')) {
            return;
        }

        // Admin Logger
        UrlManager::route('@^/admin/logger/list$@i', '\Skif\Logger\ControllerLogger', 'listAction', 0);
        UrlManager::route('@^/admin/logger/object_log/@i', '\Skif\Logger\ControllerLogger', 'object_logAction', 0);
        UrlManager::route('@^/admin/logger/record/@', '\Skif\Logger\ControllerLogger', 'recordAction', 0);

        // Admin Blocks
        UrlManager::route('@^/admin/blocks$@i', '\Skif\Blocks\ControllerBlocks', 'listAction', 0);
        UrlManager::route('@^/admin/blocks/list$@i', '\Skif\Blocks\ControllerBlocks', 'listAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/position@i', '\Skif\Blocks\ControllerBlocks', 'placeInRegionTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/region@i', '\Skif\Blocks\ControllerBlocks', 'chooseRegionTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/caching@i', '\Skif\Blocks\ControllerBlocks', 'cachingTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/ace@i', '\Skif\Blocks\ControllerBlocks', 'aceTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)/delete@i', '\Skif\Blocks\ControllerBlocks', 'deleteTabAction', 0);
        UrlManager::route('@^/admin/blocks/edit/(.+)@i', '\Skif\Blocks\ControllerBlocks', 'editAction', 0);
        UrlManager::route('@^/admin/blocks/search$@i', '\Skif\Blocks\ControllerBlocks', 'searchAction', 0);
        UrlManager::route('@^/admin/blocks/change_template/(\d+)@i', '\Skif\Blocks\ControllerBlocks', 'changeTemplateAction', 0);

        // Материалы
        UrlManager::route('@^/admin/content/(.+)/rubrics$@', '\Skif\Content\RubricController', 'listAdminRubricsAction');
        UrlManager::route('@^/admin/content/(.+)/rubrics/edit/(.+)@', '\Skif\Content\RubricController', 'editRubricAction');
        UrlManager::route('@^/admin/content/(.+)/rubrics/save/(.+)@', '\Skif\Content\RubricController', 'saveRubricAction');
        UrlManager::route('@^/admin/content/(.+)/rubrics/delete/(.+)@', '\Skif\Content\RubricController', 'deleteRubricAction');
        UrlManager::route('@^/admin/content/autocomplete$@i', '\Skif\Content\ContentController', 'autoCompleteContentAction', 0);
        UrlManager::route('@^/admin/content/(.+)/edit/(.+)$@i', '\Skif\Content\ContentController', 'editAdminAction', 0);
        UrlManager::route('@^/admin/content/(.+)/save/(.+)$@i', '\Skif\Content\ContentController', 'saveAdminAction', 0);
        UrlManager::route('@^/admin/content/(.+)/delete/(.+)$@i', '\Skif\Content\ContentController', 'deleteAction', 0);
        UrlManager::route('@^/admin/content/(.+)/delete_image/(.+)$@i', '\Skif\Content\ContentController', 'deleteImageAction', 0);
        UrlManager::route('@^/admin/content/(.+)$@i', '\Skif\Content\ContentController', 'listAdminAction', 0);

        // Меню сайта
        UrlManager::route('@^/admin/site_menu$@i', '\Skif\SiteMenu\SiteMenuController', 'listAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/edit/(.+)$@i', '\Skif\SiteMenu\SiteMenuController', 'editAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/save/(.+)$@i', '\Skif\SiteMenu\SiteMenuController', 'saveAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/delete/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'deleteAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/items/list/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'listItemsAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/items/list_for_move/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'listForMoveItemsAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/item/move/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'moveItemAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/item/edit/(.+)$@i', '\Skif\SiteMenu\SiteMenuController', 'editItemAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/item/save/(.+)$@i', '\Skif\SiteMenu\SiteMenuController', 'saveItemAdminAction', 0);
        UrlManager::route('@^/admin/site_menu/(\d+)/item/delete/(\d+)$@i', '\Skif\SiteMenu\SiteMenuController', 'deleteItemAdminAction', 0);

        // User
        UrlManager::route('@^/admin/users$@', '\Skif\Users\UserController', 'listAction');
        UrlManager::route('@^/admin/users/edit/(.+)@', '\Skif\Users\UserController', 'editAction', 0, \Skif\Conf\ConfWrapper::value('layout.admin'));
        UrlManager::route('@^/admin/users/roles$@', '\Skif\Users\UserController', 'listUsersRolesAction');
        UrlManager::route('@^/admin/users/roles/edit/(.+)@', '\Skif\Users\UserController', 'editUsersRoleAction');
        UrlManager::route('@^/admin/users/roles/save/(.+)@', '\Skif\Users\UserController', 'saveUsersRoleAction');
        UrlManager::route('@^/admin/users/roles/delete/(.+)@', '\Skif\Users\UserController', 'deleteUsersRoleAction');
    }
}
