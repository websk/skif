<?php

require '../vendor/autoload.php';

$config = require_once realpath(__DIR__ . '/../config/config.php');

\WebSK\Config\ConfWrapper::setConfig($config['settings']);

$app = new WebSK\Skif\SkifApp($config);

$app->run();
