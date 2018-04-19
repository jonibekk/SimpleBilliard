<?php
/**
 * Base validator class for validation model parameters and/or other data
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/18
 * Time: 9:56
 */

use Respect\Validation\Validator as validator;

abstract class BaseValidator
{
    /**
     * @var validator Validation for user ID.
     * Accepts non-null integer type
     */
    protected $userIdBaseValidation;

    /**
     * @var validator Validation for team ID.
     * Accepts integer type if not null
     */
    protected $teamIdBaseValidation;

    /**
     * @var validator Generic validation to check name
     * Accepts alphanumeric & may only contain apostrophe (')
     */
    protected $nameBaseValidation;

    /**
     * @var validator Generic validation to check photo
     * Accepts image of pre-determined types with size below 10MB
     */
    protected $photoBaseValidation;

    protected function __construct()
    {
        $this->userIdBaseValidation = validator::notEmpty()->intType();

        $this->teamIdBaseValidation = validator::when(validator::notEmpty(), validator::intType());

        $this->nameBaseValidation = validator::notEmpty()->alnum('\'')->length(null, 128);

        //TODO
        $this->photoBaseValidation;
    }

    public abstract function validateModel(Model $model): bool;

    /**
     * @param $attributeName
     * @param $validation
     *
     * @return mixed
     */
    protected function _createValidation($attributeName, Respect\Validation\Validator $validation)
    {
        return  $validation->attribute($attributeName)->setName($attributeName);
    }

}