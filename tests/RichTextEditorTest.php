<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\TestCase;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\CKEditor\CKEditor;
use WebSK\Skif\RichTextEditor\RichTextEditor;

final class RichTextEditorTest extends TestCase
{
    private string $filesPath;

    protected function setUp(): void
    {
        $config = require dirname(__DIR__) . '/config/config.default.php';
        $this->filesPath = sys_get_temp_dir() . '/skif-rich-text-editor-' . bin2hex(random_bytes(8));
        $config['settings']['files_data_path'] = $this->filesPath;
        ConfWrapper::setConfig($config['settings']);
    }

    protected function tearDown(): void
    {
        @rmdir($this->filesPath . '/content/images');
        @rmdir($this->filesPath . '/content');
        @rmdir($this->filesPath);
    }

    public function testRendersJoditConfigurationAndEscapesTextareaBoundary(): void
    {
        $html = RichTextEditor::create(
            'body',
            '<p>Text</p></textarea><script>alert(1)</script>',
            420,
            'content'
        );

        self::assertStringContainsString('SkifRichTextEditor.init({', $html);
        self::assertStringContainsString('elementId: "body"', $html);
        self::assertStringContainsString('height: 420', $html);
        self::assertStringContainsString('directory: "content"', $html);
        self::assertStringContainsString('&lt;/textarea&gt;&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        self::assertStringNotContainsString('</textarea><script>', $html);
    }

    public function testLegacyCkeditorEntryPointUsesJoditRenderer(): void
    {
        $html = CKEditor::createCKEditor('annotation', '<p>Text</p>');

        self::assertStringContainsString('SkifRichTextEditor.init({', $html);
        self::assertStringContainsString('elementId: "annotation"', $html);
    }
}
