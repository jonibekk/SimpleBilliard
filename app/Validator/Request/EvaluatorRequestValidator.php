<?php
require(__DIR__ . '/../BaseValidator.php');
require(__DIR__ . '/../CommonValidator.php');
include(__DIR__ . '/../Rule/Unique.php');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/01
 * Time: 12:08
 */

use Respect\Validation\Validator as validator;

class EvaluatorRequestValidator extends BaseValidator
{
    const MAX_NUMBER_OF_EVALUATORS = 7;

    public function getDefaultValidationRule(): array
    {
        return [];
    }

    public function getPostValidationRule($evaluateeId): array
    {
        validator::with('\\Validator\\Rule');

        $defaultRules = [
            'evaluatee_user_id'  => [validator::intType()],
            'evaluator_user_ids' => [
                validator::not(validator::contains($evaluateeId))
                         ->length(null, self::MAX_NUMBER_OF_EVALUATORS)
                         ->each(validator::intType())
                         ->unique()
            ]
        ];

        return $defaultRules;
    }

}