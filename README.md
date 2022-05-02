# SkIf

## Install as library
* copy /vendor/websk/skif/config/config.example.php as config/config.php
* replace settings and paths for vendor/websk/skif/config/config.default.php in config/config.php
* composer install
* create MySQL DB skif (or other)
* run auto process migration in MySQL DB: `php vendor\bin\websk_db_migration.php migrations:migration_auto`
  or run handle process migration in MySQL DB `php vendor\bin\websk_db_migration.php migrations:migration_handle`
* run process create user: `php bin\websk_auth_create_user.php auth:create_user`

## Demo

* Установить mkcert, https://github.com/FiloSottile/mkcert

* Выполнить: `$ mkcert --install`

* Сделать самоподписанный сертификат для `skif.devbox`:

  `$ mkcert skif.devbox`

* Скопировать полученные файлы _wildcard.skif.devbox.pem и _wildcard.skif.devbox.pem в `var/docker/nginx/sites`

* Прописать в `/etc/hosts` или аналог в Windows `%WINDIR%\System32\drivers\etc\hosts`

    ```
    127.0.0.1 skif.devbox
    ```

* Создаем локальный конфиг, при необходимости вносим изменения:

  `$ cp config/config.example.php config/config.php`

* Заходим в директорию с docker-compose:

  `$ cd var/docker`

* Создаем локальный env файл, при необходимости вносим изменения:

  `$ cp .example.env .env`

* Собираем и запускаем докер-контейнеры:

  `$ docker-compose up -d --build`

* Устанавливаем зависимости для проекта

  `$ docker-compose exec php-fpm composer install`

* Выполняем миграции БД

  `$ docker-compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_auto`

  or run handle process migration:
  
  `$ docker-compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_handle`

* Создаем пользователя для входа в админку

  `$ docker-compose exec php-fpm php vendor/bin/websk_auth_create_user.php auth:create_user`

* open `https://skif.devbox/admin`
* login as created user