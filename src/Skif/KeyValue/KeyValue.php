<?php

namespace Skif\KeyValue;

use Skif\Model\FactoryTrait;
use Skif\Model\InterfaceDelete;
use Skif\Model\InterfaceFactory;
use Skif\Model\InterfaceLoad;
use Skif\Model\InterfaceSave;
use Skif\Util\ActiveRecord;
use Websk\Skif\CacheWrapper;

/**
 * Class KeyValue
 * @package Skif\KeyValue
 */
class KeyValue implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'key_value';

    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $description;
    /** @var string */
    protected $value;


    public static $crud_create_button_required_fields_arr = array();
    public static $crud_create_button_title = 'Добавить параметр';

    public static $crud_model_class_screen_name = 'Параметр';
    public static $crud_model_title_field = 'name';

    public static $crud_field_titles_arr = array(
        'name' => 'Название',
        'description' => 'Описание',
        'value' => 'Значение',
    );

    public static $crud_model_class_screen_name_for_list = 'Параметры';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'name' => array('col_class' => 'col-md-4 col-sm-6 col-xs-6'),
        'description' => array('col_class' => 'col-md-4 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = array(
        'name' => array('widget_settings' => array('disabled' => true)),
        'description' => array(),
        'value' => array('widget' => 'textarea'),
    );

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public static function afterUpdate($key_value_id)
    {
        $key_value_obj = self::factory($key_value_id);

        $cache_key = KeyValueUtils::getValueByNameCacheKey($key_value_obj->getName());
        CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($key_value_id);
    }

    public function afterDelete()
    {
        $cache_key = KeyValueUtils::getValueByNameCacheKey($this->getName());
        CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($this->getId());
    }
}
