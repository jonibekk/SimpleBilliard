<?php
App::uses('EvaluateScore', 'Model');

/**
 * EvaluateScore Test Case
 *
 * @property EvaluateScore $EvaluateScore
 */
class EvaluateScoreTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluate_score',
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
        'app.notification',
        'app.notify_to_user',
        'app.notify_from_user',
        'app.oauth_token',
        'app.team_member',
        'app.job_category',
        'app.member_type',
        'app.local_name',
        'app.member_group',
        'app.group',
        'app.evaluator',
        'app.invite',
        'app.thread',
        'app.message',
        'app.evaluation'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->EvaluateScore = ClassRegistry::init('EvaluateScore');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EvaluateScore);

        parent::tearDown();
    }

    function testGetScoreList()
    {
        $data = [
            [
                'team_id' => 1,
                'comment' => 'aiueo',
            ],
            [
                'team_id' => 1,
                'comment' => 'kakikukeko'
            ],
            [
                'team_id' => 2,
                'comment' => 'sasisuseso'
            ],
            [
                'team_id' => 1,
                'comment' => 'tatituteto'
            ],
            [
                'team_id' => 1,
                'comment' => 'naninuneno'
            ],
            [
                'team_id' => 2,
                'comment' => 'hahihuheho'
            ]
        ];
        $this->EvaluateScore->save($data);
        $expected = [
            [
                'team_id' => 1,
                'comment' => 'aiueo',
            ],
            [
                'team_id' => 1,
                'comment' => 'kakikukeko'
            ],
            [
                'team_id' => 1,
                'comment' => 'tatituteto'
            ],
            [
                'team_id' => 1,
                'comment' => 'naninuneno'
            ],
        ];
        $res = $this->EvaluateScore->getScoreList(1);
        $this->assertEquals($res, $expected);
    }

}
