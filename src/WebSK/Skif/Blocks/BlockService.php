<?php

namespace WebSK\Skif\Blocks;

use WebSK\Auth\Auth;
use WebSK\Cache\CacheService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Utils\FullObjectId;

class BlockService extends EntityService
{
    protected BlockRoleService $block_role_service;

    /** @var BlockRepository */
    protected $repository;

    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        BlockRoleService $block_role_service
    ) {
        $this->block_role_service = $block_role_service;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param int $block_id
     * @return void
     * @throws \Exception
     */
    public function deleteBlocksRolesByBlockId(int $block_id): void
    {
        $block_role_ids_arr = $this->block_role_service->getIdsByBlockId($block_id);

        foreach ($block_role_ids_arr as $block_role_id) {
            $block_role_obj = $this->block_role_service->getById($block_role_id);
            $this->block_role_service->delete($block_role_obj);
        }
    }

    /**
     * @param InterfaceEntity|Block $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|Block $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        $this->deleteBlocksRolesByBlockId($entity_obj->getId());

        BlockUtils::clearBlockIdsArrByPageRegionIdCache($entity_obj->getPageRegionId(), $entity_obj->getTemplateId());
        BlockUtils::clearBlockIdsArrByPageRegionIdCache(PageRegion::BLOCK_REGION_NONE, $entity_obj->getTemplateId());

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));

        $this->removeObjFromCacheById($entity_obj->getId());
    }

    /**
     * @param int $block_id
     * @return Block|InterfaceEntity
     */
    public function getBlockObj(int $block_id): Block
    {
        if ($block_id == 'new') {
            return new Block();
        }

        return $this->getById($block_id);
    }
}