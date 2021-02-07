<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;

/**
 * Class ContentType
 * @package WebSK\Skif\Content
 */
class ContentType extends Entity
{

    const ENTITY_SERVICE_CONTAINER_ID = 'skif.content_type_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.content_type_repository';
    const DB_TABLE_NAME = 'content_types';

    const _NAME = 'name';
    /** @var string */
    protected $name;

    const _TYPE = 'type';
    /** @var string */
    protected $type;

    const _URL = 'url';
    /** @var string */
    protected $url;

    const _TEMPLATE_ID = 'template_id';
    /** @var int */
    protected $template_id;

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
}
