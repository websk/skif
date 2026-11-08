# Changelog

All notable changes to this project are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). Release dates and historical summaries are derived from the repository tags and commit messages.

## [Unreleased]

## [3.0.0] - 2026-11-08

### Added

- PHPUnit 12 test suite covering request parameters, route arguments, PHP-DI resolution, authentication, CAPTCHA, route registration, configuration, and the PHP deprecation gate.
- PHPStan 2 configuration at level 6 for `src` and `tests`, targeting PHP 8.5.12.
- Composer commands for syntax checks, static analysis, tests, and the combined validation workflow.
- Typed compatibility helpers for entity identifiers, logging, request handlers, route arguments, and route URL generation.
- MIT license metadata and license file.
- Frontend dependency inventory and regression checklist.
- ESLint checks for project-owned JavaScript and npm commands for asset reproducibility and dependency audits.
- Desktop and mobile browser regressions for authentication, navigation, content tabs, CRUD filters, and all administration lists.

### Changed

- Set the Jodit HTML source editor to the light Crimson Editor theme and load Ace from local assets.
- Raised the PHP requirement from `^8.3` to `8.5.*`; the Docker environment now runs PHP 8.5.12.
- Added explicit Composer requirements for the PHP extensions used at runtime.
- Updated Slim to 4.15, PHPMailer to 7.1, RichFileManager PHP to 2.4.0, and the WebSK component stack to the compatible 3.x generation where available.
- Updated CRUD to 4.0.3, Auth and Logger to 4.0.1, and KeyValue to 4.0.0.
- Migrated administration and public layouts to AdminLTE 4.10.0 and Bootstrap 5.3.8, including breadcrumbs, tabs, cards, sidebar search, and responsive tables.
- Replaced the root Composer autoload mapping with `WebSK\Skif\` PSR-4 mappings for production and tests.
- Migrated PHP-DI setup from annotations to attributes and adapted service registration to the mutable-container interface.
- Migrated request input handling and route argument generation to the current PSR-7 and Slim APIs.
- Tightened scalar, nullable, callback, collection, and entity identifier types throughout services, repositories, routes, handlers, and templates.
- Rebuilt the PHP-FPM image from the official PHP 8.5.12 image with pinned optional extensions and reproducible Composer and mail helper installation.
- Simplified Docker Compose and environment configuration, made Xdebug opt-in, and added read-only mounts where appropriate.
- Changed the default application URL prefix to `/`, the local canonical URL to HTTPS, and the Docker cache host to `memcached`.
- Deferred rich-text editor stylesheet URL generation until after application configuration is initialized.
- Reworked the README around the verified checkout, Docker, configuration, RichFileManager, and validation workflows.
- Replaced the copy-only Webpack setup with a deterministic Node.js 24 asset build and reproducibility check.
- Removed stale generated assets and corrected the copied Ace PHP, SQL, XML mode, worker, and Crimson Editor theme paths.
- Updated Ace, jQuery UI, jQuery Validation, Font Awesome Free, and Ionicons; legacy Font Awesome class names remain available through the official compatibility shim.
- Updated jQuery from 2.2.4 to 3.7.1, migrated project event handlers to `.on()`, and verified legacy widgets without shipping jQuery Migrate.
- Replaced Blueimp File Upload with a project-owned image uploader that validates files, reports upload progress and errors, and refreshes the photo list.
- Replaced Eonasdan Bootstrap Datetimepicker and Moment with native date and date-time controls for content publication fields.
- Replaced GPL-licensed Fancybox with MIT-licensed GLightbox for image previews and galleries.
- Replaced CKEditor 4 with MIT-licensed Jodit 4 and a project-owned RichFileManager adapter; the legacy PHP entry point remains as a compatibility wrapper.
- Updated the RichFileManager frontend from 2.7.6 to 3.2.0 and made its curated runtime assets reproducible from the pinned npm package.

### Fixed

- Matched Jodit content typography to the public site's Bootstrap, AdminLTE and main styles, including responsive headings, text, links and lists.
- Resolved file-manager image URLs against the application root and configured the editor iframe base URL so inserted and existing relative images render correctly.
- Redirected local HTTP requests to HTTPS so protected file-manager requests retain the administrator session and selected folder.
- Corrected the static data path from `/var/wwww/public/static` to `/var/www/public/static`.
- Made default and example configuration safe to load repeatedly.
- Resolved PHP 8.5 compatibility issues in handlers, repositories, service providers, templates, image handling, and dependency injection.
- Removed duplicate content route registration.
- Corrected malformed Fancybox script URLs in the main and administration layouts.
- Fixed active menu spacing, rubric and region filter spacing, and block template navigation.
- Fixed AJAX text, option, and pagination filters through the updated CRUD request handling.

### Security

- Removed the publicly bundled legacy jQuery File Upload PHP endpoints; file operations now use the authenticated RichFileManager connector.
- Excluded RichFileManager demos, tests, sample connectors, and server-side files from the public asset build.
- Updated Symfony HttpFoundation to 8.1.11 and excluded local TLS keys and browser test artifacts from release archives.

### Breaking changes

- PHP 8.5.x is required; PHP 8.3 and 8.4 are no longer supported.
- Custom themes and frontend integrations must adopt Bootstrap 5 and AdminLTE 4 markup and data attributes.
- Projects integrating Skif must use the updated WebSK dependency generation and PHP-DI attribute configuration.
- The default `settings.skif.url_path` value changed from `/admin` to `/`; deployments using a URL prefix must override it explicitly.

## [2.5.9] - 2026-05-20

### Changed

- Replaced literal HTTP 307 redirect status values with the corresponding PSR status-code constant.

## [2.5.8] - 2026-05-20

### Changed

- Changed application redirects to HTTP 307 Temporary Redirect.

## [2.5.7] - 2026-05-03

### Changed

- Updated RichFileManager.

## [2.5.6] - 2025-11-26

### Changed

- Updated PHPMailer.

## [2.5.5] - 2025-05-18

### Changed

- Updated `websk/php-auth`.

## [2.5.4] - 2025-04-21

### Changed

- Updated `websk/php-auth`.

## [2.5.3] - 2025-04-20

### Changed

- Updated the user photo directory handling.

## [2.5.2] - 2025-04-16

### Changed

- Updated SQL definitions.

## [2.5.1] - 2025-04-16

### Changed

- Updated `websk/php-auth`.

## [2.5.0] - 2025-04-12

### Changed

- Refactored the application for PHP 8.3.
- Refactored the block subsystem and updated response, CKEditor, and image dependencies during the release cycle.

## Historical releases

The following entries preserve the dates and subjects recorded by the repository tags.

| Version | Date | Repository summary |
| --- | --- | --- |
| 2.4.3 | 2024-01-17 | Fixed content rubrics |
| 2.4.2 | 2023-10-15 | Fixed content templates |
| 2.4.1 | 2023-10-15 | Updated CKEditor |
| 2.4.0 | 2023-10-15 | Updated CKEditor |
| 2.3.4 | 2023-03-24 | Fixed site menu caching |
| 2.3.3 | 2022-02-14 | Updated the site menu |
| 2.3.2 | 2022-02-13 | Added site menu changes |
| 2.3.1 | 2022-02-13 | Fixed error handling |
| 2.3.0 | 2022-02-13 | Updated Composer dependencies |
| 2.2.7 | 2021-07-22 | Adopted request and response interfaces |
| 2.2.6 | 2021-06-25 | Fixed comments |
| 2.2.5 | 2021-06-25 | Updated blocks |
| 2.2.4 | 2021-06-23 | Fixed database migration |
| 2.2.3 | 2021-06-23 | Updated database migration |
| 2.2.2 | 2021-06-23 | Added database migration changes |
| 2.2.1 | 2021-05-22 | Updated the key-value component version |
| 2.2.0 | 2021-05-22 | Added Docker environment |
| 2.1.0 | 2021-04-11 | Added form logging |
| 2.0.1 | 2019-02-27 | Updated blocks |
| 2.0.0-alpha | 2018-12-14 | Refactored the application |
| 1.0.5 | 2019-02-06 | Fixed PHP short tags |
| 1.0.4 | 2019-01-15 | Fixed PHP short tags |
| 1.0.3 | 2018-09-03 | Added cache engines |
| 1.0.2 | 2018-07-18 | Added cache engines |
| 1.0.1 | 2018-06-29 | Added README |
