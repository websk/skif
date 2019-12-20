<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class RubricRepository
 * @package WebSK\Skif\Content
 */
class RubricRepository extends EntityRepository
{

    /**
     * @param int $content_type_id
     * @return array
     */
    public function findIdsByContentTypeId(int $content_type_id)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            " FROM " . Sanitize::sanitizeSqlColumnName($db_table_name) .
            " WHERE " . Sanitize::sanitizeSqlColumnName(Rubric::_CONTENT_TYPE_ID) . " = ?";

        $rubric_ids_arr =  $this->db_service->readColumn(
            $query,
            [$content_type_id]
        );

        return $rubric_ids_arr;
    }
}
