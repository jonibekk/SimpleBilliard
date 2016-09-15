<?php App::uses('GoalousTestCase', 'Test');
App::uses('Evaluator', 'Model');

/**
 * Evaluator Test Case
 *
 * @property Evaluator $Evaluator
 */
class EvaluatorTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluator',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Evaluator = ClassRegistry::init('Evaluator');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Evaluator);

        parent::tearDown();
    }

    public function testDummy()
    {

    }

}
