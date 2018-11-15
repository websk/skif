<?php

namespace Skif;

/**
 * Class Utils
 * @package Skif
 */
class Utils
{
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

    /**
     * @param $search
     * @param $replace
     * @param $subject
     * @param int $count
     * @return null|string|string[]
     */
    public static function mbStrIreplace($search, $replace, $subject, $count = -1)
    {
        mb_internal_encoding('utf-8');

        $search = is_array($search) ? array_map(create_function('$s', 'return \'#\'. preg_quote($s) .\'#uis\';'),
            $search) : '#' . preg_quote($search) . '#uis';

        return preg_replace($search, $replace, $subject, $count);
    }

    /**
     * Проверка Email
     * @param string $email
     * @return bool
     */
    public static function checkEmail(string $email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function checkPlain(string $text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * @param array $files_arr
     * @return array
     */
    public static function rebuildFilesArray(array $files_arr)
    {
        $output_files_arr = array();
        foreach ($files_arr as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $output_files_arr[$key2][$key1] = $value2;
            }
        }

        return $output_files_arr;
    }

    /**
     * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
     * param  $number Integer Число на основе которого нужно сформировать окончание
     * param  $endingsArray  Array Массив слов или окончаний для чисел (1, 4, 5),
     *         например array('яблоко', 'яблока', 'яблок')
     * @param int $number
     * @param array $ending_array
     * @return string
     */
    public static function getDeclensionOfNumerals(int $number, array $ending_array)
    {
        $number = $number % 100;
        if ($number >= 11 && $number <= 19) {
            $ending = $ending_array[2];
        } else {
            $i = $number % 10;
            switch ($i) {
                case (1):
                    $ending = $ending_array[0];
                    break;
                case (2):
                case (3):
                case (4):
                    $ending = $ending_array[1];
                    break;
                default:
                    $ending = $ending_array[2];
            }
        }

        return $ending;
    }
}
