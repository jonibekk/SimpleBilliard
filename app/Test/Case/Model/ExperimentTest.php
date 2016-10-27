<?php
App::uses('Experiment', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * Experiment Test Case
 *
 * @property Experiment $Experiment
 */
class ExperimentTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.experiment'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Experiment = ClassRegistry::init('Experiment');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Experiment);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
