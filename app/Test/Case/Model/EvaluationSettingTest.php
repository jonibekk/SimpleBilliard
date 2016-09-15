<?php App::uses('GoalousTestCase', 'Test');
App::uses('EvaluationSetting', 'Model');

/**
 * EvaluationSetting Test Case
 *
 * @property EvaluationSetting $EvaluationSetting
 */
class EvaluationSettingTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluation_setting',
        'app.team',
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

    function testIsEvaluatorLeaderTrue()
    {
        $this->_setDefaultAllOn();
        $this->assertTrue($this->EvaluationSetting->isEnabledLeader());
    }

    function testIsEvaluatorLeaderFalse()
    {
        $this->_setDefaultAllOn([EvaluationSetting::FLG_LEADER]);
        $this->assertFalse($this->EvaluationSetting->isEnabledLeader());
    }

    function _setDefaultAllOn($set_false_flags = [])
    {
        $this->EvaluationSetting->my_uid = 1;
        $this->EvaluationSetting->current_team_id = 1;
        $this->EvaluationSetting->deleteAll(['EvaluationSetting.team_id' => 1]);
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
