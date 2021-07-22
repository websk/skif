<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Entity\Entity;

/**
 * Class SiteMenuItem
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuItem extends Entity
{
    const DB_TABLE_NAME = 'site_menu_item';

    const _NAME = 'name';
    protected string $name = '';

    const _URL = 'url';
    protected string $url = '';

    const _CONTENT_ID = 'content_id';
    protected ?int $content_id = null;

    const _WEIGHT = 'weight';
    protected int $weight = 0;

    const _PARENT_ID = 'parent_id';
    protected int $parent_id = 0;

    const _IS_PUBLISHED = 'is_published';
    protected bool $is_published = false;

    const _MENU_ID = 'menu_id';
    protected ?int $menu_id = null;

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
     * @return int|null
     */
    public function getContentId(): ?int
    {
        return $this->content_id;
    }

    /**
     * @param int|null $content_id
     */
    public function setContentId(?int $content_id): void
    {
        $this->content_id = $content_id;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parent_id;
    }

    /**
     * @param int $parent_id
     */
    public function setParentId(int $parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
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
     * @return int|null
     */
    public function getMenuId(): ?int
    {
        return $this->menu_id;
    }

    /**
     * @param int|null $menu_id
     */
    public function setMenuId(?int $menu_id): void
    {
        $this->menu_id = $menu_id;
    }
}
