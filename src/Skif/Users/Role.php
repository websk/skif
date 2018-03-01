<?php

namespace Skif\Users;

use Skif\Model\FactoryTrait;
use Skif\Model\InterfaceDelete;
use Skif\Model\InterfaceFactory;
use Skif\Model\InterfaceLoad;
use Skif\Model\InterfaceLogger;
use Skif\Model\InterfaceSave;
use Skif\Util\ActiveRecord;

class Role implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceLogger
{
    use ActiveRecord;
    use FactoryTrait;

    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $designation;

    const DB_TABLE_NAME = 'roles';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * @param string $designation
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
    }
}
