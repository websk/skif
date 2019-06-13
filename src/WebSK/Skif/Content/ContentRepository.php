<?php

namespace WebSK\Skif\Content;

use OLOG\Sanitize;
use WebSK\Entity\EntityRepository;

/**
 * Class ContentRepository
 * @package WebSK\Skif\Content
 */
class ContentRepository extends EntityRepository
{
    /**
     * @param string $alias
     * @return int
     */
    public function findIdByAlias(string $alias)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) . ' 
            FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) . '
            WHERE url = ?';

        $content_id = (int)$this->db_service->readField($query, [$alias]);

        return $content_id;
    }
}
