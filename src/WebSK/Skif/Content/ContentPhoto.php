<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;

/**
 * Class ContentPhoto
 * @package WebSK\Skif\Content
 */
class ContentPhoto extends Entity
{
    const string DB_TABLE_NAME = 'content_photo';

    const string _CONTENT_ID = 'content_id';
    protected ?int $content_id = null;

    const string _PHOTO = 'photo';
    protected ?string $photo = null;

    const string _IS_DEFAULT = 'is_default';
    protected bool $is_default = false;

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
    public function getPhotoPath(): string
    {
        return Content::CONTENT_FILES_DIR . '/' . $this->getPhoto();
    }
}
