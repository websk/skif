# WebSK Skif

Skif is an admin panel and CMS toolkit built on Slim 4 and the WebSK PHP components. It includes modules for authentication, settings, logging, content, blocks, comments, forms, polls, redirects, menus, CAPTCHA, images, and file management.

- Package: [websk/skif on Packagist](https://packagist.org/packages/websk/skif)
- Changes: [CHANGELOG.md](CHANGELOG.md)
- PHP policy: `8.5.*` (Composer platform: `8.5.12`)
- Docker runtime: PHP 8.5.12
- License: [MIT](LICENSE)

## Main components

- Slim 4 with PHP-DI integration
- WebSK PHP 3.x components with CRUD, Auth, Logger, and KeyValue 4.x
- MySQL and Memcached
- AdminLTE 4.10, Bootstrap 5.3, jQuery 3.7, and Jodit 4
- RichFileManager 3.2 frontend with the 2.4 PHP backend
- Nginx and PHP-FPM for the Docker development environment

The HTTP entry point is `public/index.php`. The administration interface is available under `/admin`, and `public/` must be configured as the web-server document root.

## Requirements

- PHP 8.5.x
- PHP extensions required by Composer:
  - `fileinfo`
  - `gd`
  - `json`
  - `mbstring`
  - `pdo`
  - `pdo_mysql`
  - `session`
  - `xmlwriter`
- Composer 2
- MySQL 8.x or a compatible server
- Memcached and the PHP `memcached` extension when using the default cache configuration
- Node.js 24 LTS and npm 11 when rebuilding frontend assets
- Docker Compose and [mkcert](https://github.com/FiloSottile/mkcert) for the local HTTPS environment

## Use Skif from another Composer project

The Composer package provides the complete `WebSK\Skif\SkifApp` Slim application, including its admin and public routes, templates, database migrations, and frontend assets. The consuming project supplies the root configuration, public entry point, writable directories, database, cache, and web-server rules.

The examples below assume this consumer layout:

```text
project/
├── config/config.default.php
├── config/config.php
├── public/index.php
├── public/assets/
├── public/filemanager/filemanager.php
├── public/files/
├── public/static/
└── vendor/
```

### 1. Install the package

```bash
composer require websk/skif
mkdir -p config public/assets public/filemanager public/files public/static log tmp
```

The PHP process must be able to write to `public/files`, `public/static`, `log`, and `tmp`.

### 2. Create the consumer configuration

Use the supplied [default configuration](config/config.default.php) and [override example](config/config.example.php) as the reference. In an installed Composer package, the same files are available under `vendor/websk/skif/config/`.

Copy both files into the consumer project:

```bash
cp vendor/websk/skif/config/config.default.php config/config.default.php
cp vendor/websk/skif/config/config.example.php config/config.php
```

`config/config.php` loads the adjacent `config.default.php` and applies local overrides with `array_replace_recursive()`. Keep environment-specific changes in `config/config.php`; after updating `websk/skif`, compare the copied default file with `vendor/websk/skif/config/config.default.php` for new or changed settings.

At minimum, review and override:

- all four entries under `settings.db`: `db_auth`, `db_keyvalue`, `db_logger`, and `db_skif`;
- the cache server under `settings.cache.servers`;
- `settings.site_domain` and `settings.site_full_path`;
- `settings.log_path`, `settings.tmp_path`, `settings.static_data_path`, and `settings.files_data_path`;
- `settings.storages.files.root_path` and its public `url_path`;
- every path under `settings.layout` and `settings.skif.layout`;
- `settings.displayErrorDetails`, which should be disabled outside development.

The four database entries must remain present because their migration dump paths come from the corresponding WebSK packages. They may all point to the same physical database.

The supplied defaults use `/var/www` paths from the included Docker environment. For an external project, point the layout settings to `vendor/websk/skif/views/layouts/`, or copy `vendor/websk/skif/views/` into the consumer project before replacing those paths.

### 3. Create the public entry point

Create `public/index.php`:

```php
<?php

declare(strict_types=1);

use Jgut\Slim\PHPDI\Configuration;
use Jgut\Slim\PHPDI\ContainerBuilder;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\ServerRequestCreatorFactory;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifApp;

require dirname(__DIR__) . '/vendor/autoload.php';

$config = require dirname(__DIR__) . '/config/config.php';

$configuration = new Configuration();
$configuration->setDefinitions([$config]);
$configuration->setUseAttributes(true);

$container = ContainerBuilder::build($configuration);
$container->set(
    ServerRequestInterface::class,
    static fn (): ServerRequestInterface => ServerRequestCreatorFactory::create()
        ->createServerRequestFromGlobals()
);

ConfWrapper::setConfig($config['settings']);

$app = new SkifApp($container);
$app->run();
```

`SkifApp` constructs the Slim application and registers the Skif route set. Use this entry point as the application kernel; `/admin` and the public module routes are registered by its constructor. The package does not currently expose a single installer for mounting the complete Skif route set into an already constructed `Slim\App`; that integration requires registering the individual WebSK service providers and route classes in the host application.

### 4. Publish public files

Copy the packaged assets and RichFileManager connector into the consumer document root:

```bash
cp -R vendor/websk/skif/public/assets/. public/assets/
cp vendor/websk/skif/public/filemanager/filemanager.php public/filemanager/filemanager.php
```

Repeat this step after updating `websk/skif`. The copied connector expects the standard paths `vendor/autoload.php` and `config/config.php` shown above.

Skif generates versioned URLs such as `/assets/1/styles/skif.css`, while the physical file is stored as `public/assets/styles/skif.css`. Configure the web server to remove the numeric version segment internally. RichFileManager also needs `/filemanager` to resolve to its PHP connector. The included Nginx configuration uses:

```nginx
location ~ ^/assets/\d+/.* {
    rewrite ^/assets/\d+/(.*) /assets/$1;
}

location /filemanager {
    try_files $uri /filemanager/filemanager.php$is_args$args;
}

location = /filemanager/filemanager.php {
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    fastcgi_pass php-upstream;
    include fastcgi_params;
}
```

Configure the remaining requests through `public/index.php`, and use `public/` as the document root.

### 5. Initialize the database and administrator

Run the package-provided Composer binaries from the consumer project root:

```bash
php vendor/bin/websk_db_migration.php migrations:migration_auto
php vendor/bin/websk_auth_create_user.php auth:create_user
```

The command generates random credentials; use the exact `Email` and `Password` printed by the command when signing in.

Complete migrations before serving the first HTTP request: public content route initialization may query the Skif database. The administration interface is then available at `/admin`. RichFileManager uses `settings.files_data_path` and accepts requests only from authenticated administrators.

## Install a source checkout

Install PHP dependencies and create the local configuration:

```bash
composer install
cp config/config.example.php config/config.php
mkdir -p public/files public/static log tmp
```

`config/config.php` and uploaded files under `public/files/` are intentionally ignored by Git. Ensure that `public/files`, `public/static`, `log`, and `tmp` are writable by the PHP-FPM user in your environment.

Frontend assets are committed to the repository. Rebuild them after changing `package.json`, `package-lock.json`, or `scripts/build-assets.mjs`:

```bash
npm ci
npm run build
npm run check
npm run audit:production
npm run audit:high
```

Use Node 24 and npm 11 as declared in `package.json`; `.nvmrc` and `.node-version` are provided for local version managers.

`npm run check` lints project and browser-test JavaScript, rejects Bootstrap 3/AdminLTE 2 contracts, validates local CSS and source-map references, enforces the generated asset budget, rejects server-side files under `public/assets`, and compares committed assets with a clean temporary build. `audit:production` fails on any production advisory of moderate severity or higher; `audit:high` checks the complete dependency tree for high and critical findings.

The build script copies an explicit set of third-party files from `node_modules/` to managed directories under `public/assets/libraries/`. RichFileManager is included in the same reproducible build from the exact npm version recorded in `package-lock.json`; its public tree contains only the files required at runtime.

Project-owned browser code is stored directly under `public/assets/scripts/`. This includes the content photo uploader, which validates GIF/JPEG/PNG files, reports upload progress, and refreshes the photo list. Blueimp File Upload and MediumEditor remain published only for their `websk/php-crud` widgets.

After starting Docker, install Chromium once and run the browser smoke suite:

```bash
npm run test:e2e:install
npm run test:e2e
```

The unauthenticated login smoke always runs. To exercise the authenticated AdminLTE shell, set `SKIF_E2E_EMAIL` and `SKIF_E2E_PASSWORD` for a dedicated test administrator. Set `SKIF_E2E_CONTENT_EDIT_URL` to an editable fixture URL to test content tabs. The suite fails on browser console errors and failed document, script, stylesheet, font, or image requests, and runs at desktop and mobile viewports.

The current browser behavior and upgrade-sensitive integration points are recorded in the [frontend baseline](docs/frontend-baseline.md). The [dependency inventory](docs/frontend-dependencies.md) records package ownership, versions, licenses, known audit findings, and the intended migration direction.

## Run the Docker environment

Create a locally trusted certificate:

```bash
mkcert --install
mkdir -p var/docker/nginx/ssl
cd var/docker/nginx/ssl
mkcert skif.devbox
cd ../../../..
```

Add the following entry to `/etc/hosts` on Linux or macOS, or to `%WINDIR%\System32\drivers\etc\hosts` on Windows:

```text
127.0.0.1 skif.devbox
```

Prepare the application and Docker configuration:

```bash
cp config/config.example.php config/config.php
mkdir -p public/files public/static log tmp
cp var/docker/.example.env var/docker/.env
```

Start the services and install PHP dependencies inside the PHP 8.5.12 container:

```bash
cd var/docker
docker compose up -d --build
docker compose exec php-fpm composer install
```

Run database migrations and create an administrator:

```bash
docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_auto
docker compose exec php-fpm php vendor/bin/websk_auth_create_user.php auth:create_user
```

The command prints the generated `Email` and `Password` required for authorization.

For interactive migration selection, use:

```bash
docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_handle
```

Open <https://skif.devbox/admin> and sign in with the administrator account you created.

Useful Docker commands:

```bash
docker compose ps
docker compose logs -f php-fpm nginx
docker compose down
```

## Configuration

`config/config.example.php` merges local development overrides into `config/config.default.php`. Copy the example to the ignored `config/config.php` and keep environment-specific settings there.

Review these settings when adapting the application:

| Setting | Purpose | Docker default |
| --- | --- | --- |
| `settings.displayErrorDetails` | Detailed Slim error responses | `true` in the example |
| `settings.db.*` | Database connection for each WebSK module | MySQL service, `skif` database |
| `settings.cache.*` | Cache engine, prefix, lifetime, and servers | Memcached service |
| `settings.skif.url_path` | URL prefix used for versioned assets | `/` |
| `settings.skif.assets_version` | Version segment in asset URLs | `1` |
| `settings.site_domain` | Canonical application URL used in links and messages | `https://skif.devbox` |
| `settings.files_data_path` | Local file-manager storage | `/var/www/public/files` |
| `settings.storages.files` | File storage adapter, URL, and allowed file types | Local storage under `/files` |
| `settings.layout.*` | Absolute paths to PHP layout templates | `/var/www/views/layouts/...` |
| `settings.rich_text_editor.styles` | Stylesheets loaded into the isolated Jodit editing area | `/assets/<version>/styles/main.css` when empty |
| `settings.rich_text_editor.filemanager_path` | Optional RichFileManager frontend URL | `/assets/<version>/libraries/filemanager/index.html` when empty |

For compatibility with existing integrations, the editor also reads the former `settings.ckeditor.styles` and `settings.ckeditor.filemanager_path` keys when the corresponding `rich_text_editor` value is absent.

The default configuration contains development credentials suitable for the included Docker environment. Replace credentials, `site_domain`, filesystem paths, and `displayErrorDetails` for deployment.

The Docker environment is configured through `var/docker/.env`, copied from `var/docker/.example.env`. It controls ports, bind-mount paths, MySQL, and optional PHP extensions. If database names or credentials change, update both Docker and application configuration.

## RichFileManager

Jodit provides separate image and file buttons that open the frontend from `/assets/<version>/libraries/filemanager/index.html`. RichFileManager returns the selected resource to the active editor through a same-origin `postMessage`. Its connector sends requests to `/filemanager`, which is handled by `public/filemanager/filemanager.php` and the `hkvstore/richfilemanager-php` backend.

The connector uses `settings.files_data_path` as its root and permits access only to authenticated administrators. Keep the Nginx `/filemanager` locations when replacing the supplied server configuration.

The RichFileManager 3.2 runtime is generated from the pinned `@hkvstore/rich-filemanager` npm package. Skif copies only the files used at runtime; upstream demos, tests, Node/PHP connectors, and sample upload endpoints are excluded. Project-specific frontend settings are maintained in `resources/frontend/filemanager/` and copied by `npm run build`.

## Development checks

```bash
composer lint
composer phpstan
composer test
composer check
composer validate --strict
```

- `composer lint` checks PHP syntax in `src`, `public`, `config`, and `tests`.
- `composer phpstan` analyzes `src` and `tests` at level 6 using the PHP 8.5.12 target.
- `composer test` runs PHPUnit 12 with warnings and deprecations treated as failures.
- `composer check` runs lint, PHPStan, and PHPUnit in sequence.

## Project structure

- `config/` — default and local application configuration
- `public/` — document root, front controller, assets, uploads, and file-manager connector
- `src/WebSK/Skif/` — application, routes, handlers, services, and modules
- `tests/` — PHPUnit tests and PHP 8.5 deprecation gate
- `views/` — application layouts and shared templates
- `var/docker/` — Nginx, PHP-FPM, MySQL, and Memcached development environment
- `scripts/build-assets.mjs` — deterministic frontend asset copying and verification

## Troubleshooting

- **Missing `config/config.php`:** copy `config/config.example.php` before running HTTP or CLI entry points.
- **Database connection errors:** verify `settings.db.*`, the MySQL service state, and values in `var/docker/.env`.
- **Cache connection errors:** start Memcached or configure another supported cache engine.
- **Asset 404 responses:** run `npm ci && npm run build` and keep the Nginx rewrite for `/assets/<version>/...`.
- **File-manager errors:** verify the `/filemanager` Nginx locations and write access to `settings.files_data_path`.
- **HTTPS errors:** confirm that `skif.devbox.pem` and `skif.devbox-key.pem` exist in `var/docker/nginx/ssl/` and are trusted by the local mkcert CA.

## License

Skif is released under the [MIT License](LICENSE).
