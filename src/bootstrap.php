<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('SRC_PATH', ROOT_PATH . '/src');

require_once SRC_PATH . '/config.php';
require_once SRC_PATH . '/db.php';
require_once SRC_PATH . '/helpers.php';
require_once SRC_PATH . '/auth.php';
require_once SRC_PATH . '/views/site.php';
require_once SRC_PATH . '/views/admin.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$config = appConfig();
date_default_timezone_set($config['app']['timezone']);
mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');
