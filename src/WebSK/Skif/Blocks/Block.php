<?php

namespace WebSK\Skif\Blocks;

use WebSK\Entity\Entity;
use WebSK\Skif\Content\Template;

/**
 * Class Block
 * @package WebSK\Skif\Blocks
 */
class Block extends Entity
{

    const string DB_TABLE_NAME = 'blocks';

    const int BLOCK_NO_CACHE = -1;
    const int BLOCK_CACHE_PER_USER = 2;
    const int BLOCK_CACHE_PER_PAGE = 4;
    const int BLOCK_CACHE_GLOBAL = 8;

    const array BLOCK_CACHES_ARRAY = [
        Block::BLOCK_NO_CACHE => 'не кэшировать',
        Block::BLOCK_CACHE_PER_USER => 'кэшировать для каждого пользователя',
        Block::BLOCK_CACHE_PER_PAGE => 'кэшировать для каждого урла',
        Block::BLOCK_CACHE_GLOBAL => 'кэшировать глобально'
    ];

    const int BLOCK_FORMAT_TYPE_PLAIN = 3;
    const int BLOCK_FORMAT_TYPE_HTML = 4;
    const int BLOCK_FORMAT_TYPE_PHP = 5;

    const array BLOCK_FORMATS_ARRAY = [
        Block::BLOCK_FORMAT_TYPE_PLAIN => 'Текст',
        Block::BLOCK_FORMAT_TYPE_HTML => 'HTML',
        Block::BLOCK_FORMAT_TYPE_PHP => 'PHP code'
    ];

    const string _TEMPLATE_ID = 'template_id';
    protected ?int $template_id = Template::TEMPLATE_ID_MAIN;

    const string _WEIGHT = 'weight';
    protected int $weight = 1;

    const string _PAGE_REGION_ID = 'page_region_id';
    protected ?int $page_region_id = PageRegion::BLOCK_REGION_NONE;

    const string _PAGES = 'pages';
    protected string $pages = '+ ^';

    const string _TITLE = 'title';
    protected string $title = '';

    const string_CACHE = 'cache';
    protected int $cache = self::BLOCK_CACHE_GLOBAL;

    const string _BODY = 'body';
    protected string $body = '';

    const string _FORMAT = 'format';
    protected int $format = self::BLOCK_FORMAT_TYPE_PLAIN;


    /**
     * Был ли загружен блок
     * @return bool
     */
    public function isLoaded(): bool
    {
        return !empty($this->id);
    }

    /**
     * ID блока
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|int
     */
    public function getPageRegionId(): ?int
    {
        return $this->page_region_id;
    }

    /**
     * @param null|int $page_region_id
     */
    public function setPageRegionId(?int $page_region_id): void
    {
        $this->page_region_id = $page_region_id;
    }

    /**
     * Вес блока
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
     * Содержимое блока
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * Формат блока
     * @return int
     */
    public function getFormat(): int
    {
        return $this->format;
    }

    /**
     * @param int $format
     */
    public function setFormat(int $format): void
    {
        $this->format = $format;
    }

    /**
     * Условия видимости для блока
     * @return string
     */
    public function getPages(): string
    {
        return $this->pages;
    }

    /**
     * @param string $pages
     */
    public function setPages(string $pages): void
    {
        $this->pages = $pages;
    }

    /**
     * @return null|int
     */
    public function getTemplateId(): ?int
    {
        return $this->template_id;
    }

    /**
     * @param null|int $template_id
     */
    public function setTemplateId(?int $template_id): void
    {
        $this->template_id = $template_id;
    }

    /**
     * Контекст кэширования
     * @return int
     */
    public function getCache(): int
    {
        return $this->cache;
    }

    /**
     * @param int $cache
     */
    public function setCache(int $cache): void
    {
        $this->cache = $cache;
    }

}
