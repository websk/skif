<?php

namespace WebSK\Skif;

use WebSK\Slim\Request;

/**
 * Class Pager
 * @package WebSK\Skif
 */
class Pager
{
    const int PAGE_SIZE_DEFAULT_VALUE = 30;
    const int PAGE_SIZE_MAX_VALUE = 10000;

    const string PARAM_PAGE_SIZE = 'page_size';
    const string PARAM_PAGE_OFFSET = 'page_offset';
    const string PARAM_PAGE_NUMBER = 'page_number';

    /**
     * @return int
     */
    public static function getPageOffset(): int
    {
        $page_offset = (int)Request::getParam(self::PARAM_PAGE_OFFSET, 0);

        if ($page_offset < 0) {
            $page_offset = 0;
        }

        return $page_offset;
    }

    /**
     * @return int
     */
    public static function getPageNumber(): int
    {
        $page_number = (int)Request::getParam(self::PARAM_PAGE_NUMBER, 1);

        if ($page_number < 1) {
            return 1;
        }

        return $page_number;
    }

    /**
     * @param int $default_page_size
     * @return int
     */
    public static function getPageSize(int $default_page_size = self::PAGE_SIZE_DEFAULT_VALUE): int
    {
        $page_size = (int)Request::getParam(self::PARAM_PAGE_SIZE, $default_page_size);

        if ($page_size < 1) {
            return $default_page_size;
        }

        if ($page_size > self::PAGE_SIZE_MAX_VALUE) {
            return $default_page_size;
        }

        return $page_size;
    }

    /**
     * @return int
     */
    public static function getPrevPageStart(): int
    {
        $start = self::getPageOffset();
        $page_size = self::getPageSize();

        return $start - $page_size;
    }

    /**
     * @param int $current_page
     * @param int $count_records
     * @param int $messages_to_page
     * @return string
     */
    public static function renderPagination(int $current_page, int $count_records, int $messages_to_page = self::PAGE_SIZE_DEFAULT_VALUE): string
    {
        $url = $_SERVER['REQUEST_URI'] ?: '';
        $url = str_replace('&', '&amp;', $url);
        $url = str_replace('?p=' . $current_page, '', $url);
        $url = str_replace('&p=' . $current_page, '', $url);
        $url = str_replace('&amp;p=' . $current_page, '', $url);

        if (strpos($url, '?') === false) {
            $url .= '?p=';
        } else {
            $url .= '&amp;p=';
        }

        $all = intval($count_records / $messages_to_page);

        if ($messages_to_page > 1) {
            $all++;
        }

        if ($all < 1) {
            return '';
        }

        $html = '<ul class="pagination pagination-sm">';

        for ($i = 1; $i <= $all; $i++) {
            $html .= '<li ' . ($i == $current_page ? 'class="active"' : '') . '><a href="' . $url . $i . '">' . $i . '</a></li>';
        }

        $html .= '</ul>';

        return $html;
    }
}
