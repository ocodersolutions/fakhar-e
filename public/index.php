<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); 

header('Content-Type: text/html; charset=utf-8');
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
//echo 'test'; exit(0);
require 'define.php';

date_default_timezone_set('America/Toronto');
chdir(dirname(__DIR__));
defined('PUBLIC_PATH') || define('PUBLIC_PATH', realpath(dirname(__FILE__)));
// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

Zend\Loader\AutoloaderFactory::factory(array(
     'Zend\Loader\StandardAutoloader' => array(
         'namespaces' => array(
             'Ocoder' => dirname(__DIR__).'/vendor/ocoder',
         ),
     )
));

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();

function asd($dataArr,$isNotStop=0)
{
    echo "<pre>";print_r($dataArr);
    if(!$isNotStop)
    {
        die;
    }
}
