<?php

/**
 *
 *
 * @author Rafael Dohms <rdohms@gmail.com>
 */

class UGD_Login_Module_Wp implements UGD_Login_Module_Interface
{

	/**
	 * UGD_Login_Manager
	 *
	 * @var UGD_Login_Manager
	 */
	private $_manager;
	private $request;
	private $controller;

	public function __construct(&$manager){
		$this->_manager = $manager;
		set_include_path( Zend_Registry::get('config')->login->module->wp->wpPath . PATH_SEPARATOR . get_include_path() );
		global $wpdb;
		@require_once('wp-config.php');

	}

	/**
	 * @see UGD_Login_Module_Interface::getActiveUser()
	 *
	 */
	public function getActiveUser() {
		$wpuser = wp_get_current_user();

		//Find User in LocalDB
		$user = Doctrine::getTable('User')->findOneById($wpuser->ID);

		if ($user === false) {

			$user = new User();
			$user->id = $wpuser->ID;
			$user->login = $wpuser->data->user_login;
			$user->name = $wpuser->data->user_firstname . " " .$wpuser->data->user_lastname;
			$user->nick = $wpuser->data->nickname;
			$user->email = $wpuser->data->user_email;
			$user->url = $wpuser->data->user_url;
			$user->bio = $wpuser->user_description;
			$user->level = $wpuser->data->wp_user_level;
			$user->save();

		}

		return $user;
	}

	/**
	 * @see UGD_Login_Module_Interface::getActiveUserID()
	 *
	 */
	public function getActiveUserID() {
		$wpuser = wp_get_current_user();
		return $wpuser->ID;
	}

	/**
	 * @see UGD_Login_Module_Interface::isAuthenticated()
	 *
	 */
	public function isAuthenticated() {
		return wp_validate_auth_cookie();
	}

	/**
	 * @see UGD_Login_Module_Interface::logWithCredentials()
	 *
	 * @param unknown_type $username
	 * @param unknown_type $passwd
	 */
	public function logWithCredentials($username, $passwd) {
	}

	/**
	 * @see UGD_Login_Module_Interface::showLoginForm()
	 *
	 */
	public function showLoginForm() {
		$url = $this->_manager->getRequest()->getRequestUri();
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		$redirector->gotoUrl( Zend_Registry::get('config')->login->module->wp->urlLogin."?redirect_to=".urlencode($url) );
	}

	/**
	 * @see UGD_Login_Module_Interface::logout()
	 *
	 */
	public function logout($url) {
		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
		$redirector->gotoUrl( Zend_Registry::get('config')->login->module->wp->urlLogin."?action=logout&redirect_to=".urlencode($url) );
	}


}
?>