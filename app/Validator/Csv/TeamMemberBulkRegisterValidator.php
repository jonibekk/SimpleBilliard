<?php

use Respect\Validation\Validator;
App::uses('User', 'Model');
App::uses('BaseValidator', 'Validator');

class TeamMemberBulkRegisterValidator extends BaseValidator
{
    const EMAIL_MAX = 255;
    const FIRST_NAME_MAX = 128;
    const LAST_NAME_MAX = 128;
    const ADMIN_FLAGS = ['on', 'off'];
    const LANGUAGES = ['jpn', 'eng'];

    /**
     * Default rule to be loaded when initializing new validator class
     * @return array
     */
    public function getDefaultValidationRule(): array
    {
        return [
            'email' => [
                Validator::email()
                    ->length(1, self::EMAIL_MAX)
                    ->notEmpty()
            ],
            'first_name' => [
                Validator::stringType()
                    ->regex('/' . User::USER_NAME_REGEX . '/')
                    ->length(1, self::FIRST_NAME_MAX)
                    ->notEmpty()
            ],
            'last_name' => [
                Validator::stringType()
                    ->regex('/' . User::USER_NAME_REGEX . '/')
                    ->length(1, self::LAST_NAME_MAX)
                    ->notEmpty()
            ],
            "admin_flg" => [
                Validator::stringType()
                    ->in(self::ADMIN_FLAGS)
                    ->notEmpty()
            ],
            'language' => [
                Validator::stringType()
                    ->in(self::LANGUAGES)
                    ->notEmpty()
            ]
        ];
    }

    /**
     * @return TeamMemberBulkRegisterValidator
     */
    public static function createDefaultValidator(): self
    {
        $validator = new static();
        $validator->addRule($validator->getDefaultValidationRule());
        return $validator;
    }
}
