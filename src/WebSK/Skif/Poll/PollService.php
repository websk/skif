<?php

namespace WebSK\Skif\Poll;

use WebSK\Auth\Auth;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Utils\FullObjectId;

/**
 * Class PollService
 * @method Poll getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Poll
 */
class PollService extends EntityService
{

    /**
     * @param Poll|InterfaceEntity $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj): void
    {
        if (!$entity_obj->getPublishedAt()) {
            $entity_obj->setPublishedAt(date('Y-m-d H:i:s'));
        }

        parent::beforeSave($entity_obj);
    }

    /**
     * @param InterfaceEntity|Poll $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|Poll $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @return int|null
     */
    public function getDefaultPollId(): ?int
    {
        $poll_ids_arr = $this->getAllIdsArrByIdAsc();

        foreach ($poll_ids_arr as $poll_id) {
            $poll_obj = $this->getById($poll_id);

            if (!$poll_obj->isPublished()) {
                continue;
            }

            if (!$poll_obj->isIsDefault()) {
                continue;
            }

            return $poll_id;
        }

        foreach ($poll_ids_arr as $poll_id) {
            $poll_obj = $this->getById($poll_id);

            if (!$poll_obj->isPublished()) {
                continue;
            }

            return $poll_id;
        }

        return null;
    }
}
