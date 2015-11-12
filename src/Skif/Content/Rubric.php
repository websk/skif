<?php

namespace Skif\Content;


class Rubric implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    protected $id;
    protected $name;
    protected $comment;

    const DB_TABLE_NAME = 'rubrics';

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

    public static function afterUpdate($rubric_id)
    {
        $rubric_obj = \Skif\Content\Rubric::factory($rubric_id);

        self::removeObjFromCacheById($rubric_id);

        \Skif\Logger\Logger::logObjectEvent($rubric_obj, 'изменение');
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        \Skif\Logger\Logger::logObjectEvent($this, 'удаление');
    }

}