<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;

/**
 * Class ContentRubric
 * @package WebSK\Skif\Content
 */
class ContentRubric extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'skif.content_rubric_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.content_rubric_repository';
    const DB_TABLE_NAME = 'content_rubrics';

    const _CONTENT_ID = 'content_id';
    /** @var int */
    protected $content_id;

    const _RUBRIC_ID = 'rubric_id';
    /** @var int */
    protected $rubric_id;

    /**
     * @return int
     */
    public function getContentId(): int
    {
        return $this->content_id;
    }

    /**
     * @param int $content_id
     */
    public function setContentId(int $content_id): void
    {
        $this->content_id = $content_id;
    }

    /**
     * @return int
     */
    public function getRubricId(): int
    {
        return $this->rubric_id;
    }

    /**
     * @param int $rubric_id
     */
    public function setRubricId(int $rubric_id): void
    {
        $this->rubric_id = $rubric_id;
    }
}
