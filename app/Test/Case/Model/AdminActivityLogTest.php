<?php
App::uses('AdminActivityLog', 'Model');

/**
 * AdminActivityLog Test Case
 */
class AdminActivityLogTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.admin_activity_log'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AdminActivityLog = ClassRegistry::init('AdminActivityLog');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AdminActivityLog);

		parent::tearDown();
	}

	// Please delete when you implement test code
	public function test_dummy() {}

}
