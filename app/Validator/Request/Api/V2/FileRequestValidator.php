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
            'file_name' => [validator::notEmpty()::stringType()],
            'file_data' => [validator::notEmpty()::stringType()]
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
