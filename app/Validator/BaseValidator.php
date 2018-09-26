<?php
/**
 * Base validator class for validation model parameters and/or other data
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/18
 * Time: 9:56
 */

use Respect\Validation\Validator as validator;
use \Respect\Validation\Exceptions\NestedValidationException;

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
     * @param array|stdClass $input Item to be validated
     *
     * @return bool         Whether validation passed or not
     * @throws \Respect\Validation\Exceptions\NestedValidationException
     * @throws Exception
     */
    public final function validate($input)
    {
        if (empty($this->rules)) {
            $this->resetValidationRules();
        }

        //If no default rules are set, return false
        if (empty($this->rules)) {
            return false;
        }

        $validatorArray = $this->generateValidationArray($this->rules, is_array($input));

        try {
            return validator::allOf($validatorArray)->assert($input) ?? false;
        } catch (NestedValidationException $exception) {
            // Applying validation message and translation message to end of validation
            throw $this->applyValidationMessageAndTranslation($exception);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Apply validation message and translation to validation errors
     *
     * @see http://respect.github.io/Validation/docs/
     *      Search the title below in Document
     *      "Custom messages"
     *      "Getting messages as an array by name"
     *
     * @param NestedValidationException $nestedValidationException
     * @return NestedValidationException
     */
    private function applyValidationMessageAndTranslation(NestedValidationException $nestedValidationException): NestedValidationException
    {
        if ($nestedValidationException instanceof NestedValidationException) {
            $nestedValidationException->findMessages($this->getValidationMessageFromConfig());
        }
        return $nestedValidationException;
    }

    /**
     * Read the validation message from config file.
     * @return array
     */
    private function getValidationMessageFromConfig(): array
    {
        // Need to load config after the language has decided.
        // Loading on bootstrap_common.php is not decided language yet.
        Configure::load("validation_messages.php");
        return Configure::read("validation_messages");
    }

    /**
     * Reset rules to default one
     */
    public final function resetValidationRules()
    {
        $this->rules = $this->getDefaultValidationRule();
    }

    /**
     * Default rule to be loaded when initializing new validator class
     */
    abstract public function getDefaultValidationRule(): array;

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
     * Validate object with default rules
     *
     * @param array|stdClass $input Item to be validated
     *
     * @return bool         Whether validation passed or not
     * @throws \Respect\Validation\Exceptions\NestedValidationException
     */
    public final function validateWithDefaultRules($input)
    {
        $validatorArray = $this->generateValidationArray($this->getDefaultValidationRule(), is_array($input));

        return validator::allOf($validatorArray)->assert($input) ?? false;
    }

    /**
     * Add new rules
     * [$key => [$customRule, "optional"]]
     *
     * @param array $rules                Custom rules that would replace / append existing rules.
     *                                    [$key => [$customRule, "optional"]]
     * @param bool  $replaceFlag          Clear existing rules and replace with new ones. By default it will merge existing with new ones
     */
    public function addRule($rules, $replaceFlag = false)
    {
        if (empty($this->rules)) {
            $this->resetValidationRules();
        }

        $this->rules = ($replaceFlag) ? $rules : array_merge($this->rules, $rules);
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
}
