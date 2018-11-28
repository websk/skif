<?php

namespace Skif\Poll;

use WebSK\Skif\CRUD\DatepickerWidget\DatepickerWidget;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceGetTitle;
use WebSK\Model\InterfaceGetUrl;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Model\ActiveRecord;

class Poll implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceGetUrl,
    InterfaceGetTitle
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'poll';

    protected $id;
    protected $title = '';
    protected $is_default = 0;
    protected $is_published = 0;
    protected $published_at;
    protected $unpublished_at;
    protected $poll_questions_ids_arr;

    public function __construct()
    {
        $this->published_at = date('Y-m-d H:i:s');
    }

    public static $active_record_ignore_fields_arr = array(
        'poll_questions_ids_arr',
    );

    public static $crud_create_button_required_fields_arr = array();
    public static $crud_create_button_title = 'Добавить опрос';

    public static $crud_model_class_screen_name = 'Опрос';
    public static $crud_model_title_field = 'title';

    public static $crud_field_titles_arr = array(
        'title' => 'Заголовок',
        'is_default' => 'По-умолчанию',
        'is_published' => 'Опубликовано',
        'published_at' => 'Показывать с',
        'unpublished_at' => 'Показывать по',
    );

    public static $crud_model_class_screen_name_for_list = 'Опросы';

    public static $crud_fields_list_arr = array(
        'id' => array('col_class' => 'col-md-1 col-sm-1 col-xs-1'),
        'title' => array('col_class' => 'col-md-6 col-sm-6 col-xs-6'),
        'is_published' => array('col_class' => 'col-md-2 hidden-sm hidden-xs', 'td_class' => 'hidden-sm hidden-xs'),
        '' => array('col_class' => 'col-md-3 col-sm-5 col-xs-5'),
    );

    public static $crud_editor_fields_arr = [
        'title' => [],
        'is_default' => ['widget' => 'checkbox'],
        'is_published' => ['widget' => 'checkbox'],
        'published_at' => [
            'widget' => [DatepickerWidget::class, 'renderWidget'],
            'widget_settings' => [
                'date_format' => 'YYYY-MM-DD'
            ],
        ],
        'unpublished_at' => [
            'widget' => [DatepickerWidget::class, 'renderWidget'],
            'widget_settings' => [
                'date_format' => 'YYYY-MM-DD'
            ],
        ],
    ];

    // Связанные модели
    public static $related_models_arr = array(
        PollQuestion::class => array(
            'link_field' => 'poll_id',
            'field_name' => 'poll_questions_ids_arr',
            'list_title' => 'Варианты ответов',
        ),
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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getIsDefault()
    {
        return $this->is_default;
    }

    /**
     * @param int $is_default
     */
    public function setIsDefault($is_default)
    {
        $this->is_default = $is_default;
    }

    /**
     * @return int
     */
    public function getIsPublished()
    {
        return $this->is_published;
    }

    /**
     * @param int $is_published
     */
    public function setIsPublished($is_published)
    {
        $this->is_published = $is_published;
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * @param mixed $published_at
     */
    public function setPublishedAt($published_at)
    {
        $this->published_at = $published_at;
    }

    /**
     * @return mixed
     */
    public function getUnpublishedAt()
    {
        return $this->unpublished_at;
    }

    /**
     * @param mixed $unpublished_at
     */
    public function setUnpublishedAt($unpublished_at)
    {
        $this->unpublished_at = $unpublished_at;
    }

    /**
     * @return mixed
     */
    public function getPollQuestionsIdsArr()
    {
        return $this->poll_questions_ids_arr;
    }

    public function getUrl()
    {
        return '/poll/' . $this->getId();
    }
}
