<?php
/**
 * AllTests - A Test Suite for your Application 
 * 
 * @author Rafael Dohms <rdohms@gmail.com>
 * @version 
 */

set_include_path(
	'.' . PATH_SEPARATOR . 
	'../library' . PATH_SEPARATOR . 
	'../library/doctrine' . PATH_SEPARATOR .
	'../application/models/' . PATH_SEPARATOR . 
	'../application/models/generated' . PATH_SEPARATOR . 
	get_include_path());

require_once 'Zend/Loader.php'; 

//Environment config (TEST)
if (!file_exists( '../config/test.config.php' )) throw new Exception("Please create file <b>'test.config.php'</b> inside <i>config</i> using provided <i>test.config.sample</i>");
require_once '../config/test.config.php';

// Set up autoload.
Zend_Loader::registerAutoload();
spl_autoload_register(array('Doctrine', 'autoload'));

$locale = new Zend_Locale('en');
Zend_Registry::set('Zend_Locale',$locale);
$frontController = Zend_Controller_Front::getInstance(); 
$initializer = new UGD_Initializer(CONFIG_TEST_ENV);
	
//Add Test Paths
set_include_path(
	'../test/application/admin/controllers/' . PATH_SEPARATOR .
	'../test/application/default/controllers/' . PATH_SEPARATOR .
	'../test/functional/' . PATH_SEPARATOR .
	get_include_path());

/**
 * AllTests class - aggregates all tests of this project
 */
class AllTests extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'AllTests' );
		
		//Default
		require_once 'IndexControllerTest.php';
		$this->addTestSuite ( 'IndexControllerTest' );
		
		//Admin
		require_once 'Admin_IndexControllerTest.php';
		$this->addTestSuite ( 'Admin_IndexControllerTest' );
		
		
		//Functional Tests
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
	
	public function setUp(){

	   	try{
			//Load path configs
		   	$root_path = str_replace('test', '', dirname(__FILE__));
	   		$docCfg = Zend_Registry::get('config')->doctrine->toArray();
		   	foreach($docCfg as &$cfg){
		   		$cfg = str_replace("{ROOT_PATH}",$root_path,$cfg);
		   	}
		   	
		   	//Generate Database structure for tests
		   	$this->tearDown();
			Doctrine::createDatabases();
			Doctrine::createTablesFromModels($docCfg['models_path']);
			Doctrine::loadModel($docCfg['models_path']);
			Doctrine::loadData($docCfg['data_fixtures_path']);
			Util_Log::get()->UnitTests()->debug('Executed Env SetUp');
	   	}catch (Exception $e){
	   		Util_Log::get()->UnitTests()->debug('Failed Setting up environment');
	   		Util_Log::get()->UnitTests()->err($e->getMessage());
	   	}
	}
	
	public function tearDown(){
		try{
			Doctrine::dropDatabases();
			Util_Log::get()->UnitTests()->debug('Executed TearDown');
		}catch (Exception $e){
			Util_Log::get()->UnitTests()->err($e->getMessage());
		}
	}
}

