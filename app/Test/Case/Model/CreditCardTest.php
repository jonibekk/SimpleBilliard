<?php
App::uses('CreditCard', 'Model');

/**
 * CreditCard Test Case
 */
class CreditCardTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.credit_card'
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

	// Please delete when you implement test code
	public function test_dummy() {}
}
