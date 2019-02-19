<?php

namespace WebSK\Auth;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

/**
 * Class SessionsRepository
 * @package WebSK\Auth
 */
class SessionsRepository extends EntityRepository
{

    /**
     * @param $session
     * @throws \Exception
     */
    public function deleteBySession($session)
    {
        $query = "DELETE FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName()) . " WHERE session=?";
        $this->db_service->query($query, [$session]);
    }

    /**
     * Удаляем просроченные сессии
     * @param int $user_id
     * @param int $delta
     * @throws \Exception
     */
    public function clearOldSessionsByUserId(int $user_id, int $delta)
    {
        $query = "DELETE FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . " WHERE user_id=? AND timestamp<=?";
        $this->db_service->query($query, [$user_id, $delta]);
    }
}
