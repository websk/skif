# Skif 3.0.0

Skif 3.0.0 migrates the application to PHP 8.5, AdminLTE 4 and Bootstrap 5, and updates the WebSK component stack.

## Upgrade requirements

- Run PHP 8.5.x with the extensions listed in `composer.json`; the tested Docker runtime and Composer platform are PHP 8.5.12.
- Update custom PHP-DI service configuration to attributes and the current container interfaces.
- Update custom layouts, widgets and themes to Bootstrap 5 and AdminLTE 4 markup and data attributes.
- Review `settings.skif.url_path`: its default is now `/`. Configure any deployment prefix explicitly.
- Follow the README configuration and database migration instructions before serving requests.
- Publish the complete supplied frontend asset tree, including `libraries/crud/crud.css`. Rebuilding requires Node 24 and npm 11.

## Highlights

- AdminLTE 4.10.0 and Bootstrap 5.3.8 layouts, responsive tables, breadcrumbs, tabs and sidebar menu search.
- CRUD 4.0.3 with corrected AJAX filter and pagination handling and separated rubric/region buttons.
- Jodit 4 rich-text editing, GLightbox previews and the authenticated RichFileManager integration.
- Native date controls and a project-owned content photo uploader.
- PHP 8.5 compatibility, typed adapters, PHP-DI attributes and stricter request handling.
- Symfony HttpFoundation 8.1.11 security fixes.
- Reproducible frontend assets, static analysis, unit tests and desktop/mobile browser regressions.

See [CHANGELOG.md](../CHANGELOG.md) for the full change list and [README.md](../README.md) for installation.

## Verification

- PHP 8.5.12: Composer validation, strict optimized autoload, platform requirements, syntax checks and PHPStan passed.
- PHPUnit: 35 tests, 105 assertions passed.
- Playwright: 20 desktop/mobile scenarios passed, including secure file-manager navigation and image selection into Jodit; a separate visual review covered 58 available administration URLs at both viewport sizes.
- Frontend contracts and 274 managed assets passed reproducibility checks.
- Composer audit and production npm audit passed. The complete npm tree has three moderate advisories in the development-only ESLint/minimatch chain; the high/critical gate passes.
- A clean Composer consumer installed the extracted release package, passed platform requirements and audit, and loaded `SkifApp` successfully.

The release archive excludes local application settings, uploaded files, dependency directories, caches, browser artifacts and local TLS certificates/keys. Runtime frontend assets remain included for Composer consumers.
