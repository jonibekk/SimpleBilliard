<?php
App::uses('Evaluation', 'Model');

/**
 * Evaluation Test Case
 *
 * @property Evaluation $Evaluation
 */
class EvaluationTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluation',
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
        'app.evaluate_term',
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
        $this->Evaluation = ClassRegistry::init('Evaluation');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Evaluation);

        parent::tearDown();
    }

    function testCheckAvailViewEvaluateListNoTeamMember()
    {
        $this->Evaluation->Team->TeamMember->deleteAll(['TeamMember.team_id' => 1]);
        try {
            $this->Evaluation->checkAvailViewEvaluationList();
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testCheckAvailViewEvaluateListNotEnabled()
    {
        $this->Evaluation->Team->TeamMember->deleteAll(['TeamMember.team_id' => 1]);
        $data = [
            'team_id'               => 1,
            'user_id'               => 1,
            'evaluation_enable_flg' => 0,
        ];
        $this->Evaluation->Team->TeamMember->save($data);
        $this->Evaluation->Team->TeamMember->current_team_id = 1;
        $this->Evaluation->Team->TeamMember->my_uid = 1;
        try {
            $this->Evaluation->checkAvailViewEvaluationList();
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testCheckAvailViewEvaluateListTrue()
    {
        $this->Evaluation->Team->TeamMember->deleteAll(['TeamMember.team_id' => 1]);
        $data = [
            'team_id'               => 1,
            'user_id'               => 1,
            'evaluation_enable_flg' => 1,
        ];
        $this->Evaluation->Team->TeamMember->save($data);
        $this->Evaluation->Team->TeamMember->current_team_id = 1;
        $this->Evaluation->Team->TeamMember->my_uid = 1;
        $res = $this->Evaluation->checkAvailViewEvaluationList();
        $this->assertTrue($res);
    }
}
