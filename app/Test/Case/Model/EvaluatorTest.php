<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Evaluator', 'Model');
App::import('Model/Object', 'EvaluatorObject');

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
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Evaluator);

        parent::tearDown();
    }

    public function test_getObject_success()
    {
        /** @var Evaluator $Evaluator */
        $Evaluator = ClassRegistry::init('Evaluator');

        $conditions = [
            'conditions' => [
                'id' => 1
            ],
            'conversion' => true,
            'object'     => true
        ];

        $result = $Evaluator->find('first', $conditions);
        
        $this->assertTrue($result instanceof EvaluatorObject);
    }

}
