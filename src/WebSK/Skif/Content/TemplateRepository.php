<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class TemplateRepository
 * @package WebSK\Skif\Content
 */
class TemplateRepository extends EntityRepository
{
    /**
     * @param string $name
     * @return false|int
     */
    public function findIdByName(string $name)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(Template::_NAME) . ' = ?';

        $id = $this->db_service->readField($query, [$name]);

        return $id;
    }
}
