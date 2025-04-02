<?php

namespace WebSK\Skif\Blocks;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

class BlockRepository extends EntityRepository
{
    /**
     * Массив Block Id в регионе
     * @param null|int $page_region_id
     * @param int $template_id
     * @return array
     */
    public function findBlockIdsArrByPageRegionId(?int $page_region_id, int $template_id): array
    {
        $params_arr = [];

        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName()) . " WHERE ";
        if ($page_region_id) {
            $query .= " " . Sanitize::sanitizeSqlColumnName(Block::_PAGE_REGION_ID) . " = ? ";
            $params_arr[] = $page_region_id;
        } else {
            $query .= " " . Sanitize::sanitizeSqlColumnName(Block::_PAGE_REGION_ID) . " is NULL ";
        }

        $query .= " AND " . Sanitize::sanitizeSqlColumnName(Block::_TEMPLATE_ID) . "=?"
            . " ORDER BY " . Sanitize::sanitizeSqlColumnName(Block::_WEIGHT) . ", " . Sanitize::sanitizeSqlColumnName(Block::_TITLE);
        $params_arr[] = $template_id;

        return $this->db_service->readColumn(
            $query,
            $params_arr
        );
    }

    /**
     * Массив Block Id в теме
     * @param int $template_id
     * @return array
     */
    public function findBlockIdsArrByTemplateId(int $template_id): array
    {
        $blocks_ids_arr = $this->db_service->readColumn(
            "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE " . Sanitize::sanitizeSqlColumnName(Block::_TEMPLATE_ID) . " = ? "
            . " ORDER BY " . Sanitize::sanitizeSqlColumnName(Block::_PAGE_REGION_ID)
            . ", " . Sanitize::sanitizeSqlColumnName(Block::_WEIGHT)
            . ", " . Sanitize::sanitizeSqlColumnName(Block::_TITLE),
            array($template_id)
        );

        return $blocks_ids_arr;
    }
}