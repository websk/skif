<?php

namespace WebSK\Skif\Poll;

use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;

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
    public function beforeSave(InterfaceEntity $entity_obj)
    {
        if (!$entity_obj->getPublishedAt()) {
            $entity_obj->setPublishedAt(date('Y-m-d H:i:s'));
        }

        parent::beforeSave($entity_obj);
    }

    /**
     * @return int|null
     */
    public function getDefaultPollId()
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
