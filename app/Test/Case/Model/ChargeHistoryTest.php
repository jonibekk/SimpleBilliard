<?php
App::uses('ChargeHistory', 'Model');

/**
 * ChargeHistory Test Case
 */
class ChargeHistoryTest extends CakeTestCase {

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

}
