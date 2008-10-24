<?php

chdir( dirname(__FILE__) );

/**
 * My new Zend Framework project
 *
 * @author
 * @version
 */
set_include_path('.' . PATH_SEPARATOR . '../library' . PATH_SEPARATOR . '../application/default/models/' . PATH_SEPARATOR . get_include_path());
set_include_path('../application/default/tables/' . PATH_SEPARATOR . get_include_path());

require_once 'Initializer.php';
require_once "Zend/Loader.php";

// Set up autoload.
Zend_Loader::registerAutoload();

/*
 * Setup Application
 */

define("CONFIG_MAIN","../config/mimimi.ini");
include '../config/local.config.php';

try {
    $opts = new Zend_Console_Getopt('a:c:m:');
    $opts->parse();

} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}


$request = new Zend_Controller_Request_Simple($opts->a,$opts->c,$opts->m);

// Prepare the front controller.
$frontController = Zend_Controller_Front::getInstance();
$frontController->setRequest($request);
$frontController->setRouter( new Mi_Controller_Router_Cli() );
$frontController->setResponse( new Zend_Controller_Response_Cli());
$frontController->addModuleDirectory( "../application/" );

// Change to 'production' parameter under production environemtn
$frontController->registerPlugin(new Initializer(CONFIG));

// Dispatch the request using the front controller.
$frontController->dispatch();
?>