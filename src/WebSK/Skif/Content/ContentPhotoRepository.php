<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class ContentPhotoRepository
 * @package WebSK\Skif\Content
 */
class ContentPhotoRepository extends EntityRepository
{
    /**
     * @param int $content_id
     * @return array|null
     */
    public function findIdsByContentId(int $content_id): ?array
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $ids_arr = $this->db_service->readColumn(
            'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name)
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name)
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(ContentPhoto::_CONTENT_ID) . '=?',
            [$content_id]
        );

        return $ids_arr;
    }
}
