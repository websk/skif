# SkIf

## Config example
* config/config.default.php

## Install
* copy config/config.default.php as config/config.php
* replace settings and paths
* composer install
* create MySQL DB db_skif (or other)
* process migration in MySQL DB: `php vendor\bin\websk_db_migration.php migrations:migration_auto` or `php vendor\bin\websk_db_migration.php migrations:migration_handle`
