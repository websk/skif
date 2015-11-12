<?php

namespace Skif\Content;


class Rubric implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    protected $id;
    protected $name;
    protected $comment;
    protected $content_type_id;
    protected $template_id;

    const DB_TABLE_NAME = 'rubrics';

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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

    public static function afterUpdate($rubric_id)
    {
        $rubric_obj = \Skif\Content\Rubric::factory($rubric_id);

        self::removeObjFromCacheById($rubric_id);

        \Skif\Logger\Logger::logObjectEvent($rubric_obj, 'изменение');
    }

    /**
     * @return mixed
     */
    public function getContentTypeId()
    {
        return $this->content_type_id;
    }

    /**
     * @param mixed $content_type_id
     */
    public function setContentTypeId($content_type_id)
    {
        $this->content_type_id = $content_type_id;
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

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        \Skif\Logger\Logger::logObjectEvent($this, 'удаление');
    }

}