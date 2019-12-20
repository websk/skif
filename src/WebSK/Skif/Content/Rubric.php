<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;

/**
 * Class Rubric
 * @package WebSK\Skif\Content
 */
class Rubric extends Entity
{

    const ENTITY_SERVICE_CONTAINER_ID = 'skif.rubric_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.rubric_repository';
    const DB_TABLE_NAME = 'rubrics';

    const _NAME = 'name';
    /** @var string */
    protected $name = '';

    const _COMMENT = 'comment';
    /** @var string */
    protected $comment = '';

    const _CONTENT_TYPE_ID = 'content_type_id';
    /** @var int */
    protected $content_type_id;

    const _TEMPLATE_ID = 'template_id';
    /** @var int */
    protected $template_id;

    const _URL = 'url';
    /** @var string */
    protected $url = '';

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
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getContentTypeId(): int
    {
        return $this->content_type_id;
    }

    /**
     * @param int $content_type_id
     */
    public function setContentTypeId(int $content_type_id): void
    {
        $this->content_type_id = $content_type_id;
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
}
