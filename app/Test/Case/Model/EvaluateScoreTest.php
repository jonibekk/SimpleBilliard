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
        'app.evaluation',
        'app.evaluate_score'
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
        $teamId = 1;
        $this->EvaluateScore->deleteAll(['team_id' => $teamId]);
        $data = [
            [
                'team_id'     => 1,
                'name'        => 'A++',
                'description' => 'aiueo',
                'index_num'   => 0
            ],
            [
                'team_id'     => 1,
                'name'        => 'A+',
                'description' => 'kakikukeko',
                'index_num'   => 1
            ],
            [
                'team_id'     => 2,
                'name'        => 'A++',
                'description' => 'sasisuseso',
                'index_num'   => 1
            ],
            [
                'team_id'     => 1,
                'name'        => 'A',
                'description' => 'tatituteto',
                'index_num'   => 2
            ],
            [
                'team_id'     => 1,
                'name'        => 'B',
                'description' => 'naninuneno',
                'index_num'   => 3
            ],
            [
                'team_id'     => 2,
                'name'        => 'A+',
                'description' => 'hahihuheho',
                'index_num'   => 2
            ]
        ];
        $this->EvaluateScore->saveAll($data);
        // plus 1 because add null index in getScoreList()
        $expected = count(Hash::extract($data, "{n}[team_id={$teamId}]")) + 1;

        $res = $this->EvaluateScore->getScoreList(1);
        $this->assertEquals(count($res), $expected);
    }
}
