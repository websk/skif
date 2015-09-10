<?php

namespace Skif;

class Translit
{

    /**
     * Транслитерация строки
     * @param $cyrillic_string - кириллическая строка
     * @return string
     */
    public static function translit($cyrillic_string, $remove_stop_words = true)
    {
        $cyrillic_string = mb_strtolower($cyrillic_string, "UTF-8");

        if($remove_stop_words) {
            $remove_arr = array(
                '|\bв\b|i', '|\bбез\b|i', '|\bбы\b|i', '|\bдо\b|i', '|\bиз\b|i', '|\bк\b|i', '|\bна\b|i', '|\bпо\b|i', '|\bо\b|i', '|\bот\b|i', '|\bто\b|i',
                '|\bперед\b|i', '|\bпри\b|i', '|\bчерез\b|i', '|\bс\b|i', '|\bу\b|i', '|\bза\b|i', '|\bнад\b|i', '|\bоб\b|i', '|\bпод\b|i',
                '|\bпро\b|i', '|\bдля\b|i', '|\bне\b|i', '|\bтак\b|i', '|\bкак\b|i', '|\bи\b|i', '|\bа\b|i', '|\bчто\b|i',

                '|\ba\b|i', '|\ban\b|i', '|\bas\b|i', '|\bat\b|i', '|\bbefore\b|i', '|\bbut\b|i', '|\bby\b|i', '|\bfor\b|i', '|\bfrom\b|i',
                '|\bis\b|i', '|\bin\b|i', '|\binto\b|i', '|\blike\b|i', '|\bof\b|i', '|\boff\b|i', '|\bon\b|i', '|\bonto\b|i', '|\bper\b|i',
                '|\bsince\b|i', '|\bthan\b|i', '|\bthe\b|i', '|\bthis\b|i', '|\bthat\b|i', '|\bto\b|i', '|\bup\b|i', '|\bvia\b|i', '|\bwith\b|i',
            );

            $cyrillic_string = preg_replace($remove_arr, '', $cyrillic_string);
        }

        $replace_arr = array(
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
        );

        $translit_string = str_replace(
            array_keys($replace_arr),
            $replace_arr,
            $cyrillic_string
        );

        $translit_string = preg_replace('|[^-a-z0-9\s]|', '', $translit_string);
        $translit_string = preg_replace('|\s+|', '-', $translit_string);
        $translit_string = preg_replace('|-+|', '-', $translit_string);
        $translit_string = trim($translit_string, '-');
        return $translit_string;
    }

}
