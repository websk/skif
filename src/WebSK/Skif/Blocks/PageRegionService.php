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
     * @return InterfaceEntity|PageRegion|null
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
     * @param InterfaceEntity|PageRegion $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        $cache_key = PageRegionsUtils::getPageRegionIdByNameAndTemplateIdCacheKey(
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
        $cache_key = PageRegionsUtils::getPageRegionIdByNameAndTemplateIdCacheKey(
            $entity_obj->getName(),
            $entity_obj->getTemplateId()
        );
        CacheWrapper::delete($cache_key);

        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}