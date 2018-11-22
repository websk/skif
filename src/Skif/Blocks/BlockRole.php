<?php

namespace Skif\Blocks;


class BlockRole implements
    \WebSK\Model\InterfaceLoad,
    \WebSK\Model\InterfaceFactory,
    \WebSK\Model\InterfaceSave,
    \WebSK\Model\InterfaceDelete,
    \WebSK\Model\InterfaceLogger
{
    use WebSK\Model\ActiveRecord;
    use WebSK\Model\FactoryTrait;

    protected $id;
    protected $block_id;
    protected $role_id;

    const DB_TABLE_NAME = 'blocks_roles';


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getBlockId()
    {
        return $this->block_id;
    }

    /**
     * @param mixed $block_id
     */
    public function setBlockId($block_id)
    {
        $this->block_id = $block_id;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * @param mixed $role_id
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
    }

}