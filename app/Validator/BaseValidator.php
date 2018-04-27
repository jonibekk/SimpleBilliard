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
     * Set of rules for any validator
     *
     * @var array
     */
    protected $rules = array();

    /**
     * Validate object.
     *
     * @param array|stdClass $input       Item to be validated
     * @param array          $customRules Custom rules that would replace existing rules. Can be set to allow null variable
     *                                    [$key => [$customRule, "optional"]]
     * @param bool           $replaceFlag Clear existing rules and replace with new ones. By default it will merge existing with new ones
     *
     * @return bool         Whether validation passed or not
     * @throws \Respect\Validation\Exceptions\NestedValidationException
     */
    public final function validate($input, $customRules = array(), bool $replaceFlag = false)
    {
        if (empty($this->rules)) {
            $this->getDefaultValidationRule();
        }

        $this->rules = ($replaceFlag) ? $customRules : array_merge($this->rules, $customRules);

        $validatorArray = $this->generateValidationArray($this->rules, is_array($input));

        return validator::allOf($validatorArray)->assert($input) ?? false;
    }

    /**
     * Validate object with default rules
     *
     * @param array|stdClass $input Item to be validated
     *
     * @return bool         Whether validation passed or not
     * @throws \Respect\Validation\Exceptions\NestedValidationException
     */
    public final function validateWithDefaultRules($input)
    {
        $this->rules = $this->getDefaultValidationRule();
        return $this->validate($input);
    }

    /**
     * Default rule to be loaded when initializing new validator class
     */
    abstract public function getDefaultValidationRule(): array;

    /**
     * Add new rules
     * [$key => [$customRule, "optional"]]
     *
     * @param $array
     */
    public function addRule($array)
    {
        $this->rules = array_merge($this->rules, $array);
    }

    /**
     * Remove a rule based on key
     *
     * @param $key
     */
    public function removeRule($key)
    {
        unset($this->rules[$key]);
    }

    /**
     * Convert a rule to a validator for array
     *
     * @param string                       $keyName    Key name
     * @param Respect\Validation\Validator $validation Validation rule
     * @param bool                         $mandatory
     *
     * @return Respect\Validation\Validator
     */
    protected function createValidationForArray(
        string $keyName,
        Respect\Validation\Validator $validation,
        bool $mandatory = true
    ) {
        return validator::key($keyName, $validation, $mandatory)->setName($keyName);
    }

    /**
     * Convert a rule to a validator for class
     *
     * @param string                       $attributeName Attribute name
     * @param Respect\Validation\Validator $validation    Validation rule
     * @param bool                         $mandatory
     *
     * @return Respect\Validation\Validator
     */
    protected function createValidationForClass(
        string $attributeName,
        Respect\Validation\Validator $validation,
        bool $mandatory = true
    ) {
        return validator::attribute($attributeName, $validation, $mandatory)->setName($attributeName);
    }

    /**
     * Convert rule array to be able to validate object
     *
     * @param array $input        Array of rules
     * @param bool  $isInputArray Whether the validated object is an array
     *
     * @return array Array of validators
     */
    protected function generateValidationArray(array $input, bool $isInputArray): array
    {
        $returnValue = array();

        foreach ($input as $key => $value) {

            //If optional flag is present, set mandatory to false
            $mandatoryFlag = !(isset($value[1]) ? $value[1] == "optional" : false);

            $returnValue[] = ($isInputArray) ? $this->createValidationForArray($key, $value[0], $mandatoryFlag) :
                $this->createValidationForClass($key, $value[0], $mandatoryFlag);
        }

        return $returnValue;
    }
}