<?php

namespace Skif\Form;

class FormField implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'form_field';

    protected $id;
    protected $form;
    protected $name;
    protected $type;
    protected $status;
    protected $num;
    protected $size;


    public static $crud_create_button_required_fields_arr = array('form');
    public static $crud_create_button_title = 'Добавить поле';

    public static $crud_model_class_screen_name = 'Название';
    public static $crud_model_title_field = 'name';

    public static $crud_field_titles_arr = array(
        'name' => 'Название',
        'form' => 'Форма',
        'type' => 'Тип',
        'status' => 'Обязательность',
        'num' => 'Сортировка',
        'size' => 'Размер'
    );

    public static $crud_model_class_screen_name_for_list = 'Набор полей формы';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'name' => array('col_class' => 'col-md-6 col-sm-6 col-xs-6'),
        'status' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = array(
        'name' => array(),
        'form' => array(
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
        'num' => array(),
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
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     */
    public function setForm($form)
    {
        $this->form = $form;
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
    public function getNum()
    {
        return $this->num;
    }

    /**
     * @param mixed $num
     */
    public function setNum($num)
    {
        $this->num = $num;
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

        \Skif\Form\Form::afterUpdate($form_field_obj->getForm());
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        \Skif\Form\Form::afterUpdate($this->getForm());
    }

}