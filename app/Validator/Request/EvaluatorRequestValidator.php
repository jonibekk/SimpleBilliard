<?php
require(__DIR__ . '/../BaseValidator.php');
require(__DIR__ . '/../CommonValidator.php');

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

    private $_evaluateeId;

    public function __construct(int $evaluateeId)
    {
        $this->_evaluateeId = $evaluateeId;
    }

    public function getDefaultValidationRule(): array
    {
        return [];
    }

    public function getPostValidationRule(): array
    {
        validator::with('Validator\\CustomRule');

        $defaultRules = [
            'evaluatee_user_id'  => [validator::intType()],
            'evaluator_user_ids' => [
                validator::not(validator::contains($this->_evaluateeId))
                         ->length(null, self::MAX_NUMBER_OF_EVALUATORS)
                         ->each(validator::intType())
                         ->unique()
            ]
        ];

        return $defaultRules;
    }

}