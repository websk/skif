<?php

return [
    'settings' => [
        'displayErrorDetails' => false,
        'cache' => [
            'engine' => \WebSK\Cache\Engines\Memcache::class,
            'cache_key_prefix' => 'skif',
            'servers' => [
                [
                    'host' => 'localhost',
                    'port' => 11211
                ]
            ]
        ],
        'db' => [
            'db_skif' => [
                'host' => 'localhost',
                'db_name' => 'skif',
                'user' => '',
                'password' => '',
            ],
        ],
        'site_title' => 'Skif',
    ],
];
