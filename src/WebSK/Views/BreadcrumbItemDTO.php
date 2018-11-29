<?php

namespace WebSK\Views;

/**
 * Class BreadcrumbItemDTO
 * @package WebSK\Views
 */
class BreadcrumbItemDTO
{
    /** @var string */
    protected $name = '';
    /** @var string */
    protected $url = '';

    /**
     * BreadcrumbItemDTO constructor.
     * @param string $name
     * @param string $url
     */
    public function __construct(string $name, string $url = '')
    {
        $this->name = $name;
        $this->url = $url;
    }

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
