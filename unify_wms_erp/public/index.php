<?php
ini_set('display_errors', 1);
error_reporting(E_ALL^E_NOTICE);
date_default_timezone_set('Asia/Shanghai');
if (phpversion() >= '5.3') {
	define('APPLICATION_PATH', dirname(__DIR__));
} else {
	define('APPLICATION_PATH', dirname(dirname(__FILE__)));
}

define('VIEW_PATH', APPLICATION_PATH.'/application/views/');
define('WEB_ROOT',  '/');
define('SSN_PASS',  'online');
define('SSN_INFO',  'msr');
define('SSN_VAR',  'hengyang');
define('SSN_LOG',   'log');
define('SSN_SA',    99999);
define('VERSION', '1.0.0');
define('DB_PREFIX',  'hengyang_');

define('VAL_YES',     1);
define('VAL_NO',      0);
define('VAL_ALL',   100);
define('VAL_NONE', -100);

define('WEB_HOST',"http://v2.chinayie.com");

require_once '../vendor/autoload.php';


$app = new Yaf_Application(APPLICATION_PATH .'/application/conf/app.ini');
$app->getDispatcher()->catchException(true);
$app->bootstrap()->run();

