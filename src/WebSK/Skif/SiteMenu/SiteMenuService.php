<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;

/**
 * Class SiteMenuService
 * @method SiteMenu getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuService extends EntityService
{
    /**
     * @param InterfaceEntity|SiteMenu $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj)
    {
        $url = $entity_obj->getUrl();
        if ($url) {
            $entity_obj->setUrl('/' . ltrim($url, '/'));
        }

        parent::beforeSave($entity_obj);
    }
}