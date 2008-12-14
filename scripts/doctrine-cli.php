#!/usr/bin/env php
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
				. '../application/tables/' . PATH_SEPARATOR
				. get_include_path()
				);

require_once '../library/UGD/Initializer.php';
require_once '../library/doctrine/Doctrine.php';
require_once '../library/Zend/Config/Ini.php';

//require_once "Zend/Loader.php";

// Set up autoload.
//Zend_Loader::registerAutoload();

spl_autoload_register(array('Doctrine', 'autoload'));
/*
 * Setup Application
 */

define("CONFIG_MAIN","../config/ugd.ini");
include '../config/local.config.php';


$config = new Zend_Config_Ini(CONFIG_MAIN,CONFIG);

try{

	$dbConfig = $config->database;
	$dsn = $dbConfig->adapter."://".$dbConfig->params->username.":".$dbConfig->params->password."@".$dbConfig->params->host."/".$dbConfig->params->dbname;
	Doctrine_Manager::connection($dsn, 'conn1');
	Doctrine_manager::getInstance()->setAttribute('model_loading', 'conservative');
	Doctrine_manager::getInstance()->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

	//Doctrine::generateModelsFromYaml('/Users/rdohms/dev/web/groupdirectory/application/doctrine/schema/schema.yml', '/Users/rdohms/dev/web/groupdirectory/application/models');

	$cli = new Doctrine_Cli( $config->doctrine->toArray() );
	$cli->run($_SERVER['argv']);
}catch (Exception $e){
	var_dump($e);
}

