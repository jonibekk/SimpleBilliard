<?php
App::uses('PaymentSettingChangeLog', 'Model');

/**
 * PaymentSettingChangeLog Test Case
 */
class PaymentSettingChangeLogTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.payment_setting_change_log'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PaymentSettingChangeLog = ClassRegistry::init('PaymentSettingChangeLog');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->PaymentSettingChangeLog);

		parent::tearDown();
	}

	// Please delete when you implement test code
	public function test_dummy() {}
}
