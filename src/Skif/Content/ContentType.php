<?php

namespace Skif\Content;


class ContentType implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete,
    \Skif\Model\InterfaceGetTitle
{
    use Skif\Model\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'content_types';

    protected $id;
    protected $name;
    protected $type;
    protected $url;
    protected $template_id;
    protected $rubric_ids_arr;

    public static $active_record_ignore_fields_arr = array(
        'rubric_ids_arr',
    );

    public static $crud_create_button_required_fields_arr = array();
    public static $crud_create_button_title = 'Добавить тип контента';

    public static $crud_model_class_screen_name = 'Тип контента';
    public static $crud_model_title_field = 'name';

    public static $crud_field_titles_arr = array(
        'name' => 'Название',
        'type' => 'Тип',
        'url' => 'URL',
        'template_id' => 'Шаблон',
    );

    public static $crud_model_class_screen_name_for_list = 'Типы контента';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'name' => array('col_class' => 'col-md-6 col-sm-6 col-xs-6'),
        'type' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = array(
        'name' => array(),
        'type' => array(),
        'url' => array(),
        'template_id' => array(
            'widget' => array('\Skif\CRUD\ModelReferenceWidget\ModelReferenceWidget', 'renderWidget'),
            'widget_settings' => array(
                'model_class_name' => '\Skif\Content\Template'
            )
        ),
    );


    public function load($id)
    {
        $is_loaded = \Skif\Model\ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        $query = "SELECT id FROM " . \Skif\Content\Rubric::DB_TABLE_NAME ." WHERE content_type_id = ?";
        $this->rubric_ids_arr = \Websk\Skif\DBWrapper::readColumn(
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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    public function getTitle()
    {
        return $this->name;
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->template_id;
    }

    /**
     * @param mixed $template_id
     */
    public function setTemplateId($template_id)
    {
        $this->template_id = $template_id;
    }

    /**
     * @return mixed
     */
    public function getRubricIdsArr()
    {
        return $this->rubric_ids_arr;
    }

}