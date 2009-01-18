<?php


chdir( dirname(__FILE__) );

/**
 * My new Zend Framework project
 *
 * @author Rafael Dohms <rdohms@gmail.com>
 * @version
 */
set_include_path('.' . PATH_SEPARATOR
				. '../library' . PATH_SEPARATOR
				. '../library/doctrine' . PATH_SEPARATOR
				. '../application/models/' . PATH_SEPARATOR
				. '../application/models/generated' . PATH_SEPARATOR
				. get_include_path()
				);

require_once "Zend/Loader.php";

// Set up autoload.
Zend_Loader::registerAutoload();
spl_autoload_register(array('Doctrine', 'autoload'));


if (!file_exists( '../config/local.config.php' )) throw new Exception("Please create file <b>'local.config.php'</b> inside <i>config</i> using provided <i>local.config.sample</i>");
require_once '../config/local.config.php';

define('CMDLINE',__FILE__);

try {
    $opts = new Zend_Console_Getopt('a:c:m:');
    $opts->parse();
    Zend_Registry::set('args',$opts);

} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}


$request = new Zend_Controller_Request_Simple($opts->a,$opts->c,$opts->m);

// Prepare the front controller.
$frontController = Zend_Controller_Front::getInstance();
$frontController->setRequest($request);
$frontController->setRouter( new UGD_Controller_Router_Cli() );
$frontController->setResponse( new Zend_Controller_Response_Cli());
$frontController->addModuleDirectory( "../application/" );
// Change to 'production' parameter under production environemtn
$frontController->registerPlugin(new UGD_initializer(CONFIG));

// Dispatch the request using the front controller.
$frontController->dispatch();

print("\n");
print("\n");
?>