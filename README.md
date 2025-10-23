# WebSK Skif

Skif is an admin panel and CMS toolkit built on Slim 4 and the WebSK PHP components. It provides ready-made modules (auth, key–value settings, logger, content, blocks, comments, forms, polls, redirects, site menu, captcha, image manager) and an AdminLTE-based UI. You can use it as a dependency in your PHP project or run the included demo with Docker.

Packagist: https://packagist.org/packages/websk/skif

## Tech stack

- Language: PHP 8.3
- Framework: Slim 4 (PSR-7/15/17), PHP-DI integration (Jgut Slim PHPDI)
- Modules: websk/php-* packages (auth, cache, captcha, config, db, entity, crud, imagemanager, keyvalue, logger, slim helpers, utils, view)
- Templates/Views: PHP view (slim/php-view)
- Frontend assets: Node.js + Webpack (copy-only build) with AdminLTE, Bootstrap 3, jQuery, CKEditor 4, etc.
- Storage: MySQL (default), Memcached (cache)
- Web server: Nginx + PHP-FPM (via Docker compose in var/docker)

## Entry points

- HTTP: public/index.php – boots the application, loads config/config.php, builds the DI container, and runs WebSK\Skif\SkifApp.
- Admin UI: /admin (route name admin:main). Public routes are registered via WebSK\Skif\SkifApp.

## Requirements

- PHP 8.3 with extensions:
  - ext-mbstring
  - ext-pdo
  - ext-json
- Composer
- MySQL 8.x (or compatible) database
- Memcached (optional, default cache engine)
- Node.js 14.17.x and npm 7.12.x (as constrained by package.json engines) for building static assets
- For the local demo: Docker and Docker Compose, mkcert for local HTTPS

## Installation and configuration

You can either use Skif as a Composer dependency in an existing Slim project or run the included demo stack.

### Use as a library in your project

1) Install dependency via Composer:

```bash
composer require websk/skif
```

2) Create your application config from the example and adjust values:

```bash
cp config/config.example.php config/config.php
```

- The example merges your overrides with config/config.default.php. Key sections:
  - settings.displayErrorDetails – toggle Slim error details
  - settings.db.* – DB connection settings per module (default host mysql, db skif, user root, password root)
  - settings.cache.* – cache engine and Memcached server
  - settings.skif.* – admin path, menu, assets, etc.
  - paths like views/layouts and data directories are absolute inside the container in the demo; adapt for your environment.

3) Prepare a MySQL database (default name skif) and run migrations:

```bash
php vendor/bin/websk_db_migration.php migrations:migration_auto
# or
php vendor/bin/websk_db_migration.php migrations:migration_handle
```

4) Create an admin user for the backend:

```bash
php vendor/bin/websk_auth_create_user.php auth:create_user
```

5) Serve the public/ directory with your web server (Nginx/Apache). The document root must be the public folder; front controller is public/index.php.

### Build static assets

```bash
npm install
npm run build
```

This copies third‑party libraries from node_modules to public/assets/libraries according to webpack.config.js. No JS bundling/minification is performed (copy only, minimize=false).

### Run the demo locally with Docker (HTTPS)

Below is a polished version of the existing demo instructions.

1) Install mkcert: https://github.com/FiloSottile/mkcert and set up the local CA:

```bash
mkcert --install
```

2) Issue a certificate for skif.devbox:

```bash
mkcert skif.devbox
```

3) Copy the generated certificate and key to var/docker/nginx/ssl:

- skif.devbox.pem
- skif.devbox-key.pem

Note: The previous README mentioned _wildcard files; use the actual filenames produced by mkcert on your machine.

4) Add host mapping (Linux/macOS: /etc/hosts; Windows: %WINDIR%\System32\drivers\etc\hosts):

```
127.0.0.1 skif.devbox
```

5) Create local app config if not present:

```bash
cp config/config.example.php config/config.php
```

6) Move to the Docker directory and create a local .env from the example, then adjust values as needed:

```bash
cd var/docker
cp .example.env .env
```

7) Build and start containers:

```bash
docker compose up -d --build
```

8) Install PHP dependencies inside the container:

```bash
docker compose exec php-fpm composer install
```

9) Run DB migrations:

```bash
docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_auto
# or
docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_handle
```

10) Create an admin user:

```bash
docker compose exec php-fpm php vendor/bin/websk_auth_create_user.php auth:create_user
```

11) Open the admin panel:

- https://skif.devbox/admin
- Login with the user you created.

## Scripts and tooling

- Composer (no custom scripts defined in composer.json):
  - vendor/bin/websk_db_migration.php – database migrations
  - vendor/bin/websk_auth_create_user.php – create admin user
- NPM scripts:
  - npm run build – runs webpack (copy assets)

## Configuration and environment variables

- Application configuration is PHP-based. Start from config/config.example.php which merges with config/config.default.php and override values for your environment.
- Docker environment: var/docker/.example.env contains variables for services (PHP version, ports, MySQL credentials, etc.). Copy it to var/docker/.env and adjust.
- Common settings to review:
  - DB credentials under settings.db.*
  - Cache engine and Memcached server under settings.cache.*
  - Paths (log_path, tmp_path, files_data_path, site_full_path) – ensure they exist and are writable
  - settings.skif.url_path (admin base path), site_domain

## Project structure

- public/ – document root (index.php entry point, static assets under assets/)
- src/WebSK/Skif – application bootstrap (SkifApp), routes, handlers
- config/ – default and example configuration; create config.php for local overrides
- views/ – PHP templates/layouts
- var/docker – Docker setup (nginx, php-fpm, mysql, memcached, compose)
- vendor/ – Composer dependencies
- node_modules/ – Frontend dependencies
- webpack.config.js – asset copying map

## Troubleshooting

- 404s to routes: ensure your web server points to public/ and forwards all requests to public/index.php (see var/docker/nginx/sites/skif.devbox.conf for an example).
- Asset 404s: run npm run build to populate public/assets/libraries.
- DB connection issues: verify settings.db.* in config/config.php and that MySQL is reachable (see var/docker/.env).
- Cache issues: either run Memcached (default) or switch cache engine in settings.cache.*.