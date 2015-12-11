<?php

namespace Skif\Form;

class Form implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete,
    \Skif\Model\InterfaceGetUrl,
    \Skif\Model\InterfaceGetTitle
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'form';

    protected $id;
    protected $form_name;
    protected $comment;
    protected $button;
    protected $mail;
    protected $re;
    protected $form_field_ids_arr;

    public static $active_record_ignore_fields_arr = array(
        'form_field_ids_arr',
    );


    public static $crud_create_button_required_fields_arr = array();
    public static $crud_create_button_title = 'Добавить форму';

    public static $crud_model_class_screen_name = 'Форма';
    public static $crud_model_title_field = 'form_name';

    public static $crud_field_titles_arr = array(
        'form_name' => 'Заголовок',
        'mail' => 'E-mail',
        'button' => 'Надпись на кнопке',
        'comment' => 'Комментарий',
        're' => 'Текст письма',
    );

    public static $crud_model_class_screen_name_for_list = 'Формы';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'form_name' => array('col_class' => 'col-md-6 col-sm-6 col-xs-6'),
        'mail' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = array(
        'form_name' => array(),
        'mail' => array(),
        'button' => array(),
        'comment' => array(
            'widget' => array('\Skif\CRUD\CKEditorWidget\CKEditorWidget', 'renderWidget'),
            'widget_settings' => array(
                'height' => 500,
                'type' => \Skif\CKEditor\CKEditor::CKEDITOR_FULL,
                'dir' => 'form'
            ),
        ),
        're' => array('widget' => 'textarea'),
    );

    public static $crud_related_models_arr = array(
        '\Skif\Form\FormField' => array(
            'link_field' => 'form',
            'list_title' => 'Набор полей формы',
        )
    );


    public function load($id)
    {
        $is_loaded = \Skif\Util\ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        $query = "SELECT id FROM " . \Skif\Form\FormField::DB_TABLE_NAME ." WHERE form = ?";
        $this->form_field_ids_arr = \Skif\DB\DBWrapper::readColumn(
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
     * @return string
     */
    public function getTitle()
    {
        return $this->form_name;
    }

    /**
     * @param mixed $form_name
     */
    public function setFormName($form_name)
    {
        $this->form_name = $form_name;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     * @param mixed $button
     */
    public function setButton($button)
    {
        $this->button = $button;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getRe()
    {
        return $this->re;
    }

    /**
     * @param mixed $re
     */
    public function setRe($re)
    {
        $this->re = $re;
    }

    public function getUrl()
    {
        return '/form/' . $this->getId();
    }

    /**
     * @return mixed
     */
    public function getFormFieldIdsArr()
    {
        return $this->form_field_ids_arr;
    }

}