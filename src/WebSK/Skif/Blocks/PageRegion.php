<?php

namespace WebSK\Skif\Blocks;

use WebSK\Auth\Auth;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Model\ActiveRecord;
use WebSK\Model\ActiveRecordHelper;
use WebSK\Model\Factory;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Cache\CacheWrapper;
use WebSK\Utils\Assert;
use WebSK\Utils\FullObjectId;

/**
 * Class PageRegion
 * @package WebSK\Skif\Blocks
 */
class PageRegion implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceEntity
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
     * @param int $id
     * @return bool
     */
    public function load(int $id)
    {
        $is_loaded = ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        return true;
    }

    public static function factory(?int $id_to_load, bool $exception_if_not_loaded = true)
    {
        if ($id_to_load == Block::BLOCK_REGION_NONE) {
            $obj = new PageRegion();
            $obj->id = $id_to_load;
            $obj->setName('disabled');
            $obj->setTitle('Отключенные блоки');

            return $obj;
        }

        $class_name = self::getMyGlobalizedClassName();
        $obj = Factory::createAndLoadObject($class_name, $id_to_load);

        if ($exception_if_not_loaded) {
            Assert::assert($obj);
        }

        return $obj;
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

        Logger::logObjectEvent($page_region_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    public function afterDelete()
    {
        $cache_key = PageRegionsUtils::getPageRegionIdByNameAndTemplateIdCacheKey(
            $this->getName(),
            $this->getTemplateId()
        );
        CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($this->getId());

        Logger::logObjectEvent($this, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
