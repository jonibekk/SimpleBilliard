<?php
App::uses('TeamVision', 'Model');

/**
 * TeamVision Test Case
 *
 * @property TeamVision $TeamVision
 */
class TeamVisionTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team_vision',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.purpose',
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
        'app.thread',
        'app.message',
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
        $this->TeamVision = ClassRegistry::init('TeamVision');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TeamVision);

        parent::tearDown();
    }

    function testSaveTeamVisionNoData()
    {
        $this->_setDefault();
        $this->assertFalse($this->TeamVision->saveTeamVision([]));
    }
    function testSaveTeamVisionSuccess()
    {
        $this->_setDefault();
        $data = ['TeamVision'=>['name'=>'test']];
        $this->assertNotEmpty($this->TeamVision->saveTeamVision($data));
    }
    function _setDefault()
    {
        $this->TeamVision->current_team_id=1;
        $this->TeamVision->my_uid=1;
    }

    function testGetTeamVision()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'=>$name
        ];
        $this->TeamVision->save($data);
        $res = $this->TeamVision->getTeamVision($team_id, 1);
        $this->assertEquals($res[0]['TeamVision']['name'], $name);
    }

    function testSetTeamVisionActiveFlag()
    {
        $team_id = 1;
        $name = 'test';
        $active_flg = 1;
        $data = [
            'team_id' => $team_id,
            'name'=>$name,
            'active_flg' => $active_flg
        ];
        $this->TeamVision->save($data);
        $res = $this->TeamVision->setTeamVisionActiveFlag($this->TeamVision->getLastInsertID(), 0);
        $this->assertEquals($res['TeamVision']['active_flg'], 0);
    }

    function testDeleteTeamVision()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'=>$name,
        ];
        $this->TeamVision->save($data);
        $this->TeamVision->deleteTeamVision($this->TeamVision->getLastInsertID());

        $options = [
            'fields' => ['del_flg'],
            'conditions' => [
                'id' => $this->TeamVision->getLastInsertID()
            ]
        ];
        $res = $this->TeamVision->find('first', $options);
        $this->assertCount(0, $res);
    }

    function testConvertData()
    {
        $team_id = 1;
        $name = 'test';
        $image_name = 'test.jpg';
        $data = [
            'team_id' => $team_id,
            'name'=>$name,
            'photo_file_name' => $image_name
        ];
        $this->TeamVision->save($data);
        $res = $this->TeamVision->getTeamVision($team_id, 1);
        $convert_data = $this->TeamVision->convertData($res);
        $this->assertNotEquals($image_name, $convert_data[0]['TeamVision']['photo_path']);
    }

    function testConvertDetailData()
    {
        $team_id = 1;
        $name = 'test';
        $image_name = 'test.jpg';
        $data = [
            'team_id' => $team_id,
            'name'=>$name,
            'photo_file_name' => $image_name
        ];
        $this->TeamVision->save($data);
        $res = $this->TeamVision->getTeamVisionDetail($this->TeamVision->getLastInsertID(), 1);
        $convert_data = $this->TeamVision->convertData($res);
        $this->assertNotEquals($image_name, $convert_data['TeamVision']['photo_path']);
    }

    function testGetTeamVisionDetail()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'=>$name
        ];
        $this->TeamVision->save($data);

        $res = $this->TeamVision->getTeamVisionDetail($this->TeamVision->getLastInsertID(), 1);
        $this->assertEquals($res['TeamVision']['name'], $name);
    }

}
