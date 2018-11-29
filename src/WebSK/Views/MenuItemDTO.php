<?php

namespace WebSK\Views;

/**
 * Class MenuItemDTO
 * @package WebSK\Views
 */
class MenuItemDTO
{
    /** @var string */
    protected $text = '';
    /** @var string */
    protected $url = '';
    /** @var self[] */
    protected $children_arr = [];
    /** @var string */
    protected $icon_classes_str = '';
    /** @var array */
    protected $permissions_arr = [];

    /**
     * MenuItem constructor.
     * @param string $text
     * @param string $url
     * @param array $children_arr
     * @param string $icon_classes_str
     * @param array $permissions_arr
     */
    public function __construct(
        string $text = '',
        string $url = '',
        array $children_arr = [],
        string $icon_classes_str = '',
        array $permissions_arr = []
    ) {
        $this->text = $text;
        $this->url = $url;
        $this->children_arr = $children_arr;
        $this->icon_classes_str = $icon_classes_str;
        $this->permissions_arr = $permissions_arr;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
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
     * @return self[]
     */
    public function getChildrenArr(): array
    {
        return $this->children_arr;
    }

    /**
     * @param array $children_arr
     */
    public function setChildrenArr(array $children_arr): void
    {
        $this->children_arr = $children_arr;
    }

    /**
     * @return string
     */
    public function getIconClassesStr(): string
    {
        return $this->icon_classes_str;
    }

    /**
     * @param string $icon_classes_str
     */
    public function setIconClassesStr(string $icon_classes_str): void
    {
        $this->icon_classes_str = $icon_classes_str;
    }

    /**
     * @return array
     */
    public function getPermissionsArr(): array
    {
        return $this->permissions_arr;
    }

    /**
     * @param array $permissions_arr
     */
    public function setPermissionsArr(array $permissions_arr): void
    {
        $this->permissions_arr = $permissions_arr;
    }
}
