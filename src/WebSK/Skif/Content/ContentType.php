<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;

/**
 * Class ContentType
 * @package WebSK\Skif\Content
 */
class ContentType extends Entity
{
    const DB_TABLE_NAME = 'content_types';

    const CONTENT_TYPE_PAGE = 'page';

    const _NAME = 'name';
    protected string $name;

    const _TYPE = 'type';
    protected string $type;

    const _URL = 'url';
    protected string $url;

    const _TEMPLATE_ID = 'template_id';
    protected ?int $template_id = null;

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
     * @return null|int
     */
    public function getTemplateId(): ?int
    {
        return $this->template_id;
    }

    /**
     * @param null|int $template_id
     */
    public function setTemplateId(?int $template_id): void
    {
        $this->template_id = $template_id;
    }
}
