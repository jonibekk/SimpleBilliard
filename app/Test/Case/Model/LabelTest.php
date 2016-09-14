<?php
App::uses('Label', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * Label Test Case
 *
 * @property Label $Label
 */
class LabelTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.label',
        'app.team',
        'app.badge',
        'app.circle',
        'app.circle_member',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',
        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.action_result_file',
        'app.attached_file',
        'app.comment_file',
        'app.post_file',
        'app.collaborator',
        'app.approval_history',
        'app.follower',
        'app.evaluation',
        'app.evaluate_term',
        'app.evaluator',
        'app.evaluate_score',
        'app.post_share_user',
        'app.post_share_circle',
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
        'app.group_vision',
        'app.recovery_code',
        'app.device',
        'app.invite',
        'app.evaluation_setting',
        'app.team_vision',
        'app.team_insight',
        'app.group_insight',
        'app.circle_insight',
        'app.access_user'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Label = ClassRegistry::init('Label');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Label);

        parent::tearDown();
    }

    function testGetListWithGoalCount()
    {
        $this->_setDefault();
        $this->_saveDefaultData();
        $actual = $this->Label->getListWithGoalCount();
        $this->assertEquals(['test3', 'test4', 'test2', 'test1'], Hash::extract($actual, '{n}.Label.name'));
    }

    function _setDefault()
    {
        $this->Label->current_team_id = 1;
        $this->Label->my_uid = 1;
    }

    function _saveDefaultData()
    {
        $fixture_data = [
            [
                'name'             => 'test1',
                'team_id'          => 1,
                'goal_label_count' => '1',
            ],
            [
                'name'             => 'test2',
                'team_id'          => 1,
                'goal_label_count' => '2',
            ],
            [
                'name'             => 'test3',
                'team_id'          => 1,
                'goal_label_count' => '4',
            ],
            [
                'name'             => 'test4',
                'team_id'          => 1,
                'goal_label_count' => '3',
            ],
        ];
        $this->Label->saveAll($fixture_data);
    }

}
