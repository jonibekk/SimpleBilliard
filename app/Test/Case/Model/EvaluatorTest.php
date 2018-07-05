<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Evaluator', 'Model');
App::import('Model/Entity', 'EvaluatorEntity');

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

    public function test_getEntityFromFind_success()
    {
        /** @var Evaluator $Evaluator */
        $Evaluator = ClassRegistry::init('Evaluator');

        $conditions = [
            'conditions' => [
                'id' => 1
            ]
        ];

        $result = $Evaluator->useType()->useEntity()->find('first', $conditions);

        $this->assertTrue($result instanceof EvaluatorEntity);
    }

    public function test_getEntityFromSave_success()
    {
        /** @var Evaluator $Evaluator */
        $Evaluator = ClassRegistry::init('Evaluator');

        $data = [
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 2,
            'team_id' => 1,
            'index_num' => 10
        ];

        $result = $Evaluator->useType()->useEntity()->save($data);

        $this->assertTrue($result instanceof EvaluatorEntity);
    }
}
