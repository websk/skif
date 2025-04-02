<?php

namespace WebSK\Skif\Blocks;

use WebSK\Entity\Entity;

/**
 * Class BlockRole
 * @package WebSK\Skif\Blocks
 */
class BlockRole extends Entity
{

    const string DB_TABLE_NAME = 'blocks_roles';

    const string _BLOCK_ID = 'block_id';
    protected int $block_id;

    const string _ROLE_ID = 'role_id';
    protected int $role_id;

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
