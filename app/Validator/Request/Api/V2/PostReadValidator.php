<?php
App::uses('BaseValidator', 'Validator');

/**
 * User: Marti Floriach
 * Date: 2018/09/20
 */

use Respect\Validation\Validator as validator;

class PostReadValidator extends BaseValidator
{
    public function getDefaultValidationRule(): array
    {
        $rules = [
            "posts_ids" => [validator::arrayType()::length(null, 10)]
        ];
        return $rules;
    }

    /**
     * Validation rules for both adding and removing like from a post
     *
     * @return array
     */
    public function getPostReadValidationRule(): array
    {
        $rules = [
            "posts_id" => [validator::intType()]
        ];

        return $rules;
    }

    public static function createDefaultPostValidator(): self
    {
        $self = new self();
        $self->addRule($self->getDefaultValidationRule());
        return $self;
    }

    public static function createPostEditValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostEditValidationRule(), true);
        return $self;
    }


    public static function createPostReadValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostReadValidationRule());
        return $self;
    }

}