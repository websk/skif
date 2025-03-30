<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;

/**
 * Class ContentRubric
 * @package WebSK\Skif\Content
 */
class ContentRubric extends Entity
{
    const string DB_TABLE_NAME = 'content_rubrics';

    const string _CONTENT_ID = 'content_id';
    protected ?int $content_id = null;

    const string _RUBRIC_ID = 'rubric_id';
    protected ?int $rubric_id = null;

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
