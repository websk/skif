<?php

namespace WebSK\Skif\Blocks;

use WebSK\Model\ActiveRecord;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceLogger;
use WebSK\Model\InterfaceSave;

/**
 * Class BlockRole
 * @package WebSK\Skif\Blocks
 */
class BlockRole implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceLogger
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
}
