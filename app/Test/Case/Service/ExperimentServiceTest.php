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
    }

    function testIsDefined()
    {

    }


}
