<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Experiment', 'Model');
App::import('Service', 'ExperimentService');

/**
 * ExperimentServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property ExperimentService $ExperimentService
 * @property Experiment        $Experiment
 */
class ExperimentServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.experiment',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ExperimentService = ClassRegistry::init('ExperimentService');
        /** @var Experiment $Experiment */
        $this->Experiment = ClassRegistry::init('Experiment');
        $this->Experiment->current_team_id = 1;
    }

    function test_isDefined_NotFound()
    {
        $this->assertFalse($this->ExperimentService->isDefined('test'));
    }

    function test_isDefined_ExistsExperiment()
    {
        $this->assertFalse($this->ExperimentService->isDefined('CircleDefaultSettingOn'));

        $this->_clearCache();
        $this->Experiment->save(['name' => 'CircleDefaultSettingOn', 'team_id' => 1]);
        $this->assertTrue($this->ExperimentService->isDefined('CircleDefaultSettingOn'));
    }
}
