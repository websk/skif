<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Model\ActiveRecord;
use WebSK\Model\ActiveRecordHelper;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Utils\Filters;

/**
 * Class SiteMenuItem
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuItem implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete
{
    use ActiveRecord;
    use FactoryTrait;

    protected $id;
    protected $name;
    protected $url;
    protected $content_id;
    protected $weight = 0;
    protected $parent_id = 0;
    protected $is_published = 0;
    protected $menu_id;
    protected $children_ids_arr;

    const DB_TABLE_NAME = 'site_menu_item';

    public static $active_record_ignore_fields_arr = array(
        'children_ids_arr'
    );

    public function load($id)
    {
        $is_loaded = ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        $this->children_ids_arr = SiteMenuUtils::getSiteMenuItemIdsArr($this->getMenuId(), $this->getId());

        return true;
    }

    public function getEditorUrl()
    {
        return '/admin/site_menu/' . $this->getMenuId() . '/item/edit/' . $this->getId();
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
        return Filters::checkPlain($this->name);
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
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getContentId()
    {
        return $this->content_id;
    }

    /**
     * @param mixed $content_id
     */
    public function setContentId($content_id)
    {
        $this->content_id = $content_id;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return mixed
     */
    public function isPublished()
    {
        return $this->is_published;
    }

    /**
     * @param mixed $is_published
     */
    public function setIsPublished($is_published)
    {
        $this->is_published = $is_published;
    }

    /**
     * @return mixed
     */
    public function getMenuId()
    {
        return $this->menu_id;
    }

    /**
     * @param mixed $menu_id
     */
    public function setMenuId($menu_id)
    {
        $this->menu_id = $menu_id;
    }

    public function getChildrenIdsArr()
    {
        return $this->children_ids_arr;
    }

    /**
     * Массив всех потомков
     * @return array
     */
    public function getDescendantsIdsArr()
    {
        $children_ids_arr = $this->getChildrenIdsArr();
        $descendants_ids_arr = $children_ids_arr;
        foreach ($children_ids_arr as $children_site_menu_item_id) {
            $children_site_menu_item_obj = self::factory($children_site_menu_item_id);

            $descendants_ids_arr = array_merge(
                $descendants_ids_arr,
                $children_site_menu_item_obj->getDescendantsIdsArr()
            );
        }
        return $descendants_ids_arr;
    }

    /**
     * Массив идентификаторов предков снизу вверх
     * @return array
     * @throws \Exception
     */
    public function getAncestorsIdsArr()
    {
        $current_site_menu_item_obj = $this;
        $ancestors_ids_arr = array();
        $iteration = 0;

        while ($parent_id = $current_site_menu_item_obj->getParentId()) {
            if ($parent_id == $current_site_menu_item_obj->getId()) {
                throw new \Exception('Пункт меню ' . $this->getId() . ' не может быть родительским по отношению к самому себе');
            }

            if ($iteration > 20) {
                break;
            }

            $ancestors_ids_arr[] = $parent_id;
            $current_site_menu_item_obj = self::factory($parent_id);

            $iteration++;
        }

        return $ancestors_ids_arr;
    }

    public static function afterUpdate($item_id)
    {
        $item_obj = self::factory($item_id);

        self::removeObjFromCacheById($item_id);
        self::removeObjFromCacheById($item_obj->getParentId());
    }

    public function afterDelete()
    {
        $children_ids_arr = $this->getChildrenIdsArr();

        foreach ($children_ids_arr as $children_site_menu_item_id) {
            $children_site_menu_item_obj = self::factory($children_site_menu_item_id);
            $children_site_menu_item_obj->delete();
        }

        self::removeObjFromCacheById($this->getId());

        self::removeObjFromCacheById($this->getParentId());
    }
}
