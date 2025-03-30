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
    const string DB_TABLE_NAME = 'template';

    const string LAYOUTS_FILES_DIR = 'layouts';

    const int TEMPLATE_ID_MAIN = 1;
    const int TEMPLATE_ID_ADMIN = 2;

    const string _TITLE = 'title';
    protected string $title = '';

    const string _NAME = 'name';
    protected string $name = '';

    const string _CSS = 'css';
    protected string $css = '';

    const string _IS_DEFAULT = 'is_default';
    protected bool $is_default = false;

    const string _LAYOUT_TEMPLATE_FILE = 'layout_template_file';
    protected string $layout_template_file = '';

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
    public function getLayoutTemplateFilePath(): string
    {
        return ViewsPath::getSiteViewsPath() . '/' . self::LAYOUTS_FILES_DIR . '/' . $this->layout_template_file;
    }
}
