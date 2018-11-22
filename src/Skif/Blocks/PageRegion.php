<?php

namespace Skif\Blocks;


class PageRegion implements
    \WebSK\Model\InterfaceLoad,
    \WebSK\Model\InterfaceFactory,
    \WebSK\Model\InterfaceSave,
    \WebSK\Model\InterfaceDelete,
    \WebSK\Model\InterfaceLogger
{
    use WebSK\Model\ActiveRecord;
    use WebSK\Model\FactoryTrait;

    protected $id;
    protected $name;
    protected $template_id;
    protected $title;

    const DB_TABLE_NAME = 'page_regions';


    public function load($id)
    {
        if ($id == \Skif\Blocks\Block::BLOCK_REGION_NONE) {
            $this->id = $id;
            $this->name = 'disabled';
            $this->title = 'Отключенные блоки';

            return true;
        }

        $is_loaded = \WebSK\Model\ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        return true;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->template_id;
    }

    /**
     * @param mixed $template_id
     */
    public function setTemplateId($template_id)
    {
        $this->template_id = $template_id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public static function afterUpdate($page_region_id)
    {
        $page_region_obj = \Skif\Blocks\PageRegion::factory($page_region_id);

        $cache_key = \Skif\Blocks\PageRegionsUtils::getPageRegionIdByNameAndTemplateIdCacheKey($page_region_obj->getName(), $page_region_obj->getTemplateId());
        \Websk\Skif\CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($page_region_id);
    }

    public function afterDelete()
    {
        $cache_key = \Skif\Blocks\PageRegionsUtils::getPageRegionIdByNameAndTemplateIdCacheKey($this->getName(), $this->getTemplateId());
        \Websk\Skif\CacheWrapper::delete($cache_key);

        self::removeObjFromCacheById($this->getId());
    }

}