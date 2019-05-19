<?php

require '../vendor/autoload.php';

$config_path = realpath(__DIR__ . '/../config/config.php');
$config = require_once $config_path;

\WebSK\Config\ConfWrapper::setConfig($config['settings']);

$app = new WebSK\Skif\SkifApp($config);
$app->run();

