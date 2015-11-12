<?php

namespace Skif\Content;


class ContentRubrics implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
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


}