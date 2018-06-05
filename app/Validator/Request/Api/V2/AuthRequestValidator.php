<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/30
 * Time: 11:39
 */

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
            'username' => [validator::email()],
            'password' => [validator::stringType()::alpha()]
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