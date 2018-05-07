<?php
App::uses('BaseValidator', 'Validator');
App::uses('CommonValidator', 'Validator');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/18
 * Time: 9:57
 */

use Respect\Validation\Validator as validator;

class GoalValidator extends BaseValidator
{
    public function getDefaultValidationRule(): array
    {
        $defaultRule = [
            'user_id'          => [validator::intType()],
            'team_id'          => [validator::intType()],
            'goal_name'        => [validator::stringType()->length(null, 200)],
            'goal_description' => [validator::stringType()->length(null, 2000), "optional"],
            'evaluate_flg'     => [validator::boolType()],
            'status'           => [validator::intType()],
            'priority'         => [validator::intType()],
            'del_flg'          => [validator::boolType()],
            'goal_category_id' => [validator::intType()],
            'start_date'       => [CommonValidator::dateValidation()],
            'end_date'         => [CommonValidator::dateValidation()],
            'start_value'      => [validator::intType()->length(null, 15)],
            'target_value'     => [validator::intType()->length(null, 15)]
        ];

        return $defaultRule;
    }

}