<?php

namespace WebSK\Skif\Comment;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

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
    public function findIdsByParentId(int $parent_id): array
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(Comment::_PARENT_ID) . ' = ?';

        return $this->db_service->readColumn($query, [$parent_id]);
    }

    /**
     * @param string $url
     * @param int $offset
     * @param int $page_size
     * @return array
     */
    public function findIdsByUrl(string $url, int $offset = 0, int $page_size = 20): array
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(Comment::_URL_MD5) . ' = ?' .
            ' AND ' . Sanitize::sanitizeSqlColumnName(Comment::_PARENT_ID) . ' IS NULL' .
            ' ORDER BY ' . Sanitize::sanitizeSqlColumnName(Comment::_ID) . ' DESC' .
            ' LIMIT ' . intval($page_size) . ' OFFSET ' . intval($offset);

        return $this->db_service->readColumn($query, [md5($url)]);
    }

    /**
     * @param string $url
     * @return int
     */
    public function findCountCommentsByUrl(string $url): int
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT COUNT(' . Sanitize::sanitizeSqlColumnName($db_id_field_name) . ')' .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE url_md5=? AND parent_id IS NULL';

        $count = $this->db_service->readField($query, [md5($url)]);

        if ($count === false) {
            return 0;
        }

        return $count;
    }
}
