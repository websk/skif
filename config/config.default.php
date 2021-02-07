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
            'db_auth' => [
                'host' => 'localhost',
                'db_name' => 'skif',
                'user' => '',
                'password' => '',
            ],
            'db_keyvalue' => [
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
            'empty' => '/var/www/skif/views/layouts/layout.empty.tpl.php',
            'blank' => '/var/www/skif/views/layouts/layout.blank.tpl.php',
            'main' => '/var/www/skif/views/layouts/layout.main.tpl.php',
            'admin' => '/var/www/skif/views/layouts/layout.adminLTE.tpl.php',
            'error' => '/var/www/skif/views/layouts/layout.error.tpl.php',
        ],
        'skif' => [
            'menu' => [
                ['link' => '/admin/content/page', 'name' => 'Страницы', 'icon' => '<i class="fa fa-files-o fa-fw"></i>'],
                ['link' => '/admin/content/news', 'name' => 'Новости', 'icon' => '<i class="fa fa-newspaper-o fa-fw"></i>'],
                ['link' => '/admin/site_menu', 'name' => 'Менеджер меню', 'icon' => '<i class="fa fa-bars fa-fw"></i>'],
                ['link' => '/admin/blocks', 'name' => 'Блоки', 'icon' => '<i class="fa fa-table fa-fw"></i>'],
                ['link' => '/admin/poll', 'name' => 'Опросы', 'icon' => '<i class="fa fa-bar-chart fa-fw"></i>'],
                ['link' => '/admin/form', 'name' => 'Формы', 'icon' => '<i class="fa fa-list-alt fa-fw"></i>'],
                ['link' => '/admin/comments', 'name' => 'Комментарии', 'icon' => '<i class="fa fa-comments-o fa-fw"></i>'],
                ['link' => '/admin/user', 'name' => 'Пользователи', 'icon' => '<i class="fa fa-users fa-fw"></i>'],
                [
                    'link' => '#',
                    'name' => 'Настройки',
                    'icon' => '<i class="fa fa-wrench fa-fw"></i>',
                    'sub_menu' => [
                        [
                            'link' => '/admin/content_type',
                            'name' => 'Типы контента',
                            'icon' => '<i class="fa fa-circle-o"></i>'
                        ],
                        [
                            'link' => '/admin/template',
                            'name' => 'Темы',
                            'icon' => '<i class="fa fa-circle-o"></i>'
                        ],
                        [
                            'link' => '/admin/redirect',
                            'name' => 'Редиректы',
                            'icon' => '<i class="fa fa-circle-o"></i>'
                        ],
                        [
                            'link' => '/admin/keyvalue',
                            'name' => 'Параметры',
                            'icon' => '<i class="fa fa-circle-o"></i>'
                        ],
                    ]
                ]
            ],
            'layout' => '/var/www/skif/views/layouts/layout.adminLTE.tpl.php',
            'url_path' => '/admin',
            'main_page' => '/admin/content/page',
            'assets_version' => 1
        ],
        'log_path' => '/var/www/log',
        'tmp_path' => '/var/www/tmp',
        'static_data_path' => '/var/www/skif/public/static',
        'files_data_path' => '/var/www/skif/public/files',
        'site_domain' => 'http://skif.dev',
        'site_full_path' => '/var/www/skif',
        'assets_url_path' => '/assets',
        'site_name' => 'Скиф',
        'site_title' => 'WebSK. Skif',
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
            'send_answer_to_email' => true,
            'no_add_comments_for_unregistered_users' => false
        ],
    ],
];
