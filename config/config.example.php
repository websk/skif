<?php
$default_config = require_once __DIR__ . '/config.default.php';

$config = [
    'settings' => [
        'displayErrorDetails' => false,
    ],
];

$config['settings']['skif']['url_path'] = '/';

return array_replace_recursive($default_config, $config);