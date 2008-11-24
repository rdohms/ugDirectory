<?php

/**
 * venueController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';

class Admin_VenueController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		// TODO Auto-generated venueController::indexAction() default action
	}
	
	public function addAction(){
		
		$params = $this->getRequest()->getParams();

		$coords = explode("/",$params['vn_coords']);
		$point = new stdClass();
		$point->lat = array_shift($coords);
		$point->lng = array_shift($coords);
		
		$venue = new Venue();
		$venue->group_id = NULL;
		$venue->name = $params['gid']."_".$params['vn_name'];
		$venue->address = utf8_decode($params['vn_address']);
		$venue->coords = $this->_helper->json->encodeJson($point);
		$venue->description = $params['vn_description'];
		$venue->icon = $params['vn_icon'];
		
		try{
			$venue->save();
			$this->_helper->json->sendJson( array("msg"=> Zend_Registry::get('Zend_Translate')->_("Venue added!"))  );
		}catch (Exception $e){
			Util_Log::get()->DataAccess()->err("Doctrine Save Error: ".$e->getMessage());
			$this->_helper->json->sendJson( array("msg"=> Zend_Registry::get('Zend_Translate')->_("Error adding Venue"))  );
		}
		
	}

}
?>