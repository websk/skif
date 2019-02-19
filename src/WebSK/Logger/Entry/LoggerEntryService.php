<?php

namespace WebSK\Logger\Entry;

use OLOG\FullObjectId;
use OLOG\IP;
use WebSK\Cache\CacheService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Auth\AuthService;

/**
 * Class EntryService
 * @package VitrinaTV\Logger\Entry
 * @method LoggerEntry getById($entity_id, $exception_if_not_loaded = true)
 */
class LoggerEntryService extends EntityService
{
    /** @var LoggerEntryRepository */
    protected $repository;

    /** @var AuthService */
    protected $auth_service;

    /**
     * LoggerEntryService constructor.
     * @param string $entity_class_name
     * @param EntityRepository $repository
     * @param CacheService $cache_service
     * @param AuthService $auth_service
     */
    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        AuthService $auth_service
    ) {
        $this->auth_service = $auth_service;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param int $current_entry_id
     * @return int
     * @throws \Exception
     */
    public function getPrevRecordEntryId(int $current_entry_id)
    {
        return $this->repository->getPrevRecordEntryId(
            $current_entry_id,
            $this->getById($current_entry_id)->getObjectFullId()
        );
    }

    /**
     * Сохранение объекта
     * Каждый сохраняемый объект запоминаем, если запрашивается сохранение с таким же object_full_id
     * то смотрим есть ли изменения по сравнению с уже сохраненным.
     * Если нет то не сохраняем. если есть то переписываем уже сохраненый
     * @param InterfaceEntity $object
     * @param string $object_full_id
     * @param string $comment
     * @param string|null $user_full_id
     * @throws \Exception
     */
    protected function logObjectAndId(
        InterfaceEntity $object,
        string $object_full_id,
        string $comment,
        ?string $user_full_id
    ) {
        $ip_address = IP::getClientIpRemoteAddr();

        /** @var LoggerEntry[] $saved_entries_arr */
        static $saved_entries_arr = [];

        if (!array_key_exists($object_full_id, $saved_entries_arr)) {
            $new_entry_obj = new LoggerEntry();
            $new_entry_obj->setUserIp($ip_address);
            $new_entry_obj->setUserFullid($user_full_id);
            $new_entry_obj->setObjectFullid($object_full_id);
            $new_entry_obj->setSerializedObject(serialize($object));
            $new_entry_obj->setComment($comment);

            $this->save($new_entry_obj);

            $saved_entries_arr[$object_full_id] = $new_entry_obj;
        } else {
            $saved_entry_obj = $saved_entries_arr[$object_full_id];
            $serialized_object = serialize($object);
            if ($serialized_object != $saved_entry_obj->getSerializedObject()) {
                $saved_entry_obj->setSerializedObject($serialized_object);

                $this->save($saved_entry_obj);

                $saved_entries_arr[$object_full_id] = $saved_entry_obj;
            }
        }
    }

    /**
     * @param InterfaceEntity $object
     * @param string $comment
     * @param null|string $user_full_id
     * @throws \Exception
     */
    public function logObjectEvent(InterfaceEntity $object, string $comment, ?string $user_full_id)
    {
        $this->logObjectAndId($object, FullObjectId::getFullObjectId($object), $comment, $user_full_id);
    }

    /**
     * @param InterfaceEntity $object
     * @param string $comment
     * @throws \Exception
     */
    public function logObjectEventForCurrentUser(InterfaceEntity $object, string $comment)
    {
        $this->logObjectAndId(
            $object,
            FullObjectId::getFullObjectId($object),
            $comment,
            FullObjectId::getFullObjectId($this->auth_service->getCurrentUserObj())
        );
    }

    /**
     * @param \DateTime $min_created_datetime
     * @param int $limit
     */
    public function removePastLoggerEntries(\DateTime $min_created_datetime, int $limit)
    {
        $this->repository->removePastEntries($min_created_datetime, $limit);
    }
}
