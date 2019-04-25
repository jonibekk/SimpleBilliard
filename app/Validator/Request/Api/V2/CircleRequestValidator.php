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

    public function getPostMemberValidationRule(): array
    {
        $rules = [
            'user_ids' => [validator::arrayVal()->each(validator::notEmpty()::numeric())]
        ];
        return $rules;
    }

    public static function createPostMemberValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostMemberValidationRule(), true);
        return $self;
    }
}
