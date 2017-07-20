<?php
App::uses('PaymentSetting', 'Model');

/**
 * PaymentSetting Test Case
 */
class PaymentSettingTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.payment_setting'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->PaymentSetting = ClassRegistry::init('PaymentSetting');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->PaymentSetting);

		parent::tearDown();
	}

}
