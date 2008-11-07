<?php

/**
 * GroupController
 *
 * @author
 * @version
 */

require_once 'Zend/Controller/Action.php';

class Admin_GroupController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->_forward('list');
	}

	public function addAction(){
		$gform = new Zend_Form();
		$gform->setAction("admin/group/save")
			  ->setMethod("POST");

		$gform->addElement('text','name',array("label"=>"Group Name","required"=>true));
		$gform->addElement('textarea','description',array("label"=>"Description","rows"=>5));
		$gform->addElement('file','logo',array("label"=>"Logo"));
		$gform->addElement('text','url',array("label"=>"Site"));
		$gform->addElement('hidden','area_coords',array());
		$gform->addElement('text','user_responsible',array("label"=>"Responsible User","value"=>UGD_Login_Manager::getInstance()->getActiveUser()->getName()));
		$gform->addElement('text','admins',array("label"=>"Group Manager(s)"));
		$gform->addElement('text','venues',array("label"=>"Meeting Points / HQ"));
		$gform->addElement('text','scope',array("label"=>"Verbose area location"));

		$gform->addDisplayGroup(array('name','logo','url','description'),'gpi');
		$gform->addDisplayGroup(array('user_responsible','admins'),'mgi');
		$gform->addDisplayGroup(array('scope','venues'),'lci');

		$this->view->assign("form",$gform);
	}

	public function saveAction(){

	}

	public function editAction(){

	}

	public function viewAction(){

	}

	public function listAction(){

	}

}
?>

