<?php

namespace WebSK\Skif\SiteMenu\RequestHandlers;

use WebSK\Skif\SkifPath;
use WebSK\Views\BreadcrumbItemDTO;

/**
 * Class AdminSiteMenuBreadcrumbsTrait
 * @package WebSK\Skif\SiteMenu\RequestHandlers
 */
trait AdminSiteMenuBreadcrumbsTrait
{
    /**
     * @return array
     */
    protected function getBreadcrumbsDTOArr(int $site_menu_id, ?int $site_menu_item_id = null): array
    {
        $site_menu_obj = $this->site_menu_service->getById($site_menu_id);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Меню сайта', $this->pathFor(AdminSiteMenuListHandler::class)),
        ];

        if (!$site_menu_item_id) {
            return $breadcrumbs_arr;
        }

        $breadcrumbs_arr[] = new BreadcrumbItemDTO(
            $site_menu_obj->getName(),
            $this->pathFor(AdminSiteMenuEditHandler::class, ['site_menu_id' => $site_menu_id])
        );

        $site_menu_item_obj = $this->site_menu_item_service->getById($site_menu_item_id);

        $ancestors_ids_arr = $this->site_menu_item_service->getAncestorsIdsArr($site_menu_item_obj);
        $ancestors_ids_arr = array_reverse($ancestors_ids_arr);

        foreach ($ancestors_ids_arr as $children_site_menu_item_id) {
            $children_site_menu_item_obj = $this->site_menu_item_service->getById($children_site_menu_item_id);

            $breadcrumbs_arr[] = new BreadcrumbItemDTO(
                $children_site_menu_item_obj->getName(),
                $this->pathFor(AdminSiteMenuItemEditHandler::class, ['site_menu_item_id' => $children_site_menu_item_id])
            );
        }

        return $breadcrumbs_arr;
    }
}