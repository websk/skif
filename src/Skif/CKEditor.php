<?php

namespace Skif;

class CKEditor
{

    public static function createFullCKEditor($editor_name, $text, $height = 300, $dir = null)
    {
        return  \Skif\PhpTemplate::renderTemplate(
            'templates/ckeditor/full.tpl.php',
            array(
                'editor_name' => $editor_name,
                'text' => $text,
                'dir' => $dir,
                'height' => $height
            )
        );
    }

    public static function createBasicCKEditor($editor_name, $text, $height = 300, $dir = null)
    {
        return  \Skif\PhpTemplate::renderTemplate(
            'templates/ckeditor/basic.tpl.php',
            array(
                'editor_name' => $editor_name,
                'text' => $text,
                'dir' => $dir,
                'height' => $height
            )
        );
    }

    public static function createUserCKEditor($editor_name, $text, $height = 300, $dir = null)
    {
        return  \Skif\PhpTemplate::renderTemplate(
            'templates/ckeditor/user.tpl.php',
            array(
                'editor_name' => $editor_name,
                'text' => $text,
                'dir' => $dir,
                'height' => $height
            )
        );
    }
}