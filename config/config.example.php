<?php
$default_config = require __DIR__ . '/config.default.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$config['settings']['skif']['url_path'] = '/';
$config['settings']['cache']['servers'][0]['host'] = 'memcached';

return array_replace_recursive($default_config, $config);
