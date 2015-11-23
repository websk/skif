<?php
/**
    CREATE TABLE `key_value` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(128) NOT NULL DEFAULT '',
        `value` MEDIUMTEXT NOT NULL,
        `description` TEXT NULL,
        PRIMARY KEY (`id`),
        INDEX `name` (`name`)
    )
*/

namespace Skif\KeyValue;

/**
 * Class KeyValue
 * @package Skif\KeyValue
 */
class KeyValue
    implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'key_value';

    protected $id;
    protected $name;
    protected $description;
    protected $value;

    public static $crud_model_class_screen_name = 'Переменная';
    public static $crud_model_title_field = 'name';

    public static $crud_field_titles_arr = array(
        "name" => 'Название',
        "description" => 'Описание',
        "value" => 'Значение',
    );

    public static $crud_editor_fields_arr = array(
        "name" => array(),
        "description" => array(),
        "value" => array(),
    );

    public function getEditorTabsArr()
    {
        $tabs_obj_arr = array();
        $tabs_obj_arr[] = new \Skif\EditorTabs\Tab(\Skif\CRUD\ControllerCRUD::getEditUrlForObj($this), 'Переменная');

        return $tabs_obj_arr;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public static function afterUpdate($key_value_id)
    {
        $key_value_obj = \Skif\KeyValue\KeyValue::factory($key_value_id);

        $cache_key = \Skif\KeyValue\KeyValueUtils::getValueByNameCacheKey($key_value_obj->getName());
        \Skif\Cache\CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($key_value_id);
    }

    public function afterDelete()
    {
        $cache_key = \Skif\KeyValue\KeyValueUtils::getValueByNameCacheKey($this->getName());
        \Skif\Cache\CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($this->getId());
    }
}