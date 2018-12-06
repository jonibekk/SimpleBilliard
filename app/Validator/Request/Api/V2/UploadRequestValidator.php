<?php
App::uses('BaseValidator', 'Validator');
App::uses('CommonValidator', 'Validator');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/08
 * Time: 16:40
 */

use Respect\Validation\Validator as validator;

class UploadRequestValidator extends BaseValidator
{
    public function getDefaultValidationRule(): array
    {
        return [];
    }

    /**
     * Rules for validating upload POST data
     *
     * @return array
     */
    public function getPostValidationRule(): array
    {
        $rules = [
            'file_name' => [validator::notEmpty()::stringType()],
            'file_data' => [validator::notEmpty()::stringType()]
        ];

        return $rules;
    }

    public static function createPostValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostValidationRule());
        return $self;
    }

}