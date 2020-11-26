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

    public function getRequestLoginValidationRule(): array
    {
        $rules = [
            'email' => [
                validator::email(),
            ],
        ];

        return $rules;
    }

    public function get2FALoginValidationRule(): array
    {
        $rules = [
            'auth_hash' => [validator::stringType()::notEmpty(),
            ],
            '2fa_token' => [
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

    public static function createRequestLoginValidator(): self
    {
        $self = new self();
        $self->addRule($self->getRequestLoginValidationRule(), true);
        return $self;
    }

    public static function create2FALoginValidator(): self
    {
        $self = new self();
        $self->addRule($self->get2FALoginValidationRule(), true);
        return $self;
    }
}
