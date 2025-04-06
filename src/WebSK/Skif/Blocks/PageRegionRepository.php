<?php

namespace WebSK\Skif\Blocks;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

class PageRegionRepository extends EntityRepository
{
    /**
     * @param string $name
     * @param int $template_id
     * @return bool|false|mixed
     * @throws \Exception
     */
    public function findIdByNameAndTemplateId(string $name, int $template_id): int
    {
        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " . Sanitize::sanitizeSqlColumnName(PageRegion::_NAME)
            . "=? AND " . Sanitize::sanitizeSqlColumnName(PageRegion::_TEMPLATE_ID) . " =?";

        return (int)$this->db_service->readField($query, array($name, $template_id));
    }

    /**
     * Массив PageRegionId для темы
     * @param int $template_id
     * @return array
     */
    public function findIdsArrByTemplateId(int $template_id): array
    {
        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " . Sanitize::sanitizeSqlColumnName(PageRegion::_TEMPLATE_ID) . " = ?";

        return $this->db_service->readColumn($query, [$template_id]);
    }
}