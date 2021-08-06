<?php

namespace WebSK\Skif\Redirect;

use WebSK\Auth\Auth;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Utils\FullObjectId;

/**
 * Class RedirectService
 * @method Redirect getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Redirect
 */
class RedirectService extends EntityService
{
    const REGEXP_IDS_CACHE_KEY = 'regexp_redirect_std_obj_arr';
    const REGEXP_IDS_CACHE_SEC = 3600;


    /** @var RedirectRepository */
    protected $repository;

    /**
     * @param string $src
     * @param int $kind
     * @return array
     */
    public function getIdsArrBySrcAndKind(string $src, int $kind): array
    {
        return $this->repository->findIdsBySrcAndKind($src, $kind);
    }

    /**
     * @return array
     */
    public function getRegexpIdsArr(): array
    {
        $cache_key = self::REGEXP_IDS_CACHE_KEY;

        $cache = $this->cache_service->get($cache_key);

        if ($cache !== false) {
            return $cache;
        }

        $ids_arr = $this->repository->findIdsByKind(Redirect::REDIRECT_KIND_REGEXP);

        $this->cache_service->set($cache_key, $ids_arr, self::REGEXP_IDS_CACHE_SEC);

        return $ids_arr;
    }

    /**
     * @param Redirect|InterfaceEntity $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj)
    {
        if ($entity_obj->getKind() == Redirect::REDIRECT_KIND_REGEXP) {
            $cache_key = self::REGEXP_IDS_CACHE_KEY;
            $this->cache_service->delete($cache_key);
        }

        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param Redirect|InterfaceEntity $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj)
    {
        if ($entity_obj->getKind() == Redirect::REDIRECT_KIND_REGEXP) {
            $cache_key = self::REGEXP_IDS_CACHE_KEY;
            $this->cache_service->delete($cache_key);
        }

        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
