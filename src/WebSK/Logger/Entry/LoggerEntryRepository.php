<?php

namespace WebSK\Logger\Entry;

use OLOG\Sanitize;
use WebSK\Entity\BaseEntityRepository;

class LoggerEntryRepository extends BaseEntityRepository
{
    /**
     * @param int $current_entry_id
     * @param string $current_entry_full_id
     * @return int
     * @throws \Exception
     */
    public function getPrevRecordEntryId(int $current_entry_id, string $current_entry_full_id)
    {
        $prev_record_id = $this->db_service->readField(
            "SELECT " . LoggerEntry::_ID . " FROM " . LoggerEntry::DB_TABLE_NAME
            . " WHERE " . LoggerEntry::_ID . " < ? AND " . LoggerEntry::_OBJECT_FULLID . " = ? 
                ORDER BY id DESC LIMIT 1",
            [$current_entry_id, $current_entry_full_id]
        );

        return (int)$prev_record_id;
    }

    /**
     * @param $value
     * @param int $offset
     * @param int $page_size
     * @return array
     * @throws \Exception
     */
    public function getIdsArrForObjectFullIdByCreatedAtDesc($value, int $offset = 0, int $page_size = 30)
    {
        if (is_null($value)) {
            return $this->db_service->readColumn(
                'select ' . LoggerEntry::_ID . ' from ' . LoggerEntry::DB_TABLE_NAME
                . ' where ' . LoggerEntry::_OBJECT_FULLID . ' is null 
                    order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        }

        return $this->db_service->readColumn(
            'select ' . LoggerEntry::_ID . ' from ' . LoggerEntry::DB_TABLE_NAME
            . ' where ' . LoggerEntry::_OBJECT_FULLID . ' = ? 
                order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
            [$value]
        );
    }

    /**
     * @param $value
     * @param int $offset
     * @param int $page_size
     * @return array
     * @throws \Exception
     */
    public function getIdsArrForUserFullIdByCreatedAtDesc($value, $offset = 0, $page_size = 30)
    {
        if (is_null($value)) {
            return $this->db_service->readColumn(
                'select ' . LoggerEntry::_ID . ' from ' . LoggerEntry::DB_TABLE_NAME
                . ' where ' . LoggerEntry::_USER_FULLID . ' is null 
                    order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        }

        return $this->db_service->readColumn(
            'select ' . LoggerEntry::_ID . ' from ' . LoggerEntry::DB_TABLE_NAME
            . ' where ' . LoggerEntry::_USER_FULLID . ' = ? 
                order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
            [$value]
        );
    }

    /**
     * @param \DateTime $min_created_datetime
     * @param int $limit
     */
    public function removePastEntries(\DateTime $min_created_datetime, int $limit)
    {
        $db_table_name = $this->getTableName();

        $query = 'DELETE FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name)
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(LoggerEntry::_CREATED_AT_TS) . '<=?'
            . ' AND ' . Sanitize::sanitizeSqlColumnName(LoggerEntry::_USER_FULLID) . ' IS NULL'
            . ' LIMIT ' . abs($limit);

        $where_arr = [$min_created_datetime->format('U')];

        $this->db_service->query($query, $where_arr);
    }
}
