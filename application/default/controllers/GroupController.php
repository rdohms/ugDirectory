<?php

/**
 * GroupController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class GroupController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->_forward('view',null,null,array('id'=>$this->_request->getParam('id')));		
	}
	
	public function viewAction(){
		
		$group = Doctrine::getTable('Group')->findOneById($this->_request->getParam('id'));
		$this->view->group = $group;
		
	}
	
	public function __call($method,$args){
		
		$id = $this->_request->getActionName();
		$this->_forward('view',null,null,array('id'=>$id));
		
	}

}
?>

