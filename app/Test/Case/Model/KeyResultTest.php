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
        $res = $this->KeyResult->getKeyResults(1, true);
        $this->assertNotEmpty($res);
    }

    function testGetSkr()
    {
        $this->setDefault();
        $skr = [
            'user_id'     => 1,
            'team_id'     => 1,
            'goal_id'     => 1,
            'special_flg' => true,
            'start_date'  => time(),
            'end_date'    => time(),
        ];
        $this->KeyResult->save($skr);
        $res = $this->KeyResult->GetSkr(1);
        $this->assertNotEmpty($res);
    }

    function testIsPermitted()
    {
        $this->setDefault();
        $kr = [
            'user_id'    => 1,
            'team_id'    => 1,
            'goal_id'    => 1,
            'start_date' => time(),
            'end_date'   => time(),
        ];
        $this->KeyResult->save($kr);
        $kr_id = $this->KeyResult->getLastInsertID();
        $res = $this->KeyResult->isPermitted($kr_id);
        $this->assertTrue($res);

        $goal = [
            'user_id' => 2,
            'team_id' => 1,
            'purpose' => 'test',
        ];
        $this->KeyResult->Goal->create();
        $this->KeyResult->Goal->save($goal);
        $kr = [
            'user_id'    => 2,
            'team_id'    => 1,
            'goal_id'    => $this->KeyResult->Goal->getLastInsertID(),
            'start_date' => time(),
            'end_date'   => time(),
        ];
        $this->KeyResult->create();
        $this->KeyResult->save($kr);
        $res = $this->KeyResult->isPermitted($this->KeyResult->getLastInsertID());

        $this->assertFalse($res);
    }

    function testGetProgress()
    {
        $this->assertEquals(0, $this->KeyResult->getProgress(0, 100, 0));
        $this->assertEquals(50, $this->KeyResult->getProgress(0, 100, 50));
        $this->assertEquals(50, $this->KeyResult->getProgress(100, 150, 125));
        $this->assertEquals(0, $this->KeyResult->getProgress(100, 150, 75));
    }

    function testSaveEdit()
    {
        $this->setDefault();

        $this->assertFalse($this->KeyResult->saveEdit([]));

        $data = [
            'KeyResult' => [
                'user_id'    => 1,
                'team_id'    => 1,
                'goal_id'    => 1,
                'value_unit' => KeyResult::UNIT_BINARY,
                'start_date' => time(),
                'end_date'   => time(),
            ]
        ];
        $res = $this->KeyResult->saveEdit($data);
        $this->assertNotEmpty($res);
    }

    function testComplete()
    {
        $this->setDefault();
        try {
            $this->KeyResult->complete(999999);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testIncomplete()
    {
        $this->setDefault();
        try {
            $this->KeyResult->incomplete(999999);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
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
