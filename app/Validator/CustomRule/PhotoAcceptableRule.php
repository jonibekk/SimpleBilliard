<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/04/19
 * Time: 12:18
 */

namespace Custom\Validation\Rule;

use Model;
use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as validator;

class PhotoAcceptableRule extends AbstractRule
{
    private $model;
    private $value;

    public function __constructor(Model $modelInput, array $valueInput)
    {
        $this->model = $modelInput;
        $this->value = $valueInput;

        $paramValidator = validator::arrayType();

        if (!$paramValidator->validate($this->value)) {
            throw new ComponentException(
                sprintf('%s is not an array', $valueInput)
            );
        }

    }

    public function validate($input)
    {
        $uploadBehavior = new \UploadBehavior();
        return $uploadBehavior->canProcessImage($this->model, $this->value);
    }

}