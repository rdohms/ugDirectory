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
		
	
	}

	public function saveAction(){
		
		$this->buildForm();
		
		var_dump($this->gForm->getValues());
		
	}

	public function editAction(){

	}

	public function viewAction(){

	}

	public function listAction(){

	}
	
	
	private function buildForm($action){

		$gform = new Zend_Form();
		$gform->setAction($action)
			  ->setMethod("POST");
		
		//Temp ID
		$gform->addElement('hidden','tmp_id',array("value"=>Util_Guid::generate()));
		
		$gform->addElement('text','name',array("label"=>"Group Name","required"=>true));
		$gform->addElement('textarea','description',array("label"=>"Description","rows"=>5));
		$gform->addElement('file','logo',array("label"=>"Logo"));
		$gform->addElement('text','url',array("label"=>"Site","value"=>"http://"));
		$gform->addElement('hidden','area_coords',array());
		$gform->addElement('hidden','user_responsible',array("value"=>UGD_Login_Manager::getInstance()->getActiveUser()->getId()));
		
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
?>

