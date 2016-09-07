<?php App::uses('GoalousTestCase', 'Test');
App::uses('GroupVision', 'Model');

/**
 * GroupVision Test Case
 *
 * @property GroupVision $GroupVision
 */
class GroupVisionTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.group_vision',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.notify_setting',

        'app.goal',
        'app.goal_category',
        'app.post',
        'app.circle',
        'app.circle_member',
        'app.post_share_circle',
        'app.action_result',
        'app.key_result',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.post_share_user',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.collaborator',
        'app.approval_history',
        'app.follower',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluator',
        'app.evaluate_score',
        'app.oauth_token',
        'app.team_member',
        'app.job_category',
        'app.member_type',
        'app.local_name',
        'app.member_group',
        'app.group',
        'app.invite',
        'app.evaluation_setting'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GroupVision = ClassRegistry::init('GroupVision');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GroupVision);

        parent::tearDown();
    }

    function testSaveGroupVisionNoData()
    {
        $this->_setDefault();
        $this->assertFalse($this->GroupVision->saveGroupVision([]));
    }

    function testSaveGroupVisionSuccess()
    {
        $this->_setDefault();
        $data = [
            'GroupVision' => [
                'name' => 'test'
            ]
        ];
        $this->assertNotEmpty($this->GroupVision->saveGroupVision($data));
    }

    function testGetGroupVision()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'    => $name
        ];
        $this->GroupVision->save($data);
        $res = $this->GroupVision->getGroupVision($team_id, 1);
        $this->assertEquals($res[0]['GroupVision']['name'], $name);
    }

    function testSetGroupVisionActiveFlag()
    {
        $team_id = 1;
        $name = 'test';
        $active_flg = 1;
        $data = [
            'team_id'    => $team_id,
            'name'       => $name,
            'active_flg' => $active_flg
        ];
        $this->GroupVision->save($data);
        $res = $this->GroupVision->setGroupVisionActiveFlag($this->GroupVision->getLastInsertID(), 0);
        $this->assertEquals($res['GroupVision']['active_flg'], 0);
    }

    function testDeleteGroupVision()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'    => $name,
        ];
        $this->GroupVision->save($data);
        $this->GroupVision->deleteGroupVision($this->GroupVision->getLastInsertID());

        $options = [
            'fields'     => ['del_flg'],
            'conditions' => [
                'id' => $this->GroupVision->getLastInsertID()
            ]
        ];
        $res = $this->GroupVision->find('first', $options);
        $this->assertCount(0, $res);
    }

    function testConvertData()
    {
        $team_id = 999;
        $group_id = 888;
        $group_name = 'sdg';

        $data = [
            'team_id' => $team_id,
            'id'      => $group_id,
            'name'    => $group_name
        ];
        $this->GroupVision->Group->save($data);

        $name = 'test';
        $data = [
            'team_id'  => $team_id,
            'group_id' => $group_id,
            'name'     => $name,
        ];
        $this->GroupVision->save($data);
        $res = $this->GroupVision->getGroupVision($team_id, 1);
        $convert_data = $this->GroupVision->convertData($team_id, $res);

        $this->assertEquals($group_name, $convert_data[0]['GroupVision']['group_name']);
    }

    function testConvertDetailData()
    {
        $team_id = 999;
        $group_id = 888;
        $group_name = 'sdg';

        $data = [
            'team_id' => $team_id,
            'id'      => $group_id,
            'name'    => $group_name
        ];
        $this->GroupVision->Group->save($data);

        $name = 'test';
        $data = [
            'team_id'  => $team_id,
            'group_id' => $group_id,
            'name'     => $name,
        ];
        $this->GroupVision->save($data);
        $res = $this->GroupVision->getGroupVisionDetail($this->GroupVision->getLastInsertID(), 1);
        $convert_data = $this->GroupVision->convertData($team_id, $res);

        $this->assertEquals($group_name, $convert_data['GroupVision']['group_name']);
    }

    function _setDefault()
    {
        $this->GroupVision->current_team_id = 1;
        $this->GroupVision->my_uid = 1;
    }

    function testGetGroupVisionDetail()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'    => $name
        ];
        $this->GroupVision->save($data);

        $res = $this->GroupVision->getGroupVisionDetail($this->GroupVision->getLastInsertID(), 1);
        $this->assertEquals($res['GroupVision']['name'], $name);
    }

}
