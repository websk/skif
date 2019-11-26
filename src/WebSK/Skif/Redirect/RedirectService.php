<?php

namespace WebSK\Skif\Redirect;

use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;

/**
 * Class RedirectService
 * @method Redirect getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Redirect
 */
class RedirectService extends EntityService
{

    /**
     * @param Redirect|InterfaceEntity $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj)
    {
        if ($entity_obj->getKind() == Redirect::REDIRECT_KIND_REGEXP) {
            $cache_key = self::getCacheKeyRegexpRedirectArr();
            $this->cache_service->delete($cache_key);
        }

        parent::afterSave($entity_obj);
    }

    /**
     * @param Redirect|InterfaceEntity $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj)
    {
        if ($entity_obj->getKind() == Redirect::REDIRECT_KIND_REGEXP) {
            $cache_key = self::getCacheKeyRegexpRedirectArr();
            $this->cache_service->delete($cache_key);
        }

        parent::afterDelete($entity_obj);
    }

    /**
     * @return string
     */
    public static function getCacheKeyRegexpRedirectArr()
    {
        return "regexp_redirect_std_obj_arr";
    }
}
