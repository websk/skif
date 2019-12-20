<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class ContentRubricRepository
 * @package WebSK\Skif\Content
 */
class ContentRubricRepository extends EntityRepository
{

    /**
     * @param int $rubric_id
     * @return array
     */
    public function findIdsByRubricId(int $rubric_id)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            " FROM " . Sanitize::sanitizeSqlColumnName($db_table_name) .
            " WHERE " . Sanitize::sanitizeSqlColumnName(ContentRubric::_RUBRIC_ID) . " = ?";

        $rubric_ids_arr =  $this->db_service->readColumn(
            $query,
            [$rubric_id]
        );

        return $rubric_ids_arr;
    }

    /**
     * @param int $rubric_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function findContentIdsByRubricId(int $rubric_id, int $limit_to_page = 0, int $page = 1)
    {
        $db_table_name = $this->getTableName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName(ContentRubric::_CONTENT_ID) . ' 
            FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) . '
            WHERE ' . Sanitize::sanitizeSqlColumnName(ContentRubric::_RUBRIC_ID) . ' = ?';

        $param_arr = [$rubric_id];

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        return $this->db_service->readColumn($query, $param_arr);
    }

    /**
     * @param int $rubric_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function findPublishedContentIdsArrByRubricId(int $rubric_id, int $limit_to_page = 0, int $page = 1)
    {
        $date = date('Y-m-d');

        $db_table_name = $this->getTableName();

        $query = "SELECT cr.content_id FROM " . $db_table_name . " cr
                JOIN " . Sanitize::sanitizeSqlColumnName(Content::DB_TABLE_NAME) . " c ON (c.id=cr.content_id)
                WHERE cr.rubric_id=?
                  AND c.is_published=1
                  AND (c.published_at<=?)
                  AND (c.unpublished_at>=? OR c.unpublished_at IS NULL)
                GROUP BY cr.content_id
                ORDER BY c.created_at DESC";

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        $contents_ids_arr = $this->db_service->readColumn($query, [$rubric_id, $date, $date]);

        return $contents_ids_arr;
    }
}
