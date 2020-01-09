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
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function findIdsByRubricId(int $rubric_id, int $limit_to_page = 0, int $page = 1)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            " FROM " . Sanitize::sanitizeSqlColumnName($db_table_name) .
            " WHERE " . Sanitize::sanitizeSqlColumnName(ContentRubric::_RUBRIC_ID) . " = ?" .
            " ORDER BY " . Sanitize::sanitizeSqlColumnName(ContentRubric::_CREATED_AT_TS) . ' DESC';

        $param_arr = [$rubric_id];

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        $ids_arr =  $this->db_service->readColumn(
            $query,
            $param_arr
        );

        return $ids_arr;
    }

    /**
     * @param int $content_id
     * @return array
     */
    public function findIdsByContentId(int $content_id)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            " FROM " . Sanitize::sanitizeSqlColumnName($db_table_name) .
            " WHERE " . Sanitize::sanitizeSqlColumnName(ContentRubric::_CONTENT_ID) . " = ?";

        $ids_arr =  $this->db_service->readColumn(
            $query,
            [$content_id]
        );

        return $ids_arr;
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

        $query = "SELECT c." . Sanitize::sanitizeSqlColumnName(Content::_ID) .
            " FROM " . $db_table_name . " cr" .
            " JOIN " . Sanitize::sanitizeSqlColumnName(Content::DB_TABLE_NAME) . " c ON (c." . Sanitize::sanitizeSqlColumnName(Content::_ID) . " = cr." . Sanitize::sanitizeSqlColumnName(ContentRubric::_CONTENT_ID) . ")" .
            " WHERE cr." . Sanitize::sanitizeSqlColumnName(ContentRubric::_RUBRIC_ID) . "=?" .
                " AND c." . Sanitize::sanitizeSqlColumnName(Content::_IS_PUBLISHED) . "=1" .
                " AND c." . Sanitize::sanitizeSqlColumnName(Content::_PUBLISHED_AT) . "<=?" .
                " AND (c." . Sanitize::sanitizeSqlColumnName(Content::_UNPUBLISHED_AT) . ">=? OR c." . Sanitize::sanitizeSqlColumnName(Content::_UNPUBLISHED_AT) ." IS NULL)" .
            " GROUP BY cr." . Sanitize::sanitizeSqlColumnName(Content::_CREATED_AT_TS) .
            " ORDER BY c." . Sanitize::sanitizeSqlColumnName(Content::_CREATED_AT_TS) . " DESC";

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        $ids_arr = $this->db_service->readColumn($query, [$rubric_id, $date, $date]);

        return $ids_arr;
    }
}
