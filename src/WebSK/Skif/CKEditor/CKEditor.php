<?php

namespace WebSK\Skif\CKEditor;

use WebSK\Config\ConfWrapper;
use WebSK\Utils\Assert;
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
     * @param string $dir
     */
    protected static function checkFilesDirectories(string $dir)
    {
        if (!$dir) {
            return;
        }

        $files_data_path = ConfWrapper::value('files_data_path');
        if (!$files_data_path) {
            $files_data_path = ConfWrapper::value('site_full_path') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files';
        }

        Assert::assert($files_data_path);

        if (!file_exists($files_data_path . DIRECTORY_SEPARATOR . $dir)) {
            mkdir($files_data_path . DIRECTORY_SEPARATOR . $dir, 0755);
        }

        if (!file_exists($files_data_path . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'images')) {
            mkdir($files_data_path . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'images', 0755);
        }
    }

    /**
     * @param string $editor_name
     * @param string $text
     * @param int $height
     * @param string $dir
     * @return string
     */
    public static function createFullCKEditor(string $editor_name, string $text, int $height = 300, string $dir = '')
    {
        self::checkFilesDirectories($dir);

        return  PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/CKEditor',
            'full.tpl.php',
            [
                'editor_name' => $editor_name,
                'text' => $text,
                'dir' => $dir,
                'height' => $height
            ]
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
        self::checkFilesDirectories($dir);

        return  PhpRender::renderTemplateForModuleNamespace(
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
        self::checkFilesDirectories($dir);

        return  PhpRender::renderTemplateForModuleNamespace(
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
