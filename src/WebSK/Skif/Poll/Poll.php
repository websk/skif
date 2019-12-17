<?php

namespace WebSK\Skif\Poll;

use WebSK\Entity\Entity;

/**
 * Class Poll
 * @package WebSK\Skif\Poll
 */
class Poll extends Entity
{

    const ENTITY_SERVICE_CONTAINER_ID = 'skif.poll_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.poll_repository';
    const DB_TABLE_NAME = 'poll';

    const _TITLE = 'title';
    /** @var string */
    protected $title = '';

    const _IS_DEFAULT = 'is_default';
    /** @var bool */
    protected $is_default = false;

    const _IS_PUBLISHED = 'is_published';
    /** @var bool */
    protected $is_published = false;

    const _PUBLISHED_AT = 'published_at';
    /** @var string */
    protected $published_at;

    const _UNPUBLISHED_AT = 'unpublished_at';
    /** @var string */
    protected $unpublished_at;


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return bool
     */
    public function isIsDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * @param bool $is_default
     */
    public function setIsDefault(bool $is_default): void
    {
        $this->is_default = $is_default;
    }

    /**
     * @return bool
     */
    public function isIsPublished(): bool
    {
        return $this->is_published;
    }

    /**
     * @param bool $is_published
     */
    public function setIsPublished(bool $is_published): void
    {
        $this->is_published = $is_published;
    }

    /**
     * @return string
     */
    public function getPublishedAt(): string
    {
        return $this->published_at;
    }

    /**
     * @param string $published_at
     */
    public function setPublishedAt(string $published_at): void
    {
        $this->published_at = $published_at;
    }

    /**
     * @return string
     */
    public function getUnpublishedAt(): string
    {
        return $this->unpublished_at;
    }

    /**
     * @param string $unpublished_at
     */
    public function setUnpublishedAt(string $unpublished_at): void
    {
        $this->unpublished_at = $unpublished_at;
    }
}
