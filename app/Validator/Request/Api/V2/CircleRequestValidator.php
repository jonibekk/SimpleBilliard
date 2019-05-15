<?php
App::uses('BaseValidator', 'Validator');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/09/06
 * Time: 13:37
 */

use Respect\Validation\Validator as validator;

class CircleRequestValidator extends BaseValidator
{
    public function getDefaultValidationRule(): array
    {
        return [];
    }

    public function getPostMembersValidationRule(): array
    {
        $rules = [
            'user_id' => [validator::arrayVal()->each(validator::notEmpty()::numeric())]
        ];
        return $rules;
    }

    public static function createPostMembersValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostMembersValidationRule(), true);
        return $self;
    }
}
