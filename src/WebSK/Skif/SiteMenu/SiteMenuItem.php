<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Entity\Entity;
use WebSK\Entity\InterfaceWeight;
use WebSK\Entity\WeightTrait;

/**
 * Class SiteMenuItem
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuItem extends Entity implements InterfaceWeight
{
    use WeightTrait;

    const string DB_TABLE_NAME = 'site_menu_item';

    const string _NAME = 'name';
    protected string $name = '';

    const string _URL = 'url';
    protected string $url = '';

    const string _CONTENT_ID = 'content_id';
    protected ?int $content_id = null;

    const string _WEIGHT = 'weight';

    const string _PARENT_ID = 'parent_id';
    protected ?int $parent_id = null;

    const string _IS_PUBLISHED = 'is_published';
    protected bool $is_published = false;

    const string _MENU_ID = 'menu_id';
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
     * @return null|int
     */
    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    /**
     * @param null|int $parent_id
     */
    public function setParentId(?int $parent_id): void
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
