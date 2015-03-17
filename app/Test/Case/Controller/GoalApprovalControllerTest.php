<?php
App::uses('GoalApprovalController', 'Controller');

/**
 * GoalApprovalController Test Case

 */
class GoalApprovalControllerTest extends ControllerTestCase
{

	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array(
		'app.user',
		'app.team',
		'app.badge',
		'app.comment_like',
		'app.comment',
		'app.post',
		'app.goal',
		'app.purpose',
		'app.goal_category',
		'app.key_result',
		'app.action_result',
		'app.collaborator',
		'app.follower',
		'app.post_share_user',
		'app.post_share_circle',
		'app.circle',
		'app.circle_member',
		'app.post_like',
		'app.post_read',
		'app.comment_mention',
		'app.given_badge',
		'app.post_mention',
		'app.comment_read',
		'app.group',
		'app.member_group',
		'app.invite',
		'app.job_category',
		'app.team_member',
		'app.member_type',
		'app.notification',
		'app.notify_to_user',
		'app.notify_from_user',
		'app.thread',
		'app.message',
		'app.evaluator',
		'app.evaluation_setting',
		'app.email',
		'app.notify_setting',
		'app.oauth_token',
		'app.local_name'
	);

	function testIndex()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();

		$team_id = 1;
		$params = [
			'first_name' => 'test',
			'last_name'  => 'test'
		];
		$GoalApproval->Collaborator->User->save($params);
		$user_id = $GoalApproval->Collaborator->User->getLastInsertID();

		$params = [
			'user_id' => $user_id,
			'team_id' => $team_id,
			'name'    => 'test'
		];
		$GoalApproval->Collaborator->Goal->Purpose->save($params);
		$purpose_id = $GoalApproval->Collaborator->Goal->Purpose->getLastInsertID();

		$params = [
			'user_id'          => $user_id,
			'team_id'          => $team_id,
			'purpose_id'       => $purpose_id,
			'name'             => 'test',
			'goal_category_id' => 1,
			'end_date'         => '1427813999',
			'photo_file_name'  => 'aa.png'
		];
		$GoalApproval->Collaborator->Goal->save($params);
		$goal_id = $GoalApproval->Collaborator->Goal->getLastInsertID();

		$valued_flg = 0;
		$params = [
			'user_id'    => $user_id,
			'team_id'    => $team_id,
			'goal_id'    => $goal_id,
			'valued_flg' => $valued_flg,
			'type'       => 0,
			'priority'   => 1,
		];
		$GoalApproval->Collaborator->save($params);

