<?php

namespace WebSK\Skif\RichTextEditor;

use WebSK\Config\ConfWrapper;
use WebSK\Utils\Assert;
use WebSK\Views\PhpRender;

class RichTextEditor
{
    protected static function checkFilesDirectories(string $directory): void
    {
        if ($directory === '') {
            return;
        }

        $files_data_path = ConfWrapper::value('files_data_path');
        Assert::assert($files_data_path);

        $directories = [
            $files_data_path,
            $files_data_path . DIRECTORY_SEPARATOR . $directory,
            $files_data_path . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . 'images',
        ];

        foreach ($directories as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    public static function create(
        string $editor_name,
        string $text,
        int $height = 300,
        string $directory = ''
    ): string {
        self::checkFilesDirectories($directory);

        return PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/RichTextEditor',
            'rich_text_editor.tpl.php',
            [
                'editor_name' => $editor_name,
                'text' => $text,
                'directory' => $directory,
                'height' => $height,
            ]
        );
    }
}
