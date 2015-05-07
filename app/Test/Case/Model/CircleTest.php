<?php
App::uses('Circle', 'Model');

/**
 * Circle Test Case
 *
 * @property Circle $Circle
 */
class CircleTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.circle',
        'app.team',
        'app.badge',
        'app.user', 'app.notify_setting',
        'app.email',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.post_like',
        'app.post_read',
        'app.post_share_user',
        'app.post_share_circle',
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
        'app.circle_member'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Circle = ClassRegistry::init('Circle');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Circle);

        parent::tearDown();
    }

    public function testUpdateModifiedIfEmpty()
    {
        $circle_list = [];
        $res = $this->Circle->updateModified($circle_list);
        $this->assertFalse($res);
    }

    function testGetPublicCircles()
    {
        $this->Circle->my_uid = 1;
        $this->Circle->current_team_id = 1;
        $this->Circle->getPublicCircles($type = 'all');
        $this->Circle->getPublicCircles($type = 'joined');
        $this->Circle->getPublicCircles($type = 'non-joined');
        $this->Circle->my_uid = 2;
        $this->Circle->getPublicCircles($type = 'joined');
        $this->Circle->getPublicCircles($type = 'non-joined');
    }

    public function testAddCircles()
    {
        $this->_setDefault();
        $data = [
            'Circle' => [
                'name'       => 'test',
                'public_flg' => true,
            ]
        ];
        $res = $this->Circle->add($data);
        $this->assertTrue($res);
    }

    function _setDefault()
    {
        $this->Circle->my_uid = 1;
        $this->Circle->current_team_id = 1;
        $this->Circle->PostShareCircle->Post->my_uid = 1;
        $this->Circle->PostShareCircle->Post->current_team_id = 1;
    }

    function testIsSecret()
    {
        $this->Circle->id = 18;
        $res = $this->Circle->isSecret($this->Circle->id);
        $this->assertTrue(empty($res));
    }

}
