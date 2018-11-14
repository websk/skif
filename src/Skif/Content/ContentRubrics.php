<?php

namespace Skif\Content;


class ContentRubrics implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use Skif\Model\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    protected $id;
    protected $content_id;
    protected $rubric_id;

    const DB_TABLE_NAME = 'content_rubrics';

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
    public function getContentId()
    {
        return $this->content_id;
    }

    /**
     * @param mixed $content_id
     */
    public function setContentId($content_id)
    {
        $this->content_id = $content_id;
    }

    /**
     * @return mixed
     */
    public function getRubricId()
    {
        return $this->rubric_id;
    }

    /**
     * @param mixed $rubric_id
     */
    public function setRubricId($rubric_id)
    {
        $this->rubric_id = $rubric_id;
    }

    public static function afterUpdate($content_rubrics_id)
    {
        $content_rubrics_obj = \Skif\Content\ContentRubrics::factory($content_rubrics_id);

        \Skif\Content\Content::afterUpdate($content_rubrics_obj->getContentId());
        \Skif\Content\Rubric::afterUpdate($content_rubrics_obj->getRubricId());

        self::removeObjFromCacheById($content_rubrics_id);
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        \Skif\Content\Content::afterUpdate($this->getContentId());
        \Skif\Content\Rubric::afterUpdate($this->getRubricId());
    }

}