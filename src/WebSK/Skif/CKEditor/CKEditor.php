<?php

namespace WebSK\Skif\CKEditor;

use WebSK\Skif\RichTextEditor\RichTextEditor;

/**
 * Class CKEditor
 * @package WebSK\Skif\CKEditor
 * @deprecated Use RichTextEditor::create() instead.
 */
class CKEditor
{

    /**
     * @param string $editor_name
     * @param string $text
     * @param int $height
     * @param string $dir
     * @return string
     */
    public static function createCKEditor(string $editor_name, string $text, int $height = 300, string $dir = ''): string
    {
        return RichTextEditor::create($editor_name, $text, $height, $dir);
    }
}
