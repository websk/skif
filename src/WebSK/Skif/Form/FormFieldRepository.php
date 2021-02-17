<?php

namespace WebSK\Skif\Form;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class FormFieldRepository
 * @package WebSK\Skif\Form
 */
class FormFieldRepository extends EntityRepository
{
    /**
     * @param int $form_id
     * @return array
     */
    public function findIdsByFormId(int $form_id): array
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(FormField::_FORM_ID) . ' = ?';

        return $this->db_service->readColumn($query, [$form_id]);
    }
}
