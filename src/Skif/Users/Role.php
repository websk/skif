<?php

namespace Skif\Users;

class Role implements
    \Skif\Model\InterfaceLoad,
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

    public function save()
    {
        \Skif\Util\ActiveRecordHelper::saveModelObj($this);

        self::removeObjFromCacheById($this->getId());

        \Skif\Logger\Logger::logObjectEvent($this, 'изменение');
    }

    public function delete()
    {
        \Skif\Util\ActiveRecordHelper::deleteModelObj($this);

        self::removeObjFromCacheById($this->getId());

        \Skif\Logger\Logger::logObjectEvent($this, 'удаление');
    }

}