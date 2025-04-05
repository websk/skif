<?php

namespace WebSK\Skif\Blocks;

use WebSK\Auth\Auth;
use WebSK\Cache\CacheWrapper;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Utils\FullObjectId;


class PageRegionService extends EntityService
{

    /** @var PageRegionRepository */
    protected $repository;

    /**
     * @param int|null $entity_id
     * @param bool $exception_if_not_loaded
     * @return PageRegion|InterfaceEntity|null
     * @throws \Exception
     */
    public function getById(?int $entity_id, bool $exception_if_not_loaded = true): ?PageRegion
    {
        if ($entity_id == PageRegion::BLOCK_REGION_NONE) {
            $obj = new PageRegion();
            $obj->setName('disabled');
            $obj->setTitle('Отключенные блоки');

            return $obj;
        }

        return parent::getById($entity_id, $exception_if_not_loaded);
    }

    /**
     * @param string $name
     * @param int $template_id
     * @return bool|false|mixed
     * @throws \Exception
     */
    public function getPageRegionIdByNameAndTemplateId(string $name, int $template_id): int
    {
        $cache_key = self::getPageRegionIdByNameAndTemplateIdCacheKey($name, $template_id);

        $cache = $this->cache_service->get($cache_key);
        if ($cache !== false) {
            return (int)$cache;
        }

        $page_region_id = $this->repository->findPageRegionIdByNameAndTemplateId($name, $template_id);

        $this->cache_service->set($cache_key, $page_region_id, 3600);

        return $page_region_id;
    }

    /**
     * @param string $name
     * @param int $template_id
     * @return string
     */
    protected function getPageRegionIdByNameAndTemplateIdCacheKey(string $name, int $template_id): string
    {
        return 'page_region_id_by_name_' . $name . '_and_template_id' . $template_id;
    }

    /**
     * Массив PageRegionId для темы
     * @param int $template_id
     * @return array
     */
    public function getPageRegionIdsArrByTemplateId(int $template_id): array
    {
        static $static_page_region_ids_arr = [];

        $page_region_ids_arr = [];

        if (!array_key_exists($template_id, $static_page_region_ids_arr)) {
            $page_region_ids_arr = $this->repository->findPageRegionIdsArrByTemplateId($template_id);
        }

        $page_region_ids_arr[] = PageRegion::BLOCK_REGION_NONE;

        $static_page_region_ids_arr[$template_id] = $page_region_ids_arr;

        return $page_region_ids_arr;
    }

    /**
     * @param InterfaceEntity|PageRegion $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        $cache_key = $this->getPageRegionIdByNameAndTemplateIdCacheKey(
            $entity_obj->getName(),
            $entity_obj->getTemplateId()
        );
        CacheWrapper::delete($cache_key);

        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|PageRegion $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        $cache_key = $this->getPageRegionIdByNameAndTemplateIdCacheKey(
            $entity_obj->getName(),
            $entity_obj->getTemplateId()
        );
        CacheWrapper::delete($cache_key);

        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}