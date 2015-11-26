<?php

namespace Skif\Redirect;


class Redirect implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'redirect_rewrites';

    protected $id;
    protected $src;
    protected $dst;
    protected $code;
    protected $kind;


    public static $crud_create_button_required_fields_arr = array();
    public static $crud_create_button_title = 'Добавить редирект';

    public static $crud_model_class_screen_name = 'Исходный урл';
    public static $crud_model_title_field = 'src';

    public static $crud_field_titles_arr = array(
        'src' => 'Исходный урл',
        'dst' => 'Назначение',
        'code' => 'HTTP-код',
        'kind' => 'Вид',
    );

    public static $crud_model_class_screen_name_for_list = 'Редиректы';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'src' => array('col_class' => 'col-md-4 col-sm-6 col-xs-6'),
        'dst' => array('col_class' => 'col-md-4 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = array(
        'kind' => array('widget' => 'options', 'options_arr' => array('1 - строка', '2 - регексп')),
        'src' => array(),
        'dst' => array(),
        'code' => array(),
    );

    public function getEditorTabsArr()
    {
        $tabs_obj_arr = array();
        $tabs_obj_arr[] = new \Skif\EditorTabs\Tab(\Skif\Redirect\RedirectController::getEditUrlForObj($this), 'Переменная');

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
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * @param mixed $src
     */
    public function setSrc($src)
    {
        $this->src = $src;
    }

    /**
     * @return mixed
     */
    public function getDst()
    {
        return $this->dst;
    }

    /**
     * @param mixed $dst
     */
    public function setDst($dst)
    {
        $this->dst = $dst;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @param mixed $kind
     */
    public function setKind($kind)
    {
        $this->kind = $kind;
    }

}