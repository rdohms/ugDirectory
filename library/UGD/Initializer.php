<?php
/**
 * My new Zend Framework project
 *
 * @author Rafael Dohms <rdohms@gmail.com>
 * @version
 */

require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Abstract.php';
require_once 'Zend/Controller/Action/HelperBroker.php';

/**
 *
 * Initializes configuration depndeing on the type of environment
 * (test, development, production, etc.)
 *
 * This can be used to configure environment variables, databases,
 * layouts, routers, helpers and more
 *
 */
class UGD_Initializer extends Zend_Controller_Plugin_Abstract
{

	/**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * @var string Current environment
     */
    protected $_env;

    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * @var string Path to application root
     */
    protected $_root;

    /**
     * Constructor
     *
     * Initialize environment, root path, and configuration.
     *
     * @param  string $env
     * @param  string|null $root
     * @return void
     */
    public function __construct($env, $root = null)
    {
        $this->_setEnv($env);
        if (null === $root) {
            $root = realpath(dirname(__FILE__) . '/../../');
        }

        $this->_root = $root;

        $this->initPhpConfig();

        $this->_front = Zend_Controller_Front::getInstance();

        // set the test environment parameters
        if ($env == 'dev') {
			// Enable all errors so we'll know when something goes wrong.
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_startup_errors', 1);
			ini_set('display_errors', 1);

			$this->_front->throwExceptions(true);
        }
        //Call Evironment Config functions
        $this->initPhpConfig();
       	$this->initLogger();
    	$this->initDb();
//    	$this->initCache();
        $this->initI18N();
    }

    /**
     * Initialize environment
     *
     * @param  string $env
     * @return void
     */
    protected function _setEnv($env)
    {
		$this->_env = $env;
    }


    /**
     * Initialize Data bases
     *
     * @return void
     */
    public function initPhpConfig()
    {
    	
    	@define('VERSION',"0.1 Alpha");
    	
    	// Load configuration file
		$config = new Zend_Config_Ini("../config/ugd.ini",$this->_env);
		Zend_Registry::set("config",$config);

		/* Report all errors directly to the screen for simple diagnostics in the dev environment */
		error_reporting( $config->php->error_reporting );
		ini_set('display_startup_errors', $config->php->display_startup_errors);
		ini_set('display_errors', $config->php->display_errors);

    	$this->_config = Zend_Registry::get('config');

    	date_default_timezone_set($this->_config->php->timezone);
    	
    	Zend_Session::start();
    }

    /**
     * Route startup
     *
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->initHelpers();
        $this->initView();
        $this->initPlugins();
        $this->initRoutes();
        $this->initControllers();
    }

    /**
     * Initialize data bases
     *
     * @return void
     */
    public function initDb()
    {
    	$dbConfig = $this->_config->database;
    	$dsn = $dbConfig->adapter."://".$dbConfig->params->username.":".$dbConfig->params->password."@".$dbConfig->params->host."/".$dbConfig->params->dbname;

    	Doctrine_Manager::connection($dsn);
    	
    	$manager = Doctrine_manager::getInstance();   	
    	$manager->setAttribute('model_loading', 'conservative');
    	
    	Zend_Registry::set('conn', $manager->getCurrentConnection());
    }

    /**
     * Initialize action helpers
     *
     * @return void
     */
    public function initHelpers()
    {
    	// register the default action helpers
    	Zend_Controller_Action_HelperBroker::addPath('../application/default/helpers', 'Zend_Controller_Action_Helper');
    }

    /**
     * Initialize view
     *
     * @return void
     */
    public function initView()
    {
		// Bootstrap layouts
		Zend_Layout::startMvc(array(
		    'layoutPath' => $this->_root .  '/application/default/layouts',
		    'layout' => 'main'
		));



    }

    /**
     * Initialize plugins
     *
     * @return void
     */
    public function initPlugins()
    {
    	$this->_front->registerPlugin(new UGD_Login_Plugin());
    	
    	$layoutController = new Util_Layout();
		$layoutController->registerModuleLayout('admin',$this->_root .  '/application/admin/layouts','admin');

		$this->_front->registerPlugin($layoutController,-999);
    }

    /**
     * Initialize routes
     *
     * @return void
     */
    public function initRoutes()
    {
    	$router = $this->_front->getRouter();
    	$router->addRoute('group',new Zend_Controller_Router_Route("/group/view/:id",array("module"=>'default', 'controller'=>'group', 'action'=>'view')));
    }

    /**
     * Initialize Controller paths
     *
     * @return void
     */
    public function initControllers()
    {
    	$this->_front->addControllerDirectory($this->_root . '/application/default/controllers', 'default');
		$this->_front->addControllerDirectory($this->_root . '/application/admin/controllers', 'admin');
    }

    public function initLogger(){
    	if (PHP_SAPI == "cli"){
    		Util_Log::loadLogger($this->_env."_cli");
    	}else{
    		Util_Log::loadLogger($this->_env."web");
    	}
    }

    public function initI18N(){

    	$translate = new Zend_Translate('gettext',$this->_root.'/i18n/');
    	Zend_Registry::set('Zend_Translate', $translate);

    }

    public function initAppCfg(){
    	define(PRODUCT_NAME,$this->_config->app->name);
    }
}
?>