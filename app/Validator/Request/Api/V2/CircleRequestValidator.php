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
            'new_member_id' => [validator::notEmpty()::numeric()]
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