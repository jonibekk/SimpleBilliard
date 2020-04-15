<?php
App::uses('LatestUserConfirmCircle', 'Model');

/**
 * LatestUserConfirmCircle Test Case
 */
class LatestUserConfirmCircleTest extends CakeTestCase {

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'app.latest_user_confirm_circle',
	);

	/**
	 * setUp method
 	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->LatestUserConfirmCircle = ClassRegistry::init('LatestUserConfirmCircle');
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->LatestUserConfirmCircle);

		parent::tearDown();
	}


	public function test_getLatestUserConfirmCircleId_success() {

		$existUserId = 1;
		$existTeamId = 2;

		$notExistUserId = 2;
		$notExistTeamId = 2;

		// search exist record
		$res1 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircleId($existUserId, $existTeamId);
		$this->assertEqual(1, $res1);

		// search doesn't exist record
		$res2 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircleId($notExistUserId, $notExistTeamId);
		$this->assertFalse($res2);

	}

	public function test_add_success() {

		$userId = 1;
		$teamId = 3;
		$circleId = 1;

		// search doesn't exist record
		$res1 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircleId($userId, $teamId);
		$this->assertFalse($res1);

		// add new record
		$res2 = $this->LatestUserConfirmCircle->add($userId, $teamId, $circleId);
		$this->assertEqual(5, $res2);

		// search new record
		$res3 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircleId($userId, $teamId);
		$this->assertEqual(1, $res3);
	}


	public function test_update_success() {

		$userId = 1;
		$teamId = 1;
		$circleId = 2;

		// search record
		$res1 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircleId($userId, $teamId);
		$this->assertEqual(1, $res1);

		// update record
		$res2 = $this->LatestUserConfirmCircle->update($userId, $teamId, $circleId);
		$this->assertFalse(!$res2);

		// search updated record
		$res3 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircleId($userId, $teamId);
		$this->assertEqual(2, $res3);
	}
}
