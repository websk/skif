<?php

namespace Skif\Content;


class Template implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete,
    \Skif\Model\InterfaceGetTitle,
    \Skif\Model\InterfaceLogger
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'template';

    protected $id;
    protected $title = '';
    protected $name = '';
    protected $css = '';
    protected $is_default = 0;
    protected $layout_template_file = '';

    public static $crud_create_button_required_fields_arr = array();
    public static $crud_create_button_title = 'Добавить тему';

    public static $crud_model_class_screen_name = 'Тема';
    public static $crud_model_title_field = 'title';

    public static $crud_field_titles_arr = array(
        'title' => 'Название',
        'name' => 'Обозначение',
        'css' => 'Файл CSS',
        'def' => 'По-умолчанию',
        'layout_template_file' => 'Файл шаблона',
    );

    public static $crud_model_class_screen_name_for_list = 'Темы';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'title' => array('col_class' => 'col-md-6 col-sm-6 col-xs-6'),
        'name' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = array(
        'title' => array(),
        'name' => array(),
        'css' => array(),
        'def' => array(
            'widget' => 'checkbox',
        ),
        'layout_template_file' => array()
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
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * @param mixed $css
     */
    public function setCss($css)
    {
        $this->css = $css;
    }

    /**
     * @return mixed
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * @param mixed $is_default
     */
    public function setIsDefault($is_default)
    {
        $this->is_default = $is_default;
    }

    /**
     * @return mixed
     */
    public function getLayoutTemplateFile()
    {
        return $this->layout_template_file;
    }

    public function getLayoutTemplateFilePath()
    {
        return 'layouts/' . $this->layout_template_file;
    }

    /**
     * @param mixed $layout_template_file
     */
    public function setLayoutTemplateFile($layout_template_file)
    {
        $this->layout_template_file = $layout_template_file;
    }

    public static function afterUpdate($template_id)
    {
        $template_obj = \Skif\Content\Template::factory($template_id);

        $cache_key = \Skif\Content\TemplateUtils::getTemplateIdByNameCacheKey($template_obj->getName());
        \Skif\Cache\CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($template_id);
    }

    public function afterDelete()
    {
        $cache_key = \Skif\Content\TemplateUtils::getTemplateIdByNameCacheKey($this->getName());
        \Skif\Cache\CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($this->getId());
    }
}