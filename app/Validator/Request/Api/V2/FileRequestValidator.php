<?php
App::uses('BaseValidator', 'Validator');
App::uses('CommonValidator', 'Validator');

use Respect\Validation\Validator as validator;

class FileRequestValidator extends BaseValidator
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
    public function getUploadValidationRule(): array
    {
        $rules = [
            'name' => [validator::notEmpty()::stringType()],
            'tmp_name' => [validator::notEmpty()::stringType()],
            'type' => [validator::notEmpty()::stringType()],
            'error' => [validator::notEmpty()::equals(0)],
            'size' => [validator::notEmpty()::intType()],
        ];

        return $rules;
    }

    public static function createUploadValidator(): self
    {
        $self = new self();
        $self->addRule($self->getUploadValidationRule());
        return $self;
    }
}
