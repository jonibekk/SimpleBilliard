<?php
App::uses('GoalousTestCase', 'Test');
include(__DIR__ . '/../../../../Validator/Rule/Unique.php');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/08
 * Time: 10:07
 */

use Respect\Validation\Validator as validator;

class UniqueRuleTest extends GoalousTestCase
{
    public function test_unique_success()
    {
        validator::with('\\Validator\\Rule');

        self::assertTrue(validator::unique()->validate([1, 2, 3, 4]));
    }

    public function test_unique_failure()
    {
        validator::with('\\Validator\\Rule');

        self::assertFalse(validator::unique()->validate([2, 2, 3, 4]));
    }

}