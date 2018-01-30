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


    function testFindExperiment()
    {
        $this->createExperiments([
            [Experiment::NAME_ENABLE_EVALUATION_FEATURE, 1],
        ]);

        $this->Experiment->current_team_id = 1;
        $experiment = $this->Experiment->findExperiment(Experiment::NAME_ENABLE_EVALUATION_FEATURE);

        $this->assertSame('1', $experiment['Experiment']['id']);
        $this->assertSame(Experiment::NAME_ENABLE_EVALUATION_FEATURE, $experiment['Experiment']['name']);
        $this->assertSame('1', $experiment['Experiment']['team_id']);
        $this->assertSame(false, $experiment['Experiment']['del_flg']);
    }

}
