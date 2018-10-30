<?php

namespace WebSK\Skif;

use VitrinaTV\Core\UI\BreadcrumbItemDTO;
use VitrinaTV\Core\UI\MenuItemDTO;
use VitrinaTV\Core\UI\NavTabItemDTO;

class LayoutDTO
{
    /** @var string */
    protected $title = '';
    /** @var string */
    protected $site_title = '';
    /** @var string */
    protected $short_site_title = '';
    /** @var int */
    protected $user_id = null;
    /** @var string */
    protected $user_name = '';
    /** @var string */
    protected $logout_url = '';
    /** @var MenuItemDTO[] */
    protected $menu_item_dto_arr = [];
    /** @var string */
    protected $content_html = '';
    /** @var string */
    protected $page_url = '';
    /** @var NavTabItemDTO[] */
    protected $nav_tabs_dto_arr = [];
    /** @var BreadcrumbItemDTO[] */
    protected $breadcrumbs_dto_arr= [];

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
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSiteTitle(): string
    {
        return $this->site_title;
    }

    /**
     * @param string $site_title
     */
    public function setSiteTitle(string $site_title)
    {
        $this->site_title = $site_title;
    }

    /**
     * @return string
     */
    public function getShortSiteTitle(): string
    {
        return $this->short_site_title;
    }

    /**
     * @param string $short_site_title
     */
    public function setShortSiteTitle(string $short_site_title)
    {
        $this->short_site_title = $short_site_title;
    }

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return $this->user_name;
    }

    /**
     * @param string $user_name
     */
    public function setUserName(string $user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * @return string
     */
    public function getLogoutUrl(): string
    {
        return $this->logout_url;
    }

    /**
     * @param string $logout_url
     */
    public function setLogoutUrl(string $logout_url)
    {
        $this->logout_url = $logout_url;
    }

    /**
     * @return MenuItemDTO[]
     */
    public function getMenuItemDtoArr(): array
    {
        return $this->menu_item_dto_arr;
    }

    /**
     * @param MenuItemDTO[] $menu_item_dto_arr
     */
    public function setMenuItemDtoArr(array $menu_item_dto_arr)
    {
        $this->menu_item_dto_arr = $menu_item_dto_arr;
    }

    /**
     * @return string
     */
    public function getContentHtml(): string
    {
        return $this->content_html;
    }

    /**
     * @param string $content_html
     */
    public function setContentHtml(string $content_html)
    {
        $this->content_html = $content_html;
    }

    /**
     * @return string
     */
    public function getPageUrl(): string
    {
        return $this->page_url;
    }

    /**
     * @param string $page_url
     */
    public function setPageUrl(string $page_url)
    {
        $this->page_url = $page_url;
    }

    /**
     * @return array
     */
    public function getNavTabsDtoArr(): array
    {
        return $this->nav_tabs_dto_arr;
    }

    /**
     * @param array $nav_tabs_dto_arr
     */
    public function setNavTabsDtoArr(array $nav_tabs_dto_arr): void
    {
        $this->nav_tabs_dto_arr = $nav_tabs_dto_arr;
    }

    /**
     * @return BreadcrumbItemDTO[]
     */
    public function getBreadcrumbsDtoArr(): array
    {
        return $this->breadcrumbs_dto_arr;
    }

    /**
     * @param BreadcrumbItemDTO[] $breadcrumbs_dto_arr
     */
    public function setBreadcrumbsDtoArr(array $breadcrumbs_dto_arr): void
    {
        $this->breadcrumbs_dto_arr = $breadcrumbs_dto_arr;
    }
}
