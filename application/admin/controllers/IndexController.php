<?php

/**
 * IndexController - The default controller class
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Action.php';

class Admin_IndexController extends Zend_Controller_Action
{
	/**
	 * The default action - show the home page
	 */
    public function indexAction()
    {
    	$user = UGD_Login_Manager::getInstance()->getActiveUser();
    	$this->view->assign('user',$user);

    }

}