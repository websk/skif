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
    public function getIdsArrByPollId(int $poll_id)
    {
        return $this->repository->findIdsByPollId($poll_id);
    }
}
