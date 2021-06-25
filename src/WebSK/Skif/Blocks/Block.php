<?php

namespace WebSK\Skif\Blocks;

use WebSK\Auth\Auth;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Model\ActiveRecord;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\DB\DBWrapper;
use WebSK\Skif\Content\Template;
use WebSK\Utils\FullObjectId;

/**
 * Class Block
 * @package WebSK\Skif\Blocks
 */
class Block implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceEntity
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'blocks';

    const BLOCK_REGION_NONE = null;

    const BLOCK_NO_CACHE = -1;
    const BLOCK_CACHE_PER_USER = 2;
    const BLOCK_CACHE_PER_PAGE = 4;
    const BLOCK_CACHE_GLOBAL = 8;

    const BLOCK_FORMAT_TYPE_PLAIN = 3;
    const BLOCK_FORMAT_TYPE_HTML = 4;
    const BLOCK_FORMAT_TYPE_PHP = 5;

    protected ?int $id = null;

    protected ?int $template_id = Template::TEMPLATE_ID_MAIN;

    protected int $weight = 1;

    protected ?int $page_region_id = self::BLOCK_REGION_NONE;

    protected string $pages = '+ ^';

    protected string $title = '';

    protected int $cache = self::BLOCK_CACHE_GLOBAL;

    protected string $body = '';

    protected int $format = self::BLOCK_FORMAT_TYPE_PLAIN;

    /**
     * @return string
     */
    public function getEditorUrl(): string
    {
        if (!$this->getId()) {
            return '/admin/blocks/edit/new';
        }

        return '/admin/blocks/edit/' . $this->getId();
    }

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
    public function setPageRegionId(?int $page_region_id)
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
    public function setWeight(int $weight)
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
    public function setTitle(string $title)
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
    public function setBody(string $body)
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
    public function setFormat(int $format)
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
    public function setPages(string $pages)
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
    public function setTemplateId(?int $template_id)
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
    public function setCache(int $cache)
    {
        $this->cache = $cache;
    }

    public function getBlockRoleIdsArr(): array
    {
        $query = "SELECT id FROM " . BlockRole::DB_TABLE_NAME . " WHERE block_id = ?";
        $block_role_ids_arr = DBWrapper::readColumn(
            $query,
            array($this->getId())
        );

        return $block_role_ids_arr;
    }

    public function getRoleIdsArr(): array
    {
        $block_role_ids_arr = $this->getBlockRoleIdsArr();

        $role_ids_arr = array();

        foreach ($block_role_ids_arr as $block_role_id) {
            $block_role_obj = BlockRole::factory($block_role_id);

            $role_ids_arr[] = $block_role_obj->getRoleId();
        }

        return $role_ids_arr;
    }

    /**
     * Выполняет PHP код в блоке и возвращает результат
     * @return string
     */
    public function evalContentPHPBlock(): string
    {
        ob_start();
        print eval('?>'. $this->getBody());
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    public function deleteBlocksRoles()
    {
        $block_role_ids_arr = $this->getBlockRoleIdsArr();

        foreach ($block_role_ids_arr as $block_role_id) {
            $block_role_obj = BlockRole::factory($block_role_id);

            $block_role_obj->delete();
        }
    }

    /**
     * @param $id
     */
    public static function afterUpdate($id)
    {
        $block_obj = self::factory($id);

        self::removeObjFromCacheById($id);

        Logger::logObjectEvent($block_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    public function afterDelete()
    {
        $this->deleteBlocksRoles();

        BlockUtils::clearBlockIdsArrByPageRegionIdCache($this->getPageRegionId(), $this->getTemplateId());
        BlockUtils::clearBlockIdsArrByPageRegionIdCache(self::BLOCK_REGION_NONE, $this->getTemplateId());

        self::removeObjFromCacheById($this->getId());

        Logger::logObjectEvent($this, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
