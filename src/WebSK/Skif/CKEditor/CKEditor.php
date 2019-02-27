<?php

namespace WebSK\Skif\CKEditor;

use WebSK\Views\PhpRender;

/**
 * Class CKEditor
 * @package WebSK\Skif\CKEditor
 */
class CKEditor
{
    const CKEDITOR_FULL = 'full';
    const CKEDITOR_BASIC = 'basic';

    /**
     * @param string $editor_name
     * @param string $text
     * @param int $height
     * @param string $dir
     * @return string
     */
    public static function createFullCKEditor(string $editor_name, string $text, int $height = 300, string $dir = '')
    {
        return  PhpRender::renderTemplateByModule(
            'WebSK/Skif/CKEditor',
            'full.tpl.php',
            array(
                'editor_name' => $editor_name,
                'text' => $text,
                'dir' => $dir,
                'height' => $height
            )
        );
    }

    /**
     * @param string $editor_name
     * @param string $text
     * @param int $height
     * @param string $dir
     * @return string
     */
    public static function createBasicCKEditor(string $editor_name, string $text, int $height = 300, string $dir = '')
    {
        return  PhpRender::renderTemplateByModule(
            'WebSK/Skif/CKEditor',
            'basic.tpl.php',
            array(
                'editor_name' => $editor_name,
                'text' => $text,
                'dir' => $dir,
                'height' => $height
            )
        );
    }

    /**
     * @param string $editor_name
     * @param string $text
     * @param int $height
     * @param string $dir
     * @return string
     */
    public static function createUserCKEditor(string $editor_name, string $text, int $height = 300, string $dir = '')
    {
        return  PhpRender::renderTemplateByModule(
            'WebSK/Skif/CKEditor',
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
