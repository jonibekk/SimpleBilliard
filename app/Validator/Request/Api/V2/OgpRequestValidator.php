<?php
App::uses('BaseValidator', 'Validator');

use Respect\Validation\Validator as validator;

class OgpRequestValidator extends BaseValidator
{
    public function getDefaultValidationRule(): array
    {
        return [];
    }

    public function getOgpInfoValidationRule(): array
    {
        $rules = [
            'text' => [validator::stringType()::notEmpty()]
        ];
        return $rules;
    }

    public static function createOgpInfoValidator(): self
    {
        $self = new self();
        $self->addRule($self->getOgpInfoValidationRule(), true);
        return $self;
    }
}
