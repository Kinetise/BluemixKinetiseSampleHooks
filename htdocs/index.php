<?php

use Silex\Application;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

defined('APP_PATH') || define('APP_PATH', realpath(dirname(__FILE__) . '/../'));

// Define application environment
defined('APP_ENV') || define(
    'APP_ENV',
    (getenv('APP_ENV') ? getenv('APP_ENV') : 'prod')
);

// Define application environment
defined('APP_DEBUG') || define(
    'APP_DEBUG',
    (getenv('APP_DEBUG') ? (bool) getenv('APP_DEBUG') : false)
);

if (APP_DEBUG === false) {
    ini_set('display_errors', 0);
}

require_once APP_PATH .'/vendor/autoload.php';

ErrorHandler::register();
ExceptionHandler::register(APP_DEBUG);

if (APP_DEBUG === true) {
    Debug::enable();
}

/** @var Application $app */
$app = require APP_PATH . '/app/kernel.php';

$app->run();