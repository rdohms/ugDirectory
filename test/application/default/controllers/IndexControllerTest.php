<?
/**
 * IndexControllerTest - Test the default index controller
 * 
 * @author Rafael Dohms <rdohms@gmail.com>
 * @version 
 */

/**
 * IndexController Test Case
 */
class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase {

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		$this->bootstrap = array ($this, 'appBootstrap' );
		parent::setUp ();
		// TODO Auto-generated FooControllerTest::setUp()		
	}

	/**
	 * Prepares the environment before running a test.
	 */
	public function appBootstrap() {
		$this->frontController->registerPlugin ( new UGD_initializer( CONFIG_TEST_ENV ) );
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated FooControllerTest::tearDown()
		parent::tearDown ();
	}
	
	/**
	 * Tests FooController->barAction()
	 */
	public function testIndexAction() {
		// TODO Auto-generated FooControllerTest->testBarAction()
		$this->dispatch ( '/index/index' );
		$this->assertController ( 'index' );
		$this->assertAction ( 'index' );
	}
}
?>
