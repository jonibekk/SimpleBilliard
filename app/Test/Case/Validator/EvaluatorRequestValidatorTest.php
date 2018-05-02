<?php
App::uses('GoalousTestCase', 'Test');
App::uses('EvaluatorRequestValidator', 'Validator/Request');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/02
 * Time: 11:21
 */
class EvaluatorRequestValidatorTest extends GoalousTestCase
{
    public function test_validatePost_success()
    {
        $evaluateeUserId = 1;

        $data = [
            'evaluatee_user_id'  => $evaluateeUserId,
            'evaluator_user_ids' => [2, 5, 3, 4]
        ];

        try {
            $evaluatorRequestValidator = new EvaluatorRequestValidator();

            $evaluatorRequestValidator->addRule($evaluatorRequestValidator->getPostValidationRule($evaluateeUserId));

            $this->assertTrue($evaluatorRequestValidator->validate($data));

        } catch (\Respect\Validation\Exceptions\NestedValidationException $exception) {
        }

    }

    public function test_validatePost_failure()
    {

        $evaluateeUserId = 1;

        $data = [
            'evaluatee_user_id'  => $evaluateeUserId,
            'evaluator_user_ids' => [1, 2, 3, 4]
        ];

        try {
            $evaluatorRequestValidator = new EvaluatorRequestValidator();

            $evaluatorRequestValidator->addRule($evaluatorRequestValidator->getPostValidationRule($evaluateeUserId));

            $this->assertFalse($evaluatorRequestValidator->validate($data));

            $data['evaluator_user_ids'] = [2, 2, 3, 4];

            $this->assertFalse($evaluatorRequestValidator->validate($data));

        } catch (\Respect\Validation\Exceptions\NestedValidationException $exception) {
        }
    }
}