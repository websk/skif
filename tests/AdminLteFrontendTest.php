<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\TestCase;

final class AdminLteFrontendTest extends TestCase
{
    public function testPinnedFrontendVersionsMatchAdminLteFourContract(): void
    {
        $package = json_decode(
            (string) file_get_contents(dirname(__DIR__) . '/package.json'),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        self::assertSame('4.10.0', $package['dependencies']['admin-lte']);
        self::assertSame('5.3.8', $package['dependencies']['bootstrap']);
    }

    public function testAdminLayoutUsesAdminLteFourMarkupAndAssets(): void
    {
        $layout = (string) file_get_contents(dirname(__DIR__) . '/views/layouts/layout.adminLTE.tpl.php');

        self::assertStringContainsString('class="app-wrapper"', $layout);
        self::assertStringContainsString('class="app-header navbar', $layout);
        self::assertStringContainsString('class="app-sidebar ', $layout);
        self::assertStringContainsString('class="app-main"', $layout);
        self::assertStringContainsString('data-lte-toggle="sidebar"', $layout);
        self::assertStringContainsString('/bootstrap/js/bootstrap.bundle.min.js', $layout);
        self::assertStringContainsString('/admin-lte/js/adminlte.min.js', $layout);
        self::assertStringNotContainsString('Online</small>', $layout);
        self::assertStringContainsString('data-lte-toggle="sidebar-search"', $layout);
        self::assertStringContainsString('data-lte-search-empty', $layout);
        self::assertStringContainsString('images/admin/skif_small_logo_adminlte.png', $layout);

        self::assertStringNotContainsString('class="main-header"', $layout);
        self::assertStringNotContainsString('data-toggle=', $layout);
        self::assertStringNotContainsString('skin-blue', $layout);
        self::assertStringNotContainsString('/admin-lte/js/app.min.js', $layout);
    }

    public function testAdminMenuUsesAdminLteFourTreeviewContract(): void
    {
        $menu = (string) file_get_contents(dirname(__DIR__) . '/views/admin_menu.tpl.php');
        self::assertStringContainsString('id="admin-sidebar-menu"', $menu);

        self::assertStringContainsString('data-lte-toggle="treeview"', $menu);
        self::assertStringContainsString('class="nav nav-treeview"', $menu);
        self::assertStringContainsString('class="nav-arrow ', $menu);
        self::assertStringNotContainsString('data-widget=', $menu);
        self::assertStringNotContainsString('treeview-menu', $menu);
    }

    public function testAdminLoginUsesPhpAuthFourRememberMeContract(): void
    {
        $layout = (string) file_get_contents(dirname(__DIR__) . '/views/layouts/layout.admin_login.tpl.php');

        self::assertStringContainsString('name="save_auth" type="checkbox" value="1"', $layout);
        self::assertStringNotContainsString('value="Запомнить меня"', $layout);
    }

    public function testContentTabsAndFormsUseBootstrapFiveContracts(): void
    {
        $form = (string) file_get_contents(
            dirname(__DIR__) . '/src/WebSK/Skif/Content/views/admin_content_form_edit.tpl.php'
        );

        self::assertStringContainsString('data-bs-toggle="tab"', $form);
        self::assertStringContainsString('data-bs-target="#seo"', $form);
        self::assertStringContainsString('class="row mb-3"', $form);
        self::assertStringContainsString('class="form-select"', $form);
        self::assertStringNotContainsString(".tab('show')", $form);
        self::assertStringNotContainsString('<select id="template_id" name="template_id" class="form-control">', $form);
    }

    public function testBlockTemplateNavigationUsesBootstrapFiveTabs(): void
    {
        $handler = (string) file_get_contents(
            dirname(__DIR__) . '/src/WebSK/Skif/Blocks/RequestHandlers/Admin/BlockListHandler.php'
        );

        self::assertStringContainsString('<li class="nav-item" role="presentation">', $handler);
        self::assertStringContainsString('<a class="nav-link', $handler);
        self::assertStringContainsString('aria-current="page"', $handler);
    }

    public function testPublicMenuUsesAccessibleBootstrapFiveNavigation(): void
    {
        $menu = (string) file_get_contents(
            dirname(__DIR__) . '/src/WebSK/Skif/SiteMenu/views/site_menu_default.tpl.php'
        );

        self::assertStringContainsString('nav flex-column nav-pills', $menu);
        self::assertStringContainsString('class="nav-item"', $menu);
        self::assertStringContainsString('aria-current="page"', $menu);
        self::assertStringNotContainsString('nav-stacked', $menu);
    }

    public function testHeavyEditorAssetsAreConditional(): void
    {
        $layout = (string) file_get_contents(dirname(__DIR__) . '/views/layouts/layout.adminLTE.tpl.php');

        self::assertStringContainsString('$needs_rich_text_editor', $layout);
        self::assertStringContainsString('$needs_glightbox', $layout);
        self::assertStringContainsString('data-skif-rich-text-editor', $layout);
        self::assertStringContainsString('data-glightbox', $layout);
    }
}
