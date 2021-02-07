<?php

namespace WebSK\Skif;

/**
 * Class Pager
 * @package WebSK\Skif
 */
class Pager
{
    public static function getPageOffset()
    {
        $page_offset = 0;
        if (array_key_exists('page_offset', $_GET)) {
            $page_offset = intval($_GET['page_offset']);
            if ($page_offset < 0) {
                $page_offset = 0;
            }
        }

        return $page_offset;
    }

    public static function getPageNumber()
    {
        $page_number = 1;
        if (array_key_exists('page_number', $_GET)) {
            $page_number = intval($_GET['page_number']);
            if ($page_number < 1) {
                return 1;
            }
        }

        return $page_number;
    }

    public static function getPageSize($default_page_size = 30)
    {
        $page_size = $default_page_size;
        if (array_key_exists('page_size', $_GET)) {
            $page_size = intval($_GET['page_size']);
            if ($page_size < 1) {
                return $default_page_size;
            }
            if ($page_size > 1000) {
                return $default_page_size;
            }
        }

        return $page_size;
    }

    public static function getNextPageStart()
    {
        $start = self::getPageOffset();
        $page_size = self::getPageSize();
        return $start + $page_size;
    }

    public static function getPrevPageStart()
    {
        $start = self::getPageOffset();
        $page_size = self::getPageSize();
        return $start - $page_size;
    }

    public static function hasPrevPage()
    {
        $start = self::getPageOffset();

        if ($start > 0) {
            return true;
        }

        return false;
    }

    /**
     * "Дальше" рисуется всегда, если параметр $elements_count не передан
     *
     * @param int $elements_count
     * @return string
     */
    public static function renderPager($elements_count = null)
    {
        $pager_needed = false;
        if (self::hasPrevPage()) {
            $pager_needed = true;
        }

        if (is_null($elements_count) || self::hasNextPage($elements_count)) {
            $pager_needed = true;
        }

        if (!$pager_needed) {
            return '';
        }

        $html = "<ul class='pagination'>";

        $page_url = $_SERVER['REQUEST_URI'];

        $page_url = str_replace('?page_offset=' . self::getPageOffset(), '', $page_url);
        $page_url = str_replace('&page_offset=' . self::getPageOffset(), '', $page_url);
        $page_url = str_replace('&page_size=' . self::getPageSize(), '', $page_url);

        if (strpos($page_url, '?') === false) {
            $page_url .= '?';
        } else {
            $page_url .= '&';
        }

        if (self::hasPrevPage()) {
            $html .= '<li><a href="' . $page_url . 'page_offset=0&page_size=' . self::getPageSize() . '"><span class="glyphicon glyphicon-home"></span> 0-' . self::getPageSize() . '</a></li>';
            $html .= '<li><a href="' . $page_url . 'page_offset=' . self::getPrevPageStart() . '&page_size=' . self::getPageSize() . '"><span class="glyphicon glyphicon-arrow-left"></span> ' . self::getPrevPageStart() . '-' . (self::getPrevPageStart() + self::getPageSize()) . '</a></li>';
        } else {
            $html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>';
            $html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-arrow-left"></span></a></li>';
        }

        $html .= "<li class='active'><a href='#'>" . self::getPageOffset() . '-' . (self::getPageOffset() + self::getPageSize()) . '</a></li>';

        if (!$elements_count || self::hasNextPage($elements_count)) {
            $html .= "<li><a class='next-page' href='" . $page_url . "page_offset=" . self::getNextPageStart() . "&page_size=" . self::getPageSize() . "'>" . self::getNextPageStart() . "-" . (self::getNextPageStart() + self::getPageSize()) . ' <span class="glyphicon glyphicon-arrow-right"></span></a></a></li>';
        } else {
            $html .= '<li class="disabled"><a href="#"><span class="glyphicon glyphicon-arrow-right"></span></a></li>';
        }

        $html .= "</ul>";

        return $html;
    }

    /**
     * @param $elements_count - Количество элементов на текущей странице.
     * Если меньше размера страницы - значит, следующей страницы нет.
     * Если null - значит оно не передано (т.е. неизвестно), при этом считаем что следующая страница есть.
     * @return bool
     */
    public static function hasNextPage($elements_count)
    {
        if (is_null($elements_count)) {
            return true;
        }

        $page_size = self::getPageSize();

        if ($elements_count < $page_size) {
            return false;
        }

        return true;
    }

    /**
     * @param $current_page
     * @param $count_records
     * @param int $messages_to_page
     * @return string
     */
    public static function renderPagination($current_page, $count_records, $messages_to_page = 10)
    {
        $url = $_SERVER['REQUEST_URI'];
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
