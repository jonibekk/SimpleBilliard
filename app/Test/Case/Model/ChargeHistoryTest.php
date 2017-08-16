<?php App::uses('GoalousTestCase', 'Test');
App::uses('ChargeHistory', 'Model');

/**
 * ChargeHistory Test Case
 *
 * @property ChargeHistory $ChargeHistory
 */
class ChargeHistoryTest extends GoalousTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.charge_history'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ChargeHistory = ClassRegistry::init('ChargeHistory');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ChargeHistory);

		parent::tearDown();
	}

	// Please delete when you implement test code
	public function test_dummy() {}
}
