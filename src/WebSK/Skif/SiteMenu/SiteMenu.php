<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Entity\Entity;

/**
 * Class SiteMenu
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenu extends Entity
{
    const DB_TABLE_NAME = 'site_menu';

    const _NAME = 'name';
    protected string $name = '';

    const _URL = 'url';
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
