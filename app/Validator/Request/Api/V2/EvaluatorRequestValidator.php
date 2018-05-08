<?php
App::uses('BaseValidator', 'Validator');
App::uses('CommonValidator', 'Validator');
require_once(__DIR__ . '/../../../Rule/Unique.php');

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

    /**
     * Validate array using the post validation rules
     *
     * @param $evaluateeId
     * @param $data array to be validated
     *
     * @return bool True if validation passes
     */
    public function validatePost($evaluateeId, $data): bool
    {
        $this->addRule($this->getPostValidationRule($evaluateeId), true);
        $return = $this->validate($data);
        $this->resetValidationRules();
        return $return;
    }

}