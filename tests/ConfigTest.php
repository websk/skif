<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    public function testExampleConfigMatchesDockerApplicationLayout(): void
    {
        $config = require dirname(__DIR__) . '/config/config.example.php';
        $settings = $config['settings'];

        self::assertSame('/', $settings['skif']['url_path']);
        self::assertSame('memcached', $settings['cache']['servers'][0]['host']);
        self::assertSame('https://skif.devbox', $settings['site_domain']);
        self::assertSame('/var/www/public/static', $settings['static_data_path']);
        self::assertSame($settings['files_data_path'], $settings['storages']['files']['root_path']);
        self::assertArrayHasKey('rich_text_editor', $settings);
        self::assertSame([], $settings['rich_text_editor']['styles']);

        foreach ($settings['layout'] as $layout) {
            self::assertFileExists(dirname(__DIR__) . substr($layout, strlen('/var/www')));
        }
    }

    public function testDefaultConfigCanBeLoadedMoreThanOnce(): void
    {
        require dirname(__DIR__) . '/config/config.default.php';
        $config = require dirname(__DIR__) . '/config/config.example.php';

        self::assertIsArray($config);
        self::assertArrayHasKey('settings', $config);
    }
}
