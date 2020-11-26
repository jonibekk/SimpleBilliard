<?php

App::uses('BaseValidator', 'Validator');

use Respect\Validation\Validator as validator;

class TeamRequestValidator extends BaseValidator
{
    public function getDefaultValidationRule(): array
    {
        return [];
    }

    /**
     * Validation rules for posting sso setting
     *
     * @return array
     */
    public function getPostSsoSettingValidationRule(): array
    {
        $rules = [
            'endpoint'    => [validator::notEmpty()],
            'idp_issuer'  => [validator::notEmpty()],
            'public_cert' => [validator::notEmpty()]
        ];

        return $rules;
    }

    public static function createPostSsoSettingValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostSsoSettingValidationRule(), true);
        return $self;
    }
}
