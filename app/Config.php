<?php
use TinyFw\DB\Mysql;

define('APP_DIR', dirname(__FILE__));
define('RESOURCE_DIR', APP_DIR.'/Resources');
define('LANGUAGE_FILE', RESOURCE_DIR.'/lang.ini');
define('LANGUAGE_CONFIG', RESOURCE_DIR.'/lang.config.php');
define('ROOT_DIR', realpath(APP_DIR . '/../'));

define('WEB_DIR', ROOT_DIR . '/web');
define('PUBLIC_DIR', WEB_DIR . '/public');
define('ASSEETS_DIR', WEB_DIR . '/public/assets');


define('WEB_ROOT_PATH', 'web/public');
define('WEB_CSS_PATH', WEB_ROOT_PATH . '/_StyleSheet');
define('WEB_JS_PATH', WEB_ROOT_PATH . '/_Scripts');
define('WEB_IMG_PATH', WEB_ROOT_PATH . '/_Image');


define('DATABASE_HOST', 'localhost');
define('DATABASE_PORT', '3306');
define('DATABASE_NAME', 'mydatabase');
define('DATABASE_USER', 'root');
define('DATABASE_PASS', '');
define('DATABASE_DSN', 'mysql:host=' . DATABASE_HOST . ';port=' . DATABASE_PORT . ';dbname=' . DATABASE_NAME);
