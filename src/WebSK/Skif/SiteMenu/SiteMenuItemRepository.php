<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Entity\WeightRepository;
use WebSK\Utils\Sanitize;

/**
 * Class SiteMenuItemRepository
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuItemRepository extends WeightRepository
{

    /**
     * @param int $site_menu_id
     * @param null|int $parent_id
     * @return array
     */
    public function findIdsArrBySiteMenuId(int $site_menu_id, ?int $parent_id = null): array
    {
        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(SiteMenuItem::_MENU_ID) . '=?';

        if ($parent_id) {
            $query .= ' AND ' . Sanitize::sanitizeSqlColumnName(SiteMenuItem::_PARENT_ID) . '=?';
        }

        $query .= ' ORDER BY ' . Sanitize::sanitizeSqlColumnName(SiteMenuItem::_WEIGHT);

        return $this->db_service->readColumn($query, [$site_menu_id, $parent_id]);
    }

    /**
     * @param string $url
     * @return int|null
     */
    public function findIdByUrl(string $url): ?int
    {
        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . ' WHERE url=?'
            . ' LIMIT 1';

        $id = $this->db_service->readField($query, [$url]);

        return ($id !== false) ? $id : null;
    }
}