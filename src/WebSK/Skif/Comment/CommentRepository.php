<?php

namespace WebSK\Skif\Comment;

use OLOG\Sanitize;
use WebSK\Entity\EntityRepository;

/**
 * Class CommentRepository
 * @package WebSK\Skif\Comment
 */
class CommentRepository extends EntityRepository
{
    /**
     * @param int $parent_id
     * @return array
     */
    public function findIdsByParentId(int $parent_id)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) . ' 
            FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) . '
            WHERE ' . Sanitize::sanitizeSqlColumnName(Comment::_PARENT_ID) . ' = ?';

        $ids_arr = $this->db_service->readColumn($query, [$parent_id]);

        return $ids_arr;
    }
}
