<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Auth\Auth;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Skif\Logger;
use WebSK\Utils\FullObjectId;

/**
 * Class SiteMenuService
 * @method ($exception_if_not_loaded is true ? SiteMenu : SiteMenu|null) getById(?int $entity_id, bool $exception_if_not_loaded = true)
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuService extends EntityService
{
    /**
     * @param InterfaceEntity|SiteMenu $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj): void
    {
        if (!$entity_obj instanceof SiteMenu) {
            throw new \InvalidArgumentException('SiteMenuService can save only SiteMenu entities');
        }

        $url = $entity_obj->getUrl();
        if ($url) {
            $entity_obj->setUrl('/' . ltrim($url, '/'));
        }

        parent::beforeSave($entity_obj);
    }

    /**
     * @param InterfaceEntity|SiteMenu $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|SiteMenu $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
