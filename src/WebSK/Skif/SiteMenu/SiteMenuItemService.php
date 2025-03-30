<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Auth\Auth;
use WebSK\Cache\CacheService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\InterfaceEntity;
use WebSK\Entity\WeightService;
use WebSK\Logger\Logger;
use WebSK\Skif\Content\ContentService;
use WebSK\Utils\FullObjectId;
use WebSK\Utils\Url;

/**
 * Class SiteMenuItemService
 * @method SiteMenuItem getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuItemService extends WeightService
{
    const string IDS_BY_SITE_MENU_ID_AND_PARENT_ID_CACHE_KEY = 'SiteMenuItemService::getIdsArrBySiteMenuId:%d:%d';
    const int IDS_BY_SITE_MENU_ID_AND_PARENT_ID_CACHE_TTL_SEC = 3600;

    /** @var SiteMenuItemRepository */
    protected $repository;

    protected ContentService $content_service;

    /**
     * SiteMenuItemService constructor.
     * @param string $entity_class_name
     * @param EntityRepository $repository
     * @param CacheService $cache_service
     * @param ContentService $content_service
     */
    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        ContentService $content_service
    ) {
        $this->content_service = $content_service;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param InterfaceEntity|SiteMenuItem $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj): void
    {
        $url = $entity_obj->getUrl();

        if ($entity_obj->getContentId()) {
            $content_obj = $this->content_service->getById($entity_obj->getContentId());
            $url = $content_obj->getUrl();
        }

        if ($url) {
            $entity_obj->setUrl('/' . ltrim($url, '/'));
        }

        $this->initWeight(
            $entity_obj,
            [
                SiteMenuItem::_MENU_ID => $entity_obj->getMenuId()
            ]
        );

        parent::beforeSave($entity_obj);
    }

    /**
     * @param SiteMenuItem|InterfaceEntity $entity_obj
     * @return void
     * @throws \Exception
     */
    public function removeFromCache(InterfaceEntity $entity_obj): void
    {
        $cache_key = sprintf(self::IDS_BY_SITE_MENU_ID_AND_PARENT_ID_CACHE_KEY, $entity_obj->getId(), $entity_obj->getParentId());
        $this->cache_service->delete($cache_key);

        parent::removeFromCache($entity_obj);
    }

    /**
     * @param InterfaceEntity|SiteMenuItem $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|SiteMenuItem $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param int $site_menu_id
     * @param null|int $parent_id
     * @return array
     */
    public function getIdsArrBySiteMenuId(int $site_menu_id, ?int $parent_id = null): array
    {
        $cache_key = sprintf(self::IDS_BY_SITE_MENU_ID_AND_PARENT_ID_CACHE_KEY, $site_menu_id, $parent_id);
        $cached = $this->cache_service->get($cache_key);
        if ($cached !== false) {
            return $cached;
        }

        $ids_arr = $this->repository->findIdsArrBySiteMenuId($site_menu_id, $parent_id);

        $this->cache_service->set($cache_key, $ids_arr, self::IDS_BY_SITE_MENU_ID_AND_PARENT_ID_CACHE_TTL_SEC);

        return $ids_arr;
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