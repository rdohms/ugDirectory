<?php

/**
 * Admin_IndexController test case.
 */
class Admin_IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->bootstrap = array ($this, 'appBootstrap' );
		parent::setUp ();
		
	// TODO Auto-generated Admin_IndexControllerTest::setUp()	
	}
	
	/**
	 * Prepares the environment before running a test.
	 */
	public function appBootstrap() {
		$this->frontController->registerPlugin ( new UGD_initializer ( CONFIG_TEST_ENV ) );
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated Admin_IndexControllerTest::tearDown()
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Admin_IndexController->indexAction()
	 */
	public function testIndexAction() {
		// TODO Auto-generated Admin_IndexControllerTest->testIndexAction()				
		
		$this->dispatch ( '/admin/index' );
		$this->assertController ( 'index' );
		$this->assertAction ( 'index' );
	
	}

}

