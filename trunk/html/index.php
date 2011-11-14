<?php

/**
 * APE-HMVC
 */

$start = microtime(true);
 
// do your own voodoo to determine if you're live or not.
define('LIVE', false);

if (LIVE === true) {
    error_reporting(0);
    ini_set('display_errors','0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors','1');
}

// Define constants for file paths, url, etc.
define('DS', DIRECTORY_SEPARATOR);
$path_info = pathinfo(__FILE__);
$root = rtrim($path_info['dirname'], DS.'html');
define('ROOT_PATH', $root);
define('CORE_PATH', ROOT_PATH.DS.'core');
define('WWW_PATH', ROOT_PATH.DS.'html');
define('APP_PATH', ROOT_PATH.DS.'app');

set_include_path(get_include_path().PATH_SEPARATOR.APP_PATH.DS.'libraries');
// defaults.
define('DEFAULT_MODULE', 'home');
define('DEFAULT_CONTROLLER', 'default');
define('DEFAULT_METHOD', 'main');

if (!defined('CLI')) {
    define('CLI', false);
}

require(CORE_PATH.DS.'registry.class.php');
require(CORE_PATH.DS.'loader.class.php');
require(CORE_PATH.DS.'router.class.php');
require(CORE_PATH.DS.'controller.class.php');

ob_start('ob_gzhandler');

$out = '';
try {
    $out = new Controller();
} catch(Exception $e){
    trigger_error($e->getMessage(), E_USER_ERROR);
}

// internal nonsense.. could be built into a debug class and sent with output.
$finish = microtime(true);
$debug = 'Total time spent: '.sprintf('%.6f',($finish-$start)).' seconds<br/>';
$debug .= 'Memory usage: '.number_format(((memory_get_usage()/1024)/1024),4,'.',',').'MB<br/>';
$out = str_replace('<!--debug-->', $debug, $out);

echo $out;

while (ob_get_level() > 0) {
    ob_end_flush();
}