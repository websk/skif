<?php

namespace WebSK\Skif\KeyValue;

use OLOG\Sanitize;
use WebSK\Entity\BaseEntityRepository;

/**
 * Class KeyValueRepository
 * @package WebSK\Skif\KeyValue
 */
class KeyValueRepository extends BaseEntityRepository
{
    /**
     * @param string $key
     * @return int|null
     */
    public function findIdByKey(string $key): ?int
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        return $this->db_service->readField(
            'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name)
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name)
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(KeyValue::_NAME) . '=?',
            [$key]
        );
    }
}
