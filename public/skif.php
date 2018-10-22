<?php

require '../vendor/autoload.php';

$config = require_once realpath(__DIR__ . '/../config/config.php');

$app = new WebSK\Skif\SkifApp($config);

$app->run();
