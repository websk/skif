<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class SiteMenuItemRepository
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuItemRepository extends EntityRepository
{

    /**
     * @param int $site_menu_id
     * @param int $parent_id
     * @return array
     */
    public function findIdsArrBySiteMenuId(int $site_menu_id, int $parent_id = 0): array
    {
        $query = 'SELECT ' . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(SiteMenuItem::_MENU_ID) . '=?'
            . ' AND ' . Sanitize::sanitizeSqlColumnName(SiteMenuItem::_PARENT_ID) . '=?'
            . ' ORDER BY weight';

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