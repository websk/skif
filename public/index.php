<?php

use WebSK\Config\ConfWrapper;

require '../vendor/autoload.php';

$config_path = realpath(__DIR__ . '/../config/config.php');
$config = require_once $config_path;

ConfWrapper::setConfig($config['settings']);

$app = new WebSK\Skif\SkifApp($config);
$app->run();

