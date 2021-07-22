<?php

namespace WebSK\Skif\Poll;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class PollQuestionRepository
 * @package WebSK\Skif\Poll
 */
class PollQuestionRepository extends EntityRepository
{
    /**
     * @param int $poll_id
     * @return array
     */
    public function findIdsByPollId(int $poll_id): array
    {
        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($this->getIdFieldName()) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName()) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(PollQuestion::_POLL_ID) . ' = ?';

        return $this->db_service->readColumn($query, [$poll_id]);
    }
}
