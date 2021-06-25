<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;
use WebSK\Views\ViewsPath;

/**
 * Class Template
 * @package WebSK\Skif\Content
 */
class Template extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'skif.template_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.template_repository';
    const DB_TABLE_NAME = 'template';

    const TEMPLATE_ID_MAIN = 1;
    const TEMPLATE_ID_ADMIN = 2;

    const _TITLE = 'title';
    /** @var string */
    protected $title = '';

    const _NAME = 'name';
    /** @var string */
    protected $name = '';

    const _CSS = 'css';
    /** @var string */
    protected $css = '';

    const _IS_DEFAULT = 'is_default';
    /** @var bool */
    protected $is_default = false;

    const _LAYOUT_TEMPLATE_FILE = 'layout_template_file';
    /** @var string  */
    protected $layout_template_file = '';

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
    public function getCss(): string
    {
        return $this->css;
    }

    /**
     * @param string $css
     */
    public function setCss(string $css): void
    {
        $this->css = $css;
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
    public function getLayoutTemplateFilePath()
    {
        return ViewsPath::getSiteViewsPath() . '/layouts/' . $this->layout_template_file;
    }
}
