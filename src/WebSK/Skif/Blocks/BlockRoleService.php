<?php

namespace WebSK\Skif\Blocks;

use WebSK\Auth\Auth;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Utils\FullObjectId;

/**
 * @method BlockRole getById($entity_id, $exception_if_not_loaded = true)
 */
class BlockRoleService extends EntityService
{
    /** @var BlockRoleRepository */
    protected $repository;


    /**
     * @param int $block_id
     * @return array
     */
    public function getIdsByBlockId(int $block_id): array
    {
        return $this->repository->findIdsByBlockId($block_id);
    }

    /**
     * @param int $block_id
     * @return array
     * @throws \Exception
     */
    public function getRoleIdsByBlockId(int $block_id): array
    {
        $block_role_ids_arr = $this->getIdsByBlockId($block_id);

        $role_ids_arr = array();

        foreach ($block_role_ids_arr as $block_role_id) {
            $block_role_obj = $this->getById($block_role_id);

            $role_ids_arr[] = $block_role_obj->getRoleId();
        }

        return $role_ids_arr;
    }

    /**
     * @param InterfaceEntity|BlockRole $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|BlockRole $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

}
