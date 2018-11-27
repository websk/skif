<?php

namespace WebSK\Skif\Blocks;

use WebSK\Model\ActiveRecord;
use WebSK\Model\ActiveRecordHelper;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceLogger;
use WebSK\Model\InterfaceSave;
use Websk\Cache\CacheWrapper;

/**
 * Class PageRegion
 * @package WebSK\Skif\Blocks
 */
class PageRegion implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceLogger
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'page_regions';

    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var int */
    protected $template_id;

    /** @var string */
    protected $title;

    /**
     * @param $id
     * @return bool
     */
    public function load($id)
    {
        if ($id == Block::BLOCK_REGION_NONE) {
            $this->id = $id;
            $this->name = 'disabled';
            $this->title = 'Отключенные блоки';

            return true;
        }

        $is_loaded = ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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

    /**
     * @param $page_region_id
     */
    public static function afterUpdate($page_region_id)
    {
        $page_region_obj = self::factory($page_region_id);

        $cache_key = PageRegionsUtils::getPageRegionIdByNameAndTemplateIdCacheKey(
            $page_region_obj->getName(),
            $page_region_obj->getTemplateId()
        );
        CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($page_region_id);
    }

    public function afterDelete()
    {
        $cache_key = PageRegionsUtils::getPageRegionIdByNameAndTemplateIdCacheKey(
            $this->getName(),
            $this->getTemplateId()
        );
        CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($this->getId());
    }
}
