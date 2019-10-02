<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/30
 * Time: 11:39
 */
App::uses('BaseValidator', 'Validator');

use Respect\Validation\Validator as validator;

class AuthRequestValidator extends BaseValidator
{
    public function getDefaultValidationRule(): array
    {
        return [];
    }

    public function getLoginValidationRule(): array
    {
        $rules = [
            'email' => [
                validator::email(),
            ],
            // Stay here simple string check.
            // Do the regex validation on the register validation.
            'password' => [
                validator::stringType()::notEmpty(),
            ]
        ];

        return $rules;
    }

    public static function createLoginValidator(): self
    {
        $self = new self();
        $self->addRule($self->getLoginValidationRule(), true);
        return $self;
    }
}