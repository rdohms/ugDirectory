<?php

/**
 * LoginController
 *
 * @author Rafael Dohms <rdohms@gmail.com>
 * @version
 */

require_once 'Zend/Controller/Action.php';

class Admin_LoginController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		UGD_Login_Manager::getInstance()->showLoginScreen();
	}

	public function __call($name,$args){

		$this->view->assign("errorCode",str_replace("Action","",$name));

		return $this->render("error");
	}

}
?>

