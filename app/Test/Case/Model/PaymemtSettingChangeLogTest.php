<?php
App::uses('PaymemtSettingChangeLog', 'Model');

/**
 * PaymemtSettingChangeLog Test Case
 */
class PaymemtSettingChangeLogTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.paymemt_setting_change_log'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PaymemtSettingChangeLog = ClassRegistry::init('PaymemtSettingChangeLog');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->PaymemtSettingChangeLog);

		parent::tearDown();
	}

	// Please delete when you implement test code
	public function test_dummy() {}
}
