<?php App::uses('GoalousTestCase', 'Test');
App::uses('Follower', 'Model');

/**
 * Follower Test Case
 *
 * @property Follower $Follower
 */
class FollowerTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.follower',
        'app.team',
        'app.user',
        'app.email',
        'app.goal',
        'app.group',
        'app.member_group',
        'app.local_name',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Follower = ClassRegistry::init('Follower');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Follower);

        parent::tearDown();
    }

    function testAddFollow()
    {
        $this->_setDefault();
        $data = [
            'user_id' => 1,
            'team_id' => 1,
            'goal_id' => 100
        ];
        $this->Follower->save($data);
        $this->assertFalse($this->Follower->addFollower(100));
    }

    function testGetFollowerListByGoalId()
    {
        $this->_setDefault();
        $expected = [(int)1 => '1'];
        $actual = $this->Follower->getFollowerListByGoalId(1);
        $this->assertEquals($expected, $actual);
    }

    function testGetFollowerByGoalId()
    {
        $this->_setDefault();

        // 対象ゴールのフォロワー全員
        $followers = $this->Follower->getFollowerByGoalId(2);
        $this->assertNotEmpty($followers);

        // limit 指定
        $followers2 = $this->Follower->getFollowerByGoalId(2, ['limit' => 1]);
        $this->assertCount(1, $followers2);

        // limit + page 指定
        $followers3 = $this->Follower->getFollowerByGoalId(2, ['limit' => 1, 'page' => 2]);
        $this->assertCount(1, $followers3);

        $this->assertNotEquals($followers2[0]['User']['id'], $followers3[0]['User']['id']);

        // グループ情報付き
        $followers = $this->Follower->getFollowerByGoalId(2);
        $this->assertArrayNotHasKey('Group', $followers[0]);
        $followers = $this->Follower->getFollowerByGoalId(2, ['with_group' => true]);
        $this->assertArrayHasKey('Group', $followers[0]);
    }

    function testDeleteFollower()
    {
        $this->_setDefault();
        $this->Follower->Goal->save(['team_id' => 1, 'user_id' => 1, 'name' => 'test']);
        $goal_id = $this->Follower->Goal->getLastInsertID();
        $this->Follower->addFollower($goal_id);
        $before_followers = $this->Follower->getFollowerByGoalId($goal_id);
        $this->assertNotEmpty($before_followers);

        $this->Follower->deleteFollower($goal_id);
        $after_followers = $this->Follower->getFollowerByGoalId($goal_id);
        $this->assertEmpty($after_followers);
    }

    function testIsExists()
    {
        $this->_setDefault();
        $actual = $this->Follower->isExists(9999);
        $this->assertEmpty($actual);

        $this->Follower->Goal->save(['team_id' => 1, 'user_id' => 1, 'name' => 'test']);
        $goal_id = $this->Follower->Goal->getLastInsertID();
        $this->Follower->addFollower($goal_id);
        $actual = $this->Follower->isExists($goal_id);
        $this->assertNotEmpty($actual);
    }

    function _setDefault()
    {
        $this->Follower->my_uid = 1;
        $this->Follower->current_team_id = 1;
    }

}
