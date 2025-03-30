<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;

/**
 * Class Rubric
 * @package WebSK\Skif\Content
 */
class Rubric extends Entity
{

    const string DB_TABLE_NAME = 'rubrics';

    const string _NAME = 'name';
    protected string $name = '';

    const string _COMMENT = 'comment';
    protected ?string $comment = null;

    const string _CONTENT_TYPE_ID = 'content_type_id';
    protected ?int $content_type_id = null;

    const string _TEMPLATE_ID = 'template_id';
    protected ?int $template_id = null;

    const string _URL = 'url';
    protected string $url = '';

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
     * @return int|null
     */
    public function getTemplateId(): ?int
    {
        return $this->template_id;
    }

    /**
     * @param int|null $template_id
     */
    public function setTemplateId(?int $template_id): void
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
