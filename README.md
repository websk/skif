# WebSK SkIf

https://packagist.org/packages/websk/skif

## Config and install as library for project

* copy /vendor/websk/skif/config/config.example.php as config/config.php
* replace settings and paths for vendor/websk/skif/config/config.default.php in config/config.php

* install as dependency using Composer
```shell
composer require websk/skif
```

* create MySQL DB skif (or other)
* run auto process migration in MySQL DB:
```shell
php vendor\bin\websk_db_migration.php migrations:migration_auto
```
* or run handle process migration in MySQL DB
```shell
php vendor\bin\websk_db_migration.php migrations:migration_handle
````
* run process create user:
```shell
php bin\websk_auth_create_user.php auth:create_user
```

## Demo

* Установить mkcert, https://github.com/FiloSottile/mkcert

* Выполнить:
```shell
mkcert --install
```

* Сделать самоподписанный сертификат для `skif.devbox`:

```shell
mkcert skif.devbox`
```

* Скопировать полученные файлы _wildcard.skif.devbox.pem и _wildcard.skif.devbox.pem в `var/docker/nginx/sites`

* Прописать в `/etc/hosts` или аналог в Windows `%WINDIR%\System32\drivers\etc\hosts`

```
127.0.0.1 skif.devbox
```

* Создаем локальный конфиг, при необходимости вносим изменения:

```shell
cp config/config.example.php config/config.php
```

* Заходим в директорию с docker-compose:

```shell
cd var/docker
```

* Создаем локальный env файл, при необходимости вносим изменения:

```shell
cp .example.env .env
```

* Собираем и запускаем докер-контейнеры:

```shell
docker compose up -d --build
```

* Устанавливаем зависимости для проекта

```shell
docker compose exec php-fpm composer install
```

* Выполняем миграции БД

```shell
docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_auto
```

  or run handle process migration:

```shell
docker compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_handle
```

* Создаем пользователя для входа в админку

```shell
docker compose exec php-fpm php vendor/bin/websk_auth_create_user.php auth:create_user
```

* open `https://skif.devbox/admin`
* login as created user