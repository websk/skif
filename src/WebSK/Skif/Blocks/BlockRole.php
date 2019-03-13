<?php

namespace WebSK\Skif\Blocks;

use WebSK\Auth\Auth;
use WebSK\Logger\Logger;
use WebSK\Model\ActiveRecord;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Entity\InterfaceEntity;
use WebSK\Model\InterfaceSave;
use WebSK\Utils\FullObjectId;

/**
 * Class BlockRole
 * @package WebSK\Skif\Blocks
 */
class BlockRole implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceEntity
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'blocks_roles';

    /** @var int */
    protected $id;

    /** @var int */
    protected $block_id;

    /** @var int */
    protected $role_id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getBlockId(): int
    {
        return $this->block_id;
    }

    /**
     * @param int $block_id
     */
    public function setBlockId(int $block_id): void
    {
        $this->block_id = $block_id;
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * @param int $role_id
     */
    public function setRoleId(int $role_id): void
    {
        $this->role_id = $role_id;
    }

    /**
     * @param $id
     */
    public static function afterUpdate($id)
    {
        $block_role_obj = self::factory($id);

        self::removeObjFromCacheById($id);

        Logger::logObjectEvent($block_role_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());
        Logger::logObjectEvent($this, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
