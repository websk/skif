<?php

namespace WebSK\Skif\Poll;

use WebSK\Auth\Auth;
use WebSK\Entity\InterfaceEntity;
use WebSK\Entity\WeightService;
use WebSK\Logger\Logger;
use WebSK\Utils\FullObjectId;

/**
 * Class PollQuestionService
 * @method PollQuestion getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Poll
 */
class PollQuestionService extends WeightService
{
    /** @var PollQuestionRepository */
    protected $repository;

    /**
     * @param int $poll_id
     * @return array
     */
    public function getIdsArrByPollId(int $poll_id): array
    {
        return $this->repository->findIdsByPollId($poll_id);
    }

    /**
     * @param int $poll_id
     * @return int
     */
    public function getSumVotesFromPollQuestionByPoll(int $poll_id): int
    {
        $poll_question_ids_arr = $this->getIdsArrByPollId($poll_id);

        $sum = 0;

        foreach ($poll_question_ids_arr as $poll_question_id) {
            $poll_question_obj = $this->getById($poll_question_id);

            $sum += $poll_question_obj->getVotes();
        }

        return $sum;
    }

    /**
     * @param int $poll_id
     * @return int
     */
    public function getMaxVotesFromPollQuestionByPoll(int $poll_id): int
    {
        $poll_question_ids_arr = $this->getIdsArrByPollId($poll_id);

        $max = 0;
        $votes_arr = array();

        foreach ($poll_question_ids_arr as $poll_question_id) {
            $poll_question_obj = $this->getById($poll_question_id);

            if (in_array($poll_question_obj->getVotes(), $votes_arr)) {
                return 0;
            }

            if ($poll_question_obj->getVotes() > $max) {
                $max = $poll_question_obj->getVotes();
                $votes_arr[] = $max;
            }
        }

        return $max;
    }

    /**
     * @param InterfaceEntity|PollQuestion $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj)
    {
        $this->initWeight(
            $entity_obj,
            [
                PollQuestion::_POLL_ID => $entity_obj->getPollId()
            ]
        );

        parent::beforeSave($entity_obj);
    }

    /**
     * @param InterfaceEntity|PollQuestion $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj)
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|PollQuestion $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj)
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
