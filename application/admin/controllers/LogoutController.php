<?php

/**
 * LoginController
 *
 * @author Rafael Dohms <rdohms@gmail.com>
 * @version
 */

require_once 'Zend/Controller/Action.php';

class Admin_LogoutController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		UGD_Login_Manager::getInstance()->logout( $this->view->url(array("module"=>"default","controller"=>"index")) );
	}

}
?>