<?php

namespace WebSK\Skif\Blocks;

use WebSK\Entity\Entity;

/**
 * Class PageRegion
 * @package WebSK\Skif\Blocks
 */
class PageRegion extends Entity
{

    const string DB_TABLE_NAME = 'page_regions';

    public const null BLOCK_REGION_NONE = null;

    const string _NAME = 'name';
    protected string $name;

    const string _TEMPLATE_ID = 'template_id';
    protected int $template_id;

    const string _TITLE = 'title';
    protected string $title;

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
     * @return int
     */
    public function getTemplateId(): int
    {
        return $this->template_id;
    }

    /**
     * @param int $template_id
     */
    public function setTemplateId(int $template_id): void
    {
        $this->template_id = $template_id;
    }

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

}
