<?php

namespace WebSK\Skif\Content;

use WebSK\Model\ActiveRecord;
use WebSK\Model\ActiveRecordHelper;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceGetTitle;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Skif\CRUD\ModelReferenceWidget\ModelReferenceWidget;
use WebSK\DB\DBWrapper;

/**
 * Class ContentType
 * @package WebSK\Skif\Content
 */
class ContentType implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceGetTitle
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'content_types';

    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var string */
    protected $url;

    /** @var int */
    protected $template_id;

    /** @var array */
    protected $rubric_ids_arr = [];

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
            'widget' => array(ModelReferenceWidget::class, 'renderWidget'),
            'widget_settings' => array(
                'model_class_name' => Template::class
            )
        ),
    );

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function load($id)
    {
        $is_loaded = ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        $query = "SELECT id FROM " . Rubric::DB_TABLE_NAME ." WHERE content_type_id = ?";
        $this->rubric_ids_arr = DBWrapper::readColumn(
            $query,
            array($this->id)
        );

        return true;
    }

    /**
     * @return int
     */
    public function getId(): int
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
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getTemplateId(): int
    {
        return $this->template_id;
    }

    /**
     * @param int $template_id
     */
    public function setTemplateId(int $template_id): void
    {
        $this->template_id = $template_id;
    }

    /**
     * @return array
     */
    public function getRubricIdsArr(): array
    {
        return $this->rubric_ids_arr;
    }

    /**
     * @param array $rubric_ids_arr
     */
    public function setRubricIdsArr(array $rubric_ids_arr): void
    {
        $this->rubric_ids_arr = $rubric_ids_arr;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->name;
    }
}
