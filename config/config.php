<?php

$default_config = require_once 'config.default.php';
$config = [
    'settings' => [
        'displayErrorDetails' => true,
        'cache' => [
            'engine' => \WebSK\Cache\Engines\Memcache::class,
            'cache_key_prefix' => 'skif',
            'servers' => [
                [
                    'host' => 'memcached',
                    'port' => 11211
                ]
            ]
        ],
        'db' => [
            'db_skif' => [
                'host' => 'mysql',
                'db_name' => 'skif',
                'user' => 'root',
                'password' => 'root',
            ],

        ],
    ]
];

return array_replace_recursive($default_config, $config);
