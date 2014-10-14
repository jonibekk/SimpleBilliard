<?php
App::uses('KeyResult', 'Model');

/**
 * KeyResult Test Case
 *
 * @property KeyResult $KeyResult
 */
class KeyResultTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.cake_session',
        'app.key_result',
        'app.key_result_user',
        'app.follower',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',
        'app.goal_category',
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
        'app.notification',
        'app.notify_to_user',
        'app.notify_from_user',
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
        $this->KeyResult = ClassRegistry::init('KeyResult');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KeyResult);

        parent::tearDown();
    }

    function testAdd()
    {
        $this->setDefault();
        try {
            $this->KeyResult->add([], 1);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
        unset($e);
        $data = [
            'KeyResult' => [
                'value_unit' => 2,
                'start_date' => '2014/7/7',
                'end_date'   => '2014/11/7',
                'name'       => 'test',
            ]
        ];
        $res = $this->KeyResult->add($data, 1);
        $this->assertTrue($res);

        $data = [
            'KeyResult' => [
                'value_unit' => 2,
                'start_date' => '2014/7/7',
                'end_date'   => '2014/11/7',
                'name'       => null,
            ]
        ];
        try {
            $this->KeyResult->add($data, 1);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testGetKeyResults()
    {
        $this->setDefault();
        $this->KeyResult->getKeyResults(1, true);
    }

    function setDefault()
    {
        $this->KeyResult->my_uid = 1;
        $this->KeyResult->current_team_id = 1;
        $this->KeyResult->Team->my_uid = 1;
        $this->KeyResult->Team->current_team_id = 1;
        $this->KeyResult->KeyResultUser->my_uid = 1;
        $this->KeyResult->KeyResultUser->current_team_id = 1;
    }

}
