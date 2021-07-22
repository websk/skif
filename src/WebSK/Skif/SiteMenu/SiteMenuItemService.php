<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Entity\EntityService;
use WebSK\Utils\Url;

/**
 * Class SiteMenuItemService
 * @method SiteMenuItem getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuItemService extends EntityService
{
    /** @var SiteMenuItemRepository */
    protected $repository;

    /**
     * @param int $site_menu_id
     * @param int $parent_id
     * @return array
     */
    public function getIdsArrBySiteMenuId(int $site_menu_id, int $parent_id = 0): array
    {
        return $this->repository->findIdsArrBySiteMenuId($site_menu_id, $parent_id);
    }

    /**
     * @param string $url
     * @return int|null
     */
    public function getIdByUrl(string $url): ?int
    {
        return $this->repository->findIdByUrl($url);
    }

    /**
     * @return int|null
     */
    public function getCurrentId(): ?int
    {
        $url = Url::getUriNoQueryString();

        return $this->getIdByUrl($url);
    }

    /**
     * @param SiteMenuItem $site_menu_item_obj
     * @return array
     */
    public function getChildrenIdsArr(SiteMenuItem $site_menu_item_obj): array
    {
        return $this->getIdsArrBySiteMenuId($site_menu_item_obj->getMenuId(), $site_menu_item_obj->getId());
    }

    /**
     * Массив всех потомков
     * @return array
     */
    public function getDescendantsIdsArr(SiteMenuItem $site_menu_item_obj): array
    {
        $children_ids_arr = $this->getChildrenIdsArr($site_menu_item_obj);
        $descendants_ids_arr = $children_ids_arr;
        foreach ($children_ids_arr as $children_site_menu_item_id) {
            $children_site_menu_item_obj = $this->getById($children_site_menu_item_id);

            $descendants_ids_arr = array_merge(
                $descendants_ids_arr,
                $this->getDescendantsIdsArr($children_site_menu_item_obj)
            );
        }
        return $descendants_ids_arr;
    }

    /**
     * Массив идентификаторов предков снизу вверх
     * @param SiteMenuItem $site_menu_item_obj
     * @return array
     */
    public function getAncestorsIdsArr(SiteMenuItem $site_menu_item_obj): array
    {
        $current_site_menu_item_obj = $site_menu_item_obj;
        $ancestors_ids_arr = array();
        $iteration = 0;

        while ($parent_id = $current_site_menu_item_obj->getParentId()) {
            if ($parent_id == $current_site_menu_item_obj->getId()) {
                throw new \Exception('Пункт меню ' . $site_menu_item_obj->getId() . ' не может быть родительским по отношению к самому себе');
            }

            if ($iteration > 20) {
                break;
            }

            $ancestors_ids_arr[] = $parent_id;
            $current_site_menu_item_obj = $this->getById($parent_id);

            $iteration++;
        }

        return $ancestors_ids_arr;
    }
}