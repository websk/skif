<?php

namespace Skif\Content;

use WebSK\Model\ActiveRecord;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;

/**
 * Class ContentRubrics
 * @package Skif\Content
 */
class ContentRubrics implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete
{
    use ActiveRecord;
    use FactoryTrait;

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
        $content_rubrics_obj = self::factory($content_rubrics_id);

        Content::afterUpdate($content_rubrics_obj->getContentId());
        Rubric::afterUpdate($content_rubrics_obj->getRubricId());

        self::removeObjFromCacheById($content_rubrics_id);
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        Content::afterUpdate($this->getContentId());
        Rubric::afterUpdate($this->getRubricId());
    }
}
