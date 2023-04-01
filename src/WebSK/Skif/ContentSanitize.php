<?php

namespace WebSK\Skif;

class ContentSanitize
{

    /**
     * Фильтрация html тегов
     * @param string $content
     * @return string
     */
    public static function sanitizeContent(string $content): string
    {
        $allowable_tags_arr = array(
            '<p>',
            '<b><strong><em><i>',
            '<span>',
            '<br>',
            '<div>',
            '<a>',
            '<img>',
            '<h1><h2><h3><h4>',
            '<table><tr><td><tbody><thead><th>',
            '<li><ul><ol>',
            '<script>',
            '<hr>',
            '<form><input><iframe>'
        );

        return strip_tags($content, implode('', $allowable_tags_arr));
    }
}