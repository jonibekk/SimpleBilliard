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
        $Experiment = ClassRegistry::init('Experiment');
        $Experiment->current_team_id = 1;
    }

    function testIsDefined()
    {
        $this->assertTrue($this->ExperimentService->isDefined('test'));
    }

}
