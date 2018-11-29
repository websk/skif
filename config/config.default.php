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
            'db_logger' => [
                'host' => 'localhost',
                'db_name' => 'skif',
                'user' => '',
                'password' => '',
            ],
        ],
        'layout' => [
            'admin' => 'layouts/layout.admin.tpl.php',
            'empty' => 'layouts/layout.empty.tpl.php',
            'blank' => 'layouts/layout.blank.tpl.php',
            'main' => 'layouts/layout.main.tpl.php'
        ],
        'admin_menu' => [
            ['link' => '/admin/site_menu', 'name' => 'Менеджер меню', 'icon' => '<i class="fa fa-bars fa-fw"></i>'],
            ['link' => '/admin/blocks', 'name' => 'Блоки', 'icon' => '<i class="fa fa-table fa-fw"></i>'],
            ['link' => '/admin/poll', 'name' => 'Опросы', 'icon' => '<i class="fa fa-bar-chart fa-fw"></i>'],
            ['link' => '/admin/form', 'name' => 'Формы', 'icon' => '<i class="fa fa-list-alt fa-fw"></i>'],
            ['link' => '/admin/comments', 'name' => 'Комментарии', 'icon' => '<i class="fa fa-comments-o fa-fw"></i>'],
            ['link' => '/admin/users', 'name' => 'Пользователи', 'icon' => '<i class="fa fa-users fa-fw"></i>'],
            ['link' => '/admin/rating', 'name' => 'Рейтинги', 'icon' => '<i class="fa fa-star-o fa-fw"></i>'],
            [
                'link' => '#',
                'name' => 'Настройки<span class="fa arrow"></span>',
                'icon' => '<i class="fa fa-wrench fa-fw"></i>',
                'sub_menu' => [
                    ['link' => \WebSK\Skif\CRUD\CRUDController::getListUrl('\Skif\Content\ContentType'), 'name' => 'Типы контента'],
                    ['link' => \WebSK\Skif\CRUD\CRUDController::getListUrl('\Skif\Content\Template'), 'name' => 'Темы'],
                    ['link' => '/admin/redirect', 'name' => 'Редиректы'],
                    ['link' => '/admin/key_value', 'name' => 'Параметры'],
                ]
            ]
        ]
    ],
    'log_path' => '/var/www/log',
    'tmp_path' => '/var/www/tmp',
    'skif_url_path' => '/admin',
    'site_url' => '/skif',
    'site_path' => '/var/www/skif/public',
    'assets_url_path' => '/assets',
    'site_name' => 'Скиф',
    'site_title' => 'WebSK\Skif',
    'site_email' => 'support@websk.ru',
    'salt' => 'webskskif',
    'content' => [
        'news' => [
            'limit_to_page' => 20,
            'require_main_rubric' => 1,
            'main_rubric_default_id' => 3
        ]
    ],
    'comments' => [
        'message_to_page' => 20,
        'send_answer_to_email' => true
    ]
];
