<?php

namespace WebSK\Skif\Redirect;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class RedirectRepository
 * @package WebSK\Skif\Redirect
 */
class RedirectRepository extends EntityRepository
{
    /**
     * @param string $src
     * @param int $kind
     * @return array
     */
    public function findIdsBySrcAndKind(string $src, int $kind)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(Redirect::_SRC) . ' = ?' .
            ' AND ' . Sanitize::sanitizeSqlColumnName(Redirect::_KIND) . ' = ?' .
            ' ORDER BY ' . Sanitize::sanitizeSqlColumnName($db_id_field_name);

        $ids_arr = $this->db_service->readColumn($query, [$src, $kind]);

        return $ids_arr;
    }

    /**
     * @param int $kind
     * @return array
     */
    public function findIdsByKind(int $kind)
    {
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(Redirect::_KIND) . ' = ?' .
            ' ORDER BY ' . Sanitize::sanitizeSqlColumnName($db_id_field_name);

        $ids_arr = $this->db_service->readColumn($query, [$kind]);

        return $ids_arr;
    }
}
