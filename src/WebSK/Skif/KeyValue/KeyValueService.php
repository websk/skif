<?php

namespace WebSK\Skif\KeyValue;

use OLOG\FullObjectId;
use VitrinaTV\Core\Auth\Auth;
use WebSK\Entity\BaseEntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Skif\Logger\Logger;

/**
 * Class KeyValueService
 * @method KeyValue getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\KeyValue
 */
class KeyValueService extends BaseEntityService
{
    const OPTIONAL_VALUES_CACHE_TTL_SEC = 60;

    /** @var KeyValueRepository */
    protected $repository;

    /**
     * @param string $key
     * @param string $default_value
     * @return string
     */
    public function getOptionalValueForKey(string $key, string $default_value = ''): string
    {
        $cache_key = $this->getOptionalValueForKeyCacheKey($key);

        $cached = $this->cache_service->get($cache_key);
        if ($cached !== false) {
            return $cached;
        }

        $key_value_id = $this->repository->findIdByKey($key);
        if (!$key_value_id) {
            return $default_value;
        }

        $key_value_obj = $this->getById($key_value_id);
        $optional_value = $key_value_obj->getValue();

        $this->cache_service->set($cache_key, $optional_value, self::OPTIONAL_VALUES_CACHE_TTL_SEC);

        return $optional_value;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getOptionalValueForKeyCacheKey(string $key)
    {
        return 'optional_value_for_key_' .  $key;
    }

    /**
     * @param KeyValue|InterfaceEntity $key_value_obj
     */
    public function beforeSave(InterfaceEntity $key_value_obj)
    {
        // Сбрасываем кеш перед сохранением объекта для старого значения имени ключа (если вдруг имя ключа поменяется)
        $existing_key_value_obj = $this->getById($key_value_obj->getId());

        $cache_key = $this->getOptionalValueForKeyCacheKey($existing_key_value_obj->getName());
        $this->cache_service->delete($cache_key);
    }

    /**
     * @param KeyValue|InterfaceEntity $key_value_obj
     */
    public function afterSave(InterfaceEntity $key_value_obj)
    {
        parent::afterSave($key_value_obj);

        $cache_key = $this->getOptionalValueForKeyCacheKey($key_value_obj->getName());
        $this->cache_service->delete($cache_key);

        Logger::logObjectEvent(
            $key_value_obj,
            'save',
            FullObjectId::getFullObjectId(Auth::getCurrentUserObj())
        );
    }

    /**
     * @param KeyValue|InterfaceEntity $key_value_obj
     */
    public function afterDelete(InterfaceEntity $key_value_obj)
    {
        parent::afterDelete($key_value_obj);

        $cache_key = $this->getOptionalValueForKeyCacheKey($key_value_obj->getName());
        $this->cache_service->delete($cache_key);

        Logger::logObjectEvent(
            $key_value_obj,
            'delete',
            FullObjectId::getFullObjectId(Auth::getCurrentUserObj())
        );
    }
}
