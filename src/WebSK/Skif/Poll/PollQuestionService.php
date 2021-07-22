<?php

namespace WebSK\Skif\Poll;

use WebSK\Entity\EntityService;

/**
 * Class PollQuestionService
 * @method PollQuestion getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Poll
 */
class PollQuestionService extends EntityService
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
    public function getSumVotesFromPollQuestionByPoll($poll_id): int
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
    public function getMaxVotesFromPollQuestionByPoll($poll_id): int
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
}
