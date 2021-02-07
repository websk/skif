<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class ContentRepository
 * @package WebSK\Skif\Content
 */
class ContentRepository extends EntityRepository
{
    /**
     * @param string $url
     * @return false|int
     */
    public function findIdByUrl(string $url)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) . ' 
            FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) . '
            WHERE ' . Sanitize::sanitizeSqlColumnName(Content::_URL) . ' = ?';

        $id = $this->db_service->readField($query, [$url]);

        return $id;
    }

    /**
     * @param string $title
     * @return array
     */
    public function findIdsByTitle(string $title)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($db_id_field_name) . " 
            FROM " . Sanitize::sanitizeSqlColumnName($db_table_name) . "
            WHERE " . Sanitize::sanitizeSqlColumnName(Content::_TITLE) . " LIKE ? " . "
            LIMIT 20";

        $ids_arr = $this->db_service->readColumn($query, [$title . '%']);

        return $ids_arr;
    }

    /**
     * @param int $content_type_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function findIdsByContentTypeId(int $content_type_id, int $limit_to_page = 0, int $page = 1)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) . ' 
            FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) . '
            WHERE ' . Sanitize::sanitizeSqlColumnName(Content::_CONTENT_TYPE_ID) . ' = ?' .
            " ORDER BY " . Sanitize::sanitizeSqlColumnName(Content::_CREATED_AT_TS) . ' DESC';

        $param_arr = [$content_type_id];

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        return $this->db_service->readColumn($query, $param_arr);
    }

    /**
     * @param int $content_type_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function findPublishedIdsByContentTypeId(int $content_type_id, $limit_to_page = 0, $page = 1)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            " FROM " . Sanitize::sanitizeSqlColumnName($db_table_name) .
            " WHERE " . Sanitize::sanitizeSqlColumnName(Content::_CONTENT_TYPE_ID) . "=?
                AND " . Sanitize::sanitizeSqlColumnName(Content::_IS_PUBLISHED) . "=?
                AND (" . Sanitize::sanitizeSqlColumnName(Content::_PUBLISHED_AT) . "<=?)
                AND (" . Sanitize::sanitizeSqlColumnName(Content::_UNPUBLISHED_AT) . ">=? OR " . Sanitize::sanitizeSqlColumnName(Content::_UNPUBLISHED_AT) . " IS NULL)
            ORDER BY " . Sanitize::sanitizeSqlColumnName(Content::_CREATED_AT_TS) . " DESC";

        if ($limit_to_page) {
            $offset = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $offset . ', ' . $limit_to_page;
        }

        $date = date('Y-m-d');

        return $this->db_service->readColumn($query, [$content_type_id, true, $date, $date]);
    }
}
