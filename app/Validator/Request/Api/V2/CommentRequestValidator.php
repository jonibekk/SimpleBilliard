<?php
App::uses('BaseValidator', 'Validator');

/**
 * User: Marti Floriach
 * Date: 2018/09/26
 */

use Respect\Validation\Validator as validator;

class CommentRequestValidator extends BaseValidator
{
    public function getDefaultValidationRule(): array {
        return [];
    }

    public function getCommentReadValidationRule(): array
    {
        $rules = [
            "comment_ids" => [validator::arrayType()::length(null, 1000)]
        ];
        return $rules;
    }

    public function getCommentEditValidationRule(): array
    {
        $rules = [
            "body" => [validator::notEmpty()::length(1, 10000)],
            "resources" => [
                // Could not use max(10) on array
                validator::arrayType()->length(null, 10),
                "optional"
            ],
        ];
        return $rules;
    }

    public static function createCommentReadValidator(): self
    {
        $self = new self();
        $self->addRule($self->getCommentReadValidationRule(), true);
        return $self;
    }

    public static function createCommentEditValidator(): self
    {
        $self = new self();
        $self->addRule($self->getCommentEditValidationRule(), true);
        return $self;
    }

}
