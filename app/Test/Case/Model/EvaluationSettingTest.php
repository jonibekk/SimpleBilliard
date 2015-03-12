<?php
App::uses('EvaluationSetting', 'Model');

/**
 * EvaluationSetting Test Case
 *
 * @property EvaluationSetting $EvaluationSetting
 */
class EvaluationSettingTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluation_setting',
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
        $this->EvaluationSetting = ClassRegistry::init('EvaluationSetting');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EvaluationSetting);

        parent::tearDown();
    }

    function testIsEnabledTrue()
    {
        $this->_setDefaultAllOn();
        $this->assertTrue($this->EvaluationSetting->isEnabled());
    }

    function testIsEnabledTrueAlreadySet()
    {
        $this->_setDefaultAllOn();
        $this->EvaluationSetting->isEnabled();
        $this->assertTrue($this->EvaluationSetting->isEnabled());
    }

    function testIsEnabledFalse()
    {
        $this->_setDefaultAllOn([EvaluationSetting::FLG_ENABLE]);
        $this->assertFalse($this->EvaluationSetting->isEnabled());
    }

    function testIsEnabledNotData()
    {
        $this->assertFalse($this->EvaluationSetting->isEnabled());
    }

    function testIsEvaluatorSelfTrue()
    {
        $this->_setDefaultAllOn();
        $this->assertTrue($this->EvaluationSetting->isEnabledSelf());
    }

    function testIsEvaluatorSelfFalse()
    {
        $this->_setDefaultAllOn([EvaluationSetting::FLG_SELF]);
        $this->assertFalse($this->EvaluationSetting->isEnabledSelf());
    }

    function testIsEvaluatorTrue()
    {
        $this->_setDefaultAllOn();
        $this->assertTrue($this->EvaluationSetting->isEnabledEvaluator());
    }

    function testIsEvaluatorFalse()
    {
        $this->_setDefaultAllOn([EvaluationSetting::FLG_EVALUATOR]);
        $this->assertFalse($this->EvaluationSetting->isEnabledEvaluator());
    }

    function testIsEvaluatorFinalTrue()
    {
        $this->_setDefaultAllOn();
        $this->assertTrue($this->EvaluationSetting->isEnabledFinal());
    }

    function testIsEvaluatorFinalFalse()
    {
        $this->_setDefaultAllOn([EvaluationSetting::FLG_FINAL]);
        $this->assertFalse($this->EvaluationSetting->isEnabledFinal());
    }

    function _setDefaultAllOn($set_false_flags = [])
    {
        $this->EvaluationSetting->my_uid = 1;
        $this->EvaluationSetting->current_team_id = 1;

        $data = [
            'team_id'    => 1,
            'enable_flg' => true,
        ];
        foreach ($set_false_flags as $val) {
            $data[$val] = false;
        }
        return $this->EvaluationSetting->save($data);
    }

}
