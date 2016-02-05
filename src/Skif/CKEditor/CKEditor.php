<?php

namespace Skif\CKEditor;

class CKEditor
{

    const CKEDITOR_FULL = 'full';
    const CKEDITOR_BASIC = 'basic';

    public static function createFullCKEditor($editor_name, $text, $height = 300, $dir = null)
    {
        return  \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CKEditor',
            'full.tpl.php',
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
        return  \Skif\PhpTemplate::renderTemplateByModule(
            'CKEditor',
            'basic.tpl.php',
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
        return  \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CKEditor',
            'user.tpl.php',
            array(
                'editor_name' => $editor_name,
                'text' => $text,
                'dir' => $dir,
                'height' => $height
            )
        );
    }
}