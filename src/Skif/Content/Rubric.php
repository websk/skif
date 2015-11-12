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
    protected $rubric_ids_arr;

    public static $active_record_ignore_fields_arr = array(
        'rubric_ids_arr',
    );

    const DB_TABLE_NAME = 'rubrics';

    public function load($id)
    {
        $is_loaded = \Skif\Util\ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        $query = "SELECT rubric_id FROM content_rubrics WHERE content_id = ?";
        $this->rubric_ids_arr = \Skif\DB\DBWrapper::readColumn(
            $query,
            array($this->id)
        );

        return true;
    }

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