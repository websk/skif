<?php

namespace Skif\Users;

class Role implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    protected $id;
    protected $name;
    protected $designation;

    const DB_TABLE_NAME = 'roles';

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * @param mixed $designation
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
    }

    public static function afterUpdate($role_id)
    {
        $role_obj = \Skif\Users\Role::factory($role_id);

        self::removeObjFromCacheById($role_id);

        \Skif\Logger\Logger::logObjectEvent($role_obj, 'изменение');
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        \Skif\Logger\Logger::logObjectEvent($this, 'удаление');
    }

}