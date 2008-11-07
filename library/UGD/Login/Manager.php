<?php

/**
 *
 *
 * @author
 * @version
 */

class UGD_Login_Manager
{
	private static $instance;
	/**
	 * Enter description here...
	 *
	 * @var UGD_Login_Module_Interface
	 */
	private $_loginModule;
	private $lastErrorCode;
	private $_request;


	/**
	 * Return Singleton
	 *
	 * @return UGD_Login_Manager
	 */
	public static function getInstance(){

		if (!self::$instance instanceof UGD_Login_Manager ){
			self::$instance = new UGD_Login_Manager();
		}

		return self::$instance;
	}

	public function __construct(){
		$this->loadLoginModule();
	}

	public function loadLoginModule(){
		$module = "UGD_Login_Module_".ucfirst(Zend_Registry::get('config')->login->module->name);

		$this->_loginModule = new $module($this);
	}

	public function requireAuthentication(){

		if (!$this->_loginModule->isAuthenticated()){

			$this->_loginModule->showLoginForm();

		}else{
			$this->_loginModule->getActiveUser();
			return true;

		}

		return false;
	}

	public function checkAuth(){

//		$this->setLastErrorCode("NotAllowed");
	}

	/**
	 * Enter description here...
	 *
	 * @return Zend_Controller_Request_Abstract
	 */
	public function getRequest(){
		return $this->_request;
	}

	public function setRequest($request){
		$this->_request = $request;
	}

	/**
	 * @return unknown
	 */
	public function getLastErrorCode() {
		return $this->lastErrorCode;
	}

	/**
	 * @param unknown_type $lastErrorCode
	 */
	public function setLastErrorCode($lastErrorCode) {
		$this->lastErrorCode = $lastErrorCode;
	}

	public  function getActiveUser(){
		return $this->_loginModule->getActiveUser();
	}

	public  function getActiveUserID(){
		return $this->_loginModule->getActiveUserID();
	}

	public function logout($url="/"){
		$this->_loginModule->logout($url);
	}

	public function showLoginScreen(){
		$this->_loginModule->showLoginForm();
	}
}
?>