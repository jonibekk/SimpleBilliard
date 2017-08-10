<?php
App::uses('GoalousTestCase', 'Test');
App::uses('CreditCard', 'Model');

/**
 * CreditCard Test Case
 */
class CreditCardTest extends GoalousTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.credit_card',
        'app.payment_setting',
        'app.payment_setting_change_log',
        'app.charge_history',
        'app.team',
        'app.team_member',
        'app.user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->CreditCard = ClassRegistry::init('CreditCard');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->CreditCard);

		parent::tearDown();
	}

	public function test_getCustomerCode()
	{
		list($teamId, $paymentSettingId) = $this->createCcPaidTeam([], [], ['customer_code' => $customerCode = '111222333aaa']);
		$this->assertEqual($this->CreditCard->getCustomerCode($teamId), $customerCode);
	}
}
