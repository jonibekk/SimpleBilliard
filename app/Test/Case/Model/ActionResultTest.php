<?php
App::uses('ActionResult', 'Model');

/**
 * ActionResult Test Case
 *
 * @property ActionResult $ActionResult
 */
class ActionResultTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result',
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

        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.job_category',
        'app.local_name',
        'app.invite',
        'app.thread',
        'app.message',
        'app.action_result_file'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ActionResult = ClassRegistry::init('ActionResult');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ActionResult);

        parent::tearDown();
    }

    function testGetCount()
    {
        $this->ActionResult->current_team_id = 1;
        $this->ActionResult->my_uid = 101;

        // 自分
        $res = $this->ActionResult->getCount('me', null, null);
        $this->assertEquals(2, $res);

        // ユーザID指定
        $res = $this->ActionResult->getCount(102, null, null);
        $this->assertEquals(1, $res);
    }

    function testActionEdit()
    {
        $this->_setDefault();
        $before_save = [
            'ActionResult' => [
                'name'    => 'test',
                'team_id' => 1,
                'user_id' => 1,
            ]
        ];
        $save_data = $this->ActionResult->save($before_save);
        $save_data['photo_delete'][1] = 1;
        $res = $this->ActionResult->actionEdit($save_data);
        $this->assertTrue(!empty($res));
    }

    function testAddCompletedAction()
    {
        $this->_setDefault();
        $data = [
            'ActionResult' => [
                'name'          => 'test',
                'key_result_id' => 1
            ]
        ];
        $res = $this->ActionResult->addCompletedAction($data, 1);
        $this->assertTrue(!empty($res));
    }

    function testAddCompletedActionFail()
    {
        $this->_setDefault();
        $res = $this->ActionResult->addCompletedAction([], 1);
        $this->assertFalse($res);
    }

    function testGetCountByGoalId()
    {
        $this->_setDefault();
        $res = $this->ActionResult->getCountByGoalId(6);
        $this->assertEquals(1, $res);
    }

    function testGetWithAttachedFiles()
    {
        $this->_setDefault();
        $row = $this->ActionResult->getWithAttachedFiles(1);
        $this->assertArrayHasKey('ActionResultFile', $row);
    }

    function testActionEditWithFile()
    {
        $this->_setDefault();
        // 通常 edit
        $data = [
            'ActionResult' => [
                'id' => 1,
                'name' => 'edit string',
            ]
        ];
        $res = $this->ActionResult->actionEdit($data);
        $this->assertTrue($res);
        $row = $this->ActionResult->findById(1);
        $this->assertEquals($row['ActionResult']['name'], $data['ActionResult']['name']);

        // 添付ファイルあり
        $this->ActionResult->ActionResultFile->AttachedFile = $this->getMockForModel('AttachedFile', array('updateRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->ActionResult->ActionResultFile->AttachedFile->expects($this->any())
                                           ->method('updateRelatedFiles')
                                           ->will($this->returnValue(true));
        $data = [
            'ActionResult' => [
                'id' => 1,
                'name' => 'edit string2',
            ],
            'file_id' => ['aaa', 'bbb']
        ];
        $res = $this->ActionResult->actionEdit($data);
        $this->assertTrue($res);
        $row = $this->ActionResult->findById(1);
        $this->assertEquals($row['ActionResult']['name'], $data['ActionResult']['name']);

        // rollback
        $this->ActionResult->ActionResultFile->AttachedFile = $this->getMockForModel('AttachedFile', array('updateRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->ActionResult->ActionResultFile->AttachedFile->expects($this->any())
                                           ->method('updateRelatedFiles')
                                           ->will($this->returnValue(false));
        $data = [
            'ActionResult' => [
                'id' => 1,
                'name' => 'edit string3',
            ],
            'file_id' => ['aaa', 'bbb']
        ];
        $res = $this->ActionResult->actionEdit($data);
        $this->assertFalse($res);
        $row = $this->ActionResult->findById(1);
        $this->assertNotEquals($row['ActionResult']['name'], $data['ActionResult']['name']);
    }


    function _setDefault()
    {
        $this->ActionResult->current_team_id = 1;
        $this->ActionResult->my_uid = 1;
    }

}
