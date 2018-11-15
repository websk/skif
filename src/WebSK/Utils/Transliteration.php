<?php

namespace WebSK\Utils;

/**
 * Class Translit
 * @package Skif
 */
class Transliteration
{

    /**
     * Транслитерация строки
     * @param string $cyrillic_string
     * @param bool $remove_stop_words
     * @return mixed|null|string|string[]
     */
    public static function transliteration(string $cyrillic_string, $remove_stop_words = true)
    {
        $cyrillic_string = mb_strtolower($cyrillic_string, "UTF-8");

        if ($remove_stop_words) {
            $remove_arr = [
                '|\bв\b|i',
                '|\bбез\b|i',
                '|\bбы\b|i',
                '|\bдо\b|i',
                '|\bиз\b|i',
                '|\bк\b|i',
                '|\bна\b|i',
                '|\bпо\b|i',
                '|\bо\b|i',
                '|\bот\b|i',
                '|\bто\b|i',
                '|\bперед\b|i',
                '|\bпри\b|i',
                '|\bчерез\b|i',
                '|\bс\b|i',
                '|\bу\b|i',
                '|\bза\b|i',
                '|\bнад\b|i',
                '|\bоб\b|i',
                '|\bпод\b|i',
                '|\bпро\b|i',
                '|\bдля\b|i',
                '|\bне\b|i',
                '|\bтак\b|i',
                '|\bкак\b|i',
                '|\bи\b|i',
                '|\bа\b|i',
                '|\bчто\b|i',

                '|\ba\b|i',
                '|\ban\b|i',
                '|\bas\b|i',
                '|\bat\b|i',
                '|\bbefore\b|i',
                '|\bbut\b|i',
                '|\bby\b|i',
                '|\bfor\b|i',
                '|\bfrom\b|i',
                '|\bis\b|i',
                '|\bin\b|i',
                '|\binto\b|i',
                '|\blike\b|i',
                '|\bof\b|i',
                '|\boff\b|i',
                '|\bon\b|i',
                '|\bonto\b|i',
                '|\bper\b|i',
                '|\bsince\b|i',
                '|\bthan\b|i',
                '|\bthe\b|i',
                '|\bthis\b|i',
                '|\bthat\b|i',
                '|\bto\b|i',
                '|\bup\b|i',
                '|\bvia\b|i',
                '|\bwith\b|i',
            ];

            $cyrillic_string = preg_replace($remove_arr, '', $cyrillic_string);
        }

        $replace_arr = [
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'y',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'shch',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',

            '№' => 'no',
            '–' => '-'
        ];

        $transliteration_str = str_replace(
            array_keys($replace_arr),
            $replace_arr,
            $cyrillic_string
        );

        $transliteration_str = preg_replace('|[^-a-z0-9\s]|', '', $transliteration_str);
        $transliteration_str = preg_replace('|\s+|', '-', $transliteration_str);
        $transliteration_str = preg_replace('|-+|', '-', $transliteration_str);
        $transliteration_str = trim($transliteration_str, '-');

        return $transliteration_str;
    }

    /**
     * Проверка на русские символы в строке
     * @param $text
     * @return int
     */
    public static function checkRussian($text)
    {
        $text = str_replace("\n", "", $text);
        $text = str_replace("\r", "", $text);
        $text = str_replace(",", "", $text);
        $text = str_replace(".", "", $text);
        $text = str_replace("!", "", $text);
        $text = str_replace("?", "", $text);
        $text = str_replace(";", "", $text);
        $text = str_replace(":", "", $text);
        $text = str_replace(")", "", $text);
        $text = str_replace("(", "", $text);
        $text = str_replace("-", "", $text);
        $text = str_replace(" ", "", $text);

        $patern = "|^[-а-я]+$|i";

        if (preg_match($patern, $text)) {
            return true;
        }

        return false;
    }
}
