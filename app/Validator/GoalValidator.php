<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/18
 * Time: 9:57
 */

class GoalValidator extends BaseValidator
{

    private $goalNameValidation = null;

    private $goalDescriptionValidation = null;

    private $evaluateFlagValidation = null;

    private $goalStatusValidation = null;

    private $goalPriorityValdiation = null;

    private $delFlagValidation = null;

    private $photoBaseValidation = null;

    private $goalCategoryIdValidation = null;

    private $startDateValidation = null;

    private $endDateValidation = null;

    private $startValueValidation = null;

    private $targetValueValidation = null;

    private $termTypeValidation = null;

    public function validate($goalModel): bool
    {
        // TODO: Implement validateModel() method.
    }

}