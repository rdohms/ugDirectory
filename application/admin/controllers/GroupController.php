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
	 * Zend_Form
	 *
	 * @var Zend_Form
	 */
	public $gForm;

	/**
	 * Zend_Form
	 *
	 * @var Zend_Form
	 */
	
	public $vForm;
	
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->_forward('list');
	}

	public function addAction(){

		$this->buildForm("admin/group/save");
		
		$this->gForm->getElement('tmp_id')->setValue(Util_Guid::generate());
		$this->gForm->getElement('url')->setValue('http://');
		$this->gForm->getElement('user_responsible')->setValue(UGD_Login_Manager::getInstance()->getActiveUser()->getId());
	
	}

	public function saveAction(){
		
		$this->getRequest()->isPost();
		$this->buildForm("admin/group/save");
		
		if ($this->gForm->isValid($_POST)){
			$values = $this->gForm->getValues();
		}

		//Build Group Object
		$group = new Group();
		$group->fromArray($values);
		
		
		//Upload Logo
		if ( copy($values['logo'],Zend_Registry::get('config')->files->logo->dir. DIRECTORY_SEPARATOR . basename($values['logo'])) ){
			$values['logo'] = Zend_Registry::get('config')->files->logo->dir. "/" . basename($values['logo']);
		}
		
		//Convert polygon cordenates
		preg_match_all("/\(([0-9\.\,\-\s]*)\)[\,]?/",$values['area_coords'],$rawcoords);
		
		foreach($rawcoords[1] as $coord){
			$cvalues = explode(",",$coord);
			$cobj = new stdClass();
			$cobj->lat = trim($cvalues[0]);
			$cobj->lng = trim($cvalues[1]);
			
			$coords[] = $cobj;
		}
		
		$values['area_coords'] = json_encode($coords);
		
		//Register Administrators
		$admins = explode(",",$values['admins']);
		
		foreach($admins as $adm){
			if ($adm != ""){
				$group->Admins[]->user_id = $adm;
			}
		}
		
		//Go through Activity Types
		$actvTypes = Doctrine_Query::create()->from('ActivityType')->orderBy("weight")->execute();

		foreach($actvTypes as $atype){
			$atype_key = "atype_".$atype->atype;
			
			$aSource = new ActivitySource();
			$aSource->atype = $atype->atype;
			$aSource->target = $values[$atype_key];
			
			$group->ActivitySources[] = $aSource;
		}		
		
		//Save Group
		$group->save();
		
		//Grab pre-saved venues and tie group_id
		$venues = Doctrine_Query::create()->from('Venue')->where("Venue.name LIKE ?",array($values['tmp_id'] . '%'))->execute();
		
		foreach ($venues as $venue){
			
			$venue->name = str_replace($values['tmp_id']."_","",$venue->name);
			$venue->group_id = $group->id;
			$venue->save();
			
		}
		
		$this->_helper->flashMessenger("Group added!");
		
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		
	}

	public function editAction(){
			
		$this->_helper->flashMessenger("Group added!");
		$this->_helper->redirector('index');
	}

	public function viewAction(){

	}

	public function listAction(){
				$this->_helper->viewRenderer->setNoRender();
	}
	
	
	private function buildForm($action){

		$gform = new Zend_Form();
		$gform->setAction($action)
			  ->setMethod("POST");
		
		//Temp ID
		$gform->addElement('hidden','tmp_id',array());
		
		$gform->addElement('text','name',array("label"=>"Group Name","required"=>true));
		$gform->addElement('textarea','description',array("label"=>"Description","rows"=>5));
		$gform->addElement('file','logo',array("label"=>"Logo"));
		$gform->addElement('text','url',array("label"=>"Site"));
		$gform->addElement('hidden','area_coords',array());
		$gform->addElement('hidden','user_responsible',array());
		$gform->addElement('hidden','admins',array());
		
		//Get Users
		$users = Doctrine_Query::create()->from('User')->orderBy('name')->execute();
		foreach ($users as $user) {
			$aUsers[$user->id] = $user->name;
		}
		
		
		$gform->addElement('select','admins_add',array("label"=>"Group Manager(s)","multiOptions"=>$aUsers));
		$gform->addElement('text','scope',array("label"=>"Verbose area location"));

		//get AtivityTypeList
		$actvTypes = Doctrine_Query::create()->from('ActivityType')->orderBy("weight")->execute();

		foreach($actvTypes as $atype){
			$gform->addElement('text',"atype_".$atype->atype,array("label"=>$atype->name));
			$atypes[] = "atype_".$atype->atype;
		}
		
		$gform->addElement('submit','submit',array("label"=>"Register"));
		
		$gform->addDisplayGroup($atypes,"actv");
		$gform->addDisplayGroup(array('name','logo','url','description'),'gpi');
		$gform->addDisplayGroup(array('user_responsible','admins'),'mgi');
		$gform->addDisplayGroup(array('scope','venues'),'lci');
		$this->view->assign("form",$gform);

		$this->gForm = $gform;
		
		//Venue icons
		$files = scandir('../public/images/admin/map_icons/');
		foreach ($files as $icon){
			
			if ($icon != "." && $icon != ".." && strpos($icon,"_s.") === false){
				$icons[basename($icon)] = "<img src='images/admin/map_icons/".basename($icon)."' />";
			}
		}
		
		//Venue Form
		$vForm = new Zend_Form();
		$vForm->setAttrib("id","vn_form")->setMethod("POST")->setAction("")->setName("venue_form");
		$vForm->addElement('text','vn_name',array('label'=>"Venue Name","require"=>true));
		$vForm->addElement('textarea','vn_address',array('label'=>"Address","require"=>true,"rows"=>4));
		$vForm->addElement('textarea','vn_description',array('label'=>"Description","rows"=>3));
		$vForm->addElement('radio','vn_icon',array('label'=>"Icon","multiOptions"=>$icons,"escape"=>false,"separator" => "  "));
		$vForm->addElement('hidden','vn_coords',array());
		$this->view->assign("vForm",$vForm);
		
		$this->vForm = $vForm;
		
	}
}
