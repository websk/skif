<?php

namespace WebSK\Skif\Form;

use WebSK\Model\ActiveRecord;
use WebSK\Model\ActiveRecordHelper;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceGetTitle;
use WebSK\Model\InterfaceGetUrl;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Skif\CKEditor\CKEditor;
use WebSK\Skif\CRUD\CKEditorWidget\CKEditorWidget;
use WebSK\Utils\Transliteration;
use Websk\Utils\Assert;
use WebSK\Utils\Url;

/**
 * Class Form
 * @package WebSK\Skif\Form
 */
class Form implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceGetUrl,
    InterfaceGetTitle
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'form';

    protected $id;
    protected $title;
    protected $comment;
    protected $button_label;
    protected $email;
    protected $email_copy;
    protected $response_mail_message;
    protected $url;
    protected $form_field_ids_arr;

    public static $active_record_ignore_fields_arr = array(
        'form_field_ids_arr',
    );

    public static $crud_create_button_required_fields_arr = array();
    public static $crud_create_button_title = 'Добавить форму';

    public static $crud_model_class_screen_name = 'Форма';
    public static $crud_model_title_field = 'title';

    public static $crud_field_titles_arr = array(
        'title' => 'Заголовок',
        'email' => 'E-mail',
        'email_copy' => 'Копия на E-mail',
        'button_label' => 'Надпись на кнопке',
        'comment' => 'Комментарий',
        'response_mail_message' => 'Текст письма',
        'url' => 'Адрес страницы',
    );

    public static $crud_model_class_screen_name_for_list = 'Формы';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'title' => array('col_class' => 'col-md-6 col-sm-6 col-xs-6'),
        'email' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = array(
        'title' => array(),
        'email' => array(),
        'email_copy' => array(),
        'button_label' => array(),
        'comment' => array(
            'widget' => array(CKEditorWidget::class, 'renderWidget'),
            'widget_settings' => array(
                'height' => 500,
                'type' => CKEditor::CKEDITOR_FULL,
                'dir' => 'form'
            ),
        ),
        'response_mail_message' => array('widget' => 'textarea'),
        'url' => array(),
    );

    public static $related_models_arr = array(
        FormField::class => array(
            'link_field' => 'form_id',
            'field_name' => 'form_field_ids_arr',
            'list_title' => 'Набор полей формы',
        )
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmailCopy()
    {
        return $this->email_copy;
    }

    /**
     * @param mixed $email_copy
     */
    public function setEmailCopy($email_copy)
    {
        $this->email_copy = $email_copy;
    }

    /**
     * @return mixed
     */
    public function getButtonLabel()
    {
        return $this->button_label;
    }

    /**
     * @param mixed $button_label
     */
    public function setButtonLabel($button_label)
    {
        $this->button_label = $button_label;
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
    public function getResponseMailMessage()
    {
        return $this->response_mail_message;
    }

    /**
     * @param mixed $response_mail_message
     */
    public function setResponseMailMessage($response_mail_message)
    {
        $this->response_mail_message = $response_mail_message;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function generateUrl()
    {
        if (!$this->getTitle()) {
            return '';
        }

        $title_for_url = Transliteration::transliteration($this->getTitle());

        $new_url = $title_for_url;
        $new_url = '/' . ltrim($new_url, '/');

        $new_url = substr($new_url, 0, 255);

        $unique_new_url = Url::getUniqueUrl($new_url);
        Assert::assert($unique_new_url);

        return $unique_new_url;
    }

    /**
     * @return mixed
     */
    public function getFormFieldIdsArr()
    {
        return $this->form_field_ids_arr;
    }

    public function save()
    {
        if ($this->url) {
            $url = '/' . ltrim($this->url, '/');

            if ($url != $this->getUrl()) {
                Url::getUniqueUrl($url);
            }
        } else {
            $url = $this->generateUrl();
            $url = '/' . ltrim($url, '/');
        }

        $this->setUrl($url);

        ActiveRecordHelper::saveModelObj($this);

        self::afterUpdate($this->getId());
    }
}