		$GoalApproval->user_id = $user_id;
		$this->testAction('/goal_approval/index', ['method' => 'GET']);
	}

	function testDone()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();

		$team_id = 1;
		$params = [
			'first_name' => 'test',
			'last_name'  => 'test'
		];
		$GoalApproval->Collaborator->User->save($params);
		$user_id = $GoalApproval->Collaborator->User->getLastInsertID();

		$params = [
			'user_id' => $user_id,
			'team_id' => $team_id,
			'name'    => 'test'
		];
		$GoalApproval->Collaborator->Goal->Purpose->save($params);
		$purpose_id = $GoalApproval->Collaborator->Goal->Purpose->getLastInsertID();

		$params = [
			'user_id'          => $user_id,
			'team_id'          => $team_id,
			'purpose_id'       => $purpose_id,
			'name'             => 'test',
			'goal_category_id' => 1,
			'end_date'         => '1427813999',
			'photo_file_name'  => 'aa.png'
		];
		$GoalApproval->Collaborator->Goal->save($params);
		$goal_id = $GoalApproval->Collaborator->Goal->getLastInsertID();

		$valued_flg = 1;
		$params = [
			'user_id'    => $user_id,
			'team_id'    => $team_id,
			'goal_id'    => $goal_id,
			'valued_flg' => $valued_flg,
			'type'       => 0,
			'priority'   => 1,
		];
		$GoalApproval->Collaborator->save($params);

		$GoalApproval->user_id = $user_id;
		$this->testAction('/goal_approval/done', ['method' => 'GET']);
	}

	function testApproval()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$params = [
			'user_id'    => 999,
			'team_id'    => 888,
			'goal_id'    => 777,
			'valued_flg' => 0,
		];
		$GoalApproval->Collaborator->save($params);
		$id = $GoalApproval->Collaborator->getLastInsertID();
		$this->testAction('/goal_approval/approval/'. $id, ['method' => 'GET']);
	}

	function testWait()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$params = [
			'user_id'    => 999,
			'team_id'    => 888,
			'goal_id'    => 777,
			'valued_flg' => 0,
		];
		$GoalApproval->Collaborator->save($params);
		$id = $GoalApproval->Collaborator->getLastInsertID();
		$this->testAction('/goal_approval/wait/'. $id, ['method' => 'GET']);
	}

	function testSetCoachFlagTrue()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$params = [
			'user_id'       => 999,
			'team_id'       => 888,
			'coach_user_id' => 777,
		];
		$GoalApproval->TeamMember->save($params);
		$GoalApproval->setCoachFlag(999, 888);
		$this->assertTrue($GoalApproval->coach_flag);
	}

	function testSetMemberFlag()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$params = [
			'user_id'       => 999,
			'team_id'       => 888,
			'coach_user_id' => 777,
		];
		$GoalApproval->TeamMember->save($params);
		$GoalApproval->setMemberFlag(777, 888);
		$this->assertTrue($GoalApproval->member_flag);
	}

	function testGetUserTypeReturn1()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$GoalApproval->coach_flag = true;
		$GoalApproval->member_flag = false;
		$type = $GoalApproval->getUserType();
		$this->assertEquals(1, $type);
	}

	function testGetUserTypeReturn2()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$GoalApproval->coach_flag = true;
		$GoalApproval->member_flag = true;
		$type = $GoalApproval->getUserType();
		$this->assertEquals(2, $type);
	}

	function testGetUserTypeReturn3()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$GoalApproval->coach_flag = false;
		$GoalApproval->member_flag = true;
		$type = $GoalApproval->getUserType();
		$this->assertEquals(3, $type);
	}

	function testGetUserTypeReturn0()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$GoalApproval->coach_flag = false;
		$GoalApproval->member_flag = false;
		$type = $GoalApproval->getUserType();
		$this->assertEquals(0, $type);
	}

	function testGetCollaboratorUserIdType1()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$GoalApproval->user_type = 1;
		$GoalApproval->user_id = 999;
		$GoalApproval->member_ids = [888, 777];
		$goal_user_id = $GoalApproval->getCollaboratorUserId();
		$this->assertContains($GoalApproval->user_id, $goal_user_id);
	}

	function testGetCollaboratorUserIdType2()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$GoalApproval->user_type = 2;

		$user_id = 999;
		$member_id = [888, 777];
		$ids = array_merge([$user_id], $member_id);

		$GoalApproval->user_id = $user_id;
		$GoalApproval->member_ids = $member_id;
		$goal_user_id = $GoalApproval->getCollaboratorUserId();
		$this->assertEquals($ids, $goal_user_id);

	}

	function testGetCollaboratorUserIdType3()
	{
		$GoalApproval = $this->_getGoalApprovalCommonMock();
		$GoalApproval->user_type = 3;

		$user_id = 999;
		$member_id = [888, 777];

		$GoalApproval->user_id = $user_id;
		$GoalApproval->member_ids = $member_id;

		$goal_user_id = $GoalApproval->getCollaboratorUserId();
		$this->assertEquals($member_id, $goal_user_id);

	}

	function _getGoalApprovalCommonMock()
	{
		/**
		 * @var GoalApprovalController $GoalApproval
		 */
		$GoalApproval = $this->generate('GoalApproval', [
			'components' => [
				'Session',
				'Auth'     => ['user', 'loggedIn'],
				'Security' => ['_validateCsrf', '_validatePost'],
				'Ogp',
				'NotifyBiz',
				'GlEmail',
			],
		]);

		$value_map = [
			[null, [
				'id'         => '1',
				'last_first' => true,
				'language'   => 'jpn'
			]],
			['id', '1'],
			['language', 'jpn'],
			['auto_language_flg', true],
		];
		/** @noinspection PhpUndefinedMethodInspection */
		$GoalApproval->Security
			->expects($this->any())
			->method('_validateCsrf')
			->will($this->returnValue(true));
		/** @noinspection PhpUndefinedMethodInspection */
		$GoalApproval->Security
			->expects($this->any())
			->method('_validatePost')
			->will($this->returnValue(true));

		/** @noinspection PhpUndefinedMethodInspection */
		$GoalApproval->Auth->expects($this->any())->method('loggedIn')
						   ->will($this->returnValue(true));
		/** @noinspection PhpUndefinedMethodInspection */
		$GoalApproval->Auth->staticExpects($this->any())->method('user')
						   ->will($this->returnValueMap($value_map)
						   );
		/** @noinspection PhpUndefinedMethodInspection */
		$GoalApproval->Session->expects($this->any())->method('read')
							  ->will($this->returnValueMap([['current_team_id', 1]]));

		$GoalApproval->Goal->Team->my_uid = 1;
		$GoalApproval->Goal->Team->current_team_id = 1;
		$GoalApproval->Goal->Team->current_team = [
			'Team' => [
				'start_term_month' => 4,
				'border_months'    => 6
			]
		];
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->my_uid = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->current_team_id = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->Team->TeamMember->my_uid = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->Team->TeamMember->current_team_id = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->ActionResult->my_uid = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->ActionResult->current_team_id = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->GoalCategory->my_uid = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->GoalCategory->current_team_id = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->KeyResult->my_uid = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->KeyResult->current_team_id = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->Collaborator->my_uid = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->Collaborator->current_team_id = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->Follower->my_uid = '1';
		/** @noinspection PhpUndefinedFieldInspection */
		$GoalApproval->Goal->Follower->current_team_id = '1';
		$GoalApproval->Goal->Post->my_uid = '1';
		$GoalApproval->Goal->Post->current_team_id = '1';

		$this->current_date = strtotime('2015/7/1');
		$this->start_date = strtotime('2015/7/1');
		$this->end_date = strtotime('2015/10/1');

		$GoalApproval->Goal->Team->current_term_start_date = strtotime('2015/1/1');
		$GoalApproval->Goal->Team->current_term_end_date = strtotime('2015/12/1');

		return $GoalApproval;
	}
}

