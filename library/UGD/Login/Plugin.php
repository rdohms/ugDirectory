<?php

/**
 *
 *
 * @author Rafael Dohms <rdohms@gmail.com>
 */

class UGD_Login_Plugin extends Zend_Controller_Plugin_Abstract
{

	/**
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		$module = $request->getModuleName();
		$controller = $request->getControllerName();

		$lgMngr = UGD_Login_Manager::getInstance();

		if ($module == 'admin' && $controller != "logout"){

			//Force Login
			$lgMngr->setRequest($request);

			if (!$lgMngr->requireAuthentication()){

				if (!$lgMngr->checkAuth()){
					$request->setControllerName('login');
					$request->setActionName( $lgMngr->getLastErrorCode() );
				}

			}

		}

		//globalize logged in user
		Zend_Layout::getMvcInstance()->assign('lUser',$lgMngr->getActiveUser());

	}

}
?>