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
* copy config/config.example.php as config/config.php
* replace settings and paths for config/config.default.php in config/config.php
* composer install
* cd var/docker
* run `docker-compose up -d`
* run auto process migration in MySQL DB: `docker-compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_auto`
  or run handle process migration in MySQL DB: `docker-compose exec php-fpm php vendor/bin/websk_db_migration.php migrations:migration_handle`
* run process create user: `docker-compose exec php-fpm php vendor/bin/websk_auth_create_user.php auth:create_user`
* open `http://skif.devbox/admin`
* login as created user