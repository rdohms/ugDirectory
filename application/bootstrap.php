<?php
/**
 * My new Zend Framework project
 *
 * @author Rafael Dohms <rdohms@gmail.com>
 * @version 0.1 aplha
 */

define('VERSION',"0.1 Alpha");

set_include_path('.' . PATH_SEPARATOR
				. '../library' . PATH_SEPARATOR
				. '../library/doctrine' . PATH_SEPARATOR
				. '../application/models/' . PATH_SEPARATOR
				. '../application/models/generated' . PATH_SEPARATOR
				. '../application/tables/' . PATH_SEPARATOR
				. get_include_path()
				);

require_once 'Initializer.php';
require_once "Zend/Loader.php";


// Set up autoload.
Zend_Loader::registerAutoload();
spl_autoload_register(array('Doctrine', 'autoload'));


define("CONFIG_MAIN","../config/ugd.ini");
include '../config/local.config.php';

// Prepare the front controller.
$frontController = Zend_Controller_Front::getInstance();

// Change to 'production' parameter under production environemtn
$frontController->registerPlugin(new Initializer(CONFIG));
$frontController->registerPlugin(new UGD_Login_Plugin());

// Dispatch the request using the front controller.
$frontController->dispatch();
?>