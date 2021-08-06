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
    public function findIdsBySrcAndKind(string $src, int $kind): array
    {
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName()) .
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
    public function findIdsByKind(int $kind): array
    {
        $db_id_field_name = $this->getIdFieldName();

        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name) .
            ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName()) .
            ' WHERE ' . Sanitize::sanitizeSqlColumnName(Redirect::_KIND) . ' = ?' .
            ' ORDER BY ' . Sanitize::sanitizeSqlColumnName($db_id_field_name);

        return $this->db_service->readColumn($query, [$kind]);
    }
}
