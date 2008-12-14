<?php
/**
 * My new Zend Framework project
 *
 * @author Rafael Dohms <rdohms@gmail.com>
 * @version 0.1 aplha
 */

set_include_path('.' . PATH_SEPARATOR
				. '../library' . PATH_SEPARATOR
				. '../library/doctrine' . PATH_SEPARATOR
				. '../application/models/' . PATH_SEPARATOR
				. '../application/models/generated' . PATH_SEPARATOR
				. get_include_path()
				);

//require_once '../library/UGD/Initializer.php';
require_once "Zend/Loader.php";

// Set up autoload.
Zend_Loader::registerAutoload();
spl_autoload_register(array('Doctrine', 'autoload'));


if (!file_exists( '../config/local.config.php' )) throw new Exception("Please create file <b>'local.config.php'</b> inside <i>config</i> using provided <i>local.config.sample</i>");
require_once '../config/local.config.php';

// Prepare the front controller.
$frontController = Zend_Controller_Front::getInstance();

// Change to 'production' parameter under production environemtn
$frontController->registerPlugin(new UGD_initializer(CONFIG));

// Dispatch the request using the front controller.
$frontController->dispatch();
?>