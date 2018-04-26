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
     * Set of default rule for any validator
     *
     * @var array
     */
    protected $rules = array();

    /**
     * @param array|stdClass $input       Item to be validated
     * @param array          $customRules Custom rules that would replace existing rules. Can be set to allow null variable
     *                                    [$key => [$customRule, "optional"]]
     * @param bool           $replaceFlag Replace existing rules with new one. By default it will merge existing with new ones
     *
     * @return bool         Whether validation passed or not
     * @throws \Respect\Validation\Exceptions\NestedValidationException
     */
    public final function validate($input, $customRules = array(), bool $replaceFlag = false)
    {
        $this->rules = ($replaceFlag) ? $customRules : array_merge($this->rules, $customRules);

        $validatorArray = $this->generateValidationArray($this->rules, is_array($input));

        return validator::allOf($validatorArray)->assert($input);
    }

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
     * @param $keyName
     * @param $validation
     * @param $mandatory
     *
     * @return Respect\Validation\Validator
     */
    protected function createValidationForArray(
        $keyName,
        Respect\Validation\Validator $validation,
        bool $mandatory = true
    ) {
        return validator::key($keyName, $validation, $mandatory)->setName($keyName);
    }

    /**
     * @param $keyName
     * @param $validation
     * @param $mandatory
     *
     * @return Respect\Validation\Validator
     */
    protected function createValidationForObject(
        $keyName,
        Respect\Validation\Validator $validation,
        bool $mandatory = true
    ) {
        return validator::attribute($keyName, $validation, $mandatory)->setName($keyName);
    }

    protected function generateValidationArray(array $input, bool $isInputArray): array
    {
        $returnValue = array();

        foreach ($input as $key => $value) {
            $returnValue[] = ($isInputArray) ? $this->createValidationForArray($key, $value[0], !isset($value[1])) :
                $this->createValidationForObject($key, $value[0], !isset($value[1]));
        }

        return $returnValue;
    }
}