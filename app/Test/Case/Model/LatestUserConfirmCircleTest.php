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


	public function test_getLatestUserConfirmCircle_success() {

		$existUserId = 1;
		$existTeamId = 2;

		$notExistUserId = 2;
		$notExistTeamId = 2;

		// search exist record
		$res1 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircle($existUserId, $existTeamId);
		$this->assertCount(1, $res1);

		// search doesn't exist record
		$res2 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircle($notExistUserId, $notExistTeamId);
		$this->assertFalse($res2);

	}

	public function test_add_success() {

		$userId = 1;
		$teamId = 3;

		// search doesn't exist record
		$res1 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircle($userId, $teamId);
		$this->assertFalse($res1);

		// add new record
		$res2 = $this->LatestUserConfirmCircle->add($userId, $teamId);
		$this->assertEqual(5, $res2);

		// search new record
		$res3 = $this->LatestUserConfirmCircle->getLatestUserConfirmCircle($userId, $teamId);
		$this->assertCount(1, $res3);
	}

	public function test_deleteByTeamId_success() {
		$teamId = 1;
		$userIds = [1, 2];

		$res1 = $this->LatestUserConfirmCircle->find('all',[
				'conditions' => [
					'team_id' => $teamId,
					'del_flg' => false
				]
			]
		);
		$this->assertCount(3, $res1);

		$res2 = $this->LatestUserConfirmCircle->deleteByTeamIdWithoutMembers($teamId, $userIds);
		$this->assertTrue($res2);

		$res3 = $this->LatestUserConfirmCircle->find('all',[
				'conditions' => [
					'team_id' => $teamId,
					'del_flg' => false
				]
			]
		);
		$this->assertCount(2, $res3);
	}
}
