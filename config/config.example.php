<?php
$default_config = require_once __DIR__ . '/config.default.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$config['settings']['skif']['url_path'] = '/';

return array_replace_recursive($default_config, $config);