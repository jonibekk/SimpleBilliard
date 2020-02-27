<?php
App::uses('CheckedCircle', 'Model');

/**
 * CheckedCircle Test Case
 */
class CheckedCircleTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'app.checked_circle',
	);

	/**
	 * setUp method
 	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->CheckedCircle = ClassRegistry::init('CheckedCircle');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->CheckedCircle);

		parent::tearDown();
	}


	public function test_getCheckedCircle_success() {

		$userId = 1;
		$teamId = 1;
		$circleId1 = 1;
		$circleId2 = 999;

		// search exist record
		$res1 = $this->CheckedCircle->getCheckedCircle($userId, $teamId, $circleId1);
		$this->assertCount(1, $res1);

		// search doesn't exist record
		$res2 = $this->CheckedCircle->getCheckedCircle($userId, $teamId, $circleId2);
		$this->assertFalse($res2);

	}

	public function test_add_success() {

		$userId = 1;
		$teamId = 1;
		$circleId = 3;

		// search doesn't exist record
		$res1 = $this->CheckedCircle->getCheckedCircle($userId, $teamId, $circleId);
		$this->assertFalse($res1);

		// add new record
		$res2 = $this->CheckedCircle->add($userId, $teamId, $circleId);
		$this->assertEqual(3, $res2);

		// search new record
		$res3 = $this->CheckedCircle->getCheckedCircle($userId, $teamId, $circleId);
		$this->assertCount(1, $res3);
	}

	public function test_isExistUncheckedCircle_true() {

		$userId = 1;
		$teamId = 1;
		$circleIds = ["1"];

		$res1 = $this->CheckedCircle->isExistUncheckedCircle($userId, $teamId, $circleIds);

		$this->assertTrue($res1);
	}

	public function test_isExistUncheckedCircle_false() {

		$userId = 1;
		$teamId = 1;
		$circleIds = ["1", "2"];

		$res1 = $this->CheckedCircle->isExistUncheckedCircle($userId, $teamId, $circleIds);

		$this->assertFalse($res1);
	}

}
