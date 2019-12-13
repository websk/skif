<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class ContentTypeRepository
 * @package WebSK\Skif\Content
 */
class ContentTypeRepository extends EntityRepository
{
    /**
     * @param string $type
     * @return false|string
     */
    public function findIdByType(string $type)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(ContentType::_TYPE) . ' = ?';

        $id = $this->db_service->readField($query, [$type]);

        return $id;
    }
}
