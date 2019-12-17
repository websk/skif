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
    public function findIdsByPollId(int $poll_id)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(PollQuestion::_POLL_ID) . ' = ?';

        $ids_arr = $this->db_service->readColumn($query, [$poll_id]);

        return $ids_arr;
    }
}
