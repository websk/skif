<?php

namespace WebSK\Skif\Form;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class FormRepository
 * @package WebSK\Skif\Form
 */
class FormRepository extends EntityRepository
{
    /**
     * @param string $url
     * @return ?int
     */
    public function findIdByUrl(string $url): ?int
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(Form::_URL) . ' = ?';

        $id = $this->db_service->readField($query, [$url]);

        if ($id === false) {
            $id = null;
        }

        return $id;
    }
}
