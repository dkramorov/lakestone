<?php

// APPLICATION
define('APPLICATION', 'Catalog');

// HTTP
define('HTTP_SERVER', 'http://lakestone:8080/');

// DIR
define('DIR_OPENCART', '/Users/jocker/lakestone/lakestone/');
define('DIR_APPLICATION', DIR_OPENCART . 'catalog/');
define('DIR_EXTENSION', DIR_OPENCART . 'extension/');
define('DIR_IMAGE', DIR_OPENCART . 'image/');
define('DIR_SYSTEM', DIR_OPENCART . 'system/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', '127.0.0.1');
define('DB_USERNAME', 'jocker');
define('DB_PASSWORD', 'reabhxbr');
define('DB_DATABASE', 'lakestone');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');



define('HTTPS_SERVER', 'http://lakestone:8080/');
define('ROOT_DOMAIN', 'http://lakestone:8080');
define('DIR_MODIFICATION', '/Users/jocker/lakestone/lakestone/');
define('HTTP_CDN', '127.0.0.1:8080/cdn');



define('POOL_ROOT', preg_replace('@/web_root(/|$)@', '', $_SERVER['DOCUMENT_ROOT']));
define('DOCUMENT_ROOT', realpath($_SERVER['DOCUMENT_ROOT']));

// Autoloader
if (is_file(DOCUMENT_ROOT . '/vendor/autoload.php')) {
  require_once(DOCUMENT_ROOT . '/vendor/autoload.php');
}
require_once POOL_ROOT . '/config.php';

if (
  !defined('ROOT_DOMAIN')
  or empty(ROOT_DOMAIN)
) {
  die('Config not found or wrong.');
}
