<?php
App::uses('Collaborator', 'Model');

/**
 * Collaborator Test Case
 *
 * @property Collaborator $Collaborator
 */
class CollaboratorTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.collaborator',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',
        'app.purpose',
        'app.goal_category',
        'app.key_result',
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

        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.job_category',
        'app.local_name',
        'app.invite',
        'app.thread',
        'app.message'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Collaborator = ClassRegistry::init('Collaborator');
        //$this->Collaborator->User = ClassRegistry::init('User');
        //$this->Collaborator->Goal->Purpose = ClassRegistry::init('Purpose');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Collaborator);

        parent::tearDown();
    }

    function testAdd()
    {
        $this->Collaborator->my_uid = 1;
        $this->Collaborator->current_team_id = 1;
        $res = $this->Collaborator->add(1);
        $this->assertTrue(!empty($res));
    }

    function testGetCollabeGoalDetail()
    {
        $team_id = 1;

        $params = [
            'first_name' => 'test',
            'last_name'  => 'test'
        ];
        $this->Collaborator->User->save($params);
        $user_id = $this->Collaborator->User->getLastInsertID();

        $params = [
            'user_id' => $user_id,
            'team_id' => $team_id,
            'name'    => 'test'
        ];
        $this->Collaborator->Goal->Purpose->save($params);
        $purpose_id = $this->Collaborator->Goal->Purpose->getLastInsertID();

        $params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'purpose_id'       => $purpose_id,
            'name'             => 'test',
            'goal_category_id' => 1,
            'end_date'         => '1427813999',
            'photo_file_name'  => 'aa.png'
        ];
        $this->Collaborator->Goal->save($params);
        $goal_id = $this->Collaborator->Goal->getLastInsertID();

        $valued_flg = 0;
        $params = [
            'user_id'    => $user_id,
            'team_id'    => $team_id,
            'goal_id'    => $goal_id,
            'valued_flg' => $valued_flg,
            'type'       => 0,
            'priority'   => 1,
        ];
        $this->Collaborator->save($params);

        $goal_detail = $this->Collaborator->getCollaboGoalDetail($team_id, $user_id, $valued_flg);
        $this->assertEquals($user_id, $goal_detail[0]['User']['id']);
    }

    function testChangeApprovalStatus()
    {
        $user_id = 777;
        $team_id = 888;
        $goal_id = 999;
        $valued_flg = 0;

        $params = [
            'user_id'    => $user_id,
            'team_id'    => $team_id,
            'goal_id'    => $goal_id,
            'valued_flg' => $valued_flg,
        ];
        $this->Collaborator->save($params);
        $id = $this->Collaborator->getLastInsertID();
        $this->Collaborator->changeApprovalStatus($id, 1);

        $res = $this->Collaborator->findById($id);
        $this->assertEquals(1, $res['Collaborator']['valued_flg']);
    }

    function testcountCollaboGoal()
    {
        $team_id = 1;
        $params = [
            'first_name' => 'test',
            'last_name'  => 'test'
        ];
        $this->Collaborator->User->save($params);
        $user_id = $this->Collaborator->User->getLastInsertID();

        $params = [
            'user_id' => $user_id,
            'team_id' => $team_id,
            'name'    => 'test'
        ];
        $this->Collaborator->Goal->Purpose->save($params);
        $purpose_id = $this->Collaborator->Goal->Purpose->getLastInsertID();

        $params = [
            'user_id'          => $user_id,
            'team_id'          => $team_id,
            'purpose_id'       => $purpose_id,
            'name'             => 'test',
            'goal_category_id' => 1,
            'end_date'         => '1427813999',
            'photo_file_name'  => 'aa.png'
        ];
        $this->Collaborator->Goal->save($params);
        $goal_id = $this->Collaborator->Goal->getLastInsertID();

        $valued_flg = 0;
        $params = [
            'user_id'    => $user_id,
            'team_id'    => $team_id,
            'goal_id'    => $goal_id,
            'valued_flg' => $valued_flg,
            'type'       => 0,
            'priority'   => 1,
        ];
        $this->Collaborator->save($params);
        $cnt = $this->Collaborator->countCollaboGoal($team_id, $user_id, [$goal_id], $valued_flg);
        $this->assertEquals(0, $cnt);
    }

    function testcountCollaboGoalModifyStatus()
    {
        $team_id = 999;
        $user_id = 888;
        $goal_id = 777;
        $valued_flg = 3;
        $params = [
            'user_id'    => $user_id,
            'team_id'    => $team_id,
            'goal_id'    => $goal_id,
            'valued_flg' => $valued_flg,
            'type'       => 0,
            'priority'   => 1,
        ];
        $this->Collaborator->save($params);
        $cnt = $this->Collaborator->countCollaboGoal($team_id, $user_id, [$goal_id], $valued_flg);
        $this->assertEquals(0, $cnt);
    }

    function testGetLeaderUidNotNull()
    {
        $this->Collaborator->my_uid = 1;
        $this->Collaborator->current_team_id = 1;
        $this->Collaborator->save(['goal_id' => 1, 'team_id' => 1, 'user_id' => 1, 'type' => Collaborator::TYPE_OWNER]);

        $actual = $this->Collaborator->getLeaderUid(1);
        $this->assertEquals(1, $actual);
    }

    function testGetLeaderUidNull()
    {
        $this->Collaborator->my_uid = 1;
        $this->Collaborator->current_team_id = 1;
        $actual = $this->Collaborator->getLeaderUid(111111);
        $this->assertEquals(null, $actual);
    }

    function testGetCollaboratorListByGoalId()
    {
        $this->Collaborator->my_uid = 1;
        $this->Collaborator->current_team_id = 1;
        $data = [
            'user_id' => 100,
            'goal_id' => 200,
            'team_id' => 1,
            'type'    => Collaborator::TYPE_COLLABORATOR
        ];
        $this->Collaborator->save($data);
        $actual = $this->Collaborator->getCollaboratorListByGoalId(200, Collaborator::TYPE_COLLABORATOR);
        $this->assertNotEmpty($actual);
    }

    function testGetCollaboratorOwnerTypeTrue() {
        $team_id = 1;
        $user_id = 100;
        $goal_id = 200;
        $data = [
            'team_id' => $team_id,
            'user_id' => $user_id,
            'goal_id' => $goal_id,
            'type'    => Collaborator::TYPE_OWNER
        ];
        $this->Collaborator->save($data);
        $res = $this->Collaborator->getCollaborator($team_id, $user_id, $goal_id);
        $this->assertCount(1, $res);
    }

    function testGetCollaboratorOwnerTypeFalse() {
        $team_id = 1;
        $user_id = 100;
        $goal_id = 200;
        $data = [
            'team_id' => $team_id,
            'user_id' => $user_id,
            'goal_id' => $goal_id,
            'type'    => Collaborator::TYPE_OWNER
        ];
        $this->Collaborator->save($data);
        $res = $this->Collaborator->getCollaborator($team_id, $user_id, $goal_id, false);
        $this->assertCount(0, $res);
    }
}
