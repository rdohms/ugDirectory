<?php

/**
 * ActivityMonitorController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class Cli_ActivityMonitorController extends Zend_Controller_Action {
	
	
    public function init()
    {
 		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
    }
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		
		echo "usage: -a manager\n";
		
		
		
	}
	
	public function managerAction() {
		
		$manager = new UGD_ActivityMonitor_Manager();
		$manager->init();
		
	}

}
?>

