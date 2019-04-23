<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;

/**
 * Class ContentPhoto
 * @package WebSK\Skif\Content
 */
class ContentPhoto extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'skif.content_photo_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.content_photo_repository';
    const DB_TABLE_NAME = 'content_photo';

    /** @var int */
    protected $content_id;

    /** @var string */
    protected $photo;

    /** @var bool */
    protected $is_default = false;

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
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
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
     * @return string
     */
    public function getPhotoPath()
    {
        $image_path = 'content/' . $this->getPhoto();

        return $image_path;
    }
}
