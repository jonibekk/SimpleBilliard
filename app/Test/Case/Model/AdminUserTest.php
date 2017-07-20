<?php
App::uses('AdminUser', 'Model');

/**
 * AdminUser Test Case
 */
class AdminUserTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.admin_user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AdminUser = ClassRegistry::init('AdminUser');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AdminUser);

		parent::tearDown();
	}

	// Please delete when you implement test code
	public function test_dummy() {}
}
