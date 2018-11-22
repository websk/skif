<?php

namespace Skif\Form;

use WebSK\Model\ActiveRecord;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;

/**
 * Class FormField
 * @package Skif\Form
 */
class FormField implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete
{
    use ActiveRecord;
    use FactoryTrait;

    const FIELD_TYPE_STRING = 1;
    const FIELD_TYPE_TEXTAREA = 2;

    const DB_TABLE_NAME = 'form_field';

    protected $id;
    protected $form_id;
    protected $name;
    protected $type;
    protected $status;
    protected $weight;
    protected $size;


    public static $crud_create_button_required_fields_arr = array('form_id');
    public static $crud_create_button_title = 'Добавить поле';

    public static $crud_model_class_screen_name = 'Название';
    public static $crud_model_title_field = 'name';

    public static $crud_field_titles_arr = array(
        'name' => 'Название',
        'form_id' => 'Форма',
        'type' => 'Тип',
        'status' => 'Обязательность',
        'weight' => 'Сортировка',
        'size' => 'Размер'
    );

    public static $crud_model_class_screen_name_for_list = 'Набор полей формы';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'name' => array('col_class' => 'col-md-4 col-sm-6 col-xs-6'),
        'status' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        'weight' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = array(
        'name' => array(),
        'form_id' => array(
            'widget' => array('\Skif\CRUD\ModelReferenceWidget\ModelReferenceWidget', 'renderWidget'),
            'widget_settings' => array(
                'model_class_name' => '\Skif\Form\Form'
            )
        ),
        'type' => array(
            'widget' => 'options',
            'options_arr' => array(
                1 => 'Строка',
                2 => 'Текст',
                3 => 'Комментарий',
                4 => 'Галочка',
            )
        ),
        'status' => array('widget' => 'checkbox'),
        'weight' => array(),
        'size' => array(),
    );


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
    public function getFormId()
    {
        return $this->form_id;
    }

    /**
     * @param mixed $form_id
     */
    public function setFormId($form_id)
    {
        $this->form_id = $form_id;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    public static function afterUpdate($id)
    {
        $form_field_obj = self::factory($id);

        self::removeObjFromCacheById($id);

        \Skif\Form\Form::afterUpdate($form_field_obj->getFormId());
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        \Skif\Form\Form::afterUpdate($this->getFormId());
    }

}